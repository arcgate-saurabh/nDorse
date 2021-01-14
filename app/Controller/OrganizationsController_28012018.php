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
    var $uses = array("User", "Department", "Organization", "OrgCoreValue", "UserOrganization", "OrgDepartment", "OrgJobTitle", "Entity", "Subscription");
    public $helpers = array("Html", "Form", "Session", "Js");

    public function beforeFilter() {
        parent::beforeFilter();
        // $this->Auth->allow('login');
        $this->Auth->allow('register', 'login', 'logout', 'forgot', 'createclient', 'setImage', 'deleteimage');
    }

    public function index() {  //indexd
        ini_set('memory_limit', '1024M');
        if ($this->Session->check('Auth.User')) {
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
                    ),
                    'hasOne' => array('Subscription' => array(
                            'className' => 'Subscription',
                        ))
                ));

                $this->Organization->recursive = 2;

                $orgdata = $this->Organization->find('all', array('order' => 'Organization.created DESC', 'limit' => 20, 'conditions' => $conditions));

                $totalrecords = $this->Organization->find('count', array('conditions' => $conditions));
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

                    foreach ($userorg as $uval) {
                        if ($uval["user_role"] == 2) {
                            $adminusr[] = $uval["user_id"];
                        }
                    }

                    $inviationStats[$target_id] = $this->Common->getInvitationDetails($userorg);

//                    $totalinvitationsaccepted[$target_id] = $this->Common->userorgcounter($userorg);
//                    $invitation_accepted[$target_id] = $totalinvitationsaccepted[$target_id]["web"] + $totalinvitationsaccepted[$target_id]["app"];
//                    $invitations_array[$target_id] = $this->Common->invitations_fetching($orgid);
//                    $invitation_pending[$target_id] = $invitations_array[$target_id]["invitations_pending"];
//                    $invitation_pending[$target_id]["web"] = $totalinvitationsaccepted[$target_id]["web"] + $invitation_pending[$target_id]["web"];
//                    $invitation_pending[$target_id]["app"] = $totalinvitationsaccepted[$target_id]["app"] + $invitation_pending[$target_id]["app"];
//                    $totalinvitations[$target_id] = array("invitation_accepted" => $invitation_accepted, "invitation_pending" => $invitation_pending);
                    $pendingrequescounter[$target_id] = $this->OrgRequest->find("count", array("conditions" => array("organization_id" => $target_id, "status" => 0)));
                    $endorsementformonth[$target_id] = $this->Common->endorsementformonth($target_id);
                    $userPool = $orgid['Subscription']['pool_purchased'];
                    foreach ($orgid['Transactions'] as $transaction) {

                        if ($transaction["status"] == "canceled") {
                            $adminusr[] = $transaction["user_id"];
                        }
                        if ($transaction['bt_subscription_id'] == $orgid['Subscription']['bt_id']) {
                            if ($transaction['type'] == 'upgrade') {
                                $userPool += $transaction['user_diff'];
                            } else if ($transaction['type'] == 'downgrade') {
                                $userPool -= $transaction['user_diff'];
                            }
                        }
                    }

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
            $org_user_data = $this->User->find('all', array("order" => "User.id DESC", 'limit' => 20, 'conditions' => array('UserOrganization.organization_id' => $id, 'UserOrganization.user_role' => array(2, 3, 6), 'UserOrganization.status' => array(0, 1, 3)), 'order' => 'UserOrganization.user_role'));


            $this->User->bindModel(array('hasOne' => array('UserOrganization')));
            $totalrecords = $this->User->find('count', array('conditions' => array('UserOrganization.organization_id' => $id, 'UserOrganization.user_role' => array(2, 3, 6), 'UserOrganization.status' => array(0, 1, 3)), 'order' => 'UserOrganization.id  DESC'));
            $coredata = $this->OrgCoreValue->find('list', array('conditions' => array('organization_id' => $id, 'status' => array(1, 2))));
            $corevalueendorsedcounter = array();
            foreach ($coredata as $key => $data) {
                $corevalueendorsedcounter[$key] = $this->EndorseCoreValue->find("count", array("conditions" => array("value_id" => $key)));
            }

            $this->loadModel("Endorsement");
            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValue')));
            $conditionsendorsement = array("organization_id" => $id, 'status' => 1);
            $totalendorsements = $this->Endorsement->find("count", array("conditions" => $conditionsendorsement));
            $conditionsendorsement[] = array("MONTH(created) = MONTH(now())", "YEAR(created) = YEAR(now())");

            $endorsementdata = $this->Endorsement->find("all", array("conditions" => $conditionsendorsement));

            $corevaluesid = array();
            foreach ($endorsementdata as $dataendorsements) {
                foreach ($dataendorsements["EndorseCoreValues"] as $corevalues) {
                    $corevaluesid[] = $corevalues["value_id"];
                }
            }

            $countermonthlyendorsements = array_count_values($corevaluesid);
            $this->loadModel("OrgRequest");
            $this->loadModel("Endorsement");
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

            $userorg = $orgdata["UserOrganization"];
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
            // end
//            pr($adminusrarray); exit;
            $this->set(compact('totalusers', 'adminusrarray', 'activeusercount', 'inactiveusercount', 'orgdata', 'coredata', 'org_user_data', 'uploadedemssage', 'invitations_array', 'pendingrequescounter', 'invitation_pending', 'invitation_accepted', 'endorsementformonth', 'corevalueendorsedcounter', 'totalrecords', 'countermonthlyendorsements', 'totalendorsements', 'inviationStats'));
            $this->set('authUser', $this->Auth->user());
        }
    }

    public function bulkusertemplate() {
        $result = array();
        $filename = "bulkuser_template.csv";
        $fp = fopen('php://output', 'w');
        //$result = $this->User->getColumnTypes();

        $result = array("EmployeeID", "FirstName", "LastName", "Suffix", "Department", "Title", "Email", "MobilePhone", "Status {Inactive:0, Active:1,Eval:2}", "SendInvitation{1:Yes,0:No}", "sub_org");
//        foreach ($removefields as $remove) {
//            unset($result[$remove]);
//        }
        //changed dob format
        //$result[3] = "dob(format should be YYYY-MM-DD)";
        //array_push($result, "departments", "entities", "jobtitles");
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
        ini_set('memory_limit', '1024M');
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
        $endorsementdata = $this->UserOrganization->find("all", array("order" => "User.fname", "conditions" => array("UserOrganization.organization_id" => $organization_id, "UserOrganization.status" => array(0, 1, 2, 3), "UserOrganization.user_role" => array(2, 3, 4, 6))));
        //pr($endorsementdata);exit;
        //===================endorsement by day graph
        $conditionsendorsementbyday["organization_id"] = $organization_id;
        $conditionsendorsementbyday['type !='] = 'guest';
        if ($startdate != "" and $enddate != "") {
            array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
        }
        $endorsementbyday = $this->Endorsement->find("all", array("conditions" => $conditionsendorsementbyday, "group" => "date(Endorsement.created)", "fields" => array("count(*) as cnt", "date(created) as cdate")));
        //=============endorsement by department
        $params['fields'] = "count(Endorsement.endorsed_id) as cnt,OrgDepartments.name as department, OrgDepartments.id as department_id";
        $conditionarray["Endorsement.organization_id"] = $organization_id;
        $conditionarray["Endorsement.endorsement_for"] = "department";
        $conditionarray["Endorsement.type !="] = "guest";
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
                    'OrgDepartments.id = Endorsement.endorsed_id'
                )
            )
        );
        $params['order'] = 'cnt desc';


        $params['group'] = 'Endorsement.endorsed_id';
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
//        pr($params);
        $leaderboard = $this->Endorsement->find("all", $params);
//        echo $this->Endorsement->getLastQuery(); exit;
//        pr($leaderboard); exit;
        //=================end of endorsement by day graph
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
        $paramsdepthistory["conditions"] = array("Endorsement.organization_id" => $organization_id, "Endorsement.endorsement_for" => "department", "type !=" => "guest");
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


        unset($this->Endorsement->virtualFields['weekdepartment']);
        unset($this->Endorsement->virtualFields['yeardepartment']);
        unset($this->Endorsement->virtualFields['endorseddepartment']);

        //pr($endorsementbydeptweek);
        $startofweekarray = "";
        $counter = "";
        //pr($endorsementbydeptweek);
        $dept_array = array();
        $date_array = array();
        foreach ($endorsementbydeptweek as $endorsementdeptweek) {
            $dept_array[$endorsementdeptweek["OrgDepartment"]['id']] = $deptname = $endorsementdeptweek["OrgDepartment"]["name"];
            $date_array[] = $startofweekarray = $this->Common->getStartAndEndDate($endorsementdeptweek["Endorsement"]["weekdepartment"], $endorsementdeptweek["Endorsement"]["yeardepartment"]);
            $counter[$startofweekarray][$deptname] = (int) $endorsementdeptweek["Endorsement"]["endorseddepartment"];
            //$startofweekarray[] = $this->Common->getStartAndEndDate($endorsementdeptweek["Endorsement"]["weekdepartment"], $endorsementdeptweek["Endorsement"]["yeardepartment"]);
        }
        //============to take date array as unique
        $date_array = array_unique($date_array);
        $dept_array = array_unique($dept_array);
        $server_data = array();
        $idData = array();
        foreach ($dept_array as $id => $deptname) {
            foreach ($counter as $key => $data) {
                $dept = array_keys($data);
                if (!in_array($deptname, $dept)) {
                    $data = 0;
                } else {
                    $data = $counter[$key][$deptname];
                }
                $server_data[$deptname][] = $data;
                $idData[$deptname] = $id;
            }
        }
        foreach ($date_array as $key => $converteddatearray) {
            $converted_date_array[$key] = $this->Common->dateConvertDisplay($converteddatearray);
        }
//       pr($server_data);die;
        #pr($counter);die;
        if (!empty($counter)) {
            $counter = $server_data;
            $counter = json_encode(array('counter' => $counter, 'date_array' => $converted_date_array));
            $idData = json_encode($idData);
        }

        $orgdata = $this->Organization->findById($organization_id);
        $companydetail = $this->Common->getcompanyinformation($orgdata["Organization"]);

        //=====common array to be used in export
        $arrayendorsementdetail = $this->Common->arrayforendorsementdetail($endorsementdata);
        $this->Session->write('orgid', $organization_id);
        $this->Session->write('datearray', array("startdate" => $startdate, "enddate" => $enddate));

        //=========================chart 5 endorsement by job title
        //========bind model functionality in common
        $this->Common->bindmodelcommonjobtitle();
        $jobtitles = $this->Common->getorgjobtitles($organization_id);
        $jobtitlesid = array_keys($jobtitles);

        $conditionsjobtitles = array(
            "UserOrganization.job_title_id" => $jobtitlesid,
            "UserOrganization.organization_id" => $organization_id,
            //"UserOrganization.status" => 1, 
            "Endorsement.organization_id" => $organization_id,
                //"Endorsement.endorsement_for" => "user"   
        );
        if ($startdate != "" and $enddate != "") {
            array_push($conditionsjobtitles, "date(Endorsement.created) between '$startdate' and '$enddate'");
        }
        //=============using below query
        /* select user_organizations.job_title_id, count(*) from user_organizations inner join endorsements on user_organizations.user_id = endorsements.endorser_id where endorsements.organization_id = 335 and  user_organizations.job_title_id in (550,551,552) and user_organizations.organization_id  = 335 and  user_organizations.status = 1  group by  user_organizations.job_title_id
          select user_organizations.job_title_id, count(*) from user_organizations inner join endorsements on user_organizations.user_id = endorsements.endorsed_id  where endorsements.organization_id = 335 and endorsements.endorsement_for = "user" and  user_organizations.job_title_id in (550,551,552) and user_organizations.organization_id  = 335 and  user_organizations.status = 1  group by  user_organizations.job_title_id */
        //=============using below query
        $groupjobtitle = array("UserOrganization.job_title_id");
        $fieldsjobtitle = array("UserOrganization.job_title_id", "count(DISTINCT Endorsement.id)");
        //$this->UserOrganization->virtualfield["counterjobtitle"] = ""
        $jobtitledataendorsed = $this->UserOrganization->find("all", array("conditions" => $conditionsjobtitles, "group" => $groupjobtitle, "fields" => $fieldsjobtitle));

        $jbiddata = array();
        foreach ($jobtitledataendorsed as $endorserjbdata) {
            $jbiddata[$endorserjbdata["UserOrganization"]["job_title_id"]] = $endorserjbdata[0]["count(DISTINCT Endorsement.id)"];
        }

        $detailedjobtitlechart = array("data" => $jbiddata, "jobtitles" => $jobtitles);

        //=======================end job title chart 5
        //======================chart 6 for endorsement by entity/ suborganizations
        $entityarray = $this->Common->getorgentities($organization_id);
        $conditionsentity = array("Endorsement.endorsement_for" => "entity", "Endorsement.organization_id" => $organization_id, "Endorsement.type" => "guest");
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
        //pr($detailedentitychart); exit;
        //======================end chart 6 for endorsement by entity/ suborganizations
        $datesarray = array("startdate" => $startdate, "enddate" => $enddate);
        $this->set(compact("authUser", "organization_id", "arrayendorsementdetail", 'companydetail', 'endorsementbyday', 'leaderboard', 'counter', 'startofweekarray', 'datesarray', 'allvaluesendorsement', 'orgcorevaluesandcode', 'detailedjobtitlechart', 'detailedentitychart', 'resultantendorsementbyDept', 'allvaluesfordeptandentity', 'idData'));
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
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
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
        $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($organization_id);
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
//            exit;

            $organizationslist = array();
            $content = $this->request->data['User']["mailingbox"];
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
            $announcement['message'] = $content;
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
            $this->Common->announcementspostdata($organizationslist, $content, $newfilename, $usersList, $departmentList, $suborgList, $scheduled, $UTCTimeToPost, $announcementID, $senderID);
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
                $announcemetnparams["fields"] = array("Announcement.*", "User.image", "concat(User.fname,' ',User.lname) as posted_user_name");
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

}

