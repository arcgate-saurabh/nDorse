<?php

App::uses('Component', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class CommonComponent extends Component {

    public $components = array('Session', 'Image', 'Auth', 'Apicalls');

    public function getDefaultDepartments($list = true, $withId = 1, $fields = array()) {
        $DepartmentModel = ClassRegistry::init('Department');
        $departments = $DepartmentModel->find("all", array("fields" => $fields));

        if ($list) {
            $departmentsList = array();

            foreach ($departments as $department) {
                if ($withId == 1) {
                    $departmentsList[$department['Department']['id']] = $department['Department']['name'];
                } else if ($withId == 2) { //to get name as option value;
                    $departmentsList[$department['Department']['name']] = $department['Department']['name'];
                } else {
                    $departmentsList[] = $department['Department']['name'];
                }
            }

            return $departmentsList;
        } else {
            return $departments;
        }
    }

    public function getDefaultHobbies($list = true, $withId = 2, $fields = array()) {
        $HobbyModel = ClassRegistry::init('Hobby');
        $hobbies = $HobbyModel->find("all", array("fields" => $fields));
        if ($list) {
            $hobbiesList = array();

            foreach ($hobbies as $hobby) {
                if ($withId == 1) {
                    $hobbiesList[$hobby['Hobby']['id']] = $hobby['Hobby']['name'];
                } else if ($withId == 2) { //to get name as option value;
                    $hobbiesList[$hobby['Hobby']['name']] = $hobby['Hobby']['name'];
                } else {
                    $hobbiesList[] = $hobby['Hobby']['name'];
                }
            }

            return $hobbiesList;
        } else {
            return $hobbies;
        }
    }

    public function getDefaultDegrees($list = true, $withId = 2, $fields = array()) {
        $DegreesModel = ClassRegistry::init('Degrees');
        $degrees = $DegreesModel->find("all", array("fields" => $fields));
        if ($list) {
            $degreesList = array();

            foreach ($degrees as $degr) {
                if ($withId == 1) {
                    $degreesList[$degr['Degrees']['id']] = $degr['Degrees']['name'];
                } else if ($withId == 2) { //to get name as option value;
                    $degreesList[$degr['Degrees']['name']] = $degr['Degrees']['name'];
                } else {
                    $degreesList[] = strtolower($degr['Degrees']['name']);
                }
            }

            return $degreesList;
        } else {
            return $degrees;
        }
    }

    public function getDefaultJobTitles($list = true, $withId = 1, $fields = array()) {
        $JobTitleModel = ClassRegistry::init('JobTitle');
        $jobTitles = $JobTitleModel->find("all", array("fields" => $fields));

        if ($list) {
            $jobTitlesList = array();

            foreach ($jobTitles as $jobTitle) {
                if ($withId == 1) {
                    $jobTitlesList[$jobTitle['JobTitle']['id']] = $jobTitle['JobTitle']['title'];
                } else if ($withId == 2) { //to get name as option value;
                    $jobTitlesList[$jobTitle['JobTitle']['title']] = $jobTitle['JobTitle']['title'];
                } else {
                    $jobTitlesList[] = $jobTitle['JobTitle']['title'];
                }
            }

            return $jobTitlesList;
        } else {
            return $jobTitles;
        }
    }

    public function getDefaultSkills($list = true, $withId = 2, $fields = array()) {
        $SkillModel = ClassRegistry::init('Skill');

        $skills = $SkillModel->find("all", array("fields" => $fields));

        if ($list) {
            $skillsList = array();

            foreach ($skills as $skill) {
                if ($withId == 1) {
                    $skillsList[$skill['Skill']['id']] = $skill['Skill']['name'];
                } else if ($withId == 2) { //to get name as option value;
                    $skillsList[$skill['Skill']['name']] = $skill['Skill']['name'];
                } else {
                    $skillsList[] = $skill['Skill']['name'];
                }
            }

            return $skillsList;
        } else {
            return $skills;
        }
    }

    public function getDefaultIndustries($list = true, $withId = 2, $fields = array()) {
        $IndustryModel = ClassRegistry::init('Industry');

        $industry = $IndustryModel->find("all", array("order" => "name", "fields" => $fields));

        if ($list) {
            $industryList = array();

            foreach ($industry as $industry) {
                if ($withId == 1) {
                    $industryList[$industry['Industry']['id']] = $industry['Industry']['name'];
                } else if ($withId == 2) { //to get name as option value;
                    $industryList[$industry['Industry']['name']] = $industry['Industry']['name'];
                } else {
                    $industryList[] = strtolower($industry['Industry']['name']);
                }
            }

            return $industryList;
        } else {
            return $industry;
        }
    }

    public function getSubOrganizations($org_id, $list = true, $withId = true, $fields = array()) {
        $EntityModel = ClassRegistry::init('Entity');
        $entities = $EntityModel->find("all", array("conditions" => array("organization_id" => $org_id)));

        if ($list) {
            $entitiesList = array();

            foreach ($entities as $entity) {
                if ($withId) {
                    $entitiesList[$entity['Entity']['id']] = $entity['Entity']['name'];
                } else {
                    $entityItem = array();
                    foreach ($fields as $field) {
                        $entityItem[$field] = $entity['Entity'][$field];
                    }
                    $entitiesList[] = $entityItem;
                }
            }

            return $entitiesList;
        } else {
            return $entities;
        }
    }

    public function getDefaultCoreValues($list = true, $withId = 2, $fields = array()) {
        $CoreValuesModel = ClassRegistry::init('CoreValues');
        $corevalues = $CoreValuesModel->find("all", array());

        if ($list) {
            $corevaluesList = array();
            $defaultcorevaluesList = array();

            foreach ($corevalues as $corevalue) {

                if ($withId == 1) {
                    $corevaluesList[$corevalue['CoreValues']['id']] = $corevalue['CoreValues']['name'];
                } else if ($withId == 2) { //to get name as option value;
                    if ($corevalue['CoreValues']['is_default'] == 1) {
                        $defaultcorevaluesList[$corevalue['CoreValues']['name']] = $corevalue['CoreValues']['name'];
                    } else {
                        $corevaluesList[$corevalue['CoreValues']['name']] = $corevalue['CoreValues']['name'];
                    }
                    $corevaluesList = array("default" => $defaultcorevaluesList, "normal" => $corevaluesList);
                } else {
                    if ($corevalue['CoreValues']['is_default'] == 1) {
                        $defaultcorevaluesList[] = $corevalue['CoreValues']['name'];
                    } else {
                        $corevaluesList[] = $corevalue['CoreValues']['name'];
                    }
                }
            }
            $corevaluesList = array("selected" => $defaultcorevaluesList, "normal" => $corevaluesList);

            return $corevaluesList;
        } else {
            return $corevalues;
        }
    }

    public function getDefaultCoreValuesWeb($list = true, $withId = 2, $fields = array()) {
        $CoreValuesModel = ClassRegistry::init('CoreValues');
        $corevalues = $CoreValuesModel->find("all", array());
        if ($list) {
            $corevaluesList = array();

            foreach ($corevalues as $corevalue) {

                if ($withId == 1) {
                    $corevaluesList[$corevalue['CoreValues']['id']] = $corevalue['CoreValues']['name'];
                } else if ($withId == 2) { //to get name as option value;
                    $corevaluesList[$corevalue['CoreValues']['name']] = $corevalue['CoreValues']['name'];
                } else {
                    $corevaluesList[] = $corevalue['CoreValues']['name'];
                }
            }

            return $corevaluesList;
        } else {
            return $corevalues;
        }
    }

    public function checkImageType($fileControl) {

        $image = $fileControl; //File upload control 
        //allowed image types
        $imageTypes = array("image/gif", "image/jpeg", "image/png");

        //check if image type fits one of allowed types
        $flagtype = 0;
        $flagError = true;
        $errorMessage = $imageName = '';

        foreach ($imageTypes as $type) {
            //echo $type; 

            if (isset($image) && $type == $image) {
                $flagtype = 1;
                $flagError = false;
                break;
            }
        }

        if ($flagtype < 1) {
            $errorMessage = 'File type of uploaded image is not allowed';
        }


        return array('status' => !$flagError, 'error' => $errorMessage, 'imageName' => $imageName);
    }

    function uploadImage($fileControl, $uploadPathOffset, $thumbPathOffset, $resizeConfig = array('height' => 279, 'width' => 279), $profiletype = "profile") {
        //$image = $this->data['Varient']['filename'];
        $image = $fileControl; //File upload control 
        //allowed image types
        $imageTypes = array("image/gif", "image/jpeg", "image/png");
        //upload folder - make sure to create one in webroot
        $uploadFolder = $uploadPathOffset;
        $uploadsmalFolder = WWW_ROOT . $uploadPathOffset . "small/";
        //full path to upload folder
        $uploadPath = WWW_ROOT . $uploadFolder;
        //check if image type fits one of allowed types
        $flagtype = 0;
        $flagError = true;
        $errorMessage = $imageName = '';
        foreach ($imageTypes as $type) {

            if ($type == $image['type']) {
                $flagtype = 1;

                //check if there wasn't errors uploading file on serwer
                if ($image['error'] == 0) {
                    //image file name
                    $imageName = str_replace(" ", "", $image['name']);
                    //check if file exists in upload folder
                    //if (file_exists($uploadPath . DIRECTORY_SEPARATOR . $imageName)) {
                    //create full filename with timestamp
                    $imageName = date('dmy_His') . $imageName;

                    //}
                    //create full path with image name
                    $full_image_path = $uploadPath . $imageName;


                    //upload image to upload folder
                    if (move_uploaded_file($image['tmp_name'], $full_image_path)) {
                        //$this->Session->setFlash('File saved successfully');
                        //echo $full_image_path."----".$thumbPathOffset . $imageName;
                        copy($full_image_path, $thumbPathOffset . $imageName);

                        $this->Image->resize($thumbPathOffset . $imageName, $resizeConfig['height'], $resizeConfig['width'], false);

                        if ($profiletype == "profile") {
                            copy($full_image_path, $uploadsmalFolder . $imageName);
                            $this->Image->resize($uploadsmalFolder . $imageName, $resizeConfig['height'], $resizeConfig['width'], false);
                        }
                        $flagError = false;
                    } else {
                        $errorMessage = 'There was a problem uploading file. Please try again.';
                    }
                } else {
                    $errorMessage = 'There was a problem uploading file. Please try again.';
                }
                break;
            }
        }

        if ($flagtype < 1) {
            $errorMessage = 'File type of uploaded image is not allowed';
        }


        return array('status' => !$flagError, 'error' => $errorMessage, 'imageName' => $imageName);
    }

    /**
     * This function is used to send email.
     * 
     * @param email(required) $to To Email address.
     * @param string(optional) $subject The subject line of the email.
     * @param string $template Name of template.
     * @param array variables to be replaced in template $viewVars The message of the email.
     * @param email(optional) $cc Cc Email address
     * @param email(optional) $bcc Bcc Email address
     * @param array(optional) $attachments Filepath
     * @return boolean If mail has been sent successfully then true otherwise false.
     */
    public function sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachments = false) {
//        if (strpos($to, "@ndorsedev.com") !== false || strpos($to, "@arcgate.com") !== false) {
//            return true;
//        }
//        $templateArray = array("invite", "invitation_admin", "register", "verification", "forgot_password");
//        
//        $template = trim($template);
//        
//        if(!in_array($template, $templateArray)) {
//            return true;
//        }
        $to = strtolower($to);
        if ($to == 'ross.goldman@vca.com' || $to == 'sandra.millon@lcmchealth.org' || $to == 'nickolate.cooper@lcmchealth.org') {
            return true;
        }

        $Email = new CakeEmail('default');
        $Email->emailFormat('html');

        $UserModel = ClassRegistry::init('User');

        $user = $UserModel->findByEmail($to);

        if (!empty($user)) {
            $viewVars['to_user'] = $user['User'];
        }

        $Email->viewVars($viewVars);
        $Email->template($template, null);

        $Email->to($to);

        if ($cc) {
            $Email->cc($cc);
        }

        if ($bcc) {
            $Email->bcc($bcc);
        }

        $Email->subject($subject);
        if ($attachments) {
            $Email->attachments($attachments);
        }

        try {

            if ($Email->send()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo 'Mail Failure with Exception: ' . $e;
            return false;
        }
    }

    public function sendfaqmail($to, $subject, $template, $viewVars) {
        try {
            $Email = new CakeEmail('default');
            $Email
                    ->template($template)
                    ->emailFormat('html')
                    ->viewVars($viewVars)
                    ->to($to)
                    ->subject($subject)
                    ->send();
        } catch (Exception $e) {
            echo 'Mail Failure with Exception: ' . $e;
        }
    }

    // upload file
    public function uploadImg($type, $id, $file, $imagetype) {
        $resizeConfig = array('height' => 279, 'width' => 279);
        $img = str_replace('data:image/' . $imagetype . ';base64,', '', $file);
        if ($type == "user") {
            $filePath = PROFILE_IMAGE_DIR;
        } else {
            $filePath = ORG_IMAGE_DIR;
        }
        $filePath = WWW_ROOT . $filePath;
        $thumbPath = $filePath . 'small/';
        $uploadfile = $filePath . $id . '.' . $imagetype;
        $imageName = $id . '.' . $imagetype;
        $smalluploadfile = $thumbPath . $imageName;
        @unlink($uploadfile);
        @unlink($smalluploadfile);
        file_put_contents($uploadfile, base64_decode($img));
        copy($uploadfile, $smalluploadfile);
        $this->Image->resize($smalluploadfile, $resizeConfig['height'], $resizeConfig['width'], false);
        return $imageName;
    }

    public function uploadPostFiles($id, $imageName, $imagetype, $tmpName) {
        $filePath = POST_FILE_DIR;
        $filePath = WWW_ROOT . $filePath;
        $uploadfile = $filePath . $imageName;
        //$imageName = $id . '.' . $imagetype;
        echo $tmpName;
        if (!move_uploaded_file($tmpName, $uploadfile)) {
            $errors['moveUploaded'] = "move_uploaded_file() failed.";
            echo "move_uploaded_file() failed.";
            exit;
            return $errors;
        } else {
            echo $uploadfile;
            exit;
        }
        return true;
    }

    public function uploadApiImage($uploadPath, $imageName, $imageData) {
        $imageData = base64_decode($imageData);
        $resizeConfig = array('height' => 279, 'width' => 279);
        $thumbPath = $uploadPath . 'small/';
        $uploadImage = $uploadPath . $imageName;
        $thumbImage = $thumbPath . $imageName;

        /* Thumb Image-2 Start */
        $resizeConfig2 = array('width' => 759);
        $thumbPath2 = $uploadPath . 'small_375/';
        $thumbImage2 = $thumbPath2 . $imageName;
        /* Thumb Image-2 End */

        /* Thumb Image-3 Start */
        $resizeConfig3 = array('width' => 900);
        $thumbPath3 = $uploadPath . 'small_900/';
        $thumbImage3 = $thumbPath3 . $imageName;
        /* Thumb Image-3 End */



        @unlink($uploadfile);
        @unlink($smalluploadfile);

        if (file_put_contents($uploadImage, $imageData)) {
            copy($uploadImage, $thumbImage);
            copy($uploadImage, $thumbImage2);
            copy($uploadImage, $thumbImage3);
            $this->Image->resize($thumbImage, $resizeConfig['height'], $resizeConfig['width'], false);
            $this->Image->resize($thumbImage2, $resizeConfig2['width'], 0, true, false, RESIZE_WIDTH);
            $this->Image->resize($thumbImage3, $resizeConfig3['width'], 0, true, false, RESIZE_WIDTH);
            return true;
        } else {
            return false;
        }
    }

    public function setSessionRoles() {
        $roleModel = ClassRegistry::init('Role');
        $roleData = $roleModel->find("all", array());
        $roleList = array();

        foreach ($roleData as $role) {
            $roleList[$role['Role']['id']] = $role['Role']['role'];
        }

        CakeSession::write('roleList', $roleList);

        return $roleList;
    }

    //==========================functions to get departments entities and job titles as per organization id
    public function getorgjobtitles($organization_id) {
        $JobTitleModel = ClassRegistry::init('OrgJobTitle');
        $jobtitles = $JobTitleModel->find("list", array("conditions" => array("organization_id" => $organization_id)));
        return $jobtitles;
    }

    public function getorgdepartments($organization_id) {
        $DeptModel = ClassRegistry::init('OrgDepartment');
        $departments = $DeptModel->find("list", array("conditions" => array("organization_id" => $organization_id)));
        return $departments;
    }

    public function getorgcorevaluesandcode($organization_id) {
        $OrgcorevaluesModel = ClassRegistry::init('OrgCoreValue');
        $orgcorevalues = $OrgcorevaluesModel->find("all", array("fields" => array("id", "name", "color_code"), "conditions" => array("organization_id" => $organization_id, "status" => array(1, 2))));
        //pr($orgcorevalues);
        $fcorevalues = array();
        foreach ($orgcorevalues as $orgvalues) {
            $fcorevalues[$orgvalues["OrgCoreValue"]["id"]] = array("name" => $orgvalues["OrgCoreValue"]["name"], "colorcode" => $orgvalues["OrgCoreValue"]["color_code"]);
        }
        return $fcorevalues;
    }

    public function getorgentities($organization_id) {
        $EntityModel = ClassRegistry::init('Entity');
        $entities = $EntityModel->find("list", array("conditions" => array("organization_id" => $organization_id)));
        return $entities;
    }

    //=======function to count no of users for desired role
    public function getusersfororg($organization_id, $user_role = null) {
        $user_role = array(2, 3);
        $EntityModel = ClassRegistry::init('UserOrganization');
        return $EntityModel->find('count', array('conditions' => array("organization_id" => $organization_id, "user_role" => $user_role, "UserOrganization.status" => array(0, 1, 3))));
    }

    public function getorgownername($owner_id) {
        $fullname = "";
        $UserModel = ClassRegistry::init('User');
        $namedetail = $UserModel->findById($owner_id, array("fname", "lname"));
        if (!empty($namedetail)) {
            $fullname = ucfirst($namedetail["User"]["fname"]) . " " . ucfirst($namedetail["User"]["lname"]);
        }
        return $fullname;
    }

    //==================function to check if file is comma separated or not
    public function getFileDelimiter($file, $checkLines = 2) {
        $file = new SplFileObject($file);
        $delimiters = array(
            ',',
            '\t',
            ';',
            '|',
            ':'
        );
        $results = array();
        $i = 0;
        while ($file->valid() && $i <= $checkLines) {
            $line = $file->fgets();
            foreach ($delimiters as $delimiter) {
                $regExp = '/[' . $delimiter . ']/';
                $fields = preg_split($regExp, $line);
                if (count($fields) > 1) {
                    if (!empty($results[$delimiter])) {
                        $results[$delimiter] ++;
                    } else {
                        $results[$delimiter] = 1;
                    }
                }
            }
            $i++;
        }
        $results = array_keys($results, max($results));
        return $results[0];
    }

    //===================to generator random password
    function randompasswordgenerator($limits) {
        $password_random = "";
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        for ($j = 0; $j < $limits; $j++) {
            $password_random .= $characters[rand(0, $charactersLength - 1)];
        }
        return $password_random;
    }

    function liststate($countryid) {
        $EntityModel = ClassRegistry::init('State');
        $listState = $EntityModel->find('list', array('conditions' => array('country_id' => $countryid)));
        foreach ($listState as $states) {
            $list[$states] = $states;
        }
        if (empty($listState)) {
            $list = "";
        }
        return $list;
    }

    function getUploadFilename($path, $filename) {

        //$filename = str_replace(" ", "", $filename);
        if (substr($path, -1) != "/") {
            $path = $path . "/";
        }

        $pathinfo = pathinfo($filename);
        $file_name = trim($pathinfo['filename']);
        //echo $file_name;
        //$file_name = trim(preg_replace("/[^a-zA-Z0-9\/_-\s]/","", $file_name));
        $file_extension = trim($pathinfo['extension']);

        $filename = $file_name;
        if ($file_extension) {
            $filename = $file_name . "." . $file_extension;
        }

        if ($filename) {
            $temp_path = $path;
            $flag = 0;
            while (1) {
                if ($flag) {
                    if ($file_extension) {
                        $filename = $file_name . "_" . $flag . "." . $file_extension;
                    } else {
                        $filename = $file_name . "_" . $flag;
                    }
                }

                if (file_exists($temp_path . $filename)) {
                    $flag++;
                    continue;
                } else {
                    break;
                }
            }
        }
        return $filename;
    }

    function invitations_fetching($orgdata) {
        $invitations_array = array(
            "pending_emails" => array(),
            "total_invitations_sent" => 0,
            "invitations_pending" => array("web" => 0, "app" => 0),
            "invitations_accepted" => 0
        );
        if (!empty($orgdata["Invite"])) {
            $invitation_sent = count($orgdata["Invite"]);
            $pending_invitation_emails = array();
            $invitations_pending["web"] = 0;
            $invitations_pending["app"] = 0;
            $invitations_accepted = 0;
            foreach ($orgdata["Invite"] as $invitations) {
                //$total_invited_emails[] = $invitations["email"];
                if ($invitations["flow"] == "web") {
                    $pending_invitation_emails[$invitations["id"]] = array("email" => $invitations["email"], "inviteflow" => $invitations["flow"]);
                    $invitations_pending["web"] ++;
                } else {
                    $pending_invitation_emails[$invitations["id"]] = array("email" => $invitations["email"], "inviteflow" => $invitations["flow"]);
                    $invitations_pending["app"] ++;
                }
//                else {
//                    $invitations_accepted++;
//                }
            }
            $invitations_array = array(
                "pending_emails" => $pending_invitation_emails,
                "total_invitations_sent" => $invitation_sent,
                "invitations_pending" => $invitations_pending,
                "invitations_accepted" => $invitations_accepted
            );
        }
        return $invitations_array;
    }

    function pending_requests($orgdata, $status) {
        $pendingreuest = array();
        if (!empty($orgdata)) {
            foreach ($orgdata as $requestdata) {
                if ($requestdata["OrgRequest"]["status"] == $status["status"]) {
                    $fname = $requestdata["User"]["fname"];
                    $lname = $requestdata["User"]["lname"];
                    $email = $requestdata["User"]["email"];
                    $mobile_number = $requestdata["OrgRequest"]["mobile_number"];
                    $relationship_to_org = $requestdata["OrgRequest"]["relationship_to_org"];
                    $relationship_to_org_desc = $requestdata["OrgRequest"]["relationship_to_org_desc"];
                    $why_want_to_join = $requestdata["OrgRequest"]["why_want_to_join"];
                    $pendingreuest[$requestdata["OrgRequest"]["id"]] = array("firstname" => $fname, "lastname" => $lname, "email" => $email,
                        "mobile_number" => $mobile_number, 'relationship_to_org' => $relationship_to_org, 'relationship_to_org_desc' => $relationship_to_org_desc,
                        "why_want_to_join" => $why_want_to_join);
                }
            }
        }
        return $pendingreuest;
    }

    function userorgcounter($userorg) {
        $counter["web"] = 0;
        $counter["app"] = 0;
        $OrganizationModel = ClassRegistry::init('Organization');
        foreach ($userorg as $UserOrganization) {
            $adminid = $OrganizationModel->field("admin_id", array("id" => $UserOrganization["organization_id"]));
            if (!empty($UserOrganization)) {
                if ($UserOrganization["flow"] == "web_invite") {
                    if ($UserOrganization["joined"] == 1 && $UserOrganization["status"] != 2 && $UserOrganization["user_id"] != $adminid) {
                        $counter["web"] ++;
                    }
                } else if ($UserOrganization["flow"] == "app_invite") {
                    if ($UserOrganization["joined"] == 1 && $UserOrganization["status"] != 2) {
                        $counter["app"] ++;
                    }
                }
            }
        }

        return $counter;
    }

    function sendPushNotification($deviceToken_msg_arr = null) {
//	return true;
	//die('Done');
        if (!empty($deviceToken_msg_arr)) {
            // Put your private key's passphrase here:
            //$pem_file = dirname(__FILE__) . '/'.'complianceprod.pem';
            //$location = dirname(__FILE__) . '/' . 'Certificates_new_1.pem'; //LOCAL
            //$location = dirname(__FILE__) . '/' . 'Certificates.pem';
            //$location = dirname(__FILE__) . '/' . 'pushcert.pem'; //LIVE
            //$location = dirname(__FILE__) . '/' . 'pushcert_29072018.pem'; //Stage
            //$location = dirname(__FILE__) . '/' . 'pushcert_new_1.pem'; //LIVE - 24-july-2018
            $location = dirname(__FILE__) . '/' . 'pushcert_production_24Sept2018.pem'; //pushcert_production_24Sept2018.pem

            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'local_cert', $location);
            //stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
            // Open a connection to the APNS server
            ////gateway.sandbox.push.apple.com
            $fp = stream_socket_client(
                    'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
//                    'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

            if (!$fp) {
                exit("Failed to connect: $err $errstr" . PHP_EOL);
            } else {
                //echo 'Connected to APNS' . PHP_EOL;
            }

            foreach ($deviceToken_msg_arr as $key => $val) {
                if (!empty($val['token']) && !empty($val['count']) && strlen($val['token']) > 10) {
                    $deviceToken = $val['token'];

                    $message = 'Hey Congrats!. You got a push notification.';
                    //$val['count']
                    $abc = (int) trim($val['count']);
                    $data = "";
                    if (isset($val['data']) && (!empty($val['data']))) {
                        $data = $val['data'];
                    }
                    $username = '';
                    if (isset($val['username']) && (!empty($val['username']))) {
                        $username = $val['username'];
                    }
                    $uName = "";
                    if (isset($username) && $username != null) {
                        $uName = $username;
                    }

                    $body['aps'] = array(
                        'alert' => $val['msg'],
                        'announcement' => $val['original_msg'],
                        'username' => $uName,
                        'data' => $data,
                        'username' => $username,
                        'sound' => 'expressnotification.wav',
                        'badge' => $abc,
                        'content-available' => 1 // Added by Babulal Prasad @8-july-18 after discuss with Dilbag(To fatch background notifications).
                    );

                    // Encode the payload as JSON
                    $payload = json_encode($body);

                    // Build the binary notification
                    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                    // Send it to the server
                    $result = fwrite($fp, $msg, strlen($msg));
//                    print_r($result);
                    if (!$result) {
                        // error not send notification
                        //echo 'message send error';
                    } else {
                        //pr($result);
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

    function functiontoinvite($email, $organization_id, $organization_name, $organization_code) {
//        $inviteModel = ClassRegistry::init('Invite');
//        $new_invite = array(
//            "email" => $email,
//            "organization_id" => $organization_id,
//            "invite_count" => 1,
//            "secret_code" => "",
//            "flow" => "web",
//        );
//        $inviteModel->save($new_invite, false);
        $viewVarsinvitations = array("org_name" => $organization_name, "org_code" => $organization_code);
        $configVarsinvitations = serialize($viewVarsinvitations);
        $subjectinvitation = "Invitation to join nDorse";
        $to = $email;
        $emailvar = array("to" => $to, "subject" => $subjectinvitation, "config_vars" => $configVarsinvitations, "template" => "invite");
        return $emailvar;
    }

    function functiontoreinvite($email, $organization_id, $organization_name, $organization_code) {
//        $inviteModel = ClassRegistry::init('Invite');
//        $new_invite = array(
//            "email" => $email,
//            "organization_id" => $organization_id,
//            "invite_count" => 1,
//            "secret_code" => "",
//            "flow" => "web",
//        );
//        $inviteModel->save($new_invite, false);
        $viewVarsinvitations = array("org_name" => $organization_name, "org_code" => $organization_code);
        $configVarsinvitations = serialize($viewVarsinvitations);
        $subjectinvitation = "Invitation to join nDorse";
        $to = $email;
        $emailvar = array("to" => $to, "subject" => $subjectinvitation, "config_vars" => $configVarsinvitations, "template" => "invite");
        return $emailvar;
    }

    function getcompanyinformation($companydetails) {
        $companydetail = array("image" => "", "name" => "", "shortname" => "", "street" => "", "city" => "", "state" => "", "zip" => "", "healthurl" => "");
        if (!empty($companydetails)) {
            $companydetail["image"] = $companydetails["image"];
            $companydetail["name"] = $companydetails["name"];
            $companydetail["shortname"] = $companydetails["short_name"];
            $companydetail["street"] = $companydetails["street"];
            $companydetail["city"] = $companydetails["city"];
            $companydetail["state"] = $companydetails["state"];
            $companydetail["zip"] = $companydetails["zip"];
            $companydetail["healthurl"] = $companydetails["health_url"];
            $companydetail["country"] = $companydetails["country"];
        }
        return $companydetail;
    }

    //============reinvite functionality
    function entryinvitetableexisting($orgdetails, $emails) {
        //=======hitting to invite table
//        $inviteModel = ClassRegistry::init('Invite');
//        if (!empty($emails)) {
//            foreach ($emails as $id => $email) {
//                if ($id == 0) {
//                    //==if data dont exist in table invite
//                    echo "data is not in invite table";
//                } else {
//                    $presentinvitecount = $inviteModel->field("invite_count", array("id" => $id));
//                    $inviteModel->id = $id;
//                    $val = array("invite_count" => $presentinvitecount + 1);
//                    $inviteModel->save($val, false);
//                }
//            }
//        }
    }

    function emailstoinvited($orgdetails, $email, $condition) {
        $EmailModel = ClassRegistry::init('Email');
        $UserModel = ClassRegistry::init('User');
        $UserorgModel = ClassRegistry::init('UserOrganization');
        $JoinOrgCodeModel = ClassRegistry::init('JoinOrgCode');
        //$email= base64_encode($email);
        $user = $UserModel->findByEmail($email);
        $user = $user['User'];
        $userOrg = $UserorgModel->find("first", array("conditions" => array("user_id" => $user["id"], "organization_id" => $orgdetails['id'])));

//        if (empty($userOrg)) {
        if ($condition == "app") {
            $viewVarsinvitations = array("org_name" => $orgdetails["name"], "org_code" => $orgdetails["code"]);
            $configVarsinvitations = serialize($viewVarsinvitations);
            $subjectinvitation = "Invitation to join nDorse";
            $emailvar = array("to" => $email, "subject" => $subjectinvitation, "config_vars" => $configVarsinvitations, "template" => "invite");
            $email = $EmailModel->create();
            $email = $EmailModel->save($emailvar);
            return "Mailed App";
        } else {

            if (empty($userOrg)) {
                $statusConfig = Configure::read("statusConfig");
                // get active users
                $params['conditions'] = array("organization_id" => $orgdetails['id'], "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
                $params['fields'] = array("COUNT(UserOrganization.user_id) as count");
                $userOrgStats = $UserorgModel->find("all", $params);
                $usercount = $userOrgStats[0][0]["count"];

                /* Check if User Exist */
                $existstatus = 1;
                $ucount = $usercount + 1;
                if ($ucount > $available_pool) {
                    $existstatus = 0;
                }
                $pool_type = "free";
                if ($ucount > 10) {
                    $pool_type = "paid";
                }
                $existingusers = $UserModel->findByEmail($email);
                if (!empty($existingusers)) {
                    $user_id = $user["id"];
                    $array_val = array(
                        "user_id" => $user_id,
                        "organization_id" => $orgdetails['id'],
                        "entity_id" => 0,
                        "department_id" => 0,
                        "job_title_id" => 0,
                        "pool_type" => $pool_type,
                        "status" => $existstatus,
                        "joined" => 0,
                        "flow" => "web_invite",
                        "send_invite" => 1
                    );
                    $UserorgModel->create();
                    $UserorgModel->save($array_val, false);
                    $userOrgId = $UserorgModel->id;
                    $userOrg = $UserorgModel->load($userOrgId);
                }
            }


            $joinedOrgCount = $UserorgModel->find("count", array("conditions" => array("user_id" => $user["id"], "joined" => 1)));

            if ($joinedOrgCount >= 1) {
                $template = "invitation_admin_existing";
                $passwordrandom = "";
            } else {
                $template = "invitation_admin";

                $passwordrandom = $this->randompasswordgenerator(8);
                $UserModel->id = $user["id"];
                $UserModel->saveField('password', $passwordrandom, false);
            }

            $joinCode = $JoinOrgCodeModel->find("first", array("conditions" => array("organization_id" => $orgdetails['id'], "user_id" => $user["id"], "is_expired" => 0)));

            if (empty($joinCode)) {
                $joinOrgCode = $this->getJoinOrgCode($orgdetails['id'], $email, $user["id"], $userOrg['UserOrganization']['id']);
            } else {
                $joinOrgCode = $joinCode['JoinOrgCode']['code'];
            }

            $viewVars = array('fname' => $user['fname'], 'username' => $user['username'], 'password' => $passwordrandom, 'organization_name' => $orgdetails['name'], "join_code" => $joinOrgCode, "no_switch" => false);

            /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
            $userIdEncrypted = base64_encode($user["id"]);
            $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
            $viewVars["pathToRender"] = $pathToRender;
            /*             * * */

            $configVars = serialize($viewVars);
            $subject = "Invitation to join nDorse";
            $emailQueue = array("to" => $user['email'], "subject" => $subject, "config_vars" => $configVars, "template" => $template);
            $EmailModel->create();
            $EmailModel->save($emailQueue);
            echo $EmailModel->id;
            echo "<hr>";
            return "Mailed App";
        }
    }

    function emailstoinvitedOld($orgdetails, $email, $condition) {
        $EmailModel = ClassRegistry::init('Email');
        $UserModel = ClassRegistry::init('User');
        $UserorgModel = ClassRegistry::init('UserOrganization');
        if ($condition == "app") {
            $viewVarsinvitations = array("org_name" => $orgdetails["name"], "org_code" => $orgdetails["code"]);
            $configVarsinvitations = serialize($viewVarsinvitations);
            $subjectinvitation = "Invitation to join nDorse";
            $emailvar = array("to" => $email, "subject" => $subjectinvitation, "config_vars" => $configVarsinvitations, "template" => "invite");
            $email = $EmailModel->create();
            $email = $EmailModel->save($emailvar);
            return "Mailed App";
        } else {
            $finduserdetail = $UserModel->findByEmail($email);
            $counter = $UserorgModel->find("count", array("conditions" => array("user_id" => $finduserdetail["User"]["id"])));
            if ($counter <= 1) {
                $passwordrandom = $this->randompasswordgenerator(8);
                $fname = $finduserdetail["User"]["fname"];
                $username = $finduserdetail["User"]["username"];
                $viewVars = array('fname' => $fname, 'username' => $username, 'password' => $passwordrandom, 'organization_name' => $orgdetails["name"], "organization_code" => $orgdetails["code"]);

                /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                $userIdEncrypted = base64_encode($data["User"]["id"]);
                $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                $viewVars["pathToRender"] = $pathToRender;
                /*                 * *** */

                $configVars = serialize($viewVars);
                $subject = "Invitation to join nDorse";
                $emailvar = array("to" => $email, "subject" => $subject, "config_vars" => $configVars, "template" => "invitation_admin");
                //==================to change pasword for the userid;
                $UserModel->id = $finduserdetail["User"]["id"];
                $UserModel->saveField('password', $passwordrandom, false);
                $EmailModel->Create();
                $email = $EmailModel->save($emailvar);
                return "Mailed Web";
            } else {
                $this->emailstoinvited($orgdetails, $email, "app");
            }
        }
    }

    function functiontoinviteifzero($email, $orgid) {
//        $InviteModel = ClassRegistry::init('Invite');
//        $new_invite = array(
//            "email" => $email,
//            "organization_id" => $orgid,
//            "invite_count" => 1,
//            "secret_code" => "",
//            "flow" => "web",
//        );
//        $InviteModel->save($new_invite, false);
    }

    //============End reinvite functionality
    //=======organization inactive delete and deactivate
    function statusmailingreport($org_id, $org_name, $orgstatus) {
        $UserModel = ClassRegistry::init('User');
        $UserOrganizationModel = ClassRegistry::init('UserOrganization');
        $EmailModel = ClassRegistry::init('Email');
        $userorgtabledata = $UserOrganizationModel->find("all", array("fields" => array("user_id", "user_role"), "conditions" => array("organization_id" => $org_id, "UserOrganization.status" => array(0, 1), "UserOrganization.user_role" => array(2, 3, 4))));
        foreach ($userorgtabledata as $usersdata) {
            $userid = $usersdata["UserOrganization"]["user_id"];
            $emailuserid = $UserModel->find("all", array("fields" => array("email", "fname"), "conditions" => array("id" => $userid)));
            if (!empty($emailuserid)) {
                $fname = $emailuserid[0]["User"]["fname"];
                $useremail = $emailuserid[0]["User"]["email"];
                if ($usersdata["UserOrganization"]["user_role"] == 3) {
                    $message = "This is to notify you that an administrator of " . $org_name . " has " . $orgstatus . " it.If it was not expected then please contact your organization's administrators.";
                    $viewVars = array("fname" => $fname, "message" => $message);

                    /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode($userid);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $viewVars["pathToRender"] = $pathToRender;
                    /*                     * */

                    $configVars = serialize($viewVars);
                    $subject = "nDorse Notification -- Organization " . $orgstatus . " by admin";
                    $emailQueue[] = array("to" => $useremail, "subject" => $subject, "config_vars" => $configVars, "template" => "org_status_action_web");
                } else {
                    $message = "This is to notify you that an administrator of " . $org_name . " has " . $orgstatus . " it.<br />If you have not initiated that or it is not required then please contact nDorse team at support@nDorse.net.";
                    $viewVars = array("fname" => $fname, "message" => $message);
                    /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode($userid);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $viewVars["pathToRender"] = $pathToRender;
                    /*                     * */
                    $configVars = serialize($viewVars);
                    $subject = "nDorse Notification -- Organization " . $orgstatus . " by admin";
                    $emailQueue[] = array("to" => $useremail, "subject" => $subject, "config_vars" => $configVars, "template" => "org_status_action_web");
                }
            }
        }
        if (!empty($emailuserid)) {
//            $EmailModel->saveMany($emailQueue);
        }
    }

    //=======user inactive delete and deactivate
    function changeuserstatusmail($org_name, $username, $fname, $actstatus, $loggeduser) {
        $EmailModel = ClassRegistry::init('Email');
        if ($loggeduser["role"] == 1) {
            $loggedusername = "Super Admin";
        } else {
            $loggedusername = trim($loggeduser['fname'] . " " . $loggeduser['lname']);
        }
        $subject = "Your nDorse login is " . $actstatus . " by your administrator";
        $viewVars = array("org_name" => $org_name, "status" => $actstatus, "username" => $username, "fname" => $fname, "admin_name" => $loggedusername);

        /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
        $userIdEncrypted = base64_encode($loggeduser["id"]);
        $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
        $viewVars["pathToRender"] = $pathToRender;
        /*         * ** */

        $configVars = serialize($viewVars);
        $emailQueue = array("to" => $username, "subject" => $subject, "config_vars" => $configVars, "template" => "org_action");
        $EmailModel->save($emailQueue);
    }

    function getorgandusers($userdata) {
        $OrganizationModel = ClassRegistry::init('Organization');
        $nooforg = "0";
        $noofusers = "0";
        foreach ($userdata as $users) {
            $user_id = $users['User']['id'];
            //no of organizations for admin
            $result = $OrganizationModel->find('all', array('conditions' => array("admin_id" => $user_id, "status" => array(0, 1))));
            $nooforg[$user_id] = count($result);
            $resultusers = 0;
            $user_role = array(3, 4);
            foreach ($result as $resultant) {
                $target_id = $resultant['Organization']['id'];
                //$resultusers+= $this->Common->getusersfororg($target_id, $user_role);
                $resultusers+= $this->getusersfororg($target_id, $user_role);
            }
            $noofusers[$user_id] = $resultusers;
        }
        return $result = array("nooforgs" => $nooforg, "noofusers" => $noofusers);
    }

    function getStartAndEndDate($week, $year) {
        $time = strtotime("1 January $year", time());
        $day = date('w', $time);
        $time += ((7 * $week) + 1 - $day) * 24 * 3600;
        $return = date('Y-n-j', $time);

        return $return;
    }

    //=====to binding modal for export and leaderboard in reports and charts
    function commonleaderboardbindings($conditionscountendorsement) {
        $UserModel = ClassRegistry::init('User');
        $UserOrganizationModel = ClassRegistry::init('UserOrganization');
        $UserModel->bindModel(array(
            'hasMany' => array(
                'endorsement_one_did' => array(
                    'className' => 'Endorsement',
                    'foreignKey' => "endorser_id",
                    'fields' => array('count(endorsement_one_did.endorser_id) as onedid'),
                    'conditions' => $conditionscountendorsement
                ),
                'endorsement_one_got' => array(
                    'className' => 'Endorsement',
                    'foreignKey' => "endorsed_id",
                    //'conditions' => array("endorsement_for = 'user'"),
                    'fields' => array('count(endorsement_one_got.endorsed_id) as onegot'),
                    'conditions' => $conditionscountendorsement
                ),
            ),
        ));

        $UserOrganizationModel->bindModel(array(
            'hasOne' => array(
                'OrgDepartment' => array(
                    'className' => 'OrgDepartment',
                    'foreignKey' => false,
                    'fields' => array("name"),
                    'conditions' => array("OrgDepartment.id = UserOrganization.department_id"),
                ),
                'Entity' => array(
                    'className' => 'Entity',
                    'foreignKey' => false,
                    'fields' => array("name"),
                    'conditions' => array("Entity.id = UserOrganization.entity_id"),
                )
            ),
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'fields' => array("User.id", "User.fname", "User.lname"),
                ),
            ),
        ));
    }

    function arrayforendorsementdetail($endorsementdata) {
        $arrayendorsementdetail = array();
        foreach ($endorsementdata as $dataendorsement) {
            $noofendorser = 0;
            if (isset($dataendorsement["User"]["endorsement_one_did"][0]) && !empty($dataendorsement["User"]["endorsement_one_did"])) {
                $noofendorser = $dataendorsement["User"]["endorsement_one_did"][0]["endorsement_one_did"][0]["onedid"];
            }
            $noofendorsed = 0;
            if (isset($dataendorsement["User"]["endorsement_one_got"][0]) && !empty($dataendorsement["User"]["endorsement_one_got"])) {
                $noofendorsed = $dataendorsement["User"]["endorsement_one_got"][0]["endorsement_one_got"][0]["onegot"];
            }
            $arrayendorsementdetail[] = array(
                "userid" => $dataendorsement["User"]["id"],
                "name" => ucfirst($dataendorsement["User"]["fname"]) . " " . ucfirst($dataendorsement["User"]["lname"]),
                "endorser" => $noofendorser,
                "endorsed" => $noofendorsed,
                "department" => $dataendorsement["OrgDepartment"]["name"],
                "entity" => $dataendorsement["Entity"]["name"],
            );
        }
        return $arrayendorsementdetail;
    }

    //=============values for endorsement data and endorsed data for leader board
    function allvaluesendorser($endorser_data, $departments, $entities) {
        $UserModel = ClassRegistry::init('User');
        $allvaluesendorser = array();
        foreach ($endorser_data as $dataendorsement) {
            $corevaluesid = array();

            $endorsed_id = $dataendorsement["Endorsement"]["endorsed_id"];
            $endorsement_date = $dataendorsement["Endorsement"]["created"];
            $endorsement_message = $dataendorsement["Endorsement"]["message"];
            $total_points = count($dataendorsement["EndorseCoreValues"]);
            foreach ($dataendorsement["EndorseCoreValues"] as $corevaluesdata) {
                $corevaluesid[] = $corevaluesdata["value_id"];
            }
            if ($dataendorsement["Endorsement"]["endorsement_for"] == "user") {
                $endorsernamedetail = $UserModel->findById($endorsed_id, array("User.fname", "User.lname"));
                $endorsername = ucfirst($endorsernamedetail["User"]["fname"]) . " " . ucfirst($endorsernamedetail["User"]["lname"]);
            } else if ($dataendorsement["Endorsement"]["endorsement_for"] == "department") {
                $endorsername = $departments[$endorsed_id];
            } else if ($dataendorsement["Endorsement"]["endorsement_for"] == "entity") {
                $endorsername = $entities[$endorsed_id];
            }

            $allvaluesendorser[$dataendorsement["Endorsement"]["id"]] = array(
                "name" => $endorsername,
                "date" => $endorsement_date,
                "endorsement_message" => $endorsement_message,
                "totalpoints" => $total_points,
                "corevaluesid" => $corevaluesid,
            );
        }
        return $allvaluesendorser;
    }

    function allvaluesendorsed($endorsed_data) {
        $UserModel = ClassRegistry::init('User');
        $allvaluesendorsed = array();
        foreach ($endorsed_data as $dataendorsement) {
            $corevaluesid = array();
            $endorser_id = $dataendorsement["Endorsement"]["endorser_id"];
            $endorsement_date = $dataendorsement["Endorsement"]["created"];
            $endorsed_message = $dataendorsement["Endorsement"]["message"];
            $total_points = count($dataendorsement["EndorseCoreValues"]);
            foreach ($dataendorsement["EndorseCoreValues"] as $corevaluesdata) {
                $corevaluesid[] = $corevaluesdata["value_id"];
            }
            $endorsernamedetail = $UserModel->findById($endorser_id, array("User.fname", "User.lname"));
            $endorsername = ucfirst($endorsernamedetail["User"]["fname"]) . " " . ucfirst($endorsernamedetail["User"]["lname"]);
            $allvaluesendorsed[$dataendorsement["Endorsement"]["id"]] = array(
                "name" => $endorsername,
                "date" => $endorsement_date,
                "endorsed_message" => $endorsed_message,
                "totalpoints" => $total_points,
                "corevaluesid" => $corevaluesid,
            );
        }
        return $allvaluesendorsed;
    }

    //==========fucntion to check if orgid is perfect for orgowners
    function checkorgid($organization_id) {

        // $OrganizationModel = ClassRegistry::init('Organization');
        $OrganizationModel = ClassRegistry::init('UserOrganization');
        $result = "perfect";
        //$orgids = $OrganizationModel->find('all', array('conditions' => array('Organization.status' => array(0, 1), 'Organization.admin_id' => $this->Auth->user("id"))));
        //foreach ($orgids as $orgid) {
        //    $checkorgids[] = $orgid["Organization"]["id"];
        //}
        // $conditions = array('Organization.status' => array(0, 1), 'Organization.admin_id' => $logged_in_user_id);
        // $conditions = array('Organization.status' => array(0, 1), 'UserOrganization.user_role' => 2,'UserOrganization.user_id' => $logged_in_user_id,'UserOrganization.status' => 1);
        $userorgdata = $OrganizationModel->find("all", array("conditions" => array("user_id" => $this->Auth->user("id"), "user_role" => array(2, 6), 'UserOrganization.status' => 1)));

        $checkorgids = array();
        foreach ($userorgdata as $uservalorg) {

            $checkorgids[] = $uservalorg["UserOrganization"]["organization_id"];
        }

        //======to check if id is of present logged in user or not for user role = admin
        if ($this->Auth->User('role') > 1 && !in_array($organization_id, $checkorgids)) {
            $result = "redirect";
        }

        return $result;
    }

    //===========function to enroll common part btn zooming and history department graph
    function historydatadeptweek($organization_id) {
        $EndorsementModel = ClassRegistry::init('Endorsement');
        $EndorsementModel->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
        $paramsdepthistory["conditions"] = array("Endorsement.organization_id" => $organization_id, "Endorsement.endorsement_for" => "department");
        $paramsdepthistory["fields"] = ("*");
        $paramsdepthistory["group"] = ("WEEKOFYEAR(date(Endorsement.created)), Endorsement.endorsed_id");
        if ($startdate != "" and $enddate != "") {
            array_push($paramsdepthistory["conditions"], "date(Endorsement.created) between '$startdate' and '$enddate'");
        }
        $EndorsementModel->virtualFields['weekdepartment'] = "WEEKOFYEAR(date(Endorsement.created))";
        $EndorsementModel->virtualFields['yeardepartment'] = "year(date(Endorsement.created))";
        $EndorsementModel->virtualFields['endorseddepartment'] = "count(Endorsement.endorsed_id)";

        $EndorsementModel->bindModel(array(
            'hasOne' => array(
                'OrgDepartment' => array(
                    'className' => 'OrgDepartment',
                    'foreignKey' => false,
                    'conditions' => array("OrgDepartment.id = Endorsement.endorsed_id"),
                )
        )));

        $endorsementbydeptweek = $EndorsementModel->find("all", $paramsdepthistory);
        //echo $this->Endorsement->getLastQuery();die;
        //pr($endorsementbydeptweek);

        return $endorsementbydeptweek;
    }

    function allvaluesendorsementcount($allendorsement, $departments, $entities, $filter = "endorsement") {
//        $UserModel = ClassRegistry::init('User');
        $allvaluesendorsement = array();
        foreach ($allendorsement as $endorsementdataall) {
            $endorsedid = "";
            $endorserid = "";

            $corevaluesid = array();
            if ($filter == "userorganization") {
                foreach ($endorsementdataall["Endorsement"]["EndorseCoreValues"] as $corevaluesdata) {
                    $corevaluesid[] = $corevaluesdata["value_id"];
                }
                $total_points = count($endorsementdataall["Endorsement"]["EndorseCoreValues"]);
            } else {
                foreach ($endorsementdataall["EndorseCoreValues"] as $corevaluesdata) {
                    $corevaluesid[] = $corevaluesdata["value_id"];
                }
                $total_points = count($endorsementdataall["EndorseCoreValues"]);
            }

            $endorsement_date = $endorsementdataall["Endorsement"]["created"];
            $endorsement_message = $endorsementdataall["Endorsement"]["message"];
            $imagecount = $endorsementdataall["Endorsement"]["image_count"];
            $emojiscount = $endorsementdataall["Endorsement"]["emojis_count"];

            $allvaluesendorsement[$endorsementdataall["Endorsement"]["id"]] = array(
                "endorserid" => $endorserid,
                "endorsedid" => $endorsedid,
                "date" => $endorsement_date,
                "endorsement_message" => $endorsement_message,
                "totalpoints" => $total_points,
                "corevaluesid" => $corevaluesid,
                "imagecount" => $imagecount,
                "emojiscount" => $emojiscount,
            );
        }

        return $allvaluesendorsement;
    }

    function allvaluesendorsement($allendorsement, $departments, $entities, $filter = "endorsement") {
        $UserModel = ClassRegistry::init('User');
        $allvaluesendorsement = array();
        foreach ($allendorsement as $endorsementdataall) {
            $endorsedid = "";
            $endorserid = "";
            $endorserdetail = $UserModel->findById($endorsementdataall["Endorsement"]["endorser_id"], array("id", "fname", "lname"));
            $endorserid = $endorserdetail["User"]["id"];
            $endorsername = ucfirst($endorserdetail["User"]["fname"]) . " " . ucfirst($endorserdetail["User"]["lname"]);

            if ($endorsementdataall["Endorsement"]["endorsement_for"] == "user") {
                $endorseddetail = $UserModel->findById($endorsementdataall["Endorsement"]["endorsed_id"], array("User.id", "User.fname", "User.lname"));
                $endorsedid = $endorseddetail["User"]["id"];
                $endorsedname = ucfirst($endorseddetail["User"]["fname"]) . " " . ucfirst($endorseddetail["User"]["lname"]);
            } else if ($endorsementdataall["Endorsement"]["endorsement_for"] == "department") {
                $endorsedname = $departments[$endorsementdataall["Endorsement"]["endorsed_id"]];
            } else if ($endorsementdataall["Endorsement"]["endorsement_for"] == "entity") {
                $endorsedname = $entities[$endorsementdataall["Endorsement"]["endorsed_id"]];
            }
            $corevaluesid = array();
            if ($filter == "userorganization") {
                foreach ($endorsementdataall["Endorsement"]["EndorseCoreValues"] as $corevaluesdata) {
                    $corevaluesid[] = $corevaluesdata["value_id"];
                }
                $total_points = count($endorsementdataall["Endorsement"]["EndorseCoreValues"]);
            } else {
                foreach ($endorsementdataall["EndorseCoreValues"] as $corevaluesdata) {
                    $corevaluesid[] = $corevaluesdata["value_id"];
                }
                $total_points = count($endorsementdataall["EndorseCoreValues"]);
            }

            $endorsement_date = $endorsementdataall["Endorsement"]["created"];
            $endorsement_message = $endorsementdataall["Endorsement"]["message"];
            $imagecount = $endorsementdataall["Endorsement"]["image_count"];
            $emojiscount = $endorsementdataall["Endorsement"]["emojis_count"];

            $allvaluesendorsement[$endorsementdataall["Endorsement"]["id"]] = array(
                "endorserid" => $endorserid,
                "endorsedid" => $endorsedid,
                "endorsername" => $endorsername,
                "endorsedname" => $endorsedname,
                "date" => $endorsement_date,
                "endorsement_message" => $endorsement_message,
                "totalpoints" => $total_points,
                "corevaluesid" => $corevaluesid,
                "imagecount" => $imagecount,
                "emojiscount" => $emojiscount,
            );
        }

        return $allvaluesendorsement;
    }

    function sendpushnotiforglobalsettings($userid, $content, $senderID) {

        /* code with HTML tag start */
        $content_1 = str_replace('&nbsp;', ' ', $content);
        $content_1 = str_replace('<br>', '/n', $content_1);
        $content_1 = html_entity_decode($content_1);
        /* code with HTML tag end */


        $content = strip_tags($content);
        $content = str_replace('&nbsp;', ' ', $content);
        $content = str_replace('<br>', '/n', $content);
        $content = html_entity_decode($content);

        $deviceToken_msg_arr_ios = array();
        $deviceToken_msg_arr_android = array();
        if (!empty($userid)) {
            $LoginStatisticsModel = ClassRegistry::init('LoginStatistics');
            $defaultOrgModel = ClassRegistry::init('DefaultOrg');
            $AlertCenterNotificationModel = ClassRegistry::init('AlertCenterNotification');
            $loginstatisticsdata = $LoginStatisticsModel->find("all", array("fields" => array("device_id", "os", "user_id"), "conditions" => array("user_id" => $userid, "live" => 1)));
            $parameter = array();
            $deviceToken_msg_arr = array();
            foreach ($loginstatisticsdata as $datalogin) {
                $token = $datalogin["LoginStatistics"]["device_id"];
//                pr($token);                continue;
                $user_ID = $datalogin["LoginStatistics"]["user_id"];
                if ($token != "" && strtolower($datalogin["LoginStatistics"]["os"]) == "ios") {
                    $UserModel = ClassRegistry::init('User');
                    $userData = $UserModel->findById($senderID);
//                    echo "sender ID";
//                    pr($senderID);
//                    pr($userData); exit;
                    $UserFullName = '';
                    if (!empty($userData) && count($userData) > 0) {
                        $UserFullName = $userData['User']['fname'] . " " . $userData['User']['lname'];
                    }
                    $deviceToken_msg_arr_ios[] = array('token' => $token, 'count' => '1', 'msg' => $content, "data" => $parameter, 'original_msg' => $content_1, 'username' => $UserFullName);

                    /** Added by Babulal Prasad @7-june-2018
                     * * SAVE DATA for alert center feature Start
                     * ** */
                    $dafultOrgData = $defaultOrgModel->find('all', array('fields' => array('DefaultOrg.organization_id'), 'conditions' => array('user_id' => $user_ID, 'DefaultOrg.status' => '1')));
                    $orgID = 0;
                    if (!empty($dafultOrgData) && count($dafultOrgData) > 0) {
                        $orgID = $dafultOrgData[0]['DefaultOrg']['organization_id'];
                    }
                    $AlertCenterNotificationArray = array();
                    //$AlertCenterNotificationModel->save($AlertCenterNotificationArray);
                    $AlertCenterNotificationArray['user_id'] = $user_ID;
                    $AlertCenterNotificationArray['org_id'] = $orgID;
                    $AlertCenterNotificationArray['alert_type'] = 'Alert Notification';
                    $AlertCenterNotificationArray['plain_msg'] = $content;
                    $AlertCenterNotificationArray['original_msg'] = $content_1;
                    $AlertCenterNotificationArray['os'] = 'ios';
                    $AlertCenterNotificationArray['status'] = 0;
                    $AlertCenterNotificationModel->save($AlertCenterNotificationArray);
                    /* SAVE DATA for alert center feature End** */
                } else if ($token != "" && strtolower($datalogin["LoginStatistics"]["os"]) == "android") {

                    $UserModel = ClassRegistry::init('User');
                    $userData = $UserModel->findById($senderID);
//                    echo "sender OD";
//                    pr($senderID);
//                    pr($userData); exit;
                    $UserFullName = '';
                    if (!empty($userData) && count($userData) > 0) {
                        $UserFullName = $userData['User']['fname'] . " " . $userData['User']['lname'];
                    }
                    //$parameter = array("org_id" => 1, "category" => "SwitchAction", "notification_type" => "post_promotion",
                    //       "title" => "nDorse App");
                    $deviceToken_msg_arr_android[] = array('token' => $token, 'count' => '1', 'msg' => $content_1, "data" => $parameter, 'username' => $UserFullName);
                    /** Added by Babulal Prasad @7-june-2018
                     * * SAVE DATA for alert center feature Start
                     * ** */
                    $dafultOrgData = $defaultOrgModel->find('all', array('fields' => array('DefaultOrg.organization_id'), 'conditions' => array('user_id' => $user_ID, 'DefaultOrg.status' => '1')));
                    $orgID = 0;
                    if (!empty($dafultOrgData) && count($dafultOrgData) > 0) {
                        $orgID = $dafultOrgData[0]['DefaultOrg']['organization_id'];
                    }
                    $AlertCenterNotificationArray = array();
                    //$AlertCenterNotificationModel->save($AlertCenterNotificationArray);
                    $AlertCenterNotificationArray['user_id'] = $user_ID;
                    $AlertCenterNotificationArray['org_id'] = $orgID;
                    $AlertCenterNotificationArray['alert_type'] = 'Alert Notification';
                    $AlertCenterNotificationArray['plain_msg'] = $content;
                    $AlertCenterNotificationArray['original_msg'] = $content_1;
                    $AlertCenterNotificationArray['os'] = 'android';
                    $AlertCenterNotificationArray['status'] = 0;
                    $AlertCenterNotificationModel->save($AlertCenterNotificationArray);
                    /* SAVE DATA for alert center feature End** */
                }
            }

            $username = '';
            if (!empty($deviceToken_msg_arr_ios)) {
                echo $this->sendPushNotification($deviceToken_msg_arr_ios);
            }
            if (!empty($deviceToken_msg_arr_android)) {
                echo $this->sendPushNotificationAndroid($deviceToken_msg_arr_android);
            }
        }
    }

    //===========emails and push notifications when he wrote emails to an organizations
    function mailstoorganizationsusers($emailsarray, $content, $attachment, $schedled_time = '0000-00-00 00:00:00', $scheduledPost = 0, $announcementID, $senderID = 0) {
        $GlobalEmailModel = ClassRegistry::init('Globalemail');
        $AnnouncementModel = ClassRegistry::init('Announcement');
        //$LoginStatisticsModel = ClassRegistry::init('LoginStatistics');

        $emailsexisted = array();
        foreach ($emailsarray as $emaildetails) {
            $usersid[] = $emaildetails["id"];
            if (in_array($emaildetails["email"], $emailsexisted) || $emaildetails["email"] == "") {
                continue;
            }
            $to = $emaildetails["email"];
            //=======pushing emails to array to check if already sent
            array_push($emailsexisted, $to);
            $subject = "Announcement";
            $config_vars = array("userid" => $emaildetails["id"], "for" => "mailing", "name" => $emaildetails["name"], "bodymsg" => "Admin sents you a Message, please go through the following message:-", "msg" => $content, "attached" => $attachment);

            /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
            $userIdEncrypted = base64_encode($emaildetails["id"]);
            $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
            $config_vars["pathToRender"] = $pathToRender;
            /*             * * */

            $config_vars = serialize($config_vars);
            $emailparamenters[] = array("to" => $to, "subject" => $subject, "config_vars" => $config_vars, "template" => "globalsettingsmail", 'scheduled_time' => $schedled_time, 'announcement_id' => $announcementID);
        }
        //$usersid = array_unique($usersid);
        if ($scheduledPost == 0) {

            $deviceToken_msg_arr = $this->sendpushnotiforglobalsettings($usersid, $content, $senderID);

            $AnnouncementModel->deleteAll(array(
                'id' => $announcementID,
            ));
        }
        //pr($emailparamenters); exit;
        //send mails to all as per selections
        $result = $GlobalEmailModel->saveMany($emailparamenters);
//        echo $GlobalEmailModel->getLastQuery();
//        pr($result); exit;
    }

    //===========emails and push notifications when he wrote emails to an organizations
    function updatemailstoorganizationsusers($emailsarray, $content, $attachment, $schedled_time = '0000-00-00 00:00:00', $scheduledPost = 0, $announcementID, $senderID) {
        $GlobalEmailModel = ClassRegistry::init('Globalemail');
        //$LoginStatisticsModel = ClassRegistry::init('LoginStatistics');

        $emailsexisted = array();
        foreach ($emailsarray as $emaildetails) {
            $usersid[] = $emaildetails["id"];
            if (in_array($emaildetails["email"], $emailsexisted) || $emaildetails["email"] == "") {
                continue;
            }
            $to = $emaildetails["email"];
            //=======pushing emails to array to check if already sent
            array_push($emailsexisted, $to);
            $subject = "Announcement";
            $config_vars = array("userid" => $emaildetails["id"], "for" => "mailing", "name" => $emaildetails["name"], "bodymsg" => "Admin sents you a Message, please go through the following message:-", "msg" => $content, "attached" => $attachment);
            $config_vars = serialize($config_vars);
            $emailparamenters[] = array("to" => $to, "subject" => $subject, "config_vars" => $config_vars, "template" => "globalsettingsmail", 'scheduled_time' => $schedled_time, 'announcement_id' => $announcementID);
        }
        //$usersid = array_unique($usersid);
        if ($scheduledPost == 0) {
            //$deviceToken_msg_arr = $this->sendpushnotiforglobalsettings($usersid, $content,$senderID);
        }
//        pr($emailparamenters); //exit;
        //send mails to all as per selections
        $GlobalEmailModel->deleteAll(array(
            'Globalemail.announcement_id' => $announcementID,
        ));
        
        $result = $GlobalEmailModel->saveMany($emailparamenters);
//        pr($result);
//        exit;
//        echo $GlobalEmailModel->getLastQuery();
//        pr($result); exit;
    }

    //=============emails notifications to all users where terms and conditions are changed
    function emailsfortermsandcoditions($content) {
        $GlobalEmailModel = ClassRegistry::init('Globalemail');
        $UserModel = ClassRegistry::init('User');
        $conditions = array("status" => array(1), "role" => array(2, 3));
        $userdata = $UserModel->find("all", array("fields" => array("id", "email", "fname"), "conditions" => $conditions));
        $subject = "Terms and Conditions has been updated";
        $content = "nDorse LLC has updated the Terms and Conditions, please go through it once again.<br>" . $content;
        foreach ($userdata as $emailsdata) {
            $usersid[] = $emailsdata["User"]["id"];
            $config_vars = array("userid" => $emailsdata["User"]["id"], "for" => "tandc", "name" => $emailsdata["User"]["fname"], "bodymsg" => "Terms and Conditions are update, please check the following message:-", "msg" => $content);
            $config_vars = serialize($config_vars);
            $emailparamenters[] = array("to" => $emailsdata["User"]["email"], "subject" => $subject, "config_vars" => $config_vars, "template" => "globalsettingsmail");
        }
        //$deviceToken_msg_arr = $this->sendpushnotiforglobalsettings($usersid, $content);
        $GlobalEmailModel->saveMany($emailparamenters);
    }

    //========reports and charts by job title common model binding
    function bindmodelcommonjobtitle() {
        $EndorsementlModel = ClassRegistry::init('Endorsement');
        $UserOrganizationModel = ClassRegistry::init('UserOrganization');
        $EndorsementlModel->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
        $UserOrganizationModel->unbindModel(array('belongsTo' => array('Organization', 'User')));
        $UserOrganizationModel->bindModel(array(
            "belongsTo" => array(
                "Endorsement" => array(
                    "className" => "Endorsement",
                    "foreignKey" => false,
                    "conditions" => array("UserOrganization.user_id = Endorsement.endorsed_id or UserOrganization.user_id = Endorsement.endorser_id")
                )
            ),
        ));
    }

    function getSubscriptionPlans($list = true) {
        $PlansModal = ClassRegistry::init('SubscriptionPlan');

        $plans = $PlansModal->find("all", array());

        if ($list) {
            $planList = array();
            foreach ($plans as $plan) {
                $planList[$plan['SubscriptionPlan']['id']] = $plan['SubscriptionPlan'];
            }
            return $planList;
        } else {
            return $plans;
        }
    }

    public function dateConvertServer($dategetter) {
        if ($dategetter == "") {
            return "";
        }
        $date = DateTime::createFromFormat('m-d-Y', $dategetter);
        return $date->format('Y-m-d');
    }

    public function dateConvertDisplay($dategetter) {
        if ($dategetter == "0000-00-00") {
            return "";
        }
        $date = DateTime::createFromFormat('Y-m-d', $dategetter);
        return $date->format('m-d-Y');
    }

    public function endorsementformonth($organization_id) {
        $EndorsementModal = ClassRegistry::init('Endorsement');
        return $EndorsementModal->find("count", array("conditions" => array("month(created) = month(NOW())", "year(created) = year(NOW())", "organization_id" => $organization_id, 'status' => 1)));
    }

    public function trimminguserdata($dataarray) {
        $dataval = "";
        $alldataarray = array();
        foreach ($dataarray as $value) {
            if ($value != "" && $value != "other") {
                $alldataarray[] = $value;
            }
        }
        if (!empty($alldataarray)) {
            $dataval = implode(", ", $alldataarray);
        }
        return $dataval;
    }

    function sendPushNotificationAndroidcommon($deviceToken_msg_arr = null) {
        if (!empty($deviceToken_msg_arr)) {
//            print_r($deviceToken_msg_arr);
            // foreach ($deviceToken_msg_arr1 as $deviceToken_msg_arr) {

            $registrationIds = array($deviceToken_msg_arr["token"]);
            $API_ACCESS_KEY_GOOGLE = Configure::read("API_ACCESS_KEY_GOOGLE");
// prep the bundle
            $msgtext = $deviceToken_msg_arr["msg"];
            $orgarray = $deviceToken_msg_arr["data"];
            $username = '';
            if (isset($deviceToken_msg_arr["username"]) && $deviceToken_msg_arr["username"] != '') {
                $username = $deviceToken_msg_arr["username"];
            }
            $data = array();
            $data["message"] = $msgtext;
            if (!empty($orgarray)) {
                if (isset($orgarray["org_id"]) && $orgarray["org_id"] > 0) {
                    $data["org_id"] = $orgarray["org_id"];
                }
                if (isset($orgarray["category"]) && $orgarray["category"] != "") {
                    $data["category"] = $orgarray["category"];
                }
                if (isset($orgarray["notification_type"]) && $orgarray["notification_type"] != "") {
                    $data["notification_type"] = $orgarray["notification_type"];
                }
                if (isset($orgarray["title"]) && $orgarray["title"] != "") {
                    $data["title"] = $orgarray["title"];
                }
                if (isset($orgarray["is_reply"]) && $orgarray["is_reply"] != "") {
                    $data["is_reply"] = $orgarray["is_reply"];
                }
                if (isset($username) && $username != "") {
                    $data["username"] = $username;
                }
            }
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
                'data' => $data
            );
            json_encode($fields);


            $headers = array
                (
                'Authorization: key=' . $API_ACCESS_KEY_GOOGLE,
                'Content-Type: application/json'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
//echo $result;
            $res = json_decode($result);

            if ($res->success == 1) {
//                    echo $res->success;
                return true;
            } else {
                return false;
            }
            // }
        }
        //  die('Done');
    }

    function sendPushNotificationAndroid($deviceToken_msg_arr1 = null) {
        if (!empty($deviceToken_msg_arr1)) {
//            print_r($deviceToken_msg_arr1);
            foreach ($deviceToken_msg_arr1 as $deviceToken_msg_arr) {

                $registrationIds = array($deviceToken_msg_arr["token"]);
                $API_ACCESS_KEY_GOOGLE = Configure::read("API_ACCESS_KEY_GOOGLE");
// prep the bundle
                $msgtext = $deviceToken_msg_arr["msg"];
                $orgarray = $deviceToken_msg_arr["data"];
                $username = '';
                if (isset($deviceToken_msg_arr["username"]) && $deviceToken_msg_arr["username"] != '') {
                    $username = $deviceToken_msg_arr["username"];
                }
//                $username = 'test username';
                $orgarray = $deviceToken_msg_arr["data"];
                $data = array();
                $data["message"] = $msgtext;
                if (!empty($orgarray)) {
                    if (isset($orgarray["org_id"]) && $orgarray["org_id"] > 0) {
                        $data["org_id"] = $orgarray["org_id"];
                    }
                    if (isset($orgarray["category"]) && $orgarray["category"] != "") {
                        $data["category"] = $orgarray["category"];
                    }
                    if (isset($orgarray["notification_type"]) && $orgarray["notification_type"] != "") {
                        $data["notification_type"] = $orgarray["notification_type"];
                    }
                    if (isset($orgarray["title"]) && $orgarray["title"] != "") {
                        $data["title"] = $orgarray["title"];
                    }
                    if (isset($orgarray["is_reply"]) && $orgarray["is_reply"] != "") {
                        $data["is_reply"] = $orgarray["is_reply"];
                    }
                }
                if (isset($username) && $username != "") {
                    $data["username"] = $username;
                }
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
                    'data' => $data
                );
                json_encode($fields);


                $headers = array
                    (
                    'Authorization: key=' . $API_ACCESS_KEY_GOOGLE,
                    'Content-Type: application/json'
                );
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
                curl_close($ch);
//echo $result;
                $res = json_decode($result);

                if ($res->success == 1) {
//                    echo $res->success;
                    return true;
                } else {
                    return false;
                }
            }
        }

        //  die('Done');
    }

    function announcementspostdata($organizationslist, $content, $attachment = "", $userList = array(), $departmentList = array(), $suborgList = array(), $scheduled = 0, $UTCTimeToPost = '0000-00-00 00:00:00', $announcementID = 0, $senderID = 0) {
        $UserOrganizationModal = ClassRegistry::init('UserOrganization');
//        if (!empty($organizationslist)) {

        $conditionsArray["conditions"]["OR"][] = array('organization_id' => $organizationslist);
        $conditionsArray["conditions"]["UserOrganization.status"] = 1;

        if (isset($userList) && count($userList) > 0) {
            $conditionsArray["conditions"]["OR"][] = array('user_id' => $userList);
        }
        if (isset($departmentList) && count($departmentList) > 0) {
            $conditionsArray["conditions"]["OR"][] = array('department_id' => $departmentList);
        }
        if (isset($suborgList) && count($suborgList) > 0) {
            $conditionsArray["conditions"]["OR"][] = array('entity_id' => $suborgList);
        }
//            pr($conditionsArray);
//            exit;
        $userorgdata = $UserOrganizationModal->find("all", $conditionsArray);
        //$userorgdata = $UserOrganizationModal->find("all", array("conditions" => array("organization_id" => $organizationslist, "UserOrganization.status" => 1,)));
//            echo $UserOrganizationModal->getLastQuery();


        foreach ($userorgdata as $userdatafromorg) {
            $emailsarray[$userdatafromorg["User"]["email"]] = array(
                "id" => $userdatafromorg["User"]["id"],
                "name" => $userdatafromorg["User"]["fname"],
                "email" => $userdatafromorg["User"]["email"]);
        }
        //pr($emailsarray); exit;

        if (!empty($emailsarray)) {
            $this->mailstoorganizationsusers($emailsarray, $content, $attachment, $UTCTimeToPost, $scheduled, $announcementID, $senderID);
            $this->Session->setFlash(__('Push notification sent to all user.'), 'default', array('class' => 'alert alert-warning'));
        } else {
            $this->Session->setFlash(__('No Users Exist for selected Organizations'), 'default', array('class' => 'alert alert-warning'));
        }
//        } else {
//            $this->Session->setFlash(__('Select atleast a Organization'), 'default', array('class' => 'alert alert-warning'));
//        }
    }

    function updateannouncementspostdata($organizationslist, $content, $attachment = "", $userList = array(), $departmentList = array(), $suborgList = array(), $scheduled = 0, $UTCTimeToPost = '0000-00-00 00:00:00', $announcementID = 0, $senderID = 0) {
        $UserOrganizationModal = ClassRegistry::init('UserOrganization');
//        if (!empty($organizationslist)) {

        $conditionsArray["conditions"]["OR"][] = array('organization_id' => $organizationslist);
        $conditionsArray["conditions"]["UserOrganization.status"] = 1;

        if (isset($userList) && count($userList) > 0) {
            $conditionsArray["conditions"]["OR"][] = array('user_id' => $userList);
        }
        if (isset($departmentList) && count($departmentList) > 0) {
            $conditionsArray["conditions"]["OR"][] = array('department_id' => $departmentList);
        }
        if (isset($suborgList) && count($suborgList) > 0) {
            $conditionsArray["conditions"]["OR"][] = array('entity_id' => $suborgList);
        }
//            pr($conditionsArray);
//            exit;
        $userorgdata = $UserOrganizationModal->find("all", $conditionsArray);
        //$userorgdata = $UserOrganizationModal->find("all", array("conditions" => array("organization_id" => $organizationslist, "UserOrganization.status" => 1,)));
//            echo $UserOrganizationModal->getLastQuery();


        foreach ($userorgdata as $userdatafromorg) {
            $emailsarray[$userdatafromorg["User"]["email"]] = array(
                "id" => $userdatafromorg["User"]["id"],
                "name" => $userdatafromorg["User"]["fname"],
                "email" => $userdatafromorg["User"]["email"]);
        }
        //pr($emailsarray); exit;
        if (!empty($emailsarray)) {
            $this->updatemailstoorganizationsusers($emailsarray, $content, $attachment, $UTCTimeToPost, $scheduled, $announcementID, $senderID);
            $this->Session->setFlash(__('Push notification sent to all user.'), 'default', array('class' => 'alert alert-warning'));
        } else {
            $this->Session->setFlash(__('No Users Exist for selected Organizations'), 'default', array('class' => 'alert alert-warning'));
        }
//        } else {
//            $this->Session->setFlash(__('Select atleast a Organization'), 'default', array('class' => 'alert alert-warning'));
//        }
    }

    function seettingkeyvalue($arrayvalues) {
        foreach ($arrayvalues as $value) {
            $finalarrray[$value["id"]] = $value["name"];
        }
        return $finalarrray;
    }

    function OrgInfoClient($token, $org_id) {
        $postdatafororginfo = array("token" => $token, "oid" => $org_id);
        $jsondatafororginfo = json_decode($this->Apicalls->curlget("getOrganization.json", $postdatafororginfo), true);
        $alldetailsorg = array();
        if ($jsondatafororginfo["result"]["status"] == true) {
            $streetcity = array();
            $statecountry = array();
            $resultant = $jsondatafororginfo["result"]["data"];
            $orgname = $resultant["Organization"]["name"];
            $org_shortname = $resultant["Organization"]["short_name"];
            $org_image = $resultant["Organization"]["image"];
            $org_totalendorsements = $resultant["total_endorsement"];
            $org_totalcv = $resultant["total_core_values"];
            $org_total_endorsement_month = $resultant["total_endorsement_month"];
            $org_core_values = $resultant["core_values"];
            if ($resultant["Organization"]["street"] != "") {
                array_push($streetcity, $resultant["Organization"]["street"]);
            }
            if ($resultant["Organization"]["city"] != "") {
                array_push($streetcity, $resultant["Organization"]["city"]);
            }
            if ($resultant["Organization"]["state"] != "") {
                array_push($statecountry, $resultant["Organization"]["state"]);
            }
            if ($resultant["Organization"]["country"] != "") {
                array_push($statecountry, $resultant["Organization"]["country"]);
            }
            $zip = $resultant["Organization"]["zip"];
            $alldetailsorg = array(
                "org_name" => $orgname,
                "org_sname" => $org_shortname,
                "org_image" => $org_image,
                "org_totalendorsements" => $org_totalendorsements,
                "org_totalcv" => $org_totalcv,
                "org_totalendorsementsmonth" => $org_total_endorsement_month,
                "org_core_values" => $org_core_values,
                "streetcity" => $streetcity,
                "statecountry" => $statecountry,
                "zip" => $zip
            );
        }
        return $alldetailsorg;
    }

    function getOrgCoreValues($orgCoreValues, $list = false) {
        $orgCoreValueList = array();

        foreach ($orgCoreValues as $coreValue) {
            $orgCoreValueList[] = $coreValue['name'];
        }

        return $orgCoreValueList;
    }

    public function getNewUserOrgFields($orgId, $providedStatus) {
        $this->UserOrganization = ClassRegistry::init('UserOrganization');
        $this->Subscription = ClassRegistry::init('Subscription');

        $statusConfig = Configure::read("statusConfig");
        $params = array();
        $params['conditions'] = array("organization_id" => $orgId, "UserOrganization.status" => array($statusConfig['active'], $statusConfig['eval']));
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
            $conditions['Subscription.organization_id'] = $orgId;
            $params['conditions'] = $conditions;
            $currentSubscription = $this->Subscription->find("first", $params);
            if (!empty($currentSubscription)) {
                $poolPurchased = $currentSubscription['Subscription']['pool_purchased'];

                if ($paidCount >= $poolPurchased) {
                    //$status = $statusConfig['invite_inactive'];
                    $status = $statusConfig['inactive'];
                } else {
                    $status = $providedStatus;
                }
            } else {
                //$status = $statusConfig['invite_inactive'];
                $status = $statusConfig['inactive'];
            }
        } else {
            $poolType = "free";
            $status = $providedStatus;
        }

        return array("status" => $status, "poolType" => $poolType);
    }

    public function getJoinOrgCode($orgId, $email = NULL, $userId = NULL, $userOrgId = NULL) {
        $OrganizationModal = ClassRegistry::init('Organization');
        $JoinOrgCodeModal = ClassRegistry::init('JoinOrgCode');

        $organization = $OrganizationModal->findById($orgId);
        $orgSecretCode = $organization['Organization']['secret_code'];
        $joinOrgCode = "";

        while (1) {
            $secretCode = substr(md5(uniqid(mt_rand(), true)), 0, 5);
            $joinOrgCode = $orgSecretCode . $secretCode;

            $recordExist = $JoinOrgCodeModal->findByCode($joinOrgCode);

            if (!empty($recordExist)) {
                continue;
            } else {
                break;
            }
        }

        $joinOrgCodeData = array(
            "email" => $email,
            "user_id" => $userId,
            "organization_id" => $orgId,
            "code" => $joinOrgCode,
//             "user_organization_id" => $userOrgId
        );

        $joinCodeDetails = array();

        if ($JoinOrgCodeModal->save($joinOrgCodeData)) {
            return $joinOrgCode;
        }

        return "";
    }

    public function getInvitationDetails($userOrganizations) {
        $invitationsAccepted = 0;
        $invitationsCount = array("web" => 0, "app" => 0);
        $pendingCount = 0;
        $pendingInvitationsList = array("active" => array(), "inactive" => array());
        foreach ($userOrganizations as $userOrg) {
//            pr($userOrg);
            if ($userOrg['send_invite'] == 1) {
                if ($userOrg['flow'] == "web_invite") {
                    $invitationsCount['web'] ++;
                } else if ($userOrg['flow'] == "app_invite") {
                    $invitationsCount['app'] ++;
                }

                if ($userOrg['joined'] == 1) {
                    $invitationsAccepted += 1;
                } else {
                    if ($userOrg['status'] == 2) {
                        continue;
                    }

                    $key = "inactive";
                    $pendingCount++;

                    if ($userOrg['status'] == 1) {
                        $key = "active";
                    }
                    $pendingInvitationsList[$key][$userOrg['User']['id']] = array("email" => $userOrg['User']['email'], "inviteflow" => $userOrg['flow']);
//                    pr($userOrg); exit;
                }
            }
        }

        $returnArray = array("pending_list" => $pendingInvitationsList, "pending_count" => $pendingCount, "invitations_accepted" => $invitationsAccepted, "total_invitations" => $invitationsCount);
//        pr($returnArray);die;

        return $returnArray;
    }

    public function daterangeToSQL($date) {
        $dateArray = explode('/', $date);
        $month = trim($dateArray['0']);
        $date = trim($dateArray['1']);
        $year = trim($dateArray['2']);
        return $year . '-' . $month . '-' . $date;
    }

    public function daterangeAndTimeToSQL($date, $time) {
        $dateArray = explode('/', $date);
        $month = trim($dateArray['0']);
        $date = trim($dateArray['1']);
        $year = trim($dateArray['2']);
        return $year . '-' . $month . '-' . $date . ' ' . $time . ':00';
    }

    public function getUserCurrentOrgId($user_id) {
        $UserModel = ClassRegistry::init('User');
        $usersOrg = $UserModel->find("all", array('joins' => array(
                array(
                    'table' => 'default_orgs',
                    'alias' => 'DefaultOrg',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'DefaultOrg.user_id = User.id'
                    )
                )
            ), "conditions" => array("User.id" => $user_id), "fields" => array("User.id", "email", "fname", "DefaultOrg.organization_id")));
        return $usersOrg[0]['DefaultOrg']['organization_id'];
    }

    public function getUserCurrentDept($user_id, $organization_id) {
        $UserOrganizationModel = ClassRegistry::init('UserOrganization');
        $UserOrganizationModel->unbindModel(array("belongsTo" => array("Organization", "User")));
        $userOrg = $UserOrganizationModel->find("all", array("conditions" => array("organization_id" => $organization_id, 'user_id' => $user_id)));
        return $userOrg[0]['UserOrganization']['department_id'];
    }

    public function getUserCurrentSubOrg($user_id, $organization_id) {
        $UserOrganizationModel = ClassRegistry::init('UserOrganization');
        $UserOrganizationModel->unbindModel(array("belongsTo" => array("Organization", "User")));
        $userOrg = $UserOrganizationModel->find("all", array("conditions" => array("organization_id" => $organization_id, 'user_id' => $user_id)));
        return $userOrg[0]['UserOrganization']['entity_id'];
    }

    public function getUserCurrentJobTitle($user_id, $organization_id) {
        $UserOrganizationModel = ClassRegistry::init('UserOrganization');
        $UserOrganizationModel->unbindModel(array("belongsTo" => array("Organization", "User")));
        $userOrg = $UserOrganizationModel->find("all", array("conditions" => array("organization_id" => $organization_id, 'user_id' => $user_id)));
        return $userOrg[0]['UserOrganization']['job_title_id'];
    }

    public function getDeptNameByIds($deptIdArray = array()) {
        $OrgDepartmentModel = ClassRegistry::init('OrgDepartment');
        $orgDeptArray = $OrgDepartmentModel->find('all', array('fields' => array('id', 'name'), 'conditions' => array('id' => $deptIdArray)));
        $orgDeptDATA = array();
        if (isset($orgDeptArray) && count($orgDeptArray) > 0) {
            foreach ($orgDeptArray as $index => $orgDATA) {
                $orgDeptDATA[$orgDATA['OrgDepartment']['id']] = $orgDATA['OrgDepartment']['name'];
            }
        }
        return $orgDeptDATA;
        //pr($orgDeptDATA);
        //exit;
    }

    public function getSubOrgNameByIds($subOrgIdArray = array()) {
        //pr($subOrgIdArray);
        $EntityModel = ClassRegistry::init('Entity');
        $EntityArray = $EntityModel->find('all', array('fields' => array('id', 'name'), 'conditions' => array('id' => $subOrgIdArray)));
        $entityNameDATA = array();
        if (isset($EntityArray) && count($EntityArray) > 0) {
            foreach ($EntityArray as $index => $entityDATA) {
                $entityNameDATA[$entityDATA['Entity']['id']] = $entityDATA['Entity']['name'];
            }
        }
        return $entityNameDATA;
        //pr($entityNameDATA);
        //exit;
    }

    public function getUsersNameByIds($usersIdArray = array()) {
        $UserModel = ClassRegistry::init('User');
        $usersArray = $UserModel->find('all', array('fields' => array('id', 'fname', 'lname'), 'conditions' => array('id' => $usersIdArray)));
        $UsersNameDATA = array();
        if (isset($usersArray) && count($usersArray) > 0) {
            foreach ($usersArray as $index => $UserDATA) {
                $UsersNameDATA[$UserDATA['User']['id']] = $UserDATA['User']['fname'] . " " . $UserDATA['User']['lname'];
            }
        }
        return $UsersNameDATA;
    }

    /* added by Babulal Prasad @28-dec-2017 */

    public function cnvt_usrTime_to_UTC($dt_start_time_formate, $UTC_TimeZone) {

        $LocalTime_start_time = new DateTime($dt_start_time_formate);
        $tz_start = new DateTimeZone($UTC_TimeZone);
        $LocalTime_start_time->setTimezone($tz_start);
        $array_start_time = (array) $LocalTime_start_time;

        return $UTC_Time_Start_Time = $array_start_time['date'];
    }

    /* added by Babulal Prasad @28-dec-2017 */

    public function ConvertOneTimezoneToAnotherTimezone($time, $currentTimezone, $timezoneRequired) {
        $system_timezone = date_default_timezone_get();
        $local_timezone = $currentTimezone;
        date_default_timezone_set($local_timezone);
        $local = date("Y-m-d h:i:s A");

        date_default_timezone_set("GMT");
        $gmt = date("Y-m-d h:i:s A");

        $require_timezone = $timezoneRequired;
        date_default_timezone_set($require_timezone);
        $required = date("Y-m-d h:i:s A");

        date_default_timezone_set($system_timezone);
        $diff1 = (strtotime($gmt) - strtotime($local));
        $diff2 = (strtotime($required) - strtotime($gmt));
        $date = new DateTime($time);
        $date->modify("+$diff1 seconds");
        $date->modify("+$diff2 seconds");
        $timestamp = $date->format("Y-m-d H:i:s");
        return $timestamp;
    }

    /* Added by Babulal Prasad to encode user data in Database @30-Aug-2018 */

    public function encodeData($data) {
        if (isset($data) && $data != '') {
            return $encodedData = $data;
            //return $encodedData = base64_encode($data);
        } else {
            return $data;
        }
    }

    /* Added by Babulal Prasad to decode user data in Database @30-Aug-2018 */

    public function decodeData($data) {
        if (isset($data) && $data != '') {
            return $encodedData = $data;
            //return $encodedData = base64_decode($data);
        } else {
            return $data;
        }
    }

}
