<?php

class ApiController extends AppController {

    public $components = array('RequestHandler', "Auth", "Common", "Image", "Session", 'Apicalls');
    var $uses = array("User", "ApiSession", "Department", "Organization", "UserOrganization", "Invite", "Email", "Entity", "OrgCoreValues",
        "OrgDepartments", "OrgJobTitles", "Endorsement", "Country", "State", "DefaultOrg", "Verification", "Subscription", "EndorseAttachment",
        "EndorseCoreValue", "LoginStatistics", 'OrgRequests', 'EndorsementLike', 'EndorseReplies', 'Badge', 'PasswordCode', "GlobalSetting",
        "Topendorser", "globalsettingFaq", "Emojis", "JoinOrgCode", "Post", "PostAttachment", "PostTrans", "PostLike", "PostComment", "FeedTran", "PostSchedule",
        "OrgJobTitle", "PostEventCount", "EndorseReply", "OrgVideo", "EndorsementComment", "OrgHashtag", "EndorseHashtag");

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow("register", "login", "logout", "getDefaultDepartments", "getDefaultJobTitles", "getOrganization", "getDefaultSkills", "getSubOrganizations", "getProfile", "saveprofile", "getPredefinedValues", "getDefaultHobbies", "isValidQRCode", "saveOrganization", "endorse", "saveEndorseAttachments", "getCountryStateList", "saveprofileorg", "getOrgoption", "saveOrgoption", "sendVerification", "joinOrganization", "searchInOrganization", "getEndorseList", "endorsedetails", "endorselike", "endorsereply", "mySearchInOrganization", "switchGroup", "getorganizationuser", "userOrgAdminAccessAction", "getjoinrequestUser", "acceptorgrequest", "endorsestats", "leaderboard", "forgotPassword", "resetPassword", "changepassword", "userOrgSearch", "getVariousOrganizationData", "termsConditions", "getTimelyUpdates", "recoverusername", "endorsementbydept", "endorsementbyday", "endorsementbycorevalues", "faq", "sendtermconditions", "getEmojis", "getBitmojis", "updateLastAppUsedTime", "renewSession", "endorsementbyjobtitles", "endorsementbyentity", "enterFeedTransData", "searchInOrganizationGuest", "guestEndorse", "getAllPendingListing", "onViewNotification", "onCancelNotification", "sendPushNotificationAndroid", "setnewpassword", "daisyEndorse", "searchInOrganizationDaisy", "ldapLogin", "getOrgShortCode", "ADFSClientLogin", "checkADFSLoginSession", "getOrgSubcenters", "getSubcenterCorevalues", "getOrgEmojis", "getPendingGuestnDorsements", "getUserFollowList");
    }

    public function sendVerification() {
        if ($this->request->is('post')) {
            $this->Auth->logout();

            if (!filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid email address. Please check."),
                    '_serialize' => array('result')
                ));
                return;
            }

            if ($this->User->email_registered($this->request->data)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "The email you entered is already registered.", 'isRegistered' => true),
                    '_serialize' => array('result')
                ));
                return;
            }

            $verificationRecord = $this->Verification->find("first", array("conditions" => array("email" => $this->request->data['email'])));

            if (!empty($verificationRecord)) {
                $this->request->data['id'] = $verificationRecord['Verification']['id'];
                $this->request->data['email_sent'] = 0;
                $verificationCode = $verificationRecord['Verification']['verification_code'];
            } else {
                $verificationCode = $this->request->data['verification_code'] = substr(md5(md5(uniqid() . $this->request->data['email'] . time())), 0, 5);
            }

            $this->Verification->set($this->request->data);
            if ($this->Verification->validates()) {
                if ($this->Verification->save()) {
//$subject = "nDorse - Verify email";
//$template = "verification";
//$viewVars = array("verification_code" => $verificationCode);
//$this->Common->sendEmail($this->request->data['email'], $subject, $template, $viewVars);
//                    exec("nohup wget " . Router::url('/', true) . "/cron/verificationEmails > /dev/null 2>&1 &");
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    exec("wget -bqO- " . $rootUrl . "/cron/verificationEmails &> /dev/null");
//                    exec( "php ".WWW_ROOT."cron_scheduler.php /cron/verificationEmails/ > /dev/null &");
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Verification email sent successfully."),
                        '_serialize' => array('result')
                    ));
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Could not send verification email now. Please try again."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $errors = $this->Verification->validationErrors;
                $errorsArray = array();

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Errors!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function register() {
        if ($this->request->is('post')) {
            $this->Auth->logout();

            $sourceArray = array("fb" => "facebook", "gplus" => "google plus", "lin" => "linkedin");

            $userExist = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['email'])));

            if (!isset($this->request->data['source']) || empty($this->request->data['source'])) {
                $this->request->data['source'] = "email";
            }


//            if ($this->request->data['source'] == 'email' && !empty($userExist)) {
            if (!empty($userExist)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "This email is already registered.", 'isRegistered' => true),
                    '_serialize' => array('result')
                ));
                return;
            }
//            elseif (!empty($userExist)) {
//                //print_r($userExist);
//                //echo $this->request->data['source'] . '_id';
//                //echo $this->request->data['source_id'];
//                //echo "<hr>";
//                //echo $userExist["User"][$this->request->data['source'] . '_id'];
//                //exit; 
//                if ($userExist["User"][$this->request->data['source'] . '_id'] == $this->request->data['source_id']) {
//                    $this->set(array(
//                        'result' => array("status" => false
//                            , "msg" => "This email is already registered with " . $sourceArray[$this->request->data['source']] . ".", 'isRegistered' => true),
//                        '_serialize' => array('result')
//                    ));
//                    return;
//                }
//            }



            $this->request->data['User'] = $this->request->data;

            if (!empty($userExist)) {
                $this->request->data['User']['id'] = $userExist['User']['id'];
            }

            if ($this->request->data['source'] == "email") {
                $verificationRecord = $this->Verification->find("first", array("conditions" => array("email" => $this->request->data['email'], "verification_code" => $this->request->data['verification_code'])));
                if (!empty($verificationRecord)) {
                    $this->request->data['User']['password'] = $verificationRecord['Verification']['password'];
                    $this->request->data['User']['password_hashed'] = true;
                    $this->request->data['User']['terms_accept'] = 1;
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Verification failed."),
                        '_serialize' => array('result')
                    ));
                    return;
                }
            } else {
                unset($this->User->validate['image']);
                if (empty($this->request->data['source_id'])) {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Third party ID is not provided."),
                        '_serialize' => array('result')
                    ));
                    return;
                } else {
                    $this->request->data['User'][$this->request->data['source'] . '_id'] = $this->request->data['source_id'];
                }
            }

            $roleList = $this->Common->setSessionRoles();

            $this->request->data['User']['role'] = array_search('endorser', $roleList);
            $this->request->data['User']['secret_code'] = $this->getSecretCode("user");
            $this->request->data['User']['username'] = $this->request->data['email'];
            $this->request->data['User']['last_app_used'] = "NOW()";

            $this->User->setValidation('register');



            $this->User->set($this->request->data);
            if ($this->User->validates()) {
                if ($this->User->save(null, false)) {
                    $this->request->data['User']['id'] = $this->User->id;
                    if ($this->Auth->login($this->request->data['User'])) {



                        $loggedinUserId = $this->User->id;
                        $token = $this->generateToken($loggedinUserId);
                        $this->Session->write('Auth.User.token', $token);

                        $returnData = $this->Auth->user();
//																								$viewVars = array( "username" => $this->request->data['email']);
//																								$configVars = serialize($viewVars);
//                       
//                        $subject = "nDorse sign up successful";
//																								$to = $this->request->data['email'];
//                        //$this->Common->sendEmail($this->request->data['email'], $subject, "register", $configVars);
//																								$email = array("to" => $to, "subject" => $subject, "config_vars" => $configVars, "template" => "register");
//																								$this->Email->save($email);

                        $returnData = $this->Auth->user();
                        $returnData['role'] = $roleList[$returnData['role']];

                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Verification successful.", 'data' => $returnData),
                            '_serialize' => array('result')
                        ));
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "Verification successful but unable to login. Please try to login again or contact support@ndorse.net if problem persists."),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "There is some problem in verification. Please try again later or contact support@ndorse.net if problem persists."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $errors = $this->User->validationErrors;
                $errorsArray = array();

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Error!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function isValidQRCode() {
        $organization = $this->Organization->findBySecretCode($this->request->data['qr_code']);
        if (empty($organization)) {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Invalid QR Code"),
                '_serialize' => array('result')
            ));
        } else {
            $invite = $this->Invite->find("first", array("conditions" => array("email" => $this->request->data['email'], 'organization_id' => $organization['Organization']['id'])));
            if (empty($invite)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You are not invited to join organization having " . $this->request->data['qr_code'] . " QR code. Please submit request to join organization."),
                    '_serialize' => array('result')
                ));
                function_exists('curl_version');

                return;
            }
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Valid QR Code", "data" => array("organization_id" => $organization['Organization']['id'])),
                '_serialize' => array('result')
            ));
        }
    }

    public function login() {
//        echo "TEST";
//        pr($this->request->data); 
//        exit;

        if ($this->request->is('post')) {
            $this->request->data['User'] = $this->request->data;
//echo "Test"; exit;
            $this->request->data['source'] = isset($this->request->data['source']) && !empty($this->request->data['source']) ? $this->request->data['source'] : "email";

            $userData = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['email'])));
//            pr($userData); exit;
            if (empty($userData)) {
                if ($this->request->data['source'] != 'email') {
                    $this->register();
                    return;
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "The email you entered is not registered.", 'isRegistered' => false),
                        '_serialize' => array('result')
                    ));
                    return;
                }
            }

            $roleList = $this->Common->setSessionRoles();
            if ($roleList[$userData['User']['role']] == "super_admin") {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Super Admin login not permitted in nDorse App only Super Admin site."),
                    '_serialize' => array('result')
                ));
                return;
            }

//            if ((int) $userData['User']['status'] != 1) {
//                $this->set(array(
//                    'result' => array("status" => false
//                        , "msg" => "Your account is inactive. Please contact administrator."),
//                    '_serialize' => array('result')
//                ));
//                return;
//            }
            $this->User->setValidation('login');
            $this->User->set($this->request->data);

            if ($this->User->validates()) {
                if ($this->request->data['source'] == "email") {
                    $logginSuccess = $this->Auth->login();
//                    echo 'logginSuccess';
//                    pr($logginSuccess); exit;
                } else {

//$userData = $this->User->find("first", array("conditions" => array("email" => $this->request->data['email'], "source" => $this->request->data['source'], "source_id" => $this->request->data['source_id'])));
//Update profile if email exists and thirdparty account is not attached
                    if (empty($userData['User'][$this->request->data['source'] . "_id"])) {
                        $this->User->id = $userData['User']['id'];
                        $saved = $this->User->saveField($this->request->data['source'] . "_id", $this->request->data['source_id']);
//$this->User->saveField("source", $this->request->data['source']);

                        if ($saved) {
                            $userData['User'][$this->request->data['source'] . "_id"] = $this->request->data['source_id'];
                        } else {
                            $this->set(array(
                                'result' => array("status" => false
                                    , "msg" => "The third party account you are using cannot be associated with your existing account. Please try  again."),
                                '_serialize' => array('result')
                            ));
                            return;
                        }
                    }



                    if (!empty($userData['User'][$this->request->data['source'] . "_id"])) {
                        $logginSuccess = $this->Auth->login($userData['User']);
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "The third party account you are using is not configured correctly."),
                            '_serialize' => array('result')
                        ));
                        return;
                    }
                }

//                pr($logginSuccess);exit;
                if ($logginSuccess) {


//if ((int) $this->Auth->user("status")  == 1) {

                    $loggedinUserId = $this->Auth->user('id');
//                    pr($loggedinUserId);exit;
                    $token = $this->generateToken($loggedinUserId);
                    $this->Session->write('Auth.User.token', $token);
                    $params = array();
                    $params['fields'] = "*";
                    $params['joins'] = array(
                        array(
                            'table' => 'user_organizations',
                            'alias' => 'UserOrganization',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'UserOrganization.user_id = ' . $loggedinUserId,
                                'UserOrganization.organization_id = DefaultOrg.organization_id'
                            )
                        )
                    );

                    $params['conditions'] = array("DefaultOrg.user_id" => $loggedinUserId);
                    $params['order'] = 'UserOrganization.id desc';

                    $defaultOrganization = $this->DefaultOrg->find("first", $params);

                    $statusConfig = Configure::read("statusConfig");



                    if (!empty($defaultOrganization) && !empty($defaultOrganization['UserOrganization']) && isset($defaultOrganization['UserOrganization']['id'])) {
                        $orgUpdates = array();
//if ($defaultOrganization['UserOrganization']['status'] == $statusConfig['active'] && $defaultOrganization['Organization']['status'] == $statusConfig['active']) {
                        $currentOrg = $defaultOrganization['Organization'];

                        $currentOrg['joined'] = $defaultOrganization['UserOrganization']['joined'];

                        if ($defaultOrganization['UserOrganization']['entity_id'] > 0) {
// $department= $this->getOrgValues($org_id, "OrgDepartments",true,array($endorserd_id));
                            $entity = $this->getOrgValues($currentOrg["id"], "Entity", true, array($defaultOrganization['UserOrganization']['entity_id']));
                            if (!empty($entity)) {
                                $currentOrg['entity'] = $entity[0]["name"];
                            } else {
                                $currentOrg['entity'] = "";
                            }
                        } else {
                            $currentOrg['entity'] = "";
                        }

                        if ($defaultOrganization['UserOrganization']['department_id'] > 0) {
// $department= $this->getOrgValues($org_id, "OrgDepartments",true,array($endorserd_id));
                            $department = $this->getOrgValues($currentOrg["id"], "OrgDepartments", true, array($defaultOrganization['UserOrganization']['department_id']));
// $department = $defaultOrganization['UserOrganization']['department_id'];
                            if (!empty($department)) {
                                $currentOrg['department'] = $department[0]["name"];
                            } else {
                                $currentOrg['department'] = "";
                            }
                        } else {
                            $currentOrg['department'] = "";
                        }
                        if ($defaultOrganization['UserOrganization']['job_title_id'] > 0) {
// $department= $this->getOrgValues($org_id, "OrgDepartments",true,array($endorserd_id));
                            $jobtitle = $this->getOrgValues($currentOrg["id"], "OrgJobTitles", 1, array($defaultOrganization['UserOrganization']['job_title_id']));

                            if (!empty($jobtitle)) {
                                $currentOrg['job_title'] = $jobtitle[0]["name"];
                            } else {
                                $currentOrg['job_title'] = "";
                            }
                        } else {
                            $currentOrg['job_title'] = "";
                        }
                        if ($defaultOrganization['UserOrganization']['job_title_id'] > 0) {
// $department= $this->getOrgValues($org_id, "OrgDepartments",true,array($endorserd_id));
                            $jobtitle = $this->getOrgValues($currentOrg["id"], "OrgJobTitles", 1, array($defaultOrganization['UserOrganization']['job_title_id']));

                            if (!empty($jobtitle)) {
                                $currentOrg['job_title'] = $jobtitle[0]["name"];
                            } else {
                                $currentOrg['job_title'] = "";
                            }
                        } else {
                            $currentOrg['job_title'] = "";
                        }


                        $currentOrg['org_role'] = $roleList[$defaultOrganization['UserOrganization']['user_role']];
                        if (!isset($defaultOrganization['UserOrganization']['subcenter_id']) || $defaultOrganization['UserOrganization']['subcenter_id'] == 0 || $defaultOrganization['UserOrganization']['subcenter_id'] == null) {
                            $currentOrg['subcenter_id'] = '';
                        } else {
                            $currentOrg['subcenter_id'] = $defaultOrganization['UserOrganization']['subcenter_id'];
                        }


                        $currentOrg['status'] = array_search($defaultOrganization['UserOrganization']["status"], $statusConfig);

                        if ($currentOrg["image"] != "") {
                            $rootUrl = Router::url('/', true);
                            $rootUrl = str_replace("http", "https", $rootUrl);
                            $currentOrg["image"] = $rootUrl . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $currentOrg["image"];
                        }




//$orgUpdates = array("is_current_org_active" => 1);
//} else {
//Check inactive/active/eval status of default user organization
//$isCurrentOrgActive = 1;
                        $msg = "";
                        $userStatus = array_search($defaultOrganization['UserOrganization']["status"], $statusConfig);
                        $orgStatus = array_search($defaultOrganization['Organization']["status"], $statusConfig);
//Get current user org status and total org count
//$this->UserOrganization->unbindModel(array('belongsTo' => array('User')));
//$totalUserOrgsActive = $this->UserOrganization->find("count", array("conditions" => array("user_id" => $loggedinUserId, "UserOrganization.status" => $statusConfig['active'], "Organization.status" => $statusConfig['active'])));
//
                        //$params = array();
//$params['conditions'] = array("user_id" => $loggedinUserId);
//$params['conditions']['OR'] = array("UserOrganization.status" => $statusConfig['inactive'], "UserOrganization.status" => $statusConfig['eval'], "Organization.status" => $statusConfig['inactive']);
//$totalUserOrgsInactive = $this->UserOrganization->find("count", $params);
//if(!empty($defaultOrganization)) {
                        if ($defaultOrganization['Organization']['status'] != $statusConfig['active']) {
//$isCurrentOrgActive = 0;

                            if ($defaultOrganization['Organization']['status'] == $statusConfig['inactive']) {
                                $msg = "Default Organization inactivated.";
                            } else {
                                $msg = "Default Organization deleted!";
                            }
                        } else if ($defaultOrganization['UserOrganization']['status'] != $statusConfig['active']) {
//$isCurrentOrgActive  = 0;
                            if ($defaultOrganization['UserOrganization']['status'] == $statusConfig['inactive'] || $defaultOrganization['UserOrganization']['status'] == $statusConfig['eval']) {
                                $msg = "nDorse access inactivated for default Organization. Contact Organization Admin.";
                            } else if ($defaultOrganization['UserOrganization']['status'] == $statusConfig['deleted']) {
                                $msg = "You have been deleted from your default nDorse Organization. Contact Organization Admin.";
                            }
                        } else {
                            $this->Session->write('Auth.User.current_org', $currentOrg);
                        }


//$orgUpdates = array("is_current_org_active" => $isCurrentOrgActive, "total_user_orgs_active" => $totalUserOrgsActive, "total_user_orgs_inactive" => $totalUserOrgsInactive, "msg" => $msg, 'user_status' => $userStatus, 'org_status' => $orgStatus);
                        $orgUpdates = array("msg" => $msg, 'user_status' => $userStatus, 'org_status' => $orgStatus);
//} 
//}
                        $loggedInUser = $returnData = $this->Auth->user();
                    } else {
                        $loggedInUser = $returnData = $this->Auth->user();
//                        
//                        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));
//                        $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("user_id" => $loggedinUserId, "status" => $statusConfig['active']), "order" => "created ASC"));
//                        
//                        $loggedInUser['accessOrgId'] = $userOrganization['UserOrganization']['organization_id'];
                    }


//                    pr($currentOrg); exit;
                    $returnData['current_org'] = $currentOrg;
                    if (isset($orgUpdates)) {
                        $returnData['org_updates'] = $orgUpdates;
                        $returnData['current_org'] = $currentOrg;
                    }

                    if ($returnData["image"] != "") {
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $returnData["image"] = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $returnData["image"];
                    }
                    if (strtotime($returnData["dob"]) > 0) {
                        $returnData["dob"] = date("m/d/Y", strtotime($returnData["dob"]));
                    } else {
                        $returnData["dob"] = "";
                    }
//
                    $source = $this->request->data['source'];
                    if ($source == "email") {
//print_r($returnData);exit;
                        unset($returnData["source_id"]);
                        unset($returnData["fb_id"]);
                        unset($returnData["gplus_id"]);
                        unset($returnData["lin_id"]);
                    } elseif ($source == "fb") {
                        unset($returnData["source_id"]);
                        unset($returnData["gplus_id"]);
                        unset($returnData["lin_id"]);
                    } elseif ($source == "gplus") {
                        unset($returnData["source_id"]);
                        unset($returnData["fb_id"]);
                        unset($returnData["lin_id"]);
                    } elseif ($source == "lin") {
                        unset($returnData["source_id"]);
                        unset($returnData["fb_id"]);
                        unset($returnData["gplus_id"]);
                    }

//

                    if (empty($returnData['fname']) || empty($returnData['lname'])) {
                        $returnData['profile_updated'] = false;
                    } else {
                        $returnData['profile_updated'] = true;
                    }
//                    pr($returnData); exit;
                    if (empty($returnData['fname']) || empty($returnData['lname']) || empty($returnData['mobile']) || empty($returnData['country']) || empty($returnData['street']) || empty($returnData['zip']) || empty($returnData['state']) || empty($returnData['city'])) {
                        $returnData['profile_completed'] = false;
                    } else {
                        $returnData['profile_completed'] = true;
                    }

                    $updated = $this->User->updateAll(array("last_app_used" => "NOW()"), array("id" => $loggedInUser['id']));

//Get pending request organizations
                    $pendingRequests = $this->OrgRequests->find("all", array("conditions" => array("user_id" => $loggedInUser['id'], "status" => 0)));
                    $pendingRequestOrgs = array();
                    foreach ($pendingRequests as $pendingRequest) {
                        $pendingRequestOrgs[] = $pendingRequest['OrgRequests']['organization_id'];
                    }

                    $this->Session->write('Auth.User.pending_requests', $pendingRequestOrgs);

                    $returnData['pending_requests'] = $pendingRequestOrgs;




//                    pr($returnData); exit;
//                    $a = $this->Auth->user();
//                    $returnData['auth_user'] = $a;

                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "", "data" => $returnData),
                        '_serialize' => array('result')
                    ));
//} else {
//    $this->Auth->logout();
//    $this->set(array(
//        'result' => array("status" => false
//            , "msg" => "Your account is not active."),
//        '_serialize' => array('result')
//    ));
//}
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "The password you entered is incorrect."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $errors = $this->User->validationErrors;

                $errorsArray = array();

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Error!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
            }
        } else {
            echo "not post";
        }
    }

    public function logout() {
        $userinfo = $this->getuserData($this->request->data['token'], true);
        $authority_name = '';
        if (isset($this->request->data['authority_name']) && $this->request->data['authority_name']) {
            $authority_name = $this->request->data['authority_name'];
        }

        $userSource = '';
        if (!empty($userinfo)) {
            $userSource = $userinfo['users']['source'];
        }
        $userID = $this->Auth->user('id');
        $adfs_link = '';


        if (isset($userSource) && $userSource == 'ADFS') {
            if ($authority_name == 'lcmch-sp') {
                $adfs_link = 'https://sso.ndorse.net/simplesaml/module.php/core/authenticate.php?as=lcmch-sp&logout';
            } else {
                $adfs_link = 'https://sso.ndorse.net/simplesaml/module.php/core/authenticate.php?as=ndorse-sp&logout';
            }
        }

        $this->logoutSystem($this->Auth->user('id'));

        $this->set(array(
            'result' => array("status" => true, 'adfs_url' => $adfs_link
                , "msg" => ""),
            '_serialize' => array('result')
        ));

//        if (isset($this->request->data['token'])) {
//            
//            $userinfo = $this->getuserData($this->request->data['token'], true);
//            $userSource = '';
//            
//            
//            if (!empty($userinfo)) {
//                $userID = $userinfo['users']['id'];
//                $userSource = $userinfo['users']['source'];
//            } else {
//                $userID = $this->Auth->user('id');
//                $userSource = $this->rAuth->user['source'];
//            }
//            $adfs_link = '';
////            echo $userID;
////            echo $userSource;
////            pr($userinfo); exit;
//            
//            if (isset($userSource) && $userSource == 'ADFS') {
//                $adfs_link = 'https://sso.ndorse.net/simplesaml/module.php/core/authenticate.php?as=ndorse-sp&logout';
//            }
//            
//            $this->logoutSystemSelf($userID, $this->request->data['token']);
//            
//            $this->set(array(
//                'result' => array("status" => true, 'adfs_url' => $adfs_link
//                    , "msg" => "Logout successfully"),
//                '_serialize' => array('result')
//            ));
//        } else {
//            $this->set(array(
//                'result' => array("status" => false
//                    , "msg" => "Token is missing in request"),
//                '_serialize' => array('result')
//            ));
//        }
    }

    public function updateApisession($data) {
        $this->ApiSession->saveField('status', $data['status']);
    }

    public function getDefaultDepartments() {
        $departments = $this->Common->getDefaultDepartments(true, false, array("name"));
        $this->set(array(
            'result' => array("status" => true
                , "msg" => "Predefined departments", 'data' => $departments),
            '_serialize' => array('result')
        ));
    }

    public function getDefaultHobbies() {
        $hobbies = $this->Common->getDefaultHobbies(true, false, array("name"));
        $this->set(array(
            'result' => array("status" => true
                , "msg" => "Predefined hobbies", 'data' => $hobbies),
            '_serialize' => array('result')
        ));
    }

    public function getDefaultJobTitles() {
        $jobTitles = $this->Common->getDefaultJobTitles(true, false, array("title"));
        $this->set(array(
            'result' => array("status" => true
                , "msg" => "Predefined job titles", 'data' => $jobTitles),
            '_serialize' => array('result')
        ));
    }

    public function getDefaultSkills() {
        $skills = $this->Common->getDefaultSkills(true, false, array("name"));
        $this->set(array(
            'result' => array("status" => true
                , "msg" => "Predefined skills", 'data' => $skills),
            '_serialize' => array('result')
        ));
    }

    public function getDefaultindustries() {

        $industries = $this->Common->getDefaultIndustries(true, true, array("name", "id"));

        $this->set(array(
            'result' => array("status" => true
                , "msg" => "Predefined industries", 'data' => $industries),
            '_serialize' => array('result')
        ));
    }

    public function getPredefinedValues() {
        if (isset($this->request->query['type'])) {
            $requirements = explode(",", $this->request->query['type']);
            $returnData = array();
            foreach ($requirements as $type) {
                $type = trim($type);

                switch ($type) {
                    case "departments" :
                        $departments = $this->Common->getDefaultDepartments(true, false, array("name"));
                        $returnData['departments'] = $departments;
                        break;

                    case "job_titles" :
                        $jobTitles = $this->Common->getDefaultJobTitles(true, false, array("title"));
                        $returnData['job_titles'] = $jobTitles;
                        break;

                    case "skills" :
                        $skills = $this->Common->getDefaultSkills(true, false, array("name"));
                        $returnData['skills'] = $skills;
                        break;

                    case "hobbies" :
                        $hobbies = $this->Common->getDefaultHobbies(true, false, array("name"));
                        $returnData['hobbies'] = $hobbies;
                        break;

                    case "core_values" :
                        $coreValues = $this->Common->getDefaultCoreValues(true, false, array("name"));

                        $returnData['core_values'] = $coreValues["normal"];
                        $returnData['selected'] = $coreValues["selected"];
                        break;
                    case "countries" :
                        $countryValues = $this->getCountryStateList();
                        $returnData['default_country'] = "United States";
                        $returnData['country'] = $countryValues;
                        break;
                    case "degrees" :
                        $degreeValues = $this->Common->getDefaultDegrees(true, false, array("name"));
                        $returnData['degree'] = $degreeValues;
                        break;
                }
            }

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Predefined values", 'data' => $returnData),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "type is missing."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getSubOrganizations() {
        if (isset($this->request->query['oid'])) {
            $org_id = $this->request->query['oid'];
            $subOrganizations = $this->Common->getSubOrganizations($org_id, true, false, array("id", "name"));
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Suborganizations list", 'data' => $subOrganizations),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Organization ID is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function createOrganization() {
        $statusConfig = Configure::read("statusConfig");
        if ($this->request->is('post')) {
//Save default values
            $this->request->data['secret_code'] = $this->getSecretCode("organization");
            $this->request->data['admin_id'] = $this->Auth->user('id');
            if (isset($this->request->data['status']) && $this->request->data['status'] != "") {
                $this->request->data['status'] = $statusConfig[$this->request->data['status']];
            }

            $this->Organization->set($this->request->data);
            if ($this->Organization->validates()) {
                if (isset($this->request->data['image']) && $this->request->data['image'] != "") {
                    $imageExtension = $this->Organization->data['Organization']['file_extension'];
                }

                if ($this->Organization->save($this->request->data)) {

//upload image
                    if (isset($this->request->data['image']) && $this->request->data['image'] != "") {
                        $uploadPath = ORG_IMAGE_DIR;

                        $imageData = $this->request->data['image'];
                        $imageName = $this->Organization->id . "_" . time() . "." . $imageExtension;
                        if ($this->Common->uploadApiImage($uploadPath, $imageName, $imageData)) {
                            $this->Organization->saveField('image', $imageName);
                        }
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $imageUrl = $rootUrl . ORG_IMAGE_DIR . $imageName;

                        $this->request->data['image'] = $imageUrl;
                    }
                    $this->request->data['id'] = $this->Organization->id;
//print_r($this->request->data['core_values']);
                    $orgCoreValues = json_decode($this->request->data['core_values']);

//print_r($orgCoreValues);exit;
                    $orgCoreValues1 = array();

                    foreach ($orgCoreValues as $key => $coreValue) {
                        if (trim($coreValue->name) != "") {
                            $orgCoreValues1[$key]["organization_id"] = $this->Organization->id;
                            $orgCoreValues1[$key]["name"] = $coreValue->name;
                            $orgCoreValues1[$key]["color_code"] = $coreValue->color_code;
                            if (isset($coreValue->from_master)) {
                                $orgCoreValues1[$key]["from_master"] = $coreValue->from_master;
                            }
                        }
                    }

                    $this->OrgCoreValues->saveMany($orgCoreValues1);

//

                    if (isset($this->request->data["department"]) && $this->request->data["department"] != "") {
//$orgDeptValues = json_decode($this->request->data["department"]);
//
                        //$orgDeptValues1 = array();
//foreach ($orgDeptValues as $key => $DeptValues) {
//
                        //    $orgDeptValues1[$key]["organization_id"] = $this->Organization->id;
//    $orgDeptValues1[$key]["name"] = $DeptValues->name;
//    $orgDeptValues1[$key]["from_master"] = $DeptValues->from_master;
//}

                        $orgDeptValues = explode(",", $this->request->data["department"]);

                        $orgDeptValues1 = array();
                        foreach ($orgDeptValues as $key => $DeptValues) {

                            $orgDeptValues1[$key]["organization_id"] = $this->Organization->id;
                            $orgDeptValues1[$key]["name"] = $DeptValues;
// $orgDeptValues1[$key]["from_master"] = $DeptValues->from_master;
                        }

                        $this->OrgDepartments->saveMany($orgDeptValues1);
                    }
                    if (isset($this->request->data["entity"]) && $this->request->data["entity"] != "") {
                        $orgEntityValues = explode(",", $this->request->data["entity"]);
                        $orgEntityValues1 = array();
                        foreach ($orgEntityValues as $key => $EntityValues) {
                            $orgEntityValues1[$key]["organization_id"] = $this->Organization->id;
                            $orgEntityValues1[$key]["name"] = $EntityValues;
                        }
                        $this->Entity->saveMany($orgEntityValues1);
                    }
                    if (isset($this->request->data["job_title"]) && $this->request->data["job_title"] != "") {
                        $orgJobTitileValues = explode(",", $this->request->data["job_title"]);
                        $orgJobTitileValues1 = array();
                        foreach ($orgJobTitileValues as $key => $TitleValues) {
                            $orgJobTitileValues1[$key]["organization_id"] = $this->Organization->id;
                            $orgJobTitileValues1[$key]["title"] = $TitleValues;
//$orgJobTitileValues1[$key]["from_master"] = $TitleValues->from_master;
                        }
                        $this->OrgJobTitles->saveMany($orgJobTitileValues1);
                    }
                    $new_userorganization = array(
                        "user_id" => $this->Auth->user('id'),
                        "organization_id" => $this->Organization->id,
                        "user_role" => 2,
                        "pool_type" => 'free',
                        "flow" => "app_invite",
                        "joined" => 1,
                        "status" => 1
                    );
                    $this->UserOrganization->save($new_userorganization);
                    $org_id = $this->Organization->id;
                    $organizationarray = array();
                    $organizationarray["org_id"] = $this->Organization->id;
                    $organizationarray["user_role"] = "Admin";

                    $array = array();
                    $array['fields'] = array('*');
                    $array['conditions'] = array('id' => $this->Organization->id);
                    $orgArray = $organization = $this->Organization->find("first", $array);
                    $orgArray["Organization"]["created"] = strtotime($orgArray["Organization"]["created"]);
                    unset($orgArray["Organization"]["updated"]);
                    $orgArray["Organization"]["status"] = array_search($orgArray["Organization"]["status"], $statusConfig); // $statusConfig[$orgArray["Organization"]["status"]];

                    if ($orgArray["Organization"]["image"] != "") {
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $orgArray["Organization"]["image"] = $rootUrl . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $orgArray["Organization"]["image"];
                    }
                    $coreinfo = $this->OrgCoreValues->find("all", array(
                        'conditions' => array('OrgCoreValues.organization_id' => $this->Organization->id, 'OrgCoreValues.status' => 1),
                        'fields' => array('OrgCoreValues.id,OrgCoreValues.from_master,OrgCoreValues.name')
                    ));


                    $cinfo = array();
                    $total_value = 0;
                    foreach ($coreinfo as $cval) {

                        $cinfo[] = array("id" => $cval["OrgCoreValues"]["id"], "name" => strtolower($cval["OrgCoreValues"]["name"]));
                    }

                    $orgArray["Organization"]["core_values"] = $cinfo;
                    $orgArray["token"] = $this->request->data["token"];
                    $orgArray["Organization"]["departments"] = $this->getOrgValues($org_id, "OrgDepartments");
                    $orgArray["Organization"]["entities"] = $this->getOrgValues($org_id, "Entity");
// $orgArray["Organization"]["entity"] = $this->getOrgValues($org_id, "Entity");
                    $orgArray["Organization"]["job_titles"] = $this->getOrgValues($org_id, "OrgJobTitles");
                    $orgArray["Organization"]["org_role"] = "admin";
                    $orgArray["Organization"]["joined"] = "1";

                    if ($this->Auth->user('role') > 2 || $this->Auth->user('role') > 2) {
                        $this->User->id = $this->Auth->user('id');
//$this->user->role = 2;
                        $this->User->saveField('role', 2);
                    }
//
                    $params = array();
                    $params['fields'] = "*";
                    $params['conditions'] = array("DefaultOrg.user_id" => $this->Auth->user('id'));
                    $defaultOrganization = $this->DefaultOrg->find("first", $params);

                    if (empty($defaultOrganization)) {
                        $roleList = $this->Common->setSessionRoles();

                        $currentOrg = $organization;
                        $currentOrg['org_role'] = 'admin';
                        $currentOrg['joined'] = "1";
                        $this->Session->write('Auth.User.current_org', $currentOrg);
                        $defaultOrg = array("organization_id" => $this->Organization->id, "user_id" => $this->Auth->user('id'));
                        $this->DefaultOrg->save($defaultOrg);
                    }



// send email
                    $emailQueue = array();
                    $subject = "nDorse notification -- New Organization Created Successfully";
                    $viewVars = array("org_name" => $orgArray["Organization"]["name"], "fname" => $this->Auth->user('fname'));

                    /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode($this->Auth->user('id'));
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                    $viewVars["pathToRender"] = $pathToRender;
                    /*                     * * */

                    $configVars = serialize($viewVars);
                    $emailQueue[] = array("to" => $this->Auth->user('email'), "subject" => $subject, "config_vars" => $configVars, "template" => "create_org");
                    $this->Email->saveMany($emailQueue);



//
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Organization created successfully!", 'data' => $orgArray),
                        '_serialize' => array('result')
                    ));
                }
            } else {

                $errors = $this->Organization->validationErrors;
                $errorsArray = array();

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Error!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function invite() {
        if ($this->request->is('post')) {
// @TODO : Remove invalid email ids
            $this->request->data['emailIds'] = str_replace(" ", "", $this->request->data['emailIds']);
            $emailIds = explode(",", $this->request->data['emailIds']);
            $loggedInUser = $this->Auth->user();
            $statusConfig = Configure::read("statusConfig");

            $current_org = $this->Auth->user("current_org");
            $roleList = $this->Common->setSessionRoles();

            if (empty($current_org)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You have not joined any Organization. Please join some Organization."),
                    '_serialize' => array('result')
                ));

                return;
            } else if ($current_org['org_role'] == 'endorser') {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You are not authorized to Invite Users for " . $current_org['name']),
                    '_serialize' => array('result')
                ));

                return;
            }

            $params['fields'] = array("*");
            $params['conditions'] = array("User.email" => $emailIds, "UserOrganization.organization_id" => $loggedInUser['current_org']['id'], "UserOrganization.status != " => $statusConfig['deleted']);
            $joinedRecords = $this->UserOrganization->find("all", $params);

            $joinedMails = array();
            $joinedMailsList = "";

            foreach ($joinedRecords as $joined) {
                $joinedMails[] = $joined['User']['email'];
                $joinedMailsList .= $joined['User']['email'] . ", ";
            }


            $invitedRecords = $this->Invite->find("all", array("conditions" => array("email" => $emailIds, "organization_id" => $loggedInUser['current_org']['id'])));

            $invitedMails = array();
            foreach ($invitedRecords as $invited) {
                $invitedMails[] = $invited['Invite']['email'];
            }

// Save invites and emails to user
            $invites = $emailQueue = array();
            $viewVars = array("org_name" => $current_org['name'], "org_code" => $current_org['secret_code']);

            $configVars = serialize($viewVars);
            $subject = "Invitation to join nDorse";
            foreach ($emailIds as $email) {
                if (!in_array($email, $joinedMails)) {
                    if (!in_array($email, $invitedMails)) {
                        $invites[] = array("organization_id" => $current_org['id'], "email" => $email, "flow" => "app");
                    }
                    $emailQueue[] = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => "invite");
                }
            }

//Email to admin
            /* $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
              $orgAdmins = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $loggedInUser['current_org']['id'], "user_role" => 2)));

              $invitedBy = array("id" => $loggedInUser['id'], "fname" => $loggedInUser['fname'], "lname" => $loggedInUser['lname']);
              $invitedUsers = $emailIds;
              if (count($invitedUsers) == 1) {
              $subject = "nDorse Notification -- A user invited by administrator";
              } else if (count($invitedUsers) > 1) {
              $subject = "nDorse Notification -- Users invited by administrator";
              }

              foreach ($orgAdmins as $adminDetails) {
              $admin = array("id" => $adminDetails['User']['id'], "first_name" => $adminDetails['User']['fname']);
              $viewVars = array("org_name" => $current_org['name'], "admin" => $admin, 'invited_by' => $invitedBy, "invited_users" => $invitedUsers);
              $configVars = serialize($viewVars);
              $emailQueue[] = array("to" => $adminDetails['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "invite_admin");
              } */

//            $adminDetails = $this->User->findById($current_org['admin_id']);
            if (!empty($invites)) {
                $this->Invite->saveMany($invites);
            }

            if (!empty($emailQueue)) {
                $this->Email->saveMany($emailQueue);
            }

            if (!empty($invitedMails)) {
                $this->Invite->updateAll(array("invite_count" => "invite_count+1"), array("email" => $invitedMails));
            }

            if (empty($joinedMails)) {
                $msg = "Invitation(s) successfully sent!";
            } else {
                $joinedMailsList = rtrim($joinedMailsList, ", ");
                if (count($joinedRecords) == 1) {
                    $msg = $joinedMailsList . " has already joined this Organization.";
                } else {
                    $msg = $joinedMailsList . " have already joined this Organization.";
                }

                if (!empty($invites)) {
                    $msg .= "\n Invitation(s) successfully sent to others!";
                }
            }

//Get subscription information for current organization
            $statusConfig = Configure::read("statusConfig");
            $params = array();
            $conditions = array();
            $todayDate = date('Y-m-d H:i:s');
//                $conditions['start_date <='] = $todayDate;
//                $conditions['end_date >='] = $todayDate;
            $conditions['Subscription.status'] = 1;
            $conditions['Subscription.organization_id'] = $loggedInUser['current_org']['id'];
            $params['conditions'] = $conditions;
            $currentSubscription = $this->Subscription->find("first", $params);
            $poolPurchased = !empty($currentSubscription) ? $currentSubscription['Subscription']['pool_purchased'] + FREE_POOL_USER_COUNT : FREE_POOL_USER_COUNT;
            $joinedUser = $this->UserOrganization->find("count", array("conditions" => array("organization_id" => $loggedInUser['current_org']['id'], "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']))));

            if ($joinedUser >= $poolPurchased) {
                $msg = str_replace("!", ".", $msg);
                if ($poolPurchased > FREE_POOL_USER_COUNT) {
                    $action = 'upgrade';
                } else {
                    $action = 'purchase';
                }
                $msg .= " \nPlease note that you have exceeded your subscription limit. Purchase or upgrade subscription to activate invited user(s) using Admin Portal on www.ndorse.net or by contacting NDORSE LLC at support@ndorse.net.";
            }



            $this->set(array(
                'result' => array("status" => true,
                    "msg" => $msg),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getProfile() {

        if (isset($this->request->data['token'])) {
            $authuser = $this->Auth->user();

            $token = $this->request->data['token'];
            $user_id = $this->request->data['user_id'];
            $org_id = 0;
            if (isset($this->request->data['org_id']) && ($this->request->data['org_id']) > 0) {
                $org_id = $this->request->data['org_id'];
            }
            $userinfo = array();
            if ($authuser["id"] == $user_id) {
                //$org_id = $authuser['current_org']['id'];
                //$org_id = $authuser['current_org']->id;
// *** GET CORE VALUES Start*** //
                $params = array();
                $params['fields'] = "count(*) as cnt";
                $conditionarray["Endorsement.organization_id"] = $org_id;
                $conditionarray["Endorsement.endorser_id"] = $user_id;
                $conditionarray["Endorsement.created >= "] = '2021-01-01 00:00:00';

                $params['conditions'] = $conditionarray;
                $params['order'] = 'Endorsement.created desc';

                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));

                unset($conditionarray["Endorsement.endorser_id"]);
                unset($conditionarray["Endorsement.endorsement_for"]);
                unset($params['order']);
                $conditionarray["Endorsement.endorsed_id"] = $user_id;
                $params['conditions'] = $conditionarray;

                $params['fields'] = "count(EndorseCoreValue.value_id) as total, OrgCoreValues.name as core_value ";
                $params['joins'] = array(
                    array(
                        'table' => 'endorse_core_values',
                        'alias' => 'EndorseCoreValue',
                        'type' => 'INNER',
                        'conditions' => array(
                            'EndorseCoreValue.endorsement_id =Endorsement.id '
                        )
                    ),
                    array(
                        'table' => 'org_core_values',
                        'alias' => 'OrgCoreValues',
                        'type' => 'INNER',
                        'conditions' => array(
                            'OrgCoreValues.id =EndorseCoreValue.value_id '
                        )
                    )
                );
                $params['group'] = 'EndorseCoreValue.value_id';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $corevalues = $this->Endorsement->find("all", $params);
//print_r($corevalues);
//	echo $this->Endorsement->getLastQuery();die;
                $core_values = array();
                if (!empty($corevalues)) {
                    foreach ($corevalues as $cval) {
                        if ($cval["OrgCoreValues"]["core_value"] != "") {
                            $core_values[] = array("name" => $cval["OrgCoreValues"]["core_value"], "value" => $cval[0]["total"]);
                        }
                    }
                }

// *** GET CORE VALUES END*** //



                /*                 * * Budgt CODE START *** */
                $this->Badge->unbindModel(array('belongsTo' => array('Trophy')));

                $params = array();
                $params['fields'] = array("*");
//$params['conditions'] = array("user_id" => $user_id, "organization_id" => $org_id);
                $params['joins'] = array(
                    array(
                        'table' => 'trophies',
                        'alias' => 'Trophy',
                        'type' => 'RIGHT',
                        'conditions' => array(
                            'Badge.trophy_id = Trophy.id',
                            'Badge.user_id = ' . $user_id,
                            'Badge.organization_id = ' . $org_id,
                        )
                    )
                );
                $badges = $this->Badge->find("all", $params);
//                echo $this->Badge->getLastQuery();
//                pr($badges);die;

                $userBadges = array();

                foreach ($badges as $badge) {
                    $badgeInfo = array();
//$badgeInfo['badge_id'] = $badge['Badge']['id'];
                    $badgeInfo['trophy_id'] = $badge['Trophy']['id'];
                    $badgeInfo['count'] = empty($badge['Badge']['count']) ? 0 : (int) $badge['Badge']['count'];
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $badgeInfo['image'] = $rootUrl . TROPHY_IMAGE_DIR . $badge['Trophy']['image'];

                    $userBadges[] = $badgeInfo;
                }
                /*                 * * Budgt CODE END *** */



                $userinfo["user_data"] = $authuser;
                $userinfo["badges"] = $userBadges;
                $userinfo["core_value"] = $core_values;
// $userinfo["user_data"] = $this->getuserData($token);
                if (isset($userinfo["user_data"]["dob"]) && strtotime($userinfo["user_data"]["dob"]) > 0) {
                    $userinfo["user_data"]["dob"] = date("m/d/Y", strtotime($userinfo["user_data"]["dob"]));
                } else {
                    $userinfo["user_data"]["dob"] = "";
                }
                unset($userinfo["current_org"]);
            } else {

// *** GET CORE VALUES Start*** //
                $params = array();
                $conditionarray = array();
                $params['fields'] = "count(*) as cnt";
                $conditionarray["Endorsement.organization_id"] = $org_id;
                $conditionarray["Endorsement.endorser_id"] = $user_id;
                $params['conditions'] = $conditionarray;
                $params['order'] = 'Endorsement.created desc';

                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));

                unset($conditionarray["Endorsement.endorser_id"]);
                unset($conditionarray["Endorsement.endorsement_for"]);
                unset($params['order']);
                $conditionarray["Endorsement.endorsed_id"] = $user_id;
                $params['conditions'] = $conditionarray;

                $params['fields'] = "count(EndorseCoreValue.value_id) as total, OrgCoreValues.name as core_value ";
                $params['joins'] = array(
                    array(
                        'table' => 'endorse_core_values',
                        'alias' => 'EndorseCoreValue',
                        'type' => 'INNER',
                        'conditions' => array(
                            'EndorseCoreValue.endorsement_id =Endorsement.id '
                        )
                    ),
                    array(
                        'table' => 'org_core_values',
                        'alias' => 'OrgCoreValues',
                        'type' => 'INNER',
                        'conditions' => array(
                            'OrgCoreValues.id =EndorseCoreValue.value_id '
                        )
                    )
                );
                $params['group'] = 'EndorseCoreValue.value_id';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $corevalues = $this->Endorsement->find("all", $params);
//print_r($corevalues);
//	echo $this->Endorsement->getLastQuery();die;
                $core_values = array();
                if (!empty($corevalues)) {
                    foreach ($corevalues as $cval) {
                        if ($cval["OrgCoreValues"]["core_value"] != "") {
                            $core_values[] = array("name" => $cval["OrgCoreValues"]["core_value"], "value" => $cval[0]["total"]);
                        }
                    }
                }

// *** GET CORE VALUES END*** //


                $conditionarray = $array = array();
                $conditionarray = array("User.id" => $user_id);
                if ($org_id > 0) {
                    $conditionarray["Organization.id"] = $org_id;
                    $array['fields'] = array('User.*', 'fname', 'lname', 'image', 'Organization.id', 'Organization.name', 'userOrganization.entity_id', 'userOrganization.department_id', 'userOrganization.job_title_id');

                    $array['joins'] = array(
                        array(
                            'table' => 'user_organizations',
                            'alias' => 'userOrganization',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'userOrganization.user_id = User.id'
                            )
                        ),
                        array(
                            'table' => 'organizations',
                            'alias' => 'Organization',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Organization.id = userOrganization.organization_id'
                            )
                        )
                    );
                } else {

                    $array['fields'] = array('User.*', 'fname', 'lname', 'image', 'Organization.id', 'Organization.name', 'userOrganization.entity_id', 'userOrganization.department_id', 'userOrganization.job_title_id');

                    $array['joins'] = array(
                        array(
                            'table' => 'user_organizations',
                            'alias' => 'userOrganization',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'userOrganization.user_id = User.id'
                            )
                        ),
                        array(
                            'table' => 'organizations',
                            'alias' => 'Organization',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Organization.id = userOrganization.organization_id',
                                'Organization.admin_id =' . $authuser["id"]
                            )
                        )
                    );
                }






                /*                 * * Budgt CODE START *** */
                $this->Badge->unbindModel(array('belongsTo' => array('Trophy')));

                $params = array();
                $params['fields'] = array("*");
//$params['conditions'] = array("user_id" => $user_id, "organization_id" => $org_id);
                $params['joins'] = array(
                    array(
                        'table' => 'trophies',
                        'alias' => 'Trophy',
                        'type' => 'RIGHT',
                        'conditions' => array(
                            'Badge.trophy_id = Trophy.id',
                            'Badge.user_id = ' . $user_id,
                            'Badge.organization_id = ' . $org_id,
                        )
                    )
                );
                $badges = $this->Badge->find("all", $params);
//                echo $this->Badge->getLastQuery();
//                pr($badges);die;

                $userBadges = array();

                foreach ($badges as $badge) {
                    $badgeInfo = array();
//$badgeInfo['badge_id'] = $badge['Badge']['id'];
                    $badgeInfo['trophy_id'] = $badge['Trophy']['id'];
                    $badgeInfo['count'] = empty($badge['Badge']['count']) ? 0 : (int) $badge['Badge']['count'];
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $badgeInfo['image'] = $rootUrl . TROPHY_IMAGE_DIR . $badge['Trophy']['image'];

                    $userBadges[] = $badgeInfo;
                }
                /*                 * * Budgt CODE END *** */

//					//select users.*,user_organizations.organization_id,organizations.name
// from  users
//left join user_organizations on user_organizations.user_id =users.id and user_organizations.user_role='3'
//left join organizations on organizations.id = user_organizations.organization_id and organizations.admin_id=2
//where users.id=4 
                $array['conditions'] = $conditionarray;
                $orgArray = $this->User->find("all", $array);
//  print_r($orgArray);

                $orgarr = array();
                foreach ($orgArray as $val) {

                    if (empty($userinfo)) {
                        $userinfo = $val["User"];
                    }
                    $org_idnew = $org_id;
                    if (isset($val["Organization"]) && !empty($val["Organization"])) {
                        $org_idnew = $val["Organization"]["id"];
                        if ($val["Organization"]["id"] != "") {
                            $orgarr[] = $val["Organization"];
                        }
                    }
                    $userinfo["entity"] = "";
                    $userinfo["department"] = "";
                    $userinfo["job_title"] = "";
                    if (isset($val["userOrganization"]) && !empty($val["userOrganization"])) {
                        if ($val["userOrganization"]["entity_id"] > 0) {
//$userinfo["entity"] = $entity;
                            $entity = $this->getOrgValues($org_idnew, "Entity", 1, array($val["userOrganization"]["entity_id"]));
                            if (!empty($entity)) {
                                $userinfo["entity"] = $entity[0]["name"];
                            }
                        }

                        if ($val["userOrganization"]["department_id"] > 0) {
// $userinfo["department"] = $department;
                            $department = $this->getOrgValues($org_idnew, "OrgDepartments", 1, array($val["userOrganization"]["department_id"]));
                            if (!empty($department)) {
                                $userinfo["department"] = $department[0]["name"];
                            }
                        }
                        if ($val["userOrganization"]["job_title_id"] > 0) {
//$userinfo["job_title"] = $job_title;
                            $job_title = $this->getOrgValues($org_id, "OrgJobTitles", 1, array($val["userOrganization"]["job_title_id"]));
// print_r($job_title);
                            if (!empty($job_title)) {
                                $userinfo["job_title"] = $job_title[0]["name"];
                            }
                        }
                    }
                }
                if (strtotime($userinfo["dob"]) > 0) {
                    $userinfo["dob"] = date("m/d/Y", strtotime($userinfo["dob"]));
                } else {
                    $userinfo["dob"] = "";
                }
                $userinfo = array("user_data" => $userinfo, "organization_data" => $orgarr);
            }

            // Added by Babulalprasad @8-may2021 
            $this->loadModel("UserFollowing");
            $followingIdsArray = array();
            $userFollowings = $this->UserFollowing->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $authuser["id"], 'status' => 1)));
            $userinfo['is_following'] = false;
            if (!empty($userFollowings)) {
                $userFollowings = array_shift($userFollowings);
                $userFollowings = $userFollowings['UserFollowing'];
                $uFollowingID = $userFollowings['id'];
                $followingIdsArray = json_decode($userFollowings['following_ids']);
            }
            if (!empty($followingIdsArray)) {
                if (in_array($user_id, $followingIdsArray)) {
                    $userinfo['is_following'] = true;
                } else {
                    $userinfo['is_following'] = false;
                }
            }



//  $userinfo = $this->getuserData($token, true);
            if (!empty($userinfo)) {
                unset($userinfo["user_data"]["password"]);
                unset($userinfo["user_data"]["gplus_id"]);
                unset($userinfo["user_data"]["fb_id"]);
                unset($userinfo["user_data"]["source"]);
                unset($userinfo["user_data"]["source_id"]);
                unset($userinfo["user_data"]["lin_id"]);
                unset($userinfo["user_data"]["secret_code"]);
// $userinfo["users"]["entity"] = "";
                if (isset($userinfo["user_data"]["image"]) && $userinfo["user_data"]["image"] != "") {
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
//                    $userinfo["user_data"]["image"] = str_replace(Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/", "", $userinfo["user_data"]["image"]);
                    $userinfo["user_data"]["image"] = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userinfo["user_data"]["image"];
                }
                $userinfo["core_value"] = $core_values;
                $userinfo["badges"] = $userBadges;

                /** Code to get endorse count to calculate Badges * */
                $endorsedCount = $this->Endorsement->find("count", array("conditions" => array("Endorsement.endorsed_id" => $user_id, "organization_id" => $org_id, "endorsement_for" => "user")));
                $endorserCount = $this->Endorsement->find("count", array("conditions" => array("Endorsement.endorser_id" => $user_id, "organization_id" => $org_id)));
                $userinfo['endorse_count'] = array('giving' => $endorserCount, "getting" => $endorsedCount);



//if ($userinfo["users"]["entity_id"] != "0") {
//    // get entity name
//
                //    $array = array();
//    $array['fields'] = array('name');
//    $array['conditions'] = array('id' => $userinfo["users"]["entity_id"]);
//    $entitydata = $this->Entity->find('first', $array);
//    if (!empty($entitydata)) {
//        $entityname = $entitydata['Entity']['name'];
//    }
//    $userinfo["users"]["entity"] = $entityname;
//}

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "profile info", 'data' => $userinfo),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "invalid token"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function getuserData($token, $data = false) {


        $fields = "id";
        if ($data == true) {
            $fields = "*";
        }
        $userinfo = $this->ApiSession->find("first", array(
            'joins' => array(array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = ApiSession.user_id'))),
            'conditions' => array('ApiSession.token' => $token),
            'fields' => array('users.' . $fields)
        ));

        return $userinfo;
    }

    public function saveprofile() {
        $authuser = $this->Auth->user();
        $firstUpdate = false;
//        pr($authuser);

        if (!isset($authuser['fname']) || empty($authuser['fname'])) {
            $firstUpdate = true;
        }


        $current_org = 0;
        if (isset($authuser["current_org"]) && !empty($authuser["current_org"])) {
            $current_org = $authuser["current_org"]["id"];
        }

        $resizeConfig = array('height' => 279, 'width' => 279);

//print_r($this->request->data);
        if (isset($this->request->data['token'])) {

            $token = $this->request->data['token'];
            $userinfo = $this->getuserData($token);
//            pr($userinfo);
            $this->request->data["id"] = $userinfo["users"]["id"];
            $this->request->data["email"] = $authuser["email"];
            if (isset($this->request->data['image']) && $this->request->data['image'] == "") {
//unset($this->request->data['image']);
            }
            if (isset($this->request->data["dob"]) && strtotime($this->request->data["dob"]) > 0) {
                $this->request->data["dob"] = date("Y-m-d", strtotime($this->request->data["dob"]));
            }
//            pr($this->request->data);
//            exit;
            unset($this->request->data['role']);
            $this->User->set($this->request->data);
// edit

            $this->User->setValidation('edit');
            $imgerror = 0;
            if ($this->User->validates()) {

                if (isset($this->request->data['image']) && $this->request->data['image'] != "") {
                    $imageExtension = $this->User->data['User']['file_extension'];
                }
                if ($this->User->save(null, false)) {
//Upload profile image
                    if (isset($this->request->data['image']) && $this->request->data['image'] != "") {
                        $uploadPath = PROFILE_IMAGE_DIR;
                        $imageData = $this->request->data['image'];
                        $imageName = $this->User->id . "_" . time() . "." . $imageExtension;
                        if ($this->Common->uploadApiImage($uploadPath, $imageName, $imageData)) {
                            $this->User->saveField('image', $imageName);
                        }
                    } elseif (isset($this->request->data['image']) && $this->request->data['image'] == "") {
                        $this->User->saveField('image', "");
                    }
//$current_org

                    $userinfo = $this->getuserData($token, true);

                    if (strtotime($userinfo["users"]["dob"]) > 0) {
                        $userinfo["users"]["dob"] = date("m/d/Y", strtotime($userinfo["users"]["dob"]));
                    } else {
                        $userinfo["users"]["dob"] = "";
                    }

                    unset($userinfo["users"]["password"]);

                    if ($userinfo["users"]["image"] != "") {
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $userinfo["users"]["image"] = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userinfo["users"]["image"];
//$userinfo["users"]["image"] = $userinfo["users"]["image"];
                    }

                    $userinfo['users']["current_org"] = $authuser["current_org"];
                    $this->Session->write('Auth.User', $userinfo['users']);
                    $this->Session->write('Auth.User.profile_updated', true);
                    $this->Session->write('Auth.User.token', $token);
//Get pending request organizations
                    $pendingRequests = $this->OrgRequests->find("all", array("conditions" => array("user_id" => $authuser['id'], "status" => 0)));
                    $pendingRequestOrgs = array();
                    foreach ($pendingRequests as $pendingRequest) {
                        $pendingRequestOrgs[] = $pendingRequest['OrgRequests']['organization_id'];
                    }

                    $this->Session->write('Auth.User.pending_requests', $pendingRequestOrgs);

                    $userinfo = $userinfo["users"];
                    $userinfo["token"] = $token;
                    $userinfo["pending_requests"] = $pendingRequestOrgs;
                    unset($userinfo["created"]);
                    unset($userinfo["updated"]);
//unset($userinfo["secret_code"]);
// unset($userinfo["role"]);//
// get user role according to
                    $userorgrole = $this->UserOrganization->find("all", array(
                        'joins' => array(array('table' => 'default_orgs', 'type' => 'INNER', 'conditions' => array('UserOrganization.organization_id = default_orgs.organization_id'))),
                        'conditions' => array('UserOrganization.user_id' => $userinfo["id"]),
                        'fields' => array('UserOrganization.user_role')
                    ));
//
                    if (!empty($userorgrole)) {

                        $userinfo["role"] = $userorgrole[0]["UserOrganization"]["user_role"];
                    }

                    $msg = "Your profile was updated successfully.";

                    if ($firstUpdate) {
                        $viewVars = array("username" => $authuser['email'], 'first_name' => $this->request->data['fname']);

                        /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($userinfo["id"]);
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /**/

                        $configVars = serialize($viewVars);

                        $subject = "nDorse sign up successful";
                        $to = $this->request->data['email'];
//$this->Common->sendEmail($this->request->data['email'], $subject, "register", $configVars);
                        $email = array("to" => $to, "subject" => $subject, "config_vars" => $configVars, "template" => "register");
                        $this->Email->save($email);
                        $msg = "Your profile was created successfully.";
                    }

//
                    if (isset($this->request->data['device_id']) && $this->request->data['device_id'] != "") {
                        $this->LoginStatistics->updateAll(
                                array('LoginStatistics.live' => "0"), array('LoginStatistics.user_id' => $userinfo["id"])
                        );
                        $loginStats = array();
                        $loginStats['user_id'] = $userinfo["id"];
                        $loginStats['os'] = isset($this->request->data['os']) ? $this->request->data['os'] : "";
                        $loginStats['os_version'] = isset($this->request->data['os_version']) ? $this->request->data['os_version'] : "";
                        $loginStats['device_id'] = isset($this->request->data['device_id']) ? $this->request->data['device_id'] : "";
                        $loginStats['app_version'] = isset($this->request->data['app_version']) ? $this->request->data['app_version'] : "";
                        $loginStats['live'] = 1;
                        $this->LoginStatistics->set($loginStats);
                        $this->LoginStatistics->save();
                    }
//

                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => $msg, 'data' => $userinfo),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $errors = $this->User->validationErrors;
                $errorsArray = array();

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Errors!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function saveprofileorg() {

        if (isset($this->request->data['token'])) {
            $userinfo = $this->getuserData($this->request->data['token'], false);
            $user_id = $userinfo["users"]["id"];
            $org_id = $this->request->data['org_id'];
            $entity_id = $this->request->data['entity_id'];
            $department_id = $this->request->data['department_id'];
            $job_title_id = $this->request->data['job_title_id'];
            $this->UserOrganization->updateAll(
                    array('UserOrganization.entity_id' => "'" . $entity_id . "'", 'UserOrganization.department_id' => "'" . $department_id . "'", 'UserOrganization.job_title_id' => "'" . $job_title_id . "'"), array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_id' => $user_id)
            );
            $userinfo = $this->UserOrganization->find("all", array(
                'joins' => array(array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id'))),
                'conditions' => array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_role' => array('endorser', 'd_admin'), 'UserOrganization.user_id' => $user_id),
                'fields' => array('users.id,users.fname,users.lname,users.image,UserOrganization.status,UserOrganization.user_role')
            ));

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "organization user details", 'data' => $userinfo),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function changepassword() {
// check users organization 1 or many
// 
        $authuser = $this->Auth->user();
        if (isset($this->request->data['token'])) {
            $org_id = $this->request->data["org_id"];
            $user_id = $this->request->data["user_id"];
//
            if ($user_id != "") {

                $array = array();
                $array['fields'] = array('Organization.id', 'Organization.name', 'User.email', 'User1.email as useremail', 'User1.fname', 'User1.lname');
                $conditionarray = array();

                $conditionarray['Organization.status'] = 1;
                $array['joins'] = array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Organization.admin_id =User.id '
                        )
                    ),
                    array(
                        'table' => 'user_organizations',
                        'alias' => 'UserOrganization',
                        'type' => 'INNER',
                        'conditions' => array(
                            'UserOrganization.user_id = ' . $user_id,
                            'UserOrganization.organization_id = Organization.id',
                            'UserOrganization.status != 2'
                        )
                    ),
                    array(
                        'table' => 'users',
                        'alias' => 'User1',
                        'type' => 'INNER',
                        'conditions' => array(
                            'UserOrganization.user_id = User1.id'
                        )
                    )
                );


                $array['conditions'] = $conditionarray;
                $orgArray = $this->Organization->find("all", $array);
                pr($orgArray);
//	echo $this->Organization->getLastQuery();die;
            }
//
            $orgemail = array();

            $this->User->set($this->request->data);
// edit
            $this->request->data["id"] = $user_id;
            $this->User->setValidation('reset_password');
            if ($this->User->validates()) {
                $emailQueue = array();
                $password = $this->request->data["password"];
                $organization_name = "";
                if ($this->User->save($this->request->data)) {
                    foreach ($orgArray as $orgval) {
                        $requestarray[] = array("organization_id" => $orgval['Organization']['id'], "user_id" => $user_id);
// orgAdmin xxxx has reset your password. Your new password is yyyyyy
                        $organization_name = $orgval['Organization']['name'];
                        if ($org_id == $orgval['Organization']['id']) {

//$subject = "Password updated by a " . $organization_name." orgAdmin";
                            $subject = "nDorse Password Reset";
                            $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User1']['fname']), "password" => $password, "user_name" => trim($orgval['User1']['fname'] . " " . $orgval['User1']['lname']));

                            /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                            $userIdEncrypted = base64_encode($user_id);
                            $rootUrl = Router::url('/', true);
                            $rootUrl = str_replace("http", "https", $rootUrl);
                            $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                            $viewVars["pathToRender"] = $pathToRender;
                            /**/

                            $configVars = serialize($viewVars);
                            $emailQueue[] = array("to" => $orgval['User1']['useremail'], "subject" => $subject, "config_vars" => $configVars, "template" => "update_password");
                        } else {
// $subject = "Password updated by a " . $organization_name." orgAdmin";
                            $subject = "nDorse Password Reset";
                            $viewVars = array("org_name" => $organization_name, "fname" => trim($orgval['User1']['fname']), "password" => $password, "user_name" => trim($orgval['User1']['fname'] . " " . $orgval['User1']['lname']));

                            /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                            $userIdEncrypted = base64_encode($user_id);
                            $rootUrl = Router::url('/', true);
                            $rootUrl = str_replace("http", "https", $rootUrl);
                            $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                            $viewVars["pathToRender"] = $pathToRender;
                            /**/

                            $configVars = serialize($viewVars);
                            $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "update_password_admin");
                        }
                    }
                    if (!empty($emailQueue)) {
                        $this->Email->saveMany($emailQueue);
                    }
// send email to user for change password
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Password update successfully. ", 'data' => true),
                        '_serialize' => array('result')
                    ));
                } else {
                    $errors = $this->User->validationErrors;

                    $errorsArray = array();

                    foreach ($errors as $error) {
                        $errorsArray[] = $error;
                    }

                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => $errorsArray),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $errors = $this->User->validationErrors;
                $errorsArray = array();

                foreach ($errors as $error) {
                    $errorsArray[] = $error;
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => $errorsArray),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

// api for get organization details
    public function getOrganization() {
        $statusConfig = Configure::read("statusConfig");
        if (isset($this->request->query['token'])) {
            if (isset($this->request->query['oid'])) {
                $org_id = $this->request->query['oid'];
                $array = array();
                $array['fields'] = array('*');
                $array['conditions'] = array('id' => $org_id);
                $orgArray = $this->Organization->find("first", $array);

                if ($orgArray["Organization"]["image"] != "") {
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $orgArray["Organization"]["image"] = $rootUrl . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $orgArray["Organization"]["image"];
                }
                if (!empty($orgArray)) {

                    $orgArray["Organization"]["status"] = array_search($orgArray["Organization"]["status"], $statusConfig);
//unset($orgArray["Organization"]["secret_code"]);
                    unset($orgArray["Organization"]["admin_id"]);
// get core values
//
                    $params = array();
                    $start_date = "";
                    $end_date = "";
                    if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                        $start_date = $this->request->data["start_date"];
                    }
                    if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                        $end_date = $this->request->data["end_date"];
                    }

                    $start_date = date('Y-m-01 00:00:00', time());
                    $end_date = date('Y-m-d 23:59:59', time());
                    $conditionarray["Endorsement.created >= "] = $start_date;
                    $conditionarray["Endorsement.created <= "] = $end_date;



                    $params = array();
                    $conditionarray['Endorsement.organization_id'] = $org_id; // array('0','1','3');

                    $params['conditions'] = $conditionarray;
                    $params['fields'] = "count(Endorsement.id) as total ";
                    $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                    $total_endorsement = $this->Endorsement->find("all", $params);

                    $total_value = $total_endorsement[0][0]["total"];
                    $cinfo = array();

                    if ($total_value > 0) {
                        $params['fields'] = "count(EndorseCoreValue.value_id) as total,Endorsement.id, OrgCoreValues.name as core_value,OrgCoreValues.id ,OrgCoreValues.color_code ";
                        $params['joins'] = array(
                            array(
                                'table' => 'endorse_core_values',
                                'alias' => 'EndorseCoreValue',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'EndorseCoreValue.endorsement_id =Endorsement.id '
                                )
                            ),
                            array(
                                'table' => 'org_core_values',
                                'alias' => 'OrgCoreValues',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'OrgCoreValues.id =EndorseCoreValue.value_id '
                                )
                            )
                        );
                        $params['group'] = 'EndorseCoreValue.value_id';
                        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                        $corevalues = $this->Endorsement->find("all", $params);




                        foreach ($corevalues as $cval) {

                            $cinfo[] = array("id" => $cval["OrgCoreValues"]["id"], "name" => $cval["OrgCoreValues"]["core_value"], "color_code" => $cval["OrgCoreValues"]["color_code"], "total" => $cval[0]["total"]);
//$total_value += $cval[0]["tot"];
                        }
                    }


                    $coreinfo = $this->OrgCoreValues->find("all", array(
                        'conditions' => array('OrgCoreValues.organization_id' => $org_id, 'OrgCoreValues.status' => 1),
                        'fields' => array('OrgCoreValues.id,OrgCoreValues.name,OrgCoreValues.color_code')
                    ));

                    $core_value = array();
                    foreach ($coreinfo as $cval) {

                        $core_value[] = array("id" => $cval["OrgCoreValues"]["id"], "name" => $cval["OrgCoreValues"]["name"], "color_code" => $cval["OrgCoreValues"]["color_code"]);
//$total_value += $cval[0]["tot"];
                    }
//        select count(`EndorseCoreValue`.`id`),`Endorsement`.`id` from `endorsements` AS `Endorsement`  
//LEFT  JOIN `ndorse_arcgate`.`endorse_core_values` AS `EndorseCoreValue` ON (`Endorsement`.`id` =`EndorseCoreValue`.`endorsement_id`) 
//where `Endorsement`.`organization_id`=258
//group by EndorseCoreValue.endorsement_id  order by `Endorsement`.`id`
                    $params = array();
                    $conditionarray['Endorsement.organization_id'] = $org_id; // array('0','1','3');
                    unset($conditionarray["Endorsement.created >= "]);
                    unset($conditionarray["Endorsement.created <= "]);

                    $params['conditions'] = $conditionarray;
                    $params['fields'] = "count(EndorseCoreValue.value_id) as total,Endorsement.id";
                    $params['joins'] = array(
                        array(
                            'table' => 'endorse_core_values',
                            'alias' => 'EndorseCoreValue',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'EndorseCoreValue.endorsement_id = Endorsement.id '
                            )
                        )
                    );
                    $params['group'] = 'EndorseCoreValue.endorsement_id';
                    $params['order'] = 'Endorsement.id asc';
                    $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));

                    $corevaluesendorsement = $this->Endorsement->find("all", $params);

//                $log = $this->Endorsement->getDataSource()->getLog(false, false);
//                pr($log);
//                exit;
                    //pr($corevaluesendorsement); exit;



                    $corevaltotal = 0;
                    foreach ($corevaluesendorsement as $coreeval) {
                        $corevaltotal += $coreeval[0]["total"];
                    }
//print_r($corevaluesendorsement);
// echo $this->Endorsement->getLastQuery();die;
//exit;
                    $orgArray["core_values"] = $cinfo;
                    $orgArray["total_core_values"] = $corevaltotal;
                    $orgArray["total_endorsement"] = count($corevaluesendorsement); //$cinfo;
                    $orgArray["org_core_values"] = $core_value;
                    $orgArray["total_endorsement_month"] = $total_value;
                    $orgArray["departments"] = $this->getOrgValues($org_id, "OrgDepartments");
                    $orgArray["entity"] = $this->getOrgValues($org_id, "Entity");
                    $orgArray["job_titles"] = $this->getOrgValues($org_id, "OrgJobTitles");
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $orgArray["Organization"]["health_url"] = $rootUrl . "img/" . $orgArray["Organization"]["health_url"];
                    pr($orgArray); exit;
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Organization info ", 'data' => $orgArray),
                        '_serialize' => array('result')
                    ));
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Organization not found"),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Organization ID is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

// api for save organization 		 
    public function saveOrganization() {
        $statusConfig = Configure::read("statusConfig");

        if ($this->request->is('post')) {
//'ruleValid'=>array(
//                   'rule' => array('validateImage'),
//            )
//$this->Organization->validate['short_name']['ruleUnique'] = array(
//    'rule' => 'isUnique',
//    'required' => 'create',
//    "on" => 'update',
//    'message' => 'Short name already exists.'
//);
            $org_id = $this->request->data["id"] = $this->request->data["org_id"];


// unset($this->Organization->validate['image']['ruleRequired']);
// unset($this->Organization->validate['secret_code']);
            if (isset($this->request->data['image']) && $this->request->data['image'] == "") {
                unset($this->request->data['image']);
            }
            if (isset($this->request->data['status']) && $this->request->data['status'] != "") {
                $this->request->data['status'] = $statusConfig[$this->request->data['status']];
            }

            $this->Organization->set($this->request->data);

            $this->Organization->validate['name']['ruleUnique'] = array(
                'rule' => 'isUnique',
                'required' => 'create',
                "on" => 'update',
                'message' => 'Organization name already exists.'
            );
// if(isset($this->request->data['name']) && $this->request->data['name']!="")
// {
// $organizations = $this->Organization->find("all", array("conditions" => array("id !=" => $org_id,"name"=>$this->request->data['name'])));
// print_r($organizations);
// }
//unset($this->Organization->validate['name']['ruleUnique']);
            $this->Organization->id = $org_id;

            if ($this->Organization->validates()) {
//


                if (isset($this->request->data['image']) && $this->request->data['image'] != "") {
                    $imageExtension = $this->Organization->data['Organization']['file_extension'];
                }

                if ($this->Organization->save(null, false)) {
//Upload profile image
                    if (isset($this->request->data['image']) && $this->request->data['image'] != "") {
                        $uploadPath = ORG_IMAGE_DIR;
                        $imageData = $this->request->data['image'];
                        $imageName = $this->Organization->id . "_" . time() . "." . $imageExtension;
                        if ($this->Common->uploadApiImage($uploadPath, $imageName, $imageData)) {
                            $this->Organization->saveField('image', $imageName);
                        }
                    }
// save organization core value
                    if (isset($this->request->data["core_values"]) && $this->request->data["core_values"] != "") {
                        $orgCoreValues = json_decode($this->request->data['core_values']);
//$orgCoreValues = array();
                        $array = array();
                        $array['fields'] = array('*');
                        $array['conditions'] = array('organization_id' => $org_id);
                        $coreValuelist = $this->OrgCoreValues->find("all", $array);
                        $existcoreValueArray = array();


                        if (!empty($coreValuelist)) {

                            foreach ($coreValuelist as $listval) {

                                $existcoreValueArray[$listval["OrgCoreValues"]["id"]] = $listval["OrgCoreValues"]["name"];
//$coreValuelist
                            }
                        }

                        $this->OrgCoreValues->updateAll(
                                array('OrgCoreValues.status' => "2"), array('OrgCoreValues.organization_id' => $org_id)
                        );
                        $norgCoreValues = array();

                        foreach ($orgCoreValues as $key => $coreValue) {
                            $valueid = array_search($coreValue->name, $existcoreValueArray);
//echo $coreValue["name"]."-----newid---".$valueid."----".$coreValue["id"];
                            if (1) {

                                if ($valueid > 0) {
                                    $norgCoreValues[$key]['id'] = $valueid;
//continue;
                                }
                            } else {
//if ($valueid > 0 && $valueid != $coreValue->id) {
//    $norgCoreValues[$key]['id'] = $valueid;
//    //continue;
//}
                            }

                            $norgCoreValues[$key]['color_code'] = $coreValue->color_code;
                            $norgCoreValues[$key]['name'] = $coreValue->name;
                            $norgCoreValues[$key]['organization_id'] = $org_id;
                            $norgCoreValues[$key]['status'] = 1;

//if (isset($coreValue->from_master)) {
//    $norgCoreValues[$key]["from_master"] = $coreValue->from_master;
//}
                        }

                        $this->OrgCoreValues->saveMany($norgCoreValues);
                    }
// end save org core values
// save entity org values
                    if (isset($this->request->data["entity"]) && $this->request->data["entity"] != "") {
                        $array = array();
                        $array['fields'] = array('*');
                        $array['conditions'] = array('organization_id' => $org_id);
                        $entityValuelist = $this->Entity->find("all", $array);
                        $existentityValueArray = array();
                        $this->Entity->updateAll(
                                array('Entity.status' => "2"), array('Entity.organization_id' => $org_id)
                        );
                        if (!empty($entityValuelist)) {

                            foreach ($entityValuelist as $listval) {

                                $existentityValueArray[$listval["Entity"]["id"]] = $listval["Entity"]["name"];
//$coreValuelist
                            }
                        }

                        $orgEntityValues = explode(",", $this->request->data["entity"]);
                        $orgEntityValues1 = array();
                        foreach ($orgEntityValues as $key => $EntityValues) {
//
                            $valueid = array_search($EntityValues, $existentityValueArray);
//echo $coreValue["name"]."-----newid---".$valueid."----".$coreValue["id"];
                            if (1) {

                                if ($valueid > 0) {
                                    $orgEntityValues1[$key]['id'] = $valueid;
//continue;
                                }
                            } else {
//if ($valueid > 0 && $valueid != $EntityValues->id) {
//    $orgEntityValues1[$key]['id'] = $valueid;
//    //continue;
//}
                            }
//
                            $orgEntityValues1[$key]["organization_id"] = $org_id;
                            $orgEntityValues1[$key]['name'] = $EntityValues;
                            $orgEntityValues1[$key]["status"] = 1;
                        }
                        $this->Entity->saveMany($orgEntityValues1);
                    } else {
                        $this->Entity->updateAll(
                                array('Entity.status' => "2"), array('Entity.organization_id' => $org_id)
                        );
                    }


// end save entity org 
// save dept org values
                    if (isset($this->request->data["department"]) && $this->request->data["department"] != "") {
                        $array = array();
                        $array['fields'] = array('*');
                        $array['conditions'] = array('organization_id' => $org_id);
                        $deptValuelist = $this->OrgDepartments->find("all", $array);
                        $existdeptValueArray = array();
                        $this->OrgDepartments->updateAll(
                                array('OrgDepartments.status' => "2"), array('OrgDepartments.organization_id' => $org_id)
                        );
                        if (!empty($deptValuelist)) {

                            foreach ($deptValuelist as $listval) {

                                $existdeptValueArray[$listval["OrgDepartments"]["id"]] = $listval["OrgDepartments"]["name"];
//$coreValuelist
                            }
                        }

                        $orgDeptValues = explode(",", $this->request->data["department"]);
                        $orgDeptValues1 = array();
                        foreach ($orgDeptValues as $key => $DeptValues) {
//
                            $valueid = array_search($DeptValues, $existdeptValueArray);
//echo $coreValue["name"]."-----newid---".$valueid."----".$coreValue["id"];
                            if (1) {

                                if ($valueid > 0) {
                                    $orgDeptValues1[$key]['id'] = $valueid;
//continue;
                                }
                            } else {
//if ($valueid > 0 && $valueid != $DeptValues->id) {
//    $orgDeptValues1[$key]['id'] = $valueid;
//    //continue;
//}
                            }
//
                            $orgDeptValues1[$key]["organization_id"] = $org_id;
                            $orgDeptValues1[$key]['name'] = $DeptValues; //$DeptValues->name;
//$orgDeptValues1[$key]["from_master"] = $DeptValues->from_master;
                            $orgDeptValues1[$key]["status"] = 1;
                        }
                        $this->OrgDepartments->saveMany($orgDeptValues1);
                    } else {
                        $this->OrgDepartments->updateAll(
                                array('OrgDepartments.status' => "2"), array('OrgDepartments.organization_id' => $org_id)
                        );
                    }


// end save Dept org
// save job title org values
                    if (isset($this->request->data["job_title"]) && $this->request->data["job_title"] != "") {
                        $array = array();
                        $array['fields'] = array('*');
                        $array['conditions'] = array('organization_id' => $org_id);
                        $JobTitleValuelist = $this->OrgJobTitles->find("all", $array);
                        $existJobTitleValueArray = array();
                        $this->OrgJobTitles->updateAll(
                                array('OrgJobTitles.status' => "2"), array('OrgJobTitles.organization_id' => $org_id)
                        );
                        if (!empty($JobTitleValuelist)) {

                            foreach ($JobTitleValuelist as $listval) {

                                $existJobTitleValueArray[$listval["OrgJobTitles"]["id"]] = $listval["OrgJobTitles"]["title"];
//$coreValuelist
                            }
                        }

                        $orgJobTitleValues = explode(",", $this->request->data["job_title"]);
                        $orgJobTitleValues1 = array();
                        foreach ($orgJobTitleValues as $key => $JobTitleValues) {
//
                            $valueid = array_search($JobTitleValues, $existJobTitleValueArray);
//echo $coreValue["name"]."-----newid---".$valueid."----".$coreValue["id"];
                            if (1) {

                                if ($valueid > 0) {
                                    $orgJobTitleValues1[$key]['id'] = $valueid;
//continue;
                                }
                            } else {
//if ($valueid > 0 && $valueid != $JobTitleValues->id) {
//    $orgJobTitleValues1[$key]['id'] = $valueid;
//    //continue;
//}
                            }
//
                            $orgJobTitleValues1[$key]['title'] = $JobTitleValues;
// $orgJobTitleValues1[$key]["from_master"] = $JobTitleValues->from_master;
                            $orgJobTitleValues1[$key]["organization_id"] = $this->Organization->id;
                            $orgJobTitleValues1[$key]["status"] = 1;
                        }
                        $this->OrgJobTitles->saveMany($orgJobTitleValues1);
                    } else {
                        $this->OrgJobTitles->updateAll(
                                array('OrgJobTitles.status' => "2"), array('OrgJobTitles.organization_id' => $org_id)
                        );
                    }



// end save Dept org
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Organization profile updated successfully.", 'data' => true),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $errors = $this->Organization->validationErrors;
                $errorsArray = array();

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Error!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

// api for get user in organization
    public function getorganizationuser() {
        $statusConfig = Configure::read("statusConfig");
        if (isset($this->request->query['token'])) {
            $org_id = $this->request->query['oid'];
//$loggedinUser = $this->Auth->user();
            $userinfo = $this->getuserData($this->request->query['token']);
            $login_user_id = $userinfo["users"]["id"];
            $limit = Configure::read("pageLimit");
            if (isset($this->request->query["page"]) && $this->request->query["page"] > 1) {
                $page = $this->request->query["page"];
                $offset = $page * $limit;
            } else {
                $page = 1;
                $offset = 0;
            }
            $userspecific = 0;
            $user_id = 0;
            if (isset($this->request->query['user_id']) && ($this->request->query['user_id'] > 0)) {
                $userspecific = 1;
                $user_id = $this->request->query['user_id'];
            }

            $params = array();
            $params['fields'] = "count(*) as cnt";
//echo $userspecific."---". $login_user_id;
            if ($userspecific == 0) {
                $params['conditions'] = array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_role' => array('3', '2'), 'UserOrganization.user_id !=' => $login_user_id, 'UserOrganization.status' => array('0', '1', '3'));
            } else {
                $params['conditions'] = array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_id' => $user_id, 'UserOrganization.user_role' => array('3', '2'), 'UserOrganization.status' => array('0', '1', '3'));
            }
            $params['order'] = 'UserOrganization.created desc';
            $params['joins'] = array(
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'INNER',
                    'conditions' => array(
                        'User.id =UserOrganization.user_id '
                    )
                )
            );
            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User',)));
            if ($userspecific == 0) {

                $totaluser = $this->UserOrganization->find("all", $params);
//print_r($totaluser);
//echo $this->UserOrganization->getLastQuery();die;
                $totaluser = $totaluser[0][0]["cnt"];
                $totalpage = ceil($totaluser / $limit);
            }

            $params['fields'] = "User.id,User.fname,User.lname,User.image,UserOrganization.status,UserOrganization.user_role,UserOrganization.entity_id,UserOrganization.department_id,UserOrganization.job_title_id";
            if ($userspecific == 0) {
                $params['limit'] = $limit;
                $params['page'] = $page;
                $params['offset'] = $offset;
                $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User',)));
            }
            $userinfo = $this->UserOrganization->find("all", $params);


//$userinfo = $this->UserOrganization->find("all", array(
//            'joins' => array(array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id'))),
//            'conditions' => array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_role' => array('3', '4'), 'UserOrganization.status' => array('0', '1', '3')),
//            'fields' => array('users.id,users.fname,users.lname,users.image,UserOrganization.status,UserOrganization.user_role,UserOrganization.entity_id,UserOrganization.department_id,UserOrganization.job_title_id')
//        ));
// echo $this->UserOrganization->getLastQuery();
// echo "<hr>";

            $userdetails = array();
            foreach ($userinfo as $userval) {

                if ($userval["User"]["image"] != "") {
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $userval["User"]["image"] = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                }

                $userval['UserOrganization']['status'] = array_search($userval['UserOrganization']['status'], $statusConfig);
                $userdetails[] = array_merge($userval['User'], $userval['UserOrganization']);
            }
            if ($userspecific == 0) {
                $data = array("users" => $userdetails, "total_page" => $totalpage);
            } else {
                $data = array("users" => $userdetails);
            }
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "organization users", 'data' => $data),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

// api for inactive/active/delete user in organization
    public function userOrgAction() {
        $statusConfig = Configure::read("statusConfig");

        if (isset($this->request->data['token'])) {
            $loggedInUser = $this->Auth->user();
            $org_id = $this->request->data['oid'];
            $useraccess = $this->request->data['status'];
            $status = $statusConfig[$useraccess];
            $user_id = $this->request->data['user_id'];
            $pool_type = "free";
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
                    $rootUrl = str_replace("http", "https", $rootUrl);
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

                /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                $userIdEncrypted = base64_encode($euser["id"]);
                $rootUrl = Router::url('/', true);
                $rootUrl = str_replace("http", "https", $rootUrl);
                $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                $viewVars["pathToRender"] = $pathToRender;
                /*                 * */

                $configVars = serialize($viewVars);
                $emailQueue[] = array("to" => $euser['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "org_action");
            }
            unset($userdetails["email"]);
//            $this->Email->saveMany($emailQueue); //Removed as per client requirement
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => $updatemsg, 'data' => $userdetails),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function userOrgAdminAccessAction() {
        if (isset($this->request->data['token'])) {
            $org_id = $this->request->data['oid'];
            $role = $this->request->data['role'];
            $user_id = $this->request->data['user_id'];

            $this->UserOrganization->updateAll(
                    array('UserOrganization.user_role' => "'" . $role . "'"), array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_id' => $user_id)
            );
            $userinfo = $this->UserOrganization->find("all", array(
                'joins' => array(array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id'))),
                'conditions' => array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_id' => $user_id),
                'fields' => array('users.id,users.fname,users.lname,users.email,users.image,UserOrganization.status,UserOrganization.user_role')
            ));
//$userdetails = array();
            $roleList = $this->Common->setSessionRoles();
            $successdata = "";
            $emailQueue = array();
            if ($role == 2)
                $subject = "nDorse Notification -- Admin control granted to a user";
            else
                $subject = "nDorse Notification -- Admin control revoked from a user";

            foreach ($userinfo as $userval) {
                if ($userval["users"]["image"] != "") {
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $userttimage = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["users"]["image"];
                    $userttimage = str_replace("http", "https", $userttimage);
                    $userval["users"]["image"] = $userttimage;
                }
                $userval['UserOrganization']["role"] = $roleList[$userval['UserOrganization']["user_role"]];
                $userval['UserOrganization']["user_role"] = $userval['UserOrganization']["user_role"];
// $userdetails[] = array_merge($userval['users'], $userval['UserOrganization']);
                $successdata = $userval['UserOrganization'];
                $viewVars = array("org_id" => $org_id, "role" => $role, "user_id" => $user_id, "fname" => $userval["users"]["fname"]);

                /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                $userIdEncrypted = base64_encode($user_id);
                $rootUrl = Router::url('/', true);
                $rootUrl = str_replace("http", "https", $rootUrl);
                $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                $viewVars["pathToRender"] = $pathToRender;
                /*                 * * */

                $configVars = serialize($viewVars);
                $emailQueue[] = array("to" => $userval["users"]['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "org_admin_access_action");
            }

//            $this->Email->saveMany($emailQueue); //Removed as per client requirement
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "User's role updated successfully.", 'data' => $successdata),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

// search user api for organization
    public function userOrgSearch() {


        if (isset($this->request->query['token'])) {

            $org_id = $this->request->query['oid'];
            $keyword = $this->request->query['keyword'];
            $keyword = $keyword . "%";
            $userinfo = $this->UserOrganization->find("all", array(
                'joins' => array(array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id'))),
                'conditions' => array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_role' => array('3', '2'), 'UserOrganization.status' => array('0', '1', '3'), array('OR' => array('users.fname LIKE' => $keyword, 'users.lname LIKE' => $keyword))),
                'fields' => array('users.id,users.fname,users.lname,users.image,users.email,UserOrganization.status')
            ));
            $userdetails = array();
            foreach ($userinfo as $userval) {
                if ($userval["users"]["image"] != "") {
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $userval["users"]["image"] = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["users"]["image"];
                }
                $userval["users"]["name"] = $userval["users"]["fname"] . " " . $userval["users"]["lname"];
                unset($userval["users"]["fname"]);
                unset($userval["users"]["lname"]);
                $userdetails[] = array_merge($userval['users'], $userval['UserOrganization']);
            }
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "organization users", 'data' => $userdetails),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

// get all organization for loggeding user
    public function getAllOrganization_old() {
        if (isset($this->request->data['token'])) {
            $token = $this->request->data['token'];
            $userid = $this->getuserData($token);
            $type = $this->request->data['type'];
            if (!empty($userid)) {
                $user_id = $userid["users"]["id"];
                $array = array();
                $array['fields'] = array('id', 'name', 'short_name', 'image', 'status');
                $conditionarray = array();
                if ($type == "user") {
                    $array['fields'] = array('id', 'name', 'short_name', 'image', 'status');
                    $conditionarray['admin_id'] = $user_id;
                    $conditionarray['status'] = array(0, 1);
                } elseif ($type == "endorser") {
                    $array['fields'] = array('id', 'name', 'short_name', 'image', 'UserOrganization.user_role');
                    $conditionarray['Organization.status'] = 1;
//$conditionarray['Organization.admin_id !='] = $user_id;
//
                    $array['joins'] = array(
                        array(
                            'table' => 'user_organizations',
                            'alias' => 'UserOrganization',
                            'type' => 'INNER',
                            'conditions' => array(
                                'UserOrganization.user_id = ' . $user_id,
                                'UserOrganization.organization_id = Organization.id'
                            )
                        )
                    );
//
                } else {
                    $array['fields'] = array('id', 'name', 'short_name', 'image', 'UserOrganization.user_id');
                    $conditionarray['Organization.status'] = 1;
                    $array['joins'] = array(
                        array(
                            'table' => 'user_organizations',
                            'alias' => 'UserOrganization',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'UserOrganization.user_id = ' . $user_id,
                                'UserOrganization.organization_id = Organization.id'
                            )
                        )
                    );
                }
                $array['conditions'] = $conditionarray;
                $orgArray = $this->Organization->find("all", $array);

                $orginfo = array();
                foreach ($orgArray as $val) {

                    $val1 = $val["Organization"];
                    if ($type == "public") {
                        $exituser = $val["UserOrganization"];
                    } elseif ($type == "endorser") {
                        if ($val["UserOrganization"]["user_role"] == 2) {
                            $role = "admin";
                        } elseif ($val["UserOrganization"]["user_role"] == 4) {
                            $role = "designated_admin";
                        } else {
                            $role = "ndorser";
                        }
                        $val1["role"] = $role;
                    }
                    if ($val1["image"] != "") {
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $val1["image"] = $rootUrl . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $val1["image"];
                    }
                    if ($type != "public") {
                        $orginfo[] = $val1;
                    } elseif ($exituser["user_id"] != $user_id) {
                        $orginfo[] = $val1;
                    }
                }
//$orginfo["user"]["token"]= $token;

                $orginfo1["token"] = $token;
                $orginfo1["organization"] = $orginfo;
                $orginfo1 = array("token" => $token, "oganization" => $orginfo);


                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "organization details", 'data' => $orginfo1),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid token in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function getAllOrganization() {
        if (isset($this->request->data['token'])) {
            $token = $this->request->data['token'];
            $userid = $this->getuserData($token);
            $type = $this->request->data['type'];
            $org_id = 0;
            if (isset($this->request->data['org_id']) && $this->request->data['org_id'] > 0) {
                $org_id = $this->request->data['org_id'];
            }
            if (isset($this->request->data["limit"])) {
                $limit = $this->request->data["limit"];
            } else {
                $limit = Configure::read("pageLimit");
            }
            if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
                $page = $this->request->data["page"];
                $offset = $page * $limit;
            } else {
                $page = 1;
                $offset = 0;
            }
            $countfields = "count(*) as cnt";
            if (!empty($userid)) {
                $user_id = $userid["users"]["id"];
                $array = array();
                $selectfields = array('id', 'name', 'short_name', 'image', 'status', 'health_url');

                $conditionarray = array();
                if ($type == "user") {
                    $array['fields'] = array('Organization.id', 'name', 'short_name', 'image', 'health_url', 'UserOrganization.status', 'UserOrganization.user_role');
//$conditionarray['admin_id'] = $user_id;

                    $array['joins'] = array(
                        array(
                            'table' => 'user_organizations',
                            'alias' => 'UserOrganization',
                            'type' => 'INNER',
                            'conditions' => array(
                                'UserOrganization.user_id = ' . $user_id,
                                'UserOrganization.organization_id = Organization.id',
//                                'UserOrganization.joined = 1',
                                'UserOrganization.status = 1',
                                'UserOrganization.user_role' => array('2')
                            )
                        )
                    );
                    $conditionarray['Organization.status'] = array(0, 1);
                } elseif ($type == "endorser") {
                    $selectfields = array('id', 'name', 'short_name', 'image', 'about', 'status', 'UserOrganization.user_role', 'health_url');
// $conditionarray['Organization.status'] = array(0, 1);
                    $conditionarray['Organization.status'] = array(0, 1);
//$conditionarray['Organization.admin_id !='] = $user_id;
//
                    $array['joins'] = array(
                        array(
                            'table' => 'user_organizations',
                            'alias' => 'UserOrganization',
                            'type' => 'INNER',
                            'conditions' => array(
                                'UserOrganization.user_id = ' . $user_id,
                                'UserOrganization.organization_id = Organization.id',
//                                'UserOrganization.joined = 1',
                                'UserOrganization.status = 1',
                                'UserOrganization.user_role' => array(2, 3, 6)
                            )
                        )
                    );
//
                } else {
                    $selectfields = array('id', 'name', 'short_name', 'image', 'about', 'health_url', 'OrgRequests.user_id');
                    $conditionarray['Organization.status'] = 1;

                    $farray = array();
                    $farray['fields'] = array('organization_id');
                    $farray['conditions'] = array('UserOrganization.user_id = ' . $user_id, 'UserOrganization.status !=2');
                    $orgusers = $this->UserOrganization->find("all", $farray);
                    $org_idarray = array();
                    foreach ($orgusers as $orgval) {
                        $org_idarray[] = $orgval["UserOrganization"]["organization_id"];
                    }
//  echo $this->UserOrganization->getLastQuery();
//print_r($org_idarray);exit;
                    $array['joins'] = array(
                        array(
                            'table' => 'org_requests',
                            'alias' => 'OrgRequests',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'OrgRequests.user_id = ' . $user_id,
                                'OrgRequests.organization_id = Organization.id',
                                'OrgRequests.status = 0'
                            )
                        )
                    );
                    /** Commented by Babulal Prasad @12152016 to get Joined Org data TOO *** */
//                    if (!empty($org_idarray)) {
//                        $conditionarray['Organization.id !='] = $org_idarray;
//                    }
                }
                if ($org_id > 0) {
                    $conditionarray['Organization.id'] = $org_id;
                }
                $array['conditions'] = $conditionarray;
                $array['fields'] = $countfields;
                if ($org_id == 0) {
                    $orgArray = $this->Organization->find("all", $array);
                    $totalorg = $orgArray[0][0]["cnt"];
                    $totalpage = ceil($totalorg / $limit);

                    $array['limit'] = $limit;
                    $array['page'] = $page;
                    $array['offset'] = $offset;
                }
                $array['fields'] = $selectfields;
                $orgArray = $this->Organization->find("all", $array);
//echo $this->Organization->getLastQuery();
//print_r($orgArray);exit;
                $orginfo = array();
                foreach ($orgArray as $val) {

                    $requestuser = 0;
                    $val1 = $val["Organization"];




                    if ($type == "public") {
// $exituser = $val["UserOrganization"];
//echo $type;
//Added By Babulal Prasad
                        $val1['is_org_joined'] = 0;
                        if (!empty($org_idarray)) {
                            if (in_array($val1["id"], $org_idarray)) {
                                $val1['is_org_joined'] = 1;
                            }
                        }

                        $requestuser = $val["OrgRequests"]["user_id"];
                        if ($requestuser > 0) {
                            $val1["is_request"] = $requestuser;
                        } else {
                            $val1["is_request"] = 0;
                        }
//print_r($val1);
                    } elseif ($type == "endorser") {
                        if ($val["UserOrganization"]["user_role"] == 2) {
                            $role = "admin";
                        } elseif ($val["UserOrganization"]["user_role"] == 4) {
                            $role = "designated_admin";
                        } else {
                            $role = "ndorser";
                        }
                        $val1["role"] = $role;
                    } elseif ($type == "user") {

//if ($val["UserOrganization"]["user_role"] == 2) {
//      $role = "admin";
//  } elseif ($val["UserOrganization"]["user_role"] == 4) {
//      $role = "designated_admin";
//  } else {
//      $role = "ndorser";
//  }
//  $val1["role"] = $role;   
                    }
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $val1["health_url"] = $rootUrl . "img/" . $val1["health_url"];
                    if ($val1["image"] != "") {
                        $val1["image"] = $rootUrl . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $val1["image"];
                    }
//if ($type != "public") {
                    $orginfo[] = $val1;
//} elseif ($exituser["user_id"] != $user_id) {
//   $orginfo[] = $val1;
// }
                }
//pr($orginfo); exit;
                $orginfo1["token"] = $token;
//$orginfo1["total_page"] = $totalpage;
                $orginfo1["organization"] = $orginfo;
                if ($org_id == 0) {
                    $orginfo1 = array("token" => $token, "organization" => $orginfo, "total_page" => $totalpage);
                } else {
                    $orginfo1 = array("token" => $token, "organization" => $orginfo);
                }

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "organization details", 'data' => $orginfo1),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid token in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

// get search all organization for loggeding user
    public function getOrgSearch() {
        if (isset($this->request->data['token']) && isset($this->request->data['keyword'])) {
            $token = $this->request->data['token'];
            $userid = $this->getuserData($token);

            if (!empty($userid)) {
                $user_id = $userid["users"]["id"];
                $keyword = $this->request->data['keyword'];
                $type = "public";
                if (isset($this->request->data['type']) && $this->request->data['type'] != "") {
                    $type = $this->request->data['type'];
                }
                $array = array();
                $array['fields'] = array('id', 'name', 'short_name');
                if ($type == "user") {
                    $array['conditions'] = array('status' => 1, 'admin_id' => $user_id, array('OR' => array('name LIKE' => '%' . $keyword . '%', 'short_name LIKE' => '%' . $keyword . '%')));
                } else {
//$array['joins'] = array(
//    array(
//        'table' => 'user_organizations',
//        'alias' => 'UserOrganization',
//        'type' => 'LEFT',
//        'conditions' => array(
//            'UserOrganization.user_id = ' . $user_id,
//            'UserOrganization.organization_id = Organization.id',
//            'UserOrganization.status IN (0,1,3)'
//        )
//    )
//);
//
                    $farray = array();
                    $farray['fields'] = array('organization_id');
                    $farray['conditions'] = array('UserOrganization.user_id = ' . $user_id, 'UserOrganization.status !=2');
                    $orgusers = $this->UserOrganization->find("all", $farray);
                    $org_idarray = array();
                    foreach ($orgusers as $orgval) {
                        $org_idarray[] = $orgval["UserOrganization"]["organization_id"];
                    }
//  echo $this->UserOrganization->getLastQuery();
//print_r($org_idarray);exit;
//                    if (!empty($org_idarray)) {
//                        //$conditionarray['Organization.id !='] = $org_idarray;
//                        $array['conditions'] = array('Organization.id !=' => $org_idarray, 'Organization.status' => 1, array('OR' => array('name LIKE' => '%' . $keyword . '%', 'short_name LIKE' => '%' . $keyword . '%')));
//                    } else {
//  
                    $array['conditions'] = array('Organization.id !=' => 415, 'Organization.status' => 1, array('OR' => array('name LIKE' => '%' . $keyword . '%', 'short_name LIKE' => '%' . $keyword . '%')));
//                    }
                }
                $orgArray = $this->Organization->find("all", $array);
// echo $this->Organization->getLastQuery();
                $orginfo = array();
                foreach ($orgArray as $val) {

                    $val1 = $val["Organization"];

//if ($val["Organization"]["image"] != "") {
//    $val["Organization"]["image"] = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $val["Organization"]["image"];
//}
// if (empty($val["UserOrganization"])) {
// 
// 
//Added By Babulal Prasad
                    $val1['is_org_joined'] = 0;

                    if (!empty($org_idarray)) {
                        if (in_array($val1["id"], $org_idarray)) {
                            $val1['is_org_joined'] = 1;
                        }
                    }
                    $orginfo[] = $val1;


// }
                }
                $orginfo1["token"] = $token;
                $orginfo1["organization"] = $orginfo;
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "search organization details", 'data' => $orginfo1),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid token in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token or keyword is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function getOrgAction() {

        if (isset($this->request->data['token'])) {
            $org_id = $this->request->data['oid'];
            $status = $this->request->data['status'];

            $this->Organization->updateAll(
                    array('Organization.status' => $status), array('id' => $org_id)
            );
            $array = array();
            $array['fields'] = array('id', 'name', 'short_name', 'image', 'status');
            $array['conditions'] = array('id' => $org_id);
            $orgArray = $this->Organization->find("all", $array);
            $orginfo = "";
            foreach ($orgArray as $val) {

                if ($val["Organization"]["image"] != "") {
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $val["Organization"]["image"] = $rootUrl . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $val["Organization"]["image"];
                }

                $orginfo = $val;
            }
            $viewVars = array("org_id" => $org_id, "status" => $status, "name" => $orgArray[0]["Organization"]["name"]);
            $configVars = serialize($viewVars);
            if ($status == 0) {
                $statusmsg = "deactivated";
            } elseif ($status == 2) {
                $statusmsg = "deleted";
            } elseif ($status == 1) {
                $statusmsg = "activated";
            }

            $subject = "nDorse Notification -- Organization " . $statusmsg . " by admin";
            $emailQueue[] = array("to" => "admin@ndorse.com", "subject" => $subject, "config_vars" => $configVars, "template" => "org_status_action");
//            $this->Email->saveMany($emailQueue); //Removed as per client requirement

            $this->getTimelyUpdates();

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Organization status updated successfully.", 'data' => $orginfo),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function saveOrgoption() {
        if (isset($this->request->data['token'])) {

            $org_id = $this->request->data['org_id'];
            $entity_id = 0;
            $job_title_id = 0;
            $dept_id = 0;
            if (isset($this->request->data['entity_id'])) {
                $entity_id = $this->request->data['entity_id'];
            }
            $userinfo = $this->getuserData($this->request->data['token'], false);
            $user_id = $userinfo["users"]["id"];
            if (isset($this->request->data['department_id'])) {
                $dept_id = $this->request->data['department_id'];
            }
            if (isset($this->request->data['job_title_id'])) {
                $job_title_id = $this->request->data['job_title_id'];
            }
            $this->UserOrganization->updateAll(
                    array('UserOrganization.entity_id' => "'" . $entity_id . "'", 'UserOrganization.department_id' => "'" . $dept_id . "'", 'UserOrganization.job_title_id' => "'" . $job_title_id . "'"), array('UserOrganization.organization_id' => $org_id, 'UserOrganization.user_id' => $user_id)
            );
            $organization = $this->Organization->findById($org_id);
//echo $this->Organization->getLastQuery();die;

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Role for " . $organization['Organization']['name'] . " updated successfully.", 'data' => true),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    /** Added by Babulal Prasad @30MAY2019
     * Post API to add endorsement comments
     * * */
    public function postEndorseComment() {
        if ($this->request->is('post')) {
            $loggedInUser = $this->Auth->user();
            $errorsArray = array();
            $error = false;
//            pr($this->request->data);
//            exit
            $token = $this->request->data['token'];
            $orgId = $this->request->data['org_id'];
            $orgDATA = $this->Organization->findById($orgId);
            $orgName = $orgDATA['Organization']['name'];

            $userinfo = $this->getuserData($token, true);

            $userID = $userinfo['users']['id'];
            $userName = $userinfo['users']['fname'] . " " . $userinfo['users']['lname'];
            $post['user_id'] = $userID;
            $endorsementId = $post['endorsement_id'] = $this->request->data['endorsement_id'];
            $post['comment'] = isset($this->request->data['comment']) ? $this->request->data['comment'] : "";

            $this->EndorsementComment->clear();
            $this->EndorsementComment->set($post);
            $commentData = array();
            if ($this->EndorsementComment->validates()) {
                if ($this->EndorsementComment->save()) {
                    $result = $this->EndorsementComment->find('all', array('fields' => array('user_id', 'endorsement_id', 'UNIX_TIMESTAMP(created) as create_date', 'comment'),
                        'conditions' => array('id' => $this->EndorsementComment->id)));
                    $commentData['EndorsementComment'] = $result[0]['EndorsementComment'];
                    $commentData['EndorsementComment']['create_date'] = $result[0][0]['create_date'];
                    $commentData['EndorsementComment']['created'] = $result[0][0]['create_date'];


                    /*                     * * Increase Comment Count ** */
                    $likearray = array("endorsement_id" => $endorsementId);
                    $this->Post->unbindModel(array('hasMany' => array('PostAttachments')));
                    $postData = $this->Endorsement->findById($endorsementId);
                    $comment_count = $postData['Endorsement']['comments_count'] + 1;
                    $this->Endorsement->id = $endorsementId;
                    $this->Endorsement->savefield("comments_count", $comment_count);
                    $status = true;

                    /* Send Push notification code start */
                    $endorsedId = $postData['Endorsement']['endorsed_id'];
                    $endorsementFor = $postData['Endorsement']['endorsement_for'];
                    $push_notification = 0;
                    if ($endorsementFor == 'user') {
                        $push_notification = 1;
                    }
                    $msg = $userName . " has commented on your nDorsement \n\n in " . $orgName . " Organization";
                    if (isset($push_notification) && $push_notification == 1) {

                        $loginuser = $this->LoginStatistics->find("all", array(
                            "conditions" => array("user_id" => $endorsedId, "live" => 1)
                        ));

                        if (!empty($loginuser[0]["LoginStatistics"]) && $loginuser[0]["LoginStatistics"]["device_id"] != "") {
//                                echo "TEST";
//                                pr($loginuser);
//                                exit;
                            if (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "ios") {
                                //print_r($device_token);
                                $deviceToken_msg_arr = array();
                                $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                $count = 1;
                                //$msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n".$val["EndorseReplies"]["reply"];
                                $msg = $userName . " has commented on your nDorsement \n\n in " . $orgName . " Organization";
                                $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion", "title" => "nDorse App");

                                $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);

                                /** Added by Babulal Prasad @7-june-2019
                                 * * SAVE DATA for alert center feature Start
                                 * ** */
//                                $this->loadModel('AlertCenterNotification');
//                                $AlertCenterNotificationArray['user_id'] = $endorsedId;
//                                $AlertCenterNotificationArray['org_id'] = $orgId;
//                                $AlertCenterNotificationArray['alert_type'] = 'nDorsement comment Notification';
//                                $AlertCenterNotificationArray['plain_msg'] = $msg;
//                                $AlertCenterNotificationArray['original_msg'] = $msg;
//                                $AlertCenterNotificationArray['status'] = 0;
//                                $AlertCenterNotificationArray['os'] = 'ios';
//                                $AlertCenterNotificationArray['feed_id'] = $endorsementId;
//                                $AlertCenterNotificationArray['sender_id'] = $userID;
//
//                                $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                                /* SAVE DATA for alert center feature End** */
//                                    print_r($deviceToken_msg_arr);
//                                    continue;
                                $this->Common->sendPushNotification($deviceToken_msg_arr);
                            } elseif (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "android") {

                                $deviceToken_msg_arr = array();
                                $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                $count = 1;
                                // $end_name = $val['User']['fname'] . " " . $val['User']['lname'];
                                $organization_id = $val['Organization']['orgid'];
                                // $msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n<br />".$val["EndorseReplies"]["reply"];
                                $msg = $userName . " has commented on your nDorsement \n\n in " . $orgName . " Organization";
                                $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion",
                                    "title" => "nDorse App");

                                $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
                                //print_r($deviceToken_msg_arr);
                                $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
                                /** Added by Babulal Prasad @7-june-2018
                                 * * SAVE DATA for alert center feature Start
                                 * ** */
//                                $this->loadModel('AlertCenterNotification');
//                                $AlertCenterNotificationArray['user_id'] = $endorsedId;
//                                $AlertCenterNotificationArray['org_id'] = $orgId;
//                                $AlertCenterNotificationArray['alert_type'] = 'nDorsement comment Notification';
//                                $AlertCenterNotificationArray['plain_msg'] = $msg;
//                                $AlertCenterNotificationArray['original_msg'] = $msg;
//                                $AlertCenterNotificationArray['status'] = 0;
//                                $AlertCenterNotificationArray['os'] = 'android';
//                                $AlertCenterNotificationArray['feed_id'] = $endorsementId;
//                                $AlertCenterNotificationArray['sender_id'] = $userID;
//                                $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                                /* SAVE DATA for alert center feature End** */
                            }
                        }
                    }
                    $this->loadModel('AlertCenterNotification');
                    $AlertCenterNotificationArray['user_id'] = $endorsedId;
                    $AlertCenterNotificationArray['org_id'] = $orgId;
                    $AlertCenterNotificationArray['alert_type'] = 'nDorsement comment Notification';
                    $AlertCenterNotificationArray['plain_msg'] = $msg;
                    $AlertCenterNotificationArray['original_msg'] = $msg;
                    $AlertCenterNotificationArray['status'] = 0;
                    $AlertCenterNotificationArray['os'] = '';
                    $AlertCenterNotificationArray['feed_id'] = $endorsementId;
                    $AlertCenterNotificationArray['feed_type'] = 'ndorse';
                    $AlertCenterNotificationArray['sender_id'] = $userID;
                    $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                    /* Send Push notification code End */

                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Comment posted successfully!", "data" => $commentData),
                        '_serialize' => array('result')
                    ));
                } else {
                    $error = true;
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Unable to Post Comment."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $error = true;
                $errors = $this->EndorsementComment->validationErrors;

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Error!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
            }

            if ($error) {
                return;
            }

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Comment posted successfully!", "data" => $commentData),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    /** Added by Babulal Prasad @12022016
     * Post API
     * * */
    public function postComment() {
        if ($this->request->is('post')) {
            $loggedInUser = $this->Auth->user();
            $errorsArray = array();
            $error = false;
//            pr($this->request->data);
//            pr($loggedInUser);
//            exit;
            $sender_id = $post['user_id'] = $loggedInUser['id'];
            $postId = $post['post_id'] = $this->request->data['post_id'];
            $post['comment'] = isset($this->request->data['comment']) ? $this->request->data['comment'] : "";
            $this->PostComment->clear();
            $this->PostComment->set($post);
            $commentData = array();
            if ($this->PostComment->validates()) {
                if ($this->PostComment->save()) {
                    $result = $this->PostComment->find('all', array('fields' => array('user_id', 'post_id', 'UNIX_TIMESTAMP(created) as create_date', 'comment'),
                        'conditions' => array('id' => $this->PostComment->id)));
                    $commentData['PostComment'] = $result[0]['PostComment'];
                    $commentData['PostComment']['create_date'] = $result[0][0]['create_date'];
                    $commentData['PostComment']['created'] = $result[0][0]['create_date'];


                    /*                     * * Increase Comment Count ** */
                    $likearray = array("post_id" => $postId);
                    $this->Post->unbindModel(array('hasMany' => array('PostAttachments')));
                    $postData = $this->Post->findById($postId);
                    $postUserId = $postData['Post']['user_id'];
                    $comment_count = $postData['Post']['comments_count'] + 1;
                    $this->Post->id = $postId;
                    $this->Post->savefield("comments_count", $comment_count);
                    $status = true;


                    /* Alert Notification added by Babulal Prasad @1-april-2021 */
                    $orgId = $loggedInUser['current_org']['id'];
                    $username = $loggedInUser['fname'] . " " . $loggedInUser['lname'];
                    if ($postUserId != $sender_id) {
                        $msg = $username . " has commented on your post!";
                        $this->loadModel('AlertCenterNotification');
                        $AlertCenterNotificationArray['user_id'] = $postUserId;
                        $AlertCenterNotificationArray['org_id'] = $orgId;
                        $AlertCenterNotificationArray['alert_type'] = 'Post Comment Notification';
                        $AlertCenterNotificationArray['plain_msg'] = $msg;
                        $AlertCenterNotificationArray['original_msg'] = $msg;
                        $AlertCenterNotificationArray['status'] = 0;
                        $AlertCenterNotificationArray['os'] = '';
                        $AlertCenterNotificationArray['feed_id'] = $postId;
                        $AlertCenterNotificationArray['feed_type'] = 'post';
                        $AlertCenterNotificationArray['sender_id'] = $sender_id;
                        $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                    }


                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Comment posted successfully!", "data" => $commentData),
                        '_serialize' => array('result')
                    ));
                } else {
                    $error = true;
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Unable to Post Comment."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $error = true;
                $errors = $this->Post->validationErrors;

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Error!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
            }

            if ($error) {
                return;
            }

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Comment posted successfully!", "data" => $commentData),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function wallpost() {
// @TODO : email and push notification to endorsee

        if ($this->request->is('post')) {
//            pr(json_encode($this->request->data)); exit;
            $loggedInUser = $this->Auth->user();
            $emojisValue = array();
            if (isset($this->request->data['emojis']) && trim($this->request->data['emojis']) != "") {
                $emojisValue = explode(",", $this->request->data['emojis']);
            }

            $errorsArray = array();
            $error = false;
            $emojisValueArray = array();
            $post['user_id'] = $loggedInUser['id'];
            $orgId = $post['organization_id'] = $loggedInUser['current_org']['id'];
            $PostMessage = $post['message'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";

            $subcenterID = $post['subcenter_id'] = (isset($this->request->data['subcenter_id']) && $this->request->data['subcenter_id'] != 0 ) ? $this->request->data['subcenter_id'] : "";

            $postTitle = $post['title'] = isset($this->request->data['title']) ? $this->request->data['title'] : "";
            $post['emojis_count'] = count($emojisValue) ? count($emojisValue) : 0;
//            pr($this->request->data);  exit;
            /*             * * Post Scheduling code begin: Added by Babulal prasad@9dec2017 *** */
            if (isset($this->request->data['endorse_list']) && count($this->request->data['endorse_list']) > 0) {
                $postUserIds = $postDeptIds = $postEnityIds = array();
                $postList = json_decode($this->request->data['endorse_list']);
                foreach ($postList as $postRequest) {
                    if ($postRequest->for == 'user') {
                        $postUserIds[] = $postRequest->id;
                    } else if ($postRequest->for == 'department') {
                        $postDeptIds[] = $postRequest->id;
                    } else if ($postRequest->for == 'entity') {
                        $postEnityIds[] = $postRequest->id;
                    }
                }
            }
            //pr($this->request->data);
            if (isset($this->request->data['post_type']) && $this->request->data['post_type'] == 'postlater') {
                $post['scheduled'] = 1;
                $post['status'] = 2; //Pending status
            }
            $sendPushNotification = 0;
            if (isset($this->request->data['push_notification']) && $this->request->data['push_notification'] == 'active') {
                $post['push_notification'] = 1;
                $sendPushNotification = 1;
            }
            $sendEmailNotification = 0;
            if (isset($this->request->data['email_notification']) && $this->request->data['email_notification'] == 'active') {
                $post['email_notification'] = 1;
                //$sendPushNotification = 1;
                $sendEmailNotification = 1;
            }

//            pr($post);
//            exit;
            /*             * * Post Scheduling code end *** */

            if (isset($this->request->data['post_type']) && $this->request->data['post_type'] == 'postlater') {
                if (isset($this->request->data['post_date']) && $this->request->data['post_date'] != '') {
                    if (isset($this->request->data['post_time']) && $this->request->data['post_time'] != '') {
                        $datetimeToSave = $this->Common->daterangeAndTimeToSQL($this->request->data['post_date'], $this->request->data['post_time']);
                        $usertimzone = 'UTC';
                        if (isset($this->request->data['usertimzone']) && $this->request->data['usertimzone'] != '') {
                            $usertimzone = $this->request->data['usertimzone'];
                        }
                        $UTCTimeToPost = $this->Common->ConvertOneTimezoneToAnotherTimezone($datetimeToSave, $usertimzone, 'UTC');
                    }
                }
                $post['post_publish_date'] = $UTCTimeToPost;
            } else {
                $post['post_publish_date'] = date("Y-m-d H:i:s", time());
            }
//            pr($post); exit;
            $this->Post->clear();
            $this->Post->set($post);
            if ($this->Post->validates()) {
                if ($this->Post->save()) {

                    $postUserArray = array();
                    $postUserArray[] = $loggedInUser['id'];
                    $postId = $this->Post->id;
                    $feedTrans['FeedTran']['feed_id'] = $postId;
                    $feedTrans['FeedTran']['org_id'] = $loggedInUser['current_org']['id'];
                    $feedTrans['FeedTran']['user_id'] = json_encode($postUserArray);
                    $feedTrans['FeedTran']['feed_type'] = 'post';
                    $feedTrans['FeedTran']['subcenter_for'] = $subcenterID;
                    $feedTrans['FeedTran']['subcenter_by'] = $subcenterID;

                    /* Post Scheduling code begin: Added by Babulal prasad@9dec2017 *** */
                    if (isset($this->request->data['post_type']) && $this->request->data['post_type'] == 'postlater') {
                        $feedTrans['FeedTran']['status'] = 2;
                    }
                    $postVisibility = 0;
                    if (isset($this->request->data['endorse_list']) && count($this->request->data['endorse_list']) > 0) {
                        $postVisibility = 1;
                        $feedTrans['FeedTran']['visibility_check'] = 1;

                        if (isset($postUserIds) && count($postUserIds) > 0) {
                            $feedTrans['FeedTran']['visible_user_ids'] = json_encode($postUserIds);
                        }
                        if (isset($postDeptIds) && count($postDeptIds) > 0) {
                            $feedTrans['FeedTran']['visible_dept'] = json_encode($postDeptIds);
                        }
                        if (isset($postEnityIds) && count($postEnityIds) > 0) {
                            $feedTrans['FeedTran']['visible_sub_org'] = json_encode($postEnityIds);
                        }
                    } else {
                        //Nothing to do Code implemeted in Cron controller to send notification to all user of organization
                    }
                    /*  Post schedule end* */

                    /* Saving data into post schedule table start added by Babulal Prasad @11-dec-2017**** */
                    if (isset($this->request->data['post_type']) && $this->request->data['post_type'] == 'postlater') {
                        $PostSchedule['PostSchedule']['post_id'] = $postId;
                        if (isset($this->request->data['post_date']) && $this->request->data['post_date'] != '') {
                            $dateToSave = $this->Common->daterangeToSQL($this->request->data['post_date']);
                            if (isset($this->request->data['post_time']) && $this->request->data['post_time'] != '') {
                                $datetimeToSave = $this->Common->daterangeAndTimeToSQL($this->request->data['post_date'], $this->request->data['post_time']);
                                $timeToSave = $this->request->data['post_time'] . ":00";
                                $usertimzone = 'UTC';
                                if (isset($this->request->data['usertimzone']) && $this->request->data['usertimzone'] != '') {
                                    $usertimzone = $this->request->data['usertimzone'];
                                }
                                $UTCTimeToPost = $this->Common->ConvertOneTimezoneToAnotherTimezone($datetimeToSave, $usertimzone, 'UTC');
                            }
                            $PostSchedule['PostSchedule']['date'] = $dateToSave;
                            $PostSchedule['PostSchedule']['time'] = $timeToSave;
                            $PostSchedule['PostSchedule']['datetime'] = $datetimeToSave;
                            $PostSchedule['PostSchedule']['utc_post_datetime'] = $UTCTimeToPost;
                            $feedTrans['FeedTran']['publish_date'] = $datetimeToSave;
                        }

                        //pr($PostSchedule); //exit;
                        $dateDATA = $this->PostSchedule->save($PostSchedule);
                        //pr($dateDATA);
                    } else {
                        $feedTrans['FeedTran']['publish_date'] = date("Y-m-d H:i:s", time());
//                        pr($feedTrans); exit;
                        /*                         * ** */
                        if ($postVisibility == 1) { //Check visible selected users
                            $userIds = $postUserIds;
                            if (isset($userIds) && count($userIds) > 0) {
                                foreach ($userIds as $userid) {
                                    $userList[$userid] = $userid;
                                }
                            }
                            $deptIds = $postDeptIds;
                            if (isset($deptIds) && count($deptIds) > 0) {
                                foreach ($deptIds as $index => $deptID) {
                                    $deptArray[$deptID] = $deptID;
                                    $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                                    $userIDbyDept = $this->UserOrganization->find('all', array("fields" => array("UserOrganization.user_id"), "conditions" => array("department_id" => $deptID, "organization_id" => $orgId, 'status' => 1)));
                                    if (isset($userIDbyDept) && count($userIDbyDept) > 0) {
                                        foreach ($userIDbyDept as $index => $userOrgData) {
//                                pr($userOrgData['UserOrganization']['user_id']);
                                            $userList[$userOrgData['UserOrganization']['user_id']] = $userOrgData['UserOrganization']['user_id'];
                                        }
                                    }
                                }
                            }

                            $subOrgIds = $postEnityIds;
                            if (isset($subOrgIds) && count($subOrgIds) > 0) {
                                foreach ($subOrgIds as $index => $subOrgID) {
                                    $subOrgArray[$subOrgID] = $subOrgID;
                                    $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                                    $userIDbySubOrg = $this->UserOrganization->find('all', array("fields" => array("UserOrganization.user_id"), "conditions" => array("entity_id" => $subOrgID, "organization_id" => $orgId, 'status' => 1)));
                                    if (isset($userIDbySubOrg) && count($userIDbySubOrg) > 0) {
                                        foreach ($userIDbySubOrg as $index => $userOrgData) {
//                                pr($userOrgData['UserOrganization']['user_id']);
                                            $userList[$userOrgData['UserOrganization']['user_id']] = $userOrgData['UserOrganization']['user_id'];
                                        }
                                    }
                                }
                            }
//                echo "<br>ORG : " . $orgId;
//                echo "<br>USER ID : " . $user_id;
//                echo "<br>PostScheduleID: " . $PostScheduleID;

                            $orgDATA = $this->Organization->findById($orgId);
                            $orgName = $orgDATA['Organization']['name'];
                            $userDATA = $this->User->findById($loggedInUser['id']);
                            $userName = $userDATA['User']['fname'] . " " . $userDATA['User']['lname'];
                            $PostTitle = $postTitle;
                            $PostMessage = $PostMessage;
                            $push_notification = $sendPushNotification;

//                            pr($userList);
                            //                            exit;
//                            echo $push_notification; exit;
                            if (isset($push_notification) && $push_notification == 1) {

                                if (isset($userList) && count($userList) > 0) {
                                    foreach ($userList as $userID) {



                                        $loginuser = $this->LoginStatistics->find("all", array(
                                            "conditions" => array("user_id" => $userID, "live" => 1)
                                        ));


                                        if (!empty($loginuser[0]["LoginStatistics"]) && $loginuser[0]["LoginStatistics"]["device_id"] != "") {
//                                echo "TEST";
//                                pr($loginuser);
//                                exit;
                                            if (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "ios") {
                                                //print_r($device_token);
                                                $deviceToken_msg_arr = array();
                                                $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                                $count = 1;
                                                //$msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n".$val["EndorseReplies"]["reply"];
                                                $msg = $userName . " has posted a post titled : " . $PostTitle . " \n\n in " . $orgName . " Organization";
                                                $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion", "title" => "nDorse App");

                                                $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);

                                                /** Added by Babulal Prasad @7-june-2018
                                                 * * SAVE DATA for alert center feature Start
                                                 * ** */
                                                $this->loadModel('AlertCenterNotification');
                                                $AlertCenterNotificationArray['user_id'] = $userID;
                                                $AlertCenterNotificationArray['org_id'] = $orgId;
                                                $AlertCenterNotificationArray['alert_type'] = 'Post Notification';
                                                $AlertCenterNotificationArray['plain_msg'] = $msg;
                                                $AlertCenterNotificationArray['original_msg'] = $msg;
                                                $AlertCenterNotificationArray['status'] = 0;
                                                $AlertCenterNotificationArray['os'] = 'ios';
                                                $AlertCenterNotificationArray['feed_id'] = $postId;
                                                $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                                                /* SAVE DATA for alert center feature End** */
//                                    print_r($deviceToken_msg_arr);
//                                    continue;
                                                $this->Common->sendPushNotification($deviceToken_msg_arr);
                                            } elseif (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "android") {

                                                $deviceToken_msg_arr = array();
                                                $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                                $count = 1;
                                                // $end_name = $val['User']['fname'] . " " . $val['User']['lname'];
                                                $organization_id = $val['Organization']['orgid'];
                                                // $msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n<br />".$val["EndorseReplies"]["reply"];
                                                $msg = $userName . " has posted a post titled : " . $PostTitle . " \n\n in " . $orgName . " Organization";
                                                $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion",
                                                    "title" => "nDorse App");

                                                $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
                                                //print_r($deviceToken_msg_arr);
                                                $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
                                                /** Added by Babulal Prasad @7-june-2018
                                                 * * SAVE DATA for alert center feature Start
                                                 * ** */
                                                $this->loadModel('AlertCenterNotification');
                                                $AlertCenterNotificationArray['user_id'] = $userID;
                                                $AlertCenterNotificationArray['org_id'] = $orgId;
                                                $AlertCenterNotificationArray['alert_type'] = 'Post Notification';
                                                $AlertCenterNotificationArray['plain_msg'] = $msg;
                                                $AlertCenterNotificationArray['original_msg'] = $msg;
                                                $AlertCenterNotificationArray['status'] = 0;
                                                $AlertCenterNotificationArray['os'] = 'android';
                                                $AlertCenterNotificationArray['feed_id'] = $postId;
                                                $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                                                /* SAVE DATA for alert center feature End** */
                                            }
                                        }
                                    }
                                }
                            }


                            /* Send email notificatin to user on post publish start* */
                            $emailArray = array();
                            $email_notification = $sendEmailNotification;
                            if (isset($email_notification) && $email_notification == 1) {
                                if (isset($userList) && count($userList) > 0) {
                                    foreach ($userList as $userID) {
                                        $userData = $this->User->findById($userID);
                                        $emailArray[$userID] = $userData['User']['email'];
                                    }
                                }
//                    pr($emailArray);

                                if (!empty($emailArray)) {
                                    $emailQueue = array();
                                    $subject = "nDorse notification -- New Post Submitted";
                                    foreach ($emailArray as $uID => $userEmail) {
                                        if ($userEmail != '') {

                                            $viewVars = array();
                                            $viewVars = array("org_name" => $orgName, "post_title" => $PostTitle, 'post_message' => $PostMessage);

                                            /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                                            $userIdEncrypted = base64_encode($uID);
                                            $rootUrl = Router::url('/', true);
                                            $rootUrl = str_replace("http", "https", $rootUrl);
                                            $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                                            $viewVars["pathToRender"] = $pathToRender;
                                            /*                                             * * */
                                            $configVars = serialize($viewVars);
                                            $emailQueue[] = array("to" => $userEmail, "subject" => $subject, "config_vars" => $configVars, "template" => "post_notification");
                                        }
                                    }
//                        pr($emailQueue);
                                    if (!empty($emailQueue)) {
                                        $this->Email->saveMany($emailQueue);
                                    }
                                }
                            }/* Send email notificatin to user on post publish END* */
                        } else {//Show to all users of organization
                            //GET ALL USERS OF ORGANIZATION TO SEND PUSH NOTIFICATION
                            $this->DefaultOrg->unbindModel(array("belongsTo" => array("Organization", "User")));
                            $userIDsOfOrg = $this->DefaultOrg->find('all', array("fields" => array("DefaultOrg.user_id"), "conditions" => array("DefaultOrg.organization_id" => $orgId, 'DefaultOrg.status' => 1)));
//                            pr($userIDsOfOrg); exit;
                            $userList = array();
                            if (isset($userIDsOfOrg) && !empty($userIDsOfOrg)) {
                                foreach ($userIDsOfOrg as $index => $userDATAa) {
                                    $userList[$userDATAa['DefaultOrg']['user_id']] = $userDATAa['DefaultOrg']['user_id'];
                                }

                                if (isset($sendPushNotification) && $sendPushNotification == 1) {

                                    $orgDATA = $this->Organization->findById($orgId);
                                    $orgName = $orgDATA['Organization']['name'];
                                    $userDATA = $this->User->findById($loggedInUser['id']);
                                    $userName = $userDATA['User']['fname'] . " " . $userDATA['User']['lname'];
                                    $PostTitle = $postTitle;

                                    if (isset($userList) && count($userList) > 0) {
                                        foreach ($userList as $userID) {
                                            $loginuser = $this->LoginStatistics->find("all", array(
                                                "conditions" => array("user_id" => $userID, "live" => 1)
                                            ));

                                            if (!empty($loginuser[0]["LoginStatistics"]) && $loginuser[0]["LoginStatistics"]["device_id"] != "") {
//                                echo "TEST";
//                                pr($loginuser);
//                                exit;
                                                if (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "ios") {
                                                    //print_r($device_token);
                                                    $deviceToken_msg_arr = array();
                                                    $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                                    $count = 1;
                                                    //$msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n".$val["EndorseReplies"]["reply"];
                                                    $msg = $userName . " has posted a post titled : " . $PostTitle . " \n\n in " . $orgName . " Organization";
                                                    $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion", "title" => "nDorse App");

                                                    $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
//                                                    pr($deviceToken_msg_arr);
//                                                    exit;
                                                    /** Added by Babulal Prasad @7-june-2018
                                                     * * SAVE DATA for alert center feature Start
                                                     * ** */
                                                    $this->loadModel('AlertCenterNotification');
                                                    $AlertCenterNotificationArray['user_id'] = $userID;
                                                    $AlertCenterNotificationArray['org_id'] = $orgId;
                                                    $AlertCenterNotificationArray['alert_type'] = 'Post Notification';
                                                    $AlertCenterNotificationArray['plain_msg'] = $msg;
                                                    $AlertCenterNotificationArray['original_msg'] = $msg;
                                                    $AlertCenterNotificationArray['status'] = 0;
                                                    $AlertCenterNotificationArray['os'] = 'ios';
                                                    $AlertCenterNotificationArray['feed_id'] = $postId;
                                                    $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                                                    /* SAVE DATA for alert center feature End** */
                                                    $this->Common->sendPushNotification($deviceToken_msg_arr);
                                                } elseif (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "android") {

                                                    $deviceToken_msg_arr = array();
                                                    $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                                    $count = 1;
                                                    // $end_name = $val['User']['fname'] . " " . $val['User']['lname'];
                                                    $organization_id = $orgId;
                                                    // $msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n<br />".$val["EndorseReplies"]["reply"];
                                                    $msg = $userName . " has posted a post titled : " . $PostTitle . " \n\n in " . $orgName . " Organization";
                                                    $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion",
                                                        "title" => "nDorse App");

                                                    $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
                                                    //print_r($deviceToken_msg_arr);
                                                    $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
                                                    /** Added by Babulal Prasad @7-june-2018
                                                     * * SAVE DATA for alert center feature Start
                                                     * ** */
                                                    $this->loadModel('AlertCenterNotification');
                                                    $AlertCenterNotificationArray['user_id'] = $userID;
                                                    $AlertCenterNotificationArray['org_id'] = $orgId;
                                                    $AlertCenterNotificationArray['alert_type'] = 'Post Notification';
                                                    $AlertCenterNotificationArray['plain_msg'] = $msg;
                                                    $AlertCenterNotificationArray['original_msg'] = $msg;
                                                    $AlertCenterNotificationArray['status'] = 0;
                                                    $AlertCenterNotificationArray['os'] = 'android';
                                                    $AlertCenterNotificationArray['feed_id'] = $postId;
                                                    $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                                                    /* SAVE DATA for alert center feature End** */
                                                }
                                            }
                                        }

//                                        exit;

                                        /* Send email notificatin to user on post publish start* */
                                        $emailArray = array();
                                        $email_notification = $sendEmailNotification;
                                        if (isset($email_notification) && $email_notification == 1) {
                                            if (isset($userList) && count($userList) > 0) {
                                                foreach ($userList as $userID) {
                                                    $userData = $this->User->findById($userID);
                                                    if (isset($userData['User']['email']) && $userData['User']['email'] != '') {
                                                        $emailArray[$userID] = $userData['User']['email'];
                                                    }
                                                }
                                            }
//                    pr($emailArray);exit;

                                            if (!empty($emailArray)) {
                                                $emailQueue = array();
                                                $subject = "nDorse notification -- New Post Submitted";
                                                foreach ($emailArray as $uID => $userEmail) {
                                                    if ($userEmail != '') {

                                                        $viewVars = array();
                                                        $viewVars = array("org_name" => $orgName, "post_title" => $PostTitle, 'post_message' => $PostMessage);

                                                        /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                                                        $userIdEncrypted = base64_encode($uID);
                                                        $rootUrl = Router::url('/', true);
                                                        $rootUrl = str_replace("http", "https", $rootUrl);
                                                        $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                                                        $viewVars["pathToRender"] = $pathToRender;
                                                        /*                                                         * * */
                                                        $configVars = serialize($viewVars);
                                                        $emailQueue[] = array("to" => $userEmail, "subject" => $subject, "config_vars" => $configVars, "template" => "post_notification");
                                                    }
                                                }
//                        pr($emailQueue);
                                                if (!empty($emailQueue)) {
                                                    $this->Email->saveMany($emailQueue);
                                                }
                                            }
                                        }/* Send email notificatin to user on post publish END* */
                                    }
                                }
                                sleep(5);
                            }
                        }
                    }

                    /* Saving data into post schedule table end**** */

//                    pr($feedTrans); exit;
                    $this->FeedTran->save($feedTrans);



                    foreach ($emojisValue as $emojis_value) {
                        $emojisValueArray[] = array("post_id" => $this->Post->id, "name" => $emojis_value, "type" => "emojis");
                    }
                } else {
                    $error = true;
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Unable to Post."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $error = true;
                $errors = $this->Post->validationErrors;

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Error!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));

//break;
            }
//}

            if ($error) {
                return;
            }

            if (!empty($emojisValueArray)) {

                $this->PostAttachment->saveMany($emojisValueArray);
            }

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Post submitted!", "data" => array('post_id' => $postId)),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function saveWallpostAttachment() {
        if ($this->request->is('post')) {
            $loggedInUser = $this->Auth->user();

//            $endorsementIds = explode(",", $this->request->data['post_id']);
            $postId = $this->request->data['post_id'];

            if ($this->request->data['type'] == "image") {
                $this->request->data['image'] = $this->request->data['attachment'];

                $this->PostAttachment->set($this->request->data);
                if ($this->PostAttachment->validates()) {
                    $uploadPath = POST_IMAGE_DIR;

                    $imageExtension = $this->PostAttachment->data['PostAttachment']['file_extension'];
                    $imageData = $this->PostAttachment->data['PostAttachment']['imageData'];

                    $imageName = $this->request->data['post_id'];
                    $imageName = $imageName . "_" . time() . "." . $imageExtension;
                    $imageName = $this->Common->getUploadFilename($uploadPath, $imageName);

                    if ($this->Common->uploadApiImage($uploadPath, $imageName, $imageData)) {
//$this->request->data['name'] = $imageName;
                        unset($this->PostAttachment->validate['image']);
//

                        $postAttachments[] = array("post_id" => $postId, "type" => $this->request->data['type'], "name" => $imageName);
// check if image_count = 0 then update image_count flag

                        if ($this->PostAttachment->saveMany($postAttachments)) {
                            $imagepost = 0;
                            $params = array();
                            $params['fields'] = "count(id) as total_images";
                            $params['conditions'] = array("post_id" => $postId, 'type' => 'image');
                            $imagepost = $this->PostAttachment->find("all", $params);
                            if (isset($imagepost[0][0]['total_images'])) {
                                $totalImages = $imagepost[0][0]['total_images'];
                                $this->Post->id = $postId;
                                $this->Post->savefield("image_count", $totalImages);
                            }

                            $postAttachmentId = $this->PostAttachment->id;

//                            $postTrans['PostTrans']['post_id'] = $postId;
//                            $postTrans['PostTrans']['post_attachment_id'] = $postAttachmentId;
//                            $this->PostTrans->save($postTrans);

                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "Attachments sent successfully."),
                                '_serialize' => array('result')
                            ));
                        } else {
                            $this->set(array(
                                'result' => array("status" => false
                                    , "msg" => "Attachment failed due to server error! Please try nDorsement later or without attachment."),
                                '_serialize' => array('result')
                            ));
                        }
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "Attachment failed due to server error! Please try nDorsement later or without attachment."),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $errors = $this->PostAttachment->validationErrors;

                    $errorsArray = array();

                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }


                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Error!", 'errors' => $errorsArray),
                        '_serialize' => array('result')
                    ));
                }
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function saveWallpostAttachmentFiles() {
        if ($this->request->is('post')) {
//            pr($this->request->data);
//            exit;
            $loggedInUser = $this->Auth->user();
            $postId = $this->request->data['post_id'];
            if ($this->request->data['type'] == "files") {
                $this->PostAttachment->set($this->request->data);
                if ($this->PostAttachment->validates()) {
                    if (isset($this->request->data['fileName']) && $this->request->data['fileName'] != '') {
                        $fileData = array();
                        $fileData['url'] = $this->request->data['fileName'];
                        $fileData['name'] = $this->request->data['originFileName'];
                        $fileData['type'] = $this->request->data['file_type'];
                        $postAttachments[] = array("post_id" => $postId, "type" => "files", "name" => json_encode($fileData));

                        if ($this->PostAttachment->saveMany($postAttachments)) {
                            $postAttachmentId = $this->PostAttachment->id;
                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "Attachments sent successfully."),
                                '_serialize' => array('result')
                            ));
                        } else {
                            $this->set(array(
                                'result' => array("status" => false
                                    , "msg" => "Attachment failed due to server error! Please try nDorsement later or without attachment."),
                                '_serialize' => array('result')
                            ));
                        }
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "Error!", 'errors' => "file name empty"),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $errors = $this->PostAttachment->validationErrors;
                    $errorsArray = array();
                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Error!", 'errors' => $errorsArray),
                        '_serialize' => array('result')
                    ));
                }
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function saveEndorseAttachmentFiles() {
        if ($this->request->is('post')) {
//            pr($this->request->data);
//            exit;
            $loggedInUser = $this->Auth->user();
            $nDorsementId = $this->request->data['endorsement_ids'];
            if ($this->request->data['type'] == "files") {
                $this->EndorseAttachment->set($this->request->data);
                if ($this->EndorseAttachment->validates()) {
                    if (isset($this->request->data['fileName']) && $this->request->data['fileName'] != '') {
                        $fileData = array();
                        $fileData['url'] = $this->request->data['fileName'];
                        $fileData['name'] = $this->request->data['originFileName'];
                        $fileData['type'] = $this->request->data['file_type'];
                        $postAttachments[] = array("endorsement_id" => $nDorsementId, "type" => "files", "name" => json_encode($fileData));

                        if ($this->EndorseAttachment->saveMany($postAttachments)) {
                            $postAttachmentId = $this->EndorseAttachment->id;
                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "Attachments sent successfully."),
                                '_serialize' => array('result')
                            ));
                        } else {
                            $this->set(array(
                                'result' => array("status" => false
                                    , "msg" => "Attachment failed due to server error! Please try nDorsement later or without attachment."),
                                '_serialize' => array('result')
                            ));
                        }
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "Error!", 'errors' => "file name empty"),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $errors = $this->PostAttachment->validationErrors;
                    $errorsArray = array();
                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Error!", 'errors' => $errorsArray),
                        '_serialize' => array('result')
                    ));
                }
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function endorse() {
// @TODO : email and push notification to endorsee

        if ($this->request->is('post')) {
            $loggedInUser = $this->Auth->user();
//            pr($this->request->data); exit;
            $endorseList = json_decode($this->request->data['endorse_list']);
            $subcenterForArray = array();

            if (isset($this->request->data['subcenter_for']) && $this->request->data['subcenter_for'] != '') {
                $subcenterForArray = json_decode($this->request->data['subcenter_for'], true);
            }


            if (isset($this->request->data['hashtags']) && $this->request->data['hashtags'] != '') {
                $hashtagsArray = json_decode($this->request->data['hashtags'], true);
            }

            $hashtagsJSON = "";
            if (!empty($hashtagsArray)) {
                $hashtagsJSON = json_encode($hashtagsArray);
            }
            if (isset($subcenterForArray) && !empty($subcenterForArray)) {
                $subcenterForArray = $subcenterForArray[0];
            }
//            pr($subcenterForArray); exit;
//            pr($subcenterForArray);exit;
//Get list of eval and inactive users
            $endorsedUserIds = array();

            foreach ($endorseList as $endorseRequest) {
                if ($endorseRequest->for == 'user') {
                    $endorsedUserIds[] = $endorseRequest->id;
                }
            }

            $params = array();
            $conditions = array();
            $conditions['UserOrganization.user_id'] = $endorsedUserIds;
            $conditions['UserOrganization.status !='] = 1;
            $conditions['UserOrganization.organization_id'] = $loggedInUser['current_org']['id'];

            $params['conditions'] = $conditions;

            $endorsedInactiveUsersList = $this->UserOrganization->find("all", $params);

            $inactiveUserIds = array();

            foreach ($endorsedInactiveUsersList as $inactiveUser) {
                $inactiveUserIds[] = $inactiveUser['UserOrganization']['user_id'];
            }

            $endorsementIds = array();
            $coreValues = explode(",", $this->request->data['core_values']);

            $emojisValue = array();
            if (isset($this->request->data['emojis']) && trim($this->request->data['emojis']) != "") {
                $emojisValue = explode(",", $this->request->data['emojis']);
            }

            /*             * *added code by Babulal Prasad for Bitmojis  @27-july-2018** */
            $bitmojisValue = array();
            if (isset($this->request->data['bitmojis']) && trim($this->request->data['bitmojis']) != "") {
                $bitmojisValue = explode(",", $this->request->data['bitmojis']);
            }

            $errorsArray = array();
            $error = false;
            $coreValueArray = array();
            $emojisValueArray = array();
            $bitmojisValueArray = array();
            $subject = $loggedInUser['fname'] . " " . $loggedInUser['lname'] . " has endorsed you.";
            $endorseeIds = array();
            $emailUserIds = array();
            $endorsementID = 0;

            $userSubcenterID = $this->Common->getUserSubcenter($loggedInUser['id'], $loggedInUser['current_org']['id']);

            foreach ($endorseList as $endorseRequest) {
                $endorseRequest->for = strtolower($endorseRequest->for);
                $endorser_id = $endorsement['endorser_id'] = $loggedInUser['id'];
                $endorsement['organization_id'] = $loggedInUser['current_org']['id'];
                $endorsed_id = $endorsement['endorsed_id'] = $endorseRequest->id;
                $endorse_type = $endorsement['endorsement_for'] = strtolower($endorseRequest->for);
                $endorseTypeLiveFeed = $endorsement['type'] = isset($this->request->data['type']) ? $this->request->data['type'] : "";
                $endorsement['message'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
                $endorsement['emojis_count'] = count($emojisValue) ? 1 : 0;
                $endorsement['bitmojis_count'] = count($bitmojisValue) ? 1 : 0;
                $endorsement['subcenter_by'] = $userSubcenterID;


                $endorsement['hashtags'] = $hashtagsJSON;



                $subcenterForID = "";
                if (isset($subcenterForArray) && !empty($subcenterForArray)) {
                    if (isset($subcenterForArray[$endorsed_id]) && $subcenterForArray[$endorsed_id] != '') {
                        $subcenterForID = $subcenterForArray[$endorsed_id];
                    }
                }
                $endorsement['subcenter_for'] = $subcenterForID;

//                pr($endorsement); exit;

                if ($endorseRequest->for == 'department' || $endorseRequest->for == 'entity' || ($endorseRequest->for == 'user' && in_array($endorseRequest->id, $inactiveUserIds))) {
                    $endorsement['email_sent'] = 2;
                } else {
                    $endorsement['email_sent'] = 1;
                    $emailUserIds[] = $endorseRequest->id;
                }

                $this->Endorsement->clear();
                $this->Endorsement->set($endorsement);
                if ($this->Endorsement->validates()) {
                    if ($this->Endorsement->save()) {
                        $endorsementIds[] = $this->Endorsement->id;
                        $endorsed_user_id = array();
                        /* Save data in Feeds table Start Added by Babulal Prasad @03012017*** */
                        $endorsementID = $endorseId = $this->Endorsement->id;
                        $this->FeedTran->clear();
                        $feedTrans['FeedTran']['feed_id'] = $endorseId;
                        $feedTrans['FeedTran']['org_id'] = $loggedInUser['current_org']['id'];
                        $feedTrans['FeedTran']['feed_type'] = 'endorse';
                        $feedTrans['FeedTran']['endorse_type'] = $endorseTypeLiveFeed;

                        $feedTrans['FeedTran']['subcenter_by'] = ($userSubcenterID == 0) ? '' : $userSubcenterID;
                        $feedTrans['FeedTran']['subcenter_for'] = $subcenterForID;

                        $endorsed_user_id[] = $endorser_id;
                        if ($endorse_type == 'department' || $endorse_type == 'entity') {
                            $feedTrans['FeedTran']['dept_id'] = $endorsed_id;
                        } else if ($endorse_type == 'user') {
                            $endorsed_user_id[] = $endorsed_id;
                            $feedTrans['FeedTran']['dept_id'] = 0;
                        }
                        $feedTrans['FeedTran']['user_id'] = json_encode($endorsed_user_id);
                        $this->FeedTran->save($feedTrans);
                        /* Save data in Feeds table END*** */

                        foreach ($coreValues as $core_value) {
                            if (trim($core_value) != "") {
                                $coreValueArray[] = array("endorsement_id" => $this->Endorsement->id, "value_id" => $core_value);
                            }
                        }

                        if (!empty($hashtagsArray)) {
                            foreach ($hashtagsArray as $hashtagID) {
                                $HashTagsArr[] = array("endorsement_id" => $this->Endorsement->id, "hashtag_id" => $hashtagID);
                            }
                        }

                        foreach ($emojisValue as $emojis_value) {
                            $emojisValueArray[] = array("endorsement_id" => $this->Endorsement->id, "name" => $emojis_value, "type" => "emojis");
                        }
                        foreach ($bitmojisValue as $bitmojis_value) {
                            $bitmojisValueArray[] = array("endorsement_id" => $this->Endorsement->id, "name" => $bitmojis_value, "type" => "bitmojis");
                        }
                    } else {
                        $error = true;
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "Unable to nDorse."),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $error = true;
                    $errors = $this->Endorsement->validationErrors;



                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }

                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Error!", 'errors' => $errorsArray),
                        '_serialize' => array('result')
                    ));

                    break;
                }
            }

            if ($error) {
                return;
            }

            if (!empty($coreValueArray)) {
                $this->EndorseCoreValue->saveMany($coreValueArray);
            }
            if (!empty($HashTagsArr)) {
                $this->EndorseHashtag->saveMany($HashTagsArr);
            }
            if (!empty($emojisValueArray)) {

                $this->EndorseAttachment->saveMany($emojisValueArray);
            }
            if (!empty($bitmojisValueArray)) {

                $this->EndorseAttachment->saveMany($bitmojisValueArray);
            }

//Save endorse emails to email table
            $params = array();
            $params['fields'] = array("*");
            $params['conditions'] = array("User.id" => $emailUserIds);
            $params['joins'] = array(
                array('table' => "login_statistics",
                    "alias" => "LoginStatistics",
                    "type" => "LEFT",
                    'conditions' => array(
                        'LoginStatistics.user_id =User.id AND LoginStatistics.live =1'
                    )
                )
            );
            //pr($params);
            $emailUsers = $this->User->find("all", $params);
            //echo $this->User->getLastQuery();
            $emailQueue = array();
            $endorserID = $loggedInUser['id'];
            $endorserName = $loggedInUser['fname'] . " " . $loggedInUser['lname'];
            $configVars = array("endorser_name" => $endorserName);
//            pr($emailUsers); exit;
            foreach ($emailUsers as $index => $user) {
                if (is_array($user["LoginStatistics"]) && !empty($user["LoginStatistics"])) {
//                    pr($user["LoginStatistics"]);
                    $deviceToken_msg_arr = array();
                    $token = $user["LoginStatistics"]["device_id"];
                    $count = 1;
                    if ($this->request->data['type'] == "anonymous") {
                        $endorserName = "anonymously";
                    }
//Hi <endorsed name>,  You were ndorsed by <endorser name> from <organizaion name>. 
                    $msg = 'Hi ' . ucfirst($user['User']['fname']) . ", You were nDorsed by " . $endorserName . " from " . $loggedInUser['current_org']["name"] . ".";
                    $notificationMsg = 'You have recieved an nDorsement from ' . $endorserName;
                    $parameter = array("org_id" => $loggedInUser['current_org']['id'], "category" => "SwitchAction", "notification_type" => "post_promotion",
                        "title" => "nDorse App");

                    $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'original_msg' => $msg, 'msg' => $msg, "data" => $parameter);
                    if (strtolower($user["LoginStatistics"]["os"]) == "ios") {
//print_r($device_token);
//print_r($deviceToken_msg_arr);
                        //Uncommented on 1-12-2017 as per discussion with Rohan - Done by Babulal
                        $this->Common->sendPushNotification($deviceToken_msg_arr);
                        /** Added by Babulal Prasad @7-june-2018
                         * * SAVE DATA for alert center feature Start
                         * ** */
//                        $this->loadModel('AlertCenterNotification');
//                        $AlertCenterNotificationArray['user_id'] = $user['User']['id'];
//                        $AlertCenterNotificationArray['org_id'] = $loggedInUser['current_org']['id'];
//                        $AlertCenterNotificationArray['alert_type'] = 'nDorse Notification';
//                        $AlertCenterNotificationArray['plain_msg'] = $notificationMsg;
//                        $AlertCenterNotificationArray['original_msg'] = $notificationMsg;
//                        $AlertCenterNotificationArray['status'] = 0;
//                        $AlertCenterNotificationArray['os'] = 'ios';
//                        $AlertCenterNotificationArray['feed_id'] = $endorsementID;
//                        $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                        /* SAVE DATA for alert center feature End** */
                    } elseif (strtolower($user["LoginStatistics"]["os"]) == "android") {
                        //Uncommented on 1-12-2017 as per discussion with Rohan - Done by Babulal
//                        pr($deviceToken_msg_arr);
//                        echo "deviceToken_msg_arr => ". $deviceToken_msg_arr;
                        $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
//                        exit;
                        /** Added by Babulal Prasad @7-june-2018
                         * * SAVE DATA for alert center feature Start
                         * ** */
//                        $this->loadModel('AlertCenterNotification');
//                        $AlertCenterNotificationArray['user_id'] = $user['User']['id'];
//                        $AlertCenterNotificationArray['org_id'] = $loggedInUser['current_org']['id'];
//                        $AlertCenterNotificationArray['alert_type'] = 'nDorse Notification';
//                        $AlertCenterNotificationArray['plain_msg'] = $notificationMsg;
//                        $AlertCenterNotificationArray['original_msg'] = $notificationMsg;
//                        $AlertCenterNotificationArray['status'] = 0;
//                        $AlertCenterNotificationArray['os'] = 'android';
//                        $AlertCenterNotificationArray['feed_id'] = $endorsementID;
//                        $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                        /* SAVE DATA for alert center feature End** */
                    }
                }

                $this->loadModel('AlertCenterNotification');
                $AlertCenterNotificationArray['user_id'] = $user['User']['id'];
                $AlertCenterNotificationArray['org_id'] = $loggedInUser['current_org']['id'];
                $AlertCenterNotificationArray['alert_type'] = 'nDorse Notification';
                $AlertCenterNotificationArray['plain_msg'] = $notificationMsg;
                $AlertCenterNotificationArray['original_msg'] = $notificationMsg;
                $AlertCenterNotificationArray['status'] = 0;
                $AlertCenterNotificationArray['os'] = '';
                $AlertCenterNotificationArray['feed_id'] = $endorsementID;
                $AlertCenterNotificationArray['feed_type'] = 'ndorse';
                $AlertCenterNotificationArray['sender_id'] = $endorserID;
                $this->AlertCenterNotification->save($AlertCenterNotificationArray);

                //sleep(4);

                $subject = "nDorsement Notification";
                $configVars['for'] = "user";
                $configVars["first_name"] = $user['User']['fname'];
                $configVars["username"] = $user['User']['username'];
                $sso_user = 0;
                if (isset($loggedInUser['current_org']['id']) && ($loggedInUser['current_org']['id'] == 415 || $loggedInUser['current_org']['id'] == 171 || $loggedInUser['current_org']['id'] == 425 || $loggedInUser['current_org']['id'] == 247)) {
                    $sso_user = 1;
                }
                $configVars["sso"] = $sso_user;
                $saveVars = serialize($configVars);

                $emailQueue[$user['User']['id']] = array("to" => $user['User']['email'], "subject" => $subject, "config_vars" => $saveVars, "template" => "endorse");
            }

//            pr($emailQueue); exit;

            if (!empty($emailQueue)) {
                $this->Email->saveMany($emailQueue);
            }

//            exec("wget -bqO- " . Router::url('/', true) . "/cron/endorseEmails &> /dev/null");

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "nDorsement submitted!", "data" => array('endorsement_ids' => $endorsementIds)),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function guestEndorse() {

        if ($this->request->is('post')) {
            $loggedInUser = $this->Auth->user();
            $endorseList = json_decode($this->request->data['endorse_list']);
            //Create Guest User 
            $userEmailId = $UserData['email'] = isset($this->request->data['User']['email']) ? $this->request->data['User']['email'] : '';
            $UserData['username'] = isset($this->request->data['User']['email']) ? $this->request->data['User']['email'] : '';
            $userFName = $UserData['fname'] = isset($this->request->data['User']['fname']) ? $this->request->data['User']['fname'] : '';
            $userLName = $UserData['lname'] = isset($this->request->data['User']['lname']) ? $this->request->data['User']['lname'] : '';
            $UserData['mobile'] = isset($this->request->data['User']['mobile']) ? $this->request->data['User']['mobile'] : '';
            $UserData['source'] = 'guest';
            $UserData['status'] = '0';
            $UserData['role'] = '5';
            $UserData['password'] = 'aba2d5949a122c89cbfbd676ab814333d2615df5'; //12345678 Static password
            $guestUser = $this->User->save($UserData);

            //Get list of eval and inactive users
            $endorsedUserIds = array();
            foreach ($endorseList as $endorseRequest) {
                if ($endorseRequest->for == 'user') {
                    $endorsedUserIds[] = $endorseRequest->id;
                }
            }
            $endorserID = $this->User->id;
            /** NEW CODE END ** */
            $params = array();


            $endorsementIds = array();
            $coreValues = explode(",", $this->request->data['core_values']);
            $emojisValue = array();


            $errorsArray = array();
            $error = false;
            $coreValueArray = array();
            $emojisValueArray = array();
            $endorseeIds = array();
            $emailUserIds = array();

            /* Email Template Start */

            if (isset($userEmailId) && $userEmailId != '') {
                $orgID = $this->request->data['org_id'];
                $orgDATA = $this->Organization->findById($orgID);
                $organizationName = '';
                if (!empty($orgDATA)) {
                    if (isset($orgDATA['Organization']['name']) && $orgDATA['Organization']['name'] != '') {
                        $organizationName = $orgDATA['Organization']['name'];
                    }
                }
                $msg = "Your feedback received successfully.";
                $viewVars = array("username" => $userEmailId, 'first_name' => $userFName, 'last_name' => $userLName, 'org_name' => $organizationName);
                /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                $userIdEncrypted = base64_encode($endorserID);
                $rootUrl = Router::url('/', true);
                $rootUrl = str_replace("http", "https", $rootUrl);
                $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                $viewVars["pathToRender"] = $pathToRender;
                /**/
                $configVars = serialize($viewVars);
                $subject = "Your feedback received successfully.";
                $to = $userEmailId;
                $email = array("to" => $to, "subject" => $subject, "config_vars" => $configVars, "template" => "guest_feedback");
                $this->Email->save($email);
            }
            /* Email Template End */



            foreach ($endorseList as $endorseRequest) {
                $endorseRequest->for = strtolower($endorseRequest->for);
                $endorser_id = $endorsement['endorser_id'] = $this->User->id;
                $endorsement['organization_id'] = $loggedInUser['current_org']['id'];
                $endorsed_id = $endorsement['endorsed_id'] = $endorseRequest->id;
                $endorse_type = $endorsement['endorsement_for'] = strtolower($endorseRequest->for);
                $endorseTypeLiveFeed = $endorsement['type'] = isset($this->request->data['type']) ? $this->request->data['type'] : "";
                $endorsement['message'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
                $endorsement['emojis_count'] = count($emojisValue) ? 1 : 0;
                $endorsement['organization_id'] = $this->request->data['org_id'];
                $endorsement['email_sent'] = 1;
                $endorsement['status'] = 0;
                $endorsement['daisy_award_type'] = $this->request->data['award_type'];
                $this->Endorsement->clear();
                $this->Endorsement->set($endorsement);

                if ($this->Endorsement->validates()) {
//                    pr($endorsement);  exit;
                    if ($this->Endorsement->save()) {
                        $endorsementIds[] = $this->Endorsement->id;
                        $endorsed_user_id = array();

                        /* Save data in Feeds table Start Added by Babulal Prasad @03012017*** */
                        $endorseId = $this->Endorsement->id;
                        $this->FeedTran->clear();
                        $feedTrans['FeedTran']['feed_id'] = $endorseId;
                        $feedTrans['FeedTran']['org_id'] = $this->request->data['org_id'];
                        $feedTrans['FeedTran']['feed_type'] = 'endorse';
                        $feedTrans['FeedTran']['status'] = 0;
                        $feedTrans['FeedTran']['endorse_type'] = $endorseTypeLiveFeed;
                        $endorsed_user_id[] = $endorser_id;
                        if ($endorse_type == 'department' || $endorse_type == 'entity') {
                            $feedTrans['FeedTran']['dept_id'] = $endorsed_id;
                        } else if ($endorse_type == 'user') {
                            $endorsed_user_id[] = $endorsed_id;
                            $feedTrans['FeedTran']['dept_id'] = "";
                        }
                        $feedTrans['FeedTran']['user_id'] = json_encode($endorsed_user_id);
                        $this->FeedTran->save($feedTrans);
                        /* Save data in Feeds table END*** */

                        foreach ($coreValues as $core_value) {
                            if (trim($core_value) != "") {
                                $coreValueArray[] = array("endorsement_id" => $this->Endorsement->id, "value_id" => $core_value);
                            }
                        }
                        foreach ($emojisValue as $emojis_value) {
                            $emojisValueArray[] = array("endorsement_id" => $this->Endorsement->id, "name" => $emojis_value, "type" => "emojis");
                        }
                    } else {
                        $error = true;
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "Unable to nDorse."),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $error = true;
                    $errors = $this->Endorsement->validationErrors;

                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }

                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Error!", 'errors' => $errorsArray),
                        '_serialize' => array('result')
                    ));

                    break;
                }
            }
            if ($error) {
                return;
            }
            if (!empty($coreValueArray)) {
                $this->EndorseCoreValue->saveMany($coreValueArray);
            }

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "nDorsement submitted!", "data" => array('endorsement_ids' => $endorsementIds)),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function daisyEndorse() {

        if ($this->request->is('post')) {
            $loggedInUser = $this->Auth->user();
            $endorseList = json_decode($this->request->data['endorse_list']);
//            pr($this->request->data);
//            exit;


            if (isset($this->request->data['source']) && $this->request->data['source'] == 'web_app') {

                $userinfo = $this->getuserData($this->request->data['token'], false);

                if (isset($userinfo) && !empty($userinfo)) {
                    $userID = $userinfo['users']['id'];
                }
            } else {
                //Create Guest User 
                $userEmailId = $UserData['email'] = isset($this->request->data['User']['email']) ? $this->request->data['User']['email'] : '';
                $UserData['username'] = isset($this->request->data['User']['email']) ? $this->request->data['User']['email'] : '';
                $userFName = $UserData['fname'] = isset($this->request->data['User']['fname']) ? $this->request->data['User']['fname'] : '';
                $userLName = $UserData['lname'] = isset($this->request->data['User']['lname']) ? $this->request->data['User']['lname'] : '';
                $UserData['nominator_title'] = isset($this->request->data['User']['nominator_title']) ? $this->request->data['User']['nominator_title'] : '';
                $UserData['mobile'] = isset($this->request->data['User']['mobile']) ? $this->request->data['User']['mobile'] : '';
                $UserData['source'] = 'daisy';
                $UserData['status'] = '0';
                $UserData['role'] = '5';
                $UserData['last_app_used'] = '';
                $UserData[''] = '';
                $UserData['password'] = 'aba2d5949a122c89cbfbd676ab814333d2615df5'; //12345678 Static password
                $guestUser = $this->User->save($UserData);
                $userID = $this->User->id;
            }

            //Get list of eval and inactive users
            $endorsedUserIds = array();
            foreach ($endorseList as $endorseRequest) {
                if ($endorseRequest->for == 'user') {
                    $endorsedUserIds[] = $endorseRequest->id;
                }
            }
            $endorserID = $userID;
            /** NEW CODE END ** */
            $params = array();


            $endorsementIds = array();
            $coreValues = explode(",", $this->request->data['core_values']);
            $emojisValue = array();


            $errorsArray = array();
            $error = false;
            $coreValueArray = array();
            $emojisValueArray = array();
            $endorseeIds = array();
            $emailUserIds = array();

            /* Email Template Start */

            if (isset($userEmailId) && $userEmailId != '') {
//                $orgID = $this->request->data['org_id'];
//                $orgDATA = $this->Organization->findById($orgID);
//                $organizationName = '';
//                if (!empty($orgDATA)) {
//                    if (isset($orgDATA['Organization']['name']) && $orgDATA['Organization']['name'] != '') {
//                        $organizationName = $orgDATA['Organization']['name'];
//                    }
//                }
//                $msg = "Your feedback received successfully.";
//                $viewVars = array("username" => $userEmailId, 'first_name' => $userFName, 'last_name' => $userLName, 'org_name' => $organizationName);
                /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
//                $userIdEncrypted = base64_encode($endorserID);
//                $rootUrl = Router::url('/', true);
//                $rootUrl = str_replace("http", "https", $rootUrl);
//                $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
//                $viewVars["pathToRender"] = $pathToRender;
//                /**/
//                $configVars = serialize($viewVars);
//                $subject = "Your feedback received successfully.";
//                $to = $userEmailId;
//                $email = array("to" => $to, "subject" => $subject, "config_vars" => $configVars, "template" => "guest_feedback");
                // $this->Email->save($email);  I have stopped mail, first i need to confirm with client 
            }
            /* Email Template End */



            foreach ($endorseList as $endorseRequest) {
                $endorseRequest->for = strtolower($endorseRequest->for);
                $endorser_id = $endorsement['endorser_id'] = $userID;
                $endorsement['organization_id'] = $loggedInUser['current_org']['id'];
                $endorsed_id = $endorsement['endorsed_id'] = $endorseRequest->id;
                $endorse_type = $endorsement['endorsement_for'] = strtolower($endorseRequest->for);
                $endorseTypeLiveFeed = $endorsement['type'] = isset($this->request->data['type']) ? $this->request->data['type'] : "";
                $endorsement['message'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
                $endorsement['daisy_award_type'] = isset($this->request->data['award_type']) ? $this->request->data['award_type'] : "";
                $endorsement['emojis_count'] = count($emojisValue) ? 1 : 0;
                $endorsement['organization_id'] = $this->request->data['org_id'];
                $endorsement['department_name'] = isset($this->request->data['department_name']) ? $this->request->data['department_name'] : "";
                $endorsement['department_id'] = isset($this->request->data['department_id']) ? $this->request->data['department_id'] : "";
                $endorsement['email_sent'] = 1;
                $endorsement['status'] = 0;
                $endorsement['daisy_nominee_name'] = isset($this->request->data['daisy_nominee_name']) ? $this->request->data['daisy_nominee_name'] : "";
                $endorsement['daisy_custom_nominee'] = isset($this->request->data['daisy_custom_nominee']) ? $this->request->data['daisy_custom_nominee'] : 0;
                $endorsement['daisy_custom_dept'] = isset($this->request->data['daisy_custom_dept']) ? $this->request->data['daisy_custom_dept'] : 0;
                $endorsement['nominee_subcenter_id'] = isset($this->request->data['nominee_subcenter_id']) ? $this->request->data['nominee_subcenter_id'] : 0;
                $this->Endorsement->clear();
                $this->Endorsement->set($endorsement);

                if ($this->Endorsement->validates()) {
//                    pr($endorsement);  exit;
                    if ($this->Endorsement->save()) {
                        $endorsementIds[] = $this->Endorsement->id;
                        $endorsed_user_id = array();

                        /* Save data in Feeds table Start Added by Babulal Prasad @03012017*** */
                        $endorseId = $this->Endorsement->id;
                        $this->FeedTran->clear();
                        $feedTrans['FeedTran']['feed_id'] = $endorseId;
                        $feedTrans['FeedTran']['org_id'] = $this->request->data['org_id'];
                        $feedTrans['FeedTran']['feed_type'] = 'endorse';
                        $feedTrans['FeedTran']['status'] = 0;
                        $feedTrans['FeedTran']['endorse_type'] = $endorseTypeLiveFeed;
                        $endorsed_user_id[] = $endorser_id;
                        if ($endorse_type == 'department' || $endorse_type == 'entity') {
                            $feedTrans['FeedTran']['dept_id'] = $endorsed_id;
                        } else if ($endorse_type == 'user') {
                            $endorsed_user_id[] = $endorsed_id;
                            $feedTrans['FeedTran']['dept_id'] = "";
                        }
                        $feedTrans['FeedTran']['user_id'] = json_encode($endorsed_user_id);
                        $this->FeedTran->save($feedTrans);
                        /* Save data in Feeds table END*** */

                        foreach ($coreValues as $core_value) {
                            if (trim($core_value) != "") {
                                $coreValueArray[] = array("endorsement_id" => $this->Endorsement->id, "value_id" => $core_value);
                            }
                        }
                        foreach ($emojisValue as $emojis_value) {
                            $emojisValueArray[] = array("endorsement_id" => $this->Endorsement->id, "name" => $emojis_value, "type" => "emojis");
                        }
                    } else {
                        $error = true;
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "Unable to nDorse."),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $error = true;
                    $errors = $this->Endorsement->validationErrors;

                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }

                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Error!", 'errors' => $errorsArray),
                        '_serialize' => array('result')
                    ));

                    break;
                }
            }
            if ($error) {
                return;
            }
            if (!empty($coreValueArray)) {
                $this->EndorseCoreValue->saveMany($coreValueArray);
            }

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "nDorsement submitted!", "data" => array('endorsement_ids' => $endorsementIds)),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function saveEndorseAttachment() {
        if ($this->request->is('post')) {
            $loggedInUser = $this->Auth->user();

            $endorsementIds = explode(",", $this->request->data['endorsement_ids']);

            if ($this->request->data['type'] == "image") {
                $this->request->data['image'] = $this->request->data['attachment'];

                $this->EndorseAttachment->set($this->request->data);
                if ($this->EndorseAttachment->validates()) {
                    $uploadPath = ENDORSE_IMAGE_DIR;

                    $imageExtension = $this->EndorseAttachment->data['EndorseAttachment']['file_extension'];
                    $imageData = $this->EndorseAttachment->data['EndorseAttachment']['imageData'];

                    $imageName = str_replace(",", "", $this->request->data['endorsement_ids']);
                    $imageName = $imageName . "_" . time() . "." . $imageExtension;
                    $imageName = $this->Common->getUploadFilename($uploadPath, $imageName);

                    if ($this->Common->uploadApiImage($uploadPath, $imageName, $imageData)) {
//$this->request->data['name'] = $imageName;
                        unset($this->EndorseAttachment->validate['image']);
//

                        $params = array();
                        $params['fields'] = "id,image_count";
                        $params['conditions'] = array("id" => $endorsementIds);
                        $imageendorsement = $this->Endorsement->find("all", $params);

                        $endorseimagearray = array();
                        foreach ($imageendorsement as $imgval) {
                            $endorseimagearray[$imgval["Endorsement"]["id"]] = $imgval["Endorsement"]["image_count"];
                        }
//print_r($endorseimagearray);
//
                        $endorseupdate = array();
                        foreach ($endorsementIds as $id) {

                            $endorseAttachments[] = array("endorsement_id" => $id, "type" => $this->request->data['type'], "name" => $imageName);
// check if image_count = 0 then update image_count flag
                            if ($endorseimagearray[$id] == 0) {
//echo $id;
                                $this->Endorsement->id = $id;
                                $this->Endorsement->savefield("image_count", 1);
                            }
                        }


                        if ($this->EndorseAttachment->saveMany($endorseAttachments)) {

                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "Attachments sent successfully."),
                                '_serialize' => array('result')
                            ));
                        } else {
                            $this->set(array(
                                'result' => array("status" => false
                                    , "msg" => "Attachment failed due to server error! Please try nDorsement later or without attachment."),
                                '_serialize' => array('result')
                            ));
                        }
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "Attachment failed due to server error! Please try nDorsement later or without attachment."),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $errors = $this->EndorseAttachment->validationErrors;

                    $errorsArray = array();

                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }


                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Error!", 'errors' => $errorsArray),
                        '_serialize' => array('result')
                    ));
                }
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getCountryStateList() {


        $countryinfo = $this->Country->find("all", array(
            'joins' => array(array('table' => 'states', 'type' => 'INNER', 'conditions' => array('states.country_id = Country.id'))),
            'fields' => array('Country.name,states.name'),
            'order' => 'Country.name,states.name'
        ));
        $countryarray = array();
        foreach ($countryinfo as $val) {
            $countryarray[$val["Country"]["name"]][] = $val["states"]["name"];
        }
        return $countryarray;
    }

    public function getOrgValues($org_id, $model = "OrgJobTitles", $all = 0, $valueid = array()) {
        $title = "name";
        $fields = array("id");
        $value = "";
//if($valueid !="")
//{
//	$value = explode(",",$valueid);
//}
        if ($model == "OrgJobTitles") {
            $title = "title";
            $fields[] = "title";
        } elseif ($model == "OrgHashtag") {
            $title = "name";
            $fields = array("name", "id");
        } elseif ($model == "OrgCoreValues") {
            $fields[] = "name";
            $fields[] = "color_code";
            $fields[] = "custom_message_text";
            $fields[] = "custom_message_enabled";
            $fields[] = "custom_message_disabled_user_id";
            $fields[] = "for_webapp";
            $fields[] = "for_guest";
        } elseif ($model == 'Organization') {
            $fields = array("name", "allow_comments", "optional_comments", "allow_attachment", "public_endorse_visible", "endorsement_limit");
        } else {
            $fields[] = "name";
            $title = "name";
        }


        if ($model == 'Organization') {
            $condarr = array($model . '.id' => $org_id);
        } else if ($model == 'OrgCoreValues') {
            $condarr = array($model . '.organization_id' => $org_id, 'for_webapp' => 1);
        } else if ($model == 'OrgHashtag') {
            $condarr = array($model . '.org_id' => $org_id);
        } else {
            $condarr = array($model . '.organization_id' => $org_id);
        }




        if ($all == 1) {
//echo "test1";
            if (!empty($valueid)) {
                $condarr[$model . '.id'] = $valueid;
            }
        } else {

            $condarr[$model . '.status'] = 1;
        }

//		if($all)
//		{
//			
//			$orgcoreinfo = $this->$model->find("all", array(
//                    'conditions' => $condarr,
//                    'fields' => $fields
//                ));
//		}else{
        $orgcoreinfo = $this->$model->find("all", array(
            'conditions' => $condarr,
            'fields' => $fields
        ));
        //pr($orgcoreinfo); exit;
//}
//echo $this->Organization->getLastQuery();
        $oinfo = array();
        foreach ($orgcoreinfo as $val) {
            if ($model == "OrgCoreValues") {
// $oinfo[] = array("id" => $val[$model]["id"], "name" => strtolower($val[$model][$title]), "color_code" => $val[$model]["color_code"]);
                $oinfo[] = array("id" => $val[$model]["id"], "name" => $val[$model][$title], "color_code" => $val[$model]["color_code"],
                    "custom_message_text" => $val[$model]["custom_message_text"], "custom_message_enabled" => $val[$model]["custom_message_enabled"],
                    "custom_message_disabled_user_id" => $val[$model]["custom_message_disabled_user_id"], "for_webapp" => $val[$model]["for_webapp"], "for_guest" => $val[$model]["for_guest"]);
            } elseif ($model == 'Organization') {
// $oinfo[] = array("id" => $val[$model]["id"], "name" => strtolower($val[$model][$title]));

                $oinfo[] = $val[$model];
            } else {
// $oinfo[] = array("id" => $val[$model]["id"], "name" => strtolower($val[$model][$title]));
                $oinfo[] = array("id" => $val[$model]["id"], "name" => $val[$model][$title]);
            }
        }
        return $oinfo;
    }

    public function getOrgoption() {

        if (isset($this->request->data['token']) && isset($this->request->data['org_id'])) {
            $orgArray = array();
            $org_id = $this->request->data['org_id'];
            $orgArray["departments"] = $this->getOrgValues($org_id, "OrgDepartments");
            $orgArray["entity"] = $this->getOrgValues($org_id, "Entity");
            $orgArray["job_titles"] = $this->getOrgValues($org_id, "OrgJobTitles");

            $userinfo = $this->getuserData($this->request->data['token'], false);
            $user_id = $userinfo["users"]["id"];
            $userOrganization = $this->UserOrganization->find("first", array("fields" => array('department_id', 'job_title_id', 'entity_id'), "conditions" => array("organization_id" => $org_id, "user_id" => $user_id)));
            $selectdata = array();
            if (!empty($userOrganization)) {
                $selectdata = $userOrganization["UserOrganization"];
            }
            $orgArray["option_selected"] = $selectdata;
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Organization entity,department,job_titles data ",
                    "data" => $orgArray),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token or keyword is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function joinOrganization() {
        if ($this->request->is('post')) {
            $loggedinUser = $this->Auth->user();

//Remove this code once invite table is empty
            $organization = $this->Organization->findBySecretCode($this->request->data['org_code']);
//echo $this->Organization->getLastQuery();die;
            if (!empty($organization)) {
                $this->joinOrganizationRemove();
                return;
            }


            $this->JoinOrgCode->bindModel(array('belongsTo' => array('Organization')));
            $joinOrgCodeRecord = $this->JoinOrgCode->findByCode($this->request->data['org_code']);

//echo $this->Organization->getLastQuery();die;
            if (empty($joinOrgCodeRecord)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid join code."),
                    '_serialize' => array('result')
                ));
                return;
            } else {
                if ($joinOrgCodeRecord['JoinOrgCode']['is_expired'] == 1) {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Code has been expired"),
                        '_serialize' => array('result')
                    ));
                    return;
                }

                $organization = $joinOrgCodeRecord['Organization'];
                if ($organization['status'] != 1) {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Organization Inactive!"),
                        '_serialize' => array('result')
                    ));
                    return;
                }

                $statusConfig = Configure::read("statusConfig");

                $alreadyJoined = $this->UserOrganization->find("first", array("conditions" => array("user_id" => $loggedinUser['id'], 'organization_id' => $organization['id'])));
                if (!empty($alreadyJoined)) {
                    if ($alreadyJoined['UserOrganization']['status'] != $statusConfig['deleted'] && $alreadyJoined['UserOrganization']['joined'] == 1) {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "You are already a member of this Organization."),
                            '_serialize' => array('result')
                        ));
                        return;
                    } else {
                        $userOrgId = $alreadyJoined['UserOrganization']['id'];
                    }
                } else {
                    $alreadyJoined = $this->UserOrganization->find("first", array("conditions" => array("user_id" => $joinOrgCodeRecord['JoinOrgCode']['user_id'], 'organization_id' => $organization['id'], 'UserOrganization.status !=' => 2)));
                    if (!empty($alreadyJoined)) {
                        $userOrgId = $alreadyJoined['UserOrganization']['id'];
                    }
                }
            }

            if (isset($this->request->data['org_code'])) {

                $currentOrg = isset($loggedinUser['current_org']) ? $loggedinUser['current_org'] : array();

                if (isset($userOrgId) && isset($alreadyJoined['UserOrganization'])) {
                    $poolType = $alreadyJoined['UserOrganization']['pool_type'];
                    $status = $alreadyJoined['UserOrganization']['status'];
                } else {
                    $params = array();
                    $params['conditions'] = array("organization_id" => $organization['id'], "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                    $params['group'] = 'pool_type';
                    $params['fields'] = array("UserOrganization.pool_type", "COUNT(UserOrganization.id) as count");
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


                    if ($freeCount >= FREE_POOL_USER_COUNT) {
                        $poolType = "paid";

                        $params = array();
                        $conditions = array();
                        $todayDate = date('Y-m-d H:i:s');
//                    $conditions['start_date <='] = $todayDate;
//                    $conditions['end_date >='] = $todayDate;
                        $conditions['Subscription.status'] = 1;
                        $conditions['Subscription.organization_id'] = $organization['id'];
                        $params['conditions'] = $conditions;
                        $currentSubscription = $this->Subscription->find("first", $params);
                        if (!empty($currentSubscription)) {
                            $poolPurchased = $currentSubscription['Subscription']['pool_purchased'];

                            if ($paidCount >= $poolPurchased) {
//$status = $statusConfig['invite_inactive'];
                                $status = $statusConfig['inactive'];
                            } else {
                                $status = $statusConfig['active'];
                            }
                        } else {
//$status = $statusConfig['invite_inactive'];
                            $status = $statusConfig['inactive'];
                        }
                    } else {
                        $poolType = "free";
                        $status = $statusConfig['active'];
                    }
                }

                $newUserOrganization = array(
                    "organization_id" => $organization['id'],
                    "user_id" => $loggedinUser['id'],
                    "pool_type" => $poolType,
                    "status" => $status,
                    "flow" => "creator",
                    "joined" => 1
                );

                if (isset($userOrgId)) {
                    $newUserOrganization['id'] = $userOrgId;
                    $newUserOrganization['flow'] = 'web_invite';
                } else {
                    $newUserOrganization['send_invite'] = 0;
                }

                if ($joinOrgCodeRecord['JoinOrgCode']['user_id'] != $loggedinUser['id']) {
                    $newUserOrganization['user_id'] = $loggedinUser['id'];
                }

                $isDefault = false;
                $saved = $this->UserOrganization->save($newUserOrganization);
                if (!isset($newUserOrganization['id'])) {
                    $userOrgId = $this->UserOrganization->id;
                }

                if ($saved) {
                    $this->JoinOrgCode->updateAll(array("is_expired" => 1, "user_organization_id" => $userOrgId), array("id" => $joinOrgCodeRecord['JoinOrgCode']['id']));

                    $this->DefaultOrg->deleteAll(
                            array('DefaultOrg.organization_id' => $organization['id'], 'DefaultOrg.user_id' => $joinOrgCodeRecord['JoinOrgCode']['user_id'])
                    );

                    $organization['org_role'] = 'endorser';
                    $organization['joined'] = "1";
                    $isActive = false;
                    if (empty($currentOrg)) {
                        $roleList = $this->Common->setSessionRoles();

//                        if ($status == $statusConfig['active']) {
//                            $currentOrg = $organization['Organization'];
//                            $currentOrg['org_role'] = 'endorser';
//                            $this->Session->write('Auth.User.current_org', $currentOrg);
//                        }

                        $defaultOrg = array("organization_id" => $organization['id'], "user_id" => $loggedinUser['id']);
                        $this->DefaultOrg->save($defaultOrg);

                        $isDefault = true;
                    }

                    if ($status == $statusConfig['active']) {
                        if ($isDefault) {
                            $currentOrg = $organization;
                            $this->Session->write('Auth.User.current_org', $currentOrg);
                        }

                        $msg = "You have successfully joined the Organization!!";
                        $isActive = true;
                    } else if ($status == $statusConfig['eval']) {
                        $msg = "You have successfully joined the Organization, but you are in evaluation mode.";

//                        $this->set(array(
//                            'result' => array("status" => true
//                                , "msg" => $msg, "data" => $organization, "isDefault" => $isDefault, "isActive" => false),
//                            '_serialize' => array('result')
//                        ));
                    } else {
                        $msg = "You have successfully joined " . $organization['name'] . "! Your status is inactive. To activate your status, contact your Organization Admin to purchase additional subscription.";

                        $admin = $this->User->findById($organization['admin_id']);

                        $subject = "Purchase subscription";
                        $template = "less_subscription_admin";

                        /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($organization['admin_id']);
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                        /*                         * * */


                        $viewVars = serialize(array("org_name" => $organization['name'], 'user' => $loggedinUser, "pathToRender" => $pathToRender));
                        $to = $admin['User']['email'];
//$this->Common->sendEmail($admin['User']['email'], $subject, $template, $viewVars);
                        $email = array("to" => $to, "subject" => $subject, "config_vars" => $viewVars, "template" => $template);
//  $this->Email->save($email);
//                        $this->set(array(
//                            'result' => array("status" => false
//                                , "msg" => $msg, "isDefault" => $isDefault, "isActive" => false),
//                            '_serialize' => array('result')
//                        ));
                    }

                    $orgReturnData = array("Organization" => $organization);

                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => $msg, "data" => $orgReturnData, "isDefault" => $isDefault, "isActive" => $isActive),
                        '_serialize' => array('result')
                    ));
                    return;

//                    $this->Invite->id = $inviteId;
//                    $this->Invite->delete();
//$this->Invite->saveField("is_accepted", 1);
                } else {
                    $errors = $this->UserOrganization->validationErrors;
                    $errorsArray = array();

                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }

                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Errors!", 'errors' => $errorsArray),
                        '_serialize' => array('result')
                    ));
                }
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function joinOrganizationRemove() {
        if ($this->request->is('post')) {
            $loggedinUser = $this->Auth->user();
            $organization = $this->Organization->findBySecretCode($this->request->data['org_code']);
//echo $this->Organization->getLastQuery();die;
            if (empty($organization)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid Unique Code."),
                    '_serialize' => array('result')
                ));
                return;
            } else {

                if ($organization['Organization']['status'] != 1) {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Organization Inactive!"),
                        '_serialize' => array('result')
                    ));
                    return;
                }

                $statusConfig = Configure::read("statusConfig");

                $alreadyJoined = $this->UserOrganization->find("first", array("conditions" => array("user_id" => $loggedinUser['id'], 'organization_id' => $organization['Organization']['id']), "fields" => array("*")));

                if (!empty($alreadyJoined)) {
                    if ($alreadyJoined['UserOrganization']['status'] != $statusConfig['deleted'] && $alreadyJoined['UserOrganization']['joined'] == 1) {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "You are already a member of this Organization."),
                            '_serialize' => array('result')
                        ));
                        return;
                    } else {
                        $userOrgId = $alreadyJoined['UserOrganization']['id'];
                    }
                }
                $invite = $this->Invite->find("first", array("conditions" => array("email" => $loggedinUser['email'], 'organization_id' => $organization['Organization']['id'])));
                if (empty($invite)) {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "You do not have access to this Organization. Please request a Unique Code through JOIN Organization."),
                        '_serialize' => array('result')
                    ));

                    return;
                } else {
                    $inviteId = $invite['Invite']['id'];
                }
            }

            if (isset($this->request->data['org_code'])) {

                $currentOrg = isset($loggedinUser['current_org']) ? $loggedinUser['current_org'] : array();

                if (isset($userOrgId) && !empty($alreadyJoined) && ($alreadyJoined['UserOrganization']['status'] == $statusConfig['active'] || $alreadyJoined['UserOrganization']['status'] == $statusConfig['eval'])) {
                    $poolType = $alreadyJoined['UserOrganization']['pool_type'];
                    $status = $alreadyJoined['UserOrganization']['status'];
                } else {
                    $params = array();
                    $params['conditions'] = array("organization_id" => $organization['Organization']['id'], "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                    $params['group'] = 'pool_type';
                    $params['fields'] = array("UserOrganization.pool_type", "COUNT(UserOrganization.id) as count");
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


                    if ($freeCount >= FREE_POOL_USER_COUNT) {
                        $poolType = "paid";

                        $params = array();
                        $conditions = array();
                        $todayDate = date('Y-m-d H:i:s');
//                    $conditions['start_date <='] = $todayDate;
//                    $conditions['end_date >='] = $todayDate;
                        $conditions['Subscription.status'] = 1;
                        $conditions['Subscription.organization_id'] = $organization['Organization']['id'];
                        $params['conditions'] = $conditions;
                        $currentSubscription = $this->Subscription->find("first", $params);
                        if (!empty($currentSubscription)) {
                            $poolPurchased = $currentSubscription['Subscription']['pool_purchased'];

                            if ($paidCount >= $poolPurchased) {
//$status = $statusConfig['invite_inactive'];
                                $status = $statusConfig['inactive'];
                            } else {
                                $status = $statusConfig['active'];
                            }
                        } else {
//$status = $statusConfig['invite_inactive'];
                            $status = $statusConfig['inactive'];
                        }
                    } else {
                        $poolType = "free";
                        $status = $statusConfig['active'];
                    }
                }

                $newUserOrganization = array(
                    "organization_id" => $organization['Organization']['id'],
                    "user_id" => $loggedinUser['id'],
                    "pool_type" => $poolType,
                    "status" => $status,
                    //"department_id" => "",
//"job_title_id" => "",
//"entity_id" => "",
                    "flow" => "app_invite",
                    "joined" => 1
                );

                if (isset($userOrgId)) {
                    $newUserOrganization['id'] = $userOrgId;
                    $newUserOrganization['flow'] = 'web_invite';
                }

                $isDefault = false;
                $saved = false;
                if ($invite['Invite']['flow'] == "app") {
                    $saved = $this->UserOrganization->save($newUserOrganization);
                    $userOrgId = $this->UserOrganization->id;
                } else {
                    $userOrgId = $alreadyJoined['UserOrganization']['id'];
                    $saved = $this->UserOrganization->save($newUserOrganization);
//                    $saved = $this->UserOrganization->updateAll(array("joined" => 1), array("UserOrganization.id" => $userOrgId));
//                    $status = $alreadyJoined['UserOrganization']['status'];
                }

                if ($saved) {
                    $organization['Organization']['org_role'] = 'endorser';
                    $isActive = false;
                    if (empty($currentOrg)) {
                        $roleList = $this->Common->setSessionRoles();

//                        if ($status == $statusConfig['active']) {
//                            $currentOrg = $organization['Organization'];
//                            $currentOrg['org_role'] = 'endorser';
//                            $this->Session->write('Auth.User.current_org', $currentOrg);
//                        }

                        $defaultOrg = array("organization_id" => $organization['Organization']['id'], "user_id" => $loggedinUser['id']);
                        $this->DefaultOrg->save($defaultOrg);

                        $isDefault = true;
                    }

                    if ($status == $statusConfig['active']) {
                        $currentOrg = $organization['Organization'];
                        $currentOrg['joined'] = "1";
                        $this->Session->write('Auth.User.current_org', $currentOrg);

                        $msg = "You have successfully joined the Organization!!";
                        $isActive = true;
                    } else if ($status == $statusConfig['eval']) {
                        $msg = "You have successfully joined the Organization, but you are in evaluation mode.";

//                        $this->set(array(
//                            'result' => array("status" => true
//                                , "msg" => $msg, "data" => $organization, "isDefault" => $isDefault, "isActive" => false),
//                            '_serialize' => array('result')
//                        ));
                    } else {
                        $msg = "You have successfully joined " . $organization['Organization']['name'] . "! Your status is inactive. To activate your status, contact your Organization Admin to purchase additional subscription.";

                        $admin = $this->User->findById($organization['Organization']['admin_id']);

                        $subject = "Purchase subscription";
                        $template = "less_subscription_admin";
                        /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($organization['admin_id']);
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                        /*                         * * */
                        $viewVars = serialize(array("org_name" => $organization['Organization']['name'], 'user' => $loggedinUser, "pathToRender" => $pathToRender));
                        $to = $admin['User']['email'];
//$this->Common->sendEmail($admin['User']['email'], $subject, $template, $viewVars);
                        $email = array("to" => $to, "subject" => $subject, "config_vars" => $viewVars, "template" => $template);
// $this->Email->save($email);
//                        $this->set(array(
//                            'result' => array("status" => false
//                                , "msg" => $msg, "isDefault" => $isDefault, "isActive" => false),
//                            '_serialize' => array('result')
//                        ));
                    }

                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => $msg, "data" => $organization, "isDefault" => $isDefault, "isActive" => $isActive),
                        '_serialize' => array('result')
                    ));

                    $this->Invite->id = $inviteId;
                    $this->Invite->delete();
//$this->Invite->saveField("is_accepted", 1);
                } else {
                    $errors = $this->UserOrganization->validationErrors;
                    $errorsArray = array();

                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }

                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Errors!", 'errors' => $errorsArray),
                        '_serialize' => array('result')
                    ));
                }
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getSubscriptionInfo() {
        if ($this->request->is('post')) {
            $loggedinUser = $this->Auth->user();
            if ($loggedinUser['current_org']['org_role'] == "admin" || $loggedinUser['current_org']['org_role'] == "designated_admin") {
                $statusConfig = Configure::read("statusConfig");
                $params = array();
                $conditions = array();
                $todayDate = date('Y-m-d H:i:s');
//                $conditions['start_date <='] = $todayDate;
//                $conditions['end_date >='] = $todayDate;
                $conditions['Subscription.status'] = 1;
                $conditions['Subscription.organization_id'] = $loggedinUser['current_org']['id'];
                $params['conditions'] = $conditions;
                $currentSubscription = $this->Subscription->find("first", $params);
                $poolPurchased = !empty($currentSubscription) ? $currentSubscription['Subscription']['pool_purchased'] + FREE_POOL_USER_COUNT : FREE_POOL_USER_COUNT;
                $joinedUser = $this->UserOrganization->find("count", array("conditions" => array("organization_id" => $loggedinUser['current_org']['id'], "UserOrganization.status" => array($statusConfig['inactive'], $statusConfig['active'], $statusConfig['eval']))));

                if ($loggedinUser['current_org']['org_role'] == "admin") {
                    $inviteMsg = "You have reached " . $poolPurchased . " user limit. To invite additional users, you'll have to purchase/upgrade the subscription.";
                } else if ($loggedinUser['current_org']['org_role'] == "designated_admin") {
                    $inviteMsg = "You have reached " . $poolPurchased . " user limit. To invite additional users, request your org admin to purchase/upgrade subscription.";
                }

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Subscription information",
                        "data" => array("pool_purchased" => $poolPurchased, "joined_user" => $joinedUser, "invite_msg" => $inviteMsg)),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You are not allowed to view this information"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function searchInOrganization() {
        if ($this->request->is('post')) {
            $resultData = array();
            $keyWord = $this->request->data['keyword'];
            $subcenter_id = '';
            if (isset($this->request->data['subcenter_id'])) {
                $subcenter_id = $this->request->data['subcenter_id'];
            }

            $loggedinUser = $this->Auth->user();

            if (!isset($loggedinUser['current_org'])) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You have not joined any organization yet. Please join."),
                    '_serialize' => array('result')
                ));
                return;
            }

            $statusConfig = Configure::read("statusConfig");

            $startDate = date("Y-m-1 00:00:00");
            $endDate = date("Y-m-t", strtotime($startDate)) . " 23:59:59";

            $searchSelf = isset($this->request->data['search_self']) ? $this->request->data['search_self'] : false;

            if ($searchSelf) {
                $searchSelfCondition = "";
            } else {
                $searchSelfCondition = " AND User.id != " . $loggedinUser['id'];
            }

            if ($subcenter_id != '') {
                $searchBySubcenter = " AND OrgSubcenter.id = " . $subcenter_id;
            } else {
                $searchBySubcenter = "";
            }

            $sql_not_using = "SELECT  User.id, User.fname, User.lname, User.email, UserOrganization.status,OrgSubcenter.short_name, IF(Endorsement. organization_id= " . $loggedinUser['current_org']['id'] . ", COUNT(Endorsement.id), 0) as count
                                    FROM user_organizations AS UserOrganization
                                    LEFT JOIN users AS User ON (UserOrganization.user_id = User.id) 
                                    LEFT JOIN org_subcenters AS OrgSubcenter ON (UserOrganization.subcenter_id = OrgSubcenter.id) 
                                    LEFT JOIN endorsements AS Endorsement ON (Endorsement.endorsed_id = User.id AND MONTH(Endorsement.created) = " . date("n") . " AND Endorsement.organization_id = " . $loggedinUser['current_org']['id'] . " AND Endorsement.endorser_id = " . $loggedinUser['id'] . ") 
                                    WHERE ((LOWER(User.fname) LIKE LOWER('%" . $keyWord . "%')) OR (LOWER(User.lname) LIKE LOWER('%" . $keyWord . "%')) OR (LOWER(CONCAT(User.fname, ' ', User.lname)) LIKE LOWER('%" . $keyWord . "%')))	AND UserOrganization.status IN (" . $statusConfig['active'] . ", " . $statusConfig['eval'] . ")
                                    AND UserOrganization.organization_id = " . $loggedinUser['current_org']['id'] . $searchSelfCondition . " " . $searchBySubcenter . "
                                    GROUP BY  User.id, Endorsement.endorsed_id";
//            $usersData = $this->UserOrganization->query($sql_not_using);
//$usersData = $this->UserOrganization->find("all", $params);
//echo $this->UserOrganization->getLastQuery();die;
//pr($usersData);die;
//            $users = array();
//
//            foreach ($usersData as $user) {
////if($user['User']['id']!=$loggedinUser['id']){
//                $userDetail = array();
//                $userDetail['id'] = $user['User']['id'];
//                $userDetail['email'] = $user['User']['email'];
//                $userDetail['name'] = $user['User']['fname'] . " " . $user['User']['lname'];
//                $userDetail['org_status'] = array_search($user['UserOrganization']['status'], $statusConfig);
//                $userDetail['endorse_count'] = $user[0]['count'];
////                $userDetail['subcenter'] = $user['OrgSubcenter']['short_name'];
//
//                $users[$user['User']['id']] = $userDetail;
////}
//            }

            /* GET POSTs Users Start */
            $postQuery = "SELECT  User.id, User.fname, User.lname, User.email, UserOrganization.status,OrgSubcenter.short_name,OrgSubcenter.long_name,OrgSubcenter.id,OrgDepartment.name, IF(Post. organization_id = " . $loggedinUser['current_org']['id'] . ", COUNT(Post.id), 0) as count
                                FROM user_organizations AS UserOrganization
                                LEFT JOIN users AS User ON (UserOrganization.user_id = User.id)  
                                LEFT JOIN org_subcenters AS OrgSubcenter ON (UserOrganization.subcenter_id = OrgSubcenter.id) 
                                LEFT JOIN org_departments AS OrgDepartment ON (UserOrganization.department_id = OrgDepartment.id) 
                                LEFT JOIN 
                                  posts AS Post ON 
                                   (Post.organization_id = " . $loggedinUser['current_org']['id'] . ") 
                                WHERE 
                                (
                                        (LOWER(User.fname) LIKE LOWER('%" . $keyWord . "%')) OR (LOWER(User.lname) LIKE LOWER('%" . $keyWord . "%')) 
                                        OR 
                                        (LOWER(CONCAT(User.fname, ' ', User.lname)) LIKE LOWER('%" . $keyWord . "%'))

                                )	
                                AND UserOrganization.status IN (" . $statusConfig['active'] . ", " . $statusConfig['eval'] . ")
                                AND UserOrganization.organization_id = " . $loggedinUser['current_org']['id'] . $searchSelfCondition . " " . $searchBySubcenter . "
                                GROUP BY  User.id";

            $postData = $this->Post->query($postQuery);
//            pr($postData);
            foreach ($postData as $user) {
//if($user['User']['id']!=$loggedinUser['id']){
                $userDetail = array();
                $userDetail['id'] = $user['User']['id'];
                $userDetail['email'] = $user['User']['email'];
                $userDetail['name'] = $user['User']['fname'] . " " . $user['User']['lname'];
                $userDetail['org_status'] = array_search($user['UserOrganization']['status'], $statusConfig);
                $userDetail['endorse_count'] = $user[0]['count'];
                $userDetail['subcenter_short'] = $user['OrgSubcenter']['short_name'];
                $userDetail['subcenter_long'] = $user['OrgSubcenter']['long_name'];
                $userDetail['subcenter_id'] = $user['OrgSubcenter']['id'];
                $userDetail['department'] = $user['OrgDepartment']['name'];

                $users[$user['User']['id']] = $userDetail;
//}
            }


            $usersArray = array();
            if (!empty($users) && count($users) > 0) {
                foreach ($users as $index => $userData) {
                    $usersArray[] = $userData;
                }
            }


            /* GET POSTs Users End */
            /* GET Search in Post Title Start */
            $postTitleQuery = "SELECT  Post.title,Post.id,User.id, User.fname, User.lname, User.email, UserOrganization.status, 
                                IF(Post. organization_id = " . $loggedinUser['current_org']['id'] . ", COUNT(Post.id), 0) as count
                                FROM user_organizations AS UserOrganization
                                LEFT JOIN posts AS Post ON (Post.organization_id = " . $loggedinUser['current_org']['id'] . ") 
                                LEFT JOIN users AS User ON (Post.user_id = User.id) 
                                WHERE LOWER(CONCAT(Post.title)) LIKE LOWER('%" . $keyWord . "%')	
                                AND UserOrganization.status IN (" . $statusConfig['active'] . ", " . $statusConfig['eval'] . ")
                                AND UserOrganization.organization_id = " . $loggedinUser['current_org']['id'] . $searchSelfCondition . "
                                GROUP BY  Post.id";
            $postTitleData = $this->Post->query($postTitleQuery);
//pr($postTitleData);
            $postTitle = array();
            foreach ($postTitleData as $index => $postDATA) {
                $postDetail = array();
                $postDetail['title'] = $postDATA['Post']['title'];
                $postDetail['id'] = $postDATA['Post']['id'];
                $postDetail['name'] = $postDATA['User']['fname'] . " " . $postDATA['User']['lname'];
                $postDetail['email'] = $postDATA['User']['email'];
                $postDetail['type'] = "post_title";
                $postTitle[] = $postDetail;
//}
            }

            /* GET Search in Post Title End */




            $departments = array();

//												$params = array();
//												$params['joins'] = array(
//                            array(
//                                'table' => 'endorsements',
//                                'alias' => 'Endorsement',
//                                'type' => 'LEFT',
//                                'conditions' => array(
//                                    'Endorsement.endorser_id = ' . $loggedinUser['id'],
//                                    'Endorsement.endorsed_id = OrgDepartments.id',
//                                    'Endorsement.organization_id = ' . $loggedinUser['current_org']['id'],
//																																				'Endorsement.created BETWEEN \'' .$startDate . '\' AND \'' .$endDate .'\''
//                                )
//                            )
//                        );
//												
//												$conditions = array();
//												$conditions['OR']["OrgDepartments.name LIKE"] = '%' . $keyWord . '%';
//												$conditions['OrgDepartments.status'] = array($statusConfig['active']);
//												$conditions['OrgDepartments.organization_id'] = $loggedinUser['current_org']['id'];
//												
//												$params['conditions'] = $conditions;
//												
//												$params['fields'] = array("OrgDepartments.id", "OrgDepartments.name", "COUNT(Endorsement.id) as count");
//												
//												$departmentsData = $this->OrgDepartments->find("all", $params);

            $sql = "SELECT OrgDepartments.id, OrgDepartments.name, COUNT(Endorsement.id) as count
                                        FROM org_departments AS OrgDepartments
                                        LEFT JOIN endorsements AS Endorsement ON (Endorsement.endorsed_id = OrgDepartments.id AND MONTH(Endorsement.created) = " . date("n") . " AND Endorsement.organization_id = " . $loggedinUser['current_org']['id'] . " AND Endorsement.endorser_id = " . $loggedinUser['id'] . ")
                                        WHERE LOWER(OrgDepartments.name) LIKE LOWER('%" . $keyWord . "%')
                                        AND OrgDepartments.status = " . $statusConfig['active'] . "
                                        AND OrgDepartments.organization_id = " . $loggedinUser['current_org']['id'] . "
                                        GROUP BY  OrgDepartments.id, Endorsement.endorsed_id";
            $departmentsData = $this->OrgDepartments->query($sql);

//echo $this->OrgDepartments->getLastQuery();die;
//
//pr($departmentsData);die;

            foreach ($departmentsData as $department) {
                $departmentDetail = array();
                $departmentDetail['id'] = $department['OrgDepartments']['id'];
                $departmentDetail['name'] = $department['OrgDepartments']['name'];
                $departmentDetail['endorse_count'] = $department[0]['count'];

                $departments[] = $departmentDetail;
            }

            $entities = array();

//												$params = array();
//												$params['joins'] = array(
//                            array(
//                                'table' => 'endorsements',
//                                'alias' => 'Endorsement',
//                                'type' => 'LEFT',
//                                'conditions' => array(
//                                    'Endorsement.endorser_id = ' . $loggedinUser['id'],
//                                    'Endorsement.endorsed_id = Entity.id',
//                                    'Endorsement.organization_id = ' . $loggedinUser['current_org']['id'],
//																																				'Endorsement.created BETWEEN \'' .$startDate . '\' AND \'' .$endDate .'\''
//                                )
//                            )
//                        );
//												
//												$conditions = array();
//												$conditions['OR']["Entity.name LIKE"] = '%' . $keyWord . '%';
//												$conditions['Entity.status'] = array($statusConfig['active']);
//												$conditions['Entity.organization_id'] = $loggedinUser['current_org']['id'];
//												
//												$params['conditions'] = $conditions;
//												
//												$params['fields'] = array("Entity.id", "Entity.name", "COUNT(Endorsement.id) as count");
//												
//												$entitiesData = $this->Entity->find("all", $params);

            $entitiesData = $this->Entity->query("
                SELECT Entity.id, Entity.name, COUNT(Endorsement.id) as count
                FROM entities AS Entity LEFT JOIN
                endorsements AS Endorsement ON (Endorsement.endorsed_id = Entity.id AND MONTH(Endorsement.created) = " . date("n") . " AND Endorsement.organization_id = " . $loggedinUser['current_org']['id'] . " AND Endorsement.endorser_id = " . $loggedinUser['id'] . ")
                WHERE LOWER(Entity.name) LIKE LOWER('%" . $keyWord . "%')
                AND Entity.status = " . $statusConfig['active'] . "
                AND Entity.organization_id = " . $loggedinUser['current_org']['id'] . "
                GROUP BY  Entity.id, Endorsement.endorsed_id");

//echo $this->Entity->getLastQuery();die;

            foreach ($entitiesData as $entity) {
                $entityDetail = array();
                $entityDetail['id'] = $entity['Entity']['id'];
                $entityDetail['name'] = $entity['Entity']['name'];
                $entityDetail['endorse_count'] = $entity[0]['count'];

                $entities[] = $entityDetail;
            }

            $resultData['users'] = $usersArray;
            $resultData['departments'] = $departments;
            $resultData['entities'] = $entities;
            $resultData['post_title'] = $postTitle;

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Search results", "data" => $resultData),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function searchInOrganizationDaisyWeb() {
        if ($this->request->is('post')) {
            $resultData = array();
            $keyWord = $this->request->data['keyword'];

            $loggedinUser = $this->Auth->user();
            $orgID = $loggedinUser['current_org']['id'];
            if (!isset($loggedinUser['current_org'])) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You have not joined any organization yet. Please join."),
                    '_serialize' => array('result')
                ));
                return;
            }

            $statusConfig = Configure::read("statusConfig");

            $startDate = date("Y-m-1 00:00:00");
            $endDate = date("Y-m-t", strtotime($startDate)) . " 23:59:59";

            $searchSelf = isset($this->request->data['search_self']) ? $this->request->data['search_self'] : false;

            if ($searchSelf) {
                $searchSelfCondition = "";
            } else {
                $searchSelfCondition = " AND User.id != " . $loggedinUser['id'];
            }

            $sql = "SELECT  User.id, User.fname, User.lname, User.email, UserOrganization.status, IF(Endorsement. organization_id= " . $orgID . ", COUNT(Endorsement.id), 0) as count,
                                    OrgDepartment.name as department_name,OrgDepartment.id as department_id,Entity.name as entity_name,User.image,User.about
                                    FROM user_organizations AS UserOrganization
                                    LEFT JOIN users AS User ON (UserOrganization.user_id = User.id) 
                                    LEFT JOIN org_departments AS OrgDepartment ON (UserOrganization.department_id = OrgDepartment.id) 
                                    LEFT JOIN entities AS Entity ON (UserOrganization.entity_id = Entity.id) 
                                    LEFT JOIN endorsements AS Endorsement ON (Endorsement.endorsed_id = User.id AND MONTH(Endorsement.created) = " . date("n") . " AND Endorsement.organization_id = " . $orgID . ") 
                                    WHERE ((LOWER(User.fname) LIKE LOWER('%" . $keyWord . "%')) OR (LOWER(User.lname) LIKE LOWER('%" . $keyWord . "%')) OR (LOWER(CONCAT(User.fname, ' ', User.lname)) LIKE LOWER('%" . $keyWord . "%')))	AND UserOrganization.status IN (" . $statusConfig['active'] . ", " . $statusConfig['eval'] . ")
                                    AND UserOrganization.organization_id = " . $orgID . $searchSelfCondition . " AND User.daisy_enabled = 1 
                                    GROUP BY  User.id, Endorsement.endorsed_id";
            $usersData = $this->UserOrganization->query($sql);

//$usersData = $this->UserOrganization->find("all", $params);
//echo $this->UserOrganization->getLastQuery();die;
//pr($usersData);die;

            $users = array();

            foreach ($usersData as $user) {
                $userDetail = array();
                $userDetail['id'] = $user['User']['id'];
                $userDetail['email'] = $user['User']['email'];
                $userDetail['image'] = $user['User']['image'];
                $userDetail['about'] = $user['User']['about'];
                $userDetail['department_name'] = $user['OrgDepartment']['department_name'];
                $userDetail['department_id'] = $user['OrgDepartment']['department_id'];
                $userDetail['entity_name'] = $user['Entity']['entity_name'];
                $userDetail['name'] = $user['User']['fname'] . " " . $user['User']['lname'];
                $userDetail['fname'] = $user['User']['fname'];
                $userDetail['lname'] = $user['User']['lname'];
                $userDetail['org_status'] = array_search($user['UserOrganization']['status'], $statusConfig);
                $userDetail['endorse_count'] = $user[0]['count'];
                $users[$user['User']['id']] = $userDetail;
            }

            $usersArray = array();
            if (!empty($users) && count($users) > 0) {
                foreach ($users as $index => $userData) {
                    $usersArray[] = $userData;
                }
            }

            $resultData['users'] = $usersArray;
            $resultData['departments'] = array();
            $resultData['entities'] = array();

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Search results", "data" => $resultData),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function searchInOrganizationGuest() {
        if ($this->request->is('post')) {
            $resultData = array();
            $keyWord = $this->request->data['keyword'];
            $orgID = $this->request->data['orgId'];

            $statusConfig = Configure::read("statusConfig");

            $startDate = date("Y-m-1 00:00:00");
            $endDate = date("Y-m-t", strtotime($startDate)) . " 23:59:59";

            $searchSelf = isset($this->request->data['search_self']) ? $this->request->data['search_self'] : false;

            $searchSelfCondition = "";


            $sql = "SELECT  User.id, User.fname, User.lname, User.email, UserOrganization.status, IF(Endorsement. organization_id= " . $orgID . ", COUNT(Endorsement.id), 0) as count,
                                    Department.name as department_name,Entity.name as entity_name,User.image,User.about
                                    FROM user_organizations AS UserOrganization
                                    LEFT JOIN users AS User ON (UserOrganization.user_id = User.id) 
                                    LEFT JOIN departments AS Department ON (UserOrganization.department_id = Department.id) 
                                    LEFT JOIN entities AS Entity ON (UserOrganization.entity_id = Entity.id) 
                                    LEFT JOIN endorsements AS Endorsement ON (Endorsement.endorsed_id = User.id AND MONTH(Endorsement.created) = " . date("n") . " AND Endorsement.organization_id = " . $orgID . ") 
                                    WHERE ((LOWER(User.fname) LIKE LOWER('%" . $keyWord . "%')) OR (LOWER(User.lname) LIKE LOWER('%" . $keyWord . "%')) OR (LOWER(CONCAT(User.fname, ' ', User.lname)) LIKE LOWER('%" . $keyWord . "%')))	AND UserOrganization.status IN (" . $statusConfig['active'] . ", " . $statusConfig['eval'] . ")
                                    AND UserOrganization.organization_id = " . $orgID . $searchSelfCondition . "
                                    GROUP BY  User.id, Endorsement.endorsed_id";
            $usersData = $this->UserOrganization->query($sql);

//$usersData = $this->UserOrganization->find("all", $params);
//echo $this->UserOrganization->getLastQuery();die;
//pr($usersData);die;

            $users = array();

            foreach ($usersData as $user) {
//if($user['User']['id']!=$loggedinUser['id']){
                $userDetail = array();
                $userDetail['id'] = $user['User']['id'];
                $userDetail['email'] = $user['User']['email'];
                $userDetail['image'] = $user['User']['image'];
                $userDetail['about'] = $user['User']['about'];
                $userDetail['department_name'] = $user['Department']['department_name'];
                $userDetail['entity_name'] = $user['Entity']['entity_name'];
                $userDetail['name'] = $user['User']['fname'] . " " . $user['User']['lname'];
                $userDetail['org_status'] = array_search($user['UserOrganization']['status'], $statusConfig);
                $userDetail['endorse_count'] = $user[0]['count'];


                $users[$user['User']['id']] = $userDetail;
//}
            }

            /* GET POSTs Users Start */
//            $postQuery = "SELECT  User.id, User.fname, User.lname, User.email, UserOrganization.status, IF(Post. organization_id = " . $orgID . ", COUNT(Post.id), 0) as count,
//                Department.name as department_name,Entity.name as entity_name
//                                FROM user_organizations AS UserOrganization
//                                LEFT JOIN users AS User ON (UserOrganization.user_id = User.id) 
//                                LEFT JOIN departments AS Department ON (UserOrganization.department_id = Department.id) 
//                                    LEFT JOIN entities AS Entity ON (UserOrganization.entity_id = Entity.id)
//                                LEFT JOIN 
//                                  posts AS Post ON 
//                                   (Post.organization_id = " . $orgID . ") 
//                                WHERE 
//                                (
//                                        (LOWER(User.fname) LIKE LOWER('%" . $keyWord . "%')) OR (LOWER(User.lname) LIKE LOWER('%" . $keyWord . "%')) 
//                                        OR 
//                                        (LOWER(CONCAT(User.fname, ' ', User.lname)) LIKE LOWER('%" . $keyWord . "%'))
//
//                                )	
//                                AND UserOrganization.status IN (" . $statusConfig['active'] . ", " . $statusConfig['eval'] . ")
//                                AND UserOrganization.organization_id = " . $orgID . $searchSelfCondition . "
//                                GROUP BY  User.id";
//
//            $postData = $this->Post->query($postQuery);
//
//            foreach ($postData as $user) {
////if($user['User']['id']!=$loggedinUser['id']){
//                $userDetail = array();
//                $userDetail['id'] = $user['User']['id'];
//                $userDetail['email'] = $user['User']['email'];
//                $userDetail['name'] = $user['User']['fname'] . " " . $user['User']['lname'];
//                $userDetail['org_status'] = array_search($user['UserOrganization']['status'], $statusConfig);
//                $userDetail['endorse_count'] = $user[0]['count'];
//                $userDetail['department_name'] = $user['Department']['department_name'];
//                $userDetail['entity_name'] = $user['Entity']['entity_name'];
//
//                $users[$user['User']['id']] = $userDetail;
////}
//            }


            $usersArray = array();
            if (!empty($users) && count($users) > 0) {
                foreach ($users as $index => $userData) {
                    $usersArray[] = $userData;
                }
            }


            /* GET POSTs Users End */
            /* GET Search in Post Title Start */
//            $postTitleQuery = "SELECT  Post.title,Post.id,User.id, User.fname, User.lname, User.email, UserOrganization.status, 
//                                IF(Post. organization_id = " . $orgID . ", COUNT(Post.id), 0) as count
//                                FROM user_organizations AS UserOrganization
//                                LEFT JOIN posts AS Post ON (Post.organization_id = " . $orgID . ") 
//                                LEFT JOIN users AS User ON (Post.user_id = User.id) 
//                                WHERE LOWER(CONCAT(Post.title)) LIKE LOWER('%" . $keyWord . "%')	
//                                AND UserOrganization.status IN (" . $statusConfig['active'] . ", " . $statusConfig['eval'] . ")
//                                AND UserOrganization.organization_id = " . $orgID . $searchSelfCondition . "
//                                GROUP BY  Post.id";
//            $postTitleData = $this->Post->query($postTitleQuery);
////pr($postTitleData);
//            $postTitle = array();
//            foreach ($postTitleData as $index => $postDATA) {
//                $postDetail = array();
//                $postDetail['title'] = $postDATA['Post']['title'];
//                $postDetail['id'] = $postDATA['Post']['id'];
//                $postDetail['name'] = $postDATA['User']['fname'] . " " . $postDATA['User']['lname'];
//                $postDetail['email'] = $postDATA['User']['email'];
//                $postDetail['type'] = "post_title";
//                $postTitle[] = $postDetail;
////}
//            }

            /* GET Search in Post Title End */




            $departments = array();

//												$params = array();
//												$params['joins'] = array(
//                            array(
//                                'table' => 'endorsements',
//                                'alias' => 'Endorsement',
//                                'type' => 'LEFT',
//                                'conditions' => array(
//                                    'Endorsement.endorser_id = ' . $loggedinUser['id'],
//                                    'Endorsement.endorsed_id = OrgDepartments.id',
//                                    'Endorsement.organization_id = ' . $orgID,
//																																				'Endorsement.created BETWEEN \'' .$startDate . '\' AND \'' .$endDate .'\''
//                                )
//                            )
//                        );
//												
//												$conditions = array();
//												$conditions['OR']["OrgDepartments.name LIKE"] = '%' . $keyWord . '%';
//												$conditions['OrgDepartments.status'] = array($statusConfig['active']);
//												$conditions['OrgDepartments.organization_id'] = $orgID;
//												
//												$params['conditions'] = $conditions;
//												
//												$params['fields'] = array("OrgDepartments.id", "OrgDepartments.name", "COUNT(Endorsement.id) as count");
//												
//												$departmentsData = $this->OrgDepartments->find("all", $params);

            $sql = "SELECT OrgDepartments.id, OrgDepartments.name, COUNT(Endorsement.id) as count
                                        FROM org_departments AS OrgDepartments
                                        LEFT JOIN endorsements AS Endorsement ON (Endorsement.endorsed_id = OrgDepartments.id AND MONTH(Endorsement.created) = " . date("n") . " AND Endorsement.organization_id = " . $orgID . ")
                                        WHERE LOWER(OrgDepartments.name) LIKE LOWER('%" . $keyWord . "%')
                                        AND OrgDepartments.status = " . $statusConfig['active'] . "
                                        AND OrgDepartments.organization_id = " . $orgID . "
                                        GROUP BY  OrgDepartments.id, Endorsement.endorsed_id";
            $departmentsData = $this->OrgDepartments->query($sql);

//echo $this->OrgDepartments->getLastQuery();die;
//
//pr($departmentsData);die;

            foreach ($departmentsData as $department) {
                $departmentDetail = array();
                $departmentDetail['id'] = $department['OrgDepartments']['id'];
                $departmentDetail['name'] = $department['OrgDepartments']['name'];
                $departmentDetail['endorse_count'] = $department[0]['count'];

                $departments[] = $departmentDetail;
            }

            $entities = array();

//												$params = array();
//												$params['joins'] = array(
//                            array(
//                                'table' => 'endorsements',
//                                'alias' => 'Endorsement',
//                                'type' => 'LEFT',
//                                'conditions' => array(
//                                    'Endorsement.endorser_id = ' . $loggedinUser['id'],
//                                    'Endorsement.endorsed_id = Entity.id',
//                                    'Endorsement.organization_id = ' . $orgID,
//																																				'Endorsement.created BETWEEN \'' .$startDate . '\' AND \'' .$endDate .'\''
//                                )
//                            )
//                        );
//												
//												$conditions = array();
//												$conditions['OR']["Entity.name LIKE"] = '%' . $keyWord . '%';
//												$conditions['Entity.status'] = array($statusConfig['active']);
//												$conditions['Entity.organization_id'] = $orgID;
//												
//												$params['conditions'] = $conditions;
//												
//												$params['fields'] = array("Entity.id", "Entity.name", "COUNT(Endorsement.id) as count");
//												
//												$entitiesData = $this->Entity->find("all", $params);

            $entitiesData = $this->Entity->query("
                SELECT Entity.id, Entity.name, COUNT(Endorsement.id) as count
                FROM entities AS Entity LEFT JOIN
                endorsements AS Endorsement ON (Endorsement.endorsed_id = Entity.id AND MONTH(Endorsement.created) = " . date("n") . " AND Endorsement.organization_id = " . $orgID . ")
                WHERE LOWER(Entity.name) LIKE LOWER('%" . $keyWord . "%')
                AND Entity.status = " . $statusConfig['active'] . "
                AND Entity.organization_id = " . $orgID . "
                GROUP BY  Entity.id, Endorsement.endorsed_id");

//echo $this->Entity->getLastQuery();die;

            foreach ($entitiesData as $entity) {
                $entityDetail = array();
                $entityDetail['id'] = $entity['Entity']['id'];
                $entityDetail['name'] = $entity['Entity']['name'];
                $entityDetail['endorse_count'] = $entity[0]['count'];

                $entities[] = $entityDetail;
            }

            $resultData['users'] = $usersArray;
            $resultData['departments'] = $departments;
            $resultData['entities'] = $entities;
//            $resultData['post_title'] = $postTitle;

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Search results", "data" => $resultData),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function searchInOrganizationDaisy() {
        if ($this->request->is('post')) {
            $resultData = array();
            $keyWord = $this->request->data['keyword'];
            $orgID = $this->request->data['orgId'];

            $statusConfig = Configure::read("statusConfig");

            $startDate = date("Y-m-1 00:00:00");
            $endDate = date("Y-m-t", strtotime($startDate)) . " 23:59:59";

            $searchSelf = isset($this->request->data['search_self']) ? $this->request->data['search_self'] : false;

            $searchSelfCondition = "";


            $sql = "SELECT  User.id, User.fname, User.lname, User.email, UserOrganization.status, IF(Endorsement. organization_id= " . $orgID . ", COUNT(Endorsement.id), 0) as count,
                                    OrgDepartment.name as department_name,OrgDepartment.id as department_id,Entity.name as entity_name,User.image,User.about
                                    FROM user_organizations AS UserOrganization
                                    LEFT JOIN users AS User ON (UserOrganization.user_id = User.id) 
                                    LEFT JOIN org_departments AS OrgDepartment ON (UserOrganization.department_id = OrgDepartment.id) 
                                    LEFT JOIN entities AS Entity ON (UserOrganization.entity_id = Entity.id) 
                                    LEFT JOIN endorsements AS Endorsement ON (Endorsement.endorsed_id = User.id AND MONTH(Endorsement.created) = " . date("n") . " AND Endorsement.organization_id = " . $orgID . ") 
                                    WHERE ((LOWER(User.fname) LIKE LOWER('%" . $keyWord . "%')) OR (LOWER(User.lname) LIKE LOWER('%" . $keyWord . "%')) OR (LOWER(CONCAT(User.fname, ' ', User.lname)) LIKE LOWER('%" . $keyWord . "%')))	AND UserOrganization.status IN (" . $statusConfig['active'] . ", " . $statusConfig['eval'] . ")
                                    AND UserOrganization.organization_id = " . $orgID . $searchSelfCondition . " AND User.daisy_enabled = 1 
                                    GROUP BY  User.id, Endorsement.endorsed_id";
            $usersData = $this->UserOrganization->query($sql);

//$usersData = $this->UserOrganization->find("all", $params);
//echo $this->UserOrganization->getLastQuery();die;
//            pr($usersData);die;

            $users = array();

            foreach ($usersData as $user) {
                $userDetail = array();
                $userDetail['id'] = $user['User']['id'];
                $userDetail['email'] = $user['User']['email'];
                $userDetail['image'] = $user['User']['image'];
                $userDetail['about'] = $user['User']['about'];
                $userDetail['department_name'] = $user['OrgDepartment']['department_name'];
                $userDetail['department_id'] = $user['OrgDepartment']['department_id'];
                $userDetail['entity_name'] = $user['Entity']['entity_name'];
                $userDetail['name'] = $user['User']['fname'] . " " . $user['User']['lname'];
                $userDetail['fname'] = $user['User']['fname'];
                $userDetail['lname'] = $user['User']['lname'];
                $userDetail['org_status'] = array_search($user['UserOrganization']['status'], $statusConfig);
                $userDetail['endorse_count'] = $user[0]['count'];
                $users[$user['User']['id']] = $userDetail;
            }

            $usersArray = array();
            if (!empty($users) && count($users) > 0) {
                foreach ($users as $index => $userData) {
                    $usersArray[] = $userData;
                }
            }

            $resultData['users'] = $usersArray;
            $resultData['departments'] = array();
            $resultData['entities'] = array();

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Search results", "data" => $resultData),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getVariousOrganizationData() {

        if (isset($this->request->data['token']) && isset($this->request->data['org_id'])) {
            $resultData = array();
            $org_id = $this->request->data['org_id'];
            $resultData["core_values"] = $this->getOrgValues($org_id, "OrgCoreValues");
            $resultData["Organization"] = $this->getOrgValues($org_id, "Organization");
            $resultData["hashtags"] = $this->getOrgValues($org_id, "OrgHashtag");

            $resultData['endorsement_limit'] = 500;

            if (isset($resultData['core_values']) && count($resultData['core_values'] > 0)) {
                foreach ($resultData['core_values'] as $key => $value) {
                    if ($value['custom_message_disabled_user_id'] == null) {
                        $resultData['core_values'][$key]['custom_message_disabled_user_id'] = array();
                    } else {
                        $resultData['core_values'][$key]['custom_message_disabled_user_id'] = json_decode($value['custom_message_disabled_user_id']);
                    }
                }
            }




            if (isset($resultData["Organization"]) && count($resultData["Organization"]) > 0) {
                $resultData['endorsement_limit'] = $resultData["Organization"][0]['endorsement_limit'];
            }



//            pr($resultData);
//            $settings = $this->GlobalSetting->findByKey("endorsement_limit");
//            if (!empty($settings)) {
//                $resultData['endorsement_limit'] = $settings['GlobalSetting']['value'];
//            }
//            $settings = $this->GlobalSetting->findByKey("endorsement_limit");
//            if (!empty($settings)) {
//                $resultData['endorsement_limit'] = $settings['GlobalSetting']['value'];
//            }

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Organization core values ",
                    "data" => $resultData),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token or keyword is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }
    
     public function getLiveFeeds_old() {

        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
//            pr($loggedInUser); exit;
            $user_id = $loggedInUser['id'];
//$loggedinUser = $this->Auth->user();
//        $params = array();
//        $params['fields'] = "*";
//        $params['conditions'] = array("DefaultOrg.user_id" => $loggedinUser['id']);
//        $defaultOrganization = $this->DefaultOrg->find("first", $params);
            $department_id = $this->Common->getUserCurrentDept($user_id, $loggedInUser['current_org']['id']);
            $entity_id = $this->Common->getUserCurrentSubOrg($user_id, $loggedInUser['current_org']['id']);

//            pr($loggedInUser); exit;
            if (isset($loggedInUser['current_org'])) {
                $org_id = $loggedInUser['current_org']['id'];
                $keyword = "";

                $params = array();
                $params['fields'] = "user_id,entity_id,department_id";
                $params['conditions'] = array(
                    "UserOrganization.organization_id" => $org_id, "UserOrganization.status" => 1);

                $userdepartmentorg = $this->UserOrganization->find("all", $params);
//                pr($userdepartmentorg);
//                exit;
                $entity_user_array = array();
                $department_user_array = array();
                foreach ($userdepartmentorg as $userorgval) {

                    if ($userorgval["UserOrganization"]["entity_id"] > 0) {
                        $entity_user_array[$userorgval["UserOrganization"]["entity_id"]][] = $userorgval["UserOrganization"]["user_id"];
                    }
                    if ($userorgval["UserOrganization"]["department_id"] > 0) {
                        $department_user_array[$userorgval["UserOrganization"]["department_id"]][] = $userorgval["UserOrganization"]["user_id"];
                    }
                }
//pr($entity_user_array);
//echo "<hr>";
//pr($department_user_array);exit;
//

                if (isset($this->request->data["type"]) && $this->request->data["type"] != "") {
                    $type = $this->request->data["type"];
                }

                $subcenterId = '';


                if (isset($this->request->data["subcenter_id"]) && $this->request->data["subcenter_id"] != "") {
                    if ($this->request->data["subcenter_id"] == 0) {
                        $subcenterId = '';
                    } else {
                        $subcenterId = $this->request->data["subcenter_id"];
                    }
                }

                $feed_type = $feed_id = "";
                $endorse_search_id = "";
//                pr($this->request->data);exit;
                if (isset($this->request->data["feed_type"]) && $this->request->data["feed_type"] != "") {
                    $feed_type = $this->request->data["feed_type"];
                    if (isset($this->request->data["feed_id"]) && $this->request->data["feed_id"] != '') {
                        $feed_id = $this->request->data["feed_id"];
                    }
                }




                $start_date = "";
                $end_date = "";

                $limit = Configure::read("pageLimit");
                if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
                    $page = $this->request->data["page"];
                    $offset = $page * $limit;
                } else {
                    $page = 1;
                    $offset = 0;
                }

                /* NEW CODE START */
//Getting total feeds count
                $NEWconditionarray = $postFeedIds = $endorseFeedIds = $feedArray = array();
//pr($this->request->data);
                if (isset($this->request->data["endorse_type"]) && $this->request->data["endorse_type"] == "user") {
                    $NEWconditionarray = array("user_id like '%" . '"' . $this->request->data["endorse_id"] . '"' . "%'");
                }

                if (isset($subcenterId) && $subcenterId != "") {
                    $NEWconditionarray["AND"] = array('OR' => array("FeedTran.subcenter_by" => $subcenterId, "FeedTran.subcenter_for" => $subcenterId));
                    //"FeedTran.subcenter_for"] = $subcenterId;
                }



                if (isset($this->request->data["endorse_type"]) && $this->request->data["endorse_type"] == "department") {
                    $NEWconditionarray["FeedTran.dept_id"] = $this->request->data["endorse_id"];
                }


                if (isset($feed_type) && $feed_type != '') {
                    $NEWconditionarray["FeedTran.feed_type"] = $feed_type;
                    if (isset($feed_id) && $feed_id != '') {
                        $NEWconditionarray["FeedTran.feed_id"] = $feed_id;
                    }
                }

                if (isset($type) && $type == 'endorser') {
                    $NEWconditionarray["FeedTran.feed_type"] = 'endorse';
                }


                /* Condition added by Babulal prasad to filter scheduled posts */
//                                $NEWconditionarray["FeedTran.org_id"] = $org_id;
                $NEWconditionarray["FeedTran.status"] = 1;


                $NEWconditionarray["OR"] = array(
                    array("AND" => array("FeedTran.visibility_check" => 1, 'FeedTran.org_id' => $org_id, array("OR" => array("visible_user_ids like '%" . '"' . $user_id . '"' . "%'",
                                    "visible_sub_org like '%" . '"' . $entity_id . '"' . "%'", "visible_dept like '%" . '"' . $department_id . '"' . "%'",
                                    "FeedTran.user_id like '%" . '"' . $user_id . '"' . "%'")))),
                    array("visibility_check" => 0, 'FeedTran.org_id' => $org_id)
                );
//(visibility_check = 1 and ((visible_user_ids like '%"2926"%' ) OR (visible_dept like'%"539"%') OR (visible_dept like'%"0"%') ) and org_id = 123  ) 
//OR 
//(visibility_check = 0 and org_id = 123  )


                /*                 * ***************** */


                $NEWparams['fields'] = "count(*) as cnt";
                $NEWparams['conditions'] = $NEWconditionarray;
//$NEWparams['order'] = 'FeedTran.created desc';
                $NEWparams['order'] = 'FeedTran.publish_date desc';
                $totaleFeeds = $this->FeedTran->find("all", $NEWparams);
//                pr($totaleFeeds); exit;
//                echo $this->FeedTran->getLastQuery();exit;
//                $log = $this->FeedTran->getDataSource()->getLog(false, false);
//                pr($log);
//                exit;

                $totalLiveFeed = $totaleFeeds[0][0]["cnt"];
                $totalpage = ceil($totalLiveFeed / $limit);
                $NEWconditionarray["FeedTran.endorse_type !="] = 'private';

//Getting live feeds
//$paramsFeed['fields'] = "*,UNIX_TIMESTAMP(created) as create_date, UNIX_TIMESTAMP() as curr_time ";
//pr($NEWconditionarray); exit;
                $paramsFeed['fields'] = "*";
                $paramsFeed['conditions'] = $NEWconditionarray;
                $paramsFeed['limit'] = $limit;
                $paramsFeed['page'] = $page;
                $paramsFeed['offset'] = $offset;

//                $paramsFeed['joins'] = array(
//                    array(
//                        'table' => 'endorsements',
//                        'alias' => 'Endorsement',
//                        'type' => 'LEFT',
//                        'conditions' => array(
//                            'FeedTran.feed_id = Endorsement.id',
//                            'FeedTran.feed_type = "endorse"',
//                            'Endorsement.type != "private"'
//                        )
//                    )
//                );
//                pr($paramsFeed);
                $paramsFeed['order'] = 'FeedTran.created desc';
                $FeedTranRes = $this->FeedTran->find("all", $paramsFeed);
//                $log = $this->FeedTran->getDataSource()->getLog(false, false);
//                pr($log);
//                exit;
//                pr($FeedTranRes);
//                exit;
                if (!empty($FeedTranRes)) {
                    foreach ($FeedTranRes as $index => $feedTransData) {
                        $feedArray[$feedTransData['FeedTran']['id']]['feed_id'] = $feedTransData['FeedTran']['feed_id'];
                        $feedArray[$feedTransData['FeedTran']['id']]['feed_type'] = $feedTransData['FeedTran']['feed_type'];
                        if ($feedTransData['FeedTran']['feed_type'] == 'post') {
                            $postFeedIds[] = $feedTransData['FeedTran']['feed_id'];
                        } else {
                            $endorseFeedIds[] = $feedTransData['FeedTran']['feed_id'];
                        }
                    }
                }

                $updateArray['live_updated'] = '"' . date("Y-m-d H:i:s") . '"';
//if ($this->UserOrganization->updateAll(array("UserOrganization.joined" => 1), array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                $this->UserOrganization->updateAll($updateArray, array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id));

//                pr($feedArray);
//                pr($postFeedIds);
//                pr($endorseFeedIds);
//                exit;

                /* NEW CODE END */
                $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id)));
//                pr($userOrganization);
//                exit;

                $params = array();
                $params['fields'] = "count(*) as cnt";


                if ($type == "endorser") {
                    $conditionarray["Endorsement.endorser_id"] = $user_id;
                } elseif ($type == "endorsed") {
//                    $updateArray['ndorsed_updated'] = '"' . date("Y-m-d H:i:s") . '"';
                    $conditionarray["Endorsement.endorsed_id"] = $user_id;
                } else {
//                    $updateArray['live_updated'] = '"' . date("Y-m-d H:i:s") . '"';
//                    $conditionarray["Endorsement.type != "] = "private";
                }



//Condition changed after discuss with rohan @2-feb-2018 by Babulal Prasad
//if ($userOrganization['UserOrganization']['user_role'] != 2) {
                $conditionarray["Endorsement.type != "] = "private";
// }


                $conditionarray["Endorsement.id"] = $endorseFeedIds;

                $params['conditions'] = $conditionarray;
                $params['order'] = 'Endorsement.created desc';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseCoreValues', 'EndorseReplies')));
                $params['joins'] = array(
                    array(
                        'table' => 'endorsement_likes',
                        'alias' => 'EndorsementLike',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Endorsement.id =EndorsementLike.endorsement_id ',
                            'EndorsementLike.user_id =' . $user_id
                        )
                    )
//                    , array(
//                        'table' => 'endorse_attachments',
//                        'alias' => 'EndorseAttachment',
//                        'type' => 'LEFT',
//                        'conditions' => array(
//                            'Endorsement.id = EndorseAttachment.endorsement_id ',
//                            'EndorseAttachment.type = "emojis"'
//                        )
//                    )
                );

                $params['fields'] = "*,UNIX_TIMESTAMP(Endorsement.created) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $params['order'] = 'Endorsement.created desc';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseCoreValues', 'EndorseReplies')));
                $this->Endorsement->bindModel(array('hasMany' => array('EndorseCoreValues')));
//pr($params);
//exit;
                $endorsement = $this->Endorsement->find("all", $params);
//                echo $this->Endorsement->getLastQuery();exit;
//                $log = $this->Endorsement->getDataSource()->getLog(false, false);
//                pr($log);
//                exit;
//pr($endorsement); 
// exit;
                $endorsmentarray = array();
                $departmentarray = array();
                $entityarray = array();
                $userarray = array();
                $core_values = $this->getOrgValues($org_id, "OrgCoreValues", 1);
                $coreval = array();
                foreach ($core_values as $cvalue) {
                    $coreval[$cvalue["id"]] = $cvalue["name"] . "&&&&" . $cvalue["color_code"];
                }

                $organizationDATA = $this->getOrgValues($org_id, "Organization", 1);
                $public_endorse_visible = 0;
                if ($organizationDATA[0]['allow_comments'] == 1 && $organizationDATA[0]['public_endorse_visible'] == 1) {
                    $public_endorse_visible = 1;
                }

                $serverCurrentTime = "";
//                pr($endorsement);exit;
                $emojis_url = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
                $emojis_url = str_replace("http", "https", $emojis_url);
//                pr($endorsement); exit;

                foreach ($endorsement as $key => $value) {
//                    pr($value["EndorseAttachments"]);
                    $displayflag = 0;
                    $endorsval = $value["Endorsement"];
                    $endorsval["created"] = $value[0]["create_date"];
                    $serverCurrentTime = $value[0]["curr_time"];
                    $endorsimgcount = $endorsval["image_count"];
                    $endorsecorevalue = $value["EndorseCoreValues"];
                    $endorsmentarray[$key]["endorse"] = $endorsval;
                    $endorsmentarray[$key]["imagecount"] = $endorsval["image_count"];

//                    echo "EMOJI COUNT : ".count($value["EndorseAttachments"]);

                    $endorsmentarray[$key]["emojis_count"] = count($value["EndorseAttachments"]);
                    $endorsmentarray[$key]["bitmojis_count"] = $endorsval["bitmojis_count"];

//                    if (isset($value["EndorseAttachment"]['name']) && $value["EndorseAttachment"]['name'] != '') {
                    if (!empty($value["EndorseAttachments"])) {
                        $endorsmentarray[$key]["bitmojis_image"] = '';
//                        pr($value["EndorseAttachments"]); exit;
                        if (count($value["EndorseAttachments"]) > 0) {
                            //pr($endorsementAttachment);
                            foreach ($value["EndorseAttachments"] as $iCount => $endorsementAttachment) {
                                if ($endorsementAttachment["type"] == 'image') {
                                    $tempEndorseImages = Router::url('/', true) . "app/webroot/" . ENDORSE_IMAGE_DIR . "small/";
                                    //$tempEndorseImages = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
                                } else {
                                    $tempEndorseImages = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
                                }
                                if (strpos($tempEndorseImages, 'localhost') < 0 || strpos($tempEndorseImages, 'staging') < 0) {
                                    $tempEndorseImages = str_replace("http", "https", $tempEndorseImages);
                                }

                                $endorsmentarray[$key]["bitmoji_images"][] = $tempEndorseImages . $endorsementAttachment['name'];
                            }
                        }
                    } else {
                        $endorsmentarray[$key]["bitmojis_image"] = '';
                        $endorsmentarray[$key]["bitmoji_images"] = array();
                    }


                    $endorsmentarray[$key]["reply"] = $endorsval["is_reply"];

                    if ($value["EndorsementLike"]["id"] != "") {
                        $endorsmentarray[$key]["like"] = 1;
                    } else {
                        $endorsmentarray[$key]["like"] = 0;
                    }
//$endorsmentarray[$key]["attatched_image"]=$value["EndorseAttachments"];

                    $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                    $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id)));
//pr($userOrganization['UserOrganization']['public_ndorse_visible_tc']); exit;


                    if ($endorsval["endorsement_for"] == "department") {
                        $endorsmentarray[$key]["reply"] = 0;
                        if ($user_id == $endorsval["endorser_id"] || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } elseif (isset($department_user_array[$endorsval["endorsed_id"]])) {
                            if (in_array($user_id, $department_user_array[$endorsval["endorsed_id"]])) {
                                $displayflag = 1;
                            }
                        }

                        if (!in_array($endorsval["endorsed_id"], $departmentarray)) {
                            $departmentarray[] = $endorsval["endorsed_id"];
                        }
                    } elseif ($endorsval["endorsement_for"] == "entity") {
                        $endorsmentarray[$key]["reply"] = 0;
                        if ($user_id == $endorsval["endorser_id"] || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } elseif (isset($entity_user_array[$endorsval["endorsed_id"]])) {
                            if (in_array($user_id, $entity_user_array[$endorsval["endorsed_id"]])) {
                                $displayflag = 1;
                            }
                        }
                        if (!in_array($endorsval["endorsed_id"], $entityarray)) {
                            $entityarray[] = $endorsval["endorsed_id"];
                        }
                    } else {
                        if (!in_array($endorsval["endorsed_id"], $userarray)) {

                            $userarray[] = $endorsval["endorsed_id"];
                        }

                        if (in_array($user_id, array($endorsval["endorser_id"], $endorsval["endorsed_id"])) || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } else {
                            $endorsmentarray[$key]["reply"] = 0;
                        }
                    }

                    if (!in_array($endorsval["endorser_id"], $userarray)) {
                        $userarray[] = $endorsval["endorser_id"];
                    }
                    $corevaluearray = array();
                    foreach ($endorsecorevalue as $eval) {


                        if (!in_array($eval["value_id"], $corevaluearray)) {
                            if (isset($coreval[$eval["value_id"]])) {
                                $ncval = explode("&&&&", $coreval[$eval["value_id"]]);
                                $corevaluearray[] = array("name" => $ncval[0], "color_code" => $ncval[1]);
                            }
                        }
                    }
                    $endorsmentarray[$key]["corevalue"] = $corevaluearray;
                    $endorsmentarray[$key]["displayflag"] = $displayflag;
                }


                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userarray),
                    'fields' => array('id', 'fname', 'lname', 'image')
                ));
                $userdata = array();
                foreach ($userinfo as $userval) {

                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                }

                $department = array();
                $entity = array();
                if (!empty($departmentarray)) {
                    $departmentarr = $this->getOrgValues($org_id, "OrgDepartments", 1);
                    if (!empty($departmentarr)) {
                        foreach ($departmentarr as $dval) {
                            $department[$dval["id"]] = $dval["name"];
                        }
                    }
                }

                if (!empty($entityarray)) {
                    $entity1 = $this->getOrgValues($org_id, "Entity", 1);
                    if (!empty($entity1)) {
                        foreach ($entity1 as $dval) {
                            $entity[$dval["id"]] = $dval["name"];
                        }
                    }
                }
                $newarray = array();

                foreach ($endorsmentarray as $key => $eval) {
                    $key = $eval["endorse"]["id"];
                    $newarray[$key]["id"] = $eval["endorse"]["id"];
                    $newarray[$key]["is_like"] = $eval["like"];
                    $newarray[$key]["is_read"] = $eval["endorse"]["is_read"];
                    $newarray[$key]["endorsement_for"] = $eval["endorse"]["endorsement_for"];
                    $newarray[$key]["endorsed_id"] = $eval["endorse"]["endorsed_id"];
                    $newarray[$key]["endorser_id"] = $eval["endorse"]["endorser_id"];
//	$newarray[$key]["attatched_image"] =  $eval["attatched_image"];
// print_r($newarray);
                    if (isset($userdata[$eval["endorse"]["endorser_id"]])) {
                        $newuserdata = explode("&&&&", $userdata[$eval["endorse"]["endorser_id"]]);
                        $newarray[$key]["endorser_name"] = $newuserdata[0];
                        if (isset($newuserdata[1])) {

//$newarray[$key]["endorser_image"] = $newuserdata[1];

                            $needle = 'default.jpg';
                            if (strpos($newuserdata[1], $needle) !== false) {
                                $newarray[$key]["endorser_image"] = '';
                            } else {
                                $newarray[$key]["endorser_image"] = $newuserdata[1];
                            }
                        }
                    } else {
                        $newarray[$key]["endorser_name"] = "";
                    }

                    $newarray[$key]["type"] = $eval["endorse"]["type"];
                    if ($eval["displayflag"] == 1) {
                        $newarray[$key]["message"] = $eval["endorse"]["message"];
                    } else {
                        $newarray[$key]["message"] = "";
                    }
                    $newarray[$key]["like_count"] = $eval["endorse"]["like_count"];
                    $newarray[$key]["created"] = $eval["endorse"]["created"];
                    if ($eval["displayflag"] == 1) {
                        $newarray[$key]["imagecount"] = $eval["imagecount"];
                        $newarray[$key]["emojiscount"] = $eval["emojis_count"];
                        $newarray[$key]["bitmojiscount"] = $eval["bitmojis_count"];
                        $newarray[$key]["bitmojis_image"] = $eval["bitmojis_image"];
                        $newarray[$key]["bitmoji_images"] = $eval["bitmoji_images"];
                        $newarray[$key]["is_reply"] = $eval["reply"];
                    } else {
                        $newarray[$key]["imagecount"] = 0;
                        $newarray[$key]["emojiscount"] = 0;
                        $newarray[$key]["is_reply"] = 0;
                    }
                    $newarray[$key]["corevalues"] = $eval["corevalue"];

                    if ($eval["endorse"]["endorsement_for"] == "user") {
                        if (isset($userdata[$eval["endorse"]["endorsed_id"]])) {
                            $newuserdata = explode("&&&&", $userdata[$eval["endorse"]["endorsed_id"]]);
//pr($newuserdata); 
//                        pr($eval["endorse"]);
                            $newarray[$key]["endorsed_name"] = $newuserdata[0];
                            if (isset($newuserdata[1])) {
//$newarray[$key]["endorsed_image"] = $newuserdata[1];
                                $needle = 'default.jpg';
                                if (strpos($newuserdata[1], $needle) !== false) {
                                    $newarray[$key]["endorsed_image"] = '';
                                } else {
                                    $newarray[$key]["endorsed_image"] = $newuserdata[1];
                                }
                            }
                        }
                    } elseif ($eval["endorse"]["endorsement_for"] == "department") {
                        if (isset($department[$eval["endorse"]["endorsed_id"]]))
                            $newarray[$key]["endorsed_name"] = $department[$eval["endorse"]["endorsed_id"]];
                        else
                            $newarray[$key]["endorsed_name"] = '';
                    } elseif ($eval["endorse"]["endorsement_for"] == "entity") {
                        if (isset($entity[$eval["endorse"]["endorsed_id"]]))
                            $newarray[$key]["endorsed_name"] = $entity[$eval["endorse"]["endorsed_id"]];
                        else
                            $newarray[$key]["endorsed_name"] = '';
                    }
                    $newarray[$key]["list_type"] = 'endorse';

//$newarray[$key]["public_endorse_visible"] = $loggedInUser['current_org']['public_endorse_visible'];
                    $newarray[$key]["public_endorse_visible"] = $public_endorse_visible;

//Added by Babulal prasad to show/hide public message according to setting to show/hide public endorsment message
                    if (!in_array($user_id, array($eval["endorse"]["endorser_id"], $eval["endorse"]["endorsed_id"]))) {
                        if ($eval["endorse"]["type"] == 'standard' && $public_endorse_visible == 0) {
//$newarray[$key]["message"] = "";
                        } else {
                            $newarray[$key]["message"] = $eval["endorse"]["message"];
                        }
                    }
                }
                $endorseServerTime = $serverCurrentTime;
                $returndata1 = array("endorse_data" => $newarray, "total_page" => $totalpage, "server_time" => $serverCurrentTime);
//pr($returndata1); 



                /* GETTING POST DATA START */

                $conditionarrayPost = array();
                $conditionarrayPost["Post.id"] = $postFeedIds;
                $params['conditions'] = $conditionarrayPost;
                $params['order'] = 'Post.created desc';

                $params['joins'] = array(
                    array(
                        'table' => 'post_likes',
                        'alias' => 'PostLike',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Post.id = PostLike.post_id ',
                            'PostLike.user_id =' . $user_id
                        )
                    ),
                    array(
                        'table' => 'post_schedules',
                        'alias' => 'PostSchedule',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Post.id = PostSchedule.post_id ',
                        )
                    )
                );

//                $params['fields'] = "*,UNIX_TIMESTAMP(PostSchedule.utc_post_datetime) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $params['fields'] = "*,UNIX_TIMESTAMP(Post.post_publish_date) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $params['order'] = 'Post.created desc';
//$params['group'] = 'Post.id';
// pr($params); exit;
//$this->Post->unbindModel(array('hasMany' => array('PostAttachments')));
//$this->Endorsement->bindModel(array('hasMany' => array('EndorseCoreValues')));
                $endorsement = $this->Post->find("all", $params);
//                $log = $this->Post->getDataSource()->getLog(false, false);
//                pr($log);

                $endorsmentarray = array();
                $departmentarray = array();
                $entityarray = array();
                $userarray = array();
                $core_values = $this->getOrgValues($org_id, "OrgCoreValues", 1);
                $coreval = array();
                foreach ($core_values as $cvalue) {
                    $coreval[$cvalue["id"]] = $cvalue["name"] . "&&&&" . $cvalue["color_code"];
                }

                $serverCurrentTime = "";

                foreach ($endorsement as $key => $value) {
                    $postAttachmentImages = array();
//                    if (isset($value['Post']['image_count']) && $value['Post']['image_count'] > 0) {
//                        $endorsmentarray[$key]["post_image"] = array(Router::url('/', true) . "app/webroot/" . POST_IMAGE_DIR . $value["PostAttachment"]['name']);
//                    } else if (isset($value['Post']['image_count']) && $value['Post']['image_count'] < 1 && $value['Post']['emojis_count'] > 0) {
//                        $endorsmentarray[$key]["post_image"] = "";
//                        $PostAttachData = $this->PostAttachment->getEmojiByPostId($value['Post']['id']);
//                        $endorsmentarray[$key]["post_image"] = array(Router::url('/', true) . EMOJIS_IMAGE_DIR . $PostAttachData);
//                    } else {
//                        $endorsmentarray[$key]["post_image"] = array();
//                    }
                    $postAttachmentemoji = array();
                    $postAttachmentimg = array();
                    $postAttachmentFiles = array();

                    if (isset($value["PostAttachments"]) && !empty($value["PostAttachments"])) {
                        foreach ($value["PostAttachments"] as $index => $postAttchment) {
                            if ($postAttchment['type'] == 'emojis') {
                                //$temppImages = Router::url('/', true) . EMOJIS_IMAGE_DIR . $postAttchment['name'];
                                $temppImages = Router::url('/', true) . BITMOJIS_IMAGE_DIR . $postAttchment['name'];
                                if (strpos($temppImages, 'localhost') < 0 || strpos($temppImages, 'staging') < 0) {
                                    $temppImages = str_replace("http", "https", $temppImages);
                                }
//                                $temppImages = str_replace("http", "https", $temppImages);

                                $postAttachmentemoji[] = $temppImages;
                            } else if ($postAttchment['type'] == 'image') {
                                $temppImages1 = Router::url('/', true) . "app/webroot/" . POST_IMAGE_DIR . $postAttchment['name'];
                                $temppImages1 = str_replace("http", "https", $temppImages1);
                                $postAttachmentimg[] = $temppImages1;
                            } else if ($postAttchment['type'] == 'files') {
                                $temppImages2 = Router::url('/', true) . "app/webroot/" . POST_FILE_DIR . $postAttchment['name'];
                                $temppImages2 = str_replace("http", "https", $temppImages2);
                                $postAttachmentFiles[] = $temppImages2;
                            }
                        }
                        $postAttachmentImages = array_merge($postAttachmentimg, $postAttachmentemoji);
                    }

                    $displayflag = 0;
                    $endorsval = $value["Post"];
                    $publishedDate = $value[0]["create_date"];
                    $endorsval["created"] = $value[0]["create_date"];
                    $serverCurrentTime = $value[0]["curr_time"];
                    $endorsimgcount = $endorsval["image_count"];
                    $endorsmentarray[$key]["post"] = $endorsval;
                    $endorsmentarray[$key]["post_attachment"] = $postAttachmentImages;
                    $endorsmentarray[$key]["post_attachment_files"] = count($postAttachmentFiles);
                    $endorsmentarray[$key]["imagecount"] = $endorsval["image_count"];
                    $endorsmentarray[$key]["emojis_count"] = $endorsval["emojis_count"];
//                    $endorsmentarray[$key]["bitmojis_count"] = $endorsval["bitmojis_count"];
                    $endorsmentarray[$key]["user_id"] = $endorsval["user_id"];
                    $endorsmentarray[$key]["published_date"] = $publishedDate;

                    $endorsmentarray[$key]["reply"] = $endorsval["is_reply"];

                    if ($value["PostLike"]["id"] != "") {
                        $endorsmentarray[$key]["like"] = 1;
                    } else {
                        $endorsmentarray[$key]["like"] = 0;
                    }

                    $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                    $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $endorsval["user_id"], "UserOrganization.organization_id" => $org_id)));

                    $jobTitle = '';

                    if (isset($userOrganization['UserOrganization']['job_title_id']) && $userOrganization['UserOrganization']['job_title_id'] != '') {
                        $jobTitleData = $this->OrgJobTitle->findById($userOrganization['UserOrganization']['job_title_id']);
                        if (isset($jobTitleData['OrgJobTitle']) && !empty($jobTitleData['OrgJobTitle'])) {
                            $jobTitle = $jobTitleData['OrgJobTitle']['title'];
                        } else {
                            $jobTitle = '';
                        }
                    }
                    $endorsmentarray[$key]["user_job_title"] = $jobTitle;
                    $userarray[] = $endorsval["user_id"];
                    $endorsmentarray[$key]["displayflag"] = $displayflag;
                }
//pr($endorsmentarray);exit;
                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userarray),
                    'fields' => array('id', 'fname', 'lname', 'image', 'about')
                ));
//pr($userinfo);
                $userdata = array();
                foreach ($userinfo as $userval) {
                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                    $userabout[$userval["User"]["id"]]['about'] = trim($userval["User"]["about"]);
                }

                $newarray = array();
//pr($endorsmentarray); exit;
                foreach ($endorsmentarray as $key => $eval) {
                    $key = $eval["post"]["id"];
                    $newarray[$key]["id"] = $eval["post"]["id"];
                    $newarray[$key]["is_like"] = $eval["like"];
                    $newarray[$key]["is_read"] = $eval["post"]["is_read"];
                    $newarray[$key]["post_id"] = $eval["post"]["id"];
                    $newarray[$key]["user_id"] = $eval["post"]["user_id"];
                    $newarray[$key]["post_image"] = $eval["post_attachment"];
                    $newarray[$key]["post_files"] = $eval["post_attachment_files"];
                    $newarray[$key]["comments_count"] = $eval['post']["comments_count"];
                    $newarray[$key]["user_job_title"] = $eval["user_job_title"];

                    if (isset($userdata[$eval["post"]["user_id"]])) {
                        $newuserdata = explode("&&&&", $userdata[$eval["post"]["user_id"]]);
                        $newarray[$key]["user_name"] = $newuserdata[0];
                        $newarray[$key]["user_about"] = $userabout[$userval["User"]["id"]]['about'];
                        if (isset($newuserdata[1])) {

                            $needle = 'default.jpg';
                            if (strpos($newuserdata[1], $needle) !== false) {
                                $newarray[$key]["user_image"] = '';
                            } else {
                                $newarray[$key]["user_image"] = $newuserdata[1];
                            }
                        }
                    } else {
                        $newarray[$key]["user_name"] = "";
                    }
//pr($newarray);
//exit;
                    $newarray[$key]["message"] = $eval["post"]["message"];
                    $newarray[$key]["title"] = $eval["post"]["title"];

                    $newarray[$key]["like_count"] = $eval["post"]["like_count"];
//$newarray[$key]["created"] = $eval["post"]["created"];
                    $newarray[$key]["created"] = $eval["published_date"];

                    $newarray[$key]["imagecount"] = $eval["imagecount"];
                    $newarray[$key]["emojiscount"] = $eval["emojis_count"];
//                    $newarray[$key]["bitmojiscount"] = $eval["bitmojis_count"];
                    $newarray[$key]["is_reply"] = $eval["reply"];
                    $newarray[$key]["list_type"] = 'wallpost';
                }
                if ($loggedInUser['current_org']['joined'] == 0) {

                    $updateArray['UserOrganization.joined'] = 1;
//$updateArray['live_updated'] = '"' . date("Y-m-d H:i:s") . '"';
//if ($this->UserOrganization->updateAll(array("UserOrganization.joined" => 1), array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                    if ($this->UserOrganization->updateAll($updateArray, array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                        $this->Session->write('Auth.User.current_org.joined', 1);
                        $this->JoinOrgCode->updateAll(array("is_expired" => 1), array("email" => $loggedInUser['email'], "organization_id" => $org_id));
                    }
                }

                $returndata2 = array("post_data" => $newarray, "total_page" => $totalpage, "server_time" => $serverCurrentTime);

//pr($returndata2);
                $liveFeedDATA = array();
                foreach ($feedArray as $feedId => $feedData) {
                    if ($feedData['feed_type'] == 'post') {
                        if (isset($returndata2['post_data'][$feedData['feed_id']]))
                            $liveFeedDATA[] = $returndata2['post_data'][$feedData['feed_id']];
                    } else {
                        if (isset($returndata1['endorse_data'][$feedData['feed_id']]))
                            $liveFeedDATA[] = $returndata1['endorse_data'][$feedData['feed_id']];
                    }
                }
                $resServerTime = '';
                if (isset($endorseServerTime) && $endorseServerTime != '') {
                    $resServerTime = $endorseServerTime;
                } elseif (isset($serverCurrentTime) && $serverCurrentTime != '') {
                    $resServerTime = $serverCurrentTime;
                }
//                pr($liveFeedDATA);
//                exit;

                /* GETTING POST DATA END */
                $returndata = array("endorse_data" => $liveFeedDATA, "total_page" => $totalpage, "server_time" => $resServerTime);




                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Organization Endorsement ",
                        "data" => $returndata),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => ""),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getLiveFeeds() {

        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
//            pr($loggedInUser); exit;
            $user_id = $loggedInUser['id'];
//$loggedinUser = $this->Auth->user();
//        $params = array();
//        $params['fields'] = "*";
//        $params['conditions'] = array("DefaultOrg.user_id" => $loggedinUser['id']);
//        $defaultOrganization = $this->DefaultOrg->find("first", $params);
            $department_id = $this->Common->getUserCurrentDept($user_id, $loggedInUser['current_org']['id']);
            $entity_id = $this->Common->getUserCurrentSubOrg($user_id, $loggedInUser['current_org']['id']);

//            pr($loggedInUser); exit;
            if (isset($loggedInUser['current_org'])) {
                $org_id = $loggedInUser['current_org']['id'];
                $keyword = "";

                $params = array();
                $params['fields'] = "user_id,entity_id,department_id";
                $params['conditions'] = array(
                    "UserOrganization.organization_id" => $org_id, "UserOrganization.status" => 1);

                $userdepartmentorg = $this->UserOrganization->find("all", $params);
//                pr($userdepartmentorg);
//                exit;
                $entity_user_array = array();
                $department_user_array = array();
                foreach ($userdepartmentorg as $userorgval) {

                    if ($userorgval["UserOrganization"]["entity_id"] > 0) {
                        $entity_user_array[$userorgval["UserOrganization"]["entity_id"]][] = $userorgval["UserOrganization"]["user_id"];
                    }
                    if ($userorgval["UserOrganization"]["department_id"] > 0) {
                        $department_user_array[$userorgval["UserOrganization"]["department_id"]][] = $userorgval["UserOrganization"]["user_id"];
                    }
                }
//pr($entity_user_array);
//echo "<hr>";
//pr($department_user_array);exit;
//

                if (isset($this->request->data["type"]) && $this->request->data["type"] != "") {
                    $type = $this->request->data["type"];
                }

                $subcenterId = '';


                if (isset($this->request->data["subcenter_id"]) && $this->request->data["subcenter_id"] != "") {
                    if ($this->request->data["subcenter_id"] == 0) {
                        $subcenterId = '';
                    } else {
                        $subcenterId = $this->request->data["subcenter_id"];
                    }
                }

                $feed_type = $feed_id = "";
                $endorse_search_id = "";
//                pr($this->request->data);exit;
                if (isset($this->request->data["feed_type"]) && $this->request->data["feed_type"] != "") {
                    $feed_type = $this->request->data["feed_type"];
                    if (isset($this->request->data["feed_id"]) && $this->request->data["feed_id"] != '') {
                        $feed_id = $this->request->data["feed_id"];
                    }
                }




                $start_date = "";
                $end_date = "";

                $limit = Configure::read("pageLimit");
                if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
                    $page = $this->request->data["page"];
                    $offset = $page * $limit;
                } else {
                    $page = 1;
                    $offset = 0;
                }

                /* NEW CODE START */
//Getting total feeds count
                $NEWconditionarray = $postFeedIds = $endorseFeedIds = $feedArray = array();
//pr($this->request->data);
                if (isset($this->request->data["endorse_type"]) && $this->request->data["endorse_type"] == "user") {
                    $NEWconditionarray = array("user_id like '%" . '"' . $this->request->data["endorse_id"] . '"' . "%'");
                }

                if (isset($subcenterId) && $subcenterId != "") {
                    $NEWconditionarray["AND"] = array('OR' => array("FeedTran.subcenter_by" => $subcenterId, "FeedTran.subcenter_for" => $subcenterId));
                    //"FeedTran.subcenter_for"] = $subcenterId;
                }



                if (isset($this->request->data["endorse_type"]) && $this->request->data["endorse_type"] == "department") {
                    $NEWconditionarray["FeedTran.dept_id"] = $this->request->data["endorse_id"];
                }


                if (isset($feed_type) && $feed_type != '') {
                    if ($feed_type == 'following') {
                        /* Added by Babulal prasad at @11MAY2021
                         * if filter is following then get following users list and get the feed
                         */
                        $this->loadModel("UserFollowing");
                        $followingIdsArray = array();
                        $userFollowings = $this->UserFollowing->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $user_id, 'status' => 1)));
                        if (!empty($userFollowings)) {
                            $userFollowings = array_shift($userFollowings);
                            $userFollowings = $userFollowings['UserFollowing'];
                            $uFollowingID = $userFollowings['id'];
                            $followingIdsArray = json_decode($userFollowings['following_ids']);
                            $followingIdsArray = array_filter($followingIdsArray); // Removing empty value/ids
                        }

//                        $NEWconditionarray["FeedTran.user_id"] = $followingIdsArray;
                    } else { //If feed_type != 'following'
                        $NEWconditionarray["FeedTran.feed_type"] = $feed_type;
                    }

                    if (isset($feed_id) && $feed_id != '') {
                        $NEWconditionarray["FeedTran.feed_id"] = $feed_id;
                    }
                }

                if (isset($type) && ($type == 'endorser' || $type == 'endorsed')) {
                    $NEWconditionarray["FeedTran.feed_type"] = 'endorse';
//                    $NEWconditionarray["FeedTran.user_id"] = "like '%'".$user_id."'%'";
                }


                $start_date = "";
                $end_date = "";
             /*   if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                    $start_date = $this->request->data["start_date"];
                }
                if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                    $end_date = $this->request->data["end_date"];
                }
*/



                /* Condition added by Babulal prasad to filter scheduled posts */
//                                $NEWconditionarray["FeedTran.org_id"] = $org_id;
                $NEWconditionarray["FeedTran.status"] = 1;


//                        pr($Usercondition ); exit;

                if ($feed_type == 'following') {

                    $Usercondition = implode(" OR ", array_map(function($ids) {
                                return "JSON_CONTAINS(user_id, JSON_ARRAY('$ids'))";
                            }, $followingIdsArray));
//                            pr($Usercondition); exit;
                            
                    $NEWconditionarray["OR"] = array(
                        array("AND" => array("FeedTran.visibility_check" => 1, 'FeedTran.org_id' => $org_id, array("OR" => array("visible_user_ids" => $followingIdsArray,
                                        "visible_sub_org like '%" . '"' . $entity_id . '"' . "%'", "visible_dept like '%" . '"' . $department_id . '"' . "%'",
                                        $Usercondition)))),
                        array("visibility_check" => 0, 'FeedTran.org_id' => $org_id, $Usercondition)
                    );
                } else {
                    
                    $NEWconditionarray["OR"] = array(
                        array("AND" => array("FeedTran.visibility_check" => 1, 'FeedTran.org_id' => $org_id, array("OR" => array("visible_user_ids like '%" . '"' . $user_id . '"' . "%'",
                                        "visible_sub_org like '%" . '"' . $entity_id . '"' . "%'", "visible_dept like '%" . '"' . $department_id . '"' . "%'",
                                        "FeedTran.user_id like '%" . '"' . $user_id . '"' . "%'")))),
                        array("visibility_check" => 0, 'FeedTran.org_id' => $org_id /*, "user_id like '%" . '"' . $user_id . '"' . "%'",*/)
                    );
                }
             /*   if ($start_date != "") {
                    $NEWconditionarray["FeedTran.created >= "] = date("Y-m-d 00:00:00", $start_date);
                }
                if ($end_date != "") {
                    $NEWconditionarray["FeedTran.created <= "] = date("Y-m-d 23:59:59", $end_date);
                } */
//(visibility_check = 1 and ((visible_user_ids like '%"2926"%' ) OR (visible_dept like'%"539"%') OR (visible_dept like'%"0"%') ) and org_id = 123  ) 
//OR 
//(visibility_check = 0 and org_id = 123  )


                /*                 * ***************** */

//                pr($NEWconditionarray);
//                exit;
//                $NEWparams['fields'] = "*";
                $NEWparams['fields'] = "count(*) as cnt";
                $NEWparams['conditions'] = $NEWconditionarray;
//$NEWparams['order'] = 'FeedTran.created desc';
                $NEWparams['order'] = 'FeedTran.publish_date desc';
                $totaleFeeds = $this->FeedTran->find("all", $NEWparams);
//                pr($totaleFeeds);
//                exit;
//                echo $this->FeedTran->getLastQuery();
//                exit;
//                $log = $this->FeedTran->getDataSource()->getLog(false, false);
//                pr($log);
//                exit;

                $totalLiveFeed = $totaleFeeds[0][0]["cnt"];
                $totalpage = ceil($totalLiveFeed / $limit);
                $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id)));

                if ($userOrganization['UserOrganization']['user_role'] != 2) {
                    $NEWconditionarray["FeedTran.endorse_type !="] = 'private';
                }

//Getting live feeds
//$paramsFeed['fields'] = "*,UNIX_TIMESTAMP(created) as create_date, UNIX_TIMESTAMP() as curr_time ";
//                pr($NEWconditionarray);
//                exit;
                $paramsFeed['fields'] = "*";
                $paramsFeed['conditions'] = $NEWconditionarray;
                $paramsFeed['limit'] = $limit;
                $paramsFeed['page'] = $page;
                $paramsFeed['offset'] = $offset;

//                $paramsFeed['joins'] = array(
//                    array(
//                        'table' => 'endorsements',
//                        'alias' => 'Endorsement',
//                        'type' => 'LEFT',
//                        'conditions' => array(
//                            'FeedTran.feed_id = Endorsement.id',
//                            'FeedTran.feed_type = "endorse"',
//                            'Endorsement.type != "private"'
//                        )
//                    )
//                );
//                pr($paramsFeed);
                $paramsFeed['order'] = 'FeedTran.created desc';
                $FeedTranRes = $this->FeedTran->find("all", $paramsFeed);
//                $log = $this->FeedTran->getDataSource()->getLog(false, false);
//                pr($log);
//                exit;
//                pr($FeedTranRes);
//                exit;
                if (!empty($FeedTranRes)) {
                    foreach ($FeedTranRes as $index => $feedTransData) {
                        $feedArray[$feedTransData['FeedTran']['id']]['feed_id'] = $feedTransData['FeedTran']['feed_id'];
                        $feedArray[$feedTransData['FeedTran']['id']]['feed_type'] = $feedTransData['FeedTran']['feed_type'];
                        if ($feedTransData['FeedTran']['feed_type'] == 'post') {
                            $postFeedIds[] = $feedTransData['FeedTran']['feed_id'];
                        } else {
                            $endorseFeedIds[] = $feedTransData['FeedTran']['feed_id'];
                        }
                    }
                }

                $updateArray['live_updated'] = '"' . date("Y-m-d H:i:s") . '"';
//if ($this->UserOrganization->updateAll(array("UserOrganization.joined" => 1), array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                $this->UserOrganization->updateAll($updateArray, array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id));

//                pr($feedArray);
//                pr($postFeedIds);
//                pr($endorseFeedIds);
//                exit;

                /* NEW CODE END */
                $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id)));
//                pr($userOrganization);
//                exit;

                $params = array();
                $params['fields'] = "count(*) as cnt";


                if ($type == "endorser") {
                    $conditionarray["Endorsement.endorser_id"] = $user_id;
                } elseif ($type == "endorsed") {
//                    $updateArray['ndorsed_updated'] = '"' . date("Y-m-d H:i:s") . '"';
                    $conditionarray["Endorsement.endorsed_id"] = $user_id;
                } else {
//                    $updateArray['live_updated'] = '"' . date("Y-m-d H:i:s") . '"';
//                    $conditionarray["Endorsement.type != "] = "private";
                }



//Condition changed after discuss with rohan @2-feb-2018 by Babulal Prasad
//if ($userOrganization['UserOrganization']['user_role'] != 2) {
                $conditionarray["Endorsement.type != "] = "private";
// }
//                pr($endorseFeedIds);
                $conditionarray["Endorsement.id"] = $endorseFeedIds;

                $params['conditions'] = $conditionarray;
                $params['order'] = 'Endorsement.created desc';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseCoreValues', 'EndorseReplies')));
                $params['joins'] = array(
                    array(
                        'table' => 'endorsement_likes',
                        'alias' => 'EndorsementLike',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Endorsement.id =EndorsementLike.endorsement_id ',
                            'EndorsementLike.user_id =' . $user_id
                        )
                    )
//                    , array(
//                        'table' => 'endorse_attachments',
//                        'alias' => 'EndorseAttachment',
//                        'type' => 'LEFT',
//                        'conditions' => array(
//                            'Endorsement.id = EndorseAttachment.endorsement_id ',
//                            'EndorseAttachment.type = "emojis"'
//                        )
//                    )
                );

                $params['fields'] = "*,UNIX_TIMESTAMP(Endorsement.created) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $params['order'] = 'Endorsement.created desc';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseCoreValues', 'EndorseReplies')));
                $this->Endorsement->bindModel(array('hasMany' => array('EndorseCoreValues')));
//pr($params);
//exit;
                $endorsement = $this->Endorsement->find("all", $params);
//                echo $this->Endorsement->getLastQuery();exit;
//                $log = $this->Endorsement->getDataSource()->getLog(false, false);
//                pr($log);
//                exit;
//                pr($endorsement);
//                exit;
                $endorsmentarray = array();
                $departmentarray = array();
                $entityarray = array();
                $userarray = array();
                $core_values = $this->getOrgValues($org_id, "OrgCoreValues", 1);
                $coreval = array();
                foreach ($core_values as $cvalue) {
                    $coreval[$cvalue["id"]] = $cvalue["name"] . "&&&&" . $cvalue["color_code"];
                }

                $organizationDATA = $this->getOrgValues($org_id, "Organization", 1);
                $public_endorse_visible = 0;
                if ($organizationDATA[0]['allow_comments'] == 1 && $organizationDATA[0]['public_endorse_visible'] == 1) {
                    $public_endorse_visible = 1;
                }

                $serverCurrentTime = "";
//                pr($endorsement);exit;
                $emojis_url = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
                $emojis_url = str_replace("http", "https", $emojis_url);
//                pr($endorsement); exit;

                foreach ($endorsement as $key => $value) {
//                    pr($value["EndorseAttachments"]);
                    $displayflag = 0;
                    $endorsval = $value["Endorsement"];
                    $endorsval["created"] = $value[0]["create_date"];
                    $serverCurrentTime = $value[0]["curr_time"];
                    $endorsimgcount = $endorsval["image_count"];
                    $endorsecorevalue = $value["EndorseCoreValues"];
                    $endorsmentarray[$key]["endorse"] = $endorsval;
                    $endorsmentarray[$key]["imagecount"] = $endorsval["image_count"];

//                    echo "EMOJI COUNT : ".count($value["EndorseAttachments"]);

                    $endorsmentarray[$key]["emojis_count"] = count($value["EndorseAttachments"]);
                    $endorsmentarray[$key]["bitmojis_count"] = $endorsval["bitmojis_count"];

//                    if (isset($value["EndorseAttachment"]['name']) && $value["EndorseAttachment"]['name'] != '') {
                    if (!empty($value["EndorseAttachments"])) {
                        $endorsmentarray[$key]["bitmojis_image"] = '';
//                        pr($value["EndorseAttachments"]); exit;
                        if (count($value["EndorseAttachments"]) > 0) {
                            //pr($endorsementAttachment);
                            foreach ($value["EndorseAttachments"] as $iCount => $endorsementAttachment) {
                                if ($endorsementAttachment["type"] == 'image') {
                                    $tempEndorseImages = Router::url('/', true) . "app/webroot/" . ENDORSE_IMAGE_DIR . "small/";
                                    //$tempEndorseImages = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
                                } else {
                                    $tempEndorseImages = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
                                }
                                if (strpos($tempEndorseImages, 'localhost') < 0 || strpos($tempEndorseImages, 'staging') < 0) {
                                    $tempEndorseImages = str_replace("http", "https", $tempEndorseImages);
                                }

                                $endorsmentarray[$key]["bitmoji_images"][] = $tempEndorseImages . $endorsementAttachment['name'];
                            }
                        }
                    } else {
                        $endorsmentarray[$key]["bitmojis_image"] = '';
                        $endorsmentarray[$key]["bitmoji_images"] = array();
                    }


                    $endorsmentarray[$key]["reply"] = $endorsval["is_reply"];

                    if ($value["EndorsementLike"]["id"] != "") {
                        $endorsmentarray[$key]["like"] = 1;
                    } else {
                        $endorsmentarray[$key]["like"] = 0;
                    }
//$endorsmentarray[$key]["attatched_image"]=$value["EndorseAttachments"];

                    $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                    $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id)));
//pr($userOrganization['UserOrganization']['public_ndorse_visible_tc']); exit;


                    if ($endorsval["endorsement_for"] == "department") {
                        $endorsmentarray[$key]["reply"] = 0;
                        if ($user_id == $endorsval["endorser_id"] || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } elseif (isset($department_user_array[$endorsval["endorsed_id"]])) {
                            if (in_array($user_id, $department_user_array[$endorsval["endorsed_id"]])) {
                                $displayflag = 1;
                            }
                        }

                        if (!in_array($endorsval["endorsed_id"], $departmentarray)) {
                            $departmentarray[] = $endorsval["endorsed_id"];
                        }
                    } elseif ($endorsval["endorsement_for"] == "entity") {
                        $endorsmentarray[$key]["reply"] = 0;
                        if ($user_id == $endorsval["endorser_id"] || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } elseif (isset($entity_user_array[$endorsval["endorsed_id"]])) {
                            if (in_array($user_id, $entity_user_array[$endorsval["endorsed_id"]])) {
                                $displayflag = 1;
                            }
                        }
                        if (!in_array($endorsval["endorsed_id"], $entityarray)) {
                            $entityarray[] = $endorsval["endorsed_id"];
                        }
                    } else {
                        if (!in_array($endorsval["endorsed_id"], $userarray)) {

                            $userarray[] = $endorsval["endorsed_id"];
                        }

                        if (in_array($user_id, array($endorsval["endorser_id"], $endorsval["endorsed_id"])) || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } else {
                            $endorsmentarray[$key]["reply"] = 0;
                        }
                    }

                    if (!in_array($endorsval["endorser_id"], $userarray)) {
                        $userarray[] = $endorsval["endorser_id"];
                    }
                    $corevaluearray = array();
                    foreach ($endorsecorevalue as $eval) {


                        if (!in_array($eval["value_id"], $corevaluearray)) {
                            if (isset($coreval[$eval["value_id"]])) {
                                $ncval = explode("&&&&", $coreval[$eval["value_id"]]);
                                $corevaluearray[] = array("name" => $ncval[0], "color_code" => $ncval[1]);
                            }
                        }
                    }
                    $endorsmentarray[$key]["corevalue"] = $corevaluearray;
                    $endorsmentarray[$key]["displayflag"] = $displayflag;
                }


                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userarray),
                    'fields' => array('id', 'fname', 'lname', 'image')
                ));
                $userdata = array();
                foreach ($userinfo as $userval) {

                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                }

                $department = array();
                $entity = array();
                if (!empty($departmentarray)) {
                    $departmentarr = $this->getOrgValues($org_id, "OrgDepartments", 1);
                    if (!empty($departmentarr)) {
                        foreach ($departmentarr as $dval) {
                            $department[$dval["id"]] = $dval["name"];
                        }
                    }
                }

                if (!empty($entityarray)) {
                    $entity1 = $this->getOrgValues($org_id, "Entity", 1);
                    if (!empty($entity1)) {
                        foreach ($entity1 as $dval) {
                            $entity[$dval["id"]] = $dval["name"];
                        }
                    }
                }
                $newarray = array();

                foreach ($endorsmentarray as $key => $eval) {
                    $key = $eval["endorse"]["id"];
                    $newarray[$key]["id"] = $eval["endorse"]["id"];
                    $newarray[$key]["is_like"] = $eval["like"];
                    $newarray[$key]["is_read"] = $eval["endorse"]["is_read"];
                    $newarray[$key]["endorsement_for"] = $eval["endorse"]["endorsement_for"];
                    $newarray[$key]["endorsed_id"] = $eval["endorse"]["endorsed_id"];
                    $newarray[$key]["endorser_id"] = $eval["endorse"]["endorser_id"];
//	$newarray[$key]["attatched_image"] =  $eval["attatched_image"];
// print_r($newarray);
                    if (isset($userdata[$eval["endorse"]["endorser_id"]])) {
                        $newuserdata = explode("&&&&", $userdata[$eval["endorse"]["endorser_id"]]);
                        $newarray[$key]["endorser_name"] = $newuserdata[0];
                        if (isset($newuserdata[1])) {

//$newarray[$key]["endorser_image"] = $newuserdata[1];

                            $needle = 'default.jpg';
                            if (strpos($newuserdata[1], $needle) !== false) {
                                $newarray[$key]["endorser_image"] = '';
                            } else {
                                $newarray[$key]["endorser_image"] = $newuserdata[1];
                            }
                        }
                    } else {
                        $newarray[$key]["endorser_name"] = "";
                    }

                    $newarray[$key]["type"] = $eval["endorse"]["type"];
                    if ($eval["displayflag"] == 1) {
                        $newarray[$key]["message"] = $eval["endorse"]["message"];
                    } else {
                        $newarray[$key]["message"] = "";
                    }
                    $newarray[$key]["like_count"] = $eval["endorse"]["like_count"];
                    $newarray[$key]["created"] = $eval["endorse"]["created"];
                    if ($eval["displayflag"] == 1) {
                        $newarray[$key]["imagecount"] = $eval["imagecount"];
                        $newarray[$key]["emojiscount"] = $eval["emojis_count"];
                        $newarray[$key]["bitmojiscount"] = $eval["bitmojis_count"];
                        $newarray[$key]["bitmojis_image"] = $eval["bitmojis_image"];
                        $newarray[$key]["bitmoji_images"] = $eval["bitmoji_images"];
                        $newarray[$key]["is_reply"] = $eval["reply"];
                    } else {
                        $newarray[$key]["imagecount"] = 0;
                        $newarray[$key]["emojiscount"] = 0;
                        $newarray[$key]["is_reply"] = 0;
                    }
                    $newarray[$key]["corevalues"] = $eval["corevalue"];

                    if ($eval["endorse"]["endorsement_for"] == "user") {
                        if (isset($userdata[$eval["endorse"]["endorsed_id"]])) {
                            $newuserdata = explode("&&&&", $userdata[$eval["endorse"]["endorsed_id"]]);
//pr($newuserdata); 
//                        pr($eval["endorse"]);
                            $newarray[$key]["endorsed_name"] = $newuserdata[0];
                            if (isset($newuserdata[1])) {
//$newarray[$key]["endorsed_image"] = $newuserdata[1];
                                $needle = 'default.jpg';
                                if (strpos($newuserdata[1], $needle) !== false) {
                                    $newarray[$key]["endorsed_image"] = '';
                                } else {
                                    $newarray[$key]["endorsed_image"] = $newuserdata[1];
                                }
                            }
                        }
                    } elseif ($eval["endorse"]["endorsement_for"] == "department") {
                        if (isset($department[$eval["endorse"]["endorsed_id"]]))
                            $newarray[$key]["endorsed_name"] = $department[$eval["endorse"]["endorsed_id"]];
                        else
                            $newarray[$key]["endorsed_name"] = '';
                    } elseif ($eval["endorse"]["endorsement_for"] == "entity") {
                        if (isset($entity[$eval["endorse"]["endorsed_id"]]))
                            $newarray[$key]["endorsed_name"] = $entity[$eval["endorse"]["endorsed_id"]];
                        else
                            $newarray[$key]["endorsed_name"] = '';
                    }
                    $newarray[$key]["list_type"] = 'endorse';

//$newarray[$key]["public_endorse_visible"] = $loggedInUser['current_org']['public_endorse_visible'];
                    $newarray[$key]["public_endorse_visible"] = $public_endorse_visible;

//Added by Babulal prasad to show/hide public message according to setting to show/hide public endorsment message
                    if (!in_array($user_id, array($eval["endorse"]["endorser_id"], $eval["endorse"]["endorsed_id"]))) {
                        if ($eval["endorse"]["type"] == 'standard' && $public_endorse_visible == 0) {
//$newarray[$key]["message"] = "";
                        } else {
                            $newarray[$key]["message"] = $eval["endorse"]["message"];
                        }
                    }
                }
                $endorseServerTime = $serverCurrentTime;
                $returndata1 = array("endorse_data" => $newarray, "total_page" => $totalpage, "server_time" => $serverCurrentTime);
//pr($returndata1); 



                /* GETTING POST DATA START */

                $conditionarrayPost = array();
                $conditionarrayPost["Post.id"] = $postFeedIds;
                $params['conditions'] = $conditionarrayPost;
                $params['order'] = 'Post.created desc';

                $params['joins'] = array(
                    array(
                        'table' => 'post_likes',
                        'alias' => 'PostLike',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Post.id = PostLike.post_id ',
                            'PostLike.user_id =' . $user_id
                        )
                    ),
                    array(
                        'table' => 'post_schedules',
                        'alias' => 'PostSchedule',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Post.id = PostSchedule.post_id ',
                        )
                    )
                );

//                $params['fields'] = "*,UNIX_TIMESTAMP(PostSchedule.utc_post_datetime) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $params['fields'] = "*,UNIX_TIMESTAMP(Post.post_publish_date) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $params['order'] = 'Post.created desc';
//$params['group'] = 'Post.id';
// pr($params); exit;
//$this->Post->unbindModel(array('hasMany' => array('PostAttachments')));
//$this->Endorsement->bindModel(array('hasMany' => array('EndorseCoreValues')));
                $endorsement = $this->Post->find("all", $params);
//                $log = $this->Post->getDataSource()->getLog(false, false);
//                pr($log);

                $endorsmentarray = array();
                $departmentarray = array();
                $entityarray = array();
                $userarray = array();
                $core_values = $this->getOrgValues($org_id, "OrgCoreValues", 1);
                $coreval = array();
                foreach ($core_values as $cvalue) {
                    $coreval[$cvalue["id"]] = $cvalue["name"] . "&&&&" . $cvalue["color_code"];
                }

                $serverCurrentTime = "";

                foreach ($endorsement as $key => $value) {
                    $postAttachmentImages = array();
//                    if (isset($value['Post']['image_count']) && $value['Post']['image_count'] > 0) {
//                        $endorsmentarray[$key]["post_image"] = array(Router::url('/', true) . "app/webroot/" . POST_IMAGE_DIR . $value["PostAttachment"]['name']);
//                    } else if (isset($value['Post']['image_count']) && $value['Post']['image_count'] < 1 && $value['Post']['emojis_count'] > 0) {
//                        $endorsmentarray[$key]["post_image"] = "";
//                        $PostAttachData = $this->PostAttachment->getEmojiByPostId($value['Post']['id']);
//                        $endorsmentarray[$key]["post_image"] = array(Router::url('/', true) . EMOJIS_IMAGE_DIR . $PostAttachData);
//                    } else {
//                        $endorsmentarray[$key]["post_image"] = array();
//                    }
                    $postAttachmentemoji = array();
                    $postAttachmentimg = array();
                    $postAttachmentFiles = array();

                    if (isset($value["PostAttachments"]) && !empty($value["PostAttachments"])) {
                        foreach ($value["PostAttachments"] as $index => $postAttchment) {
                            if ($postAttchment['type'] == 'emojis') {
                                //$temppImages = Router::url('/', true) . EMOJIS_IMAGE_DIR . $postAttchment['name'];
                                $temppImages = Router::url('/', true) . BITMOJIS_IMAGE_DIR . $postAttchment['name'];
                                if (strpos($temppImages, 'localhost') < 0 || strpos($temppImages, 'staging') < 0) {
                                    $temppImages = str_replace("http", "https", $temppImages);
                                }
//                                $temppImages = str_replace("http", "https", $temppImages);

                                $postAttachmentemoji[] = $temppImages;
                            } else if ($postAttchment['type'] == 'image') {
                                $temppImages1 = Router::url('/', true) . "app/webroot/" . POST_IMAGE_DIR . $postAttchment['name'];
                                $temppImages1 = str_replace("http", "https", $temppImages1);
                                $postAttachmentimg[] = $temppImages1;
                            } else if ($postAttchment['type'] == 'files') {
                                $temppImages2 = Router::url('/', true) . "app/webroot/" . POST_FILE_DIR . $postAttchment['name'];
                                $temppImages2 = str_replace("http", "https", $temppImages2);
                                $postAttachmentFiles[] = $temppImages2;
                            }
                        }
                        $postAttachmentImages = array_merge($postAttachmentimg, $postAttachmentemoji);
                    }

                    $displayflag = 0;
                    $endorsval = $value["Post"];
                    $publishedDate = $value[0]["create_date"];
                    $endorsval["created"] = $value[0]["create_date"];
                    $serverCurrentTime = $value[0]["curr_time"];
                    $endorsimgcount = $endorsval["image_count"];
                    $endorsmentarray[$key]["post"] = $endorsval;
                    $endorsmentarray[$key]["post_attachment"] = $postAttachmentImages;
                    $endorsmentarray[$key]["post_attachment_files"] = count($postAttachmentFiles);
                    $endorsmentarray[$key]["imagecount"] = $endorsval["image_count"];
                    $endorsmentarray[$key]["emojis_count"] = $endorsval["emojis_count"];
//                    $endorsmentarray[$key]["bitmojis_count"] = $endorsval["bitmojis_count"];
                    $endorsmentarray[$key]["user_id"] = $endorsval["user_id"];
                    $endorsmentarray[$key]["published_date"] = $publishedDate;

                    $endorsmentarray[$key]["reply"] = $endorsval["is_reply"];

                    if ($value["PostLike"]["id"] != "") {
                        $endorsmentarray[$key]["like"] = 1;
                    } else {
                        $endorsmentarray[$key]["like"] = 0;
                    }

                    $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                    $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $endorsval["user_id"], "UserOrganization.organization_id" => $org_id)));

                    $jobTitle = '';

                    if (isset($userOrganization['UserOrganization']['job_title_id']) && $userOrganization['UserOrganization']['job_title_id'] != '') {
                        $jobTitleData = $this->OrgJobTitle->findById($userOrganization['UserOrganization']['job_title_id']);
                        if (isset($jobTitleData['OrgJobTitle']) && !empty($jobTitleData['OrgJobTitle'])) {
                            $jobTitle = $jobTitleData['OrgJobTitle']['title'];
                        } else {
                            $jobTitle = '';
                        }
                    }
                    $endorsmentarray[$key]["user_job_title"] = $jobTitle;
                    $userarray[] = $endorsval["user_id"];
                    $endorsmentarray[$key]["displayflag"] = $displayflag;
                }
//pr($endorsmentarray);exit;
                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userarray),
                    'fields' => array('id', 'fname', 'lname', 'image', 'about')
                ));
//pr($userinfo);
                $userdata = array();
                foreach ($userinfo as $userval) {
                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                    $userabout[$userval["User"]["id"]]['about'] = trim($userval["User"]["about"]);
                }

                $newarray = array();
//pr($endorsmentarray); exit;
                foreach ($endorsmentarray as $key => $eval) {
                    $key = $eval["post"]["id"];
                    $newarray[$key]["id"] = $eval["post"]["id"];
                    $newarray[$key]["is_like"] = $eval["like"];
                    $newarray[$key]["is_read"] = $eval["post"]["is_read"];
                    $newarray[$key]["post_id"] = $eval["post"]["id"];
                    $newarray[$key]["user_id"] = $eval["post"]["user_id"];
                    $newarray[$key]["post_image"] = $eval["post_attachment"];
                    $newarray[$key]["post_files"] = $eval["post_attachment_files"];
                    $newarray[$key]["comments_count"] = $eval['post']["comments_count"];
                    $newarray[$key]["user_job_title"] = $eval["user_job_title"];

                    if (isset($userdata[$eval["post"]["user_id"]])) {
                        $newuserdata = explode("&&&&", $userdata[$eval["post"]["user_id"]]);
                        $newarray[$key]["user_name"] = $newuserdata[0];
                        $newarray[$key]["user_about"] = $userabout[$userval["User"]["id"]]['about'];
                        if (isset($newuserdata[1])) {

                            $needle = 'default.jpg';
                            if (strpos($newuserdata[1], $needle) !== false) {
                                $newarray[$key]["user_image"] = '';
                            } else {
                                $newarray[$key]["user_image"] = $newuserdata[1];
                            }
                        }
                    } else {
                        $newarray[$key]["user_name"] = "";
                    }
//pr($newarray);
//exit;
                    $newarray[$key]["message"] = $eval["post"]["message"];
                    $newarray[$key]["title"] = $eval["post"]["title"];

                    $newarray[$key]["like_count"] = $eval["post"]["like_count"];
//$newarray[$key]["created"] = $eval["post"]["created"];
                    $newarray[$key]["created"] = $eval["published_date"];

                    $newarray[$key]["imagecount"] = $eval["imagecount"];
                    $newarray[$key]["emojiscount"] = $eval["emojis_count"];
//                    $newarray[$key]["bitmojiscount"] = $eval["bitmojis_count"];
                    $newarray[$key]["is_reply"] = $eval["reply"];
                    $newarray[$key]["list_type"] = 'wallpost';
                }
                if ($loggedInUser['current_org']['joined'] == 0) {

                    $updateArray['UserOrganization.joined'] = 1;
//$updateArray['live_updated'] = '"' . date("Y-m-d H:i:s") . '"';
//if ($this->UserOrganization->updateAll(array("UserOrganization.joined" => 1), array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                    if ($this->UserOrganization->updateAll($updateArray, array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                        $this->Session->write('Auth.User.current_org.joined', 1);
                        $this->JoinOrgCode->updateAll(array("is_expired" => 1), array("email" => $loggedInUser['email'], "organization_id" => $org_id));
                    }
                }

                $returndata2 = array("post_data" => $newarray, "total_page" => $totalpage, "server_time" => $serverCurrentTime);

//pr($returndata2);
                $liveFeedDATA = array();
                foreach ($feedArray as $feedId => $feedData) {
                    if ($feedData['feed_type'] == 'post') {
                        if (isset($returndata2['post_data'][$feedData['feed_id']]))
                            $liveFeedDATA[] = $returndata2['post_data'][$feedData['feed_id']];
                    } else {
                        if (isset($returndata1['endorse_data'][$feedData['feed_id']]))
                            $liveFeedDATA[] = $returndata1['endorse_data'][$feedData['feed_id']];
                    }
                }
                $resServerTime = '';
                if (isset($endorseServerTime) && $endorseServerTime != '') {
                    $resServerTime = $endorseServerTime;
                } elseif (isset($serverCurrentTime) && $serverCurrentTime != '') {
                    $resServerTime = $serverCurrentTime;
                }
//                pr($liveFeedDATA);
//                exit;

                /* GETTING POST DATA END */
                $returndata = array("endorse_data" => $liveFeedDATA, "total_page" => $totalpage, "server_time" => $resServerTime);




                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Organization Endorsement ",
                        "data" => $returndata),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => ""),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }


    public function getLiveFeeds2() { /// Function clone Created by babulal prasad to test on live site
        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
            $user_id = $loggedInUser['id'];

            if (isset($loggedInUser['current_org'])) {
                $org_id = $loggedInUser['current_org']['id'];
                $keyword = "";

                $params = array();
                $params['fields'] = "user_id,entity_id,department_id";
                $params['conditions'] = array(
                    "UserOrganization.organization_id" => $org_id, "UserOrganization.status" => 1);

                $userdepartmentorg = $this->UserOrganization->find("all", $params);
//pr($userdepartmentorg);
                $entity_user_array = array();
                $department_user_array = array();
                foreach ($userdepartmentorg as $userorgval) {

                    if ($userorgval["UserOrganization"]["entity_id"] > 0) {
                        $entity_user_array[$userorgval["UserOrganization"]["entity_id"]][] = $userorgval["UserOrganization"]["user_id"];
                    }
                    if ($userorgval["UserOrganization"]["department_id"] > 0) {
                        $department_user_array[$userorgval["UserOrganization"]["department_id"]][] = $userorgval["UserOrganization"]["user_id"];
                    }
                }
//pr($entity_user_array);
//echo "<hr>";
//pr($department_user_array);exit;
//

                if (isset($this->request->data["type"]) && $this->request->data["type"] != "") {
                    $type = $this->request->data["type"];
                }

                $feed_type = $feed_id = "";
                $endorse_search_id = "";

                if (isset($this->request->data["feed_type"]) && $this->request->data["feed_type"] == "post") {
                    $feed_type = $this->request->data["feed_type"];
                    $feed_id = $this->request->data["feed_id"];
                }

                $start_date = "";
                $end_date = "";

                $limit = Configure::read("pageLimit");
                if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
                    $page = $this->request->data["page"];
                    $offset = $page * $limit;
                } else {
                    $page = 1;
                    $offset = 0;
                }

                /* NEW CODE START */
//Getting total feeds count
                $NEWconditionarray = $postFeedIds = $endorseFeedIds = $feedArray = array();
//pr($this->request->data);
                if (isset($this->request->data["endorse_type"]) && $this->request->data["endorse_type"] == "user") {
                    $NEWconditionarray = array("user_id like '%" . $this->request->data["endorse_id"] . "%'");
                }

                if (isset($this->request->data["endorse_type"]) && $this->request->data["endorse_type"] == "department") {
                    $NEWconditionarray["FeedTran.dept_id"] = $this->request->data["endorse_id"];
                }

                $NEWconditionarray["FeedTran.org_id"] = $org_id;
                $NEWconditionarray["FeedTran.status"] = 1;

                if (isset($feed_type) && $feed_type != '') {
                    $NEWconditionarray["FeedTran.feed_type"] = $feed_type;
                    $NEWconditionarray["FeedTran.feed_id"] = $feed_id;
                }


                $NEWparams['fields'] = "count(*) as cnt";
                $NEWparams['conditions'] = $NEWconditionarray;
                $NEWparams['order'] = 'FeedTran.created desc';
                $totaleFeeds = $this->FeedTran->find("all", $NEWparams);

                $totalLiveFeed = $totaleFeeds[0][0]["cnt"];
                $totalpage = ceil($totalLiveFeed / $limit);


//Getting live feeds
//$paramsFeed['fields'] = "*,UNIX_TIMESTAMP(created) as create_date, UNIX_TIMESTAMP() as curr_time ";

                $paramsFeed['fields'] = "*";
                $paramsFeed['conditions'] = $NEWconditionarray;
                $paramsFeed['limit'] = $limit;
                $paramsFeed['page'] = $page;
                $paramsFeed['offset'] = $offset;
                $paramsFeed['order'] = 'FeedTran.created desc';
                $FeedTranRes = $this->FeedTran->find("all", $paramsFeed);
//                pr($FeedTranRes);
//                $log = $this->FeedTran->getDataSource()->getLog(false, false);
//                pr($log);
                if (!empty($FeedTranRes)) {
                    foreach ($FeedTranRes as $index => $feedTransData) {
                        $feedArray[$feedTransData['FeedTran']['id']]['feed_id'] = $feedTransData['FeedTran']['feed_id'];
                        $feedArray[$feedTransData['FeedTran']['id']]['feed_type'] = $feedTransData['FeedTran']['feed_type'];
                        if ($feedTransData['FeedTran']['feed_type'] == 'post') {
                            $postFeedIds[] = $feedTransData['FeedTran']['feed_id'];
                        } else {
                            $endorseFeedIds[] = $feedTransData['FeedTran']['feed_id'];
                        }
                    }
                }

                $updateArray['live_updated'] = '"' . date("Y-m-d H:i:s") . '"';
//if ($this->UserOrganization->updateAll(array("UserOrganization.joined" => 1), array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                $this->UserOrganization->updateAll($updateArray, array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id));

//                pr($feedArray);
//                pr($postFeedIds);
//                pr($endorseFeedIds);
//                exit;

                /* NEW CODE END */

                $params = array();
                $params['fields'] = "count(*) as cnt";
                $conditionarray["Endorsement.type != "] = "private";
                $conditionarray["Endorsement.id"] = $endorseFeedIds;

                $params['conditions'] = $conditionarray;
                $params['order'] = 'Endorsement.created desc';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $params['joins'] = array(
                    array(
                        'table' => 'endorsement_likes',
                        'alias' => 'EndorsementLike',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Endorsement.id =EndorsementLike.endorsement_id ',
                            'EndorsementLike.user_id =' . $user_id
                        )
                    )
                );

                $params['fields'] = "*,UNIX_TIMESTAMP(created) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $params['order'] = 'Endorsement.created desc';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $this->Endorsement->bindModel(array('hasMany' => array('EndorseCoreValues')));
//pr($params);
//exit;
                $endorsement = $this->Endorsement->find("all", $params);
//                $log = $this->Endorsement->getDataSource()->getLog(false, false);
//                pr($log);
//exit;
//pr($endorsement);
//exit;
                $endorsmentarray = array();
                $departmentarray = array();
                $entityarray = array();
                $userarray = array();
                $core_values = $this->getOrgValues($org_id, "OrgCoreValues", 1);
                $coreval = array();
                foreach ($core_values as $cvalue) {
                    $coreval[$cvalue["id"]] = $cvalue["name"] . "&&&&" . $cvalue["color_code"];
                }

                $serverCurrentTime = "";

                foreach ($endorsement as $key => $value) {
                    $displayflag = 0;
                    $endorsval = $value["Endorsement"];
                    $endorsval["created"] = $value[0]["create_date"];
                    $serverCurrentTime = $value[0]["curr_time"];
                    $endorsimgcount = $endorsval["image_count"];
                    $endorsecorevalue = $value["EndorseCoreValues"];
                    $endorsmentarray[$key]["endorse"] = $endorsval;
                    $endorsmentarray[$key]["imagecount"] = $endorsval["image_count"];
                    $endorsmentarray[$key]["emojis_count"] = $endorsval["emojis_count"];

                    $endorsmentarray[$key]["reply"] = $endorsval["is_reply"];

                    if ($value["EndorsementLike"]["id"] != "") {
                        $endorsmentarray[$key]["like"] = 1;
                    } else {
                        $endorsmentarray[$key]["like"] = 0;
                    }
//$endorsmentarray[$key]["attatched_image"]=$value["EndorseAttachments"];

                    $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                    $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id)));

                    if ($endorsval["endorsement_for"] == "department") {
                        $endorsmentarray[$key]["reply"] = 0;
                        if ($user_id == $endorsval["endorser_id"] || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } elseif (isset($department_user_array[$endorsval["endorsed_id"]])) {
                            if (in_array($user_id, $department_user_array[$endorsval["endorsed_id"]])) {
                                $displayflag = 1;
                            }
                        }

                        if (!in_array($endorsval["endorsed_id"], $departmentarray)) {
                            $departmentarray[] = $endorsval["endorsed_id"];
                        }
                    } elseif ($endorsval["endorsement_for"] == "entity") {
                        $endorsmentarray[$key]["reply"] = 0;
                        if ($user_id == $endorsval["endorser_id"] || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } elseif (isset($entity_user_array[$endorsval["endorsed_id"]])) {
                            if (in_array($user_id, $entity_user_array[$endorsval["endorsed_id"]])) {
                                $displayflag = 1;
                            }
                        }
                        if (!in_array($endorsval["endorsed_id"], $entityarray)) {
                            $entityarray[] = $endorsval["endorsed_id"];
                        }
                    } else {
                        if (!in_array($endorsval["endorsed_id"], $userarray)) {

                            $userarray[] = $endorsval["endorsed_id"];
                        }

                        if (in_array($user_id, array($endorsval["endorser_id"], $endorsval["endorsed_id"])) || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } else {
                            $endorsmentarray[$key]["reply"] = 0;
                        }
                    }

                    if (!in_array($endorsval["endorser_id"], $userarray)) {
                        $userarray[] = $endorsval["endorser_id"];
                    }
                    $corevaluearray = array();
                    foreach ($endorsecorevalue as $eval) {


                        if (!in_array($eval["value_id"], $corevaluearray)) {
                            if (isset($coreval[$eval["value_id"]])) {
                                $ncval = explode("&&&&", $coreval[$eval["value_id"]]);
                                $corevaluearray[] = array("name" => $ncval[0], "color_code" => $ncval[1]);
                            }
                        }
                    }
                    $endorsmentarray[$key]["corevalue"] = $corevaluearray;
                    $endorsmentarray[$key]["displayflag"] = $displayflag;
                }


                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userarray),
                    'fields' => array('id', 'fname', 'lname', 'image')
                ));
                $userdata = array();
                foreach ($userinfo as $userval) {

                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                }

                $department = array();
                $entity = array();
                if (!empty($departmentarray)) {
                    $departmentarr = $this->getOrgValues($org_id, "OrgDepartments", 1);
                    if (!empty($departmentarr)) {
                        foreach ($departmentarr as $dval) {
                            $department[$dval["id"]] = $dval["name"];
                        }
                    }
                }

                if (!empty($entityarray)) {
                    $entity1 = $this->getOrgValues($org_id, "Entity", 1);
                    if (!empty($entity1)) {
                        foreach ($entity1 as $dval) {
                            $entity[$dval["id"]] = $dval["name"];
                        }
                    }
                }
                $newarray = array();

                foreach ($endorsmentarray as $key => $eval) {
                    $key = $eval["endorse"]["id"];
                    $newarray[$key]["id"] = $eval["endorse"]["id"];
                    $newarray[$key]["is_like"] = $eval["like"];
                    $newarray[$key]["is_read"] = $eval["endorse"]["is_read"];
                    $newarray[$key]["endorsement_for"] = $eval["endorse"]["endorsement_for"];
                    $newarray[$key]["endorsed_id"] = $eval["endorse"]["endorsed_id"];
                    $newarray[$key]["endorser_id"] = $eval["endorse"]["endorser_id"];
//	$newarray[$key]["attatched_image"] =  $eval["attatched_image"];
// print_r($newarray);
                    if (isset($userdata[$eval["endorse"]["endorser_id"]])) {
                        $newuserdata = explode("&&&&", $userdata[$eval["endorse"]["endorser_id"]]);
                        $newarray[$key]["endorser_name"] = $newuserdata[0];
                        if (isset($newuserdata[1])) {

//$newarray[$key]["endorser_image"] = $newuserdata[1];

                            $needle = 'default.jpg';
                            if (strpos($newuserdata[1], $needle) !== false) {
                                $newarray[$key]["endorser_image"] = '';
                            } else {
                                $newarray[$key]["endorser_image"] = $newuserdata[1];
                            }
                        }
                    } else {
                        $newarray[$key]["endorser_name"] = "";
                    }

                    $newarray[$key]["type"] = $eval["endorse"]["type"];
                    if ($eval["displayflag"] == 1) {
                        $newarray[$key]["message"] = $eval["endorse"]["message"];
                    } else {
                        $newarray[$key]["message"] = "";
                    }
                    $newarray[$key]["like_count"] = $eval["endorse"]["like_count"];
                    $newarray[$key]["created"] = $eval["endorse"]["created"];
                    if ($eval["displayflag"] == 1) {
                        $newarray[$key]["imagecount"] = $eval["imagecount"];
                        $newarray[$key]["emojiscount"] = $eval["emojis_count"];
                        $newarray[$key]["is_reply"] = $eval["reply"];
                    } else {
                        $newarray[$key]["imagecount"] = 0;
                        $newarray[$key]["emojiscount"] = 0;
                        $newarray[$key]["is_reply"] = 0;
                    }
                    $newarray[$key]["corevalues"] = $eval["corevalue"];

                    if ($eval["endorse"]["endorsement_for"] == "user") {
                        if (isset($userdata[$eval["endorse"]["endorsed_id"]])) {
                            $newuserdata = explode("&&&&", $userdata[$eval["endorse"]["endorsed_id"]]);
//pr($newuserdata); 
//                        pr($eval["endorse"]);
                            $newarray[$key]["endorsed_name"] = $newuserdata[0];
                            if (isset($newuserdata[1])) {
//$newarray[$key]["endorsed_image"] = $newuserdata[1];
                                $needle = 'default.jpg';
                                if (strpos($newuserdata[1], $needle) !== false) {
                                    $newarray[$key]["endorsed_image"] = '';
                                } else {
                                    $newarray[$key]["endorsed_image"] = $newuserdata[1];
                                }
                            }
                        }
                    } elseif ($eval["endorse"]["endorsement_for"] == "department") {
                        if (isset($department[$eval["endorse"]["endorsed_id"]]))
                            $newarray[$key]["endorsed_name"] = $department[$eval["endorse"]["endorsed_id"]];
                        else
                            $newarray[$key]["endorsed_name"] = '';
                    } elseif ($eval["endorse"]["endorsement_for"] == "entity") {
                        if (isset($entity[$eval["endorse"]["endorsed_id"]]))
                            $newarray[$key]["endorsed_name"] = $entity[$eval["endorse"]["endorsed_id"]];
                        else
                            $newarray[$key]["endorsed_name"] = '';
                    }
                    $newarray[$key]["list_type"] = 'endorse';
                }
                $endorseServerTime = $serverCurrentTime;
                $returndata1 = array("endorse_data" => $newarray, "total_page" => $totalpage, "server_time" => $serverCurrentTime);
//pr($returndata1); 



                /* GETTING POST DATA START */

                $conditionarrayPost = array();
                $conditionarrayPost["Post.id"] = $postFeedIds;
                $params['conditions'] = $conditionarrayPost;
                $params['order'] = 'Post.created desc';

                $params['joins'] = array(
                    array(
                        'table' => 'post_likes',
                        'alias' => 'PostLike',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Post.id = PostLike.post_id ',
                            'PostLike.user_id =' . $user_id
                        )
                    ),
//                    array(
//                        'table' => 'post_attachments',
//                        'alias' => 'PostAttachment',
//                        'type' => 'LEFT',
//                        'conditions' => array(
//                            'Post.id = PostAttachment.post_id ',
//                             /* 'PostAttachment.type = "image"'*/
//                        )
//                    )
                );

                $params['fields'] = "*,UNIX_TIMESTAMP(Post.created) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $params['order'] = 'Post.created desc';
//$params['group'] = 'Post.id';
// pr($params); exit;
//$this->Post->unbindModel(array('hasMany' => array('PostAttachments')));
//$this->Endorsement->bindModel(array('hasMany' => array('EndorseCoreValues')));
                $endorsement = $this->Post->find("all", $params);
//                $log = $this->Post->getDataSource()->getLog(false, false);
//                pr($log);

                $endorsmentarray = array();
                $departmentarray = array();
                $entityarray = array();
                $userarray = array();
                $core_values = $this->getOrgValues($org_id, "OrgCoreValues", 1);
                $coreval = array();
                foreach ($core_values as $cvalue) {
                    $coreval[$cvalue["id"]] = $cvalue["name"] . "&&&&" . $cvalue["color_code"];
                }

                $serverCurrentTime = "";

                foreach ($endorsement as $key => $value) {
                    $postAttachmentImages = array();
//                    if (isset($value['Post']['image_count']) && $value['Post']['image_count'] > 0) {
//                        $endorsmentarray[$key]["post_image"] = array(Router::url('/', true) . "app/webroot/" . POST_IMAGE_DIR . $value["PostAttachment"]['name']);
//                    } else if (isset($value['Post']['image_count']) && $value['Post']['image_count'] < 1 && $value['Post']['emojis_count'] > 0) {
//                        $endorsmentarray[$key]["post_image"] = "";
//                        $PostAttachData = $this->PostAttachment->getEmojiByPostId($value['Post']['id']);
//                        $endorsmentarray[$key]["post_image"] = array(Router::url('/', true) . EMOJIS_IMAGE_DIR . $PostAttachData);
//                    } else {
//                        $endorsmentarray[$key]["post_image"] = array();
//                    }
                    $postAttachmentemoji = array();
                    $postAttachmentimg = array();
                    $postAttachmentFiles = array();

                    if (isset($value["PostAttachments"]) && !empty($value["PostAttachments"])) {
                        foreach ($value["PostAttachments"] as $index => $postAttchment) {
                            if ($postAttchment['type'] == 'emojis') {
                                $ttmpImg = Router::url('/', true) . EMOJIS_IMAGE_DIR . $postAttchment['name'];
                                $ttmpImg = str_replace("http", "https", $ttmpImg);
                                $postAttachmentemoji[] = $ttmpImg;
                            } else if ($postAttchment['type'] == 'image') {
                                $ttmpImg1 = Router::url('/', true) . "app/webroot/" . POST_IMAGE_DIR . $postAttchment['name'];
                                $ttmpImg1 = str_replace("http", "https", $ttmpImg1);
                                $postAttachmentimg[] = $ttmpImg1;
                            } else if ($postAttchment['type'] == 'files') {
                                $ttmpImg2 = Router::url('/', true) . "app/webroot/" . POST_FILE_DIR . $postAttchment['name'];
                                $ttmpImg2 = str_replace("http", "https", $ttmpImg2);
                                $postAttachmentFiles[] = $ttmpImg2;
                            }
                        }
                        $postAttachmentImages = array_merge($postAttachmentimg, $postAttachmentemoji);
                    }

                    $displayflag = 0;
                    $endorsval = $value["Post"];

                    $endorsval["created"] = $value[0]["create_date"];
                    $serverCurrentTime = $value[0]["curr_time"];
                    $endorsimgcount = $endorsval["image_count"];
                    $endorsmentarray[$key]["post"] = $endorsval;
                    $endorsmentarray[$key]["post_attachment"] = $postAttachmentImages;
                    $endorsmentarray[$key]["post_attachment_files"] = count($postAttachmentFiles);
                    $endorsmentarray[$key]["imagecount"] = $endorsval["image_count"];
                    $endorsmentarray[$key]["emojis_count"] = $endorsval["emojis_count"];
                    $endorsmentarray[$key]["user_id"] = $endorsval["user_id"];

                    $endorsmentarray[$key]["reply"] = $endorsval["is_reply"];

                    if ($value["PostLike"]["id"] != "") {
                        $endorsmentarray[$key]["like"] = 1;
                    } else {
                        $endorsmentarray[$key]["like"] = 0;
                    }

                    $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                    $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $endorsval["user_id"], "UserOrganization.organization_id" => $org_id)));

                    $jobTitle = '';

                    if (isset($userOrganization['UserOrganization']['job_title_id']) && $userOrganization['UserOrganization']['job_title_id'] != '') {
                        $jobTitleData = $this->OrgJobTitle->findById($userOrganization['UserOrganization']['job_title_id']);
                        if (isset($jobTitleData['OrgJobTitle']) && !empty($jobTitleData['OrgJobTitle'])) {
                            $jobTitle = $jobTitleData['OrgJobTitle']['title'];
                        } else {
                            $jobTitle = '';
                        }
                    }
                    $endorsmentarray[$key]["user_job_title"] = $jobTitle;
                    $userarray[] = $endorsval["user_id"];
                    $endorsmentarray[$key]["displayflag"] = $displayflag;
                }
//pr($endorsmentarray);exit;
                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userarray),
                    'fields' => array('id', 'fname', 'lname', 'image', 'about')
                ));
//pr($userinfo);
                $userdata = array();
                foreach ($userinfo as $userval) {
                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                    $userabout[$userval["User"]["id"]]['about'] = trim($userval["User"]["about"]);
                }

                $newarray = array();
//pr($endorsmentarray); exit;
                foreach ($endorsmentarray as $key => $eval) {
                    $key = $eval["post"]["id"];
                    $newarray[$key]["id"] = $eval["post"]["id"];
                    $newarray[$key]["is_like"] = $eval["like"];
                    $newarray[$key]["is_read"] = $eval["post"]["is_read"];
                    $newarray[$key]["post_id"] = $eval["post"]["id"];
                    $newarray[$key]["user_id"] = $eval["post"]["user_id"];
                    $newarray[$key]["post_image"] = $eval["post_attachment"];
                    $newarray[$key]["post_files"] = $eval["post_attachment_files"];
                    $newarray[$key]["comments_count"] = $eval['post']["comments_count"];
                    $newarray[$key]["user_job_title"] = $eval["user_job_title"];

                    if (isset($userdata[$eval["post"]["user_id"]])) {
                        $newuserdata = explode("&&&&", $userdata[$eval["post"]["user_id"]]);
                        $newarray[$key]["user_name"] = $newuserdata[0];
                        $newarray[$key]["user_about"] = $userabout[$userval["User"]["id"]]['about'];
                        if (isset($newuserdata[1])) {

                            $needle = 'default.jpg';
                            if (strpos($newuserdata[1], $needle) !== false) {
                                $newarray[$key]["user_image"] = '';
                            } else {
                                $newarray[$key]["user_image"] = $newuserdata[1];
                            }
                        }
                    } else {
                        $newarray[$key]["user_name"] = "";
                    }
//pr($newarray);
//exit;
                    $newarray[$key]["message"] = $eval["post"]["message"];
                    $newarray[$key]["title"] = $eval["post"]["title"];

                    $newarray[$key]["like_count"] = $eval["post"]["like_count"];
                    $newarray[$key]["created"] = $eval["post"]["created"];

                    $newarray[$key]["imagecount"] = $eval["imagecount"];
                    $newarray[$key]["emojiscount"] = $eval["emojis_count"];
                    $newarray[$key]["is_reply"] = $eval["reply"];
                    $newarray[$key]["list_type"] = 'wallpost';
                }
                if ($loggedInUser['current_org']['joined'] == 0) {

                    $updateArray['UserOrganization.joined'] = 1;
//$updateArray['live_updated'] = '"' . date("Y-m-d H:i:s") . '"';
//if ($this->UserOrganization->updateAll(array("UserOrganization.joined" => 1), array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                    if ($this->UserOrganization->updateAll($updateArray, array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                        $this->Session->write('Auth.User.current_org.joined', 1);
                        $this->JoinOrgCode->updateAll(array("is_expired" => 1), array("email" => $loggedInUser['email'], "organization_id" => $org_id));
                    }
                }

                $returndata2 = array("post_data" => $newarray, "total_page" => $totalpage, "server_time" => $serverCurrentTime);

//pr($returndata2);
                $liveFeedDATA = array();
                foreach ($feedArray as $feedId => $feedData) {
                    if ($feedData['feed_type'] == 'post') {
                        if (isset($returndata2['post_data'][$feedData['feed_id']]))
                            $liveFeedDATA[] = $returndata2['post_data'][$feedData['feed_id']];
                    } else {
                        if (isset($returndata1['endorse_data'][$feedData['feed_id']]))
                            $liveFeedDATA[] = $returndata1['endorse_data'][$feedData['feed_id']];
                    }
                }
                $resServerTime = '';
                if (isset($endorseServerTime) && $endorseServerTime != '') {
                    $resServerTime = $endorseServerTime;
                } elseif (isset($serverCurrentTime) && $serverCurrentTime != '') {
                    $resServerTime = $serverCurrentTime;
                }
//                pr($liveFeedDATA);
//                exit;

                /* GETTING POST DATA END */
                $returndata = array("endorse_data" => $liveFeedDATA, "total_page" => $totalpage, "server_time" => $resServerTime);




                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Organization Endorsement ",
                        "data" => $returndata),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => ""),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getEndorseList() {

        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();


            $user_id = $loggedInUser['id'];
            if (isset($loggedInUser['current_org'])) {
//print_r($loggedInUser['current_org']);
                $org_id = $loggedInUser['current_org']['id'];
                $keyword = "";
//if($this->request->data["keyword"])
//if(isset($this->request->data["keyword"]))
//{
//$keyword = $this->request->data["keyword"];
//}
//
                $params = array();
                $params['fields'] = "user_id,entity_id,department_id";
                $params['conditions'] = array(
                    "UserOrganization.organization_id" => $org_id, "UserOrganization.status" => 1);
                $userdepartmentorg = $this->UserOrganization->find("all", $params);

                $entity_user_array = array();
                $department_user_array = array();
                foreach ($userdepartmentorg as $userorgval) {



                    if ($userorgval["UserOrganization"]["entity_id"] > 0) {
                        $entity_user_array[$userorgval["UserOrganization"]["entity_id"]][] = $userorgval["UserOrganization"]["user_id"];
                    }
                    if ($userorgval["UserOrganization"]["department_id"] > 0) {
                        $department_user_array[$userorgval["UserOrganization"]["department_id"]][] = $userorgval["UserOrganization"]["user_id"];
                    }
                }
//  print_r($entity_user_array);
//  echo "<hr>";
// print_r($department_user_array);exit;
//
                $type = $this->request->data["type"];
                $endorse_type = "";
                $endorse_search_id = "";
                if (isset($this->request->data["endorse_type"]) && $this->request->data["endorse_type"] != "") {
                    $endorse_type = $this->request->data["endorse_type"];
                }
                if (isset($this->request->data["endorse_id"]) && $this->request->data["endorse_id"] != "") {
                    $endorse_search_id = $this->request->data["endorse_id"];
                }
                $start_date = "";
                $end_date = "";
                if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                    $start_date = $this->request->data["start_date"];
                }
                if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                    $end_date = $this->request->data["end_date"];
                }

                $limit = Configure::read("pageLimit");
                if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
                    $page = $this->request->data["page"];
                    $offset = $page * $limit;
                } else {
                    $page = 1;
                    $offset = 0;
                }

                $params = array();
                $params['fields'] = "count(*) as cnt";
                $conditionarray["Endorsement.organization_id"] = $org_id;
                $conditionarray["Endorsement.status"] = '1';
                $conditionarray["Endorsement.type !="] = 'daisy';
                $updateArray = array();

                if ($type == "endorser") {
                    $conditionarray["Endorsement.endorser_id"] = $user_id;
//$conditionarray["Endorsement.endorsed_id"] = $user_id;
                } elseif ($type == "endorsed") {
                    $updateArray['ndorsed_updated'] = '"' . date("Y-m-d H:i:s") . '"';
//$conditionarray["Endorsement.endorser_id"] = $user_id;
                    $conditionarray["Endorsement.endorsed_id"] = $user_id;
                } else {
                    $updateArray['live_updated'] = '"' . date("Y-m-d H:i:s") . '"';
                    $conditionarray["Endorsement.type != "] = "private";
                }
//if($keyword!=""){
//$conditionarray["Endorsement.id"]= $endorsmentids;		
//}
                if ($type != "endorser") {
                    $this->UserOrganization->updateAll($updateArray, array("user_id" => $loggedInUser['id'], "organization_id" => $loggedInUser['current_org']['id']));
                }

                if ($start_date != "") {
                    $conditionarray["Endorsement.created >= "] = date("Y-m-d 00:00:00", $start_date);
                }
                if ($end_date != "") {
                    $conditionarray["Endorsement.created <= "] = date("Y-m-d 23:59:59", $end_date);
                }
// for date search
//	array('Equipment.created <= ' => $date,
//  'Equipment.creatd >= ' => $date
// )
// end
                if ($endorse_type != "" && $endorse_search_id != "") {
//$conditionarray["Endorsement.endorsement_for"] = $endorse_type;
//$conditionarray["Endorsement.endorsed_id"]= $endorse_search_id;
                    if ($type == "endorsed") {
                        $conditionarray["Endorsement.endorser_id"] = $endorse_search_id;
                    } elseif ($type == "endorser") {
                        $conditionarray["Endorsement.endorsed_id"] = $endorse_search_id;
                    } else {
// array('OR' => array('Endorsement.endorsed_id ' => $endorse_search_id, 'Endorsement.endorser_id' => $endorse_search_id));
                        $conditionarray = array_merge($conditionarray, array('OR' => array('Endorsement.endorsed_id ' => $endorse_search_id, 'Endorsement.endorser_id' => $endorse_search_id)));
                    }
                }
//pr( $conditionarray);
                $params['conditions'] = $conditionarray;
                $params['order'] = 'Endorsement.created desc';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $totalendorsement = $this->Endorsement->find("all", $params);
//echo $this->Endorsement->getLastQuery();die;
                $totalendorse = $totalendorsement[0][0]["cnt"];
                $totalpage = ceil($totalendorse / $limit);
                $params['joins'] = array(
                    array(
                        'table' => 'endorsement_likes',
                        'alias' => 'EndorsementLike',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Endorsement.id =EndorsementLike.endorsement_id ',
                            'EndorsementLike.user_id =' . $user_id
                        )
                    )
                );

                $params['fields'] = "*,UNIX_TIMESTAMP(created) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $params['limit'] = $limit;
                $params['page'] = $page;
                $params['offset'] = $offset;
                $params['order'] = 'Endorsement.created desc';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $this->Endorsement->bindModel(array('hasMany' => array('EndorseCoreValues')));
                $endorsement = $this->Endorsement->find("all", $params);
//print_r($endorsement);exit;
                $endorsmentarray = array();
                $departmentarray = array();
                $entityarray = array();
                $userarray = array();
                $core_values = $this->getOrgValues($org_id, "OrgCoreValues", 1);
                $coreval = array();
                foreach ($core_values as $cvalue) {
                    $coreval[$cvalue["id"]] = $cvalue["name"] . "&&&&" . $cvalue["color_code"];
                }

                $serverCurrentTime = "";

                foreach ($endorsement as $key => $value) {
                    $displayflag = 0;
                    $endorsval = $value["Endorsement"];
                    $endorsval["created"] = $value[0]["create_date"];
                    $serverCurrentTime = $value[0]["curr_time"];
                    $endorsimgcount = $endorsval["image_count"];
                    $endorsecorevalue = $value["EndorseCoreValues"];
                    $endorsmentarray[$key]["endorse"] = $endorsval;
                    $endorsmentarray[$key]["imagecount"] = $endorsval["image_count"];
                    $endorsmentarray[$key]["emojis_count"] = $endorsval["emojis_count"];

                    $endorsmentarray[$key]["reply"] = $endorsval["is_reply"];

                    if ($value["EndorsementLike"]["id"] != "") {
                        $endorsmentarray[$key]["like"] = 1;
                    } else {
                        $endorsmentarray[$key]["like"] = 0;
                    }
//$endorsmentarray[$key]["attatched_image"]=$value["EndorseAttachments"];

                    $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                    $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id)));


                    if ($endorsval["endorsement_for"] == "department") {
                        $endorsmentarray[$key]["reply"] = 0;
                        if ($user_id == $endorsval["endorser_id"] || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } elseif (isset($department_user_array[$endorsval["endorsed_id"]])) {
                            if (in_array($user_id, $department_user_array[$endorsval["endorsed_id"]])) {
                                $displayflag = 1;
                            }
                        }



                        if (!in_array($endorsval["endorsed_id"], $departmentarray)) {
                            $departmentarray[] = $endorsval["endorsed_id"];
                        }
                    } elseif ($endorsval["endorsement_for"] == "entity") {
                        $endorsmentarray[$key]["reply"] = 0;
                        if ($user_id == $endorsval["endorser_id"] || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } elseif (isset($entity_user_array[$endorsval["endorsed_id"]])) {
                            if (in_array($user_id, $entity_user_array[$endorsval["endorsed_id"]])) {
                                $displayflag = 1;
                            }
                        }
                        if (!in_array($endorsval["endorsed_id"], $entityarray)) {
                            $entityarray[] = $endorsval["endorsed_id"];
                        }
                    } else {
                        if (!in_array($endorsval["endorsed_id"], $userarray)) {

                            $userarray[] = $endorsval["endorsed_id"];
                        }

                        if (in_array($user_id, array($endorsval["endorser_id"], $endorsval["endorsed_id"])) || $userOrganization['UserOrganization']['user_role'] == 2) {
                            $displayflag = 1;
                        } else {
                            $endorsmentarray[$key]["reply"] = 0;
                        }
                    }

                    if (!in_array($endorsval["endorser_id"], $userarray)) {
                        $userarray[] = $endorsval["endorser_id"];
                    }
                    $corevaluearray = array();
                    foreach ($endorsecorevalue as $eval) {


                        if (!in_array($eval["value_id"], $corevaluearray)) {
                            if (isset($coreval[$eval["value_id"]])) {
                                $ncval = explode("&&&&", $coreval[$eval["value_id"]]);
                                $corevaluearray[] = array("name" => $ncval[0], "color_code" => $ncval[1]);
                            }
                        }
                    }
                    $endorsmentarray[$key]["corevalue"] = $corevaluearray;
                    $endorsmentarray[$key]["displayflag"] = $displayflag;
                }


                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userarray),
                    'fields' => array('id', 'fname', 'lname', 'image')
                ));
                $userdata = array();
                foreach ($userinfo as $userval) {

                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                }

                $department = array();
                $entity = array();
                if (!empty($departmentarray)) {
                    $departmentarr = $this->getOrgValues($org_id, "OrgDepartments", 1);
                    if (!empty($departmentarr)) {
                        foreach ($departmentarr as $dval) {
                            $department[$dval["id"]] = $dval["name"];
                        }
                    }
                }

                if (!empty($entityarray)) {
                    $entity1 = $this->getOrgValues($org_id, "Entity", 1);
                    if (!empty($entity1)) {
                        foreach ($entity1 as $dval) {
                            $entity[$dval["id"]] = $dval["name"];
                        }
                    }
                }
                $newarray = array();

                foreach ($endorsmentarray as $key => $eval) {

                    $newarray[$key]["id"] = $eval["endorse"]["id"];
                    $newarray[$key]["is_like"] = $eval["like"];
                    $newarray[$key]["is_read"] = $eval["endorse"]["is_read"];
                    $newarray[$key]["endorsement_for"] = $eval["endorse"]["endorsement_for"];
                    $newarray[$key]["endorsed_id"] = $eval["endorse"]["endorsed_id"];
                    $newarray[$key]["endorser_id"] = $eval["endorse"]["endorser_id"];
//	$newarray[$key]["attatched_image"] =  $eval["attatched_image"];
// print_r($newarray);
                    if (isset($userdata[$eval["endorse"]["endorser_id"]])) {
                        $newuserdata = explode("&&&&", $userdata[$eval["endorse"]["endorser_id"]]);
                        $newarray[$key]["endorser_name"] = $newuserdata[0];
                        if (isset($newuserdata[1])) {

//$newarray[$key]["endorser_image"] = $newuserdata[1];

                            $needle = 'default.jpg';
                            if (strpos($newuserdata[1], $needle) !== false) {
                                $newarray[$key]["endorser_image"] = '';
                            } else {
                                $newarray[$key]["endorser_image"] = $newuserdata[1];
                            }
                        }
                    } else {
                        $newarray[$key]["endorser_name"] = "";
                    }

                    $newarray[$key]["type"] = $eval["endorse"]["type"];
                    if ($eval["displayflag"] == 1) {
                        $newarray[$key]["message"] = $eval["endorse"]["message"];
                    } else {
                        $newarray[$key]["message"] = "";
                    }
                    $newarray[$key]["like_count"] = $eval["endorse"]["like_count"];
                    $newarray[$key]["created"] = $eval["endorse"]["created"];
                    if ($eval["displayflag"] == 1) {
                        $newarray[$key]["imagecount"] = $eval["imagecount"];
                        $newarray[$key]["emojiscount"] = $eval["emojis_count"];
                        $newarray[$key]["is_reply"] = $eval["reply"];
                    } else {
                        $newarray[$key]["imagecount"] = 0;
                        $newarray[$key]["emojiscount"] = 0;
                        $newarray[$key]["is_reply"] = 0;
                    }
                    $newarray[$key]["corevalues"] = $eval["corevalue"];


                    if ($eval["endorse"]["endorsement_for"] == "user") {
                        $newuserdata = explode("&&&&", $userdata[$eval["endorse"]["endorsed_id"]]);
                        $newarray[$key]["endorsed_name"] = $newuserdata[0];
                        if (isset($newuserdata[1])) {
//$newarray[$key]["endorsed_image"] = $newuserdata[1];
                            $needle = 'default.jpg';
                            if (strpos($newuserdata[1], $needle) !== false) {
                                $newarray[$key]["endorsed_image"] = '';
                            } else {
                                $newarray[$key]["endorsed_image"] = $newuserdata[1];
                            }
                        }
                    } elseif ($eval["endorse"]["endorsement_for"] == "department") {
                        $newarray[$key]["endorsed_name"] = $department[$eval["endorse"]["endorsed_id"]];
                    } elseif ($eval["endorse"]["endorsement_for"] == "entity") {
                        $newarray[$key]["endorsed_name"] = $entity[$eval["endorse"]["endorsed_id"]];
                    }
//			if((strtolower($eval["endorse"]["type"])=="standard" || strtolower($eval["endorse"]["type"])=="private" ) && ($eval["endorse"]["endorsement_for"]=="user") )
//{
//	
//	$endorsereply = $eval["reply"];
//	$reply = array();
//	if(!empty($endorsereply)){
//			foreach($e ndorsereply as $replyval){
//				
//			  $reply[]	=array("reply"=>$replyval["reply"]);
//			  
//			}
//	}
//	$newarray[$key]["endorse_reply"] =$reply;
//	$newarray[$key]["endorse_reply_count"] =count($reply);
//	
//}
                }
                if ($loggedInUser['current_org']['joined'] == 0) {
                    if ($this->UserOrganization->updateAll(array("UserOrganization.joined" => 1), array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                        $this->Session->write('Auth.User.current_org.joined', 1);
                        $this->JoinOrgCode->updateAll(array("is_expired" => 1), array("email" => $loggedInUser['email'], "organization_id" => $org_id));
                    }
                }

                $returndata = array("endorse_data" => $newarray, "total_page" => $totalpage, "server_time" => $serverCurrentTime);
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Organization Endorsement ",
                        "data" => $returndata),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => ""),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function endorsedetails() {
        $loggedinUser = $this->Auth->user();

        $params = array();
        $params['fields'] = "*";
        $params['conditions'] = array("DefaultOrg.user_id" => $loggedinUser['id']);
        $defaultOrganization = $this->DefaultOrg->find("first", $params);
//$defaultOrganization['Organization']['public_endorse_visible'];
        if ($this->request->is('post')) {
            $eid = $this->request->data["e_id"];

            $selfuser_id = $loggedinUser["id"];
            $isRead = 0;
            $message_display_flag = 1;

            $params = array();
            $params['fields'] = "*";
            $params['conditions'] = array("Endorsement.id" => $eid);
            $endorsement = $this->Endorsement->find("all", $params);
//            pr($endorsement); exit;
            $endorse = array();
            if (!empty($endorsement)) {
//pr($endorsement);

                $userid = array();
                $endorse = $endorsement[0]["Endorsement"];
//
                if ($endorse["is_read"] == 0 && isset($this->request->data["isRead"]) && $this->request->data["isRead"] == 1) {
                    $this->Endorsement->id = $eid;
                    $this->Endorsement->savefield("is_read", 1);
                    $endorse["is_read"] = 1;
                }

//
                $org_id = $endorsement[0]["Endorsement"]["organization_id"];
                $endorse["endorsed_name"] = "";
                $endorserd_id = $endorsement[0]["Endorsement"]["endorsed_id"];
                $endorse["endorser_name"] = "";
                $userid[] = $endorser_id = $endorsement[0]["Endorsement"]["endorser_id"];
                $endorsecorevalues = $endorsement[0]["EndorseCoreValues"];
                //pr($endorsement); exit;
                if (isset($endorsement[0]["EndorseHashtag"])) {
                    $endorsehashtags = $endorsement[0]["EndorseHashtag"];
                } else {
                    $endorsehashtags = array();
                }


                $cvalarray = $hashArray = array();
                if (!empty($endorsecorevalues)) {
                    foreach ($endorsecorevalues as $cval) {
                        $cvalarray[] = $cval["value_id"];
                    }
                }

                if (!empty($endorsehashtags)) {
                    foreach ($endorsehashtags as $hTag) {
                        $hashArray[] = $hTag["hashtag_id"];
                    }
                }

                $core_values = array();
                if (!empty($cvalarray)) {
                    $core_values = $this->getOrgValues($org_id, "OrgCoreValues", 1, $cvalarray);
                    foreach ($core_values as $key => $value) {
                        //pr($value['custom_message_disabled_user_id']);
                        $core_values[$key]['custom_message_disabled_user_id'] = json_decode($value['custom_message_disabled_user_id']);
                    }
                }

                $hashtag_values = array();
                if (!empty($hashArray)) {
                    $hashtag_values = $this->getOrgValues($org_id, "OrgHashtag", 1, $hashArray);
                }

//                pr($core_values); exit;


                $endorse["core_values"] = $core_values;
                $endorse["hashtags"] = $hashtag_values;



                if ($endorsement[0]["Endorsement"]["endorsement_for"] == "user") {
                    $userid[] = $endorserd_id;
                }
//

                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userid),
                    'fields' => array('id', 'fname', 'lname', 'image')
                ));
                $userdata = array();
                foreach ($userinfo as $userval) {

                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                }
//
                $endorse["is_reply"] = 0;
                $endorse["reply"] = "";
                $endorse["reply_counter"] = "";
                if ((strtolower($endorse["type"]) == "standard" || strtolower($endorse["type"]) == "private" ) && ($endorsement[0]["Endorsement"]["endorsement_for"] == "user") && (in_array($selfuser_id, $userid))) {
                    $endorsereply = $endorsement[0]["EndorseReplies"];
                    $reply = array();

                    if (!empty($endorsereply)) {
                        foreach ($endorsereply as $replyval) {
                            if ($replyval["user_id"] == $endorserd_id) {
                                $endorse["reply"] = $replyval["reply"];
                            } elseif ($replyval["user_id"] == $endorser_id) {
                                $endorse["reply_counter"] = $replyval["reply"];
                            }
                        }
                    }
                    $reply_count = count($endorsereply);
                    $endorse["endorse_reply_count"] = $reply_count;
                    if ($selfuser_id == $endorserd_id && $reply_count == 0) {
                        $endorse["is_reply"] = 1;
                    } elseif ($selfuser_id == $endorser_id && $reply_count == 1) {
                        $endorse["is_reply"] = 1;
                    }
                }
                $newuserdata = explode("&&&&", $userdata[$endorser_id]);
                $endorse["endorser_name"] = $newuserdata[0];
                if (isset($newuserdata[1])) {
                    $endorse["endorse_image"] = $newuserdata[1];
                }

                $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $loggedinUser['id'], "UserOrganization.organization_id" => $org_id)));
                $endormentMsg = "";
                if (isset($endorsement[0]["Endorsement"]['message'])) {
                    $endormentMsg = $endorsement[0]["Endorsement"]['message'];
                }
                $endorsval = $endorsement[0]["Endorsement"];
                if ($endorsement[0]["Endorsement"]["endorsement_for"] == "user") {
                    $newuserdata = explode("&&&&", $userdata[$endorserd_id]);
                    $endorse["endorsed_name"] = $newuserdata[0];
                    if (isset($newuserdata[1])) {
                        $endorse["endorsed_image"] = $newuserdata[1];
                    }
                    $user_id = $loggedinUser['id'];
                    $endorsval = $endorsement[0]["Endorsement"];
//pr($endorsement[0]["Endorsement"]); exit;
//                    pr($userOrganization);die;
//                        if(in_array($user_id,array($endorsval["endorser_id"],$endorsval["endorsed_id"])) || $userOrganization['UserOrganization']['user_role'] == 2){
//                    print_r($selfuser_id);
//                    print_r($userid);
//                    exit;
                    if ($userOrganization['UserOrganization']['user_role'] == 2) {
                        $message_display_flag = 1;
                    } else {
                        if (!in_array($selfuser_id, $userid)) {
                            $message_display_flag = 0;
                            $endorse["message"] = "";
                            $endorse["endorse_reply_count"] = 0;
                        }
                    }
                } elseif ($endorsement[0]["Endorsement"]["endorsement_for"] == "department") {
                    if ($selfuser_id != $endorser_id) {
                        $params = array();
                        $params['fields'] = "id";
                        $params['conditions'] = array("UserOrganization.user_id" => $selfuser_id, "UserOrganization.joined" => 1,
                            "UserOrganization.organization_id" => $org_id, "UserOrganization.department_id" => $endorserd_id);

                        $userdepartmentorg = $this->UserOrganization->find("first", $params);
                        if ($userOrganization['UserOrganization']['user_role'] == 2) {
                            $message_display_flag = 1;
                        } else if (empty($userdepartmentorg)) {
                            $message_display_flag = 0;
                            $endorse["message"] = "";
                        }
                    }
                    $department = $this->getOrgValues($org_id, "OrgDepartments", 1, array($endorserd_id));
                    if (!empty($department)) {
                        $endorse["endorsed_name"] = $department[0]["name"];
                    }
                } elseif ($endorsement[0]["Endorsement"]["endorsement_for"] == "entity") {
                    if ($selfuser_id != $endorser_id) {
                        $params = array();
                        $params['fields'] = "id";
                        $params['conditions'] = array("UserOrganization.user_id" => $selfuser_id, "UserOrganization.joined" => 1,
                            "UserOrganization.organization_id" => $org_id, "UserOrganization.entity_id" => $endorserd_id);

                        $userdepartmentorg = $this->UserOrganization->find("first", $params);
                        if ($userOrganization['UserOrganization']['user_role'] == 2) {
                            $message_display_flag = 1;
                        } else if (empty($userdepartmentorg)) {
                            $message_display_flag = 0;
                            $endorse["message"] = "";
                        }
                    }
                    $entity = $this->getOrgValues($org_id, "Entity", 1, array($endorserd_id));
                    if (!empty($entity)) {
                        $endorse["endorsed_name"] = $entity[0]["name"];
                    }
                }
// fetching attatched image
                $attachedimg = array();
                $emojisimg = array();
                $attachedimage = $endorsement[0]["EndorseAttachments"];

                if ($endorse["type"] == "anonymous") {
                    $endorse["message"] = "";
                }


                if (!empty($attachedimage) && $message_display_flag == 1) {

                    foreach ($attachedimage as $attachval) {
// ENDORSE_IMAGE_DIR

                        if ($attachval["name"] != "" && $attachval["type"] == "image") {
                            $tempIimgs = Router::url('/', true) . "app/webroot/" . ENDORSE_IMAGE_DIR . "small/" . $attachval["name"];
                            $tempIimgs = str_replace("http", "https", $tempIimgs);
                            $attachedimg[] = $tempIimgs;
                        } elseif ($attachval["name"] != "" && trim($attachval["type"]) == "emojis") {
                            $tempIimgs1 = Router::url('/', true) . "app/webroot/" . BITMOJIS_IMAGE_DIR . $attachval["name"];
                            if (strpos($tempIimgs1, 'localhost') < 0 || strpos($tempIimgs1, 'staging') < 0) {
                                $tempIimgs1 = str_replace("http", "https", $tempIimgs1);
                            }
                            $emojisimg[] = $tempIimgs1;
//                            $emojisimg[] = $attachval["name"];
                        } elseif ($attachval["name"] != "" && trim($attachval["type"]) == "bitmojis") {
//                            $emojis_url = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
                            $tempIimgs2 = Router::url('/', true) . "app/webroot/" . BITMOJIS_IMAGE_DIR . $attachval["name"];
                            $tempIimgs2 = str_replace("http", "https", $tempIimgs2);
                            $bitmojisimg[] = $tempIimgs2;
                        }
                    }
                }
// end
                $endorse["attatched_image"] = $attachedimg;
                $endorse["emojis_image"] = $emojisimg;
                if (isset($bitmojisimg) && $bitmojisimg != '') {
                    $endorse["bitmoji_images"] = $endorse["bitmojis_image"] = $bitmojisimg;
                } else {
                    $endorse["bitmoji_images"] = $endorse["bitmojis_image"] = array();
                }



//Added by Babulal prasad to show/hide public message according to setting to show/hide public endorsment message
                $organizationDATA = $this->getOrgValues($org_id, "Organization", 1);
                $public_endorse_visible = 0;
                if ($organizationDATA[0]['allow_comments'] == 1 && $organizationDATA[0]['public_endorse_visible'] == 1) {
                    $public_endorse_visible = 1;
                }
                if (!in_array($selfuser_id, array($endorsval["endorser_id"], $endorsval["endorsed_id"]))) {
                    if ($endorse["type"] == 'standard' && $public_endorse_visible == 0) {
//$endorse["message"] = "";
                    } else {
                        $endorse["message"] = $endormentMsg;
                    }
                }


                // Added by Babulalprasad @8-may2021 
                $this->loadModel("UserFollowing");
                $followingIdsArray = array();
                $userFollowings = $this->UserFollowing->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $selfuser_id, 'status' => 1)));
                if (!empty($userFollowings)) {
                    $userFollowings = array_shift($userFollowings);
                    $userFollowings = $userFollowings['UserFollowing'];
                    $uFollowingID = $userFollowings['id'];
                    $followingIdsArray = json_decode($userFollowings['following_ids']);
                }

                $endorse['endorsed_following'] = false;
                $endorse['endorser_following'] = false;
                if (!empty($followingIdsArray)) {
                    if (in_array($endorser_id, $followingIdsArray)) {
                        $endorse['endorser_following'] = true;
                    }
                    if (in_array($endorserd_id, $followingIdsArray)) {
                        $endorse['endorsed_following'] = true;
                    }
                }

                //pr($endorse); exit;
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Endorsement details",
                        "data" => $endorse),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "invalid endorsement ."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getPendingPostsList() {
        if ($this->request->is('post')) {
            $loggedInUser = $this->Auth->user();
            if (isset($loggedInUser['current_org'])) {
                $org_id = $loggedInUser['current_org']['id'];
                $loggedInUserID = $loggedInUser["id"];
                $conditionsArray = array();
                $limit = Configure::read("pageLimit");
                if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
                    $page = $this->request->data["page"];
                    $offset = $page * $limit;
                } else {
                    $page = 1;
                    $offset = 0;
                }

                $params = array();
                $params['fields'] = "count(*) as cnt";
                $conditionsArray = array('PostSchedule.status' => 0, 'Post.user_id' => $loggedInUserID);
                $params['conditions'] = $conditionsArray;
                $params['joins'] = array(
                    array(
                        'table' => 'posts',
                        'alias' => 'Post',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Post.id = PostSchedule.post_id',
                        )
                    )
                );
                $totalPending = $this->PostSchedule->find('all', $params); // Geting count for all pending post of this user
//                echo $this->PostSchedule->getLastQuery(); exit;

                $totalPendingPosts = $totalPending[0][0]["cnt"];
                $totalpage = ceil($totalPendingPosts / $limit);

                $paramsFeed['conditions'] = $conditionsArray;
                $paramsFeed['limit'] = $limit;
                $paramsFeed['page'] = $page;
                $paramsFeed['offset'] = $offset;
                $paramsFeed['fields'] = "*,UNIX_TIMESTAMP(FeedTrans.publish_date) as publish_date,UNIX_TIMESTAMP(Post.created) as create_date,UNIX_TIMESTAMP() as curr_time ";
                $paramsFeed['order'] = 'PostSchedule.datetime desc';
                $paramsFeed['joins'] = array(
                    array(
                        'table' => 'posts',
                        'alias' => 'Post',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Post.id = PostSchedule.post_id',
                        )
                    ),
                    array(
                        'table' => 'feed_trans',
                        'alias' => 'FeedTrans',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'FeedTrans.feed_id = PostSchedule.post_id',
                            'FeedTrans.feed_type= "post"'
                        )
                    )
                );

                $allPendingPostData = $this->PostSchedule->find('all', $paramsFeed);
                $returndata = array("pending_posts" => $allPendingPostData, "total_page" => $totalpage);
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => ""
                        , 'data' => $returndata),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => ""),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getWallPostList() {

        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();


            $user_id = $loggedInUser['id'];
            if (isset($loggedInUser['current_org'])) {
//print_r($loggedInUser['current_org']);
                $org_id = $loggedInUser['current_org']['id'];
                $keyword = "";
//if($this->request->data["keyword"])
//if(isset($this->request->data["keyword"]))
//{
//$keyword = $this->request->data["keyword"];
//}
//
                $params = array();
                $params['fields'] = "user_id,entity_id,department_id";
                $params['conditions'] = array(
                    "UserOrganization.organization_id" => $org_id, "UserOrganization.status" => 1);
                $userdepartmentorg = $this->UserOrganization->find("all", $params);

                $entity_user_array = array();
                $department_user_array = array();
                foreach ($userdepartmentorg as $userorgval) {
                    if ($userorgval["UserOrganization"]["entity_id"] > 0) {
                        $entity_user_array[$userorgval["UserOrganization"]["entity_id"]][] = $userorgval["UserOrganization"]["user_id"];
                    }
                    if ($userorgval["UserOrganization"]["department_id"] > 0) {
                        $department_user_array[$userorgval["UserOrganization"]["department_id"]][] = $userorgval["UserOrganization"]["user_id"];
                    }
                }
//                  print_r($entity_user_array);
//                  echo "<hr>";
//                 print_r($department_user_array);
//                 exit;
//$type = $this->request->data["type"];
                $endorse_type = "";
                $endorse_search_id = "";

                $start_date = "";
                $end_date = "";
                if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                    $start_date = $this->request->data["start_date"];
                }
                if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                    $end_date = $this->request->data["end_date"];
                }

                $limit = Configure::read("pageLimit");
                if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
                    $page = $this->request->data["page"];
                    $offset = $page * $limit;
                } else {
                    $page = 1;
                    $offset = 0;
                }

                $params = array();
                $params['fields'] = "count(*) as cnt";
                $conditionarray["Post.organization_id"] = $org_id;
                $updateArray = array();


                $conditionarray["Post.user_id"] = $user_id;
                $conditionarray["Post.status"] = 1;

//if ($type != "endorser") {
///$this->UserOrganization->updateAll($updateArray, array("user_id" => $loggedInUser['id'], "organization_id" => $loggedInUser['current_org']['id']));
//}

                if ($start_date != "") {
                    $conditionarray["Post.created >= "] = date("Y-m-d 00:00:00", $start_date);
                }
                if ($end_date != "") {
                    $conditionarray["Post.created <= "] = date("Y-m-d 23:59:59", $end_date);
                }
                $params['conditions'] = $conditionarray;
//pr($conditionarray); exit
                $params['order'] = 'Post.created desc';
                $totalendorsement = $this->Post->find("all", $params);

//                $conditionarray["PostAttachment.type"] = 'image';
//                $params['conditions'] = $conditionarray;
//echo $this->Endorsement->getLastQuery();die;
                $totalendorse = $totalendorsement[0][0]["cnt"];
                $totalpage = ceil($totalendorse / $limit);
                $params['joins'] = array(
                    array(
                        'table' => 'post_likes',
                        'alias' => 'PostLike',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Post.id = PostLike.post_id ',
                            'PostLike.user_id =' . $user_id
                        )
                    ),
                    array(
                        'table' => 'post_attachments',
                        'alias' => 'PostAttachment',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Post.id = PostAttachment.post_id ',
                            'PostAttachment.type = "image"'
                        )
                    )
                );

                $params['fields'] = "*,UNIX_TIMESTAMP(Post.created) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $params['limit'] = $limit;
                $params['page'] = $page;
                $params['offset'] = $offset;
                $params['order'] = 'Post.created desc';
                $params['group'] = 'Post.id';
//                pr($params);
//                exit;
                $this->Post->unbindModel(array('hasMany' => array('PostAttachments')));
//$this->Endorsement->bindModel(array('hasMany' => array('EndorseCoreValues')));
                $endorsement = $this->Post->find("all", $params);
//                $log = $this->Post->getDataSource()->getLog(false, false);
//                pr($log);

                $endorsmentarray = array();
                $departmentarray = array();
                $entityarray = array();
                $userarray = array();
                $core_values = $this->getOrgValues($org_id, "OrgCoreValues", 1);
                $coreval = array();
                foreach ($core_values as $cvalue) {
                    $coreval[$cvalue["id"]] = $cvalue["name"] . "&&&&" . $cvalue["color_code"];
                }

                $serverCurrentTime = "";
//pr($endorsement); exit;
                foreach ($endorsement as $key => $value) {

                    if (isset($value['Post']['image_count']) && $value['Post']['image_count'] > 0) {
                        $endorsmentarray[$key]["post_image"] = array(Router::url('/', true) . "app/webroot/" . POST_IMAGE_DIR . $value["PostAttachment"]['name']);
                    } else if (isset($value['Post']['image_count']) && $value['Post']['image_count'] < 1 && $value['Post']['emojis_count'] > 0) {
                        $endorsmentarray[$key]["post_image"] = "";
                        $PostAttachData = $this->PostAttachment->getEmojiByPostId($value['Post']['id']);
                        $endorsmentarray[$key]["post_image"] = array(Router::url('/', true) . EMOJIS_IMAGE_DIR . $PostAttachData);
                    } else {
                        $endorsmentarray[$key]["post_image"] = array();
                    }

                    $displayflag = 0;
                    $endorsval = $value["Post"];

                    $endorsval["created"] = $value[0]["create_date"];
                    $serverCurrentTime = $value[0]["curr_time"];
                    $endorsimgcount = $endorsval["image_count"];
                    $endorsmentarray[$key]["post"] = $endorsval;
                    $endorsmentarray[$key]["imagecount"] = $endorsval["image_count"];
                    $endorsmentarray[$key]["emojis_count"] = $endorsval["emojis_count"];
                    $endorsmentarray[$key]["user_id"] = $endorsval["user_id"];

                    $endorsmentarray[$key]["reply"] = $endorsval["is_reply"];

                    if ($value["PostLike"]["id"] != "") {
                        $endorsmentarray[$key]["like"] = 1;
                    } else {
                        $endorsmentarray[$key]["like"] = 0;
                    }
//$endorsmentarray[$key]["attatched_image"]=$value["EndorseAttachments"];
                    $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                    $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id)));
                    $userarray[] = $endorsval["user_id"];
                    $endorsmentarray[$key]["displayflag"] = $displayflag;
                }


                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userarray),
                    'fields' => array('id', 'fname', 'lname', 'image', 'about')
                ));
//pr($userinfo);
                $userdata = array();
                foreach ($userinfo as $userval) {
                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                    $userabout[$userval["User"]["id"]]['about'] = trim($userval["User"]["about"]);
                }



                $department = array();
                $entity = array();
                if (!empty($departmentarray)) {
                    $departmentarr = $this->getOrgValues($org_id, "OrgDepartments", 1);
                    if (!empty($departmentarr)) {
                        foreach ($departmentarr as $dval) {
                            $department[$dval["id"]] = $dval["name"];
                        }
                    }
                }

                if (!empty($entityarray)) {
                    $entity1 = $this->getOrgValues($org_id, "Entity", 1);
                    if (!empty($entity1)) {
                        foreach ($entity1 as $dval) {
                            $entity[$dval["id"]] = $dval["name"];
                        }
                    }
                }
                $newarray = array();
//pr($endorsmentarray); exit;
                foreach ($endorsmentarray as $key => $eval) {

                    $newarray[$key]["id"] = $eval["post"]["id"];
                    $newarray[$key]["is_like"] = $eval["like"];
                    $newarray[$key]["is_read"] = $eval["post"]["is_read"];
                    $newarray[$key]["post_id"] = $eval["post"]["id"];
                    $newarray[$key]["user_id"] = $eval["post"]["user_id"];
                    $newarray[$key]["post_image"] = $eval["post_image"];
                    $newarray[$key]["comments_count"] = $eval['post']["comments_count"];

//	$newarray[$key]["attatched_image"] =  $eval["attatched_image"];
                    if (isset($userdata[$eval["post"]["user_id"]])) {
                        $newuserdata = explode("&&&&", $userdata[$eval["post"]["user_id"]]);

                        $newarray[$key]["user_name"] = $newuserdata[0];
                        $newarray[$key]["user_about"] = $userabout[$userval["User"]["id"]]['about'];
                        if (isset($newuserdata[1])) {
                            $newarray[$key]["user_image"] = $newuserdata[1];
                        }
                    } else {
                        $newarray[$key]["user_name"] = "";
                    }

//$newarray[$key]["type"] = $eval["endorse"]["type"];
                    $newarray[$key]["message"] = $eval["post"]["message"];
                    $newarray[$key]["title"] = $eval["post"]["title"];
//                    if ($eval["displayflag"] == 1) {
//                        $newarray[$key]["message"] = $eval["post"]["message"];
//                    } else {
//                        $newarray[$key]["message"] = "";
//                    }
                    $newarray[$key]["like_count"] = $eval["post"]["like_count"];
                    $newarray[$key]["created"] = $eval["post"]["created"];

                    $newarray[$key]["imagecount"] = $eval["imagecount"];
                    $newarray[$key]["emojiscount"] = $eval["emojis_count"];
                    $newarray[$key]["is_reply"] = $eval["reply"];
                    $newarray[$key]["list_type"] = 'wallpost';
                }
                if ($loggedInUser['current_org']['joined'] == 0) {
                    if ($this->UserOrganization->updateAll(array("UserOrganization.joined" => 1), array("UserOrganization.user_id" => $loggedInUser['id'], "UserOrganization.organization_id" => $org_id))) {
                        $this->Session->write('Auth.User.current_org.joined', 1);
                        $this->JoinOrgCode->updateAll(array("is_expired" => 1), array("email" => $loggedInUser['email'], "organization_id" => $org_id));
                    }
                }

                $returndata = array("post_data" => $newarray, "total_page" => $totalpage, "server_time" => $serverCurrentTime);
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Organization's Post",
                        "data" => $returndata),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => ""),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    function getPostLastComment($postId) {
        $res = $this->PostComment->find('all', array(
            'fields' => array('*', "UNIX_TIMESTAMP(PostComment.created) as create_date"),
            'conditions' => array('PostComment.post_id' => $postId),
            'order' => 'created desc', 'limit' => '1'
        ));
        $commentData = array();
        if (!empty($res[0])) {
            $userId = $res[0]['PostComment']['user_id'];
            $userinfo = $this->User->find('all', array(
                'conditions' => array('User.id' => $userId),
                'fields' => array('id', "CONCAT(trim(fname),' ',trim(lname)) as fullname", 'image')
            ));
            $userName = $userImage = '';
            if (!empty($userinfo)) {
                if (isset($userinfo[0]['User']['image'])) {
                    $userImagehttp = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userinfo[0]['User']['image'];
                    $userImage = str_replace("http", "https", $userImagehttp);
                }
                if (isset($userinfo[0][0]['fullname']) && $userinfo[0][0]['fullname'] != '') {
                    $userName = $userinfo[0][0]['fullname'];
                }
            }

            $commentData = $res[0]['PostComment'];
            $commentData['create_date'] = $res[0][0]['create_date'];
            $commentData['created'] = $res[0][0]['create_date'];
            $commentData['user_name'] = $userName;
            $commentData['user_image'] = $userImage;
        }
        return $commentData;
    }

    public function increasePostClickCount($user_id, $post_id) {
        $clickEventArray = array("post_id" => $post_id, "user_id" => $user_id, "post_click" => 1);
        $result = $this->PostEventCount->save($clickEventArray);
    }

    public function wallPostdetails() {

        if ($this->request->is('post')) {
            $post_id = $this->request->data["post_id"];
            $loggedinUser = $this->Auth->user();
            $selfuser_id = $loggedinUser["id"];
            $isRead = 0;
            $message_display_flag = 1;
            $user_id = $loggedinUser["id"];
            $params = array();
            $params['fields'] = "*,UNIX_TIMESTAMP() as curr_time,UNIX_TIMESTAMP(post_publish_date) as created";
            $params['conditions'] = array("Post.id" => $post_id);
            $params['joins'] = array(
                array(
                    'table' => 'post_likes',
                    'alias' => 'PostLike',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Post.id = PostLike.post_id ',
                        'PostLike.user_id =' . $user_id
                    )
                )
            );
            $post = $this->Post->find("all", $params);
            $this->increasePostClickCount($user_id, $post_id);
            $postData = array();
            if (!empty($post)) {

                $userid = array();
                $postData = $post[0]["Post"];
                $currentTime = $post[0][0]['curr_time'];
                $createdTime = $post[0][0]['created'];

                if ($post[0]["PostLike"]["id"] != "") {
                    $postData["is_like"] = 1;
                } else {
                    $postData["is_like"] = 0;
                }
//
                if ($postData["is_read"] == 0 && isset($this->request->data["isRead"]) && $this->request->data["isRead"] == 1) {
                    $this->Post->id = $post_id;
                    $this->Post->savefield("is_read", 1);
                    $postData["is_read"] = 1;
                }


                $org_id = $post[0]["Post"]["organization_id"];
                $userid[] = $endorser_id = $user_id = $post[0]["Post"]["user_id"];

                $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id " => $user_id, "UserOrganization.organization_id" => $org_id)));

                $jobTitle = '';
                if (isset($userOrganization['UserOrganization']['job_title_id']) && $userOrganization['UserOrganization']['job_title_id'] != '') {
                    $jobTitleData = $this->OrgJobTitle->findById($userOrganization['UserOrganization']['job_title_id']);
                    if (isset($jobTitleData['OrgJobTitle']) && !empty($jobTitleData['OrgJobTitle'])) {
                        $jobTitle = $jobTitleData['OrgJobTitle']['title'];
                    } else {
                        $jobTitle = '';
                    }
                }
                $postData["user_job_title"] = $jobTitle;


                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userid),
                    'fields' => array('id', 'fname', 'lname', 'image', 'about')
                ));
                $userdata = array();
                $userAbout = '';
                foreach ($userinfo as $userval) {
                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                    $userAbout = $userval["User"]["about"];
                }
//
                $postData["is_reply"] = 0;
                $postData["reply"] = "";
                $postData["reply_counter"] = "";

                $newuserdata = explode("&&&&", $userdata[$user_id]);
                $postData["user_name"] = $newuserdata[0];
                $postData["user_about"] = $userAbout;
                if (isset($newuserdata[1])) {
                    $postData["user_image"] = $newuserdata[1];
                }

                $postCommentData = new Object();
                if ($post[0]['Post']['comments_count'] > 0) {
                    $postCommentData = $this->getPostLastComment($post_id);
                }
                $postData["PostComment"] = $postCommentData;


// fetching attatched image
                $attachedimg = array();
                $emojisimg = array();
                $postAttachmentFiles = array();
                $attachedimage = $post[0]["PostAttachments"];

//                if ($postData["type"] == "anonymous") {
//                    $postData["message"] = "";
//                }


                if (!empty($attachedimage) && $message_display_flag == 1) {
                    foreach ($attachedimage as $attachval) {
                        if ($attachval["name"] != "" && $attachval["type"] == "image") {
                            $tmpImageee = Router::url('/', true) . "app/webroot/" . POST_IMAGE_DIR . "small/" . $attachval["name"];
                            $attachedimg[] = str_replace("http", "https", $tmpImageee);
                        } elseif ($attachval["name"] != "" && trim($attachval["type"]) == "emojis") {
                            $tmpImageee1 = Router::url('/', true) . "app/webroot/" . BITMOJIS_IMAGE_DIR . $attachval["name"];
                            if (strpos($tmpImageee1, 'localhost') < 0 || strpos($tmpImageee1, 'staging') < 0) {
                                $tmpImageee1 = str_replace("http", "https", $tmpImageee1);
                            }
                            $emojisimg[] = $tmpImageee1;
//                            $emojisimg[] = $attachval["name"];
                        } elseif ($attachval["name"] != "" && trim($attachval["type"]) == "files") {
                            $fileData = json_decode($attachval["name"], true);
                            $orgUrl = $fileData['url'];
                            $tmpImageee2 = Router::url('/', true) . "app/webroot/" . POST_FILE_DIR . $orgUrl;
                            $fileData['url'] = str_replace("http", "https", $tmpImageee2);
                            $fileData['url_web'] = POST_FILE_DIR . $orgUrl;
                            $postAttachmentFiles[] = $fileData;
                        }
                    }
                }

                $postData["attatched_files"] = $postAttachmentFiles;
                $postData["attatched_image"] = $attachedimg;
                $postData["emojis_image"] = $emojisimg;
                $postData["server_time"] = $currentTime;
                $postData["created"] = $createdTime;

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Post details",
                        "data" => $postData),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "invalid post."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getPendingwallPostdetails() {

        if ($this->request->is('post')) {
            $post_id = $this->request->data["post_id"];
            $loggedinUser = $this->Auth->user();
            $selfuser_id = $loggedinUser["id"];
            $isRead = 0;
            $message_display_flag = 1;
            $user_id = $loggedinUser["id"];
            $params = array();
            $params['fields'] = "*,UNIX_TIMESTAMP() as curr_time,UNIX_TIMESTAMP(Post.created) as created,PostSchedule.*";
            $params['conditions'] = array("Post.id" => $post_id);
            $params['joins'] = array(
                array(
                    'table' => 'post_likes',
                    'alias' => 'PostLike',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Post.id = PostLike.post_id ',
                        'PostLike.user_id =' . $user_id
                    )
                ),
                array(
                    'table' => 'post_schedules',
                    'alias' => 'PostSchedule',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'PostSchedule.post_id = Post.id '
                    )),
                array(
                    'table' => 'feed_trans',
                    'alias' => 'FeedTran',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'FeedTran.feed_id = Post.id',
                        'FeedTran.feed_type = "post"'
                    )
                ),
            );

            $post = $this->Post->find("all", $params);
            $this->increasePostClickCount($user_id, $post_id);
            $postData = array();
            if (!empty($post)) {

                $userid = array();
                $postData = $post[0]["Post"];
                $currentTime = $post[0][0]['curr_time'];
                $createdTime = $post[0][0]['created'];

                if ($post[0]["PostLike"]["id"] != "") {
                    $postData["is_like"] = 1;
                } else {
                    $postData["is_like"] = 0;
                }
//
                if ($postData["is_read"] == 0 && isset($this->request->data["isRead"]) && $this->request->data["isRead"] == 1) {
                    $this->Post->id = $post_id;
                    $this->Post->savefield("is_read", 1);
                    $postData["is_read"] = 1;
                }

//
                $org_id = $post[0]["Post"]["organization_id"];
                $userid[] = $endorser_id = $user_id = $post[0]["Post"]["user_id"];

                $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                $userOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id " => $user_id, "UserOrganization.organization_id" => $org_id)));

                $jobTitle = '';
                if (isset($userOrganization['UserOrganization']['job_title_id']) && $userOrganization['UserOrganization']['job_title_id'] != '') {
                    $jobTitleData = $this->OrgJobTitle->findById($userOrganization['UserOrganization']['job_title_id']);
                    if (isset($jobTitleData['OrgJobTitle']) && !empty($jobTitleData['OrgJobTitle'])) {
                        $jobTitle = $jobTitleData['OrgJobTitle']['title'];
                    } else {
                        $jobTitle = '';
                    }
                }
                $postData["user_job_title"] = $jobTitle;


                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $userid),
                    'fields' => array('id', 'fname', 'lname', 'image', 'about')
                ));
                $userdata = array();
                $userAbout = '';
                foreach ($userinfo as $userval) {
                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                    $userAbout = $userval["User"]["about"];
                }
//
                $postData["is_reply"] = 0;
                $postData["reply"] = "";
                $postData["reply_counter"] = "";

                $newuserdata = explode("&&&&", $userdata[$user_id]);
                $postData["user_name"] = $newuserdata[0];
                $postData["user_about"] = $userAbout;
                if (isset($newuserdata[1])) {
                    $postData["user_image"] = $newuserdata[1];
                }

                $postCommentData = new Object();
                if ($post[0]['Post']['comments_count'] > 0) {
                    $postCommentData = $this->getPostLastComment($post_id);
                }
                $postData["PostComment"] = $postCommentData;


// fetching attatched image
                $attachedimg = array();
                $emojisimg = array();
                $postAttachmentFiles = array();
                $attachedimage = $post[0]["PostAttachments"];

//                if ($postData["type"] == "anonymous") {
//                    $postData["message"] = "";
//                }


                if (!empty($attachedimage) && $message_display_flag == 1) {
                    foreach ($attachedimage as $attachval) {
                        if ($attachval["name"] != "" && $attachval["type"] == "image") {
                            $attachedimg[] = Router::url('/', true) . "app/webroot/" . POST_IMAGE_DIR . "small/" . $attachval["name"];
                        } elseif ($attachval["name"] != "" && trim($attachval["type"]) == "emojis") {
                            $emojisimg[] = Router::url('/', true) . "app/webroot/" . EMOJIS_IMAGE_DIR . $attachval["name"];
                        } elseif ($attachval["name"] != "" && trim($attachval["type"]) == "files") {
                            $fileData = json_decode($attachval["name"], true);
                            $orgUrl = $fileData['url'];
                            $fileData['url'] = Router::url('/', true) . "app/webroot/" . POST_FILE_DIR . $orgUrl;
                            $fileData['url_web'] = POST_FILE_DIR . $orgUrl;
                            $postAttachmentFiles[] = $fileData;
                        }
                    }
                }

                $postData["attatched_files"] = $postAttachmentFiles;
                $postData["attatched_image"] = $attachedimg;
                $postData["emojis_image"] = $emojisimg;
                $postData["server_time"] = $currentTime;
                $postData["created"] = $createdTime;
                $postData["FeedTran"] = $post[0]["FeedTran"];
                $postData["PostSchedule"] = $post[0]["PostSchedule"];
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Post details",
                        "data" => $postData),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "invalid post."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getWallPostCommentLists() {

        if ($this->request->is('post')) {
            $post_id = $this->request->data["post_id"];
            $loggedinUser = $this->Auth->user();
            $selfuser_id = $loggedinUser["id"];
            $isRead = 0;
            $message_display_flag = 1;

            if (isset($this->request->data["limit"])) {
                $limit = $this->request->data["limit"];
            } else {
                $limit = Configure::read("pageLimit");
            }
            if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
                $page = $this->request->data["page"];
                $offset = $page * $limit;
            } else {
                $page = 1;
                $offset = 0;
            }

//            $limit = 5;
//            $page = 2;

            $params = array();
            $params['conditions'] = array("PostComment.post_id" => $post_id);
            $params['order'] = 'created desc';

            /*             * * GET TOTAL PAGES OF POST START*** */
            $countfields = "count(*) as cnt";
            $params['fields'] = $countfields;
            $PostCArray = $this->PostComment->find("all", $params);
            $totalorg = $PostCArray[0][0]["cnt"];
            $totalpage = ceil($totalorg / $limit);
            /*             * * GET TOTAL PAGES OF POST END*** */

            $params['fields'] = "*,UNIX_TIMESTAMP(created) as created, UNIX_TIMESTAMP() as curr_time";
            $params['limit'] = $limit;
            $params['page'] = $page;
            $params['offset'] = $offset;

            $postAllComments = $this->PostComment->find("all", $params);

            $postData = array();
            if (!empty($postAllComments)) {
//print_r($endorsement);
                $commentsList = $usersList = array();
                foreach ($postAllComments as $index => $postCmtData) {
                    $data = array_merge($postCmtData['PostComment'], $postCmtData[0]);
                    $currentTime = $postCmtData[0]['curr_time'];
                    $commentsList[] = $data;
                    $usersList[$data['user_id']] = $data['user_id'];
                }
                krsort($commentsList);
//sort($commentsList);

                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $usersList),
                    'fields' => array('id', 'fname', 'lname', 'image')
                ));
//pr($userinfo);
                $userdata = array();
                foreach ($userinfo as $userval) {
                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                }

                $responseData = array();
                foreach ($commentsList as $index => $data) {

                    $tmpUserId = $data['user_id'];

                    $newuserdata = explode("&&&&", $userdata[$tmpUserId]);
                    $data["user_name"] = $newuserdata[0];
                    if (isset($newuserdata[1])) {
                        $data["user_image"] = $newuserdata[1];
                    } else {
                        $data["user_image"] = '';
                    }
                    $responseData[] = $data;
                }
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Wall post comments list",
                        "data" => array('postcommentlist' => $responseData, 'total_pages' => $totalpage, 'server_time' => $currentTime, 'current_page' => $page)),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "No comment found."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    /* Added by Babulal Prasad @6June2019
     * To get All Comments list
     */

    public function getEndorseCommentsLists() {

        if ($this->request->is('post')) {
            $endorse_id = $this->request->data["e_id"];

//            $token = $this->request->data['token'];
//            $userinfo = $this->getuserData($token, true);
//            
//            $loggedinUser = $this->Auth->user();
//            $selfuser_id = $loggedinUser["id"];

            $isRead = 0;
            $message_display_flag = 1;

            if (isset($this->request->data["limit"])) {
                $limit = $this->request->data["limit"];
            } else {
                $limit = Configure::read("pageLimit");
            }
            if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
                $page = $this->request->data["page"];
                $offset = $page * $limit;
            } else {
                $page = 1;
                $offset = 0;
            }

//            $limit = 5;
//            $page = 2;

            $params = array();
            $params['conditions'] = array("EndorsementComment.endorsement_id" => $endorse_id);
            $params['order'] = 'created desc';

            /*             * * GET TOTAL PAGES OF POST START*** */
            $countfields = "count(*) as cnt";
            $params['fields'] = $countfields;
            $PostCArray = $this->EndorsementComment->find("all", $params);
            $totalorg = $PostCArray[0][0]["cnt"];
            $totalpage = ceil($totalorg / $limit);
            /*             * * GET TOTAL PAGES OF POST END*** */

            $params['fields'] = "*,UNIX_TIMESTAMP(created) as created, UNIX_TIMESTAMP() as curr_time";
            $params['limit'] = $limit;
            $params['page'] = $page;
            $params['offset'] = $offset;

            $postAllComments = $this->EndorsementComment->find("all", $params);

            $postData = array();
            if (!empty($postAllComments)) {
//print_r($endorsement);
                $commentsList = $usersList = array();
                foreach ($postAllComments as $index => $postCmtData) {
                    $data = array_merge($postCmtData['EndorsementComment'], $postCmtData[0]);
                    $currentTime = $postCmtData[0]['curr_time'];
                    $commentsList[] = $data;
                    $usersList[$data['user_id']] = $data['user_id'];
                }
                krsort($commentsList);
//sort($commentsList);

                $userinfo = $this->User->find('all', array(
                    'conditions' => array('User.id' => $usersList),
                    'fields' => array('id', 'fname', 'lname', 'image')
                ));
//pr($userinfo);
                $userdata = array();
                foreach ($userinfo as $userval) {
                    $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                    if ($userval["User"]["image"] != "") {
                        $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                    }
                }

                $responseData = array();
                foreach ($commentsList as $index => $data) {

                    $tmpUserId = $data['user_id'];

                    $newuserdata = explode("&&&&", $userdata[$tmpUserId]);
                    $data["user_name"] = $newuserdata[0];
                    if (isset($newuserdata[1])) {
                        $data["user_image"] = $newuserdata[1];
                    } else {
                        $data["user_image"] = '';
                    }
                    $responseData[] = $data;
                }
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Endorsements comments list",
                        "data" => array('endorsecommentlist' => $responseData, 'total_pages' => $totalpage, 'server_time' => $currentTime, 'current_page' => $page)),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "No comment found."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function JoinReqOrg() {
        if (isset($this->request->data['token']) && isset($this->request->data['org_data'])) {
            $org_data = json_decode($this->request->data['org_data'], true);
            foreach ($org_data as $orgId => $orgData) {
                $org_ids = array();
                $org_ids = array($orgData['orgid']);
                $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))), "conditions" => array("organization_id" => $org_ids, "user_role" => 2, 'UserOrganization.status' => 1)));
                $adminorg = array();
                foreach ($userorgdata as $uservalorg) {
                    $adminorg[$uservalorg['Organization']['id']][] = $uservalorg;
                }
                $array = array();
                $array['fields'] = array('id', 'name');
                $array['conditions'] = array('id' => $org_ids);
                $orgArray = array();
                $orgArray = $this->Organization->find("all", $array);
                $loggedInUser = $this->Auth->user();
                $user_id = $loggedInUser['id'];
                $alreadyrequested = 0;
                $organization_name = "";
                $emailQueue = array();
                foreach ($orgArray as $orgval) {

//echo $orgval['Organization']['id'];
                    $requestinfo = $this->OrgRequests->find('first', array(
                        'conditions' => array('user_id' => $user_id, 'organization_id' => $orgval['Organization']['id'], "status" => 0),
                        'fields' => array('id')
                    ));

// print_r($requestinfo);
                    if (empty($requestinfo)) {
                        $contact_number = $orgData['contact'];
                        $relation_to_org = $orgData['relation_to_org'];
                        $why_want_to_join = $orgData['why_want_to_join'];
                        $relation_to_org_other = '';
                        if (isset($orgData['relation_to_org_other']) && $orgData['relation_to_org_other'] != '') {
                            $relation_to_org_other = $orgData['relation_to_org_other'];
                        }


                        $requestarray[] = array("organization_id" => $orgval['Organization']['id'], "user_id" => $user_id, "mobile_number" => $contact_number,
                            "relationship_to_org" => $relation_to_org, 'relationship_to_org_desc' => $relation_to_org_other, 'why_want_to_join' => $why_want_to_join);


                        $organization_name .= $orgval['Organization']['name'] . ",";
                        foreach ($adminorg[$orgval['Organization']['id']] as $userval) {

                            $subject = "nDorse Notification -- A user has sent a request to join your organization :" . $orgval['Organization']['name'];
                            $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($userval['User']['fname']), "user_name" => $loggedInUser['email']);

                            /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                            $userIdEncrypted = base64_encode($userval["User"]["id"]);
                            $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                            $viewVars["pathToRender"] = $pathToRender;
                            /*                             * * */

                            $configVars = serialize($viewVars);
                            if (!empty($userval['User']['email'])) {
                                $emailQueue[] = array("to" => $userval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "join_request");
                            }
                        }
                    } else {
                        $alreadyrequested = 1;
                    }
                }
            }//Multiple org data loop
//Your request to join [ORG-NAME]
            if ($alreadyrequested == 0 && !empty($requestarray)) {
                $organization_name = substr($organization_name, 0, -1);
                $this->OrgRequests->saveMany($requestarray);
                if (!empty($emailQueue)) {
                    $this->Email->saveMany($emailQueue);
                }
//save to pending_requests
                $pendingRequestOrgs = $loggedInUser['pending_requests'];
                if ($pendingRequestOrgs == "") {
                    $pendingRequestOrgs = array();
                }
                $pendingRequestOrgs = array_merge($pendingRequestOrgs, $org_ids);
                $this->Session->write('Auth.User.pending_requests', $pendingRequestOrgs);

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Your request to join [" . $organization_name . "] was sent successfully!",
                        "data" => true),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "you have already sent request for this user. ",
                        "data" => true),
                    '_serialize' => array('result')
                ));
            }
        } else if (isset($this->request->data['token']) && isset($this->request->data['org_id'])) {

            $org_ids = explode(",", $this->request->data['org_id']);
            $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))), "conditions" => array("organization_id" => $org_ids, "user_role" => 2, 'UserOrganization.status' => 1)));
            $adminorg = array();
            foreach ($userorgdata as $uservalorg) {

                $adminorg[$uservalorg['Organization']['id']][] = $uservalorg;
            }


            $array = array();
            $array['fields'] = array('id', 'name');
            $array['conditions'] = array('id' => $org_ids);

//
            $orgArray = $this->Organization->find("all", $array);
            $loggedInUser = $this->Auth->user();
            $user_id = $loggedInUser['id'];
            $alreadyrequested = 0;
            $organization_name = "";
            $emailQueue = array();
            foreach ($orgArray as $orgval) {

//echo $orgval['Organization']['id'];

                $requestinfo = $this->OrgRequests->find('first', array(
                    'conditions' => array('user_id' => $user_id, 'organization_id' => $orgval['Organization']['id'], "status" => 0),
                    'fields' => array('id')
                ));

// print_r($requestinfo);
                if (empty($requestinfo)) {
                    $requestarray[] = array("organization_id" => $orgval['Organization']['id'], "user_id" => $user_id, "mobile_number" => '',
                        "relationship_to_org" => '', 'relationship_to_org_desc' => '', 'why_want_to_join' => '');
                    $organization_name .= $orgval['Organization']['name'] . ",";
                    foreach ($adminorg[$orgval['Organization']['id']] as $userval) {

                        $subject = "nDorse Notification -- A user has sent a request to join your organization :" . $orgval['Organization']['name'];
                        $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($userval['User']['fname']), "user_name" => $loggedInUser['email']);

                        /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($userval["User"]["id"]);
                        $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /*                         * * */

                        $configVars = serialize($viewVars);
                        if (!empty($userval['User']['email'])) {
                            $emailQueue[] = array("to" => $userval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "join_request");
                        }
                    }
                } else {
                    $alreadyrequested = 1;
                }
            }
//Your request to join [ORG-NAME]
            if ($alreadyrequested == 0 && !empty($requestarray)) {
                $organization_name = substr($organization_name, 0, -1);
                $this->OrgRequests->saveMany($requestarray);
                if (!empty($emailQueue)) {
                    $this->Email->saveMany($emailQueue);
                }

//save to pending_requests

                $pendingRequestOrgs = $loggedInUser['pending_requests'];
                $pendingRequestOrgs = array_merge($pendingRequestOrgs, $org_ids);

                $this->Session->write('Auth.User.pending_requests', $pendingRequestOrgs);

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Your request to join [" . $organization_name . "] was sent successfully!",
                        "data" => true),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "you have already sent request for this user. ",
                        "data" => true),
                    '_serialize' => array('result')
                ));
            }
//
//			  $invites = $emailQueue = array();
//            $viewVars = array("org_name" => $current_org['name']);
//            $configVars = serialize($viewVars);
//            $subject = "Request to join ".$orgArray['Organization']['name']." organization";
//			$requestorg = array("organization_id" => $current_org['id']);
//            foreach ($emailIds as $email) {
//                if (!in_array($email, $invitedMails)) {
//                    $invites[] = array("organization_id" => $current_org['id'], "email" => $email);
//                    $emailQueue[] = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => "invite");
//                }
//            }
//
//
//            if (!empty($invites)) {
//                $this->Invite->saveMany($invites);
//                $this->Email->saveMany($emailQueue);
//                $msg = "Invitation sent.";
//                $status = true;
//            } else {
//                $msg = "All people are already invited";
//                $status = false;
//            }
//
//            $returnData = array("alreadyInvited" => $invitedMails);
//
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token or organization id  is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function JoinReqOrg_bk_20_sept_2017() {

        if (isset($this->request->data['token']) && isset($this->request->data['org_id'])) {

            $org_ids = explode(",", $this->request->data['org_id']);
            $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))), "conditions" => array("organization_id" => $org_ids, "user_role" => 2, 'UserOrganization.status' => 1)));
            $adminorg = array();
            foreach ($userorgdata as $uservalorg) {

                $adminorg[$uservalorg['Organization']['id']][] = $uservalorg;
            }


            $array = array();
            $array['fields'] = array('id', 'name');
            $array['conditions'] = array('id' => $org_ids);
//
//					    $array['joins'] = array(
//                array(
//                    'table' => 'users',
//                    'alias' => 'user',
//                    'type' => 'INNER',
//                    'conditions' => array(
//                        'Organization.id' => $org_ids,
//                        'Organization.admin_id = user.id'
//                    )
//                )
//            );
//
            $orgArray = $this->Organization->find("all", $array);
            $loggedInUser = $this->Auth->user();
            $user_id = $loggedInUser['id'];
            $alreadyrequested = 0;
            $organization_name = "";
            $emailQueue = array();
            foreach ($orgArray as $orgval) {

//echo $orgval['Organization']['id'];
                $requestinfo = $this->OrgRequests->find('first', array(
                    'conditions' => array('user_id' => $user_id, 'organization_id' => $orgval['Organization']['id'], "status" => 0),
                    'fields' => array('id')
                ));

// print_r($requestinfo);
                if (empty($requestinfo)) {
                    $requestarray[] = array("organization_id" => $orgval['Organization']['id'], "user_id" => $user_id);

                    $organization_name .= $orgval['Organization']['name'] . ",";
                    foreach ($adminorg[$orgval['Organization']['id']] as $userval) {

                        $subject = "nDorse Notification -- A user has sent a request to join your organization :" . $orgval['Organization']['name'];
                        $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($userval['User']['fname']), "user_name" => $loggedInUser['email']);

                        /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($userval["User"]["id"]);
                        $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /*                         * * */

                        $configVars = serialize($viewVars);
                        if (!empty($userval['User']['email'])) {
                            $emailQueue[] = array("to" => $userval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "join_request");
                        }
                    }
                } else {
                    $alreadyrequested = 1;
                }
            }
//Your request to join [ORG-NAME]
            if ($alreadyrequested == 0 && !empty($requestarray)) {
                $organization_name = substr($organization_name, 0, -1);
                $this->OrgRequests->saveMany($requestarray);
                if (!empty($emailQueue)) {
                    $this->Email->saveMany($emailQueue);
                }

//save to pending_requests

                $pendingRequestOrgs = $loggedInUser['pending_requests'];
                $pendingRequestOrgs = array_merge($pendingRequestOrgs, $org_ids);

                $this->Session->write('Auth.User.pending_requests', $pendingRequestOrgs);

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Your request to join [" . $organization_name . "] was sent successfully!",
                        "data" => true),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "you have already sent request for this user. ",
                        "data" => true),
                    '_serialize' => array('result')
                ));
            }
//
//			  $invites = $emailQueue = array();
//            $viewVars = array("org_name" => $current_org['name']);
//            $configVars = serialize($viewVars);
//            $subject = "Request to join ".$orgArray['Organization']['name']." organization";
//			$requestorg = array("organization_id" => $current_org['id']);
//            foreach ($emailIds as $email) {
//                if (!in_array($email, $invitedMails)) {
//                    $invites[] = array("organization_id" => $current_org['id'], "email" => $email);
//                    $emailQueue[] = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => "invite");
//                }
//            }
//
//
//            if (!empty($invites)) {
//                $this->Invite->saveMany($invites);
//                $this->Email->saveMany($emailQueue);
//                $msg = "Invitation sent.";
//                $status = true;
//            } else {
//                $msg = "All people are already invited";
//                $status = false;
//            }
//
//            $returnData = array("alreadyInvited" => $invitedMails);
//
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token or organization id  is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

// endorse like
    public function endorselike() {

        if ($this->request->is('post')) {
            $e_id = $this->request->data["e_id"];
            $like = $this->request->data["like"];
            $loggedinUser = $this->Auth->user();
            $user_id = $loggedinUser["id"];
            $likeresult = $this->EndorsementLike->find('first', array(
                'conditions' => array('user_id' => $user_id, 'endorsement_id' => $e_id),
                'fields' => array('id')
            ));
            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
            $endorsment_count = $this->Endorsement->find("first", array("conditions" => array("id" => $e_id), "fields" => array("like_count", "endorsed_id", "organization_id")));
            //pr($endorsment_count); exit;
            $like_count = $endorsment_count["Endorsement"]["like_count"];
            $endorsed_id = $endorsment_count["Endorsement"]["endorsed_id"];
            $orgId = $endorsment_count["Endorsement"]["organization_id"];
            $status = false;
            if ($like == 1) {

                if (empty($likeresult)) {
                    $likearray = array("endorsement_id" => $e_id, "user_id" => $user_id);
                    $this->EndorsementLike->save($likearray);
                    $this->Endorsement->id = $e_id;
                    $like_count = $endorsment_count["Endorsement"]["like_count"] + 1;
                    $this->Endorsement->id = $e_id;
                    $this->Endorsement->savefield("like_count", $like_count);

                    $status = true;
                    $sendPushNotification = 1;
                    $userDATA = $this->User->findById($user_id);
                    $userName = $userDATA['User']['fname'] . " " . $userDATA['User']['lname'];
                    $msg = $userName . " liked your nDorsement.";
                    /* Send Push notification */
                    if (isset($sendPushNotification) && $sendPushNotification == 1) {
//
//                        $userDATA = $this->User->findById($user_id);
//                        $userName = $userDATA['User']['fname'] . " " . $userDATA['User']['lname'];


                        $loginuser = $this->LoginStatistics->find("all", array(
                            "conditions" => array("user_id" => $endorsed_id, "live" => 1)
                        ));

                        if (!empty($loginuser[0]["LoginStatistics"]) && $loginuser[0]["LoginStatistics"]["device_id"] != "") {


                            if (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "ios") {
                                //print_r($device_token);
                                $deviceToken_msg_arr = array();
                                $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                $count = 1;
                                //$msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n".$val["EndorseReplies"]["reply"];
                                $msg = $userName . " liked your nDorsement.";
                                $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion", "title" => "nDorse App");

                                $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
//                                pr($deviceToken_msg_arr);
//                                exit;
                                /** Added by Babulal Prasad @30-may-2019
                                 * * SAVE DATA for alert center feature Start
                                 * ** */
//                                $this->loadModel('AlertCenterNotification');
//                                $AlertCenterNotificationArray['user_id'] = $endorsed_id;
//                                $AlertCenterNotificationArray['org_id'] = $orgId;
//                                $AlertCenterNotificationArray['alert_type'] = 'Endorsement Like Notification';
//                                $AlertCenterNotificationArray['plain_msg'] = $msg;
//                                $AlertCenterNotificationArray['original_msg'] = $msg;
//                                $AlertCenterNotificationArray['status'] = 0;
//                                $AlertCenterNotificationArray['os'] = 'ios';
//                                $AlertCenterNotificationArray['feed_id'] = $e_id;
//                                $AlertCenterNotificationArray['sender_id'] = $user_id;
//                                $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                                /* SAVE DATA for alert center feature End** */
                                $this->Common->sendPushNotification($deviceToken_msg_arr);
                            } elseif (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "android") {

                                $deviceToken_msg_arr = array();
                                $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                $count = 1;
                                // $end_name = $val['User']['fname'] . " " . $val['User']['lname'];
                                $organization_id = $orgId;
                                // $msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n<br />".$val["EndorseReplies"]["reply"];
                                $msg = $userName . " liked your nDorsement.";
                                $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion",
                                    "title" => "nDorse App");

                                $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
                                //print_r($deviceToken_msg_arr);
                                $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);

                                /** Added by Babulal Prasad @30-may-2019
                                 * * SAVE DATA for alert center feature Start
                                 * ** */
//                                $this->loadModel('AlertCenterNotification');
//                                $AlertCenterNotificationArray['user_id'] = $endorsed_id;
//                                $AlertCenterNotificationArray['org_id'] = $orgId;
//                                $AlertCenterNotificationArray['alert_type'] = 'Endorsement Like Notification';
//                                $AlertCenterNotificationArray['plain_msg'] = $msg;
//                                $AlertCenterNotificationArray['original_msg'] = $msg;
//                                $AlertCenterNotificationArray['status'] = 0;
//                                $AlertCenterNotificationArray['os'] = 'android';
//                                $AlertCenterNotificationArray['feed_id'] = $e_id;
//                                $AlertCenterNotificationArray['sender_id'] = $user_id;
//                                $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                                /* SAVE DATA for alert center feature End** */
                            }
                        }
                    }
                    $this->loadModel('AlertCenterNotification');
                    $AlertCenterNotificationArray['user_id'] = $endorsed_id;
                    $AlertCenterNotificationArray['org_id'] = $orgId;
                    $AlertCenterNotificationArray['alert_type'] = 'Endorsement Like Notification';
                    $AlertCenterNotificationArray['plain_msg'] = $msg;
                    $AlertCenterNotificationArray['original_msg'] = $msg;
                    $AlertCenterNotificationArray['status'] = 0;
                    $AlertCenterNotificationArray['os'] = 'android';
                    $AlertCenterNotificationArray['feed_id'] = $e_id;
                    $AlertCenterNotificationArray['feed_type'] = 'ndorse';
                    $AlertCenterNotificationArray['sender_id'] = $user_id;
                    $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                    $msg = "Endorsement liked successfully";
                    /** END send notification code** */
                } else {
                    $msg = "you have already liked Endorsement";
                    $status = true;
                }
            } else {

                if (!empty($likeresult) && $likeresult["EndorsementLike"]["id"] > 0) {
//["EndorsementLike"]["id"]
                    $this->EndorsementLike->delete($likeresult["EndorsementLike"]["id"]);
                    $like_count = $endorsment_count["Endorsement"]["like_count"] - 1;
                    $this->Endorsement->id = $e_id;
                    $this->Endorsement->savefield("like_count", $like_count);
                    $msg = "Endorsement disliked successfully";
                } else {
                    $msg = "you have already disliked Endorsement";
                }
                $status = true;
            }
            $total_like = 0;
            $this->set(array(
                'result' => array("status" => $status
                    , "msg" => $msg,
                    "data" => array("like_count" => $like_count)),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function increasePostAttachClick() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
                $post_id = $this->request->data["post_id"];
                $loggedinUser = $this->Auth->user();
                $user_id = $loggedinUser["id"];
                $attachmentEventArray = array("post_id" => $post_id, "user_id" => $user_id, "post_attachment_click" => 1);
                $result = $this->PostEventCount->save($attachmentEventArray);
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "count increased."),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function increasePostAttachPinClick() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
                $post_id = $this->request->data["post_id"];
                $loggedinUser = $this->Auth->user();
                $user_id = $loggedinUser["id"];
                $attachmentEventArray = array("post_id" => $post_id, "user_id" => $user_id, "post_attachment_pin_click" => 1);
                $result = $this->PostEventCount->save($attachmentEventArray);
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "count increased."),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function increasePostLikeEventCount($p_id, $user_id) {
        $likeEventArray = array("post_id" => $p_id, "user_id" => $user_id, "post_like_counts" => 1);
        $result = $this->PostEventCount->save($likeEventArray);
    }

    public function removePostLikeEventCount($p_id, $user_id) {
        $result = $this->PostEventCount->deleteAll(array('user_id' => $user_id, 'post_id' => $p_id, "post_like_counts" => 1));
    }

    /** Added by Babulal Prasad at 12062016 * */
    public function postlike() {
        if ($this->request->is('post')) {
            $p_id = $this->request->data["p_id"];
            $like = $this->request->data["like"];
            $loggedinUser = $this->Auth->user();
            $user_id = $loggedinUser["id"];
            $likeresult = $this->PostLike->find('first', array(
                'conditions' => array('user_id' => $user_id, 'post_id' => $p_id),
                'fields' => array('id')
            ));
            $this->Post->unbindModel(array('hasMany' => array('PostAttachments')));
            $post_count = $this->Post->find("first", array("conditions" => array("id" => $p_id), "fields" => array("like_count", "user_id", "title", "organization_id")));

            $like_count = $post_count["Post"]["like_count"];
            $postedUserID = $post_count["Post"]["user_id"];
            $postTitle = $post_count["Post"]["title"];
            $orgId = $post_count["Post"]["organization_id"];

            $status = false;
            if ($like == 1) {

                /*                 * Increase count of postlike click event code here */
                $this->increasePostLikeEventCount($p_id, $user_id);
                /*
                 */

                if (empty($likeresult)) {
                    $likearray = array("post_id" => $p_id, "user_id" => $user_id);
                    $this->PostLike->save($likearray);
                    $this->Post->id = $p_id;
                    $like_count = $post_count["Post"]["like_count"] + 1;
                    $this->Post->id = $p_id;
                    $this->Post->savefield("like_count", $like_count);
                    $msg = "Wall Post liked successfully";
                    $status = true;
                    $sendPushNotification = 1;
                    $userDATA = $this->User->findById($user_id);
                    $userName = $userDATA['User']['fname'] . " " . $userDATA['User']['lname'];
                    $msg = "Your post titled : " . $postTitle . " \n\n liked by " . $userName;
                    /* Send Push notification */
//                    if (isset($sendPushNotification) && $sendPushNotification == 1) {
//
//                        $userDATA = $this->User->findById($user_id);
//                        $userName = $userDATA['User']['fname'] . " " . $userDATA['User']['lname'];
//                        $PostTitle = $postTitle;
//
//                        $loginuser = $this->LoginStatistics->find("all", array(
//                            "conditions" => array("user_id" => $postedUserID, "live" => 1)
//                        ));
//
//
//                        if (!empty($loginuser[0]["LoginStatistics"]) && $loginuser[0]["LoginStatistics"]["device_id"] != "") {
//
//
//                            if (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "ios") {
//                                //print_r($device_token);
//                                $deviceToken_msg_arr = array();
//                                $token = $loginuser[0]["LoginStatistics"]["device_id"];
//                                $count = 1;
//                                //$msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n".$val["EndorseReplies"]["reply"];
//                                $msg = "Your post titled : " . $PostTitle . " \n\n liked by " . $userName;
//                                $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion", "title" => "nDorse App");
//
//                                $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
////                                pr($deviceToken_msg_arr);
////                                exit;
//                                /** Added by Babulal Prasad @30-may-2019
//                                 * * SAVE DATA for alert center feature Start
//                                 * ** */
//                                $this->loadModel('AlertCenterNotification');
//                                $AlertCenterNotificationArray['user_id'] = $postedUserID;
//                                $AlertCenterNotificationArray['org_id'] = $orgId;
//                                $AlertCenterNotificationArray['alert_type'] = 'Post Like Notification';
//                                $AlertCenterNotificationArray['plain_msg'] = $msg;
//                                $AlertCenterNotificationArray['original_msg'] = $msg;
//                                $AlertCenterNotificationArray['status'] = 0;
//                                $AlertCenterNotificationArray['os'] = 'ios';
//                                $this->AlertCenterNotification->save($AlertCenterNotificationArray);
//                                /* SAVE DATA for alert center feature End** */
//                                $this->Common->sendPushNotification($deviceToken_msg_arr);
//                            } elseif (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "android") {
//
//                                $deviceToken_msg_arr = array();
//                                $token = $loginuser[0]["LoginStatistics"]["device_id"];
//                                $count = 1;
//                                // $end_name = $val['User']['fname'] . " " . $val['User']['lname'];
//                                $organization_id = $orgId;
//                                // $msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n<br />".$val["EndorseReplies"]["reply"];
//                                $msg = $userName . "Your post titled : " . $PostTitle . " \n\n liked by " . $userName . "";
//                                $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion",
//                                    "title" => "nDorse App");
//
//                                $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
//                                //print_r($deviceToken_msg_arr);
//                                $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
//
//                                /** Added by Babulal Prasad @30-may-2019
//                                 * * SAVE DATA for alert center feature Start
//                                 * ** */
//                                $this->loadModel('AlertCenterNotification');
//                                $AlertCenterNotificationArray['user_id'] = $postedUserID;
//                                $AlertCenterNotificationArray['org_id'] = $orgId;
//                                $AlertCenterNotificationArray['alert_type'] = 'Post Like Notification';
//                                $AlertCenterNotificationArray['plain_msg'] = $msg;
//                                $AlertCenterNotificationArray['original_msg'] = $msg;
//                                $AlertCenterNotificationArray['status'] = 0;
//                                $AlertCenterNotificationArray['os'] = 'android';
//                                $this->AlertCenterNotification->save($AlertCenterNotificationArray);
//                                /* SAVE DATA for alert center feature End** */
//                            }
//                        }
//                    }
                    $this->loadModel('AlertCenterNotification');
                    $AlertCenterNotificationArray['user_id'] = $postedUserID;
                    $AlertCenterNotificationArray['org_id'] = $orgId;
                    $AlertCenterNotificationArray['alert_type'] = 'Post Like Notification';
                    $AlertCenterNotificationArray['plain_msg'] = $msg;
                    $AlertCenterNotificationArray['original_msg'] = $msg;
                    $AlertCenterNotificationArray['status'] = 0;
                    $AlertCenterNotificationArray['os'] = '';
                    $AlertCenterNotificationArray['feed_id'] = $p_id;
                    $AlertCenterNotificationArray['feed_type'] = 'post';
                    $AlertCenterNotificationArray['sender_id'] = $user_id;
                    $this->AlertCenterNotification->save($AlertCenterNotificationArray);
                    /** END send notification code** */
                } else {
                    $msg = "you have already liked Post";
                    $status = true;
                }
            } else {

                if (!empty($likeresult) && $likeresult["PostLike"]["id"] > 0) {
                    /*                     * Increase count of postlike click event code here */
                    $this->removePostLikeEventCount($p_id, $user_id);
                    /*
                     */
//["EndorsementLike"]["id"]
                    $this->PostLike->delete($likeresult["PostLike"]["id"]);
                    $like_count = $post_count["Post"]["like_count"] - 1;
                    $this->Post->id = $p_id;
                    $this->Post->savefield("like_count", $like_count);
                    $msg = "Wall post disliked successfully";
                } else {
                    $msg = "you have already disliked wall post";
                }
                $status = true;
            }
            $total_like = 0;
            $this->set(array(
                'result' => array("status" => $status
                    , "msg" => $msg,
                    "data" => array("like_count" => $like_count)),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    /** Added by Babulal Prasad at 12162016 * */
    public function setDoNotRemindMe() {
        if ($this->request->is('post')) {
            $status = $this->request->data["status"];
            $loggedinUser = $this->Auth->user();
            $user_id = $loggedinUser["id"];
            $this->User->id = $user_id;
            $this->User->savefield("do_not_remind", $status);
            if ($status == 1) {
                $msg = "Reminder stopped successfully";
                $resStatus = true;
            } else {
                $msg = "Reminder activated successfully";
                $resStatus = true;
            }

            $this->set(array(
                'result' => array("status" => $resStatus
                    , "msg" => $msg,
                ),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    /** Added by Babulal Prasad at 18012019 * */
    public function setDoNotRemindCoreValue() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
                $token = $this->request->data['token'];
                $userinfo = $this->getuserData($token, true);
                if (!empty($userinfo)) {
                    $core_value_id = $this->request->data["core_value_id"];
                    $user_id = $userinfo['users']['id'];
                    $coreValueDetails = $this->OrgCoreValues->findById($core_value_id);
                    if (!empty($coreValueDetails)) {
                        $disabledUserList = $coreValueDetails['OrgCoreValues']['custom_message_disabled_user_id'];
                        if (isset($disabledUserList) && !empty($disabledUserList)) {

                            $disabledUserListDecoded = json_decode($disabledUserList);
                            if (!in_array($user_id, $disabledUserListDecoded)) {
                                $disabledUserListDecoded = array_merge(array($user_id), $disabledUserListDecoded);
                            }
                            $disabledUserList = json_encode($disabledUserListDecoded);
                        } else {
                            $disabledUserList = json_encode(array($user_id));
                        }
                    }
                    $this->OrgCoreValues->id = $core_value_id;
                    $result = $this->OrgCoreValues->savefield("custom_message_disabled_user_id", $disabledUserList);
                    if ($result) {
                        $msg = "Reminder stopped successfully";
                        $resStatus = true;
                    } else {
                        $msg = "Unable to stop reminder";
                        $resStatus = false;
                    }

                    $this->set(array(
                        'result' => array("status" => $resStatus
                            , "msg" => $msg, "id" => $core_value_id,
                        ),
                        '_serialize' => array('result')
                    ));
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Invalid token or expired."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function postdelete() {
        if ($this->request->is('post')) {
            $p_id = $this->request->data["p_id"];
            $loggedinUser = $this->Auth->user();
            $user_id = $loggedinUser["id"];
            $this->Post->id = $p_id;
            $deletePost = $this->Post->savefield("status", 0);
            $deleteFeedTrans = $this->FeedTran->updateAll(array('status' => 0), array('feed_id' => $p_id, 'feed_type' => 'post'));
            $this->loadModel('PostSchedule');

            $deletePostSchedule = $this->PostSchedule->query("Delete from post_schedules  where post_id = " . $p_id);

//            pr($deletePostSchedule); exit;
//$deletePost = $this->Post->delete($p_id);
//            $deletePostAttachment = $this->PostAttachment->deleteAll(['PostAttachment.post_id' => $p_id], false);
//            $deletePostLike = $this->PostLike->deleteAll(['PostLike.post_id' => $p_id], false);
            if ($deletePost) {
                $msg = "Wall Post deleted successfully";
                $status = true;
            } else {
                $msg = "Wall Post unable tp delete";
                $status = false;
            }
            $this->set(array(
                'result' => array("status" => $status
                    , "msg" => $msg,
                ),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function ndorsedelete() {
        if ($this->request->is('post')) {
            $e_id = $this->request->data["e_id"];
            $loggedinUser = $this->Auth->user();
            $user_id = $loggedinUser["id"];

            $this->Endorsement->id = $e_id;

//$deleteEndorsement = $this->Endorsement->savefield("status", 0);
            $deleteEndorsement = $this->Endorsement->deleteAll(array('id' => $e_id));

            $deleteFeedTrans = $this->FeedTran->updateAll(array('status' => 0), array('feed_id' => $e_id, 'feed_type' => 'endorse'));

            $resultEndorsementReply = $this->EndorseReply->deleteAll(array('endorsement_id' => $e_id));
            $resultEndorsementLike = $this->EndorsementLike->deleteAll(array('endorsement_id' => $e_id));
            $resultEndorseCoreValue = $this->EndorseCoreValue->deleteAll(array('endorsement_id' => $e_id));
            $resultEndorsementAttachment = $this->EndorseAttachment->deleteAll(array('endorsement_id' => $e_id));

// pr($deleteFeedTrans); exit;
//$deletePost = $this->Post->delete($p_id);
//            $deletePostAttachment = $this->PostAttachment->deleteAll(['PostAttachment.post_id' => $p_id], false);
//            $deletePostLike = $this->PostLike->deleteAll(['PostLike.post_id' => $p_id], false);
            if ($deleteEndorsement) {
                $msg = "Endorsement deleted successfully";
                $status = true;
            } else {
                $msg = "Endorsement unable tp delete";
                $status = false;
            }
            $this->set(array(
                'result' => array("status" => $status
                    , "msg" => $msg,
                ),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function publicndorseacceptcondition() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
                $loggedinUser = $this->Auth->user();
                $tc_step = $this->request->data["tc_step"];
                $user_id = $loggedinUser["id"];
                $current_org_id = $loggedinUser['current_org']['id'];
                $tcAccepted = $this->UserOrganization->updateAll(array('public_ndorse_visible_tc' => $tc_step), array('user_id' => $user_id, 'organization_id' => $current_org_id));
//$deletePost = $this->Post->delete($p_id);
//            $deletePostAttachment = $this->PostAttachment->deleteAll(['PostAttachment.post_id' => $p_id], false);
//            $deletePostLike = $this->PostLike->deleteAll(['PostLike.post_id' => $p_id], false);
                if ($tcAccepted == 1) {
                    $msg = "1st step t&c accept successfully";
                    $status = true;
                } else {
                    $msg = "1st step t&c acceptance failed";
                    $status = false;
                }
                $this->set(array(
                    'result' => array("status" => $status
                        , "msg" => $msg,
                    ),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function endorsereply() {
        if ($this->request->is('post')) {
            $e_id = $this->request->data["e_id"];
            $loggedinUser = $this->Auth->user();
            $user_id = $loggedinUser["id"];
            $reply = $this->request->data["reply"];
            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));

            $endorsment_details = $this->Endorsement->find("first", array("conditions" => array("id" => $e_id, "endorsement_for" => "user"), "fields" => array("endorsed_id")));

            $status = false;
            $msg = "";
            if (!empty($endorsment_details)) {

                $endorsereply = array("endorsement_id" => $e_id, "user_id" => $user_id, "reply" => $reply);
                $this->EndorseReplies->save($endorsereply);
                $this->Endorsement->id = $e_id;
                $this->Endorsement->savefield("is_reply", 1);
                $status = true;
                $msg = "nDorsement Reply submitted!";
                exec("wget -bqO- " . Router::url('/', true) . "/cron/replynotify &> /dev/null");
            } else {
                $msg = "You are not allowed for endorsment reply";
            }
            $this->set(array(
                'result' => array("status" => $status
                    , "msg" => $msg
                    , "data" => array("reply" => $reply)
                ),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function mySearchInOrganization() {
        if ($this->request->is('post')) {
            $resultData = array();
            $keyWord = $this->request->data['keyword'];
            $type = $this->request->data['type'];
            $loggedinUser = $this->Auth->user();
            $user_id = $loggedinUser["id"];
            if (!isset($loggedinUser['current_org'])) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You have not joined any organization yet. Please join."),
                    '_serialize' => array('result')
                ));
                return;
            }

            $statusConfig = Configure::read("statusConfig");

//												$startDate = date("Y-m-1 00:00:00");
//                                                $endDate = date("Y-m-t", strtotime($startDate)) . " 23:59:59";
            $endorsecondition = "";
            $endorser_id_name = "";
            if ($type == "endorser") {
                $endorsecondition = " AND Endorsement.endorsed_id = " . $user_id;
                $endorser_id_name = "endorser_id";
            } elseif ($type == "endorsed") {
                $endorsecondition = " AND Endorsement.endorser_id = " . $user_id;
                $endorser_id_name = "endorsed_id";
            }

//												

            $usersData = $this->UserOrganization->query("
																												SELECT  User.id, User.fname, User.lname,  UserOrganization.status, COUNT(Endorsement.id) as count FROM 
																												user_organizations AS UserOrganization
																												LEFT JOIN users AS User ON (UserOrganization.user_id = User.id) 
																												LEFT JOIN endorsements AS Endorsement ON (Endorsement." . $endorser_id_name . " = User.id ) 
																												WHERE ((User.fname LIKE '%" . $keyWord . "%') OR (User.lname LIKE '%" . $keyWord . "%'))
																												AND UserOrganization.status IN (" . $statusConfig['active'] . ", " . $statusConfig['eval'] . ")
																												AND UserOrganization.organization_id = " . $loggedinUser['current_org']['id'] .
                    $endorsecondition . "
																												AND (Endorsement.organization_id = " . $loggedinUser['current_org']['id'] . "  OR  Endorsement.organization_id IS NULL)
																												GROUP BY  User.id, Endorsement." . $endorser_id_name);
//echo $this->User->getLastQuery();die;

            $users = array();

            foreach ($usersData as $user) {
                if ($user['User']['id'] != $loggedInUser['id']) {
                    $userDetail = array();
                    $userDetail['id'] = $user['User']['id'];
                    $userDetail['name'] = $user['User']['fname'] . " " . $user['User']['lname'];
                    $userDetail['org_status'] = array_search($user['UserOrganization']['status'], $statusConfig);
                    $userDetail['endorse_count'] = $user[0]['count'];

                    $users[] = $userDetail;
                }
            }
            if ($type == "endorsed") {
                $departments = array();


                $departmentsData = $this->OrgDepartments->query("
																																				SELECT OrgDepartments.id, OrgDepartments.name, COUNT(Endorsement.id) as count
																																				FROM org_departments AS OrgDepartments
																																				LEFT JOIN endorsements AS Endorsement ON (Endorsement." . $endorser_id_name . " = OrgDepartments.id)
																																				WHERE OrgDepartments.name LIKE '%" . $keyWord . "%'
																																				AND OrgDepartments.status = " . $statusConfig['active'] . "
																																				AND OrgDepartments.organization_id = " . $loggedinUser['current_org']['id']
                        . $endorsecondition . "
																																				AND (Endorsement.organization_id = " . $loggedinUser['current_org']['id'] . "  OR  Endorsement.organization_id IS NULL)
																																				GROUP BY  OrgDepartments.id, Endorsement." . $endorser_id_name);

//echo $this->OrgDepartments->getLastQuery();die;
//
//pr($departmentsData);die;

                foreach ($departmentsData as $department) {
                    $departmentDetail = array();
                    $departmentDetail['id'] = $department['OrgDepartments']['id'];
                    $departmentDetail['name'] = $department['OrgDepartments']['name'];
                    $departmentDetail['endorse_count'] = $department[0]['count'];

                    $departments[] = $departmentDetail;
                }

                $entities = array();

                $entitiesData = $this->Entity->query("
																																SELECT Entity.id, Entity.name, COUNT(Endorsement.id) as count
																																FROM entities AS Entity LEFT JOIN
																																endorsements AS Endorsement ON (Endorsement." . $endorser_id_name . " = Entity.id)
																																WHERE Entity.name LIKE '%" . $keyWord . "%'
																																AND Entity.status = " . $statusConfig['active'] . "
																																AND Entity.organization_id = " . $loggedinUser['current_org']['id'] . $endorsecondition . "
																																AND (Endorsement.organization_id = " . $loggedinUser['current_org']['id'] . "  OR  Endorsement.organization_id IS NULL)
																																GROUP BY  Entity.id, Endorsement." . $endorser_id_name);

//echo $this->Entity->getLastQuery();die;

                foreach ($entitiesData as $entity) {
                    $entityDetail = array();
                    $entityDetail['id'] = $entity['Entity']['id'];
                    $entityDetail['name'] = $entity['Entity']['name'];
                    $entityDetail['endorse_count'] = $entity[0]['count'];

                    $entities[] = $entityDetail;
                }
            }
            $resultData['users'] = $users;
            if ($type == "endorsed") {
                $resultData['departments'] = $departments;
                $resultData['entities'] = $entities;
            }

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Search results", "data" => $resultData),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

// switch group
    public function switchGroup() {
        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $org_id = $this->request->data["org_id"];
            $loggedinUser = $this->Auth->user();

            $user_id = $loggedinUser["id"];
// check user exist this organization
// check user default organization if exist then deleted if other
// save default org this endoreser
//$joinedUser = $this->UserOrganization->find("count", array("conditions" => array("organization_id" => $org_id, "user_id"=>$user_id,"UserOrganization.status" => array( $statusConfig['active'], $statusConfig['eval']))));

            $params = array();
            $params['fields'] = "*";
            $params['joins'] = array(
                array(
                    'table' => 'default_orgs',
                    'alias' => 'DefaultOrg',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'UserOrganization.user_id = DefaultOrg.user_id',
                        'UserOrganization.organization_id = DefaultOrg.organization_id',
                        'DefaultOrg.status=1'
                    )
                )
            );

            $params['conditions'] = array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id);
            $params['order'] = 'UserOrganization.id desc';
            $Organization = $this->UserOrganization->find("first", $params);


            if (!empty($Organization)) {
//print_r($Organization["DefaultOrg"]);
                if (1) {
                    $joined = $Organization['UserOrganization']['joined'];
                    if ($joined == 0) {
                        if ($this->UserOrganization->updateAll(array("UserOrganization.joined" => 1), array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id))) {
                            $joined = "1";
                        }
                    }

                    $user_role = $Organization["UserOrganization"]["user_role"];

                    $subcenter_id = $Organization["UserOrganization"]["subcenter_id"];

                    $organization = $this->Organization->findById($org_id);

                    $roleList = $this->Common->setSessionRoles();

                    $currentOrg = $organization['Organization'];

                    $currentOrg['org_role'] = $roleList[$user_role];
                    $currentOrg['joined'] = $joined;
                    $currentOrg['subcenter_id'] = $subcenter_id;

                    if ($currentOrg["image"] != "") {
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $currentOrg["image"] = $rootUrl . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $currentOrg["image"];
                    }


                    $this->Session->write('Auth.User.current_org', $currentOrg);
                    $defaultorg_id = $this->DefaultOrg->findByUserId((int) $loggedinUser['id']);

                    if (!empty($defaultorg_id)) {
                        $deorg_id = $defaultorg_id["DefaultOrg"]["id"];
// $this->DefaultOrg->delete($deorg_id);
                        $defaultOrg = array("id" => $deorg_id, "organization_id" => $organization['Organization']['id'], "user_id" => $loggedinUser['id'], "status" => 1);
                    } else {
                        $defaultOrg = array("organization_id" => $organization['Organization']['id'], "user_id" => $loggedinUser['id']);
                    }

                    $this->DefaultOrg->save($defaultOrg);

                    $currentorg = array("Organization" => $currentOrg);
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Organization switched successfully."
                            , "data" => $currentorg),
                        '_serialize' => array('result')
                    ));
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "this group is current group also"),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You are not joined this group.Plz join this group"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function leaveGroup() {
        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $org_id = $this->request->data["org_id"];
            $loggedinUser = $this->Auth->user();

            $user_id = $loggedinUser["id"];

            if (!empty($org_id)) {
//print_r($Organization["DefaultOrg"]);
                if (1) {
//$status == 2 // Deleted
//                    echo "User ID : ".$user_id;
//                    echo "<br>Org ID : ".$org_id; exit;

                    $this->UserOrganization->updateAll(array("UserOrganization.status" => 2, "UserOrganization.joined" => 0,), array("UserOrganization.user_id" => $user_id, "UserOrganization.organization_id" => $org_id));


                    $data = array("status" => true);
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Organization leaved successfully."
                            , "data" => $data),
                        '_serialize' => array('result')
                    ));
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "this group is current group also"),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You are not joined this group.Plz join this group"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getjoinrequestUser() {
        if ($this->request->is('post')) {
            $org_id = $this->request->data["org_id"];
            $loggedinUser = $this->Auth->user();
            $user_id = $loggedinUser["id"];

            $params = array();
            $params['fields'] = "User.id,User.fname,User.lname,User.email,User.image,Organization.id as org_id,UserOrganization.user_id,"
                    . "OrgRequests.mobile_number,OrgRequests.relationship_to_org,OrgRequests.why_want_to_join,OrgRequests.relationship_to_org_desc";
            $params['joins'] = array(
                array(
                    'table' => 'org_requests',
                    'alias' => 'OrgRequests',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Organization.id = OrgRequests.organization_id',
                        'OrgRequests.status=0',
                    )
                ),
                array(
                    'table' => 'user_organizations',
                    'alias' => 'UserOrganization',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'UserOrganization.user_id = OrgRequests.user_id',
                        'UserOrganization.organization_id=' . $org_id,
                        'UserOrganization.status !=2'
                    )
                ),
                array(
                    'table' => 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'OrgRequests.user_id = User.id',
                        'User.status=1',
                    )
                ),
            );

            $params['conditions'] = array("Organization.id" => $org_id);

            $Organization = $this->Organization->find("all", $params);



//echo $this->Organization->getLastQuery();die;
            if (!empty($Organization)) {
//pr($Organization);exit;
                $OrgRequests = $Organization[0]["User"];

//print_r($Organization);exit;
                $userarray = array();
                if (!empty($OrgRequests) && $OrgRequests["id"] != "") {

                    foreach ($Organization as $org) {
//pr($Organization); exit;
                        if ($org["UserOrganization"]["user_id"] != $org["User"]["id"]) {
//  $userarray[] = $org["User"];
                            $img = "";
                            if ($org["User"]["image"] != "") {
                                $img = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $org["User"]["image"];
                            }
                            $userarray[] = array("id" => $org["User"]["id"], "name" => trim($org["User"]["fname"] . " " . $org["User"]["lname"]),
                                "email" => $org["User"]['email'],
                                "imag" => $img, "image" => $img, "mobile_number" => ($org["OrgRequests"]["mobile_number"] == null) ? '' : $org["OrgRequests"]["mobile_number"], "relationship_to_org" => ($org["OrgRequests"]["relationship_to_org"] == null) ? '' : $org["OrgRequests"]["relationship_to_org"],
                                "why_want_to_join" => ($org["OrgRequests"]["why_want_to_join"] == null) ? '' : $org["OrgRequests"]["why_want_to_join"], "relationship_to_org_desc" => ($org["OrgRequests"]["relationship_to_org_desc"] == null) ? '' : $org["OrgRequests"]["relationship_to_org_desc"]);
                        }
                    }

                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Requested users",
                            "data" => $userarray),
                        '_serialize' => array('result')
                    ));
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Currenty no user requested to join this organization."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You are not the owner of this organization."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function acceptorgrequest() {

        if ($this->request->is('post')) {
            $org_id = $this->request->data["org_id"];
            $loggedinUser = $this->Auth->user();
            $user_id = $loggedinUser["id"];

            $request_user_id = explode(",", $this->request->data["user_id"]);

            $statusConfig = Configure::read("statusConfig");

            $confirm = "yes";
            if (isset($this->request->data["confirm"]) && $this->request->data["confirm"] != "") {
                $confirm = $this->request->data["confirm"];
            }

            if ($confirm == "no") {
                foreach ($request_user_id as $val_id) {
                    $this->OrgRequests->deleteAll(
                            array('OrgRequests.organization_id' => $org_id, 'OrgRequests.user_id' => $val_id)
                    );
                }
                $this->set(array(
                    'result' => array("status" => true,
                        "msg" => "Request are deleted successfully", "data" => true, "confirm" => $confirm),
                    '_serialize' => array('result')
                ));
                return;
            } else {
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
                $request_count = count($request_user_id);
                $freeCount += $request_count;
                $addacountflag = 0;

                if ($freeCount > FREE_POOL_USER_COUNT) {
                    $poolType = "paid";

                    $params = array();
                    $conditions = array();
                    $todayDate = date('Y-m-d H:i:s');
//                    $conditions['start_date <='] = $todayDate;
//                    $conditions['end_date >='] = $todayDate;
                    $conditions['Subscription.status'] = 1;
                    $conditions['Subscription.organization_id'] = $org_id;
                    $params["conditions"] = $conditions;
                    $currentSubscription = $this->Subscription->find("first", $params);

                    $paidCount += $request_count;
                    $poolPurchased = 10;
                    if (!empty($currentSubscription)) {
                        $poolPurchased = $currentSubscription['Subscription']['pool_purchased'];

                        $purchaseflag = 0;
                        if ($paidCount >= $poolPurchased) {
                            $purchaseflag = 1;
                        }
                        /* if ($paidCount >= $poolPurchased) {
                          // $status = $statusConfig['invite_inactive'];
                          $allow_user_count = $poolPurchased - $paidCount;
                          $this->set(array(
                          'result' => array("status" => false
                          , "msg" => "You are allow " . $allow_user_count . " endorser.please select " . $allow_user_count . " endorser or please renew your subscription"),
                          '_serialize' => array('result')
                          ));
                          return;
                          //
                          } */ if (1) {

                            $addacountflag = 1;
                            $organizations = $this->Organization->find("all", array("conditions" => array("id" => $org_id)));

                            $organizations = $organizations[0]["Organization"];


                            $users = $this->User->find("all", array('joins' => array(
                                    array(
                                        'table' => 'default_orgs',
                                        'alias' => 'DefaultOrg',
                                        'type' => 'LEFT',
                                        'conditions' => array(
                                            'DefaultOrg.user_id = User.id'
                                        )
                                    )
                                ), "conditions" => array("User.id" => $request_user_id), "fields" => array("User.id", "email", "fname", "DefaultOrg.organization_id")));


                            $userArray = array();
                            foreach ($users as $uval) {
                                $userArray[$uval["User"]["id"]] = array("email" => $uval["User"]["email"], "default_org" => $uval["DefaultOrg"]["organization_id"]);
                            }


                            $poolType = "paid";
                            if ($purchaseflag == 1) {
                                $status = $statusConfig['inactive'];
                            } else {
                                $status = $statusConfig['active'];
                            }
                            $addacountflag = 1;

                            $requests = $emailQueue = array();
                            $viewVars = array("org_name" => $organizations['name']);
                            $configVars = serialize($viewVars);
                            $subject = "Congratulations. Your request to accepted for " . $organizations['name'];
//print_r($request_user_id);
                            foreach ($request_user_id as $val_id) {
//if (!in_array($val_id, $requests)) {
                                $userOrg = $this->UserOrganization->find("first", array("conditions" => array("user_id" => $val_id, "organization_id" => $org_id)));

                                if (!empty($userOrg)) {
                                    $requests[] = array("id" => $userOrg["UserOrganization"]["id"], "organization_id" => $org_id, "joined" => 1, "user_id" => $val_id, "user_role" => 3, "status" => $status, "pool_type" => "paid", "flow" => "request");
                                } else {
                                    $requests[] = array("organization_id" => $org_id, "joined" => 1, "user_id" => $val_id, "user_role" => 3, "status" => $status, "pool_type" => "paid", "flow" => "request");
                                }
                                if ($userArray[$val_id]["default_org"] == "") {
                                    $defaultOrg = array("organization_id" => $org_id, "user_id" => $val_id);
                                    $this->DefaultOrg->save($defaultOrg);
                                    $isDefault = true;
                                }
                                $emailQueue[] = array("to" => $userArray[$val_id]["email"], "subject" => $subject, "config_vars" => $configVars, "template" => "accept_request");
//}
                            }


                            if (!empty($requests)) {
//$this->OrgRequests->updateAll(
//        array('OrgRequests.status' => "'1'"), array('OrgRequests.organization_id' => $org_id, 'OrgRequests.user_id' => $request_user_id)
//);
                                $this->OrgRequests->deleteAll(
                                        array('OrgRequests.organization_id' => $org_id, 'OrgRequests.user_id' => $request_user_id)
                                );
                                $this->UserOrganization->saveMany($requests);
                                $this->Email->saveMany($emailQueue);
                                if ($purchaseflag == 1) {

                                    $msg = "Pending request(s) have been accepted and new users added to Organization. Purchase or upgrade subscription to activate added user(s) using Admin Portal on www.ndorse.net or by contacting NDORSE LLC at support@ndorse.net.";
                                } else {
                                    $msg = "Pending request(s) accepted successfully!";
                                }
                                $status = true;
                            }

                            $returnData = array("accepted_users" => $userArray);
                            if ($purchaseflag == 1) {

                                $msg = "Pending request(s) have been accepted and new users added to Organization. Purchase or upgrade subscription to activate added user(s) using Admin Portal on www.ndorse.net or by contacting NDORSE LLC at support@ndorse.net";
                            }

                            $this->set(array(
                                'result' => array("status" => true,
                                    "msg" => $msg, "data" => $returnData, "confirm" => $confirm),
                                '_serialize' => array('result')
                            ));
                        }
                    } else {
                        $addacountflag = 0;

                        $organizations = $this->Organization->find("all", array("conditions" => array("id" => $org_id)));
                        $organizations = $organizations[0]["Organization"];

                        $users = $this->User->find("all", array('joins' => array(
                                array(
                                    'table' => 'default_orgs',
                                    'alias' => 'DefaultOrg',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'DefaultOrg.user_id = User.id'
                                    )
                                )
                            ), "conditions" => array("User.id" => $request_user_id), "fields" => array("User.id", "email", "fname", "DefaultOrg.organization_id")));


                        $userArray = array();
                        foreach ($users as $uval) {
                            $userArray[$uval["User"]["id"]] = array("email" => $uval["User"]["email"], "default_org" => $uval["DefaultOrg"]["organization_id"]);
                        }


                        $poolType = "paid";
                        $status = $statusConfig['inactive'];
                        $addacountflag = 1;

                        $requests = $emailQueue = $updaterequest = array();
                        $viewVars = array("org_name" => $organizations['name']);
                        $configVars = serialize($viewVars);
                        $subject = "Congratulations. Your request to accepted for " . $organizations['name'];

                        foreach ($request_user_id as $val_id) {
                            if (!in_array($val_id, $requests)) {

//$requests[] = array("organization_id" => $org_id, "user_id" => $val_id, "user_role" => 3, "status" => 1, "pool_type" => "free","flow"=>"request");
//	$updaterequest
                                $userOrg = $this->UserOrganization->find("first", array("conditions" => array("user_id" => $val_id, "organization_id" => $org_id)));
                                if (!empty($userOrg)) {
                                    $requests[] = array("id" => $userOrg["UserOrganization"]["id"], "organization_id" => $org_id, "user_id" => $val_id, "user_role" => 3, "status" => 0, "pool_type" => "paid", "joined" => 1, "flow" => "request");
                                } else {
                                    $requests[] = array("organization_id" => $org_id, "user_id" => $val_id, "user_role" => 3, "status" => 0, "pool_type" => "paid", "joined" => 1, "flow" => "request");
                                }
                                if ($userArray[$val_id]["default_org"] == "") {
                                    $defaultOrg = array("organization_id" => $org_id, "user_id" => $val_id);
                                    $this->DefaultOrg->save($defaultOrg);
                                    $isDefault = true;
                                }
                                $emailQueue[] = array("to" => $userArray[$val_id]["email"], "subject" => $subject, "config_vars" => $configVars, "template" => "accept_request");
                            }
                        }


                        if (!empty($requests)) {
//$this->OrgRequests->updateAll(
//        array('OrgRequests.status' => "'1'"), array('OrgRequests.organization_id' => $org_id, 'OrgRequests.user_id' => $request_user_id)
//);
                            $this->OrgRequests->deleteAll(
                                    array('OrgRequests.organization_id' => $org_id, 'OrgRequests.user_id' => $request_user_id)
                            );
// echo $this->OrgRequests->getLastQuery();die;
                            $this->UserOrganization->saveMany($requests);
                            $this->Email->saveMany($emailQueue);
                            $msg = "Pending request(s) accepted successfully!";
                            $status = true;
                        }

                        $returnData = array("accepted_users" => $userArray);
                        $msg = "Pending request(s) have been accepted and new users added to Organization. Purchase or upgrade subscription to activate added user(s) using Admin Portal on www.ndorse.net or by contacting NDORSE LLC at support@ndorse.net";
                        $this->set(array(
                            'result' => array("status" => $status,
                                "msg" => $msg, "data" => $returnData),
                            '_serialize' => array('result')
                        ));
                        return;
//
                    }
                } else {

                    $organizations = $this->Organization->find("all", array("conditions" => array("id" => $org_id)));
                    $organizations = $organizations[0]["Organization"];

                    $users = $this->User->find("all", array('joins' => array(
                            array(
                                'table' => 'default_orgs',
                                'alias' => 'DefaultOrg',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'DefaultOrg.user_id = User.id'
                                )
                            )
                        ), "conditions" => array("User.id" => $request_user_id), "fields" => array("User.id", "email", "fname", "DefaultOrg.organization_id")));


                    $userArray = array();
                    foreach ($users as $uval) {
                        $userArray[$uval["User"]["id"]] = array("email" => $uval["User"]["email"], "default_org" => $uval["DefaultOrg"]["organization_id"]);
                    }


                    $poolType = "free";
                    $status = $statusConfig['active'];
                    $addacountflag = 1;

                    $requests = $emailQueue = $updaterequest = array();
                    $viewVars = array("org_name" => $organizations['name']);
                    $configVars = serialize($viewVars);
                    $subject = "Congratulations. Your request to accepted for " . $organizations['name'];

                    foreach ($request_user_id as $val_id) {
                        if (!in_array($val_id, $requests)) {

//$requests[] = array("organization_id" => $org_id, "user_id" => $val_id, "user_role" => 3, "status" => 1, "pool_type" => "free","flow"=>"request");
//	$updaterequest
                            $userOrg = $this->UserOrganization->find("first", array("conditions" => array("user_id" => $val_id, "organization_id" => $org_id)));
                            if (!empty($userOrg)) {
                                $requests[] = array("id" => $userOrg["UserOrganization"]["id"], "organization_id" => $org_id, "user_id" => $val_id, "user_role" => 3, "status" => 1, "pool_type" => "free", "joined" => 1, "flow" => "request");
                            } else {
                                $requests[] = array("organization_id" => $org_id, "user_id" => $val_id, "user_role" => 3, "status" => 1, "pool_type" => "free", "joined" => 1, "flow" => "request");
                            }
                            if ($userArray[$val_id]["default_org"] == "") {
                                $defaultOrg = array("organization_id" => $org_id, "user_id" => $val_id);
                                $this->DefaultOrg->save($defaultOrg);
                                $isDefault = true;
                            }
                            $emailQueue[] = array("to" => $userArray[$val_id]["email"], "subject" => $subject, "config_vars" => $configVars, "template" => "accept_request");
                        }
                    }


                    if (!empty($requests)) {
//$this->OrgRequests->updateAll(
//        array('OrgRequests.status' => "'1'"), array('OrgRequests.organization_id' => $org_id, 'OrgRequests.user_id' => $request_user_id)
//);
                        $this->OrgRequests->deleteAll(
                                array('OrgRequests.organization_id' => $org_id, 'OrgRequests.user_id' => $request_user_id)
                        );
// echo $this->OrgRequests->getLastQuery();die;
                        $this->UserOrganization->saveMany($requests);
                        $this->Email->saveMany($emailQueue);
                        $msg = "Pending request(s) accepted successfully!";
                        $status = true;
                    }

                    $returnData = array("accepted_users" => $userArray);


                    $this->set(array(
                        'result' => array("status" => $status,
                            "msg" => $msg, "data" => $returnData),
                        '_serialize' => array('result')
                    ));
                }
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function endorsestats() {
        if ($this->request->is('post')) {
            $endorse_stats = array();
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
            $user_id = $loggedInUser['id'];
            if (isset($loggedInUser['current_org'])) {
//print_r($loggedInUser['current_org']);
                $org_id = $loggedInUser['current_org']['id'];
                $start_date = "";
                $end_date = "";
                if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                    $start_date = $this->request->data["start_date"];
                }
                if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                    $end_date = $this->request->data["end_date"];
                }
                $params = array();
                $params['fields'] = "count(*) as cnt";
                $conditionarray["Endorsement.organization_id"] = $org_id;
                $conditionarray["Endorsement.status"] = '1';
                $conditionarray["Endorsement.endorser_id"] = $user_id;
//$conditionarray["Endorsement.endorsement_for"] = 'user';
                if ($start_date != "") {

                    $conditionarray["Endorsement.created >= "] = date("Y-m-d 00:00:00", $start_date);
                }
                if ($end_date != "") {
                    $conditionarray["Endorsement.created <= "] = date("Y-m-d 23:59:59", $end_date);
                }

                $params['conditions'] = $conditionarray;
                $params['order'] = 'Endorsement.created desc';
// $params['group'] = 'Endorsement.endorsed_id';

                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $totalendorsement = $this->Endorsement->find("all", $params);
// print_r($totalendorsement);
//echo $this->Endorsement->getLastQuery();die;
                if (!empty($totalendorsement)) {
                    $endorse_stats["endorse_given"] = (string) $totalendorsement[0][0]["cnt"];
                } else {
                    $endorse_stats["endorse_given"] = (string) 0;
                }

                unset($conditionarray["Endorsement.endorser_id"]);
                unset($conditionarray["Endorsement.endorsement_for"]);
// unset($params['group']);
                unset($params['order']);

                $conditionarray["Endorsement.endorsed_id"] = $user_id;
                $conditionarray["Endorsement.status"] = '1';
                $params['conditions'] = $conditionarray;
                $totalendorsement = $this->Endorsement->find("all", $params);
                $endorse_stats["endorse_received"] = $totalendorsement[0][0]["cnt"];
                $params['fields'] = "count(EndorseCoreValue.value_id) as total, OrgCoreValues.name as core_value ";
                $params['joins'] = array(
                    array(
                        'table' => 'endorse_core_values',
                        'alias' => 'EndorseCoreValue',
                        'type' => 'INNER',
                        'conditions' => array(
                            'EndorseCoreValue.endorsement_id =Endorsement.id '
                        )
                    ),
                    array(
                        'table' => 'org_core_values',
                        'alias' => 'OrgCoreValues',
                        'type' => 'INNER',
                        'conditions' => array(
                            'OrgCoreValues.id =EndorseCoreValue.value_id '
                        )
                    )
                );
                $params['group'] = 'EndorseCoreValue.value_id';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $corevalues = $this->Endorsement->find("all", $params);
                $core_values = array();
                if (!empty($corevalues)) {
                    foreach ($corevalues as $cval) {
                        if ($cval["OrgCoreValues"]["core_value"] != "") {
                            $core_values[] = array("name" => $cval["OrgCoreValues"]["core_value"], "value" => $cval[0]["total"]);
                        }
                    }
                }
                $endorse_stats["core_value"] = $core_values;


                $this->Badge->unbindModel(array('belongsTo' => array('Trophy')));

                $params = array();
                $params['fields'] = array("*");
//$params['conditions'] = array("user_id" => $user_id, "organization_id" => $org_id);
                $params['joins'] = array(
                    array(
                        'table' => 'trophies',
                        'alias' => 'Trophy',
                        'type' => 'RIGHT',
                        'conditions' => array(
                            'Badge.trophy_id = Trophy.id',
                            'Badge.user_id = ' . $user_id,
                            'Badge.organization_id = ' . $org_id,
                        )
                    ),
                );
                $badges = $this->Badge->find("all", $params);
//echo $this->Badge->getLastQuery();
//pr($badges);die;
                $userBadges = array();

                foreach ($badges as $badge) {
                    $badgeInfo = array();
//$badgeInfo['badge_id'] = $badge['Badge']['id'];
                    $badgeInfo['trophy_id'] = $badge['Trophy']['id'];
                    $badgeInfo['count'] = empty($badge['Badge']['count']) ? 0 : (int) $badge['Badge']['count'];
                    $badgeInfo['image'] = Router::url('/', true) . TROPHY_IMAGE_DIR . $badge['Trophy']['image'];

                    $userBadges[] = $badgeInfo;
                }

                $endorse_stats["badges"] = $userBadges;

                $endorsedCount = $this->Endorsement->find("count", array("conditions" => array("Endorsement.endorsed_id" => $user_id, "organization_id" => $org_id, "endorsement_for" => "user", "Endorsement.status" => 1)));
                $endorserCount = $this->Endorsement->find("count", array("conditions" => array("Endorsement.endorser_id" => $user_id, "organization_id" => $org_id, "Endorsement.status" => 1)));
                $endorse_stats['endorse_count'] = array('giving' => $endorserCount, "getting" => $endorsedCount);

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Stats data"
                        , "data" => $endorse_stats),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Please join some organization."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

// get leaderboard
    public function leaderboard() {
        if ($this->request->is('post')) {
            $endorse_stats = array();
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
            $user_id = $loggedInUser['id'];
            $type = $this->request->data["type"];
            $endorse_field = "endorsed_id";
            if ($type == "endorser") {
                $endorse_field = "endorser_id";
            }
            if (isset($loggedInUser['current_org'])) {
//print_r($loggedInUser['current_org']);
                $org_id = $loggedInUser['current_org']['id'];
                $start_date = "";
                $end_date = "";
                if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                    $start_date = $this->request->data["start_date"];
                }
                if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                    $end_date = $this->request->data["end_date"];
                }
                $params = array();

                $params['fields'] = "count(Endorsement." . $endorse_field . ") as cnt,User.fname ,User.lname,OrgDepartments.name as department";
                $conditionarray["Endorsement.organization_id"] = $org_id;
//$conditionarray["Endorsement.endorsement_for"]= 'user';
                if ($start_date != "") {
                    $conditionarray["Endorsement.created >= "] = date("Y-m-d 00:00:00", $start_date);
                }
                if ($end_date != "") {
                    $conditionarray["Endorsement.created <= "] = date("Y-m-d 23:59:59", $end_date);
                }
                if ($type == "endorsed") {
                    $conditionarray["Endorsement.endorsement_for"] = "user";
                }
                $params['conditions'] = $conditionarray;
                $params['joins'] = array(
                    array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'INNER',
                        'conditions' => array(
                            'User.id =Endorsement.' . $endorse_field
                        )
                    ),
                    array(
                        'table' => 'user_organizations',
                        'alias' => 'UserOrganization',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'UserOrganization.user_id =User.id',
                            'UserOrganization.organization_id =' . $org_id
                        )
                    ),
                    array(
                        'table' => 'org_departments',
                        'alias' => 'OrgDepartments',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'OrgDepartments.id =UserOrganization.department_id'
                        )
                    )
                );
                $params['order'] = 'cnt desc';

                $params['group'] = 'Endorsement.' . $endorse_field;
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $leaderboard = $this->Endorsement->find("all", $params);
//echo $this->Endorsement->getLastQuery();exit;
//	echo $this->Organization->getLastQuery();die;

                $leaderboardArr = array();
                $arraydata = array();
                foreach ($leaderboard as $lvalue) {
                    $deptval = $lvalue["OrgDepartments"]["department"];
                    if ($lvalue["OrgDepartments"]["department"] == null) {
                        $deptval = "";
                    }
                    $tot = (string) $lvalue[0]["cnt"];
                    $leaderboardArr[] = array("name" => $lvalue["User"]["fname"] . " " . $lvalue["User"]["lname"], "department" => $deptval, "Total" => $tot);
                }
                $arraydata[] = array("title" => "Employee", "list" => $leaderboardArr);
                if ($type == "endorsed") {
                    if ($start_date != "") {
                        $startDate = date("Y-m-d 00:00:00", $start_date);
                    }
                    if ($end_date != "") {
                        $endDate = date("Y-m-d 23:59:59", $end_date);
                    }
                    $departmentsql = "SELECT OrgDepartments.id, OrgDepartments.name, COUNT(Endorsement.id) as count FROM  endorsements AS Endorsement
                                        INNER JOIN  org_departments AS OrgDepartments ON (Endorsement.endorsed_id = OrgDepartments.id)
										WHERE  OrgDepartments.status = " . $statusConfig['active'] . "
										AND OrgDepartments.organization_id = " . $org_id . "
									   AND (Endorsement.organization_id = " . $org_id . "  OR  Endorsement.organization_id IS NULL)";
                    if ($start_date != "" && $end_date != "") {

                        $departmentsql .= "AND (Endorsement.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
                    } elseif ($start_date != "") {
                        $departmentsql .= "AND (Endorsement.created >= '" . $startDate . "' )";
                    } elseif ($end_date != "") {
                        $departmentsql .= "AND (Endorsement.created <= '" . $endDate . "' )";
                    }
                    $departmentsql .= "GROUP BY  OrgDepartments.id, Endorsement.endorsed_id";

                    $departmentsData = $this->OrgDepartments->query($departmentsql);
                    if (!empty($departmentsData)) {
// print_r($departmentsData);
                        $leaderboardArr = array();
                        foreach ($departmentsData as $department) {
//$departmentDetail = array();
//$departmentDetail['id'] = $department['OrgDepartments']['id'];
//$departmentDetail['name'] = $department['OrgDepartments']['name'];
//$departmentDetail['endorse_count'] = $department[0]['count'];
                            $tot = (string) $department[0]['count'];
                            $leaderboardArr[] = array("name" => $department['OrgDepartments']['name'], "department" => "", "Total" => $tot);
// $departments[] = $departmentDetail;
                        }
                        $arraydata[] = array("title" => "Department", "list" => $leaderboardArr);
                    }
                    $entities = array();
                    $entitysql = "SELECT Entity.id, Entity.name, COUNT(Endorsement.id) as count
            FROM endorsements AS Endorsement
            INNER JOIN entities AS Entity ON (Endorsement.endorsed_id = Entity.id)
			WHERE  Entity.status = " . $statusConfig['active'] . "
			AND Entity.organization_id = " . $org_id . "
			AND (Endorsement.organization_id = " . $org_id . "  OR  Endorsement.organization_id IS NULL)";

                    if ($start_date != "" && $end_date != "") {

                        $entitysql .= "AND (Endorsement.created BETWEEN '" . $startDate . "' AND '" . $endDate . "')";
                    } elseif ($start_date != "") {
                        $entitysql .= "AND (Endorsement.created >= '" . $startDate . "' )";
                    } elseif ($end_date != "") {
                        $entitysql .= "AND (Endorsement.created <= '" . $endDate . "' )";
                    }

                    $entitysql .= "GROUP BY  Entity.id, Endorsement.endorsed_id";

                    $entitiesData = $this->Entity->query($entitysql);

//echo $this->Entity->getLastQuery();die;
//print_r($entitiesData);
                    $leaderboardArr = array();
                    if (!empty($entitiesData)) {
                        foreach ($entitiesData as $entity) {
//$entityDetail = array();
//$entityDetail['id'] = $entity['Entity']['id'];
//$entityDetail['name'] = $entity['Entity']['name'];
//$entityDetail['endorse_count'] = $entity[0]['count'];
//  $entities[] = $entityDetail;
                            $tot = (string) $entity[0]['count'];
                            $leaderboardArr[] = array("name" => $entity['Entity']['name'], "entity" => "", "Total" => $tot);
                        }
                        $arraydata[] = array("title" => "Sub Organization", "list" => $leaderboardArr);
                    }
                }

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Stats data"
                        , "data" => $arraydata),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "You current organization is not set or may be inactive."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    //Modified by Babulal Prasad @24-oct-2018
    public function forgotPassword() {
        if ($this->request->is('post')) {
            if (!filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid email address. Please check."),
                    '_serialize' => array('result')
                ));
                return;
            }

            $userData = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['email'])));

            if (empty($userData)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "This email is not registered.", 'isRegistered' => false),
                    '_serialize' => array('result')
                ));
                return;
            }

            $secretCode = $this->getForgotSecretCode();
            //pr($userData);
            $userID = $userData['User']['id'];

            $endcodedID = base64_encode($userID);
            $endcodedCODE = base64_encode($secretCode);
            $link = $endcodedID . "@$@" . $endcodedCODE;

//            $resetPasswordURL = Router::url('/', true) . "client/resetpassword/" . $link;
//
//            if (strpos($resetPasswordURL, 'localhost') < 0) {
//                $resetPasswordURL = str_replace("http", "https", $resetPasswordURL);
//            }



            $data = array();
            $data['email'] = $this->request->data['email'];
            $data['code'] = $endcodedCODE;
            $data['user_id_encpt'] = $endcodedID;

            //$checkOldEntry = $this->PasswordCode->find('all', array('conditions' => array('email' => $data['email'], 'email_sent' => 0)));

            $this->PasswordCode->updateAll(array('expired' => 1), array('email' => $data['email']));

            $this->PasswordCode->create();
            $saved = $this->PasswordCode->save($data);
            if ($saved) {
                //pr($saved); exit;
                //                echo "<br>" . $resetPasswordURL;
                //                exit;
                //$subject = "Forgot Password";
                //$template = "forgot_password";
                //$viewVars = array("verification_code" => $secretCode);
                //$this->Common->sendEmail($this->request->data['email'], $subject, $template, $viewVars);
                $url = Router::url('/', true) . "/cron/forgotPasswordEmails &> /dev/null";

                if (strpos($url, 'localhost') < 0 || strpos($url, 'staging') < 0) {
                    $url = str_replace("http", "https", $url);
                }

//                echo $url; exit;
                exec("wget -bqO- " . Router::url('/', true) . "/cron/forgotPasswordEmails &> /dev/null");
//                exit;
//                exec("nohup wget " . Router::url('/', true) . "/cron/forgotPasswordEmails > /dev/null 2>&1 &");
//                exec( "php ".WWW_ROOT."cron_scheduler.php /cron/forgotPasswordEmails/ > /dev/null &");
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Email has been sent with instructions to reset password."),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Unable to send verification code. Please try again or contact us at support@ndorse.net."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function forgotPassword_24_Oct_2018() {
        if ($this->request->is('post')) {
            if (!filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid email address. Please check."),
                    '_serialize' => array('result')
                ));
                return;
            }

            $userData = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['email'])));

            if (empty($userData)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "This email is not registered.", 'isRegistered' => false),
                    '_serialize' => array('result')
                ));
                return;
            }

            $secretCode = $this->getForgotSecretCode();

            $data = array();
            $data['email'] = $this->request->data['email'];
            $data['code'] = $secretCode;
            if ($this->PasswordCode->save($data)) {
//$subject = "Forgot Password";
//$template = "forgot_password";
//$viewVars = array("verification_code" => $secretCode);
//$this->Common->sendEmail($this->request->data['email'], $subject, $template, $viewVars);

                exec("wget -bqO- " . Router::url('/', true) . "/cron/forgotPasswordEmails &> /dev/null");
//                exec("nohup wget " . Router::url('/', true) . "/cron/forgotPasswordEmails > /dev/null 2>&1 &");
//                exec( "php ".WWW_ROOT."cron_scheduler.php /cron/forgotPasswordEmails/ > /dev/null &");
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "Email has been sent with instructions to reset password."),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Unable to send verification code. Please try again or contact us at support@ndorse.net."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function resetPassword() {
        if ($this->request->is('post')) {
            $passCode = $this->PasswordCode->find("first", array("conditions" => array("code" => $this->request->data['verification_code'])));
//            $passCode = $this->PasswordCode->find("first", array("conditions" => array("code" => $this->request->data['verification_code'], "email" => $this->request->data['email'])));

            if (empty($passCode)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Verification code did not match. Please re-try!"),
                    '_serialize' => array('result')
                ));
                return;
            } else if ($passCode['PasswordCode']['status'] != 0) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Verification code has already been used. Go to reset password."),
                    '_serialize' => array('result')
                ));
                return;
            }

            $this->User->set($this->request->data);
            $this->User->setValidation('reset_password');

            if ($this->User->validates()) {
                if ($this->User->updateAll(array("password" => "'" . $this->User->getHashPassword($this->request->data['password']) . "'"), array("email" => $passCode['PasswordCode']['email']))) {
                    $this->PasswordCode->id = $passCode['PasswordCode']['id'];
                    $this->PasswordCode->saveField("status", 1);

                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Password reset successfully!"),
                        '_serialize' => array('result')
                    ));
                } else {
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Unable to reset password. Please try again or contact support@ndorse.net."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $errors = $this->User->validationErrors;
                $errorsArray = array();

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Errors!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    function sendPushNotification($deviceToken_msg_arr = null) {
        $this->layout = 'ajax';
        $this->autoRender = false;
        //$deviceToken_msg_arr[0]['token'] = '34e4b5d373818b24ee36dfe6f9f511f5d684942eedfdfefde680b0e7a46a9663';
        $deviceToken_msg_arr[0]['token'] = 'b77e3d224cc8ea37ed6c58d5019c900e496919bd11ce66f6ba34cf8b25c72d5b';
        $deviceToken_msg_arr[0]['count'] = '1';

        if (!empty($deviceToken_msg_arr)) {
// Put your private key's passphrase here:
//            $location = dirname(__FILE__) . '/' . 'pushcert_30102019.pem';
//            $location = dirname(__FILE__) . '/' . 'pushcert_24sept20.pem';
            $location = dirname(__FILE__) . '/' . 'pushcert_29sept2020.pem';
//            echo $location; exit;
//            echo $location; exit;
            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $location);

//stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
// Open a connection to the APNS server
////gateway.sandbox.push.apple.com
            $fp = stream_socket_client(
//                    'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx); //Production
                    'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx); //Dev env

            if (!$fp) {
                exit("Failed to connect: $err $errstr" . PHP_EOL);
            } else {
                echo 'Connected to APNS' . PHP_EOL;
            }

            foreach ($deviceToken_msg_arr as $key => $val) {

                if (!empty($val['token']) && !empty($val['count']) && strlen($val['token']) > 10) {
                    $deviceToken = $val['token'];

                    $message = 'Hey Congrats!. You got a push notification.';
//$val['count']
                    $abc = (int) trim($val['count']);

                    $body['aps'] = array(
                        'alert' => $message,
                        'sound' => 'default',
                        'badge' => $abc,
                            //'content-available' => 1
                    );

// Encode the payload as JSON
                    $payload = json_encode($body);
//                    pr($payload);
//                    exit;
// Build the binary notification
                    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
//                    echo $msg;
// Send it to the server
                    $result = fwrite($fp, $msg, strlen($msg));
//                    print_r($result);
//                    exit;

                    if (!$result) {
// error not send notification
                        echo 'message send error';
                    } else {
                        pr($result);
                    }
                }
//usleep(250000);
            }

// Close the connection to the server
            fclose($fp);

            return true;
        } else {
            return false;
        }

        return true;

        die('Done');
    }

    function sendPushNotificationAndroid($deviceToken_msg_arr1 = null) {
        $this->layout = 'ajax';
        $this->autoRender = false;
        //// $deviceToken_msg_arr1[0]['token'] = 'fSaXOtQPAw8:APA91bEtIiI5vr0OtDC5ys2cAIOgBJmfVpqc9_8NWCvQqcv73woRTObrlEgCLkrfVXjcmyR8-JZrjVLoC6DruwM77uUnqSmJ1B7pnMBUJnx_Nn-HclNQGZwZPjb1Ai7trAaluLb20c2r';
        // $deviceToken_msg_arr1[0]['msg'] = 'testing local';
        // $deviceToken_msg_arr1[0]['data'] = 'testing local';
        // if (!empty($deviceToken_msg_arr1)) {
//            print_r($deviceToken_msg_arr1);
        // foreach ($deviceToken_msg_arr1 as $deviceToken_msg_arr) {
//                $registrationIds = array($deviceToken_msg_arr["token"]);
        $registrationIds = array('fSaXOtQPAw8:APA91bEtIiI5vr0OtDC5ys2cAIOgBJmfVpqc9_8NWCvQqcv73woRTObrlEgCLkrfVXjcmyR8-JZrjVLoC6DruwM77uUnqSmJ1B7pnMBUJnx_Nn-HclNQGZwZPjb1Ai7trAaluLb20c2r');

        $API_ACCESS_KEY_GOOGLE = Configure::read("API_ACCESS_KEY_GOOGLE");
// prep the bundle
        $msgtext = 'testing local';
        //$orgarray = 'testing local';
//                $username = 'test username';
//                if (isset($deviceToken_msg_arr["username"]) && $deviceToken_msg_arr["username"] != '') {
//                    $username = $deviceToken_msg_arr["username"];
//                }
        $username = 'test username';
        //$orgarray = $deviceToken_msg_arr["data"];
        $data = array();
        $data["message"] = $msgtext;

        $payload['title'] = 'test';
        $payload['message'] = 'test';
        $payload['body'] = $msgtext;
        $payload['badge'] = 1;


//                pr($data);
//                exit;
//$msg = array
//(
//	'message' 	=> $msgtext,
//	'title'		=> 'This is a title. title',
//	'subtitle'	=> 'This is a subtitle. subtitle',
//	'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',
//	
//	'largeIcon'	=> 'large_icon',
//	'smallIcon'	=> 'small_icon'
//);
        $fields = array
            (
            'registration_ids' => $registrationIds,
            'data' => $payload,
            'notification' => $payload,
            "icon" => "workmobappicon", // "firebase-logo.png"
            "sound" => "notification.wav",
//                    'to' => $registrationIds
        );
        //json_encode($fields);


        $headers = array
            (
            'Authorization: key=' . $API_ACCESS_KEY_GOOGLE,
            'Content-Type: application/json'
        );
        $ch = curl_init();
//                curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        echo $result;
        $res = json_decode($result);
        pr($res);
        if ($res->success == 1) {
//                    echo $res->success;
            return true;
        } else {
            return false;
        }
        // }
        // }

        die('Done');
    }

    public function termsConditions() {
//$termsConditions = "<h1>Terms & Conditions</h1><p><strong>nDorse Terms and Conditions</strong></p><p>Terms and Condition1</p><p>Terms and Condition2</p><p>Terms and Condition3</p>";
        $termstext = $this->GlobalSetting->findByKey("tandc");
        if (!empty($termstext)) {
            $termstext = $termstext['GlobalSetting']['value'];
        }

        if (isset($this->request->query["is_web"])) {
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Terms & Conditionss", "data" => $termstext),
                '_serialize' => array('result')
            ));
        } else {
            $view = new View($this, false);
            $view->set('style', 'style="padding-left:20px;padding-right:20px;padding-top:10px;');
            $view->viewPath = 'Elements';

            $view->set('terms', $termstext);
            /* Grab output into variable without the view actually outputting! */
            $view_output = $view->render('termsandcondition');
//echo $view_output;exit;
            $view_output = str_replace("\r", "", $view_output);
            $view_output = str_replace("\t", "", $view_output);
            $view_output = str_replace("\n", "", $view_output);
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Terms & Conditions", "data" => $view_output),
                '_serialize' => array('result')
            ));
        }

//$this->set(array(
//    'result' => array("status" => true
//        , "msg" => "Terms & Conditions", "data" => $termstext),
//    '_serialize' => array('result')
//));
    }

    public function checkADFSLoginSession() {
        $this->autoRender = false;
        $this->layout = false;
//        pr($this->request->data); 
        $data = array();
        $apiurl = $this->request->data['sso_url'];
//        exit;
//        $apiurl = 'https://sso.ndorse.net/simplesaml/module.php/core/authenticate.php?as=ndorse-sp';
        $this->log($apiurl, 'debug');
        $headers[] = "Accept: */*";
        $headers[] = "Connection: Keep-Alive";
        $headers[] = "Content-type: application/x-www-form-urlencoded;charset=UTF-8";
        $headers[] = "Cookie: " . $this->Apicalls->getToken();
        $cSession = curl_init();
        curl_setopt($cSession, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($cSession, CURLOPT_COOKIESESSION, TRUE);
        curl_setopt($cSession, CURLOPT_URL, $apiurl);
        curl_setopt($cSession, CURLOPT_POST, true);
        curl_setopt($cSession, CURLOPT_POSTFIELDS, http_build_query($data));
        ob_start();
        curl_exec($cSession);
        $result = ob_get_contents();
    }

    public function getTimelyUpdates() {
        if ($this->request->is('post')) {

            /* Added by Babulal Prasad at 25-Sept-2019
             * Check for ADFS Session  */

            /*             * */


            $loggedInUser = $this->Auth->user();
//            pr($loggedInUser); exit;
            $returnData = array();
            $msg = "";
            $liveUpdatedCount = $ndorsedUpdatedCount = $postUpdatedCount = 0;
            $statusConfig = Configure::read("statusConfig");
            $roleList = $this->Common->setSessionRoles();
            $userRoleChanged = false;
            $userRole = "";
            $userOrgStatusUpdated = false;

//Get feeds update
            if (isset($loggedInUser['current_org'])) {
                $userOrg = $this->UserOrganization->find("first", array("conditions" => array("user_id" => $loggedInUser['id'], "organization_id" => $loggedInUser['current_org']['id']), 'order' => 'UserOrganization.id desc'));

                if ($userOrg['Organization']['status'] == $statusConfig['active'] && $userOrg['UserOrganization']['status'] == $statusConfig['active']) {

                    $liveUpdated = $userOrg['UserOrganization']['live_updated'];
                    $ndorsedUpdated = $userOrg['UserOrganization']['ndorsed_updated'];
                    $postUpdated = $userOrg['UserOrganization']['post_updated'];

                    if ($liveUpdated != "0000-00-00 00:00:00") {
                        $liveUpdatedCount = $this->Endorsement->find("count", array("conditions" => array(
                                "organization_id" => $loggedInUser['current_org']['id'],
                                "type !=" => "private",
                                "status !=" => 0,
                                "created > " => $liveUpdated
                        )));
                    } else {
                        $liveUpdatedCount = 0;
                    }

                    if ($ndorsedUpdated != "0000-00-00 00:00:00") {
                        $ndorsedUpdatedCount = $this->Endorsement->find("count", array("conditions" => array(
                                "organization_id" => $loggedInUser['current_org']['id'],
                                "created > " => $ndorsedUpdated,
                                "status !=" => 0,
                                "endorsed_id " => $loggedInUser['id']
                        )));
//                        echo $postUpdated;exit;
//                         $sqllog = $this->Endorsement->getDataSource()->getLog(false, false);       
//  debug($sqllog);
                    } else {
                        $ndorsedUpdatedCount = 0;
                    }

                    /** by Babulal prasad @21=-022017 Add to get Post live feed notification *** */
                    if ($postUpdated != "0000-00-00 00:00:00") {
                        $postUpdatedCount = $this->Post->find("count", array("conditions" => array(
                                "organization_id" => $loggedInUser['current_org']['id'],
                                "created > " => $liveUpdated,
                                "status" => '1'//only posted and active post
                        )));
//                         $sqllog = $this->Endorsement->getDataSource()->getLog(false, false);       
//  debug($sqllog);
                    } else {
                        $postUpdatedCount = 0;
                    }

                    $liveUpdatedCount = (int) $liveUpdatedCount + (int) $postUpdatedCount;

                    if ($liveUpdated == "0000-00-00 00:00:00" || $ndorsedUpdated == "0000-00-00 00:00:00" || $postUpdated == '0000-00-00 00:00:00') {

                        $updateArray = array();
                        if ($liveUpdated == "0000-00-00 00:00:00") {
                            $updateArray['live_updated'] = '"' . date("Y-m-d H:i:s") . '"';
                        }
                        if ($ndorsedUpdated == "0000-00-00 00:00:00") {
                            $updateArray['ndorsed_updated'] = '"' . date("Y-m-d H:i:s") . '"';
                        }
                        if ($postUpdated == "0000-00-00 00:00:00") {
                            $updateArray['post_updated'] = '"' . date("Y-m-d H:i:s") . '"';
                        }

                        $this->UserOrganization->updateAll($updateArray, array("user_id" => $loggedInUser['id'], "organization_id" => $loggedInUser['current_org']['id']));
                    }
                }


//$isCurrentOrgActive = 1;

                $userStatus = array_search($userOrg['UserOrganization']["status"], $statusConfig);
                $orgStatus = array_search($userOrg['Organization']["status"], $statusConfig);

                $userRole = $roleList[$userOrg['UserOrganization']['user_role']];

                if ($loggedInUser['current_org']['org_role'] != $userRole) {

                    $message = $loggedInUser['email'] . "---> " . $loggedInUser['current_org']['org_role'] . "  === " . $userRole;
                    $this->log($message, "getTimelyUpdates");
                    $userRoleChanged = true;
                    $this->Session->write('Auth.User.current_org.org_role', $userRole);
                }

                if ($userStatus != 'active' || $orgStatus != 'active') {
                    $userOrgStatusUpdated = true;
                    unset($loggedInUser['current_org']);
                    $this->Session->write('Auth.User', $loggedInUser);
                }

                /*                 * UPDATE LAST LOGIN TIME* */
                $this->updateLastAppUsedTime();
            } else {


                $params = array();
                $params['fields'] = "*";
                $params['joins'] = array(
                    array(
                        'table' => 'user_organizations',
                        'alias' => 'UserOrganization',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'UserOrganization.user_id = ' . $loggedInUser['id'],
                            'UserOrganization.organization_id = DefaultOrg.organization_id'
                        )
                    )
                );

                $params['conditions'] = array("DefaultOrg.user_id" => $loggedInUser['id']);

                $defaultOrganization = $this->DefaultOrg->find("first", $params);


                if (empty($defaultOrganization)) {
//$isCurrentOrgActive = 0;
                    $userStatus = "";
                    $orgStatus = "";
                } else {
                    if ($defaultOrganization['Organization']['status'] == $statusConfig['active'] && $defaultOrganization['UserOrganization']['status'] == $statusConfig['active']) {
                        $currentOrg = $defaultOrganization['Organization'];

                        if ($defaultOrganization['UserOrganization']['entity_id'] > 0) {
// $department= $this->getOrgValues($org_id, "OrgDepartments",true,array($endorserd_id));
                            $entity = $this->getOrgValues($currentOrg["id"], "Entity", true, array($defaultOrganization['UserOrganization']['entity_id']));
                            if (!empty($entity)) {
                                $currentOrg['entity'] = $entity[0]["name"];
                            } else {
                                $currentOrg['entity'] = "";
                            }
                        } else {
                            $currentOrg['entity'] = "";
                        }

                        if ($defaultOrganization['UserOrganization']['department_id'] > 0) {
// $department= $this->getOrgValues($org_id, "OrgDepartments",true,array($endorserd_id));
                            $department = $this->getOrgValues($currentOrg["id"], "OrgDepartments", true, array($defaultOrganization['UserOrganization']['department_id']));
// $department = $defaultOrganization['UserOrganization']['department_id'];
                            if (!empty($department)) {
                                $currentOrg['department'] = $department[0]["name"];
                            } else {
                                $currentOrg['department'] = "";
                            }
                        } else {
                            $currentOrg['department'] = "";
                        }
                        if ($defaultOrganization['UserOrganization']['job_title_id'] > 0) {
// $department= $this->getOrgValues($org_id, "OrgDepartments",true,array($endorserd_id));
                            $jobtitle = $this->getOrgValues($currentOrg["id"], "OrgJobTitles", 1, array($defaultOrganization['UserOrganization']['job_title_id']));

                            if (!empty($jobtitle)) {
                                $currentOrg['job_title'] = $jobtitle[0]["name"];
                            } else {
                                $currentOrg['job_title'] = "";
                            }
                        } else {
                            $currentOrg['job_title'] = "";
                        }


                        $userRole = $currentOrg['org_role'] = $roleList[$defaultOrganization['UserOrganization']['user_role']];
                        if ($currentOrg["image"] != "") {
                            $currentOrg["image"] = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $currentOrg["image"];
                        }

                        $currentOrg['joined'] = $defaultOrganization['UserOrganization']['joined'];


                        $this->Session->write('Auth.User.current_org', $currentOrg);
                        $returnData['current_org'] = $currentOrg;
//$isCurrentOrgActive = 1;
                        $msg = "Default Organization activated!";
                        $userOrgStatusUpdated = true;
                    } else {
                        $userRole = isset($roleList[$defaultOrganization['UserOrganization']['user_role']]) && !empty($roleList[$defaultOrganization['UserOrganization']['user_role']]) ? $roleList[$defaultOrganization['UserOrganization']['user_role']] : "";
                    }
                    $userStatus = array_search($defaultOrganization['UserOrganization']["status"], $statusConfig);
                    $orgStatus = array_search($defaultOrganization['Organization']["status"], $statusConfig);
                }
            }






            /* Adding following list into timley updates */

//            $userID = $loggedInUser['id'];
//            $this->loadModel("UserFollowing");
//            $followingIdsArray = array();
//            $userFollowings = $this->UserFollowing->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $userID, 'status' => 1)));
//            if (!empty($userFollowings)) {
//                $userFollowings = array_shift($userFollowings);
//                $userFollowings = $userFollowings['UserFollowing'];
//                $uFollowingID = $userFollowings['id'];
//                $followingIdsArray = json_decode($userFollowings['following_ids']);
//            }
//            $returnData['following_users'] = $followingIdsArray;


            if (!empty($orgStatus) && !empty($userStatus)) {
                if ($orgStatus != 'active') {
//$isCurrentOrgActive = 0;

                    if ($orgStatus == 'inactive') {
                        $msg = "Default Organization inactivated.";
                    } else {
                        $msg = "Default Organization deleted!";
                    }
                } else if ($userStatus != 'active') {
//$isCurrentOrgActive  = 0;

                    if ($userStatus == 'inactive' || $userStatus == 'eval') {
                        $msg = "nDorse access inactivated for default Organization. Contact Organization Admin.";
                    } else if ($userStatus == 'deleted') {
                        $msg = "You have been deleted from your default nDorse Organization. Contact Organization Admin.";
                    }
                }

                $returnData['feed_updates'] = array("live_updated_count" => (int) $liveUpdatedCount, "ndorsed_updated_count" => (int) $ndorsedUpdatedCount, "post_updated_count" => (int) $postUpdatedCount);
                $returnData['org_updates'] = array("user_status" => $userStatus, "org_status" => $orgStatus, "user_role_changed" => $userRoleChanged, "user_role" => $userRole, "msg" => $msg);
            }

            $isRequestAccepted = false;
            $requestMsg = "";

            if (!($userOrgStatusUpdated || $userRoleChanged)) {
                $this->OrgRequests->bindModel(array('belongsTo' => array('Organization')));
                $acceptedRequests = $this->OrgRequests->find("all", array("conditions" => array("OrgRequests.user_id" => $loggedInUser['id'], "OrgRequests.organization_id" => $loggedInUser['pending_requests'], "OrgRequests.status" => 1)));

                if (!empty($acceptedRequests)) {
                    $isRequestAccepted = true;
                    $acceptedOrgs = array();
                    $requestMsg = "Your join request has been accepted for nDorse Organization ";

                    $totalAccepts = count($acceptedRequests);
                    $counter = 1;

                    foreach ($acceptedRequests as $acceptedRequest) {
                        if ($counter == $totalAccepts && $totalAccepts != 1) {
                            $requestMsg .= " and ";
                        } else if ($counter < $totalAccepts - 1) {
                            $requestMsg .= ", ";
                        }

                        $requestMsg .= $acceptedRequest['Organization']['name'];

                        $counter++;
//                            $orgDetails = array();
//                            $orgDetails['id'] = $acceptedRequest['Organization']['id'];
//                            $orgDetails['name'] = $acceptedRequest['Organization']['name'];
//                            $acceptedOrgs[] = $orgDetails;
                    }

                    $requestMsg .= ". You can switch to the organization.";

                    $this->Session->write('Auth.User.pending_requests', array());
                }
            }

            $returnData['accepted_request'] = array("is_accepted" => $isRequestAccepted, "msg" => $requestMsg, "button_text" => "Go To Organization");

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Updates", "data" => $returnData),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function recoverusername() {
        if (isset($this->request->query["email"])) {
            $email = $this->request->query["email"];
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $userdata = $this->User->findByEmail(strtolower($email));
                if (!empty($userdata)) {
                    $subject = "Recover Username";
                    $viewVars = array("username" => $userdata["User"]["email"], "fname" => $userdata["User"]["fname"]);

                    /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode($userdata["User"]['id']);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $viewVars["pathToRender"] = $pathToRender;
                    /**/

                    $configVars = serialize($viewVars);
                    $emailQueue[] = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => "recover_username");
                    $this->Email->saveMany($emailQueue);
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "username detail send successfully",
                            "data" => true),
                        '_serialize' => array('result')
                    ));
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "This username not exist this system"),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid email address"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "email address key not exist."),
                '_serialize' => array('result')
            ));
        }
    }

    public function topendorse() {

        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
            $user_id = $loggedInUser['id'];
            if (isset($loggedInUser['current_org'])) {
//print_r($loggedInUser['current_org']);
                $org_id = $loggedInUser['current_org']['id'];
//token=a640fe8d1ccd9949adef9103cdfe2615&month=March&year=2015
                $month = date("F");
                $year = date("Y");
                $currentmonth = 1;
//$last_week_start = strtotime('-2 week monday 00:00:00');
//$last_week_end = strtotime('-1 week sunday 23:59:59');
                if (date('N') == 1) {
                    $last_week_start = date('Y-m-d 00:00:00', strtotime('-1 week monday 00:00:00'));
                } else {
                    $last_week_start = date('Y-m-d 00:00:00', strtotime('-2 week monday 00:00:00'));
                }



                $last_week_end = date('Y-m-d 23:59:59', strtotime('-1 week sunday 23:59:59'));

                if (isset($this->request->data["month"]) && $this->request->data["month"] != "" && isset($this->request->data["year"]) && $this->request->data["year"] != "") {
                    $month = $this->request->data["month"];
                    $year = $this->request->data["year"];
                    $currentmonth = 0;
                }

                if ($month != "" && $year != "") {

                    $month = strtolower($month);



                    $startdate = $month . ' 01 ' . $year;
                    $sdate = date('Y-m-d', strtotime($startdate));
                    $enddate = date('Y-m-t', strtotime($sdate));
                    $month = date('m', strtotime($sdate));
                    $start_date = date('Y-m-d 00:00:00', strtotime($sdate));

                    $topendorsedmontharray = array();
                    $topendorsedweekarray = array();
                    $weekarray = array();
                    $end_date = date('Y-m-d 23:59:59', strtotime($enddate));

                    $allusernew = array();
                    $first_day = date('N', strtotime($startdate));
                    if ($first_day != 1) {
                        $first_day = 7 - $first_day + 1;
                    } else {
                        $first_day = 0;
                    }
                    $last_day = date('t', strtotime($startdate));
                    $days = array();
                    for ($i = $first_day; $i <= $last_day; $i = $i + 7) {
                        $days[] = $i;
                    }
// print_r($days);
                    $params = array();
                    $params['fields'] = "count(Endorsement.endorser_id) as cnt,Endorsement.endorser_id,User.id, User.fname ,User.lname,User.image,Endorsement.organization_id,OrgDepartments.name,OrgJobTitles.title";
//$conditionarray["Endorsement.endorsement_for"] = 'user';
                    $conditionarray["Endorsement.organization_id"] = $org_id;
                    $conditionarray["Endorsement.created >= "] = $start_date;
                    $conditionarray["Endorsement.created <= "] = $end_date;
                    $params['joins'] = array(
                        array(
                            'table' => 'users',
                            'alias' => 'User',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Endorsement.endorser_id =User.id '
                            )
                        ), array(
                            'table' => 'user_organizations',
                            'alias' => 'UserOrganization',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Endorsement.endorser_id =UserOrganization.user_id ',
                                'UserOrganization.organization_id =' . $org_id
                            )
                        ), array(
                            'table' => 'org_departments',
                            'alias' => 'OrgDepartments',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'OrgDepartments.id =UserOrganization.department_id'
                            )
                        ),
                        array(
                            'table' => 'org_job_titles',
                            'alias' => 'OrgJobTitles',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'OrgJobTitles.id =UserOrganization.job_title_id'
                            )
                        )
                    );
                    $params['group'] = 'Endorsement.endorser_id,Endorsement.organization_id';
                    $params['order'] = array('cnt DESC');
                    $params['conditions'] = $conditionarray;
                    $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                    $topendorserd = $this->Endorsement->find("all", $params);
// print_r($topendorserd);
//echo $this->Endorsement->getLastQuery();	
                    $topendorsedmontharray["endorser"] = $topendorserd;


                    $params['fields'] = "count(Endorsement.endorsed_id) as cnt,Endorsement.endorsed_id,User.id, User.fname ,User.lname,User.image,Endorsement.organization_id,OrgDepartments.name,OrgJobTitles.title";
                    $conditionarray["Endorsement.endorsement_for"] = 'user';

                    $params['joins'] = array(
                        array(
                            'table' => 'users',
                            'alias' => 'User',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Endorsement.endorsed_id =User.id '
                            )
                        ),
                        array(
                            'table' => 'user_organizations',
                            'alias' => 'UserOrganization',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Endorsement.endorsed_id =UserOrganization.user_id ',
                                'UserOrganization.organization_id =' . $org_id
                            )
                        ), array(
                            'table' => 'org_departments',
                            'alias' => 'OrgDepartments',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'OrgDepartments.id =UserOrganization.department_id'
                            )
                        ),
                        array(
                            'table' => 'org_job_titles',
                            'alias' => 'OrgJobTitles',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'OrgJobTitles.id =UserOrganization.job_title_id'
                            )
                        )
                    );
                    $params['group'] = 'Endorsement.endorsed_id,Endorsement.organization_id';
                    $params['order'] = array('cnt DESC');
                    $params['conditions'] = $conditionarray;
                    $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                    $topendorserd = $this->Endorsement->find("all", $params);
//print_r($topendorserd);
                    $topendorsedmontharray["endorsed"] = $topendorserd;

//print_r($topendorsedmontharray);
                    $wcount = 1;

                    foreach ($days as $dayval) {
                        if ($wcount < count($days)) {
// echo $dayval;
                            $startweekday = $year . "-" . $month . "-" . ($dayval + 1);
                            $endweekday = $year . "-" . $month . "-" . ($dayval + 7);
//echo "start date";
                            $start_date = date('Y-m-d 00:00:00', strtotime($startweekday));

                            $end_date = date('Y-m-d 23:59:59', strtotime($endweekday));
//  $last_week_start=  date('Y-m-d 00:00:00',  strtotime('-2 week monday 00:00:00'));
//$last_week_end =  date('Y-m-d 23:59:59', strtotime('-1 week sunday 23:59:59'));

                            if ($currentmonth == 1 && $wcount > 1) {

                                continue;
                            } elseif ($currentmonth == 1) {
                                $start_date = $last_week_start;
                                $end_date = $last_week_end;
                                $startweekday = date("Y-m-d", strtotime($start_date)); //$year."-".$month."-".($dayval+1);
                                $endweekday = date("Y-m-d", strtotime($end_date)); // $year."-".$month."-".($dayval+7);
                            }

                            $params = array();
                            $params['fields'] = "count(Endorsement.endorser_id) as cnt,Endorsement.endorser_id,User.id, User.fname ,User.lname,User.image,Endorsement.organization_id,OrgDepartments.name,OrgJobTitles.title";
//$conditionarray["Endorsement.endorsement_for"] = 'user';
                            $conditionarray["Endorsement.organization_id"] = $org_id;
                            $conditionarray["Endorsement.created >= "] = $start_date;
                            $conditionarray["Endorsement.created <= "] = $end_date;
                            $params['joins'] = array(
                                array(
                                    'table' => 'users',
                                    'alias' => 'User',
                                    'type' => 'INNER',
                                    'conditions' => array(
                                        'Endorsement.endorser_id =User.id '
                                    )
                                ), array(
                                    'table' => 'user_organizations',
                                    'alias' => 'UserOrganization',
                                    'type' => 'INNER',
                                    'conditions' => array(
                                        'Endorsement.endorser_id =UserOrganization.user_id ',
                                        'UserOrganization.organization_id =' . $org_id
                                    )
                                ), array(
                                    'table' => 'org_departments',
                                    'alias' => 'OrgDepartments',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'OrgDepartments.id =UserOrganization.department_id'
                                    )
                                ),
                                array(
                                    'table' => 'org_job_titles',
                                    'alias' => 'OrgJobTitles',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'OrgJobTitles.id =UserOrganization.job_title_id'
                                    )
                                )
                            );
                            $params['group'] = 'Endorsement.endorser_id,Endorsement.organization_id';
                            $params['order'] = array('cnt DESC');
                            $params['conditions'] = $conditionarray;
                            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                            $topendorserd = $this->Endorsement->find("all", $params);
//
//  print_r($topendorserd);
                            $topendorsedusernew = 0;
                            $topendorseduserweek = array();
                            $topcnt = 0;


                            foreach ($topendorserd as $val) {

                                if ($topendorsedusernew == 0) {
                                    $topendorsedusernew = $val[0]["cnt"];
                                    $topendorseduserweek = array($val["User"]["id"]);
                                } else {
                                    if ($val[0]["cnt"] > $topendorsedusernew) {
                                        $topendorsedusernew = $val[0]["cnt"];
                                        $topendorseduserweek = array($val["User"]["id"]);
                                    } elseif ($val[0]["cnt"] == $topendorsedusernew) {
                                        $previous_arr = $topendorseduserweek;
                                        $topendorseduserweek = array_merge($previous_arr, array($val["User"]["id"]));
                                    }
                                }
                                if (!in_array($val["User"]["id"], $allusernew)) {
                                    $allusernew[$val["User"]["id"]] = array_merge($val["User"], $val["OrgDepartments"], $val["OrgJobTitles"]);
                                }
                            }

                            $topendorseduserweekdata[$startweekday . "&&&&" . $endweekday]["endorser"] = $topendorseduserweek;
//
//print_r($topendorserd);
//$topendorsedweekarray[$startweekday."&&&&".$endweekday]["endorse"] =  $topendorserd;
//$topendorsedmontharray["endorse"]= $topendorserd;

                            $params['fields'] = "count(Endorsement.endorsed_id) as cnt,Endorsement.endorsed_id,User.id, User.fname ,User.lname,User.image,Endorsement.organization_id,OrgDepartments.name,OrgJobTitles.title";
                            $conditionarray["Endorsement.endorsement_for"] = 'user';

                            $params['joins'] = array(
                                array(
                                    'table' => 'users',
                                    'alias' => 'User',
                                    'type' => 'INNER',
                                    'conditions' => array(
                                        'Endorsement.endorsed_id =User.id '
                                    )
                                ),
                                array(
                                    'table' => 'user_organizations',
                                    'alias' => 'UserOrganization',
                                    'type' => 'INNER',
                                    'conditions' => array(
                                        'Endorsement.endorsed_id =UserOrganization.user_id ',
                                        'UserOrganization.organization_id =' . $org_id
                                    )
                                ), array(
                                    'table' => 'org_departments',
                                    'alias' => 'OrgDepartments',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'OrgDepartments.id =UserOrganization.department_id'
                                    )
                                ),
                                array(
                                    'table' => 'org_job_titles',
                                    'alias' => 'OrgJobTitles',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'OrgJobTitles.id =UserOrganization.job_title_id'
                                    )
                                )
                            );
                            $params['group'] = 'Endorsement.endorsed_id,Endorsement.organization_id';
                            $params['order'] = array('cnt DESC');
                            $params['conditions'] = $conditionarray;
                            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                            $topendorserd = $this->Endorsement->find("all", $params);
                            $topendorsedusernew = 0;
                            $topendorseduserweek = array();
                            $topcnt = 0;


                            foreach ($topendorserd as $val) {
//print_r($val[0]["cnt"])."dddttt";
// echo "<hr>";

                                if ($topendorsedusernew == 0) {
                                    $topendorsedusernew = $val[0]["cnt"];
                                    $topendorseduserweek = array($val["User"]["id"]);
                                } else {
                                    if ($val[0]["cnt"] > $topendorsedusernew) {
                                        $topendorsedusernew = $val[0]["cnt"];
                                        $topendorseduserweek = array($val["User"]["id"]);
                                    } elseif ($val[0]["cnt"] == $topendorsedusernew) {
                                        $previous_arr = $topendorseduserweek;
                                        $topendorseduserweek = array_merge($previous_arr, array($val["User"]["id"]));
                                    }
                                }
                                if (!in_array($val["User"]["id"], $allusernew)) {
                                    $allusernew[$val["User"]["id"]] = array_merge($val["User"], $val["OrgDepartments"], $val["OrgJobTitles"]);
                                }
                            }
//echo "<hr>";
//print_r($topendorseduserweek);
//echo "<hr>";
//echo $this->Endorsement->getLastQuery();
                            $topendorseduserweekdata[$startweekday . "&&&&" . $endweekday]["endorsed"] = $topendorseduserweek;
// print_r($topendorserd);
// $topendorsedmontharray["endorsed"]= $topendorserd;
// print_r($topendorsedmontharray);
                            $week[] = $startweekday . "&&&&" . $endweekday;
                        }
                        $wcount++;
                    }
// print_r($topendorseduserweekdata);
                    $topendorsedusermonthnew = 0;
                    $topendorsedusermonth = array();
                    $topcnt = 0;


                    foreach ($topendorsedmontharray["endorser"] as $val) {

                        if ($topendorsedusermonthnew == 0) {
                            $topendorsedusermonthnew = $val[0]["cnt"];
                            $topendorsedusermonth = array($val["User"]["id"]);
                        } else {
                            if ($val[0]["cnt"] > $topendorsedusermonthnew) {
                                $topendorsedusermonthnew = $val[0]["cnt"];
                                $topendorsedusermonth = array($val["User"]["id"]);
                            } elseif ($val[0]["cnt"] == $topendorsedusermonthnew) {
                                $previous_arr = $topendorsedusermonth;
                                $topendorsedusermonth = array_merge($previous_arr, array($val["User"]["id"]));
                            }
                        }
                        if (!in_array($val["User"]["id"], $allusernew)) {
                            $allusernew[$val["User"]["id"]] = array_merge($val["User"], $val["OrgDepartments"], $val["OrgJobTitles"]);
                        }
                    }

                    $topendorsedmontharray["endorser"] = $topendorsedusermonth;
                    $topendorsedusermonthnew = 0;
                    $topendorsedusermonth = array();
                    $topcnt = 0;


                    foreach ($topendorsedmontharray["endorsed"] as $val) {

                        if ($topendorsedusermonthnew == 0) {
                            $topendorsedusermonthnew = $val[0]["cnt"];
                            $topendorsedusermonth = array($val["User"]["id"]);
                        } else {
                            if ($val[0]["cnt"] > $topendorsedusermonthnew) {
                                $topendorsedusermonthnew = $val[0]["cnt"];
                                $topendorsedusermonth = array($val["User"]["id"]);
                            } elseif ($val[0]["cnt"] == $topendorsedusermonthnew) {
                                $previous_arr = $topendorsedusermonth;

                                $topendorsedusermonth = array_merge($previous_arr, array($val["User"]["id"]));
                            }
                        }
                        if (!in_array($val["User"]["id"], $allusernew)) {
                            $allusernew[$val["User"]["id"]] = array_merge($val["User"], $val["OrgDepartments"], $val["OrgJobTitles"]);
                        }
                    }
                    $topendorsedmontharray["endorsed"] = $topendorsedusermonth;
//print_r($topendorsedmontharray);
//print_r($allusernew);
                    $topendoserwise = array();
                    $arraydata = array();

                    foreach ($topendorseduserweekdata as $key => $topval) {

                        $weekday = explode("&&&&", $key);
                        $startweekday = $weekday[0];
                        $endweekday = $weekday[1];
                        if (!empty($topval["endorser"])) {
//if ($currentmonth != 1) {
                            $duration = "(" . date("m/d/Y", strtotime($startweekday)) . " - " . date("m/d/Y", strtotime($endweekday)) . ")";
//} else {
//  $duration = "";
// }
                            $headtitle = "Top nDorser of the Week";
                            $tdata = array();
                            foreach ($topval["endorser"] as $tval) {
                                $val = $allusernew[$tval];

                                if ($val["image"] != "") {
                                    $val["image"] = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $val["image"];
                                }
                                $val["department"] = $val["name"];
                                if ($val["name"] == null) {
                                    $val["department"] = "";
                                }
                                $val["job_title"] = $val["title"];
                                if ($val["job_title"] == null) {
                                    $val["job_title"] = "";
                                }

                                $val["name"] = trim($val["fname"] . " " . $val["lname"]);
                                $val["isData"] = "1";
                                $val["type"] = "week";
                                $tdata[] = $val;
                            }
                            if (!empty($tdata[0])) {
                                $arraydata[] = array("title" => $headtitle, "duration" => $duration, "list" => $tdata);
                            }
                        } else {
                            $arraydata1 = array();
                            $arraydata1[] = array("name" => "", "image" => "", "isData" => "0", "job_title" => "", "type" => "week");
                            $duration = "(" . date("m/d/Y", strtotime($startweekday)) . " - " . date("m/d/Y", strtotime($endweekday)) . ")";
                            $headtitle = "Top nDorser of the Week";
                            $arraydata[] = array("title" => $headtitle, "duration" => $duration, "list" => $arraydata1);
                        }
                        if (!empty($topval["endorsed"])) {
// $duration ="(".date("m/d/Y",strtotime($startweekday))." - ".date("m/d/Y",strtotime($endweekday)).")";
// if ($currentmonth != 1) {
                            $duration = "(" . date("m/d/Y", strtotime($startweekday)) . " - " . date("m/d/Y", strtotime($endweekday)) . ")";
// } else {
//     $duration = "";
// }
                            $headtitle = "Top nDorsed of the Week";
                            $tdata = array();
                            foreach ($topval["endorsed"] as $tval) {
                                $val = $allusernew[$tval];
                                if ($val["image"] != "") {
                                    $val["image"] = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $val["image"];
                                }
                                $val["department"] = $val["name"];
                                if ($val["name"] == null) {
                                    $val["department"] = "";
                                }
                                $val["job_title"] = $val["title"];
                                if ($val["job_title"] == null) {
                                    $val["job_title"] = "";
                                }
                                $val["name"] = trim($val["fname"] . " " . $val["lname"]);
                                $val["isData"] = "1";
                                $val["type"] = "week";
                                $tdata[] = $val;
                            }
                            if (!empty($tdata[0])) {
                                $arraydata[] = array("title" => $headtitle, "duration" => $duration, "list" => $tdata);
                            }
                        } else {
                            $arraydata1 = array();
                            $arraydata1[] = array("name" => "", "image" => "", "isData" => "0", "job_title" => "", "type" => "week");
// if ($currentmonth != 1) {
                            $duration = "(" . date("m/d/Y", strtotime($startweekday)) . " - " . date("m/d/Y", strtotime($endweekday)) . ")";
//} else {
//    $duration = "";
// }
// $duration ="(".date("m/d/Y",strtotime($startweekday))." - ".date("m/d/Y",strtotime($endweekday)).")";
                            $headtitle = "Top nDorsed of the Week";
                            $arraydata[] = array("title" => $headtitle, "duration" => $duration, "list" => $arraydata1);
                        }
                    }
                    $monthdata = array();
                    foreach ($topendorsedmontharray as $key => $topval) {


                        if (!empty($topval)) {
                            $ndorsename = str_replace("endorse", "nDorse", $key);
                            $headtitle = "Top " . $ndorsename . " of the Month";
                            $tdata = array();
// print_r($topval);
                            foreach ($topval as $tval) {
//echo $tval;echo "<hr>";
                                $val = $allusernew[$tval];

                                if ($val["image"] != "") {
                                    $val["image"] = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $val["image"];
                                }
                                $val["department"] = $val["name"];
                                if ($val["name"] == null) {
                                    $val["department"] = "";
                                }
                                $val["job_title"] = $val["title"];
                                if ($val["job_title"] == null) {
                                    $val["job_title"] = "";
                                }

                                $val["job_title"] = $val["title"];
                                $val["name"] = trim($val["fname"] . " " . $val["lname"]);
                                $val["isData"] = "1";
                                $val["type"] = "month";

                                $tdata[] = $val;
                            }
                            if (!empty($tdata[0])) {
                                $monthdata[] = $arraydata[] = array("title" => $headtitle, "list" => $tdata);
                            }
                        } else {
                            $arraydata1 = array();
                            $ndorsename = str_replace("endorse", "nDorse", $key);
                            $headtitle = "Top " . $ndorsename . " of the Month";
                            $arraydata1[] = array("name" => "", "image" => "", "isData" => "0", "job_title" => "", "type" => "month");
                            $arraydata[] = array("title" => $headtitle, "list" => $arraydata1);
                        }
                    }
                    $topendoserwise[] = $arraydata;
//print_r($arraydata);
                    if (!empty($arraydata)) {
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Top endorsed data", "data" => $arraydata),
                            '_serialize' => array('result')
                        ));
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "No Top Endorsements available for this Month", "data" => true),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $topendorserorg = $this->Topendorser->find("all", array("conditions" => array("organization_id" => $org_id)));
// print_r($topendorserorg);
                    $topendorseuser = array();
                    $alluser = array();
                    if (!empty($topendorserorg)) {
                        foreach ($topendorserorg as $val) {
// print_r($val);
                            $endorser_user = explode(",", $val["Topendorser"]["endorser"]);
                            $endorsed_user = explode(",", $val["Topendorser"]["endorsed"]);
                            $alluser1 = array_merge($endorser_user, $endorsed_user);
                            $alluser = array_merge($alluser, $alluser1);
                            $alluser = array_unique($alluser);
                            $topendorseuser[$val["Topendorser"]["type"]] = array("top_endorser" => $endorser_user, "top_endorsed" => $endorsed_user);
                        }

                        $alluser = array_unique($alluser);
                        $userDetails = $this->User->find("all", array(
                            "joins" => array(
                                array(
                                    'table' => 'user_organizations',
                                    'alias' => 'UserOrganization',
                                    'type' => 'INNER',
                                    'conditions' => array(
                                        'UserOrganization.user_id = User.id',
                                        'UserOrganization.organization_id=' . $org_id
                                    )
                                ), array(
                                    'table' => 'org_departments',
                                    'alias' => 'OrgDepartments',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'OrgDepartments.id =UserOrganization.department_id'
                                    )
                                ),
                                array(
                                    'table' => 'org_job_titles',
                                    'alias' => 'OrgJobTitles',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'OrgJobTitles.id =UserOrganization.job_title_id'
                                    )
                                )
                            ),
                            "conditions" => array("User.id" => $alluser)
                            , "fields" => array("User.id", "User.fname", "User.lname", "User.image", "OrgJobTitles.title", "OrgDepartments.name")));


                        $userdata = array();

                        foreach ($userDetails as $val) {

                            $job_title = "";
                            $department_name = "";

                            if ($val["OrgDepartments"]["name"] != "") {
                                $job_title = $val["OrgDepartments"]["name"];
                            }
                            if ($val["OrgJobTitles"]["title"] != "") {
                                $department_name = $val["OrgJobTitles"]["title"];
                            }
                            if ($val["User"]["image"] != "") {
                                $val["User"]["image"] = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $val["User"]["image"];
                            }
                            $val["User"]["name"] = trim($val["User"]["fname"] . " " . $val["User"]["lname"]);

                            $val["User"]["department"] = $department_name;
                            $val["User"]["job_title"] = $job_title;


                            $val["User"]["isData"] = "1";
                            unset($val["User"]["fname"]);
                            unset($val["User"]["lname"]);
                            $userdata[$val["User"]["id"]] = $val["User"];
// print_r($val["User"]);
                        }

//print_r($topendorseuser);
                        $topendoserwise = array();
                        $arraydata = array();
                        foreach ($topendorseuser as $key => $topval) {

                            $headtitle = "Top nDorser of the " . $key;
                            $t_endorser = $topval["top_endorser"];
                            $tdata = array();
                            foreach ($t_endorser as $tval) {
                                if ($tval > 0) {
                                    $tdata[] = $userdata[$tval];
                                }
                            }
                            if (!empty($tdata[0])) {
                                $arraydata[] = array("title" => $headtitle, "list" => $tdata);
                            }
                            $headtitle = "Top nDorsed of the " . $key;
                            $t_endorsed = $topval["top_endorsed"];

                            $tdata = array();
                            foreach ($t_endorsed as $tval) {
                                if ($tval > 0) {
                                    $tdata[] = $userdata[$tval];
                                }
                            }
                            if (!empty($tdata[0])) {
                                $arraydata[] = array("title" => $headtitle, "list" => $tdata);
                            }
                        }
                        $topendoserwise[] = $arraydata;
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Top endorsed data", "data" => $arraydata),
                            '_serialize' => array('result')
                        ));
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "No Top Endorsements available"),
                            '_serialize' => array('result')
                        ));
                    }
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Currently any organization has been not joined."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "email address key not exist."),
                '_serialize' => array('result')
            ));
        }
    }

    public function endorsementbydept() {


        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
            $user_id = $loggedInUser['id'];
//pr($loggedInUser['current_org']); exit;
            if (isset($loggedInUser['current_org'])) {

                if (isset($this->request->data["height"]) && $this->request->data["height"] > 0) {
                    $height = $this->request->data["height"];
                } else {
                    $height = 400;
                }
                if (isset($this->request->data["width"]) && $this->request->data["width"] > 0) {
                    $width = $this->request->data["width"];
                } else {
                    $width = 450;
                }


                $org_id = $loggedInUser['current_org']['id'];

                $params = array();
                $start_date = "";
                $end_date = "";
                if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                    $start_date = $this->request->data["start_date"];
                }
                if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                    $end_date = $this->request->data["end_date"];
                }
                $params = array();

                if ($start_date != "") {

                    $conditionarray["Endorsement.created >= "] = date("Y-m-d 00:00:00", $start_date);
                }
                if ($end_date != "") {
                    $conditionarray["Endorsement.created <= "] = date("Y-m-d 23:59:59", $end_date);
                }

                $params['fields'] = "count(Endorsement.endorsed_id) as cnt,OrgDepartments.name as department";
                $conditionarray["Endorsement.organization_id"] = $org_id;

                $conditionarray["Endorsement.endorsement_for"] = "department";

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
//pr($params); exit;
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $leaderboard = $this->Endorsement->find("all", $params);
//                pr($leaderboard); 
//                echo $this->Endorsement->getLastQuery();die;
//                exit;

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

//$this->autoRender = false;
                    /* Set up new view that won't enter the ClassRegistry */
                    $view = new View($this, false);
                    $view->set('data', $series);
                    $view->set('type', "Department");
                    $view->set('height', $height);
                    $view->set('width', $width);
                    $view->viewPath = 'Elements';

                    /* Grab output into variable without the view actually outputting! */
                    $view_output = $view->render('box');
//echo $view_output;
                    $view_output = str_replace("\r", "", $view_output);
                    $view_output = str_replace("\t", "", $view_output);
                    $view_output = str_replace("\n", "", $view_output);
                    if (isset($this->request->data["web"]) && $this->request->data["web"] == 1) {
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Endorsed by department", "data" => $seriesdata),
                            '_serialize' => array('result')
                        ));
                    } else {

                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Endorsed by department", "data" => $view_output),
                            '_serialize' => array('result')
                        ));
                    }

//$this->set(array(
//    'result' => array("status" => true
//        , "msg" => "Endorsed by department", "data" => $view_output),
//    '_serialize' => array('result')
//));
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "No data available."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Currently any organization has been not joined."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "token not exist."),
                '_serialize' => array('result')
            ));
        }
    }

    public function endorsementbyday() {


        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
            $user_id = $loggedInUser['id'];
            if (isset($loggedInUser['current_org'])) {

                $org_id = $loggedInUser['current_org']['id'];

                if (isset($this->request->data["height"]) && $this->request->data["height"] > 0) {
                    $height = $this->request->data["height"];
                } else {
                    $height = 400;
                }
                if (isset($this->request->data["width"]) && $this->request->data["width"] > 0) {
                    $width = $this->request->data["width"];
                } else {
                    $width = 450;
                }

                $params = array();
                $start_date = "";
                $end_date = "";
                if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                    $start_date = $this->request->data["start_date"];
                }
                if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                    $end_date = $this->request->data["end_date"];
                }
                $params = array();

                if ($start_date != "") {

                    $conditionarray["Endorsement.created >= "] = date("Y-m-d 00:00:00", $start_date);
                }
                if ($end_date != "") {
                    $conditionarray["Endorsement.created <= "] = date("Y-m-d 23:59:59", $end_date);
                }


                $array = array();


                $array['fields'] = array('count(Endorsement.id) as cnt', 'DATE_FORMAT(Endorsement.created,"%m-%d-%Y") as cdate');

                $conditionarray['Endorsement.organization_id'] = $org_id; // array('0','1','3');

                $array['conditions'] = $conditionarray;
                $array['order'] = 'Endorsement.created asc';
                $array['group'] = 'DATE_FORMAT(Endorsement.created, "%m-%d-%Y")';

                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $leaderboard = $this->Endorsement->find("all", $array);
// echo $this->Endorsement->getLastQuery();exit; 		
// print_r($leaderboard);

                if (!empty($leaderboard)) {

                    $seriesdata = "";
                    foreach ($leaderboard as $lval) {
                        if ($seriesdata == "") {
                            $seriesdata = "{
              name: '" . $lval[0]["cdate"] . "',
             y: " . $lval[0]["cnt"] . "}";
                        } else {
                            $seriesdata .= ",{
               name: '" . $lval[0]["cdate"] . "',
             y: " . $lval[0]["cnt"] . "}";
                        }
                    }

//echo $seriesdata;exit;
                    $series = "  {
            name: 'Date',
            colorByPoint: false,
            data: [" . $seriesdata . "]}";

//$this->autoRender = false;
                    /* Set up new view that won't enter the ClassRegistry */
                    $view = new View($this, false);
                    $view->set('data', $series);
                    $view->set('height', $height);
                    $view->set('width', $width);
                    $view->viewPath = 'Elements';

                    /* Grab output into variable without the view actually outputting! */
                    $view_output = $view->render('column');
//print_r($view_output);

                    $view_output = str_replace("\r", "", $view_output);
                    $view_output = str_replace("\t", "", $view_output);
                    $view_output = str_replace("\n", "", $view_output);
                    if (isset($this->request->data["web"]) && $this->request->data["web"] == 1) {
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Endorsed by day", "data" => $seriesdata),
                            '_serialize' => array('result')
                        ));
                    } else {

                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Endorsed by day", "data" => $view_output),
                            '_serialize' => array('result')
                        ));
                    }
//$this->set(array(
//    'result' => array("status" => true
//        , "msg" => "Endorsed by department", "data" => $view_output),
//    '_serialize' => array('result')
//));
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "No data available."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Currently any organization has been not joined."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "token not exist."),
                '_serialize' => array('result')
            ));
        }
    }

    public function endorsementbycorevalues() {
        if ($this->request->is('post')) {
            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
            if (isset($loggedInUser['current_org'])) {
                if (isset($this->request->data["org_id"]) && $this->request->data["org_id"] > 0) {
                    $org_id = $this->request->data["org_id"];
                } else {

                    $org_id = $loggedInUser['current_org']['id'];
                }
                if (isset($this->request->data["height"]) && $this->request->data["height"] > 0) {
                    $height = $this->request->data["height"];
                } else {
                    $height = 400;
                }
                if (isset($this->request->data["width"]) && $this->request->data["width"] > 0) {
                    $width = $this->request->data["width"];
                } else {
                    $width = 450;
                }
                $orgtype = "org";
                if (isset($this->request->data["type"]) && $this->request->data["type"] != "") {
                    $orgtype = $this->request->data["type"];
                }
                $user_id = $loggedInUser['id'];
                if (isset($loggedInUser['current_org'])) {

// $org_id = $loggedInUser['current_org']['id'];

                    $params = array();
                    $start_date = "";
                    $end_date = "";
                    if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                        $start_date = $this->request->data["start_date"];
                    }
                    if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                        $end_date = $this->request->data["end_date"];
                    }


                    if ($start_date != "") {

                        $conditionarray["Endorsement.created >= "] = date("Y-m-d 00:00:00", $start_date);
                    }
                    if ($end_date != "") {
                        $conditionarray["Endorsement.created <= "] = date("Y-m-d 23:59:59", $end_date);
                    }


                    $params = array();
                    $conditionarray['Endorsement.organization_id'] = $org_id; // array('0','1','3');
                    if ($orgtype == "user") {
                        $conditionarray["Endorsement.endorsed_id"] = $user_id;
                    }
                    $params['conditions'] = $conditionarray;

                    $params['fields'] = "count(EndorseCoreValue.value_id) as total, OrgCoreValues.name as core_value ";
                    $params['joins'] = array(
                        array(
                            'table' => 'endorse_core_values',
                            'alias' => 'EndorseCoreValue',
                            'type' => 'INNER',
                            'conditions' => array(
                                'EndorseCoreValue.endorsement_id =Endorsement.id '
                            )
                        ),
                        array(
                            'table' => 'org_core_values',
                            'alias' => 'OrgCoreValues',
                            'type' => 'INNER',
                            'conditions' => array(
                                'OrgCoreValues.id =EndorseCoreValue.value_id '
                            )
                        )
                    );
                    $params['group'] = 'EndorseCoreValue.value_id';
                    $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                    $corevalues = $this->Endorsement->find("all", $params);
//echo $this->Endorsement->getLastQuery();		
//  print_r($corevalues);exit;

                    if (!empty($corevalues)) {


                        $seriesdata = "";
                        foreach ($corevalues as $lval) {
                            if ($seriesdata == "") {
                                $seriesdata = "{
              name: '" . addslashes($lval["OrgCoreValues"]["core_value"]) . "',
             y: " . $lval[0]["total"] . "}";
                            } else {
                                $seriesdata .= ",{
              name: '" . addslashes($lval["OrgCoreValues"]["core_value"]) . "',
             y: " . $lval[0]["total"] . "}";
                            }
                        }


                        $series = "  {
            name: 'Core Value',
            colorByPoint: true,
            data: [" . $seriesdata . "]}";

//$this->autoRender = false;
                        /* Set up new view that won't enter the ClassRegistry */
                        $view = new View($this, false);
                        $view->set('data', $series);
                        $view->set('type', "Core Values");
                        $view->set('height', $height);
                        $view->set('width', $width);
                        $view->viewPath = 'Elements';

                        /* Grab output into variable without the view actually outputting! */
                        $view_output = $view->render('box');
//echo $view_output;
                        $view_output = str_replace("\r", "", $view_output);
                        $view_output = str_replace("\t", "", $view_output);
                        $view_output = str_replace("\n", "", $view_output);

                        if (isset($this->request->data["web"]) && $this->request->data["web"] == 1) {
                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "nDorsement by core values", "data" => $seriesdata),
                                '_serialize' => array('result')
                            ));
                        } else {

                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "nDorsement by core values", "data" => $view_output),
                                '_serialize' => array('result')
                            ));
                        }
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "No data available."),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Currently any organization has been not joined."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Currently any organization has been not joined."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "token not exist."),
                '_serialize' => array('result')
            ));
        }
    }

//
    public function endorsementbyjobtitles() {


        if ($this->request->is('post')) {

            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
            if (isset($loggedInUser['current_org'])) {
                if (isset($this->request->data["org_id"]) && $this->request->data["org_id"] > 0) {
                    $org_id = $this->request->data["org_id"];
                } else {

                    $org_id = $loggedInUser['current_org']['id'];
                }
// $org_id =335;
                if (isset($this->request->data["height"]) && $this->request->data["height"] > 0) {
                    $height = $this->request->data["height"];
                } else {
                    $height = 400;
                }
                if (isset($this->request->data["width"]) && $this->request->data["width"] > 0) {
                    $width = $this->request->data["width"];
                } else {
                    $width = 450;
                }
                $orgtype = "org";
                if (isset($this->request->data["type"]) && $this->request->data["type"] != "") {
                    $orgtype = $this->request->data["type"];
                }
                $user_id = $loggedInUser['id'];
                if (isset($loggedInUser['current_org'])) {

// $org_id = $loggedInUser['current_org']['id'];

                    $params = array();
                    $start_date = "";
                    $end_date = "";
                    if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                        $start_date = $this->request->data["start_date"];
                    }
                    if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                        $end_date = $this->request->data["end_date"];
                    }

                    $startdate = $enddate = "";
                    if ($start_date != "") {

                        $conditionarray["Endorsement.created >= "] = $startdate = date("Y-m-d 00:00:00", $start_date);
                    }
                    if ($end_date != "") {
                        $conditionarray["Endorsement.created <= "] = $enddate = date("Y-m-d 23:59:59", $end_date);
                    }
                    $this->Common->bindmodelcommonjobtitle();
                    $jobtitles = $this->Common->getorgjobtitles($org_id);
                    $jobtitlesid = array_keys($jobtitles);

                    $conditionsjobtitles = array(
                        "UserOrganization.job_title_id" => $jobtitlesid,
                        "UserOrganization.organization_id" => $org_id,
                        //"UserOrganization.status" => 1, 
                        "Endorsement.organization_id" => $org_id,
                            //"Endorsement.endorsement_for" => "user"   
                    );

                    if ($startdate != "" && $enddate != "") {

                        array_push($conditionsjobtitles, "date(Endorsement.created) between '$startdate' and '$enddate'");
                    } elseif ($startdate != "") {

                        array_push($conditionsjobtitles, "date(Endorsement.created) >= '$startdate'");
                    }
                    $groupjobtitle = array("UserOrganization.job_title_id");
                    $fieldsjobtitle = array("UserOrganization.job_title_id", "count(DISTINCT Endorsement.id)");

                    $jobtitledataendorsed = $this->UserOrganization->find("all", array("conditions" => $conditionsjobtitles, "group" => $groupjobtitle, "fields" => $fieldsjobtitle));

                    $jbiddata = array();
                    foreach ($jobtitledataendorsed as $endorserjbdata) {
                        $jbiddata[$endorserjbdata["UserOrganization"]["job_title_id"]] = $endorserjbdata[0]["count(DISTINCT Endorsement.id)"];
                    }

                    $detailedjobtitlechart = array("data" => $jbiddata, "jobtitles" => $jobtitles);

//echo $this->UserOrganization->getLastQuery();	exit;	
// print_r($detailedjobtitlechart);exit;

                    if (!empty($jbiddata)) {


                        $seriesdata = "";
                        foreach ($jbiddata as $lkey => $lval) {
                            if ($seriesdata == "") {
                                $seriesdata = "{
              name: '" . addslashes($jobtitles[$lkey]) . "',
             y: " . $lval . "}";
                            } else {
                                $seriesdata .= ",{
              name: '" . addslashes($jobtitles[$lkey]) . "',
             y: " . $lval . "}";
                            }
                        }


                        $series = "  {
            name: 'Job Title',
            colorByPoint: true,
            data: [" . $seriesdata . "]}";

//$this->autoRender = false;
                        /* Set up new view that won't enter the ClassRegistry */
                        $view = new View($this, false);
                        $view->set('data', $series);
                        $view->set('type', "Job Title");
                        $view->set('height', $height);
                        $view->set('width', $width);
                        $view->viewPath = 'Elements';

                        /* Grab output into variable without the view actually outputting! */
                        $view_output = $view->render('box');

                        $view_output = str_replace("\r", "", $view_output);
                        $view_output = str_replace("\t", "", $view_output);
                        $view_output = str_replace("\n", "", $view_output);

                        if (isset($this->request->data["web"]) && $this->request->data["web"] == 1) {
                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "Endorsed by job title", "data" => $seriesdata),
                                '_serialize' => array('result')
                            ));
                        } else {

                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "Endorsed by job title", "data" => $view_output),
                                '_serialize' => array('result')
                            ));
                        }
//$this->set(array(
//    'result' => array("status" => true
//        , "msg" => "Endorsed by job title", "data" => $view_output),
//    '_serialize' => array('result')
//));
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "No data available."),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Currently any organization has been not joined."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Currently any organization has been not joined."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "token not exist."),
                '_serialize' => array('result')
            ));
        }
    }

//
    public function endorsementbyentity() {


        if ($this->request->is('post')) {

            $statusConfig = Configure::read("statusConfig");
            $loggedInUser = $this->Auth->user();
            if (isset($loggedInUser['current_org'])) {
                if (isset($this->request->data["org_id"]) && $this->request->data["org_id"] > 0) {
                    $org_id = $this->request->data["org_id"];
                } else {

                    $org_id = $loggedInUser['current_org']['id'];
                }
// $org_id =335;
                if (isset($this->request->data["height"]) && $this->request->data["height"] > 0) {
                    $height = $this->request->data["height"];
                } else {
                    $height = 400;
                }
                if (isset($this->request->data["width"]) && $this->request->data["width"] > 0) {
                    $width = $this->request->data["width"];
                } else {
                    $width = 450;
                }
                $orgtype = "org";
                if (isset($this->request->data["type"]) && $this->request->data["type"] != "") {
                    $orgtype = $this->request->data["type"];
                }
                $user_id = $loggedInUser['id'];
                if (isset($loggedInUser['current_org'])) {

// $org_id = $loggedInUser['current_org']['id'];

                    $params = array();
                    $start_date = "";
                    $end_date = "";
                    if (isset($this->request->data["start_date"]) && $this->request->data["start_date"] != "") {
                        $start_date = $this->request->data["start_date"];
                    }
                    if (isset($this->request->data["end_date"]) && $this->request->data["end_date"] != "") {
                        $end_date = $this->request->data["end_date"];
                    }

                    $startdate = $enddate = "";
                    if ($start_date != "") {

                        $conditionarray["Endorsement.created >= "] = $startdate = date("Y-m-d 00:00:00", $start_date);
                    }
                    if ($end_date != "") {
                        $conditionarray["Endorsement.created <= "] = $enddate = date("Y-m-d 23:59:59", $end_date);
                    }
                    $entityarray = $this->Common->getorgentities($org_id);
                    $conditionsentity = array("Endorsement.endorsement_for" => "entity", "Endorsement.organization_id" => $org_id);
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

//echo $this->Endorsement->getLastQuery();		


                    if (!empty($entityiddata)) {


                        $seriesdata = "";
                        foreach ($entityiddata as $lkey => $lval) {
                            if ($seriesdata == "") {
                                $seriesdata = "{
              name: '" . addslashes($entityarray[$lkey]) . "',
             y: " . $lval . "}";
                            } else {
                                $seriesdata .= ",{
              name: '" . addslashes($entityarray[$lkey]) . "',
             y: " . $lval . "}";
                            }
                        }


                        $series = "  {
            name: 'Entity',
            colorByPoint: true,
            data: [" . $seriesdata . "]}";

//$this->autoRender = false;
                        /* Set up new view that won't enter the ClassRegistry */
                        $view = new View($this, false);
                        $view->set('data', $series);
                        $view->set('type', "Sub Org");
                        $view->set('height', $height);
                        $view->set('width', $width);
                        $view->viewPath = 'Elements';

                        /* Grab output into variable without the view actually outputting! */
                        $view_output = $view->render('box');

                        $view_output = str_replace("\r", "", $view_output);
                        $view_output = str_replace("\t", "", $view_output);
                        $view_output = str_replace("\n", "", $view_output);
//echo $view_output;exit;
                        if (isset($this->request->data["web"]) && $this->request->data["web"] == 1) {
                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "Endorsed by job title", "data" => $seriesdata),
                                '_serialize' => array('result')
                            ));
                        } else {

                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "Endorsed by job title", "data" => $view_output),
                                '_serialize' => array('result')
                            ));
                        }
//$this->set(array(
//    'result' => array("status" => true
//        , "msg" => "Endorsed by Sub Organization", "data" => $view_output),
//    '_serialize' => array('result')
//));
                    } else {
                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "No data available."),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Currently any organization has been not joined."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Currently any organization has been not joined."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "token not exist."),
                '_serialize' => array('result')
            ));
        }
    }

//
    public function faq() {
        $faqdata = $this->globalsettingFaq->find("all", array("order" => "updated DESC"));
        $is_web = 0;
        if (isset($this->request->query["is_web"])) {
            $is_web = 1;
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Faqscheck", "data" => $faqdata),
                '_serialize' => array('result')
            ));
        }
        if ($is_web == 0) {
            $view = new View($this, false);
            $view->viewPath = 'Elements';
            $view->set('style', 'style=padding-left:20px;padding-right:20px;padding-top:20px;');
            $view->set('faq', $faqdata);
            /* Grab output into variable without the view actually outputting! */
            $view_output = $view->render('faqelementother');
//echo $view_output;exit;
            $view_output = str_replace("\r", "", $view_output);
            $view_output = str_replace("\t", "", $view_output);
            $view_output = str_replace("\n", "", $view_output);
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Faqs", "data" => $view_output),
                '_serialize' => array('result')
            ));
        }
    }

    public function sendtermconditions() {

        if (isset($this->request->query["email"])) {
            $email = $this->request->query["email"];
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $subject = "End User License Agreement for nDorse";
//  $viewVars = array("attatched" => 1, "docs" => "termandcondition.pdf");
                $termstext = $this->GlobalSetting->findByKey("tandc");
                $termsmsg = "";
                if (!empty($termstext)) {
                    $termsmsg = $termstext['GlobalSetting']['value'];
                }
                $viewVars = array("terms" => $termsmsg);
                $configVars = serialize($viewVars);

                $emailQueue[] = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => "terms_conditions");
                $this->Email->saveMany($emailQueue);
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "End User License Agreement sent successfully.", "data" => true),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid email address"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "email address key not exist."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getEmojis() {

        $Emojis = Configure::read("Emojis");
        $emojis_url = Router::url('/', true) . EMOJIS_IMAGE_DIR;
        if (strpos($emojis_url, 'localhost') < 0) {
            $emojis_url = str_replace("http", "https", $emojis_url);
        }
        //pr($this->request->data);exit;
        $personalizedBitmoji = 0;
        if (isset($this->request->data['org_id'])) {
            $organizationID = $this->request->data['org_id'];
            $currentOrgData = $this->Organization->find("all", array('fields' => array('personalized_bitmoji_enabled', 'org_bitmojis'), 'conditions' => array('id' => $organizationID)));
            if (!empty($currentOrgData)) {
                if (isset($currentOrgData[0]['Organization']['personalized_bitmoji_enabled'])) {
                    $personalizedBitmoji = $currentOrgData[0]['Organization']['personalized_bitmoji_enabled'];
                    $orgBitmojis = $currentOrgData[0]['Organization']['org_bitmojis'];
                }
            }
        }
//        echo $personalizedBitmoji; exit;
        $BitEmojis = Configure::read("Bitmojis");
        $bit_emojis_url = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
        if (strpos($bit_emojis_url, 'localhost') < 0) {
            $bit_emojis_url = str_replace("http", "https", $bit_emojis_url);
        }
        if ($personalizedBitmoji == 0) {
            $emojis_array = array();
//            $Emojisdata = $this->Emojis->find("all");
//
//            foreach ($Emojisdata as $emojisval) {
//                $emojis_array[] = array("id" => $emojisval["Emojis"]["id"],"image" => $emojisval["Emojis"]["name"], "url" => $emojis_url . $emojisval["Emojis"]["name"]);
//            }

            /* Combining Bitmojis Start */

            $bitemojis_array = array();
            $this->loadModel('Bitmojis');
            $Emojisdata = $this->Bitmojis->find("all", array('conditions' => array('personalized' => 0, 'status' => 1)));


            foreach ($Emojisdata as $emojisval) {
                $bitemojis_array[] = array("personalized" => $emojisval["Bitmojis"]["personalized"], "title" => $emojisval["Bitmojis"]["title"], "image" => $emojisval["Bitmojis"]["name"], "url" => $bit_emojis_url . $emojisval["Bitmojis"]["name"], "id" => $emojisval["Bitmojis"]["id"]);
            }
            if (count($bitemojis_array) > 0) {
                $emojis_array = array_merge($emojis_array, $bitemojis_array);
            }
            /* Combining Bitmojis End */
        } else {
//            pr($orgBitmojis);
            $bitmojisArray = json_decode($orgBitmojis);

            $bitemojis_array = $bitemoji_array = array();
            $this->loadModel('Bitmojis');
            $Emojisdata = $this->Bitmojis->find("all", array('conditions' => array('personalized' => 0, 'status' => 1)));

            $bitemojis_array = $this->Bitmojis->find("all", array('conditions' => array('org_id' => $organizationID, 'personalized' => 1, 'status' => 1)));
//            pr($bitemojis_array); exit;
            foreach ($Emojisdata as $emojisval) {
                $emojis_array[] = array("personalized" => $emojisval["Bitmojis"]["personalized"], "title" => $emojisval["Bitmojis"]["title"], "image" => $emojisval["Bitmojis"]["name"], "url" => $bit_emojis_url . $emojisval["Bitmojis"]["name"], "id" => $emojisval["Bitmojis"]["id"]);
            }
            foreach ($bitemojis_array as $Bitmojisval) {
                $bitemoji_array[] = array("personalized" => $Bitmojisval["Bitmojis"]["personalized"], "title" => $Bitmojisval["Bitmojis"]["title"], "image" => $Bitmojisval["Bitmojis"]["name"], "url" => $bit_emojis_url . $Bitmojisval["Bitmojis"]["name"], "id" => $Bitmojisval["Bitmojis"]["id"]);
//                pr($bitemoji_array);exit;
            }
            if (count($bitemoji_array) > 0) {
                $emojis_array = array_merge($emojis_array, $bitemoji_array);
            }
        }
//                pr($emojis_array);
//                exit; 

        if (count($emojis_array) > 0) {
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Emojis data", "data" => $emojis_array),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "No stickers available", "data" => $emojis_array),
                '_serialize' => array('result')
            ));
        }
//$this->set(array(
//    'result' => array("status" => true
//        , "msg" => "Emojis data", "data" => $Emojis),
//    '_serialize' => array('result')
//));
    }

    public function getOrgEmojis() {

        $Emojis = Configure::read("Emojis");
        $emojis_url = Router::url('/', true) . EMOJIS_IMAGE_DIR;
        if (strpos($emojis_url, 'localhost') < 0) {
            $emojis_url = str_replace("http", "https", $emojis_url);
        }
//        pr($this->request->data);exit;
        $personalizedBitmoji = 0;
        if (isset($this->request->data['org_id'])) {
            $organizationID = $this->request->data['org_id'];
            $currentOrgData = $this->Organization->find("all", array('fields' => array('personalized_bitmoji_enabled', 'org_bitmojis'), 'conditions' => array('id' => $organizationID)));
            if (!empty($currentOrgData)) {
                if (isset($currentOrgData[0]['Organization']['personalized_bitmoji_enabled'])) {
                    $personalizedBitmoji = $currentOrgData[0]['Organization']['personalized_bitmoji_enabled'];
                    $orgBitmojis = $currentOrgData[0]['Organization']['org_bitmojis'];
                }
            }
        }
//        echo $personalizedBitmoji; exit;
        $BitEmojis = Configure::read("Bitmojis");
        $bit_emojis_url = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
        if (strpos($bit_emojis_url, 'localhost') < 0) {
            $bit_emojis_url = str_replace("http", "https", $bit_emojis_url);
        }



        if ($personalizedBitmoji == 0) {
            $emojis_array = array();
//            $Emojisdata = $this->Emojis->find("all");
//
//            foreach ($Emojisdata as $emojisval) {
//                $emojis_array[] = array("id" => $emojisval["Emojis"]["id"],"image" => $emojisval["Emojis"]["name"], "url" => $emojis_url . $emojisval["Emojis"]["name"]);
//            }

            /* Combining Bitmojis Start */

            $bitemojis_array = array();
            $this->loadModel('Bitmojis');
            $Emojisdata = $this->Bitmojis->find("all", array('conditions' => array('personalized' => 0, 'status' => 1)));


            foreach ($Emojisdata as $emojisval) {
                $bitemojis_array[] = array("image" => $emojisval["Bitmojis"]["name"], "url" => $bit_emojis_url . $emojisval["Bitmojis"]["name"], "id" => $emojisval["Bitmojis"]["id"]);
            }
            if (count($bitemojis_array) > 0) {
                $emojis_array = array_merge($emojis_array, $bitemojis_array);
                $emojis_array_all = array_merge($emojis_array, $bitemojis_array);
            }
            /* Combining Bitmojis End */
        } else {
//            pr($orgBitmojis);
            $bitmojisArray = json_decode($orgBitmojis);

            $bitemojis_array = $emojis_array_all = array();
            $this->loadModel('Bitmojis');

            $Emojisdata = $this->Bitmojis->find("all", array('conditions' => array('id' => $bitmojisArray, 'status' => 1), 'order' => array('personalized desc')));
//            pr($Emojisdata);
//            exit;
            foreach ($Emojisdata as $emojisval) {
//                pr($emojisval);
                $emojis_array_all[] = array("image" => $emojisval["Bitmojis"]["name"], "url" => $bit_emojis_url . $emojisval["Bitmojis"]["name"], "id" => $emojisval["Bitmojis"]["id"]);

                if (isset($emojisval["Bitmojis"]["personalized"]) && $emojisval["Bitmojis"]["personalized"] == 1) {
                    $emojis_array["custom"][] = array("image" => $emojisval["Bitmojis"]["name"], "url" => $bit_emojis_url . $emojisval["Bitmojis"]["name"], "id" => $emojisval["Bitmojis"]["id"]);
                } else {

                    $emojis_array["default"][] = array("image" => $emojisval["Bitmojis"]["name"], "url" => $bit_emojis_url . $emojisval["Bitmojis"]["name"], "id" => $emojisval["Bitmojis"]["id"]);
                }
            }
//                pr($emojis_array);
//                exit;
        }

        if (count($emojis_array_all) > 0) {
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Emojis data", "data" => $emojis_array_all, "data_new" => $emojis_array),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "No stickers available", "data" => $emojis_array_all, "data_new" => $emojis_array),
                '_serialize' => array('result')
            ));
        }
//$this->set(array(
//    'result' => array("status" => true
//        , "msg" => "Emojis data", "data" => $Emojis),
//    '_serialize' => array('result')
//));
    }

//Added by Babulal Prasad top get bitmojis lists from aPI Created @25-070-2018
    public function getBitmojis() {

        $Emojis = Configure::read("Bitmojis");
        $emojis_url = Router::url('/', true) . BITMOJIS_IMAGE_DIR;
        if (strpos($emojis_url, 'localhost') < 0) {
            $emojis_url = str_replace("http", "https", $emojis_url);
        }
        $emojis_array = array();
        $this->loadModel('Bitmojis');
        $Emojisdata = $this->Bitmojis->find("all", array('conditions' => array('status' => 1)));

        foreach ($Emojisdata as $emojisval) {
            $emojis_array[] = array("image" => $emojisval["Bitmojis"]["name"], "url" => $emojis_url . $emojisval["Bitmojis"]["name"]);
        }

        if (count($emojis_array) > 0) {
            $this->set(array(
                'result' => array("status" => true
                    , "msg" => "Bitmojis data", "data" => $emojis_array),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "No stickers available", "data" => $emojis_array),
                '_serialize' => array('result')
            ));
        }

//$this->set(array(
//    'result' => array("status" => true
//        , "msg" => "Emojis data", "data" => $Emojis),
//    '_serialize' => array('result')
//));
    }

    public function updateLastAppUsedTime() {
        if ($this->request->is('post')) {
            $loggedInUser = $this->Auth->user();
//echo date("Y-m-d H:i:s"); exit;
//$updated = $this->User->updateAll(array("last_app_used" => "NOW()"), array("id" => $loggedInUser['id']));
            $updated = $this->User->updateAll(array("last_app_used" => "'" . date("Y-m-d H:i:s") . "'"), array("id" => $loggedInUser['id']));
//            pr($updated); exit;
            if ($updated) {
                $this->set(array('result' => array("status" => true
                        , "msg" => "Last app used time updated."),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array('result' => array("status" => false
                        , "msg" => "Last app used time is not updated."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function renewSession() {
        if ($this->request->is('post')) {
            $status = $this->renewToken(true);
            $data = NULL;

            if ($status == 'notoken') {
                $status = false;
                $msg = "Invalid token";
                $isExpired = true;
            } else if ($status == "auto_logout") {
                $status = false;
                $msg = "You have been loggedin to some other device.";
                $isExpired = true;
            } else if ($status == "logout") {
                $status = false;
                $msg = "You are logged out. Please login to continue.";
                $isExpired = true;
            } else {
                $data = $this->Auth->user();
                $status = true;
                $msg = "Renew session successfully";
                $isExpired = false;
            }
            $this->set(array('result' => array("status" => $status,
                    "msg" => $msg,
                    "isExpired" => $isExpired,
                    "data" => $data),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function resetmypassword() {

        if (isset($this->request->data['token'])) {
            $authuser = $this->Auth->user();
            if (isset($this->request->data['current_password']) && $this->request->data['current_password'] != "") {
// print_r($this->request->data);
                $current_password = $this->Auth->password($this->request->data['current_password']);

                $userInfo = $this->User->findById($authuser["id"]);
                $userpasswod = $userInfo['User']['password'];
//echo $current_password;
// echo "<hr>";
//echo $userpasswod;
// echo "<hr>";
                if ($userpasswod != $current_password) {

                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Current password was not entered correctly"),
                        '_serialize' => array('result')
                    ));
// $this->redirect(array('controller' => 'users', 'action' => 'changePassword'));
                } else {

                    $this->User->set($this->request->data);
// edit
                    $this->request->data["id"] = $authuser["id"];

                    $this->User->setValidation('reset_password');
                    if ($this->User->validates()) {
                        $password = $this->request->data["password"];
                        if ($this->User->save($this->request->data)) {

// send email to user for change password
                            $this->set(array(
                                'result' => array("status" => true
                                    , "msg" => "Password updated successfully.", 'data' => true),
                                '_serialize' => array('result')
                            ));
                        } else {
                            $errors = $this->User->validationErrors;
                            $errorsArray = array();

                            foreach ($errors as $key => $error) {
                                $errorsArray[$key] = $error[0];
                            }


                            $this->set(array(
                                'result' => array("status" => false
                                    , "msg" => "Errors!", 'errors' => $errorsArray),
                                '_serialize' => array('result')
                            ));
                        }
                    } else {
// $errors = $this->User->validationErrors;
                        $errors = $this->User->validationErrors;
                        $errorsArray = array();

                        foreach ($errors as $key => $error) {
                            $errorsArray[$key] = $error[0];
                        }

                        $this->set(array(
                            'result' => array("status" => false
                                , "msg" => "Errors!", 'errors' => $errorsArray),
                            '_serialize' => array('result')
                        ));
                    }
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "current password required"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    public function setnewpassword() {
//        pr($this->request->data);
//        if (isset($this->request->data['token'])) {
        if (isset($this->request->data['id']) && $this->request->data['id'] != "") {

            $userInfo = $this->User->findById($this->request->data['id']);
            $this->User->set($this->request->data);
            $this->User->setValidation('reset_password');

            if ($this->User->validates()) {
                $password = $this->request->data["password"];
                if ($this->User->save($this->request->data)) {

// send email to user for change password
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Password updated successfully.", 'data' => true),
                        '_serialize' => array('result')
                    ));
                } else {
                    $errors = $this->User->validationErrors;
                    $errorsArray = array();

                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }


                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Errors!", 'errors' => $errorsArray),
                        '_serialize' => array('result')
                    ));
                }
            } else {
// $errors = $this->User->validationErrors;
                $errors = $this->User->validationErrors;
                $errorsArray = array();
                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Errors!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Invalid User."),
                '_serialize' => array('result')
            ));
        }
//        } else {
//            $this->set(array(
//                'result' => array("status" => false
//                    , "msg" => "Token is missing in request"),
//                '_serialize' => array('result')
//            ));
//        }
    }

    public function acceptTnC() {
        $loggedInUser = $this->Auth->user();
        $this->User->id = $loggedInUser['id'];
        $this->User->saveField("terms_accept", 1);

        $this->set(array(
            'result' => array("status" => true
                , "msg" => "Terms and condition accepted.", 'data' => true),
            '_serialize' => array('result')
        ));
    }

    public function checkSession() {
        $this->set(array(
            'result' => array("status" => true
                , "msg" => "Session exitsts.", 'data' => true),
            '_serialize' => array('result')
        ));
    }

    public function getAcceptedRequests() {
        $loggedInUser = $this->Auth->user();
//        $this->OrgRequests->bindModel(array('belongsTo' => array('Organization')));
//        $acceptedRequests = $this->OrgRequests->find("all", array("conditions" => array("OrgRequests.user_id" => $loggedInUser['id'], "OrgRequests.organization_id" => $loggedInUser['pending_requests'], "OrgRequests.status" => 1)));
//        $acceptedOrgs = array();
//        
//        foreach ($acceptedRequests as $acceptedRequest) {
//            $orgDetails = array();
//            $orgDetails['id'] = $acceptedRequest['Organization']['id'];
//            $orgDetails['name'] = $acceptedRequest['Organization']['name'];
//            $acceptedOrgs[] = $orgDetails;
//        }
        $this->DefaultOrg->unbindModel(array('belongsTo' => array('User')));
        $params = array();
        $params['fields'] = "*";
        $params['joins'] = array(
            array(
                'table' => 'user_organizations',
                'alias' => 'UserOrganization',
                'type' => 'LEFT',
                'conditions' => array(
                    'UserOrganization.user_id = ' . $loggedInUser['id'],
                    'UserOrganization.organization_id = DefaultOrg.organization_id'
                )
            )
        );

        $params['conditions'] = array("DefaultOrg.user_id" => $loggedInUser['id'], "DefaultOrg.status" => 1);

        $defaultOrg = $this->DefaultOrg->find("first", $params);

        if (!empty($defaultOrg)) {
            $defaultOrg['Organization']['joined'] = $defaultOrg['UserOrganization']['joined'];
            if (in_array($defaultOrg['DefaultOrg']['organization_id'], $loggedInUser['pending_requests'])) {
                $this->Session->write('Auth.User.pending_requests', array());
                $msg = "Your join request has been accepted by administrator of nDorse Organization " . $defaultOrg['Organization']['name'] . ". You can go to Live Feed screen of this organization.";
                $type = "request";
            } else {
                $msg = "You have been added to nDorse Organization " . $defaultOrg['Organization']['name'] . ". You can go to Live Feed screen of this organization.";
                $type = "add";
            }

            $statusConfig = Configure::read("statusConfig");
            $userStatus = array_search($defaultOrg['UserOrganization']["status"], $statusConfig);
            $orgStatus = array_search($defaultOrg['Organization']["status"], $statusConfig);
            $roleList = $this->Common->setSessionRoles();
            $userRole = $roleList[$defaultOrg['UserOrganization']['user_role']];

            $orgUpdates = array("user_status" => $userStatus, "org_status" => $orgStatus, "user_role_changed" => false, "user_role" => $userRole, "msg" => $msg);

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => $msg, 'data' => $defaultOrg['Organization'], 'type' => $type, 'org_updates' => $orgUpdates, "button_text" => "Go To Organization"),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Organization not available.", 'data' => array(), 'type' => 'none'),
                '_serialize' => array('result')
            ));
        }
    }

    public function addUser() {
        $email = $this->request->data['email'];
        $userExist = $this->User->findByEmail($email);
        $loggedinUser = $this->Auth->user();
        $statusConfig = Configure::read("statusConfig");
        $sendInvite = false;

        if ($this->request->data['send_invite'] == 1 && $this->request->data["status"] == 1) {
            $sendInvite = true;
        }

        if (empty($userExist)) {
            $roleList = $this->Common->setSessionRoles();

            $user = array();
            $user['fname'] = $this->request->data['fname'];
            $user['lname'] = $this->request->data['lname'];
            $user['email'] = $this->request->data['email'];
            $user['role'] = array_search('endorser', $roleList);
            $user['secret_code'] = $this->getSecretCode("user");
            $user['username'] = $this->request->data['email'];
            $user['last_app_used'] = "NOW()";
            $user['password'] = $this->Common->randompasswordgenerator(8);

            $this->User->setValidation('register');



            $this->User->set($user);
            if ($this->User->validates()) {
                if ($this->User->save()) {
                    $user['id'] = $this->User->id;
                    $template = "invitation_admin";
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "There is some problem in adding user. Please try again!"),
                        '_serialize' => array('result')
                    ));
                    return;
                }
            } else {
                $errors = $this->User->validationErrors;
                $errorsArray = array();

                foreach ($errors as $key => $error) {
                    $errorsArray[$key] = $error[0];
                }

                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Error!", 'errors' => $errorsArray),
                    '_serialize' => array('result')
                ));
                return;
            }
        } else {
            $user = $userExist['User'];
            $template = "invitation_admin_existing";
        }

//        $status = $this->request->data['status'];

        if ($this->request->data['status'] == $statusConfig['active'] || $this->request->data['status'] == $statusConfig['eval']) {
            $statusFields = $this->Common->getNewUserOrgFields($loggedinUser['current_org']['id'], $this->request->data['status']);
        } else {
            $statusFields = array("poolType" => "paid", "status" => 0);
        }

        $newUserOrganization = array(
            "organization_id" => $loggedinUser['current_org']['id'],
            "user_id" => $user['id'],
            "pool_type" => $statusFields['poolType'],
            "status" => $statusFields['status'],
            "flow" => "app_invite",
            "joined" => 0,
            "send_invite" => $sendInvite
        );

        $saved = $this->UserOrganization->save($newUserOrganization);
        $userOrgId = $this->UserOrganization->id;

        $defaultOrg = $this->DefaultOrg->findByUserId($user['id']);

        if ($this->request->data['status'] == $statusConfig['active']) {
            if (empty($defaultOrg)) {
                $defaultOrgData = array("user_id" => $user['id'], "organization_id" => $loggedinUser['current_org']['id'], "status" => 1);
                $this->DefaultOrg->save($defaultOrgData);
            }
        }

        if ($this->request->data['send_invite'] == 1 && $this->request->data['status'] == $statusConfig['active']) {

            $noSwitch = false;
            if (empty($defaultOrg)) {
                $noSwitch = true;
            }

            $joinOrgCode = $this->Common->getJoinOrgCode($loggedinUser['current_org']['id'], $email, $user['id'], $userOrgId);
            $viewVars = array('fname' => $user['fname'], 'username' => $user['username'], 'password' => $user['password'], 'organization_name' => $loggedinUser['current_org']['name'], "join_code" => $joinOrgCode, "no_switch" => $noSwitch);

            /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
            $userIdEncrypted = base64_encode($user['id']);
            $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
            $viewVars["pathToRender"] = $pathToRender;
            /*             * */

            $configVars = serialize($viewVars);
            $subject = "Invitation to join nDorse";
            $emailQueue = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => $template);
            $this->Email->save($emailQueue);
        }

        $this->set(array(
            'result' => array("status" => true
                , "msg" => "User added successfully."),
            '_serialize' => array('result')
        ));
    }

    public function ifUserExist() {
        $loggedinUser = $this->Auth->user();
        if (empty($this->request->data['email'])) {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Please enter email."),
                '_serialize' => array('result')
            ));
            return;
        }

        $user = $this->User->findByEmail($this->request->data['email']);
        if (empty($user)) {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => ""),
                '_serialize' => array('result')
            ));
        } else {
            $orgId = $loggedinUser['current_org']['id'];
            $useOrganization = $this->UserOrganization->find("first", array("conditions" => array("UserOrganization.user_id" => $user['User']['id'], "UserOrganization.organization_id" => $orgId, "UserOrganization.status !=" => 2)));
            if (!empty($useOrganization)) {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "This user is already part of your organization"),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => "This user is already part of nDorse. Please fill other details.", 'data' => array("fname" => $user['User']['fname'], "lname" => $user['User']['lname'])),
                    '_serialize' => array('result')
                ));
            }
        }
    }

    /*     * *Added by Babulal Prasad @ 26122016 
     * To import endorsement Ids into Feed Trans Table
     * This will import only endorment data not the post data
     */

    function enterFeedTransData() {
        echo "DONOT USE IT";
        echo "MADE TO USE IT ONCE";
        exit;
        $this->layout = "ajax";
        $this->autoRender = false;
        $count = $this->Endorsement->find('all', array("fields" => array('count(id) as total_records')));
        $totalCount = 0;
        if (!empty($count) && $count[0][0]['total_records'] != '') {
            $totalCount = $count[0][0]['total_records'];
            $loopCount = ceil($totalCount / 500);
            $offset = 0;
            $limit = 500;
            for ($i = 0; $i < $loopCount; $i++) {
                $endorsementData = $this->Endorsement->find('all', array("fields" => array('id', 'created', 'endorsed_id', 'endorser_id', 'endorsement_for', 'organization_id'), "order" => "id", "offset" => $offset, "limit" => $limit, 'recursive' => 0));
                foreach ($endorsementData as $index => $endorseData) {
                    $endorsed_user_id = array();
                    $feedQueryData[$index]['feed_id'] = $endorseData['Endorsement']['id'];
                    $feedQueryData[$index]['feed_type'] = 'endorse';
                    $feedQueryData[$index]['created'] = $endorseData['Endorsement']['created'];
                    $endorsed_user_id[] = $endorseData['Endorsement']['endorser_id'];
                    if ($endorseData['Endorsement']['endorsement_for'] == 'department' || $endorseData['Endorsement']['endorsement_for'] == 'entity') {
                        $feedQueryData[$index]['dept_id'] = $endorseData['Endorsement']['endorsed_id'];
                    } else if ($endorseData['Endorsement']['endorsement_for'] == 'user') {
                        $endorsed_user_id[] = $endorseData['Endorsement']['endorsed_id'];
                        $feedQueryData[$index]['dept_id'] = "";
                    }
                    $feedQueryData[$index]['org_id'] = $endorseData['Endorsement']['organization_id'];
                    $feedQueryData[$index]['user_id'] = json_encode($endorsed_user_id);
                }
                $offset = $offset + $limit;
//                pr($feedQueryData);
//                exit;
                $this->FeedTran->saveMany($feedQueryData);
            }
            echo "Data Imported Successfully";
        }
    }

    public function getActiveUserList() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
//                pr($this->request->data); 
//                exit;
                $statusConfig = Configure::read("statusConfig");
                $loggedInUser = $this->Auth->user();
//            pr($loggedInUser); exit;
                if (isset($loggedInUser['current_org'])) {
                    $currentOrgId = $loggedInUser['current_org']['id'];
                    $user_id = $loggedInUser['id'];
                    $this->loadModel('DefaultOrg');

                    /*                     * ************************************** */

                    $conditions = array();
                    $conditions['DefaultOrg.organization_id'] = $currentOrgId;
                    $conditions['DefaultOrg.status'] = 1;
                    $conditions['UserOrganization.status'] = 1;
                    $conditions['User.status'] = 1;
                    if (isset($this->request->data['keyword']) && trim($this->request->data['keyword']) != '') {
                        $conditions["CONCAT(User.fname, ' ', User.lname) like "] = "%" . trim($this->request->data['keyword']) . "%";
                    }

                    $this->DefaultOrg->unbindModel(array('belongsTo' => array('Organization', 'User')));
                    $totalUsersCount = $this->DefaultOrg->find('all', array(
                        'fields' => array('count(DefaultOrg.id) as total_records'),
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
                        //'conditions' => array('DefaultOrg.organization_id' => $currentOrgId, 'DefaultOrg.status' => 1, 'UserOrganization.status' => 1, 'User.status' => 1),
                        'conditions' => $conditions
                    ));
//echo $this->DefaultOrg->getLastQuery(); exit;
                    $totalRecords = isset($totalUsersCount[0][0]['total_records']) ? $totalUsersCount[0][0]['total_records'] : 0;
//echo $totalRecords; exit;

                    /*                     * ********************************* */




                    $limit = Configure::read("pageLimit");

                    if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
                        $page = $this->request->data["page"];
                        $offset = $page * $limit;
                    } else {
                        $page = 1;
                        $offset = 0;
                    }
                    $params = array();
                    $totalPage = 0;
                    if (isset($totalRecords) && $totalRecords > 0) {
                        $totalPage = ceil($totalRecords / $limit);
                    }



                    $params['joins'] = array(
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
                        ),
                        array(
                            'table' => 'org_departments',
                            'alias' => 'OrgDepartment',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'UserOrganization.department_id = OrgDepartment.id',
                                'OrgDepartment.status = 1'
                            )
                        )
                    );

                    $params['fields'] = array('DefaultOrg.*', "CONCAT(trim(fname),' ',trim(lname)) as fullname", 'User.*', 'UserOrganization.user_role',
                        'UNIX_TIMESTAMP(User.last_app_used) as last_used_date', 'UNIX_TIMESTAMP() as curr_time', 'OrgDepartment.name');

                    $params['limit'] = $limit;
                    $params['page'] = $page;
                    $params['offset'] = $offset;
                    $params['order'] = 'User.last_app_used desc';

                    $conditionarray["DefaultOrg.organization_id"] = $currentOrgId;
                    $conditionarray["DefaultOrg.status"] = 1;
                    $conditionarray["UserOrganization.status"] = 1;
                    $conditionarray["User.status"] = 1;
                    if (isset($this->request->data['keyword']) && trim($this->request->data['keyword']) != '') {
                        $conditionarray["CONCAT(User.fname, ' ', User.lname) like "] = "%" . trim($this->request->data['keyword']) . "%";
                    }
                    $conditionarray["DefaultOrg.user_id != "] = $user_id;


                    $params['conditions'] = $conditionarray;


                    $this->DefaultOrg->unbindModel(array('belongsTo' => array('Organization', 'User')));
                    $ActiveUsersList = $this->DefaultOrg->find('all', $params);


                    $activeUsers = array();
                    $activeUsers['total_records'] = $totalRecords;
                    $activeUsers['current_page'] = $page;
                    $activeUsers['total_pages'] = $totalPage;
                    $activeUsers['org_id'] = $currentOrgId;


                    /* Adding following list into timley updates */

                    $userID = $loggedInUser['id'];
                    $this->loadModel("UserFollowing");
                    $followingIdsArray = array();
                    $userFollowings = $this->UserFollowing->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $userID, 'status' => 1)));
                    if (!empty($userFollowings)) {
                        $userFollowings = array_shift($userFollowings);
                        $userFollowings = $userFollowings['UserFollowing'];
                        $uFollowingID = $userFollowings['id'];
                        $followingIdsArray = json_decode($userFollowings['following_ids']);
                    }
//                    $returnData['following_users'] = $followingIdsArray;

                    if (isset($ActiveUsersList)) {
                        if (is_array($ActiveUsersList) && count($ActiveUsersList) > 0) {
                            $User = array();
                            foreach ($ActiveUsersList as $index => $UserData) {

//pr($UserData); exit;
                                $User['is_following'] = false;
                                if (in_array($UserData['User']['id'], $followingIdsArray)) {
                                    $User['is_following'] = true;
                                }

                                $User['name'] = $UserData[0]['fullname'];
                                $User['about'] = $UserData['User']['about'];
                                $User['last_used_date'] = $UserData[0]['last_used_date'];
                                $User['curr_time'] = $UserData[0]['curr_time'];
                                $User['user_id'] = $UserData['User']['id'];
                                $User['email'] = $UserData['User']['email'];
                                $User['last_app_used'] = $UserData['User']['last_app_used'];
                                if (isset($UserData['User']['image']) && $UserData['User']['image'] != '') {
                                    $User['image'] = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $UserData['User']['image'];
                                } else {
                                    $User['image'] = '';
                                }


                                $User['id'] = $UserData['User']['id'];
                                $User['dept_name'] = $UserData['OrgDepartment']['name'];
                                $User['user_role'] = $UserData['UserOrganization']['user_role'];
//pr($User); exit;   
                                $activeUsers['user'][] = $User;
                            }
                        }
                    }
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Active User List ",
                            "data" => $activeUsers),
                        '_serialize' => array('result')
                    ));
                } else {

                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getAllPendingListing() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
//                pr($this->request->data); 
//                exit; 
                $statusConfig = Configure::read("statusConfig");
                $loggedInUser = $this->Auth->user();
//                pr($loggedInUser); exit;
                if (isset($loggedInUser['current_org'])) {
                    $currentOrgId = $loggedInUser['current_org']['id'];
                    $user_id = $loggedInUser['id'];
                    /*                     * ** Get All pending notification list START*** */
//                    echo $currentOrgId; exit;
                    $this->loadModel('AlertCenterNotification');
                    $allNotificationsList = $this->AlertCenterNotification->find('all', array('fields' => array('*'),
                        'conditions' => array('user_id' => $user_id, /* 'org_id' => $currentOrgId, */ 'status' => 0)));

                    if (!empty($allNotificationsList)) {
                        $alertCenterNotificationArray = array();
                        foreach ($allNotificationsList as $index => $notificationDATA) {
                            $alertCenterNotificationArray['AlertCenterNotification'][] = $notificationDATA['AlertCenterNotification'];
                        }
                    } else {
//$alertCenterNotificationArray = json_encode((object) null);
                        $alertCenterNotificationArray['AlertCenterNotification'] = array();
                    }
//                    pr($alertCenterNotificationArray);
//                    exit;
                    /*                     * ** Get All pending notification list END*** */


                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Pending Alert Cente Notification.",
                            "data" => $alertCenterNotificationArray),
                        '_serialize' => array('result')
                    ));
                } else {

                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getAllNotificationListing() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
//                pr($this->request->data); 
//                exit; 
                $statusConfig = Configure::read("statusConfig");
                $loggedInUser = $this->Auth->user();
//                pr($loggedInUser); exit;
                if (isset($loggedInUser['current_org'])) {
                    $currentOrgId = $loggedInUser['current_org']['id'];
                    $user_id = $loggedInUser['id'];
                    /*                     * ** Get All pending notification list START*** */
//                    echo $currentOrgId; exit;
                    $this->loadModel('AlertCenterNotification');
                    $allNotificationsList = $this->AlertCenterNotification->find('all', array('fields' => array('*'),
                        'conditions' => array('user_id' => $user_id /* , 'org_id' => $currentOrgId */)));

                    if (!empty($allNotificationsList)) {

                        foreach ($allNotificationsList as $index => $notificationDATA) {
                            $alertCenterNotificationArray['AlertCenterNotification'][] = $notificationDATA['AlertCenterNotification'];
                        }
                    } else {
//                        $alertCenterNotificationArray = json_encode((object) null);
                        $alertCenterNotificationArray['AlertCenterNotification'] = array();
                    }
//                    pr($alertCenterNotificationArray);
//                    exit;
                    /*                     * ** Get All pending notification list END*** */


                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Pending Alert Center Notification.",
                            "data" => $alertCenterNotificationArray),
                        '_serialize' => array('result')
                    ));
                } else {

                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getAllLast15Notifications() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
//                pr($this->request->data); 
//                exit; 
                $statusConfig = Configure::read("statusConfig");
                $loggedInUser = $this->Auth->user();
//                pr($loggedInUser); exit;
                if (isset($loggedInUser['current_org'])) {
                    $currentOrgId = $loggedInUser['current_org']['id'];
                    $user_id = $loggedInUser['id'];
                    /*                     * ** Get All pending notification list START*** */
//                    echo $currentOrgId; exit;
                    $this->loadModel('AlertCenterNotification');
                    $allNotificationsList = $this->AlertCenterNotification->find('all', array('fields' => array('AlertCenterNotification.*', "concat(User.fname,' ',User.lname) as username", 'User.image'),
                        'joins' => array(
                            array(
                                'table' => 'users',
                                'alias' => 'User',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'AlertCenterNotification.sender_id = User.id '
                                )
                            )
                        )
                        ,
                        'conditions' => array('user_id' => $user_id /* , 'org_id' => $currentOrgId */), 'order' => array('AlertCenterNotification.created desc'), 'limit' => 15));

                    if (!empty($allNotificationsList)) {

                        foreach ($allNotificationsList as $index => $notificationDATA) {
                            //pr($notificationDATA); exit;
//                            $img = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/user.png";
                            $img = Router::url('/', true) . "app/webroot/img/user.png";
                            if ($notificationDATA["User"]["image"] != "") {
                                $img = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $notificationDATA["User"]["image"];
                            }
                            $notificationDATA['AlertCenterNotification']['user_image'] = $img;
                            $notificationDATA['AlertCenterNotification']['user_name'] = $notificationDATA[0]["username"];
                            $alertCenterNotificationArray['AlertCenterNotification'][] = $notificationDATA['AlertCenterNotification'];
                        }
                    } else {
//                        $alertCenterNotificationArray = json_encode((object) null);
                        $alertCenterNotificationArray['AlertCenterNotification'] = array();
                    }
//                    pr($alertCenterNotificationArray);
//                    exit;
                    /*                     * ** Get All pending notification list END*** */


                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Last 15 notifications",
                            "data" => $alertCenterNotificationArray),
                        '_serialize' => array('result')
                    ));
                } else {

                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function onViewNotification() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
//                pr($this->request->data);
//                exit;
                $statusConfig = Configure::read("statusConfig");
                $loggedInUser = $this->Auth->user();
//                pr($loggedInUser); exit;
                if (isset($loggedInUser['current_org'])) {
                    $currentOrgId = $loggedInUser['current_org']['id'];
                    $user_id = $loggedInUser['id'];
                    /*                     * ** Get All pending notification list START*** */
//                    echo $currentOrgId; exit;
                    $this->loadModel('AlertCenterNotification');
                    $id = $this->request->data['id'];
                    $aCMotification['AlertCenterNotification']['id'] = $id;
                    $aCMotification['AlertCenterNotification']['status'] = 1; //0= pending, 1= viewed , 2 = cancelled
                    $allID = $this->AlertCenterNotification->save($aCMotification);
//                    $allID = $this->AlertCenterNotification->delete($id);
//                    pr($allID);
//                    exit;
//                    pr($alertCenterNotificationArray);
//                    exit;
                    /*                     * ** Get All pending notification list END*** */
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Alert Cente Notification has been viewed."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function onCancelNotification() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
//                pr($this->request->data);
//                exit;
                $statusConfig = Configure::read("statusConfig");
                $loggedInUser = $this->Auth->user();
//                pr($loggedInUser); exit;
                if (isset($loggedInUser['current_org'])) {
                    $currentOrgId = $loggedInUser['current_org']['id'];
                    $user_id = $loggedInUser['id'];
                    /*                     * ** Get All pending notification list START*** */
//                    echo $currentOrgId; exit;
                    $this->loadModel('AlertCenterNotification');
                    $id = $this->request->data['id'];
                    $aCMotification = array();
                    $aCMotification['AlertCenterNotification']['id'] = $id;
                    $aCMotification['AlertCenterNotification']['status'] = 2; //0= pending, 1= viewed , 2 = cancelled
                    $allID = $this->AlertCenterNotification->save($aCMotification);
//                    pr($allID);
//                    exit;
//                    pr($alertCenterNotificationArray);
//                    exit;
                    /*                     * ** Get All pending notification list END*** */
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Alert Cente Notification has been cancelled."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function getLikesList() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
                $token = $this->request->data['token'];
                $userinfo = $this->getuserData($token, true);
                if (!empty($userinfo)) {
                    $id = $this->request->data['id'];
                    $feedType = $this->request->data['type'];

                    if ($feedType == 'post') {// POST LIKES LIST
                        $limit = Configure::read("pageLimit");
                        if (isset($this->request->query["page"]) && $this->request->query["page"] > 1) {
                            $page = $this->request->query["page"];
                            $offset = $page * $limit;
                        } else {
                            $page = 1;
                            $offset = 0;
                        }

                        $params = array();
                        $params['fields'] = "count(*) as cnt";
                        $conditionarray = array();
                        $conditionarray["PostLike.post_id"] = $id;
                        $params['conditions'] = $conditionarray;
                        $params['order'] = 'User.fname';
                        $params['joins'] = array(
                            array(
                                'table' => 'users',
                                'alias' => 'User',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'User.id =PostLike.user_id '
                                )
                            )
                        );

                        $totalPostLikes = $this->PostLike->find("all", $params);
                        $totalPostLikes = $totalPostLikes[0][0]["cnt"];
                        $totalpage = ceil($totalPostLikes / $limit);
                        $params['fields'] = "concat(User.fname,' ',User.lname) as username,User.image,User.id,PostLike.id";
                        $params['limit'] = $limit;
                        $params['page'] = $page;
                        $params['offset'] = $offset;
                        $PostLikeList = $this->PostLike->find("all", $params);
                        $postLikeArray = array();
                        if (!empty($PostLikeList)) {
                            foreach ($PostLikeList as $index => $postLikedata) {
                                $postLikeArray[$index]['username'] = $postLikedata[0]['username'];
                                $userImage = $postLikedata['User']['image'];
                                if (isset($userImage) && $userImage != '') {
                                    $userImagehttp = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userImage;
                                    $userImage = str_replace("http", "https", $userImagehttp);
                                } else {
                                    $userImagehttp = Router::url('/', true) . "app/webroot/img/user.png";
                                    $userImage = str_replace("http", "https", $userImagehttp);
                                }
                                $postLikeArray[$index]['user_image'] = $userImage;
                                $postLikeArray[$index]['post_like_id'] = $postLikedata['PostLike']['id'];
                                $postLikeArray[$index]['user_id'] = $postLikedata['User']['id'];
                            }
                        }
//                        pr($postLikeArray);
//                        exit;
                        $returnData = array("likes_list" => $postLikeArray, "total_page" => $totalpage, "curr_page" => $page);
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Post Likes List",
                                "data" => $returnData),
                            '_serialize' => array('result')
                        ));
                    } else if ($feedType == 'endorse') { // ENDORSEMENT LIKES LIST
                        $limit = Configure::read("pageLimit");
                        if (isset($this->request->query["page"]) && $this->request->query["page"] > 1) {
                            $page = $this->request->query["page"];
                            $offset = $page * $limit;
                        } else {
                            $page = 1;
                            $offset = 0;
                        }

                        $params = array();
                        $params['fields'] = "count(*) as cnt";
                        $conditionarray = array();
                        $conditionarray["EndorsementLike.endorsement_id"] = $id;
                        $params['conditions'] = $conditionarray;
                        $params['order'] = 'User.fname';
                        $params['joins'] = array(
                            array(
                                'table' => 'users',
                                'alias' => 'User',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'User.id = EndorsementLike.user_id '
                                )
                            )
                        );

                        $totalEndorsementLikes = $this->EndorsementLike->find("all", $params);
                        $totalEndorsementLikes = $totalEndorsementLikes[0][0]["cnt"];
                        $totalpage = ceil($totalEndorsementLikes / $limit);
                        $params['fields'] = "concat(User.fname,' ',User.lname) as username,User.image,User.id,EndorsementLike.id";
                        $params['limit'] = $limit;
                        $params['page'] = $page;
                        $params['offset'] = $offset;
                        $EndorsementLikeList = $this->EndorsementLike->find("all", $params);
                        $endorsementLikeArray = array();
                        if (!empty($EndorsementLikeList)) {
                            foreach ($EndorsementLikeList as $index => $postLikedata) {
                                $endorsementLikeArray[$index]['username'] = $postLikedata[0]['username'];
                                $userImage = $postLikedata['User']['image'];
                                if (isset($userImage) && $userImage != '') {
                                    $userImagehttp = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userImage;
                                    $userImage = str_replace("http", "https", $userImagehttp);
                                } else {
                                    $userImagehttp = Router::url('/', true) . "app/webroot/img/user.png";
                                    $userImage = str_replace("http", "https", $userImagehttp);
                                }
                                $endorsementLikeArray[$index]['user_image'] = $userImage;
                                $endorsementLikeArray[$index]['endorsement_like_id'] = $postLikedata['EndorsementLike']['id'];
                                $endorsementLikeArray[$index]['user_id'] = $postLikedata['User']['id'];
                            }
                        }
//                        pr($endorsementLikeArray);
//                        
                        $returnData = array("likes_list" => $endorsementLikeArray, "total_page" => $totalpage, "curr_page" => $page);
//                        exit;
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Endorsement Likes List",
                                "data" => $returnData),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Invalid token."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    /** Added by Babulal Prasad @27-dec-2018 ** */
    public function getFeaturedVideoList() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
                $token = $this->request->data['token'];
                $userinfo = $this->getuserData($token, true);

                if (!empty($userinfo)) {
                    $orgid = $this->request->data['org_id'];

                    $currentOrgData = $this->Organization->find("all", array('fields' => array('featured_video_enabled'), 'conditions' => array('id' => $orgid)));
//                    pr($currentOrgData ); exit;
                    if (!empty($currentOrgData)) {
                        $featured_video_enabled = $currentOrgData[0]['Organization']['featured_video_enabled'];
                    }
                    $orgVideoListArray = array();
                    $totalVideo = 0;

                    if ($featured_video_enabled == 0) {
                        $returnData = array("org_video_list" => $orgVideoListArray, "total_video" => $totalVideo, 'featured_video_enabled' => $featured_video_enabled);
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Organizations video list.",
                                "data" => $returnData),
                            '_serialize' => array('result')
                        ));
                    } else {
                        $params = array();
                        $params['fields'] = "*";
                        $params['conditions'] = array('org_id' => $orgid, 'status' => 1);
                        $orgActiveVideoListing = $this->OrgVideo->find("all", $params);

                        $totalVideo = count($orgActiveVideoListing);
                        if (!empty($orgActiveVideoListing)) {
                            foreach ($orgActiveVideoListing as $index => $videoList) {

                                $viewedArray = json_decode($videoList['OrgVideo']['viewed_by'], true);
                                $video_url = $videoList['OrgVideo']['video_url'];
                                $videoThumbnail = $videoList['OrgVideo']['thumbnail'];
                                $video_urlhttp = '';
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
                                //$orgVideoListArray[$index] = $videoList['OrgVideo'];
                                if (is_null($viewedArray)) {
                                    $viewedArray = array();
                                }
                                $orgVideoListArray[$index]['id'] = $videoList['OrgVideo']['id'];


                                $orgVideoListArray[$index]['id'] = $videoList['OrgVideo']['id'];
                                $orgVideoListArray[$index]['viewed_by'] = $viewedArray;
                                $orgVideoListArray[$index]['video_url'] = $video_urlhttp;
                                $orgVideoListArray[$index]['thumbnail'] = $videoThumbnailhttp;
                            }
                        }
                        $returnData = array("org_video_list" => $orgVideoListArray, "total_video" => $totalVideo, 'featured_video_enabled' => $featured_video_enabled);
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => "Organizations video list.",
                                "data" => $returnData),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Invalid token."),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    /** Added by Babulal Prasad at 12162016 * */
    public function videoviewed() {
        if ($this->request->is('post')) {

            $videoid = $this->request->data["video_id"];
            if (isset($this->request->data['token'])) {
                $token = $this->request->data['token'];
                $userinfo = $this->getuserData($token, true);
                if (!empty($userinfo)) {
                    $userID = $userinfo['users']['id'];
                    $videoDetails = $this->OrgVideo->find('all', array('fields' => array('viewed_by'), 'conditions' => array('id' => $videoid)));
                    $updateViews = false;
                    if (!empty($videoDetails)) {
                        $viewedByUsers = $videoDetails[0]['OrgVideo']['viewed_by'];
                        if ($viewedByUsers == '') {
                            $viewedByNew = array($userID);
                            $viewedByNew = json_encode($viewedByNew);
                            $updateViews = true;
                        } else {
                            $viewedByUsersDecoded = json_decode($viewedByUsers);
                            if (!in_array($userID, $viewedByUsersDecoded)) {
                                array_push($viewedByUsersDecoded, $userID);
                                $viewedByNew = json_encode($viewedByUsersDecoded);
                                $updateViews = true;
                            }
                        }
                        if ($updateViews) {
                            $this->OrgVideo->id = $videoid;
                            $this->OrgVideo->save(array('viewed_by' => $viewedByNew));
                        }
                    }
                }
            }

            $this->set(array(
                'result' => array("status" => true
                    , "msg" => 'Video viewed',
                ),
                '_serialize' => array('result')
            ));
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    /** Added by Babulal Prasad at 12162016 * */
    public function deleteFeaturedVideo() {
        if ($this->request->is('post')) {
            $videoid = $this->request->data["video_id"];
            if (isset($this->request->data['token'])) {
                $token = $this->request->data['token'];
//              $userinfo = $this->getuserData($token, true);
//              $userID = $userinfo['users']['id'];
                $this->OrgVideo->id = $videoid;
                $this->OrgVideo->save(array('status' => 3));
                $this->set(array(
                    'result' => array("status" => 1
                        , "msg" => 'Video deleted',
                    ),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    /** Added by Babulal Prasad at 13AUG2019
     * LDAP authentication
     * * */
    public function ldapLogin() {
        if ($this->request->is('post')) {
            /* LDAP Auth */
//            username - bunts
//            password-Bunty1!
//            username - rbrajpoot
//            password- Ram1234
//Administrator / Redhat#777!!            

            $org_id = $this->request->data['org_id'];
            $username = $this->request->data['username'];
            $ldap_password = $this->request->data['password'];

            $domainName = 'adtest.in';
            $basedn = 'dc=adtest,dc=in'; //LDAP://adtest.in/DC=adtest,DC=in

            $ldap_dn = "cn=" . $username . $basedn;
            $domain = '@' . $domainName;
//            $ldap_connect = ldap_connect("ldap://adtest.in");
//            ldap_set_option($ldap_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
            $ldap_connect = ldap_connect("ldap://" . $domainName, 636);
//            echo $ldap_connect;

            if (!$ldap_connect) {
                echo 'Could not connect to LDAP server dfsdgsdfg .';
            }

            ldap_set_option($ldap_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_connect, LDAP_OPT_REFERRALS, 0);

            //if (ldap_bind($ldap_connect, $ldap_dn, $ldap_password)) {
            if (@ldap_bind($ldap_connect, $username . $domain, $ldap_password)) {
//                echo "Authenticated Successfully";
                $returnData = $this->checkNDorseLogin($ldap_connect, $username, $basedn, $domain, $org_id);
//                pr($returnData);
//                exit;
                if ($returnData['type'] == 'login') {
                    $this->set(array(
                        'result' => array("status" => true, 'data' => $returnData['data']),
                        '_serialize' => array('result')
                    ));
                } elseif ($returnData['type'] == 'signup') {

                    $this->set(array(
                        'result' => array("status" => true, 'data' => $returnData['data'], 'msg' => $returnData['msg']),
                        '_serialize' => array('result')
                    ));
//                    $this->set(array(
//                        'result' => array("status" => true, 'data' => $returnData['data'], 'isDefault' => $returnData['isDefault'], 'isActive' => $returnData['isActive'], 'msg' => $returnData['msg']),
//                        '_serialize' => array('result')
//                    ));
                }
            } else {
                $errorMsg = ldap_error($ldap_connect);
                if ($errorMsg == "Can't contact LDAP server") {
                    $errorMsg = 'Unable to connect server.';
                }
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => $errorMsg),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function checkNDorseLogin($ad, $samaccountname, $basedn, $domain, $org_id) {
        $sr = ldap_search($ad, $basedn, "(userprincipalname={$samaccountname}{$domain})");
        $entry = ldap_first_entry($ad, $sr);
        $user_email = '';
        do {
            $value = ldap_get_values($ad, $entry, "mail");
            $uniqueAccountName = $samaccountname;
            $givenname = ldap_get_values($ad, $entry, "givenname");
            $snName = ldap_get_values($ad, $entry, "sn");
            if (isset($value[0])) {
                $user_email = $value[0];
            }

            if (isset($givenname[0])) {
                $firstName = $givenname[0];
            }
            if (isset($snName[0])) {
                $lastName = $snName[0];
            }
        } while ($entry = ldap_next_entry($ad, $entry));

        $entries = ldap_get_entries($ad, $sr);
//        pr($entries);
//        pr($user_email);
//        pr($uniqueAccountName);
        $organizationData = $this->Organization->findById($org_id);
        $organization = $organizationData['Organization'];

//        pr($entries);
//        exit;

        $userData = $this->User->find('first', array('conditions' => array('User.email' => $user_email)));

//        pr($userData);
//        exit;
        if (!empty($userData)) {
            /*             * *** LOGIN ON NDORSE SERVER SESSION***** */

            $roleList = $this->Common->setSessionRoles();

            $this->User->id = $userData['User']['id'];
            $saved = $this->User->saveField("ad_accountname", $uniqueAccountName);

            if ($saved) {
                $userData['User']['ad_accountname'] = $uniqueAccountName;
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "The third party account you are using cannot be associated with your existing account. Please try  again."),
                    '_serialize' => array('result')
                ));
                return;
            }

//            if (!empty($userData['User']['ad_accountname'])) {
//                $logginSuccess = $this->Auth->login($userData['User']);
//            } else {
//                $this->set(array(
//                    'result' => array("status" => false
//                        , "msg" => "The third party account you are using is not configured correctly."),
//                    '_serialize' => array('result')
//                ));
//                return;
//            }

            if ($logginSuccess) {

                $loggedinUserId = $this->Auth->user('id');
//                    pr($loggedinUserId);exit;
                $token = $this->generateToken($loggedinUserId);
                $this->Session->write('Auth.User.token', $token);
                $params = array();
                $params['fields'] = "*";
                $params['joins'] = array(
                    array(
                        'table' => 'user_organizations',
                        'alias' => 'UserOrganization',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'UserOrganization.user_id = ' . $loggedinUserId,
                            'UserOrganization.organization_id = DefaultOrg.organization_id'
                        )
                    )
                );

                $params['conditions'] = array("DefaultOrg.user_id" => $loggedinUserId);
                $params['order'] = 'UserOrganization.id desc';

                $defaultOrganization = $this->DefaultOrg->find("first", $params);

                $statusConfig = Configure::read("statusConfig");

                if (!empty($defaultOrganization) && !empty($defaultOrganization['UserOrganization']) && isset($defaultOrganization['UserOrganization']['id'])) {
                    $orgUpdates = array();
                    $currentOrg = $defaultOrganization['Organization'];

                    $currentOrg['joined'] = $defaultOrganization['UserOrganization']['joined'];

                    if ($defaultOrganization['UserOrganization']['entity_id'] > 0) {

                        $entity = $this->getOrgValues($currentOrg["id"], "Entity", true, array($defaultOrganization['UserOrganization']['entity_id']));
                        if (!empty($entity)) {
                            $currentOrg['entity'] = $entity[0]["name"];
                        } else {
                            $currentOrg['entity'] = "";
                        }
                    } else {
                        $currentOrg['entity'] = "";
                    }

                    if ($defaultOrganization['UserOrganization']['department_id'] > 0) {

                        $department = $this->getOrgValues($currentOrg["id"], "OrgDepartments", true, array($defaultOrganization['UserOrganization']['department_id']));

                        if (!empty($department)) {
                            $currentOrg['department'] = $department[0]["name"];
                        } else {
                            $currentOrg['department'] = "";
                        }
                    } else {
                        $currentOrg['department'] = "";
                    }
                    if ($defaultOrganization['UserOrganization']['job_title_id'] > 0) {
                        $jobtitle = $this->getOrgValues($currentOrg["id"], "OrgJobTitles", 1, array($defaultOrganization['UserOrganization']['job_title_id']));

                        if (!empty($jobtitle)) {
                            $currentOrg['job_title'] = $jobtitle[0]["name"];
                        } else {
                            $currentOrg['job_title'] = "";
                        }
                    } else {
                        $currentOrg['job_title'] = "";
                    }

                    $currentOrg['org_role'] = $roleList[$defaultOrganization['UserOrganization']['user_role']];

                    $currentOrg['status'] = array_search($defaultOrganization['UserOrganization']["status"], $statusConfig);

                    if ($currentOrg["image"] != "") {
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $currentOrg["image"] = $rootUrl . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $currentOrg["image"];
                    }

                    $msg = "";
                    $userStatus = array_search($defaultOrganization['UserOrganization']["status"], $statusConfig);
                    $orgStatus = array_search($defaultOrganization['Organization']["status"], $statusConfig);
                    if ($defaultOrganization['Organization']['status'] != $statusConfig['active']) {
                        if ($defaultOrganization['Organization']['status'] == $statusConfig['inactive']) {
                            $msg = "Default Organization inactivated.";
                        } else {
                            $msg = "Default Organization deleted!";
                        }
                    } else if ($defaultOrganization['UserOrganization']['status'] != $statusConfig['active']) {
                        if ($defaultOrganization['UserOrganization']['status'] == $statusConfig['inactive'] || $defaultOrganization['UserOrganization']['status'] == $statusConfig['eval']) {
                            $msg = "nDorse access inactivated for default Organization. Contact Organization Admin.";
                        } else if ($defaultOrganization['UserOrganization']['status'] == $statusConfig['deleted']) {
                            $msg = "You have been deleted from your default nDorse Organization. Contact Organization Admin.";
                        }
                    } else {
                        $this->Session->write('Auth.User.current_org', $currentOrg);
                    }
                    $orgUpdates = array("msg" => $msg, 'user_status' => $userStatus, 'org_status' => $orgStatus);
                    $loggedInUser = $returnData = $this->Auth->user();
                } else {
                    $loggedInUser = $returnData = $this->Auth->user();
                }

                if (isset($orgUpdates)) {
                    $returnData['org_updates'] = $orgUpdates;
                    $returnData['current_org'] = $currentOrg;
                }

                if ($returnData["image"] != "") {
                    $rootUrl = Router::url('/', true);
                    $rootUrl = str_replace("http", "https", $rootUrl);
                    $returnData["image"] = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $returnData["image"];
                }
                if (strtotime($returnData["dob"]) > 0) {
                    $returnData["dob"] = date("m/d/Y", strtotime($returnData["dob"]));
                } else {
                    $returnData["dob"] = "";
                }
//
                $source = 'activedirectory';
                if ($source == "activedirectory") {
                    unset($returnData["source_id"]);
                    unset($returnData["fb_id"]);
                    unset($returnData["gplus_id"]);
                    unset($returnData["lin_id"]);
                }

                if (empty($returnData['fname']) || empty($returnData['lname'])) {
                    $returnData['profile_updated'] = false;
                } else {
                    $returnData['profile_updated'] = true;
                }
//                    pr($returnData); exit;
                if (empty($returnData['fname']) || empty($returnData['lname']) || empty($returnData['mobile']) || empty($returnData['country']) || empty($returnData['street']) || empty($returnData['zip']) || empty($returnData['state']) || empty($returnData['city'])) {
                    $returnData['profile_completed'] = false;
                } else {
                    $returnData['profile_completed'] = true;
                }

                $updated = $this->User->updateAll(array("last_app_used" => "NOW()"), array("id" => $loggedInUser['id']));

//Get pending request organizations
                $pendingRequests = $this->OrgRequests->find("all", array("conditions" => array("user_id" => $loggedInUser['id'], "status" => 0)));
                $pendingRequestOrgs = array();
                foreach ($pendingRequests as $pendingRequest) {
                    $pendingRequestOrgs[] = $pendingRequest['OrgRequests']['organization_id'];
                }

                $this->Session->write('Auth.User.pending_requests', $pendingRequestOrgs);

                $returnData['pending_requests'] = $pendingRequestOrgs;
                //pr($returnData); exit;
//                    $a = $this->Auth->user();
//                    $returnData['auth_user'] = $a;
//                echo "<hr />";
                $returnArray['data'] = $returnData;
                $returnArray['msg'] = 'Succussfully Logged in';
                $returnArray['type'] = 'login';
//                pr($returnArray); exit;
                return $returnArray;
            }
        } else {

            $returnData['data'] = '';
            $returnData['msg'] = 'You are not authorized to access. Please contact your system administrator.';
            $returnData['type'] = 'signup';

//                    pr($returnData); exit;
            return $returnData;


            //Create user in our DataBase and assign the organization
            unset($this->User->validate['image']);
            $roleList = $this->Common->setSessionRoles();

            $userAdDATA['User']['fname'] = $firstName;
            $userAdDATA['User']['lname'] = $lastName;
            $userAdDATA['User']['ad_accountname'] = $uniqueAccountName;
            $userAdDATA['User']['source'] = 'activedirectory';
            $userAdDATA['User']['email'] = $user_email;
            $userAdDATA['User']['username'] = $user_email;
            $userAdDATA['User']['role'] = array_search('endorser', $roleList);
            $userAdDATA['User']['secret_code'] = $this->getSecretCode("user");
            $userAdDATA['User']['last_app_used'] = "NOW()";

//            echo "CHECK AND SAVE USER DATA";
//            pr($userAdDATA); exit;

            $this->User->setValidation('register');
            $this->User->set($userAdDATA);

            if ($this->User->validates()) {
//                pr($userAdDATA);
//                exit;
                if ($userData = $this->User->save(null, false)) {
                    $userRes = $this->User->findById($this->User->id);
//                    pr($userRes); exit;
                    $userAdDATA['User']['id'] = $this->User->id;
                    $loggedinUserId = $this->User->id;
                    $token = $this->generateToken($loggedinUserId);
                    $this->Auth->login($userRes['User']);
                    $this->Session->write('Auth.User.token', $token);
                    $returnData['data'] = $this->Auth->user();
//                    pr($returnData); exit;
//                    $returnData['role'] = $roleList[$returnData['role']];

                    /*                     * ** ASSIGNING ORG CODE START **** */
                    $statusConfig = Configure::read("statusConfig");
                    $params = array();
                    $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                    $params['group'] = 'pool_type';
                    $params['fields'] = array("UserOrganization.pool_type", "COUNT(UserOrganization.id) as count");
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


                    if ($freeCount >= FREE_POOL_USER_COUNT) {
                        $poolType = "paid";

                        $params = array();
                        $conditions = array();
                        $todayDate = date('Y-m-d H:i:s');
//                    $conditions['start_date <='] = $todayDate;
//                    $conditions['end_date >='] = $todayDate;
                        $conditions['Subscription.status'] = 1;
                        $conditions['Subscription.organization_id'] = $org_id;
                        $params['conditions'] = $conditions;
                        $currentSubscription = $this->Subscription->find("first", $params);
                        if (!empty($currentSubscription)) {
                            $poolPurchased = $currentSubscription['Subscription']['pool_purchased'];

                            if ($paidCount >= $poolPurchased) {
//$status = $statusConfig['invite_inactive'];
                                $status = $statusConfig['inactive'];
                            } else {
                                $status = $statusConfig['active'];
                            }
                        } else {
//$status = $statusConfig['invite_inactive'];
                            $status = $statusConfig['inactive'];
                        }
                    } else {
                        $poolType = "free";
                        $status = $statusConfig['active'];
                    }
                }

                $newUserOrganization = array(
                    "organization_id" => $org_id,
                    "user_id" => $loggedinUserId,
                    "pool_type" => $poolType,
                    "status" => $status,
                    "flow" => "creator",
                    "joined" => 1
                );

                $newUserOrganization['send_invite'] = 0;

                $newUserOrganization['user_id'] = $loggedinUserId;

                $isDefault = false;
                $saved = $this->UserOrganization->save($newUserOrganization);
                if (!isset($newUserOrganization['id'])) {
                    $userOrgId = $this->UserOrganization->id;
                }
                $currentOrg = array();
                if ($saved) {

                    $organization['org_role'] = 'endorser';
                    $organization['joined'] = "1";
                    $isActive = false;
                    if (empty($currentOrg)) {
                        $defaultOrg = array("organization_id" => $org_id, "user_id" => $loggedinUserId);
                        $this->DefaultOrg->save($defaultOrg);

                        $isDefault = true;
                    }

                    if ($status == $statusConfig['active']) {
                        if ($isDefault) {
                            $currentOrg = $organization;
                            $this->Session->write('Auth.User.current_org', $currentOrg);
                        }

                        $msg = "You have successfully joined the Organization!!";
                        $isActive = true;
                    } else if ($status == $statusConfig['eval']) {
                        $msg = "You have successfully joined the Organization, but you are in evaluation mode.";
//                        $this->set(array(
//                            'result' => array("status" => true
//                                , "msg" => $msg, "data" => $organization, "isDefault" => $isDefault, "isActive" => false),
//                            '_serialize' => array('result')
//                        ));
                    } else {
                        $msg = "You have successfully joined " . $organization['name'] . "! Your status is inactive. To activate your status, contact your Organization Admin to purchase additional subscription.";
//                        $isActive = false;
//                        $admin = $this->User->findById($organization['admin_id']);
//
//                        $subject = "Purchase subscription";
//                        $template = "less_subscription_admin";
//
//                        /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
//                        $userIdEncrypted = base64_encode($organization['admin_id']);
//                        $rootUrl = Router::url('/', true);
//                        $rootUrl = str_replace("http", "https", $rootUrl);
//                        $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
//                        /*                         * * */
//
//
//                        $viewVars = serialize(array("org_name" => $organization['name'], 'user' => $loggedinUser, "pathToRender" => $pathToRender));
//                        $to = $admin['User']['email'];
////$this->Common->sendEmail($admin['User']['email'], $subject, $template, $viewVars);
//                        $email = array("to" => $to, "subject" => $subject, "config_vars" => $viewVars, "template" => $template);
//  $this->Email->save($email);
//                        $this->set(array(
//                            'result' => array("status" => false
//                                , "msg" => $msg, "isDefault" => $isDefault, "isActive" => false),
//                            '_serialize' => array('result')
//                        ));
                    }

//                    $orgReturnData = array("Organization" => $organization);
                    $returnData['data']['current_org'] = $organization;
                    $returnData['isDefault'] = $isDefault;
                    $returnData['isActive'] = $isActive;
                    $returnData['msg'] = $msg;
                    $returnData['type'] = 'signup';
//                    pr($returnData); exit;
                    return $returnData;
//                    $this->set(array(
//                        'result' => array("status" => true
//                            , "msg" => $msg, "data" => $orgReturnData, "isDefault" => $isDefault, "isActive" => $isActive),
//                        '_serialize' => array('result')
//                    ));
//                    return;
                    /*                     * ** ASSIGNING ORG END **** */
                } else {
                    $errors = $this->User->validationErrors;
                    $errorsArray = array();

                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }
                }

                /**/
            }

            /*             * ******************************** */
        }
    }

    public function getOrgShortCode() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['short_code'])) {
                $short_code = trim($this->request->data['short_code']);
                if (strtolower($short_code) == 'lgh') {
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Organization ADFS link.", 'adfs_link' => 'https://sso.ndorse.net/simplesaml/module.php/core/authenticate.php?as=ndorse-sp'),
//                            , "msg" => "Organization ADFS link.", 'adfs_link' => 'https://adfs.lgh.org/adfs/ls/?SAMLRequest=nZLNTsMwEIRfJfI9ceKqKbGaSoUeqFRE1QYOXJBjb36k2A5eB%2FH4pCmI9tIDJ3t2%2FY12R16i0F3P14NvzAE%2BBkAffOnOIJ8aORmc4VZgi9wIDci95Mf1046zKOa9s95K25EL5DYhEMH51hoSbDc5eZ8tykpKgPlMqgUrY5nOlRqvUKVllmWKJVUSlykwRoJXcDiSORmNRhxxgK1BL4wfS3GShXEWJmmRMB7f8Rl7I8Fm3KY1wk9U432PnFKhKoy6uomsqydBO6QkWP9O9mANDhrcEdxnK%2BHlsPtjEW0knKyFh0haTbHVfQentam2augg6pueThrPJwuFxKlqlHUIIfYk2P%2Fkdt8a1Zr6dmTl%2BRHyx6LYh%2FvnY0FWy5M1nyJwq%2F8Np8ELJby4nm1JL53P6vp3rL4B&RelayState=https%3A%2F%2Fsso.arcgate.com%2Fsimplesaml%2Fmodule.php%2Fcore%2Fpostredirect.php%3FRedirId%3D_b8673e00349e9b43d3548a82b5bd1505e29354e422&SigAlg=http%3A%2F%2Fwww.w3.org%2F2001%2F04%2Fxmldsig-more%23rsa-sha256&Signature=OtfL1UN8gU52Z9ArVE6hGyDTeb8wWzUsZT2QF50HUcz%2FnA51LJ6cW4fc%2Bpx%2F%2Fpmy6BENflp3Z7y10D9unVBHanzb2hI97oH%2B%2BGmHUR4NFODFPSV%2BeahewIsPH48o%2FoPesM7DLu9j1ai3%2F83T1zo8EfgieFfC8204FQtcwoX5Z8qk1kCmJOwfBItaMGKyudJ0%2FJswSghPTTmCRQAD9Q9Zg0BfqkqxweiHhpzROQdhH4cellk96r6LB%2BTSYCAlMRVzkmXOoiaerSsh4cBlhI4g3od8U%2Brjg4ynHcc%2F1ivBolRPMg88f7YBMtWmEMgtQ6N3wFi%2FrjRZP2BzYxTpUq1bYQ%3D%3D'),
                        '_serialize' => array('result')
                    ));
                } elseif (strtolower($short_code) == 'lcmc') {
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Organization ADFS link.", 'adfs_link' => 'https://sso.ndorse.net/simplesaml/module.php/core/authenticate.php?as=lcmch-sp'),
                        '_serialize' => array('result')
                    ));
                } elseif (strtolower($short_code) == 'arc') {
                    $this->set(array(
                        'result' => array("status" => true
                            , "msg" => "Organization ADFS link.", 'adfs_link' => 'https://sso.ndorse.net/simplesaml/module.php/core/authenticate.php?as=arcgate-sp'),
                        '_serialize' => array('result')
                    ));
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Wrong Organization Code.", 'adfs_link' => ''),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Organization short code missing."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    public function ADFSClientLogin() {

        if ($this->request->is('post')) {
//            pr($this->request->data);
//            exit;
//            exit;
            $params = json_decode($this->request->data['params'], true);
//            echo "params : ";
//            pr($params);

            $authorityname = '';
            if (isset($this->request->data['authorityname'])) {
                $authorityname = $this->request->data['authorityname'];
            }
//            echo "<br/> authorityname : " . $authorityname;
//            exit;
            $this->request->data = $params;
            $UPN = reset($this->request->data);

            if (!empty($UPN) && is_array($UPN)) {
                $UPN = $UPN[0];
            }

            if ($authorityname == 'lcmch-sp') { // For LCMC Health SSO
                $org_id = 425; //Tested with LCMC Organzation on LIVE server
//                $org_id = 171; //Tested with LCMC Organzation on LOCAL machine
//                $org_id = 247; //Tested with LCMC Organzation on Staging Server
            } else { // For LGH Health SSO
                $org_id = 415; //Tested with LGH Organzation on LIVE server
            }
//            echo $org_id; exit;
            //            $org_id = 148; //Tested with ARCGATE Organzation on Local 
//            $org_id = 2; //Tested with LGH Organzation on STAGING server
//            $org_id = 415; //Tested with LGH Organzation on LIVE server
//            exit;
//            $requestJson = json_encode($this->request->data);
//            $path = 'uploads' . DS . 'profile';
//            $filePath = $file = $path . DS . 'requestedjson.json';
//            //Writting on json file
//            unlink($file);
//            $file = new File($file, true);
//            $file->write($requestJson);
//            
            $user_email = $lastName = $uniqueAccountName = $dept = $uid = '';
            $firstName = '';
            $userData = array();
            if (isset($this->request->data['uid'][0]) && $this->request->data['uid'][0] != '') {
                $uid = $uniqueAccountName = $this->request->data['uid'][0];
            }
            if (isset($uniqueAccountName) && $uniqueAccountName != '') {
//                $user_email = $this->request->data['mail'][0];
                $userData = $this->User->find('first', array('conditions' => array('User.ad_uid' => $uniqueAccountName)));
            }

            if (isset($this->request->data['mail'][0]) && $this->request->data['mail'][0] != '') {
                $user_email = $this->request->data['mail'][0];
            }
            if (isset($this->request->data['dept'][0]) && $this->request->data['dept'][0] != '') {
                $dept = $this->request->data['dept'][0];
            }
            if (isset($this->request->data['FirstName'][0]) && $this->request->data['FirstName'][0] != '') {
                $firstName = $this->request->data['FirstName'][0];
            }
            if (isset($this->request->data['lastname'][0]) && $this->request->data['lastname'][0] != '') {
                $lastName = $this->request->data['lastname'][0];
            }
            if (isset($this->request->data['uid'][0]) && $this->request->data['uid'][0] != '') {
                $uniqueAccountName = $this->request->data['uid'][0];
            }




            $organizationData = $this->Organization->findById($org_id);
            $organization = $organizationData['Organization'];
//            pr($userData); exit;
            if (!empty($userData)) {
                /*                 * *** LOGIN ON NDORSE SERVER SESSION***** */

                $roleList = $this->Common->setSessionRoles();
//                pr($roleList); exit;
                $this->User->id = $userData['User']['id'];
                $saved = $this->User->saveField("ad_accountname", $uniqueAccountName);
//                pr($saved); exit;
                if ($saved) {
                    $userData['User']['ad_accountname'] = $uniqueAccountName;
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "The third party account you are using cannot be associated with your existing account. Please try  again."),
                        '_serialize' => array('result')
                    ));
                    return;
                }

                if (!empty($userData['User']['ad_accountname'])) {
                    $logginSuccess = $this->Auth->login($userData['User']);
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "The third party account you are using is not configured correctly."),
                        '_serialize' => array('result')
                    ));
                    return;
                }

                if ($logginSuccess) {

                    $loggedinUserId = $this->Auth->user('id');
//                    pr($loggedinUserId);exit;
                    $token = $this->generateToken($loggedinUserId);
                    $this->Session->write('Auth.User.token', $token);
                    $params = array();
                    $params['fields'] = "*";
                    $params['joins'] = array(
                        array(
                            'table' => 'user_organizations',
                            'alias' => 'UserOrganization',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'UserOrganization.user_id = ' . $loggedinUserId,
                                'UserOrganization.organization_id = DefaultOrg.organization_id'
                            )
                        )
                    );

                    $params['conditions'] = array("DefaultOrg.user_id" => $loggedinUserId);
                    $params['order'] = 'UserOrganization.id desc';

                    $defaultOrganization = $this->DefaultOrg->find("first", $params);
//                    pr($defaultOrganization); exit;
                    $statusConfig = Configure::read("statusConfig");

                    if (!empty($defaultOrganization) && !empty($defaultOrganization['UserOrganization']) && isset($defaultOrganization['UserOrganization']['id'])) {
                        $orgUpdates = array();
                        $currentOrg = $defaultOrganization['Organization'];

                        $currentOrg['joined'] = $defaultOrganization['UserOrganization']['joined'];

                        if ($defaultOrganization['UserOrganization']['entity_id'] > 0) {

                            $entity = $this->getOrgValues($currentOrg["id"], "Entity", true, array($defaultOrganization['UserOrganization']['entity_id']));
                            if (!empty($entity)) {
                                $currentOrg['entity'] = $entity[0]["name"];
                            } else {
                                $currentOrg['entity'] = "";
                            }
                        } else {
                            $currentOrg['entity'] = "";
                        }

                        if ($defaultOrganization['UserOrganization']['department_id'] > 0) {

                            $department = $this->getOrgValues($currentOrg["id"], "OrgDepartments", true, array($defaultOrganization['UserOrganization']['department_id']));

                            if (!empty($department)) {
                                $currentOrg['department'] = $department[0]["name"];
                            } else {
                                $currentOrg['department'] = "";
                            }
                        } else {
                            $currentOrg['department'] = "";
                        }
                        if ($defaultOrganization['UserOrganization']['job_title_id'] > 0) {
                            $jobtitle = $this->getOrgValues($currentOrg["id"], "OrgJobTitles", 1, array($defaultOrganization['UserOrganization']['job_title_id']));

                            if (!empty($jobtitle)) {
                                $currentOrg['job_title'] = $jobtitle[0]["name"];
                            } else {
                                $currentOrg['job_title'] = "";
                            }
                        } else {
                            $currentOrg['job_title'] = "";
                        }

                        $currentOrg['org_role'] = $roleList[$defaultOrganization['UserOrganization']['user_role']];

                        $currentOrg['status'] = array_search($defaultOrganization['UserOrganization']["status"], $statusConfig);

                        $currentOrg['subcenter_id'] = $defaultOrganization['UserOrganization']["subcenter_id"];

                        $currentOrg['authority_name'] = $authorityname;


                        if ($currentOrg["image"] != "") {
                            $rootUrl = Router::url('/', true);
                            $rootUrl = str_replace("http", "https", $rootUrl);
                            $currentOrg["image"] = $rootUrl . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $currentOrg["image"];
                        }

                        $msg = "";
                        $userStatus = array_search($defaultOrganization['UserOrganization']["status"], $statusConfig);
                        $orgStatus = array_search($defaultOrganization['Organization']["status"], $statusConfig);
                        if ($defaultOrganization['Organization']['status'] != $statusConfig['active']) {
                            if ($defaultOrganization['Organization']['status'] == $statusConfig['inactive']) {
                                $msg = "Default Organization inactivated.";
                            } else {
                                $msg = "Default Organization deleted!";
                            }
                        } else if ($defaultOrganization['UserOrganization']['status'] != $statusConfig['active']) {
                            if ($defaultOrganization['UserOrganization']['status'] == $statusConfig['inactive'] || $defaultOrganization['UserOrganization']['status'] == $statusConfig['eval']) {
                                $msg = "nDorse access inactivated for default Organization. Contact Organization Admin.";
                            } else if ($defaultOrganization['UserOrganization']['status'] == $statusConfig['deleted']) {
                                $msg = "You have been deleted from your default nDorse Organization. Contact Organization Admin.";
                            }
                        } else {
                            $this->Session->write('Auth.User.current_org', $currentOrg);
                        }
                        $orgUpdates = array("msg" => $msg, 'user_status' => $userStatus, 'org_status' => $orgStatus);
                        $loggedInUser = $returnData = $this->Auth->user();
                    } else {
                        $loggedInUser = $returnData = $this->Auth->user();
                    }

                    if (isset($orgUpdates)) {
                        $returnData['org_updates'] = $orgUpdates;
                        $returnData['current_org'] = $currentOrg;
                    }

                    if ($returnData["image"] != "") {
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $returnData["image"] = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $returnData["image"];
                    }
                    if (strtotime($returnData["dob"]) > 0) {
                        $returnData["dob"] = date("m/d/Y", strtotime($returnData["dob"]));
                    } else {
                        $returnData["dob"] = "";
                    }
//
                    $source = 'ADFS';
                    if ($source == "ADFS") {
                        unset($returnData["source_id"]);
                        unset($returnData["fb_id"]);
                        unset($returnData["gplus_id"]);
                        unset($returnData["lin_id"]);
                    }

                    if (empty($returnData['fname']) || empty($returnData['lname'])) {
                        $returnData['profile_updated'] = false;
                    } else {
                        $returnData['profile_updated'] = true;
                    }
//                    pr($returnData); exit;
                    if (empty($returnData['fname']) || empty($returnData['lname']) || empty($returnData['mobile']) || empty($returnData['country']) || empty($returnData['street']) || empty($returnData['zip']) || empty($returnData['state']) || empty($returnData['city'])) {
                        $returnData['profile_completed'] = false;
                    } else {
                        $returnData['profile_completed'] = true;
                    }

                    $updated = $this->User->updateAll(array("last_app_used" => "NOW()"), array("id" => $loggedInUser['id']));

//Get pending request organizations
                    $pendingRequests = $this->OrgRequests->find("all", array("conditions" => array("user_id" => $loggedInUser['id'], "status" => 0)));
                    $pendingRequestOrgs = array();
                    foreach ($pendingRequests as $pendingRequest) {
                        $pendingRequestOrgs[] = $pendingRequest['OrgRequests']['organization_id'];
                    }

                    $this->Session->write('Auth.User.pending_requests', $pendingRequestOrgs);

                    $returnData['pending_requests'] = $pendingRequestOrgs;
                    //pr($returnData); exit;
//                    $a = $this->Auth->user();
//                    $returnData['auth_user'] = $a;
//                echo "<hr />";
                    $returnArray['data'] = $returnData;
                    $returnArray['msg'] = 'Succussfully Logged in';
                    $returnArray['type'] = 'login';
//                pr($returnArray); exit;
                    $this->set(array(
                        'result' => array("status" => true, 'data' => $returnArray['data'], 'msg' => $returnArray['msg']),
                        '_serialize' => array('result')
                    ));
                    return $returnArray;
                }
            } else {

                //Create user in our DataBase and assign the organization
                unset($this->User->validate['image']);
                $roleList = $this->Common->setSessionRoles();

                $userAdDATA['User']['ad_upn'] = $UPN;
                $userAdDATA['User']['ad_uid'] = $uid;
                $userAdDATA['User']['fname'] = $firstName;
                $userAdDATA['User']['lname'] = $lastName;
                $userAdDATA['User']['ad_accountname'] = $uniqueAccountName;
                $userAdDATA['User']['source'] = 'ADFS';
                $userAdDATA['User']['email'] = $user_email;
                $userAdDATA['User']['username'] = $UPN;
                $userAdDATA['User']['role'] = array_search('endorser', $roleList);
                $userAdDATA['User']['secret_code'] = $this->getSecretCode("user");
                $userAdDATA['User']['last_app_used'] = "NOW()";

//                echo "CHECK AND SAVE USER DATA";
//                pr($userAdDATA);
//                exit;

                $this->User->setValidation('register');
                $this->User->set($userAdDATA);

//                if ($this->User->validates()) {
//                    pr($userAdDATA);
//                    exit;
                if ($userData = $this->User->save(null, false)) {
                    $userRes = $this->User->findById($this->User->id);
//                    pr($userRes); exit;
                    $userAdDATA['User']['id'] = $this->User->id;
                    $loggedinUserId = $this->User->id;
                    $token = $this->generateToken($loggedinUserId);
                    $this->Auth->login($userRes['User']);
                    $this->Session->write('Auth.User.token', $token);
                    $returnData['data'] = $this->Auth->user();
//                    pr($returnData); exit;
//                    $returnData['role'] = $roleList[$returnData['role']];

                    /*                     * ** ASSIGNING ORG CODE START **** */
                    $statusConfig = Configure::read("statusConfig");
                    $params = array();
                    $params['conditions'] = array("organization_id" => $org_id, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                    $params['group'] = 'pool_type';
                    $params['fields'] = array("UserOrganization.pool_type", "COUNT(UserOrganization.id) as count");
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


                    if ($freeCount >= FREE_POOL_USER_COUNT) {
                        $poolType = "paid";

                        $params = array();
                        $conditions = array();
                        $todayDate = date('Y-m-d H:i:s');
//                    $conditions['start_date <='] = $todayDate;
//                    $conditions['end_date >='] = $todayDate;
                        $conditions['Subscription.status'] = 1;
                        $conditions['Subscription.organization_id'] = $org_id;
                        $params['conditions'] = $conditions;
                        $currentSubscription = $this->Subscription->find("first", $params);
                        if (!empty($currentSubscription)) {
                            $poolPurchased = $currentSubscription['Subscription']['pool_purchased'];

                            if ($paidCount >= $poolPurchased) {
//$status = $statusConfig['invite_inactive'];
                                $status = $statusConfig['inactive'];
                            } else {
                                $status = $statusConfig['active'];
                            }
                        } else {
//$status = $statusConfig['invite_inactive'];
                            $status = $statusConfig['inactive'];
                        }
                    } else {
                        $poolType = "free";
                        $status = $statusConfig['active'];
                    }
                }

                $newUserOrganization = array(
                    "organization_id" => $org_id,
                    "user_id" => $loggedinUserId,
                    "pool_type" => $poolType,
                    "status" => $status,
                    "flow" => "creator",
                    "joined" => 1
                );

                $newUserOrganization['send_invite'] = 0;

                $newUserOrganization['user_id'] = $loggedinUserId;

                $isDefault = false;
                $saved = $this->UserOrganization->save($newUserOrganization);
                if (!isset($newUserOrganization['id'])) {
                    $userOrgId = $this->UserOrganization->id;
                }
                $currentOrg = array();
                if ($saved) {

                    $organization['org_role'] = 'endorser';
                    $organization['joined'] = "1";
                    $organization['authority_name'] = $authorityname;
                    $isActive = false;
                    if (empty($currentOrg)) {
                        $defaultOrg = array("organization_id" => $org_id, "user_id" => $loggedinUserId);
                        $this->DefaultOrg->save($defaultOrg);

                        $isDefault = true;
                    }

                    if ($status == $statusConfig['active']) {
                        if ($isDefault) {
                            $currentOrg = $organization;
                            $this->Session->write('Auth.User.current_org', $currentOrg);
                        }

                        $msg = "You have successfully joined the Organization!!";
                        $isActive = true;
                    } else if ($status == $statusConfig['eval']) {
                        $msg = "You have successfully joined the Organization, but you are in evaluation mode.";
//                        $this->set(array(
//                            'result' => array("status" => true
//                                , "msg" => $msg, "data" => $organization, "isDefault" => $isDefault, "isActive" => false),
//                            '_serialize' => array('result')
//                        ));
                    } else {
                        $msg = "You have successfully joined " . $organization['name'] . "! Your status is inactive. To activate your status, contact your Organization Admin to purchase additional subscription.";
                    }

                    if ($organization["image"] != "") {
                        $rootUrl = Router::url('/', true);
                        $rootUrl = str_replace("http", "https", $rootUrl);
                        $organization["image"] = $rootUrl . "app/webroot/" . ORG_IMAGE_DIR . "small/" . $organization["image"];
                    }

//                    $orgReturnData = array("Organization" => $organization);
                    $returnData['data']['current_org'] = $organization;
                    $returnData['isDefault'] = $isDefault;
                    $returnData['isActive'] = $isActive;
                    $returnData['msg'] = $msg;
                    $returnData['type'] = 'signup';
//                    pr($returnData); exit;
//                        return $returnData;

                    $this->set(array(
                        'result' => array("status" => true, 'data' => $returnData['data'], 'msg' => $returnData['msg']), 'isDefault' => $returnData['isDefault'], 'isActive' => $returnData['isActive'],
                        '_serialize' => array('result')
                    ));
                    return;
//                    $this->set(array(
//                        'result' => array("status" => true
//                            , "msg" => $msg, "data" => $orgReturnData, "isDefault" => $isDefault, "isActive" => $isActive),
//                        '_serialize' => array('result')
//                    ));
//                    return;
                    /*                     * ** ASSIGNING ORG END **** */
                } else {
                    $errors = $this->User->validationErrors;
                    $errorsArray = array();

                    foreach ($errors as $key => $error) {
                        $errorsArray[$key] = $error[0];
                    }
                }

                /**/
//                } else {
//                    $this->set(array(
//                        'result' => array("status" => false
//                            , "msg" => "User profile not configured correctly. Please contact your system administrator."),
//                        '_serialize' => array('result')
//                    ));
//                    return;
//                }

                /*                 * ******************************** */
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
        exit;
    }

    //Added by Babulal Prasad at 28-nov-2019 to get organization subcenter list
    public function getOrgSubcenters() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['org_id'])) {
                $orgID = $this->request->data['org_id'];
                $this->loadModel('OrgSubcenter');
                $subCenterData = $this->OrgSubcenter->find('all', array('conditions' => array('org_id' => $orgID, 'status' => 1), 'order' => array('short_name')));
                $subCenterArray = array();
                if (isset($subCenterData) && !empty($subCenterData)) {
                    foreach ($subCenterData as $index => $scDATA) {
                        $temp = $scDATA['OrgSubcenter'];
                        $subCenterArray[$temp['id']] = $temp['short_name'];
                    }
                }
                $this->set(array(
                    'result' => array("status" => true, 'data' => $subCenterArray, 'msg' => 'Sub center data.'), '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    //Added by Babulal Prasad at 28-nov-2019 to get organization subcenter list
    public function getSubcenterCorevalues() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
//                echo "API Call";
//                pr($this->request->data);exit;
//                
                if (isset($this->request->data['subcenter_id'])) {

                    $subcenterId = $this->request->data['subcenter_id'];
                    $orgId = $this->request->data['org_id'];

                    $this->loadModel('SubcenterCorevalue');
                    $subcenterCheck = "";

                    $params = array();
                    $params['conditions'] = array();

                    if ($subcenterId != '') {

                        $params['fields'] = "*";
                        $params['joins'] = array(
                            array(
                                'table' => 'org_core_values',
                                'alias' => 'OrgCoreValue',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'SubcenterCorevalue.corevalue_id = OrgCoreValue.id '
                                    , "OrgCoreValue.status" => 1
                                )
                            )
                        );
                        $params['conditions'] = array("SubcenterCorevalue.subcenter_id" => $subcenterId, "SubcenterCorevalue.status" => 1, 'SubcenterCorevalue.for_web' => 1);
                        $params['order'] = 'SubcenterCorevalue.id desc';

//                    pr($params);
                        $SubcenterCorevalueS = $this->SubcenterCorevalue->find("all", $params);
//                    pr($SubcenterCorevalueS);
//                    exit;
                        $subcenterCoreValuesArray = array();
                        if (isset($SubcenterCorevalueS) && !empty($SubcenterCorevalueS)) {
                            foreach ($SubcenterCorevalueS as $index => $subcenterdata) {
                                $coreValueID = $subcenterdata['OrgCoreValue']['id'];
                                $subcenterCoreValuesArray[$coreValueID]['id'] = $coreValueID;
                                $subcenterCoreValuesArray[$coreValueID]['name'] = $subcenterdata['OrgCoreValue']['name'];
                                $subcenterCoreValuesArray[$coreValueID]['color_code'] = $subcenterdata['OrgCoreValue']['color_code'];
                                $subcenterCoreValuesArray[$coreValueID]['custom_message_text'] = trim($subcenterdata['OrgCoreValue']['custom_message_text']);
                                $subcenterCoreValuesArray[$coreValueID]['custom_message_enabled'] = $subcenterdata['OrgCoreValue']['custom_message_enabled'];
                                $usersArray = array();
                                if ($subcenterdata['OrgCoreValue']['custom_message_disabled_user_id'] != '') {
                                    $usersArray = json_decode($subcenterdata['OrgCoreValue']['custom_message_disabled_user_id']);
                                }
                                $subcenterCoreValuesArray[$coreValueID]['custom_message_disabled_user_id'] = $usersArray;
                                $subcenterCoreValuesArray[$coreValueID]['for_webapp'] = $subcenterdata['SubcenterCorevalue']['for_web'];
                                $subcenterCoreValuesArray[$coreValueID]['for_guest'] = $subcenterdata['SubcenterCorevalue']['for_feedback'];
//                            pr($subcenterCoreValuesArray);
                            }
                        }
                    } else {//Get All Core values of organization
                    }


                    $this->set(array(
                        'result' => array("status" => true, 'data' => $subcenterCoreValuesArray, 'msg' => 'Sub center core values.'), '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid token in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    //Added by Babulal Prasad at 28-nov-2019 to get organization subcenter list
    public function validateManagerCode() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
                if (isset($this->request->data['managerCode'])) {

                    $managerCode = $this->request->data['managerCode'];
                    $orgId = $this->request->data['orgId'];

                    $this->loadModel('Organization');
                    $subcenterCheck = "";

                    $params = array();
                    $params['conditions'] = array();
                    if ($managerCode != '') {
                        $params['fields'] = "*";
                        $params['conditions'] = array("Organization.id" => $orgId, "Organization.manager_code" => $managerCode);
//                    pr($params);
                        $orgData = $this->Organization->find("all", $params);
                        if (!empty($orgData)) {
                            $this->set(array(
                                'result' => array("status" => true, 'msg' => 'Valid manager code.'), '_serialize' => array('result')
                            ));
                        } else {
                            $this->set(array(
                                'result' => array("status" => false
                                    , "msg" => "Invalid manager code."),
                                '_serialize' => array('result')
                            ));
                        }
                    }
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Invalid token in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed."),
                '_serialize' => array('result')
            ));
        }
    }

    /** Added by Babulal Prasad @12-april-2021 ** */
    public function getPendingGuestnDorsements() {
//        if ($this->request->is('get')) {
        if (isset($this->request->data['token'])) {
            $token = $this->request->data['token'];
            $userinfo = $this->getuserData($token, true);
            $loggedinUser = $this->Auth->user();
//                pr($current_org);
//                exit;
            $status = 0; //Pending
            if (isset($this->request->data['status'])) {
                $status = $this->request->data['status'];
            }


            if ($loggedinUser['current_org']['org_role'] == "admin") {
                $orgid = $loggedinUser['current_org']['id'];
                $this->loadModel('Endorsements');

                $conditionarray["Endorsement.type"] = "guest";
                $conditionarray["Endorsement.status"] = $status;
                $conditionarray["Endorsement.organization_id"] = $orgid;
                $params['conditions'] = $conditionarray;
                $params['order'] = 'Endorsement.created desc';
                $params['fields'] = "*,UNIX_TIMESTAMP(Endorsement.created) as create_date, UNIX_TIMESTAMP() as curr_time ";
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseCoreValues', 'EndorseReplies', 'EndorseAttachments', 'EndorseHashtag')));
                $orgDetail = $this->Endorsement->find("all", $params);
//                    pr($orgDetail); exit;
                foreach ($orgDetail as $key => $endorsementdata) {
                    $tempEndorse = $endorsementdata['Endorsement'];
                    $userid[] = $tempEndorse["endorser_id"];
                    if ($tempEndorse["endorsement_for"] == "user") {
                        $userid[] = $tempEndorse["endorsed_id"];
                    }
                }

                $userdata = array();
                if (!empty($userid)) {
                    $totaluserdetails = $this->User->find("all", array("conditions" => array("id" => $userid),
                        "fields" => array("id", "fname", "lname", "image")));
                    foreach ($totaluserdetails as $userval) {
//                            $userdetails[$userval["User"]["id"]] = $userval;
                        $userdata[$userval["User"]["id"]] = trim($userval["User"]["fname"] . " " . $userval["User"]["lname"]);
                        if ($userval["User"]["image"] != "") {
                            $userdata[$userval["User"]["id"]] .= "&&&&" . Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $userval["User"]["image"];
                        }
                    }
                }
//                    pr($userdata);
//                    exit;
//                    pr($orgDetail);
                $guestPendingNdorseData = array();
                foreach ($orgDetail as $key => $endorsementdata) {
//                        pr($endorsementdata);
                    $endorseData = $endorsementdata['Endorsement'];

                    $guestPendingNdorseData[$key]['id'] = $endorseData['id'];
                    $guestPendingNdorseData[$key]['endorsed_id'] = $endorseData['endorsed_id'];
                    $guestPendingNdorseData[$key]['endorser_id'] = $endorseData['endorser_id'];
                    $guestPendingNdorseData[$key]['organization_id'] = $endorseData['organization_id'];
                    $guestPendingNdorseData[$key]['endorsement_for'] = $endorseData['endorsement_for'];
                    $guestPendingNdorseData[$key]['type'] = $endorseData['type'];
                    $guestPendingNdorseData[$key]['message'] = $endorseData['message'];
                    $guestPendingNdorseData[$key]['created'] = $endorsementdata[0]['create_date'];
                    $guestPendingNdorseData[$key]['servertime'] = $endorsementdata[0]['curr_time'];

                    $endorserId = $endorseData["endorser_id"];
                    $endorsedId = $endorseData["endorsed_id"];

                    if (isset($userdata[$endorserId])) {
                        $newuserdata = explode("&&&&", $userdata[$endorserId]);
//                            pr($newuserdata);
                        $guestPendingNdorseData[$key]["endorser_name"] = $newuserdata[0];
                        $defaultImg = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/user.png";
                        if (isset($newuserdata[1])) {

                            $needle = 'default.jpg';
                            if (strpos($newuserdata[1], $needle) !== false) {
                                $guestPendingNdorseData[$key]["endorser_image"] = $defaultImg;
                            } else {
                                $guestPendingNdorseData[$key]["endorser_image"] = $newuserdata[1];
                            }
                        } else {

                            $guestPendingNdorseData[$key]["endorser_image"] = $defaultImg;
                        }
                    } else {
                        $guestPendingNdorseData[$key]["endorser_name"] = "";
                        $guestPendingNdorseData[$key]["endorser_image"] = "";
                    }


                    if (isset($userdata[$endorsedId])) {
                        $newuserdata = explode("&&&&", $userdata[$endorsedId]);
                        $guestPendingNdorseData[$key]["endorsed_name"] = $newuserdata[0];

                        if (isset($newuserdata[1])) {

                            $needle = 'default.jpg';
                            if (strpos($newuserdata[1], $needle) !== false) {
                                $guestPendingNdorseData[$key]["endorsed_image"] = '';
                            } else {
                                $guestPendingNdorseData[$key]["endorsed_image"] = $newuserdata[1];
                            }
                        }
                    } else {
                        $guestPendingNdorseData[$key]["endorsed_name"] = "";
                        $guestPendingNdorseData[$key]["endorsed_image"] = "";
                    }
//                        pr($guestPendingNdorseData);
//                        exit;
                }
//                    exit;

                $totalrecords = $this->Endorsement->find("count", array("conditions" => array("organization_id" => $orgid, "Endorsement.type" => 'guest', "Endorsement.status =" . $status)));
//                    pr($orgDetail);
//                    exit;
//                    $this->set('orgDetail', $orgDetail);

                $returndata = array("endorsements_list" => $guestPendingNdorseData, "total_page" => $totalrecords);


                $msg = "Pending guest ndorsements list";
                if (isset($this->request->data['status'])) {
                    switch ($status) {
                        case 1:
                            $msg = "Approved guest ndorsements list";
                            break;
                        case 2:
                            $msg = "Rejected guest ndorsements list";
                            break;
                        case 4:
                            $msg = "Deleted guest ndorsements list";
                            break;

                        default:
                            $msg = "Pending guest ndorsements list";
                            break;
                    }
                }

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => $msg,
                        "data" => $returndata),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Only admin can access."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
//        } else {
//            $this->set(array(
//                'result' => array("status" => false
//                    , "msg" => "Get call not allowed."),
//                '_serialize' => array('result')
//            ));
//        }
    }

    //Added by Babulal prasad to update guest ndorsement status @16-april-2021
    public function changeGuestEndorsementStatus() {
        if (isset($this->request->data['token'])) {
            $token = $this->request->data['token'];
            $userinfo = $this->getuserData($token, true);
            $loggedinUser = $this->Auth->user();

            if ($loggedinUser['current_org']['org_role'] == "admin") {
                if (isset($this->request->data['endorsement_id'])) {
                    try {

                        $this->loadModel("Endorsement");
                        $this->loadModel("FeedTran");
                        $endorsementId = $this->request->data['endorsement_id'];
                        $status = 0; //Pending
                        if (isset($this->request->data['status'])) {
                            $status = $this->request->data['status'];
                        }

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

                        $msg = "endorsement ID " . $endorsementId . " Updated";
                        if (isset($this->request->data['status'])) {
                            switch ($status) {
                                case 1:
                                    $msg = "endorsement ID " . $endorsementId . " Approved";
                                    break;
                                case 2:
                                    $msg = "endorsement ID " . $endorsementId . " Rejected";
                                    break;
                                case 3:
                                    $msg = "endorsement ID " . $endorsementId . " Drafted";
                                    break;
                                case 4:
                                    $msg = "endorsement ID " . $endorsementId . " Deleted";
                                    break;
                                case 0:
                                    $msg = "endorsement ID " . $endorsementId . " Marked as pending";
                                    break;

                                default:
                                    $msg = "endorsement ID " . $endorsementId . " Updated";
                                    break;
                            }
                        }
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => $msg),
                            '_serialize' => array('result')
                        ));
                    } catch (Exception $e) {
                        echo json_encode(array("message" => $e));
                    }
                } else {
                    $this->set(array(
                        'result' => array("status" => false
                            , "msg" => "Invalid endorsement Id in request"),
                        '_serialize' => array('result')
                    ));
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Only admin can access."),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Token is missing in request"),
                '_serialize' => array('result')
            ));
        }
    }

    /* Created by Babulalprasad at 05-may-2021
     * To Follow any user
     */

    public function followingUser() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
                $token = $this->request->data['token'];
                $userinfo = $this->getuserData($token, true);
                $userID = $userinfo['users']['id'];
                if (isset($this->request->data['user_id'])) {
                    $fllowingUserId = $this->request->data['user_id']; //1364
                }
                $this->loadModel("UserFollowing");
                $this->loadModel("UserFollower");
                /* Check and put entry in Following table */
                $userFollowings = $this->UserFollowing->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $userID, 'status' => 1)));

                /* Check entry in follower lists */
                $userFollower = $this->UserFollower->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $fllowingUserId, 'status' => 1)));
//                pr($userFollower);
//                exit;

                $newEntry = array();
                if (!empty($userFollowings)) {
                    $userFollowings = array_shift($userFollowings);
                    $userFollowings = $userFollowings['UserFollowing'];
                    $uFollowingID = $userFollowings['id'];
                    $followingIds = $userFollowings['following_ids'];
                    $followingIdsArray = json_decode($followingIds, true);
                    $followingIdsArray = array_merge($followingIdsArray, array($fllowingUserId));
                    $followingIdsArray = array_unique($followingIdsArray);
                    $ids = json_encode($followingIdsArray, true);
                    $newdata = $this->UserFollowing->updateAll(array('following_ids' => "'" . $ids . "'"), array('id' => $uFollowingID));
                    $msg = "User successfully followed.";
                } else {
                    $newEntry['UserFollowing']['user_id'] = $userID;
                    $newEntry['UserFollowing']['following_ids'] = json_encode(array($fllowingUserId));
                    $newdata = $this->UserFollowing->save($newEntry);
                    $msg = "User successfully followed.";
                }

                /* Put/Update followers table */
                $newUEntry = array();
                if (!empty($userFollower)) {
                    $userFollower = array_shift($userFollower);
                    $userFollower = $userFollower['UserFollower'];
                    $uFollowerID = $userFollower['id'];
                    $followerIds = $userFollower['followers_ids'];
                    $followersIdsArray = json_decode($followerIds, true);
                    $followersIdsArray = array_merge($followersIdsArray, array($userID));
                    $followersIdsArray = array_unique($followersIdsArray);
                    $followesIds = json_encode($followersIdsArray, true);
                    $newdata = $this->UserFollower->updateAll(array('followers_ids' => "'" . $followesIds . "'"), array('id' => $uFollowerID));
                } else {
                    $newUEntry['UserFollower']['user_id'] = $fllowingUserId;
                    $newUEntry['UserFollower']['followers_ids'] = json_encode(array($userID));
                    $newUfollowerData = $this->UserFollower->save($newUEntry);
                }

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => $msg),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed"),
                '_serialize' => array('result')
            ));
        }
    }

    /* Created by Babulalprasad at 05-may-2021
     * To get followers and following users
     */

    public function getUserFollowList() {
        if ($this->request->is('get')) {
            if (isset($this->request->query['token'])) {
                $token = $this->request->query['token'];
                $userinfo = $this->getuserData($token, true);

                $userID = $userinfo['users']['id'];
                $type = 'following'; // or follower
                if (isset($this->request->query['type'])) {
                    $type = $this->request->query['type'];
                }

                if ($type == 'following') {
                    $this->loadModel("UserFollowing");
                    $userFollowings = $this->UserFollowing->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $userID, 'status' => 1)));
                    if (!empty($userFollowings)) {
                        $userFollowings = array_shift($userFollowings);
                        $userFollowings = $userFollowings['UserFollowing'];
                        $uFollowingID = $userFollowings['id'];
                        $followingIdsArray = json_decode($userFollowings['following_ids']);
                        $users = array();
                        $rootUrl = Router::url('/', true);
                        if (!empty($followingIdsArray)) {
                            $usersData = $this->User->find('all', array('fields' => array('id', "CONCAT(trim(fname),' ',trim(lname)) as fullname", 'image'), 'conditions' => array('id' => $followingIdsArray)));
                            if (!empty($usersData)) {
                                foreach ($usersData as $index => $uData) {
                                    $users[$index]['id'] = $uData['User']['id'];
                                    $img = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/user.png";
                                    if (isset($uData['User']['image']) && $uData['User']['image'] != '') {
                                        $img = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $uData['User']['image'];
                                    }
                                    $users[$index]['image'] = $img;
                                    $users[$index]['fullname'] = $uData[0]['fullname'];
                                }
                            }
                        }

                        $returnData['following'] = $users;
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => 'User following list.',
                                "data" => $returnData),
                            '_serialize' => array('result')
                        ));
                    } else {
                        // Following no-one
                        $x = new stdClass();
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => 'User following list.',
                                "data" => $x),
                            '_serialize' => array('result')
                        ));
                    }
                } else {
                    $this->loadModel("UserFollower");
                    $userFollower = $this->UserFollower->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $userID, 'status' => 1)));

                    if (!empty($userFollower)) {
                        $userFollower = array_shift($userFollower);
                        $userFollower = $userFollower['UserFollower'];
                        $uFollowingID = $userFollower['id'];
                        $followerIdsArray = json_decode($userFollower['followers_ids']);
                        $users = array();
                        $rootUrl = Router::url('/', true);
                        if (!empty($followerIdsArray)) {
                            $usersData = $this->User->find('all', array('fields' => array('id', "CONCAT(trim(fname),' ',trim(lname)) as fullname", 'image'), 'conditions' => array('id' => $followerIdsArray)));
                            if (!empty($usersData)) {
                                foreach ($usersData as $index => $uData) {
                                    $users[$index]['id'] = $uData['User']['id'];
                                    $img = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/user.png";
                                    if (isset($uData['User']['image']) && $uData['User']['image'] != '') {
                                        $img = $rootUrl . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $uData['User']['image'];
                                    }
                                    $users[$index]['image'] = $img;
                                    $users[$index]['fullname'] = $uData[0]['fullname'];
                                }
                            }
                        }



                        $this->loadModel("UserFollowing");
                        $followingIdsArray = array();
                        $userFollowings = $this->UserFollowing->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $userID, 'status' => 1)));
                        if (!empty($userFollowings)) {
                            $userFollowings = array_shift($userFollowings);
                            $userFollowings = $userFollowings['UserFollowing'];
                            $uFollowingID = $userFollowings['id'];
                            $followingIdsArray = json_decode($userFollowings['following_ids']);
                        }


                        $returnData['following_users'] = $followingIdsArray;
                        $returnData['followers'] = $users;

                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => 'User followers list.',
                                "data" => $returnData),
                            '_serialize' => array('result')
                        ));
                    } else {
                        // Following no-one
                        $x = new stdClass();
                        $this->set(array(
                            'result' => array("status" => true
                                , "msg" => 'User followers list.',
                                "data" => $x),
                            '_serialize' => array('result')
                        ));
                    }
                }
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Post call not allowed"),
                '_serialize' => array('result')
            ));
        }
    }

    /* Created by Babulalprasad at 06-may-2021
     * To UnFollow any user
     */

    public function UnfollowUser() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['token'])) {
                $token = $this->request->data['token'];
                $userinfo = $this->getuserData($token, true);
                $userID = $userinfo['users']['id'];
                if (isset($this->request->data['user_id'])) {
                    $fllowingUserId = $this->request->data['user_id']; //1364
                }
                $this->loadModel("UserFollowing");
                $this->loadModel("UserFollower");

                /* Check and put entry in Following table */
                $userFollowings = $this->UserFollowing->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $userID, 'status' => 1)));

                /* Check entry in follower lists */
                $userFollower = $this->UserFollower->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $fllowingUserId, 'status' => 1)));

                $newEntry = array();
                if (!empty($userFollowings)) {
                    $userFollowings = array_shift($userFollowings);
                    $userFollowings = $userFollowings['UserFollowing'];
                    $uFollowingID = $userFollowings['id'];
                    $followingIds = $userFollowings['following_ids'];
                    $followingIdsArray = json_decode($followingIds, true);
                    $followingIdsArray = array_diff($followingIdsArray, array($fllowingUserId));
                    sort($followingIdsArray);
                    $ids = json_encode($followingIdsArray);
                    $newdata = $this->UserFollowing->updateAll(array('following_ids' => "'" . $ids . "'"), array('id' => $uFollowingID));
                    $msg = "User successfully unfollowed.";
                }

                /* Put/Update followers table */
                $newUEntry = array();
                if (!empty($userFollower)) {
                    $userFollower = array_shift($userFollower);
                    $userFollower = $userFollower['UserFollower'];
                    $uFollowerID = $userFollower['id'];
                    $followerIds = $userFollower['followers_ids'];
                    $followersIdsArray = json_decode($followerIds, true);
                    $followersIdsArray = array_diff($followersIdsArray, array($userID));
                    sort($followersIdsArray);
                    $followesIds = json_encode($followersIdsArray, true);
                    $newdata = $this->UserFollower->updateAll(array('followers_ids' => "'" . $followesIds . "'"), array('id' => $uFollowerID));
                }

                $this->set(array(
                    'result' => array("status" => true
                        , "msg" => $msg),
                    '_serialize' => array('result')
                ));
            } else {
                $this->set(array(
                    'result' => array("status" => false
                        , "msg" => "Token is missing in request"),
                    '_serialize' => array('result')
                ));
            }
        } else {
            $this->set(array(
                'result' => array("status" => false
                    , "msg" => "Get call not allowed"),
                '_serialize' => array('result')
            ));
        }
    }

}
