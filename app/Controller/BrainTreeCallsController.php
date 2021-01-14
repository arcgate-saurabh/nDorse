<?php

App::import('Vendor', 'Braintree', array('file' => 'Braintree/lib' . DS . 'Braintree.php'));

class BrainTreeCallsController extends AppController {

    public $components = array("Common");
    public $uses = array("Email", "Organization", "Subscription", "Transaction", "BraintreeCustomer", "User");

    public function beforeFilter() {
        parent::beforeFilter();
        $this->layout = null;
        $this->Auth->allow("updateSubscriptionOnSettlement", "chargeSubscription", "chargeFailed", "pastDueSubscription", "subscriptionCancelled", "test");
        if (isset($this->Security) &&
                (
                $this->action == 'updateSubscriptionOnSettlement' || $this->action == 'chargeSubscription' || $this->action == 'chargeFailed' || $this->action == 'pastDueSubscription' || $this->action == 'subscriptionCancelled' || $this->action == 'test'
                )) {
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = false;
        }

        Braintree_Configuration::environment(Configure::read('Braintree.env'));
        Braintree_Configuration::merchantId(Configure::read('Braintree.merchantId'));
        Braintree_Configuration::publicKey(Configure::read('Braintree.publicKey'));
        Braintree_Configuration::privateKey(Configure::read('Braintree.privateKey'));
    }

    /*
     * Update transactions and subscriptions when transaction get settled
     */

    public function updateSubscriptionOnSettlement() {
//        $btTransaction = Braintree_Transaction::find($transaction['Transaction']['bt_transaction_id']);
//               pr($btTransaction);die;

        $params = array();
        $params['conditions'] = array("status !=" => "settled");
        $params['order'] = array("organization_id ASC", "created ASC");
        $transactions = $this->Transaction->find("all", $params);

        $organizationId = "";
        $settled = false;
        foreach ($transactions as $transaction) {
            if ($organizationId == $transaction['Transaction']['organization_id'] && $settled == false) {
                continue;
            }

            if (!empty($transaction['Transaction']['bt_transaction_id'])) {
                $btTransaction = Braintree_Transaction::find($transaction['Transaction']['bt_transaction_id']);
//               pr($btTransaction);die;
                if ($btTransaction->status == "settled") {
//                    pr($btTransaction);die;
                    $settled = true;
                } else {
                    $settled = false;
                }
            } else {
                $settled = true;
            }

            if ($settled) {
                $userDiff = $transaction['Transaction']['user_diff'];
                $btSubscriptionId = $transaction['Transaction']['bt_subscription_id'];

                if ($transaction['Transaction']['type'] == 'purchase') {
                    $updateArray = array("bt_status" => "'settled'", "status" => 1);
                } else if ($transaction['Transaction']['type'] == 'upgrade') {
                    $updateArray = array("pool_purchased" => "pool_purchased + " . $userDiff);
                } else if ($transaction['Transaction']['type'] == 'downgrade') {
                    $updateArray = array("pool_purchased" => "pool_purchased - " . $userDiff);
                }

                if (!empty($updateArray)) {
                    if ($this->Subscription->updateAll($updateArray, array('bt_id' => $btSubscriptionId))) {
                        $this->Transaction->id = $transaction['Transaction']['id'];
                        $this->Transaction->saveField("status", "settled");
                    }
                }
            }

            $organizationId = $transaction['Transaction']['organization_id'];
        }

        echo "Transactions settled down";
        exit;
    }

    /*
     * 
     */

    public function chargeSubscription() {

//        $_POST = Braintree_WebhookTesting::sampleNotification(
//            Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_SUCCESSFULLY,
//            '6ysnrm'
//        );
        if (isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {
            $webhookNotification = Braintree_WebhookNotification::parse(
                            $_POST["bt_signature"], $_POST["bt_payload"]
            );

            $message = "[Webhook Received " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . "] "
                    . "Kind: " . $webhookNotification->kind . " | "
                    . "Subscription: " . $webhookNotification->subscription->id . "\n";

            $this->log($message, "chargeSubscription");
            $this->log($webhookNotification, "chargeSubscription");

            if ($webhookNotification->kind == Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_SUCCESSFULLY) {
                $btSubscription = $webhookNotification->subscription;
                $billingPeriodEndDate = $btSubscription->billingPeriodEndDate->format('Y-m-d');
                $subscription = $this->Subscription->findByBtId($btSubscription->id);

                $organizationId = $subscription['Subscription']['organization_id'];

                if ($subscription['Subscription']['end_date'] != $billingPeriodEndDate) {
                    $lastTransactionId = $btSubscription->transactions[0]->id;
                    $transaction = $this->Transaction->findByBtTransactionId($lastTransactionId);

                    $newTransaction = array(
                        'organization_id' => $organizationId,
                        'user_id' => $subscription['Subscription']['user_id'],
                        'type' => 'renew',
                        'user_diff' => $subscription['Subscription']['pool_purchased'],
                        'bt_subscription_id' => $btSubscription->id,
                        'amount' => 0,
                        'status' => Braintree_Transaction::SETTLED,
                        'balance' => abs($btSubscription->balance)
                    );

                    if (empty($transaction)) {
                        $newTransaction['amount'] = $btSubscription->transactions[0]->amount;
                        $newTransaction['bt_transaction_id'] = $lastTransactionId;
                    }

                    $subUpdateArray = array("end_date" => "'" . $billingPeriodEndDate . "'");

                    if ($this->Subscription->updateAll($subUpdateArray, array('bt_id' => $btSubscription->id))) {
                        $this->Transaction->save($newTransaction);
                    }

                    $admins = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $organizationId, 'user_role' => 2)));

                    $subject = "nDorse Notification – Your nDorse App Subscription was successfully renewed!";
                    $template = "renew_success";
                    $viewVars = array();

                    foreach ($admins as $admin) {
                        $viewVars['fname'] = $admin['User']['fname'];
                        $viewVars['organization'] = $admin['Organization']['name'];
//                        $configVars = serialize($viewVars);
                        $to = $admin['User']['email'];

                        /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($admin['User']['id']);
                        $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /*                         * */

//                        $emailQueue[] = array("to" => $to, "subject" => $subject, "config_vars" => $configVars, "template" => $template);
                        $this->Common->sendEmail($to, $subject, $template, $viewVars);
                    }
                }
            }
        }
        echo 'charged successfully';
        exit;
    }

    /*
     * On successfully charged subscription
     */

    public function chargeSubscriptionNew() {

//        $_POST = Braintree_WebhookTesting::sampleNotification(
//            Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_SUCCESSFULLY,
//            '6ysnrm'
//        );
        if (isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {
            $webhookNotification = Braintree_WebhookNotification::parse(
                            $_POST["bt_signature"], $_POST["bt_payload"]
            );

            $message = "[Webhook Received " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . "] "
                    . "Kind: " . $webhookNotification->kind . " | "
                    . "Subscription: " . $webhookNotification->subscription->id . "\n";

            $this->log($message, "chargeSubscription");
            $this->log($webhookNotification, "chargeSubscription");

            if ($webhookNotification->kind == Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_SUCCESSFULLY) {
                $btSubscription = $webhookNotification->subscription;
                $totalTransactionCount = count($btSubscription->transactions);

                //get subscription details from db
                $subscription = $this->Subscription->findByBtId($btSubscription->id);


                if ($totalTransactionCount == 1) {
                    $type = 'purchase';
                } else {
                    $billingPeriodEndDate = $btSubscription->billingPeriodEndDate->format('Y-m-d');
                }

                $newTransaction = array(
                    'organization_id' => $subscription['Subscription']['organization_id'],
                    'user_id' => $subscription['Subscription']['user_id'],
                    'type' => $type,
                    'user_diff' => $subscription['Subscription']['pool_purchased'],
                    'bt_subscription_id' => $btSubscription->id,
                    'amount' => $transactionAmount,
                    'status' => Braintree_Transaction::SETTLED
                );
            }
        }
    }

    /*
     * Webhook to call when subscription went past due
     */

    public function chargeFailed() {
//        $_POST = Braintree_WebhookTesting::sampleNotification(
//            Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_UNSUCCESSFULLY,
//            'gvqg2m'
//        );
        if (isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {
            $webhookNotification = Braintree_WebhookNotification::parse(
                            $_POST["bt_signature"], $_POST["bt_payload"]
            );

            $message = "[Webhook Received " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . "] "
                    . "Kind: " . $webhookNotification->kind . " | "
                    . "Subscription: " . $webhookNotification->subscription->id . "\n";

            $this->log($message, "chargeFailed");
            $this->log($webhookNotification, "chargeFailed");

            if ($webhookNotification->kind == Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_UNSUCCESSFULLY) {
                //template
                //to all admins?
//                $subscription = $this->Subscription->findByBtId($webhookNotification->subscription->id);
//                $organizationId = $subscription['Subscription']['organization_id'];
//                
//                $admins = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $organizationId, 'user_role' => 2)));
//                
//                $subject = "Subscription canot renew";
//                $template = "charge_unsuccess";
//                $viewVars = array();
//                
//                foreach ($admins as $admin) {
//                    $to = $admin['User']['email'];
//                    $this->Common->sendEmail($to, $subject, $template, $viewVars);
//                }
//                $this->Common->sendEmail($to, $subject, $template, $viewVars);
            }
        }
        echo 'charge failed';
        exit;
    }

    public function subscriptionCancelled() {
//              $_POST = Braintree_WebhookTesting::sampleNotification(
//            Braintree_WebhookNotification::SUBSCRIPTION_CANCELED,
//            'jrxjtb'
//        );
        //Mail that your subscription has been canceled
        if (isset($_POST["bt_signature"]) && isset($_POST["bt_payload"])) {
            $webhookNotification = Braintree_WebhookNotification::parse(
                            $_POST["bt_signature"], $_POST["bt_payload"]
            );

            $message = "[Webhook Received " . $webhookNotification->timestamp->format('Y-m-d H:i:s') . "] "
                    . "Kind: " . $webhookNotification->kind . " | "
                    . "Subscription: " . $webhookNotification->subscription->id . "\n";

            $this->log($message, "CancelSubscription");

            if ($webhookNotification->kind == Braintree_WebhookNotification::SUBSCRIPTION_CANCELED) {
                $subscription = $this->Subscription->findByBtId($webhookNotification->subscription->id);
                $organizationId = $subscription['Subscription']['organization_id'];

//                if($subscription['Subscription']['is_deleted'] == 0 || 1) {
                $updated = $this->Subscription->updateAll(array("cancelled" => 1, "is_deleted" => 1), array("organization_id" => $organizationId));

                $customer = $this->BraintreeCustomer->findByOrganizationId($organizationId);
                $customerId = $customer['BraintreeCustomer']['customer_id'];
                $result = @Braintree_Customer::delete($customerId);
                if ($result->success) {
                    //delete from database
                    $this->BraintreeCustomer->id = $customer['BraintreeCustomer']['id'];
                    $this->BraintreeCustomer->delete();
                }

                $this->Transaction->bindModel(array('belongsTo' => array('User')));
                $canceledTransaction = $this->Transaction->find("first", array("conditions" => array("Transaction.organization_id" => $organizationId, "Transaction.status" => 'canceled')));

                $admins = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $organizationId, 'user_role' => 2)));

                if (!empty($canceledTransaction)) {
                    //Manually canceled
                    $subject = "nDorse Notification – Subscription canceled";
                    $template = "subscription_cancel_admin";
                    $viewVars = array('canceled_by' => $canceledTransaction['User']['fname'] . " " . $canceledTransaction['User']['lname']);
                } else {
                    //Canceled on auto renew
                    $transaction = array(
                        'organization_id' => $organizationId,
                        'user_id' => 0,
                        'type' => 'cancel',
                        'user_diff' => 0,
                        'bt_subscription_id' => $subscription['Subscription']['bt_id'],
                        'amount' => 0,
                        'status' => "canceled"
                    );
                    $this->Transaction->save($transaction);

                    $this->UserOrganization->updateAll(array("status" => 0), array("organization_id" => $organizationId, "pool_type" => 'paid'));

                    //send email on cancelation
                    $subject = "nDorse Notification – The Payment did not go through! Your subscription has been terminated";
                    $template = "subscription_cancel";
                    $viewVars = array();
                }

                foreach ($admins as $admin) {
                    $viewVars['fname'] = $admin['User']['fname'];
                    $viewVars['organization'] = $admin['Organization']['name'];

                    /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode( $admin['User']['id']);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $viewVars["pathToRender"] = $pathToRender;
                    /**/

                    $to = $admin['User']['email'];
                    $this->Common->sendEmail($to, $subject, $template, $viewVars);
                }
//                }
            }
        }
        echo "Subscription cancelled";
        exit;
    }

    public function test() {
        $organizationId = 663;
        $this->Transaction->bindModel(array('belongsTo' => array('User')));
        $canceledTransaction = $this->Transaction->find("all", array("conditions" => array("Transaction.organization_id" => $organizationId, "Transaction.status" => 'canceled')));

        pr($canceledTransaction);
        die;
    }

}
