<?php

class ClientController extends AppController {

    public $components = array('RequestHandler', "Auth", "Common", "Session", "Apicalls");

    public function beforeFilter() {
        parent::beforeFilter();

        $this->layout = "clientDefault";
        $loggedinUser = $this->Auth->user();
        if (!empty($loggedinUser) && isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'client') {
            $this->layout = "clientlayout";
        }

        $this->Auth->allow('register', 'login', 'logout', 'forgotPassword', "verification", "index", "tnc", "recoverUsername", "faq", "fbLogin", "linkedinLogin", "googlelogin", "google_login", "setPassword");
    }

    public function login() {
        $loggedinUser = $this->Auth->user();
        if (!empty($loggedinUser) && isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'client') {
            if (!$loggedinUser['profile_updated']) {
                $this->redirect(array('controller' => 'client', 'action' => 'editprofile'));
            } else if (!isset($loggedinUser['current_org']) || $loggedinUser['current_org']->joined == 0) {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            } else {
                $this->redirect(array('controller' => 'endorse', 'action' => 'index'));
            }
        }
//        else if ($this->Cookie->read("remember_me_endorse_cookie")) {
//            $remembermecookie = $this->Cookie->read("remember_me_endorse_cookie");
//            $postData = array('token' => $remembermecookie);
//
//            $response = $this->Apicalls->curlpost("renewSession.json", $postData);
//            $response = json_decode($response);
//            $response = $response->result;
//
//            if ($response->status == 1) {
//                $userData = (array) $response->data;
//                $userData['portal'] = 'client';
//                $userData['org_updates'] = (array) $userData['org_updates'];
//                $this->Session->write('Auth.User', $userData);
//                //set last login typ[e cookie
//                $this->Cookie->write("portal_cookie", "client", true, "1 week");
//                if (!$userData['profile_updated']) {
//                    $this->redirect(array('controller' => 'client', 'action' => 'profile'));
//                } else if (!isset($userData['current_org'])) {
//                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
//                } else {
//                    $this->redirect(array('controller' => 'endorse', 'action' => 'index'));
//                }
//            } else {
//                $this->Cookie->delete('remember_me_endorse_cookie');
//                $this->Cookie->delete('portal_cookie');
//                $this->Session->setFlash(__($response->msg), 'default', array('class' => 'alert alert-warning'));
//                $this->redirect(array('controller' => 'client', 'action' => 'login'));
//            }
//        } 
        else if ($this->request->is('post')) {
            if ($this->Session->check('Auth.User')) {
//                $this->Cookie->delete("remember_me_cookie");
                $this->Auth->logout();
            }

            $postData = $this->request->data['User'];

            $response = $this->Apicalls->curlpost("login.json", $postData);
           // pr($response); exit;
            $response = json_decode($response);
            $response = $response->result;
            if ($response->status == 1) {
                $userData = (array) $response->data;
                $userData['portal'] = 'client';
                if (isset($userData['org_updates'])) {
                    $userData['org_updates'] = (array) $userData['org_updates'];
                }
//                pr($userData);die;
                $this->Session->write('Auth.User', $userData);
                if (isset($userData['org_updates']) && ($userData['org_updates']['org_status'] != 'active' || $userData['org_updates']['user_status'] != "active")) {
                    $this->Session->write('from_login', true);
                }

                //set last login typ[e cookie
                $this->Cookie->write("portal_cookie", "client", true, "1 week");
//                echo 'here';die;
                //Set token in cookie and 
                if (isset($this->request->data['User']['rememberme']) && $this->request->data['User']['rememberme'] == 1) {
                    $this->Cookie->write("remember_me_endorse_cookie", $this->request->data['User'], true, "1 week");
                } else {
                    if ($this->Cookie->read("remember_me_endorse_cookie")) {
                        $this->Cookie->delete("remember_me_endorse_cookie");
                    }
                }
                //redirect to some page

                if (!$userData['profile_updated']) {
                    $this->redirect(array('controller' => 'client', 'action' => 'editprofile'));
                } else if (!isset($userData['current_org']) || $userData['current_org']->joined == 0) {
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                } else {
                    $this->redirect(array('controller' => 'endorse', 'action' => 'index'));
                }
            } else {
                if (isset($response->msg)) {
                    $errorMsg = $response->msg;
                    $this->set('errorMsg', $errorMsg);
                } else {
                    $this->Session->setFlash(__("Unable to login. Please try after sometime"), 'default', array('class' => 'alert alert-warning'));
                    $this->redirect(array('controller' => 'client', 'action' => 'login'));
                }
            }
        }

        if ($this->Cookie->read("remember_me_endorse_cookie")) {
            $remembermeCookie = $this->Cookie->read("remember_me_endorse_cookie");
            $this->request->data['User'] = $remembermeCookie;
        }

        //Set data for third party integration 
        $this->setfbData();
        $this->setGoogleData();
        $this->setLinkedInData();

        unset($_SESSION["tp_profile"]);

        $this->set('jsIncludes', array('register', "loginCommon"));
    }

    private function setfbData() {
        App::import('Vendor', 'Facebook/facebook');

        $fbConfig = Configure::read("fbConfig");
        $this->Facebook = new Facebook($fbConfig);
        $fbPermissions = Configure::read("fbPermissions");
        $this->set('fbLoginUrl', $this->Facebook->getLoginUrl(array('scope' => $fbPermissions, 'redirect_uri' => Router::url(array('controller' => 'client', 'action' => 'fbLogin'), true))));
    }

    public function logout() {
        $loggedinUser = $this->Auth->user();
        $postData = array("token" => $loggedinUser['token']);
        $response = $this->Apicalls->curlpost("logout.json", $postData);
        $response = json_decode($response);
        $response = $response->result;

        if ($response->status == 1) {
            $this->Auth->logout();
//            $this->Cookie->delete("remember_me_endorse_cookie");
            $this->Cookie->delete("portal_cookie");
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
    }

    public function setOrg() {
        $this->set('jsIncludes', array('createorgclient'));
        $this->set('noLeftMenu', true);
        $termsMessage = $this->Session->read("Message.flash.message");
        $this->Session->delete("Message.flash");
        $this->set('termsMessage', $termsMessage);
    }

    public function inactiveOrg() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('from_login')) {
            $this->set("alertMsg", $loggedinUser['org_updates']['msg']);
            $this->Session->delete('from_login');
        }

        if (isset($loggedinUser['org_updates']) && $loggedinUser['org_updates']['org_status'] == 'active' && $loggedinUser['org_updates']['user_status'] == "active") {
            $this->redirect(array('controller' => 'endorse'));
        }
    }

    public function index() {
        $loggedinUser = $this->Auth->user();
        if (empty($loggedinUser)) {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        } else {
            if (isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'client') {
                if (!$loggedinUser['profile_updated']) {
                    $this->redirect(array('controller' => 'client', 'action' => 'editprofile'));
                } else if (!isset($loggedinUser['current_org']) || $loggedinUser['current_org']->joined == 0) {
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                } else {
                    $this->redirect(array('controller' => 'endorse', 'action' => 'index'));
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'login'));
            }
        }
    }

    public function register() {
        $loggedinUser = $this->Auth->user();
        if (!empty($loggedinUser) && isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'client') {
            if (!$loggedinUser['profile_updated']) {
                $this->redirect(array('controller' => 'client', 'action' => 'editprofile'));
            } else if (!isset($loggedinUser['current_org']) || $loggedinUser['current_org']->joined == 0) {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            } else {
                $this->redirect(array('controller' => 'endorse', 'action' => 'index'));
            }
        } else {
            if ($this->request->is('post')) {
                $registeredEmail = $this->Session->read('register');
                if (empty($registeredEmail) && isset($this->request->data['User'])) {
                    $postdata = $this->request->data['User'];
                    $response = $this->Apicalls->curlpost("sendVerification.json", $postdata);
                    $response = json_decode($response);
                    $response = $response->result;
                    $msg = $response->msg;

                    if ($response->status == 1) {
                        $this->Session->write('register', $this->request->data['User']['email']);
                        $this->set('successMsg', $msg);
                    } else {
                        $this->set('errorMsg', $msg);
                    }
                } else if (!empty($registeredEmail) && isset($this->request->data['Verification']['verification_code'])) {
                    $postdata = $this->request->data['Verification'];
                    $postdata['email'] = $registeredEmail;
                    $response = $this->Apicalls->curlpost("register.json", $postdata);
                    $response = json_decode($response);
                    $response = $response->result;

                    if ($response->status == 1) {
                        $userData = (array) $response->data;
                        $userData['portal'] = 'client';
                        $this->Cookie->write("portal_cookie", "client", true, "1 week");
                        $this->Session->write('Auth.User', $userData);
//                        pr($this->Auth->user());die;
                        $this->redirect(array('controller' => 'client', 'action' => 'editprofile'));
                    } else {
                        $msg = $response->msg;
                        $this->set('errorMsg', $msg);
                    }
                } else if (isset($this->request->data['Verification']['verification_code'])) {
                    $this->Session->setFlash(__('Session expired. Please try again.'), 'default', array('class' => 'alert alert-warning'));
                }
            } else {
                $this->Session->write('register', "");
            }

            //Set data for third party integration 
            $this->setfbData();
            $this->setGoogleData();
            $this->setLinkedInData();
        }

        unset($_SESSION["tp_profile"]);

        $this->set('jsIncludes', array('register'));
    }

    public function joinanorganization() {
        if ($this->Session->check('Auth.User')) {
            $loggedinUser = $this->Auth->user();

            if (isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'client') {
                $termsAccept = isset($loggedinUser['terms_accept']) ? $loggedinUser['terms_accept'] : 0;
                if (!$termsAccept) {
                    $this->Session->setFlash(__('Accept End User License Agreement'), 'default', array('class' => 'alert alert-warning'));
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }

                $type = "public";
                $postdata = array("token" => $loggedinUser["token"], "type" => $type, "limit" => 15);
                $jsondata = $this->Apicalls->curlpost("getAllOrganization.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);
                //$orgdata = isset($jsondatadecoded["result"]["data"]) ? $jsondatadecoded["result"]["data"] : $jsondatadecoded["result"]["msg"];
                if (isset($jsondatadecoded["result"]["data"])) {
                    $orgdata = $jsondatadecoded["result"]["data"];
                } else {
                    $this->Session->setFlash(__($jsondatadecoded["result"]["msg"]), 'default', array('class' => 'alert alert-warning'));
                    $this->redirect($this->Auth->logout());
                }
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
        $this->set('jsIncludes', array('joinorg'));
        $this->set('MenuName', 'Join Organization');
        $this->set(compact("orgdata", "type", 'loggedinUser'));
    }

    public function profile($id = 0) {
        $errormsg = "";
        $successmsg = "";
        if ($this->Session->check('Auth.User')) {
            if ($this->Session->read('successmessage') != "") {
                $successmsg = $this->Session->read('successmessage');
            }
            if ($successmsg != "") {
                $this->Session->write('successmessage', "");
            }
            $loggedinUser = $this->Auth->user();
            //pr($loggedinUser);
            if (isset($loggedinUser['current_org'])) {
                $this->Session->write('current_org', (array) $loggedinUser['current_org']);
            } else {
                $this->Session->write('current_org', array());
            }
            //pr($this->Session->read('current_org'));
            //pr((array)$loggedinUser['current_org']);
            if (isset($loggedinUser['current_org'])) {
                $current_org = $loggedinUser['current_org']->id;
            } else {
                $current_org = 0;
            }

            $user_id = $loggedinUser["id"];
            if (is_numeric($id) && $id > 0) {
                $user_id = $id;
            }
            $postdata = array("token" => $loggedinUser["token"], "user_id" => $user_id, "org_id" => $current_org);
            $jsondata = $this->Apicalls->curlpost("getProfile.json", $postdata);
//            pr($jsondata);
            //exit;
            $jsondatadecoded = json_decode($jsondata, true);

            //$orgdata = isset($jsondatadecoded["result"]["data"]) ? $jsondatadecoded["result"]["data"] : $jsondatadecoded["result"]["msg"];
            if (isset($jsondatadecoded["result"]["data"])) {
                $profiledata = $jsondatadecoded["result"]["data"]["user_data"];
                $badgesData = $jsondatadecoded["result"]["data"]["badges"];
                $coreValuesData = $jsondatadecoded["result"]["data"]["core_value"];
                $statesdata = $jsondatadecoded["result"]["data"]["endorse_count"];
            } else {
                $this->Session->setFlash(__($jsondatadecoded["result"]["msg"]), 'default', array('class' => 'alert alert-warning'));
                $this->redirect($this->Auth->logout());
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('MenuName', 'My Profile');
        $logindata = $loggedinUser;
        $this->set(compact("profiledata", "logindata", "successmsg", "coreValuesData", "badgesData", "statesdata"));
    }

    public function resetpassword() {
        $errormsg = "";
        $successmsg = "";
        if ($this->Session->check('Auth.User')) {
            $loggedinUser = $this->Auth->user();
            if (isset($this->request->data["User"]["current_password"]) && $this->request->data["User"]["current_password"] != "") {
                $postdata = array("token" => $loggedinUser["token"], "current_password" => $this->request->data["User"]["current_password"], "password" => $this->request->data["User"]["password"], "confirm_password" => $this->request->data["User"]["confirm_password"]);
                $jsondata = $this->Apicalls->curlpost("resetmypassword.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);

                if ($jsondatadecoded["result"]["status"]) {
                    $this->Session->write('successmessage', $jsondatadecoded["result"]["msg"]);
                    $this->redirect(array('controller' => 'client', 'action' => 'profile'));
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                }
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
        $this->set('jsIncludes', array('change_password'));
        $this->set('MenuName', 'Change Password');

        $this->set(compact("successmsg", "errormsg"));
    }

    public function editprofile() {
        // print_r($_SESSION);exit;
        $errormsg = "";
        $successmsg = "";
        if ($this->Session->check('Auth.User')) {

            $loggedinUser = $this->Auth->user();

            if (!isset($loggedinUser['profile_updated']) || !$loggedinUser['profile_updated']) {
                $this->set("noLeftMenu", true);
                $this->set('MenuName', 'Complete your profile');
            } else {
                $this->set('MenuName', 'Edit Profile');
            }

            $postdata = array("token" => $loggedinUser["token"], "user_id" => $loggedinUser["id"]);
            $jsondata = $this->Apicalls->curlpost("getProfile.json", $postdata);
            $getdata = array("type" => "countries");
            $countrydata = $this->Apicalls->curlget("getPredefinedValues.json", $getdata);


            $data = json_decode($countrydata, true);
            $countrydata = $data["result"]["data"]["country"];
            $default_country = $data["result"]["data"]["default_country"];
            $countryarray = array();
            $statearray = array();
            foreach ($countrydata as $key => $val) {
                $countryarray[$key] = $key;
                $valuarray = array();
                foreach ($val as $value) {
                    $valuarray[$value] = $value;
                }
                $statearray[$key] = $valuarray;
            }

            //echo "<hr>";

            $jsondatadecoded = json_decode($jsondata, true);

            //$orgdata = isset($jsondatadecoded["result"]["data"]) ? $jsondatadecoded["result"]["data"] : $jsondatadecoded["result"]["msg"];
            if (isset($jsondatadecoded["result"]["data"])) {

                $profiledata = $jsondatadecoded["result"]["data"]["user_data"];

                if (isset($this->request->data["User"]["fname"]) && $this->request->data["User"]["fname"] != "") {

                    $imagedata = "";

                    if ($this->request->data["User"]["Userphoto"]["tmp_name"] != "") {
                        //======converting the image to base 64
                        $filepath = $this->request->data["User"]["Userphoto"]["tmp_name"];
                        $type = pathinfo($filepath, PATHINFO_EXTENSION);
                        $data = file_get_contents($filepath);
                        $base64 = base64_encode($data);
                        $imagedata = $base64;
                    } elseif (isset($_SESSION["tp_profile"]["image"])) {
                        //$this->request->data["User"]["Userphoto"] = $_SESSION["tp_profile"]["image"];
                        //$type = pathinfo($_SESSION["tp_profile"]["image"], PATHINFO_EXTENSION);
                        $data = file_get_contents($_SESSION["tp_profile"]["image"]);
                        $base64 = base64_encode($data);
                        $imagedata = $base64;
                    }

                    $skills = $hobies = "";
                    if (!empty($this->request->data["User"]["skills"])) {
                        $skillarray = $this->request->data["User"]["skills"];
                        //print_r($skillarray);
                        foreach ($skillarray as $skillvals) {
                            // echo $skillvals;
                            if (trim($skillvals) != "" && trim($skillvals) != "other") {
                                if ($skills != "") {
                                    $skills.="," . $skillvals;
                                } else {
                                    $skills = $skillvals;
                                }
                            }
                        }
                        //$skills = implode(",", $this->request->data["User"]["skills"]);
                    }
//print_r($this->request->data["User"]["hobbies"]);
                    if (!empty($this->request->data["User"]["hobbies"])) {
                        $hobbiesarray = $this->request->data["User"]["hobbies"];
                        foreach ($hobbiesarray as $lvals) {
                            if (trim($lvals) != "" && trim($lvals) != "other") {
                                if ($hobies != "") {
                                    $hobies.="," . $lvals;
                                } else {
                                    $hobies = $lvals;
                                }
                            }
                        }
                        // $hobies = implode(",", $this->request->data["User"]["hobbies"]);
                    }
                    $dobdate = "";

                    if (isset($this->request->data["User"]["dob"]) && $this->request->data["User"]["dob"] != "") {
                        $dobdate = explode("-", $this->request->data["User"]["dob"]);
                        $dobdate = mktime(0, 0, 0, $dobdate[0], $dobdate[1], $dobdate[2]);
                        $dobdate = date("Y-m-d", $dobdate);
                    }

                    $country = $state = $city = $zip = "";
                    if (isset($this->request->data["User"]["country"]) && $this->request->data["User"]["country"] != "") {
                        $country = $this->request->data["User"]["country"];
                    }
                    if (isset($this->request->data["User"]["state"]) && $this->request->data["User"]["state"] != "") {
                        $state = $this->request->data["User"]["state"];
                    }

                    if (isset($this->request->data["User"]["city"]) && $this->request->data["User"]["city"] != "") {
                        $city = $this->request->data["User"]["city"];
                    }
                    if (isset($this->request->data["User"]["zip"]) && $this->request->data["User"]["zip"] != "") {
                        $zip = $this->request->data["User"]["zip"];
                    }


                    $savedata = array("token" => $loggedinUser["token"], "fname" => $this->request->data["User"]["fname"], "lname" => $this->request->data["User"]["lname"], "about" => $this->request->data["User"]["about"], "street" => $this->request->data["User"]["street"], "mobile" => $this->request->data["User"]["mobile"],
                        "skills" => $skills, "hobbies" => $hobies, "dob" => $dobdate,
                        "country" => $country, "state" => $state, "city" => $city, "zip" => $zip);
                    if ($imagedata != "") {
                        $savedata["image"] = $imagedata;
                    } elseif ($this->request->data["User"]["image"] == "") {
                        $savedata["image"] = "";
                    }
//print_r($savedata);exit;
                    $jsondata = $this->Apicalls->curlpost("saveprofile.json", $savedata);

                    if (isset($_SESSION["tp_profile"]["fname"])) {
                        unset($_SESSION["tp_profile"]);
                    }
                    $jsondatadecoded = json_decode($jsondata, true);

                    if ($jsondatadecoded["result"]["status"]) {
                        $profiledata = $jsondatadecoded["result"]["data"];
                        $this->Session->write('Auth.User.image', $profiledata["image"]);
                        $this->Session->write('Auth.User.fname', $profiledata["fname"]);
                        $this->Session->write('Auth.User.lname', $profiledata["lname"]);
//                        $this->Session->write('Auth.User.profile_updated', true);
                        //   $this->Session->write('Auth.User', $profiledata);
                        //pr($loggedinUser); exit;
                        if (!isset($loggedinUser['profile_updated']) || !$loggedinUser['profile_updated']) {
                            $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                        } else {
                            $this->redirect(array('controller' => 'client', 'action' => 'profile'));
                        }
                        $successmsg = $jsondatadecoded["result"]["msg"];
                        $profiledata = $jsondatadecoded["result"]["data"];
                        $this->request->data["User"]["Userphoto"] = $profiledata["image"];
                    } else {
                        $errormsg = $jsondatadecoded["result"]["msg"];
                    }
                } else {

                    $this->request->data["User"] = $profiledata;
                    if (isset($_SESSION["tp_profile"]["image"])) {
                        $this->request->data["User"]["Userphoto"] = $_SESSION["tp_profile"]["image"];
                    } elseif (isset($profiledata["image"])) {
                        $this->request->data["User"]["Userphoto"] = $profiledata["image"];
                    } else {
                        $this->request->data["User"]["Userphoto"] = "";
                    }
                    if (isset($_SESSION["tp_profile"]["fname"])) {
                        $this->request->data["User"]["fname"] = $_SESSION["tp_profile"]["fname"];
                    }
                    if (isset($_SESSION["tp_profile"]["lname"])) {
                        $this->request->data["User"]["lname"] = $_SESSION["tp_profile"]["lname"];
                    }
                    $skills = $hobies = "";
                    if (isset($profiledata['skills'])) {
                        $skills = $profiledata['skills'];
                    }
                    if (isset($profiledata['hobbies'])) {
                        $hobies = $profiledata['hobbies'];
                    }
                    // $skills = $profiledata['skills'];
                    //$hobies = $profiledata['hobbies'];
                }


                $skill = $this->Common->getDefaultSkills();
                $selectedskills = array();
                if (isset($profiledata['skills']) && $profiledata['skills'] != "") {
                    $selectedskills = explode(",", $skills);
                    $selectedskills = array_map("trim", $selectedskills);
                    foreach ($selectedskills as $skillsselected) {
                        if (!in_array($skillsselected, $skill)) {
                            $skill = array_merge($skill, array($skillsselected => $skillsselected));
                        }
                    }
                }

                $selectedhobbies = array();
                $hobbies = $this->Common->getDefaultHobbies();
                if (isset($profiledata['hobbies']) && $profiledata['hobbies'] != "") {
                    $selectedhobbies = explode(",", $hobies);
                    $selectedhobbies = array_map("trim", $selectedhobbies);
                    foreach ($selectedhobbies as $hobbiesselected) {
                        if (!in_array($hobbiesselected, $hobbies)) {
                            $hobbies = array_merge($hobbies, array($hobbiesselected => $hobbiesselected));
                        }
                    }
                }


                $skill = array_merge($skill, array("other" => "Add More Skills"));
                $hobbies = array_merge($hobbies, array("other" => "Add More Hobbies"));
            } else {
                $this->Session->setFlash(__($jsondatadecoded["result"]["msg"]), 'default', array('class' => 'alert alert-warning'));
                $this->redirect($this->Auth->logout());
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
        $this->set('jsIncludes', array('profile'));
        //  $this->set(compact("profiledata"));

        $this->set(compact("profiledata", "skill", "hobbies", "selectedskills", "selectedhobbies", "successmsg", "errormsg", "countryarray", "statearray", "default_country"));
    }

    public function tnc() {
        if ($this->Session->check('Auth.User')) {
            $layout = "default";
            $response = $this->Apicalls->curlget("termsConditions.json", array("is_web" => 1));
        } else {
            $this->layout = "ajax";
            $layout = "ajax";
            $response = $this->Apicalls->curlget("termsConditions.json", array());
        }
        $response = json_decode($response);
        $response = $response->result;
        $this->set('data', $response->data);
        $this->set('layout', $layout);
    }

    public function myorganizations() {
        if ($this->Session->check('Auth.User')) {
            $loggedinUser = $this->Auth->user();
            if (isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'client') {
                $defaultorg = "";
                $type = "endorser";
                $postdata = array("token" => $loggedinUser["token"], "type" => $type, "limit" => 15);
                $jsondata = $this->Apicalls->curlpost("getAllOrganization.json", $postdata);
//                pr($jsondata); exit;
                $jsondatadecoded = json_decode($jsondata, true);
                if (isset($jsondatadecoded["result"]["data"])) {
                    $orgdata = $jsondatadecoded["result"]["data"];
                } else {
                    $this->Session->setFlash(__($jsondatadecoded["result"]["msg"]), 'default', array('class' => 'alert alert-warning'));
                    $this->redirect($this->Auth->logout());
                }
                if (isset($loggedinUser["current_org"]) && !empty($loggedinUser["current_org"])) {
                    $defaultorg = $loggedinUser["current_org"]->id;
                }

                //if(isset($loggedinUser['org_updates']) && ($loggedinUser['org_updates']['org_status'] != 'active' || $loggedinUser['org_updates']['user_status'] != "active") && !empty($orgdata["organization"]))
                //{
                //   $this->redirect(array('controller' => 'client', 'action' => 'inactiveOrg')); 
                //}
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
        $this->set('jsIncludes', array('myorg'));
        $this->set('MenuName', 'My Organizations');
        $this->set(compact("orgdata", "type", "defaultorg"));
    }

    function orginfo($org_id) {
        if ($this->Session->check('Auth.User')) {
            $loggedinUser = $this->Auth->user();
            if (isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'client') {
                //=====api for org data
                $alldetailsorg = $this->Common->OrgInfoClient($loggedinUser["token"], $org_id);
//                $postdatafororginfo = array("token" => $loggedinUser["token"], "oid" => $org_id);
//                $jsondatafororginfo = json_decode($this->Apicalls->curlget("getOrganization.json", $postdatafororginfo), true);
//                $alldetailsorg = array();
//                if ($jsondatafororginfo["result"]["status"] == true) {
//                    $streetcity = array();
//                    $statecountry = array();
//                    $resultant = $jsondatafororginfo["result"]["data"];
//                    $orgname = $resultant["Organization"]["name"];
//                    $org_shortname = $resultant["Organization"]["short_name"];
//                    $org_image = $resultant["Organization"]["image"];
//                    $org_totalendorsements = $resultant["total_endorsement"];
//                    $org_totalcv = $resultant["total_core_values"];
//                    $org_total_endorsement_month = $resultant["total_endorsement_month"];
//                    $org_core_values = $resultant["core_values"];
//                    if ($resultant["Organization"]["street"] != "") {
//                        array_push($streetcity, $resultant["Organization"]["street"]);
//                    }
//                    if ($resultant["Organization"]["city"] != "") {
//                        array_push($streetcity, $resultant["Organization"]["city"]);
//                    }
//                    if ($resultant["Organization"]["state"] != "") {
//                        array_push($statecountry, $resultant["Organization"]["state"]);
//                    }
//                    if ($resultant["Organization"]["country"] != "") {
//                        array_push($statecountry, $resultant["Organization"]["country"]);
//                    }
//                    $zip = $resultant["Organization"]["zip"];
//                    $alldetailsorg = array(
//                        "org_name" => $orgname,
//                        "org_sname" => $org_shortname,
//                        "org_image" => $org_image,
//                        "org_totalendorsements" => $org_totalendorsements,
//                        "org_totalcv" => $org_totalcv,
//                        "org_totalendorsementsmonth" => $org_total_endorsement_month,
//                        "org_core_values" => $org_core_values,
//                        "streetcity" => $streetcity,
//                        "statecountry" => $statecountry,
//                        "zip" => $zip
//                    );
//                }
                //====end 
                //=====api for myrole in organization
                $postdata = array("token" => $loggedinUser["token"], "org_id" => $org_id);
                $jsondata = $this->Apicalls->curlpost("getOrgoption.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);

                $optionsselected = array("department_id" => 0, "entity_id" => 0, "job_title_id" => 0);
                $departments = array();
                $entities = array();
                $jobtitles = array();
                if ($jsondatadecoded["result"]["status"] == 1) {
                    $resultant = $jsondatadecoded["result"]["data"];
                    if (!empty($resultant["departments"])) {
                        $departmentarray = $resultant["departments"];
                        $departments = $this->Common->seettingkeyvalue($departmentarray);
                    }
                    if (!empty($resultant["entity"])) {
                        $entityarray = $resultant["entity"];
                        $entities = $this->Common->seettingkeyvalue($entityarray);
                    }
                    if (!empty($resultant["job_titles"])) {
                        $job_titlesarray = $resultant["job_titles"];
                        $jobtitles = $this->Common->seettingkeyvalue($job_titlesarray);
                    }
                    $optionsselected["department_id"] = $resultant["option_selected"]["department_id"];
                    $optionsselected["entity_id"] = $resultant["option_selected"]["entity_id"];
                    $optionsselected["job_title_id"] = $resultant["option_selected"]["job_title_id"];
                }
                $allexistinvalues = array("departments" => $departments, "entities" => $entities, "jobtitles" => $jobtitles);
                //====end 
                //===api to create graph for core values
                $postdataforgraph = array("token" => $loggedinUser["token"], "org_id" => $org_id, "web" => 1);
                $startdate = "";
                $enddate = "";
                if ($this->request->is("post")) {
                    $startdate = $this->request->data["startdate"];
                    $enddate = $this->request->data["enddate"];
                    if ($startdate != "" && $enddate != "") {
                        $startdatenew = explode("-", $this->request->data["startdate"]);
                        $enddatenew = explode("-", $this->request->data["enddate"]);
                        $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);
                        $enddatenew = mktime(0, 0, 0, $enddatenew[0], $enddatenew[1], $enddatenew[2]);
                        $postdataforgraph = array("token" => $loggedinUser["token"], "org_id" => $org_id, "web" => 1, "start_date" => $startdatenew, "end_date" => $enddatenew);
                    } else {
                        $postdataforgraph = array("token" => $loggedinUser["token"], "org_id" => $org_id, "web" => 1);
                    }
                }

                $jsondataforgraph = json_decode($this->Apicalls->curlpost("endorsementbycorevalues.json", $postdataforgraph), true);
                $graphbycorevalues = "";
                if ($jsondataforgraph["result"]["status"] == 1) {
                    $graphbycorevalues = $jsondataforgraph["result"]["data"];
                }
                //===========end
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('jsIncludes', array('myorg'));
        $this->set('MenuName', 'Organization Detail');
        $this->set(compact("allexistinvalues", "optionsselected", "org_id", "alldetailsorg", "graphbycorevalues", "startdate", "enddate"));
    }

    function createorg() {
        if ($this->Session->check('Auth.User')) {
            $country_code = 232;
            $errormsg = "";
            $stateselected = "";
            $loggedinUser = $this->Auth->user();
            $this->loadModel('Country');
            $listCountries = $this->Country->find('list', array("order" => "Country.name", 'fields' => array('Country.id', 'Country.name')));
            $listState = $this->Common->liststate(232);

            $termsAccept = isset($loggedinUser['terms_accept']) ? $loggedinUser['terms_accept'] : 0;
            if (!$termsAccept) {
                $this->Session->setFlash(__('Accept End User License Agreement'), 'default', array('class' => 'alert alert-warning'));
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }

            $swtichedvalues = array("cv" => "core_values", "departments" => "departments", "jb" => "job_titles");
            $jsondataforcorevalues = json_decode($this->Apicalls->curlget("getPredefinedValues.json", array("type" => $swtichedvalues["cv"])), true);
            if ($jsondataforcorevalues["result"]["status"]) {
                $corevalues = array_combine($jsondataforcorevalues["result"]["data"]["core_values"], $jsondataforcorevalues["result"]["data"]["core_values"]);
            }
            $corevalues = array_merge($corevalues, array("other" => "other"));
            $jsondatafordepartments = json_decode($this->Apicalls->curlget("getPredefinedValues.json", array("type" => $swtichedvalues["departments"])), true);
            if ($jsondatafordepartments["result"]["status"]) {
                $departments = array_combine($jsondatafordepartments["result"]["data"]["departments"], $jsondatafordepartments["result"]["data"]["departments"]);
            }
            $departments = array_merge($departments, array("other" => "other"));
            $jsondataforjobtitles = json_decode($this->Apicalls->curlget("getPredefinedValues.json", array("type" => $swtichedvalues["jb"])), true);
            if ($jsondataforjobtitles["result"]["status"]) {
                $jobtitles = array_combine($jsondataforjobtitles["result"]["data"]["job_titles"], $jsondataforjobtitles["result"]["data"]["job_titles"]);
            }
            $jobtitles = array_merge($jobtitles, array("other" => "other"));
            if ($this->request->is("post")) {
//                pr($this->request->data);
//                exit;
                $imagedata = "";
                if ($this->request->data["Org"]["Image"]["tmp_name"] != "") {
                    //======converting the image to base 64
                    $filepath = $this->request->data["Org"]["Image"]["tmp_name"];
                    $type = pathinfo($filepath, PATHINFO_EXTENSION);
                    $data = file_get_contents($filepath);
                    $base64 = base64_encode($data);
                    $imagedata = $base64;
                }
                //unset($this->Organization->validate['image']);
                //to check if any of the value is saved or not
                //============department
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
                            $department = $this->request->data['Org']['departments'][$i];
                            array_push($departmentarray, $department);
                            //$totalsaveddepartment++;
                        }
                    }
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
                            $jobtitle = $this->request->data['Org']['jobtitle'][$i];
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
                            $entity = $this->request->data['Org']['entitytextbox'][$i];
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
                            $core_values = array("name" => $this->request->data['Org']['corevalues'][$i], "color_code" => $this->request->data['Org']['cp'][$i], "from_master" => $from_master);
                            array_push($cvarray, $core_values);
                            $totalsavedcv++;
                        }
                    }
                }

                $departmentobject = implode(",", $departmentarray);
                $corevalueobject = json_encode($cvarray);
                $entityobject = implode(",", $entityarray);
                $jbobject = implode(",", $jobtitlearray);
                
                
                $country_code = $this->request->data['Org']['country'];
                if ($country_code == "") {
                    $country_code = 232;
                }
                if ($country_code) {
                    $stateselected = $this->request->data['Org']['state'];
                    $this->request->data['Org']['country'] = $listCountries[$country_code];
                }
                
                if (isset($this->request->data['Org']['allow_comments']) && $this->request->data['Org']['allow_comments'] == 1) {
                    $optional_comment = $this->request->data['Org']['optional_comments'];
                } else {
                    $optional_comment = 1;
                }

                $postdata = array(
                    "token" => $loggedinUser["token"],
                    "name" => $this->request->data['Org']['name'],
                    "image" => $imagedata,
                    "allow_attachment" => $this->request->data['Org']['allow_attachment'],
                    "allow_comments" => $this->request->data['Org']['allow_comments'],
                    "optional_comments" => $optional_comment,
                    "core_values" => $corevalueobject,
                    "public_endorse_visible" => $this->request->data['Org']['public_endorse_visible'],
                    "show_leader_board" => $this->request->data['Org']['show_leader_board'],
                    "country" => $this->request->data['Org']['country'],
                    "state" => $this->request->data['Org']['state'],
                    "city" => $this->request->data['Org']['city'],
                    "street" => $this->request->data['Org']['street'],
                    "zip" => $this->request->data['Org']['zip'],
                    "phone_number" => $this->request->data['Org']['phone_number'],
                );

                if (!empty($departmentarray)) {
                    $postdata["department"] = $departmentobject;
                }
                if (!empty($entityarray)) {
                    $postdata["entity"] = $entityobject;
                }
                if (!empty($jobtitlearray)) {
                    $postdata["job_title"] = $jbobject;
                }

                $jsondata = json_decode($this->Apicalls->curlpost("createOrganization.json", $postdata), true);

                if ($jsondata["result"]["status"]) {
                    if (!isset($loggedinUser["current_org"])) {
                        $currentOrg = (object) $jsondata["result"]["data"]["Organization"];
                        $this->Session->write('Auth.User.current_org', $currentOrg);
                        //$loggeduser["current_org"] = $newdata["result"]["data"]["Organization"];
                    }
                    $this->redirect(array("controller" => "client", "action" => "myorganizations"));
                } else {
                    $errormsg = $jsondata["result"]["errors"]["name"];
                }
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
        $this->set('jsIncludes', array('createorgclient', 'customcreateorg.js'));
        $this->set('MenuName', 'Create Org');
        $this->set(compact("allvalues", "errormsg", "corevalues", "departments", "jobtitles", "country_code", 'listCountries', 'listState', 'stateselected'));
    }

    public function forgotPassword() {
        $this->layout = null;
        if ($this->request->is('post')) {
            $postData = $this->request->data;
            $response = $this->Apicalls->curlpost("forgotPassword.json", $postData);
            $response = json_decode($response);
            $response = $response->result;

            echo json_encode(array("success" => $response->status, "msg" => $response->msg));
            exit;
        }
    }

    public function recoverUsername() {
        $this->layout = null;
        if ($this->request->is('post')) {
            $postData = $this->request->data;
            $response = $this->Apicalls->curlget("recoverusername.json", $postData);
            $response = json_decode($response);
            $response = $response->result;

            echo json_encode(array("success" => $response->status, "msg" => $response->msg));
            exit;
        }
    }

    public function faq($param = "loginfaq") {
        if ($this->Session->check('Auth.User')) {
            $layout = "default";
            $response = $this->Apicalls->curlget("faq.json", array("is_web" => "1"));
        } else {
            $this->layout = "ajax";
            $layout = "ajax";
            $response = $this->Apicalls->curlget("faq.json", array());
        }
        $response = json_decode($response);
        $response = $response->result;
        $this->set('data', $response->data);
        $this->set('layout', $layout);
    }

    public function leaderboard() {
        if ($this->Session->check('Auth.User')) {
            $loggedinUser = $this->Auth->user();
            if (isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'client') {
                if (isset($loggedinUser['org_updates']) && ($loggedinUser['org_updates']['org_status'] == 'inactive' || $loggedinUser['org_updates']['user_status'] == "inactive")) {
                    $this->redirect(array('controller' => 'client', 'action' => 'inactiveOrg'));
                } else {
                    $postdata = array("token" => $loggedinUser["token"], "type" => "endorsed");
                    $jsondatafororginfo = json_decode($this->Apicalls->curlpost("leaderboard.json", $postdata), true);
                    $alldata = array();
                    if ($jsondatafororginfo["result"]["status"] == 1) {
                        $resultant = $jsondatafororginfo["result"];
                        $alldata = $resultant["data"];
                    }
                    $org_id = $loggedinUser["current_org"]->id;
                    $alldetailsorg = $this->Common->OrgInfoClient($loggedinUser["token"], $org_id);
                }
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
        $this->set('jsIncludes', array('leaderboard'));
        $this->set('MenuName', 'Leader Board');
        $this->set(compact("alldata", 'alldetailsorg'));
    }

    public function whatsnew() {
        if ($this->Session->check('Auth.User')) {
            $loggedinUser = $this->Auth->user();
            if (isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'client') {
                if (isset($loggedinUser['org_updates']) && ($loggedinUser['org_updates']['org_status'] == 'inactive' || $loggedinUser['org_updates']['user_status'] == "inactive")) {
                    $this->redirect(array('controller' => 'client', 'action' => 'inactiveOrg'));
                } else {
                    $postdata = array("token" => $loggedinUser["token"]);
                    $alldata = array();
                    $jsondatafororginfo = json_decode($this->Apicalls->curlpost("topendorse.json", $postdata), true);
                    if ($jsondatafororginfo["result"]["status"]) {
                        $alldata = $jsondatafororginfo["result"]["data"];
                    }
                }
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
        $this->set('jsIncludes', array('whatsnew'));
        $this->set("MenuName", "What's New");
        $this->set(compact("alldata"));
    }

    public function fbLogin() {
        if ($this->request->query('code')) {
            $this->setfbData();
            // User login successful
            $fbUser = $this->Facebook->getUser();          # Returns facebook user_id
            if ($fbUser) {
                $fbUser = $this->Facebook->api('/me?fields=id,name,email');     # Returns user information

                if (!isset($fbUser['email']) || empty($fbUser['email'])) {
                    $this->Session->setFlash(__('Not able to get your email id. nDorse app requires your email id. So, use a facebook id that is having your email id'), 'default', array('class' => 'alert alert-warning'));
                    $this->redirect(array('controller' => 'client', 'action' => 'login'));
                }

                $postData = array();
                $postData['email'] = $fbUser['email'];
                $postData['source'] = 'fb';
                $postData['source_id'] = $fbUser['id'];
                $name = $fbUser['name'];
                $nameSplitted = explode(" ", $name, 2);
                $profileData['fname'] = $nameSplitted[0];
                $profileData['lname'] = $nameSplitted[1];
                $profileData['image'] = "https://graph.facebook.com/" . $fbUser['id'] . "/picture";

                $this->performLogin($postData, $profileData);
            } else {
                $this->Session->setFlash(__('Unable to login with facebook. Please try again.'), 'default', array('class' => 'alert alert-warning'));
                $this->redirect(array('controller' => 'client', 'action' => 'login'));
            }
        } else {
            $this->Session->setFlash(__('Unable to login with facebook. Please try again.'), 'default', array('class' => 'alert alert-warning'));
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
    }

    private function performLogin($postData, $profileData = array()) {
        try {
            $response = $this->Apicalls->curlpost("login.json", $postData);
            $response = json_decode($response);
            $response = $response->result;

            if ($response->status == 1) {
                $userData = (array) $response->data;
                $userData['portal'] = 'client';
                if (isset($userData['org_updates'])) {
                    $userData['org_updates'] = (array) $userData['org_updates'];
                }

                if (isset($userData['org_updates']) && ($userData['org_updates']['org_status'] != 'active' || $userData['org_updates']['user_status'] != "active")) {
                    $this->Session->write('from_login', true);
                }

                //                pr($userData);die;
                $this->Session->write('Auth.User', $userData);

                //set last login typ[e cookie
                $this->Cookie->write("portal_cookie", "client", true, "1 week");
                //redirect to some page

                if (!isset($userData['profile_updated']) || !$userData['profile_updated']) {
                    if (!empty($profileData)) {
                        $this->Session->write('tp_profile', $profileData);
                    }
                    $this->redirect(array('controller' => 'client', 'action' => 'editprofile'));
                } else if (!isset($userData['current_org']) || $userData['current_org']->joined == 0) {
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                } else {
                    $this->redirect(array('controller' => 'endorse', 'action' => 'index'));
                }
            } else {
                if (isset($response->msg)) {
                    $errorMsg = $response->msg;
                    $this->set('errorMsg', $errorMsg);
                } else {
                    $this->Session->setFlash(__("Unable to login. Please try after sometime"), 'default', array('class' => 'alert alert-warning'));
                    $this->redirect(array('controller' => 'client', 'action' => 'login'));
                }
            }
        } catch (Exception $e) {
            $this->Session->setFlash("Unable to login. Please try after sometime", 'default', array('class' => 'message error'), 'error');
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
    }

    public function linkedinLogin() {
        $this->setLinkedInData();

        if ($this->request->query('code')) {
            $token = $this->LinkedIn->getAccessToken($_REQUEST['code']);
            $this->LinkedIn->setAccessToken($token);
            $user = $this->LinkedIn->get('/people/~:(id,first-name,last-name,date-of-birth,picture-url,email-address,main-address)');

            if (!isset($user['emailAddress']) || empty($user['emailAddress'])) {
                $this->Session->setFlash(__('Not able to get your email id. nDorse app requires your email id. So, use a LinkedIn id that is having your email id'), 'default', array('class' => 'alert alert-warning'));
                $this->redirect(array('controller' => 'client', 'action' => 'login'));
            }
            $postData['email'] = $user['emailAddress'];
            $postData['source'] = 'lin';
            $postData['source_id'] = $user['id'];
            $profileData['fname'] = $user['firstName'];
            $profileData['lname'] = $user['lastName'];
            $profileData['image'] = $user['pictureUrl'];

            $this->performLogin($postData, $profileData);
        } else {
            $this->Session->setFlash(__('Unable to login with linkedin. Please try again.'), 'default', array('class' => 'alert alert-warning'));
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
    }

    public function setLinkedInData() {
        App::import('Vendor', 'LinkedIn/LinkedIn');
        $linkedinConfig = Configure::read("linkedinConfig");
        $linkedinConfig['callback_url'] = Router::url('/', true) . "client/linkedinLogin";
        $linkedinPermissions = Configure::read("linkedinPermissions");
        $this->LinkedIn = new LinkedIn(
                $linkedinConfig
        );

        $url = $this->LinkedIn->getLoginUrl($linkedinPermissions);
        $this->set("linkedinLoginUrl", $url);
    }

    public function linkedinLoginOl() {
        App::import('Vendor', 'LinkedIn/http');
        App::import('Vendor', 'LinkedIn/oauth_client');

        $linkedinConfig = Configure::read("linkedinConfig");

        if (isset($_GET["oauth_problem"]) && $_GET["oauth_problem"] <> "") {
            // in case if user cancel the login. redirect back to home page.
            $_SESSION["err_msg"] = $_GET["oauth_problem"];
            $this->Session->setFlash(__('Unable to login with linkedin. Please try again.'), 'default', array('class' => 'alert alert-warning'));
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
            exit;
        }
        $this->linkedinClient = new oauth_client_class;

        $this->linkedinClient->debug = false;
        $this->linkedinClient->debug_http = true;
        $this->linkedinClient->redirect_uri = Router::url('/', true) . "client/linkedinLogin";

        $this->linkedinClient->client_id = $linkedinConfig['apikey'];
        $application_line = __LINE__;
        $this->linkedinClient->client_secret = $linkedinConfig['secret'];
        $this->linkedinClient->scope = $linkedinConfig['scope'];

        $successUser = false;
        if (($success = $this->linkedinClient->Initialize())) {
            if (($success = $this->linkedinClient->Process())) {
                if (strlen($this->linkedinClient->authorization_error)) {
                    $this->linkedinClient->error = $this->linkedinClient->authorization_error;
                    $success = false;
                } elseif (strlen($this->linkedinClient->access_token)) {
                    $successUser = $this->linkedinClient->CallAPI(
                            'http://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,location,picture-url,public-profile-url,formatted-name)', 'GET', array(
                        'format' => 'json'
                            ), array('FailOnAccessError' => true), $user);
                }
            }
            $success = $this->linkedinClient->Finalize($success);
        }

        if ($success) {
            //login
//                       pr($user);die;
            if ($successUser) {
                if (!isset($user->emailAddress) || empty($user->emailAddress)) {
                    $this->Session->setFlash(__('Not able to get your email id. nDorse app requires your email id. So, use a LinkedIn id that is having your email id'), 'default', array('class' => 'alert alert-warning'));
                    $this->redirect(array('controller' => 'client', 'action' => 'login'));
                }
                $postData['email'] = $user->emailAddress;
                $postData['source'] = 'lin';
                $postData['source_id'] = $user->id;
                $profileData['fname'] = $user->firstName;
                $profileData['lname'] = $user->lastName;
                $profileData['image'] = $user->pictureUrl;

                $this->performLogin($postData, $profileData);
            }
        } else {
//             $_SESSION["err_msg"] = $this->linkedinClient->error;
            $this->Session->setFlash(__($this->linkedinClient->error), 'default', array('class' => 'alert alert-warning'));
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
    }

    /**
     * This function will makes Oauth Api reqest
     */
//    public function googlelogin() {
//        $this->autoRender = false;
//        App::import('Vendor', 'Google/src/config');
//        App::import('Vendor', 'Google/src/Google_Client');
//        App::import('Vendor', 'Google/src/contrib/Google_PlusService');
//        App::import('Vendor', 'Google/src/contrib/Google_Oauth2Service');
//        //require_once '../Config/google_login.php';
//        $this->GoogleClient = new Google_Client();
//        $this->GoogleClient->setScopes(Configure::read('googleScopeArray'));
//        $this->GoogleClient->setApprovalPrompt('auto');
//        $url = $this->GoogleClient->createAuthUrl();
//        $this->redirect($url);
//    }

    /**
     * This function will handle Oauth Api response
     */
    public function google_login() {
        $this->autoRender = false;

        $this->setGoogleData();

        $plus = new Google_PlusService($this->GoogleClient);
        $oauth2 = new Google_Oauth2Service($this->GoogleClient);

        if (isset($_GET['code'])) {
            $this->GoogleClient->authenticate(); // Authenticate

            if ($this->GoogleClient->getAccessToken()) {
//                $_SESSION['access_token'] = $this->GoogleClient->getAccessToken();
                $user = $oauth2->userinfo->get();
                try {
                    if (!empty($user)) {

                        if (!isset($user["email"]) || empty($user["email"])) {
                            $this->Session->setFlash(__('Not able to get your email id. nDorse app requires your email id. So, use a LinkedIn id that is having your email id'), 'default', array('class' => 'alert alert-warning'));
                            $this->redirect(array('controller' => 'client', 'action' => 'login'));
                        }
                        $postData['email'] = $user["email"];
                        $postData['source'] = 'gplus';
                        $postData['source_id'] = $user["id"];
                        $profileData['fname'] = $user["given_name"];
                        $profileData['lname'] = $user["family_name"];
                        $profileData['image'] = $user["picture"];

                        $this->performLogin($postData, $profileData);
                    }
                } catch (Exception $e) {
                    $this->Session->setFlash(GOOGLE_LOGIN_FAILURE, 'default', array('class' => 'message error'), 'error');
                    $this->redirect(array('controller' => 'client', 'action' => 'login'));
                }
            }
        }

        exit;
    }

    public function setGoogleData() {
        App::import('Vendor', 'Google/src/config');
        App::import('Vendor', 'Google/src/Google_Client');
        App::import('Vendor', 'Google/src/contrib/Google_PlusService');
        App::import('Vendor', 'Google/src/contrib/Google_Oauth2Service');
        //require_once '../Config/google_login.php';
        $this->GoogleClient = new Google_Client();
        $this->GoogleClient->setScopes(Configure::read('googleScopeArray'));
        $this->GoogleClient->setApprovalPrompt('auto');
        $this->GoogleClient->setRedirectUri(Router::url('/', true) . "google_login");
        $googleSecretvariable = Configure::read('googleSecretvariable');
        $this->GoogleClient->setClientId($googleSecretvariable['clientid']);
        $this->GoogleClient->setClientSecret($googleSecretvariable['clientsecret']);
        $url = $this->GoogleClient->createAuthUrl();
        $this->set('gplusLoginUrl', $url);
    }

    public function expire() {
        $this->Session->setFlash(__('Seems like Someone else has logged in on other Machines'), 'default', array('class' => 'alert alert-warning', 'action' => 'login'));
//        $this->redirect(array('controller' => 'client', 'action' => 'login'));
    }

    public function checkSession() {
        $loggedinUser = $this->Auth->user();
        $postdata['token'] = $loggedinUser['token'];
        $apiResponse = $this->Apicalls->curlpost("checkSession.json", $postdata);
    }

    public function setPassword() {
        $this->layout = null;
        if ($this->request->is('post')) {
            $postData = $this->request->data;
            $response = $this->Apicalls->curlpost("resetPassword.json", $postData);
            $response = json_decode($response);
            $response = $response->result;

            echo json_encode(array("success" => $response->status, "msg" => $response->msg));
            exit;
        }
    }

}

?>
