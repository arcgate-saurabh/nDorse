<?php

class GuestController extends AppController {

    public $components = array("Apicalls", "Session");
    var $uses = array("User", "Organization", "OrgCoreValue", "Auth");

    public function beforeFilter() {
        $this->theme = 'Guest';
        $this->layout = "guest";
        $this->Auth->allow("index", "endorse", "thanks", "searchInOrgGuest");
    }

    public function index() {
        $this->Session->delete('Guest');
        $encryptID = $this->request->params['id'];
        $orgId = base64_decode($encryptID);
        $orgDetail = $this->Organization->findById($orgId);
        $this->set('orgDetail', $orgDetail);
        $this->set('encryptID', $encryptID);
    }

    public function endorse() {
        $encryptID = $this->request->params['id'];
        $orgId = base64_decode($encryptID);
        $orgDetail = $this->Organization->findById($orgId);
        $orgCoreValues = $this->Common->getOrgGuestCoreValuesAndCode($orgId);

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
        } else {
            $this->redirect(array('controller' => 'guest', 'action' => 'index', 'id' => $encryptID));
        }
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

        $this->set('orgDetail', $orgDetail);
        $this->set('orgId', $orgId);
        $this->set('encryptID', $encryptID);
        $this->set('coreValues', $coreValues);
    }

    public function thanks() {
        $encryptID = $this->request->params['id'];
        $orgId = base64_decode($encryptID);
        if ($this->request->is('post')) {

//            pr($this->$endorsedTyperequest->data); exit;
//                        pr($this->request->data); 
//            exit;
            
            if(isset($this->request->data['default_user_checked']) && $this->request->data['default_user_checked'] == 'on'){
                $endorse_id = $this->request->data['default_user_id'];
                $endorsedType = 'user';
            }else{
                $endorse_id =  $this->request->data['selected_endorse_id'];
                $endorsedType = isset($this->request->data['selected_endorse_type']) ? $this->request->data['selected_endorse_type'] : "";
            }
            
            $endorsedName = isset($this->request->data['selected_endorse_name']) ? $this->request->data['selected_endorse_name'] : "";

            if (!isset($_SESSION['Guest'])) {
                $this->redirect(array('controller' => 'guest', 'action' => 'index', 'id' => $encryptID));
            }

            //-- Save guest endorsement through API --
            $postData = array();
            $postData['core_values'] = array();
            $postData['type'] = 'guest'; //Endorsement type
            
            $postData['org_id'] = $orgId;
            $postData['message'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
            if(isset($this->request->data['core_value']) && !empty($this->request->data['core_value'])){
                $postData['core_values'] = implode(",", $this->request->data['core_value']);
            }
            
            
            if (isset($this->request->data['emojis'])) {
                $postData['emojis'] = implode(",", $this->request->data['emojis']);
            }
            $endorseList = array();
            
            $endorseList[] = array("for" => $endorsedType, "id" => $endorse_id);
            
            $postData['endorse_list'] = json_encode($endorseList);

            //User Data 
            $postData['User'] = $_SESSION['Guest'];
//            pr($postData); //exit;
            $response = $this->Apicalls->curlpost("guestEndorse.json", $postData);
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

        $response = $this->Apicalls->curlpost("searchInOrganizationGuest.json", $postData);
        $response = json_decode($response);
        $response = $response->result;
        //pr($response);die;
        $this->set("searchResult", $response->data);
        if (isset($this->request->data['endorseSelected'])) {
            $this->set('endorseSelected', $this->request->data['endorseSelected']);
        }
    }

}
?>
