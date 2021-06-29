<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));

class AjaxController extends AppController {

    var $name = 'Ajax';
    public $helpers = array('Html', 'Form');
    public $components = array("Common", 'Session', 'Image', "Braintree");
    var $uses = array("UserOrganization");

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('submitfaqform', 'isLoggedIn');
    }

    function states() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->request->is('post')) {
            $requestData = $this->request->data;
//pr($requestData);exit;
            $this->loadModel('State');
            $countryId = $requestData['countryId'];

//$allCourse = $this->Chapter->find('list', array('fields' => array('Chapter.id', 'Chapter.name')));
            $filterStates = $this->State->find('list', array('conditions' => array('State.country_id' => $countryId)));

            $json = json_encode($filterStates);
            $this->response->body($json);
        }
    }

    function changeorgstatus() {
        try {
            $orgstatus = "";
            $loadmodel = $this->request->data['file'];
            $this->loadModel($loadmodel);
            $this->autoRender = false;
            $this->layout = 'ajax';
            $targetId = $this->request->data['targetid'];
            $status = ($this->request->data['status']) ? 0 : 1;
            //========process to mail all users that wether the process is activated or deactivated;
            if ($loadmodel == "Organization") {
                $this->loadModel("UserOrganization");
                $this->loadModel("User");
                $this->loadModel("Email");
                if ($status == 0) {
                    $orgstatus = "Deactivated";
                } else if ($status == 1) {
                    $orgstatus = "Activated";
                }
                $org_name = $this->Organization->field("name", array("id" => $targetId));
//                $this->Common->statusmailingreport($targetId, $org_name, $orgstatus);
            }

            if ($loadmodel == "Organization") {
                $this->loadModel('Subscription');
                $this->Organization->id = $targetId;
                $result = $this->Organization->saveField('status', $status, false);
                // delete subscription
                $is_purchase = 0;
                if ($status == 1) {
                    $data = $this->Subscription->findByOrganizationId($targetId);
                    if (!empty($data["Subscription"]) && $data["Subscription"]["id"] > 0 && $data["Subscription"]["payment_method"] == "ndorse") {
                        $is_purchase = 1;
                        $subscription_id = $data["Subscription"]["id"];
                        $this->Subscription->delete($subscription_id);
                        $this->UserOrganization->UpdateAll(
                                array("status" => 0), array("organization_id" => $targetId, "pool_type" => "paid")
                        );
                    } elseif (!empty($data["Subscription"]) && $data["Subscription"]["id"] > 0 && $data["Subscription"]["payment_method"] == "web") {

                        $this->loadModel('Transaction');
                        //print_r($data['Subscription']);
                        // $this->Braintree->cancelSubscription($data['Subscription']['bt_id']);
                        $encodeid = base64_encode(base64_encode($targetId));
                        $this->Braintree->cancelSubscription($data['Subscription']['bt_id'], $encodeid);
                        $is_purchase = 1;
                    }
                }
                // end 
            }

            if ($loadmodel == "User") {
                $this->loadModel("Organization");
                $this->User->id = $targetId;
                $result = $this->User->saveField('status', $status, false);
                $orgidarray = $this->Organization->find("all", array("fields" => array("id"), "conditions" => array("admin_id" => $targetId)));
                if ($status == 0) {
                    $orgstatus = "Deactivated";
                } else if ($status == 1) {
                    $orgstatus = "Activated";
                }
                foreach ($orgidarray as $orgdetail) {
                    $targetorgid = $orgdetail["Organization"]["id"];
                    $org_name = $this->Organization->field("name", array("id" => $targetorgid));
//                    $this->Common->statusmailingreport($targetorgid, $org_name, $orgstatus);
                }

                $this->Organization->updateAll(
                        array('status' => $status), //fields to update
                        array('admin_id' => $targetId)  //condition
                );
//$this->UserOrganization->save($val, false);
            }
            $loggeduser = $this->Auth->User();
            $encodeid = base64_encode(base64_encode($targetId));
            echo json_encode(array("status" => $status, "role" => $loggeduser["role"], "encodeid" => $encodeid, "is_purchase" => $is_purchase));
        } catch (Exception $e) {
            echo json_encode(array("status" => "true", "error" => "true", "error_message" => "Not able to Save", "msg" => $e->getMessage()));
        }
        exit();
    }

    function deleteorgstatus() {
        try {
            $this->layout = 'ajax';
            $this->render = false;
            $this->loadModel('Organization');
            $this->loadModel('UserOrganization');
            $this->loadModel('Subscription');
            $targetid = $this->request->data['targetid'];
            $orgstatus = "Deleted";
            // terminate subscription
            $data = $this->Subscription->findByOrganizationId($targetid);
            if (!empty($data["Subscription"]) && $data["Subscription"]["id"] > 0 && $data["Subscription"]["payment_method"] == "ndorse") {
                $subscription_id = $data["Subscription"]["id"];
                $this->Subscription->delete($subscription_id);
            } elseif (!empty($data["Subscription"]) && $data["Subscription"]["id"] > 0 && $data["Subscription"]["payment_method"] == "web") {
                $this->Braintree->cancelSubscription($data['Subscription']['bt_id'], $targetid);
            }
            // end
            $org_name = $this->Organization->field("name", array("id" => $targetid));
// mailing to other users including admin about the status
//            $this->Common->statusmailingreport($targetid, $org_name, $orgstatus);
            $this->Organization->id = $targetid;
            $this->Organization->saveField('status', 2, 'false');
            $this->UserOrganization->UpdateAll(
                    array("status" => 2), array("organization_id" => $targetid)
            );

            echo json_encode(array("message" => "ID " . $targetid . " Deleted"));
        } catch (Exception $e) {
            echo json_encode(array("message" => "ID " . $targetid . " Deleted"));
        }
        exit();
    }

    function deleteuserstatus() {
        try {
            $this->loadModel("User");
            $this->loadModel("Organization");
            $this->layout = 'ajax';
            $this->autoRender = false;
            $targetId = $this->request->data['targetid'];
            $this->User->id = $targetId;
            $this->User->saveField('status', 2, 'false');
            $this->Organization->UpdateAll(
                    array("status" => 2), array("admin_id" => $targetId)
            );
            echo json_encode(array("message" => "ID " . $targetId . " Deleted"));
            exit();
        } catch (Exception $e) {
            echo json_encode(array("message" => $e));
        }
    }

    function setdaisyuserstatus() {
        try {
            $this->loadModel("User");
            $this->layout = 'ajax';
            $this->autoRender = false;
            $authUser = $this->Auth->User();
            $userID = $this->request->data['userid'];
            $daisy_status = $this->request->data['daisy_status'];
            $this->User->id = $userID;
            $this->User->saveField('daisy_enabled', $daisy_status, 'false');
            $this->Session->write('Auth.User', $authUser);
            echo json_encode(array("message" => "ID " . $userID . " daisy updated"));
            exit();
        } catch (Exception $e) {
            echo json_encode(array("message" => $e));
        }
    }

    function deleteendorserstatus() {
        try {
            $this->loadModel("UserOrganization");
            $this->loadModel("Invite");
            $this->layout = 'ajax';
            $this->autoRender = false;
            $targetId = $this->request->data['targetid'];
            $actstatus = "Deleted";
            $this->UserOrganization->bindModel(array(
                'belongsTo' => array(
                    'Organization' => array(
                        'className' => 'Organization',
                    ),
                )
            ));
            $orgdata = $this->UserOrganization->findById($targetId);
            $org_name = $orgdata["Organization"]["name"];
            $orgid = $orgdata["Organization"]["id"];
            $username = $orgdata["User"]["email"];
            $fname = $orgdata["User"]["fname"];
            $loggeduser = $this->Auth->User();
            $inviteid = $this->Invite->field("id", array("email" => $username, "organization_id" => $orgid));
            $this->Invite->delete($inviteid);
            //========mailing on change status
//            $this->Common->changeuserstatusmail($org_name, $username, $fname, $actstatus, $loggeduser);

            $this->UserOrganization->id = $targetId;
            $this->UserOrganization->saveField('status', 2, 'false');
            echo json_encode(array("message" => "ID " . $targetId . " Deleted"));
            exit();
        } catch (Exception $e) {
            echo json_encode(array("message" => $e));
        }
    }

    function endorserrolechange() {
        try {
            $this->loadModel("UserOrganization");
            $this->loadModel("User");
            $this->loadModel("Organization");
            $this->loadModel("Email");
            $this->layout = 'ajax';
            $this->autoRender = false;
            $userid = $this->request->data["userid"];
            $org = $this->request->data["orgid"];
            $newrole = $this->request->data["changeTo"];
            $this->UserOrganization->bindModel(array(
                'belongsTo' => array(
                    'Organization' => array(
                        'className' => 'Organization',
                    ),
                )
            ));

            $targetid = $this->UserOrganization->field('id', array('organization_id' => $org, 'user_id' => $userid));
            $orgdata = $this->UserOrganization->findById($targetid);

//================mail to organization owner

            $adminuserid = $orgdata["Organization"]["admin_id"];

            $adminemail = $this->User->field("email", array("id" => $adminuserid));
            $admindetail = $this->User->findById($adminuserid);

            $adminfname = $orgdata["User"]["fname"];

            //Base 64 decoded added by babulal @10-sept-2018
//            $adminfname = base64_decode($orgdata["User"]["fname"]);

            $adminemail = "admin@ndorse.com";
            $useremail = $orgdata["User"]["email"];
            $orgname = $orgdata["Organization"]["name"];
            if ($newrole == 2) {
                // save role as admin in user table if not
                $subject = "nDorse Notification -- Admin control granted to a user";
                $message = "This is to notify you that administrator control has been granted to the following username: " . $useremail . ". If you have not initiated that or it is not required then please contact nDorse team at  <a href=mailto:support@ndorse.net>support@ndorse.net</a>.";
            } else if ($newrole == 3) {
                $subject = "nDorse Notification -- Admin control revoked from a user";
                $message = "This is to notify you that administrator control has been revoked from the following username: " . $useremail . ". If you have not initiated that or it is not required then please contact nDorse team at  <a href=mailto:support@ndorse.net>support@ndorse.net</a>.";
            } else if ($newrole == 6) {
                // save role as admin in user table if not
                $subject = "nDorse Notification -- nDorse Elite control granted to a user";
                $message = "This is to notify you that elite control has been granted to the following username: " . $useremail . ". If you have not initiated that or it is not required then please contact nDorse team at  <a href=mailto:support@ndorse.net>support@ndorse.net</a>.";
            }
            $viewVars = array("fname" => $adminfname, "message" => $message, "org_id" => $org, "role" => $newrole, "user_id" => $userid);
            /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
            $userIdEncrypted = base64_encode($userid);
            $rootUrl = Router::url('/', true);
            //$rootUrl = str_replace("http", "https", $rootUrl);
            //Added by saurabh on 23/06/2021
            //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
            $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
            $viewVars["pathToRender"] = $pathToRender;
            /*             * * */

            $configVars = serialize($viewVars);
            $emailsend[] = array("to" => $adminemail, "subject" => $subject, "config_vars" => $configVars, "template" => "org_admin_access_action");
//$this->Email->save($emailsend);
//================end mail to organization admin
//================mail to organization user
            //if ($newrole == 2) {
            //    $subject = "nDorse Notification -- Admin control granted to a user";
            //    $message = "This is to notify you that administrator control has been granted to your following username: " . $useremail . " for " . $orgname . " organization.";
            //} else {
            //    $subject = "nDorse Notification -- Admin control revoked from a user";
            //    $message = "This is to notify you that administrator control has been granted to your following username: " . $useremail . " for " . $orgname . " organization.";
            //}
            //$viewVars = array("fname" => $adminfname, "message" => $message);
            //$configVars = serialize($viewVars);           //$emailsend[] = array("to" => $useremail, "subject" => $subject, "config_vars" => $configVars, "template" => "org_admin_access_action");
//================end mail to organization user
//            $this->Email->saveMany($emailsend);

            $this->UserOrganization->id = $targetid;
            $this->UserOrganization->saveField('user_role', $newrole, 'false');

            $this->User->id = $userid;
            $this->User->saveField('role', $newrole, 'false');

            echo json_encode(array("message" => "Id " . $userid . " has its role changed to " . $newrole));
            exit();
        } catch (Exception $e) {
            echo json_encode(array("message" => $e));
        }
    }

    function changeuserstatus() {
        try {
            $this->loadModel("UserOrganization");
            $this->loadModel("Organization");
            $this->layout = 'ajax';
            $this->autoRender = false;
//        $userid = $this->request->data["userid"];
//        $org = $this->request->data["orgid"];
// check pool available or not for activated users
// check user pool
            $ustatus = $this->request->data["checkedvalue"];
            $targetid = $this->request->data["targetid"];

            if ($ustatus == 0) {
                $actstatus = "Inactivated";
            } else if ($ustatus == 3) {
                $actstatus = "Evaluated";
            } else {
                $actstatus = "Activated";
            }
            $this->UserOrganization->bindModel(array(
                'belongsTo' => array(
                    'Organization' => array(
                        'className' => 'Organization',
                    ),
                )
            ));
            $orgdata = $this->UserOrganization->findById($targetid);

            $org_name = $orgdata["Organization"]["name"];
            $username = $orgdata["User"]["email"];
            $fname = $orgdata["User"]["fname"];
            //Added by Babulal @10-sept-2018 decode fname
//            $fname = base64_decode($orgdata["User"]["fname"]);
            $loggeduser = $this->Auth->User();
            //
            if ($orgdata["UserOrganization"]["status"] == 0) {
                $statusConfig = Configure::read("statusConfig");
                $this->loadModel('Subscription');

                $org_id = $orgdata["Organization"]["id"];
                $data = $this->Subscription->findByOrganizationId($org_id);
                $poolcount = 0;

                if (!empty($data["Subscription"])) {

                    $end_date = strtotime(date("Y-m-d 23:23:59", strtotime($data["Subscription"]["start_date"])));


                    if ($data["Subscription"]["organization_id"] == $org_id && $data["Subscription"]["cancelled"] != 1 && $end_date >= time()) {

                        $poolcount = $data["Subscription"]["pool_purchased"];
                    }
                }

                $poolcount += 10;
                $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));

                $params['fields'] = array("COUNT(UserOrganization.user_id) as count");
                $userOrgStats = $this->UserOrganization->find("all", $params);

                $usercount = $userOrgStats[0][0]["count"];

                $availablecount = $usercount + 1;
                //   echo $poolcount."  ".$availablecount;
                if ($availablecount > $poolcount) {
                    echo json_encode(array("status" => 0, "user_id" => $orgdata["UserOrganization"]["user_id"], "message" => "not pool available"));
                    exit();
                }
            }
            //
//========mailing on change status
//            $this->Common->changeuserstatusmail($org_name, $username, $fname, $actstatus, $loggeduser);
            $this->UserOrganization->id = $targetid;
            $this->UserOrganization->saveField('status', $ustatus, 'false');
            echo json_encode(array("status" => 1, "user_id" => $orgdata["UserOrganization"]["user_id"], "message" => "Id " . $targetid . " has its role changed to " . $ustatus));
            exit();
        } catch (Exception $e) {
            echo json_encode(array("message" => $e));
        }
    }

    public function userOrgActionFromAdmin() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $statusConfig = Configure::read("statusConfig");
//        pr($this->request->data); exit;
        if (isset($this->request->data['user_id'])) {
            $loggedInUser = $this->Auth->user();
//            pr($loggedInUser); exit;
            $org_id = $this->request->data['oid'];
            $useraccess = $this->request->data['status'];
            $status = $statusConfig[$useraccess];
            $user_id = $this->request->data['user_id'];
            $pool_type = "free";
            $this->loadModel('Subscription');
            $this->loadModel('UserOrganization');
            $this->loadModel('DefaultOrg');
            $this->loadModel('User');
            $this->loadModel('Invite');

            $subscriptiondata = $this->Subscription->findByOrganizationId($org_id);
            if (in_array($status, array(1, 3))) {
                $available_pool = 10;
// get subscription info

                if (!empty($subscriptiondata) && $subscriptiondata["Subscription"]["status"] == 1) {
                    $available_pool += $subscriptiondata["Subscription"]["pool_purchased"];
                }
//$params =array();
//$params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
//$params['fields'] = array("COUNT(UserOrganization.user_id) as count");
//$userOrgStats = $this->UserOrganization->find("all", $params);
// $usercount = $userOrgStats[0][0]["count"];
//  $ucount = $usercount+1;
//
                $params = array();
// $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['inactive'], $statusConfig['active'], $statusConfig['eval']));
                $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                $params['group'] = 'pool_type';
                $params['fields'] = array("UserOrganization.pool_type", "COUNT(UserOrganization.user_id) as count");
                $userOrgStats = $this->UserOrganization->find("all", $params);

                $freeCount = 0;
                $paidCount = 0;

                foreach ($userOrgStats as $stats) {
                    if ($stats['UserOrganization']['pool_type'] == 'free') {
                        $freeCount = $stats[0]['count'];
                    } else {
                        $paidCount = $stats[0]['count'];
                    }
                }

                $usercount = $freeCount + $paidCount;
                $ucount = $usercount + 1;
//
//User cannot be set to evaluation mode since subscription limit is over
                $modemsg = "Active";
                if ($status == 3) {
                    $modemsg = "Evaluation mode";
                }

                if ($ucount > $available_pool) {
                    $msg = "No quota available.";
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Subscription limit exceeded! To add user(s) to " . $modemsg . "; purchase or upgrade subscription to activate added user(s) using Admin Portal on www.ndorse.net or by contacting NDORSE LLC at support@ndorse.net."),
                        '_serialize' => array('result')
                    ));
                    return;
                }
                $pool_type = "'free'";
//if($ucount>10){
//  $pool_type ="'paid'";   
//}
                if ($freeCount >= 10) {
                    $pool_type = "'paid'";
                }
            }

//
            $updatemsg = "User status updated successfully.";
            if (in_array($status, array(0, 2))) {
                $pool_type = "'paid'";
                if (!empty($subscriptiondata) && $subscriptiondata["Subscription"]["status"] == 1) {
                    $delmsg = "Inactivated";
                    if ($status == 2) {
                        $delmsg = "Deleted";
                    }

                    $updatemsg = "User was successfully " . $delmsg . ". Go to Admin Portal to downgrade subscription.";
                }
            }
            $this->UserOrganization->updateAll(
                    array('UserOrganization.status' => $status, 'UserOrganization.pool_type' => $pool_type), array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_id' => $user_id)
            );
            $userstatus = 1;
            if ($useraccess == "inactive" || $useraccess == "eval" || $useraccess == "deleted") {
                $userstatus = 0;
            }
            $defaultorg = $this->DefaultOrg->find("first", array("conditions" => array("user_id" => $user_id)));
            if (!empty($defaultorg)) {
                $this->DefaultOrg->updateAll(
                        array('DefaultOrg.status' => $userstatus), array('DefaultOrg.organization_id' => $org_id, 'DefaultOrg.user_id' => $user_id)
                );
            } elseif ($useraccess == "active") {
                $userorgs = $this->UserOrganization->find("all", array('joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
                        array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id and organizations.status=1'))),
                    'conditions' => array('UserOrganization.user_id' => $user_id),
                    'fields' => array('organizations.id')
                ));
                if (count($userorgs) == 1) {
                    $newdefaultOrg = array("organization_id" => $org_id, "user_id" => $user_id);
                    $this->DefaultOrg->save($newdefaultOrg);
                }
            }
            $userinfo = $this->UserOrganization->find("all", array(
                'joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
                    array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))),
                'conditions' => array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_id' => $user_id),
                'fields' => array('UserOrganization.user_id,UserOrganization.status,UserOrganization.user_role,organizations.name,organizations.admin_id')
            ));
// print_r($userinfo);
            $userid = array();
            foreach ($userinfo as $userval) {
                $userid[] = $userval["UserOrganization"]["user_id"];
                $userid[] = $userval["organizations"]["admin_id"];
            }

            $userdetailid = array();
            $userdetailsql = $this->User->find("all", array("conditions" => array("id" => $userid), "fields" => array("id", "fname", "lname", "image", "email")));
            foreach ($userdetailsql as $val) {
                $userdetailid[$val["User"]["id"]] = array("fname" => $val["User"]["fname"], "image" => $val["User"]["image"], "lname" => $val["User"]["lname"], "email" => $val["User"]["email"]);
                //Upper commented & added by babulal prasad @10-sept-2018 to decode user's encoded data
//                $userdetailid[$val["User"]["id"]] = array("fname" => base64_decode($val["User"]["fname"]), "image" => $val["User"]["image"], "lname" => base64_decode($val["User"]["lname"]), "email" => base64_decode($val["User"]["email"]));
            }
            if ($useraccess == "deleted") {

                $this->Invite->updateAll(
                        array('Invite.is_deleted' => 1), array('Invite.organization_id' => $org_id, 'Invite.email' => $userdetailid[$user_id]["email"])
                );
            } $userdetails = array();
            $emailQueue = array();
            foreach ($userinfo as $userval) {
// print_r($userval);exit;
                $euser = $userdetailid[$userval["UserOrganization"]["user_id"]];
                $orguser = $userdetailid[$userval["organizations"]["admin_id"]];
// $orguser = $loggedInUser['fname']." ".$loggedInUser['lname'];
                if ($euser["image"] != "") {
                    $rootUrl = Router::url('/', true);
                    //$rootUrl = str_replace("http", "https", $rootUrl);
                    //Added by saurabh on 23/06/2021
                    //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
                    $euser["image"] = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $euser["image"];
                }
                $userval['UserOrganization']['status'] = array_search($userval['UserOrganization']['status'], $statusConfig);
                ;
                $euser["id"] = $userval["UserOrganization"]["user_id"];
                $userdetails = array_merge($euser, $userval['UserOrganization']);

                $organization_name = $userval['organizations']['name'];
                $actstatus = "";
                if ($useraccess == "inactive") {
                    $actstatus = "deactivated";
                } elseif ($useraccess == "active") {
                    $actstatus = "activated";
                } elseif ($useraccess == "eval") {
                    $actstatus = "evaluated";
                } elseif ($useraccess == "deleted") {
                    $actstatus = "deleted";
                    $subject = "Your nDorse login has been deleted";
                }
                if ($useraccess != "deleted") {
                    $subject = "Your nDorse login " . $actstatus . " by your administrator";
                }
                $viewVars = array("org_name" => $organization_name, "status" => $actstatus, "username" => $euser['email'], "fname" => $euser["fname"], "user_name" => trim($euser["fname"] . " " . $euser["lname"]), "admin_name" => trim($loggedInUser['fname'] . " " . $loggedInUser['lname']));
                //Upper commented & added by babulal prasad @10-sept-2018 to decode user's encoded data
//                $viewVars = array("org_name" => $organization_name, "status" => $actstatus, "username" => base64_decode($euser['email']), "fname" => base64_decode($euser["fname"]), "user_name" => base64_decode(trim($euser["fname"]) . " " . base64_decode($euser["lname"])), "admin_name" => trim(base64_decode($loggedInUser['fname']) . " " . base64_decode($loggedInUser['lname'])));

                /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                $userIdEncrypted = base64_encode($euser["id"]);
                $rootUrl = Router::url('/', true);
                //$rootUrl = str_replace("http", "https", $rootUrl);
                //Added by saurabh on 23/06/2021
                //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
                $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                $viewVars["pathToRender"] = $pathToRender;
                /*                 * */

                $configVars = serialize($viewVars);
                $emailQueue[] = array("to" => $euser['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "org_action");
            }
            unset($userdetails["email"]);
//            $this->Email->saveMany($emailQueue); //Removed as per client requirement
            echo json_encode(array(
                'result' => array("status" => true
                    , "msg" => $updatemsg, 'data' => $userdetails),
                '_serialize' => array('result')
            ));
            exit();
        } else {
            echo json_encode(array(
                'result' => array("status" => false
                    , "msg" => "user_id is missing in request"),
                '_serialize' => array('result')
            ));
            exit();
        }
    }

    /** Modified by : Babulal Prasad by 16-May-2019
     * 
     * @modified - 21jun2021 by saurabh
     * Desc : New fields(daisy enabled,status) added and if user exist then update the information
     */

    function uploadbulkuserscsv() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $this->loadModel("Subscription");
        $this->loadModel("User");
        $this->loadModel("OrgDepartment");
        $this->loadModel("OrgJobTitle");
        $this->loadModel("Entity");
        $this->loadModel("Invite");
        $this->loadModel("Email");
        $this->loadModel("Subscription");

        $this->loadModel("OrgSubcenter");

        $roleList = $this->Common->setSessionRoles();
        $filedata = $this->request->data["targetdata"];
        //pr($filedata); exit;
        //Extract fields from filedata

        $employeeId = $filedata[0];
        $fname = $filedata[1];
        $lname = $filedata[2];
        $suffix = $filedata[3];
        $department = $filedata[4];
        $jobtitle = $filedata[5];
        $email = $filedata[6];
        $username = $filedata[6];
        $mobile = $filedata[7];
        $status = (int) $filedata[8] == 2 ? 3 : ($filedata[8] == 1 ? 1 : 0);
        $sendInvitation = $filedata[9];
        $subOrg = $filedata[10];
        $daisyEnabled = $filedata[11];
        $subCenterName = $filedata[12];


        $orgId = $this->request->data["orgId"];
        $orgName = $this->request->data["orgName"];
        $orgCode = $this->request->data["orgcode"];
        $error = false;

        if ((!filter_var($email, FILTER_VALIDATE_EMAIL)) || $email == "") {
            $queryresult = "Check Email";
            $idvalue = "";
            $status = "";
        } else if (trim($fname == "") || trim($lname == "")) {
            $queryresult = "First Name or Last Name is Empty";
            $idvalue = "";
            $status = "";
        } else if (trim($employeeId == "")) {
            $queryresult = "Employee ID is Empty";
            $idvalue = "";
            $status = "";
        } else {
            $queryresultUpdateEmail = "";
//          $userExist = $this->User->findByEmail($email);
            $userExist = $this->User->findByEmployeeId($employeeId);
            
            /**
            *Added by saurabh for checking if sub-center exists or not in org_subcenters table.
            */
            $subcenterExist = $this->OrgSubcenter->find("first", array("conditions" => array("OrgSubcenter.long_name" => $subCenterName, "OrgSubcenter.org_id" => $orgId, "OrgSubcenter.status" => 1)));
            
            if (trim($subCenterName)!= "" && empty($subcenterExist))
            {
                $queryresult = "Sub center does not exist";
                $idvalue = "";
                $status = "";
                $result = array("id" => $idvalue, "result" => $queryresult, "status" => $status);
                echo json_encode($result);
                exit();
            }
            //ends here
            
//            pr($userExist); exit;
            if (!empty($userExist)) {
                $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $userExist['User']['id'], "UserOrganization.organization_id" => $orgId, "UserOrganization.status !=" => 2)));
            } else {
                $userOrganization = array();
            }

            if (!empty($userOrganization)) {

                $user = $userExist['User'];
                $template = "invitation_admin_existing";
                $queryresult = "Updated";


//                pr($filedata);
//                pr($userOrganization);
//                pr($userExist);

                /* Update user data
                 * Added by Babulal Prasad @20MAY2019
                 */
                $user = array();

                $UserID = $userExist['User']['id'];
                $user['id'] = $UserID;
                $user['daisy_enabled'] = $daisyEnabled;
                $user['fname'] = $fname;
                $user['lname'] = $lname;
                $queryresultUpdateEmail = '';

                if ($userExist['User']['email'] != $email) {
                    $duplicateEmail = $this->User->find("first", array("conditions" => array("email" => $email, "id !=" => $UserID)));
                    if ($duplicateEmail) {
                        $queryresultUpdateEmail = "Email not updated";
                    } else {
                        $user['email'] = $email;
                        $user['username'] = $email;
                    }
                }


                $this->User->id = $UserID;

                $this->User->setValidation('edit');
                $this->User->set($user);
                if ($this->User->validates()) {
                    if ($this->User->save($user, array('id' => $UserID))) {
                        $user['id'] = $this->User->id;
                        $template = "invitation_admin_existing";
                        $queryresult = ($queryresultUpdateEmail != '') ? "User updated but " . $queryresultUpdateEmail : $queryresult;

//                        $queryresult = "Updated";
                    } else {
                        //Error on saving
                        $queryresult = "Error in saving user";
                        $idvalue = "";
                        $status = "";
                        $error = true;
                    }
                } else {
                    //Error on validation
                    $errors = $this->User->validationErrors;
                    $errormsg = "";
                    foreach ($errors as $error) {
                        $errormsg .= $error[0] . "\n";
                    }
                    $queryresult = $errormsg;
                    $idvalue = "";
                    $status = "";
                    $error = true;
                }
//                pr($user);exit;
                //Department set
                if (!empty($department)) {
                    $deptRecord = $this->OrgDepartment->find("first", array("conditions" => array("name" => $department, "organization_id" => $orgId)));

                    if (empty($deptRecord)) {
                        $deptArray = array(
                            "organization_id" => $orgId,
                            "name" => $department,
                            "from_master" => 0,
                            "status" => 1,
                            "updated" => date('Y-m-d H:i:s'),
                        );
                        $this->OrgDepartment->create();
                        $this->OrgDepartment->save($deptArray);
                        $departmentId = $this->OrgDepartment->getLastInsertId();
                    } else {
                        $departmentId = $deptRecord['OrgDepartment']['id'];
                    }
                } else {
                    $departmentId = 0;
                }

                //JobTitle set

                if (!empty($jobtitle)) {

                    $jobtitleRecord = $this->OrgJobTitle->find("first", array("conditions" => array("title" => $jobtitle, "organization_id" => $orgId)));
//                    echo $this->OrgJobTitle->getLastQuery();

                    if (empty($jobtitleRecord)) {
                        $jobtitleArray = array(
                            "organization_id" => $orgId,
                            "title" => $jobtitle,
                            "from_master" => 0,
                            "status" => 1
                        );

                        $this->OrgJobTitle->create();
                        $this->OrgJobTitle->save($jobtitleArray);
//                         echo $this->OrgJobTitle->getLastQuery();
                        $jobtitleId = $this->OrgJobTitle->getLastInsertId();
                    } else {
                        $jobtitleId = $jobtitleRecord['OrgJobTitle']['id'];
                    }
                } else {
                    $jobtitleId = "";
                }

                //org_subcenters id saurabh
                $subcenterId = 0;   
                if($subcenterExist) 
                {
                    if (!empty($subCenterName)) {
                        $subcenterRecord = $this->OrgSubcenter->find("first", array("conditions" => array("long_name" => $subCenterName, "org_id" => $orgId, "status" => 1))); //check active status also
                        if (empty($subcenterRecord)) {
                            $subcenterArray = array(
                                "org_id" => $orgId,
                                "long_name" => $subCenterName,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->OrgSubcenter->create();
                            $this->OrgSubcenter->save($subcenterArray);
                            $subcenterId = $this->OrgSubcenter->getLastInsertId();
                        } else {
                            $subcenterId = $subcenterRecord['OrgSubcenter']['id'];
                        }
                    } else {
                        $subcenterId = 0;
                    }
                }
                    
                $UserOrgID = $userOrganization['UserOrganization']['id'];

                $newUserOrganization = array(
                    "department_id" => $departmentId,
                    "job_title_id" => $jobtitleId,
                    "status" => $status,
                    "subcenter_id" => $subcenterId, //saurabh
                );

//                pr($newUserOrganization); exit;
                $saved = $this->UserOrganization->updateAll($newUserOrganization, array('UserOrganization.id' => $UserOrgID));

                $idvalue = $UserID;
                /* -------------------- */
                //exit;
                //$error = true;
            } else {

                $statusConfig = Configure::read("statusConfig");

                $sendInvite = 0;
                if ($sendInvitation == 1 && $status == 1) {
                    $sendInvite = 1;
                }

                if (empty($userExist)) {
                    $user = array();
                    $user['fname'] = $fname;
                    $user['lname'] = $lname;
                    $user['email'] = $email;
                    $user['role'] = array_search('endorser', $roleList);
                    $user['secret_code'] = $this->getSecretCode("user");
                    $user['username'] = $username;
                    $user['suffix'] = $suffix;
                    $user['employee_id'] = $employeeId;
                    $user['last_app_used'] = "NOW()";
                    $user['password'] = $this->Common->randompasswordgenerator(8);
                    $user['daisy_enabled'] = $daisyEnabled;
                    $user['sub_center_name_row'] = $subCenterName;

                    $this->User->setValidation('register');



                    $this->User->set($user);
                    if ($this->User->validates()) {
                        if ($this->User->save()) {
                            $user['id'] = $this->User->id;
                            $template = "invitation_admin";
                            $queryresult = "Inserted";
                        } else {
                            //Error on saving
                            $queryresult = "Error in saving user";
                            $idvalue = "";
                            $status = "";
                            $error = true;
                        }
                    } else {
                        //Error on validation
                        $errors = $this->User->validationErrors;
                        $errormsg = "";
                        foreach ($errors as $error) {
                            $errormsg .= $error[0] . "\n";
                        }
                        $queryresult = $errormsg;
                        $idvalue = "";
                        $status = "";
                        $error = true;
                    }
                } else {
                    $user = $userExist['User'];
                    $template = "invitation_admin_existing";
                    $queryresult = ($queryresultUpdateEmail != '') ? "Updated & " . $queryresultUpdateEmail : $queryresult;
//                    $queryresult = "Updated";
                }



                //        $status = $this->request->data['status'];

                if (!$error) {
                    if ($status == $statusConfig['active'] || $status == $statusConfig['eval']) {
                        $statusFields = $this->Common->getNewUserOrgFields($orgId, $status);
                    } else {
                        $statusFields = array("poolType" => "paid", "status" => 0);
                    }

                    //Department set
                    if (!empty($department)) {
                        $deptRecord = $this->OrgDepartment->find("first", array("conditions" => array("name" => $department, "organization_id" => $orgId)));

                        if (empty($deptRecord)) {
                            $deptArray = array(
                                "organization_id" => $orgId,
                                "name" => $department,
                                "from_master" => 0,
                                "status" => 1,
                                "updated" => date('Y-m-d H:i:s'),
                            );
                            $this->OrgDepartment->create();
                            $this->OrgDepartment->save($deptArray);
                            $departmentId = $this->OrgDepartment->getLastInsertId();
                        } else {
                            $departmentId = $deptRecord['OrgDepartment']['id'];
                        }
                    } else {
                        $departmentId = "";
                    }

                    //JobTitle set
                    if (!empty($jobtitle)) {
                        $jobtitleRecord = $this->OrgJobTitle->find("first", array("conditions" => array("title" => $jobtitle, "organization_id" => $orgId)));
                        if (empty($jobtitleRecord)) {
                            $jobtitleArray = array(
                                "organization_id" => $orgId,
                                "title" => $jobtitle,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->OrgJobTitle->create();
                            $this->OrgJobTitle->save($jobtitleArray);
                            $jobtitleId = $this->OrgJobTitle->getLastInsertId();
                        } else {
                            $jobtitleId = $jobtitleRecord['OrgJobTitle']['id'];
                        }
                    } else {
                        $jobtitleId = "";
                    }

                    //Department set
                    if (!empty($subOrg)) {
                        $deptRecord = $this->Entity->find("first", array("conditions" => array("name" => $subOrg, "organization_id" => $orgId)));
                        if (empty($deptRecord)) {
                            $subOrgArray = array(
                                "organization_id" => $orgId,
                                "name" => $subOrg,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->Entity->create();
                            $this->Entity->save($subOrgArray);
                            $subOrgId = $this->Entity->getLastInsertId();
                        } else {
                            $subOrgId = $deptRecord['Entity']['id'];
                        }
                    } else {
                        $subOrgId = 0;
                    }

                    //org_subcenters id saurabh
                    $subcenterId = 0;
                    if($subcenterExist) 
                    {
                        if (!empty($subCenterName)) {
                            $subcenterRecord = $this->OrgSubcenter->find("first", array("conditions" => array("long_name" => $subCenterName, "org_id" => $orgId, "status" => 1)));
                            if (empty($subcenterRecord)) {
                                $subcenterArray = array(
                                    "org_id" => $orgId,
                                    "long_name" => $subCenterName,
                                    "from_master" => 0,
                                    "status" => 1
                                );
                                $this->OrgSubcenter->create();
                                $this->OrgSubcenter->save($subcenterArray);
                                $subcenterId = $this->OrgSubcenter->getLastInsertId();
                            } else {
                                $subcenterId = $subcenterRecord['OrgSubcenter']['id'];
                            }
                        } else {
                            $subcenterId = 0;
                        }
                    }


                    $newUserOrganization = array(
                        "organization_id" => $orgId,
                        "user_id" => $user['id'],
                        "pool_type" => $statusFields['poolType'],
                        "status" => $statusFields['status'],
                        "flow" => "web_invite",
                        "joined" => 0,
                        "send_invite" => $sendInvite,
                        "department_id" => $departmentId,
                        "job_title_id" => $jobtitleId,
                        "entity_id" => $subOrgId,
                        "user_role" => array_search('endorser', $roleList),
                        "subcenter_id" => $subcenterId,
                    );

                    $saved = $this->UserOrganization->save($newUserOrganization);
                    $userOrgId = $this->UserOrganization->id;

                    $defaultOrg = $this->DefaultOrg->findByUserId($user['id']);

                    if ($status == $statusConfig['active']) {
                        if (empty($defaultOrg)) {
                            $defaultOrgData = array("user_id" => $user['id'], "organization_id" => $orgId, "status" => 1);
                            $this->DefaultOrg->save($defaultOrgData);
                        }
                    }

                    if ($sendInvite == 1) {

                        $noSwitch = false;
                        if (empty($defaultOrg)) {
                            $noSwitch = true;
                        }

                        $joinOrgCode = $this->Common->getJoinOrgCode($orgId, $email, $user['id'], $userOrgId);
                        $viewVars = array('fname' => $user['fname'], 'username' => $user['username'], 'password' => $user['password'], 'organization_name' => $orgName, "join_code" => $joinOrgCode, "no_switch" => $noSwitch);

                        $subject = "Invitation to join nDorse";

                        /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($user['id']);
                        $rootUrl = Router::url('/', true);
                        //$rootUrl = str_replace("http", "https", $rootUrl);
                        //Added by saurabh on 23/06/2021
                        //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
                        
                        $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /**/
                        $configVars = serialize($viewVars);
                        $emailQueue = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => $template);
                        $this->Email->save($emailQueue);
                    }

                    $idvalue = $user['id'];
                    $status = $statusFields['status'];
                }
            }
        }

        $result = array("id" => $idvalue, "result" => $queryresult, "status" => $status);
        echo json_encode($result);
        exit();
    }

    /* Created by : Babulal Prasad
     * at : 30-Sept-2019
     * Desc : New fields(daisy enabled,status) added and if user exist then update the information
     */

    function uploadbulkusersadfscsv() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $this->loadModel("Subscription");
        $this->loadModel("User");
        $this->loadModel("OrgDepartment");
        $this->loadModel("OrgJobTitle");
        $this->loadModel("Entity");
        $this->loadModel("Invite");
        $this->loadModel("Email");
        $this->loadModel("Subscription");

        $roleList = $this->Common->setSessionRoles();
        $filedata = $this->request->data["targetdata"];

        //Extract fields from filedata

        $UID = $filedata[0];
        $fname = $filedata[1];
        $lname = $filedata[2];
        $department = $filedata[3];
        $jobtitle = $filedata[4];
        $email = $filedata[5];
        $username = $filedata[5];
        $subCenter = $filedata[6];
        $daisyEnabled = 0;

        $orgId = $this->request->data["orgId"];
        $orgName = $this->request->data["orgName"];
        $orgCode = $this->request->data["orgcode"];

        $error = false;

        if ((!filter_var($email, FILTER_VALIDATE_EMAIL)) || $email == "") {
            $queryresult = "Check Email";
            $idvalue = "";
            $status = "";
        } else if (trim($fname == "") || trim($lname == "")) {
            $queryresult = "First Name or Last Name is Empty";
            $idvalue = "";
            $status = "";
        } else {
            $userExist = $this->User->findByEmail($email);
            if (!empty($userExist)) {
                $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $userExist['User']['id'], "UserOrganization.organization_id" => $orgId, "UserOrganization.status !=" => 2)));
            } else {
                $userOrganization = array();
            }

            if (!empty($userOrganization)) {

                $user = $userExist['User'];
                $queryresult = "Updated";

                $user = array();

                $UserID = $userExist['User']['id'];
                $user['id'] = $UserID;
                $user['daisy_enabled'] = $daisyEnabled;
                $user['fname'] = $fname;
                $user['lname'] = $lname;
                $user['sub_center_name_row'] = $subCenter;

                $this->User->id = $UserID;

                $this->User->setValidation('edit');
                $this->User->set($user);
                if ($this->User->validates()) {
                    if ($this->User->save($user, array('id' => $UserID))) {
                        $user['id'] = $this->User->id;
                        $queryresult = "Updated";
                    } else {
                        //Error on saving
                        $queryresult = "Error in saving user";
                        $idvalue = "";
                        $status = "";
                        $error = true;
                    }
                } else {
                    //Error on validation
                    $errors = $this->User->validationErrors;
                    $errormsg = "";
                    foreach ($errors as $error) {
                        $errormsg .= $error[0] . "\n";
                    }
                    $queryresult = $errormsg;
                    $idvalue = "";
                    $status = "";
                    $error = true;
                }
//                pr($user);exit;
                //Department set
                if (!empty($department)) {
                    $deptRecord = $this->OrgDepartment->find("first", array("conditions" => array("name" => $department, "organization_id" => $orgId)));

                    if (empty($deptRecord)) {
                        $deptArray = array(
                            "organization_id" => $orgId,
                            "name" => $department,
                            "from_master" => 0,
                            "status" => 1
                        );
                        $this->OrgDepartment->create();
                        $this->OrgDepartment->save($deptArray);
                        $departmentId = $this->OrgDepartment->getLastInsertId();
                    } else {
                        $departmentId = $deptRecord['OrgDepartment']['id'];
                    }
                } else {
                    $departmentId = "";
                }

                //JobTitle set

                if (!empty($jobtitle)) {

                    $jobtitleRecord = $this->OrgJobTitle->find("first", array("conditions" => array("title" => $jobtitle, "organization_id" => $orgId)));
//                    echo $this->OrgJobTitle->getLastQuery();

                    if (empty($jobtitleRecord)) {
                        $jobtitleArray = array(
                            "organization_id" => $orgId,
                            "title" => $jobtitle,
                            "from_master" => 0,
                            "status" => 1
                        );

                        $this->OrgJobTitle->create();
                        $this->OrgJobTitle->save($jobtitleArray);
//                         echo $this->OrgJobTitle->getLastQuery();
                        $jobtitleId = $this->OrgJobTitle->getLastInsertId();
                    } else {
                        $jobtitleId = $jobtitleRecord['OrgJobTitle']['id'];
                    }
                } else {
                    $jobtitleId = "";
                }

                $UserOrgID = $userOrganization['UserOrganization']['id'];
                $status = 1;
                $newUserOrganization = array(
                    "department_id" => $departmentId,
                    "job_title_id" => $jobtitleId,
                    "status" => $status,
                );
//                pr($newUserOrganization); exit;
                $saved = $this->UserOrganization->updateAll($newUserOrganization, array('UserOrganization.id' => $UserOrgID));

                $idvalue = $UserID;
                /* -------------------- */
                //exit;
                //$error = true; 
            } else {

                $statusConfig = Configure::read("statusConfig");
                $sendInvite = false;

                if (empty($userExist)) {
                    $user = array();
                    $user['fname'] = $fname;
                    $user['lname'] = $lname;
                    $user['email'] = $email;
                    $user['role'] = array_search('endorser', $roleList);
                    $user['secret_code'] = $this->getSecretCode("user");
                    $user['username'] = $username;
                    $user['suffix'] = '';
                    $user['employee_id'] = $UID;
                    $user['last_app_used'] = "NOW()";
                    $user['created'] = "NOW()";
                    $user['updated'] = "NOW()";
                    $user['password'] = '';
                    $user['source'] = 'ADFS';
                    $user['sub_center_name_row'] = $subCenter;
                    $user['ad_accountname'] = $UID;
                    $user['ad_uid'] = $UID;

                    $this->User->setValidation('register');

                    //pr($user); exit;
                    $this->User->set($user);
                    if ($this->User->validates()) {
                        if ($this->User->save()) {
                            $user['id'] = $this->User->id;
                            $queryresult = "Inserted";
                        } else {
                            //Error on saving
                            $queryresult = "Error in saving user";
                            $idvalue = "";
                            $status = "";
                            $error = true;
                        }
                    } else {
                        //Error on validation
                        $errors = $this->User->validationErrors;
                        $errormsg = "";
                        foreach ($errors as $error) {
                            $errormsg .= $error[0] . "\n";
                        }
                        $queryresult = $errormsg;
                        $idvalue = "";
                        $status = "";
                        $error = true;
                    }
                } else {
                    $user = $userExist['User'];
                    $queryresult = "Updated";
                }



                //        $status = $this->request->data['status'];

                if (!$error) {

                    $status = 1;

                    if ($status == $statusConfig['active'] || $status == $statusConfig['eval']) {
                        $statusFields = $this->Common->getNewUserOrgFields($orgId, $status);
                    } else {
                        $statusFields = array("poolType" => "paid", "status" => 0);
                    }

                    //Department set
                    if (!empty($department)) {
                        $deptRecord = $this->OrgDepartment->find("first", array("conditions" => array("name" => $department, "organization_id" => $orgId)));

                        if (empty($deptRecord)) {
                            $deptArray = array(
                                "organization_id" => $orgId,
                                "name" => $department,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->OrgDepartment->create();
                            $this->OrgDepartment->save($deptArray);
                            $departmentId = $this->OrgDepartment->getLastInsertId();
                        } else {
                            $departmentId = $deptRecord['OrgDepartment']['id'];
                        }
                    } else {
                        $departmentId = "";
                    }

                    //JobTitle set
                    if (!empty($jobtitle)) {
                        $jobtitleRecord = $this->OrgJobTitle->find("first", array("conditions" => array("title" => $jobtitle, "organization_id" => $orgId)));
                        if (empty($jobtitleRecord)) {
                            $jobtitleArray = array(
                                "organization_id" => $orgId,
                                "title" => $jobtitle,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->OrgJobTitle->create();
                            $this->OrgJobTitle->save($jobtitleArray);
                            $jobtitleId = $this->OrgJobTitle->getLastInsertId();
                        } else {
                            $jobtitleId = $jobtitleRecord['OrgJobTitle']['id'];
                        }
                    } else {
                        $jobtitleId = "";
                    }

                    //Department set
                    if (!empty($subOrg)) {
                        $deptRecord = $this->Entity->find("first", array("conditions" => array("name" => $subOrg, "organization_id" => $orgId)));
                        if (empty($deptRecord)) {
                            $subOrgArray = array(
                                "organization_id" => $orgId,
                                "name" => $subOrg,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->Entity->create();
                            $this->Entity->save($subOrgArray);
                            $subOrgId = $this->Entity->getLastInsertId();
                        } else {
                            $subOrgId = $deptRecord['Entity']['id'];
                        }
                    } else {
                        $subOrgId = "";
                    }

                    $newUserOrganization = array(
                        "organization_id" => $orgId,
                        "user_id" => $user['id'],
                        "pool_type" => $statusFields['poolType'],
                        "status" => $statusFields['status'],
                        "flow" => "adfs",
                        "joined" => 0,
                        "send_invite" => 0,
                        "department_id" => $departmentId,
                        "job_title_id" => $jobtitleId,
                        "entity_id" => $subOrgId,
                        "user_role" => array_search('endorser', $roleList)
                    );

                    $saved = $this->UserOrganization->save($newUserOrganization);
                    $userOrgId = $this->UserOrganization->id;

                    $defaultOrg = $this->DefaultOrg->findByUserId($user['id']);

                    if ($status == $statusConfig['active']) {
                        if (empty($defaultOrg)) {
                            $defaultOrgData = array("user_id" => $user['id'], "organization_id" => $orgId, "status" => 1);
                            $this->DefaultOrg->save($defaultOrgData);
                        }
                    }



                    $idvalue = $user['id'];
                    $status = $statusFields['status'];
                }
            }
        }

        $result = array("id" => $idvalue, "result" => $queryresult, "status" => $status);
        echo json_encode($result);
        exit();
    }

    /* Created by : Babulal Prasad
     * at : 30-Sept-2019
     * Desc : New fields(daisy enabled,status) added and if user exist then update the information
     */

    function updatebulkusersempidcsv() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("User");
        $filedata = $this->request->data["targetdata"];

        //Extract fields from filedata

        $currentEmpID = $filedata[0];
        $newEmpID = $filedata[1];
        $email = $filedata[2];
        $department = $filedata[3];

        $orgId = $this->request->data["orgId"];

        $error = false;

//        if ((!filter_var($email, FILTER_VALIDATE_EMAIL)) || $email == "") {
//            $queryresult = "Check Email";
//            $idvalue = "";
//            $status = "";
//        } else
            if (trim($newEmpID == "") || trim($newEmpID == "")) {
            $queryresult = "New employee id is Empty";
            $idvalue = "";
            $status = "";
        } else {
//            pr($currentEmpID);
            if ($currentEmpID != '') {

                $userExist = $this->User->find('first', array('conditions' => array('User.employee_id' => $currentEmpID)));
                $queryresult = "Updated by old id.";
            } else {
                $userExist = $this->User->findByEmail($email);
                $queryresult = "Updated by email.";
            }

            //Check for duplicate empID
            if (($currentEmpID != $newEmpID) && (!empty($userExist))) {
                $empIdExist = $this->User->find('first', array('conditions' => array('User.employee_id' => $newEmpID)));
            }

            if (empty($empIdExist)) {
                if (!empty($userExist)) {

                    $user = $userExist['User'];


                    $user = array();

                    $UserID = $userExist['User']['id'];
                    $user['id'] = $UserID;
                    $user['employee_id'] = $newEmpID;

                    $this->User->id = $UserID;

                    $this->User->setValidation('edit');
                    $this->User->set($user);
                    if ($this->User->validates()) {
                        if ($this->User->save($user, array('id' => $UserID))) {
                            $user['id'] = $this->User->id;
                            $queryresult = "Updated";
                            $idvalue = $UserID;
                        } else {
                            //Error on saving
                            $queryresult = "Error in saving user";
                            $idvalue = "";
                            $status = "";
                            $error = true;
                        }
                    } else {
                        //Error on validation
                        $errors = $this->User->validationErrors;
                        $errormsg = "";
                        foreach ($errors as $error) {
                            $errormsg .= $error[0] . "\n";
                        }
                        $queryresult = $errormsg;
                        $idvalue = "";
                        $status = "";
                        $error = true;
                    }
                } else {
                    //Error on saving
                    $queryresult = "User not found by given details.";
                    $idvalue = "";
                    $status = "";
                    $error = true;
                }
            } else {
                //Error on saving
                    $queryresult = "new Empid already assigned.";
                    $idvalue = "";
                    $status = "";
                    $error = true;
            }
        }

        $result = array("id" => $idvalue, "result" => $queryresult, "status" => 'active');
        echo json_encode($result);
        exit();
    }

    /* Created by : Babulal Prasad
     * at : 12-jan-2020
     * Desc : 
     */

    function uploadbulkusersLCMCcsv() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $this->loadModel("Subscription");
        $this->loadModel("User");
        $this->loadModel("OrgDepartment");
        $this->loadModel("OrgJobTitle");
        $this->loadModel("Entity");
        $this->loadModel("Invite");
        $this->loadModel("Email");
        $this->loadModel("Subscription");

        $roleList = $this->Common->setSessionRoles();
        $filedata = $this->request->data["targetdata"];
//        pr($filedata);
//        exit;
        //Extract fields from filedata

        $username = $filedata[0];
        $fname = $filedata[1];
        $lname = $filedata[2];
        $suffix = $filedata[3];
        $department = $filedata[4];
        $jobtitle = $filedata[5];
        $email = $filedata[6];
        $mobile = $filedata[7];
        $status = (int) $filedata[8] == 2 ? 3 : ($filedata[8] == 1 ? 1 : 0);
        $subOrg = $filedata[9];
        $sendInvitation = $filedata[10];
        $daisyEnabled = $filedata[11];
        $subCenterID = $filedata[12];

        $orgId = $this->request->data["orgId"];
        $orgName = $this->request->data["orgName"];
        $orgCode = $this->request->data["orgcode"];

        $error = false;

        if ((!filter_var($email, FILTER_VALIDATE_EMAIL)) || $email == "") {
            $queryresult = "Check Email";
            $idvalue = "";
            $status = "";
        } else if (trim($fname == "") || trim($lname == "")) {
            $queryresult = "First Name or Last Name is Empty";
            $idvalue = "";
            $status = "";
        } else {
            $userExist = $this->User->findByEmail($email);

            if (!empty($userExist)) {
                $this->UserOrganization->unbindModel(array('belongsTo' => array('User')));
                $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $userExist['User']['id'], "UserOrganization.organization_id !=" => $orgId, "UserOrganization.status !=" => 2)));
            } else {
                $userOrganization = array();
            }

            if (!empty($userOrganization)) {

                $user = $userExist['User'];
                $queryresult = "Updated";

                $user = array();

                $UserID = $userExist['User']['id'];

                $this->loadModel("LoginStatistics");
                $this->LoginStatistics->updateAll(array('LoginStatistics.live' => "0"), array('LoginStatistics.user_id' => $UserID));

                $user['id'] = $UserID;
                $user['daisy_enabled'] = $daisyEnabled;
                $user['fname'] = $fname;
                $user['lname'] = $lname;
                $user['sub_center_name_row'] = $subCenterID;

                $this->User->id = $UserID;

                $this->User->setValidation('edit');
                $this->User->set($user);
                if ($this->User->validates()) {
                    if ($this->User->save($user, array('id' => $UserID))) {
                        $user['id'] = $this->User->id;
                        $queryresult = "Updated";
                    } else {
                        //Error on saving
                        $queryresult = "Error in saving user";
                        $idvalue = "";
                        $status = "";
                        $error = true;
                    }
                } else {
                    //Error on validation
                    $errors = $this->User->validationErrors;
                    $errormsg = "";
                    foreach ($errors as $error) {
                        $errormsg .= $error[0] . "\n";
                    }
                    $queryresult = $errormsg;
                    $idvalue = "";
                    $status = "";
                    $error = true;
                }
//                pr($user);exit;
                //Department set
                if (!empty($department)) {
                    $deptRecord = $this->OrgDepartment->find("first", array("conditions" => array("name" => $department, "organization_id" => $orgId)));

                    if (empty($deptRecord)) {
                        $deptArray = array(
                            "organization_id" => $orgId,
                            "name" => $department,
                            "from_master" => 0,
                            "status" => 1
                        );
                        $this->OrgDepartment->create();
                        $this->OrgDepartment->save($deptArray);
                        $departmentId = $this->OrgDepartment->getLastInsertId();
                    } else {
                        $departmentId = $deptRecord['OrgDepartment']['id'];
                    }
                } else {
                    $departmentId = "";
                }

                //JobTitle set

                if (!empty($jobtitle)) {

                    $jobtitleRecord = $this->OrgJobTitle->find("first", array("conditions" => array("title" => $jobtitle, "organization_id" => $orgId)));
//                    echo $this->OrgJobTitle->getLastQuery();

                    if (empty($jobtitleRecord)) {
                        $jobtitleArray = array(
                            "organization_id" => $orgId,
                            "title" => $jobtitle,
                            "from_master" => 0,
                            "status" => 1
                        );

                        $this->OrgJobTitle->create();
                        $this->OrgJobTitle->save($jobtitleArray);
//                         echo $this->OrgJobTitle->getLastQuery();
                        $jobtitleId = $this->OrgJobTitle->getLastInsertId();
                    } else {
                        $jobtitleId = $jobtitleRecord['OrgJobTitle']['id'];
                    }
                } else {
                    $jobtitleId = "";
                }

                //$UserOrgID = $userOrganization['UserOrganization']['id'];
                $UserOrgID = 426; //LCMC new ORG on Live site
                $status = 1;

                $newUserOrganization = array(
                    "organization_id" => $orgId,
                    "user_id" => $user['id'],
                    "pool_type" => 'paid',
                    "status" => 1,
                    "flow" => "web_invite",
                    "joined" => 1,
                    "send_invite" => 0,
                    "department_id" => $departmentId,
                    "job_title_id" => $jobtitleId,
                    "entity_id" => 0,
                    "user_role" => array_search('endorser', $roleList),
                    "subcenter_id" => $subCenterID,
                    "created" => date('Y-m-d'),
                    "updated" => date('Y-m-d')
                );

//                pr($newUserOrganization);
//                exit;
                $idvalue = $UserID;

                $saved = $this->UserOrganization->save($newUserOrganization);

                $defaultOrg = $this->DefaultOrg->findByUserId($UserID);


                if (!empty($defaultOrg)) {
                    $defaultOrgID = $defaultOrg['DefaultOrg']['id'];
                    $this->DefaultOrg->id = $defaultOrgID;
                    $defaultOrgData = array("status" => 0);
                    $this->DefaultOrg->save($defaultOrgData, array('DefaultOrg.id' => $defaultOrgID));
                }

                $defaultOrgData = array("user_id" => $UserID, "organization_id" => 426, "status" => 1);
                $this->DefaultOrg->save($defaultOrgData);
                /* -------------------- */
//                exit;
                //$error = true; 
            } else {

                $statusConfig = Configure::read("statusConfig");
                $sendInvite = false;

                if (empty($userExist)) {
                    $user = array();
                    $user['fname'] = $fname;
                    $user['lname'] = $lname;
                    $user['email'] = $email;
                    $user['role'] = array_search('endorser', $roleList);
                    $user['secret_code'] = $this->getSecretCode("user");
                    $user['username'] = $username;
                    $user['suffix'] = '';
                    $user['employee_id'] = '';
                    $user['last_app_used'] = "NOW()";
                    $user['created'] = "NOW()";
                    $user['updated'] = "NOW()";
                    $user['password'] = '';
                    $user['source'] = 'email';
                    $user['sub_center_name_row'] = $subCenterID;
                    $user['ad_accountname'] = '';
                    $user['ad_uid'] = '';

                    $this->User->setValidation('register');

//                    pr($user);
//                    exit;

                    $this->User->set($user);
                    if ($this->User->validates()) {
                        if ($this->User->save()) {
                            $user['id'] = $this->User->id;
                            $queryresult = "Inserted";
                        } else {
                            //Error on saving
                            $queryresult = "Error in saving user";
                            $idvalue = "";
                            $status = "";
                            $error = true;
                        }
                    } else {
                        //Error on validation
                        $errors = $this->User->validationErrors;
                        $errormsg = "";
                        foreach ($errors as $error) {
                            $errormsg .= $error[0] . "\n";
                        }
                        $queryresult = $errormsg;
                        $idvalue = "";
                        $status = "";
                        $error = true;
                    }
                } else {
                    $user = $userExist['User'];
                    $queryresult = "Updated";
                }

//                pr($user);
//                exit;

                if (!$error) {

                    $status = 1;

                    if ($status == $statusConfig['active'] || $status == $statusConfig['eval']) {
                        $statusFields = $this->Common->getNewUserOrgFields($orgId, $status);
                    } else {
                        $statusFields = array("poolType" => "paid", "status" => 0);
                    }

                    //Department set
                    if (!empty($department)) {
                        $deptRecord = $this->OrgDepartment->find("first", array("conditions" => array("name" => $department, "organization_id" => $orgId)));

                        if (empty($deptRecord)) {
                            $deptArray = array(
                                "organization_id" => $orgId,
                                "name" => $department,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->OrgDepartment->create();
                            $this->OrgDepartment->save($deptArray);
                            $departmentId = $this->OrgDepartment->getLastInsertId();
                        } else {
                            $departmentId = $deptRecord['OrgDepartment']['id'];
                        }
                    } else {
                        $departmentId = "";
                    }

                    //JobTitle set
                    if (!empty($jobtitle)) {
                        $jobtitleRecord = $this->OrgJobTitle->find("first", array("conditions" => array("title" => $jobtitle, "organization_id" => $orgId)));
                        if (empty($jobtitleRecord)) {
                            $jobtitleArray = array(
                                "organization_id" => $orgId,
                                "title" => $jobtitle,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->OrgJobTitle->create();
                            $this->OrgJobTitle->save($jobtitleArray);
                            $jobtitleId = $this->OrgJobTitle->getLastInsertId();
                        } else {
                            $jobtitleId = $jobtitleRecord['OrgJobTitle']['id'];
                        }
                    } else {
                        $jobtitleId = "";
                    }

                    //Department set
                    if (!empty($subOrg)) {
                        $deptRecord = $this->Entity->find("first", array("conditions" => array("name" => $subOrg, "organization_id" => $orgId)));
                        if (empty($deptRecord)) {
                            $subOrgArray = array(
                                "organization_id" => $orgId,
                                "name" => $subOrg,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->Entity->create();
                            $this->Entity->save($subOrgArray);
                            $subOrgId = $this->Entity->getLastInsertId();
                        } else {
                            $subOrgId = $deptRecord['Entity']['id'];
                        }
                    } else {
                        $subOrgId = "";
                    }

                    $newUserOrganization = array(
                        "organization_id" => $orgId,
                        "user_id" => $user['id'],
                        "pool_type" => $statusFields['poolType'],
                        "status" => $statusFields['status'],
                        "flow" => "web_invite",
                        "joined" => 1,
                        "send_invite" => 1,
                        "department_id" => $departmentId,
                        "job_title_id" => $jobtitleId,
                        "entity_id" => $subOrgId,
                        "user_role" => array_search('endorser', $roleList),
                        "subcenter_id" => $subCenterID,
                    );

                    $saved = $this->UserOrganization->save($newUserOrganization);
                    $userOrgId = $this->UserOrganization->id;

                    $defaultOrg = $this->DefaultOrg->findByUserId($user['id']);

                    if ($status == $statusConfig['active']) {
                        if (empty($defaultOrg)) {
                            $defaultOrgData = array("user_id" => $user['id'], "organization_id" => $orgId, "status" => 1);
                            $this->DefaultOrg->save($defaultOrgData);
                        }
                    }

                    $idvalue = $user['id'];
                    $status = $statusFields['status'];
                }
            }
        }

        $result = array("id" => $idvalue, "result" => $queryresult, "status" => $status);
        echo json_encode($result);
        exit();
    }

    function uploadbulkuserscsv_16052019() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $this->loadModel("Subscription");
        $this->loadModel("User");
        $this->loadModel("OrgDepartment");
        $this->loadModel("OrgJobTitle");
        $this->loadModel("Entity");
        $this->loadModel("Invite");
        $this->loadModel("Email");
        $this->loadModel("Subscription");

        $roleList = $this->Common->setSessionRoles();
        $filedata = $this->request->data["targetdata"];

        //Extract fields from filedata

        $employeeId = $filedata[0];
        $fname = $filedata[1];
        $lname = $filedata[2];
        $suffix = $filedata[3];
        $department = $filedata[4];
        $jobtitle = $filedata[5];
        $email = $filedata[6];
        $username = $filedata[6];
        $mobile = $filedata[7];
        $status = (int) $filedata[8] == 2 ? 3 : ($filedata[8] == 1 ? 1 : 0);
        $sendInvitation = $filedata[9];
        $subOrg = $filedata[10];



        $orgId = $this->request->data["orgId"];
        $orgName = $this->request->data["orgName"];
        $orgCode = $this->request->data["orgcode"];
        $error = false;

        if ((!filter_var($email, FILTER_VALIDATE_EMAIL)) || $email == "") {
            $queryresult = "Check Email";
            $idvalue = "";
            $status = "";
        } else if (trim($fname == "") || trim($lname == "")) {
            $queryresult = "First Name or Last Name is Empty";
            $idvalue = "";
            $status = "";
        } else {
            $userExist = $this->User->findByEmail($email);
            if (!empty($userExist)) {
                $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $userExist['User']['id'], "UserOrganization.organization_id" => $orgId, "UserOrganization.status !=" => 2)));
            } else {
                $userOrganization = array();
            }

            if (!empty($userOrganization)) {
                $queryresult = "Already in organization";
                $idvalue = "";
                $status = "";
                $error = true;
            } else {
                $statusConfig = Configure::read("statusConfig");

                $sendInvite = false;
                if ($sendInvitation == 1 && $status == 1) {
                    $sendInvite = 1;
                }

                if (empty($userExist)) {
                    $user = array();
                    $user['fname'] = $fname;
                    $user['lname'] = $lname;
                    $user['email'] = $email;
                    $user['role'] = array_search('endorser', $roleList);
                    $user['secret_code'] = $this->getSecretCode("user");
                    $user['username'] = $username;
                    $user['suffix'] = $suffix;
                    $user['employee_id'] = $employeeId;
                    $user['ad_uid'] = $employeeId;
                    $user['ad_accountname'] = $employeeId;
                    $user['ad_upn'] = $email;
                    $user['last_app_used'] = "NOW()";
                    $user['password'] = $this->Common->randompasswordgenerator(8);

                    $this->User->setValidation('register');



                    $this->User->set($user);
                    if ($this->User->validates()) {
                        if ($this->User->save()) {
                            $user['id'] = $this->User->id;
                            $template = "invitation_admin";
                            $queryresult = "Inserted";
                        } else {
                            //Error on saving
                            $queryresult = "Error in saving user";
                            $idvalue = "";
                            $status = "";
                            $error = true;
                        }
                    } else {
                        //Error on validation
                        $errors = $this->User->validationErrors;
                        $errormsg = "";
                        foreach ($errors as $error) {
                            $errormsg .= $error[0] . "\n";
                        }
                        $queryresult = $errormsg;
                        $idvalue = "";
                        $status = "";
                        $error = true;
                    }
                } else {
                    $user = $userExist['User'];
                    $template = "invitation_admin_existing";
                    $queryresult = "Updated";
                }



                //        $status = $this->request->data['status'];

                if (!$error) {
                    if ($status == $statusConfig['active'] || $status == $statusConfig['eval']) {
                        $statusFields = $this->Common->getNewUserOrgFields($orgId, $status);
                    } else {
                        $statusFields = array("poolType" => "paid", "status" => 0);
                    }

                    //Department set
                    if (!empty($department)) {
                        $deptRecord = $this->OrgDepartment->find("first", array("conditions" => array("name" => $department, "organization_id" => $orgId)));

                        if (empty($deptRecord)) {
                            $deptArray = array(
                                "organization_id" => $orgId,
                                "name" => $department,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->OrgDepartment->create();
                            $this->OrgDepartment->save($deptArray);
                            $departmentId = $this->OrgDepartment->getLastInsertId();
                        } else {
                            $departmentId = $deptRecord['OrgDepartment']['id'];
                        }
                    } else {
                        $departmentId = "";
                    }

                    //JobTitle set
                    if (!empty($jobtitle)) {
                        $jobtitleRecord = $this->OrgJobTitle->find("first", array("conditions" => array("title" => $jobtitle, "organization_id" => $orgId)));
                        if (empty($jobtitleRecord)) {
                            $jobtitleArray = array(
                                "organization_id" => $orgId,
                                "title" => $jobtitle,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->OrgJobTitle->create();
                            $this->OrgJobTitle->save($jobtitleArray);
                            $jobtitleId = $this->OrgJobTitle->getLastInsertId();
                        } else {
                            $jobtitleId = $jobtitleRecord['OrgJobTitle']['id'];
                        }
                    } else {
                        $jobtitleId = "";
                    }

                    //Department set
                    if (!empty($subOrg)) {
                        $deptRecord = $this->Entity->find("first", array("conditions" => array("name" => $subOrg, "organization_id" => $orgId)));
                        if (empty($deptRecord)) {
                            $subOrgArray = array(
                                "organization_id" => $orgId,
                                "name" => $subOrg,
                                "from_master" => 0,
                                "status" => 1
                            );
                            $this->Entity->create();
                            $this->Entity->save($subOrgArray);
                            $subOrgId = $this->Entity->getLastInsertId();
                        } else {
                            $subOrgId = $deptRecord['Entity']['id'];
                        }
                    } else {
                        $subOrgId = "";
                    }

                    $newUserOrganization = array(
                        "organization_id" => $orgId,
                        "user_id" => $user['id'],
                        "pool_type" => $statusFields['poolType'],
                        "status" => $statusFields['status'],
                        "flow" => "web_invite",
                        "joined" => 0,
                        "send_invite" => $sendInvite,
                        "department_id" => $departmentId,
                        "job_title_id" => $jobtitleId,
                        "entity_id" => $subOrgId,
                        "user_role" => array_search('endorser', $roleList)
                    );

                    $saved = $this->UserOrganization->save($newUserOrganization);
                    $userOrgId = $this->UserOrganization->id;

                    $defaultOrg = $this->DefaultOrg->findByUserId($user['id']);

                    if ($status == $statusConfig['active']) {
                        if (empty($defaultOrg)) {
                            $defaultOrgData = array("user_id" => $user['id'], "organization_id" => $orgId, "status" => 1);
                            $this->DefaultOrg->save($defaultOrgData);
                        }
                    }

                    if ($sendInvite == 1) {

                        $noSwitch = false;
                        if (empty($defaultOrg)) {
                            $noSwitch = true;
                        }

                        $joinOrgCode = $this->Common->getJoinOrgCode($orgId, $email, $user['id'], $userOrgId);
                        $viewVars = array('fname' => $user['fname'], 'username' => $user['username'], 'password' => $user['password'], 'organization_name' => $orgName, "join_code" => $joinOrgCode, "no_switch" => $noSwitch);

                        $subject = "Invitation to join nDorse";

                        /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($user['id']);
                        $rootUrl = Router::url('/', true);
                        //$rootUrl = str_replace("http", "https", $rootUrl);
                        //Added by saurabh on 23/06/2021
                        //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
                        
                        $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /**/
                        $configVars = serialize($viewVars);
                        $emailQueue = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => $template);
                        $this->Email->save($emailQueue);
                    }

                    $idvalue = $user['id'];
                    $status = $statusFields['status'];
                }
            }
        }

        $result = array("id" => $idvalue, "result" => $queryresult, "status" => $status);
        echo json_encode($result);
        exit();
    }

    function uploadbulkuserscsvOld() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $this->loadModel("Subscription");
        $this->loadModel("User");
        $this->loadModel("OrgDepartment");
        $this->loadModel("OrgJobTitle");
        $this->loadModel("Entity");
        $this->loadModel("Invite");
        $this->loadModel("Email");
        $this->loadModel("Subscription");
        $statusConfig = Configure::read("statusConfig");
        $filedata = $this->request->data["targetdata"];
        $orgid = $this->request->data["orgId"];
        $org_name = $this->request->data["orgName"];
        $org_code = $this->request->data["orgcode"];
        $password_random = $this->Common->randompasswordgenerator(8);
        //===$filedata[1] = "firstname", $filedata[2] = "lastname", $filedata[6] = "email"
        if ((!filter_var($filedata[6], FILTER_VALIDATE_EMAIL)) || $filedata[6] == "") {
            $queryresult = "Check Email";
            $idvalue = "";
            $status = "";
        } else if (trim($filedata[1] == "") || trim($filedata[2] == "")) {
            $queryresult = "First Name or Last Name is Empty";
            $idvalue = "";
            $status = "";
        } else {
            $available_pool = 10;
            // get subscription info
            $subscriptiondata = $this->Subscription->findByOrganizationId($orgid);

            if (!empty($subscriptiondata) && $subscriptiondata["Subscription"]["organization_id"] == $orgid) {
                $available_pool += $subscriptiondata["Subscription"]["pool_purchased"];
            }
            // get active users
            //$params['conditions'] = array("organization_id" => $orgid, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
            //$params['fields'] = array("COUNT(UserOrganization.user_id) as count");
            //$userOrgStats = $this->UserOrganization->find("all", $params);
            //$usercount = $userOrgStats[0][0]["count"];
            $userorgdata = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $orgid, "UserOrganization.status" => array(1, 3), "UserOrganization.user_role" => array(2, 3))));

            $fresult = array();
            if (!empty($userorgdata)) {

                foreach ($userorgdata as $usersdata) {
                    $fresult[] = $usersdata["UserOrganization"]["id"];
                }
            }

//
            $params = array();
            // $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['inactive'], $statusConfig['active'], $statusConfig['eval']));
            $params['conditions'] = array("organization_id" => $orgid, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
            $params['group'] = 'pool_type';
            $params['fields'] = array("UserOrganization.pool_type", "COUNT(UserOrganization.user_id) as count");
            $userOrgStats = $this->UserOrganization->find("all", $params);
            // echo $this->UserOrganization->getLastQuery();
            //print_r($userOrgStats);

            $freeCount = 0;
            $paidCount = 0;
            //print_r($userOrgStats);

            foreach ($userOrgStats as $stats) {
                if ($stats['UserOrganization']['pool_type'] == 'free') {
                    $freeCount = $stats[0]['count'];
                } else {
                    $paidCount = $stats[0]['count'];
                }
            }
            $usercount = $freeCount + $paidCount;
//
//            $secret_code = $this->requestAction('/api/getSecretCode', array('user'));
            $secret_code = $this->getSecretCode('user');
            $querydepartments = $this->OrgDepartment->find("list", array('conditions' => array('organization_id' => $orgid)));
            $querydepartments = array_map('strtolower', $querydepartments);
            $queryjobtitles = $this->OrgJobTitle->find("list", array('conditions' => array('organization_id' => $orgid)));
            $queryjobtitles = array_map('strtolower', $queryjobtitles);
            $queryentities = $this->Entity->find("list", array('conditions' => array('organization_id' => $orgid)));
            $queryentities = array_map('strtolower', $queryentities);
            $existingemail = $this->User->find('list', array('fields' => 'email'));
            $department_key = "";
            $entity_key = "";
            $jobtitle_key = "";
            $values_array = array(
                'employee_id' => $filedata[0],
                'email' => $filedata[6],
                'username' => $filedata[6],
                'password' => $password_random,
                'fname' => $filedata[1],
                'lname' => $filedata[2],
                'suffix' => $filedata[3],
                'secret_code' => $secret_code,
                'mobile' => $filedata[7],
                'role' => 3,
                'created' => date("Y-m-d h:i:s"),
                'updated' => date("Y-m-d h:i:s"),
            );

            $emailids_exist = $filedata[6];
            $arrayorgdetail = array("id" => $orgid, "name" => $org_name, "code" => $org_code);
            //==============to insert values in users table
            if (in_array($emailids_exist, $existingemail)) {
                //=============update data as same email id is found in the database
                $idvalue = array_search($emailids_exist, $existingemail);
                $inviteid = $this->Invite->field("id", array("organization_id" => $orgid, "email" => $filedata[6]));

                if ($inviteid == 0) {
//                    $this->Common->functiontoinviteifzero($filedata[6], $orgid);
                    if ($filedata[9] == 1) {
                        $this->Common->emailstoinvited($arrayorgdetail, $filedata[6], "web");
                    }
                } else {
                    $useremail[$inviteid] = $filedata[6];
//                    $this->Common->entryinvitetableexisting($arrayorgdetail, $useremail);
                    if ($filedata[9] == 1) {
                        $this->Common->emailstoinvited($arrayorgdetail, $filedata[6], "web");
                    }
                }
            } else {
                //=============insert data
                //$this->User->create();
                $this->User->save($values_array);
                $idval = $this->User->find("list", array('conditions' => array('email' => $emailids_exist), 'fields' => 'id'));
                foreach ($idval as $ids) {
                    $idvalue = $ids;
                }
//                $this->Common->functiontoinviteifzero($filedata[6], $orgid);
                $this->Common->emailstoinvited($arrayorgdetail, $filedata[6], "web");
            }
            //======================department table search
            if (isset($filedata[4]) && !empty($filedata[4])) {
                $department = strtolower($filedata[4]);
                $department_key = array_search($department, $querydepartments);
                if ($department_key == "") {
                    $department_array = array(
                        "organization_id" => $orgid,
                        "name" => $filedata[4],
                        "from_master" => 0,
                        "status" => 1
                    );
                    $this->OrgDepartment->create();
                    $this->OrgDepartment->save($department_array);
                    $department_key = $this->OrgDepartment->getLastInsertId();
                }
            }
            //======================jobtitle table search
            if (isset($filedata[5]) && !empty($filedata[5])) {
                $jobtitle = strtolower($filedata[5]);
                $jobtitle_key = array_search($jobtitle, $queryjobtitles);
            }
        }

        //=======================now inserting or updataing to users organizations table
        $find_user_org_id = $this->UserOrganization->field('id', array('organization_id' => $orgid, 'user_id' => $idvalue));


        //=checked if status = 0 took it in eval mode, else if 1 its 1 else if other than these 2 than its inactive
        //$status = ($filedata[8] == 0) ? 3 : ($filedata[8] == 1) ? 1 : 0;
        //$status = ($filedata[8] == 0) ? 1 : 0;
        $status = ($filedata[8] != "" && $filedata[8] == 0) ? 1 : 0;
//        $status = ($filedata[8] != "" && $filedata[8] == 0) ? 1 : 3;
        //$statusflag = ($filedata[8] == 1) ? "inactiveduetosheet" : "active";
        $existstatus = $status;
        $ucount = $usercount + 1;
        if ($ucount > $available_pool) {
            $status = 0;
            //$statusflag = "inactiveduetosubscription";
        }
        $pool_type = "free";
        //echo $freeCount;
        if ($freeCount >= 10) {
            $pool_type = "paid";
        }

        // echo $pool_type;
        if ($find_user_org_id) {
            //if record found update 
            $this->UserOrganization->id = $find_user_org_id;

            $status = ($filedata[8] == 0) ? 1 : 0;
//            $status = ($filedata[8] == 0) ? 1 : 3;
            if ((!in_array($find_user_org_id, $fresult)) && $usercount >= $available_pool) {
                $status = 0;
            }

            if (in_array($find_user_org_id, $fresult)) {
                $array_orgdeparment = array(
                    'user_id' => $idvalue,
                    'organization_id' => $orgid,
                    'user_role' => 3,
                    'department_id' => $department_key,
                    'job_title_id' => $jobtitle_key,
                    'status' => $status
                );
            } else {
                $array_orgdeparment = array(
                    'user_id' => $idvalue,
                    'organization_id' => $orgid,
                    'user_role' => 3,
                    'department_id' => $department_key,
                    'job_title_id' => $jobtitle_key,
                    'pool_type' => $pool_type,
                    'status' => $status
                );
            }


            $this->UserOrganization->save($array_orgdeparment, false);
            $queryresult = "Updated";

            //$find_user_otherorg = $this->UserOrganization->field('id', array('organization_id!' => $orgid, 'user_id' => $idvalue));
        } else {
            $array_orgdeparment = array(
                'user_id' => $idvalue,
                'organization_id' => $orgid,
                'user_role' => 3,
                'department_id' => $department_key,
                'job_title_id' => $jobtitle_key,
                'pool_type' => $pool_type,
                'status' => $status
            );

            $this->UserOrganization->save($array_orgdeparment, false);
            $queryresult = "Inserted";
        }
        //================sending final email to user
        $result = array("id" => $idvalue, "result" => $queryresult, "status" => $status, "usercount" => $usercount, "ucount" => $ucount, "available_pool" => $available_pool);
        echo json_encode($result);
        exit();
    }

    function uploadbulkimagescsv() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $orgid = $this->request->data["orgid"];
        $filedata = $this->request->data["targetdata"];
        $this->loadModel("UserOrganization");
        $this->loadModel("User");
//$existinguserid = $this->UserOrganization->field("user_id", array("organization_id" => $orgid, "status" => array(0,1,3)));
        $existinguserid = $this->UserOrganization->find("all", array("fields" => array("user_id"), "conditions" => array("organization_id" => $orgid, "user_role" => array(3, 2), "UserOrganization.status" => array(0, 1, 3))));
        foreach ($existinguserid as $userids) {
            $useridsarray[] = $userids["UserOrganization"]["user_id"];
        }
        $mime = array('image/gif', 'image/jpeg', 'image/png');
        for ($i = 1; $i < count($filedata); $i++) {
            if (isset($filedata[$i][0]) && !empty($filedata[$i][0]) && isset($filedata[$i][1]) && !empty($filedata[$i][1])) {
                $rand = rand(1000, 9999);
//======check image validity if it is not jpeg or other image type
                $user = $this->User->findByEmail($filedata[$i][0]);
                if (!in_array($user["User"]["id"], $useridsarray)) {
                    $result[$filedata[$i][0]] = "This user is not of your organization";
                } else {
                    if ($user["User"]["image"] == "") {
//========upload image as per expected from file
//$imageinfo = getimagesize(strtok($filedata[$i][1], '?'));
                        $imageinfo = getimagesize($filedata[$i][1]);
                        if (!in_array($imageinfo["mime"], $mime)) {
                            $result[$filedata[$i][0]] = "Not valid Image";
                            continue;
                        }
                        $filename = $rand . basename($filedata[$i][1]);
//==replacing query string from image
                        $filename = strtok($filename, '?');
                        $filenamelarge = $rand . basename($filedata[$i][1]);
//=======if query string remove
                        $filenamelarge = strtok($filenamelarge, '?');
                        $newfile = fopen(WWW_ROOT . PROFILE_IMAGE_DIR . 'small/' . $filename, "wb");
                        $newfilelarge = fopen(WWW_ROOT . PROFILE_IMAGE_DIR . $filenamelarge, "wb");
                        if ($newfile) {
                            $file = fopen($filedata[$i][1], "rb");
                            while (!feof($file)) {
                                fwrite($newfile, fread($file, 1024 * 8), 1024 * 8);
                            }
                            $small_image = $this->Image->resize(WWW_ROOT . PROFILE_IMAGE_DIR . 'small/' . $filename, "320", "200", false);
                            fclose($file);
                        }
                        if ($newfilelarge) {
                            $file = fopen($filedata[$i][1], "rb");
                            while (!feof($file)) {
                                fwrite($newfilelarge, fread($file, 1024 * 8), 1024 * 8);
                            }
                            fclose($file);
                        }

//update image to db
                        $this->User->id = $user["User"]["id"];
                        $this->User->saveField("image", $filename, false);
                        $result[$filedata[$i][0]] = "User Image Updated";
                    } else {
                        $result[$filedata[$i][0]] = "User Already has Image";
                    }
                }
            }
        }
        echo json_encode($result);
        exit;
    }

    function invitationsemails() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("Invite");
        $this->loadModel("Email");
        $this->loadModel("User");

        $emails = json_decode($this->request->data["emails"], true);
        $emailsflow = json_decode($this->request->data["emailsflow"], true);
        $orgdetails = json_decode($this->request->data["orgdetails"], true);

//$orgid = $this->request->data["orgid"];
//$othervalues = json_decode($this->request->data["othervalues"], true);
        if (!empty($emails)) {
            //==============send array of emails and orgdetails it will increase counter in invite table
//            $this->Common->entryinvitetableexisting($orgdetails, $emails);
        }
        foreach ($emails as $invitedid => $email) {
            //=======send email

            $result = $this->Common->emailstoinvited($orgdetails, $email, "web");

//            if ($emailsflow[$invitedid] == "web_invite") {
//                //==============web invitations
//                $result = $this->Common->emailstoinvited($orgdetails, $email, "web");
//            } else {
//                //=======app invitations
//                $result = $this->Common->emailstoinvited($orgdetails, $email, "app");
//            }
        }
    }

    function pendingemails() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("OrgRequest");
        $this->loadModel("UserOrganization");
        $this->loadModel("Subscription");
        $this->loadModel("User");
        $this->loadModel("Email");
        $emailschecked = $this->request->data["emailschecked"];
        $orgname = $this->request->data["orgname"];
        $orgid = $this->request->data["orgid"];
        $rule = $this->request->data["rule"];
        if ($rule == "accept") {
            $statusConfig = Configure::read("statusConfig");
            $available_pool = 10;
            // get subscription info
            $subscriptiondata = $this->Subscription->findByOrganizationId($orgid);
            if (!empty($subscriptiondata) && $subscriptiondata["Subscription"]["status"] == 1) {
                $available_pool += $subscriptiondata["Subscription"]["pool_purchased"];
            }

            $lastinsertedid = "";
            $resultant_user_table = array();
            $defaultorgarray = array();
            $abc = 1;
            foreach ($emailschecked as $key => $value) {
                //========pending request
                $viewVarsinvitations = array("org_name" => $orgname);
                $configVarsinvitations = serialize($viewVarsinvitations);
                $subject = "Congratulations. Your request to accepted for " . $orgname;
                $emailvar = array("to" => $value, "subject" => $subject, "config_vars" => $configVarsinvitations, "template" => "accept_request");
                $this->Email->create();
                $email = $this->Email->save($emailvar);

                //======end
                $this->OrgRequest->id = $key;
                $val = array("status" => 1);
                $this->OrgRequest->save($val, false);
                $user_id = $this->OrgRequest->field("user_id", array("id" => $key));
                $userorganizationtable = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $orgid, "user_id" => $user_id), "fields" => "id,user_id,status"));

                if (empty($userorganizationtable)) {
                    $params = array();
                    $params['conditions'] = array("organization_id" => $orgid, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                    $params['fields'] = array("COUNT(UserOrganization.user_id) as count");
                    $userOrgStats = $this->UserOrganization->find("all", $params);
                    $usercount = $userOrgStats[0][0]["count"];
                    $ucount = $usercount + 1;
                    $userstatus = 1;
                    if ($ucount > $available_pool) {
                        $userstatus = 0;
                    }
                    $pool_type = "free";
                    if ($ucount > 10) {
                        $pool_type = "paid";
                    }


                    $finalresult = array("user_id" => $user_id, "organization_id" => $orgid, "user_role" => 3, "pool_type" => $pool_type, "joined" => 1, "status" => $userstatus);
                    $this->UserOrganization->create();
                    $this->UserOrganization->save($finalresult, false);
                    $lastinsertedid[] = $this->UserOrganization->getLastInsertId();
                    $userdata = $this->User->findById($user_id);
                    $userdata["User"]["status"] = $userstatus;
                    $resultant_user_table[] = $userdata;
                } elseif ($userorganizationtable[0]["UserOrganization"]["status"] == 2) {
                    $this->UserOrganization->id = $userorganizationtable[0]["UserOrganization"]["id"];
                    $params = array();
                    $params['conditions'] = array("organization_id" => $orgid, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                    $params['fields'] = array("COUNT(UserOrganization.user_id) as count");
                    $userOrgStats = $this->UserOrganization->find("all", $params);
                    $usercount = $userOrgStats[0][0]["count"];
                    $ucount = $usercount + 1;
                    $userstatus = 1;
                    if ($ucount > $available_pool) {
                        $userstatus = 0;
                    }
                    $pool_type = "free";
                    if ($ucount > 10) {
                        $pool_type = "paid";
                    }
                    $val = array("status" => $userstatus, "user_role" => 3, "pool_type" => $pool_type);
                    $this->UserOrganization->save($val, false);
                }


                //========to add the user in default org
                //$checkdefaultorg = $this->DefaultOrg->find("count", array("conditions" => array("organization_id" => $orgid, "user_id" => $user_id)));
                $checkdefaultorg = $this->DefaultOrg->find("count", array("conditions" => array("user_id" => $user_id)));
                if ($checkdefaultorg == 0) {
                    $defaultorgarray[] = array(
                        "user_id" => $user_id,
                        "organization_id" => $orgid,
                        "status" => 1
                    );
                }
            }
            $this->DefaultOrg->saveMany($defaultorgarray);
            $result = array("fresult" => $resultant_user_table, "userorgid" => $lastinsertedid);
            echo json_encode($result);
            exit;
        } else {
            foreach ($emailschecked as $key => $value) {
                $this->OrgRequest->id = $key;
                $this->OrgRequest->delete();
            }
            $result = array("fresult" => "true");
            echo json_encode($result);
        }
    }

    function createemails() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("OrgRequest");
        $orgid = $this->request->data["orgid"];
        $this->OrgRequest->bindModel(array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User'
                ),
            ),
        ));
        $resultantemails = $this->OrgRequest->find("all", array("conditions" => array("organization_id" => $orgid)));
        $status = array("status" => 0);
        $pendingrequests = $this->Common->pending_requests($resultantemails, $status);
        echo json_encode($pendingrequests);
    }

    function findendorser() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("User");
        $emailid = $this->request->data["targetemail"];
        $orgid = $this->request->data["orgid"];
        //$emailid = $this->Common->encodeData($emailid);
        $options["conditions"] = array("email" => $emailid);
        $this->User->bindModel(array(
            'hasMany' => array(
                'UserOrganization' => array(
                    'className' => 'UserOrganization',
                    'conditions' => array("UserOrganization.organization_id" => $orgid, "UserOrganization.status !=" => 2)
                ),
            ),
        ));
        $resultant = $this->User->find("all", $options);
        echo json_encode($resultant);
    }

    function reinviteemail() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $this->loadModel("Email");
        $this->loadModel("Invite");
        $userid = $this->request->data["userid"];
        $useremail = $this->request->data["useremail"];
        $userfname = $this->request->data["fname"];
        $orgid = $this->request->data["orgid"];
        $orgname = $this->request->data["orgname"];
        $orgsecretcode = $this->request->data["orgsecretcode"];
//$dataall = $this->Invite->find("all",array("fields" => array("id", "invite_count"),"conditions" => array("email" => $useremail, "organization_id" => $orgid)));
        $result = "";
//        $inviteid = $this->Invite->field("id", array("email" => $useremail, "organization_id" => $orgid));
//        $useremails = array($inviteid => $this->request->data["useremail"]);
        $orgdetails = array("id" => $orgid, "name" => $orgname, "code" => $orgsecretcode);
//        if ($inviteid != 0) {
//            if (!empty($useremails)) {
        //==============send array of emails and orgdetails it will increase counter in invite table
//                $this->Common->entryinvitetableexisting($orgdetails, $useremails);
//            }
//            foreach ($useremail as $invitedid => $email) {
        $result = $this->Common->emailstoinvited($orgdetails, $useremail, "web");
//            }
//        } else {
//            $result = "No result in invite table";
//        }
        echo $result;
        exit;
        /* $counter = $this->UserOrganization->find("count", array("conditions" => array("user_id" => $userid)));
          if($counter > 1){
          //===========send only invite email
          $dataall = $this->Invite->find("all",array("fields" => array("id", "invite_count"),"conditions" => array("email" => $useremail, "organization_id" => $orgid)));
          if(!empty($dataall)){
          $this->Invite->id = $dataall[0]["Invite"]["id"];
          $val = array("invite_count" => $dataall[0]["Invite"]["invite_count"] + 1);
          $this->Invite->save($val, false);
          }
          $viewVarsinvitations = array("org_name" => $orgname, "org_code" => $orgsecretcode);
          $configVarsinvitations = serialize($viewVarsinvitations);
          $subjectinvitation = "Invitation to join nDorse";
          $emailvar = array("to" => $useremail, "subject" => $subjectinvitation, "config_vars" => $configVarsinvitations, "template" => "invite");
          $email = $this->Email->save($emailvar);
          echo json_encode($email);
          }else{
          $dataall = $this->Invite->find("all",array("fields" => array("id", "invite_count"),"conditions" => array("email" => $useremail, "organization_id" => $orgid)));
          if(!empty($dataall)){
          $this->Invite->id = $dataall[0]["Invite"]["id"];
          $val = array("invite_count" => $dataall[0]["Invite"]["invite_count"] + 1);
          $this->Invite->save($val, false);
          }
          //===========send invite email with password
          $password_random = $this->Common->randompasswordgenerator(8);
          $viewVars = array('fname' => $userfname, 'username' => $useremail, 'password' => $password_random   , 'organization_name' => $orgname, "organization_code" => $orgsecretcode);
          $configVars = serialize($viewVars);
          $subject = "Invitation to join nDorse";
          $emailvar = array("to" => $useremail, "subject" => $subject, "config_vars" => $configVars, "template" => "invitation_admin");
          //==================to change pasword for the userid;
          $this->User->id = $userid;
          $this->User->saveField('password', $password_random, false);
          $this->Email->Create();
          $email = $this->Email->save($emailvar);
          echo json_encode($email);
          } */
    }

    function resetuserpassword() {
        $this->loadModel("User");
        $this->loadModel("Email");
        $this->layout = 'ajax';
        $this->autoRender = false;
        $fname = $this->request->data["fname"];
        $uid = $this->request->data["uid"];
        $newpassword = $this->request->data["npassword"];
        $uemail = $this->request->data["uemail"];
        $orgname = $this->request->data["orgname"];
        $this->User->id = $uid;
        $saved = $this->User->saveField("password", $newpassword, false);
        $subject = "nDorse Password Reset";
        $viewVars = array("org_name" => $orgname, "fname" => trim($fname), "password" => $newpassword);
        $configVars = serialize($viewVars);
        /** Mail commented intensionaly after dicsuss with Rohan @24-Aug-17 by Babulal Prasad
          //        $emailQueue = array("to" => $uemail, "subject" => $subject, "config_vars" => $configVars, "template" => "update_password_admin");
          //        $this->Email->Create();
          //        $this->Email->save($emailQueue);
          echo "success";
          }

          function loadmoreajax() {
          //================loadmore data for users index page
          $this->loadModel("User");
          $this->layout = "ajax";
          $this->autoRender = false;
          $totalrecords = $this->request->data["totalrecords"];
          $searchvalue = $this->request->data["searchvalue"];
          $conditions = array('User.role' => '2', "User.status" => array(0, 1));
          if ($searchvalue != "") {
          $conditions["OR"] = array("fname like '%$searchvalue%'", "lname like '%$searchvalue%'", "email like '%$searchvalue%'");
          }

          $userdata = $this->User->find('all', array('limit' => 10, 'offset' => $totalrecords, 'order' => 'User.id DESC', 'conditions' => $conditions));
          $orgsandusers = $this->Common->getorgandusers($userdata);
          $nooforg = $orgsandusers["nooforgs"];
          $noofusers = $orgsandusers["noofusers"];
          //$totaluserrecords = $this->User->find("count", array("conditions" => array('User.role' => '2', "User.status" => array(0, 1))));
          $this->set('userdata', $userdata);
          $this->set(compact("nooforg", "noofusers"));
          echo $htmlstring = $this->render('/Elements/rowusersindex');
          exit;
          //$htmlstring = "";
          /* foreach($userdata as $users){
          $id = $users["User"]["id"];
          $email = $users["User"]["email"];
          $image = $users["User"]["image"];
          $fullname = $users["User"]["fname"]." ".$users["User"]["lname"];
          $created = $users["User"]["created"];
          $updated = $users["User"]["updated"];
          $status = $users["User"]["status"];

          $htmlstring .= '<tr class = "" id = "row_'.$id.'">';
          $htmlstring .= '<td>';
          if($image == ""){
          $htmlstring .= '<img alt = "" class = "img-circle" src = "/ndorsedev/img/user.png"> </td>';
          }else{
          //change the image
          $htmlstring .= '<img alt = "" class = "img-circle" src = "/ndorsedev/img/user.png"> </td>';
          }
          $htmlstring .= '<td><h6 style = "color:#ffffff; font-size:18px;">';
          $htmlstring .= '<a href = "/ndorsedev/users/clientinfo/88">'.$fullname.'</a> </h6>';
          $htmlstring .= '<p style = "color:#c2c1c1; font-size:14px;">';
          $htmlstring .= $email;
          $htmlstring .= '<br>';
          $htmlstring .= 'Last updated on: '.$updated;
          $htmlstring .= 'Created on: '.$created.'</p></td>';
          $htmlstring .= '<td>Free</td>';
          $htmlstring .= '<td>N/A</td>';
          $htmlstring .= '<td class = "text-active" id = "statusactivity_'.$id.'">';
          if($status == 0){
          $htmlstring .= 'Inactive </td>';
          }else{
          $htmlstring .= 'Active </td>';
          }
          $htmlstring .= '<td>0</td>';
          $htmlstring .= '<td>0</td>';
          $htmlstring .= '<td><div class = "ThreeDotsImg"><a class = "dots" rel = "88_one" href = "javascript:void(0);">';
          $htmlstring .= '<img alt = "" src = "/ndorsedev/img/3dots.png"> </a>';
          $htmlstring .= '<div class = "arrow_box 88_one" style = "display: none;">';
          $htmlstring .= '<div class = "pull-right popupArrow">';
          $htmlstring .= '<img alt = "" src = "/ndorsedev/img/popupArrow.png"></div>';
          $htmlstring .= '<ul>';
          $htmlstring .= '<li id = "statuschanges_88"><a data-toggle = "modal" onclick = "changestatus()" href = "#">Inactivate</a></li>';
          $htmlstring .= '<li><a data-target = "#myModa2_88" data-toggle = "modal" href = "#">Delete</a></li>';
          $htmlstring .= '</ul>';
          $htmlstring .= '</div>';
          $htmlstring .= '</div></td>';
          $htmlstring .= '</tr>';
          }
          echo $htmlstring; */
    }

    function searchendorsement() {
        $this->loadModel("Organization");
        $this->loadModel("Endorsement");
        $this->loadModel("OrgDepartment");
        $this->layout = "ajax";
        $this->autoRender = false;
        $orgid = $this->request->data["orgid"];
        $searchvalue = trim($this->request->data["searchvalue"]);
        $totalrecords = isset($this->request->data["totalrecords"]) ? $this->request->data["totalrecords"] : "";

        $endorsementdata = $this->Endorsement->find("all", array("conditions" => array("organization_id" => $orgid)));
        $input = preg_quote($searchvalue, '~');
        $input = strtolower($input);
        $departments = $this->Common->getorgdepartments($orgid);
        $departmentsarray = array_map('strtolower', $departments);
        $resultdepartments = preg_grep('~' . $input . '~', $departmentsarray);
        $entities = $this->Common->getorgentities($orgid);
        $entitiesarray = array_map('strtolower', $entities);
        $resultentities = preg_grep('~' . $input . '~', $entitiesarray);

        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($orgid);
        $allvalues = array("department" => $departments, "entities" => $entities, "orgcorevaluesandcode" => $orgcorevaluesandcode);
        $searchedvalues = array("department" => $resultdepartments, "entities" => $resultentities, "orgcorevaluesandcode" => $orgcorevaluesandcode);
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
        $orgdata = $this->Endorsement->findAllByOrganizationId($orgid);
        foreach ($orgdata as $endorsementdata) {
            if ($endorsementdata["Endorsement"]["endorsement_for"] == "user") {
                $userid[] = $endorsementdata["Endorsement"]["endorsed_id"];
                $userid[] = $endorsementdata["Endorsement"]["endorser_id"];
            } else if ($endorsementdata["Endorsement"]["endorsement_for"] == "department") {
                //$department_id[] = $endorsementdata["Endorsement"]["endorsed_id"];
            }
        }
        $userid = (array_unique($userid));
        $userdetails = array();
        $searcheddetails = array();
        if (!empty($userid)) {
            $totaluserdetails = $this->User->find("all", array("conditions" => array("id" => $userid, "OR" => array("concat(fname,' ',lname) LIKE '%$searchvalue%'", "fname LIKE '%$searchvalue%'", "lname LIKE '%$searchvalue%'")), "fields" => array("id", "fname", "lname", "image")));
            if (!empty($totaluserdetails)) {
                foreach ($totaluserdetails as $userdetail) {
                    //$userdetails[$userdetail["User"]["id"]] = $userdetail;
                    $searcheddetails[$userdetail["User"]["id"]] = $userdetail;
                }
            } else {
                //$totaluserdetails = $this->OrgDepartment->find("all", array("conditions" => array("id" => $userid, "name LIKE '%$searchvalue%'"), "fields" => array("id", "name")));
            }
        }



//        if(!empty($department_id)){
//            $searcheddetail = $this->OrgDepartment->find("all", array("conditions" => array("id" => $department_id, "name LIKE '%$searchvalue%'"), "fields" => array("id","name")));
//            foreach($searcheddetail as $searcheddetailss){
//                $searcheddetails[$searcheddetailss["OrgDepartment"]["id"]] = $searcheddetailss["OrgDepartment"];
//            }
//        }

        $user_details = 0;
        if (!empty($searcheddetails)) {
            $user_details = implode(", ", array_keys($searcheddetails));
        }

        if (!empty($userid)) {
            array_unique($userid);
            $totaluserdetail = $this->User->find("all", array("conditions" => array("id" => $userid), "fields" => array("id", "fname", "lname", "image")));
            if (!empty($totaluserdetail)) {
                foreach ($totaluserdetail as $userdetailed) {
                    $userdetails[$userdetailed["User"]["id"]] = $userdetailed;
                    //$searcheduserdetails[$userdetail["User"]["id"]] = $userdetail;
                }
            }
        }
        $departments_details = 0;
        if (!empty($resultdepartments)) {
            $departments_details = implode(", ", array_keys($resultdepartments));
        }


        $entities_details = 0;
        if (!empty($resultentities)) {
            $entities_details = implode(", ", array_keys($resultentities));
        }

        $condition = '';
        if ($entities_details != 0) {
            $condition .= "(endorsement_for = 'entity' AND endorsed_id in ($entities_details))";
        }
        if ($departments_details != 0) {
            if (!empty($condition)) {
                $condition .= " OR ";
            }
            $condition .= "(endorsement_for = 'department' AND endorsed_id in ($departments_details))";
        }
        if ($user_details != 0) {
            if (!empty($condition)) {
                $condition .= " OR ";
            }
            $condition = "(endorsement_for = 'user' AND (endorsed_id in ($user_details) or endorser_id in ($user_details)))";
        }
        if ($condition != "" && $totalrecords == "") {
            $this->Organization->bindModel(array(
                'hasMany' => array(
                    "Endorsement" => array(
                        "className" => "Endorsement",
                        "conditions" => array($condition),
                        "order" => "Endorsement.created DESC",
                        "limit" => 20
                    ),
                )
                    )
            );
        } else if ($condition != "" && $totalrecords != "") {
            $this->Organization->bindModel(array(
                'hasMany' => array(
                    "Endorsement" => array(
                        "className" => "Endorsement",
                        "conditions" => array($condition),
                        "order" => "Endorsement.created DESC",
                        "limit" => 20,
                        "offset" => $totalrecords
                    ),
                )
                    )
            );
        }
        $this->Organization->recursive = 2;
        $orgdata = $this->Organization->findById($orgid);
        //echo $this->Organization->getLastQuery();
        $this->set('orgdata', $orgdata);
        $this->set(compact("userdetails", "allvalues"));
        $htmlstring = $this->render('/Elements/livesearchdata');
    }

    function searchusers() {
        $this->User->bindModel(array('hasOne' => array('UserOrganization')));
        $this->loadModel("Organization");
        $this->loadModel("User");
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $orgid = $this->request->data["orgid"];
        $searchvalue = $this->request->data["searchvalue"];

        //Commented by Babulal Prasad to add more filter @29-may-2019
        //$org_user_data = $this->User->find('all', array('limit' => 20, 'conditions' => array('OR' => array("concat(User.fname,' ',User.lname) LIKE '%$searchvalue%'", "User.fname LIKE '%$searchvalue%'", "User.lname LIKE '%$searchvalue%'", "User.email LIKE '%$searchvalue%'"), 'UserOrganization.organization_id' => $orgid, 'UserOrganization.user_role' => array(3, 2), 'UserOrganization.status' => array(0, 1, 3)), 'order' => 'UserOrganization.user_role'));

        $jobtitle = isset($this->request->data["jobtitles"]) ? $this->request->data["jobtitles"] : "";
        $departments = isset($this->request->data["departments"]) ? $this->request->data["departments"] : "";
        $status = isset($this->request->data["status"]) ? $this->request->data["status"] : "";
        $usertype = isset($this->request->data["usertype"]) ? $this->request->data["usertype"] : "";
        $conditions = '';

        if (!empty($jobtitle)) {
            $conditions["UserOrganization.job_title_id"] = $jobtitle;
        }
        if (!empty($status)) {
            $conditions["UserOrganization.status"] = $status;
        } else {
            $conditions["UserOrganization.status"] = array(0, 1, 3);
        }
        if (!empty($departments)) {
            $conditions["UserOrganization.department_id"] = $departments;
        }

        if (!empty($usertype)) {
            $conditions["UserOrganization.user_role"] = $usertype;
        } else {
            $conditions["UserOrganization.user_role"] = array(3, 2, 6);
        }


        //pr($conditions); exit;
//        $data = array(
//            'limit' => 20,
//            'conditions' => array('OR' => array("concat(User.fname,' ',User.lname) LIKE '%$searchvalue%'", "User.fname LIKE '%$searchvalue%'", "User.lname LIKE '%$searchvalue%'", "User.email LIKE '%$searchvalue%'"),
//                'UserOrganization.organization_id' => $orgid,
//                'UserOrganization.user_role' => array(3, 2),
//                $conditions,
//                'UserOrganization.status' => array(0, 1, 3)), 'order' => 'UserOrganization.user_role');
//        pr($data);
        $org_user_data = $this->User->find('all', array(
            'limit' => 20,
            'conditions' => array('OR' => array("concat(User.fname,' ',User.lname) LIKE '%$searchvalue%'", "User.fname LIKE '%$searchvalue%'", "User.lname LIKE '%$searchvalue%'", "User.email LIKE '%$searchvalue%'"),
                'UserOrganization.organization_id' => $orgid,
                $conditions,
            ), 'order' => 'UserOrganization.user_role'));




        $totalrecords = count($org_user_data);
        $this->loadModel("OrgRequest");
        $this->loadModel("Endorsement");



        $conditions[] = array("Organization.id" => $orgid);

        $this->Organization->bindModel(array(
            'hasMany' => array(
                'Invite' => array(
                    'className' => 'Invite',
                ),
                'UserOrganization' => array(
                    'className' => 'UserOrganization',
                )
            )
        ));

        $this->Organization->recursive = 2;

//        $orgdata = $this->Organization->find("all", array("conditions" => $conditions));
//        
//        echo $this->Organization->getLastQuery();
        $orgdata = $this->Organization->findById($orgid);
//        pr($orgdata);
//        exit;
        $orgstatus = $orgdata["Organization"]["status"];
        $userorg = $orgdata["UserOrganization"];
        $this->set('admin_id', $orgdata["Organization"]["admin_id"]);
        $this->set('orguser_id', $this->Auth->User("id"));
        $this->set('org_user_data', $org_user_data);
        $this->set(compact("orgdata", "orgstatus", "totalrecords"));
        $htmlstring = $this->render('/Elements/userslisting');

        //$this->set(compact("userdetails", "allvalues"));
    }

    function submitfaqform() {
        try {
            $this->loadModel("Organization");
            $this->loadModel("User");
            $this->layout = "ajax";
            $this->autoRender = false;
            $name = $this->request->data["name"];
            $email = $this->request->data["email"];
            $subject = $this->request->data["subject"];
            $msg = $this->request->data["msg"];
            $template = "usersfaq";
            $viewVars = array("name" => $name, "email" => $email, "subject" => $subject, "message" => $msg);

            /** Added by Babulal Prasad @7-feb-2018 to unsubscribe from emails * */
            $logged_in_user_id = $this->Auth->user('id');
            $userIdEncrypted = base64_encode($logged_in_user_id);
            $rootUrl = Router::url('/', true);
            //$rootUrl = str_replace("http", "https", $rootUrl);
            //Added by saurabh on 23/06/2021
            //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
            $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
            $viewVars["pathToRender"] = $pathToRender;
            /**/

            //=======Role 1 will be superadmin
            //=======dont delete this needs to be on atlast
            $emailstosend = $this->User->find("all", array("fields" => array("email"), "conditions" => array("role" => 1, "status" => 1)));
            foreach ($emailstosend as $emailstoadmin) {
                $this->Common->sendfaqmail($emailstoadmin["User"]["email"], "nDorse FAQ Email", $template, $viewVars);
            }

            //$this->Common->sendfaqmail("dugararchit.arcgate@gmail.com", "nDorse FAQ Email", $template, $viewVars);
            echo json_encode(array("message" => "Message Sent"));
        } catch (Exception $e) {
            echo json_encode(array("message" => $e));
        }
        exit;
    }

    function loadmoreusers() {
//================loadmore data for users index page
        $this->loadModel("User");
        $this->loadModel("Organization");
        $this->User->bindModel(array('hasOne' => array('UserOrganization')));
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $totalrecords = $this->request->data["totalrecords"];
        $searchkeyword = $this->request->data["searchkeyword"];
        $orgid = $this->request->data["orgid"];


        $jobtitle = isset($this->request->data["jobtitles"]) ? $this->request->data["jobtitles"] : "";
        $departments = isset($this->request->data["departments"]) ? $this->request->data["departments"] : "";
        $status = isset($this->request->data["status"]) ? $this->request->data["status"] : "";
        $usertype = isset($this->request->data["usertype"]) ? $this->request->data["usertype"] : "";
        $conditions_new = array();

        if (!empty($jobtitle)) {
            $conditions_new["UserOrganization.job_title_id"] = $jobtitle;
        }
        if (!empty($status)) {
            $conditions_new["UserOrganization.status"] = $status;
        } else {
            $conditions_new["UserOrganization.status"] = array(0, 1, 3);
        }
        if (!empty($departments)) {
            $conditions_new["UserOrganization.department_id"] = $departments;
        }

        if (!empty($usertype)) {
            $conditions_new["UserOrganization.user_role"] = $usertype;
        } else {
            $conditions_new["UserOrganization.user_role"] = array(2, 3, 6);
        }



        $conditions = array('UserOrganization.organization_id' => $orgid);

        $conditions = array_merge($conditions, $conditions_new);

        if ($searchkeyword != "") {
            $conditions['OR'] = array("User.fname LIKE '%$searchkeyword%'", "User.lname LIKE '%$searchkeyword%'");
        }

        $org_user_data = $this->User->find('all', array('offset' => $totalrecords, 'limit' => 20, 'conditions' => $conditions, 'order' => 'UserOrganization.user_role'));

        //echo $totalrecords = $totalrecords-10;
        //$orgstatus = $this->Organization->field("status,admin_id", array("id" => $orgid));
        //$orgstatus = $this->Organization->findById($orgid, array("status", "admin_id"));
        $orgdata = $this->Organization->findById($orgid);
        //$org_user_data = $this->User->find('all', array('limit' => 5, 'conditions' => array( "NOT" => array("UserOrganization.id" => $totalpostvalues), 'UserOrganization.organization_id' => $orgid, 'UserOrganization.user_role' => array(3, 4), 'UserOrganization.status' => array(0, 1, 3)), 'order' => 'UserOrganization.id  DESC'));
        //$totaluserrecords = $this->User->find("count", array("conditions" => array('User.role' => '2', "User.status" => array(0, 1))));
        $this->set('org_user_data', $org_user_data);
        $this->set('admin_id', $orgdata["Organization"]["admin_id"]);
        $this->set('orguser_id', $this->Auth->User("id"));

        //$this->set(compact("orgstatus", "totalrecords"));
        $this->set("orgstatus", $orgdata["Organization"]["status"]);
        $this->set("orgdata", $orgdata);

        echo $htmlstring = $this->render('/Elements/userslisting');

        exit;
    }

    function loadmoreorganizations() {
        $this->loadModel("Organization");
        $this->layout = "ajax";
        $this->autoRender = false;
        $totalrecords = $this->request->data["totalrecords"];
        $searchkeyword = $this->request->data["searchkeyword"];
        /** Added by Babulal Prasad at 12212016**** */
        $orgType = $this->request->data["orgType"];
        $userOrgStatus = 1;
        if ($orgType == 'inactive') {
            $userOrgStatus = 0;
        }
        $logged_in_user_role = $this->Auth->user('role');
        $logged_in_user_id = $this->Auth->user('id');
        if ($logged_in_user_role == 2) {
            // $conditions = array('Organization.status' => array(0, 1), 'Organization.admin_id' => $logged_in_user_id);
            $this->LoadModel("UserOrganization");
            // $conditions = array('Organization.status' => array(0, 1), 'Organization.admin_id' => $logged_in_user_id);
            // $conditions = array('Organization.status' => array(0, 1), 'UserOrganization.user_role' => 2,'UserOrganization.user_id' => $logged_in_user_id,'UserOrganization.status' => 1);
            $userorgdata = $this->UserOrganization->find("all", array("conditions" => array("user_id" => $logged_in_user_id, "user_role" => 2, 'UserOrganization.status' => 1)));

            $organization_id = array();
            foreach ($userorgdata as $uservalorg) {

                $organization_id[] = $uservalorg["UserOrganization"]["organization_id"];
            }

            $conditions = array('Organization.status' => array(0, 1), 'Organization.id' => $organization_id);
        } else if ($logged_in_user_role == 1) {
            //$conditions = array('Organization.status' => array(0, 1));
            /** Added by Babulal Prasad at 12212016**** */
            if ($orgType == 'all') {
                $conditions = array('Organization.status' => array(0, 1));
            } else {
                $conditions = array('Organization.status' => $userOrgStatus);
            }
        }
        if ($searchkeyword != "") {
            $conditions['OR'] = array("Organization.name LIKE '%$searchkeyword%'", "Organization.short_name LIKE '%$searchkeyword%'");
        }
        //pr($conditions);
        $this->LoadModel("OrgRequest");
        $this->LoadModel("Endorsement");
//        $this->Organization->bindModel(array(
//            'hasMany' => array(
//                'Invite' => array(
//                    'className' => 'Invite',
//                ),
//                'UserOrganization' => array(
//                    'className' => 'UserOrganization',
//                ),
//                'Transactions' => array(
//                    'className' => 'Transactions',
//                    'conditions' => array('Transactions.status' => 'canceled')
//                )
//            ),
////            'hasOne' => array('Subscription' => array(
////                    'className' => 'Subscription',
////                    'conditions' => array('Subscription.status' => '1')
////                ))
//        ));

        /*         * * ADDED By Babulal Prasad @ 12212016 Start*** */
//        if ($orgType == 'all' || $orgType == 'inactive' || $orgType == 'nosubscription') {
//            $joinType = 'LEFT';
//        } else {
//            $joinType = 'RIGHT';
//        }
//
        if ($orgType == 'trial') {
            $conditionsArray["conditions"] = array_merge(array('Subscription.type' => 'trial', 'is_deleted' => 0), $conditions);
        } elseif ($orgType == 'subscription') {
            $conditionsArray["conditions"] = array_merge(array('Subscription.type' => 'paid', 'is_deleted' => 0), $conditions);
        } elseif ($orgType == 'nosubscription') {
            $conditionsArray["conditions"] = $conditions;
        } else {
            $conditionsArray["conditions"] = $conditions;
        }
        //pr($conditionsArray); exit;
//        $joins = array(
//            array(
//                'table' => 'subscriptions',
//                'alias' => 'Subscription',
//                'type' => $joinType,
//                'conditions' => array(
//                    'Subscription.organization_id = Organization.id'
//                )
//            )
//        );
        /*         * * ADDED By Babulal Prasad @ 12212016 END*** */

        $this->Organization->recursive = 0;

        $orgBasicData = $this->Organization->find("all", array('fields' => array('id', 'admin_id', 'name', 'short_name', 'country', 'state', 'city', 'zip', 'street', 'status', 'image', 'about'),
            "order" => "Organization.created DESC", "offset" => $totalrecords, "limit" => 20, "conditions" => $conditionsArray['conditions']));
        //$orgdata = $this->Organization->find("all", array("fields"=> array('Subscription.*','Organization.*'), 'joins' => $joins,"conditions" => $conditionsArray['conditions'],"limit" => 20));
        $orgDataIndexed = array();
//                pr($orgBasicData);
//                exit;
        $organizationIds = array();
        foreach ($orgBasicData as $key => $orgBdata) {
            $orgID = $orgBdata['Organization']['id'];
            $organizationIds[] = $orgID;
            $orgDataIndexed[$orgID] = $orgBdata['Organization'];
        }
//                pr($organizationIds); 
//                exit;
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));
        $userOrgData = $this->UserOrganization->find('all', array('fields' => array('id', 'organization_id', 'status', 'joined', 'flow', 'send_invite'),
            'conditions' => array('organization_id' => $organizationIds, 'status' => 1)));
        $orgdata = array();
        if (!empty($userOrgData)) {
            foreach ($userOrgData as $indx => $uOrgData) {
                $orgID = $uOrgData['UserOrganization']['organization_id'];
                $userOrgID = $uOrgData['UserOrganization']['id'];
                $orgdata[$orgID]['Organization'] = $orgDataIndexed[$orgID];
                $orgdata[$orgID]['UserOrganization'][] = $uOrgData['UserOrganization'];
            }
        }
        rsort($orgdata);



        $user_role = array(3, 2);
        //print_r($orgdata);
        $adminusr = array();
//        
//                $log = $this->Organization->getDataSource()->getLog(false, false);
//        echo "<hr>";
//        pr($log);
//        exit;
        /** Added by Babulal Prasad added Filter tags Start** */
//        $filterOrg = array();
//        switch ($orgType) {
//            case 'trial':
//                foreach ($orgdata as $index => $orgDATA) {
//                    if (isset($orgDATA['Subscription']) && !empty($orgDATA['Subscription'])) {
//                        if ($orgDATA['Subscription']['type'] == 'trial') {
//                            //TRIAL ORGANIZATION
//                            $filterOrg[] = $orgDATA;
//                        }
//                    }
//                }
//                break;
//            case 'subscription':
//                foreach ($orgdata as $index => $orgDATA) {
//                    if (isset($orgDATA['Subscription']) && $orgDATA['Subscription']['id'] != '') {
//                        if ($orgDATA['Subscription']['type'] == 'paid') {
//                            //PAID SUBSCRIPTION ORGANIZATION
//                            $filterOrg[] = $orgDATA;
//                        }
//                    }
//                }
//                break;
//            case 'nosubscription':
//                foreach ($orgdata as $orgid => $orgDATA) {
//                    if (isset($orgDATA['Subscription']) && $orgDATA['Subscription']['id'] == '') {
//                        //NO-SUBSCRIPTION ORGANIZATION
//                        $filterOrg[] = $orgDATA;
//                    }
//                }
//                break;
//
//            default:
//                //DO NOTHING
//                $filterOrg = $orgdata;
//                break;
//        }
//        $orgdata = $filterOrg;


        /** Added by Babulal Prasad added Filter tags End** */
        $user_role = array(2, 3);
        $adminusr = array();
        foreach ($orgdata as $key => $orgid) {
            $target_id = $orgid["Organization"]["id"];
            $owner_id = $orgid["Organization"]["admin_id"];
            $totalorgusers = $this->Common->getusersfororg($target_id, $user_role);
            $orgowner = $this->Common->getorgownername($owner_id);
            $totalusers[$target_id] = $totalorgusers;
            $ownersarray[$target_id][$owner_id] = $orgowner;
            $userorg = $orgid["UserOrganization"];
//            foreach ($userorg as $uval) {
//                if ($uval["user_role"] == 2) {
//                    $adminusr[] = $uval["user_id"];
//                }
//            }

            $inviationStats[$target_id] = $this->Common->getInvitationDetails_2($userorg);

//            $totalinvitationsaccepted[$target_id] = $this->Common->userorgcounter($userorg);
//            $invitation_accepted[$target_id] = $totalinvitationsaccepted[$target_id]["web"] + $totalinvitationsaccepted[$target_id]["app"];
//            $invitations_array[$target_id] = $this->Common->invitations_fetching($orgid);
//            $invitation_pending[$target_id] = $invitations_array[$target_id]["invitations_pending"];
//            $invitation_pending[$target_id]["web"] = $totalinvitationsaccepted[$target_id]["web"] + $invitation_pending[$target_id]["web"];
//            $invitation_pending[$target_id]["app"] = $totalinvitationsaccepted[$target_id]["app"] + $invitation_pending[$target_id]["app"];
//            $totalinvitations[$target_id] = array("invitation_accepted" => $invitation_accepted, "invitation_pending" => $invitation_pending);
            $pendingrequescounter = 0;
            $endorsementformonth = 0;
//            $pendingrequescounter[$target_id] = $this->OrgRequest->find("count", array("conditions" => array("organization_id" => $target_id, "status" => 0)));
//            $endorsementformonth[$target_id] = $this->Common->endorsementformonth($target_id);
//            foreach ($orgid['Transactions'] as $transaction) {
//
//                if ($transaction["status"] == "canceled") {
//                    $adminusr[] = $transaction["user_id"];
//                }
//            }
        }
        $adminusrarray = array();
        if (!empty($adminusr)) {
            $params['fields'] = array("User.fname,User.lname,User.id");
            $params['conditions'] = array("id" => $adminusr);
            $userOrgarray = $this->User->find("all", $params);

            foreach ($userOrgarray as $val) {
                $adminusrarray[$val["User"]["id"]] = $val["User"]["fname"] . " " . $val["User"]["lname"];
            }
        }
        //$this->set(compact('orgdata', 'totalusers'));
        $this->set('authUser', $this->Auth->user());
        $this->set(compact('orgdata', 'totalusers', 'invitations_array', 'pendingrequescounter', 'invitation_pending', 'invitation_accepted', 'endorsementformonth', 'ownersarray', 'adminusrarray', 'inviationStats'));
        echo $htmlstring = $this->render('/Elements/organizationslisting');
        exit;
    }

    function searchorganization() {
        ini_set('memory_limit', '512M');
        $this->loadModel("Organization");
        $this->layout = "ajax";
        $this->autoRender = false;
        $searchvalue = $this->request->data["searchvalue"];
        /** Added by Babulal Prasad at 12212016**** */
        $orgType = $this->request->data["orgType"];
        $userOrgStatus = 1;
        if ($orgType == 'inactive') {
            $userOrgStatus = 0;
        }
        $logged_in_user_role = $this->Auth->user('role');

        $logged_in_user_id = $this->Auth->user('id');

        if ($logged_in_user_role > 1) {
            //$conditions = array('Organization.status' => array(0, 1), 'Organization.admin_id' => $logged_in_user_id);
            $this->LoadModel("UserOrganization");
            // $conditions = array('Organization.status' => array(0, 1), 'Organization.admin_id' => $logged_in_user_id);
            // $conditions = array('Organization.status' => array(0, 1), 'UserOrganization.user_role' => 2,'UserOrganization.user_id' => $logged_in_user_id,'UserOrganization.status' => 1);
            $userorgdata = $this->UserOrganization->find("all", array("conditions" => array("user_id" => $logged_in_user_id, "user_role" => 2, 'UserOrganization.status' => 1)));

            $organization_id = array();
            foreach ($userorgdata as $uservalorg) {

                $organization_id[] = $uservalorg["UserOrganization"]["organization_id"];
            }

            $conditions = array('Organization.status' => array(0, 1), 'Organization.id' => $organization_id);
        } else if ($logged_in_user_role == 1) {
            /** Added by Babulal Prasad at 12212016**** */
            if ($orgType == 'all') {
                $conditions = array('Organization.status' => array(0, 1));
            } else {
                $conditions = array('Organization.status' => $userOrgStatus);
            }
        }
        if (isset($searchvalue) && $searchvalue != '')
            $conditions['OR'] = array("Organization.name LIKE '%$searchvalue%'", "Organization.short_name LIKE '%$searchvalue%'");

        $this->LoadModel("OrgRequest");
        $this->LoadModel("Endorsement");
//        $this->Organization->bindModel(array(
//            'hasMany' => array(
//                'Invite' => array(
//                    'className' => 'Invite',
//                ),
//                'UserOrganization' => array(
//                    'className' => 'UserOrganization',
//                ),
//                'Transactions' => array(
//                    'className' => 'Transactions',
//                    'conditions' => array('Transactions.status' => 'canceled')
//                )
//            ),
////            'hasOne' => array('Subscription' => array(
////                    'className' => 'Subscription'
////                ))
//        ));
        /*         * * ADDED By Babulal Prasad @ 12212016 Start*** */
        if ($orgType == 'all' || $orgType == 'inactive' || $orgType == 'nosubscription') {
            $joinType = 'LEFT';
        } else {
            $joinType = 'RIGHT';
        }

        if ($orgType == 'trial') {
            $conditionsArray["conditions"] = array_merge(array('Subscription.type' => 'trial', 'is_deleted' => 0), $conditions);
        } elseif ($orgType == 'subscription') {
            $conditionsArray["conditions"] = array_merge(array('Subscription.type' => 'paid', 'is_deleted' => 0), $conditions);
        } elseif ($orgType == 'nosubscription') {
            $conditionsArray["conditions"] = $conditions;
        } else {
            $conditionsArray["conditions"] = $conditions;
        }
//        pr($conditionsArray);
//        exit;
        $joins = array(
            array(
                'table' => 'subscriptions',
                'alias' => 'Subscription',
                'type' => $joinType,
                'conditions' => array(
                    'Subscription.organization_id = Organization.id'
                )
        ));
        /*         * * ADDED By Babulal Prasad @ 12212016 END*** */
//        pr($conditionsArray);
        $this->Organization->recursive = 0;
        $orgBasicData = $this->Organization->find("all", array("offset" => 0, "limit" => 20, 'fields' => array('Organization.id', 'admin_id', 'name', 'short_name', 'country', 'state', 'city', 'zip', 'street', 'Organization.status', 'image', 'about', 'Subscription.id', 'Subscription.type'),
            'joins' => $joins, "order" => "Organization.created DESC", "conditions" => $conditionsArray['conditions']));
//        pr($orgBasicData);
//        exit;
        $orgDataIndexed = array();
//                pr($orgBasicData);
//                exit;
        $organizationIds = $orgSubscriptionData = array();
        foreach ($orgBasicData as $key => $orgBdata) {
            $orgID = $orgBdata['Organization']['id'];
            $organizationIds[] = $orgID;
            $orgDataIndexed[$orgID] = $orgBdata['Organization'];
            $orgSubscriptionData[$orgID] = $orgBdata['Subscription'];
        }
//                pr($organizationIds); 
//                exit;
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));
        $userOrgData = $this->UserOrganization->find('all', array('fields' => array('id', 'organization_id', 'status', 'joined', 'flow', 'send_invite'),
            'conditions' => array('organization_id' => $organizationIds, 'status' => 1)));
        $orgdata = array();
        if (!empty($userOrgData)) {
            foreach ($userOrgData as $indx => $uOrgData) {
                $orgID = $uOrgData['UserOrganization']['organization_id'];
                $userOrgID = $uOrgData['UserOrganization']['id'];
                $orgdata[$orgID]['Organization'] = $orgDataIndexed[$orgID];
                $orgdata[$orgID]['Subscription'] = $orgSubscriptionData[$orgID];
                $orgdata[$orgID]['UserOrganization'][] = $uOrgData['UserOrganization'];
            }
        }
        rsort($orgdata);
//        pr($orgdata);
//        exit;
//        $log = $this->Organization->getDataSource()->getLog(false, false);
//        echo "<hr>";
//        pr($log);
//        exit;
//        pr(count($orgdata));
//        pr($orgdata);exit;
        $user_role = array(3, 2);
        $adminusr = array();
        /** Added by Babulal Prasad added Filter tags Start** */
        $filterOrg = array();
////        pr($orgdata);
        switch ($orgType) {
            case 'trial':
                foreach ($orgdata as $index => $orgDATA) {
                    if (isset($orgDATA['Subscription']) && !empty($orgDATA['Subscription'])) {
                        if ($orgDATA['Subscription']['type'] == 'trial') {
                            //TRIAL ORGANIZATION
                            $filterOrg[] = $orgDATA;
                        }
                    }
                }
                break;
            case 'subscription':
                foreach ($orgdata as $index => $orgDATA) {
                    if (isset($orgDATA['Subscription']) && $orgDATA['Subscription']['id'] != '') {
                        if ($orgDATA['Subscription']['type'] == 'paid') {
                            //PAID SUBSCRIPTION ORGANIZATION
                            $filterOrg[] = $orgDATA;
                        }
                    }
                }
                break;
            case 'nosubscription':
                foreach ($orgdata as $orgid => $orgDATA) {
                    if (isset($orgDATA['Subscription']) && $orgDATA['Subscription']['id'] == '') {
                        //NO-SUBSCRIPTION ORGANIZATION
                        $filterOrg[] = $orgDATA;
                    }
                }
                break;
            case 'inactive':
                foreach ($orgdata as $orgid => $orgDATA) {
                    if (isset($orgDATA['Organization']) && $orgDATA['Organization']['status'] == 0) {
                        //NO-SUBSCRIPTION ORGANIZATION
                        $filterOrg[] = $orgDATA;
                    }
                }
                break;
            default:
                //DO NOTHING
                $filterOrg = $orgdata;
                break;
        }
        $orgdata = $filterOrg;
//        pr($orgdata); exit;
        /** Added by Babulal Prasad added Filter tags End** */
        $pendingrequescounter = 0;
        $endorsementformonth = 0;
        $inviationStats = array();
        foreach ($orgdata as $key => $orgid) {
            $target_id = $orgid["Organization"]["id"];
            $owner_id = $orgid["Organization"]["admin_id"];
            $totalorgusers = $this->Common->getusersfororg($target_id, $user_role);
            $orgowner = $this->Common->getorgownername($owner_id);
            $ownersarray[$target_id][$owner_id] = $orgowner;
            $totalusers[$target_id] = $totalorgusers;
            $userorg = $orgid["UserOrganization"];
//            foreach ($userorg as $uval) {
//                if ($uval["user_role"] == 2) {
//                    $adminusr[] = $uval["user_id"];
//                }
//            }

            $inviationStats[$target_id] = $this->Common->getInvitationDetails_2($userorg);

//            $totalinvitationsaccepted[$target_id] = $this->Common->userorgcounter($userorg);
//            $invitation_accepted[$target_id] = $totalinvitationsaccepted[$target_id]["web"] + $totalinvitationsaccepted[$target_id]["app"];
//            $invitations_array[$target_id] = $this->Common->invitations_fetching($orgid);
//            $invitation_pending[$target_id] = $invitations_array[$target_id]["invitations_pending"];
//            $invitation_pending[$target_id]["web"] = $totalinvitationsaccepted[$target_id]["web"] + $invitation_pending[$target_id]["web"];
//            $invitation_pending[$target_id]["app"] = $totalinvitationsaccepted[$target_id]["app"] + $invitation_pending[$target_id]["app"];
//            $totalinvitations[$target_id] = array("invitation_accepted" => $invitation_accepted, "invitation_pending" => $invitation_pending);
//            $pendingrequescounter[$target_id] = $this->OrgRequest->find("count", array("conditions" => array("organization_id" => $target_id, "status" => 0)));
//            $endorsementformonth[$target_id] = $this->Common->endorsementformonth($target_id);
//            foreach ($orgid['Transactions'] as $transaction) {
//
//                if ($transaction["status"] == "canceled") {
//                    $adminusr[] = $transaction["user_id"];
//                }
//            }
        }
        $adminusrarray = array();
//        if (!empty($adminusr)) {
//            $params['fields'] = array("User.fname,User.lname,User.id");
//            $params['conditions'] = array("id" => $adminusr);
//            $userOrgarray = $this->User->find("all", $params);
//
//            foreach ($userOrgarray as $val) {
//                $adminusrarray[$val["User"]["id"]] = $val["User"]["fname"] . " " . $val["User"]["lname"];
//            }
//        }
        $this->set('authUser', $this->Auth->user());
        $this->set(compact('orgdata', 'totalusers', 'invitations_array', 'pendingrequescounter', 'invitation_pending', 'invitation_accepted', 'endorsementformonth', 'ownersarray', 'adminusrarray', 'inviationStats'));
        $htmlstring = $this->render('/Elements/organizationslisting');
    }

    function searchorgowners() {
        $this->loadModel("User");
        $this->layout = "ajax";
        $this->autoRender = false;
        $searchvalue = trim($this->request->data["searchvalue"]);
        $conditions["OR"] = array("fname like '%$searchvalue%'", "lname like '%$searchvalue%'", "email like '%$searchvalue%'");
        $userdata = $this->User->find("all", array("limit" => 10, "conditions" => $conditions));
        $orgsandusers = $this->Common->getorgandusers($userdata);
        $nooforg = $orgsandusers["nooforgs"];
        $noofusers = $orgsandusers["noofusers"];
        $this->set('userdata', $userdata);
        $this->set(compact("nooforg", "noofusers", "totaluserrecords"));
        echo $htmlstring = $this->render('/Elements/rowusersindex');
        exit;
    }

    function leaderboardzoomin() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("Endorsement");
        $organization_id = $this->request->data["orgid"];
        $graphname = $this->request->data["graphname"];
        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdaterandc"]) && !empty($this->request->data["enddaterandc"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdaterandc"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddaterandc"]);
        }

        if ($graphname == "leader_board") {
            $this->loadModel("OrgDepartment");
            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues')));
            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
            //=========means number of guys he endorse
            $conditionscountendorsement = array('organization_id' => $organization_id);
            if ($startdate != "" and $enddate != "") {
                array_push($conditionscountendorsement, "date(created) between '$startdate' and '$enddate'");
            }
            //===============binding model conditions
            $this->Common->commonleaderboardbindings($conditionscountendorsement);
            $this->UserOrganization->recursive = 2;
            $endorsementdata = $this->UserOrganization->find("all", array("order" => "User.fname", "conditions" => array("UserOrganization.organization_id" => $organization_id, "UserOrganization.status" => array(0, 1, 2, 3), "UserOrganization.user_role" => array(2, 3))));
            $arrayendorsementdetail = $this->Common->arrayforendorsementdetail($endorsementdata);

            //===============finally rendering the page
            $zoomingfeature = "zooming";
            $this->set(compact("arrayendorsementdetail", "zoomingfeature", "organization_id"));
            //echo $htmlstring = $this->render('/Elements/leaderboarddata');
        } else if ($graphname == "history_by_day") {
            $conditionsendorsementbyday = array("organization_id" => $organization_id);
            if ($startdate != "" and $enddate != "") {
                array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
            }
            $endorsementbyday = $this->Endorsement->find("all", array("conditions" => $conditionsendorsementbyday, "group" => "date(Endorsement.created)", "fields" => array("count(*) as cnt", "date(created) as cdate")));
            $series = "";
            if (!empty($endorsementbyday)) {
                $seriesdata = "";
                foreach ($endorsementbyday as $lval) {
                    if ($seriesdata == "") {
                        $seriesdata = "{
                  name: '" . $this->Common->dateConvertDisplay($lval[0]["cdate"]) . "',
                 y: " . $lval[0]["cnt"] . "}";
                    } else {
                        $seriesdata .= ",{
                   name: '" . $this->Common->dateConvertDisplay($lval[0]["cdate"]) . "',
                 y: " . $lval[0]["cnt"] . "}";
                    }
                }

                //echo $seriesdata;exit;
                $series = "  {
                    name: 'Date',
                    colorByPoint: false,
                    data: [" . $seriesdata . "]}";
                //echo $seriesdata;
            }
            $this->set('data', $series);
            $zoomingfeature = "zooming";
            $this->set(compact("zoomingfeature"));
            echo $htmlstring = $this->render('/Elements/endorsementbyday_web');
            //echo $this->Element("endorsementbyday_web", array("data" => $series));
        } else if ($graphname == "history_by_department") {
            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));

            $paramsdepthistory["conditions"] = array("Endorsement.organization_id" => $organization_id, "Endorsement.endorsement_for" => "department");
            $paramsdepthistory["fields"] = ("*");
            $paramsdepthistory["group"] = ("WEEKOFYEAR(date(Endorsement.created)), Endorsement.endorsed_id");
            if ($startdate != "" and $enddate != "") {
                array_push($paramsdepthistory["conditions"], "date(Endorsement.created) between '$startdate' and '$enddate'");
            }
            $this->Endorsement->virtualFields['weekdepartment'] = "WEEKOFYEAR(date(Endorsement.created))";
            $this->Endorsement->virtualFields['yeardepartment'] = "year(date(Endorsement.created))";
            $this->Endorsement->virtualFields['endorseddepartment'] = "count(Endorsement.endorsed_id)";

            $this->Endorsement->bindModel(array(
                'hasOne' => array(
                    'OrgDepartment' => array(
                        'className' => 'OrgDepartment',
                        'foreignKey' => false,
                        'conditions' => array("OrgDepartment.id = Endorsement.endorsed_id"),
                    )
            )));

            $endorsementbydeptweek = $this->Endorsement->find("all", $paramsdepthistory);
            //echo $this->Endorsement->getLastQuery();die;
            //pr($endorsementbydeptweek);
            $startofweekarray = "";
            $counter = "";
            //pr($endorsementbydeptweek);
            $dept_array = array();
            $date_array = array();
            foreach ($endorsementbydeptweek as $endorsementdeptweek) {
                $dept_array[] = $deptname = $endorsementdeptweek["OrgDepartment"]["name"];
                $date_array[] = $startofweekarray = $this->Common->getStartAndEndDate($endorsementdeptweek["Endorsement"]["weekdepartment"], $endorsementdeptweek["Endorsement"]["yeardepartment"]);
                $counter[$startofweekarray][$deptname] = (int) $endorsementdeptweek["Endorsement"]["endorseddepartment"];
                //$startofweekarray[] = $this->Common->getStartAndEndDate($endorsementdeptweek["Endorsement"]["weekdepartment"], $endorsementdeptweek["Endorsement"]["yeardepartment"]);
            }
            $date_array = array_unique($date_array);
            $dept_array = array_unique($dept_array);
            $server_data = array();
            foreach ($dept_array as $deptname) {
                foreach ($counter as $key => $data) {
                    $dept = array_keys($data);
                    if (!in_array($deptname, $dept)) {
                        $data = 0;
                    } else {
                        $data = $counter[$key][$deptname];
                    }
                    $server_data[$deptname][] = $data;
                }
            }
            foreach ($date_array as $key => $converteddatearray) {
                $converted_date_array[$key] = $this->Common->dateConvertDisplay($converteddatearray);
            }
            #pr($server_data);
            #pr($counter);die;
            if (!empty($counter)) {
                $counter = $server_data;
                $counter = json_encode(array('counter' => $counter, 'date_array' => $converted_date_array));
            }
            $zoomingfeature = "zooming";
            $this->set(compact("zoomingfeature", "counter", "startofweekarray"));
            echo $htmlstring = $this->render('/Elements/endorsementhistorybydept_web');
        } else if ($graphname == "by_department") {
            $params['fields'] = "count(Endorsement.endorsed_id) as cnt,OrgDepartments.name as department";
            $conditionarray["Endorsement.organization_id"] = $organization_id;
            $conditionarray["Endorsement.endorsement_for"] = "department";
            if ($startdate != "" and $enddate != "") {
                array_push($conditionarray, "date(Endorsement.created) between '$startdate' and '$enddate'");
            }
            $params['conditions'] = $conditionarray;
            $params['joins'] = array(
                array(
                    'table' => 'org_departments',
                    'alias' => 'OrgDepartments',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OrgDepartments.id =Endorsement.endorsed_id'
                    )
                )
            );
            $params['order'] = 'cnt desc';
            $params['group'] = 'Endorsement.endorsed_id';
            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
            $leaderboard = $this->Endorsement->find("all", $params);
            $series = "";
            if (!empty($leaderboard)) {
                $seriesdata = "";
                foreach ($leaderboard as $lval) {
                    if ($seriesdata == "") {
                        $seriesdata = "{
                            name: '" . addslashes($lval["OrgDepartments"]["department"]) . "',
                                y: " . $lval[0]["cnt"] . "}";
                    } else {
                        $seriesdata .= ",{
                            name: '" . addslashes($lval["OrgDepartments"]["department"]) . "',
                            y: " . $lval[0]["cnt"] . "}";
                    }
                }
                $series = "  {
                    name: 'organization',
                    colorByPoint: true,
                    data: [" . $seriesdata . "]}";
            }
            $this->set('data', $series);
            $zoomingfeature = "zooming";
            $this->set(compact("zoomingfeature"));
            echo $htmlstring = $this->render('/Elements/endorsementbydept_web');
        } else if ($graphname == "by_jobtitle") {
            $this->Common->bindmodelcommonjobtitle();
            $jobtitles = $this->Common->getorgjobtitles($organization_id);
            $jobtitlesid = array_keys($jobtitles);
            $conditionsjobtitles = array(
                "UserOrganization.job_title_id" => $jobtitlesid,
                "UserOrganization.organization_id" => $organization_id,
                "UserOrganization.status" => 1,
                "Endorsement.organization_id" => $organization_id,
                "Endorsement.endorsement_for" => "user"
            );
            if ($startdate != "" and $enddate != "") {
                array_push($conditionsjobtitles, "date(Endorsement.created) between '$startdate' and '$enddate'");
            }
            //=============using below query
            /* select user_organizations.job_title_id, count(*) from user_organizations inner join endorsements on user_organizations.user_id = endorsements.endorser_id where endorsements.organization_id = 335 and  user_organizations.job_title_id in (550,551,552) and user_organizations.organization_id  = 335 and  user_organizations.status = 1  group by  user_organizations.job_title_id
              select user_organizations.job_title_id, count(*) from user_organizations inner join endorsements on user_organizations.user_id = endorsements.endorsed_id  where endorsements.organization_id = 335 and endorsements.endorsement_for = "user" and  user_organizations.job_title_id in (550,551,552) and user_organizations.organization_id  = 335 and  user_organizations.status = 1  group by  user_organizations.job_title_id */
            //=============using below query
            $groupjobtitle = array("UserOrganization.job_title_id");
            $fieldsjobtitle = array("UserOrganization.job_title_id, count(*)");
            //$this->UserOrganization->virtualfield["counterjobtitle"] = ""
            $jobtitledataendorsed = $this->UserOrganization->find("all", array("conditions" => $conditionsjobtitles, "group" => $groupjobtitle, "fields" => $fieldsjobtitle));
            $jbiddata = array();
            foreach ($jobtitledataendorsed as $endorserjbdata) {
                $jbiddata[$endorserjbdata["UserOrganization"]["job_title_id"]] = $endorserjbdata[0]["count(*)"];
            }

            $detailedjobtitlechart = array("data" => $jbiddata, "jobtitles" => $jobtitles);
            $seriesjbtitle = "";
            $htmljbtitledata = "";
            if (!empty($detailedjobtitlechart)) {
                foreach ($detailedjobtitlechart["data"] as $name => $yaxis) {
                    if (isset($detailedjobtitlechart["jobtitles"][$name])) {
                        $htmljbtitledata .= "{
                                name:'" . $detailedjobtitlechart["jobtitles"][$name] . "',
                                y:" . $yaxis . ",
                            },";
                    }
                }
            }
            $seriesjbtitle = "  {
                    name: 'jbendorsement',
                    colorByPoint: true,
                    data: [" . $htmljbtitledata . "]}";

            $dataarray = array("data" => $seriesjbtitle, "chartfor" => "jobtitle", "zoomchart" => "yes");
            $this->set(compact("dataarray"));
            echo $htmlstring = $this->render('/Elements/endorsementspiecharts_web');
            //echo $this->Element("endorsementspiecharts_web", array("dataarray" => $dataarray));
        } else if ($graphname == "by_suborganization") {
            $entityarray = $this->Common->getorgentities($organization_id);
            $conditionsentity = array("Endorsement.endorsement_for" => "entity", "Endorsement.organization_id" => $organization_id);
            if ($startdate != "" and $enddate != "") {
                array_push($conditionsentity, "date(Endorsement.created) between '$startdate' and '$enddate'");
            }
            $fieldsentity = array("Endorsement.endorsed_id, count(*)");
            $groupentity = array("Endorsement.endorsed_id");
            $entityiddata = array();
            $endorsementdataentity = $this->Endorsement->find("all", array("conditions" => $conditionsentity, "group" => $groupentity, "fields" => $fieldsentity));
            foreach ($endorsementdataentity as $entitydata) {
                $entityiddata[$entitydata["Endorsement"]["endorsed_id"]] = $entitydata[0]["count(*)"];
            }
            $detailedentitychart = array("data" => $entityiddata, "entites" => $entityarray);
            $seriesentity = "";
            $htmlentity = "";
            if (!empty($detailedentitychart)) {
                $htmlentitydata = "";
                foreach ($detailedentitychart["data"] as $name => $yaxis) {

                    //======as in if elelment is deleted so we need to check
                    if (isset($detailedentitychart["entites"][$name])) {
                        $htmlentity .= "{
                            name:'" . $detailedentitychart["entites"][$name] . "',
                            y:" . $yaxis . ",
                        },";
                    }
                }
            }
            $seriesentity = "  {
                name: 'jbendorsement',
                colorByPoint: true,
                data: [" . $htmlentity . "]}";

            $dataarray = array("data" => $seriesentity, "chartfor" => "entity", "zoomchart" => "yes");
            $this->set(compact("dataarray"));
            echo $htmlstring = $this->render('/Elements/endorsementspiecharts_web');
        }
        exit;
    }

    function searchleaderboard() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $organization_id = $this->request->data["orgid"];
        $searchvalue = $this->request->data["searchvalue"];
        $startdate = $this->request->data["searchstartdate"];
        $enddate = $this->request->data["searchenddate"];
        $conditionscountendorsement = array('organization_id' => $organization_id);
        if ($startdate != "" and $enddate != "") {
            array_push($conditionscountendorsement, "date(created) between '$startdate' and '$enddate'");
        }
        $this->Common->commonleaderboardbindings($conditionscountendorsement);
        $this->UserOrganization->recursive = 2;
        $conditionsarray = array("UserOrganization.organization_id" => $organization_id, "UserOrganization.status" => array(0, 1, 2, 3), "UserOrganization.user_role" => array(2, 3));
        if ($searchvalue != "") {
            $conditionsarray["OR"] = array("concat(User.fname,' ',User.lname) LIKE '%$searchvalue%'", "User.fname like '%$searchvalue%'", "User.lname like '%$searchvalue%'");
        }

        $endorsementdata = $this->UserOrganization->find("all", array("order" => "User.fname", "conditions" => $conditionsarray));
        $arrayendorsementdetail = $this->Common->arrayforendorsementdetail($endorsementdata);
        $this->set(compact("arrayendorsementdetail"));
        echo $htmlstring = $this->render('/Elements/leaderboardsearching');
        exit;
    }

    function addsubscriptionadmin() {
        $loggeduser = $this->Auth->User();
        if ($this->request->is('post')) {
            try {

                $this->layout = 'ajax';
                $this->render = false;
                $this->loadModel('Subscription');
                $this->loadModel('Transaction');
                $this->loadModel('UserOrganization');
                //  print_r($this->request->data);
                $org_id = $this->request->data['org_id'];
                $mode = $this->request->data['mode'];
                $duration = $this->request->data['duration'];
                $qty = $this->request->data['qty'];
                $amt = $this->request->data['amt'];
                //$oldest = "yes";
                $istrial = 0;
                $startdate = date('Y-m-d 00:00:00', time());
                $type = "paid";
                if ($mode == "trial") {
                    $istrial = 1;
                    $amt = 0;
                    $enddate = date('Y-m-d 23:59:59', strtotime('+' . $duration . ' month'));
                    $type = "trial";
                } else {
                    $enddate = date('Y-m-d 23:59:59', strtotime('+' . $duration . ' year'));
                }
                $enddate = date('Y-m-d 23:59:59', strtotime($enddate . '-1 day'));
                $subscription = array("user_id" => $loggeduser['id'], "organization_id" => $org_id, "pool_purchased" => $qty, "payment_method" => "ndorse", "amount_paid" => $amt, "istrial" => $istrial, "start_date" => $startdate, "end_date" => $enddate, "type" => $type);
                $this->Subscription->save($subscription);
                // save email
                //Removed as per client requirement
                /* $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))), "conditions" => array("organization_id" => $org_id, "user_role" => 2, 'UserOrganization.status' => 1)));
                  $adminorg = array();
                  //echo $this->UserOrganization->getLastQuery();
                  //print_r($userorgdata);
                  foreach ($userorgdata as $orgval) {
                  //$adminorg[] = $uservalorg;
                  if ($type == "paid") {
                  $subject = "nDorse Notification – Your subscription is ACTIVE!";
                  } else {
                  $subject = "nDorse Notification -- Trial Subscription Created Successfully";
                  }
                  $viewVars = array("org_name" => $orgval['Organization']['name'], "type" => $type, "fname" => trim($orgval['User']['fname']));
                  $configVars = serialize($viewVars);
                  $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "create_subscription_su");
                  }
                  if (!empty($emailQueue)) {
                  $this->loadModel('Email');
                  //                    $this->Email->saveMany($emailQueue); //Removed as per client requirement
                  } */

                //
                //if ($oldest == "yes") {
                //    // active oldest one paid users
                //    $sqlupdate = "UPDATE user_organizations set status =1  where organization_id='" . $org_id . "' and status=0 and pool_type='paid'  order by created desc limit " . $qty;
                //    $this->Subscription->query($sqlupdate);
                //}
                $transaction = array(
                    'organization_id' => $org_id,
                    'user_id' => $loggeduser['id'],
                    'type' => "purchase",
                    'user_diff' => $qty,
                    'method' => "ndorse",
                    'amount' => $amt,
                    'status' => 'settled'
                );
                $this->Transaction->save($transaction);
                echo json_encode(array("status" => "true", "msg" => "Subscription created successfully"));
            } catch (Exception $e) {
                echo json_encode(array("status" => "true", "error" => "true", "error_message" => "Not able to Save Subscription", "msg" => $e->getMessage()));
            }
        }
        exit();
    }

//
    function overwritesubscriptionadmin() {
        $loggeduser = $this->Auth->User();
        if ($this->request->is('post')) {
            try {
                $statusConfig = Configure::read("statusConfig");
                $this->layout = 'ajax';
                $this->render = false;
                $this->loadModel('Subscription');
                $this->loadModel('UserOrganization');
                $this->loadModel('Transaction');
                //  print_r($this->request->data);
                $org_id = $this->request->data['org_id'];
                $org_id = $this->ViewCont->decodeString($org_id);
                $qty = $this->request->data['qty'];
                $amt = $this->request->data['amt'];

                $data = $this->Subscription->findByOrganizationId($org_id);

                $subscription_id = $data["Subscription"]["id"];
                //$startdate = date('Y-m-d 00:00:00', time());
                // $startdate = $data["Subscription"]["start_date"];
                $startdate = date('Y-m-d 00:00:00', time());
                $start_date = date('m-d-Y', time());
                $enddate = date('Y-m-d 23:59:59', strtotime($startdate . '+1 year'));
                $enddate = date('Y-m-d 23:59:59', strtotime($enddate . '-1 day'));
                $subscriptionarr = array("organization_id" => $org_id, "user_id" => $loggeduser["id"], "start_date" => $startdate, "end_date" => $enddate, "id" => $subscription_id, "payment_method" => "ndorse", 'amount_paid' => $amt, 'pool_purchased' => $qty, "plan_id" => 0, "cancelled" => 0, "status" => 1, "is_deleted" => 0);
                $this->Subscription->save($subscriptionarr);
                $transaction = array(
                    'organization_id' => $org_id,
                    'user_id' => $loggeduser['id'],
                    'type' => "purchase",
                    'user_diff' => $qty,
                    'method' => "ndorse",
                    'amount' => $amt,
                    'status' => 'settled'
                );
                $this->Transaction->save($transaction);

                // active oldest one paid users
                $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));

                $params['fields'] = array("COUNT(UserOrganization.user_id) as count");
                $userOrgStats = $this->UserOrganization->find("all", $params);

                $usercount = $userOrgStats[0][0]["count"];
                $allqty = $qty + 10;
                if ($usercount > $allqty) {
                    $deactiveusr = $usercount - $allqty;
                    $sqlupdate = "UPDATE user_organizations set status =0  where organization_id='" . $org_id . "' and status IN (1,3) and pool_type='paid'  order by created desc limit " . $deactiveusr;
                    $this->Subscription->query($sqlupdate);
                }

                //Removed as per client requirement
                /* $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))), "conditions" => array("organization_id" => $org_id, "user_role" => 2, 'UserOrganization.status' => 1)));

                  //echo $this->UserOrganization->getLastQuery();
                  //print_r($userorgdata);
                  $emailQueue = array();
                  $subject = "nDorse Notification – Your subscription is overriden by NDORSE LLC!";
                  foreach ($userorgdata as $orgval) {
                  $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User']['fname']), "start_date" => $start_date);
                  $configVars = serialize($viewVars);
                  $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "override_subscription_su");
                  }
                  if (!empty($emailQueue)) {
                  $this->loadModel('Email');
                  //                    $this->Email->saveMany($emailQueue); //Removed as per client requirement
                  } */

                echo json_encode(array("status" => "true", "msg" => "Subscription created successfully", "org_id" => $org_id));
            } catch (Exception $e) {
                echo json_encode(array("status" => "true", "error" => "true", "error_message" => "Not able to Save Subscription", "msg" => $e->getMessage()));
            }
        }
        exit();
    }

//
    function updatesubscriptionadmin() {
        $loggeduser = $this->Auth->User();
        if ($this->request->is('post')) {
            try {

                $this->layout = 'ajax';
                $this->render = false;
                $this->loadModel('Subscription');
                $this->loadModel('Transaction');
                $this->loadModel('UserOrganization');
                //  print_r($this->request->data);
                $org_id = $this->request->data['org_id'];

                $qty = $this->request->data['qty'];
                $amt = $this->request->data['amt'];


                $data = $this->Subscription->findByOrganizationId($org_id);
                // print_r($data);
                $pool_purchased = $data["Subscription"]["pool_purchased"] + $qty;
                $total_amount = $data["Subscription"]["amount_paid"] + $amt;
                $this->Subscription->updateAll(
                        array('amount_paid' => $total_amount, 'pool_purchased' => $pool_purchased), //fields to update
                        array('organization_id' => $org_id)  //condition
                );
                $transaction = array(
                    'organization_id' => $org_id,
                    'user_id' => $loggeduser['id'],
                    'type' => "upgrade",
                    'user_diff' => $qty,
                    'method' => 'ndorse',
                    'amount' => $amt,
                    'status' => 'settled'
                );
                $this->Transaction->save($transaction);
                //
                //Removed as per client requirement
                /* $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))), "conditions" => array("organization_id" => $org_id, "user_role" => 2, 'UserOrganization.status' => 1)));
                  $adminorg = array();
                  //echo $this->UserOrganization->getLastQuery();
                  //print_r($userorgdata);
                  $subject = "nDorse Notification - Your subscription had been UPGRADED!";
                  foreach ($userorgdata as $orgval) {
                  $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User']['fname']));
                  $configVars = serialize($viewVars);
                  $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "update_subscription_su");
                  }
                  if (!empty($emailQueue)) {
                  $this->loadModel('Email');
                  $this->Email->saveMany($emailQueue);
                  } */
                //
                // $pool_purchased  +=10;
                echo json_encode(array("status" => "true", "msg" => "Subscription u successfully", "qty" => $pool_purchased));
            } catch (Exception $e) {
                echo json_encode(array("status" => "true", "error" => "true", "error_message" => "Not able to Save Subscription", "msg" => $e->getMessage()));
            }
        }
        exit();
    }

    function downgradesubscriptionadmin() {
        $loggeduser = $this->Auth->User();
        if ($this->request->is('post')) {
            try {

                $this->layout = 'ajax';
                $this->render = false;
                $this->loadModel('Subscription');
                $this->loadModel('Transaction');
                //  print_r($this->request->data);
                $org_id = $this->request->data['org_id'];
                // $newest = $this->request->data['newset'];
                $qty = $this->request->data['qty'];
                // $down_qty = $this->request->data['down_qty'];
                $amt = $this->request->data['amt'];
                if (isset($this->request->data['select_user_id'])) {
                    $select_user_id = $this->request->data['select_user_id'];
                }
                // $checkinactive_user = $this->request->data['inact_user'];


                $data = $this->Subscription->findByOrganizationId($org_id);
                //print_r($data);
                $is_del = 0;
                $pool_purchased = 0;
                if ($data["Subscription"]["pool_purchased"] >= $qty) {
                    $pool_purchased = $data["Subscription"]["pool_purchased"] - $qty;
                    $total_amount = $data["Subscription"]["amount_paid"] - $amt;
                    $this->Subscription->updateAll(
                            array('amount_paid' => $total_amount, 'pool_purchased' => $pool_purchased), //fields to update
                            array('organization_id' => $org_id)  //condition
                    );
                    // send mail
                    //Removed as per client requirement
                    /* $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))), "conditions" => array("organization_id" => $org_id, "user_role" => 2, 'UserOrganization.status' => 1)));
                      $adminorg = array();

                      $subject = "nDorse Notification - Your subscription had been downgraded";
                      foreach ($userorgdata as $orgval) {
                      $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User']['fname']));
                      $configVars = serialize($viewVars);
                      $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "downgrade_subscription_su");
                      }
                      if (!empty($emailQueue)) {
                      $this->loadModel('Email');
                      $this->Email->saveMany($emailQueue);
                      } */
                    //
                    // end
                    //if ($newest == "yes" && $checkinactive_user == 1) {
                    //    // active oldest one paid users
                    //    $sqlupdate = "UPDATE user_organizations set status =0  where organization_id='" . $org_id . "' and status IN (1,3) and pool_type='paid'  order by created asc limit " . $down_qty;
                    //    $this->Subscription->query($sqlupdate);
                    //} elseif ($checkinactive_user == 1) {
                    //    $sqlupdate = "UPDATE user_organizations set status =0  where organization_id='" . $org_id . "' and  user_id IN(" . $select_user_id . ")";
                    //    $this->Subscription->query($sqlupdate);
                    //}
                } else {
                    $is_del = 1;
                    //$sqlupdate = "UPDATE user_organizations set status =0  where organization_id='" . $org_id . "' and   pool_type='paid' ";
                    //$this->Subscription->query($sqlupdate);
                    //$this->Subscription->delete($data["Subscription"]["id"]);
                }
                // $pool_purchased  +=10;
                $transaction = array(
                    'organization_id' => $org_id,
                    'user_id' => $loggeduser['id'],
                    'type' => "downgrade",
                    'user_diff' => $qty,
                    'method' => 'ndorse',
                    'amount' => $amt,
                    'status' => 'settled'
                );
                $this->Transaction->save($transaction);
                echo json_encode(array("status" => "true", "msg" => "Subscription u successfully", "qty" => $pool_purchased, "is_deleted" => $is_del));
            } catch (Exception $e) {
                echo json_encode(array("status" => "true", "error" => "true", "error_message" => "Not able to Save Subscription", "msg" => $e->getMessage()));
            }
        }
        exit();
    }

    function terminate() {
        $loggeduser = $this->Auth->User();
        if ($this->request->is('post')) {
            try {

                $this->layout = 'ajax';
                $this->render = false;
                $this->loadModel('Subscription');
                $this->loadModel('Transaction');
                $org_id = $this->request->data['terminate_org_id'];
                $deleteoption = $this->request->data['adminterminatesubscription'];
                $data = $this->Subscription->findByOrganizationId($org_id);
                $subscription_id = $data["Subscription"]["id"];
                $this->Subscription->id = $subscription_id;
                $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))), "conditions" => array("organization_id" => $org_id, "user_role" => 2, 'UserOrganization.status' => 1)));

                if ($deleteoption["option"] == 2) {
                    $subject = "nDorse Notification - Your nDorse App Subscription has been ended!";
                    //$start_date = $data["Subscription"]["start_date"];
                    //$enddate = date('Y-m-d 23:59:59', strtotime($start_date . " +1 month "));
                    $val = array('is_deleted' => 1);
                    $this->Subscription->save($val, false);
                } else {
                    $subject = "nDorse Notification - Your Subscription ended! ";
                    if ($data["Subscription"]["type"] == "trial") {
                        $subject = "nDorse Notification - Your Subscription is terminated";
                    }
                    $sqlupdate = "UPDATE user_organizations set status =0  where organization_id='" . $org_id . "' and status IN (1,3) and pool_type='paid' ";
                    $this->Subscription->query($sqlupdate);
                    $this->Subscription->delete($subscription_id);

                    // inactive paid users
                }

                //Removed as per client requirement
                /* foreach ($userorgdata as $orgval) {
                  $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User']['fname']), "type" => $data["Subscription"]["type"], "option" => $deleteoption["option"]);
                  $configVars = serialize($viewVars);
                  $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "terminate_subscription_su");
                  }
                  if (!empty($emailQueue)) {
                  $this->loadModel('Email');
                  $this->Email->saveMany($emailQueue);
                  } */

                $transaction = array(
                    'organization_id' => $org_id,
                    'user_id' => $this->Auth->User("id"),
                    'type' => 'cancel',
                    'user_diff' => 0,
                    'amount' => 0,
                    'method' => 'ndorse',
                    'status' => "canceled"
                );
                $this->Transaction->save($transaction);
                echo json_encode(array("status" => "true", "msg" => "Subscription u successfully", "deleted" => $deleteoption["option"], "org_id" => $org_id));
            } catch (Exception $e) {
                echo json_encode(array("status" => "false", "error" => "true", "error_message" => "Not able to Save Subscription", "msg" => $e->getMessage()));
            }
        }
        exit();
    }

    function revert() {
        $loggeduser = $this->Auth->User();
        if ($this->request->is('post')) {
            try {

                $this->layout = 'ajax';
                $this->render = false;
                $this->loadModel('Subscription');
                $org_id = $this->request->data['targetid'];

                $data = $this->Subscription->findByOrganizationId($org_id);
                $subscription_id = $data["Subscription"]["id"];
                $this->Subscription->id = $subscription_id;

                $start_date = $data["Subscription"]["start_date"];
                if (strtolower($data["Subscription"]["payment_method"]) == "ndorse") {
                    $enddate = date('Y-m-d 23:59:59', strtotime($start_date . " +1 year "));
                    $val = array('is_deleted' => 0);
                } else {
                    $val = array('is_deleted' => 0);
                }
                $this->Subscription->save($val, false);
                // send mail
                //Removed as per client requirement
                /* $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))), "conditions" => array("organization_id" => $org_id, "user_role" => 2, 'UserOrganization.status' => 1)));
                  $adminorg = array();

                  $subject = "nDorse Notification - Your subscription has been re-activated!";
                  foreach ($userorgdata as $orgval) {
                  $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User']['fname']));
                  $configVars = serialize($viewVars);
                  $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "revert_subscription_su");
                  }
                  if (!empty($emailQueue)) {
                  $this->loadModel('Email');
                  $this->Email->saveMany($emailQueue);
                  } */
                // end


                echo json_encode(array("status" => "true", "msg" => "Subscription u successfully", "org_id" => $org_id, "type" => $data["Subscription"]["type"]));
            } catch (Exception $e) {
                echo json_encode(array("status" => "false", "error" => "true", "error_message" => "Not able to Save Subscription", "msg" => $e->getMessage()));
            }
        }
        exit();
    }

    function getinactiveusers() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $orgid = $this->request->data["org_id"];
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
        $userorgdata = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $orgid, "UserOrganization.status" => array(1, 3), "UserOrganization.user_role" => array(2, 3), "UserOrganization.user_id !=" => $this->Auth->User("id")), "order" => "UserOrganization.id ASC"));

        $fresult = array();
        if (!empty($userorgdata)) {

            foreach ($userorgdata as $usersdata) {
                $fresult[$usersdata["UserOrganization"]["id"]] = array("id" => $usersdata["User"]["id"], "user_role" => $usersdata["UserOrganization"]["user_role"], "fname" => $usersdata["User"]["fname"], "lname" => $usersdata["User"]["lname"], "email" => $usersdata["User"]["email"]);
            }
        }

        $result = array("fresult" => $fresult, "org_id" => $orgid);
        echo json_encode($result);
    }

    function getactiveusers() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $orgid = $this->request->data["org_id"];
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
        $userorgdata = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $orgid, "UserOrganization.status" => 0, "UserOrganization.user_role" => array(2, 3), "UserOrganization.user_id !=" => $this->Auth->User("id")), "order" => "UserOrganization.id ASC"));
        $fresult = array();
        if (!empty($userorgdata)) {

            foreach ($userorgdata as $usersdata) {
                $fresult[$usersdata["UserOrganization"]["id"]] = array("id" => $usersdata["User"]["id"], "user_role" => $usersdata["UserOrganization"]["user_role"], "fname" => $usersdata["User"]["fname"], "lname" => $usersdata["User"]["lname"], "email" => $usersdata["User"]["email"]);
            }
        }

        $result = array("fresult" => $fresult, "org_id" => $orgid);
        echo json_encode($result);
    }

    function searchallendorsement() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $this->loadModel("Endorsement");
        $this->loadModel("Entity");
        $this->loadModel("OrgDepartment");
        $organization_id = $this->request->data["orgid"];
        $searchvalue = trim($this->request->data["searchvalue"]);
        $jobtitles = $this->request->data["jobtitles"];
        $departments = $this->request->data["departments"];

        $entities = isset($this->request->data["entities"]) ? $this->request->data["entities"] : "";
        //searching users as per the searching value
        $conditionssearchingallendorsement = array("organization_id" => $organization_id);
        if (!empty($jobtitles[0])) {
            $conditionssearchingallendorsement[] = array("job_title_id" => $jobtitles);
        }
        if (!empty($entities[0])) {
            $conditionssearchingallendorsement[] = array("entity_id" => $entities);
        }
        if (!empty($departments[0])) {
            $conditionssearchingallendorsement[] = array("department_id" => $departments);
        }
        if ($searchvalue != "") {
            $conditionssearchingallendorsement["OR"] = array(
                "fname LIKE '%$searchvalue%'",
                "lname LIKE '%$searchvalue%'",
                "concat(User.fname,' ',User.lname) LIKE '%$searchvalue%'"
            );
        }

        $fieldssearchingallendorsement = array("User.id");
        $userorgdata = $this->UserOrganization->find("all", array("fields" => $fieldssearchingallendorsement, "conditions" => $conditionssearchingallendorsement));

        $usersid = array("user" => array(), "entity" => array(), "department" => array());
        foreach ($userorgdata as $usersdata) {
            $usersid["user"][] = $usersdata["User"]["id"];
        }

        //searching entity as per the searching value
        $conditionentity = array("organization_id" => $organization_id);
        if ($searchvalue != "") {
            $conditionentity["OR"] = array(
                "name LIKE '%$searchvalue%'",
            );
        }
        $fieldsentity = array("Entity.id");
        $entitydata = $this->Entity->find("all", array("fields" => $fieldsentity, "conditions" => $conditionentity));
        foreach ($entitydata as $entitiesid) {
            $usersid["entity"][] = $entitiesid["Entity"]["id"];
        }
        //searching department as per the searching value
        $conditionorgdept = array("organization_id" => $organization_id);
        if ($searchvalue != "") {
            $conditionorgdept["OR"] = array(
                "name LIKE '%$searchvalue%'",
            );
        }
        $orgdeptdata = $this->OrgDepartment->find("all", array("fields" => array("OrgDepartment.id"), "conditions" => $conditionorgdept));
        foreach ($orgdeptdata as $departmentdata) {
            $usersid["department"][] = $departmentdata["OrgDepartment"]["id"];
        }


//        if ($startdate != "" and $enddate != "") {
//            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
//        }
        $orgcorevaluesandcode = array();
        $allvaluesendorsement = array();
        $allendorsementdept = array();
        if (!empty($usersid)) {
            $allendorsementusers = array();
            $allendorsemententity = array();
            if (!empty($usersid["user"])) {
                $condtionsallendorsementusers = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsementusers = array("Endorsement.type != " => "guest");
//                $condtionsallendorsementusers["Endorsement.organization_id"] = $organization_id;
//                $condtionsallendorsementusers["Endorsement.type != "] = ['guest'];
                $condtionsallendorsementusers["OR"] = array(
                    "Endorsement.endorser_id" => $usersid["user"],
                    array("Endorsement.endorsed_id" => $usersid["user"], "endorsement_for" => "user"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsementusers = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsementusers));
            }

            if (!empty($usersid["entity"])) {
                $condtionsallendorsemententity = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsemententity["OR"] = array(
                    array("Endorsement.endorsed_id" => $usersid["entity"], "endorsement_for" => "entity"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsemententity = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsemententity));
            }

            if (!empty($usersid["department"])) {
                $condtionsallendorsementdept = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsementdept["OR"] = array(
                    array("Endorsement.endorsed_id" => $usersid["department"], "endorsement_for" => "department"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsementdept = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsementdept));
            }



            $allendorsement = array_merge($allendorsemententity, $allendorsementusers, $allendorsementdept);

            $departments = $this->Common->getorgdepartments($organization_id);
            $entities = $this->Common->getorgentities($organization_id);
            $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
            $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities);
        }
        $this->set(compact("allvaluesendorsement", "orgcorevaluesandcode"));
        echo $htmlstring = $this->render('/Elements/allendorsementslisting');
        exit;
    }

    //Added By Babulal Prasad @24-may-2018 To filter on all guest nDorsements in admin
    function searchallguestendorsement() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $this->loadModel("Endorsement");
        $this->loadModel("Entity");
        $this->loadModel("OrgDepartment");
        $organization_id = $this->request->data["orgid"];
        $searchvalue = trim($this->request->data["searchvalue"]);
        $jobtitles = $this->request->data["jobtitles"];
        $departments = $this->request->data["departments"];

        $entities = isset($this->request->data["entities"]) ? $this->request->data["entities"] : "";
        //searching users as per the searching value
        $conditionssearchingallendorsement = array("organization_id" => $organization_id);
        if (!empty($jobtitles[0])) {
            $conditionssearchingallendorsement[] = array("job_title_id" => $jobtitles);
        }
        if (!empty($entities[0])) {
            $conditionssearchingallendorsement[] = array("entity_id" => $entities);
        }
        if (!empty($departments[0])) {
            $conditionssearchingallendorsement[] = array("department_id" => $departments);
        }
        $userConditions = array();
        if ($searchvalue != "") {
            $userConditions['OR'] = $conditionssearchingallendorsement["OR"] = array(
                "fname LIKE '%$searchvalue%'",
                "lname LIKE '%$searchvalue%'",
                "concat(User.fname,' ',User.lname) LIKE '%$searchvalue%'"
            );
        }
        $userConditions['role'] = 5;

        $fieldssearchingallendorsement = array("User.id");
        $userorgdata = $this->UserOrganization->find("all", array("fields" => $fieldssearchingallendorsement, "conditions" => $conditionssearchingallendorsement));
//        pr($conditionssearchingallendorsement); 
//        pr($userConditions); 
        $guestuserdata = $this->User->find("all", array("fields" => $fieldssearchingallendorsement, "conditions" => $userConditions));
//        pr($userorgdata);
//        pr($userdata);
//        echo $this->UserOrganization->getLastQuery();
//        exit;
//pr($userorgdata); exit;
        $usersid = array("user" => array(), "entity" => array(), "department" => array());
        foreach ($userorgdata as $usersdata) {
            $usersid["user"][] = $usersdata["User"]["id"];
        }
        if (count($guestuserdata) > 0) {
            foreach ($guestuserdata as $gusersdata) {
                $usersid["user"][] = $gusersdata["User"]["id"];
            }
        }

        //searching entity as per the searching value
        $conditionentity = array("organization_id" => $organization_id);
        if ($searchvalue != "") {
            $conditionentity["OR"] = array(
                "name LIKE '%$searchvalue%'",
            );
        }
        $fieldsentity = array("Entity.id");
        $entitydata = $this->Entity->find("all", array("fields" => $fieldsentity, "conditions" => $conditionentity));
        foreach ($entitydata as $entitiesid) {
            $usersid["entity"][] = $entitiesid["Entity"]["id"];
        }
        //searching department as per the searching value
        $conditionorgdept = array("organization_id" => $organization_id);
        if ($searchvalue != "") {
            $conditionorgdept["OR"] = array(
                "name LIKE '%$searchvalue%'",
            );
        }
        $orgdeptdata = $this->OrgDepartment->find("all", array("fields" => array("OrgDepartment.id"), "conditions" => $conditionorgdept));
        foreach ($orgdeptdata as $departmentdata) {
            $usersid["department"][] = $departmentdata["OrgDepartment"]["id"];
        }


//        if ($startdate != "" and $enddate != "") {
//            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
//        }
        $orgcorevaluesandcode = array();
        $allvaluesendorsement = array();
        $allendorsementdept = array();
        if (!empty($usersid)) {
            $allendorsementusers = array();
            $allendorsemententity = array();
            if (!empty($usersid["user"])) {

                $condtionsallendorsementusers = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsementusers = array("Endorsement.type" => "guest");
//                $condtionsallendorsementusers["Endorsement.organization_id"] = $organization_id;
//                $condtionsallendorsementusers["Endorsement.type"] = ['guest'];

                $condtionsallendorsementusers["OR"] = array(
                    "Endorsement.endorser_id" => $usersid["user"],
                    array("Endorsement.endorsed_id" => $usersid["user"], "endorsement_for" => "user"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsementusers = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsementusers));
//                echo $this->Endorsement->getLastQuery();
//                pr($allendorsementusers);
//                exit;
            }

            if (!empty($usersid["entity"])) {
                $condtionsallendorsemententity = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsemententity["OR"] = array(
                    array("Endorsement.endorsed_id" => $usersid["entity"], "endorsement_for" => "entity"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsemententity = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsemententity));
            }

            if (!empty($usersid["department"])) {
                $condtionsallendorsementdept = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsementdept["OR"] = array(
                    array("Endorsement.endorsed_id" => $usersid["department"], "endorsement_for" => "department"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsementdept = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsementdept));
            }



            $allendorsement = array_merge($allendorsemententity, $allendorsementusers, $allendorsementdept);

            $departments = $this->Common->getorgdepartments($organization_id);
            $entities = $this->Common->getorgentities($organization_id);
            $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
            $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities);
        }
        $this->set(compact("allvaluesendorsement", "orgcorevaluesandcode"));
        echo $htmlstring = $this->render('/Elements/allendorsementslisting');
        exit;
    }

    //Added By Babulal Prasad @24-APril-2021 To filter on all guest nDorsements in admin
    function searchallguestendorsement2() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $this->loadModel("Endorsement");
        $this->loadModel("Entity");
        $this->loadModel("OrgDepartment");
        $organization_id = $this->request->data["orgid"];
        $searchvalue = trim($this->request->data["searchvalue"]);
        $jobtitles = $this->request->data["jobtitles"];
        $departments = $this->request->data["departments"];

        $entities = isset($this->request->data["entities"]) ? $this->request->data["entities"] : "";
        //searching users as per the searching value
        $conditionssearchingallendorsement = array("organization_id" => $organization_id);
        if (!empty($jobtitles[0])) {
            $conditionssearchingallendorsement[] = array("job_title_id" => $jobtitles);
        }
        if (!empty($entities[0])) {
            $conditionssearchingallendorsement[] = array("entity_id" => $entities);
        }
        if (!empty($departments[0])) {
            $conditionssearchingallendorsement[] = array("department_id" => $departments);
        }
        $userConditions = array();
        if ($searchvalue != "") {
            $userConditions['OR'] = $conditionssearchingallendorsement["OR"] = array(
                "fname LIKE '%$searchvalue%'",
                "lname LIKE '%$searchvalue%'",
                "concat(User.fname,' ',User.lname) LIKE '%$searchvalue%'"
            );
        }
        $userConditions['role'] = 5;

        $fieldssearchingallendorsement = array("User.id");
        $userorgdata = $this->UserOrganization->find("all", array("fields" => $fieldssearchingallendorsement, "conditions" => $conditionssearchingallendorsement));
//        pr($conditionssearchingallendorsement); 
//        pr($userConditions); 
        $guestuserdata = $this->User->find("all", array("fields" => $fieldssearchingallendorsement, "conditions" => $userConditions));
//        pr($userorgdata);
//        pr($userdata);
//        echo $this->UserOrganization->getLastQuery();
//        exit;
//pr($userorgdata); exit;
        $usersid = array("user" => array(), "entity" => array(), "department" => array());
        foreach ($userorgdata as $usersdata) {
            $usersid["user"][] = $usersdata["User"]["id"];
        }
        if (count($guestuserdata) > 0) {
            foreach ($guestuserdata as $gusersdata) {
                $usersid["user"][] = $gusersdata["User"]["id"];
            }
        }

        //searching entity as per the searching value
        $conditionentity = array("organization_id" => $organization_id);
        if ($searchvalue != "") {
            $conditionentity["OR"] = array(
                "name LIKE '%$searchvalue%'",
            );
        }
        $fieldsentity = array("Entity.id");
        $entitydata = $this->Entity->find("all", array("fields" => $fieldsentity, "conditions" => $conditionentity));
        foreach ($entitydata as $entitiesid) {
            $usersid["entity"][] = $entitiesid["Entity"]["id"];
        }
        //searching department as per the searching value
        $conditionorgdept = array("organization_id" => $organization_id);
        if ($searchvalue != "") {
            $conditionorgdept["OR"] = array(
                "name LIKE '%$searchvalue%'",
            );
        }
        $orgdeptdata = $this->OrgDepartment->find("all", array("fields" => array("OrgDepartment.id"), "conditions" => $conditionorgdept));
        foreach ($orgdeptdata as $departmentdata) {
            $usersid["department"][] = $departmentdata["OrgDepartment"]["id"];
        }


//        if ($startdate != "" and $enddate != "") {
//            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
//        }
        $orgcorevaluesandcode = array();
        $allvaluesendorsement = array();
        $allendorsementdept = array();
        if (!empty($usersid)) {
            $allendorsementusers = array();
            $allendorsemententity = array();
            if (!empty($usersid["user"])) {

                $condtionsallendorsementusers = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsementusers = array("Endorsement.type" => "guest");
//                $condtionsallendorsementusers["Endorsement.organization_id"] = $organization_id;
//                $condtionsallendorsementusers["Endorsement.type"] = ['guest'];

                $condtionsallendorsementusers["OR"] = array(
                    "Endorsement.endorser_id" => $usersid["user"],
                    array("Endorsement.endorsed_id" => $usersid["user"], "endorsement_for" => "user"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsementusers = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsementusers));
//                echo $this->Endorsement->getLastQuery();
//                pr($allendorsementusers);
//                exit;
            }

            if (!empty($usersid["entity"])) {
                $condtionsallendorsemententity = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsemententity["OR"] = array(
                    array("Endorsement.endorsed_id" => $usersid["entity"], "endorsement_for" => "entity"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsemententity = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsemententity));
            }

            if (!empty($usersid["department"])) {
                $condtionsallendorsementdept = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsementdept["OR"] = array(
                    array("Endorsement.endorsed_id" => $usersid["department"], "endorsement_for" => "department"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsementdept = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsementdept));
            }



            $allendorsement = array_merge($allendorsemententity, $allendorsementusers, $allendorsementdept);

            $departments = $this->Common->getorgdepartments($organization_id);
            $entities = $this->Common->getorgentities($organization_id);
            $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
            $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities);
        }
        $this->set(compact("allvaluesendorsement", "orgcorevaluesandcode"));
        echo $htmlstring = $this->render('/Elements/allguestendorsementslisting');
        exit;
    }

    //Added By Babulal Prasad @24-may-2018 To filter on all guest nDorsements in admin
    function searchalldaisyendorsement() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $this->loadModel("Endorsement");
        $this->loadModel("Entity");
        $this->loadModel("OrgDepartment");
        $organization_id = $this->request->data["orgid"];
        $searchvalue = trim($this->request->data["searchvalue"]);
        $jobtitles = $this->request->data["jobtitles"];
        $departments = $this->request->data["departments"];

        $entities = isset($this->request->data["entities"]) ? $this->request->data["entities"] : "";
        //searching users as per the searching value
        $conditionssearchingallendorsement = array("organization_id" => $organization_id);
        if (!empty($jobtitles[0])) {
            $conditionssearchingallendorsement[] = array("job_title_id" => $jobtitles);
        }
        if (!empty($entities[0])) {
            $conditionssearchingallendorsement[] = array("entity_id" => $entities);
        }
        if (!empty($departments[0])) {
            $conditionssearchingallendorsement[] = array("department_id" => $departments);
        }
        $userConditions = array();
        if ($searchvalue != "") {
            $userConditions['OR'] = $conditionssearchingallendorsement["OR"] = array(
                "fname LIKE '%$searchvalue%'",
                "lname LIKE '%$searchvalue%'",
                "concat(User.fname,' ',User.lname) LIKE '%$searchvalue%'"
            );
        }
        $userConditions['role'] = 5;

        $fieldssearchingallendorsement = array("User.id");
        $userorgdata = $this->UserOrganization->find("all", array("fields" => $fieldssearchingallendorsement, "conditions" => $conditionssearchingallendorsement));
//        pr($conditionssearchingallendorsement); 
//        pr($userConditions); 
        $guestuserdata = $this->User->find("all", array("fields" => $fieldssearchingallendorsement, "conditions" => $userConditions));
//        pr($userorgdata);
//        pr($userdata);
//        echo $this->UserOrganization->getLastQuery();
//        exit;
//pr($userorgdata); exit;
        $usersid = array("user" => array(), "entity" => array(), "department" => array());
        foreach ($userorgdata as $usersdata) {
            $usersid["user"][] = $usersdata["User"]["id"];
        }
        if (count($guestuserdata) > 0) {
            foreach ($guestuserdata as $gusersdata) {
                $usersid["user"][] = $gusersdata["User"]["id"];
            }
        }

        //searching entity as per the searching value
        $conditionentity = array("organization_id" => $organization_id);
        if ($searchvalue != "") {
            $conditionentity["OR"] = array(
                "name LIKE '%$searchvalue%'",
            );
        }
        $fieldsentity = array("Entity.id");
        $entitydata = $this->Entity->find("all", array("fields" => $fieldsentity, "conditions" => $conditionentity));
        foreach ($entitydata as $entitiesid) {
            $usersid["entity"][] = $entitiesid["Entity"]["id"];
        }
        //searching department as per the searching value
        $conditionorgdept = array("organization_id" => $organization_id);
        if ($searchvalue != "") {
            $conditionorgdept["OR"] = array(
                "name LIKE '%$searchvalue%'",
            );
        }
        $orgdeptdata = $this->OrgDepartment->find("all", array("fields" => array("OrgDepartment.id"), "conditions" => $conditionorgdept));
        foreach ($orgdeptdata as $departmentdata) {
            $usersid["department"][] = $departmentdata["OrgDepartment"]["id"];
        }


//        if ($startdate != "" and $enddate != "") {
//            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
//        }
        $orgcorevaluesandcode = array();
        $allvaluesendorsement = array();
        $allendorsementdept = array();
        if (!empty($usersid)) {
            $allendorsementusers = array();
            $allendorsemententity = array();
            if (!empty($usersid["user"])) {

                $condtionsallendorsementusers = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsementusers = array("Endorsement.type" => "daisy");
//                $condtionsallendorsementusers["Endorsement.organization_id"] = $organization_id;
//                $condtionsallendorsementusers["Endorsement.type"] = ['daisy'];

                $condtionsallendorsementusers["OR"] = array(
                    "Endorsement.endorser_id" => $usersid["user"],
                    array("Endorsement.endorsed_id" => $usersid["user"], "endorsement_for" => "user"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsementusers = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsementusers));
//                echo $this->Endorsement->getLastQuery();
//                pr($allendorsementusers);
//                exit;
            }

            if (!empty($usersid["entity"])) {
                $condtionsallendorsemententity = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsemententity["OR"] = array(
                    array("Endorsement.endorsed_id" => $usersid["entity"], "endorsement_for" => "entity"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsemententity = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsemententity));
            }

            if (!empty($usersid["department"])) {
                $condtionsallendorsementdept = array("Endorsement.organization_id" => $organization_id);
                $condtionsallendorsementdept["OR"] = array(
                    array("Endorsement.endorsed_id" => $usersid["department"], "endorsement_for" => "department"),
                );
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
                $allendorsementdept = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsementdept));
            }



            $allendorsement = array_merge($allendorsemententity, $allendorsementusers, $allendorsementdept);

            $departments = $this->Common->getorgdepartments($organization_id);
            $entities = $this->Common->getorgentities($organization_id);
//            $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
            $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcodeForReports($organization_id);
            $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities);
//            pr($allvaluesendorsement);
        }
        $portal = 'daisy';
        $DAISYAwards = Configure::read("DAISY_Awards");
        $this->set(compact("allvaluesendorsement", "orgcorevaluesandcode", "portal", "DAISYAwards"));
        echo $htmlstring = $this->render('/Elements/alldaisyendorsementslisting');
//        exit;
    }

    function filterallendorsement() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("Endorsement");
        $organization_id = $this->request->data["orgid"];
        $jobtitle = isset($this->request->data["jobtitles"]) ? $this->request->data["jobtitles"] : "";
        $departments = isset($this->request->data["departments"]) ? $this->request->data["departments"] : "";
        $entities = isset($this->request->data["entities"]) ? $this->request->data["entities"] : "";
        $startdate = $this->Common->dateConvertServer($this->request->data["startdate"]);
        $enddate = $this->Common->dateConvertServer($this->request->data["enddate"]);
        $this->Common->bindmodelcommonjobtitle();
        $this->Endorsement->bindModel(array(
            'hasMany' => array(
                'EndorseCoreValues' => array(
                    'className' => 'EndorseCoreValues',
                ),
            )
        ));
        $conditions = array();
        if (!empty($jobtitle)) {
            $conditions[] = array("UserOrganization.job_title_id" => $jobtitle);
        }
        if (!empty($entities)) {
            $conditions[] = array("UserOrganization.entity_id" => $entities);
        }
        if (!empty($departments)) {
            $conditions[] = array("UserOrganization.department_id" => $departments);
        }

        $conditions[] = array(
            //"UserOrganization.organization_id" => $organization_id,
            //"UserOrganization.status" => 1, 
            "Endorsement.organization_id" => $organization_id,
                //"Endorsement.endorsement_for" => "user"   
        );

        if ($startdate != "" and $enddate != "") {
            $conditions[] = "date(Endorsement.created) between '$startdate' and '$enddate'";
        }

        $this->UserOrganization->recursive = 2;
        $allendorsement = $this->UserOrganization->find("all", array("order" => "Endorsement.created DESC", "conditions" => $conditions));

        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities, "userorganization");

        $filtered = "yes";
        $this->set(compact("allvaluesendorsement", "orgcorevaluesandcode", "filtered"));
        echo $htmlstring = $this->render('/Elements/allendorsementslisting');
        exit;
//        foreach($user_org_data as $userorguserid){
//            $user_id[] = $userorguserid["UserOrganization"]["user_id"];
//        }
    }

    function filteralldaisyendorsement() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("Endorsement");
        $organization_id = $this->request->data["orgid"];
        $jobtitle = isset($this->request->data["jobtitles"]) ? $this->request->data["jobtitles"] : "";
        $departments = isset($this->request->data["departments"]) ? $this->request->data["departments"] : "";
        $entities = isset($this->request->data["entities"]) ? $this->request->data["entities"] : "";
        $startdate = $this->Common->dateConvertServer($this->request->data["startdate"]);
        $enddate = $this->Common->dateConvertServer($this->request->data["enddate"]);
        $this->Common->bindmodelcommonjobtitle();
        $this->Endorsement->bindModel(array(
            'hasMany' => array(
                'EndorseCoreValues' => array(
                    'className' => 'EndorseCoreValues',
                ),
            )
        ));
        $conditions = array();
        if (!empty($jobtitle)) {
            $conditions[] = array("UserOrganization.job_title_id" => $jobtitle);
        }
        if (!empty($entities)) {
            $conditions[] = array("UserOrganization.entity_id" => $entities);
        }
        if (!empty($departments)) {
            $conditions[] = array("UserOrganization.department_id" => $departments);
        }

        $conditions[] = array(
            //"UserOrganization.organization_id" => $organization_id,
            //"UserOrganization.status" => 1, 
            "Endorsement.organization_id" => $organization_id,
                //"Endorsement.endorsement_for" => "user"   
        );

        if ($startdate != "" and $enddate != "") {
            $conditions[] = "date(Endorsement.created) between '$startdate' and '$enddate'";
        }

        $this->UserOrganization->recursive = 2;
        $allendorsement = $this->UserOrganization->find("all", array("order" => "Endorsement.created DESC", "conditions" => $conditions));

        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities, "userorganization");

        $filtered = "yes";
        $this->set(compact("allvaluesendorsement", "orgcorevaluesandcode", "filtered"));
        echo $htmlstring = $this->render('/Elements/alldaisyendorsementslisting');
        exit;
//        foreach($user_org_data as $userorguserid){
//            $user_id[] = $userorguserid["UserOrganization"]["user_id"];
//        }
    }

    function filterallguestendorsement() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("Endorsement");
        $organization_id = $this->request->data["orgid"];
        $jobtitle = isset($this->request->data["jobtitles"]) ? $this->request->data["jobtitles"] : "";
        $departments = isset($this->request->data["departments"]) ? $this->request->data["departments"] : "";
        $entities = isset($this->request->data["entities"]) ? $this->request->data["entities"] : "";
        $startdate = $this->Common->dateConvertServer($this->request->data["startdate"]);
        $enddate = $this->Common->dateConvertServer($this->request->data["enddate"]);
        $this->Common->bindmodelcommonjobtitle();
        $this->Endorsement->bindModel(array(
            'hasMany' => array(
                'EndorseCoreValues' => array(
                    'className' => 'EndorseCoreValues',
                ),
            )
        ));
        $conditions = array();
        if (!empty($jobtitle)) {
            $conditions[] = array("UserOrganization.job_title_id" => $jobtitle);
        }
        if (!empty($entities)) {
            $conditions[] = array("UserOrganization.entity_id" => $entities);
        }
        if (!empty($departments)) {
            $conditions[] = array("UserOrganization.department_id" => $departments);
        }

        $conditions[] = array(
            //"UserOrganization.organization_id" => $organization_id,
            //"UserOrganization.status" => 1, 
            "Endorsement.organization_id" => $organization_id,
            "Endorsement.type" => "guest",
                //"Endorsement.endorsement_for" => "user"   
        );

        if ($startdate != "" and $enddate != "") {
            $conditions[] = "date(Endorsement.created) between '$startdate' and '$enddate'";
        }

        $this->UserOrganization->recursive = 2;
        $allendorsement = $this->UserOrganization->find("all", array("order" => "Endorsement.created DESC", "conditions" => $conditions));

        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities, "userorganization");

        $filtered = "yes";
        $this->set(compact("allvaluesendorsement", "orgcorevaluesandcode", "filtered"));
        echo $htmlstring = $this->render('/Elements/allguestendorsementslisting');
        exit;
//        foreach($user_org_data as $userorguserid){
//            $user_id[] = $userorguserid["UserOrganization"]["user_id"];
//        }
    }

    function filterallpost() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("Endorsement");
        $this->loadModel("PostEventCount");

//        pr($this->request->data); exit;

        $organization_id = $org_id = $this->request->data["orgid"];
        $selected_user_id = $this->request->data["Userid"];
        $reportType = $this->request->data["reporttype"];
        $jobtitle = isset($this->request->data["jobtitles"]) ? $this->request->data["jobtitles"] : "";
        $departments = isset($this->request->data["departments"]) ? $this->request->data["departments"] : "";
        $entities = isset($this->request->data["entities"]) ? $this->request->data["entities"] : "";
        $startdate = $this->Common->dateConvertServer($this->request->data["startdate"]);
        $enddate = $this->Common->dateConvertServer($this->request->data["enddate"]);
        $this->Common->bindmodelcommonjobtitle();
        $this->Endorsement->bindModel(array(
            'hasMany' => array(
                'EndorseCoreValues' => array(
                    'className' => 'EndorseCoreValues',
                ),
            )
        ));



        $conditions = array();
        if (!empty($jobtitle)) {
            $conditions[] = array("UserOrganization.job_title_id" => $jobtitle);
        }
        if (!empty($entities)) {
            $conditions[] = array("UserOrganization.entity_id" => $entities);
        }
        if (!empty($departments)) {
            $conditions[] = array("UserOrganization.department_id" => $departments);
        }

//        $conditions[] = array(
//            //"UserOrganization.organization_id" => $organization_id,
//            //"UserOrganization.status" => 1, 
//            "Endorsement.organization_id" => $organization_id,
//                //"Endorsement.endorsement_for" => "user"   
//        );
//        if ($startdate != "" and $enddate != "") {
//            $conditions[] = "date(Endorsement.created) between '$startdate' and '$enddate'";
//        }
//        $this->UserOrganization->recursive = 2;
//        $allendorsement = $this->UserOrganization->find("all", array("order" => "Endorsement.created DESC", "conditions" => $conditions));
//        pr($allendorsement); exit;



        /*  NEW CODE START        * *** */

        $conditionarray = array();
        $conditionarray["UserOrganization.organization_id"] = $org_id;
        if ($startdate != "" and $enddate != "") {
            array_push($conditionarray, "date(PostEventCount.created) between '$startdate' and '$enddate'");
        }

        $selectedUserName = "All";
        if ($reportType == 'Users' && $selected_user_id != '') {
            $conditionarray["UserOrganization.user_id"] = $selected_user_id;
//            $selectedUserName = $orgUserList[$selected_user_id];
        }
        if (!empty($jobtitle)) {
            $conditionarray[] = array("UserOrganization.job_title_id" => $jobtitle);
        }
        if (!empty($entities)) {
            $conditionarray[] = array("UserOrganization.entity_id" => $entities);
        }
        if (!empty($departments)) {
            $conditionarray[] = array("UserOrganization.department_id" => $departments);
        }
//        pr($conditionarray);exit;
        $params = array();
        $params['conditions'] = $conditionarray;
        $params['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'LEFT',
                'conditions' => array(
                    'User.id = PostEventCount.user_id'
                )
            ),
            array(
                'table' => 'user_organizations',
                'alias' => 'UserOrganization',
                'type' => 'LEFT',
                'conditions' => array(
                    'UserOrganization.user_id  =  PostEventCount.user_id',
                )
            ),
            array(
                'table' => 'organizations',
                'alias' => 'Organization',
                'type' => 'LEFT',
                'conditions' => array(
                    'Organization.id = UserOrganization.organization_id'
                )
            ),
            array(
                'table' => 'org_job_titles',
                'alias' => 'OrgJobTitle',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgJobTitle.id = UserOrganization.job_title_id',
                )
            ),
            array(
                'table' => 'org_departments',
                'alias' => 'OrgDepartment',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgDepartment.id = UserOrganization.department_id',
                )
            ),
            array(
                'table' => 'entities',
                'alias' => 'Entity',
                'type' => 'LEFT',
                'conditions' => array(
                    'Entity.id = UserOrganization.entity_id',
                )
            ),
        );

        $params['fields'] = "PostEventCount.post_id,sum(post_click) as total_post_click, sum(post_attachment_click) as total_attachment_click, sum(post_attachment_pin_click) as total_attachment_pin_click,
                                    sum(post_like_counts) as total_post_like,OrgJobTitle.title,concat(User.fname,' ',User.lname) as user_name,
                                    Organization.name as org_name,OrgDepartment.name as department_name,
                                    Entity.name as sub_org_name";
//        $params['limit'] = $limit;
//        $params['page'] = $page;
//        $params['offset'] = $offset;
        //$params['order'] = 'Post.created desc';
        $params['group'] = 'PostEventCount.user_id';


//                pr($params);
//                exit;
//$this->Endorsement->bindModel(array('hasMany' => array('EndorseCoreValues')));
        $allPostData = $this->PostEventCount->find("all", $params);
//        pr($allPostData); exit;


        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $jobtitles = $this->Common->getorgjobtitles($organization_id);
        $this->set(compact('allPostData'));
        echo $htmlstring = $this->render('/Elements/allpostslisting');
        exit;
        /*    NEW CODE END   * *** */
    }

    function bulkactiveusers() {
        $loggeduser = $this->Auth->User();
        if ($this->request->is('post')) {
            try {
                $statusConfig = Configure::read("statusConfig");
                $this->layout = 'ajax';
                $this->render = false;
                $this->loadModel('Subscription');

                $org_id = $this->request->data['org_id'];
                $opt = $this->request->data['option'];
                $qty = $this->request->data['qty'];

                if (isset($this->request->data['select_user_id']) && trim($this->request->data['select_user_id']) != "") {
                    $select_user_id = $this->request->data['select_user_id'];
                    $select_user_id = explode(",", $select_user_id);
                    $qty = count($select_user_id);
                }



                $data = $this->Subscription->findByOrganizationId($org_id);

                $poolcount = 10;
                if (!empty($data["Subscription"]) && $data["Subscription"]["organization_id"] == $org_id) {
                    $subscriptionpool = 1;
                    $poolcount = $data["Subscription"]["pool_purchased"] + $poolcount;
                }
                //$params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                //
                //$params['fields'] = array("COUNT(UserOrganization.user_id) as count");
                //$userOrgStats = $this->UserOrganization->find("all", $params);
                //
                //$usercount = $userOrgStats[0][0]["count"];

                $params = array();
                // $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['inactive'], $statusConfig['active'], $statusConfig['eval']));
                $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                $params['group'] = 'pool_type';
                $params['fields'] = array("UserOrganization.pool_type", "COUNT(UserOrganization.user_id) as count");
                $userOrgStats = $this->UserOrganization->find("all", $params);

                $freeCount = 0;
                $paidCount = 0;

                foreach ($userOrgStats as $stats) {
                    if ($stats['UserOrganization']['pool_type'] == 'free') {
                        $freeCount = $stats[0]['count'];
                    } else {
                        $paidCount = $stats[0]['count'];
                    }
                }

                $usercount = $freeCount + $paidCount;
                if ($opt == "recent") {
                    // get inactive users
                    $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => $statusConfig['inactive']);
                    $params['fields'] = array("COUNT(UserOrganization.user_id) as count");
                    $inactiveuserOrgStats = $this->UserOrganization->find("all", $params);
                    $inactiveusercount = $inactiveuserOrgStats[0][0]["count"];
                    if ($qty > $inactiveusercount) {
                        $qty = $inactiveusercount;
                    }
                    //
                }

                $free_type = 0;
                $paid_type = 0;
                $availablecount = $usercount + $qty;
                if ($freeCount > 10) {
                    $paid_type = $qty;
                } elseif ($availablecount > 10) {

                    if ($freeCount < 10) {
                        $totalfree = 10 - $freeCount;
                        if ($totalfree >= $qty) {
                            $free_type = $qty;
                        } else {
                            $free_type = $totalfree;
                            $paid_type = $qty - $totalfree;
                        }
                    } else {

                        $paid_type = $qty;
                    }
                } else {
                    $free_type = $qty;
                }


                if ($availablecount > $poolcount) {
                    echo json_encode(array("status" => "true", "msg" => "no available quota", "update" => 0));
                } else {
                    if ($opt == "recent") {

                        // active oldest one paid users
                        if ($free_type > 0) {
                            $sqlupdate = "UPDATE user_organizations set status =1,pool_type='free'  where organization_id='" . $org_id . "' and status =0   order by id asc limit " . $free_type;
                            $this->Subscription->query($sqlupdate);
                        }
                        if ($paid_type > 0) {
                            $sqlupdate = "UPDATE user_organizations set status =1,pool_type='paid'  where organization_id='" . $org_id . "' and status =0   order by id asc limit " . $paid_type;
                            $this->Subscription->query($sqlupdate);
                        }
                    } elseif ($opt == "no") {
                        //$sqlupdate = "UPDATE user_organizations set status =1  where organization_id='" . $org_id . "' and  user_id IN(" . $select_user_id . ")";
                        //$this->Subscription->query($sqlupdate);
                        // echo $free_type." ".$paid_type;
                        // print_r($select_user_id);
                        if ($free_type > 0) {
                            $select_id = "";
                            for ($i = 0; $i < $free_type; $i++) {
                                $select_id .= $select_user_id[$i] . ",";
                            }
                            $select_id = substr($select_id, 0, -1);
                            $sqlupdate = "UPDATE user_organizations set status =1,pool_type='free'  where organization_id='" . $org_id . "' and  user_id IN(" . $select_id . ")";
                            $this->Subscription->query($sqlupdate);
                        }
                        if ($paid_type > 0) {
                            $select_id = "";
                            $start = $free_type;
                            for ($start = 0; $start < $qty; $start++) {
                                $select_id .= $select_user_id[$start] . ",";
                            }
                            $select_id = substr($select_id, 0, -1);
                            $sqlupdate = "UPDATE user_organizations set status =1,pool_type='paid'  where organization_id='" . $org_id . "' and  user_id IN(" . $select_id . ")";
                            $this->Subscription->query($sqlupdate);
                        }
                        //  $sqlupdate = "UPDATE user_organizations set status =1  where organization_id='" . $org_id . "' and  user_id IN(" . $select_user_id . ")";
                        //  $this->Subscription->query($sqlupdate);
                    }
                    echo json_encode(array("status" => "true", "msg" => "update successfully", "update" => 1));
                }
            } catch (Exception $e) {
                echo json_encode(array("status" => "true", "error" => "true", "error_message" => "Not able to Save Subscription", "msg" => $e->getMessage()));
            }
        }
        exit();
    }

    function bulkinactiveusers() {
        $loggeduser = $this->Auth->User();
        if ($this->request->is('post')) {
            try {
                $statusConfig = Configure::read("statusConfig");
                $this->layout = 'ajax';
                $this->render = false;
                $this->loadModel('Subscription');

                $org_id = $this->request->data['org_id'];
                $opt = $this->request->data['option'];
                $qty = $this->request->data['qty'];

                if (isset($this->request->data['select_user_id']) && trim($this->request->data['select_user_id']) != "") {
                    $select_user_id = $this->request->data['select_user_id'];
                    $qty = count(explode(",", $select_user_id));
                }



                $data = $this->Subscription->findByOrganizationId($org_id);
                $poolcount = 10;
                $subscriptionpool = 0;
                if (!empty($data["Subscription"]) && $data["Subscription"]["organization_id"] == $org_id) {
                    $subscriptionpool = 1;
                    $poolcount = $data["Subscription"]["pool_purchased"] + $poolcount;
                    $subscriptionType = $data["Subscription"]['payment_method'];
                }
                if ($opt == "recent") {
                    // active oldest one paid users
                    $sqlupdate = "UPDATE user_organizations set status =0,pool_type='paid'  where organization_id='" . $org_id . "' and user_id !='" . $loggeduser["id"] . "' and status IN (1,3)   order by id desc limit " . $qty;
                    $this->Subscription->query($sqlupdate);
                } elseif ($opt == "no") {
//                    $qty = count($select_user_id);
                    $sqlupdate = "UPDATE user_organizations set status =0,pool_type='paid'  where organization_id='" . $org_id . "' and  user_id IN(" . $select_user_id . ")";
                    $this->Subscription->query($sqlupdate);
                }
                $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['active']));

                $params['fields'] = array("COUNT(UserOrganization.user_id) as count");
                $userOrgStats = $this->UserOrganization->find("all", $params);

                $usercount = $userOrgStats[0][0]["count"];

                if ($subscriptionpool == 1) {
                    //if ($usercount >= $poolcount) {
                    //    echo json_encode(array("status" => "true", "msg" => "update successfully", "downgrade" => 1, "subscription_type" => $subscriptionType, "inactive_users" => $qty));
                    //} else {

                    $activeUsers = $this->UserOrganization->find("count", array("conditions" => array(
                            "UserOrganization.status" => 1,
                            "UserOrganization.pool_type" => 'paid',
                            "UserOrganization.organization_id" => $org_id
                    )));


                    if ($qty > $data["Subscription"]["pool_purchased"]) {
                        $downgradeUsers = $data["Subscription"]["pool_purchased"];
                    } else {
                        $downgradeUsers = $qty;
                    }
//                    $downgradeUsers = $data["Subscription"]["pool_purchased"] - $activeUsers;

                    if ($downgradeUsers > 0 && $data["Subscription"]['is_deleted'] == 0 && $loggeduser['role'] != 1) {
                        echo json_encode(array("status" => "true", "msg" => "update successfully", "downgrade" => 1, "subscription_type" => $subscriptionType, "downgrade_users" => $downgradeUsers, "qty" => $qty));
                    } else {
                        echo json_encode(array("status" => "true", "msg" => "update successfully", "downgrade" => 0));
                    }
                    //}
                } else {
                    echo json_encode(array("status" => "true", "msg" => "update successfully", "downgrade" => 2));
                }
            } catch (Exception $e) {
                echo json_encode(array("status" => "true", "error" => "true", "error_message" => "Not able to Save Subscription", "msg" => $e->getMessage()));
            }
        }
        exit();
    }

    /* function filterallendorsement() {
      $this->layout = "ajax";
      $this->autoRender = false;
      $this->loadModel("Endorsement");
      $organization_id = $this->request->data["orgid"];
      $jobtitle = $this->request->data["jobtitles"];
      //        $this->Common->bindmodelcommonjobtitle();
      //        $this->Endorsement->bindModel(array(
      //            'hasMany' => array(
      //                'EndorseCoreValues' => array(
      //                    'className' => 'EndorseCoreValues',
      //                ),
      //            )
      //        ));

      if(!empty($jobtitle[0])){
      $conditions = array("UserOrganization.job_title_id" => $jobtitle);
      }

      $conditions[] = array(
      "UserOrganization.organization_id" => $organization_id,
      //"UserOrganization.status" => 1,
      //"Endorsement.organization_id" => $organization_id,
      //"Endorsement.endorsement_for" => "user"
      );
      //$this->UserOrganization->recursive = 2;
      $alluserorgdata = $this->UserOrganization->find("all", array("conditions" => $conditions));
      foreach($alluserorgdata as $userids){
      $uid[] = $userids["UserOrganization"]["user_id"];
      }
      $uid = array_unique($uid);
      $condtionsendorsement = array("Endorsement.Organization_id" => $organization_id);
      $condtionsendorsement["OR"] = array("endorser_id" => $uid, "endorsed_id" => $uid);
      $endorsementdata = $this->Endorsement->find("all", array("conditions" => $condtionsendorsement));
      $departments = $this->Common->getorgdepartments($organization_id);
      $entities = $this->Common->getorgentities($organization_id);
      $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
      $allvaluesendorsement = $this->Common->allvaluesendorsement($endorsementdata, $departments, $entities);
      $this->set(compact("allvaluesendorsement", "orgcorevaluesandcode"));
      echo $htmlstring = $this->render('/Elements/allendorsementslisting');
      exit;
      //        foreach($user_org_data as $userorguserid){
      //            $user_id[] = $userorguserid["UserOrganization"]["user_id"];
      //        }

      } */

    //===to set layout for printing functionality
    function printing($datatoprint) {
        $this->layout = "printing";
        $this->set(compact("datatoprint"));
    }

    //================to save spreadsheet for all endorsements

    function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    function saveasspreadsheetallendorsements() {
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
        ini_set('memory_limit', '1024M');
        $this->layout = "ajax";
        $this->loadModel("Organization");
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $folderToSaveXls = WWW_ROOT . 'xlsxfolder';
        //header('Content-Type: application/vnd.ms-excel');
        //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header('Content-Disposition: attachment;filename="usersreports.xlsx"');
        $organization_id = $this->request->data["orgid"];
        $type = $this->request->data["type"];
        $ifAttachment = $this->request->data["ifAttachment"];
        $information = $this->request->data["information"];
        $spreadsheetobject = json_decode($this->request->data["spreadsheetobject"]);
        if ($type == "guest") {
//            $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcodeForReports($organization_id);
            $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        } else {

            $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcodeForReports($organization_id);
        }

        $totalEndorsements = $this->request->data["totalendorsements"];
        $org_detail = $this->Organization->findById($organization_id, array("name", "image"));
        $orgname = $org_detail["Organization"]["name"];
        $orgimage = $org_detail["Organization"]["image"];
        $imagetype = "";
        if ($orgimage != "") {
            $imagefullpath = WWW_ROOT . ORG_IMAGE_DIR . $orgimage;
            if (file_exists($imagefullpath)) {
                $imagevalidity = getimagesize($imagefullpath);

                $mime = array('image/gif', 'image/jpeg', 'image/png');
                if (in_array($imagevalidity["mime"], $mime)) {
                    $imagetype = explode("/", $imagevalidity["mime"]);
                }
            }
        }
        $objPHPExcel->getProperties()->setCreator("Ndorse");
        $objPHPExcel->getProperties()->setLastModifiedBy("Ndorse");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHPExcel classes.");
        // Add some data
        // echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0);
        if ($information == "allendorsements") {
            try {
                //====set orgname to excel
                $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Org Name:-" . $orgname);
                $objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);

                if ($type == "both") {
                    $result = array("nDorser", "nDorser Dept.", "nDorsed", "nDorsed Dept.", "nDorsement Date", "CORE VALUES EMBODIED");
                    $coreValueCountCol = "D";
                    $countCol = 3;
                } else if ($type == 'endorsed') {
                    $result = array("nDorser", "nDorsement Date", "CORE VALUES EMBODIED");
                    $coreValueCountCol = "C";
                    $countCol = 2;
                } else if ($type == 'endorser') {
                    $result = array("nDorsed", "nDorsement Date", "CORE VALUES EMBODIED");
                    $coreValueCountCol = "C";
                    $countCol = 2;
                } else if ($type == "daisy") {
                    //$result = array("nDorser", "nDorsed","", "nDorsement Date", "Award", 'CORE VALUES');
                    $result = array("Nominator", "Nominator Title", "Nominator Email", "Nominator Mobile", "Nominee", "Nominee Department/Unit", "Sub-Center/Facility", "nDorsement Date", "Award", 'CORE VALUES');
                    $coreValueCountCol = "E";
                    $countCol = 4;
                } else if ($type == "guest") {
                    //$result = array("nDorser", "nDorsed","", "nDorsement Date", "Award", 'CORE VALUES');
                    $result = array("nDorser", "nDorsed", "nDorsed Dept.", "nDorsed Faciliy", "nDorsement Date", 'CORE VALUES');
                    $coreValueCountCol = "E";
                    $countCol = 4;
                }
                if (!empty($orgcorevaluesandcode)) {
                    foreach ($orgcorevaluesandcode as $key => $corevaluesall) {
                        array_push($result, $corevaluesall["name"]);
                    }
                    array_push($result, "Comments");
                    if ($type != "daisy") {

                        if ((bool) $ifAttachment) {
                            array_push($result, "Attachment", "Emojis");
                        }
                    }
                }
                if ($type == "daisy" || $type == "guest") {
                    array_push($result, 'Status');
                }


                $countercolumn = 3;
                $i = 65;
                $j = 0;
                $verticalText = false;
                $commentColumnLetter = 0;
                foreach ($result as $resultheader) {
                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                    $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $resultheader);
                    //=========

                    if ($resultheader == 'CORE VALUES' || $resultheader == 'CORE VALUES EMBODIED') {
                        $verticalText = true;
                        $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
                    } elseif ($resultheader == 'Comments') {
                        $verticalText = false;
                        $commentColumnLetter = $columnLetter;
                        $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setWidth(50);
                    } else {
                        $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
                    }

                    if ($verticalText) {
                        $objPHPExcel->getActiveSheet()->getStyle($columnLetter . $countercolumn)->getAlignment()->setTextRotation(90);
                    }

                    $j++;
                    $i++;
                }

                //===to bold the first row
                $objPHPExcel->getActiveSheet()->getStyle("A3:" . $columnLetter . "3")->getFont()->setBold(true);
                //$objPHPExcel->getActiveSheet()->SetCellValue("A3", $orgname);
                $countercolumn = 4;
                $columnsum = 0;
                foreach ($spreadsheetobject as $savespreadsheetdata) {
                    $i = 65;
                    $j = 0;
                    if (!empty($savespreadsheetdata[$countCol])) {
                        $columnsum += (int) $savespreadsheetdata[$countCol];
                    }
                    foreach ($savespreadsheetdata as $finaldata) {
                        $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                        $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $finaldata);
                        $i++;
                        $j++;
                    }
                    $countercolumn++;
                }

                //$totalEndorsements = $countercolumn - 4;
                //=========to write on colmuns after all values, leaving 4 cells
                $columntowriteon = $countercolumn + 4;
                //=calculate sum for corevalues
                //$objPHPExcel->getActiveSheet()->SetCellValue($coreValueCountCol . $countercolumn, $columnsum);
                $styleArray = array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => '#000000')
                    ),
//                'fill' => array(
//                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                    'color' => array('rgb' => '#ffff66')
//                )
                );


                $objPHPExcel->getActiveSheet()->getStyle($commentColumnLetter)->getAlignment()->setWrapText(true);

//                $highestRow = $$objPHPExcel->getActiveSheet()->getHighestRow();
//                for ($row = 1; $row <= $highestRow; $row++) {
//                    $sheet->getStyle("D$row")->getAlignment()->setWrapText(true);
//                }




                $objPHPExcel->getActiveSheet()->getStyle('D' . $countercolumn)->applyFromArray($styleArray);

                if ($type == "daisy") {
                    $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteon, "Total Nominations   " . $totalEndorsements);
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteon, "Total nDorsements   " . $totalEndorsements);
                }

                if ($type == "daisy") {
                    $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteon, "Total Nominations   " . $totalEndorsements);
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteon, "Total nDorsements   " . $totalEndorsements);
                }

                $columntowriteoncorevalues = $columntowriteon + 1;
                if ($type != "daisy" && $type != "guest") {
                    $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteoncorevalues, "Core Values Embodied  " . $columnsum);
                }
                //==set row height of last columns
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $columntowriteon . ':C' . $columntowriteon);
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $columntowriteoncorevalues . ':C' . $columntowriteoncorevalues);

                $objPHPExcel->getActiveSheet()->getRowDimension($columntowriteon)->setRowHeight(40);
                $objPHPExcel->getActiveSheet()->getRowDimension($columntowriteoncorevalues)->setRowHeight(40);
                $styleArray = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 20,
                        'name' => 'Verdana',
                ));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $columntowriteon)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $columntowriteoncorevalues)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $columntowriteoncorevalues)->applyFromArray($styleArray);


                $objPHPExcel->getActiveSheet()->setTitle('Simple');
                //$gdImage = imagecreatefromjpeg(WWW_ROOT . ORG_IMAGE_DIR . '/28.jpeg');
                $gdImage = imagecreatefrompng(WWW_ROOT . 'img/logo-excel.png');
                // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                $objDrawing->setName('Sample image');
                $objDrawing->setDescription('Sample image');
                $objDrawing->setImageResource($gdImage);
                $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                $objDrawing->setHeight(50);
                $objDrawing->setCoordinates('E1');
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


                if ($imagetype != "") {
                    $objDrawingnew = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawingnew->setName('Sample image');
                    $objDrawingnew->setDescription('Sample image');
                    if ($imagetype[1] == "png") {
                        $orgimage = imagecreatefrompng(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                        $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                    } else if ($imagetype[1] == "jpeg") {
                        $orgimage = imagecreatefromjpeg(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                        $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                    } else if ($imagetype[1] == "gif") {
                        $orgimage = imagecreatefromgif(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                        $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF);
                    }

                    // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
                    $objDrawingnew->setImageResource($orgimage);
                    $objDrawingnew->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawingnew->setHeight(70);
                    $objDrawingnew->setCoordinates('A1');
                    $objDrawingnew->setWorksheet($objPHPExcel->getActiveSheet());
                }


                //======set height of first column
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);
                $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(100);

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
                //$objWriter->save($folderToSaveXls . '/test.xls');
                //echo date('H:i:s') . " Done writing file.\r\n";
//                $filename = 'org' . str_replace(" ", "", $orgname) . '_allendorsements.xlsx';
                $orgNewName = preg_replace('/[^a-zA-Z0-9.]/', '_', $orgname);
                $filename = 'org' . $orgNewName . '_endorsement_history.xlsx';
                $objWriter->save($folderToSaveXls . '/' . $filename);
                echo json_encode(array("filename" => $filename, "msg" => "success"));
                //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                //header('Content-Disposition: attachment;filename='.$folderToSaveXls . '/testajax.xlsx');
                //$objWriter->save('php://output');
                //$fp = @fopen( 'php://output', 'w' );
            } catch (Exception $e) {
                echo json_encode(array("filename" => $filename, "msg" => $e));
            }
        }
        // Set properties
        //$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Hello');
        //$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'world!');
        //$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
        //$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');



        exit();
    }

    function saveasspreadsheetallposts() {
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
        ini_set('memory_limit', '1024M');
        $this->layout = "ajax";
        $this->loadModel("Organization");
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $folderToSaveXls = WWW_ROOT . 'xlsxfolder';
        //header('Content-Type: application/vnd.ms-excel');
        //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //header('Content-Disposition: attachment;filename="usersreports.xlsx"');
        $organization_id = $this->request->data["orgid"];
        $type = $this->request->data["type"];
        $userSelected = $this->request->data["userSelected"];
        $DateRange = $this->request->data["DateRange"];
        $ifAttachment = $this->request->data["ifAttachment"];
        $information = $this->request->data["information"];
        $spreadsheetobject = json_decode($this->request->data["spreadsheetobject"]);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $totalEndorsements = $this->request->data["totalendorsements"];
        $org_detail = $this->Organization->findById($organization_id, array("name", "image"));
        $orgname = $org_detail["Organization"]["name"];
        $orgimage = $org_detail["Organization"]["image"];
        $imagetype = "";
        if ($orgimage != "") {
            $imagefullpath = WWW_ROOT . ORG_IMAGE_DIR . $orgimage;
            if (file_exists($imagefullpath)) {
                $imagevalidity = getimagesize($imagefullpath);

                $mime = array('image/gif', 'image/jpeg', 'image/png');
                if (in_array($imagevalidity["mime"], $mime)) {
                    $imagetype = explode("/", $imagevalidity["mime"]);
                }
            }
        }
        $objPHPExcel->getProperties()->setCreator("Ndorse");
        $objPHPExcel->getProperties()->setLastModifiedBy("Ndorse");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHPExcel classes.");
        // Add some data
        // echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0);
        if ($information == "allposts") {
            try {
                //====set username to excel with date filter
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Report On: " . $userSelected . " " . $DateRange);
                $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
                //====set orgname to excel
                $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Org Name:-" . $orgname);
                $objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);

                if ($type == "both") {
                    $result = array("Posts", "Post Clicked", "Clicked on Paper Clip", "Clicked on Attachment");
                    $coreValueCountCol = "D";
                    $countCol = 3;
                }

                $countercolumn = 3;
                $i = 65;
                $j = 0;
                foreach ($result as $resultheader) {
                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                    $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $resultheader);
                    //=========
                    $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
                    $j++;
                    $i++;
                }

                //===to bold the first row
                $objPHPExcel->getActiveSheet()->getStyle("A3:" . $columnLetter . "3")->getFont()->setBold(true);
                //$objPHPExcel->getActiveSheet()->SetCellValue("A3", $orgname);
                $countercolumn = 4;
                $columnsum = 0;
                foreach ($spreadsheetobject as $savespreadsheetdata) {
                    $i = 65;
                    $j = 0;
                    if (!empty($savespreadsheetdata[$countCol])) {
                        $columnsum += (int) $savespreadsheetdata[$countCol];
                    }
                    foreach ($savespreadsheetdata as $finaldata) {
                        $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                        $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $finaldata);
                        $i++;
                        $j++;
                    }
                    $countercolumn++;
                }

                //$totalEndorsements = $countercolumn - 4;
                //=========to write on colmuns after all values, leaving 4 cells
                $columntowriteon = $countercolumn + 4;
                //=calculate sum for corevalues
//                $objPHPExcel->getActiveSheet()->SetCellValue($coreValueCountCol . $countercolumn, $columnsum);
                $styleArray = array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => '#000000')
                    ),
//                'fill' => array(
//                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                    'color' => array('rgb' => '#ffff66')
//                )
                );

                $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteon, "Total Posts   " . $totalEndorsements);
                $columntowriteoncorevalues = $columntowriteon + 1;
                //$objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteoncorevalues, "Core Values Embodied  " . $columnsum);
                //==set row height of last columns
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $columntowriteon . ':C' . $columntowriteon);
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $columntowriteoncorevalues . ':C' . $columntowriteoncorevalues);

                $objPHPExcel->getActiveSheet()->getRowDimension($columntowriteon)->setRowHeight(40);
//                $objPHPExcel->getActiveSheet()->getRowDimension($columntowriteoncorevalues)->setRowHeight(40);
                $styleArray = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 20,
                        'name' => 'Verdana',
                ));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $columntowriteon)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $columntowriteoncorevalues)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $columntowriteoncorevalues)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->setTitle('Simple');
                //$gdImage = imagecreatefromjpeg(WWW_ROOT . ORG_IMAGE_DIR . '/28.jpeg');
                $gdImage = imagecreatefrompng(WWW_ROOT . 'img/logo-excel.png');
                // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                $objDrawing->setName('Sample image');
                $objDrawing->setDescription('Sample image');
                $objDrawing->setImageResource($gdImage);
                $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                $objDrawing->setHeight(50);
                $objDrawing->setCoordinates('E1');
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


                if ($imagetype != "") {
                    $objDrawingnew = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawingnew->setName('Sample image');
                    $objDrawingnew->setDescription('Sample image');
                    if ($imagetype[1] == "png") {
                        $orgimage = imagecreatefrompng(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                        $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                    } else if ($imagetype[1] == "jpeg") {
                        $orgimage = imagecreatefromjpeg(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                        $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                    } else if ($imagetype[1] == "gif") {
                        $orgimage = imagecreatefromgif(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                        $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF);
                    }

                    // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
                    $objDrawingnew->setImageResource($orgimage);
                    $objDrawingnew->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawingnew->setHeight(70);
                    $objDrawingnew->setCoordinates('B1');
                    $objDrawingnew->setWorksheet($objPHPExcel->getActiveSheet());
                }


                //======set height of first column
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);


                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
                //$objWriter->save($folderToSaveXls . '/test.xls');
                //echo date('H:i:s') . " Done writing file.\r\n";
//                $filename = 'org' . str_replace(" ", "", $orgname) . '_allendorsements.xlsx';
                $orgNewName = preg_replace('/[^a-zA-Z0-9.]/', '_', $orgname);
                $filename = 'org' . $orgNewName . 'post_click_Postwise_data.xlsx';
                $objWriter->save($folderToSaveXls . '/' . $filename);
                echo json_encode(array("filename" => $filename, "msg" => "success"));
                //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                //header('Content-Disposition: attachment;filename='.$folderToSaveXls . '/testajax.xlsx');
                //$objWriter->save('php://output');
                //$fp = @fopen( 'php://output', 'w' );
            } catch (Exception $e) {
                echo json_encode(array("filename" => $filename, "msg" => $e));
            }
        } elseif ($information == "allpostsusers") {
            try {
                //====set username to excel with date filter
                $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Report On: " . $userSelected . " " . $DateRange);
                $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
                //====set orgname to excel
                $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Org Name:-" . $orgname);
                $objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);

                if ($type == "both") {
                    $result = array("User", "Title", "Department", "Sub Org", "Total Post Cliks", 'Total Clicks on Paper Clip', 'Total Attachment Clicks', 'Total Clicks (Paper Clip + Attachments)', 'Total Post Like Clicks');
                    $coreValueCountCol = "D";
                    $countCol = 3;
                }

                $countercolumn = 3;
                $i = 65;
                $j = 0;
                foreach ($result as $resultheader) {
                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                    $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $resultheader);
                    //=========
                    $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
                    $j++;
                    $i++;
                }

                //===to bold the first row
                $objPHPExcel->getActiveSheet()->getStyle("A3:" . $columnLetter . "3")->getFont()->setBold(true);
                //$objPHPExcel->getActiveSheet()->SetCellValue("A3", $orgname);
                $countercolumn = 4;
                $columnsum = 0;
                foreach ($spreadsheetobject as $savespreadsheetdata) {
                    $i = 65;
                    $j = 0;
                    if (!empty($savespreadsheetdata[$countCol])) {
                        $columnsum += (int) $savespreadsheetdata[$countCol];
                    }
                    foreach ($savespreadsheetdata as $finaldata) {
                        $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                        $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $finaldata);
                        $i++;
                        $j++;
                    }
                    $countercolumn++;
                }

                //$totalEndorsements = $countercolumn - 4;
                //=========to write on colmuns after all values, leaving 4 cells
                $columntowriteon = $countercolumn + 4;
                //=calculate sum for corevalues
//                $objPHPExcel->getActiveSheet()->SetCellValue($coreValueCountCol . $countercolumn, $columnsum);
                $styleArray = array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => '#000000')
                    ),
//                'fill' => array(
//                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                    'color' => array('rgb' => '#ffff66')
//                )
                );

                $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteon, "Total Users   " . $totalEndorsements);
                $columntowriteoncorevalues = $columntowriteon + 1;
                //$objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteoncorevalues, "Core Values Embodied  " . $columnsum);
                //==set row height of last columns
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $columntowriteon . ':C' . $columntowriteon);
                $objPHPExcel->getActiveSheet()->mergeCells('A' . $columntowriteoncorevalues . ':C' . $columntowriteoncorevalues);

                $objPHPExcel->getActiveSheet()->getRowDimension($columntowriteon)->setRowHeight(40);
//                $objPHPExcel->getActiveSheet()->getRowDimension($columntowriteoncorevalues)->setRowHeight(40);
                $styleArray = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 20,
                        'name' => 'Verdana',
                ));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $columntowriteon)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('A' . $columntowriteoncorevalues)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $columntowriteoncorevalues)->applyFromArray($styleArray);
                $objPHPExcel->getActiveSheet()->setTitle('Simple');
                //$gdImage = imagecreatefromjpeg(WWW_ROOT . ORG_IMAGE_DIR . '/28.jpeg');
                $gdImage = imagecreatefrompng(WWW_ROOT . 'img/logo-excel.png');
                // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
                $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                $objDrawing->setName('Sample image');
                $objDrawing->setDescription('Sample image');
                $objDrawing->setImageResource($gdImage);
                $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                $objDrawing->setHeight(50);
                $objDrawing->setCoordinates('E1');
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


                if ($imagetype != "") {
                    $objDrawingnew = new PHPExcel_Worksheet_MemoryDrawing();
                    $objDrawingnew->setName('Sample image');
                    $objDrawingnew->setDescription('Sample image');
                    if ($imagetype[1] == "png") {
                        $orgimage = imagecreatefrompng(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                        $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                    } else if ($imagetype[1] == "jpeg") {
                        $orgimage = imagecreatefromjpeg(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                        $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                    } else if ($imagetype[1] == "gif") {
                        $orgimage = imagecreatefromgif(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                        $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF);
                    }

                    // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
                    $objDrawingnew->setImageResource($orgimage);
                    $objDrawingnew->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                    $objDrawingnew->setHeight(70);
                    $objDrawingnew->setCoordinates('B1');
                    $objDrawingnew->setWorksheet($objPHPExcel->getActiveSheet());
                }


                //======set height of first column
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);


                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
                //$objWriter->save($folderToSaveXls . '/test.xls');
                //echo date('H:i:s') . " Done writing file.\r\n";
//                $filename = 'org' . str_replace(" ", "", $orgname) . '_allendorsements.xlsx';
                $orgNewName = preg_replace('/[^a-zA-Z0-9.]/', '_', $orgname);
                $filename = 'org' . $orgNewName . '_posts_click_Userwise_data.xlsx';
                $objWriter->save($folderToSaveXls . '/' . $filename);
                echo json_encode(array("filename" => $filename, "msg" => "success"));
                //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                //header('Content-Disposition: attachment;filename='.$folderToSaveXls . '/testajax.xlsx');
                //$objWriter->save('php://output');
                //$fp = @fopen( 'php://output', 'w' );
            } catch (Exception $e) {
                echo json_encode(array("filename" => $filename, "msg" => $e));
            }
        }
        // Set properties
        //$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Hello');
        //$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'world!');
        //$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
        //$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');



        exit();
    }

    function leaderboardsavespreadsheet() {
        ini_set('memory_limit', '1024M');
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
        $objPHPExcel = new PHPExcel();
        $folderToSaveXls = WWW_ROOT . 'xlsxfolder';
        $objPHPExcel->getProperties()->setCreator("Ndorse");
        $objPHPExcel->getProperties()->setLastModifiedBy("Ndorse");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHPExcel classes.");
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("OrgDepartment");
        $this->loadModel("Endorsement");
        $this->loadModel("Organization");
        //ob_start();
        $result = array();
        $organization_id = $this->request->data["orgid"];
        $orgname = $this->Organization->field("name", array("id" => $organization_id));
        $orgnamewithoutspace = preg_replace('/[^a-zA-Z0-9.]/', '_', $orgname);
        $filename = WWW_ROOT . 'xlsxfolder/' . $orgnamewithoutspace . '.csv';
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        //$fp = fopen('php://output', 'w');
        $fp = fopen($filename, 'w');

        $startdate = $this->request->data["startdate"];
        $enddate = $this->request->data["enddate"];
        $searchvalue = $this->request->data["searchvalue"];

        $searchedvalue = isset($this->request->query['searchvalue']) ? $this->request->query['searchvalue'] : "";
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues')));
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
        //=========means number of guys he endorse
        $conditionscountendorsement = array('organization_id' => $organization_id);

        if ($startdate != "" and $enddate != "") {

            $startDateArray = explode('-', $startdate);
            $endDateArray = explode('-', $enddate);

//            $startDateToSearch = $startDateArray[2] . '-' . $startDateArray[1] . '-' . $startDateArray[0];
//            $endDateToSearch = $endDateArray[2] . '-' . $endDateArray[1] . '-' . $endDateArray[0];
            $startDateToSearch = $startDateArray[2] . '-' . $startDateArray[0] . '-' . $startDateArray[1];
            $endDateToSearch = $endDateArray[2] . '-' . $endDateArray[0] . '-' . $endDateArray[1];


            array_push($conditionscountendorsement, "date(created) between '$startDateToSearch' and '$endDateToSearch'");
        }

        //===============binding model conditions
        $this->Common->commonleaderboardbindings($conditionscountendorsement);
        $this->UserOrganization->recursive = 2;
        $conditionsuserorg = array("UserOrganization.organization_id" => $organization_id, "UserOrganization.status" => array(0, 1, 2, 3), "UserOrganization.user_role" => array(2, 3, 6));
        if ($searchvalue != "") {
            $conditionsuserorg["OR"] = array("User.fname like '%$searchvalue%'", "User.lname like '%$searchvalue%'");
        }
        $endorsementdata = $this->UserOrganization->find("all", array("order" => "User.fname", "conditions" => $conditionsuserorg));
//        pr($conditionsuserorg);
//        echo $this->UserOrganization->getLastQuery();
//        $log = $this->UserOrganization->getDataSource()->getLog(false, false);
//        pr($log);
//        exit;


        $arrayendorsementdetail = $this->Common->arrayforendorsementdetail($endorsementdata);

//        echo "test";
//        pr($endorsementdata);
//        pr($arrayendorsementdetail);
//        exit;

        $result = array("Name", "nDorser", "nDorsed", "Total", "Department", "Sub-Organization", "Title", "Sub-Center");

        //====setting header for the sheet
        $countercolumn = 1;
        $j = 0;
        foreach ($result as $resultheader) {
            //$cellpos = $this->getNameFromNumber($i);
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
            $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $resultheader);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
            $j++;
        }


        //fputcsv($fp, $result);
        if (!empty($arrayendorsementdetail)) {
            foreach ($arrayendorsementdetail as $endorsementdetail) {
                $resultspreadsheeet[] = array($endorsementdetail["name"], $endorsementdetail["endorser"], $endorsementdetail["endorsed"], $endorsementdetail["endorsed"] + $endorsementdetail["endorser"], $endorsementdetail["department"], $endorsementdetail["entity"], $endorsementdetail["title"], $endorsementdetail["subcenter_name"]);
                //fputcsv($fp, $result);
            }
        }

//        pr($resultspreadsheeet); exit;
        $countercolumn = 2;
        foreach ($resultspreadsheeet as $finalresultantsheet) {
            $j = 0;
            foreach ($finalresultantsheet as $columnsinsheet) {
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $columnsinsheet);
                $j++;
            }
            $countercolumn++;
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $filename = 'org' . $orgnamewithoutspace . '_leaderboard.xlsx';
        $objWriter->save($folderToSaveXls . '/' . $filename);
        $contentob = ob_get_contents();
//        ob_get_clean();
//        fclose($fp);
        //$result = array("filename" => $orgnamewithoutspace . ".csv", "result" => "success");
        $result = array("filename" => $filename, "result" => "success");
        echo json_encode($result);
        exit;
    }

    function leaderboardsavespreadsheetNew() {
        ini_set('memory_limit', '1024M');
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
        $objPHPExcel = new PHPExcel();
        $folderToSaveXls = WWW_ROOT . 'xlsxfolder';
        $objPHPExcel->getProperties()->setCreator("Ndorse");
        $objPHPExcel->getProperties()->setLastModifiedBy("Ndorse");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHPExcel classes.");
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("OrgDepartment");
        $this->loadModel("Endorsement");
        $this->loadModel("Organization");
        //ob_start();
        $result = array();
        $organization_id = $this->request->data["orgid"];
        $orgname = $this->Organization->field("name", array("id" => $organization_id));
        $orgnamewithoutspace = preg_replace('/[^a-zA-Z0-9.]/', '_', $orgname);
        $filename = WWW_ROOT . 'xlsxfolder/' . $orgnamewithoutspace . '.csv';
//	chmod($filename, 0777);
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        //$fp = fopen('php://output', 'w');
        $fp = fopen($filename, 'w');

        $startdate = $this->request->data["startdate"];
        $enddate = $this->request->data["enddate"];
        $searchvalue = $this->request->data["searchvalue"];

        $subcenterid = $this->request->data["subcenterid"];
        $departmentid = $this->request->data["departmentid"];

        $searchedvalue = isset($this->request->query['searchvalue']) ? $this->request->query['searchvalue'] : "";
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues', 'EndorseHashtag')));
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
        //=========means number of guys he endorse
        $conditionscountendorsement = array('organization_id' => $organization_id);

        if ($startdate != "" and $enddate != "") {

            $startDateArray = explode('-', $startdate);
            $endDateArray = explode('-', $enddate);

//            $startDateToSearch = $startDateArray[2] . '-' . $startDateArray[1] . '-' . $startDateArray[0];
//            $endDateToSearch = $endDateArray[2] . '-' . $endDateArray[1] . '-' . $endDateArray[0];
            $startDateToSearch = $startDateArray[2] . '-' . $startDateArray[0] . '-' . $startDateArray[1];
            $endDateToSearch = $endDateArray[2] . '-' . $endDateArray[0] . '-' . $endDateArray[1];


            array_push($conditionscountendorsement, "date(created) between '$startDateToSearch' and '$endDateToSearch'");
        }

        $conditionscountendorsement['type !='] = array('guest', 'daisy');
        //===============binding model conditions
        $this->Common->commonleaderboardbindings($conditionscountendorsement);
        $this->UserOrganization->recursive = 2;


        if (isset($subcenterid) && $subcenterid != 0) {
            if (isset($departmentid) && $departmentid != 0) {
                $conditionsuserorg = array("UserOrganization.organization_id" => $organization_id, "UserOrganization.status" => array(0, 1, 2, 3), "UserOrganization.user_role" => array(2, 3, 6),
                    "UserOrganization.subcenter_id" => $subcenterid, "UserOrganization.department_id" => $departmentid);
            } else {
                $conditionsuserorg = array("UserOrganization.organization_id" => $organization_id, "UserOrganization.status" => array(0, 1, 2, 3), "UserOrganization.user_role" => array(2, 3, 6),
                    "UserOrganization.subcenter_id" => $subcenterid);
            }
        } else {
            $conditionsuserorg = array("UserOrganization.organization_id" => $organization_id, "UserOrganization.status" => array(0, 1, 2, 3), "UserOrganization.user_role" => array(2, 3, 6));
        }

        if ($searchvalue != "") {
            $conditionsuserorg["OR"] = array("User.fname like '%$searchvalue%'", "User.lname like '%$searchvalue%'");
        }
        $this->UserOrganization->recursive = 2;
        $endorsementdata = $this->UserOrganization->find("all", array("order" => "User.fname", "conditions" => $conditionsuserorg));
//        pr($endorsementdata);exit;
        //echo $this->UserOrganization->getLastQuery();
        //$log = $this->UserOrganization->getDataSource()->getLog(false, false);
        //pr($log);
        // exit;


        $arrayendorsementdetail = $this->Common->arrayforendorsementdetail($endorsementdata);

//        echo "test";
//        pr($endorsementdata);
//        pr($arrayendorsementdetail);
//        exit;

        $result = array("Name", "nDorser", "nDorsed", "Total", "Department", "Sub-Organization", "Title", "Sub-center");

        //====setting header for the sheet
        $countercolumn = 1;
        $j = 0;
        foreach ($result as $resultheader) {
            //$cellpos = $this->getNameFromNumber($i);
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
            $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $resultheader);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
            $j++;
        }


        //fputcsv($fp, $result);
        if (!empty($arrayendorsementdetail)) {
            foreach ($arrayendorsementdetail as $endorsementdetail) {
                $resultspreadsheeet[] = array($endorsementdetail["name"], $endorsementdetail["endorser"], $endorsementdetail["endorsed"], $endorsementdetail["endorsed"] + $endorsementdetail["endorser"], $endorsementdetail["department"], $endorsementdetail["entity"], $endorsementdetail["title"], $endorsementdetail["subcenter_short_name"]);
                //fputcsv($fp, $result);
            }
        }

//        pr($resultspreadsheeet); exit;
        $countercolumn = 2;
        foreach ($resultspreadsheeet as $finalresultantsheet) {
            $j = 0;
            foreach ($finalresultantsheet as $columnsinsheet) {
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $columnsinsheet);
                $j++;
            }
            $countercolumn++;
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $filename = 'org' . $orgnamewithoutspace . '_leaderboard.xlsx';
        $objWriter->save($folderToSaveXls . '/' . $filename);
        $contentob = ob_get_contents();
//        ob_get_clean();
//        fclose($fp);
        //$result = array("filename" => $orgnamewithoutspace . ".csv", "result" => "success");
        $result = array("filename" => $filename, "result" => "success");
        echo json_encode($result);
        exit;
    }

    function managerReportSpreadsheet() {
        ini_set('memory_limit', '1024M');
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
        $objPHPExcel = new PHPExcel();
        $folderToSaveXls = WWW_ROOT . 'xlsxfolder';
        $objPHPExcel->getProperties()->setCreator("Ndorse");
        $objPHPExcel->getProperties()->setLastModifiedBy("Ndorse");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHPExcel classes.");
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("OrgDepartment");
        $this->loadModel("Endorsement");
        $this->loadModel("Organization");
        //ob_start();
        $result = array();
        $organization_id = $this->request->data["orgid"];
        $orgname = $this->Organization->field("name", array("id" => $organization_id));
        $orgnamewithoutspace = preg_replace('/[^a-zA-Z0-9.]/', '_', $orgname);
        $filename = WWW_ROOT . 'xlsxfolder/' . $orgnamewithoutspace . '.csv';
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);

        //$fp = fopen('php://output', 'w');
        $fp = fopen($filename, 'w');

        $startdate = $this->request->data["startdate"];
        $enddate = $this->request->data["enddate"];
        $orgname = $this->request->data["orgname"];

        $facility_id = $this->request->data["facility_id"];
        $departmentId = $this->request->data["department_id"];


        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues')));
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
        //=========means number of guys he endorse
        $conditionscountendorsement = array('organization_id' => $organization_id);

        if ($startdate != "" and $enddate != "") {

            $startDateArray = explode('-', $startdate);
            $endDateArray = explode('-', $enddate);

//            $startDateToSearch = $startDateArray[2] . '-' . $startDateArray[1] . '-' . $startDateArray[0];
//            $endDateToSearch = $endDateArray[2] . '-' . $endDateArray[1] . '-' . $endDateArray[0];
            $startDateToSearch = $startDateArray[2] . '-' . $startDateArray[0] . '-' . $startDateArray[1];
            $endDateToSearch = $endDateArray[2] . '-' . $endDateArray[0] . '-' . $endDateArray[1];


            array_push($conditionscountendorsement, "date(created) between '$startDateToSearch' and '$endDateToSearch'");
        }
        $conditionscountendorsement['type !='] = array('guest', 'daisy');
//        pr($conditionscountendorsement);
//        exit;
        //===============binding model conditions
        $this->Common->commonleaderboardbindings($conditionscountendorsement);
        $subcenterCondition = "";
        if (isset($facility_id) && $facility_id != '' && $facility_id != 0) {
            $subcenterCondition = "subcenter_id = " . $facility_id;
        }

        $deptCondition = "";
        if (isset($departmentId) && $departmentId != '' && $departmentId != 0) {
            $deptCondition = "department_id = " . $departmentId;
        }
        $this->UserOrganization->recursive = 2;
        $endorsementdata = $this->UserOrganization->find("all", array("order" => "User.fname", "conditions" =>
            array("UserOrganization.organization_id" => $organization_id, $subcenterCondition, $deptCondition, "UserOrganization.status" => array(1, 2, 3), "UserOrganization.user_role" => array(2, 3, 4, 6))));
//        pr($conditionsuserorg);
//        echo $this->UserOrganization->getLastQuery();
//        $log = $this->UserOrganization->getDataSource()->getLog(false, false);
//        pr($log);
//        exit;
        $arrayendorsementdetail = $this->Common->arrayforendorsementdetail($endorsementdata);

//        echo "test";
//        pr($endorsementdata);
//        pr($arrayendorsementdetail);
//        exit;

        $result = array("Manager View Report");

        //====setting header for the sheet
        $countercolumn = 1;
        $j = 0;
        foreach ($result as $resultheader) {
            //$cellpos = $this->getNameFromNumber($i);
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
            $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $resultheader);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
            $j++;
        }
        $result = array("Organization:", $orgname);

        //====setting header for the sheet
        $countercolumn = 2;
        $j = 0;
        foreach ($result as $resultheader) {
            //$cellpos = $this->getNameFromNumber($i);
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
            $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $resultheader);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
            $j++;
        }
        $result = array("Name", "nDorsement Received", "nDorsement Sent", "Last Login Date", "Department", "Facility");

        //====setting header for the sheet
        $countercolumn = 4;
        $j = 0;
        foreach ($result as $resultheader) {
            //$cellpos = $this->getNameFromNumber($i);
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
            $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $resultheader);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
            $j++;
        }


        //fputcsv($fp, $result);
        if (!empty($arrayendorsementdetail)) {
            foreach ($arrayendorsementdetail as $endorsementdetail) {
                $subcenterName = '';
                if (isset($endorsementdetail['subcenter_short_name']) && $endorsementdetail['subcenter_short_name'] != '') {
                    $subcenterName = $endorsementdetail['subcenter_short_name'];
                } else if (isset($endorsementdetail['subcenter_name']) && $endorsementdetail['subcenter_name'] != '') {
                    $subcenterName = $endorsementdetail['subcenter_name'];
                }
                $resultspreadsheeet[] = array($endorsementdetail["name"], $endorsementdetail["endorsed"], $endorsementdetail["endorser"], $endorsementdetail["last_app_used"], $endorsementdetail["department"], $subcenterName);
                //fputcsv($fp, $result);
            }
        }

//        pr($resultspreadsheeet); exit;
        $countercolumn = 5;
        if (!empty($resultspreadsheeet)) {
            foreach ($resultspreadsheeet as $finalresultantsheet) {
                $j = 0;
                foreach ($finalresultantsheet as $columnsinsheet) {
                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                    $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $columnsinsheet);
                    $j++;
                }
                $countercolumn++;
            }
        }
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $filename = 'org' . $orgnamewithoutspace . '_leaderboard.xlsx';
        $objWriter->save($folderToSaveXls . '/' . $filename);
        $contentob = ob_get_contents();
//        ob_get_clean();
//        fclose($fp);
        //$result = array("filename" => $orgnamewithoutspace . ".csv", "result" => "success");
        $result = array("filename" => $filename, "result" => "success");
        echo json_encode($result);
        exit;
    }

    function saslistingreports() {
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("Endorsement");
        $this->loadModel("User");
        $this->loadModel("Organization");
        $this->loadModel("OrgDepartment");
        $result = array();
        //===========declaring xls pattern and object
        $objPHPExcel = new PHPExcel();
        $folderToSaveXls = WWW_ROOT . 'xlsxfolder';
        $objPHPExcel->getProperties()->setCreator("Ndorse");
        $objPHPExcel->getProperties()->setLastModifiedBy("Ndorse");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHPExcel classes.");
        $organization_id = $this->Session->read('orgid');
        $org_detail = $this->Organization->findById($organization_id, array("name", "image"));
        $orgname = $org_detail["Organization"]["name"];
        $orgimage = $org_detail["Organization"]["image"];

        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $user_id = $this->request->data["userid"];
        $information = $this->request->data["information"];
        $datearray = array("startdate" => "", "enddate" => "");
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));

        $this->UserOrganization->bindModel(array(
            'hasOne' => array(
                'OrgDepartment' => array(
                    'className' => 'OrgDepartment',
                    "foreignKey" => false,
                    "conditions" => array("UserOrganization.department_id = OrgDepartment.id"),
                ),
                'OrgJobTitle' => array(
                    'className' => 'OrgJobTitle',
                    "foreignKey" => false,
                    "conditions" => array("UserOrganization.job_title_id = OrgJobTitle.id"),
                )
            )
        ));

        $userdeptdata = $this->UserOrganization->find("all", array("conditions" => array("UserOrganization.organization_id" => $organization_id, "UserOrganization.user_id" => $user_id)));

        $userdepartment = $userdeptdata[0]["OrgDepartment"]["name"];
        $userjobtitle = $userdeptdata[0]["OrgJobTitle"]["title"];

        if ($this->Session->read('datearray')) {
            $datearray = $this->Session->read('datearray');
        }
        $allothervalues = array(
            "departments" => $departments,
            "entities" => $entities,
            "corevalues" => $orgcorevaluesandcode,
        );

        $userdata = $this->User->findById($user_id, array("fname", "lname", "image"));
        $username = $userdata["User"]["fname"] . " " . $userdata["User"]["lname"];
        $imagetype = "";
        $userimage = $userdata["User"]["image"];
        if ($userimage != "") {
            $imagefullpath = WWW_ROOT . PROFILE_IMAGE_DIR . $userimage;
            if (file_exists($imagefullpath)) {
                $imagevalidity = getimagesize($imagefullpath);
                $mime = array('image/gif', 'image/jpeg', 'image/png');
                if (in_array($imagevalidity["mime"], $mime)) {
                    $imagetype = explode("/", $imagevalidity["mime"]);
                }
            }
        }
        //start writing to excel


        $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Org Name:-" . $orgname);
        $objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('A1', "User:-" . $username);
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', "");
        $objPHPExcel->getActiveSheet()->SetCellValue('B1', "Department:-" . $userdepartment . "\n Job Title:-" . $userjobtitle);
        $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
        $columnsum = 0;

        if ($information == "endorsed" || $information == "endorser") {
            $result = array("nDorsed", "nDorsement Date", "Core Values Embodied ");
            if (!empty($allothervalues["corevalues"])) {
                foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                    array_push($result, $corevaluesall["name"]);
                }
                array_push($result, "Comments");
            }
        }
        $countercolumn = 3;
        $i = 65;
        $j = 0;
        foreach ($result as $resultheader) {
            //$cellpos = $this->getNameFromNumber($i);
            $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
            $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $resultheader);
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
            $i++;
            $j++;
        }
        $countercolumn = 4;
        $objPHPExcel->getActiveSheet()->getStyle("A3:" . $columnLetter . "3")->getFont()->setBold(true);
        if ($information == "endorser") {
            $spreadsheetobject = array();
            $conditionsendorser = array("organization_id" => $organization_id, "endorser_id" => $user_id);
            if ($datearray["startdate"] != "" and $datearray["enddate"] != "") {
                $startdate = $datearray["startdate"];
                $enddate = $datearray["enddate"];
                array_push($conditionsendorser, "date(Endorsement.created) between '$startdate' and '$enddate'");
            }
            $endorser_data = $this->Endorsement->find("all", array("conditions" => $conditionsendorser));
            $allvaluesendorser = $this->Common->allvaluesendorser($endorser_data, $departments, $entities);

            if (!empty($allvaluesendorser)) {

                foreach ($allvaluesendorser as $endorservalues) {
                    $date = new DateTime($endorservalues["date"]);
                    $endorservalues["date"] = $date->format('m-d-Y');
                    $result = array($endorservalues["name"], $endorservalues["date"], $endorservalues["totalpoints"]);
                    foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                        if (in_array($key, $endorservalues["corevaluesid"])) {
                            array_push($result, "YES");
                        } else {
                            array_push($result, "NO");
                        }
                    }
                    array_push($result, $endorservalues["endorsement_message"]);
                    $spreadsheetobject[] = $result;
                }
            }
            $countercolumn = 4;
            $columnsum = 0;
            foreach ($spreadsheetobject as $savespreadsheetdata) {
                $i = 65;
                $j = 0;
                //$cellpos = $this->getNameFromNumber($i);
                if (!empty($savespreadsheetdata[2])) {
                    $columnsum += (int) $savespreadsheetdata[2];
                }
                foreach ($savespreadsheetdata as $finaldata) {
                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                    $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $finaldata);
                    $i++;
                    $j++;
                }
                $countercolumn++;
            }
        } else if ($information == "endorsed") {
            $conditionsendorsed = array("organization_id" => $organization_id, "endorsed_id" => $user_id);
            if ($datearray["startdate"] != "" and $datearray["enddate"] != "") {
                $startdate = $datearray["startdate"];
                $enddate = $datearray["enddate"];
                array_push($conditionsendorsed, "date(Endorsement.created) between '$startdate' and '$enddate'");
            }

            $endorsed_data = $this->Endorsement->find("all", array("conditions" => $conditionsendorsed));
            //pr($endorsed_data);
            $endorsernamedetail = "";
            $allvaluesendorsed = $this->Common->allvaluesendorsed($endorsed_data);
            if (!empty($allvaluesendorsed)) {
                foreach ($allvaluesendorsed as $endorsedvalues) {
                    $date = new DateTime($endorsedvalues["date"]);
                    $endorsedvalues["date"] = $date->format('m-d-Y');
                    $result = array($endorsedvalues["name"], $endorsedvalues["date"], $endorsedvalues["totalpoints"]);
                    foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                        if (in_array($key, $endorsedvalues["corevaluesid"])) {
                            array_push($result, "YES");
                        } else {
                            array_push($result, "NO");
                        }
                    }
                    array_push($result, $endorsedvalues["endorsed_message"]);
                    $spreadsheetobject[] = $result;
                }
                $countercolumn = 4;
                $columnsum = 0;
                foreach ($spreadsheetobject as $savespreadsheetdata) {
                    $i = 65;
                    $j = 0;
                    if (!empty($savespreadsheetdata[2])) {
                        $columnsum += (int) $savespreadsheetdata[2];
                    }
                    foreach ($savespreadsheetdata as $finaldata) {
                        $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                        $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $countercolumn, $finaldata);
                        $i++;
                        $j++;
                    }
                    $countercolumn++;
                }
            }
        }
        $totalEndorsements = $countercolumn - 4;
        //=========to write on colmuns after all values, leaving 4 cells
        $columntowriteon = $countercolumn + 4;

        //=calculate sum for corevalues
        $objPHPExcel->getActiveSheet()->SetCellValue("C" . $countercolumn, $columnsum);
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '#000000')
            ),
        );
        $objPHPExcel->getActiveSheet()->getStyle('C' . $countercolumn)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteon, "Total Endorsements   " . $totalEndorsements);
        $columntowriteoncorevalues = $columntowriteon + 1;
        $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteoncorevalues, "Core Values Embodied  " . $columnsum);

        //==set row height of last columns
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $columntowriteon . ':C' . $columntowriteon);
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $columntowriteoncorevalues . ':C' . $columntowriteoncorevalues);

        $objPHPExcel->getActiveSheet()->getRowDimension($columntowriteon)->setRowHeight(40);
        $objPHPExcel->getActiveSheet()->getRowDimension($columntowriteoncorevalues)->setRowHeight(40);
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'size' => 20,
                'name' => 'Verdana',
        ));
        $objPHPExcel->getActiveSheet()->getStyle('A' . $columntowriteon)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('A' . $columntowriteoncorevalues)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->getStyle('B' . $columntowriteoncorevalues)->applyFromArray($styleArray);
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        //$gdImage = imagecreatefromjpeg(WWW_ROOT . ORG_IMAGE_DIR . '/28.jpeg');
        $gdImage = imagecreatefrompng(WWW_ROOT . 'img/logo-excel.png');
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample image');
        $objDrawing->setDescription('Sample image');
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(50);
        $objDrawing->setCoordinates('E1');
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());



        if ($imagetype != "") {
            $objDrawingnew = new PHPExcel_Worksheet_MemoryDrawing();
            $objDrawingnew->setName('Sample image');
            $objDrawingnew->setDescription('Sample image');
            if ($imagetype[1] == "png") {
                $userimage = imagecreatefrompng(WWW_ROOT . PROFILE_IMAGE_DIR . $userimage);
                $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
            } else if ($imagetype[1] == "jpeg") {
                $userimage = imagecreatefromjpeg(WWW_ROOT . PROFILE_IMAGE_DIR . $userimage);
                $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
            } else if ($imagetype[1] == "gif") {
                $userimage = imagecreatefromgif(WWW_ROOT . PROFILE_IMAGE_DIR . $userimage);
                $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF);
            }

            // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
            $objDrawingnew->setImageResource($userimage);
            $objDrawingnew->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);

            $objDrawingnew->setCoordinates('A1');
            //$maxWidth = 250;
            $maxHeight = 60;
            $objDrawingnew->setHeight($maxHeight);

            $objDrawingnew->setWorksheet($objPHPExcel->getActiveSheet());
        }



        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(70);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //$filename = str_replace(" ","", $orgname) . '_Endorsements_summary.xlsx';

        $filename = 'org' . str_replace(" ", "", $username) . '_Endorsements_Received_Summary.xlsx';
        if ($information == "endorsed") {
            $filename = 'org' . str_replace(" ", "", $username) . '_Endorsements_Given_Summary.xlsx';
        }

        $objWriter->save($folderToSaveXls . '/' . $filename);

        echo json_encode(array("filename" => $filename, "msg" => "success"));
        exit;
    }

    function faqformsubmit() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("globalsettingFaq");
        $question = trim($this->request->data["question"]);
        $answer = trim($this->request->data["answer"]);
        $formsubmitvalue = trim($this->request->data["formsubmitvalue"]);
        if ($formsubmitvalue == 0) {
            $faqarray = array(
                "question" => $question,
                "answer" => $answer,
                "created" => date("Y-m-d H:i:sa"),
                "updated" => date("Y-m-d H:i:sa"),
            );
            $conditionsfaq = array("question" => $question);
            $counter = $this->globalsettingFaq->find("count", array("conditions" => $conditionsfaq));
            if ($counter == 0) {
                $this->globalsettingFaq->save($faqarray);
                $faqkey_key = $this->globalsettingFaq->getLastInsertId();
                $result = array("lastid" => $faqkey_key, "recorddata" => $faqarray, "msg" => "success");
            } else {
                $result = array("msg" => "Qustion already Exist");
            }
        } else {
            $faqarray = array(
                "question" => $question,
                "answer" => nl2br($answer),
                "updated" => date("Y-m-d H:i:sa"),
            );
            $conditionsfaq = array("question" => $question, "id !=" => $formsubmitvalue);
            $counter = $this->globalsettingFaq->find("count", array("conditions" => $conditionsfaq));
            if ($counter == 0) {
                $this->globalsettingFaq->id = $formsubmitvalue;
                $this->globalsettingFaq->save($faqarray);
                $result = array("lastid" => $formsubmitvalue, "recorddata" => $faqarray, "msg" => "success");
            } else {
                $result = array("msg" => "Such Question already Exist");
            }
        }

        echo json_encode($result);
        exit;
    }

    //=editing the faq data
    function faqformedit() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("globalsettingFaq");
        $idtoedit = trim($this->request->data["idtoedit"]);
        $datatoedit = $this->globalsettingFaq->findById($idtoedit);
        $result = array("datatoedit" => $datatoedit, "msg" => "success");
        echo json_encode($result);
    }

    //=editing the faq delete
    function faqformdelete() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $this->loadModel("globalsettingFaq");
        $idtodelete = trim($this->request->data["idtodelete"]);
        $datatodelete = $this->globalsettingFaq->delete($idtodelete);
        $result = array("msg" => "deleted");
        echo json_encode($result);
    }

    function loadmoreliveendorsements() {
        $this->loadModel("Organization");
        $this->loadModel("Endorsement");
        $this->layout = "ajax";
        $this->autoRender = false;
        $totalrecords = $this->request->data["totalrecords"];
        $searchkeyword = $this->request->data["searchkeyword"];
        $organization_id = $this->request->data["orgid"];
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValue')));
        $this->Organization->bindModel(array(
            'hasMany' => array(
                "Endorsement" => array(
                    "className" => "Endorsement",
                    'order' => 'created DESC',
                    'limit' => 20,
                    'conditions' => array("Endorsement.type!='private'", 'status=1'),
                    'offset' => $totalrecords
                ),
            )
                )
        );
        $this->Organization->recursive = 2;
        $orgdata = $this->Organization->findById($organization_id);
        //$this->set(compact('orgdata', 'totalusers'));
        foreach ($orgdata["Endorsement"] as $endorsementdata) {
            //=====finding endorsement for the month
            $userid[] = $endorsementdata["endorser_id"];
            if ($endorsementdata["endorsement_for"] == "user") {
                $userid[] = $endorsementdata["endorsed_id"];
            }
        }
        if (!empty($userid)) {
            $totaluserdetails = $this->User->find("all", array("conditions" => array("id" => $userid), "fields" => array("id", "fname", "lname", "image")));
            foreach ($totaluserdetails as $userdetail) {
                $userdetails[$userdetail["User"]["id"]] = $userdetail;
            }
        }
        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $allvalues = array("department" => $departments, "entities" => $entities, "orgcorevaluesandcode" => $orgcorevaluesandcode);
        $this->set('authUser', $this->Auth->user());
        $this->set(compact('orgdata', 'userdetails', 'allvalues'));
        echo $htmlstring = $this->render('/Elements/livesearchdata');
        exit;
    }

    function loadmoreguestndorsements() {
        $this->loadModel("Organization");
        $this->loadModel("Endorsement");
        $this->layout = "ajax";
        $this->autoRender = false;

        $totalrecords = isset($this->request->data["totalrecords"]) ? $this->request->data["totalrecords"] : 0;
        $status = $this->request->data["status"];
        $orgid = $this->request->data["orgId"];

        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));

        $this->Organization->recursive = 2;

        $this->Organization->bindModel(array(
            'hasMany' => array(
                "Endorsement" => array(
                    "className" => "Endorsement",
                    'order' => 'created DESC',
                    'conditions' => array("Endorsement.type ='guest'", "status =" . $status),
                    'limit' => 20,
//                    'offset' => $totalrecords
                ))
        ));
//        echo "test";
        $orgDetail = $this->Organization->findById($orgid);
//        pr($orgDetail);
//        $log = $this->Organization->getDataSource()->getLog(false, false);
//        debug($log);
//        echo $this->Organization->getLastQuery();
//        exit;

        foreach ($orgDetail["Endorsement"] as $endorsementdata) {
            $userid[] = $endorsementdata["endorser_id"];
            if ($endorsementdata["endorsement_for"] == "user") {
                $userid[] = $endorsementdata["endorsed_id"];
            }
        }

        if (!empty($userid)) {
            $totaluserdetails = $this->User->find("all", array("conditions" => array("id" => $userid), "fields" => array("id", "fname", "lname", "image")));
            foreach ($totaluserdetails as $userdetail) {
                $userdetails[$userdetail["User"]["id"]] = $userdetail;
            }
        }

        $totalrecords = $this->Endorsement->find("count", array("conditions" => array("organization_id" => $orgid, "Endorsement.type" => 'guest', "Endorsement.status =" . $status)));

        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($orgid);
        $allvalues = array("orgcorevaluesandcode" => $orgcorevaluesandcode);

        $this->set('authUser', $this->Auth->user());
        $this->set('orgdata', $orgDetail);

        $this->set(compact('orgDetail', 'userdetails', 'allvalues', 'totalrecords'));
        echo $htmlstring = $this->render('/Elements/livesearchdataguest');
        exit;
    }

    function loadmoredaisyndorsements() {
        $this->loadModel("Organization");
        $this->loadModel("Endorsement");
        $this->layout = "ajax";
        $this->autoRender = false;

        $totalrecords = isset($this->request->data["totalrecords"]) ? $this->request->data["totalrecords"] : 0;
        $status = $this->request->data["status"];
        $orgid = $this->request->data["orgId"];

        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));

        $this->Organization->recursive = 2;

        $this->Organization->bindModel(array(
            'hasMany' => array(
                "Endorsement" => array(
                    "className" => "Endorsement",
                    'order' => 'created DESC',
                    'conditions' => array("Endorsement.type ='daisy'", "status =" . $status),
                    'limit' => 20,
//                    'offset' => $totalrecords
                ))
        ));
//        echo "test";
        $orgDetail = $this->Organization->findById($orgid);
//        pr($orgDetail);
//        $log = $this->Organization->getDataSource()->getLog(false, false);
//        debug($log);
//        echo $this->Organization->getLastQuery();
//        exit;

        foreach ($orgDetail["Endorsement"] as $endorsementdata) {
            $userid[] = $endorsementdata["endorser_id"];
            if ($endorsementdata["endorsement_for"] == "user") {
                $userid[] = $endorsementdata["endorsed_id"];
            }
        }

        if (!empty($userid)) {
            $totaluserdetails = $this->User->find("all", array("conditions" => array("id" => $userid), "fields" => array("id", "fname", "lname", "image")));
            foreach ($totaluserdetails as $userdetail) {
                $userdetails[$userdetail["User"]["id"]] = $userdetail;
            }
        }

        $totalrecords = $this->Endorsement->find("count", array("conditions" => array("organization_id" => $orgid, "Endorsement.type" => 'daisy', "Endorsement.status =" . $status)));

        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($orgid);
        $allvalues = array("orgcorevaluesandcode" => $orgcorevaluesandcode);

        $this->set('authUser', $this->Auth->user());
        $this->set('orgdata', $orgDetail);

        $this->set(compact('orgDetail', 'userdetails', 'allvalues', 'totalrecords'));
        echo $htmlstring = $this->render('/Elements/livesearchdataguest');
        exit;
    }

    function loadmorevideos() {
        $this->loadModel("Organization");
        $this->loadModel("OrgVideo");
        $this->layout = "ajax";
        $this->autoRender = false;

        $totalrecords = isset($this->request->data["totalrecords"]) ? $this->request->data["totalrecords"] : 0;
        $status = $this->request->data["status"];
        $orgid = $this->request->data["orgId"];

        ////=====================================================////

        $params = array();
        $params['fields'] = array("OrgVideo.*", "CONCAT(trim(fname),' ',trim(lname)) as fullname", "User.image");
        $params['conditions'] = array('org_id' => $orgid, 'OrgVideo.status' => $status);
        $params['order'] = array('OrgVideo.created' => 'desc');
        $params['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'LEFT',
                'conditions' => array(
                    'User.id = OrgVideo.uploaded_by'
                )
            )
        );

        $orgActiveVideoListing = $this->OrgVideo->find("all", $params);
//        pr($orgActiveVideoListing);exit;
        $orgVideoListArray = array();
        $totalVideo = count($orgActiveVideoListing);
        if (!empty($orgActiveVideoListing)) {
            foreach ($orgActiveVideoListing as $index => $videoList) {
                $videoThumbnail = $videoList['OrgVideo']['thumbnail'];
                $video_url = $videoList['OrgVideo']['video_url'];
                if (isset($video_url) && $video_url != '') {
                    $video_urlhttp = Router::url('/', true) . "app/webroot/" . VIDEO_DIR . $video_url;
                    if (strpos($video_urlhttp, 'localhost') < 0) {
                        $video_urlhttp = str_replace("http", "https", $video_urlhttp);
                    }
                }
                $videoThumbnailhttp = '';
                if (isset($videoThumbnail) && $videoThumbnail != '') {
                    $videoThumbnailhttp = Router::url('/', true) . "app/webroot/" . $videoThumbnail;
                    if (strpos($videoThumbnailhttp, 'localhost') < 0) {
                        $videoThumbnailhttp = str_replace("http", "https", $videoThumbnailhttp);
                    }
                }

                $orgVideoListArray[$index] = $videoList['OrgVideo'];
                $orgVideoListArray[$index]['video_full_url'] = $video_urlhttp;
                $orgVideoListArray[$index]['thumbnail'] = $videoThumbnailhttp;
                $orgVideoListArray[$index]['uploaded_by_name'] = $videoList[0]['fullname'];
                $orgVideoListArray[$index]['user_image'] = $videoList['User']['image'];
            }
        }
        if (isset($orgid)) {
            $this->loadModel('Organization');
            $this->Organization->recursive = 2;
            $orgDetail = $this->Organization->findById($orgid);
            $this->loadModel('OrgVideo');

            //pr($orgDetail);

            if (!empty($userid)) {
                $totaluserdetails = $this->User->find("all", array("conditions" => array("id" => $userid), "fields" => array("id", "fname", "lname", "image")));
                foreach ($totaluserdetails as $userdetail) {
                    $userdetails[$userdetail["User"]["id"]] = $userdetail;
                }
            }
            $this->set('orgDetail', $orgDetail);
        }
        $this->set(compact('userdetails', 'allvalues', "totalrecords", "loggedinUser", "orgVideoListArray"));
        $this->set('jsIncludes', array('customerportal.js'));
        $this->set('authUser', $this->Auth->user());
        echo $htmlstring = $this->render('/Elements/livesearchdatavideo');
        exit;
        ////=====================================================////









        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));

        $this->Organization->recursive = 2;

        $this->Organization->bindModel(array(
            'hasMany' => array(
                "Endorsement" => array(
                    "className" => "Endorsement",
                    'order' => 'created DESC',
                    'conditions' => array("Endorsement.type ='guest'", "status =" . $status),
                    'limit' => 20,
//                    'offset' => $totalrecords
                ))
        ));
//        echo "test";
        $orgDetail = $this->Organization->findById($orgid);
//        pr($orgDetail);
//        $log = $this->Organization->getDataSource()->getLog(false, false);
//        debug($log);
//        echo $this->Organization->getLastQuery();
//        exit;

        foreach ($orgDetail["Endorsement"] as $endorsementdata) {
            $userid[] = $endorsementdata["endorser_id"];
            if ($endorsementdata["endorsement_for"] == "user") {
                $userid[] = $endorsementdata["endorsed_id"];
            }
        }

        if (!empty($userid)) {
            $totaluserdetails = $this->User->find("all", array("conditions" => array("id" => $userid), "fields" => array("id", "fname", "lname", "image")));
            foreach ($totaluserdetails as $userdetail) {
                $userdetails[$userdetail["User"]["id"]] = $userdetail;
            }
        }

        $totalrecords = $this->Endorsement->find("count", array("conditions" => array("organization_id" => $orgid, "Endorsement.type" => 'guest', "Endorsement.status =" . $status)));

        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($orgid);
        $allvalues = array("orgcorevaluesandcode" => $orgcorevaluesandcode);

        $this->set('authUser', $this->Auth->user());
        $this->set('orgdata', $orgDetail);

        $this->set(compact('orgDetail', 'userdetails', 'allvalues', 'totalrecords'));
        echo $htmlstring = $this->render('/Elements/livesearchdatavideo');
        exit;
    }

    function SearchLiveEndorsementsHints() {
        try {
            $this->loadModel("Organization");
            $this->loadModel("Endorsement");
            $this->layout = "ajax";
            $this->autoRender = false;
            $searchvalue = $this->request->data["searchvalue"];
            $organization_id = $this->request->data["orgid"];
            $departments = $this->Common->getorgdepartments($organization_id);
            $departmentsarray = array_map('strtolower', $departments);
            $resultdepartments = preg_grep('~' . $searchvalue . '~', $departmentsarray);
            foreach ($resultdepartments as $key => $value) {
                $resultdepartments[$key] = $departments[$key];
            }
            $entities = $this->Common->getorgentities($organization_id);
            $entitiesarray = array_map('strtolower', $entities);
            $resultentities = preg_grep('~' . $searchvalue . '~', $entitiesarray);
            foreach ($resultentities as $key => $value) {
                $resultentities[$key] = $entities[$key];
            }
            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
            $conditionsuo = array("organization_id" => $organization_id);
            $conditionsuo["OR"] = array(
                "User.fname LIKE '%$searchvalue%'",
                "User.lname LIKE '%$searchvalue%'",
                "concat(User.fname,' ',User.lname) LIKE '%$searchvalue%'"
            );
            $fields = array("User.id", "User.fname", "User.lname");
            $ueserorgdata = $this->UserOrganization->find("all", array("conditions" => $conditionsuo, "fields" => $fields));
            $resultusers = array();
            foreach ($ueserorgdata as $datausers) {
                $fullname = ucfirst($datausers["User"]["fname"]) . " " . ucfirst($datausers["User"]["lname"]);
                $resultusers[$datausers["User"]["id"]] = $fullname;
            }
            $allvalues = array("departmentsresults" => $resultdepartments, "entityresults" => $resultentities, "usersresults" => $resultusers, "msg" => "success");
        } catch (Exception $e) {
            $allvalues = array("msg" => $e);
        }
        echo json_encode($allvalues);
        exit;
    }

    function searchendorsementfiltered() {
        try {
            $this->loadModel("Endorsement");
            $this->layout = "ajax";
            $this->autoRender = false;
            $organization_id = $this->request->data["orgid"];
            $endorsementfor = $this->request->data["endorsementfor"];
            $endorsementid = $this->request->data["endorsementid"];
            $totalrecords = isset($this->request->data["totalrecords"]) ? $this->request->data["totalrecords"] : "";

            $conditionsendorsements = array("organization_id" => $organization_id, "not" => array("type" => "private"));
            if ($endorsementfor == "user") {
                $conditionsendorsements["OR"] = array("endorser_id" => $endorsementid, "endorsed_id" => $endorsementid);
            } else if ($endorsementfor == "department") {
                $conditionsendorsements[] = array("endorsed_id" => $endorsementid, "endorsement_for" => $endorsementfor);
            } else if ($endorsementfor == "entity") {
                $conditionsendorsements[] = array("endorsed_id" => $endorsementid, "endorsement_for" => $endorsementfor);
            }
            if ($totalrecords == "") {
                $endorsementdatas = $this->Endorsement->find("all", array("order" => "created DESC", "limit" => 20, "conditions" => $conditionsendorsements));
            } else {
                $endorsementdatas = $this->Endorsement->find("all", array("offset" => $totalrecords, "order" => "created DESC", "limit" => 20, "conditions" => $conditionsendorsements));
            }

            //==as in earlier it is same
            $userid = array();
            $userdetails = array();
            foreach ($endorsementdatas as $endorsementdata) {
                //=====finding endorsement for the month
                $userid[] = $endorsementdata["Endorsement"]["endorser_id"];
                if ($endorsementdata["Endorsement"]["endorsement_for"] == "user") {
                    $userid[] = $endorsementdata["Endorsement"]["endorsed_id"];
                }
            }
            if (!empty($userid)) {
                $totaluserdetails = $this->User->find("all", array("conditions" => array("id" => $userid), "fields" => array("id", "fname", "lname", "image")));
                foreach ($totaluserdetails as $userdetail) {
                    $userdetails[$userdetail["User"]["id"]] = $userdetail;
                }
            }
            $departments = $this->Common->getorgdepartments($organization_id);
            $entities = $this->Common->getorgentities($organization_id);
            $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
            $allvalues = array("department" => $departments, "entities" => $entities, "orgcorevaluesandcode" => $orgcorevaluesandcode);
            $this->set(compact('endorsementdatas', 'userdetails', 'allvalues'));
            echo $htmlstring = $this->render('/Elements/liveendorsementsfilterdata');
        } catch (Exception $e) {
            
        }
        exit;
    }

    function viewprofile() {
        $this->loadModel("UserOrganization");
        $this->layout = "ajax";
        $this->autoRender = false;
        $userorgid = $this->request->data["userorgid"];
        try {
            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
            $this->UserOrganization->bindModel(array(
                'hasOne' => array(
                    'OrgDepartment' => array(
                        'className' => 'OrgDepartment',
                        "foreignKey" => false,
                        "conditions" => array("UserOrganization.department_id = OrgDepartment.id"),
                    ),
                    'OrgJobTitle' => array(
                        'className' => 'OrgJobTitle',
                        "foreignKey" => false,
                        "conditions" => array("UserOrganization.job_title_id = OrgJobTitle.id"),
                    ),
                    'OrgSubcenter' => array(
                        'className' => 'OrgSubcenter',
                        "foreignKey" => false,
                        "conditions" => array("UserOrganization.subcenter_id = OrgSubcenter.id"),
                    )
                )
            ));
            $userorgdata = $this->UserOrganization->findById($userorgid);
            $userorgdata["User"]["dob"] = $this->Common->dateConvertDisplay($userorgdata["User"]["dob"]);
            $userorgdata["User"]["fname"] = $this->Common->decodeData($userorgdata["User"]["fname"]);
            $userorgdata["User"]["lname"] = $this->Common->decodeData($userorgdata["User"]["lname"]);
            $userorgdata["User"]["email"] = $this->Common->decodeData($userorgdata["User"]["email"]);
            $userorgdata["User"]["username"] = $this->Common->decodeData($userorgdata["User"]["username"]);
            $userorgdata["User"]["daisy_enabled"] = ($userorgdata["User"]["daisy_enabled"] == '0') ? 'No' : 'Yes';
            echo json_encode(array("data" => $userorgdata, "msg" => "success"));
        } catch (Exception $e) {
            echo json_encode(array("data" => "", "msg" => $e));
        }

        exit;
    }

    public function getAllowedDowngrade() {
        $this->loadModel("Subscription");
        $statusConfig = Configure::read("statusConfig");
        $organizationId = $this->ViewCont->decodeString($this->request->data['organizationId']);
        $loggedinUser = $this->Auth->user();

        $subscription = $this->Subscription->findByOrganizationId($organizationId);
        $purchasedPool = $subscription['Subscription']['pool_purchased'];

        $activeUsers = $this->UserOrganization->find("count", array("conditions" => array(
                "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']),
                "UserOrganization.pool_type" => 'paid',
                "UserOrganization.organization_id" => $organizationId
        )));
        $diff = $purchasedPool - $activeUsers;
        echo json_encode(array("allowedUsers" => $diff, "msg" => "success"));
        exit;
    }

    public function isLoggedIn() {
        $portal = $this->Cookie->read("portal_cookie");
        if ($this->Session->check('Auth.User')) {
            echo json_encode(array("success" => true, "portal" => $portal));
            exit;
        } else {
            echo json_encode(array("success" => false, "portal" => $portal));
            exit;
        }
    }

    //=attachemd image call all ndorsement
    function getattachedimagespopup() {
        $this->loadModel("EndorseAttachment");
        $this->layout = "ajax";
        $this->autoRender = false;
        $endorsementid = $this->request->data["endorsementid"];
        $type = $this->request->data["type"];
        $imagesarray = array();
        $endorsementimagedata = $this->EndorseAttachment->find("all", array("conditions" => array("endorsement_id" => $endorsementid, "type" => $type)));
        if (!empty($endorsementimagedata)) {
            foreach ($endorsementimagedata as $dataimages) {
                $directory = $type == "image" ? ENDORSE_IMAGE_DIR : EMOJIS_IMAGE_DIR;
                if (file_exists(WWW_ROOT . $directory . $dataimages["EndorseAttachment"]["name"])) {
                    if ($type == "image") {
//                        $image = WWW_ROOT . ENDORSE_IMAGE_DIR . $dataimages["EndorseAttachment"]["name"];
//                        
//                        $imagedata = file_get_contents($image);
//                        $base64 = base64_encode($imagedata);
//                        $src = 'data: '.mime_content_type($image).';base64,'.$base64;
                        //$imagesarray[] = $src;
                        $rootUrl = Router::url('/', true);
                        //$rootUrl = str_replace("http", "https", $rootUrl);
                        //Added by saurabh on 23/06/2021
                        //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
                        $imagesarray[] = $rootUrl . ENDORSE_IMAGE_DIR . $dataimages["EndorseAttachment"]["name"];
                    } else {
                        $rootUrl = Router::url('/', true);
                        //$rootUrl = str_replace("http", "https", $rootUrl);
                        //Added by saurabh on 23/06/2021
                        //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
                        $imagesarray[] = $rootUrl . EMOJIS_IMAGE_DIR . $dataimages["EndorseAttachment"]["name"];
                    }
                }
            }
        }
        echo json_encode(array("result" => "true", "data" => $imagesarray));
        exit;
    }

    function downloadattachedimages() {
        $this->layout = "ajax";
        $this->autoRender = false;
        $files = $this->request->data["imagestodownload"];
        $counter = count($files);
        if ($counter > 1) {
            //=========zip download
            //$tmpFile = tempnam('/tmp', '');
            $tmpFile = WWW_ROOT . "zipfiles/tmp.zip";
            @unlink($tmpFile);
            $zip = new ZipArchive;
            $zip->open($tmpFile, ZipArchive::CREATE);
            foreach ($files as $file) {
                $fileContent = file_get_contents($file);
                $zip->addFromString(basename($file), $fileContent);
            }
            $zip->close();
            $url = "tmp.zip";
        } else {
            $url = $files[0];
        }
        echo $url;

//        header('Content-Type: application/zip');
//        header('Content-disposition: attachment; filename=file.zip');
//        header("location:".$tmpFile);   
        //header('Content-Length: ' . filesize($tmpFile));
        //readfile($tmpFile);
        //unlink($tmpFile);
        exit;
    }

    public function saveUserSpreadsheet() {
        ini_set('memory_limit', '256M');
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
        $this->layout = "ajax";
        $this->loadModel("Organization");
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $folderToSaveXls = WWW_ROOT . 'xlsxfolder';

        $organization_id = $this->request->data["orgid"];
//        $type = $this->request->data["type"];
//        $ifAttachment = $this->request->data["ifAttachment"];
//        $information = $this->request->data["information"];
//        $spreadsheetobject = json_decode($this->request->data["spreadsheetobject"]);
//        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
//        $totalEndorsements = $this->request->data["totalendorsements"];
        $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization")));
//        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
        $userOrganizations = $this->UserOrganization->find("all", array("conditions" => array('organization_id' => $organization_id)));
//        pr($userOrganization);die;
        $org_detail = $this->Organization->findById($organization_id, array("name", "image"));
        $orgname = $org_detail["Organization"]["name"];
        $orgimage = $org_detail["Organization"]["image"];
        $imagetype = "";
        if ($orgimage != "") {
            $imagefullpath = WWW_ROOT . ORG_IMAGE_DIR . $orgimage;
            if (file_exists($imagefullpath)) {
                $imagevalidity = getimagesize($imagefullpath);

                $mime = array('image/gif', 'image/jpeg', 'image/png');
                if (in_array($imagevalidity["mime"], $mime)) {
                    $imagetype = explode("/", $imagevalidity["mime"]);
                }
            }
        }
        $objPHPExcel->getProperties()->setCreator("Ndorse");
        $objPHPExcel->getProperties()->setLastModifiedBy("Ndorse");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHPExcel classes.");
        // Add some data
        // echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0);
//        if ($information == "allendorsements") {
        try {
            //====set orgname to excel
            $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Org Name:-" . $orgname);
            $objPHPExcel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);

//                if($type == "both") {
//                    $result = array("nDorser", "nDorsed", "nDorsement Date", "CORE VALUES EMBODIED");
//                    $coreValueCountCol = "D";
//                    $countCol = 3;
//                } else if ($type == 'endorsed') {
//                    $result = array("nDorser", "nDorsement Date", "CORE VALUES EMBODIED");
//                    $coreValueCountCol = "C";
//                    $countCol = 2;
//                } else if ($type == 'endorser') {
//                    $result = array("nDorsed", "nDorsement Date", "CORE VALUES EMBODIED");
//                    $coreValueCountCol = "C";
//                    $countCol = 2;
//                }



            $header = array("First Name", "Last Name", "Email", "Role", "Status");

            $j = 0;
            $rowCount = 3;
            foreach ($header as $resultheader) {
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $resultheader);
                //=========
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
                $j++;
            }

            //===to bold the first row
            $objPHPExcel->getActiveSheet()->getStyle("A3:" . $columnLetter . "3")->getFont()->setBold(true);
            //$objPHPExcel->getActiveSheet()->SetCellValue("A3", $orgname);
            $rowCount = 4;
            $columnsum = 0;
            $j = 0;
            $statusConfig = Configure::read('statusConfig');
            $roleList = $this->Common->setSessionRoles();

            foreach ($userOrganizations as $userOrganization) {
                $user = $userOrganization['User'];
                $userOrg = $userOrganization['UserOrganization'];
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(0);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $user['fname']);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(1);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $user['lname']);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(2);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $user['email']);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(3);

                if ($roleList[$userOrg['user_role']] == 'endorser') {
                    $role = "nDorser";
                } else {
                    $role = ucfirst($roleList[$userOrg['user_role']]);
                }

                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $role);


                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(4);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, array_search($userOrg["status"], $statusConfig));

                $j++;
                $rowCount++;
            }

//                //$totalEndorsements = $countercolumn - 4;
//                //=========to write on colmuns after all values, leaving 4 cells
//                $columntowriteon = $countercolumn + 4;
//                //=calculate sum for corevalues
//
//                $objPHPExcel->getActiveSheet()->SetCellValue($coreValueCountCol . $countercolumn, $columnsum);
//                $styleArray = array(
//                    'font' => array(
//                        'bold' => true,
//                        'color' => array('rgb' => '#000000')
//                    ),
//
//                );
//                $objPHPExcel->getActiveSheet()->getStyle('D' . $countercolumn)->applyFromArray($styleArray);
//
//
//                $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteon, "Total Endorsements   " . $totalEndorsements);
//                $columntowriteoncorevalues = $columntowriteon + 1;
//                $objPHPExcel->getActiveSheet()->SetCellValue("A" . $columntowriteoncorevalues, "Core Values Embodied  " . $columnsum);
//
//                //==set row height of last columns
//                $objPHPExcel->getActiveSheet()->mergeCells('A' . $columntowriteon . ':C' . $columntowriteon);
//                $objPHPExcel->getActiveSheet()->mergeCells('A' . $columntowriteoncorevalues . ':C' . $columntowriteoncorevalues);
//
//                $objPHPExcel->getActiveSheet()->getRowDimension($columntowriteon)->setRowHeight(40);
//                $objPHPExcel->getActiveSheet()->getRowDimension($columntowriteoncorevalues)->setRowHeight(40);
//                $styleArray = array(
//                    'font' => array(
//                        'bold' => true,
//                        'size' => 20,
//                        'name' => 'Verdana',
//                ));
//                $objPHPExcel->getActiveSheet()->getStyle('A' . $columntowriteon)->applyFromArray($styleArray);
//                $objPHPExcel->getActiveSheet()->getStyle('A' . $columntowriteoncorevalues)->applyFromArray($styleArray);
//                $objPHPExcel->getActiveSheet()->getStyle('B' . $columntowriteoncorevalues)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->setTitle('Simple');
            //$gdImage = imagecreatefromjpeg(WWW_ROOT . ORG_IMAGE_DIR . '/28.jpeg');
            $gdImage = imagecreatefrompng(WWW_ROOT . 'img/logo-excel.png');
            // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setName('Sample image');
            $objDrawing->setDescription('Sample image');
            $objDrawing->setImageResource($gdImage);
            $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
            $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
            $objDrawing->setHeight(50);
            $objDrawing->setCoordinates('E1');
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());



            if ($imagetype != "") {
                $objDrawingnew = new PHPExcel_Worksheet_MemoryDrawing();
                $objDrawingnew->setName('Sample image');
                $objDrawingnew->setDescription('Sample image');
                if ($imagetype[1] == "png") {
                    $orgimage = imagecreatefrompng(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                    $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                } else if ($imagetype[1] == "jpeg") {
                    $orgimage = imagecreatefromjpeg(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                    $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                } else if ($imagetype[1] == "gif") {
                    $orgimage = imagecreatefromgif(WWW_ROOT . ORG_IMAGE_DIR . $orgimage);
                    $objDrawingnew->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF);
                }

                // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
                $objDrawingnew->setImageResource($orgimage);
                $objDrawingnew->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                $objDrawingnew->setHeight(70);
                $objDrawingnew->setCoordinates('A1');
                $objDrawingnew->setWorksheet($objPHPExcel->getActiveSheet());
            }


            //======set height of first column
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);


            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
            //$objWriter->save($folderToSaveXls . '/test.xls');
            //echo date('H:i:s') . " Done writing file.\r\n";
//                $filename = 'org' . str_replace(" ", "", $orgname) . '_allendorsements.xlsx';
            $orgNewName = preg_replace('/[^a-zA-Z0-9.]/', '_', $orgname);
            $filename = 'org' . str_replace(" ", "", $orgNewName) . '_userlist.xlsx';
            $objWriter->save($folderToSaveXls . '/' . $filename);
            echo json_encode(array("filename" => $filename, "msg" => "success"));
            //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            //header('Content-Disposition: attachment;filename='.$folderToSaveXls . '/testajax.xlsx');
            //$objWriter->save('php://output');
            //$fp = @fopen( 'php://output', 'w' );
        } catch (Exception $e) {
            echo json_encode(array("filename" => $filename, "msg" => $e));
        }
//        }
        // Set properties
        //$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Hello');
        //$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'world!');
        //$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
        //$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');



        exit();
    }

    public function ifEmailExist() {
        if ($this->User->email_registered($this->request->data)) {
            echo json_encode(array('status' => true, "msg" => "The email you entered is already registered."));
        } else {
            echo json_encode(array('status' => false));
        }

        exit;
    }

    public function getActiveUserList() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $this->loadModel("UserOrganization");
        $orgid = $this->request->data["org_id"];
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
        $userorgdata = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $orgid, "UserOrganization.status" => array(1), "UserOrganization.user_role" => array(2, 3), "UserOrganization.user_id !=" => $this->Auth->User("id")), "order" => "UserOrganization.id ASC"));

        $fresult = array();
        if (!empty($userorgdata)) {

            foreach ($userorgdata as $usersdata) {
                $fresult[$usersdata["UserOrganization"]["id"]] = array("id" => $usersdata["User"]["id"], "user_role" => $usersdata["UserOrganization"]["user_role"], "fname" => $usersdata["User"]["fname"], "lname" => $usersdata["User"]["lname"], "email" => $usersdata["User"]["email"]);
            }
        }

        $result = array("fresult" => $fresult, "org_id" => $orgid);
        echo json_encode($result);
        exit;
    }

    public function bulkReinviteUsers() {
//        pr($this->request->data);die;

        $this->loadModel("Organization");
        $this->loadModel("User");
        $this->loadModel("UserOrganization");
        $this->loadModel("Email");

        $organization = $this->Organization->findById($this->request->data['orgId']);
        $orgDetails = array("id" => $this->request->data['orgId'], "name" => $organization['Organization']['name'], "code" => $organization['Organization']['secret_code']);

        $userIds = explode(",", $this->request->data['userIds']);

        $joinCodes = $this->getUnexpiredJoinCodes($userIds, $this->request->data['orgId']);

        $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization")));
        $users = $this->UserOrganization->find("all", array("conditions" => array("User.id" => $userIds, "UserOrganization.organization_id" => $this->request->data['orgId']), "fields" => array("*")));
        foreach ($users as $user) {
            $passwordRandom = $this->Common->randompasswordgenerator(8);
            $fname = $user["User"]["fname"];
            $username = $user["User"]["username"];
            $email = $user["User"]["email"];

            $viewVars = array('fname' => $fname, 'username' => $username, 'password' => $passwordRandom, 'organization_name' => $organization['Organization']['name']);

            if ($user["UserOrganization"]['joined'] == 0) {
                $viewVars['show_code'] = true;
                $viewVars['join_code'] = $joinCodes[$user["User"]['id']];
            } else {
                $viewVars['show_code'] = false;
                $viewVars['join_code'] = "";
            }

            /** Added by Babulal Prasad @7-feb-2017 to unsubscribe from emails * */
            $userIdEncrypted = base64_encode($user["User"]['id']);
            $rootUrl = Router::url('/', true);
            //$rootUrl = str_replace("http", "https", $rootUrl);
            //Added by saurabh on 23/06/2021
            //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
            $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
            $viewVars["pathToRender"] = $pathToRender;
            /**/

            $configVars = serialize($viewVars);
            $subject = "Invitation to join nDorse";
            $emailvar = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => "invitation_admin");
            //==================to change pasword for the userid;
            $this->User->id = $user["User"]["id"];
            $this->User->saveField('password', $passwordRandom, false);
            $this->Email->Create();
            $email = $this->Email->save($emailvar);
        }

        echo json_encode(array("success" => true));
        exit;
    }

    private function getUnexpiredJoinCodes($userIds, $organizationId) {
        $this->loadModel("JoinOrgCode");
        $joinCodes = $this->JoinOrgCode->find("all", array("conditions" => array("organization_id" => $organizationId, "user_id" => $userIds, "is_expired" => 0)));
        $joinCodeList = array();

        foreach ($joinCodes as $joinCode) {
            $joinCodeList[$joinCode['JoinOrgCode']['user_id']] = $joinCode['JoinOrgCode']['code'];
        }

        return $joinCodeList;
    }

    public function getOrgList() {
        $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
        $userOrgs = $this->UserOrganization->find("all", array("conditions" => array("user_id" => $this->request->query['userId'], "status !=" => 2)));

        $userOrgIds = array();

        foreach ($userOrgs as $userOrg) {
            $userOrgIds[] = $userOrg['UserOrganization']['organization_id'];
        }


        $organizations = $this->Organization->find("all", array("conditions" => array('Organization.status' => 1, "Organization.id NOT IN" => $userOrgIds)));
        $orgList = array();

        foreach ($organizations as $organization) {
            $orgList[$organization['Organization']['id']] = $organization['Organization']['name'];
        }

        echo json_encode(array("success" => true, "orgList" => $orgList));
        exit();
    }

    public function orgInviteUser() {
        $this->loadModel("Organization");
        $this->loadModel("User");
        $this->loadModel("Email");
        $this->loadModel("Invite");

        $userId = $this->request->data['userId'];
        $orgId = $this->request->data['orgId'];


        $user = $this->User->findById($userId);
        $email = $user["User"]["email"];

        //check in invite table and save in invote accordingly else only send maillike done in app invite
//        $invitedRecords = $this->Invite->find("all", array("conditions" => array("email" => $email, "organization_id" => $orgIds)));
//        
//        $invitedOrgs = array();
//            foreach ($invitedRecords as $invited) {
//                $invitedOrgs[] = $invited['Invite']['organization_id'];
//            }

        $organization = $this->Organization->find("first", array("conditions" => array('Organization.id' => $orgId)));
        $userOrgData = array();

        $statusFields = $this->Common->getNewUserOrgFields($orgId, 1);

        $userOrgData = array(
            "user_id" => $userId,
            "organization_id" => $organization['Organization']['id'],
            "user_role" => 3,
            "pool_type" => $statusFields['poolType'],
            "status" => $statusFields['status'],
            "joined" => 0,
            "flow" => "web_invite",
            "send_invite" => 1
        );
        $this->UserOrganization->create();
        $this->UserOrganization->save($userOrgData, false);
        $userOrgId = $this->UserOrganization->id;

        $joinOrgCode = $this->Common->getJoinOrgCode($orgId, $email, $userId, $userOrgId);

        $viewVars = array('fname' => $user['User']['fname'], 'organization_name' => $organization['Organization']['name'], "join_code" => $joinOrgCode, "no_switch" => false);

        /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
        $userIdEncrypted = base64_encode($userId);
        $rootUrl = Router::url('/', true);
        //$rootUrl = str_replace("http", "https", $rootUrl);
        //Added by saurabh on 23/06/2021
        //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
        $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
        $viewVars["pathToRender"] = $pathToRender;
        /*         * * */

        $configVars = serialize($viewVars);
        $subject = "Invitation to join nDorse";
        $emailVar = array("to" => $user['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "invitation_admin_existing");
        $this->Email->Create();
        $this->Email->save($emailVar);



//            if(!in_array($organization['Organization']['id'], $invitedOrgs)) {
//                $invites[] = array("organization_id" => $organization['Organization']['id'], "email" => $email, "flow" => "web");
//            }
//           
//            $viewVars = array('org_name' => $organization['Organization']['name'], "org_code" => $organization['Organization']['secret_code']);
//            
//            $configVars = serialize($viewVars);
//            $subject = "Invitation to join nDorse";
////            $emailvar = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => "invite");
//            $emailQueue[] = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => "invite");
//            
////            $this->Email->Create();
////            $email = $this->Email->save($emailvar);
//        
//         if (!empty($invites)) {
//            $this->Invite->saveMany($invites);
//        }
//
//        if (!empty($emailQueue)) {
//            $this->Email->saveMany($emailQueue);
//        }

        echo json_encode(array("success" => true));
        exit;
    }

    public function upgradeTrialSubscription() {
        $this->loadModel("Subscription");

        $loggedInUser = $this->Auth->User();
//                pr($this->request->data);exit();
        $upgradeUsers = $this->request->data['users'];
        $upgradeDuration = $this->request->data['duration'];
        $orgId = $this->request->data['orgId'];

        $updates = array();
        if (!empty($upgradeUsers)) {
            $updates['Subscription.pool_purchased'] = 'Subscription.pool_purchased + ' . $upgradeUsers;
        }

        if (!empty($upgradeDuration)) {
            $updates['Subscription.end_date'] = 'DATE_ADD(Subscription.end_date, INTERVAL ' . $upgradeDuration . ' MONTH)';
        }

//        $this->Subscription->updateAll(
//            array(
//                'Subscription.pool_purchased' =>'Subscription.pool_purchased + ' . $upgradeUsers, 
//                'Subscription.end_date' => 'DATE_ADD(Subscription.end_date, INTERVAL 6 MONTH) - INTERVAL 1 DAY'
//                ), 
//            array('Subscription.organization_id' => $orgId));

        $this->Subscription->updateAll($updates, array('Subscription.organization_id' => $orgId));
//        
//         $transaction = array(
//            'organization_id' => $orgId,
//            'user_id' => $loggedInUser['id'],
//            'type' => "upgrade",
//            'user_diff' => $upgradeUsers,
//            'method' => 'ndorse',
//            'amount' => 0,
//            'status' => 'settled'
//        );
//        $this->Transaction->save($transaction);

        $subscription = $this->Subscription->findByOrganizationId($orgId);

        echo json_encode(array("success" => true, "poolAvailable" => $subscription['Subscription']['pool_purchased'] + FREE_POOL_USER_COUNT));
        exit;
    }

    public function convertToPaid() {
        $this->loadModel("Subscription");
        $loggedInUser = $this->Auth->User();

        $orgId = $this->request->data['orgId'];
        $updates = array();
        $updates['pool_purchased'] = $this->request->data['usersCount'];
        $updates['amount_paid'] = $this->request->data['amount'];
        $updates['type'] = "'paid'";
        $updates['user_id'] = $loggedInUser['id'];

        $updates['start_date'] = "'" . date('Y-m-d 00:00:00', time()) . "'";

        $enddate = date('Y-m-d 23:59:59', strtotime('+1 year'));
        $enddate = date('Y-m-d 23:59:59', strtotime($enddate . '-1 day'));
        $updates['end_date'] = "'" . $enddate . "'";

        if ($this->Subscription->updateAll($updates, array('Subscription.organization_id' => $orgId))) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false));
        }
        exit();
    }

    public function generateJoinCode() {
        $joinOrgCode = $this->Common->getJoinOrgCode($this->request->data['orgId']);
        if (!empty($joinOrgCode)) {
            echo json_encode(array("success" => true, "code" => $joinOrgCode));
        } else {
            echo json_encode(array("success" => false));
        }
        exit();
    }

    //Added by Babulal prasad to update guest ndorsement status @24-may-2018
    function changeguestndorsementstatus() {
        try {

            $this->loadModel("Endorsement");
            $this->loadModel("FeedTran");
            $this->layout = 'ajax';
            $this->autoRender = false;
            $endorsementId = $this->request->data['endorsement_id'];
            $status = $this->request->data['status'];

            if ($status == 4) {//Delete 
                $this->Endorsement->id = $endorsementId;
                $this->Endorsement->delete($endorsementId);
            } else {
                $this->Endorsement->id = $endorsementId;
                $this->Endorsement->saveField('status', $status, 'false');
            }

            if ($status == 1) { //Approve then active feed to show on live feed
                $this->FeedTran->UpdateAll(
                        array("status" => 1), array("feed_id" => $endorsementId, "feed_type" => 'endorse', 'endorse_type' => 'guest')
                );
            } elseif ($status == 4) {//Deleted & hide from live feeds
                $this->FeedTran->deleteAll(array("feed_id" => $endorsementId, "feed_type" => 'endorse', 'endorse_type' => 'guest'));
            } else {//Rejected or drafted then inactive feed to hide from live feed
                $this->FeedTran->UpdateAll(
                        array("status" => 0), array("feed_id" => $endorsementId, "feed_type" => 'endorse', 'endorse_type' => 'guest')
                );
            }


            echo json_encode(array("endorsement" => "ID " . $endorsementId . " Updated"));
            exit();
        } catch (Exception $e) {
            echo json_encode(array("message" => $e));
        }
    }

    //Added by Babulal prasad to update guest ndorsement status @24-may-2018
    function changevideostatus() {
        try {
            $this->layout = 'ajax';
            $this->autoRender = false;
            $this->loadModel("OrgVideo");
            $this->loadModel("Organization");
            $orgId = $this->request->data['org_id'];
            $videoId = $this->request->data['video_id'];
            $status = $this->request->data['status'];
            $featuredOrgVideoLimit = 0;

            if ($status != 1) { // If Disable/In-active or delete the video
                $this->OrgVideo->id = $videoId;
                $this->OrgVideo->saveField('status', $status, 'false');
            } else if ($status == 1) { // If Want to re-publish/active the video
                $featuredVideoLimit = $this->Organization->find('all', array('fields' => array('featured_video_limit'), 'conditions' => array('id' => $orgId)));

                if (!empty($featuredVideoLimit)) {
                    $featuredOrgVideoLimit = $featuredVideoLimit[0]['Organization']['featured_video_limit'];
                }

                $orgActivefeaturedVideos = $this->OrgVideo->find('list', array('fields' => array('id'), 'conditions' => array('org_id' => $orgId, 'status' => 1), 'limit' => $featuredOrgVideoLimit));

                if (!empty($orgActivefeaturedVideos)) {
                    $uploadedVideos = count($orgActivefeaturedVideos);
                    if ($uploadedVideos >= $featuredOrgVideoLimit) {
                        $prvActiveVideoID = array_shift($orgActivefeaturedVideos);
                        $this->OrgVideo->clear();
                        $this->OrgVideo->id = $prvActiveVideoID;
                        $this->OrgVideo->save(array('status' => 3));
                    }
                }

                $orgVideoDetails = $this->OrgVideo->find('all', array('fields' => array('*'), 'conditions' => array('id' => $videoId)));

                if (!empty($orgVideoDetails)) {
                    $newVideoRebulish['OrgVideo']['org_id'] = $orgVideoDetails[0]['OrgVideo']['org_id'];
                    $newVideoRebulish['OrgVideo']['video_url'] = $orgVideoDetails[0]['OrgVideo']['video_url'];
                    $newVideoRebulish['OrgVideo']['thumbnail'] = $orgVideoDetails[0]['OrgVideo']['thumbnail'];
                    $newVideoRebulish['OrgVideo']['uploaded_by'] = $orgVideoDetails[0]['OrgVideo']['uploaded_by'];
                }
                $this->OrgVideo->clear();
                $this->OrgVideo->create();
                $newVideo = $this->OrgVideo->save($newVideoRebulish);
            }

            echo json_encode(array("msg" => "ID " . $videoId . " Updated", 'updated status' => $status, 'apistatus' => true));

            exit();
        } catch (Exception $e) {
            echo json_encode(array("msg" => "Unable to change the video status.", 'apistatus' => false));
            //echo json_encode(array("message" => $e));
        }
    }

    function searchallusers() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("Organization");
        $searchvalue = $this->request->data["searchvalue"];
        $loggeduser = $this->Auth->User();
        $loggedUserId = $loggeduser['id'];
//        pr($loggeduser['id']); exit;
        if ($this->Session->check('Auth.User.role') != "1" || $this->Session->check('Auth.User.role') != "2") {
            $this->Auth->logout();
            $this->redirect(array('action' => 'login'));
        } else {
            $role = $this->Auth->User('role');
            if ($role == 2) {
                $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
            }

            $this->loadModel('DefaultOrg');

            /*             * ************************************** */

            $this->DefaultOrg->unbindModel(array('belongsTo' => array('User')));
            $totalUsersCount = $this->DefaultOrg->find('all', array(
                'fields' => array('*'),
                'joins' => array(
                    array(
                        'table' => 'user_organizations',
                        'alias' => 'UserOrganization',
                        'type' => 'Left',
                        'conditions' => array('UserOrganization.organization_id = DefaultOrg.organization_id', 'UserOrganization.user_id = DefaultOrg.user_id')
                    ),
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'Left',
                        'conditions' => array('User.id = DefaultOrg.user_id')
                    )
                ),
                'conditions' => array('DefaultOrg.status' => 1, 'UserOrganization.status' => 1, 'User.status' => 1,
                    'OR' => array("concat(User.fname,' ',User.lname) LIKE '%$searchvalue%'", 'User.email' => $searchvalue, 'User.fname like' => "%" . $searchvalue . "%", 'User.lname like' => "%" . $searchvalue . "%")),
            ));
//            pr($totalUsersCount); exit;
            $totalrecords = isset($totalUsersCount[0][0]['total_records']) ? $totalUsersCount[0][0]['total_records'] : 0;
        }

        $this->set(compact('totalrecords'));
        $this->set("authUser", $loggeduser);
        $this->set("org_user_data", $totalUsersCount);
        $this->set(compact("orgdata", "orgstatus", "totalrecords"));
        echo $htmlstring = $this->render('/Elements/searchuserslisting');
        exit;
    }

    public function saveDaisyFacility() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("DaisySubcenter");
//        pr($this->request->data['facility_name']);
        $facility_name = $DaisySubcenter['DaisySubcenter']['name'] = $this->request->data['facility_name'];
        $org_id = $DaisySubcenter['DaisySubcenter']['org_id'] = $this->request->data['org_id'];

        //find duplicate entery 
        $duplicateChk = $this->DaisySubcenter->find("all", array("fields" => array("id"), "conditions" => array("org_id" => $org_id, 'name' => $facility_name, 'status' => 1)));
        if (!empty($duplicateChk)) {
            echo json_encode(array("msg" => "Subcenter name already exists!", 'status' => false,));
            exit;
        } else {
            $this->DaisySubcenter->save($DaisySubcenter);
            echo json_encode(array("msg" => "Successfully added", 'status' => true,));
            exit;
        }
    }

    public function saveDaisyNotificationUser() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("DaisyNotifyUsers");
//        pr($this->request->data);
//        exit;
        $facility_name = $DaisySubcenter['DaisyNotifyUsers']['name'] = $this->request->data['facility_name'];
        $org_id = $DaisySubcenter['DaisyNotifyUsers']['org_id'] = $this->request->data['org_id'];
        $DaisySubcenter['DaisyNotifyUsers']['email_enabled'] = $this->request->data['email_enabled'];
        $DaisySubcenter['DaisyNotifyUsers']['sms_enabled'] = $this->request->data['sms_enabled'];
        $email = $DaisySubcenter['DaisyNotifyUsers']['email'] = $this->request->data['user_email'];
        $DaisySubcenter['DaisyNotifyUsers']['mobile_no'] = $this->request->data['user_sms'];

        //find duplicate entery 
//        $duplicateChk = $this->DaisySubcenter->find("all", array("fields" => array("id"), "conditions" => array("org_id" => $org_id, 'name' => $facility_name, 'status' => 1)));
//        if (!empty($duplicateChk)) {
//            echo json_encode(array("msg" => "Subcenter name already exists!", 'status' => false,));
//            exit;
//        } else {
        $this->DaisyNotifyUsers->save($DaisySubcenter);
        echo json_encode(array("msg" => "Successfully added", 'status' => true,));
        exit;
//        }
    }

    public function updateDaisyNotificationUser() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("DaisyNotifyUsers");
//        pr($this->request->data);
//        exit;
        $facility_name = $this->request->data['facility_name'];
        $org_id = $this->request->data['org_id'];
        $email_enabled = $this->request->data['email_enabled'];
        $sms_enabled = $this->request->data['sms_enabled'];
        $email = $this->request->data['user_email'];
//        $mobile_no = $this->request->data['user_sms'];
        $mobile_no = '';
        $id = $this->request->data['id'];

        //find duplicate entery 
//        $duplicateChk = $this->DaisySubcenter->find("all", array("fields" => array("id"), "conditions" => array("org_id" => $org_id, 'name' => $facility_name, 'status' => 1)));
//        if (!empty($duplicateChk)) {
//            echo json_encode(array("msg" => "Subcenter name already exists!", 'status' => false,));
//            exit;
//        } else {
        $this->DaisyNotifyUsers->updateAll(array("name" => "'" . $facility_name . "'", 'email_enabled' => $email_enabled, 'sms_enabled' => $sms_enabled, 'email' => "'" . $email . "'"), array("id" => $id));

        echo json_encode(array("msg" => "Successfully updated", 'status' => true,));
        exit;
//        }
    }

    public function saveFeedbackNotificationUser() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("FeedbackNotifyUsers");
//        pr($this->request->data);
//        exit;
        $facility_name = $feedBackNotify['FeedbackNotifyUsers']['name'] = $this->request->data['facility_name'];
        $org_id = $feedBackNotify['FeedbackNotifyUsers']['org_id'] = $this->request->data['org_id'];
        $feedBackNotify['FeedbackNotifyUsers']['email_enabled'] = $this->request->data['email_enabled'];
        $feedBackNotify['FeedbackNotifyUsers']['sms_enabled'] = $this->request->data['sms_enabled'];
        $email = $feedBackNotify['FeedbackNotifyUsers']['email'] = $this->request->data['user_email'];
        $feedBackNotify['FeedbackNotifyUsers']['mobile_no'] = $this->request->data['user_sms'];

        //find duplicate entery 
//        $duplicateChk = $this->DaisySubcenter->find("all", array("fields" => array("id"), "conditions" => array("org_id" => $org_id, 'name' => $facility_name, 'status' => 1)));
//        if (!empty($duplicateChk)) {
//            echo json_encode(array("msg" => "Subcenter name already exists!", 'status' => false,));
//            exit;
//        } else {
        $this->FeedbackNotifyUsers->save($feedBackNotify);
        echo json_encode(array("msg" => "Successfully added", 'status' => true,));
        exit;
//        }
    }

    public function updateFeedbackNotificationUser() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("FeedbackNotifyUsers");
//        pr($this->request->data);
//        exit;
        $facility_name = $this->request->data['facility_name'];
        $org_id = $this->request->data['org_id'];
        $email_enabled = $this->request->data['email_enabled'];
        $sms_enabled = $this->request->data['sms_enabled'];
        $email = $this->request->data['user_email'];
//        $mobile_no = $this->request->data['user_sms'];
        $mobile_no = '';
        $id = $this->request->data['id'];

        //find duplicate entery 
//        $duplicateChk = $this->DaisySubcenter->find("all", array("fields" => array("id"), "conditions" => array("org_id" => $org_id, 'name' => $facility_name, 'status' => 1)));
//        if (!empty($duplicateChk)) {
//            echo json_encode(array("msg" => "Subcenter name already exists!", 'status' => false,));
//            exit;
//        } else {
        $this->FeedbackNotifyUsers->updateAll(array("name" => "'" . $facility_name . "'", 'email_enabled' => $email_enabled, 'sms_enabled' => $sms_enabled, 'email' => "'" . $email . "'"), array("id" => $id));

        echo json_encode(array("msg" => "Successfully updated", 'status' => true,));
        exit;
//        }
    }

    public function updateDaisyFacility() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("DaisySubcenter");
        $facility_name = $this->request->data['facility_name'];
        $org_id = $this->request->data['org_id'];
        $facility_id = $this->request->data['facility_id'];

        //find duplicate entery 
        $duplicateChk = $this->DaisySubcenter->find("all", array("fields" => array("id"), "conditions" => array("id !=" => $facility_id, "org_id" => $org_id, 'name' => $facility_name, 'status' => 1)));
        if (!empty($duplicateChk)) {
            echo json_encode(array("msg" => "Subcenter name already exists!", 'status' => false,));
            exit;
        } else {
            //Updating subcenter name
            $this->DaisySubcenter->UpdateAll(array("name" => "'" . $facility_name . "'"), array("id" => $facility_id, "org_id" => $org_id));
            echo json_encode(array("msg" => "Successfully updated", 'status' => true,));
            exit;
        }
    }

    public function deleteDaisyFacility() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("DaisySubcenter");
        $org_id = $this->request->data['org_id'];
        $facility_id = $this->request->data['facility_id'];
        //Updating subcenter name
        $this->DaisySubcenter->UpdateAll(array("status" => 0), array("id" => $facility_id, "org_id" => $org_id));
        echo json_encode(array("msg" => "Successfully deleted", 'status' => true,));
        exit;
    }

    public function deleteDaisyNotifier() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("DaisyNotifyUsers");

        $org_id = $this->request->data['org_id'];
        $notify_id = $this->request->data['notify_id'];

        //Updating subcenter name
        $this->DaisyNotifyUsers->UpdateAll(array("status" => '2'), array("id" => $notify_id));

        echo json_encode(array("msg" => "Successfully deleted", 'status' => true));
        exit;
    }

    public function deleteFeedbackNotifier() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("FeedbackNotifyUsers");

        $org_id = $this->request->data['org_id'];
        $notify_id = $this->request->data['notify_id'];

        //Updating subcenter name
        $this->FeedbackNotifyUsers->UpdateAll(array("status" => '2'), array("id" => $notify_id));

        echo json_encode(array("msg" => "Successfully deleted", 'status' => true));
        exit;
    }

    public function deleteCustomSticker() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("Bitmoji");

        $sticker_id = $this->request->data['sticker_id'];

        //Updating subcenter name
        $this->Bitmoji->UpdateAll(array("status" => '2'), array("id" => $sticker_id));

        echo json_encode(array("msg" => "Successfully deleted", 'status' => true));
        exit;
    }

    public function updateDaisyNotification() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $this->loadModel("Endorsement");

        $id = $this->request->data['id'];
        $message = $this->request->data['message'];
        $nomineeFName = $this->request->data['nomineeFName'];
        $nomineeLName = $this->request->data['nomineeLName'];
        $deptName = $this->request->data['deptName'];
        $userID = $this->request->data['nomineeId'];
        $customUser = $this->request->data['customUser'];
        $deptId = $this->request->data['deptId'];

        if ($deptId == '') {
            $endorsement['department_name'] = "'" . $deptName . "'";
        }
        $endorsement['message'] = "'" . $message . "'";
        $this->Endorsement->UpdateAll($endorsement, array("id" => $id, "type" => "daisy"));

        if ($customUser == 1) {
            $this->loadModel("User");
            $user['fname'] = "'" . $nomineeFName . "'";
            $user['lname'] = "'" . $nomineeLName . "'";
            $this->User->UpdateAll($user, array("id" => $userID));
        }

        echo json_encode(array("msg" => "Successfully updated", 'status' => true,));
        exit;
    }

    /**
    *This function is created by saurabh for adding loggedin user details in api_session_logs table.
    */
    // public function addApiSessionLogs()
    // {
    //     $this->layout = "ajax_new";
    //     $this->autoRender = false;
    //     $this->loadModel("ApiSessionLogs");
        
    //     $logged_in_user_role = $this->Auth->user('role');
    //     $logged_in_user_id = $this->Auth->user('id');
    //     $logged_in_user_token = $this->Auth->user('token');
    //     //print_r($logged_in_user_role); exit;
    //     //echo '-----';
    //     //print_r($this->Auth->User()); exit;
        

    //     if ($logged_in_user_role > 1) {
    //         //$token = md5(uniqid() . $logged_in_user_id . time());

    //         $apiSessionLogs = array();
    //         $apiSessionLogs['user_id'] = $logged_in_user_id;
    //         $apiSessionLogs['token'] = $logged_in_user_token; //$token;
    //         $apiSessionLogs['order'] = array("ApiSessionLogs.created DESC");
            
    //         $this->ApiSessionLogs->clear();
    //         $this->ApiSessionLogs->set(array("ApiSessionLogs" => $apiSessionLogs));
    //         $this->ApiSessionLogs->save();    

    //         echo json_encode(array("msg" => "Api session logs successfully created", 'status' => true,));
    //         exit;
    //     } 
    //     else 
    //     {
    //         echo json_encode(array("msg" => "Problem in creating logs. Please login again! ", 'status' => false,));
    //         exit;
    //     }   
    // }
}

//end of ajax controller class



    
