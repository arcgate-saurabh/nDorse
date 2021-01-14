<?php

class DaisyController extends AppController {

    public $components = array("Apicalls", "Session");
    var $uses = array("User", "Organization", "OrgCoreValue", "Auth");

    public function beforeFilter() {
        $this->theme = 'Guest';
        $this->layout = "guest";
        $this->Auth->allow("index", "endorse", "thanks", "searchInOrgGuest", "searchInOrgGuestDept");
    }

    public function index() {
        $this->Session->delete('Guest');
        $encryptID = $this->request->params['id'];

        if ($encryptID === 'MjM=') { //UMCNO old to new redirection
            $this->redirect(array('controller' => 'daisy', 'action' => 'index', 'id' => 'NDMw'));
        } elseif ($encryptID === 'MjY1') { //CHNOLA old to new redirection
            $this->redirect(array('controller' => 'daisy', 'action' => 'index', 'id' => 'NDI5'));
        }

        $orgId = base64_decode($encryptID);
        $orgDetail = $this->Organization->findById($orgId);
        $this->set('orgDetail', $orgDetail);
        $this->set('encryptID', $encryptID);
    }

    public function endorse() {
        $encryptID = $this->request->params['id'];
        $orgId = base64_decode($encryptID);
        $orgDetail = $this->Organization->findById($orgId);
//        $orgCoreValues = $this->Common->getOrgGuestCoreValuesAndCode($orgId);
        $orgCoreValues = $this->Common->getOrgDAISYCoreValuesAndCode($orgId);
        $DAISYAwards = Configure::read("DAISY_Awards");

        if ($this->request->is('post')) {

//            pr($this->request->data); 
//            exit;

            if (isset($this->request->data['fname']) && trim($this->request->data['fname']) != '') {
                $this->Session->write('Guest.fname', trim($this->request->data['fname']));
            }
            if (isset($this->request->data['lname']) && trim($this->request->data['lname']) != '') {
                $this->Session->write('Guest.lname', trim($this->request->data['lname']));
            }
            if (isset($this->request->data['email']) && trim($this->request->data['email']) != '') {
                $this->Session->write('Guest.email', trim($this->request->data['email']));
            }
            if (isset($this->request->data['mobile']) && trim($this->request->data['mobile']) != '') {
                $this->Session->write('Guest.mobile', trim($this->request->data['mobile']));
            }
            if (isset($this->request->data['nominator_title']) && trim($this->request->data['nominator_title']) != '') {
                $this->Session->write('Guest.nominator_title', trim($this->request->data['nominator_title']));
            }
        }
        //  else {
        //     $this->redirect(array('controller' => 'daisy', 'action' => 'index', 'id' => $encryptID));
        // }
        //$defaultCoreValues = $this->Common->getDefaultCoreValuesWeb();

        $coreValues = array();
        if (!empty($defaultCoreValues)) {
            foreach ($defaultCoreValues as $corevaluename) {
                $coreValues[] = array('name' => $corevaluename, 'colorcode' => '#FFFFFFF');
            }
        }

        if (!empty($orgCoreValues)) {
            foreach ($orgCoreValues as $CoreValueID => $corevalueDAta) {
                $coreValues[$CoreValueID] = $corevalueDAta;
            }
        }

        $this->loadModel('DaisySubcenter');
        $DaisySubcenters = $this->DaisySubcenter->find("all", array("conditions" => array("org_id" => $orgId, "status" => 1)));
//        pr($DaisySubcenters);
//        exit;    

        $this->set('DaisySubcenters', $DaisySubcenters);
        $this->set('orgDetail', $orgDetail);
        $this->set('orgId', $orgId);
        $this->set('encryptID', $encryptID);
        $this->set('coreValues', $coreValues);
        $this->set('DAISYAwards', $DAISYAwards);
    }

    public function thanks() {
        $encryptID = $this->request->params['id'];
        $orgId = base64_decode($encryptID);
        if ($this->request->is('post')) {

//            pr($this->request->data);
//            exit;
//            
            if (isset($this->request->data['default_user_checked']) && $this->request->data['default_user_checked'] == 'on') {
                $endorse_id = $this->request->data['default_user_id'];
                $endorsedType = 'user';
            } else {

                $endorsedType = isset($this->request->data['selected_endorse_type']) ? $this->request->data['selected_endorse_type'] : "";

                $nomineeSubcenterId = isset($this->request->data['nominee_subcenter']) ? $this->request->data['nominee_subcenter'] : "0";
                $daisyNomineeName = "";
                $daisy_custom_nominee = 0;
                
                if (isset($this->request->data['selected_endorse_id']) && $this->request->data['selected_endorse_id'] != '') {
                    $endorse_id = $this->request->data['selected_endorse_id'];
                } else {
                    //Create Guest User 
                    $UserData['email'] = '';
                    $UserData['username'] = '';
                    $UserData['fname'] = isset($this->request->data['endorse']['firstname']) ? $this->request->data['endorse']['firstname'] : '';
                    $UserData['lname'] = isset($this->request->data['endorse']['lastName']) ? $this->request->data['endorse']['lastName'] : '';
                    $UserData['mobile'] = '';
                    $UserData['source'] = 'daisy';
                    $UserData['status'] = '0';
                    $UserData['role'] = '5';
                    $UserData['password'] = 'aba2d5949a122c89cbfbd676ab814333d2615df5'; //12345678 Static password
                    $guestUser = $this->User->save($UserData);
                    $endorse_id = $this->User->id;
                    $daisyNomineeName = $UserData['fname'] . " " . $UserData['lname'];
                    $daisy_custom_nominee = 1;
                }
            }

            $endorsedName = isset($this->request->data['selected_endorse_name']) ? $this->request->data['selected_endorse_name'] : "";

            if (!isset($_SESSION['Guest'])) {
                $this->redirect(array('controller' => 'guest', 'action' => 'index', 'id' => $encryptID));
            }

            //-- Save guest endorsement through API --
            $postData = array();
            $postData['core_values'] = array();
            $postData['type'] = 'daisy'; //Endorsement type
            $postData['nominee_subcenter_id'] = $nomineeSubcenterId; //In case DAISY nominations
		//pr($postData); exit;
            $postData['award_type'] = isset($this->request->data['award_type']) ? $this->request->data['award_type'] : 0;
            $postData['department_name'] = isset($this->request->data['endorse']['department']) ? $this->request->data['endorse']['department'] : '';
            $deptID = $postData['department_id'] = isset($this->request->data['selected_dept_id']) ? $this->request->data['selected_dept_id'] : 0;
            $postData['org_id'] = $orgId;
            $postData['message'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
            $postData['daisy_nominee_name'] = $daisyNomineeName;
            $postData['daisy_custom_nominee'] = $daisy_custom_nominee;
            
            $daisy_custom_dept = 0;
            if ($deptID == 0 || $deptID == '') {
                $daisy_custom_dept = 1;
            }
            $postData['daisy_custom_dept'] = $daisy_custom_dept;
            
//            pr($postData); exit;
            if (isset($this->request->data['core_value']) && !empty($this->request->data['core_value'])) {
                $postData['core_values'] = implode(",", $this->request->data['core_value']);
            }


            if (isset($this->request->data['emojis'])) {
                $postData['emojis'] = implode(",", $this->request->data['emojis']);
            }
            $endorseList = array();

            $endorseList[] = array("for" => 'user', "id" => $endorse_id);

            $postData['endorse_list'] = json_encode($endorseList);

            //award_type
            //User Data 
            $postData['User'] = $_SESSION['Guest'];
//            pr($postData);
//            exit;
            $response = $this->Apicalls->curlpost("daisyEndorse.json", $postData);
//            pr($response); exit;
            $this->set('endorsedName', $endorsedName);
        } else {
            $this->redirect(array('controller' => 'guest', 'action' => 'index', 'id' => $encryptID));
        }

        $orgDetail = $this->Organization->findById($orgId);

        $this->set('orgDetail', $orgDetail);
        $this->set('encryptID', $encryptID);
    }

    public function searchInOrgGuest() {
        //pr($this->request->data);die;
        $this->layout = "ajax";

        $this->set("endorsementLimit", $this->request->data['limit']);

        $postData = array();
        $postData['keyword'] = $this->request->data['keyword'];
        $postData['limit'] = $this->request->data['limit'];
        $postData['orgId'] = $this->request->data['orgId'];
        $postData['searchSelf'] = false;

        //$response = $this->Apicalls->curlpost("searchInOrganizationGuest.json", $postData);
        $response = $this->Apicalls->curlpost("searchInOrganizationDaisy.json", $postData);
        //pr($response); exit;
        $response = json_decode($response);
        $response = $response->result;

        if (isset($response->data->users) && count($response->data->users) > 0) {
            $this->set("searchResult", $response->data);
            if (isset($this->request->data['endorseSelected'])) {
                $this->set('endorseSelected', $this->request->data['endorseSelected']);
            }
        } else {
            exit;
        }
//        die;
    }

    public function searchInOrgGuestDept() {
//        pr($this->request->data);
//        die;
        $this->layout = "ajax";

        $this->set("endorsementLimit", $this->request->data['limit']);
        $this->loadModel('OrgDepartment');
        $postData = array();
        $keyWord = $this->request->data['keyword'];
        $postData['limit'] = $this->request->data['limit'];
        $orgId = $this->request->data['orgId'];
        $postData['searchSelf'] = false;


        $orgDetail = $this->OrgDepartment->find('all', array('conditions' => array('name LIKE' => '%' . $keyWord . '%', 'organization_id' => $orgId, 'status' => 1)));
        $orgDeptArray = array();
        if (!empty($orgDetail)) {
            foreach ($orgDetail as $index => $orgDeptData) {
                $orgDeptArray[$orgDeptData['OrgDepartment']['id']]['id'] = $orgDeptData['OrgDepartment']['id'];
                $orgDeptArray[$orgDeptData['OrgDepartment']['id']]['name'] = $orgDeptData['OrgDepartment']['name'];
            }
        }
//        pr($orgDeptArray);exit;
//        $response = array(
//            'result' => array("status" => true
//                , "msg" => "Search results", "data" => $orgDeptArray),
//            '_serialize' => array('result')
//        );
//        echo $this->OrgDepartment->getLastQuery();
//        die;
//        pr($orgDetail);
//        exit;
//        $response = json_decode($response);
//        $response = $response['result'];

        if (isset($orgDeptArray) && count($orgDeptArray) > 0) {
            $this->set("searchResult", $orgDeptArray);
        } else {
            exit;
        }
//        die;
    }

}

?>
