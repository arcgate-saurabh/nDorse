<?php

class ReportsController extends AppController {

    public $components = array("Auth", "Common", "Session");
    public $uses = array("User", "UserOrganization", "Organization", "Subscription", "EndorseCoreValue", "Endorsement");

    public function beforeFilter() {
        parent::beforeFilter();

        $loggedinUserRole = $this->Auth->user('role');

//        if ($loggedinUserRole != 1) {
//            $this->Auth->logout();
//        }
    }

    public function index() {
        
    }

    public function users() {
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 0);
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
        $this->layout = "ajax";
        $this->loadModel("Organization");
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $folderToSaveXls = WWW_ROOT . 'xlsxfolder';

        $this->UserOrganization->bindModel(array(
            'belongsTo' => array(
                'Department' => array(
                    'className' => 'Department',
                ),
                'Entity' => array(
                    'className' => 'Entity',
                ),
            )
        ));
        $userOrganizations = $this->UserOrganization->find("all", array("order" => array("UserOrganization.user_id ASC")));

        $objPHPExcel->getProperties()->setCreator("Ndorse");
        $objPHPExcel->getProperties()->setLastModifiedBy("Ndorse");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHPExcel classes.");
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        try {

            $header = array("First Name", "Last Name", "Username", "Email",
                "Organization Name", "Role", "Status", "DOB",
                "Phone No.", "Department", "Sub-Organization", "Country", "State", "City", "Street", "Zip");
            $fieldList = array(
                array("User", "fname"), array("User", "lname"), array("User", "username"), array("User", "email"),
                array("Organization", "name"), array("UserOrganization", "user_role"), array("UserOrganization", "status"), array("User", "dob"),
                array("User", "mobile"), array("Department", "name"), array("Entity", "name"), array("User", "country"), array("User", "state"), array("User", "city"),
                array("User", "street"), array("User", "zip"));

//            $fieldList = array(
//                                        array("User" => "fname"), array("User" => "lname"), array("User" => "username"), array("User" => "email"), 
//                                        array("Organization" => "name"), array("UserOrganization" => "user_role"), array("UserOrganization" => "status"), 
//                                        array("User" => "dob"), array("User" => "mobile"), array("Department" => "name"), array("Entity" => "name"), 
//                                        array("User" => "country"), array("User" => "state"), array("User" => "city"), array("User" => "street"), 
//                                        array("User" => "zip"));

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
            $rowCount = 5;
            $columnsum = 0;
            $j = 0;
            $statusConfig = Configure::read('statusConfig');
            $roleList = $this->Common->setSessionRoles();

            foreach ($userOrganizations as $userOrganization) {
                $columCount = 0;

                foreach ($fieldList as $field) {
                    $modalName = $field[0];
                    $fieldName = $field[1];
                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($columCount++);

                    if ($fieldName == 'user_role') {
                        if ($roleList[$userOrganization['UserOrganization']['user_role']] == 'endorser') {
                            $fieldValue = "nDorser";
                        } else {
                            $fieldValue = ucfirst($roleList[$userOrganization['UserOrganization']['user_role']]);
                        }
                    } else if ($fieldName == 'status') {
                        $fieldValue = array_search($userOrganization['UserOrganization']["status"], $statusConfig);
                    } else {
                        $fieldValue = $userOrganization[$modalName][$fieldName];
                    }

                    $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $fieldValue);
                }

                $j++;
                $rowCount++;
            }

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

            //======set height of first column
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);


            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            $filename = 'alluserlist.xlsx';
            $objWriter->save($folderToSaveXls . '/' . $filename);
            echo json_encode(array("filename" => $filename, "msg" => "success"));
            exit();
        } catch (Exception $e) {
            echo json_encode(array("filename" => $filename, "msg" => $e));
        }

        exit();
    }

    public function organizations() {
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 0);
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
        $this->layout = "ajax";
        $this->loadModel("Organization");
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $folderToSaveXls = WWW_ROOT . 'xlsxfolder';

        $objPHPExcel->getProperties()->setCreator("Ndorse");
        $objPHPExcel->getProperties()->setLastModifiedBy("Ndorse");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHPExcel classes.");
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        try {

            $header = array("Organization Name", "Code", "Country", "State", "City", "Street", "Zip",
                "Status", "Available Limit", "Paid Users", "Free Users", "Total Users", "Invitations Sent", "Invitations Accepted", "Core Values");

            $j = 0;
            $rowCount = 3;
            foreach ($header as $resultheader) {
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $resultheader);
                //=========
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
                $j++;
            }
            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization')));
            $this->Organization->bindModel(array(
                'hasMany' => array(
                    'Invite' => array(
                        'className' => 'Invite',
                    ),
                    'UserOrganization' => array(
                        'className' => 'UserOrganization',
                    ),
                    'OrgCoreValues' => array(
                        'className' => 'OrgCoreValue',
                        'order' => 'created ASC'
                    )
                ),
                'hasOne' => array('Subscription' => array(
                        'className' => 'Subscription',
                    ))
            ));
            $this->Organization->recursive = 2;

            $orgdata = $this->Organization->find('all', array('order' => 'Organization.id ASC'));
            $userRole = array(2, 3);
            $rowCount = 5;

            foreach ($orgdata as $key => $orgDetails) {

                $orgId = $orgDetails['Organization']['id'];
                $userorg = $orgDetails["UserOrganization"];

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(0);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $orgDetails['Organization']['name']);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(1);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $orgDetails['Organization']['secret_code']);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(2);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $orgDetails['Organization']['country']);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(3);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $orgDetails['Organization']['state']);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(4);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $orgDetails['Organization']['city']);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(5);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $orgDetails['Organization']['street']);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(6);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $orgDetails['Organization']['zip']);

                $statusConfig = Configure::read('statusConfig');
                $statusValue = array_search($orgDetails['Organization']["status"], $statusConfig);
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(7);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $statusValue);

                if ($orgDetails['Subscription']['is_deleted'] == 1) {
                    $availableLimit = FREE_POOL_USER_COUNT;
                } else {
                    $availableLimit = $orgDetails['Subscription']['pool_purchased'] + FREE_POOL_USER_COUNT;
                }

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(8);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $availableLimit);

                $paidUsers = $availableLimit - FREE_POOL_USER_COUNT;
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(9);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $paidUsers);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(10);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, FREE_POOL_USER_COUNT);


                $totalorgusers = $this->Common->getusersfororg($orgId, $userRole);
                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(11);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $totalorgusers);

                $inviationStats = $this->Common->getInvitationDetails($userorg);

//                $totalInvitationsAccepted = $this->Common->userorgcounter($userorg);
//                $invitationAccepted = $totalInvitationsAccepted["web"] + $totalInvitationsAccepted["app"];
//                $invitationsArray = $this->Common->invitations_fetching($orgDetails);
//                
//                $invitationsPending = $invitationsArray["invitations_pending"];
//                
//                $invitationsPending["web"] = $totalInvitationsAccepted["web"] + $invitationsPending["web"];
//                $invitationsPending["app"] = $totalInvitationsAccepted["app"] + $invitationsPending["app"];
//                
                $totalInvitationsSent = $inviationStats['total_invitations']["app"] + $inviationStats['total_invitations']["web"];

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(12);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $totalInvitationsSent);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(13);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $inviationStats['invitations_accepted']);

                $coreValues = $this->Common->getOrgCoreValues($orgDetails['OrgCoreValues']);
                $coreValuesList = implode(", ", $coreValues);

                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(14);
                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $coreValuesList);

//                $orgId = $orgDetails["Organization"]["id"];
//                $owner_id = $orgDetails["Organization"]["admin_id"];
//                $totalorgusers = $this->Common->getusersfororg($orgId, $userRole);
//                $orgowner = $this->Common->getorgownername($owner_id);
//                $totalusers[$orgId] = $totalorgusers;
//
//                $ownersarray[$orgId][$owner_id] = $orgowner;
//                $userorg = $orgDetails["UserOrganization"];
//
//                $totalInvitationsAccepted[$orgId] = $this->Common->userorgcounter($userorg);
//                $invitation_accepted[$orgId] = $totalInvitationsAccepted[$orgId]["web"] + $totalInvitationsAccepted[$orgId]["app"];
//                $invitations_array[$orgId] = $this->Common->invitations_fetching($orgDetails);
//                $invitationsPending[$orgId] = $invitations_array[$orgId]["invitations_pending"];
//                $invitationsPending[$orgId]["web"] = $totalInvitationsAccepted[$orgId]["web"] + $invitationsPending[$orgId]["web"];
//                $invitationsPending[$orgId]["app"] = $totalInvitationsAccepted[$orgId]["app"] + $invitationsPending[$orgId]["app"];
//                
//                pr($invitationsPending);die;
//                
//                $totalinvitations[$orgId] = array("invitation_accepted" => $invitation_accepted, "invitation_pending" => $invitationsPending);
//                $pendingrequescounter[$orgId] = $this->OrgRequest->find("count", array("conditions" => array("organization_id" => $orgId, "status" => 0)));
//                $endorsementformonth[$orgId] = $this->Common->endorsementformonth($orgId);
//                
//                foreach ($orgDetails['Transactions'] as $transaction) {
//
//                    if ($transaction["status"] == "canceled") {
//                        $adminusr[] = $transaction["user_id"];
//                    }
//                    if ($transaction['bt_subscription_id'] == $orgDetails['Subscription']['bt_id']) {
//                        if ($transaction['type'] == 'upgrade') {
//                            $userPool += $transaction['user_diff'];
//                        } else if ($transaction['type'] == 'downgrade') {
//                            $userPool -= $transaction['user_diff'];
//                        }
//                    }
//                }
//
//                $orgdata[$key]['Subscription']['user_pool'] = $userPool;
//                
//                
//                
//                
//                
//                
//                
//               
//                
//                
//                
//                
//                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(0);
//                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $orgDetails['Organization']['name']);
//                
//                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(0);
//                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $orgDetails['Organization']['name']);
//                
//                $columnLetter = PHPExcel_Cell::stringFromColumnIndex(0);
//                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $orgDetails['Organization']['name']);






                $rowCount++;
            }


//            $fieldList = array(
//                array("User", "fname"), array("User", "lname"), array("User", "username"), array("User", "email"),
//                array("Organization", "name"), array("UserOrganization", "user_role"), array("UserOrganization", "status"), array("User", "dob"),
//                array("User", "mobile"), array("Department", "name"), array("Entity", "name"), array("User", "country"), array("User", "state"), array("User", "city"),
//                array("User", "street"), array("User", "zip"));
//
////            $fieldList = array(
////                                        array("User" => "fname"), array("User" => "lname"), array("User" => "username"), array("User" => "email"), 
////                                        array("Organization" => "name"), array("UserOrganization" => "user_role"), array("UserOrganization" => "status"), 
////                                        array("User" => "dob"), array("User" => "mobile"), array("Department" => "name"), array("Entity" => "name"), 
////                                        array("User" => "country"), array("User" => "state"), array("User" => "city"), array("User" => "street"), 
////                                        array("User" => "zip"));
//
//            $j = 0;
//            $rowCount = 3;
//            foreach ($header as $resultheader) {
//                $columnLetter = PHPExcel_Cell::stringFromColumnIndex($j);
//                $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $resultheader);
//                //=========
//                $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
//                $j++;
//            }
//
            //===to bold the first row
            $objPHPExcel->getActiveSheet()->getStyle("A3:" . $columnLetter . "3")->getFont()->setBold(true);
//            $rowCount = 5;
//            $columnsum = 0;
//            $j = 0;
//            $statusConfig = Configure::read('statusConfig');
//            $roleList = $this->Common->setSessionRoles();
//
//            foreach ($userOrganizations as $userOrganization) {
//                $columCount = 0;
//
//                foreach ($fieldList as $field) {
//                    $modalName = $field[0];
//                    $fieldName = $field[1];
//                    $columnLetter = PHPExcel_Cell::stringFromColumnIndex($columCount++);
//
//                    if ($fieldName == 'user_role') {
//                        if ($roleList[$userOrganization['UserOrganization']['user_role']] == 'endorser') {
//                            $fieldValue = "nDorser";
//                        } else {
//                            $fieldValue = ucfirst($roleList[$userOrganization['UserOrganization']['user_role']]);
//                        }
//                    } else if ($fieldName == 'status') {
//                        $fieldValue = array_search($userOrganization['UserOrganization']["status"], $statusConfig);
//                    } else {
//                        $fieldValue = $userOrganization[$modalName][$fieldName];
//                    }
//
//                    $objPHPExcel->getActiveSheet()->SetCellValue($columnLetter . $rowCount, $fieldValue);
//                }
//
//                $j++;
//                $rowCount++;
//            }

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

            //======set height of first column
            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(50);


            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            $filename = 'allOrgList.xlsx';
            $objWriter->save($folderToSaveXls . '/' . $filename);
            echo json_encode(array("filename" => $filename, "msg" => "success"));
            exit();
        } catch (Exception $e) {
            echo json_encode(array("filename" => $filename, "msg" => $e));
        }

        exit();
    }

    public function ndorsement_history_day() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $organization_id = $this->request->data['organization_id'];

        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
        }

        $conditionsendorsementbyday["organization_id"] = $organization_id;
        $conditionsendorsementbyday['type !='] = 'guest';
        if ($startdate != "" and $enddate != "") {
            array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
        }
        $endorsementbyday = $this->Endorsement->find("all", array("conditions" => $conditionsendorsementbyday, "group" => "date(Endorsement.created)", "fields" => array("count(*) as cnt", "date(created) as cdate")));
        $this->set(compact("endorsementbyday"));

        echo $this->render('/Elements/endorsementbyday_web');
//        echo $this->Element("endorsementbyday_web", array("data" => $endorsementbyday));
        exit;
    }

    public function leaderboard() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $organization_id = $this->request->data['organization_id'];

        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
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
//        pr($endorsementdata);
//        exit;
        $arrayendorsementdetail = $this->Common->arrayforendorsementdetail($endorsementdata);

        $this->set('jsIncludes', array('jquery.tablesorter.js'));

        $this->set(compact("endorsementdata", "organization_id", "arrayendorsementdetail"));
        echo $this->render('/Elements/leaderboarddata');
//        echo $this->Element("endorsementbyday_web", array("data" => $endorsementbyday));
        exit;
    }

    public function weekly_ndorsement_histoty_dept() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $organization_id = $this->request->data['organization_id'];

        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
        }


        $this->loadModel("OrgDepartment");
        $this->loadModel("Endorsement");
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
        //pr($endorsementbydeptweek); exit;   

        unset($this->Endorsement->virtualFields['weekdepartment']);
        unset($this->Endorsement->virtualFields['yeardepartment']);
        unset($this->Endorsement->virtualFields['endorseddepartment']);

        //pr($endorsementbydeptweek);
        $startofweekarray = "";
        $counter = "";
//        //pr($endorsementbydeptweek); exit;
        $dept_array = array();
        $date_array = array();
        foreach ($endorsementbydeptweek as $endorsementdeptweek) {
            $dept_array[$endorsementdeptweek["OrgDepartment"]['id']] = $deptname = $endorsementdeptweek["OrgDepartment"]["name"];
            $date_array[] = $startofweekarray = $this->Common->getStartAndEndDate($endorsementdeptweek["Endorsement"]["weekdepartment"], $endorsementdeptweek["Endorsement"]["yeardepartment"]);
            $counter[$startofweekarray][$deptname] = (int) $endorsementdeptweek["Endorsement"]["endorseddepartment"];
            //$startofweekarray[] = $this->Common->getStartAndEndDate($endorsementdeptweek["Endorsement"]["weekdepartment"], $endorsementdeptweek["Endorsement"]["yeardepartment"]);
        }

//        //============to take date array as unique
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
//        pr($counter); exit;
        if (!empty($counter)) {
            $counter = $server_data;
            $counter = json_encode(array('counter' => $counter, 'date_array' => $converted_date_array));
        }
        $idData = json_encode($idData);
//        pr($counter);
//        pr($idData); exit;
//        pr($leaderboard); exit;
        $this->set(compact("organization_id", "endorsementbydeptweek", "counter", "leaderboard", "startofweekarray", "idData"));
        echo $this->render('/Elements/endorsementhistorybydept_web');
//        echo $this->Element("endorsementbyday_web", array("data" => $endorsementbyday));
        exit;
    }

    public function ndorsement_histoty_dept() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $organization_id = $this->request->data['organization_id'];

        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
        }


        $this->loadModel("OrgDepartment");
        $this->loadModel("Endorsement");




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




        $this->set(compact("organization_id", "leaderboard"));
        echo $this->render('/Elements/endorsementbydept_web');
//        echo $this->Element("endorsementbyday_web", array("data" => $endorsementbyday));
        exit;
    }

    public function ndorsement_histoty_title() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $organization_id = $this->request->data['organization_id'];

        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
        }


        $this->loadModel("Endorsement");

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


        $this->set(compact("organization_id", "detailedjobtitlechart"));
        echo $this->render('/Elements/endorsementspiecharts_web');
//        echo $this->Element("endorsementbyday_web", array("data" => $endorsementbyday));
        exit;
    }

    public function ndorsement_histoty_sub_org() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $organization_id = $this->request->data['organization_id'];

        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
        }


        $this->loadModel("Endorsement");

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
        $detailedjobtitlechart = $detailedentitychart;
        $this->set(compact("organization_id", "detailedjobtitlechart"));

        echo $this->render('/Elements/endorsementspiecharts_web');
//        echo $this->Element("endorsementbyday_web", array("data" => $endorsementbyday));
        exit;
    }

    // Function to get all the dates in given range
    function getDatesFromRange($start, $end, $format = 'Y-m-d') {

        // Declare an empty array
        $array = array();

        // Variable that store the date interval
        // of period 1 day
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        // Use loop to store date into array
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        // Return the array elements
        return $array;
    }

    public function ndorsement_history_day_weeks() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $organization_id = $this->request->data['organization_id'];
//        pr($this->request->data); ///exit;
        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
        }

        $conditionsendorsementbyday["organization_id"] = $organization_id;
        $conditionsendorsementbyday['type !='] = array('guest', 'daisy');



        if ($startdate != "" and $enddate != "") {
            array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
        } else {
            $conditionsendorsementbyday[] = 'MONTH(created)=MONTH(CURRENT_DATE())';
            $conditionsendorsementbyday[] = 'YEAR(created) =YEAR(CURRENT_DATE())';
        }
//        pr($conditionsendorsementbyday);
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues', 'EndorseHashtag')));
        $endorsementbyday = $this->Endorsement->find("all", array("conditions" => $conditionsendorsementbyday, "group" => "date(Endorsement.created)", "fields" => array("count(*) as cnt", "date(created) as cdate")));
//        echo $this->Endorsement->getLastQuery(); exit;
//        pr($endorsementbyday); exit;
//        $endorsementbyday = $this->Endorsement->find("all", array("conditions" => $conditionsendorsementbyday, "group" => "date(Endorsement.created)", "fields" => array("count(*) as cnt", "date(created) as cdate")));
        //pr($endorsementbyday); exit;


        /* nDorserment History By Week Query */
        $conditionsendorsementbyWeek = array();
        $conditionsendorsementbyWeek["organization_id"] = $organization_id;
        $conditionsendorsementbyWeek['type !='] = array('guest', 'daisy');

        if ($startdate != "" and $enddate != "") {
            array_push($conditionsendorsementbyWeek, "date(created) between '$startdate' and '$enddate'");
        } else {
            $conditionsendorsementbyWeek[] = 'MONTH(created)=MONTH(CURRENT_DATE())';
            $conditionsendorsementbyWeek[] = 'YEAR(created) =YEAR(CURRENT_DATE())';
        }



        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues', 'EndorseHashtag')));
        $endorsementbyWeek = $this->Endorsement->find("all", array("conditions" => $conditionsendorsementbyWeek, "group" => "date_format(created,'%U')", "order" => "created", "fields" => array("count(*) as cnt", "date(created) as cdate")));
//        echo $this->Endorsement->getLastQuery();
//        pr($endorsementbyWeek);
//        exit;

        /** Code to get monthly active users data *
         * added by Babulal prasad
         * @30-march-2021
         */
        $startdate = $enddate = '';

//        pr($this->request->data);
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
        } else {
            $now = new DateTime();
            $back = $now->sub(DateInterval::createFromDateString('29 days'));
            $startdate = $back->format('Y-m-d');
            $enddate = date('Y-m-d');
        }

        $until = new DateTime();
        if ($organization_id == 0) {//426 //415
            $interval = new DateInterval('P1M'); //3 months
        } else {
            $interval = new DateInterval('P12M'); //12 months
//            $interval = new DateInterval('P1D'); //12 months
        }

        $from = $until->sub($interval);

        $last12Mnth = $from->format('Y-m-t');

//        echo "startdate = " . $startdate;
//        echo "<br/>enddate = " . $enddate;
        // Function call with passing the start date and end date
        $DailyDateArray = $this->getDatesFromRange($startdate, $enddate);
//        var_dump($Date);


        $acitveUserConditions = array();


        $acitveUserConditions['(CAST(ApiSession.created AS DATE) BETWEEN ? AND ? )'] = array($startdate, $enddate);

//        $acitveUserConditions["ApiSession.created >"] = $last12Mnth;
        $acitveUserConditions["UserOrganization.organization_id"] = $organization_id;
//        $acitveUserConditions["DefaultOrg.organization_id"] = $organization_id;
//        $acitveUserConditions["DefaultOrg.status"] = 1;

        $params['conditions'] = $acitveUserConditions;
//        $params['joins'] = array(
//            array(
//                'table' => 'user_organizations',
//                'alias' => 'UserOrganization',
//                'type' => 'RIGHT',
//                'conditions' => array(
//                    'UserOrganization.user_id = ApiSession.user_id',
//                    'UserOrganization.organization_id = ' . $organization_id,
//                )
//            )
//        );
//        $params['fields'] = array('ApiSession.*');

        $params['group'] = array('DATE(ApiSession.created)');
        $params['fields'] = array('COUNT(DISTINCT ApiSession.user_id) AS login_counts', 'DATE(ApiSession.created) AS login_date');
//        $params['joins'] = array(
//            array(
//                'table' => 'api_sessions',
//                'alias' => 'ApiSession',
//                'type' => 'LEFT',
//                'conditions' => array(
//                    'ApiSession.user_id = UserOrganization.user_id1',
//                )
//            ) /* ,
//                  array(
//                  'table' => 'default_orgs',
//                  'alias' => 'DefaultOrg',
//                  'type' => 'LEFT',
//                  'conditions' => array(
//                  'DefaultOrg.user_id = UserOrganization.user_id',
//                  )
//                  ) */
//        );
        $params['joins'] = array(
            array(
                'table' => 'user_organizations',
                'alias' => 'UserOrganization',
                'type' => 'LEFT',
                'conditions' => array(
                    'UserOrganization.user_id = ApiSession.user_id',
                )
            ) /* ,
                  array(
                  'table' => 'default_orgs',
                  'alias' => 'DefaultOrg',
                  'type' => 'LEFT',
                  'conditions' => array(
                  'DefaultOrg.user_id = UserOrganization.user_id',
                  )
                  ) */
        );
//        pr($params);
//        exit;
//        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies', 'EndorseHashtag')));

//        $activeUserData = $this->UserOrganization->find("all", $params);
        $activeUserData = $this->ApiSession->find("all", $params);
//        echo $this->UserOrganization->getLastQuery();
//        exit;
//        pr($activeUserData);
//        exit;
        $dailyAllData = array();
        foreach ($activeUserData as $index => $data) {

//            pr($data); exit;
            $count = $data[0]['login_counts'];
            $date = $data[0]['login_date'];
            $month = date("M-y", strtotime($data[0]['login_counts']));
            $dailyAllData[$date] = (int) $count;
        }
//        pr($dailyAllData); exit;
        $activeMonthwiseCounts = array();
        $orgActiveUserCountArray = array();
//        pr($monthwiseallData);
//        pr($DailyDateArray); exit;
        if (!empty($DailyDateArray)) {
            foreach ($DailyDateArray as $i => $daily_date) {
                if (!isset($dailyAllData[$daily_date])) {
                    $dailyAllData[$daily_date] = 0;
                }
            }
        }
        ksort($dailyAllData);
        $totalActiveUsers = array();
        foreach ($dailyAllData as $dateIndex => $counts) {
            $totalActiveUsers[] = $counts;
        }
//        $totalActiveUsers  = $dailyAllData;
//        pr($activeMonthwiseCounts);
//        exit;
        //Calculation for dynamic months range on date range selection
//        $date1 = new DateTime($enddate);
//        $date2 = new DateTime($startdate);
//        $diff = $date1->diff($date2);
//        $monthsDiff = (($diff->format('%y') * 12) + $diff->format('%m'));
//
//        if ($startdate != "" and $enddate != "") {
//            for ($i = 0; $i <= $monthsDiff; $i++) {
//                $months[] = date("M-y", strtotime($enddate . " -$i months"));
//            }
//            $months = array_reverse($months);
//        } else {
//            for ($i = 0; $i <= 11; $i++) {
//                $months[] = date("M-y", strtotime(date('Y-m-01') . " -$i months"));
//            }
//            $months = array_reverse($months);
//        }
//        foreach ($months as $index => $monthID) {
//            //Monthwise Org Endorsement Count Data
//            if (isset($activeMonthwiseCounts[$monthID])) {
//                $orgActiveUserCountArray[$monthID] = count($activeMonthwiseCounts[$monthID]);
//            } else {
////                foreach ($corevaluesIDsArray as $indx => $cvID) {
//                $orgActiveUserCountArray[$monthID] = 0;
////                }
//            }
//        }
////        pr($orgActiveUserCountArray);
////        exit;
//        $monthsnew = json_encode($months);
//        $totalActiveUsers = array();
//        foreach ($orgActiveUserCountArray as $monthID => $totalCount) {
//            $totalActiveUsers[] = array($totalCount);
//        }



        $DailyDateArray = json_encode($DailyDateArray);

        $totalActiveUsers = json_encode($totalActiveUsers); //exit;
//        pr($totalActiveUsers);
        $this->set(compact("endorsementbyWeek", "endorsementbyday", "DailyDateArray", "totalActiveUsers"));
        echo $this->render('/Elements/leaderboard_barchart');
        exit;
    }

    public function ndorsement_history_leaderboard() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $organization_id = $this->request->data['organization_id'];

        $this->loadModel('OrgSubcenter');
        $this->loadModel('Endorsement');
        $this->loadModel('SubcenterDepartment');


        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
        }
//        echo $startdate; 
//        echo $enddate; 
//        exit;
        $orgDepartments = $this->Common->getorgdepartments($organization_id);

        //Getting all subcenters
        $subCenterData = $this->OrgSubcenter->find('all', array('conditions' => array('org_id' => $organization_id, 'status' => 1),
//            'order' => array('OrgSubcenter.short_name')
        ));
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
//        $params['order'] = 'OrgDeptName';
        $subcenterDepartment = $this->SubcenterDepartment->find("all", $params);
//        pr($subcenterDepartment); exit;
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
        //$params['fields'] = "User.id,concat(User.fname,' ',User.lname) as user_name /*,OrgSubcenter.short_name,OrgDepartment.name as dept_name,OrgJobTitle.title,UserOrganization.subcenter_id as subcenterID*/ ,UserOrganization.department_id as deptID";
        $params['fields'] = "User.id,concat(User.fname,' ',User.lname) as user_name,UserOrganization.department_id as deptID,UserOrganization.subcenter_id as subcenterID,UserOrganization.job_title_id as jobTitleID";
        $conditionarray["UserOrganization.organization_id"] = $organization_id;
        $conditionarray["UserOrganization.status"] = 1;
        $params['conditions'] = $conditionarray;
        $params['limit'] = 100;
        $params['joins'] = array(
            array(
                'table' => 'user_organizations',
                'alias' => 'UserOrganization',
                'type' => 'LEFT',
                'conditions' => array(
                    'UserOrganization.user_id = User.id'
                )
            ),
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
        );
//        $params['order'] = 'user_name';
        $userSubcenterData = $this->User->find("all", $params);
        $orgAllUserDataArray = $subcenterArray = $jobTitleArray = $departmentArray = array();

//        pr($userSubcenterData); exit;

        if (isset($userSubcenterData) && !empty($userSubcenterData)) {
            foreach ($userSubcenterData as $index => $uUScData) {
                $userId = $uUScData['User']['id'];
                $subCenterID = ($uUScData['UserOrganization']['subcenterID'] != '') ? $uUScData['UserOrganization']['subcenterID'] : 0;
                $deptID = ($uUScData['UserOrganization']['deptID'] != '') ? $uUScData['UserOrganization']['deptID'] : 0;
                $jobTitleID = ($uUScData['UserOrganization']['jobTitleID'] != '') ? $uUScData['UserOrganization']['jobTitleID'] : 0;
                $orgAllUserDataArray[$userId]['name'] = $uUScData[0]['user_name'];
//                $orgAllUserDataArray[$userId]['subcenter_name'] = $uUScData['OrgSubcenter']['short_name'];
//                $orgAllUserDataArray[$userId]['dept_name'] = $uUScData['OrgDepartment']['dept_name'];
//                $orgAllUserDataArray[$userId]['user_title'] = $uUScData['OrgJobTitle']['title'];
                $orgAllUserDataArray[$userId]['subcenter_name'] = "";
                $orgAllUserDataArray[$userId]['dept_name'] = "";
                $orgAllUserDataArray[$userId]['user_title'] = "";
                $orgAllUserDataArray[$userId]['dept_id'] = $deptID;
                $orgAllUserDataArray[$userId]['subcenter_id'] = $subCenterID;
                $orgAllUserDataArray[$userId]['jobtitle_id'] = $jobTitleID;
                $orgAllUserDataArray[$userId]['dept_id'] = $deptID;
                $subcenterArray[] = $subCenterID;
                $jobTitleArray[] = $jobTitleID;
                $departmentArray[] = $deptID;
            }
        }
        $this->loadModel('OrgDepartment');
        $this->loadModel('OrgJobTitle');

        $subcenterData = $this->OrgSubcenter->find('all', array('fields' => array('short_name', 'id'), 'conditions' => array('id' => $subcenterArray)));
        $deprtmentData = $this->OrgDepartment->find('all', array('fields' => array('name', 'id'), 'conditions' => array('id' => $departmentArray)));
        $jobTitleData = $this->OrgJobTitle->find('all', array('fields' => array('title', 'id'), 'conditions' => array('id' => $jobTitleArray)));

//        pr($subcenterData);
//        pr($deprtmentData);
//        pr($jobTitleData);
        $subcenterIdArray = $deptIDArray = $jobTitleIdArray = array();
        if (!empty($subcenterData)) {
            foreach ($subcenterData as $index => $scenterData) {
                $id = $scenterData['OrgSubcenter']['id'];
                $scname = $scenterData['OrgSubcenter']['short_name'];
                $subcenterIdArray[$id] = $scname;
            }
        }
        if (!empty($deprtmentData)) {
            foreach ($deprtmentData as $index => $deptData) {
                $id = $deptData['OrgDepartment']['id'];
                $deptname = $deptData['OrgDepartment']['name'];
                $deptIDArray[$id] = $deptname;
            }
        }
        if (!empty($jobTitleData)) {
            foreach ($jobTitleData as $index => $JTData) {
                $id = $JTData['OrgJobTitle']['id'];
                $jtname = $JTData['OrgJobTitle']['title'];
                $jobTitleIdArray[$id] = $jtname;
            }
        }

//        pr($subcenterIdArray);
//        pr($deptIDArray);
//        pr($jobTitleIdArray);
//        exit;
//        pr($orgAllUserDataArray);
//        exit;

        $fields['fields'] = array('endorsement_for', 'endorsed_id', 'endorser_id', 'subcenter_for', 'subcenter_by');
        $conditionsendorsementbyday["organization_id"] = $organization_id;
        $conditionsendorsementbyday['type !='] = array('guest', 'daisy');



        if ($startdate != "" and $enddate != "") {
            array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
        } else {
//            $conditionsendorsementbyday[] = 'MONTH(created)=MONTH(CURRENT_DATE())';
//            $conditionsendorsementbyday[] = 'YEAR(created) =YEAR(CURRENT_DATE())';
            $d = new DateTime('first day of this month');
            $startdate = $d->format('Y-m-d');
            $enddate = date('Y-m-d', time());
            array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
        }
//        pr($conditionsendorsementbyday);
//        exit;
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues', 'EndorseHashtag')));
        $endorsementLeaderboardData = $this->Endorsement->find("all", array('fields' => array('endorsement_for', 'endorsed_id', 'endorser_id', 'subcenter_for', 'subcenter_by'), "conditions" => $conditionsendorsementbyday /* , "group" => "date(Endorsement.created)" */));
//        echo $this->Endorsement->getLastQuery();
//        exit;
//        pr($endorsementLeaderboardData);
//        exit;

        /* LeaderBoard counts */
//        pr($endorsementLeaderboardData); exit;
        $subcenterIDarray = $subcenterNdorsementArray = $deptNdorsementCount = $userlisting = $usersNdorsementsCounts = array();
//        pr($endorsementLeaderboardData);
//        exit;


        if (!empty($endorsementLeaderboardData)) {


            foreach ($endorsementLeaderboardData as $index => $dataC) {
                if ($dataC['Endorsement']['endorsement_for'] == 'user') {
                    $receivedUserId = $dataC['Endorsement']['endorsed_id'];
                    $senderUserId = $dataC['Endorsement']['endorser_id'];
                    $userlisting[$receivedUserId] = $receivedUserId;
                    $userlisting[$senderUserId] = $senderUserId;
                }
            }

            //        pr($userlisting);
            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));
            $totalUserOfOrg = $this->UserOrganization->find('all', array(
                'fields' => array('UserOrganization.department_id,UserOrganization.user_id'),
                'conditions' => array('user_id' => $userlisting, 'UserOrganization.status' => 1, 'UserOrganization.organization_id' => $organization_id)));
//        echo $this->UserOrganization->getLastQuery();
//        exit;
            $userOrgArray = array();
            if (!empty($totalUserOfOrg)) {
                foreach ($totalUserOfOrg as $index => $userData) {
                    $deptID = $userData['UserOrganization']['department_id'];
                    $userID = $userData['UserOrganization']['user_id'];
                    $userOrgArray[$userID] = $deptID;
                }
            }



            foreach ($endorsementLeaderboardData as $index => $dataC) {


                /* CALCULATION FOR USER's nDorsement */
                if ($dataC['Endorsement']['endorsement_for'] == 'user') {
                    $receivedUserId = $dataC['Endorsement']['endorsed_id'];
                    $senderUserId = $dataC['Endorsement']['endorser_id'];

                    if (isset($userOrgArray[$senderUserId])) {
                        $deptIdSender = $userOrgArray[$senderUserId]; //nDorser
                    }

                    if (isset($userOrgArray[$receivedUserId])) {
                        $deptIdReceiver = $userOrgArray[$receivedUserId]; //nDorsed
                    }

                    if (isset($deptNdorsementCount[$deptIdSender]['sent'])) {
                        $deptNdorsementCount[$deptIdSender]['sent']++;
                    } else {
                        $deptNdorsementCount[$deptIdSender]['sent'] = 1;
                    }

                    if (isset($deptNdorsementCount[$deptIdReceiver]['received'])) {
                        $deptNdorsementCount[$deptIdReceiver]['received']++;
                    } else {
                        $deptNdorsementCount[$deptIdReceiver]['received'] = 1;
                    }


                    if (isset($usersNdorsementsCounts[$senderUserId]['sent'])) {
                        $usersNdorsementsCounts[$senderUserId]['sent']++;
                    } else {
                        $usersNdorsementsCounts[$senderUserId]['sent'] = 1;
                    }

                    if (isset($usersNdorsementsCounts[$receivedUserId]['received'])) {
                        $usersNdorsementsCounts[$receivedUserId]['received']++;
                    } else {
                        $usersNdorsementsCounts[$receivedUserId]['received'] = 1;
                    }
                }

                /* CALCULATION FOR SUBCENTER */
                //nDorsements received by subcenters 
                $dataC['Endorsement']['subcenter_for'];

                if ($dataC['Endorsement']['subcenter_for'] != '' && $dataC['Endorsement']['subcenter_for'] != 0) {
                    if (isset($subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'])) {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'] = $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'] + 1;
                    } else {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'] = 1;
                    }
                }

                //nDorsements Given by Subcenters
                $dataC['Endorsement']['subcenter_by'];
                if ($dataC['Endorsement']['subcenter_by'] != '' && $dataC['Endorsement']['subcenter_by'] != 0) {
                    if (isset($subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'])) {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'] = $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'] + 1;
                    } else {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'] = 1;
                    }
                }
            }
        }

//        pr($subcenterNdorsementArray);
//        exit;

        $this->set(compact('jobTitleIdArray', 'deptIDArray', 'subcenterIdArray', 'orgDepartments', 'subCenterArray', 'deptNdorsementCount', 'subcenterDepartmentArray', 'orgAllUserDataArray', 'authUser', 'companydetail', 'organization_id', 'datesarray', 'subcenterNdorsementArray', 'usersNdorsementsCounts'));



        echo $this->render('/Elements/leaderboard_grid');
//        echo $this->render('/Elements/endorsementbyday_web');
        exit;
    }

    public function ndorsement_history_leaderboard_loadmore() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $organization_id = $this->request->data['organization_id'];

        $this->loadModel('OrgSubcenter');
        $this->loadModel('Endorsement');
        $this->loadModel('SubcenterDepartment');
        $totalrecords = $this->request->data["totalrecords"];
        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
        }

        //Getting all Users and subcenters and Departments
        $params = $conditionarray = array();
//        $params['fields'] = "User.id,concat(User.fname,' ',User.lname) as user_name,OrgSubcenter.short_name,OrgDepartment.name as dept_name,OrgJobTitle.title,UserOrganization.subcenter_id as subcenterID,UserOrganization.department_id as deptID";
        $params['fields'] = "User.id,concat(User.fname,' ',User.lname) as user_name,UserOrganization.department_id as deptID,UserOrganization.subcenter_id as subcenterID,UserOrganization.job_title_id as jobTitleID";
        $conditionarray["UserOrganization.organization_id"] = $organization_id;
        $conditionarray["UserOrganization.status"] = 1;
        $params['conditions'] = $conditionarray;
        $params['limit'] = 50;
        $params['offset'] = $totalrecords;

        $params['joins'] = array(
            array(
                'table' => 'user_organizations',
                'alias' => 'UserOrganization',
                'type' => 'LEFT',
                'conditions' => array(
                    'UserOrganization.user_id = User.id'
                )
            ),
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
        );
        $params['order'] = 'user_name';
        $userSubcenterData = $this->User->find("all", $params);
        $orgAllUserDataArray = $subcenterArray = $jobTitleArray = $departmentArray = array();
//        pr($userSubcenterData); exit;
        if (isset($userSubcenterData) && !empty($userSubcenterData)) {
            foreach ($userSubcenterData as $index => $uUScData) {
                $userId = $uUScData['User']['id'];
                $subCenterID = ($uUScData['UserOrganization']['subcenterID'] != '') ? $uUScData['UserOrganization']['subcenterID'] : 0;
                $deptID = ($uUScData['UserOrganization']['deptID'] != '') ? $uUScData['UserOrganization']['deptID'] : 0;
                $jobTitleID = ($uUScData['UserOrganization']['jobTitleID'] != '') ? $uUScData['UserOrganization']['jobTitleID'] : 0;
                $orgAllUserDataArray[$userId]['name'] = $uUScData[0]['user_name'];
//                $orgAllUserDataArray[$userId]['subcenter_name'] = $uUScData['OrgSubcenter']['short_name'];
//                $orgAllUserDataArray[$userId]['dept_name'] = $uUScData['OrgDepartment']['dept_name'];
//                $orgAllUserDataArray[$userId]['user_title'] = $uUScData['OrgJobTitle']['title'];
                $orgAllUserDataArray[$userId]['subcenter_name'] = "";
                $orgAllUserDataArray[$userId]['dept_name'] = "";
                $orgAllUserDataArray[$userId]['user_title'] = "";
                $orgAllUserDataArray[$userId]['dept_id'] = $deptID;
                $orgAllUserDataArray[$userId]['subcenter_id'] = $subCenterID;
                $orgAllUserDataArray[$userId]['jobtitle_id'] = $jobTitleID;
                $orgAllUserDataArray[$userId]['dept_id'] = $deptID;
                $subcenterArray[] = $subCenterID;
                $jobTitleArray[] = $jobTitleID;
                $departmentArray[] = $deptID;
            }
        }
//        pr($orgAllUserDataArray); exit;

        $this->loadModel('OrgSubcenter');
        $this->loadModel('OrgDepartment');
        $this->loadModel('OrgJobTitle');

        $subcenterData = $this->OrgSubcenter->find('all', array('fields' => array('short_name', 'id'), 'conditions' => array('id' => $subcenterArray)));
        $deprtmentData = $this->OrgDepartment->find('all', array('fields' => array('name', 'id'), 'conditions' => array('id' => $departmentArray)));
        $jobTitleData = $this->OrgJobTitle->find('all', array('fields' => array('title', 'id'), 'conditions' => array('id' => $jobTitleArray)));

//        pr($subcenterData);
//        pr($deprtmentData);
//        pr($jobTitleData);
        $subcenterIdArray = $deptIDArray = $jobTitleIdArray = array();
        if (!empty($subcenterData)) {
            foreach ($subcenterData as $index => $scenterData) {
                $id = $scenterData['OrgSubcenter']['id'];
                $scname = $scenterData['OrgSubcenter']['short_name'];
                $subcenterIdArray[$id] = $scname;
            }
        }
        if (!empty($deprtmentData)) {
            foreach ($deprtmentData as $index => $deptData) {
                $id = $deptData['OrgDepartment']['id'];
                $deptname = $deptData['OrgDepartment']['name'];
                $deptIDArray[$id] = $deptname;
            }
        }
        if (!empty($jobTitleData)) {
            foreach ($jobTitleData as $index => $JTData) {
                $id = $JTData['OrgJobTitle']['id'];
                $jtname = $JTData['OrgJobTitle']['title'];
                $jobTitleIdArray[$id] = $jtname;
            }
        }

//        pr($subcenterIdArray);
//        pr($deptIDArray);
//        pr($jobTitleIdArray);
//        exit;
//        pr($orgAllUserDataArray);
//        exit;

        $fields['fields'] = array('endorsement_for', 'endorsed_id', 'endorser_id', 'subcenter_for', 'subcenter_by');
        $conditionsendorsementbyday["organization_id"] = $organization_id;
        $conditionsendorsementbyday['type !='] = array('guest', 'daisy');


        if ($startdate != "" and $enddate != "") {
            array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
        } else {
            $conditionsendorsementbyday[] = 'MONTH(created)=MONTH(CURRENT_DATE())';
            $conditionsendorsementbyday[] = 'YEAR(created) =YEAR(CURRENT_DATE())';
        }

        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues', 'EndorseHashtag')));
        $endorsementLeaderboardData = $this->Endorsement->find("all", array($fields, "conditions" => $conditionsendorsementbyday /* , "group" => "date(Endorsement.created)" */, "fields" => array("*")));
//        echo $this->Endorsement->getLastQuery();
//        exit;
//        pr($endorsementLeaderboardData); exit;

        /* LeaderBoard counts */
//        pr($endorsementLeaderboardData); exit;
        $subcenterIDarray = $subcenterNdorsementArray = $deptNdorsementCount = $userlisting = $usersNdorsementsCounts = array();
//        pr($endorsementLeaderboardData);
//        exit;


        if (!empty($endorsementLeaderboardData)) {


            foreach ($endorsementLeaderboardData as $index => $dataC) {
                if ($dataC['Endorsement']['endorsement_for'] == 'user') {
                    $receivedUserId = $dataC['Endorsement']['endorsed_id'];
                    $senderUserId = $dataC['Endorsement']['endorser_id'];
                    $userlisting[$receivedUserId] = $receivedUserId;
                    $userlisting[$senderUserId] = $senderUserId;
                }
            }

            //        pr($userlisting);
            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));
            $totalUserOfOrg = $this->UserOrganization->find('all', array(
                'fields' => array('UserOrganization.department_id,UserOrganization.user_id'),
                'conditions' => array('user_id' => $userlisting, 'UserOrganization.status' => 1, 'UserOrganization.organization_id' => $organization_id)));
//        echo $this->UserOrganization->getLastQuery();
//        exit;
            $userOrgArray = array();
            if (!empty($totalUserOfOrg)) {
                foreach ($totalUserOfOrg as $index => $userData) {
                    $deptID = $userData['UserOrganization']['department_id'];
                    $userID = $userData['UserOrganization']['user_id'];
                    $userOrgArray[$userID] = $deptID;
                }
            }

            foreach ($endorsementLeaderboardData as $index => $dataC) {


                /* CALCULATION FOR USER's nDorsement */
                if ($dataC['Endorsement']['endorsement_for'] == 'user') {
                    $receivedUserId = $dataC['Endorsement']['endorsed_id'];
                    $senderUserId = $dataC['Endorsement']['endorser_id'];

                    $deptIdSender = $userOrgArray[$senderUserId]; //nDorser
                    $deptIdReceiver = $userOrgArray[$receivedUserId]; //nDorsed

                    if (isset($usersNdorsementsCounts[$senderUserId]['sent'])) {
                        $usersNdorsementsCounts[$senderUserId]['sent']++;
                    } else {
                        $usersNdorsementsCounts[$senderUserId]['sent'] = 1;
                    }

                    if (isset($usersNdorsementsCounts[$receivedUserId]['received'])) {
                        $usersNdorsementsCounts[$receivedUserId]['received']++;
                    } else {
                        $usersNdorsementsCounts[$receivedUserId]['received'] = 1;
                    }
                }
            }
        }

//        pr($subcenterNdorsementArray);
//        exit;

        $this->set(compact('jobTitleIdArray', 'deptIDArray', 'subcenterIdArray', 'orgDepartments', 'orgAllUserDataArray', 'organization_id', 'datesarray', 'usersNdorsementsCounts'));



        echo $this->render('/Elements/orgsubcenter_user_report');
//        echo $this->render('/Elements/endorsementbyday_web');
        exit;
    }

    public function ndorsement_history_department() {
        $this->layout = "ajax_new";
        $this->autoRender = false;
        $organization_id = $this->request->data['organization_id'];

        $startdate = "";
        $enddate = "";
        if (!empty($this->request->data["startdate"]) && !empty($this->request->data["enddate"])) {
            $requestdata = $this->request->data;
            $startdate = $this->Common->dateConvertServer($requestdata["startdate"]);
            $enddate = $this->Common->dateConvertServer($requestdata["enddate"]);
        }

        $conditionsendorsementbyday["organization_id"] = $organization_id;
        $conditionsendorsementbyday['type !='] = 'guest';

        if ($startdate != "" and $enddate != "") {
            array_push($conditionsendorsementbyday, "date(created) between '$startdate' and '$enddate'");
        } else {
//            $conditionsendorsementbyday['MONTH(created)'] = 'MONTH(CURRENT_DATE())';
//            $conditionsendorsementbyday['YEAR(created)'] = 'YEAR(CURRENT_DATE())';
        }




        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', 'EndorseCoreValues', 'EndorseHashtag')));
        $endorsementLeaderboardData = $this->Endorsement->find("all", array("conditions" => $conditionsendorsementbyday, "group" => "date(Endorsement.created)", "fields" => array("*")));

//        pr($endorsementLeaderboardData);
//        exit;
        $usersNdorsementsCounts = $userlisting = array();

        if (!empty($endorsementLeaderboardData)) {
            foreach ($endorsementLeaderboardData as $index => $dataC) {


                /* CALCULATION FOR USER's nDorsement */

                if ($dataC['Endorsement']['endorsement_for'] == 'user') {
                    $receivedUserId = $dataC['Endorsement']['endorsed_id'];
                    $senderUserId = $dataC['Endorsement']['endorser_id'];
                    $userlisting[$receivedUserId] = $receivedUserId;
                    $userlisting[$senderUserId] = $senderUserId;
                    if (isset($usersNdorsementsCounts[$senderUserId]['sent'])) {
                        $usersNdorsementsCounts[$senderUserId]['sent']++;
                    } else {
                        $usersNdorsementsCounts[$senderUserId]['sent'] = 1;
                    }

                    if (isset($usersNdorsementsCounts[$receivedUserId]['received'])) {
                        $usersNdorsementsCounts[$receivedUserId]['received']++;
                    } else {
                        $usersNdorsementsCounts[$receivedUserId]['received'] = 1;
                    }
                }



                //nDorsements received by subcenters 
                $dataC['Endorsement']['subcenter_for'];

                if ($dataC['Endorsement']['subcenter_for'] != '' && $dataC['Endorsement']['subcenter_for'] != 0) {
                    if (isset($subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'])) {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'] = $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'] + 1;
                    } else {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_for']]['received'] = 1;
                    }
                }

                //nDorsements Given by Subcenters
                $dataC['Endorsement']['subcenter_by'];
                if ($dataC['Endorsement']['subcenter_by'] != '' && $dataC['Endorsement']['subcenter_by'] != 0) {
                    if (isset($subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'])) {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'] = $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'] + 1;
                    } else {
                        $subcenterNdorsementArray[$dataC['Endorsement']['subcenter_by']]['given'] = 1;
                    }
                }
            }
        }
//        pr($usersNdorsementsCounts); 
////        exit;
//        pr($subcenterNdorsementArray);
//        exit;

        $this->set(compact("endorsementbyday"));

        echo $this->render('/Elements/leaderboard_paichart');
//        echo $this->render('/Elements/endorsementbyday_web');
        exit;
    }

}
