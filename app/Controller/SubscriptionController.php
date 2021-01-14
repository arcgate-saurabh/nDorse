
<?php

/* @TODO check roles against any action that it can be superadmin or admin of that org */

App::import('Vendor', 'Braintree', array('file' => 'Braintree/lib' . DS . 'Braintree.php'));

class SubscriptionController extends AppController {

    public $components = array("Auth", "Common", "ViewCont", "Braintree");
    var $uses = array("User", "Organization", "Subscription", "Transaction", "BraintreeCustomer", "SubscriptionPlan", "UserOrganization", "Email");

    public function beforeFilter() {

        parent::beforeFilter();
        Braintree_Configuration::environment(Configure::read('Braintree.env'));
        Braintree_Configuration::merchantId(Configure::read('Braintree.merchantId'));
        Braintree_Configuration::publicKey(Configure::read('Braintree.publicKey'));
        Braintree_Configuration::privateKey(Configure::read('Braintree.privateKey'));

        $this->Auth->deny();
    }

    private function _ifAllowed($organizationId) {
        $loggedinUser = $this->Auth->user();
        if ($this->Auth->User("role") == 1) {
            return;
        }
        //@TODO : check for all admins allowed or not
        $params = array();
        $params['fields'] = array("*");
        $roleList = $this->Common->setSessionRoles();
        $statusConfig = Configure::read("statusConfig");
        $conditions = array("organization_id" => $organizationId,
            "user_role" => array(array_search('admin', $roleList), array_search('super_admin', $roleList)),
            "user_id" => $loggedinUser['id'],
            "UserOrganization.status" => $statusConfig['active'],
            "Organization.status" => $statusConfig['active']
        );
        $params['conditions'] = $conditions;
        $userFound = $this->UserOrganization->find("count", $params);
        if ($userFound < 1) {
            $this->Session->setFlash(__('You are not allowed to change subscription of provided organization.'), 'default', array('class' => 'alert alert-warning'));
            $this->redirect($this->Auth->logout());
        }
    }

    private function _getClientToken() {
        return Braintree_ClientToken::generate();
    }

    private function _getCustomerId($organizationId) {
        $customer = $this->BraintreeCustomer->findByOrganizationId($organizationId);

        if (!empty($customer)) {
            return $customer['BraintreeCustomer']['customer_id'];
        } else {
            return null;
        }
    }

    private function _getUserInfo($organizationId) {
        $loggedinUser = $this->Auth->user();
        $userInfo['firstName'] = $loggedinUser['fname'];
        $userInfo['lastName'] = $loggedinUser['lname'];
        //$userInfo['cardholderName'] = $loggedinUser['fname'] . " " . $loggedinUser['lname'];
        $userInfo['email'] = $loggedinUser['email'];

        $organization = $this->Organization->findById($organizationId);

        $userInfo['company'] = $organization['Organization']['name'];

        return $userInfo;
    }

    public function btpurchase($organizationId = null) {
        $this->layout = "admin";
        $loggedinUser = $this->Auth->user();
        if ($organizationId == null) {
            //redirect to some page with message org id can not be blank
//            $this->Session->setFlash(__('Subscription already purchased for this organization'), 'default', array('class' => 'alert alert-warning'));
            $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
        }

        $decodedOrgId = $this->ViewCont->decodeString($organizationId);
        $this->_ifAllowed($decodedOrgId);
        $customer = null;
        $customerId = $this->_getCustomerId($decodedOrgId);
        if ($customerId) {
            $customer = Braintree_Customer::find($customerId);
            if (!$customer) {
                $this->BraintreeCustomer->deleteAll(array("organization_id" => $decodedOrgId));
            }
        }

        if (!empty($customer)) {
            //@TODO : show proper message or redirect
//            $this->Session->setFlash(__('Subscription already purchased for this organization'), 'default', array('class' => 'alert alert-warning', 'orgMessage'));
            $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
        }

        $this->set('clientToken', $this->_getClientToken());
        $this->set('btog', $organizationId);
        $this->set("headerTitle", "Purchase Subscription");
        $this->set("organizationId", $decodedOrgId);
        $this->set("plans", $this->Common->getSubscriptionPlans(true));
        $this->set("maxUsers", Configure::read('Braintree.max_users'));
    }

    public function btcheckout() {
        $this->layout = "admin";
        //@TODO : you want to verify all cards before they are stored in your Vault, you can turn on card verification for your entire Braintree account in the Control Panel.

        $loggedinUser = $this->Auth->user();

//       pr($this->request->data);die;


        if (!isset($this->request->data['btog']) || empty($this->request->data['btog'])) {
            //required data missing error and redirect to
            $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
        }

        if ($this->request->data['usercount'] < 1 || $this->request->data['usercount'] > Configure::read('Braintree.max_users')) {
            //Error : please enter user count between 1-100
            $_SESSION['errorMsg'] = "Please enter user count between 1-" . Configure::read('Braintree.max_users');
            $this->redirect(array('controller' => 'subscription', 'action' => 'index'));
        }

        $organizationId = $this->ViewCont->decodeString($this->request->data['btog']);

        $this->_ifAllowed($organizationId);

        $customer = null;

        $customerId = $this->_getCustomerId($organizationId);

        if (!empty($customerId)) {
            $customer = Braintree_Customer::find($customerId);

            if (empty($customer)) {
                $this->BraintreeCustomer->id = $customerId;
                $this->BraintreeCustomer->delete();
            }
        }


        if (empty($customer)) {

            if (!isset($this->request->data['payment_method_nonce'])) {
                $this->set("error", "general");
                $this->set("msg", "There is some problem in processing your payment. Please try again in few minutes.");
                $this->set('redirectUrl', Router::url('/', true) . 'subscription/btpurchase/' . urlencode($this->request->data['btog']));
            }

            $userInfo = $this->_getUserInfo($organizationId);

            $data = array_merge(array('paymentMethodNonce' => $this->request->data['payment_method_nonce']), $userInfo);

            //Create customer
            $result = Braintree_Customer::create($data);
            $_SESSION['customer_result'] = $result;
            //echo "customer response : ";
//            pr($result);
//            var_dump($result);die;

            if ($result->success) {
                $customer = $result->customer;
                //echo "cutomer success : token-> " . $customer->creditCards[0]->token;


                $customerId = $customer->id;
                $token = $customer->creditCards[0]->token;

                //save customer info in db
                $customerData = array();
                $customerData['customer_id'] = $customerId;
                $customerData['user_id'] = $loggedinUser['id'];
                $customerData['organization_id'] = $organizationId;
                $customerData['token'] = $token;

                $this->BraintreeCustomer->save($customerData);
            } else {

                $this->set("error", "credit_card");
                $this->set("msg", "Your credit card details are not valid. Please try again");
                $this->set('redirectUrl', Router::url('/', true) . 'subscription/btpurchase/' . urlencode($this->request->data['btog']));

//               foreach($result->errors->deepAll() AS $error) {
//                //@TODO : set some error data
//                   
//               }
            }
        } else {
            $customerId = $customer->id;
            $token = $customer->creditCards[0]->token;
        }

        if (!empty($customer)) {
            $this->subscribe($customer);
        }
    }

    public function subscribe($customer = null) {
        $organizationId = $this->ViewCont->decodeString($this->request->data['btog']);

        $this->_ifAllowed($organizationId);

        $subscriptionExist = $this->Subscription->findByOrganizationId($organizationId);

        if (!empty($subscriptionExist)) {
            //@TODO show error or do something
            $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
        } else {


            if ($customer == null) {
                $customerId = $this->_getCustomerId($organizationId);
                $customer = Braintree_Customer::find($customerId);
            }

            if (!$customer) {
                $this->set("error", "general");
                $this->set("msg", "There is some problem in processing your payment. Please try again in few minutes.");
                $this->set('redirectUrl', Router::url('/', true) . 'subscription/btpurchase/' . urlencode($this->request->data['btog']));
                //@TODO : error message
            } else {
                $loggedinUser = $this->Auth->user();
                $token = $customer->creditCards[0]->token;
                //Create subscitpion
                $plan = $this->request->data['plan'];

                $planDetails = $this->SubscriptionPlan->findById($plan);

                $planId = $planDetails['SubscriptionPlan']['bt_plan_id'];
                $planPrice = $planDetails['SubscriptionPlan']['rate'];

                //Calculate price
                $price = $this->request->data['usercount'] * $planPrice;

                $result = Braintree_Subscription::create(array(
                            'paymentMethodToken' => $token,
                            'planId' => $planId,
                            "price" => $price
                ));
                $_SESSION['subscription_result'] = $result;
                //echo "Subscription Result";


                if ($result->success) {
                    $startDateObj = $result->subscription->billingPeriodStartDate;
                    $endDateObj = $result->subscription->billingPeriodEndDate;


                    //save subscription
                    $subscription = array(
                        'user_id' => $loggedinUser['id'],
                        'organization_id' => $organizationId,
                        'pool_purchased' => $this->request->data['usercount'],
                        'payment_method' => 'web',
                        'start_date' => $startDateObj->format('Y-m-d'),
                        'end_date' => $endDateObj->format('Y-m-d'),
                        'amount_paid' => $price,
                        'status' => 1,
                        'type' => 'paid',
                        'bt_id' => $result->subscription->id,
                        'bt_status' => $result->subscription->transactions[0]->status,
                        'plan_id' => $plan
                    );

                    //pr($subscription);

                    if ($this->Subscription->save($subscription)) {
                        $transaction = array(
                            'organization_id' => $organizationId,
                            'user_id' => $loggedinUser['id'],
                            'type' => 'purchase',
                            'amount' => $price,
                            'user_diff' => $this->request->data['usercount'],
                            'bt_transaction_id' => $result->subscription->transactions[0]->id,
                            'bt_subscription_id' => $result->subscription->id,
                            'status' => $result->subscription->transactions[0]->status,
                            'balance' => 0
                        );

                        if ($this->Transaction->save($transaction)) {

                            //send email on successful purchase

                            $admins = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $organizationId, 'user_role' => 2)));

                            $subject = "nDorse Notification – The subscription is now ACTIVE!";
                            $template = "purchase_success";
                            $viewVars = array("purchased_by" => $loggedinUser['fname'] . " " . $loggedinUser['lname']);

                            foreach ($admins as $admin) {
                                $viewVars['fname'] = $admin['User']['fname'];
                                $viewVars['organization'] = $admin['Organization']['name'];

                                /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                                $userIdEncrypted = base64_encode($admin['User']['id']);
                                $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                                $viewVars["pathToRender"] = $pathToRender;
                                /**/

                                $configVars = serialize($viewVars);
                                $to = $admin['User']['email'];
                                $emailQueue[] = array("to" => $to, "subject" => $subject, "config_vars" => $configVars, "template" => $template);
                            }

                            $this->Email->saveMany($emailQueue);

                            $this->set('redirectUrl', Router::url('/', true) . 'organizations/info/' . $organizationId);
                        } else {
                            //@TODO : set error message
                            echo 'error';
                            die;
                        }
                    } else {
                        //@TODO : set error message
                        echo 'error';
                        die;
                    }

                    //save transaction
                } else {
                    $this->set("error", "general");
                    $this->set("msg", "There is some problem in processing your payment. Please try again in few minutes.");
                    $this->set('redirectUrl', Router::url('/', true) . 'subscription/btpurchase/' . urlencode($this->request->data['btog']));
                }
            }
        }
    }

    public function cancel() {
        $loggedinUser = $this->Auth->user();
        $organizationId = $this->request->data['organizationId'];

        if (!empty($organizationId)) {
            $organizationId = $this->ViewCont->decodeString($organizationId);
            $this->_ifAllowed($organizationId);

            $subscription = $this->Subscription->findByOrganizationId($organizationId);

            if ($subscription['Subscription']['payment_method'] == 'web') {
                $success = $this->Braintree->cancelSubscription($subscription['Subscription']['bt_id'], $organizationId);
            } else {
                $success = false;
            }

            if ($success) {
//                $updated = $this->Subscription->updateAll(array("cancelled" => 1, "is_deleted" => 1), array("organization_id" => $organizationId));
//                
//                $transaction = array(
//                           'organization_id' => $organizationId,
//                           'user_id' => $loggedinUser['id'],
//                           'type' => 'cancel',
//                           'user_diff' => 0,
//                           'bt_subscription_id' => $subscription['Subscription']['bt_id'],
//                           'amount' => 0,
//                           'status' => "canceled"
//                       );
//                
//                $this->Transaction->save($transaction);

                $showPurchase = true;

                if (strtotime($subscription["Subscription"]['end_date']) > time()) {
                    $showPurchase = false;
                }

                echo json_encode(array("success" => true, "og" => $organizationId, 'showPurchase' => $showPurchase, 'msg' => 'Subscription canceled successfully. Please note that your subscription will be active till next billing cycle. You can enjoy all the features of nDorse till then.'));
            } else {
                echo json_encode(array("success" => false, 'msg' => 'Unable to cancel subscription. Please try again'));
            }
        } else {
            echo json_encode(array("success" => false, 'msg' => "Organization ID is missing"));
        }

        exit;
    }

    public function update() {
        $statusConfig = Configure::read("statusConfig");
        $loggedinUser = $this->Auth->user();
        //pr($this->request->data);die;
        $organizationId = $this->request->data['updateadminsubscription']['organizationId'];

        if (empty($organizationId)) {
            echo json_encode(array("success" => false, "msg" => "Organization id is missing"));
            exit;
        }

        $organizationId = $this->ViewCont->decodeString($organizationId);

        $this->_ifAllowed($organizationId);

        $action = $this->request->data['updateadminsubscription']['action'];

        if (empty($action) && $action != "upgrade" && $action != "downgrade") {
            echo json_encode(array("success" => false, "msg" => "Please specify either upgrade or downgrade to update subscription."));
            exit;
        }

        $userCount = $this->request->data['updateadminsubscription']['userCount'];

        if (empty($userCount) || $userCount == 0) {
            echo json_encode(array("success" => false, "msg" => "Number of users cannot be 0 or empty."));
            exit;
        }

        $subscription = $this->Subscription->findByOrganizationId($organizationId);
        if ($subscription['Subscription']['is_deleted'] == 1) {
            echo json_encode(array("success" => false, "msg" => "Subscription is cancelled"));
            exit;
        }

//        if($subscription['Subscription']['bt_status'] != "settled") {
//            echo json_encode(array("success" => false, "msg" => "Subscription is not settled yet."));exit;
//        }

        $userPool = $purchasedPool = $subscription['Subscription']['pool_purchased'];

        if ($action == 'downgrade') {
            if ($userCount > $purchasedPool) {
                echo json_encode(array("success" => false, "msg" => "Number of users to downgrade cannot be more than " . $purchasedPool . " users."));
                exit;
            }

            $activeUsers = $this->UserOrganization->find("count", array("conditions" => array(
                    "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']),
                    "UserOrganization.pool_type" => 'paid',
                    "UserOrganization.organization_id" => $organizationId
            )));
//            echo $this->UserOrganization->getLastQuery();
//            echo $activeUsers;die;
            $diff = $purchasedPool - $activeUsers;
            if ($diff < $userCount) {
                echo json_encode(array("success" => false, "msg" => "Please inactivate at least " . ($userCount - $diff) . " users to downgrade the subscription by " . $userCount . " users."));
                exit;
            }
        }


//        $transactions = $this->Transaction->find("all", array("conditions" => array(
//                                                                                                        "organization_id" => $organizationId, 
//                                                                                                        "status !=" => "settled",
//                                                                                                        "bt_subscription_id" => $subscription['Subscription']['bt_id'])));
//        
//
//        
//        foreach ($transactions as $transaction) {
//            $transaction = $transaction['Transaction'];
//                if($transaction['type'] == 'upgrade') {
//                    $userPool += $transaction['user_diff'];
//                } else if($transaction['type'] == 'downgrade'){
//                    $userPool -= $transaction['user_diff'];
//                }
//        }

        $currentPool = $userPool;

        if ($action == 'upgrade') {
            $newPool = $currentPool + $userCount;
//            echo $newPool;die;
            if ($newPool > Configure::read('Braintree.max_users')) {
                if (Configure::read('Braintree.max_users') - $currentPool > 0) {
                    echo json_encode(array("success" => false, "msg" => "You can upgrade the subscription by maximum of  " . (Configure::read('Braintree.max_users') - $currentPool) . " users. Please contact <a href='mailto:" . SUPPORTEMAIL . "'>nDorse support</a>, if you want to purchase more than " . (Configure::read('Braintree.max_users') - $currentPool) . " users."));
                    exit;
                } else {
                    echo json_encode(array("success" => false, "msg" => "You cannot upgrade the subscription because the maximum limit is reached. Please contact <a href='mailto:" . SUPPORTEMAIL . "'>nDorse support</a>, if you want to purchase more subscriptions."));
                    exit;
                }
            }
        } else {
            $newPool = $purchasedPool - $userCount;
        }

        $newPrice = $newPool * $subscription['SubscriptionPlan']['rate'];

        $success = true;
        if ($subscription['Subscription']['payment_method'] == 'web') {
            $subscriptionData = array(
                'price' => $newPrice,
            );
            $result = Braintree_Subscription::update($subscription['Subscription']['bt_id'], $subscriptionData);
            $_SESSION['update'] = $result;

            if ($result->success) {
                $transaction = array(
                    'organization_id' => $organizationId,
                    'user_id' => $loggedinUser['id'],
                    'type' => $action,
                    'user_diff' => $userCount,
                    'bt_subscription_id' => $subscription['Subscription']['bt_id'],
                    'amount' => 0,
                    'status' => 'settled',
                    'balance' => abs($result->subscription->balance)
                );

                $totalTransactions = $this->Transaction->find("count", array("conditions" => array("bt_subscription_id" => $result->subscription->id)));
                ;

                $resultTrasactions = count($result->subscription->transactions);

                if ($totalTransactions < $resultTrasactions) {
                    $transaction['amount'] = $result->subscription->transactions[0]->amount;
                    $transaction['status'] = $result->subscription->transactions[0]->status;
                    $transaction['bt_transaction_id'] = $result->subscription->transactions[0]->id;
                } else {
//                        $params = array();
//                        $conditions = array();
//                        $conditions['bt_subscription_id'] = $subscription['Subscription']['bt_id'];
//                        $conditions['status'] = 'submitted_for_settlement';
//                        $params['conditions'] = $params;
//                        
//                        $unsettledCount = $this->Transaction->find("count", $params);
//                        if($unsettledCount == 0) {
//                            $this->Subscription->id = $subscription['Subscription']['id'];
//                            $this->Subscription->saveField('pool_purchased', $newPool);
//                        }
                }

                $this->Subscription->id = $subscription['Subscription']['id'];
                $this->Subscription->saveField('pool_purchased', $newPool);

                $this->Transaction->save($transaction);

                //send email on successful purchase
                //Removed as per client requirement
                /* $admins = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $organizationId, 'user_role' => 2)));

                  $subject = "nDorse Notification – The subscription had been " . strtoupper($action . "d") . "!";
                  $template = "up_down";
                  $viewVars = array("updated_by" => $loggedinUser['fname'] . " " . $loggedinUser['lname'], "action" => $action . "d", "user_count" => $userCount);

                  foreach ($admins as $admin) {
                  $viewVars['fname'] = $admin['User']['fname'];
                  $viewVars['organization'] = $admin['Organization']['name'];
                  $configVars = serialize($viewVars);
                  $to = $admin['User']['email'];
                  $emailQueue[] = array("to" => $to, "subject" => $subject, "config_vars" => $configVars, "template" => $template);
                  }

                  $this->Email->saveMany($emailQueue); */

                echo json_encode(array("success" => true, "msg" => "Subscription " . $action . "d successfully.", 'available_quota' => $newPool + FREE_POOL_USER_COUNT, 'pool_purchased' => $newPool, 'og' => $organizationId));
                exit;
            }
        }

        echo json_encode(array("success" => false, "msg" => "Unable to " . $action . " the subscription."));
        exit;
    }

    public function testWebhook() {
        $subscription = Braintree_Subscription::find('7wb322');
        pr($subscription);

        $sampleNotification = Braintree_WebhookTesting::sampleNotification(
                        Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_SUCCESSFULLY, 'jb78nr'
        );

//        pr($sampleNotification);die;

        $webhookNotification = Braintree_WebhookNotification::parse(
                        $sampleNotification['bt_signature'], $sampleNotification['bt_payload']
        );

        pr($webhookNotification);
        die;

        $webhookNotification->subscription->id;
    }

    public function test() {
        
    }

}

?>