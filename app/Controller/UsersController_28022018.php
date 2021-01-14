<?php

class UsersController extends AppController {

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $components = array('RequestHandler', "Auth", "Common", "Session");
    var $uses = array("User", "Department", "Organization", "UserOrganization", "CoreValues", "OrgCoreValue", "Country", "State", "Entity", "OrgDepartment", "OrgJobTitle", "GlobalSetting", "Announcement", "Endorsement");
    public $helpers = array("Html", "Form", "Session", "Tinymce");

    public function beforeFilter() {
        parent::beforeFilter();
        // $this->Auth->allow('login');
        $this->Auth->allow('register', 'login', 'logout', 'forgot', 'createclient', 'setImage', 'deleteimage', 'usersfaq', 'test', 'setOrgImage', 'setorgcpimage', 'deleteorgimage', "forgotPassword", "setPassword", 'unsubscribe');
    }

    public function index() {
        $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
        $loggeduser = $this->Auth->User();
        $logged_in_user_role = $this->Auth->user('role');
        //if ($this->Auth->User('role') != 1 && $this->Auth->User('role') != 2 && (!empty($loggeduser))) {
        //    $this->Auth->logout();
        //    $this->redirect(array('action' => 'login'));
        //} else
        if ($this->Auth->User('role') > 1) {
            $this->redirect(array('controller' => 'site', 'action' => 'index'));
        } else {
            $role = $this->Auth->User('role');
            if ($role == 2) {
                $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
            } else {
                $userdata = $this->User->find('all', array('limit' => 10, 'order' => 'User.id DESC', 'conditions' => array('User.role' => '2', "User.status" => array(0, 1))));
                //===========to fcount number of organizations for users
                $orgsandusers = $this->Common->getorgandusers($userdata);
                $totaluserrecords = $this->User->find("count", array("conditions" => array('User.role' => '2', "User.status" => array(0, 1))));
                $nooforg = $orgsandusers["nooforgs"];
                $noofusers = $orgsandusers["noofusers"];
                $this->set('userdata', $userdata);
                $this->set('authUser', $this->Auth->user());
                $this->set(compact("nooforg", "noofusers", "totaluserrecords"));
            }
        }
    }

    /** login page * */
    public function login() {
        $errormsg = "";
        $username = "";

        $loggedinUser = $this->Auth->user();
//        pr($loggedinUser); exit;
        if (!empty($loggedinUser) && isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'admin') {

//            if (isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'admin') {
            if ($this->Session->check('Auth.User.role') != "1") {

                $userorgdata = $this->UserOrganization->find("all", array("conditions" => array("user_id" => $this->Auth->user("id"), "user_role" => 2, 'UserOrganization.status' => 1)));

                $checkorgids = array();
                foreach ($userorgdata as $uservalorg) {

                    $checkorgids[] = $uservalorg["UserOrganization"]["organization_id"];
                }

                if (!empty($checkorgids)) {
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Auth->logout();
                    $this->redirect(array('action' => 'login'));
                }
            } else {
                $this->redirect(array('action' => 'index'));
            }
//            }
        } else if ($this->request->is('post')) {

            if ($this->Session->check('Auth.User')) {
//                $this->Cookie->delete("remember_me_endorse_cookie");
                $this->Auth->logout();
            }

            if (isset($this->request->data['User']['rememberme']) && $this->request->data['User']['rememberme'] == 1) {
                $this->Cookie->write("remember_me_cookie", $this->request->data["User"], true, "1 week");
            } else {
                $this->Cookie->delete("remember_me_cookie");
            }
            //$this->request->data['User']['username'] = $this->request->data['User']['email'];
            $this->User->setValidation('login');
            $this->User->set($this->request->data);

            if ($this->User->validates()) {
                if ($this->Auth->login()) {
//                     echo $this->Auth->User("role"); exit;
                    if ($this->Auth->User("role") > 2 || $this->Auth->User("role") == 6) {

                        if ($this->Auth->User("role") == 6) {
                            $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id', 'organizations.status !=2'))), "conditions" => array("user_id" => $this->Auth->user("id"), "user_role" => 6, 'UserOrganization.status' => 1)));
                        } else {
                            $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id', 'organizations.status !=2'))), "conditions" => array("user_id" => $this->Auth->user("id"), "user_role" => 2, 'UserOrganization.status' => 1)));
                        }
                        //echo $this->UserOrganization->getLastQuery();
                        //pr($userorgdata); exit;
                        $checkorgids = array();
                        foreach ($userorgdata as $uservalorg) {

                            $checkorgids[] = $uservalorg["UserOrganization"]["organization_id"];
                        }

                        if (empty($checkorgids)) {

                            $this->Auth->logout();
                            $this->redirect(array('action' => 'login'));
                        }
                    }
                    //$userId = $this->Auth->user('id');
                    $this->User->id = $userId = $this->Auth->user('id');
                    $this->User->saveField("last_app_used", date("Y-m-d h:i:s"), false);
                    //=============generating token for orgowners
                    // if ($this->Auth->User("role") == 2) {
                    if ($this->Auth->User("role") > 1) {
                        $token = $this->generateToken($userId);
                        $this->Session->write('Auth.User.token', $token);
                    }

                    $this->Session->write('Auth.User.portal', 'admin');
                    //set last login typ[e cookie
                    $this->Cookie->write("portal_cookie", "admin", true, "1 week");

                    $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
                    // $this->redirect($this->Auth->redirectUrl());
                } else {
                    $username = $this->request->data['User']['email'];
                    $errormsg = 'Invalid email or password';
                }
            } else {
                $username = $this->request->data['User']['email'];
                $errors = $this->User->validationErrors;
                foreach ($errors as $error) {
//                    $errormsg .= $error[0] . "<br/>";
                }
            }
        } else if ($this->Cookie->read("remember_me_cookie")) {
            $remembermecookie = $this->Cookie->read("remember_me_cookie");
//            $this->request->data = array('User' =>
//                array('email' => $remembermecookie["email"], 'password' => $remembermecookie["password"]));
            $this->request->data['User'] = $remembermecookie;
//            if ($this->Auth->login()) {
//                $this->request->data['User'] = $remembermecookie;
//                //echo $userId = $this->Auth->user('id');
//                //$this->User->id = $userId = $this->Auth->user('id');
//                //$this->User->saveField("last_app_used", date("Y-m-d h:i:s"), false);
//                //$this->User->saveField("last_app_used", date("Y-m-d h:i:s"), false);
//                //=============generating token for orgowners
////                if ($this->Auth->User("role") == 2) {
////                    $token = $this->generateToken($userId);
////                    $this->Session->write('Auth.User.token', $token);
////                }
//
//                $this->Session->write('Auth.User.portal', 'admin');
//                //set last login typ[e cookie
//                $this->Cookie->write("portal_cookie", "admin", true, "1 week");
//
//                //$this->redirect($this->Auth->redirectUrl());
//            }
        }
        $this->set('title_for_layout', "nDorse");
        $this->set('errormsg', $errormsg);
        $this->set('description', 'nDorse - Administrator portal login page');
        $this->set('errormsg', $errormsg);

        $this->set('jsIncludes', array('loginCommon'));
        //$this->set('username', $username);
    }

    public function editorg($id = null) {
        $errormsg = "";
        $this->set('jsIncludes', array('createorgclient'));
        $listCountries = $this->Country->find('list', array('fields' => array('Country.id', 'Country.name')));
        $listState = array('0' => 'Select State');
        $country_id = 0;
        if ($this->Session->check('Auth.User.role') != "1" || $this->Session->check('Auth.User.role') != "2") {
            $this->Auth->logout();
            $this->redirect(array('action' => 'login'));
        } else {
            $authUser = $this->Auth->User();
            $result = $this->Common->checkorgid($id);
            if ($result == "redirect") {
                $this->redirect(array("controller" => "organizations", "action" => "index"));
            }
            if (!$id) {
                throw new NotFoundException(__('Invalid post'));
            }
            $org_data = $this->Organization->findById($id);

            if ($org_data['Organization']['country'] == "") {
                $org_data['Organization']['country'] = "United States";
            }
            if ($org_data['Organization']['country'] != "") {
                $country_id = array_search($org_data['Organization']['country'], $listCountries);
//                $listS = $this->State->find('all', array('conditions' => array('State.country_id' => $country_id)));
//                foreach ($listS as $states) {
//                    $listState[$states['State']['name']] = $states['State']['name'];
//                }
                $listState = $this->Common->liststate($country_id);
            }
            $org_id = $org_data['Organization']['id'];
            $org_image = $org_data['Organization']['image'];
            if (!empty($org_image)) {
                $this->Session->write('Auth.User.org_image', $org_image);
            }
            $industry_value = $org_data['Organization']['industry'];
            if (!$org_data) {
                throw new NotFoundException(__('Invalid post'));
            }
            if ($this->request->is(array('post', 'put'))) {
                //pr($this->request->data);
                if (!empty($this->request->data['Org']['country'])) {
                    $this->request->data['Org']['country'] = $listCountries[$this->request->data['Org']['country']];
                }
                $values = $this->OrgCoreValue->find("list", array('conditions' => array('organization_id' => $id)));
                $departmentvalues = $this->OrgDepartment->find("list", array('conditions' => array('organization_id' => $id)));
                $jobtitlesvalues = $this->OrgJobTitle->find("list", array('conditions' => array('organization_id' => $id)));
                $entitiesvalues = $this->Entity->find("list", array('conditions' => array('organization_id' => $id)));
                $this->Organization->findById($id);
                $this->Organization->set($this->request->data['Org']);
                unset($this->Organization->validate['image']);
                unset($this->Organization->validate['secret_code']);
                $this->Organization->validate['name']['ruleUnique'] = array(
                    'rule' => 'isUnique',
                    'required' => 'create',
                    "on" => 'update',
                    'message' => 'Organization name already exists.'
                );
                $this->Organization->id = $id;
                if ($this->Organization->validates()) {
                    //entity
                    if (isset($this->request->data['Org']['entityactive'])) {
                        $counterentities = count($this->request->data['Org']['entityactive']);
                        for ($i = 0; $i < $counterentities; $i++) {
                            $hiddenentityid = isset($this->request->data['Org']['entityhiddenid'][$i]) ? $this->request->data['Org']['entityhiddenid'][$i] : "";
                            if ($this->request->data['Org']['entityactive'][$i] == "active" && $this->request->data['Org']['entitysave'][$i] == "save") {
                                $status = "1";
                            } else if ($this->request->data['Org']['entityactive'][$i] == "inactive" && $this->request->data['Org']['entitysave'][$i] == "save") {
                                $status = "0";
                            } else {
                                $status = "2";
                            }
                            if ($this->request->data['Org']['entitytextbox'][$i] != "") {
                                $entity_values = array(
                                    "name" => $this->request->data['Org']['entitytextbox'][$i],
                                    "organization_id" => $id,
                                    "status" => $status,
                                );
                                if ($this->Entity->findById($hiddenentityid)) {
                                    $this->Entity->id = $hiddenentityid;
                                    $this->Entity->save($entity_values);
                                } else if (in_array($this->request->data['Org']['entitytextbox'][$i], $entitiesvalues)) {
                                    foreach ($entitiesvalues as $editedid => $value) {
                                        if ($value == $this->request->data['Org']['entitytextbox'][$i]) {
                                            $this->Entity->id = $editedid;
                                            $this->Entity->set(array('status' => 1));
                                            $this->Entity->save();
                                        }
                                    }
                                } else {
                                    $this->Entity->create();
                                    $this->Entity->save($entity_values);
                                }
                            }
                        }
                    }
                    //department
                    if (isset($this->request->data['Org']['departmentactive'])) {

                        $counterdepartments = count($this->request->data['Org']['departmentactive']);
                        for ($i = 0; $i < $counterdepartments; $i++) {
                            $hiddendeptid = isset($this->request->data['Org']['departmenthiddenid'][$i]) ? $this->request->data['Org']['departmenthiddenid'][$i] : "";
                            $from_master = 1;
                            if ($this->request->data['Org']['departments'][$i] == "" || $this->request->data['Org']['departments'][$i] == "other") {
                                $this->request->data['Org']['departments'][$i] = $this->request->data['Org']['department_other_department'][$i];
                                $from_master = 0;
                            } else {
                                $this->request->data['Org']['departments'][$i] = $this->request->data['Org']['departments'][$i];
                            }
                            if ($this->request->data['Org']['departmentactive'][$i] == "active" && $this->request->data['Org']['departmentsave'][$i] == "save") {
                                $status = "1";
                            } else if ($this->request->data['Org']['departmentactive'][$i] == "inactive" && $this->request->data['Org']['departmentsave'][$i] == "save") {
                                $status = "0";
                            } else {
                                $status = "2";
                            }
                            if ($this->request->data['Org']['departments'][$i] != "") {
                                $department_values = array(
                                    "organization_id" => $id,
                                    "name" => $this->request->data['Org']['departments'][$i],
                                    "from_master" => $from_master,
                                    "status" => $status,
                                );
                                if ($this->OrgDepartment->findById($hiddendeptid)) {
                                    $this->OrgDepartment->id = $hiddendeptid;
                                    $this->OrgDepartment->save($department_values);
                                } else if (in_array($this->request->data['Org']['departments'][$i], $departmentvalues)) {
                                    foreach ($departmentvalues as $editedid => $value) {
                                        if ($value == $this->request->data['Org']['departments'][$i]) {
                                            $this->OrgDepartment->id = $editedid;
                                            $this->OrgDepartment->set(array('status' => 1));
                                            $this->OrgDepartment->save();
                                        }
                                    }
                                } else {
                                    $this->OrgDepartment->create();
                                    $this->OrgDepartment->save($department_values);
                                }
                            }
                        }
                    }
                    //job title
                    if (isset($this->request->data['Org']['jobtitleactive'])) {

                        $counterjobtitles = count($this->request->data['Org']['jobtitleactive']);
                        for ($i = 0; $i < $counterjobtitles; $i++) {
                            $hiddenjobtid = isset($this->request->data['Org']['jobtitlehiddenid'][$i]) ? $this->request->data['Org']['jobtitlehiddenid'][$i] : "";
                            $from_master = 1;
                            if ($this->request->data['Org']['jobtitles'][$i] == "" || $this->request->data['Org']['jobtitles'][$i] == "other") {
                                $this->request->data['Org']['jobtitles'][$i] = $this->request->data['Org']['jobtitle_other_department'][$i];
                                $from_master = 0;
                            } else {
                                $this->request->data['Org']['jobtitles'][$i] = $this->request->data['Org']['jobtitles'][$i];
                            }
                            if ($this->request->data['Org']['jobtitleactive'][$i] == "active" && $this->request->data['Org']['jobtitlesave'][$i] == "save") {
                                $status = 1;
                            } else if ($this->request->data['Org']['jobtitleactive'][$i] == "inactive" && $this->request->data['Org']['jobtitlesave'][$i] == "save") {
                                $status = 0; //inactive condition
                            } else {
                                $status = 2; //delete condition
                            }
                            if ($this->request->data['Org']['jobtitles'][$i] != "") {
                                $jobtitle_values = array(
                                    "organization_id" => $id,
                                    "title" => $this->request->data['Org']['jobtitles'][$i],
                                    "status" => $status,
                                    "from_master" => $from_master,
                                );
                                if ($this->OrgJobTitle->findById($hiddenjobtid)) {
                                    $this->OrgJobTitle->id = $hiddenjobtid;
                                    $this->OrgJobTitle->save($jobtitle_values);
                                    //	$log = $this->OrgJobTitle->getDataSource()->getLog(false, false);
                                    //    debug($log);
                                    //	end($log);
                                } else if (in_array($this->request->data['Org']['jobtitles'][$i], $jobtitlesvalues)) {
                                    foreach ($jobtitlesvalues as $editedid => $value) {
                                        if ($value == $this->request->data['Org']['jobtitles'][$i]) {
                                            $this->OrgJobTitle->id = $editedid;
                                            $this->OrgJobTitle->set(array('status' => 1));
                                            $this->OrgJobTitle->save();
                                        }
                                    }
                                } else {
                                    $this->OrgJobTitle->create();
                                    $this->OrgJobTitle->save($jobtitle_values);
                                }
                            }
                        }
                    }
                    //corevalues
                    if (isset($this->request->data['Org']['cvactive'])) {
                        $countercvalues = count($this->request->data['Org']['cvactive']);
                        for ($i = 0; $i < $countercvalues; $i++) {
                            $hiddenid = isset($this->request->data['Org']['hiddenid'][$i]) ? $this->request->data['Org']['hiddenid'][$i] : "";
                            if ($this->request->data['Org']['corevalues'][$i] == "" || $this->request->data['Org']['corevalues'][$i] == "other") {
                                $this->request->data['Org']['corevalues'][$i] = $this->request->data['Org']['other_department'][$i];
                            } else {
                                $this->request->data['Org']['corevalues'][$i] = $this->request->data['Org']['corevalues'][$i];
                            }
                            if ($this->request->data['Org']['cvactive'][$i] == "active" && $this->request->data['Org']['save'][$i] == "save") {
                                $status = "1";
                            } else if ($this->request->data['Org']['cvactive'][$i] == "inactive" && $this->request->data['Org']['save'][$i] == "save") {
                                $status = "0";
                            } else {
                                $status = "2";
                            }
                            $core_values = array(
                                "organization_id" => $id,
                                "name" => $this->request->data['Org']['corevalues'][$i],
                                "color_code" => $this->request->data['Org']['cp'][$i],
                                "status" => $status,
                            );
                            if ($this->OrgCoreValue->findById($hiddenid)) {
                                $this->OrgCoreValue->id = $hiddenid;
                                $this->OrgCoreValue->save($core_values);
                            } else if (in_array($this->request->data['Org']['corevalues'][$i], $values)) {
                                foreach ($values as $editedid => $value) {
                                    if ($value == $this->request->data['Org']['corevalues'][$i]) {
                                        $this->OrgCoreValue->id = $editedid;
                                        $this->OrgCoreValue->set(array('status' => 1));
                                        $this->OrgCoreValue->save();
                                    }
                                }
                            } else {
                                $this->OrgCoreValue->create();
                                $this->OrgCoreValue->save($core_values);
                            }
                        }
                    }

                    $this->Organization->save($this->request->data['Org']);
                    $this->redirect(array('controller' => 'organizations', 'action' => 'info', $id));
                } else {
                    $errors = $this->Organization->validationErrors;
                    foreach ($errors as $error) {
                        $errormsg .= $error[0] . "<br/>";
                    }
                    $errormsg;
                }
                //$this->redirect(array('controller'=>'users', 'action' => 'editorg', $id));
            }
            if (!isset($this->request->data['Org'])) {
                $this->request->data['Org'] = $org_data['Organization'];
                $this->request->data['Org']['country'] = $country_id;
                $this->request->data['Org']['state'] = array_search($org_data['Organization']['state'], $listState);
            }
            $industry = $this->Common->getDefaultIndustries(true, 2);
            $departments = $this->Common->getDefaultDepartments(true, 2);
            $departments = array_merge($departments, array("other" => "other"));
            $jobtitles = $this->Common->getDefaultJobTitles(true, 2);
            $jobtitles = array_merge($jobtitles, array("other" => "other"));
            $existing_corevalues = $this->existingcorevalues($org_id);
            //================departments
            $existing_dept = $this->OrgDepartment->find("all", array('fields' => array('id', 'name', 'status'), 'conditions' => array('organization_id' => $id, 'status' => array(0, 1))));
            foreach ($existing_dept as $depts) {
                $existing_departments[$depts['OrgDepartment']['id']] = $depts['OrgDepartment']['name'];
                $existing_departmentsstatus[$depts['OrgDepartment']['id']] = $depts['OrgDepartment']['status'];
            }
            //================jobtitles
            $existing_titles = $this->OrgJobTitle->find("all", array('fields' => array('id', 'title', 'status'), 'conditions' => array('organization_id' => $id, 'status' => array(0, 1))));
            foreach ($existing_titles as $jbtitles) {
                $existing_jobtitles[$jbtitles['OrgJobTitle']['id']] = $jbtitles['OrgJobTitle']['title'];
                $existing_jobtitlesstatus[$jbtitles['OrgJobTitle']['id']] = $jbtitles['OrgJobTitle']['status'];
            }
            //================entities
            $existing_ent = $this->Entity->find("all", array('fields' => array('id', 'name', 'status'), 'conditions' => array('organization_id' => $id, 'status' => array(0, 1))));
            foreach ($existing_ent as $ent) {
                $existing_entities[$ent['Entity']['id']] = $ent['Entity']['name'];
                $existing_entitiesstatus[$ent['Entity']['id']] = $ent['Entity']['status'];
            }
            $corevalues = $this->Common->getDefaultCoreValuesWeb();
            $corevalues = array_merge($corevalues, array("other" => "other"));
            $this->set(compact('authUser', 'org_data', 'org_id', 'org_image', 'org_data', 'corevalues', 'errormsg', 'industry', 'industry_value', 'existing_corevalues', 'existing_departments', 'departments', 'existing_jobtitles', 'jobtitles', 'existing_entities', 'existing_jobtitlesstatus', 'existing_departmentsstatus', 'existing_entitiesstatus', 'listCountries', 'listState', 'country_id'));
        }
    }

    public function createorg() {
        $errormsg = "";
        $nextorgId = 0;
        if ($this->Session->check('Auth.User.role') != "1" || $this->Session->check('Auth.User.role') != "2") {
            $this->Auth->logout();
            $this->redirect(array('action' => 'login'));
        } else {
            $country_code = 232;
            $stateselected = "";
            $authUser = $this->Auth->User();
            $client_id = "";
            if (!empty($this->params->params["named"])) {
                $client_id = $this->params->params["named"]["client_id"];
            }
            //=========to check if org admin is logged in
            if ($authUser["role"] == 2) {
                $client_id = $authUser["id"];
            }
            $nextorgId = $this->nextOrgId();
            //$org_image = $this->Session->read('Auth.User.org_image');
            $org_image = "";
            $listCountries = $this->Country->find('list', array("order" => "Country.name", 'fields' => array('Country.id', 'Country.name')));
            //list states for USA
            $listState = $this->Common->liststate(232);
//            if (!empty($this->request->data['User']['country'])) {
//                $listState = $this->State->find('list', array('conditions' => array('State.country_id' => trim($this->request->data['User']['country']))));
//            }
            if (!empty($this->request->data['Org']['country'])) {
                $listState = $this->Common->liststate($this->request->data['Org']['country']);
//                $listS = $this->State->find('all', array('conditions' => array('State.country_id' => trim($this->request->data['Org']['country']))));
//                foreach ($listS as $states) {
//                     $listState[$states['State']['name']] = $states['State']['name'];
//                }
            }
            //$industry = $this->Industry->find("all");
            $industry = $this->Common->getDefaultIndustries(true, 2);
            if ($this->request->is('post')) {
                $country_code = $this->request->data['Org']['country'];
                if ($country_code == "") {
                    $country_code = 232;
                }
                if ($country_code) {
                    $stateselected = $this->request->data['Org']['state'];
                    $this->request->data['Org']['country'] = $listCountries[$country_code];
                }
                $this->request->data['Org']['secret_code'] = $this->requestAction('/api/getSecretCode', array('organization'));
                //$this->request->data['Org']['secret_code'] = md5(md5(uniqid() . $this->request->data['Org']['short_name'] . time()));
                $this->request->data['Org']['admin_id'] = 0;
                $this->Organization->set($this->request->data['Org']);


                //pr($this->request->data);
                unset($this->Organization->validate['image']);
                //to check if any of the value is saved or not
                if ($this->Organization->validates()) {
                    //===============to save department
                    if (isset($this->request->data['Org']['departmentactive'])) {
                        $counterdepartments = count($this->request->data['Org']['departmentactive']);
                        //$totalsaveddepartment = 0;
                        //$departmentarray = array();
                        $departmentarray = array();
                        for ($i = 0; $i < $counterdepartments; $i++) {
                            if ($this->request->data['Org']['departmentactive'][$i] == "active" && $this->request->data['Org']['departmentsave'][$i] == "save") {
                                $frommaster = 1;
                                if ($this->request->data['Org']['departments'][$i] == "other") {
                                    $this->request->data['Org']['departments'][$i] = $this->request->data['Org']['department_other_department'][$i];
                                    $frommaster = 0;
                                }
                                $department = array("name" => $this->request->data['Org']['departments'][$i], "frommaster" => $frommaster);
                                array_push($departmentarray, $department);
                                //$totalsaveddepartment++;
                            }
                            //array_push($departmentarray, $this->request->data['Org']['departments'][$i]);
                        }
//                        if($totalsaveddepartment == 0){
//                            $errormsg .= "Atleast one Department Needs to be Save & Active<br>"; 
//                        }
                        //pr($departmentarray);exit;
                    }
                    //==============to save Job titles
                    if (isset($this->request->data['Org']['jobtitleactive'])) {
                        $counterjobtitles = count($this->request->data['Org']['jobtitleactive']);
                        $jobtitlearray = array();
                        //$totalsavedjobtitle = 0;
                        for ($i = 0; $i < $counterjobtitles; $i++) {
                            if ($this->request->data['Org']['jobtitleactive'][$i] == "active" && $this->request->data['Org']['jobtitlesave'][$i] == "save") {
                                $frommaster = 1;
                                if ($this->request->data['Org']['jobtitle'][$i] == "other") {
                                    $this->request->data['Org']['jobtitle'][$i] = $this->request->data['Org']['jobtitle_other_department'][$i];
                                    $frommaster = 0;
                                }
                                $jobtitle = array("name" => $this->request->data['Org']['jobtitle'][$i], "frommaster" => $frommaster);
                                array_push($jobtitlearray, $jobtitle);

                                //$totalsavedjobtitle++;
                            }
                        }
//                        if($totalsavedjobtitle == 0){
//                            $errormsg .= "Atleast one Job Title Needs to be Save & Active<br>"; 
//                        }
                    }
                    $entityarray = array();
                    //===============to save entities
                    if (isset($this->request->data['Org']['entityactive'])) {

                        $counterentities = count($this->request->data['Org']['entityactive']);

                        for ($i = 0; $i < $counterentities; $i++) {
                            if ($this->request->data['Org']['entityactive'][$i] == "active" && $this->request->data['Org']['entitysave'][$i] == "save") {
                                $entity = array("name" => $this->request->data['Org']['entitytextbox'][$i]);
                                array_push($entityarray, $entity);
                            }
                        }
                    }
                    //=================to save Core values
                    if (isset($this->request->data['Org']['cvactive'])) {
                        $countercvalues = count($this->request->data['Org']['cvactive']);
                        $totalsavedcv = 0;
                        $cvarray = array();
                        for ($i = 0; $i < $countercvalues; $i++) {
                            if ($this->request->data['Org']['cvactive'][$i] == "active" && $this->request->data['Org']['save'][$i] == "save") {
                                $from_master = 1;
                                if ($this->request->data['Org']['corevalues'][$i] == "other") {
                                    $this->request->data['Org']['corevalues'][$i] = $this->request->data['Org']['other_department'][$i];
                                    $from_master = 0;
                                }
                                echo $from_master;
                                $core_values = array("name" => $this->request->data['Org']['corevalues'][$i], "colorcode" => $this->request->data['Org']['cp'][$i], "frommaster" => $from_master);
                                array_push($cvarray, $core_values);
                                $totalsavedcv++;
                            }
                        }
                        if ($totalsavedcv == 0) {
                            $errormsg .= "Atleast one Core Value Needs to be Save & Active<br>";
                        }
                    }
                    //$totalsaveddepartment > 0 && $totalsavedjobtitle && 
                    if ($totalsavedcv > 0) {

                        //===========enter Department
                        for ($j = 0; $j < count($departmentarray); $j++) {
                            $department = array(
                                "organization_id" => $nextorgId,
                                "name" => $departmentarray[$j]["name"],
                                "from_master" => $departmentarray[$j]["frommaster"],
                                "status" => "1",
                            );
                            $this->OrgDepartment->create();
                            $this->OrgDepartment->save($department);
                        }
                        //===========enter jobtitle
                        for ($j = 0; $j < count($jobtitlearray); $j++) {
                            $jobtitle = array(
                                "organization_id" => $nextorgId,
                                "title" => $jobtitlearray[$j]["name"],
                                "from_master" => $jobtitlearray[$j]["frommaster"],
                                "status" => "1",
                            );
                            $this->OrgJobTitle->create();
                            $this->OrgJobTitle->save($jobtitle);
                        }
                        //===========enter entity
                        for ($j = 0; $j < count($entityarray); $j++) {
                            $entity = array(
                                "organization_id" => $nextorgId,
                                "name" => $entityarray[$j]["name"],
                                "status" => "1",
                            );
                            $this->Entity->create();
                            $this->Entity->save($entity);
                        }
                        //===========enter corevalues
                        for ($j = 0; $j < count($cvarray); $j++) {
                            $core_values = array(
                                "organization_id" => $nextorgId,
                                "name" => $cvarray[$j]["name"],
                                "color_code" => $cvarray[$j]["colorcode"],
                                "from_master" => $cvarray[$j]["frommaster"],
                                "status" => "1",
                            );
                            $this->OrgCoreValue->create();
                            $this->OrgCoreValue->save($core_values);
                        }
                        //===========save organiztions and redirect to desired page
                        if (!empty($client_id)) {
                            $this->loadModel("User");
                            $this->loadModel("Email");
                            $this->request->data['Org']['admin_id'] = $client_id;
                            $this->Organization->save($this->request->data['Org']);

                            //=====email to org owner if client id is already created
                            $Usermodel = $this->User->find("all", array("fields" => array("fname", "email"), "conditions" => array("id" => $client_id)));
                            foreach ($Usermodel as $userdetail) {
                                $firstname = $userdetail["User"]["fname"];
                                $emailclient = $userdetail["User"]["email"];
                            }
                            //==email to organization owner
                            $subject = "nDorse notification -- New Organization Created Successfully";
                            $viewVars = array("org_name" => $this->request->data['Org']['name'], "fname" => $firstname);

                            /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                            $userIdEncrypted = base64_encode($client_id);
                            $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                            $viewVars["pathToRender"] = $pathToRender;
                            /*                             * * */

                            $configVars = serialize($viewVars);
                            $emailQueue = array("to" => $emailclient, "subject" => $subject, "config_vars" => $configVars, "template" => "create_org");
                            $this->Email->save($emailQueue);
                            //
                            $new_userorganization = array(
                                "user_id" => $client_id,
                                "organization_id" => $nextorgId,
                                "user_role" => 2,
                                "is_default" => '1',
                                "pool_type" => 'free',
                                "status" => 1,
                                "joined" => 1,
                                "flow" => "web_invite"
                            );
                            $this->UserOrganization->save($new_userorganization);
                            //
                            //==email to organization owner end
                            $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
                        } else {
                            //======organization is deleted until create client functionality runs
                            $this->Session->write('statusorganization', $this->request->data['Org']['status']);
                            $this->request->data['Org']['status'] = 3;
                            $this->Organization->save($this->request->data['Org']);
                            $this->Session->write('Auth.User.org_id', $nextorgId);
                            $this->Session->write('showClientMsg', true);
                            $this->redirect(array('action' => 'createclient'));
                        }
                    }
                } else {
                    $errors = $this->Organization->validationErrors;
                    foreach ($errors as $error) {
                        $errormsg .= $error[0] . "<br/>";
                    }
                }
            }
            $corevalues = $this->Common->getDefaultCoreValuesWeb();
            $corevalues = array_merge($corevalues, array("other" => "other"));
            $departments = $this->Common->getDefaultDepartments(true, 2);
            $departments = array_merge($departments, array("other" => "other"));
            $jobtitles = $this->Common->getDefaultJobTitles(true, 2);
            $jobtitles = array_merge($jobtitles, array("other" => "other"));
            $this->set(compact('stateselected', 'country_code', 'authUser', 'nextorgId', 'org_image', 'corevalues', 'errormsg', 'industry', 'departments', 'jobtitles', 'listCountries', 'listState'));
        }
    }

    /** function to create client * */
    public function createclient() {
        $authUser = $this->Auth->User();
        $org_id = $this->Session->read('Auth.User.org_id');
        $this->loadModel('UserOrganization');
        $errormsg = "";
        $nextclientId = 0;
        if ($this->Session->check('Auth.User.role') != "1") {
            $this->Auth->logout();
            $this->redirect(array('action' => 'login'));
        } else {
            $country_code = 232;
            $nextclientId = $this->nextId();
            $client_image = "";
            $listCountries = $this->Country->find('list', array('order' => 'Country.name', 'fields' => array('Country.id', 'Country.name')));
            //$listState = array('0' => 'Select State');
            $listState = $this->Common->liststate(232);
            if (!empty($this->request->data['User']['country'])) {
                $this->Common->liststate($this->request->data['User']['country']);
                //$listState = $this->State->find('list', array('conditions' => array('State.country_id' => trim($this->request->data['User']['country']))));
            }
            $org_id = $this->Session->read('Auth.User.org_id');
            if ($org_id == "") {
                $this->redirect(array('controller' => 'users', 'action' => 'createorg'));
            }
            //if($org_id){
            if ($this->request->is('post')) {
                $this->Session->delete('showClientMsg');
                //============mobile visible conditions
                if (isset($this->request->data["User"]["mobile_visible"])) {
                    $this->request->data['User']['mobile_visible'] = 1;
                } else {
                    $this->request->data['User']['mobile_visible'] = 0;
                }
                //==changing the dob format 
                $this->request->data['User']['dob'] = $this->Common->dateConvertServer($this->request->data['User']['dob']);
                $country_code = $this->request->data['User']['country'];
                $country = $this->Country->findById($country_code);
                if (!empty($country)) {
                    $this->request->data['User']['country'] = $country['Country']['name'];
                }
                $this->request->data['User']['username'] = $this->request->data['User']['email'];
                //$this->request->data['User']['secret_code'] = md5(md5(uniqid() . $this->request->datadata['User']['email'] . time()));
                $this->request->data['User']['secret_code'] = $this->requestAction('/api/getSecretCode', array('user'));
                $password_random = $this->Common->randompasswordgenerator(8);
                $this->request->data['User']['password'] = $password_random;
                $skillsval = "";
                if (!empty($this->request->data['User']['skills'])) {
                    $skillsarray = $this->request->data['User']['skills'];
                    $this->request->data['User']['skills'] = $this->Common->trimminguserdata($skillsarray);
                }
                $hobbiesval = "";
                if (!empty($this->request->data['User']['hobbies'])) {
                    $hobbiesarray = $this->request->data['User']['hobbies'];
                    $this->request->data['User']['hobbies'] = $this->Common->trimminguserdata($hobbiesarray);
                }
                //$this->request->data['User']['skills'] = $skillsval;
                //$this->request->data['User']['hobbies'] = $hobbiesval;
                $this->User->setValidation('register');
                $this->User->set($this->request->data['User']);

                unset($this->User->validate['image']);
                if ($this->User->validates()) {
                    $this->loadModel("Email");
                    $this->loadModel("DefaultOrg");
                    //if ($org_id) {
                    $new_userorganization = array(
                        "user_id" => $nextclientId,
                        "organization_id" => $org_id,
                        "user_role" => $this->request->data['User']['role'],
                        "is_default" => '1',
                        "pool_type" => 'free',
                        "status" => $this->request->data['User']['status'],
                        "joined" => 1,
                        "flow" => "web_invite"
                    );
                    //=====overwrited since in user table we put status as 1 always
                    $this->request->data['User']['status'] = 1;
                    //}
                    $this->User->save($this->request->data['User']);
                    //==========to insert in 


                    $lastinsertedid = $this->User->getLastInsertId();
                    $defaultorgarray = array(
                        "user_id" => $lastinsertedid,
                        "organization_id" => $org_id,
                        "status" => 1
                    );
                    //$defaultorgexisting = $this->DefaultOrg->findByUserId($lastinsertedid);

                    $this->DefaultOrg->Create();
                    $this->DefaultOrg->save($defaultorgarray);


                    //=============invitation email
                    $subject = "nDorse notification -- Welcome to nDorse";
                    $viewVars = array("fname" => $this->request->data['User']['fname'], "username" => $this->request->data['User']['email'], "password" => $password_random);

                    /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode($lastinsertedid);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $viewVars["pathToRender"] = $pathToRender;
                    /*                     * * */

                    $configVars = serialize($viewVars);
                    $emailQueue[] = array("to" => $this->request->data['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "invitation_admin_orgowner");

                    if ($org_id) {
                        $this->loadModel("Organization");
                        $this->Organization->id = $org_id;
                        $orgarraynew = array("admin_id" => $nextclientId, "status" => $this->Session->read('statusorganization'));
                        $this->Organization->save($orgarraynew, false);
                        //$this->Organization->saveField('status', 1, false);
                        $this->UserOrganization->save($new_userorganization);
                        //====email to orgadmin with their orgname
                        $orgname = $this->Organization->field("name", array("id" => $org_id));
                        $subject = "nDorse notification -- New Organization Created Successfully";
                        $viewVars = array("org_name" => $orgname, "fname" => $this->request->data['User']['fname']);

                        /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($lastinsertedid);
                        $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /*                         * * */

                        $configVars = serialize($viewVars);
                        $emailQueue[] = array("to" => $this->request->data['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "create_org");
                        $this->Email->saveMany($emailQueue);
                        //$this->Email->save($emailQueue);
                        //====end of email to orgadmin with their orgname
                        $this->Session->delete('Auth.User.org_id');
                        $this->Session->delete('statusorganization');
                    }
                    $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
                    exit;
                } else {
                    $this->request->data['User']['skills'] = explode(",", $skillsval);
                    $this->request->data['User']['hobbies'] = explode(",", $hobbiesval);
                    $errors = $this->User->validationErrors;
                    foreach ($errors as $error) {
                        $errormsg .= $error[0] . "<br/>";
                    }
                }
            }
        }
        $this->request->data['User']['password'] = "";
        $role = array("2" => "admin");
        //$departments = $this->Common->getDefaultDepartments(true, 2);
        //$jobtitles = $this->Common->getDefaultJobTitles(true, 2);
        $skill = $this->Common->getDefaultSkills(true, 2);
        $hobbies = $this->Common->getDefaultHobbies(true, 2);
        //$departments = array_merge($departments, array("other" => "other"));
        $skill = array_merge($skill, array("other" => "Add More Skills"));
        $hobbies = array_merge($hobbies, array("other" => "Add More Hobbies"));
        //$jobtitles = array_merge($jobtitles, array("other" => "other"));
        $this->set(compact('authUser', 'nextclientId', 'client_image', "role", "departments", "jobtitles", "skill", "hobbies", "errormsg", "listCountries", "listState", "country_code"));

        //   $this->redirect(array('action' => 'index'));
    }

    /** function to create client * */
    public function editclient($id = null) {
        $errormsg = "";
        $listCountries = $this->Country->find('list', array('order' => 'name', 'fields' => array('Country.id', 'Country.name')));
        $listState = array('0' => 'Select State');
        $country_id = 0;
        $authUser = $this->Auth->User();
        if ($id) {
            if (isset($id)) {
                if ($this->Session->check('Auth.User.role') != "1") {
                    $this->Auth->logout();
                    $this->redirect(array('action' => 'login'));
                } else {
                    //$userdata = $this->User->find('all');
                    if ($authUser["role"] == 2 || $authUser["role"] == 6) {
                        $id = $authUser["id"];
                    }
                    $userdata = $this->User->findById((int) $id);
                    //===============fetch roles as per number
                    $roles = Configure::read("Users_Role");
//                    pr($roles); exit;
                    if ($authUser["role"] == 2) {
                        $userdata["User"]["role"] = $roles[2];
                    } else if ($authUser["role"] == 6) {
                        $userdata["User"]["role"] = $roles[6];
                    }


                    if ($userdata['User']['country'] != "") {
                        $country_id = array_search($userdata['User']['country'], $listCountries);
                        //$listState = $this->State->find('list', array('conditions' => array('State.country_id' => $country_id)));
                        $listState = $this->Common->liststate($country_id);
                    }

                    $client_image = $userdata['User']['image'];
                    $userdata['User']['dob'] = $this->Common->dateConvertDisplay($userdata['User']['dob']);
                    $this->Session->write('Auth.User.client_image', $client_image);
                    //$client_image = $this->Session->read('Auth.User.client_image');
                    if ($this->request->is(array('post', 'put'))) {

                        //==obile visible field
                        if (isset($this->request->data["User"]["mobile_visible"])) {
                            $this->request->data['User']['mobile_visible'] = 1;
                        } else {
                            $this->request->data['User']['mobile_visible'] = 0;
                        }
                        $listState = $this->Common->liststate($this->request->data['User']['country']);
                        if (isset($this->request->data['User']['country']) && !empty($this->request->data['User']['country'])) {
                            $country_name = $listCountries[$this->request->data['User']['country']];
                            $this->request->data['User']['country'] = $country_name;
                        }
                        if ($listState == "") {
                            $this->request->data['User']['state'] = $this->request->data['User']['state_name'];
                        }
                        if (trim($this->request->data['User']['changepassword']) != "") {
                            $this->request->data['User']['password'] = trim($this->request->data['User']['changepassword']);
                        }
                        $this->request->data['User']['dob'] = $this->Common->dateConvertServer($this->request->data['User']['dob']);
                        //$this->request->data['User']['dob'] = date("Y-m-d", strtotime($this->request->data['User']['dob']));
                        $this->request->data['User']['username'] = $this->request->data['User']['email'];
                        $this->request->data['User']['role'] = array_search($this->request->data['User']['role'], $roles);
                        $skillsval = "";
                        if (!empty($this->request->data['User']['skills'])) {
                            $skillsarray = $this->request->data['User']['skills'];
                            $skillsval = $this->Common->trimminguserdata($skillsarray);
                            //$skillsval = implode(",", $skillsarray);
                        }
                        $hobbiesval = "";
                        if (!empty($this->request->data['User']['hobbies'])) {
                            $hobbiesarray = $this->request->data['User']['hobbies'];
                            $hobbiesval = $this->Common->trimminguserdata($hobbiesarray);
                            //$hobbiesval = implode(",", $hobbiesarray);
                        }
                        $this->request->data['User']['skills'] = $skillsval;
                        $this->request->data['User']['hobbies'] = $hobbiesval;
                        $this->User->setValidation('edit');
                        $this->User->set($this->request->data['User']);
                        unset($this->User->validate['image']);
                        if ($this->User->Validates()) {
                            $this->User->id = $id;
                            $this->User->save($this->request->data['User']);
                            if ($authUser["role"] == 1) {
                                $this->redirect(array("controller" => "users", "action" => "clientinfo", $id));
                            } else {
                                $this->Session->write('alertMsg', "Profile updated successfully.");
                                $this->redirect(array("controller" => "organizations", "action" => "index"));
                            }
                        } else {
                            $errors = $this->User->validationErrors;
                            foreach ($errors as $error) {
                                $errormsg .= $error[0] . "<br/>";
                            }
                            $errormsg;
                        }
                    }
                    $this->request->data = $userdata;
                    $this->request->data['User']['country'] = $country_id;
//                    if(!empty($listState)){
//                        $this->request->data['User']['state'] = array_search($userdata['User']['state'], $listState);
//                    }
                    //pr($this->request->data);
                }
                $role = array("2" => "admin");
                $departments = $this->Common->getDefaultDepartments();
                $jobtitles = $this->Common->getDefaultJobTitles();
                $skill = $this->Common->getDefaultSkills();
                $selectedskills = array();
                if ($userdata['User']['skills'] != "") {
                    $selectedskills = explode(",", $userdata['User']['skills']);
                    $selectedskills = array_map("trim", $selectedskills);
                    foreach ($selectedskills as $skillsselected) {
                        if (!in_array($skillsselected, $skill)) {
                            $skill = array_merge($skill, array($skillsselected => $skillsselected));
                        }
                    }
                }

                $selectedhobbies = array();
                $hobbies = $this->Common->getDefaultHobbies();
                if ($userdata['User']['hobbies'] != "") {
                    $selectedhobbies = explode(",", $userdata['User']['hobbies']);
                    $selectedhobbies = array_map("trim", $selectedhobbies);
                    foreach ($selectedhobbies as $hobbiesselected) {
                        if (!in_array($hobbiesselected, $hobbies)) {
                            $hobbies = array_merge($hobbies, array($hobbiesselected => $hobbiesselected));
                        }
                    }
                }

                $departments = array_merge($departments, array("other" => "other"));
                $skill = array_merge($skill, array("other" => "Add More Skills"));
                $hobbies = array_merge($hobbies, array("other" => "Add More Hobbies"));
                $jobtitles = array_merge($jobtitles, array("other" => "other"));
                $this->set(compact('country_code', 'authUser', 'userdata', 'nextclientId', 'client_image', "role", "departments", "jobtitles", "skill", "hobbies", "errormsg", 'selectedskills', 'selectedhobbies', 'listCountries', 'listState'));
            }
        }
    }

    /** function to create client * */
    public function editcuser($id = null) {
        $errormsg = "";
        $listCountries = $this->Country->find('list', array('order' => 'name', 'fields' => array('Country.id', 'Country.name')));
        $listState = array('0' => 'Select State');
        $country_id = 0;
        $authUser = $this->Auth->User();
        if ($id) {
            if (isset($id)) {
                if ($this->Session->check('Auth.User.role') != "3") {
                    $this->Auth->logout();
                    $this->redirect(array('action' => 'login'));
                } else {
                    //$userdata = $this->User->find('all');
                    $userdata = $this->User->findById((int) $id);
                    //===============fetch roles as per number
                    $roles = Configure::read("Users_Role");
                    $userdata["User"]["role"] = $roles[2];
                    if ($userdata['User']['country'] != "") {
                        $country_id = array_search($userdata['User']['country'], $listCountries);
                        //$listState = $this->State->find('list', array('conditions' => array('State.country_id' => $country_id)));
                        $listState = $this->Common->liststate($country_id);
                    }

                    $client_image = $userdata['User']['image'];
                    $userdata['User']['dob'] = $this->Common->dateConvertDisplay($userdata['User']['dob']);
                    $this->Session->write('Auth.User.client_image', $client_image);
                    //$client_image = $this->Session->read('Auth.User.client_image');
                    if ($this->request->is(array('post', 'put'))) {

                        if (isset($this->request->data["User"]["user_curr_org_id"])) {
//                            pr($this->request->data);
//                            exit;
                            $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));
                            $currntOrgId = $this->request->data["User"]["user_curr_org_id"];
                            if (isset($this->request->data["User"]["department_id"]) && $this->request->data["User"]["department_id"] != '') {
                                $userOrganization['department_id'] = $this->request->data["User"]["department_id"];
                            } else {
                                $userOrganization['department_id'] = 0;
                            }
                            if (isset($this->request->data["User"]["entityid"]) && $this->request->data["User"]["entityid"] != '') {
                                $userOrganization['entity_id'] = $this->request->data["User"]["entityid"];
                            } else {
                                $userOrganization['entity_id'] = 0;
                            }
                            if (isset($this->request->data["User"]["jobtitle_id"]) && $this->request->data["User"]["jobtitle_id"] != "") {
                                $userOrganization['job_title_id'] = $this->request->data["User"]["jobtitle_id"];
                            } else {
                                $userOrganization['job_title_id'] = 0;
                            }
//                            pr($userOrganization);

                            $userOrgResponse = $this->UserOrganization->updateAll(
                                    $userOrganization, array('user_id' => $id, 'organization_id' => $currntOrgId, 'status' => 1));


//                            pr($userOrgResponse);
//                            exit;
                        }

                        //==obile visible field
                        if (isset($this->request->data["User"]["mobile_visible"])) {
                            $this->request->data['User']['mobile_visible'] = 1;
                        } else {
                            $this->request->data['User']['mobile_visible'] = 0;
                        }
                        $listState = $this->Common->liststate($this->request->data['User']['country']);
                        if (isset($this->request->data['User']['country']) && !empty($this->request->data['User']['country'])) {
                            $country_name = $listCountries[$this->request->data['User']['country']];
                            $this->request->data['User']['country'] = $country_name;
                        }
                        if ($listState == "") {
                            $this->request->data['User']['state'] = $this->request->data['User']['state_name'];
                        }

                        $this->request->data['User']['dob'] = $this->Common->dateConvertServer($this->request->data['User']['dob']);
                        //$this->request->data['User']['dob'] = date("Y-m-d", strtotime($this->request->data['User']['dob']));
                        $this->request->data['User']['username'] = $this->request->data['User']['email'];
//                        $this->request->data['User']['role'] = array_search($this->request->data['User']['role'], $roles);
                        $skillsval = "";
                        if (!empty($this->request->data['User']['skills'])) {
                            $skillsarray = $this->request->data['User']['skills'];
                            $skillsval = $this->Common->trimminguserdata($skillsarray);
                            //$skillsval = implode(",", $skillsarray);
                        }
                        $hobbiesval = "";
                        if (!empty($this->request->data['User']['hobbies'])) {
                            $hobbiesarray = $this->request->data['User']['hobbies'];
                            $hobbiesval = $this->Common->trimminguserdata($hobbiesarray);
                            //$hobbiesval = implode(",", $hobbiesarray);
                        }
                        $this->request->data['User']['skills'] = $skillsval;
                        $this->request->data['User']['hobbies'] = $hobbiesval;
                        $this->User->setValidation('edit');
                        $this->User->set($this->request->data['User']);
                        unset($this->User->validate['image']);
                        if ($this->User->Validates()) {
                            $this->User->id = $id;
                            $this->User->save($this->request->data['User']);
//                            if ($authUser["role"] == 1) {
                            $this->redirect(array("controller" => "users", "action" => "editcuser", $id));
//                            } else {
//                                $this->Session->write('alertMsg', "Profile updated successfully.");
//                                $this->redirect(array("controller" => "organizations", "action" => "index"));
//                            }
                        } else {
                            $errors = $this->User->validationErrors;
                            foreach ($errors as $error) {
                                $errormsg .= $error[0] . "<br/>";
                            }
                            $errormsg;
                        }
                    }

                    /** Department and job title editable start ** */
                    $userCurrntOrgID = $this->Common->getUserCurrentOrgId($id);
                    $userDept = $this->Common->getUserCurrentDept($id, $userCurrntOrgID);
                    $userSubDept = $this->Common->getUserCurrentSubOrg($id, $userCurrntOrgID);
                    $userJobTitle = $this->Common->getUserCurrentJobTitle($id, $userCurrntOrgID);
                    $orgJobTitle = $this->Common->getorgjobtitles($userCurrntOrgID);
                    $orgDepartment = $this->Common->getorgdepartments($userCurrntOrgID);
                    $orgSubOrg = $this->Common->getorgentities($userCurrntOrgID);
//                     pr($orgDepartment); 
                    $userdata['User']['dept_id'] = $userDept;
                    $userdata['User']['entity_id'] = $userSubDept;
                    $userdata['User']['jobtitle_id'] = $userJobTitle;


                    /** Department and job title editable end** */
//                    pr($userdata); exit;
                    $this->request->data = $userdata;
                    $this->request->data['User']['country'] = $country_id;
//                    if(!empty($listState)){
//                        $this->request->data['User']['state'] = array_search($userdata['User']['state'], $listState);
//                    }
                    //pr($this->request->data);
                }
                $role = array("2" => "admin");

                $skill = $this->Common->getDefaultSkills();
                $selectedskills = array();
                if ($userdata['User']['skills'] != "") {
                    $selectedskills = explode(",", $userdata['User']['skills']);
                    $selectedskills = array_map("trim", $selectedskills);
                    foreach ($selectedskills as $skillsselected) {
                        if (!in_array($skillsselected, $skill)) {
                            $skill = array_merge($skill, array($skillsselected => $skillsselected));
                        }
                    }
                }

                $selectedhobbies = array();
                $hobbies = $this->Common->getDefaultHobbies();
                if ($userdata['User']['hobbies'] != "") {
                    $selectedhobbies = explode(",", $userdata['User']['hobbies']);
                    $selectedhobbies = array_map("trim", $selectedhobbies);
                    foreach ($selectedhobbies as $hobbiesselected) {
                        if (!in_array($hobbiesselected, $hobbies)) {
                            $hobbies = array_merge($hobbies, array($hobbiesselected => $hobbiesselected));
                        }
                    }
                }


                $skill = array_merge($skill, array("other" => "Add More Skills"));
                $hobbies = array_merge($hobbies, array("other" => "Add More Hobbies"));

                $this->set(compact('country_code', 'authUser', 'userdata', 'nextclientId', 'client_image', "role", "skill", "hobbies", "errormsg", 'selectedskills', 'selectedhobbies', 'listCountries', 'listState', 'userDept', 'userSubDept', 'userJobTitle', 'orgJobTitle', 'orgDepartment', 'orgSubOrg', 'userCurrntOrgID'));
            }
        }
    }

    public function nextId() {
        $result = $this->User->query("SELECT Auto_increment FROM information_schema.tables AS NextId  WHERE table_name='users' AND table_schema='" . DATABASESCHEMANAME . "'");
        return $result[0]['NextId']['Auto_increment'];
    }

    public function nextOrgId() {
        $result = $this->Organization->query("SELECT Auto_increment FROM information_schema.tables AS NextId  WHERE table_name='organizations' AND table_schema='" . DATABASESCHEMANAME . "'");
        return $result[0]['NextId']['Auto_increment'];
    }

    public function existingcorevalues($id) {
        $result = $this->OrgCoreValue->query("Select id,name,color_code,status FROM org_core_values WHERE organization_id = '$id' and (status=1 or status=0)");
        return $result;
    }

    public function setOrgImage() {

        $user_id = $this->Auth->user('id');


        $this->User->id = $user_id;
        $errorFlag = true;
        $filePath = ORG_IMAGE_DIR;
        $thumbPath = WWW_ROOT . $filePath . 'small/';
        if ($this->request->is('post')) {
            $filedata = $this->request->data;

            $filedata = $filedata['Orgphoto']['Userphoto'];

            $errorMesage = '';
            if (isset($filedata)) {

                $responseLogo = $this->Common->checkImageType($filedata['type']);
                if (!$responseLogo['status'] && $filedata['name'] != '') {

                    $errorMesage .= 'Make sure that profile image you have uploaded have png, jpg or jpeg format only';
                } else {
                    $response = $this->Common->uploadImage($filedata, $filePath, $thumbPath);

                    if ($response['status'] == 1) {
                        //$this->User->saveField('photo', $response["imageName"], false);
                        $this->Session->write('Auth.User.org_image', $response["imageName"]);
                        echo json_encode(array("status" => 1, "message" => "organization image has been updated.", "imageloc" => $response["imageName"], "error" => ""));
                        exit;
                    } else {
                        echo json_encode(array("status" => 0, "message" => "", "imageloc" => "", "error" => $response["error"]));
                        exit;
                    }
                }
                if ($errorMesage != '') {

                    echo json_encode(array("status" => 0, "message" => "organization image has been not updated.", "error" => $errorMesage));
                }
                exit;
            }
        }
    }

    public function setorgcpimage() {

        $user_id = $this->Auth->user('id');


        $this->User->id = $user_id;
        $errorFlag = true;
        $filePath = ORG_IMAGE_DIR;
        $thumbPath = WWW_ROOT . $filePath . 'small/';
        if ($this->request->is('post')) {
            $filedata = $this->request->data;

            $filedata = $filedata['Orgphoto']['cp_logo'];

            $errorMesage = '';
            if (isset($filedata)) {

                $responseLogo = $this->Common->checkImageType($filedata['type']);
                if (!$responseLogo['status'] && $filedata['name'] != '') {

                    $errorMesage .= 'Make sure that profile image you have uploaded have png, jpg or jpeg format only';
                } else {
                    $response = $this->Common->uploadImage($filedata, $filePath, $thumbPath);

                    if ($response['status'] == 1) {
                        //$this->User->saveField('photo', $response["imageName"], false);
                        $this->Session->write('Auth.User.org_image', $response["imageName"]);
                        echo json_encode(array("status" => 1, "message" => "organization image has been updated.", "imageloc" => $response["imageName"], "error" => ""));
                        exit;
                    } else {
                        echo json_encode(array("status" => 0, "message" => "", "imageloc" => "", "error" => $response["error"]));
                        exit;
                    }
                }
                if ($errorMesage != '') {

                    echo json_encode(array("status" => 0, "message" => "organization image has been not updated.", "error" => $errorMesage));
                }
                exit;
            }
        }
    }

    public function setImage() {
        $user_id = $this->Auth->user('id');
        $this->User->id = $user_id;
        $errorFlag = true;
        $filePath = PROFILE_IMAGE_DIR;
        $thumbPath = WWW_ROOT . $filePath . 'small/';
        if ($this->request->is('post')) {
            $filedata = $this->request->data;
            $filedata = $filedata['Userphoto']['Userphoto'];
            $errorMesage = '';
            if (isset($filedata)) {
                $responseLogo = $this->Common->checkImageType($filedata['type']);

                if (!$responseLogo['status'] && $filedata['name'] != '') {
                    $errorMesage .= 'Make sure that profile image you have uploaded have png, jpg or jpeg format only';
                } else {
                    $response = $this->Common->uploadImage($filedata, $filePath, $thumbPath);
                    if ($response['status'] == 1) {
                        $this->Session->write('Auth.User.client_image', $response["imageName"]);
                        echo json_encode(array("status" => 1, "message" => "profile photo has been updated.", "imageloc" => $response["imageName"], "error" => ""));
                        exit;
                    } else {
                        echo json_encode(array("status" => 0, "message" => "", "imageloc" => "", "error" => $response["error"]));
                        exit;
                    }
                }
                if ($errorMesage != '') {
                    echo json_encode(array("status" => 0, "message" => "profile image has been not updated.", "error" => $errorMesage));
                }
                exit;
            }
        }
    }

    public function deleteimage() {
        $errorFlag = true;
        $filePath = PROFILE_IMAGE_DIR;
        $thumbPath = WWW_ROOT . $filePath . 'small/';
        if ($this->request->is('post')) {
            $file_name = $this->request->data['image_name'];
            unlink(WWW_ROOT . $filePath . $file_name);
            unlink($thumbPath . $file_name);
            $this->Session->delete('Auth.User.client_image');
            //$this->Session->write('Auth.User.client_image', "");
            echo json_encode(array("status" => 1, "message" => "image has been deleted.", "imageloc" => "img/p_pic.png", "error" => ""));
            exit;
        }
    }

    public function deleteorgimage() {
        $errorFlag = true;
        $filePath = ORG_IMAGE_DIR;
        $thumbPath = WWW_ROOT . $filePath . 'small/';
        if ($this->request->is('post')) {
            $this->request->data;
            $file_name = $this->request->data['image_name'];
            unlink(WWW_ROOT . $filePath . $file_name);
            unlink($thumbPath . $file_name);
            $this->Session->write('Auth.User.org_image', "");
            echo json_encode(array("status" => 1, "message" => "image has been deleted.", "imageloc" => "img/comp_pic.png", "error" => ""));
            exit;
        }
    }

    /** function to logout * */
    public function logout() {
        if ($this->Auth->User("role") == 2) {
            $this->logoutSystem($this->Auth->user('id'));
        }
        //$this->Cookie->delete("remember_me_cookie");
        //$this->Cookie->delete("portal_cookie");
        $this->redirect($this->Auth->logout());
    }

    public function setting() {
        ini_set('memory_limit', '2G');
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
            $this->loadModel("globalsettingFaq");
            $prev_page = Router::url($this->referer(), true);
            $conditionsset["OR"] = array("key" => array("endorsement_limit", "Notification_On", "tandc"));
            $detailedsettings_array = $this->GlobalSetting->find("all", array("conditions" => $conditionsset, "fields" => array("value")));
            $allvalues = array("limit" => "", "notification" => "", "tandc" => "");
            if (!empty($detailedsettings_array)) {
                $limitendorsement = $detailedsettings_array[0]["GlobalSetting"]["value"];
                $notificationendorsement = $detailedsettings_array[1]["GlobalSetting"]["value"];
                $tandcendorsement = $detailedsettings_array[2]["GlobalSetting"]["value"];
                $allvalues = array("limit" => $limitendorsement, "notification" => $notificationendorsement, "tandc" => $tandcendorsement);
            }
            $faqdata = $this->globalsettingFaq->find("all", array("order" => "updated DESC"));
            //====================get all Organizations to mail
            $params["conditions"] = array("status" => array(1));
            $params["fields"] = array("id", "name", "announcement_status");
            $params["order"] = "name ASC";

            $orgdata = $this->Organization->find("all", $params);
            //====================End get all Organizations to mail
            //
            //====================get all USers to mail
            $params["conditions"] = array("status" => array(1), "fname !=" => '');
            $params["fields"] = array("id", "fname", "lname");
            $params["order"] = "fname ASC";
            $userdata = $this->User->find("all", $params);
            //pr($userdata); exit;
            //====================End get all Users to mail
            //====================get all Department to mail
            $params["conditions"] = array("OrgDepartment.status" => array(1), "OrgDepartment.name !=" => '');
            $params["fields"] = array("OrgDepartment.id", "OrgDepartment.name", "organization_id", "Organization.name");
            $params["order"] = "OrgDepartment.name ASC";
            $params["joins"] = array(array(
                    'table' => 'organizations',
                    'alias' => 'Organization',
                    'type' => 'left',
                    'conditions' => array('OrgDepartment.organization_id = Organization.id')
            ));
            $deptdata = $this->OrgDepartment->find("all", $params);
            //pr($deptdata); exit;
            //====================End get all Department to mail
            //
            //====================get all Department to mail
            $params["conditions"] = array("Entity.status" => array(1), "Entity.name !=" => '');
            $params["fields"] = array("Entity.id", "Entity.name", "organization_id", "Organization.name");
            $params["order"] = "Entity.name ASC";
            $params["joins"] = array(array(
                    'table' => 'organizations',
                    'alias' => 'Organization',
                    'type' => 'left',
                    'conditions' => array('Entity.organization_id = Organization.id')
            ));

            $entitydata = $this->Entity->find("all", $params);
            //pr($entitydata); exit;
            //====================End get all Department to mail

            $formname = "";
            if ($this->request->is('post')) {
                $formname = $this->request->data["formname"];
                if ($formname == "generalsettings") {
//                    $enorsement_settings["limit"] = array(
//                        "name" => "Endorsements For Month",
//                        "key" => "Endorsement_key",
//                        "value" => $this->request->data['general']['value'],
//                    );
                    $enorsement_settings["limit"] = array(
                        "name" => "Total Endorsements For Month",
                        "key" => "endorsement_limit",
                        "value" => $this->request->data['general']['value'],
                    );


                    $enorsement_settings["notification"] = array(
                        "name" => "Notifications For Month",
                        "key" => "Notification_On",
                        "value" => $this->request->data['general']['notification'],
                    );
                    //$existinglimit = $this->GlobalSetting->findByKey("Endorsement_Key");
                    $existinglimit = $this->GlobalSetting->findByKey("endorsement_limit");
                    $existingnotifications = $this->GlobalSetting->findByKey("Notification_On");
                    if (!empty($existinglimit)) {
                        $this->GlobalSetting->id = $existinglimit["GlobalSetting"]["id"];
                        $enorsement_settings["limit"] = array("value" => $this->request->data['general']['value']);
                        $this->GlobalSetting->save($enorsement_settings["limit"]);
                    }
                    if (!empty($existingnotifications)) {
                        $this->GlobalSetting->id = $existingnotifications["GlobalSetting"]["id"];
                        $enorsement_settings["notification"] = array("value" => $this->request->data['general']['notification']);
                        $this->GlobalSetting->save($enorsement_settings["notification"]);
                    }
                    if (empty($existinglimit) && empty($existingnotifications)) {
                        $this->GlobalSetting->saveMany($enorsement_settings);
                    }

                    $allvalues = array(
                        "limit" => $this->request->data['general']['value'],
                        "notification" => $this->request->data['general']['notification'],
                        "tandc" => $allvalues["tandc"]
                    );
                    $this->Session->setFlash(__('Settings Saved'), 'default', array('class' => 'alert alert-warning'));
                } else if ($formname == "tandc") {
                    $content = $this->request->data['User']['tandc'];
                    $enorsement_settings["tandc"] = array(
                        "name" => "Terms And Conditions",
                        "key" => "tandc",
                        "value" => $content,
                    );
                    $existingtandc = $this->GlobalSetting->findByKey("tandc");
                    if (!empty($existingtandc)) {
                        $this->GlobalSetting->id = $existingtandc["GlobalSetting"]["id"];
                        $enorsement_settings["tandc"] = array("value" => $content);
                        $this->GlobalSetting->save($enorsement_settings["tandc"]);
                    }
                    if (empty($existingtandc)) {
                        $this->GlobalSetting->save($enorsement_settings["tandc"]);
                    }
                    $allvalues = array(
                        "limit" => $allvalues["limit"],
                        "notification" => $allvalues["notification"],
                        "tandc" => $this->request->data['User']['tandc']
                    );

                    if (isset($this->request->data['User']['notify'])) {
                        $this->Common->emailsfortermsandcoditions($content);
                    }
                    $this->Session->setFlash(__('End user license agreement saved.'), 'default', array('class' => 'alert alert-warning'));
                } else if ($formname == "mailingorganizations") {
//                    pr($this->request->data); exit;
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
                    $resultAnnounce = $this->Announcement->save($announcement);
                    $announcementID = $this->Announcement->id;
//                    pr($resultAnnounce);
//                    exit;
                    /*                     * ** */

                    $senderID = $loggedUserId;

                    if (isset($announcementID) && $announcementID != '') {
                        //=========common announcement feature for admin and superaadmin
                        $this->Common->announcementspostdata($organizationslist, $content, $newfilename, $usersList, $departmentList, $suborgList, $scheduled, $UTCTimeToPost, $announcementID, $senderID);
                        //exit;
                    } else {
                        $this->Session->setFlash(__('Unable to send announcements'), 'default', array('class' => 'alert alert-warning'));
                    }
                } else if ($formname == "announcementsorg") {



                    $organizationslist = array();
                    if (isset($this->request->data['User']["Organizations"])) {
                        $organizationslist = $this->request->data['User']["Organizations"];
                    }
                    if (!empty($organizationslist)) {
                        $this->Organization->updateAll(
                                array('announcement_status' => 1), array('id' => $organizationslist)
                        );
                        $this->Organization->updateAll(
                                array('announcement_status' => 0), array('id !=' => $organizationslist)
                        );
                    } else {
                        $this->Organization->updateAll(
                                array('announcement_status' => 0)
                        );
                    }
                }
                exec("wget -bqO- " . Router::url('/', true) . "/cron/globalemailcron &> /dev/null");
                //exec("wget -q " . Router::url('/', true) . "cron/globalemailcron 2> ".File, $outputOnly, $return_value);
                //$output = shell_exec("wget -bq " . Router::url('/', true) . "cron/globalemailcron");
                $this->redirect(array("controller" => "users", "action" => "setting"));
            }
            //$this->Invite->updateAll(array("invite_count" => "invite_count+1"), array("email" => $invitedMails));
//echo "Working"; exit;
            $this->set(compact('prev_page', 'allvalues', 'orgdata', 'faqdata', 'formname', 'userdata', 'deptdata', 'entitydata'));
            $this->set('authUser', $this->Auth->user());
        }
    }

    /* Added by Babulal Prasad @10-jan-2018 to edit or delete pending scheduled announcement * */

    public function pendingannouncement() {
        $loggeduser = $this->Auth->User();
        if ($this->Session->check('Auth.User.role') != "1" || $this->Session->check('Auth.User.role') != "2") {
            $this->Auth->logout();
            $this->redirect(array('action' => 'login'));
        } else {
            $role = $this->Auth->User('role');
            if ($role == 2) {
                $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
            }

            $this->loadModel("Announcement");
            $prev_page = Router::url($this->referer(), true);
            $announcemetnparams["fields"] = array("Announcement.*", "User.image", "concat(User.fname,' ',User.lname) as posted_user_name");
            $announcemetnparams["conditions"] = array("Announcement.status" => 'active', "Announcement.scheduled" => '1');
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

    public function announcementedit($id = '') {
        $loggeduser = $this->Auth->User();
        $loggedUserId = $loggeduser['id'];
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
            if ($role == 2) {
                $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
            }

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
            //====================get all Organizations to mail
            $params["conditions"] = array("status" => array(1));
            $params["fields"] = array("id", "name", "announcement_status");
            $params["order"] = "name ASC";
            $orgdata = $this->Organization->find("all", $params);

            //====================End get all Organizations to mail
            //
            //====================get all USers to mail
            $params["conditions"] = array("status" => array(1), "fname !=" => '');
            $params["fields"] = array("id", "fname", "lname");
            $params["order"] = "fname ASC";
            $userdata = $this->User->find("all", $params);
//            pr($userdata); exit;
            //====================End get all Users to mail
            //====================get all Department to mail
            $params["conditions"] = array("OrgDepartment.status" => array(1), "OrgDepartment.name !=" => '');
            $params["fields"] = array("OrgDepartment.id", "OrgDepartment.name", "organization_id", "Organization.name");
            $params["order"] = "OrgDepartment.name ASC";
            $params["joins"] = array(array(
                    'table' => 'organizations',
                    'alias' => 'Organization',
                    'type' => 'left',
                    'conditions' => array('OrgDepartment.organization_id = Organization.id')
            ));
            $deptdata = $this->OrgDepartment->find("all", $params);
            //pr($deptdata); exit;
            //====================End get all Department to mail
            //
            //====================get all Department to mail
            $params["conditions"] = array("Entity.status" => array(1), "Entity.name !=" => '');
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
                /*                 * ** */


                $senderID = $loggedUserId;
                //=========common announcement feature for admin and superaadmin
                $this->Common->updateannouncementspostdata($organizationslist, $content, $newfilename, $usersList, $departmentList, $suborgList, $reportType, $UTCTimeToPost, $announcementID, $senderID);
                //exit;
                exec("wget -bqO- " . Router::url('/', true) . "/cron/globalemailcron &> /dev/null");
                //exec("wget -q " . Router::url('/', true) . "cron/globalemailcron 2> ".File, $outputOnly, $return_value);
                //$output = shell_exec("wget -bq " . Router::url('/', true) . "cron/globalemailcron");
                $this->redirect(array("controller" => "users", "action" => "setting"));
            }
            //pr($detailedsettings_array); exit;
            $announcementData = $detailedsettings_array;
            //$this->Invite->updateAll(array("invite_count" => "invite_count+1"), array("email" => $invitedMails));
            $this->set(compact('prev_page', 'allvalues', 'orgdata', 'faqdata', 'formname', 'userdata', 'deptdata', 'entitydata', 'announcementData'));
            $this->set('authUser', $this->Auth->user());
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

            if ($deleteGlobalemail == 1) {
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

    public function clientinfo($id) {
        $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
        if ($this->Session->check('Auth.User.role') != "1") {
            $this->Auth->logout();
            $this->redirect(array('action' => 'login'));
        } else {
            $authUser = $this->Auth->User();
            $user_image = $this->Session->read('Auth.User.client_image');
            $userdata = $this->User->findById((int) $id);
            if (empty($userdata)) {
                $this->redirect(array('action' => 'index'));
            }
            $this->LoadModel("UserOrganization");
            // $conditions = array('Organization.status' => array(0, 1), 'Organization.admin_id' => $logged_in_user_id);
            // $conditions = array('Organization.status' => array(0, 1), 'UserOrganization.user_role' => 2,'UserOrganization.user_id' => $logged_in_user_id,'UserOrganization.status' => 1);
            $userorgdata = $this->UserOrganization->find("all", array('order' => 'Organization.created DESC', "conditions" => array("user_id" => $id, "user_role" => 2, 'UserOrganization.status' => 1)));
            $organization_id = array();
            foreach ($userorgdata as $uservalorg) {

                $organization_id[] = $uservalorg["UserOrganization"]["organization_id"];
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
                        'conditions' => array('Transactions.status' => 'submitted_for_settlement')
                    )
                ),
                'hasOne' => array('Subscription' => array(
                        'className' => 'Subscription',
                    ))
            ));
            $user_org_data = $this->Organization->find('all', array('order' => 'Organization.created DESC', 'conditions' => array('Organization.id' => $organization_id, 'Organization.status' => array(0, 1))));
            foreach ($user_org_data as $orgdata) {
                $target_id = $orgdata['Organization']['id'];
                $user_role = array(3, 4);
                //=======function to count no of users for desired role
                $results = $this->Common->getusersfororg($target_id, $user_role);
                $nooforgusers[$target_id] = $results;
                $userorg = $orgdata["UserOrganization"];
                //=total invitations accepted
                $totalinvitationsaccepted[$target_id] = $this->Common->userorgcounter($userorg);
                $invitation_accepted[$target_id] = $totalinvitationsaccepted[$target_id]["web"] + $totalinvitationsaccepted[$target_id]["app"];
                $invitations_array[$target_id] = $this->Common->invitations_fetching($orgdata);
                $invitation_pending[$target_id] = $invitations_array[$target_id]["invitations_pending"];
                $invitation_pending[$target_id]["web"] = $totalinvitationsaccepted[$target_id]["web"] + $invitation_pending[$target_id]["web"];
                $invitation_pending[$target_id]["app"] = $totalinvitationsaccepted[$target_id]["app"] + $invitation_pending[$target_id]["app"];
                $totalinvitations[$target_id] = array("invitation_accepted" => $invitation_accepted, "invitation_pending" => $invitation_pending);
                $pendingrequescounter[$target_id] = $this->OrgRequest->find("count", array("conditions" => array("organization_id" => $target_id, "status" => 0)));
                $endorsementformonth[$target_id] = $this->Endorsement->find("count", array("conditions" => array("month(created) = month(NOW())", "organization_id" => $target_id)));
            }

            //===========find no of users(endorsers)
            $noofclientusers = 0;
            if ($user_org_data) {
                $noofclientusers = array_sum($nooforgusers);
            }
            $this->set(compact('authUser', 'userdata', 'user_image', 'user_org_data', 'noofclientusers', 'nooforgusers', 'totalinvitations', 'invitations_array', 'pendingrequescounter', 'invitation_pending', 'invitation_accepted', 'endorsementformonth'));
        }
    }

    function createendorser($organization_id = "null") {
        $result = $this->Common->checkorgid($organization_id);
        //=============to redirect if orgid is wrong
        if ($result == "redirect") {
            $this->redirect(array("controller" => "organizations", "action" => "index"));
        }
        $errormsg = "";
        $country_code = 232;
        $authUser = $this->Auth->User();
        $nextendorserid = $this->nextId();
        //$client_image = $this->Session->read('Auth.User.client_image');
        $client_image = "";
        $jobtitles = $this->Common->getorgjobtitles($organization_id);
        $departments = $this->Common->getorgdepartments($organization_id);
        $entities = $this->Common->getorgentities($organization_id);
        $skills = $this->Common->getDefaultSkills(true, 2);
        $hobbies = $this->Common->getDefaultHobbies(true, 2);
        $skills = array_merge($skills, array("other" => "Add More Skills"));
        $hobbies = array_merge($hobbies, array("other" => "Add More Hobbies"));
        $listCountries = $this->Country->find('list', array('order' => 'Country.name', 'fields' => array('Country.id', 'Country.name')));
        $listState = $this->Common->liststate($country_code);
        if (!empty($this->request->data['User']['country'])) {
            //$listState = $this->State->find('list', array('conditions' => array('State.country_id' => trim($this->request->data['User']['country']))));
            $listState = $this->Common->liststate($this->request->data['User']['country']);
        }
        $organization_detail = $this->Organization->find("all", array("fields" => array("name", "secret_code"), "conditions" => array("id" => $organization_id)));
        $organization_name = $organization_detail[0]["Organization"]["name"];
        $organization_code = $organization_detail[0]["Organization"]["secret_code"];
        if ($this->request->is('post')) {
            if (isset($this->request->data["User"]["mobile_visible"])) {
                $this->request->data['User']['mobile_visible'] = 1;
            } else {
                $this->request->data['User']['mobile_visible'] = 0;
            }
            $statusConfig = Configure::read("statusConfig");
            $this->loadModel("Subscription");
            $this->loadModel("Email");
            $this->loadModel('UserOrganization');
            $this->loadModel('Invite');
            $this->loadModel('JoinOrgCode');

            $sendInvite = false;
            if (isset($this->request->data['User']['invitation']) && $this->request->data['User']['invitation'] == 1 && $this->request->data["User"]["status"] == 1) {
                $sendInvite = true;


//                $joinOrgCodeData = array(
//                    "email" => $this->request->data['User']['email'],
//                    "organization_id" => $organization_id,
//                    "code" => $joinOrgCode
//                );
            }
            $available_pool = 10;
            // get subscription info
            $subscriptiondata = $this->Subscription->findByOrganizationId($organization_id);

            if (!empty($subscriptiondata) && $subscriptiondata["Subscription"]["status"] == 1) {
                $available_pool += $subscriptiondata["Subscription"]["pool_purchased"];
            }
            // get active users
            $params['conditions'] = array("organization_id" => $organization_id, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
            $params['fields'] = array("COUNT(UserOrganization.user_id) as count");
            $userOrgStats = $this->UserOrganization->find("all", $params);
            $usercount = $userOrgStats[0][0]["count"];

            $email = $this->request->data["User"]["email"];

            /* Check if User Exist */
            $existstatus = $this->request->data["User"]["status"];
            $ucount = $usercount + 1;
            if ($ucount > $available_pool) {
                $existstatus = 0;
            }
            $pool_type = "free";
            if ($ucount > 10) {
                $pool_type = "paid";
            }
            $emailEncoded = base64_encode($email);
            $existingusers = $this->User->findByEmail($email);
            if (!empty($existingusers)) {
                $user_id = $existingusers["User"]["id"];
                $array_val = array(
                    "user_id" => $user_id,
                    "organization_id" => $organization_id,
                    "user_role" => $this->request->data["User"]["role"],
                    "entity_id" => $this->request->data["User"]["entity"],
                    "department_id" => $this->request->data["User"]["department"],
                    "job_title_id" => $this->request->data["User"]["job_title"],
                    "pool_type" => $pool_type,
                    "status" => $existstatus,
                    "joined" => 0,
                    "flow" => "web_invite",
                    "send_invite" => $sendInvite
                );
                $this->UserOrganization->create();
                $this->UserOrganization->save($array_val, false);
                $userOrgId = $this->UserOrganization->id;

                if ($this->request->data["User"]["status"] == 1) {
                    $this->loadModel('DefaultOrg');
                    $defaultOrg = $this->DefaultOrg->findByUserId($user_id);
                    if (empty($defaultOrg)) {
                        $defaultOrgData = array("user_id" => $user_id, "organization_id" => $organization_id, "status" => 1);
                        $this->DefaultOrg->save($defaultOrgData);
                    }
                }

                if ($this->request->data["User"]["status"] == 1 && $this->request->data["User"]["status"] == 1) {
                    $joinOrgCode = $this->Common->getJoinOrgCode($organization_id, $email, $user_id, $userOrgId);
//                    $this->JoinOrgCode->save($joinOrgCodeData);
//                    $email_var = $this->Common->functiontoinvite($this->request->data['User']["email"], $organization_id, $organization_name, $organization_code);

                    $noSwitch = false;
                    if (empty($defaultOrg)) {
                        $noSwitch = true;
                    }

                    $viewVars = array('fname' => $existingusers['User']['fname'], 'organization_name' => $organization_name, "join_code" => $joinOrgCode, "no_switch" => $noSwitch);

                    /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode($user_id);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $viewVars["pathToRender"] = $pathToRender;
                    /*                     * * */

                    $configVars = serialize($viewVars);
                    $subject = "Invitation to join nDorse";
                    $email_var = array("to" => $existingusers['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "invitation_admin_existing");
                    //foreach($email_var as $emails){
                    $this->Email->Create();
                    //$this->Email->save($emails);
                    $this->Email->save($email_var);
                    //  }
                }
                $this->Session->delete('Auth.User.org_id');
                $this->Session->delete('Auth.User.client_image');
                // $this->Session->write('Auth.User.inactiveuser', $existstatus);

                if ($this->request->data["User"]["status"] == 1 && $existstatus == 0) {
                    $_SESSION["useralert"] = "User account created successfully but is set to inactive since subscription limit is over";
                } else {
                    $_SESSION["useralert"] = "User account created successfully";
                }
//                $_SESSION["useralert"] = ($existstatus + 1);
                $this->redirect(array('controller' => 'organizations', 'action' => 'info', $organization_id));
                exit;
            }

            //If user not in system

            $this->request->data["User"]["dob"] = $this->Common->dateConvertServer($this->request->data["User"]["dob"]);
            $password_random = $this->Common->randompasswordgenerator(8);
            //$organization_name = $this->Organization->field("name", array("id" => $organization_id));
            $country_code = $this->request->data['User']['country'];
            $country = $this->Country->findById($country_code);
            if (!empty($country)) {
                $this->request->data['User']['country'] = $country['Country']['name'];
            }
            $this->request->data['User']['username'] = $this->request->data['User']['email'];
            //$this->request->data['User']['secret_code'] = md5(md5(uniqid() . $this->request->datadata['User']['email'] . time()));
            $this->request->data['User']['secret_code'] = $this->requestAction('/api/getSecretCode', array('user'));
            $department = $this->request->data['User']['department'];
            $jobtitle = $this->request->data['User']['job_title'];
            $entity = $this->request->data['User']['entity'];
            $skillsval = "";
            if (!empty($this->request->data['User']['skills'])) {
                $skillsarray = $this->request->data['User']['skills'];
                $this->request->data['User']['skills'] = $this->Common->trimminguserdata($skillsarray);
            }
            $hobbiesval = "";
            if (!empty($this->request->data['User']['hobbies'])) {
                $hobbiesarray = $this->request->data['User']['hobbies'];
                $this->request->data['User']['hobbies'] = $this->Common->trimminguserdata($hobbiesarray);
            }
            //$this->request->data['User']['skills'] = $skillsval;
            //$this->request->data['User']['hobbies'] = $hobbiesval;
            $this->User->setValidation('register');
            $this->User->set($this->request->data['User']);
            unset($this->User->validate['image']);
            if ($this->User->validates()) {
                $fname = $this->request->data["User"]["fname"];

                $this->request->data['User']['password'] = $password_random;
                $this->User->save($this->request->data['User']);
                $savedUserId = $this->User->id;

                $new_userorganization = array(
                    "user_id" => $savedUserId,
                    "organization_id" => $organization_id,
                    "entity_id" => $entity,
                    "department_id" => $department,
                    "job_title_id" => $jobtitle,
                    "user_role" => $this->request->data['User']['role'],
                    "is_default" => '1',
                    "pool_type" => $pool_type,
                    "status" => $existstatus,
                    "joined" => 0,
                    "flow" => "web_invite",
                    "send_invite" => $sendInvite
                );


                $this->UserOrganization->save($new_userorganization, false);
                $userOrgId = $this->UserOrganization->id;

                if ($this->request->data["User"]["status"] == 1) {
                    $this->loadModel('DefaultOrg');
                    $defaultOrgData = array("user_id" => $savedUserId, "organization_id" => $organization_id, "status" => 1);
                    $this->DefaultOrg->save($defaultOrgData);
                }

                if ($sendInvite) {
                    $joinOrgCode = $this->Common->getJoinOrgCode($organization_id, $email, $savedUserId, $userOrgId);
//                    $this->JoinOrgCode->save($joinOrgCodeData);

                    $viewVars = array('fname' => $fname, 'username' => $email, 'password' => $password_random, 'organization_name' => $organization_name, "join_code" => $joinOrgCode);

                    /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode($savedUserId);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $viewVars["pathToRender"] = $pathToRender;
                    /*                     * * */

                    $configVars = serialize($viewVars);
                    $subject = "Invitation to join nDorse";
                    $to = $email;
                    $emailvar = array("to" => $to, "subject" => $subject, "config_vars" => $configVars, "template" => "invitation_admin");
                    $this->Email->Create();
                    $this->Email->save($emailvar);
                }
//                $new_invite = array(
//                    "email" => $email,
//                    "organization_id" => $organization_id,
//                    "invite_count" => 1,
//                    "secret_code" => "",
//                    "flow" => "web",
//                );
//                $this->Invite->save($new_invite, false);

                $this->Session->delete('Auth.User.org_id');
                $this->Session->delete('Auth.User.client_image');
                //$this->Session->write('Auth.User.inactiveuser', $existstatus);
//                $_SESSION["useralert"] = ($existstatus + 1);
                if ($this->request->data["User"]["status"] == 1 && $existstatus == 0) {
                    $_SESSION["useralert"] = "User account created successfully but is set to inactive since subscription limit is over";
                } else {
                    $_SESSION["useralert"] = "User account created successfully";
                }
                $this->redirect(array('controller' => 'organizations', 'action' => 'info', $organization_id));
                exit;
            } else {
                $errors = $this->User->validationErrors;
                foreach ($errors as $error) {
                    $errormsg .= $error[0] . "<br/>";
                }
            }
        }
        $this->set(compact("nextendorserid", "client_image", "errormsg", "jobtitles", "departments", "entities", "skills", "hobbies", "listCountries", "listState", "authUser", "country_code", "organization_id"));
    }

    function usersfaq() {
        $this->loadModel('globalsettingFaq');
        $authUser = $this->Auth->User();
        $faqdata = $this->globalsettingFaq->find("all", array("order" => "updated DESC"));
        $this->set(compact('authUser', 'faqdata'));
    }

    public function testxls() {
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel/Classes/PHPExcel.php'));
        //include 'PHPExcel.php';
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        //header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="usersreports.xlsx"');
        $folderToSaveXls = '/var/www/html/ndorsedev/app/tmp';
        // Set properties
        $objPHPExcel->getProperties()->setCreator("Ndorse");
        $objPHPExcel->getProperties()->setLastModifiedBy("Ndorse");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHPExcel classes.");
        // Add some data
        // echo date('H:i:s') . " Add some data\n";
        $objPHPExcel->setActiveSheetIndex(0);
        //$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Hello');
        //$objPHPExcel->getActiveSheet()->SetCellValue('B2', 'world!');
        //$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
        //$objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        //$gdImage = imagecreatefromjpeg(WWW_ROOT . ORG_IMAGE_DIR . '/28.jpeg');
        $gdImage = imagecreatefrompng(WWW_ROOT . 'img/img1.png');
        // Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('Sample image');
        $objDrawing->setDescription('Sample image');
        $objDrawing->setImageResource($gdImage);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
        $objDrawing->setHeight(100);
        $objDrawing->setCoordinates('E1');
        //======set height of first column
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(80);
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        //$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
        //$objWriter->save($folderToSaveXls . '/test.xls');
        //echo date('H:i:s') . " Done writing file.\r\n";
        $objWriter->save('php://output');
        //$fp = @fopen( 'php://output', 'w' );


        exit();
    }

    public function addSuperAdmin() {
        $loggedinUser = $this->Auth->User();
        if ($loggedinUser['role'] != 1) {
            $this->redirect(array('controller' => 'organizations', 'action' => 'index'));
        }

        if ($this->request->is('post')) {
            $user = $this->request->data;
            $user['User']['role'] = 1;
            $user['User']['secret_code'] = $this->getSecretCode("user");
            $user['User']['username'] = $user['User']['email'];
//            pr($user);die;
            $this->User->setValidation('register');
            $this->User->set($user);
            if ($this->User->validates()) {
//                echo 'here';die;
                if ($this->User->save()) {
                    $this->request->data = array();
                    $this->Session->setFlash(__('Superadmin created successfully'), 'default', array('class' => 'alert alert-warning'));
                } else {
                    $this->Session->setFlash(__('Unable to create superadmin'), 'default', array('class' => 'alert alert-warning'));
                }
            } else {
                $errors = $this->User->validationErrors;
                $errorsDisplay = "";

                foreach ($errors as $key => $error) {
                    $errorsDisplay .= $error[0] . "<br>";
                }

                $errorsDisplay = rtrim($errorsDisplay, "<br>");

//                $this->Session->setFlash(__($errorsDisplay), 'default', array('class' => 'alert alert-warning'));
            }
        }
    }

    public function forgotPassword() {
        $this->loadModel("PasswordCode");
        if (!filter_var($this->request->data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array("success" => false, "msg" => "Invalid email address. Please check."));
            exit;
        }

        $userData = $this->User->find('first', array('conditions' => array('User.email' => $this->request->data['email'])));

        if (empty($userData)) {
            echo json_encode(array("success" => false, "msg" => "This email is not registered."));
            exit;
        }

        $secretCode = $this->getForgotSecretCode();

        $data = array();
        $data['email'] = $this->request->data['email'];
        $data['code'] = $secretCode;
        if ($this->PasswordCode->save($data)) {
            exec("wget -bqO- " . Router::url('/', true) . "/cron/forgotPasswordEmails &> /dev/null");
            echo json_encode(array("success" => true, "msg" => "Email has been sent with verification code to reset password."));
            exit;
        } else {
            echo json_encode(array("success" => false, "msg" => "Unable to send verification code. Please try again or contact us at support@ndorse.net."));
            exit;
        }
    }

    public function setPassword() {
        $this->loadModel("PasswordCode");
        $this->layout = null;
        if ($this->request->is('post')) {
            $passCode = $this->PasswordCode->find("first", array("conditions" => array("code" => $this->request->data['verification_code'])));
//            $passCode = $this->PasswordCode->find("first", array("conditions" => array("code" => $this->request->data['verification_code'], "email" => $this->request->data['email'])));

            if (empty($passCode)) {
                echo json_encode(array("success" => false, "msg" => "Verification code did not match. Please re-try!"));
                exit;
            } else if ($passCode['PasswordCode']['status'] != 0) {
                echo json_encode(array("success" => false, "msg" => "Verification code has already been used. Go to reset password."));
                exit;
            }

            $this->User->set($this->request->data);
            $this->User->setValidation('reset_password');

            if ($this->User->validates()) {
                if ($this->User->updateAll(array("password" => "'" . $this->User->getHashPassword($this->request->data['password']) . "'"), array("email" => $passCode['PasswordCode']['email']))) {
                    $this->PasswordCode->id = $passCode['PasswordCode']['id'];
                    $this->PasswordCode->saveField("status", 1);

                    echo json_encode(array("success" => true, "msg" => "Password reset successfully!"));
                    exit;
                } else {
                    echo json_encode(array("success" => true, "msg" => "Unable to reset password. Please try again or contact support@ndorse.net."));
                    exit;
                }
            } else {
                
            }
        }
    }

    public function resetpassword() {

        $errormsg = "";
        $successmsg = "";

        $loggedinUser = $this->Auth->user();

        if ($this->request->is("post")) {

            if (isset($this->request->data['User']['current_password']) && $this->request->data['User']['current_password'] != "") {
                // print_r($this->request->data['User']);
                $current_password = $this->Auth->password($this->request->data['User']['current_password']);

                $userInfo = $this->User->findById($loggedinUser["id"]);
                $userpasswod = $userInfo['User']['password'];
                //echo $current_password;
                // echo "<hr>";
                //echo $userpasswod;
                // echo "<hr>";
                if ($userpasswod != $current_password) {
                    $errormsg = "Current password was not entered correctly.";
                    // $this->redirect(array('controller' => 'users', 'action' => 'changePassword'));
                } else {

                    $this->User->set($this->request->data['User']);
                    // edit
                    $this->request->data['User']["id"] = $loggedinUser["id"];

                    $this->User->setValidation('reset_password');
                    if ($this->User->validates()) {
                        $password = $this->request->data['User']["password"];
                        if ($this->User->save($this->request->data['User'])) {

                            // send email to user for change password
                            $successmsg = 'Password updated successfully.';
                        } else {
                            
                        }
                    } else {
//                        // $errors = $this->User->validationErrors;
//                        $errors = $this->User->validationErrors;
//                        $errorsArray = array();
//
//                        foreach ($errors as $key => $error) {
//                            $errorsArray[$key] = $error[0];
//                        }
//
//                        $this->set(array(
//                            'result' => array("status" => false
//                                , "msg" => "Errors!", 'errors' => $errorsArray),
//                            '_serialize' => array('result')
//                        ));
                    }
                }
            } else {
                $errormsg = "Current password required";
            }
        }

        if ($successmsg != "") {
            $this->Session->setFlash(__($successmsg), 'default', array('class' => 'alert alert-warning'));
        } else if ($errormsg != "") {
//            echo 'here';die;
            $this->Session->setFlash(__($errormsg), 'default', array('class' => 'alert alert-warning'));
        }

        $this->set('jsIncludes', array('change_password'));
        $this->set(compact("successmsg", "errormsg"));
    }

    function unsubscribe() {
        if (isset($this->request->params['key']) && $this->request->params['key'] != '') {
            $userid = base64_decode($this->request->params['key']);
            $this->User->id = $userid;
            $this->User->saveField('notification_unsubscribed', 1);
        } else {
            $this->redirect(array('action' => 'index'));
        }
    }

    public function customerportalsetting($orgid) {
        $loggedinUser = $this->Auth->User();
        $this->set('title_for_layout', "Customer Feedback");
        if (isset($orgid)) {
            $this->loadModel('Organization');
            if ($this->request->is('post')) {
                $orgID = $this->request->data['Organization']['id'];
                $cpLogo = $this->request->data['Organization']['image'];
                $cpShowCoreValues = $this->request->data['Organization']['cp_show_core_values'];
                $cpShowComment = $this->request->data['Organization']['cp_show_comment'];

                $updated = $this->Organization->updateAll(
                        array('cp_logo' => "'" . $cpLogo . "'", 'cp_show_comment' => $cpShowComment, 'cp_show_core_values' => $cpShowCoreValues), array('id' => $orgID)
                );

                if ($updated) {
                    $this->request->data = array();
                    $this->Session->setFlash(__('Customer Portal setting updated successfully'), 'default', array('class' => 'alert alert-warning'));
                } else {
                    $this->Session->setFlash(__('Unable to update setting'), 'default', array('class' => 'alert alert-warning'));
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
            $orgDetail = $this->Organization->findById($orgid);
            //pr($orgDetail);
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

            $totalrecords = $this->Endorsement->find("count", array("conditions" => array("organization_id" => $orgid, "Endorsement.type" => 'guest', "Endorsement.status = 0")));
            $orgcorevaluesandcode = $this->Common->getorgcorevaluesandcode($orgid);
//            $allvalues = array("orgcorevaluesandcode" => $orgcorevaluesandcode);
            $departments = $this->Common->getorgdepartments($orgid);
            $entities = $this->Common->getorgentities($orgid);
            $allvalues = array("department" => $departments, "entities" => $entities, "orgcorevaluesandcode" => $orgcorevaluesandcode);

            $this->set('orgDetail', $orgDetail);
        }
        $this->set(compact('userdetails', 'allvalues', "totalrecords"));
        $this->set('jsIncludes', array('customerportal.js'));
        $this->set('authUser', $this->Auth->user());
    }

    function adminsearch() {

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
            $this->DefaultOrg->unbindModel(array('belongsTo' => array('Organization', 'User')));
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
                'conditions' => array('DefaultOrg.status' => 1, 'UserOrganization.status' => 1, 'User.status' => 1, 'User.email' => 'babulal.arcgate@gmail.com'),
            ));
//            pr($totalUsersCount); exit;
            $totalrecords = isset($totalUsersCount[0][0]['total_records']) ? $totalUsersCount[0][0]['total_records'] : 0;


            /*             * ********************************* */


            //====================get all USers to mail
            $params["conditions"] = array("status" => array(1), "fname !=" => '');
            $params["fields"] = array("id", "fname", "lname");
            $params["order"] = "fname ASC";

            $userdata = $this->User->find("all", $params);

//            pr($userdata);
//            exit;
            //====================End get all Users to mail
        }

        $this->set(compact('totalrecords'));
        $this->set("authUser", $loggeduser);
    }

}
