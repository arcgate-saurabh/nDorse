<?php

App::uses('CakeEmail', 'Network/Email');
App::uses('Component', 'Controller');

class OrganizationsController extends AppController {

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $components = array('RequestHandler', "Auth", "Common", "Session", "Image", "Paginator");
    var $uses = array("User", "Department", "Organization", "OrgCoreValue", "UserOrganization", "OrgDepartment", "OrgJobTitle", "Entity", "Subscription", "OrgAdSetting");
    public $helpers = array("Html", "Form", "Session", "Js");

    public function beforeFilter() {
        parent::beforeFilter();
        // $this->Auth->allow('login');
        $this->Auth->allow('register', 'login', 'logout', 'forgot', 'createclient', 'setImage', 'deleteimage');
    }

    public function index() {  //index
        ini_set('memory_limit', '1024M');
        if ($this->Session->check('Auth.User')) {
            $loggedUser = $this->Auth->user();

            $logged_in_user_role = $this->Auth->user('role');
            $logged_in_user_id = $this->Auth->user('id');
            if ($logged_in_user_id < 1) {
                $this->Auth->logout();
                $this->redirect(array('controller' => 'users', 'action' => 'login'));
            } else {
                if ($logged_in_user_role > 1) {
                    $this->LoadModel("UserOrganization");
                    // $conditions = array('Organization.status' => array(0, 1), 'Organization.admin_id' => $logged_in_user_id);
                    // $conditions = array('Organization.status' => array(0, 1), 'UserOrganization.user_role' => 2,'UserOrganization.user_id' => $logged_in_user_id,'UserOrganization.status' => 1);
                    if ($logged_in_user_role == 6) {
                        $userorgdata = $this->UserOrganization->find("all", array("conditions" => array("user_id" => $logged_in_user_id, "user_role" => 6, 'UserOrganization.status' => 1)));
                    } else {
                        $userorgdata = $this->UserOrganization->find("all", array("conditions" => array("user_id" => $logged_in_user_id, "user_role" => 2, 'UserOrganization.status' => 1)));
                    }

//                    echo $logged_in_user_role;
//                    pr($userorgdata);
//                    exit;
                    $organization_id = array();
                    foreach ($userorgdata as $uservalorg) {
                        $organization_id[] = $uservalorg["UserOrganization"]["organization_id"];
                    }
                    $conditions = array('Organization.status' => array(0, 1), 'Organization.id' => $organization_id);
                } else if ($logged_in_user_role == 1) {
                    $conditions = array('Organization.status' => array(0, 1));
                }
                $this->LoadModel("OrgRequest");
                $this->LoadModel("Endorsement");

                $totalrecords = $this->Organization->find('count', array('conditions' => $conditions));

//                $this->Organization->bindModel(array(
//                    'hasMany' => array(
//                        'Invite' => array(
//                            'className' => 'Invite',
//                        ),
//                        'UserOrganization' => array(
//                            'className' => 'UserOrganization',
//                        ),
//                        'Transactions' => array(
//                            'className' => 'Transactions',
//                            'conditions' => array('Transactions.status' => 'canceled'),
//                            'order' => 'created DESC'
//                        )
//                    ),
//                    'hasOne' => array('Subscription' => array(
//                            'className' => 'Subscription',
//                        ))
//                ));
                //$this->Organization->recursive = 2;

                $orgBasicData = $this->Organization->find('all', array('fields' => array('id', 'admin_id', 'name', 'short_name', 'country', 'state', 'city', 'zip', 'street', 'status', 'image', 'about'),
                    'order' => 'Organization.created DESC', 'limit' => 10, 'conditions' => $conditions));
                $orgDataIndexed = array();
//                pr($orgBasicData);
//                exit;
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

//                pr($orgdata);
//                exit;
                $user_role = array(2, 3);
                $adminusr = array();
                foreach ($orgdata as $key => $orgid) {
//                    pr($orgid); exit;
                    $target_id = $orgid["Organization"]["id"];
                    $owner_id = $orgid["Organization"]["admin_id"];
                    $totalorgusers = $this->Common->getusersfororg($target_id, $user_role);
                    $orgowner = $this->Common->getorgownername($owner_id);
                    $totalusers[$target_id] = $totalorgusers;

                    $ownersarray[$target_id][$owner_id] = $orgowner;

//                    $userorg[$key] = $orgid["UserOrganization"];
                    $userorg = $orgid["UserOrganization"];
//
//                    foreach ($userorg as $uval) {
//                        if ($uval["user_role"] == 2) {
//                            $adminusr[] = $uval["user_id"];
//                        }
//                    }
//                    $inviationStats = array();
//                    $inviationStats[$target_id] = $this->Common->getInvitationDetails($userorg);
                    $inviationStats[$target_id] = $this->Common->getInvitationDetails_2($userorg);
//                    $totalinvitationsaccepted[$target_id] = $this->Common->userorgcounter($userorg);
//                    $invitation_accepted[$target_id] = $totalinvitationsaccepted[$target_id]["web"] + $totalinvitationsaccepted[$target_id]["app"];
//                    $invitations_array[$target_id] = $this->Common->invitations_fetching($orgid);
//                    $invitation_pending[$target_id] = $invitations_array[$target_id]["invitations_pending"];
//                    $invitation_pending[$target_id]["web"] = $totalinvitationsaccepted[$target_id]["web"] + $invitation_pending[$target_id]["web"];
//                    $invitation_pending[$target_id]["app"] = $totalinvitationsaccepted[$target_id]["app"] + $invitation_pending[$target_id]["app"];
//                    $totalinvitations[$target_id] = array("invitation_accepted" => $invitation_accepted, "invitation_pending" => $invitation_pending);
//                    $pendingrequescounter[$target_id] = $this->OrgRequest->find("count", array("conditions" => array("organization_id" => $target_id, "status" => 0)));
                    $pendingrequescounter = 0;
                    $endorsementformonth = 0;
//                    $endorsementformonth[$target_id] = $this->Common->endorsementformonth($target_id);
//                    $userPool = $orgid['Subscription']['pool_purchased'];
//                    foreach ($orgid['Transactions'] as $transaction) {
//
//                        if ($transaction["status"] == "canceled") {
//                            $adminusr[] = $transaction["user_id"];
//                        }
//                        if ($transaction['bt_subscription_id'] == $orgid['Subscription']['bt_id']) {
//                            if ($transaction['type'] == 'upgrade') {
//                                $userPool += $transaction['user_diff'];
//                            } else if ($transaction['type'] == 'downgrade') {
//                                $userPool -= $transaction['user_diff'];
//                            }
//                        }
//                    }
                    $userPool = array();
                    $orgdata[$key]['Subscription']['user_pool'] = $userPool;
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

                // end
//                pr($inviationStats); exit;
                $this->set(compact('orgdata', 'totalusers', 'totalrecords', 'invitations_array', 'pendingrequescounter', 'invitation_pending', 'invitation_accepted', 'endorsementformonth', 'ownersarray', 'adminusrarray', 'inviationStats'));
                $this->set('authUser', $this->Auth->user());
            }
        }
    }

    public function info($id) {
        $statusConfig = Configure::read("statusConfig");
        $this->User->bindModel(array('hasOne' => array('UserOrganization')));
//        pr($statusConfig); exit;
        if ($this->Session->check('Auth.User.role') != "1") {
            $this->Auth->logout();
            $this->redirect(array('action' => 'login'));
        } else {

            $this->loadModel("EndorseCoreValue");
            $logged_in_user_id = $this->Auth->user("id");
            //$orgids = $this->Organization->find('all', array('conditions' => array('Organization.status' => array(0, 1), 'Organization.admin_id' => $logged_in_user_id)));
            //======to check if id is of present logged in user or not for user role = admin
            //foreach ($orgids as $orgid) {
            //  $checkorgids[] = $orgid["Organization"]["id"];
            //}
            //$result = $this->Common->checkorgid($id);
            //if ($result == "redirect") {
            //    $this->redirect(array("controller" => "organizations", "action" => "index"));
            //}
            //======end
            $org_user_data = $this->User->find('all', array(
                "fields" => array("User.id", "User.fname", "User.lname", "UserOrganization.id", "User.daisy_enabled", "User.image", "User.source", "User.email", "User.updated", "User.created", "UserOrganization.status", "UserOrganization.user_id", "UserOrganization.user_role", "UserOrganization.joined", "UserOrganization.organization_id"),
                "order" => "User.id DESC", 'limit' => 20, 'conditions' => array('UserOrganization.organization_id' => $id, 'UserOrganization.user_role' => array(2, 3, 6), 'UserOrganization.status' => array(0, 1, 3)), 'order' => 'UserOrganization.user_role'));
//            pr($org_user_data); exit;
            $this->User->bindModel(array('hasOne' => array('UserOrganization')));
            $totalrecords = $this->User->find('count', array('conditions' => array('UserOrganization.organization_id' => $id, 'UserOrganization.user_role' => array(2, 3, 6), 'UserOrganization.status' => array(0, 1, 3)), 'order' => 'UserOrganization.id  DESC'));
            $coredata = $this->OrgCoreValue->find('list', array('conditions' => array('organization_id' => $id, 'status' => array(1, 2))));
//            pr($coredata); exit;
            $corevalueendorsedcounter = array();
            foreach ($coredata as $key => $data) {
                $corevalueendorsedcounter[$key] = $this->EndorseCoreValue->find("count", array("conditions" => array("value_id" => $key)));
            }

            $this->loadModel("Endorsement");
            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValue', 'EndorseHashtag')));
            $conditionsendorsement = array();
            $conditionsendorsement = array("organization_id" => $id, 'status' => 1);
            $totalendorsements = $this->Endorsement->find("count", array("conditions" => $conditionsendorsement));

            //$conditionsendorsement[] = array("MONTH(created) = MONTH(now())", "YEAR(created) = YEAR(now())");
            $d = new DateTime('first day of this month');
            $startdate = $d->format('Y-m-d');
            $conditionsendorsement[] = array('date(created) >= "' . $startdate . '"');

//            pr($conditionsendorsement); exit;
            //$this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValue', 'EndorseHashtag')));
            $this->Endorsement->recursive = 0;
            $endorsementdata = $this->Endorsement->find("all", array("fields" => array("EndorseCoreValue.value_id"),
                'joins' => array(array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValue', 'type' => 'LEFT', 'conditions' => array('Endorsement.id = EndorseCoreValue.endorsement_id'))),
                "conditions" => $conditionsendorsement));
            $corevaluesid = $coreValuesID = array();
//=            echo $this->Endorsement->getLastQuery();
//            pr($endorsementdata);
//            exit;
            if (!empty($endorsementdata)) {
                foreach ($endorsementdata as $indx => $enDCoreValue) {
                    $count = '';
                    $value_id = $enDCoreValue['EndorseCoreValue']['value_id'];
                    if (!isset($coreValuesID[$value_id])) {
                        $coreValuesID[$value_id] = 1;
                    } else {
                        $count = $coreValuesID[$value_id];
                        $coreValuesID[$value_id] = $count + 1;
                    }
                }
            }
            $countermonthlyendorsements = $coreValuesID;
//            pr($coreValuesID);
//            exit;
//            echo $this->Endorsement->getLastQuery(); 
//            echo "<hr>";
//            exit;
//            $corevaluesid = array();
//            $endorsementdata = $this->Endorsement->find("all", array("conditions" => $conditionsendorsement));
//            foreach ($endorsementdata as $dataendorsements) {
//                foreach ($dataendorsements["EndorseCoreValues"] as $corevalues) {
//                    $corevaluesid[] = $corevalues["value_id"];
//                }
//            }
//            pr($corevaluesid);
//            exit;
//
//            $countermonthlyendorsements = array_count_values($corevaluesid);
//            pr($countermonthlyendorsements);
//            exit;
            $this->loadModel("OrgRequest");
//            $this->loadModel("Endorsement");
            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
            $this->Organization->bindModel(array(
                'hasMany' => array(
                    'Invite' => array(
                        'className' => 'Invite',
                    ),
                    'UserOrganization' => array(
                        'className' => 'UserOrganization',
                    ),
                    'Transactions' => array(
                        'className' => 'Transactions',
                        'conditions' => array('Transactions.status' => 'canceled'),
                        'order' => 'created DESC'
                    )
                ), 'hasOne' => array('Subscription' => array(
                        'className' => 'Subscription',
                    ))
            ));
            $this->Organization->recursive = 2;
            $orgdata = $this->Organization->findById($id);
//            pr($orgdata);exit;
            $userorg = $orgdata["UserOrganization"];
//            pr($userorg); exit;
            $adminusr = array();
            foreach ($userorg as $uval) {
                if ($uval["user_role"] == 2 || $uval["user_role"] == 6) {
                    $adminusr[] = $uval["user_id"];
                }
            }
//            pr($adminusr); exit;
            if (!in_array($this->Auth->user("id"), $adminusr) && $this->Auth->user("role") > 1) {
                $this->redirect(array("controller" => "organizations", "action" => "index"));
            }
            $inviationStats = $this->Common->getInvitationDetails($userorg);
            //pr($inviationStats); exit;
//            $totalinvitationsaccepted = $this->Common->userorgcounter($userorg);
//            $invitation_accepted = $totalinvitationsaccepted["web"] + $totalinvitationsaccepted["app"];
//            $invitations_array = $this->Common->invitations_fetching($orgdata);
//            $invitation_pending = $invitations_array["invitations_pending"];
//            $invitation_pending["web"] = $totalinvitationsaccepted["web"] + $invitation_pending["web"];
//            $invitation_pending["app"] = $totalinvitationsaccepted["app"] + $invitation_pending["app"];
            $status_orgrequest = array("status" => 0);
            //$pendingrequests = $this->Common->pending_requests($orgdata["OrgRequest"], $status_orgrequest);
            $pendingrequescounter = $this->OrgRequest->find("count", array("conditions" => array("organization_id" => $id, "status" => 0)));

            $uploadedemssage = "";
            $user_role = array(3, 2, 6);
            $totalusers = $this->Common->getusersfororg($id, $user_role);
//            pr($totalusers); exit;
            $endorsementformonth = $this->Common->endorsementformonth($id);
//            $subcenterEndorsementformonth = $this->Common->subcenterendorsementformonth($id);
            //pr($subcenterEndorsementformonth); exit;
            //============================data on post request
            if ($this->request->is("post")) {
                
            }
            // get active and inactive  user
            $params = array();
            $params['conditions'] = array("organization_id" => $id, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));

            $params['fields'] = array("COUNT(UserOrganization.user_id) as count");
            $userOrgStats = $this->UserOrganization->find("all", $params);
//            pr($userOrgStats); exit;
            $activeusercount = $userOrgStats[0][0]["count"];
            $params['conditions'] = array("organization_id" => $id, "UserOrganization.status" => array($statusConfig['inactive']));
            $userOrgStatsinactive = $this->UserOrganization->find("all", $params);
//            pr($userOrgStatsinactive); exit;
            $inactiveusercount = $userOrgStatsinactive[0][0]["count"];
            foreach ($orgdata['Transactions'] as $transaction) {
                if ($transaction["status"] == "canceled") {
                    $adminusr[] = $transaction["user_id"];
                }
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

            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
            $this->Organization->recursive = 2;
            $this->Organization->bindModel(array(
                'hasMany' => array(
                    "Endorsement" => array(
                        "className" => "Endorsement",
                        'order' => 'created DESC',
                        'conditions' => array("Endorsement.type ='guest'", "status = 0"),
                        'limit' => 20
                    ))
            ));
            $pendindnDorsementCount = 0;
            $orgDetail = $this->Organization->findById($id);
            $orgSubcenterDetail = array();
            $this->loadModel('OrgSubcenter');
            $orgSubcenterDetail = $this->OrgSubcenter->find('all', array('fields' => '*', 'conditions' => array('org_id' => $id, 'status' => 1)));
//            pr($orgSubcenterDetail);
            $subcenterIDArray = $subcenterUserCountArray = array();
            if (!empty($orgSubcenterDetail)) {
                foreach ($orgSubcenterDetail as $index => $subData) {
//                   pr($subData);
                    $subcenterIDArray[] = $subData['OrgSubcenter']['id'];
                }
            }
//            pr($subcenterIDArray);
//            exit;
            if (!empty($subcenterIDArray)) {
                $params = array();
                $params['conditions'] = array("organization_id" => $id, "subcenter_id" => $subcenterIDArray, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                $params['fields'] = array("COUNT(UserOrganization.user_id) as count", "subcenter_id");
                $params['group'] = 'subcenter_id';
                $userOrgStats = $this->UserOrganization->find("all", $params);
//                pr($userOrgStats);
                $subCountExist = array();
                if (!empty($userOrgStats)) {
                    foreach ($userOrgStats as $i => $subUcount) {
                        $subcenterUserCountArray[$subUcount['UserOrganization']['subcenter_id']] = $subUcount[0]['count'];
                        $subCountExist[] = $subUcount['UserOrganization']['subcenter_id'];
                    }
                }

                foreach ($subcenterIDArray as $index => $subID) {
                    if (!in_array($subID, $subCountExist)) {
                        $subcenterUserCountArray[$subID] = 0;
                    }
                }

                //pr($subcenterUserCountArray);
            }


            if (isset($orgDetail["Endorsement"])) {
                $pendindnDorsementCount = count($orgDetail["Endorsement"]);
            }


            $departments = $this->Common->getorgdepartments($id);
            $entities = $this->Common->getorgentities($id);
            $jobtitles = $this->Common->getorgjobtitles($id);
            $adfsUsers = $this->Common->getorgADFSusers($id);
            $totalADFSUsers = count($adfsUsers);
            $activeAdfsUsers = 0;
            if (!empty($adfsUsers) && count($adfsUsers) > 0) {
                foreach ($adfsUsers as $index => $adUsers) {
                    if ($adUsers['User']['last_app_used'] != '0000-00-00 00:00:00') {
                        $activeAdfsUsers++;
                    }
                }
            }

            //BP
            // end
//            pr($adminusrarray); exit;
            $this->set(compact('totalusers', 'adminusrarray', 'activeusercount', 'inactiveusercount', 'orgdata', 'coredata', 'org_user_data', 'uploadedemssage', 'invitations_array', 'pendingrequescounter', 'invitation_pending', 'invitation_accepted', 'endorsementformonth', 'corevalueendorsedcounter', 'totalrecords', 'countermonthlyendorsements', 'totalendorsements', 'inviationStats', 'pendindnDorsementCount', 'departments', 'entities', 'jobtitles', 'totalADFSUsers', 'activeAdfsUsers', 'orgSubcenterDetail', 'subcenterUserCountArray'));
            $this->set('authUser', $this->Auth->user());
        }
    }

    public function bulkusertemplate() {
        //$result = array();
        $filename = "bulkuser_template.csv";
        $fp = fopen('php://output', 'w+');
        //$result = $this->User->getColumnTypes();
        $result = array("EmployeeID", "FirstName", "LastName", "Suffix", "Department", "Title", "Email", "MobilePhone", "Status {Inactive:0, Active:1,Eval:2}", "SendInvitation{1:Yes,0:No}", "sub_org", "daisy_enabled{1:Yes,0:No}", "sub_center_name");
//        foreach ($removefields as $remove) {
//            unset($result[$remove]);
//        }
        //changed dob format
        //$result[3] = "dob(format should be YYYY-MM-DD)";
        //array_push($result, "departments", "entities", "jobtitles");
        header('Content-Encoding: UTF-8');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp, $result);
        exit;
    }

    //============================BULK LINK IMPORTS TEMPLATE
    function bulklinkimportstemp() {
        $id = $this->params->params['pass'][0];
        $this->User->bindModel(array(
            'hasOne' => array(
                'UserOrganization' => array(
                    "className" => "UserOrganization"
                //'foreignKey' => false,
                //'conditions' => array('UserOrganization.user_id = User.id'),
                //'type' => 'INNER',
                ),
            )
        ));
        $filename = "bulklinkimports_tmeplate.csv";
        $fp = fopen("php://output", "w");
        $header = array("Email", "Links");
        $results = $this->User->find("all", array("fields" => array("User.email"), "conditions" => array("UserOrganization.organization_id" => $id, "UserOrganization.user_role" => array(3, 4), "UserOrganization.status" => array(0, 1, 3))));
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp, $header);
        foreach ($results as $result) {
            fputcsv($fp, $result['User']);
        }
        fclose($fp);
        exit();
    }

    function liveendorsement($organization_id = "null") {
        $result = $this->Common->checkorgid($organization_id);
        //=============to redirect if orgid is wrong
        if ($result == "redirect") {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }
        $authUser = $this->Auth->User();
        $this->loadModel("Endorsement");
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
        $this->Organization->bindModel(array(
            'hasMany' => array(
                "Endorsement" => array(
                    "className" => "Endorsement",
                    'order' => 'created DESC',
                    'conditions' => array("Endorsement.type!='private'", "status = 1"),
                    'limit' => 20
                ),
                "Invite" => array(
                    "className" => "Invite"
                ),
                "UserOrganization" => array(
                    "className" => "UserOrganization"
                )
            )
                )
        );

        $this->Organization->recursive = 2;
        //====defining company detail variable
        $companydetail["totalusers"] = 0;
        $companydetail["totalusers"] = 0;
        $companydetail["invitation_sent"] = 0;
        $companydetail["invitation_accepted"] = 0;
        $orgdata = $this->Organization->findById($organization_id);

        $totalrecords = $this->Endorsement->find("count", array("conditions" => array("organization_id" => $organization_id)));
        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $allvalues = array("department" => $departments, "entities" => $entities, "orgcorevaluesandcode" => $orgcorevaluesandcode);
        $userorg = $orgdata["UserOrganization"];
        $totalinvitationsaccepted = $this->Common->userorgcounter($userorg);
        $invitationpending = $this->Common->invitations_fetching($orgdata);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
        $invitationaccepted = $totalinvitationsaccepted["web"] + $totalinvitationsaccepted["app"];
        $companydetail["invitation_sent"] = $invitationpending["total_invitations_sent"] + $invitationaccepted;
        $companydetail["invitation_accepted"] = $invitationaccepted;
        $user_role = array(3, 4);
        $companydetail["totalusers"] = $this->Common->getusersfororg($organization_id, $user_role);

        //=================finding endorsed id detail
        $endorsementformonth = 0;
        $userid = array();

        foreach ($orgdata["Endorsement"] as $endorsementdata) {
            //=====finding endorsement for the month
//            if ((date("m", strtotime($endorsementdata["created"])) == date("m")) && (date("y", strtotime($endorsementdata["created"])) == date("y"))) {
//                $endorsementformonth++;
//            }
            $userid[] = $endorsementdata["endorser_id"];
            if ($endorsementdata["endorsement_for"] == "user") {
                $userid[] = $endorsementdata["endorsed_id"];
            }
        }
        $companydetail["endorsementformonth"] = $this->Common->endorsementformonth($organization_id);
        //$companydetail["endorsementformonth"] = $endorsementformonth;
        if (!empty($userid)) {
            $totaluserdetails = $this->User->find("all", array("conditions" => array("id" => $userid), "fields" => array("id", "fname", "lname", "image")));
            foreach ($totaluserdetails as $userdetail) {
                $userdetails[$userdetail["User"]["id"]] = $userdetail;
            }
        }
        $this->set(compact("authUser", "companydetail", "orgdata", "allvalues", "userdetails", "endorserdetail", "totalrecords"));
    }

    function reportsandcharts($organization_id = "null") {
        ini_set('memory_limit', '-1');
        $result = $this->Common->checkorgid($organization_id);

        //=============to redirect if orgid is wrong
        if ($result == "redirect") {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }
        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdaterandc"]) && !empty($this->request->data["enddaterandc"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdaterandc"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddaterandc"]);
        }
        $this->loadModel("OrgDepartment");
        $this->loadModel("Endorsement");
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues')));
        $authUser = $this->Auth->User();
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
        //=========means number of guys he endorse

        $conditionscountendorsement['organization_id'] = $organization_id;
        $conditionscountendorsement['type !='] = 'guest';
        if ($startdate != "" and $enddate != "") {
            array_push($conditionscountendorsement, "date(created) between '$startdate' and '$enddate'");
        }

        //===============binding model conditions
        $this->Common->commonleaderboardbindings($conditionscountendorsement);
        $this->UserOrganization->recursive = 2;
        $endorsementdata = $this->UserOrganization->find("all", array("order" => "User.fname", "conditions" => array("UserOrganization.organization_id" => $organization_id, "UserOrganization.status" => array(1, 2, 3), "UserOrganization.user_role" => array(2, 3, 4, 6))));
        //pr($endorsementdata);exit;
        //===================endorsement by day graph
        $conditionsendorsementbyday["organization_id"] = $organization_id;
        $conditionsendorsementbyday['type !='] = 'guest';
        if ($startdate != "" and $enddate != "") {
            array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
        }
        $endorsementbyday = $this->Endorsement->find("all", array("conditions" => $conditionsendorsementbyday, "group" => "date(Endorsement.created)", "fields" => array("count(*) as cnt", "date(created) as cdate")));
//        pr($endorsementbyday); exit;  
        //=============endorsement by department
//        $params['fields'] = "count(Endorsement.endorsed_id) as cnt,OrgDepartments.name as department, OrgDepartments.id as department_id";
//        $conditionarray["Endorsement.organization_id"] = $organization_id;
//        $conditionarray["Endorsement.endorsement_for"] = "department";
//        $conditionarray["Endorsement.type !="] = "guest";
//        if ($startdate != "" and $enddate != "") {
//            array_push($conditionarray, "date(Endorsement.created) between '$startdate' and '$enddate'");
//        }
//        $params['conditions'] = $conditionarray;
//        $params['joins'] = array(
//            array(
//                'table' => 'org_departments',
//                'alias' => 'OrgDepartments',
//                'type' => 'LEFT',
//                'conditions' => array(
//                    'OrgDepartments.id = Endorsement.endorsed_id'
//                )
//            )
//        );
//        $params['order'] = 'cnt desc';
//
//
//        $params['group'] = 'Endorsement.endorsed_id';
//        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
////        pr($params);
//        $leaderboard = $this->Endorsement->find("all", $params);
//        echo $this->Endorsement->getLastQuery(); exit;
//        pr($leaderboard); exit;
        //=================end of endorsement by day graph
//        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
//        $paramsdepthistory["conditions"] = array("Endorsement.organization_id" => $organization_id, "Endorsement.endorsement_for" => "department", "type !=" => "guest");
//        $paramsdepthistory["fields"] = ("*");
//        $paramsdepthistory["group"] = ("WEEKOFYEAR(date(Endorsement.created)), Endorsement.endorsed_id");
//        if ($startdate != "" and $enddate != "") {
//            array_push($paramsdepthistory["conditions"], "date(Endorsement.created) between '$startdate' and '$enddate'");
//        }
//        $this->Endorsement->virtualFields['weekdepartment'] = "WEEKOFYEAR(date(Endorsement.created))";
//        $this->Endorsement->virtualFields['yeardepartment'] = "year(date(Endorsement.created))";
//        $this->Endorsement->virtualFields['endorseddepartment'] = "count(Endorsement.endorsed_id)";
//
//        $this->Endorsement->bindModel(array(
//            'hasOne' => array(
//                'OrgDepartment' => array(
//                    'className' => 'OrgDepartment',
//                    'foreignKey' => false,
//                    'conditions' => array("OrgDepartment.id = Endorsement.endorsed_id"),
//                )
//        )));
//
//        $endorsementbydeptweek = $this->Endorsement->find("all", $paramsdepthistory);
//
//
//        unset($this->Endorsement->virtualFields['weekdepartment']);
//        unset($this->Endorsement->virtualFields['yeardepartment']);
//        unset($this->Endorsement->virtualFields['endorseddepartment']);
//
//        //pr($endorsementbydeptweek);
//        $startofweekarray = "";
//        $counter = "";
//        //pr($endorsementbydeptweek); exit;
//        $dept_array = array();
//        $date_array = array();
//        foreach ($endorsementbydeptweek as $endorsementdeptweek) {
//            $dept_array[$endorsementdeptweek["OrgDepartment"]['id']] = $deptname = $endorsementdeptweek["OrgDepartment"]["name"];
//            $date_array[] = $startofweekarray = $this->Common->getStartAndEndDate($endorsementdeptweek["Endorsement"]["weekdepartment"], $endorsementdeptweek["Endorsement"]["yeardepartment"]);
//            $counter[$startofweekarray][$deptname] = (int) $endorsementdeptweek["Endorsement"]["endorseddepartment"];
//            //$startofweekarray[] = $this->Common->getStartAndEndDate($endorsementdeptweek["Endorsement"]["weekdepartment"], $endorsementdeptweek["Endorsement"]["yeardepartment"]);
//        }
//        $endorsementbydeptweek = array();
//        //============to take date array as unique
//        $date_array = array_unique($date_array);
//        $dept_array = array_unique($dept_array);
//        $server_data = array();
//        $idData = array();
//        foreach ($dept_array as $id => $deptname) {
//            foreach ($counter as $key => $data) {
//                $dept = array_keys($data);
//                if (!in_array($deptname, $dept)) {
//                    $data = 0;
//                } else {
//                    $data = $counter[$key][$deptname];
//                }
//                $server_data[$deptname][] = $data;
//                $idData[$deptname] = $id;
//            }
//        }
//        foreach ($date_array as $key => $converteddatearray) {
//            $converted_date_array[$key] = $this->Common->dateConvertDisplay($converteddatearray);
//        }
////       pr($server_data);die;
//        #pr($counter);die;
//        if (!empty($counter)) {
//            $counter = $server_data;
//            $counter = json_encode(array('counter' => $counter, 'date_array' => $converted_date_array));
//            $idData = json_encode($idData);
//        }
//
        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
//
//        //=====common array to be used in export
        $arrayendorsementdetail = $this->Common->arrayforendorsementdetail($endorsementdata);
        $this->Session->write('orgid', $organization_id);
        $this->Session->write('datearray', array("startdate" => $startdate, "enddate" => $enddate));

        //=========================chart 5 endorsement by job title
        //========bind model functionality in common
//        $this->Common->bindmodelcommonjobtitle();
//        $jobtitles = $this->Common->getorgjobtitles($organization_id);
//        $jobtitlesid = array_keys($jobtitles);
//
//        $conditionsjobtitles = array(
//            "UserOrganization.job_title_id" => $jobtitlesid,
//            "UserOrganization.organization_id" => $organization_id,
//            //"UserOrganization.status" => 1, 
//            "Endorsement.organization_id" => $organization_id,
//                //"Endorsement.endorsement_for" => "user"   
//        );
//        if ($startdate != "" and $enddate != "") {
//            array_push($conditionsjobtitles, "date(Endorsement.created) between '$startdate' and '$enddate'");
//        }
//        //=============using below query
//        /* select user_organizations.job_title_id, count(*) from user_organizations inner join endorsements on user_organizations.user_id = endorsements.endorser_id where endorsements.organization_id = 335 and  user_organizations.job_title_id in (550,551,552) and user_organizations.organization_id  = 335 and  user_organizations.status = 1  group by  user_organizations.job_title_id
//          select user_organizations.job_title_id, count(*) from user_organizations inner join endorsements on user_organizations.user_id = endorsements.endorsed_id  where endorsements.organization_id = 335 and endorsements.endorsement_for = "user" and  user_organizations.job_title_id in (550,551,552) and user_organizations.organization_id  = 335 and  user_organizations.status = 1  group by  user_organizations.job_title_id */
//        //=============using below query
//        $groupjobtitle = array("UserOrganization.job_title_id");
//        $fieldsjobtitle = array("UserOrganization.job_title_id", "count(DISTINCT Endorsement.id)");
//        //$this->UserOrganization->virtualfield["counterjobtitle"] = ""
//        $jobtitledataendorsed = $this->UserOrganization->find("all", array("conditions" => $conditionsjobtitles, "group" => $groupjobtitle, "fields" => $fieldsjobtitle));
//
//        $jbiddata = array();
//        foreach ($jobtitledataendorsed as $endorserjbdata) {
//            $jbiddata[$endorserjbdata["UserOrganization"]["job_title_id"]] = $endorserjbdata[0]["count(DISTINCT Endorsement.id)"];
//        }
//
//        $detailedjobtitlechart = array("data" => $jbiddata, "jobtitles" => $jobtitles);
        $detailedjobtitlechart = array();
        //=======================end job title chart 5
        //======================chart 6 for endorsement by entity/ suborganizations
//        $entityarray = $this->Common->getorgentities($organization_id);
//        $conditionsentity = array("Endorsement.endorsement_for" => "entity", "Endorsement.organization_id" => $organization_id, "Endorsement.type" => "guest");
//        if ($startdate != "" and $enddate != "") {
//            array_push($conditionsentity, "date(Endorsement.created) between '$startdate' and '$enddate'");
//        }
//        $fieldsentity = array("Endorsement.endorsed_id, count(*)");
//        $groupentity = array("Endorsement.endorsed_id");
//        $entityiddata = array();
//        $endorsementdataentity = $this->Endorsement->find("all", array("conditions" => $conditionsentity, "group" => $groupentity, "fields" => $fieldsentity));
//        foreach ($endorsementdataentity as $entitydata) {
//            $entityiddata[$entitydata["Endorsement"]["endorsed_id"]] = $entitydata[0]["count(*)"];
//        }
//        $detailedentitychart = array("data" => $entityiddata, "entites" => $entityarray);
        $detailedentitychart = array();
        //pr($detailedentitychart); exit;
        //======================end chart 6 for endorsement by entity/ suborganizations
        $datesarray = array("startdate" => $startdate, "enddate" => $enddate);
        $this->set(compact("authUser", "organization_id", "arrayendorsementdetail", 'companydetail', 'endorsementbyday', 'leaderboard', 'counter', 'startofweekarray', 'datesarray', 'allvaluesendorsement', 'orgcorevaluesandcode', 'detailedjobtitlechart', 'detailedentitychart', 'resultantendorsementbyDept', 'allvaluesfordeptandentity', 'idData'));
    }

    /* Added by Babulal Prasad @04-dec-2019 */

    function orgreport($organization_id = "null") {
        ini_set('memory_limit', '-1');
        $result = $this->Common->checkorgid($organization_id);
        $this->loadModel('OrgSubcenter');
        $this->loadModel('Endorsement');
        $this->loadModel('SubcenterDepartment');

        //=============to redirect if orgid is wrong
        if ($result == "redirect") {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }
        $startdate = $enddate = '';
        $authUser = $this->Auth->User();
        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
        $datesarray = array("startdate" => $startdate, "enddate" => $enddate);

        /* nDorserment History By Day Query */
        $conditionsendorsementbyday["organization_id"] = $organization_id;
        $conditionsendorsementbyday['type !='] = 'guest';
        $conditionsendorsementbyday[] = 'MONTH(created)=MONTH(CURRENT_DATE())';
        $conditionsendorsementbyday[] = 'YEAR(created) =YEAR(CURRENT_DATE())';
        if ($startdate != "" and $enddate != "") {
            array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
        }

        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues', 'EndorseHashtag')));
        $endorsementLeaderboardData = $this->Endorsement->find("all", array("conditions" => $conditionsendorsementbyday, "group" => "date(Endorsement.created)", "fields" => array("*")));
//        echo $this->Endorsement->getLastQuery();
//        exit;
//        pr($endorsementLeaderboardData);
//        exit;

        /* LeaderBoard counts */
//        pr($endorsementLeaderboardData); exit;
        $subcenterIDarray = $subcenterNdorsementArray = $subcenterDepartmentNdorsementArray = $usersNdorsementsCounts = $userlisting = array();
//        pr($endorsementLeaderboardData);
//        exit;

        if (!empty($endorsementLeaderboardData)) {
            foreach ($endorsementLeaderboardData as $index => $dataC) {
//                pr($dataC);
//                exit;

                /* CALCULATION FOR USER's nDorsement */

                if ($dataC['Endorsement']['endorsement_for'] == 'user') {
                    $receivedUserId = $dataC['Endorsement']['endorsed_id'];
                    $senderUserId = $dataC['Endorsement']['endorser_id'];
                    $userlisting[$receivedUserId] = $receivedUserId;
                    $userlisting[$senderUserId] = $senderUserId;
                    if (isset($usersNdorsementsCounts[$senderUserId]['sent'])) {
                        $usersNdorsementsCounts[$senderUserId]['sent'] ++;
                    } else {
                        $usersNdorsementsCounts[$senderUserId]['sent'] = 1;
                    }

                    if (isset($usersNdorsementsCounts[$receivedUserId]['received'])) {
                        $usersNdorsementsCounts[$receivedUserId]['received'] ++;
                    } else {
                        $usersNdorsementsCounts[$receivedUserId]['received'] = 1;
                    }
                }

                /* CALCULATION FOR SUBCENTER */
                //nDorsements received by subcenters 
                if ($dataC['Endorsement']['subcenter_for'] != '' && $dataC['Endorsement']['subcenter_for'] != 0) {
                    if (isset($subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'])) {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'] ++;
                    } else {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'] = 1;
                    }
                }
                //nDorsements Given by Subcenters
                if ($dataC['Endorsement']['subcenter_by'] != '' && $dataC['Endorsement']['subcenter_by'] != 0) {
                    if (isset($subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'])) {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'] ++;
                    } else {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'] = 1;
                    }
                }
            }
        }

//        pr($userlisting);
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));
        $totalUserOfOrg = $this->UserOrganization->find('all', array(
            'fields' => array('UserOrganization.department_id,UserOrganization.user_id'),
            'conditions' => array('user_id' => $userlisting, 'UserOrganization.status' => 1, 'UserOrganization.organization_id' => $organization_id)));
//        echo $this->UserOrganization->getLastQuery();
//        pr($totalUserOfOrg);
//        pr($usersNdorsementsCounts);
//        exit;
//        pr($subcenterNdorsementArray);
//        exit;
        //Getting all subcenters
        $subCenterData = $this->OrgSubcenter->find('all', array('conditions' => array('org_id' => $organization_id, 'status' => 1), 'order' => array('OrgSubcenter.short_name')));
        $subCenterArray = array();
        if (isset($subCenterData) && !empty($subCenterData)) {
            foreach ($subCenterData as $index => $scDATA) {
                $temp = $scDATA['OrgSubcenter'];
                $subCenterArray[$temp['id']] = $temp['short_name'];
            }
        }

        //Getting all subcenters and Departments
        $params['fields'] = "OrgDepartments.name as OrgDeptName,SubcenterDepartment.department_id as deptID,SubcenterDepartment.id as SdeptID,SubcenterDepartment.subcenter_id as SubcenterID,OrgSubcenter.short_name";
        $conditionarray["SubcenterDepartment.org_id"] = $organization_id;
        $conditionarray["SubcenterDepartment.status"] = 1;
        $conditionarray["SubcenterDepartment.status"] = 1;
        $params['conditions'] = $conditionarray;
        $params['joins'] = array(
            array(
                'table' => 'org_departments',
                'alias' => 'OrgDepartments',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgDepartments.id = SubcenterDepartment.department_id'
                )
            ),
            array(
                'table' => 'org_subcenters',
                'alias' => 'OrgSubcenter',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgSubcenter.id = SubcenterDepartment.subcenter_id'
                )
            ),
        );
        $params['order'] = 'OrgDeptName';
        $subcenterDepartment = $this->SubcenterDepartment->find("all", $params);
        $subcenterDepartmentArray = $dataSubcenterDeptFilterArray = array();
        if (isset($subcenterDepartment) && !empty($subcenterDepartment)) {
            foreach ($subcenterDepartment as $index => $data) {
                $scID = $data['SubcenterDepartment']['SdeptID'];
                $subCenterID = $data['SubcenterDepartment']['SubcenterID'];
                $subcenterDepartmentArray[$scID]['dept_id'] = $data['SubcenterDepartment']['deptID'];
                $subcenterDepartmentArray[$scID]['dept_name'] = $data['OrgDepartments']['OrgDeptName'];
                $subcenterDepartmentArray[$scID]['subcenter_name'] = $data['OrgSubcenter']['short_name'];
                $subcenterDepartmentArray[$scID]['subcenter_id'] = $subCenterID;
                $dataSubcenterDeptFilterArray[$subCenterID][$scID] = $subcenterDepartmentArray[$scID];
            }
        }


        //Getting all Users and subcenters and Departments
        $params = $conditionarray = array();
        $params['fields'] = "User.id,concat(User.fname,' ',User.lname) as user_name,OrgSubcenter.short_name,OrgDepartment.name as dept_name,OrgJobTitle.title,UserOrganization.subcenter_id as subcenterID,UserOrganization.department_id as deptID";
        $conditionarray["UserOrganization.organization_id"] = $organization_id;
        $conditionarray["UserOrganization.status"] = 1;
        $params['conditions'] = $conditionarray;
        $params['joins'] = array(
            array(
                'table' => 'user_organizations',
                'alias' => 'UserOrganization',
                'type' => 'LEFT',
                'conditions' => array(
                    'UserOrganization.user_id = User.id'
                )
            ),
            array(
                'table' => 'org_subcenters',
                'alias' => 'OrgSubcenter',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgSubcenter.id = UserOrganization.subcenter_id'
                )
            ),
            array(
                'table' => 'org_departments',
                'alias' => 'OrgDepartment',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgDepartment.id = UserOrganization.department_id'
                )
            ),
            array(
                'table' => 'org_job_titles',
                'alias' => 'OrgJobTitle',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgJobTitle.id = UserOrganization.job_title_id'
                )
            ),
        );
        $params['order'] = 'user_name';
        $userSubcenterData = $this->User->find("all", $params);
        $orgAllUserDataArray = array();
//        pr($userSubcenterData); exit;
        if (isset($userSubcenterData) && !empty($userSubcenterData)) {
            foreach ($userSubcenterData as $index => $uUScData) {
                $userId = $uUScData['User']['id'];
                $subCenterID = ($uUScData['UserOrganization']['subcenterID'] != '') ? $uUScData['UserOrganization']['subcenterID'] : 0;
                $deptID = ($uUScData['UserOrganization']['deptID'] != '') ? $uUScData['UserOrganization']['deptID'] : 0;
                $orgAllUserDataArray[$userId]['name'] = $uUScData[0]['user_name'];
                $orgAllUserDataArray[$userId]['subcenter_name'] = $uUScData['OrgSubcenter']['short_name'];
                $orgAllUserDataArray[$userId]['dept_name'] = $uUScData['OrgDepartment']['dept_name'];
                $orgAllUserDataArray[$userId]['user_title'] = $uUScData['OrgJobTitle']['title'];
                $orgAllUserDataArray[$userId]['dept_id'] = $deptID;
                $orgAllUserDataArray[$userId]['subcenter_id'] = $subCenterID;
            }
        }
        $this->set(compact('subCenterArray', 'subcenterDepartmentArray', 'orgAllUserDataArray', 'authUser', 'companydetail', 'organization_id', 'datesarray', 'subcenterNdorsementArray', 'usersNdorsementsCounts'));
    }

    function orgreport_BK_05_FEB_2020($organization_id = "null") {
        ini_set('memory_limit', '-1');
        $result = $this->Common->checkorgid($organization_id);

        //=============to redirect if orgid is wrong
        if ($result == "redirect") {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }
        $startdate = $enddate = '';
        $authUser = $this->Auth->User();
        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
        $datesarray = array("startdate" => $startdate, "enddate" => $enddate);



        //Getting all subcenters
        $this->loadModel('OrgSubcenter');
        $this->loadModel('SubcenterDepartment');
        $subCenterData = $this->OrgSubcenter->find('all', array('conditions' => array('org_id' => $organization_id, 'status' => 1), 'order' => array('OrgSubcenter.short_name')));
        $subCenterArray = array();
        if (isset($subCenterData) && !empty($subCenterData)) {
            foreach ($subCenterData as $index => $scDATA) {
                $temp = $scDATA['OrgSubcenter'];
                $subCenterArray[$temp['id']] = $temp['short_name'];
            }
        }

        //Getting all subcenters and Departments
        $params['fields'] = "OrgDepartments.name as OrgDeptName,SubcenterDepartment.department_id as deptID,SubcenterDepartment.id as SdeptID,SubcenterDepartment.subcenter_id as SubcenterID,OrgSubcenter.short_name";
        $conditionarray["SubcenterDepartment.org_id"] = $organization_id;
        $conditionarray["SubcenterDepartment.status"] = 1;
        $conditionarray["SubcenterDepartment.status"] = 1;
        $params['conditions'] = $conditionarray;
        $params['joins'] = array(
            array(
                'table' => 'org_departments',
                'alias' => 'OrgDepartments',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgDepartments.id = SubcenterDepartment.department_id'
                )
            ),
            array(
                'table' => 'org_subcenters',
                'alias' => 'OrgSubcenter',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgSubcenter.id = SubcenterDepartment.subcenter_id'
                )
            ),
        );
        $params['order'] = 'OrgDeptName';
        $subcenterDepartment = $this->SubcenterDepartment->find("all", $params);
        $subcenterDepartmentArray = $dataSubcenterDeptFilterArray = array();
        if (isset($subcenterDepartment) && !empty($subcenterDepartment)) {
            foreach ($subcenterDepartment as $index => $data) {
                $scID = $data['SubcenterDepartment']['SdeptID'];
                $subCenterID = $data['SubcenterDepartment']['SubcenterID'];
                $subcenterDepartmentArray[$scID]['dept_name'] = $data['SubcenterDepartment']['deptID'];
                $subcenterDepartmentArray[$scID]['dept_name'] = $data['OrgDepartments']['OrgDeptName'];
                $subcenterDepartmentArray[$scID]['subcenter_name'] = $data['OrgSubcenter']['short_name'];
                $subcenterDepartmentArray[$scID]['subcenter_id'] = $subCenterID;
                $dataSubcenterDeptFilterArray[$subCenterID][$scID] = $subcenterDepartmentArray[$scID];
            }
        }


        //Getting all Users and subcenters and Departments
        $params = $conditionarray = array();
        $params['fields'] = "User.id,concat(User.fname,' ',User.lname) as user_name,OrgSubcenter.short_name,OrgDepartment.name as dept_name,OrgJobTitle.title,UserOrganization.subcenter_id as subcenterID,UserOrganization.department_id as deptID";
        $conditionarray["UserOrganization.organization_id"] = $organization_id;
        $conditionarray["UserOrganization.status"] = 1;
        $params['conditions'] = $conditionarray;
        $params['joins'] = array(
            array(
                'table' => 'user_organizations',
                'alias' => 'UserOrganization',
                'type' => 'LEFT',
                'conditions' => array(
                    'UserOrganization.user_id = User.id'
                )
            ),
            array(
                'table' => 'org_subcenters',
                'alias' => 'OrgSubcenter',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgSubcenter.id = UserOrganization.subcenter_id'
                )
            ),
            array(
                'table' => 'org_departments',
                'alias' => 'OrgDepartment',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgDepartment.id = UserOrganization.department_id'
                )
            ),
            array(
                'table' => 'org_job_titles',
                'alias' => 'OrgJobTitle',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrgJobTitle.id = UserOrganization.job_title_id'
                )
            ),
        );
        $params['order'] = 'user_name';
        $userSubcenterData = $this->User->find("all", $params);
        $orgAllUserDataArray = array();
//        pr($userSubcenterData); exit;
        if (isset($userSubcenterData) && !empty($userSubcenterData)) {
            foreach ($userSubcenterData as $index => $uUScData) {
                $userId = $uUScData['User']['id'];
                $subCenterID = ($uUScData['UserOrganization']['subcenterID'] != '') ? $uUScData['UserOrganization']['subcenterID'] : 0;
                $deptID = ($uUScData['UserOrganization']['deptID'] != '') ? $uUScData['UserOrganization']['deptID'] : 0;
                $orgAllUserDataArray[$userId]['name'] = $uUScData[0]['user_name'];
                $orgAllUserDataArray[$userId]['subcenter_name'] = $uUScData['OrgSubcenter']['short_name'];
                $orgAllUserDataArray[$userId]['dept_name'] = $uUScData['OrgDepartment']['dept_name'];
                $orgAllUserDataArray[$userId]['user_title'] = $uUScData['OrgJobTitle']['title'];
                $orgAllUserDataArray[$userId]['dept_id'] = $deptID;
                $orgAllUserDataArray[$userId]['subcenter_id'] = $subCenterID;
            }
        }


        $this->set(compact('subCenterArray', 'subcenterDepartmentArray', 'orgAllUserDataArray', 'authUser', 'companydetail', 'organization_id', 'datesarray'));
    }

    function orgreportoverall($organization_id = "null") {
        ini_set('memory_limit', '-1');
        $result = $this->Common->checkorgid($organization_id);

        //=============to redirect if orgid is wrong
        if ($result == "redirect") {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }


//        pr($this->request->data); //exit;
        $startdate = $enddate = '';

        if (!empty($this->request->data["startdaterandc"]) && !empty($this->request->data["enddaterandc"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdaterandc"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddaterandc"]);
        }
//        echo $startdate ; exit;




        $authUser = $this->Auth->User();
        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
//        pr($companydetail); exit;
        //Getting all subcenters
        $this->loadModel('Endorsement');
        $this->loadModel('OrgSubcenter');
        $this->loadModel('OrgHashtags');
        $this->loadModel('SubcenterDepartment');
        $subCenterData = $this->OrgSubcenter->find('all', array('conditions' => array('org_id' => $organization_id, 'status' => 1),
//            'order' => array('OrgSubcenter.short_name')
        ));
//        pr($subCenterData);
//        exit;
        $subCenterArray = array();
        if (isset($subCenterData) && !empty($subCenterData)) {
            foreach ($subCenterData as $index => $scDATA) {
                $temp = $scDATA['OrgSubcenter'];
                $subCenterArray[$temp['id']] = $temp['short_name'];
            }
        }

        $HashtagData = $this->OrgHashtags->find('all', array('conditions' => array('org_id' => $organization_id, 'status' => 1)));
        $hashtagArray = $hashtagIDArray = array();
        if (isset($HashtagData) && !empty($HashtagData)) {
            foreach ($HashtagData as $index => $scDATA) {
                $tempo = $scDATA['OrgHashtags'];
                $hashtagArray[$tempo['id']] = $tempo['name'];
                $hashtagIDArray[] = $tempo['id'];
            }
        }

        //=======================================endorsement all feature
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcodeForReports($organization_id);
//        pr($orgcorevaluesandcode); exit;
        //$this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies','EndorseHashtag')));
        $condtionsallendorsement["organization_id"] = $organization_id;
        $condtionsallendorsement["type !="] = array("guest", "daisy");


        if ($startdate != "" and $enddate != "") {
            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
        }
        //Babulal prasad
//          echo $this->Endorsement->getLastQuery(); //exit;
//                    pr($condtionsallendorsement); exit;
        ini_set('memory_limit', '-1');
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies', 'EndorseHashtag')));

        $allendorsement = $this->Endorsement->find("all", array(
            'fields' => array(/* 'Endorsement.*', */ "count(Endorsement.id) as total"),
            'joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
            /* array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id')) */
            ),
//            "order" => "Endorsement.created DESC", 
            "conditions" => $condtionsallendorsement));

//        pr($allendorsement); //exit;
//        echo "<hr>";
//        echo $this->Endorsement->getLastQuery();
//        exit;



        $rd = array();
//        foreach ($allendorsement as $index => $data) {
//            $endorseId = $data['Endorsement']['id'];
//            $rd[$endorseId]['Endorsement'] = $data['Endorsement'];
////            $rd[$endorseId]['EndorseCoreValues'][] = $data['EndorseCoreValues'];
//        }
//        $allendorsement = $rd;
//        $allvaluesendorsement = count($allendorsement);

        $allvaluesendorsement = $allendorsement[0][0]['total'];

        $monthWiseCondition = $condtionsallendorsement;
        if ($startdate != "" and $enddate != "") {
            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
        } else {
            array_push($condtionsallendorsement, "month(Endorsement.created) = month(NOW())", "year(Endorsement.created) = year(NOW())");
            $d = new DateTime('first day of this month');
//            $startdate = $d->format('d-m-Y');
//            $startdate = $d->format('Y-m-d');
            //$enddate = date('m-d-Y', time());
//            $enddate = date('Y-m-d', time());
        }


//echo $startdate; exit;
//        pr($condtionsallendorsement); exit;

        /*         * * CURRENT MONTH Endorsement DATA ** */

        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies', 'EndorseHashtag')));

        $allendorsementmnthly = $this->Endorsement->find("all", array(
            'fields' => array(/* 'EndorseCoreValues.*', */ "count(Endorsement.id) as total"),
            'joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
            /* array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id')) */
            ),
//            "order" => "Endorsement.created DESC",
            "conditions" => $condtionsallendorsement));


//        echo $this->Endorsement->getLastQuery();
//        exit;

        $rd1 = array();
//        foreach ($allendorsementmnthly as $index => $data) {
//            $endorseId = $data['Endorsement']['id'];
//            $rd1[$endorseId]['Endorsement'] = $data['Endorsement'];
////            $rd1[$endorseId]['EndorseCoreValues'][] = $data['EndorseCoreValues'];
//        }
//        $allendorsementMonthly = $rd1;
////        pr($allendorsementMonthly);
////        exit;
        $allvaluesendorsementMonthly = $allendorsementmnthly[0][0]['total'];


        $until = new DateTime();
        if ($organization_id == 0) {//426 //415
            $interval = new DateInterval('P1M'); //3 months
        } else {
            $interval = new DateInterval('P12M'); //12 months
        }

        $from = $until->sub($interval);
        $last12Mnth = $from->format('Y-m-t');

        /* LAST 12 Month Endorsement DATA */
        $currentDATE = date('Y-m-d h:i:s', time());

//        array_push($monthWiseCondition, "created > DATEADD(year, -1, " . $currentDATE . ")");
//            array_push($monthWiseCondition, "month(created) >= dateadd(month,datediff(month,0,getdate())-12,0)");
//        $d = new DateTime('first day of this month');
//        $startdate = $d->format('d-m-Y');
        //$enddate = date('m-d-Y', time());
//        $enddate = date('Y-m-d', time());

        if ($startdate != "" and $enddate != "") {
            array_push($monthWiseCondition, "date(Endorsement.created) between '$startdate' and '$enddate'");
        } else {
//            array_push($condtionsallendorsement, "month(Endorsement.created) = month(NOW())", "year(Endorsement.created) = year(NOW())");
//            $d = new DateTime('first day of this month');
//            $startdate = $d->format('d-m-Y');
//            //$enddate = date('m-d-Y', time());
//            $enddate = date('Y-m-d', time());
//            
            $monthWiseCondition["Endorsement.created >"] = $last12Mnth;
        }


//
//        pr($monthWiseCondition);
//        exit;
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies', 'EndorseHashtag')));
//        $monthwiseEndorsementmnthly = $this->Endorsement->find("all", array(
//            'fields' => array('EndorseCoreValues.id', 'EndorseCoreValues.value_id',
//                'Endorsement.created', 'Endorsement.subcenter_for', 'Endorsement.subcenter_by', 'Endorsement.id',
//                'EndorseHashtag.id', 'EndorseHashtag.hashtag_id'
//            /* , 'OrgSubcenter.*' */            ),
//            'joins' => array(
//                array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id')),
//                array('table' => 'endorse_hashtags', 'alias' => 'EndorseHashtag', 'type' => 'LEFT', 'conditions' => array('EndorseHashtag.endorsement_id = Endorsement.id')),
//            /* array('table' => 'org_subcenters', 'alias' => 'OrgSubcenter', 'type' => 'LEFT', 'conditions' => array('OR' => array('OrgSubcenter.id = Endorsement.subcenter_for', 'OrgSubcenter.id = Endorsement.subcenter_by'))), */
//            ),
////            "group" => "Endorsement.id",
//            //"order" => "Endorsement.created DESC",
//            "conditions" => $monthWiseCondition));

        $monthwiseEndorsementmnthly = $this->Endorsement->find("all", array(
            'fields' => array('Endorsement.created', 'Endorsement.subcenter_for', 'Endorsement.subcenter_by', 'Endorsement.id'),
//            "group" => "Endorsement.id",
            //"order" => "Endorsement.created DESC",
            "conditions" => $monthWiseCondition));
//
//        echo $this->Endorsement->getLastQuery();
//        exit;
//        pr($monthwiseEndorsementmnthly);
//        exit;
        $endorseSubcenterData = $hashTagEndorsement = $subcenterEndorsmentsFor = $subcenterEndorsmentsBy = array();
        //$subCenterArray;
        $monthwiseallData = array();
        $endorsementAllData = array();
        $endorsementIds = array();



        foreach ($monthwiseEndorsementmnthly as $index => $data) {
            $month = date("M-y", strtotime($data['Endorsement']['created']));
            $monthwiseallData[$month][] = $data;
        }


        $dataArray = array();
        $endorsementMonthwiseArray = array();
        $coreValuesMonthWiseArray = array();
        $hashtagsMonthWiseArray = array();
        $corevaluesIDsArray = array();
        $subcenterIDsArray = array();
//        pr($monthwiseallData); exit;
        foreach ($monthwiseallData as $monthIndex => $mnthData) {
            foreach ($mnthData as $index => $data) {
//                pr($data);
                $endorseId = $data['Endorsement']['id'];
                $endorsementIds[] = $endorseId;
                //DATA CALCULATION FOR ORGANIZATION ENDORSEMENT
                if (!isset($endorsementMonthwiseCounts[$monthIndex][$endorseId])) {
                    $endorsementMonthwiseCounts[$monthIndex][$endorseId] = $endorseId;
                }

                //DATA CALCULATION FOR SUBCENTERS

                if ($data['Endorsement']['subcenter_for'] != '' && $data['Endorsement']['subcenter_for'] != 0) {
                    if (!isset($endorseSubcenterData[$monthIndex][$endorseId][$data['Endorsement']['subcenter_for']])) {
                        $subcenterFor = 0;
                        if (isset($subcenterEndorsmentsFor[$monthIndex][$data['Endorsement']['subcenter_for']])) {
                            $subcenterFor = $subcenterEndorsmentsFor[$monthIndex][$data['Endorsement']['subcenter_for']];
                        }
                        $subcenterEndorsmentsFor[$monthIndex][$data['Endorsement']['subcenter_for']] = $subcenterFor + 1;
                        $endorseSubcenterData[$monthIndex][$endorseId][$data['Endorsement']['subcenter_for']] = $subcenterFor + 1;
                        $subcenterIDsArray[$data['Endorsement']['subcenter_for']] = $data['Endorsement']['subcenter_for'];
                    }
                }
                if ($data['Endorsement']['subcenter_by'] != '' && $data['Endorsement']['subcenter_by'] != 0) {

                    if (!isset($endorseSubcenterData[$monthIndex][$endorseId][$data['Endorsement']['subcenter_by']])) {
                        $subcenterBy = 0;
                        if (isset($subcenterEndorsmentsBy[$monthIndex][$data['Endorsement']['subcenter_by']])) {
                            $subcenterBy = $subcenterEndorsmentsBy[$monthIndex][$data['Endorsement']['subcenter_by']];
                        }
                        $subcenterEndorsmentsBy[$monthIndex][$data['Endorsement']['subcenter_by']] = $subcenterBy + 1;
                        $endorseSubcenterData[$monthIndex][$endorseId][$data['Endorsement']['subcenter_by']] = $subcenterBy + 1;
                    }
                }

//                //DATA CALCULATION FOR CORE VALUES
//                if (isset($data['EndorseCoreValues']['id'])) {
//                    $endrsCoreValueID = $data['EndorseCoreValues']['id'];
//                    $coreValueID = $data['EndorseCoreValues']['value_id'];
//                    $coreValuesMonthWiseArray[$monthIndex][$coreValueID][$endrsCoreValueID] = $endrsCoreValueID;
//                    $corevaluesIDsArray[$coreValueID] = $coreValueID;
//                }
//
//                //DATA CALCULATION FOR HASHTAGS
//                if (isset($data['EndorseHashtag']['id'])) {
//                    $endrsHashtagID = $data['EndorseHashtag']['id'];
//                    $hashtagID = $data['EndorseHashtag']['hashtag_id'];
//                    $hashtagsMonthWiseArray[$monthIndex][$hashtagID][$endrsHashtagID] = $endrsHashtagID;
//                }
            }
        }


        $this->loadModel('EndorseCoreValues');
        $monthwiseEndorsementCoreValuesMnthly = $this->EndorseCoreValues->find("all", array(
            'fields' => array('EndorseCoreValues.id', 'EndorseCoreValues.value_id', 'EndorseCoreValues.endorsement_id', 'Endorsement.created'),
            'joins' => array(
                array('table' => 'endorsements', 'alias' => 'Endorsement', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id')),
            ),
//            "group" => "Endorsement.id",
            //"order" => "Endorsement.created DESC",
            "conditions" => array('endorsement_id' => $endorsementIds)));
//        pr($monthwiseEndorsementCoreValuesMnthly);
//        exit;
        $monthwiseCVData = array();
        foreach ($monthwiseEndorsementCoreValuesMnthly as $index => $data) {
            $month = date("M-y", strtotime($data['Endorsement']['created']));
            $monthwiseCVData[$month][] = $data;
        }
//        pr($monthwiseCVData);
//        exit;
        foreach ($monthwiseCVData as $monthIndex1 => $mnthcvData) {
            foreach ($mnthcvData as $index1 => $cvData) {
                //DATA CALCULATION FOR CORE VALUES
                if (isset($cvData['EndorseCoreValues']['id'])) {
                    $endrsCoreValueID = $cvData['EndorseCoreValues']['id'];
                    $coreValueID = $cvData['EndorseCoreValues']['value_id'];
                    $coreValuesMonthWiseArray[$monthIndex1][$coreValueID][$endrsCoreValueID] = $endrsCoreValueID;
                    $corevaluesIDsArray[$coreValueID] = $coreValueID;
                }
            }
        }


        $this->loadModel('EndorseHashtag');
        $monthwiseHashtag = $this->EndorseHashtag->find("all", array(
            'fields' => array('EndorseHashtag.id', 'EndorseHashtag.hashtag_id', 'EndorseHashtag.endorsement_id', 'Endorsement.created'),
            'joins' => array(
                array('table' => 'endorsements', 'alias' => 'Endorsement', 'type' => 'LEFT', 'conditions' => array('EndorseHashtag.endorsement_id = Endorsement.id')),
            ),
//            "group" => "Endorsement.id",
            //"order" => "Endorsement.created DESC",
            "conditions" => array('endorsement_id' => $endorsementIds)));
//        pr($monthwiseHashtag);
//        exit;
        $monthwiseHashData = array();
        foreach ($monthwiseHashtag as $index2 => $data2) {
            $month = date("M-y", strtotime($data2['Endorsement']['created']));
            $monthwiseHashData[$month][] = $data2;
        }
//        pr($monthwiseCVData);
//        exit;
        foreach ($monthwiseHashData as $monthIndex2 => $mnthhashData) {
            foreach ($mnthhashData as $index2 => $hashData) {
//                //DATA CALCULATION FOR HASHTAGS
                $endrsHashtagID = $hashData['EndorseHashtag']['id'];
                $hashtagID = $hashData['EndorseHashtag']['hashtag_id'];
                $hashtagsMonthWiseArray[$monthIndex2][$hashtagID][$endrsHashtagID] = $endrsHashtagID;
            }
        }
//        pr($hashtagsMonthWiseArray);
//        exit;
//        
//        
        //Calculation for dynamic months range on date range selection
        $date1 = new DateTime($enddate);
        $date2 = new DateTime($startdate);
        $diff = $date1->diff($date2);
        $monthsDiff = (($diff->format('%y') * 12) + $diff->format('%m'));



        if ($startdate != "" and $enddate != "") {
            for ($i = 0; $i <= $monthsDiff; $i++) {
                $months[] = date("M-y", strtotime($enddate . " -$i months"));
            }
            $months = array_reverse($months);
        } else {
            for ($i = 0; $i <= 11; $i++) {
                $months[] = date("M-y", strtotime(date('Y-m-01') . " -$i months"));
            }
            $months = array_reverse($months);
        }
//        pr($months );exit;




        $hashtagsCountArray = $corevaluesCountArray = $orgEndorsementsCountArray = $subcenterCountArray = array();

        foreach ($months as $index => $monthID) {

            //Monthwise Hashtag Count Data
            if (isset($hashtagsMonthWiseArray[$monthID])) {
                foreach ($hashtagIDArray as $indx => $hashtagID) {
                    if (isset($hashtagsMonthWiseArray[$monthID][$hashtagID])) {
                        $hData = $hashtagsMonthWiseArray[$monthID][$hashtagID];
                        $hashtagsCountArray[$monthID][$hashtagID] = count($hData);
                    } else {
                        $hashtagsCountArray[$monthID][$hashtagID] = 0;
                    }
                }
            } else {
                foreach ($hashtagIDArray as $indx => $hashtagID) {
                    $hashtagsCountArray[$monthID][$hashtagID] = 0;
                }
            }

            //Monthwise Core values Count Data
            if (isset($coreValuesMonthWiseArray[$monthID])) {
                foreach ($corevaluesIDsArray as $indx => $cvID) {
                    if (isset($coreValuesMonthWiseArray[$monthID][$cvID])) {
                        $cvData = $coreValuesMonthWiseArray[$monthID][$cvID];
                        $corevaluesCountArray[$monthID][$cvID] = count($cvData);
                    } else {
                        $corevaluesCountArray[$monthID][$cvID] = 0;
                    }
                }
            } else {
                foreach ($corevaluesIDsArray as $indx => $cvID) {
                    $corevaluesCountArray[$monthID][$cvID] = 0;
                }
            }

            //Monthwise Org Endorsement Count Data
            if (isset($endorsementMonthwiseCounts[$monthID])) {
                $orgEndorsementsCountArray[$monthID] = count($endorsementMonthwiseCounts[$monthID]);
            } else {
                foreach ($corevaluesIDsArray as $indx => $cvID) {
                    $orgEndorsementsCountArray[$monthID] = 0;
                }
            }

//            //Monthwise Core values Count Data
            if (isset($subcenterEndorsmentsFor[$monthID])) {

                foreach ($subcenterIDsArray as $indx => $scID) {
                    if (isset($subcenterEndorsmentsFor[$monthID][$scID])) {
                        $scData = $subcenterEndorsmentsFor[$monthID][$scID];
                        $subcenterCountArray[$monthID][$scID] = $scData;
                    } else {
                        $subcenterCountArray[$monthID][$scID] = 0;
                    }
                }
            } else {
                foreach ($subcenterIDsArray as $indx => $scID) {
                    $subcenterCountArray[$monthID][$scID] = 0;
                }
            }
        }

        $months = json_encode($months);

        $totalEndorsements = array();
        foreach ($orgEndorsementsCountArray as $monthID => $totalCount) {
            $totalEndorsements[] = array($totalCount);
        }
        $totalEndorsements = json_encode($totalEndorsements);



        $subcenterEndorsements = array();
        foreach ($subcenterCountArray as $monthID => $scData) {
//            pr($scData);
            foreach ($scData as $subcenterID => $sData) {
                $subcenterEndorsements[$subcenterID][] = $sData;
            }
        }
        $finalSubcenterData = array();
        //pr($subCenterArray); exit;
        foreach ($subcenterEndorsements as $subID => $subDataArray) {
            $subcenterName = '';
            if (isset($subCenterArray[$subID])) {
                $subcenterName = $subCenterArray[$subID];
            }
            $finalSubcenterData[] = array('data' => $subDataArray, 'name' => $subcenterName, 'id' => $subcenterName);
        }
        $finalSubcenterData = json_encode($finalSubcenterData);
//        pr($finalSubcenterData);
//        exit;


        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
//        pr($orgcorevaluesandcode[871]['name']);
        $finalCoreValueData = array();
        $corevaluesEndorsements = array();
//        pr($orgcorevaluesandcode);
//        pr($corevaluesCountArray); exit;

        foreach ($corevaluesCountArray as $monthID => $cvData) {
            foreach ($cvData as $coreVID => $cData) {
                $corevaluesEndorsements[$coreVID][] = $cData;
            }
        }
        foreach ($corevaluesEndorsements as $coreID => $cDataArray) {
            $corevalueName = '';
            if (isset($orgcorevaluesandcode[$coreID])) {
                $corevalueName = $orgcorevaluesandcode[$coreID]['name'];
            }
            $finalCoreValueData[] = array('data' => $cDataArray, 'name' => $corevalueName);
        }
        $finalCoreValueData = json_encode($finalCoreValueData);


        $finalHashtagsData = array();
        $hashtagsEndorsements = array();
        foreach ($hashtagsCountArray as $monthID => $hsData) {
            foreach ($hsData as $htID => $htData) {
                $hashtagsEndorsements[$htID][] = $htData;
            }
        }
        foreach ($hashtagsEndorsements as $hashtagID => $hashDataArray) {
            $hashtagName = '';
            if (isset($hashtagArray[$hashtagID])) {
                $hashtagName = $hashtagArray[$hashtagID];
            }
            $finalHashtagsData[] = array('data' => $hashDataArray, 'name' => $hashtagName);
        }
        $finalHashtagsData = json_encode($finalHashtagsData);


        $datesarray = array("startdate" => $startdate, "enddate" => $enddate);
        $this->set(compact('startdate', 'enddate', 'months', 'finalHashtagsData', 'finalCoreValueData', 'finalSubcenterData', 'totalEndorsements', 'hashtagArray', 'startdate', 'enddate', 'datesarray', 'subCenterArray', 'authUser', 'companydetail', 'organization_id', 'orgdata', 'allvaluesendorsement', 'allvaluesendorsementMonthly'));
//        pr($finalHashtagsData);exit;



        /* SECOND REPORT ORGREPORT 
         * 
          LEADERBOARD/ nDorsement DATA Report
         * 
         */





        $startdate = $enddate = '';

        if (!empty($this->request->data["startdaterandc_1"]) && !empty($this->request->data["enddaterandc_1"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdaterandc_1"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddaterandc_1"]);
        } else {
            $d = new DateTime('first day of this month');
            $startdate = $d->format('m-d-Y');
            $enddate = date('m-d-Y', time());
        }
//
//
//        /* nDorserment History By Day Query */
//        $conditionsendorsementbyday["organization_id"] = $organization_id;
//        $conditionsendorsementbyday['type !='] = array('guest', 'daisy');
//
//        if ($startdate != "" and $enddate != "") {
//            array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
//        } else {
//            $conditionsendorsementbyday[] = 'MONTH(created)=MONTH(CURRENT_DATE())';
//            $conditionsendorsementbyday[] = 'YEAR(created) =YEAR(CURRENT_DATE())';
//            $d = new DateTime('first day of this month');
//            $startdate = $d->format('d-m-Y');
//            //$enddate = date('m-d-Y', time());
//            $enddate = date('Y-m-d', time());
//        }
        $datesarray1 = array("startdate_1" => $startdate, "enddate_1" => $enddate);
////        echo $startdate . " // ";
////        echo $enddate; exit;
////        pr($conditionsendorsementbyday);
////        exit;
//        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues', 'EndorseHashtag')));
//        $endorsementLeaderboardData = $this->Endorsement->find("all", array(
////            'joins' => array(array('table' => 'user_organizations', 'type' => 'LEFT', 'conditions' => array('user_organizations.user_id = UserOrganization.user_id'))),
//            "conditions" => $conditionsendorsementbyday, /* "group" => "date(Endorsement.created)", */ "fields" => array("*")));
////        echo $this->Endorsement->getLastQuery();
////        exit;
////        pr($endorsementLeaderboardData);
////        exit;
//
//        /* LeaderBoard counts */
////        pr($endorsementLeaderboardData); exit;
//        $subcenterIDarray = $subcenterNdorsementArray = $usersNdorsementsCounts = $deptNdorsementCount = $userlisting = array();
////        pr($endorsementLeaderboardData);
////        exit;
//
//        if (!empty($endorsementLeaderboardData)) {
//
//
//            foreach ($endorsementLeaderboardData as $index => $dataC) {
//                if ($dataC['Endorsement']['endorsement_for'] == 'user') {
//                    $receivedUserId = $dataC['Endorsement']['endorsed_id'];
//                    $senderUserId = $dataC['Endorsement']['endorser_id'];
//                    $userlisting[$receivedUserId] = $receivedUserId;
//                    $userlisting[$senderUserId] = $senderUserId;
//                }
//            }
//
//            //        pr($userlisting);
//            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));
//            $totalUserOfOrg = $this->UserOrganization->find('all', array(
//                'fields' => array('UserOrganization.department_id,UserOrganization.user_id'),
//                'conditions' => array('user_id' => $userlisting, 'UserOrganization.status' => 1, 'UserOrganization.organization_id' => $organization_id)));
////        echo $this->UserOrganization->getLastQuery();exit;
//
//            $userOrgArray = array();
//            if (!empty($totalUserOfOrg)) {
//                foreach ($totalUserOfOrg as $index => $userData) {
//                    $deptID = $userData['UserOrganization']['department_id'];
//                    $userID = $userData['UserOrganization']['user_id'];
//                    $userOrgArray[$userID] = $deptID;
//                }
//            }
//
//            foreach ($endorsementLeaderboardData as $index => $dataC) {
////                pr($dataC);
////                exit;
//
//                /* CALCULATION FOR USER's nDorsement */
//
//                if ($dataC['Endorsement']['endorsement_for'] == 'user') {
//                    $receivedUserId = $dataC['Endorsement']['endorsed_id'];
//                    $senderUserId = $dataC['Endorsement']['endorser_id'];
//
//                    $deptIdSender = $userOrgArray[$senderUserId]; //nDorser
//                    $deptIdReceiver = $userOrgArray[$receivedUserId]; //nDorsed
//
//                    if (isset($deptNdorsementCount[$deptIdSender]['sent'])) {
//                        $deptNdorsementCount[$deptIdSender]['sent'] ++;
//                    } else {
//                        $deptNdorsementCount[$deptIdSender]['sent'] = 1;
//                    }
//
//                    if (isset($deptNdorsementCount[$deptIdReceiver]['received'])) {
//                        $deptNdorsementCount[$deptIdReceiver]['received'] ++;
//                    } else {
//                        $deptNdorsementCount[$deptIdReceiver]['received'] = 1;
//                    }
//
//
//
//
//                    if (isset($usersNdorsementsCounts[$senderUserId]['sent'])) {
//                        $usersNdorsementsCounts[$senderUserId]['sent'] ++;
//                    } else {
//                        $usersNdorsementsCounts[$senderUserId]['sent'] = 1;
//                    }
//
//                    if (isset($usersNdorsementsCounts[$receivedUserId]['received'])) {
//                        $usersNdorsementsCounts[$receivedUserId]['received'] ++;
//                    } else {
//                        $usersNdorsementsCounts[$receivedUserId]['received'] = 1;
//                    }
//                }
//
//                /* CALCULATION FOR SUBCENTER */
////                nDorsements received by subcenters 
//                if ($dataC['Endorsement']['subcenter_for'] != '' && $dataC['Endorsement']['subcenter_for'] != 0) {
//                    if (isset($subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'])) {
//                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'] ++;
//                    } else {
//                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'] = 1;
//                    }
//                }
//
//                //nDorsements Given by Subcenters
//                if ($dataC['Endorsement']['subcenter_by'] != '' && $dataC['Endorsement']['subcenter_by'] != 0) {
//                    if (isset($subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'])) {
//                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'] ++;
//                    } else {
//                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'] = 1;
//                    }
//                }
//            }
//        }
//
////        pr($deptNdorsementCount); exit;
//        //Getting all subcenters
////        $subCenterData = $this->OrgSubcenter->find('all', array('conditions' => array('org_id' => $organization_id, 'status' => 1), 'order' => array('OrgSubcenter.short_name')));
////        $subCenterArray = array();
////        if (isset($subCenterData) && !empty($subCenterData)) {
////            foreach ($subCenterData as $index => $scDATA) {
////                $temp = $scDATA['OrgSubcenter'];
////                $subCenterArray[$temp['id']] = $temp['short_name'];
////            }
////        }
//        
//        
//        $orgDepartments = $this->Common->getorgdepartments($organization_id);
////        pr($orgDepartments); exit;
//        
//        
//        //Getting all subcenters and Departments
//        $params['fields'] = "OrgDepartments.name as OrgDeptName, SubcenterDepartment.department_id as deptID, SubcenterDepartment.id as SdeptID, SubcenterDepartment.subcenter_id as SubcenterID, OrgSubcenter.short_name";
//        $conditionarray["SubcenterDepartment.org_id"] = $organization_id;
//        $conditionarray["SubcenterDepartment.status"] = 1;
//        $conditionarray["SubcenterDepartment.status"] = 1;
//        $params['conditions'] = $conditionarray;
//        $params['joins'] = array(
//            array(
//                'table' => 'org_departments',
//                'alias' => 'OrgDepartments',
//                'type' => 'LEFT',
//                'conditions' => array(
//                    'OrgDepartments.id = SubcenterDepartment.department_id'
//                )
//            ),
//            array(
//                'table' => 'org_subcenters',
//                'alias' => 'OrgSubcenter',
//                'type' => 'LEFT',
//                'conditions' => array(
//                    'OrgSubcenter.id = SubcenterDepartment.subcenter_id'
//                )
//            ),
//        );
//        $params['order'] = 'OrgDeptName';
//        $subcenterDepartment = $this->SubcenterDepartment->find("all", $params);
//        $subcenterDepartmentArray = $dataSubcenterDeptFilterArray = array();
//        if (isset($subcenterDepartment) && !empty($subcenterDepartment)) {
//            foreach ($subcenterDepartment as $index => $data) {
//                $scID = $data['SubcenterDepartment']['SdeptID'];
//                $subCenterID = $data['SubcenterDepartment']['SubcenterID'];
//                $subcenterDepartmentArray[$scID]['dept_id'] = $data['SubcenterDepartment']['deptID'];
//                $subcenterDepartmentArray[$scID]['dept_name'] = $data['OrgDepartments']['OrgDeptName'];
//                $subcenterDepartmentArray[$scID]['subcenter_name'] = $data['OrgSubcenter']['short_name'];
//                $subcenterDepartmentArray[$scID]['subcenter_id'] = $subCenterID;
//                $dataSubcenterDeptFilterArray[$subCenterID][$scID] = $subcenterDepartmentArray[$scID];
//            }
//        }
////        pr($dataSubcenterDeptFilterArray); exit;
//
//        //Getting all Users and subcenters and Departments
//        $params = $conditionarray = array();
//        $params['fields'] = "User.id, concat(User.fname, ' ', User.lname) as user_name, OrgSubcenter.short_name, OrgDepartment.name as dept_name, OrgJobTitle.title, UserOrganization.subcenter_id as subcenterID, UserOrganization.department_id as deptID";
//        $conditionarray["UserOrganization.organization_id"] = $organization_id;
//        $conditionarray["UserOrganization.status"] = 1;
//        $params['conditions'] = $conditionarray;
//        $params['joins'] = array(
//            array(
//                'table' => 'user_organizations',
//                'alias' => 'UserOrganization',
//                'type' => 'LEFT',
//                'conditions' => array(
//                    'UserOrganization.user_id = User.id'
//                )
//            ),
//            array(
//                'table' => 'org_subcenters',
//                'alias' => 'OrgSubcenter',
//                'type' => 'LEFT',
//                'conditions' => array(
//                    'OrgSubcenter.id = UserOrganization.subcenter_id'
//                )
//            ),
//            array(
//                'table' => 'org_departments',
//                'alias' => 'OrgDepartment',
//                'type' => 'LEFT',
//                'conditions' => array(
//                    'OrgDepartment.id = UserOrganization.department_id'
//                )
//            ),
//            array(
//                'table' => 'org_job_titles',
//                'alias' => 'OrgJobTitle',
//                'type' => 'LEFT',
//                'conditions' => array(
//                    'OrgJobTitle.id = UserOrganization.job_title_id'
//                )
//            ),
//        );
//        $params['order'] = 'user_name';
//        $userSubcenterData = $this->User->find("all", $params);
//        $orgAllUserDataArray = array();
////        pr($userSubcenterData); exit;
//        if (isset($userSubcenterData) && !empty($userSubcenterData)) {
//            foreach ($userSubcenterData as $index => $uUScData) {
//                $userId = $uUScData['User']['id'];
//                $subCenterID = ($uUScData['UserOrganization']['subcenterID'] != '') ? $uUScData['UserOrganization']['subcenterID'] : 0;
//                $deptID = ($uUScData['UserOrganization']['deptID'] != '') ? $uUScData['UserOrganization']['deptID'] : 0;
//                $orgAllUserDataArray[$userId]['name'] = $uUScData[0]['user_name'];
//                $orgAllUserDataArray[$userId]['subcenter_name'] = $uUScData['OrgSubcenter']['short_name'];
//                $orgAllUserDataArray[$userId]['dept_name'] = $uUScData['OrgDepartment']['dept_name'];
//                $orgAllUserDataArray[$userId]['user_title'] = $uUScData['OrgJobTitle']['title'];
//                $orgAllUserDataArray[$userId]['dept_id'] = $deptID;
//                $orgAllUserDataArray[$userId]['subcenter_id'] = $subCenterID;
//            }
//        }
//
////        pr($this->request->data);exit;
        $activeTab = 'data_summary';
        if (!empty($this->request->data["reporttab"]) && !empty($this->request->data["reporttab"])) {
            $activeTab = $this->request->data["reporttab"];
        }
//
        $endorsementbyWeek = array();
        $this->set(compact('activeTab', 'endorsementbyWeek', 'datesarray1'));
//        $this->set(compact('orgDepartments','activeTab', 'endorsementbyWeek', 'subCenterArray', 'deptNdorsementCount', 'subcenterDepartmentArray', 'orgAllUserDataArray', 'authUser', 'companydetail', 'organization_id', 'datesarray1', 'subcenterNdorsementArray', 'usersNdorsementsCounts'));
//        
    }

    function allendorsements($organization_id = "null") {
        $result = $this->Common->checkorgid($organization_id);
        //=============to redirect if orgid is wrong
        if ($result == "redirect") {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }
        $this->loadModel("Endorsement");
        $this->loadModel("User");
        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdaterandc"]) && !empty($this->request->data["enddaterandc"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdaterandc"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddaterandc"]);
        }
        $this->Session->write('orgid', $organization_id);
        $this->Session->write('datearray', array("startdate" => $startdate, "enddate" => $enddate));
        $authUser = $this->Auth->User();
        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
        //=======================================endorsement all feature
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcodeForReports($organization_id);
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
        $condtionsallendorsement["organization_id"] = $organization_id;
        $condtionsallendorsement["type !="] = "guest";
        if ($startdate != "" and $enddate != "") {
            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
        } else {
            //month(created) = month(NOW())", "year(created) = year(NOW())
            array_push($condtionsallendorsement, "month(created) = month(NOW())", "year(created) = year(NOW())");
//            echo "DATE : "+month(NOW())+"-"+year(NOW());

            $d = new DateTime('first day of this month');
            $startdate = $d->format('m-d-Y');
            //$enddate = date('m-d-Y', time());
            $enddate = date('Y-m-d', time());
        }
//        exit;
        $limit = Configure::read("pageLimit");
        if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
            $page = $this->request->data["page"];
            $offset = $page * $limit;
        } else {
            $page = 1;
            $offset = 0;
        }

        //ini_set('memory_limit', '256M');
        ini_set('memory_limit', '1024M');
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));

//        $allendorsementList = $this->Endorsement->find("all", array(
//            'fields' => array('EndorseCoreValues.*', 'Endorsement.*'),
//            'joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
//                array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id'))
//            ), "order" => "Endorsement.created DESC", "group" => 'Endorsement.id', "conditions" => $condtionsallendorsement));
//
//        $totalEndorsements = count($allendorsementList);

        $allendorsement = $this->Endorsement->find("all", array(
            'fields' => array('EndorseCoreValues.*', 'Endorsement.*'),
            'joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
                array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id'))
            ),
            "order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsement/* , "limit" => 200, "page" => $page, 'offset' => $offset */));
        //
//        $allendorsement = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsement));
//        echo $this->Endorsement->getLastQuery();
//        echo $endorsementformonth = $this->Common->endorsementformonth($organization_id);
//        exit;
//        exit;
//        exit;
        $rd = array();
        foreach ($allendorsement as $index => $data) {
            $endorseId = $data['Endorsement']['id'];
            $rd[$endorseId]['Endorsement'] = $data['Endorsement'];
            $rd[$endorseId]['EndorseCoreValues'][] = $data['EndorseCoreValues'];
        }
//        pr($allendorsement); exit;
        $allendorsement = $rd;
        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities);
//        echo $totalEndorsements = count($allvaluesendorsement);exit;
        $jobtitles = $this->Common->getorgjobtitles($organization_id);
//        pr($allvaluesendorsement); exit;
        //=======================================end endorsement all feature
        $datesarray = array("startdate" => $startdate, "enddate" => $enddate);
        $this->set(compact("authUser", "organization_id", "companydetail", 'allvaluesendorsement', 'orgcorevaluesandcode', 'datesarray', 'jobtitles', 'departments', 'entities', 'totalEndorsements'));
    }

    function guestendorsements($organization_id = "null") {
        $result = $this->Common->checkorgid($organization_id);

        //=============to redirect if orgid is wrong
        if ($result == "redirect") {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }
        $this->loadModel("Endorsement");
        $this->loadModel("User");
        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdaterandc"]) && !empty($this->request->data["enddaterandc"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdaterandc"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddaterandc"]);
        }
        $this->Session->write('orgid', $organization_id);
        $this->Session->write('datearray', array("startdate" => $startdate, "enddate" => $enddate));
        $authUser = $this->Auth->User();
        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
        //=======================================endorsement all feature
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcodeForReports($organization_id);
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
//        $condtionsallendorsement = array("organization_id" => $organization_id);
//        $condtionsallendorsement = array("type" => "guest");
        $condtionsallendorsement["organization_id"] = $organization_id;
        $condtionsallendorsement["type"] = "guest";
        if ($startdate != "" and $enddate != "") {
            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
        } else {
            //month(created) = month(NOW())", "year(created) = year(NOW())
            array_push($condtionsallendorsement, "month(created) = month(NOW())", "year(created) = year(NOW())");
//            echo "DATE : "+month(NOW())+"-"+year(NOW());

            $d = new DateTime('first day of this month');
            $startdate = $d->format('m-d-Y');
            //$enddate = date('m-d-Y', time());
            $enddate = date('Y-m-d', time());
        }
//        exit;
        $limit = Configure::read("pageLimit");
        if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
            $page = $this->request->data["page"];
            $offset = $page * $limit;
        } else {
            $page = 1;
            $offset = 0;
        }

        //ini_set('memory_limit', '256M');
        ini_set('memory_limit', '1024M');
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));

//        $allendorsementList = $this->Endorsement->find("all", array(
//            'fields' => array('EndorseCoreValues.*', 'Endorsement.*'),
//            'joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
//                array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id'))
//            ), "order" => "Endorsement.created DESC", "group" => 'Endorsement.id', "conditions" => $condtionsallendorsement));
//
//        $totalEndorsements = count($allendorsementList);

        $allendorsement = $this->Endorsement->find("all", array(
            'fields' => array('EndorseCoreValues.*', 'Endorsement.*'),
            'joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
                array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id'))
            ),
            "order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsement/* , "limit" => 200, "page" => $page, 'offset' => $offset */));
        //
//        $allendorsement = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsement));
//        echo $this->Endorsement->getLastQuery();
//        echo $endorsementformonth = $this->Common->endorsementformonth($organization_id);
//        exit;
//        exit;
//        exit;
        $rd = array();
        foreach ($allendorsement as $index => $data) {
            $endorseId = $data['Endorsement']['id'];
            $rd[$endorseId]['Endorsement'] = $data['Endorsement'];
            $rd[$endorseId]['EndorseCoreValues'][] = $data['EndorseCoreValues'];
        }
//        pr($allendorsement); exit;
        $allendorsement = $rd;
        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities);
//        echo $totalEndorsements = count($allvaluesendorsement);exit;
        $jobtitles = $this->Common->getorgjobtitles($organization_id);
//        pr($allvaluesendorsement); exit;
        //=======================================end endorsement all feature
        $datesarray = array("startdate" => $startdate, "enddate" => $enddate);
        $this->set(compact("authUser", "organization_id", "companydetail", 'allvaluesendorsement', 'orgcorevaluesandcode', 'datesarray', 'jobtitles', 'departments', 'entities', 'totalEndorsements'));
    }

    function daisyendorsements($organization_id = "null") {
        $result = $this->Common->checkorgid($organization_id);

        //=============to redirect if orgid is wrong
        if ($result == "redirect") {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }
        $this->loadModel("Endorsement");
        $this->loadModel("User");
        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdaterandc"]) && !empty($this->request->data["enddaterandc"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdaterandc"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddaterandc"]);
        }
        $this->Session->write('orgid', $organization_id);
        $this->Session->write('datearray', array("startdate" => $startdate, "enddate" => $enddate));
        $authUser = $this->Auth->User();
        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
        //=======================================endorsement all feature
        //$orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcodeForReports($organization_id);
//        pr($orgcorevaluesandcode); exit;
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
//        $condtionsallendorsement = array("organization_id" => $organization_id);
//        $condtionsallendorsement = array("type" => "daisy");
        $condtionsallendorsement["organization_id"] = $organization_id;
        $condtionsallendorsement["type"] = "daisy";
        //$condtionsallendorsement["Endorsement.status"] = 1;  Show all nDorsements
        if ($startdate != "" and $enddate != "") {
            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
        } else {
            //month(created) = month(NOW())", "year(created) = year(NOW())
            array_push($condtionsallendorsement, "month(Endorsement.created) = month(NOW())", "year(Endorsement.created) = year(NOW())");
//            echo "DATE : "+month(NOW())+"-"+year(NOW());

            $d = new DateTime('first day of this month');
            $startdate = $d->format('m-d-Y');
            //$enddate = date('m-d-Y', time());
            $enddate = date('Y-m-d', time());
        }
//        exit;
        $limit = Configure::read("pageLimit");
        if (isset($this->request->data["page"]) && $this->request->data["page"] > 1) {
            $page = $this->request->data["page"];
            $offset = $page * $limit;
        } else {
            $page = 1;
            $offset = 0;
        }

        //ini_set('memory_limit', '256M');
        ini_set('memory_limit', '1024M');
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies', 'Endorse')));

//        $allendorsementList = $this->Endorsement->find("all", array(
//            'fields' => array('EndorseCoreValues.*', 'Endorsement.*'),
//            'joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
//                array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id'))
//            ), "order" => "Endorsement.created DESC", "group" => 'Endorsement.id', "conditions" => $condtionsallendorsement));
//
//        $totalEndorsements = count($allendorsementList);

        $allendorsement = $this->Endorsement->find("all", array(
            'fields' => array('EndorseCoreValues.*', 'Endorsement.*', 'DaisySubcenter.name'),
            'joins' => array(
                /* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
                array('table' => 'daisy_subcenters', 'alias' => 'DaisySubcenter', 'type' => 'LEFT', 'conditions' => array('DaisySubcenter.id = Endorsement.nominee_subcenter_id')),
                array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id'))
            ),
            "order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsement/* , "limit" => 200, "page" => $page, 'offset' => $offset */));
        //
//        $allendorsement = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsement));
//        echo $this->Endorsement->getLastQuery();
//        echo $endorsementformonth = $this->Common->endorsementformonth($organization_id);
//        exit;
//        exit;
//        exit;
//        pr($allendorsement);
//        exit;
        $rd = array();
        foreach ($allendorsement as $index => $data) {
            $endorseId = $data['Endorsement']['id'];
            $rd[$endorseId]['Endorsement'] = $data['Endorsement'];
            $nomineeSubcenterName = "";
            if (!empty($data['DaisySubcenter']) && isset($data['DaisySubcenter']['name'])) {
                $nomineeSubcenterName = $data['DaisySubcenter']['name'];
            }
            $rd[$endorseId]['Endorsement']['nominee_subcenter_name'] = $nomineeSubcenterName;

            $rd[$endorseId]['EndorseCoreValues'][] = $data['EndorseCoreValues'];
        }
//        pr($rd);
//        exit;
        $allendorsement = $rd;
        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities, 'daisy');
//        echo $totalEndorsements = count($allvaluesendorsement);exit;
        $jobtitles = $this->Common->getorgjobtitles($organization_id);
//        pr($allvaluesendorsement);
//        exit;
        //=======================================end endorsement all feature
        $DAISYAwards = Configure::read("DAISY_Awards");
        $datesarray = array("startdate" => $startdate, "enddate" => $enddate);
        $this->set(compact("authUser", "organization_id", "companydetail", 'allvaluesendorsement', 'orgcorevaluesandcode', 'datesarray', 'jobtitles', 'departments', 'entities', 'totalEndorsements', 'orgdata', 'DAISYAwards'));
    }

    function allposts($org_id = "null") {
        $organization_id = $org_id;
        $result = $this->Common->checkorgid($org_id);
        //=============to redirect if orgid is wrong
        if ($result == "redirect") {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }
        $this->loadModel("Post");
        $this->loadModel("PostEventCount");
        $this->loadModel("UserOrganization");
        /* ------------------------------------------------ */
        $user_role = array(2, 3);
        $this->UserOrganization->unbindModel(array('hasMany' => array('Organization')));
        $totalUserOfOrg = $this->UserOrganization->find('all', array('conditions' => array("organization_id" => $organization_id, "user_role" => $user_role, "UserOrganization.status" => array(0, 1, 3)), 'order' => 'fname'));
        $orgUserList = array();
        $selected_user_id = 0;
        if (isset($totalUserOfOrg) && !empty($totalUserOfOrg)) {
            foreach ($totalUserOfOrg as $index => $data) {
                if (isset($data['User']) && !empty($data['User'])) {
                    $user_id = $data['User']['id'];
                    $orgUserList[$user_id] = $data['User']['fname'] . " " . $data['User']['lname'];
                }
            }
        }
        $authUser = $this->Auth->User();
        $reportType = 'Posts';
//        pr($this->request->data); exit;
        if (!empty($this->request->data['daterangerandc'])) {
            $reportType = $this->request->data['report_type'];
            $selected_user_id = $this->request->data['daterangerandc']['user_id'];
        }

        $startdate = "";
        $enddate = "";
        $this->Session->write('orgid', $organization_id);
//        pr($this->request->data); //exit;
        if (!empty($this->request->data["startdaterandc"]) && !empty($this->request->data["enddaterandc"])) {
            $requestdata = $this->request->data;
            echo "startdate " . $startdate = $this->Common->dateConvertServer($requestdata["startdaterandc"]);
            echo $enddate = $this->Common->dateConvertServer($requestdata["enddaterandc"]);
        }
        $this->Session->write('datearray', array("startdate" => $startdate, "enddate" => $enddate));
        $datesarray = array("startdate" => $startdate, "enddate" => $enddate);

//        echo $reportType;exit;
        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
        /* ----------------------------------------------------------------------------------------------------- */
        $conditionarray = array();
        if ($reportType == 'Users') {
            $conditionarray["UserOrganization.organization_id"] = $org_id;
            $conditionarray["Post.organization_id"] = $org_id;
            if ($startdate != "" and $enddate != "") {
                array_push($conditionarray, "date(PostEventCount.created) between '$startdate' and '$enddate'");
            }

            $selectedUserName = "All";
            if ($reportType == 'Users' && $selected_user_id != '') {
                $conditionarray["UserOrganization.user_id"] = $selected_user_id;
                $selectedUserName = $orgUserList[$selected_user_id];
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
                    'table' => 'posts',
                    'alias' => 'Post',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Post.id = PostEventCount.post_id'
                    )
                ),
                array(
                    'table' => 'user_organizations',
                    'alias' => 'UserOrganization',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'UserOrganization.user_id = PostEventCount.user_id',
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
        } else {

            $conditionarray["UserOrganization.organization_id"] = $org_id;
            $conditionarray["Post.organization_id"] = $org_id;
            if ($startdate != "" and $enddate != "") {
                array_push($conditionarray, "date(PostEventCount.created) between '$startdate' and '$enddate'");
            }

            $selectedUserName = "All";
            if ($selected_user_id != '') {
                $conditionarray["UserOrganization.user_id"] = $selected_user_id;
                $selectedUserName = $orgUserList[$selected_user_id];
            } else {
                $selected_user_id = key($orgUserList);
                $conditionarray["UserOrganization.user_id"] = $selected_user_id;
                $selectedUserName = $orgUserList[$selected_user_id];
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
                    'table' => 'posts',
                    'alias' => 'Post',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Post.id = PostEventCount.post_id'
                    )
                ),
                array(
                    'table' => 'user_organizations',
                    'alias' => 'UserOrganization',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'UserOrganization.user_id = PostEventCount.user_id',
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

            $params['fields'] = "PostEventCount.post_id,Post.title,PostEventCount.created,sum(post_click) as total_post_click, sum(post_attachment_click) as total_attachment_click, sum(post_attachment_pin_click) as total_attachment_pin_click,
                                    sum(post_like_counts) as total_post_like,OrgJobTitle.title,concat(User.fname,' ',User.lname) as user_name,
                                    Organization.name as org_name,OrgDepartment.name as department_name,
                                    Entity.name as sub_org_name";
//        $params['limit'] = $limit;
//        $params['page'] = $page;
//        $params['offset'] = $offset;
            $params['order'] = 'PostEventCount.created desc';
            $params['group'] = 'PostEventCount.post_id';
        }

//                pr($params);
//                exit;
//$this->Endorsement->bindModel(array('hasMany' => array('EndorseCoreValues')));
        $allPostData = $this->PostEventCount->find("all", $params);
//        $log = $this->PostEventCount->getDataSource()->getLog(false, false);
//        pr($log);exit;
//        pr($allPostData); exit;


        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $jobtitles = $this->Common->getorgjobtitles($organization_id);

        $selectedType = $reportType;
        $this->set(compact('allPostData', 'selectedType', 'orgUserList', 'selected_user_id', 'selectedUserName'));
        $this->set(compact("authUser", "organization_id", "companydetail", 'allvaluesendorsement', 'orgcorevaluesandcode', 'datesarray', 'jobtitles', 'departments', 'entities'));
        //pr($allPostData); exit;
        /* ----------------------------------------------------------------------------------------------------- */
    }

    function listingreports($user_id = "null") {
        $this->loadModel("Endorsement");
        $authUser = $this->Auth->User();
        $organization_id = $this->Session->read('orgid');
        $datearray = $this->Session->read('datearray');

        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $allothervalues = array(
            "departments" => $departments,
            "entities" => $entities,
            "corevalues" => $orgcorevaluesandcode,
        );
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));

        $conditionsendorser = array("organization_id" => $organization_id, "endorser_id" => $user_id);
        $conditionsendorsed = array("organization_id" => $organization_id, "endorsed_id" => $user_id);
        if ($datearray["startdate"] != "" and $datearray["enddate"] != "") {
            $startdate = $datearray["startdate"];
            $enddate = $datearray["enddate"];
            array_push($conditionsendorser, "date(Endorsement.created) between '$startdate' and '$enddate'");
            array_push($conditionsendorsed, "date(Endorsement.created) between '$startdate' and '$enddate'");
        }
        //=================endorsement he got
        $endorser_data = $this->Endorsement->find("all", array("conditions" => $conditionsendorser));
        $endorsed_data = $this->Endorsement->find("all", array("conditions" => $conditionsendorsed));
        //pr($endorsed_data);
        $endorsernamedetail = "";
        $allvaluesendorser = $this->Common->allvaluesendorser($endorser_data, $departments, $entities);
        $allvaluesendorsed = $this->Common->allvaluesendorsed($endorsed_data);
        $userdata = $this->User->findById($user_id);
        $this->set(compact("authUser", "organization_id", "arrayendorsementdetail", "allvaluesendorser", "allothervalues", "allvaluesendorsed", "companydetail", "user_id", 'userdata'));
    }

    function userreport($organization_id = "null", $user_id = "null") {

        $this->loadModel("Endorsement");
        $authUser = $this->Auth->User();

        $datearray = $this->Session->read('datearray');
        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $allothervalues = array(
            "departments" => $departments,
            "entities" => $entities,
            "corevalues" => $orgcorevaluesandcode,
        );
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));

        $conditionsendorser = array("organization_id" => $organization_id, "endorser_id" => $user_id);
        $conditionsendorsed = array("organization_id" => $organization_id, "endorsed_id" => $user_id);
        if ($datearray["startdate"] != "" and $datearray["enddate"] != "") {
            $startdate = $datearray["startdate"];
            $enddate = $datearray["enddate"];
            array_push($conditionsendorser, "date(Endorsement.created) between '$startdate' and '$enddate'");
            array_push($conditionsendorsed, "date(Endorsement.created) between '$startdate' and '$enddate'");
        }
        //=================endorsement he got
        $endorser_data = $this->Endorsement->find("all", array("conditions" => $conditionsendorser));
        $endorsed_data = $this->Endorsement->find("all", array("conditions" => $conditionsendorsed));
        //pr($endorsed_data);
        $endorsernamedetail = "";
        $allvaluesendorser = $this->Common->allvaluesendorser($endorser_data, $departments, $entities);
        $allvaluesendorsed = $this->Common->allvaluesendorsed($endorsed_data);
        $userdata = $this->User->findById($user_id);

        $userInfo = $this->UserOrganization->find("all", array(
            'fields' => array('User.*', 'OrgJobtitle.title', 'OrgDepartment.name', 'OrgSubcenter.short_name'),
            'joins' => array(
                array('table' => 'org_job_titles', 'alias' => 'OrgJobtitle', 'type' => 'left', 'conditions' => array('OrgJobtitle.id = UserOrganization.job_title_id')),
                array('table' => 'org_departments', 'alias' => 'OrgDepartment', 'type' => 'left', 'conditions' => array('OrgDepartment.id = UserOrganization.department_id')),
                array('table' => 'org_subcenters', 'alias' => 'OrgSubcenter', 'type' => 'left', 'conditions' => array('OrgSubcenter.id = UserOrganization.subcenter_id')),
            ),
            "conditions" => array('UserOrganization.organization_id' => $organization_id, 'UserOrganization.user_id' => $user_id, 'UserOrganization.status' => 1)));
//        pr($userdata); //exit;
        //        pr($userdata);
//        exit;
        $userdata = $userInfo[0];
        $this->set(compact("authUser", "organization_id", "arrayendorsementdetail", "allvaluesendorser", "allothervalues", "allvaluesendorsed", "companydetail", "user_id", 'userdata'));





        /*
          ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
          +                       * NEW GRAPH REPORTS START FROM HERE                        +
          ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

         */

        $startdate = $enddate = '';
        if (!empty($this->request->data["startdaterandc"]) && !empty($this->request->data["enddaterandc"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdaterandc"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddaterandc"]);
        }
//        echo $startdate ; exit;
        //Getting all subcenters
        $this->loadModel('Endorsement');
        $this->loadModel('OrgHashtags');

        $HashtagData = $this->OrgHashtags->find('all', array('conditions' => array('org_id' => $organization_id, 'status' => 1)));
        $hashtagArray = $hashtagIDArray = array();
        if (isset($HashtagData) && !empty($HashtagData)) {
            foreach ($HashtagData as $index => $scDATA) {
                $tempo = $scDATA['OrgHashtags'];
                $hashtagArray[$tempo['id']] = $tempo['name'];
                $hashtagIDArray[] = $tempo['id'];
            }
        }

        //=======================================endorsement all feature
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcodeForReports($organization_id);
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
        $condtionsallendorsement["organization_id"] = $organization_id;
        $condtionsallendorsement["type !="] = array("guest", "daisy");
        $condtionsallendorsement["endorser_id"] = $user_id;

        if ($startdate != "" and $enddate != "") {
            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
        }


        ini_set('memory_limit', '1024M');
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));

        $allendorsement = $this->Endorsement->find("all", array(
            'fields' => array('EndorseCoreValues.*', 'Endorsement.*'),
            'joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
                array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id'))
            ),
            "order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsement));

        $rd = array();
        foreach ($allendorsement as $index => $data) {
            $endorseId = $data['Endorsement']['id'];
            $rd[$endorseId]['Endorsement'] = $data['Endorsement'];
            $rd[$endorseId]['EndorseCoreValues'][] = $data['EndorseCoreValues'];
        }
        $allendorsement = $rd;
        $allvaluesendorsement = count($allendorsement);

        $monthWiseCondition = $condtionsallendorsement;
        if ($startdate != "" and $enddate != "") {
            array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
        } else {
            array_push($condtionsallendorsement, "month(Endorsement.created) = month(NOW())", "year(Endorsement.created) = year(NOW())");
            $d = new DateTime('first day of this month');
//            $startdate = $d->format('d-m-Y');
//            $startdate = $d->format('Y-m-d');
            //$enddate = date('m-d-Y', time());
//            $enddate = date('Y-m-d', time());
        }
//echo $startdate; exit;
//        pr($condtionsallendorsement); exit;

        /*         * * CURRENT MONTH Endorsement DATA ** */
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
        $allendorsementmnthly = $this->Endorsement->find("all", array(
            'fields' => array('EndorseCoreValues.*', 'Endorsement.*'),
            'joins' => array(/* array('table' => 'users', 'type' => 'INNER', 'conditions' => array('users.id = UserOrganization.user_id')), */
                array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id'))
            ),
            "order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsement));
        $rd1 = array();
        foreach ($allendorsementmnthly as $index => $data) {
            $endorseId = $data['Endorsement']['id'];
            $rd1[$endorseId]['Endorsement'] = $data['Endorsement'];
            $rd1[$endorseId]['EndorseCoreValues'][] = $data['EndorseCoreValues'];
        }
        $allendorsementMonthly = $rd1;
        $allvaluesendorsementMonthly = count($allendorsementMonthly);


        $until = new DateTime();
        $interval = new DateInterval('P12M'); //12 months
        $from = $until->sub($interval);
        $last12Mnth = $from->format('Y-m-t');

        /* LAST 12 Month Endorsement DATA */
        $currentDATE = date('Y-m-d h:i:s', time());


        if ($startdate != "" and $enddate != "") {
            array_push($monthWiseCondition, "date(Endorsement.created) between '$startdate' and '$enddate'");
        } else {
            $monthWiseCondition["Endorsement.created >"] = $last12Mnth;
        }


//
//        pr($monthWiseCondition);
//        exit;
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
        $monthwiseEndorsementmnthly = $this->Endorsement->find("all", array(
            'fields' => array('EndorseCoreValues.*', 'Endorsement.*', 'EndorseHashtag.*', 'OrgSubcenter.*'),
            'joins' => array(
                array('table' => 'endorse_core_values', 'alias' => 'EndorseCoreValues', 'type' => 'LEFT', 'conditions' => array('EndorseCoreValues.endorsement_id = Endorsement.id')),
                array('table' => 'endorse_hashtags', 'alias' => 'EndorseHashtag', 'type' => 'LEFT', 'conditions' => array('EndorseHashtag.endorsement_id = Endorsement.id')),
                array('table' => 'org_subcenters', 'alias' => 'OrgSubcenter', 'type' => 'LEFT', 'conditions' => array('OR' => array('OrgSubcenter.id = Endorsement.subcenter_for', 'OrgSubcenter.id = Endorsement.subcenter_by'))),
            ),
            "order" => "Endorsement.created DESC", "conditions" => $monthWiseCondition));

//        pr($monthwiseEndorsementmnthly);
//        exit;
        $endorseSubcenterData = $hashTagEndorsement = array();
        //$subCenterArray;
        $monthwiseallData = array();
        foreach ($monthwiseEndorsementmnthly as $index => $data) {
            $month = date("M-y", strtotime($data['Endorsement']['created']));
            $monthwiseallData[$month][] = $data;
        }

        $dataArray = array();
        $endorsementMonthwiseArray = array();
        $coreValuesMonthWiseArray = array();
        $hashtagsMonthWiseArray = array();
        $corevaluesIDsArray = array();


        foreach ($monthwiseallData as $monthIndex => $mnthData) {
            foreach ($mnthData as $index => $data) {
//                pr($data);
                $endorseId = $data['Endorsement']['id'];

                //DATA CALCULATION FOR ORGANIZATION ENDORSEMENT
                if (!isset($endorsementMonthwiseCounts[$monthIndex][$endorseId])) {
                    $endorsementMonthwiseCounts[$monthIndex][$endorseId] = $endorseId;
                }

                //DATA CALCULATION FOR CORE VALUES
                if (isset($data['EndorseCoreValues']['id'])) {
                    $endrsCoreValueID = $data['EndorseCoreValues']['id'];
                    $coreValueID = $data['EndorseCoreValues']['value_id'];
                    $coreValuesMonthWiseArray[$monthIndex][$coreValueID][$endrsCoreValueID] = $endrsCoreValueID;
                    $corevaluesIDsArray[$coreValueID] = $coreValueID;
                }

                //DATA CALCULATION FOR HASHTAGS
                if (isset($data['EndorseHashtag']['id'])) {
                    $endrsHashtagID = $data['EndorseHashtag']['id'];
                    $hashtagID = $data['EndorseHashtag']['hashtag_id'];
                    $hashtagsMonthWiseArray[$monthIndex][$hashtagID][$endrsHashtagID] = $endrsHashtagID;
                }
            }
        }

        for ($i = 0; $i <= 11; $i++) {
            $months[] = date("M-y", strtotime(date('Y-m-01') . " -$i months"));
        }
        $months = array_reverse($months);
        $hashtagsCountArray = $corevaluesCountArray = $orgEndorsementsCountArray = $subcenterCountArray = array();

        foreach ($months as $index => $monthID) {

            //Monthwise Hashtag Count Data
            if (isset($hashtagsMonthWiseArray[$monthID])) {
                foreach ($hashtagIDArray as $indx => $hashtagID) {
                    if (isset($hashtagsMonthWiseArray[$monthID][$hashtagID])) {
                        $hData = $hashtagsMonthWiseArray[$monthID][$hashtagID];
                        $hashtagsCountArray[$monthID][$hashtagID] = count($hData);
                    } else {
                        $hashtagsCountArray[$monthID][$hashtagID] = 0;
                    }
                }
            } else {
                foreach ($hashtagIDArray as $indx => $hashtagID) {
                    $hashtagsCountArray[$monthID][$hashtagID] = 0;
                }
            }

            //Monthwise Core values Count Data
            if (isset($coreValuesMonthWiseArray[$monthID])) {
                foreach ($corevaluesIDsArray as $indx => $cvID) {
                    if (isset($coreValuesMonthWiseArray[$monthID][$cvID])) {
                        $cvData = $coreValuesMonthWiseArray[$monthID][$cvID];
                        $corevaluesCountArray[$monthID][$cvID] = count($cvData);
                    } else {
                        $corevaluesCountArray[$monthID][$cvID] = 0;
                    }
                }
            } else {
                foreach ($corevaluesIDsArray as $indx => $cvID) {
                    $corevaluesCountArray[$monthID][$cvID] = 0;
                }
            }

            //Monthwise Org Endorsement Count Data
            if (isset($endorsementMonthwiseCounts[$monthID])) {
                $orgEndorsementsCountArray[$monthID] = count($endorsementMonthwiseCounts[$monthID]);
            } else {
                foreach ($corevaluesIDsArray as $indx => $cvID) {
                    $orgEndorsementsCountArray[$monthID] = 0;
                }
            }
        }

        $months = json_encode($months);

        $totalEndorsements = array();
        foreach ($orgEndorsementsCountArray as $monthID => $totalCount) {
            $totalEndorsements[] = array($totalCount);
        }
        $totalEndorsements = json_encode($totalEndorsements);

        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
//        pr($orgcorevaluesandcode[871]['name']);
        $finalCoreValueData = array();
        $corevaluesEndorsements = array();
//        pr($orgcorevaluesandcode);
//        pr($corevaluesCountArray); exit;

        foreach ($corevaluesCountArray as $monthID => $cvData) {
            foreach ($cvData as $coreVID => $cData) {
                $corevaluesEndorsements[$coreVID][] = $cData;
            }
        }
        foreach ($corevaluesEndorsements as $coreID => $cDataArray) {
            $corevalueName = '';
            if (isset($orgcorevaluesandcode[$coreID])) {
                $corevalueName = $orgcorevaluesandcode[$coreID]['name'];
            }
            $finalCoreValueData[] = array('data' => $cDataArray, 'name' => $corevalueName);
        }
        $finalCoreValueData = json_encode($finalCoreValueData);


        $finalHashtagsData = array();
        $hashtagsEndorsements = array();
        foreach ($hashtagsCountArray as $monthID => $hsData) {
            foreach ($hsData as $htID => $htData) {
                $hashtagsEndorsements[$htID][] = $htData;
            }
        }
        foreach ($hashtagsEndorsements as $hashtagID => $hashDataArray) {
            $hashtagName = '';
            if (isset($hashtagArray[$hashtagID])) {
                $hashtagName = $hashtagArray[$hashtagID];
            }
            $finalHashtagsData[] = array('data' => $hashDataArray, 'name' => $hashtagName);
        }
        $finalHashtagsData = json_encode($finalHashtagsData);


        $datesarray = array("startdate" => $startdate, "enddate" => $enddate);
        $this->set(compact('startdate', 'enddate', 'months', 'finalHashtagsData', 'finalCoreValueData', 'finalSubcenterData', 'totalEndorsements', 'hashtagArray', 'startdate', 'enddate', 'datesarray', 'subCenterArray', 'authUser', 'companydetail', 'organization_id', 'orgdata', 'allvaluesendorsement', 'allvaluesendorsementMonthly'));


        /* NEW GRAPH REPORTS ENDED HERE */
    }

    public function announcements() {
        $orgdata = $this->Organization->find("all");
        $logged_in_user_role = $this->Auth->user('role');
        $loggedUserId = $logged_in_user_id = $this->Auth->user('id');
        if ($logged_in_user_id <= 1) {
            $this->Auth->logout();
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        } else {
            if ($logged_in_user_role > 1) {
                $this->LoadModel("UserOrganization");
                $conditions = array("user_id" => $logged_in_user_id, "user_role" => array(2, 6), 'UserOrganization.status' => 1, "Organization.announcement_status" => 1);
                $userorgdata = $this->UserOrganization->find("all", array("conditions" => $conditions));
//                pr($userorgdata); exit;
//                echo $this->UserOrganization->getLastQuery();
//                exit;

                $orgnizationIds = array();
                foreach ($userorgdata as $index => $orgData) {
                    $orgnizationIds[] = $orgData['UserOrganization']['organization_id'];
                }
//                pr($orgnizationIds);
//                exit;
                //====================get all USers to mail
                $params = array();
//                $this->UserOrganization->unbindModel(array('hasMany' => array('User', 'Organization')));
                $params["conditions"] = array("UserOrganization.status" => 1, "User.status" => 1, "fname !=" => '', 'UserOrganization.organization_id' => $orgnizationIds);
                $params["fields"] = array("User.id", "User.fname", "User.lname");
                $params["order"] = "fname ASC";
                $userdata = $this->UserOrganization->find("all", $params);
//                pr($userdata); exit;
                //====================End get all Users to mail
                //====================get all Department to mail

                $params = array();
                $params["conditions"] = array("OrgDepartment.status" => 1, "OrgDepartment.name !=" => '', "OrgDepartment.organization_id " => $orgnizationIds);
                $params["fields"] = array("OrgDepartment.id", "OrgDepartment.name", "organization_id", "Organization.name");
                $params["order"] = "OrgDepartment.name ASC";
                $params["joins"] = array(array(
                        'table' => 'organizations',
                        'alias' => 'Organization',
                        'type' => 'left',
                        'conditions' => array('OrgDepartment.organization_id = Organization.id')
                ));
                $deptdata = $this->OrgDepartment->find("all", $params);
//                pr($deptdata);
//                exit;
                //====================End get all Department to mail
                //
                //====================get all Department to mail
                $params = array();
                $params["conditions"] = array("Entity.status" => array(1), "Entity.name !=" => '', "Entity.organization_id " => $orgnizationIds);
                $params["fields"] = array("Entity.id", "Entity.name", "organization_id", "Organization.name");
                $params["order"] = "Entity.name ASC";
                $params["joins"] = array(array(
                        'table' => 'organizations',
                        'alias' => 'Organization',
                        'type' => 'left',
                        'conditions' => array('Entity.organization_id = Organization.id')
                ));

                $entitydata = $this->Entity->find("all", $params);
//            pr($entitydata); exit;
                //====================End get all Department to mail
            }
        }
        if ($this->request->is('post')) {

//            pr($this->request->data);
////            exit;

            $organizationslist = array();
            $content = $this->request->data['User']["mailingbox"];
            $RAWContent = $this->request->data['MailingOrg']["messagebox"];
            if (isset($this->request->data['User']["Organizations"])) {
                $organizationslist = $this->request->data['User']["Organizations"];
            }
            $attachment = "";
            $newfilename = "";
            if (isset($this->request->data["MailingOrg"]["attachment"]) && $this->request->data["MailingOrg"]["attachment"]["tmp_name"] != "" && $this->request->data["MailingOrg"]["attachment"]["error"] == 0) {
                $fullpath = $this->request->data["MailingOrg"]["attachment"];
                $attachemntname = $fullpath["name"];
                $attachment = $fullpath["tmp_name"];
                $temp = explode(".", $attachemntname);
                $newfilename = time() . '_attached.' . end($temp);
                move_uploaded_file($attachment, WWW_ROOT . "attachmentimages/" . $newfilename);
            }






            /*             * * NEW CODE FOR SCHEDULED ANNOUNCEMENT ADDED BY BABULAL PRASAD @29-jan-2018*** */
            $organizationslist = $usersList = $departmentList = $suborgList = array();
            $content = $this->request->data['User']["mailingbox"];
            $RAWContent;
            if (isset($this->request->data['User']["Organizations"])) {
                $organizationslist = $this->request->data['User']["Organizations"];
            }
            if (isset($this->request->data['User']["Users"])) {
                $usersList = $this->request->data['User']["Users"];
            }
            if (isset($this->request->data['User']["Deprtment"])) {
                $departmentList = $this->request->data['User']["Deprtment"];
            }
            if (isset($this->request->data['User']["SubOrg"])) {
                $suborgList = $this->request->data['User']["SubOrg"];
            }
//                    pr($this->request->data);
//                    exit;
            $scheduled = 0;
            $UTCTimeToPost = '0000-00-00 00:00:00';
            if (isset($this->request->data['report_type']) && $this->request->data['report_type'] == 'postlater') {
                $scheduled = 1;
                if (isset($this->request->data['post_time']) && $this->request->data['post_time'] != '') {
                    $datetimeToSave = $this->Common->daterangeAndTimeToSQL($this->request->data['post_date'], $this->request->data['post_time']);
                    $timeToSave = $this->request->data['post_time'] . ":00";
                    $dateToSave = $this->Common->daterangeToSQL($this->request->data['post_date']);
                    $usertimzone = 'UTC';
                    if (isset($this->request->data['usertimzone']) && $this->request->data['usertimzone'] != '') {
                        $usertimzone = $this->request->data['usertimzone'];
                    }
                    $UTCTimeToPost = $this->Common->ConvertOneTimezoneToAnotherTimezone($datetimeToSave, $usertimzone, 'UTC');
                }
            }

            /* SAVE DATA TO ANNOUNCEMENT TABLE added by Babulal Prasad @10-jan-2018 */
            $announcement['message'] = $RAWContent;
            $announcement['organizations'] = json_encode($organizationslist);
            $announcement['users'] = json_encode($usersList);
            $announcement['departments'] = json_encode($departmentList);
            $announcement['suborgs'] = json_encode($suborgList);
            $announcement['posted_by_id'] = $loggedUserId;
            if (isset($scheduled) && $scheduled == 1) {
                $announcement['scheduled'] = $scheduled;
                $announcement['date'] = $dateToSave;
                $announcement['time'] = $timeToSave;
                $announcement['scheduled_datetime'] = $datetimeToSave;
                $announcement['utc_scheduled_datetime'] = $UTCTimeToPost;
            }

//            pr($announcement); exit;
            $this->loadModel('Announcement');
            $resultAnnounce = $this->Announcement->save($announcement);
//            pr($announcement);
            $announcementID = $this->Announcement->id;
//                    pr($resultAnnounce);
//                    exit;
            /*             * ** */
            /*             * * ANNOUNCEMNET SCHEDULE CODE ENDED ** */



            $senderID = $loggedUserId;

            //==common announcement form method for admin and superadmin
            $this->Common->announcementspostdata($organizationslist, $RAWContent, $newfilename, $usersList, $departmentList, $suborgList, $scheduled, $UTCTimeToPost, $announcementID, $senderID);
            //$this->Common->announcementspostdata($organizationslist, $content, $newfilename);
//            exec( "php ".WWW_ROOT."cron_scheduler.php /cron/globalemailcron/ > /dev/null &");
            exec("wget -bqO- " . Router::url('/', true) . "/cron/globalemailcron &> /dev/null");

            $this->redirect(array("controller" => "organizations", "action" => "announcements"));
        }
        $this->set(compact('userorgdata', 'userdata', 'deptdata', 'entitydata'));
    }

    /* Added by Babulal Prasad @10-jan-2018 to edit or delete pending scheduled announcement * */

    public function pendingannouncement() {
        $loggeduser = $this->Auth->User();
        $logged_in_user_role = $this->Auth->user('role');
        $loggedUserId = $logged_in_user_id = $this->Auth->user('id');
        if ($logged_in_user_id <= 1) {
            $this->Auth->logout();
            $this->redirect(array('controller' => 'users', 'action' => 'login'));
        } else {
            if ($logged_in_user_role > 1) {


                $this->loadModel("Announcement");
                $prev_page = Router::url($this->referer(), true);
                $announcemetnparams["fields"] = array("Announcement.*", "User.image", "User.fname", "User.lname");
                $announcemetnparams["conditions"] = array("Announcement.status" => 'active', "Announcement.scheduled" => '1', 'posted_by_id' => $logged_in_user_id);
                $announcemetnparams["order"] = array("Announcement.created" => 'desc');
                $announcemetnparams["joins"] = array(array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'left',
                        'conditions' => array('User.id = Announcement.posted_by_id')
                ));
                $detailedsettings_array = $this->Announcement->find("all", $announcemetnparams);
                $announcementData = $detailedsettings_array;
                $this->set(compact('announcementData'));
                $this->set('authUser', $this->Auth->user());
            }
        }
    }

    public function announcementedit($id = '') {
        $loggeduser = $this->Auth->User();
        $logged_in_user_id = $loggedUserId = $loggeduser['id'];
        if ($id == '') {
            $this->Session->setFlash(__('Invalid announcement selected'), 'default', array('class' => 'alert alert-warning'));
            $this->redirect(array('controller' => 'users', 'action' => 'pendingannouncement'));
        }
        $loggeduser = $this->Auth->User();
        if ($this->Session->check('Auth.User.role') != "1" || $this->Session->check('Auth.User.role') != "2") {
            $this->Auth->logout();
            $this->redirect(array('action' => 'login'));
        } else {
            $role = $this->Auth->User('role');
            if ($role > 1) {

                $this->loadModel("Announcement");
                $prev_page = Router::url($this->referer(), true);
                $announcemetnparams["fields"] = array("Announcement.*", "User.image", "concat(User.fname,' ',User.lname) as posted_user_name");
                $announcemetnparams["conditions"] = array("Announcement.status" => 'active', "Announcement.id" => $id);
                $announcemetnparams["order"] = array("Announcement.created" => 'desc');
                $announcemetnparams["joins"] = array(array(
                        'table' => 'users',
                        'alias' => 'User',
                        'type' => 'left',
                        'conditions' => array('User.id = Announcement.posted_by_id')
                ));
                //pr($conditions); exit;
                $detailedsettings_array = $this->Announcement->find("all", $announcemetnparams);

//            echo $this->Announcement->getLastQuery();
//            pr($detailedsettings_array); exit;
                $this->LoadModel("UserOrganization");
                $conditions = array("user_id" => $logged_in_user_id, "user_role" => array(2, 6), 'UserOrganization.status' => 1, "Organization.announcement_status" => 1);
                $userorgdata = $this->UserOrganization->find("all", array("conditions" => $conditions));

//                echo $this->UserOrganization->getLastQuery();
//                exit;

                $orgnizationIds = array();
                foreach ($userorgdata as $index => $orgData) {
                    $orgnizationIds[] = $orgData['UserOrganization']['organization_id'];
                }
//                pr($orgnizationIds);
//                exit;
                //====================get all USers to mail
                $params = array();
//                $this->UserOrganization->unbindModel(array('hasMany' => array('User', 'Organization')));
                $params["conditions"] = array("UserOrganization.status" => 1, "User.status" => 1, "fname !=" => '', 'UserOrganization.organization_id' => $orgnizationIds);
                $params["fields"] = array("User.id", "User.fname", "User.lname");
                $params["order"] = "fname ASC";
                $userdata = $this->UserOrganization->find("all", $params);
//                pr($userdata); exit;
                //====================End get all Users to mail
                //====================get all Department to mail

                $params = array();
                $params["conditions"] = array("OrgDepartment.status" => 1, "OrgDepartment.name !=" => '', "OrgDepartment.organization_id " => $orgnizationIds);
                $params["fields"] = array("OrgDepartment.id", "OrgDepartment.name", "organization_id", "Organization.name");
                $params["order"] = "OrgDepartment.name ASC";
                $params["joins"] = array(array(
                        'table' => 'organizations',
                        'alias' => 'Organization',
                        'type' => 'left',
                        'conditions' => array('OrgDepartment.organization_id = Organization.id')
                ));
                $deptdata = $this->OrgDepartment->find("all", $params);
//                pr($deptdata);
//                exit;
                //====================End get all Department to mail
                //
                //====================get all Department to mail
                $params = array();
                $params["conditions"] = array("Entity.status" => array(1), "Entity.name !=" => '', "Entity.organization_id " => $orgnizationIds);
                $params["fields"] = array("Entity.id", "Entity.name", "organization_id", "Organization.name");
                $params["order"] = "Entity.name ASC";
                $params["joins"] = array(array(
                        'table' => 'organizations',
                        'alias' => 'Organization',
                        'type' => 'left',
                        'conditions' => array('Entity.organization_id = Organization.id')
                ));

                $entitydata = $this->Entity->find("all", $params);
//            pr($entitydata); exit;
                //====================End get all Department to mail


                if ($this->request->is('post')) {

//                pr($this->request->data); exit;

                    $attachment = "";
                    $newfilename = "";
                    if (isset($this->request->data["MailingOrg"]["attachment"]) && $this->request->data["MailingOrg"]["attachment"]["tmp_name"] != "" && $this->request->data["MailingOrg"]["attachment"]["error"] == 0) {
                        $fullpath = $this->request->data["MailingOrg"]["attachment"];
                        $attachemntname = $fullpath["name"];
                        $attachment = $fullpath["tmp_name"];
                        $temp = explode(".", $attachemntname);
                        $newfilename = time() . '_attached.' . end($temp);
                        move_uploaded_file($attachment, WWW_ROOT . "attachmentimages/" . $newfilename);
                    }

                    $organizationslist = $usersList = $departmentList = $suborgList = array();
                    $content = $this->request->data['User']["mailingbox"];
                    if (isset($this->request->data['User']["Organizations"])) {
                        $organizationslist = $this->request->data['User']["Organizations"];
                    }
                    if (isset($this->request->data['User']["Users"])) {
                        $usersList = $this->request->data['User']["Users"];
                    }
                    if (isset($this->request->data['User']["Deprtment"])) {
                        $departmentList = $this->request->data['User']["Deprtment"];
                    }
                    if (isset($this->request->data['User']["SubOrg"])) {
                        $suborgList = $this->request->data['User']["SubOrg"];
                    }
//                    pr($this->request->data);
//                    exit;
                    $reportType = $scheduled = 0;
                    $UTCTimeToPost = '0000-00-00 00:00:00';
                    if (isset($this->request->data['report_type']) && $this->request->data['report_type'] == 'postlater') {
                        $reportType = $scheduled = 1;
                        if (isset($this->request->data['post_time']) && $this->request->data['post_time'] != '') {
                            $datetimeToSave = $this->Common->daterangeAndTimeToSQL($this->request->data['post_date'], $this->request->data['post_time']);
                            $timeToSave = $this->request->data['post_time'] . ":00";
                            $dateToSave = $this->Common->daterangeToSQL($this->request->data['post_date']);
                            $usertimzone = 'UTC';
                            if (isset($this->request->data['usertimzone']) && $this->request->data['usertimzone'] != '') {
                                $usertimzone = $this->request->data['usertimzone'];
                            }
                            $UTCTimeToPost = $this->Common->ConvertOneTimezoneToAnotherTimezone($datetimeToSave, $usertimzone, 'UTC');
                        }
                    }
//                pr($this->request->data);
                    /* SAVE DATA TO ANNOUNCEMENT TABLE added by Babulal Prasad @10-jan-2018 */
                    $announcement['message'] = $content;
                    $announcement['organizations'] = json_encode($organizationslist);
                    $announcement['users'] = json_encode($usersList);
                    $announcement['departments'] = json_encode($departmentList);
                    $announcement['suborgs'] = json_encode($suborgList);
                    $announcement['posted_by_id'] = $loggedUserId;

//                    pr($this->request->data['announcement_id']); exit;



                    $announcementId = $this->request->data['announcement_id'];

//                    pr($announcement); exit;
                    if (isset($scheduled) && $scheduled == 1) {
                        $announcement['scheduled'] = $scheduled;
                        $announcement['date'] = $dateToSave;
                        $announcement['time'] = $timeToSave;
                        $announcement['scheduled_datetime'] = $datetimeToSave;
                        $announcement['utc_scheduled_datetime'] = $UTCTimeToPost;
                    }
//                    pr($announcement); exit;
                    $this->Announcement->id = $announcementId;
                    $this->Announcement->set($announcement);

                    $resultAnnounce = $this->Announcement->save($announcement);
                    $announcementID = $this->Announcement->id;

//                    pr($resultAnnounce);
//                    exit;
                    /*                     * ** */


                    $senderID = $loggedUserId;
                    //=========common announcement feature for admin and superaadmin
                    $this->Common->updateannouncementspostdata($organizationslist, $content, $newfilename, $usersList, $departmentList, $suborgList, $reportType, $UTCTimeToPost, $announcementID, $senderID);
                    //exit;
                    exec("wget -bqO- " . Router::url('/', true) . "/cron/globalemailcron &> /dev/null");
                    //exec("wget -q " . Router::url('/', true) . "cron/globalemailcron 2> ".File, $outputOnly, $return_value);
                    //$output = shell_exec("wget -bq " . Router::url('/', true) . "cron/globalemailcron");
                    //$this->redirect(array("controller" => "users", "action" => "setting"));
                    $this->redirect(array("controller" => "organizations", "action" => "pendingannouncement"));
                }
                //pr($detailedsettings_array); exit;
                $announcementData = $detailedsettings_array;
                //$this->Invite->updateAll(array("invite_count" => "invite_count+1"), array("email" => $invitedMails));
                $this->set(compact('prev_page', 'allvalues', 'orgdata', 'faqdata', 'formname', 'userdata', 'deptdata', 'entitydata', 'announcementData', 'userorgdata'));
                $this->set('authUser', $this->Auth->user());
            }
        }
    }

    public function announcementdelete() {
        $this->autoRender = false;
        $this->layout = false;
        if ($this->request->is('post')) {
            $announcement_id = $this->request->data["announcementId"];
            $loggedinUser = $this->Auth->user();

            $user_id = $loggedinUser["id"];
            $this->loadModel('Announcement');
            $this->Announcement->id = $announcement_id;

            $deleteAnnouncements = $this->Announcement->delete(array('id' => $announcement_id));

            $this->loadModel('Globalemail');
            $deleteGlobalemail = $this->Globalemail->query("Delete from globalemails  where announcement_id = " . $announcement_id);

//            pr($deleteGlobalemail); exit;
//$deletePost = $this->Post->delete($announcement_id);
//            $deletePostAttachment = $this->PostAttachment->deleteAll(['PostAttachment.post_id' => $announcement_id], false);
//            $deletePostLike = $this->PostLike->deleteAll(['PostLike.post_id' => $p_id], false);

            if ($deleteAnnouncements == 1) {
                $msg = "Announcement deleted successfully";
                $status = true;
            } else {
                $msg = "Announcement unable to delete";
                $status = false;
            }

            echo json_encode(array('success' => $status, "msg" => $msg));
        } else {
            echo json_encode(array('success' => false, "msg" => 'Get call not allowed'));
        }
    }

    public function export() {
        ob_start();
        $result = array();
        $filename = "usersreports_template.csv";
        $fp = fopen('php://output', 'w');
        //$result = $this->User->getColumnTypes();
        $this->loadModel("Endorsement");
        $datearray = array("startdate" => "", "enddate" => "");
        if ($this->Session->read('datearray')) {
            $datearray = $this->Session->read('datearray');
        }
        $organization_id = isset($this->request->query['orgid']) ? $this->request->query['orgid'] : null;
        $user_id = isset($this->request->query['userid']) ? $this->request->query['userid'] : null;
        $information = isset($this->request->query['information']) ? $this->request->query['information'] : null;
        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $allothervalues = array(
            "departments" => $departments,
            "entities" => $entities,
            "corevalues" => $orgcorevaluesandcode,
        );
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
        if ($information == "endorsed" || $information == "endorser") {
            $result = array("Endorsed", "Endorsement Date", "Total Points");
            if (!empty($allothervalues["corevalues"])) {
                foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                    array_push($result, $corevaluesall["name"]);
                }
                array_push($result, "Comments");
            }
            fputcsv($fp, $result);
        }

        if ($information == "endorser") {
            $conditionsendorser = array("organization_id" => $organization_id, "endorser_id" => $user_id);
            if ($datearray["startdate"] != "" and $datearray["enddate"] != "") {
                $startdate = $datearray["startdate"];
                $enddate = $datearray["enddate"];
                array_push($conditionsendorser, "date(Endorsement.created) between '$startdate' and '$enddate'");
            }
            $endorser_data = $this->Endorsement->find("all", array("conditions" => $conditionsendorser));
            $allvaluesendorser = $this->Common->allvaluesendorser($endorser_data, $departments);
            if (!empty($allvaluesendorser)) {
                foreach ($allvaluesendorser as $endorservalues) {
                    $date = new DateTime($endorservalues["date"]);
                    $endorservalues["date"] = $date->format('Y-m-d');
                    $result = array($endorservalues["name"], $endorservalues["date"], $endorservalues["totalpoints"]);
                    foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                        if (in_array($key, $endorservalues["corevaluesid"])) {
                            array_push($result, "YES");
                        } else {
                            array_push($result, "NO");
                        }
                    }
                    array_push($result, $endorservalues["endorsement_message"]);
                    fputcsv($fp, $result);
                }
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
                    $endorsedvalues["date"] = $date->format('Y-m-d');
                    $result = array($endorsedvalues["name"], $endorsedvalues["date"], $endorsedvalues["totalpoints"]);
                    foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                        if (in_array($key, $endorsedvalues["corevaluesid"])) {
                            array_push($result, "YES");
                        } else {
                            array_push($result, "NO");
                        }
                    }
                    array_push($result, $endorsedvalues["endorsed_message"]);
                    fputcsv($fp, $result);
                }
            }
        } else if ($information == "leaderboard") {
            $this->loadModel("OrgDepartment");
            $this->loadModel("Endorsement");
            $searchedvalue = isset($this->request->query['searchvalue']) ? $this->request->query['searchvalue'] : "";
            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues')));
            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
            //=========means number of guys he endorse
            $conditionscountendorsement = array('organization_id' => $organization_id);
            if ($datearray["startdate"] != "" and $datearray["enddate"] != "") {
                $startdate = $datearray["startdate"];
                $enddate = $datearray["enddate"];
                array_push($conditionscountendorsement, "date(created) between '$startdate' and '$enddate'");
            }
            //===============binding model conditions
            $this->Common->commonleaderboardbindings($conditionscountendorsement);
            $this->UserOrganization->recursive = 2;
            $endorsementdata = $this->UserOrganization->find("all", array("conditions" => array("UserOrganization.organization_id" => $organization_id, "UserOrganization.status" => array(0, 1, 2, 3), "UserOrganization.user_role" => array(2, 3, 4))));
            $arrayendorsementdetail = $this->Common->arrayforendorsementdetail($endorsementdata);
            //pr($endorsementdata);
            $result = array("Name", "Endorser", "Endorsed", "Total", "Department", "Entity");
            fputcsv($fp, $result);
            if (!empty($arrayendorsementdetail)) {
                foreach ($arrayendorsementdetail as $endorsementdetail) {
                    $result = array($endorsementdetail["name"], $endorsementdetail["endorser"], $endorsementdetail["endorsed"], $endorsementdetail["endorsed"] + $endorsementdetail["endorser"], $endorsementdetail["department"], $endorsementdetail["entity"]);
                    fputcsv($fp, $result);
                }
            }
        } else if ($information = "allendorsement") {
            $startdate = "";
            $enddate = "";
            if (!empty($this->request->data["startdaterandc"]) && !empty($this->request->data["enddaterandc"])) {
                $requestdata = $this->request->data;
                $startdate = $requestdata["startdaterandc"];
                $enddate = $requestdata["enddaterandc"];
            }
            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
            $condtionsallendorsement = array("organization_id" => $organization_id);
            if ($startdate != "" and $enddate != "") {
                array_push($condtionsallendorsement, "date(Endorsement.created) between '$startdate' and '$enddate'");
            }

            $allendorsement = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsement));
            //pr($endorsed_data);

            $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities);
            $result = array("Endorser", "Endorsed", "Endorsement Date", "Total Points");
            if (!empty($allothervalues["corevalues"])) {
                foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                    array_push($result, $corevaluesall["name"]);
                }
                array_push($result, "Comments");
            }
            fputcsv($fp, $result);
            if (!empty($allvaluesendorsement)) {
                $ctr = 0;
                foreach ($allvaluesendorsement as $endorsedvalues) {
                    $ctr++;
                    if ($ctr % 100 == 0) {
                        //flush();
                        //pr($result);
                        // break;
                    }

                    $date = new DateTime($endorsedvalues["date"]);
                    $endorsedvalues["date"] = $date->format('Y-m-d');
                    $result = array(
                        $endorsedvalues["endorsername"],
                        $endorsedvalues["endorsedname"],
                        $endorsedvalues["date"],
                        $endorsedvalues["totalpoints"]
                    );
                    foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                        if (in_array($key, $endorsedvalues["corevaluesid"])) {
                            array_push($result, "YES");
                        } else {
                            array_push($result, "NO");
                        }
                    }
                    array_push($result, $endorsedvalues["endorsement_message"]);
                    #pr($result);die;
                    fputcsv($fp, $result);
                }
            }
        }

        //=============start creating data for csv file
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        exit;
    }

    function downloadimage($imagepath) {
        $imagepath = base64_decode($imagepath);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . urlencode(basename($imagepath)));
        header('Content-Transfer-Encoding: binary');
        readfile($imagepath);
        exit;
    }

    function deptHistory($id) {
        $this->loadModel("OrgDepartment");
        $this->loadModel("Endorsement");

        $organizationId = $this->Session->read('orgid');
        $orgdata = $this->Organization->findById($organizationId);
        $companyDetail = $this->Common->getcompanyinformation($orgdata["Organization"]);

        $department = $this->OrgDepartment->find("first", array("conditions" => array("id" => $id)));

        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));

        $datearray = $this->Session->read('datearray');
        if ($datearray["startdate"] != "" and $datearray["enddate"] != "") {
            $startdate = $datearray["startdate"];
            $enddate = $datearray["enddate"];
        } else {
            $startdate = "";
            $enddate = "";
        }

        $params['fields'] = "Endorsement.*";
        $condition["Endorsement.endorsement_for"] = "department";
        $condition["Endorsement.endorsed_id"] = $id;
        if ($startdate != "" and $enddate != "") {
            array_push($condition, "date(Endorsement.created) between '$startdate' and '$enddate'");
        }
        $params['conditions'] = $condition;

        $endorsed_data = $this->Endorsement->find("all", $params);

        $allvaluesendorsed = $this->Common->allvaluesendorsed($endorsed_data);

        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organizationId);
        $allothervalues = array(
            "corevalues" => $orgcorevaluesandcode,
        );


        $this->set(compact('department', 'endorsed_data', 'allvaluesendorsed', 'organizationId', 'companyDetail', 'allothervalues'));
    }

    function subOrgHistory($id) {
        $this->loadModel("Endorsement");

        $organizationId = $this->Session->read('orgid');
        $orgdata = $this->Organization->findById($organizationId);
        $companyDetail = $this->Common->getcompanyinformation($orgdata["Organization"]);

        $entity = $this->Entity->find("first", array("conditions" => array("id" => $id)));
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));

        $datearray = $this->Session->read('datearray');
        if ($datearray["startdate"] != "" and $datearray["enddate"] != "") {
            $startdate = $datearray["startdate"];
            $enddate = $datearray["enddate"];
        } else {
            $startdate = "";
            $enddate = "";
        }

        $params['fields'] = "Endorsement.*";
        $condition["Endorsement.endorsement_for"] = "entity";
        $condition["Endorsement.endorsed_id"] = $id;
        if ($startdate != "" and $enddate != "") {
            array_push($condition, "date(Endorsement.created) between '$startdate' and '$enddate'");
        }
        $params['conditions'] = $condition;

        $endorsed_data = $this->Endorsement->find("all", $params);

        $allvaluesendorsed = $this->Common->allvaluesendorsed($endorsed_data);

        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organizationId);
        $allothervalues = array(
            "corevalues" => $orgcorevaluesandcode,
        );


        $this->set(compact('entity', 'endorsed_data', 'allvaluesendorsed', 'organizationId', 'companyDetail', 'allothervalues'));
    }

    function titleHistory($id) {
        $this->loadModel("OrgJobTitle");
        $this->loadModel("Endorsement");

        $organizationId = $this->Session->read('orgid');
        $orgdata = $this->Organization->findById($organizationId);
        $companyDetail = $this->Common->getcompanyinformation($orgdata["Organization"]);

        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organizationId);
        $allothervalues = array(
            "corevalues" => $orgcorevaluesandcode,
        );

        $jobTitle = $this->OrgJobTitle->find("first", array("conditions" => array("id" => $id)));

        $datearray = $this->Session->read('datearray');
        if ($datearray["startdate"] != "" and $datearray["enddate"] != "") {
            $startdate = $datearray["startdate"];
            $enddate = $datearray["enddate"];
        } else {
            $startdate = "";
            $enddate = "";
        }

        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));

        //Bind for endorsed_id
//        $this->UserOrganization->bindModel(array(
//            "belongsTo" => array(
//                "Endorsement" => array(
//                    "className" => "Endorsement",
//                    "foreignKey" => false,
//                    "conditions" => array("UserOrganization.user_id = Endorsement.endorsed_id")
//                )
//            ),
//        ));

        $this->Endorsement->bindModel(array(
            "belongsTo" => array(
                "UserOrganization" => array(
                    "className" => "UserOrganization",
                    "foreignKey" => false,
                    "conditions" => array("UserOrganization.user_id = Endorsement.endorsed_id")
                )
            ),
        ));

        $conditionsjobtitles = array(
            "UserOrganization.job_title_id" => $id,
//            "UserOrganization.organization_id" => $organization_id,
            //"UserOrganization.status" => 1, 
            "Endorsement.organization_id" => $organizationId,
                //"Endorsement.endorsement_for" => "user"   
        );
        if ($startdate != "" and $enddate != "") {
            array_push($conditionsjobtitles, "date(Endorsement.created) between '$startdate' and '$enddate'");
        }

        $params = array();
        $params['fields'] = array("*");
        $params['conditions'] = $conditionsjobtitles;

        $endorsed_data = $this->Endorsement->find("all", $params);

//        pr($endorsed_data);die;

        $allvaluesendorsed = $this->Common->allvaluesendorsed($endorsed_data);
//        pr($allvaluesendorsed);die;

        $this->Endorsement->unbindModel(array('belongsTo' => array('UserOrganization'), 'hasMany' => array('EndorseAttachments', 'EndorseReplies')));

        //Bind for endorser_id
        $this->Endorsement->bindModel(array(
            "belongsTo" => array(
                "UserOrganization" => array(
                    "className" => "UserOrganization",
                    "foreignKey" => false,
                    "conditions" => array("UserOrganization.user_id = Endorsement.endorser_id")
                )
            ),
        ));

        $endorser_data = $this->Endorsement->find("all", $params);

        $departments = $this->Common->getorgdepartments($organizationId);
        $entities = $this->Common->getorgentities($organizationId);
        $allvaluesendorser = $this->Common->allvaluesendorser($endorser_data, $departments, $entities);

        $this->set(compact('jobTitle', 'endorsed_data', 'allvaluesendorsed', 'endorser_data', 'allvaluesendorser', 'organizationId', 'companyDetail', 'allothervalues'));
    }

    public function weekHistory($id, $week) {
        $weekDate = str_replace("-", "/", $week);
        $timeWeek = strtotime($weekDate);
        $startdate = date('Y-m-d', $timeWeek);
        $enddate = date('Y-m-d', strtotime($startdate . " +6 days"));
//        $date = strtotime($startdate . " +1 week");
//        echo $startdate . " -- " . $enddate;die;

        $this->loadModel("OrgDepartment");
        $this->loadModel("Endorsement");

        $organizationId = $this->Session->read('orgid');
        $orgdata = $this->Organization->findById($organizationId);
        $companyDetail = $this->Common->getcompanyinformation($orgdata["Organization"]);

        $department = $this->OrgDepartment->find("first", array("conditions" => array("id" => $id)));

        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));

        $params['fields'] = "Endorsement.*";
        $condition["Endorsement.endorsement_for"] = "department";
        $condition["Endorsement.endorsed_id"] = $id;
        if ($startdate != "" and $enddate != "") {
            array_push($condition, "date(Endorsement.created) between '$startdate' and '$enddate'");
        }
        $params['conditions'] = $condition;

        $endorsed_data = $this->Endorsement->find("all", $params);

        $allvaluesendorsed = $this->Common->allvaluesendorsed($endorsed_data);

        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organizationId);
        $allothervalues = array(
            "corevalues" => $orgcorevaluesandcode,
        );


        $this->set(compact('department', 'endorsed_data', 'allvaluesendorsed', 'organizationId', 'companyDetail', 'allothervalues'));
    }

    public function dayHistory($day) {
        $day = str_replace("-", "/", $day);
        $time = strtotime($day);
        $date = date('Y-m-d', $time);

        $this->loadModel("Endorsement");
        $this->loadModel("User");
        $authUser = $this->Auth->User();
        $organization_id = $this->Session->read('orgid');
        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);
        //=======================================endorsement all feature
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));
        $condtionsallendorsement = array("organization_id" => $organization_id, "DATE(created)" => $date);

        $allendorsement = $this->Endorsement->find("all", array("order" => "Endorsement.created DESC", "conditions" => $condtionsallendorsement));
//        echo $this->Endorsement->getLastQuery();die;
//pr($allendorsement);die;

        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $allvaluesendorsement = $this->Common->allvaluesendorsement($allendorsement, $departments, $entities);
        $jobtitles = $this->Common->getorgjobtitles($organization_id);
        //=======================================end endorsement all feature
        $this->set(compact("authUser", "organization_id", "companydetail", 'allvaluesendorsement', 'orgcorevaluesandcode', 'jobtitles', 'departments', 'entities'));
    }

    public function colorsettings($organization_id = null) {
        $errormsg = "";
        $this->set('jsIncludes', array('colorbranding'));
        $this->loadModel('OrgColor');

        //pr($orgColorData); exit;

        $orgdata = $this->Organization->findById($organization_id);
//        pr($orgdata); exit;

        if ($this->Session->check('Auth.User.role') != "1" || $this->Session->check('Auth.User.role') != "2") {
            $this->Auth->logout();
            $this->redirect(array('action' => 'login'));
        } else {
            $authUser = $this->Auth->User();
            $result = $this->Common->checkorgid($organization_id);
            if ($result == "redirect") {
                $this->redirect(array("controller" => "organizations", "action" => "index"));
            }
            if (!$organization_id) {
                throw new NotFoundException(__('Invalid post'));
            }

            if ($this->request->is('post')) {
                //pr($this->request->data);
                $this->Organization->id = $organization_id;

                $orgData['header_footer_color_light'] = isset($this->request->data['header_footer_color_light']) ? "'" . $this->request->data['header_footer_color_light'] . "'" : 'F47521';
                $orgData['header_footer_color_dark'] = isset($this->request->data['header_footer_color_dark']) ? "'" . $this->request->data['header_footer_color_dark'] . "'" : 'ED5B13';
                $orgData['background_color_light'] = isset($this->request->data['background_color_light']) ? "'" . $this->request->data['background_color_light'] . "'" : '1C2255';
                $orgData['background_color_dark'] = isset($this->request->data['background_color_dark']) ? "'" . $this->request->data['background_color_dark'] . "'" : '0C102F';
                $orgData['font_color'] = isset($this->request->data['font_color']) ? "'" . $this->request->data['font_color'] . "'" : 'FFFFFF';
                $orgData['button_color'] = isset($this->request->data['button_color']) ? "'" . $this->request->data['button_color'] . "'" : 'ED5B13';

                $updated = $this->Organization->updateAll($orgData, array('id' => $organization_id));

                if ($updated) {
                    $this->request->data = array();
                    $this->Session->setFlash(__('Color setting updated successfully'), 'default', array('class' => 'alert alert-warning'));
                } else {
                    $this->Session->setFlash(__('Unable to update setting'), 'default', array('class' => 'alert alert-warning'));
                }
            }
            $org_data = $this->Organization->findById($organization_id);

            $org_id = $org_data['Organization']['id'];
            $org_image = $org_data['Organization']['image'];

            $this->set(compact('authUser', 'org_data', 'org_id', 'org_image'));
        }
    }

    public function adsettings($orgid = null) {
        $loggedinUser = $this->Auth->User();
        $login_user_id = $loggedinUser['id'];
        $this->set('title_for_layout', "nDorse : AD Settings");
        if (isset($orgid)) {
            $this->loadModel('Organization');
            $org_id = $orgid;

            if ($this->request->is('post')) {
//                pr($this->request->data);
                $ID = $this->request->data['Organization']['id'];
                $orgID = $this->request->data['Organization']['org_id'];
                $ad_domain = $this->request->data['Organization']['ad_domain'];
                $admin_username = $this->request->data['Organization']['admin_username'];
                $base_dn = $this->request->data['Organization']['base_dn'];
                $admin_password = $this->request->data['Organization']['admin_password'];

                $OrganizationData['org_id'] = $orgID;
                $OrganizationData['ad_port'] = 389;

                if ($ID == '') {
                    $OrganizationData['admin_username'] = $admin_username;
                    $OrganizationData['admin_password'] = $admin_password;
                    $OrganizationData['base_dn'] = $base_dn;
                    $OrganizationData['ad_domain'] = $ad_domain;
                    $saved = $this->OrgAdSetting->save($OrganizationData);
                } else {
                    $OrganizationData['admin_username'] = "'" . $admin_username . "'";
                    $OrganizationData['admin_password'] = "'" . $admin_password . "'";
                    $OrganizationData['base_dn'] = "'" . $base_dn . "'";
                    $OrganizationData['ad_domain'] = "'" . $ad_domain . "'";
                    $saved = $this->OrgAdSetting->updateAll($OrganizationData, array('id' => $ID));
                }

                if ($saved) {
                    $this->request->data = array();
                    $this->Session->setFlash(__('Active Directory setting saved successfully'), 'default', array('class' => 'alert alert-warning'));
                } else {
                    $this->Session->setFlash(__('Unable to update setting'), 'default', array('class' => 'alert alert-warning'));
                }
            }
            $orgADSettings = $this->OrgAdSetting->find('all', array('conditions' => array('org_id' => $org_id, 'status' => '1',), array('limit' => 1)));
//            pr($orgADSettings); exit;
            if (!empty($orgADSettings)) {
                if (isset($orgADSettings[0]['OrgAdSetting']['id'])) {
                    $orgAdSetting = $orgADSettings[0]['OrgAdSetting'];

                    /* Get all AD Users */
                    $adAllUsersArray = $this->getAllADUsers($orgAdSetting);

                    //pr($adAllUsersArray);
                    $serverRespone = '';
                    if (!is_array($adAllUsersArray)) {
                        $serverRespone = $adAllUsersArray;
                    } else {

                        if ($orgAdSetting['all_users_uploaded_once'] == 0) { //RUN ONLY ONCE WHEN ORG AD NEW SETUP
                            /* Save all users to nDorse users and user-organizations */
                            $uploaded = $this->saveAdUsersToNdorse($adAllUsersArray, $orgid);
                            if ($uploaded) {
                                $OrganizationDataUpate['all_users_uploaded_once'] = 1;
                                $this->OrgAdSetting->updateAll($OrganizationDataUpate, array('id' => $orgAdSetting['id']));
                            }
                        }
                    }
                    $totalAdrecords = count($adAllUsersArray);
                }

                $this->User->bindModel(array('hasOne' => array('UserOrganization')));
                $org_user_data = $this->User->find('all', array("order" => "User.id DESC", 'limit' => 20,
                    'conditions' => array('UserOrganization.organization_id' => $orgid, 'UserOrganization.user_role' => array(2, 3, 6),
                        'User.source' => 'activedirectory'), 'order' => 'UserOrganization.user_role'));
                $this->User->bindModel(array('hasOne' => array('UserOrganization')));

                $totalOrgAdUsers = $this->User->find('all', array('fields' => array('User.*', 'UserOrganization.id', 'UserOrganization.status'), 'conditions' => array('UserOrganization.organization_id' => $orgid,
                        'UserOrganization.user_role' => array(2, 3, 6),
                        'User.source' => 'activedirectory'),
                    'order' => 'UserOrganization.id  DESC'));

                /* Filter AD list for fresh add */

                $totalOrgrecords = count($totalOrgAdUsers);

                $orgAdUsers = $orgAdExistUsers = array();

                if (!empty($totalOrgAdUsers)) {
                    foreach ($totalOrgAdUsers as $index => $uData) {
                        $orgAdUsers[] = $uData['User']['ad_accountname'];
                        $orgAdExistUsers[$uData['User']['ad_accountname']] = array('user' => $uData['User'], 'user_org_id' => $uData['UserOrganization']['id'], 'user_org_status' => $uData['UserOrganization']['status']);
                    }
                }

                $unAddedUsersList = $addedUsers = $disabledUsers = array();

                if (!empty($adAllUsersArray) && is_array($adAllUsersArray)) {
                    foreach ($adAllUsersArray as $index => $adUsers) {
                        $adSamName = $adUsers['User']['ad_accountname'];

                        if (!in_array($adSamName, $orgAdUsers)) {
                            $unAddedUsersList[$adSamName] = $adUsers['User'];
                        } else {
                            if ($orgAdExistUsers[$adSamName]['user']['status'] == 0 || $orgAdExistUsers[$adSamName]['user_org_status'] == 0) {
                                $disabledUsers[$adSamName] = $orgAdExistUsers[$adSamName];
                            } else {

                                $addedUsers[$adSamName] = $orgAdExistUsers[$adSamName];
                            }

//                            $addedUsers[$adSamName] = $adUsers['User'];
                        }
                    }
                }
//                pr($orgAdUsers);
//                pr($unAddedUsersList);
//                exit;

                /* -- */
            }
            $orgDetail = $this->Organization->findById($orgid);
            $roleList = $this->Common->setSessionRoles();
        } else {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }

        $this->set(compact("loggedinUser", 'orgADSettings', 'disabledUsers', 'orgDetail', 'addedUsers', 'adAllUsersArray', 'org_user_data', 'totalAdrecords', 'adAllUsersArray', 'serverRespone', 'roleList', 'totalOrgrecords', 'unAddedUsersList'));
        $this->set('jsIncludes', array('daisyportal.js'));
        $this->set('authUser', $this->Auth->user());
    }

    function getAllADUsers($orgAdSetting) {

        $domainName = $orgAdSetting['ad_domain'];
        $basedn = $orgAdSetting['base_dn']; //LDAP://adtest.in/DC=adtest,DC=in
        $username = $orgAdSetting['admin_username'];
        $ldap_password = $orgAdSetting['admin_password'];

        $ldap_dn = "cn=" . $username . $basedn;
        $domain = '@' . $domainName;
        $ldap_connect = ldap_connect("ldap://" . $domainName, 636);

        if (!$ldap_connect) {
            echo 'Could not connect to LDAP server dfsdgsdfg .';
        }

        ldap_set_option($ldap_connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap_connect, LDAP_OPT_REFERRALS, 0);

        if (@ldap_bind($ldap_connect, $username . $domain, $ldap_password)) {

            $result = ldap_search($ldap_connect, $basedn, "(&(objectClass=user)(cn=*)(!(objectClass=computer)))");

            if ($result === FALSE) {
                return 'No Result';
            }
            $entries = ldap_get_entries($ldap_connect, $result);
            if (!empty($entries)) {
                $totalCount = $entries['count'];
//                pr($entries);
                $adAllUsersArray = array();
                foreach ($entries as $index => $users) {
                    if ($index == 'count') {
                        continue;
                    }

                    $firstName = isset($users['givenname'][0]) ? $users['givenname'][0] : '';

                    $img = '';

                    if ($firstName == '') {
                        $firstName = isset($users['name'][0]) ? $users['name'][0] : '';
                    }
                    $lastName = isset($users['sn'][0]) ? $users['sn'][0] : '';
                    $uniqueAccountName = isset($users['samaccountname'][0]) ? $users['samaccountname'][0] : '';
                    $user_email = isset($users['mail'][0]) ? $users['mail'][0] : '';

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
                    $userAdDATA['User']['image'] = $img;
//                    pr($userAdDATA);
                    $adAllUsersArray[] = $userAdDATA;
                }
//                exit;
                return $adAllUsersArray;
            } else {
                return array();
            }
        } else {
            $errorMsg = ldap_error($ldap_connect);
            if ($errorMsg == "Can't contact LDAP server") {
                $errorMsg = 'Unable to connect server.';
            }
            return $errorMsg;
        }
    }

    function saveAdUsersToNdorse($allAdUsers, $org_id) {
//        pr($allAdUsers);
        $results = $this->User->find('all', array('fields' => array('ad_accountname'), 'conditions' => array('source' => 'activedirectory')));
        $orgAdUsers = array();

        if (!empty($results)) {
            foreach ($results as $index => $uData) {
                $orgAdUsers[] = $uData['User']['ad_accountname'];
            }
        }
//        pr($orgAdUsers);
        if (!empty($allAdUsers) && is_array($allAdUsers)) {
            foreach ($allAdUsers as $index => $adUsers) {
//                pr($adUsers);
                $adSamName = $adUsers['User']['ad_accountname'];
                if (!in_array($adSamName, $orgAdUsers)) {
//
//                    echo "ADDING";
//                    continue;
                    //Save User to ndorse DB users and user organization
                    $adUsers['User']['about'] = '';
                    $adUsers['User']['hobbies'] = '';

                    if ($adUsers['User']['email'] == '') {
                        $adUsers['User']['email'] = $adSamName . '@ndorse.net';
                        $adUsers['User']['username'] = $adSamName . '@ndorse.net';
                    }

                    $this->User->clear();
                    $this->User->setValidation('register');
                    $this->User->set($adUsers);
//                    pr($adUsers);
//                    exit;
                    if ($this->User->validates()) {
                        if ($userData = $this->User->save(null, false)) {
//                            pr($userData); exit;
//                            continue;
                            $loggedinUserId = $this->User->id;

                            /*                             * ** ASSIGNING ORG CODE START **** */
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
                                $conditions['Subscription.status'] = 1;
                                $conditions['Subscription.organization_id'] = $org_id;
                                $params['conditions'] = $conditions;
                                $currentSubscription = $this->Subscription->find("first", $params);
                                if (!empty($currentSubscription)) {
                                    $poolPurchased = $currentSubscription['Subscription']['pool_purchased'];
                                    if ($paidCount >= $poolPurchased) {
                                        $status = $statusConfig['inactive'];
                                    } else {
                                        $status = $statusConfig['active'];
                                    }
                                } else {
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
                        $this->UserOrganization->clear();
                        $saved = $this->UserOrganization->save($newUserOrganization);

                        if ($saved) {
                            $defaultOrg = array("organization_id" => $org_id, "user_id" => $loggedinUserId);
                            $this->DefaultOrg->clear();
                            $this->DefaultOrg->save($defaultOrg);
                            /*                             * ** ASSIGNING ORG END **** */
                        } else {
                            $errors = $this->User->validationErrors;
                            $errorsArray = array();

                            foreach ($errors as $key => $error) {
                                $errorsArray[$key] = $error[0];
                            }
                        }

                        /**/
                    } else {
                        //echo "INVALID";
                    }
//                    exit;
                    //SAVE To ndorse DB end.
                }
            }
        }
        return true;
        //pr($orgAdUsers);
        //pr($allAdUsers);
    }

    function addNewADUser() {
        $this->autoRender = false;
        $this->layout = false;
//        pr($this->request->data['userData']);
        $userData = $this->request->data['userData'];
        $org_id = $this->request->data['org_id'];
//        echo $org_id; exit;
        $results = $this->User->find('all', array('fields' => array('ad_accountname', 'email'), 'conditions' => array('source' => 'activedirectory')));
        $orgAdUsers = array();

        if (!empty($results)) {
            foreach ($results as $index => $uData) {
                $orgAdUsers[] = $uData['User']['ad_accountname'];
            }
        }
        if (!empty($results)) {
            foreach ($results as $index => $uData) {
                $orgAdMailUsers[] = $uData['User']['email'];
            }
        }

        if (!empty($userData) && is_array($userData)) {
            foreach ($userData as $index => $userInfo) {
                $adUsers = array();
                $adUsers['User'] = $userInfo;

                $adSamName = $adUsers['User']['ad_accountname'];
                $adEmail = $adUsers['User']['email'];
                if (!in_array($adSamName, $orgAdUsers) || !in_array($adEmail, $orgAdMailUsers)) {
//
//                    echo "ADDING";
//                    continue;
                    //Save User to ndorse DB users and user organization
                    $adUsers['User']['about'] = '';
                    $adUsers['User']['hobbies'] = '';

                    if ($adUsers['User']['email'] == '') {
                        $adUsers['User']['email'] = $adSamName . '@ndorse.net';
                        $adUsers['User']['username'] = $adSamName . '@ndorse.net';
                    }

                    $this->User->clear();
                    $this->User->setValidation('register');
                    $this->User->set($adUsers);
//                    pr($adUsers);
//                    exit;
                    if ($this->User->validates()) {
                        if ($userData = $this->User->save(null, false)) {
//                            pr($userData);
//                            exit;
//                            continue;
                            $loggedinUserId = $this->User->id;

                            /*                             * ** ASSIGNING ORG CODE START **** */
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
                                $conditions['Subscription.status'] = 1;
                                $conditions['Subscription.organization_id'] = $org_id;
                                $params['conditions'] = $conditions;
                                $currentSubscription = $this->Subscription->find("first", $params);
                                if (!empty($currentSubscription)) {
                                    $poolPurchased = $currentSubscription['Subscription']['pool_purchased'];
                                    if ($paidCount >= $poolPurchased) {
                                        $status = $statusConfig['inactive'];
                                    } else {
                                        $status = $statusConfig['active'];
                                    }
                                } else {
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
                        $this->UserOrganization->clear();
                        $saved = $this->UserOrganization->save($newUserOrganization);

                        if ($saved) {
                            $defaultOrg = array("organization_id" => $org_id, "user_id" => $loggedinUserId);
                            $this->DefaultOrg->clear();
                            $this->DefaultOrg->save($defaultOrg);
                            /*                             * ** ASSIGNING ORG END **** */
                        } else {
                            $errors = $this->User->validationErrors;
                            $errorsArray = array();

                            foreach ($errors as $key => $error) {
                                $errorsArray[$key] = $error[0];
                            }
                        }

                        /**/
                    } else {
                        //echo "INVALID";
//                        $this->Session->setFlash(__('Unable to save users'), 'default', array('class' => 'alert alert-danger'));
//                        echo json_encode(array('success' => false, "msg" => 'Validataion error!'));
//                        exit;
                    }
//                    exit;
                    //SAVE To ndorse DB end.
                }
            }
        }
        $this->Session->setFlash(__('Users added successfully.'), 'default', array('class' => 'alert alert-success'));
        echo json_encode(array('success' => true, "msg" => 'All users added successfully.'));
    }

    function disableAdUser() {
        $this->autoRender = false;
        $this->layout = false;
        $userIdsArray = $this->request->data['userData'];
        $orgId = $this->request->data['org_id'];
        if (is_array($userIdsArray) && !empty($userIdsArray)) {
            foreach ($userIdsArray as $index => $userId) {
                $this->User->clear();
                $user['status'] = 0;
                $this->User->updateAll($user, array('id' => $userId));

                $this->UserOrganization->clear();
                $userOrganization['status'] = 0;
                $this->UserOrganization->updateAll($userOrganization, array('user_id' => $userId, 'organization_id' => $orgId));
            }
        }
        $this->Session->setFlash(__('Users disabled successfully.'), 'default', array('class' => 'alert alert-success'));
        echo json_encode(array('success' => true, "msg" => 'All users disabled successfully.'));
    }

    function enableAdUser() {
        $this->autoRender = false;
        $this->layout = false;

        $userIdsArray = $this->request->data['userData'];
        $orgId = $this->request->data['org_id'];

        if (is_array($userIdsArray) && !empty($userIdsArray)) {
            foreach ($userIdsArray as $index => $userId) {
                $this->User->clear();
                $user['status'] = 1;
                $this->User->updateAll($user, array('id' => $userId));

                $this->UserOrganization->clear();
                $userOrganization['status'] = 1;
                $this->UserOrganization->updateAll($userOrganization, array('user_id' => $userId, 'organization_id' => $orgId));
            }
        }
        $this->Session->setFlash(__('Users Enabled successfully.'), 'default', array('class' => 'alert alert-success'));
        echo json_encode(array('success' => true, "msg" => 'All users enabled successfully.'));
    }

    public function bulkimportadfs($orgId) {
        $this->set(compact('orgId'));
    }

}
