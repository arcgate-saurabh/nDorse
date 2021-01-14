<?php
ini_set("display_errors",1);
ini_set ('max_execution_time', 999999999);


//update admin id in organization if role is orgadmin
//save in UserOrganization // check subscription before inserting
//save in defaut_orgs
//send emails for updated password
//save in org_departments

require_once('ws.php');
App::uses('AuthComponent', 'Controller/Component');
App::uses('CommonComponent', 'Controller/Component');

$commonComponent = new CommonComponent(new ComponentCollection());

//MYSQL connection
$conn = mysql_connect("stonex36","ndorse_user","ndorse_pass");
mysql_selectdb("ndorse");

//$conn = mysql_connect("localhost","ndorse_user","ndorse_pass");
//mysql_selectdb("ndorse_dev");

//$password = AuthComponent::password("123456789");
//echo $password;die;

$row = 1;
$fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . "ndorse_data_backup" . DIRECTORY_SEPARATOR . "Users.csv";

$roleArray = array("SuperAdmin" => 1, "OrgAdmin" => 2, "Endorser" => 3);

$multipleEmails = "";

$emailUsers = array("kevinmclau@gmail.com", "jeanne.bergeron@ololrmc.com", "geraldine.pittman@ololrmc.com");

if (($handle = fopen($fileName, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        //pr($data);die;
        $orgData = array();
        $orgCoreValues = array();
        
        if($row <= 1) {
            $row++;
            continue;
        }
        
        echo $row . "<br>--------------------------------------------------------------------------------------------------------------<br><br>";
        
        $userEmail = $data[11];
        
        if(empty($userEmail)) {
            $row++;
            continue;
        }
        
        $orgMongoId = $data[7];
        
        if(!empty($orgMongoId)) {
            // get organization id
            $sql = "SELECT id, status FROM organizations WHERE mongo_id = '" . $data[7] . "' LIMIT 1";
            $result = mysql_query($sql);
            
            if (!$result) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $sql;
                die($message);
            }
            
            $organizationRecord = mysql_fetch_assoc($result);
        } else {
            $organizationRecord['status'] = 1;
        }
        
        $email = mysql_real_escape_string($data[11]);
        
        $email = trim($email);
        
        $email = strtolower($email);
        
        //remove this when import on live server
//        if($email != "rrohanw@yahoo.com" && $email != "rrohanw@gmail.com" && $email != "rwalve@lsuhsc.edu" && $email != "sk@ndorsetest.net") {
//            $emailArray = explode("@", $email);
//            $emailExist = $emailArray[0] . "_" . $emailArray[1] . "@mailinator.com";
//        } else {
            $emailExist = $email;
//        }
        
        $existsResult = mysql_query("SELECT * FROM users WHERE email = '" . $emailExist . "'");
        $existingRecord = mysql_fetch_assoc($existsResult);
        
        $user = array();
        $user['mongo_id'] = mysql_real_escape_string($data[0]);
        $user['fname'] = mysql_real_escape_string($data[4]);
        $user['lname'] = mysql_real_escape_string($data[5]);
        $user['suffix'] = mysql_real_escape_string($data[6]);
        $user['employee_id'] = mysql_real_escape_string($data[16]);
        $user['mobile'] = mysql_real_escape_string($data[18]);
        $user['role'] = !empty($data[8]) ? $roleArray[$data[8]] : 3;
        
        
        if(!empty($existingRecord)) {
            $multipleEmails .= $email . ", ";
            
            echo $email . " already exist";
            
            $userId = $existingRecord['id'];
            
            if(empty($existingRecord['mongo_id'])) {
                $newMongoId = "@" . $user['mongo_id'] . "@";
            } else {
                $newMongoId = $existingRecord['mongo_id'] . $user['mongo_id'] . "@";
            }
            
            
            $sql = "UPDATE users SET mongo_id = '" . $newMongoId . "' WHERE id = " . $userId;
            $updated = mysql_query($sql);
            
            //continue;
        } else {
        
            //remove this when import on live server
//            if($email != "rrohanw@yahoo.com" && $email != "rrohanw@gmail.com" && $email != "rwalve@lsuhsc.edu" && $email != "sk@ndorsetest.net") {
//                $emailArray = explode("@", $email);
//                $email = $emailArray[0] . "_" . $emailArray[1] . "@mailinator.com";
//            }
            
            //save user
            
            $user['email'] = $email;
            $user['username'] = $email;
            $user['secret_code'] = getSecretCode();
            $user['status'] = 1;
            
            
            $password = $commonComponent->randompasswordgenerator(8);
            $user['password'] = AuthComponent::password($password);
            
            
            $sql = "INSERT INTO users (mongo_id, employee_id, password, fname, lname, suffix, email, username, secret_code, status, mobile, role, created, updated, last_app_used)
                    values (
                    '@" . $user['mongo_id'] . "@', 
                    '" . $user['employee_id'] . "', 
                    '" . $user['password'] . "', 
                    '" . $user['fname'] . "', 
                    '" . $user['lname'] . "', 
                    '" . $user['suffix'] . "', 
                    '" . $user['email'] . "',
                    '" . $user['username'] . "',
                    '" . $user['secret_code'] . "',
                    " . $user['status'] . ",
                    '" . $user['mobile'] . "',
                    " . $user['role'] . ",
                    NOW(),
                    NOW(),
                    NOW()
                    )";
                    
            $result = mysql_query($sql);
            
            if (!$result) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $sql;
                die($message);
            }
            
            $userId = mysql_insert_id();
            
            echo "User : User ID : " . $userId . "<br>" .
                "mongo id : " . $user['mongo_id'] . "<br>" .
                "email : " . $user['email'] . "<br>" .
                "password : " . $password . "<br>" .
                "name : " . $user['fname'] . " " . $user['lname'] . "<br>" .
                "role : " . $data[8] . "<br>" .
                "<br><br>";
            
            
            if(empty($userId)) {
                $row++;
                continue;
            }
            
           if($organizationRecord['status'] == 1 || in_array($user['email'], $emailUsers)) {
                //send emails to users on update password
                $subject = "nDorse App Update - update your password to access nDorse Version 2! ";
                $viewVars = array("fname" => $user['fname'], "password" => $password);
                $configVars = serialize($viewVars);

                 $sql = "INSERT INTO email_migrations (`to`, `subject`, `config_vars`, `template`, `created`)
                        VALUES (
                        '" . $user['email'] . "', 
                        '" . $subject . "', 
                        '" . $configVars . "', 
                        'update_password_system', 
                        NOW()
                        )";


                $result = mysql_query($sql);

                if (!$result) {
                    $message  = 'Invalid query: ' . mysql_error() . "\n";
                    $message .= 'Whole query: ' . $sql;
                    die($message);
                }

                $emailMigrationId = mysql_insert_id();
                echo "Email : " . $emailMigrationId . " - " . $user['email'] . " with password " . $password . " inserted <br><br>";
            }
        }
        
        //Organization related updates
        if(!empty($orgMongoId)) {
            
            //update admin id in organization if role is orgadmin
            if ($user['role'] == 2) {
                $sql = "UPDATE organizations SET admin_id=" . $userId . " WHERE  mongo_id = '" . $data[7] . "'";
                $result = mysql_query($sql);
                if (!$result) {
                    $message  = 'Invalid query: ' . mysql_error() . "\n";
                    $message .= 'Whole query: ' . $sql;
                    die($message);
                }
            }
            
//            // get organization id
//            $sql = "SELECT id FROM organizations WHERE mongo_id = '" . $data[7] . "' LIMIT 1";
//            $result = mysql_query($sql);
//            
//            if (!$result) {
//                $message  = 'Invalid query: ' . mysql_error() . "\n";
//                $message .= 'Whole query: ' . $sql;
//                die($message);
//            }
//            
//            $organizationRecord = mysql_fetch_assoc($result);
            $organizationId = $organizationRecord['id'];
            
            //Inset a new department if not exists and get department id
            $orgDepartment = mysql_real_escape_string($data[10]);
            
            $sql = "SELECT id FROM org_departments WHERE organization_id = " . $organizationId . " AND name = '" . $orgDepartment . "' LIMIT 1";
            $result = mysql_query($sql);
            
            if (!$result) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $sql;
                die($message);
            }
            
            $departmentRecord = mysql_fetch_assoc($result);
            
            if(empty($departmentRecord)) {
                $sql = "INSERT INTO org_departments (name, organization_id, created, updated)
                    values (
                    '" . $orgDepartment . "', 
                    " . $organizationId . ", 
                    NOW(),
                    NOW()
                    )";
                    
                $result = mysql_query($sql);
                
                if (!$result) {
                    $message  = 'Invalid query: ' . mysql_error() . "\n";
                    $message .= 'Whole query: ' . $sql;
                    die($message);
                }
                $userOrgDepartmentId = mysql_insert_id();
                echo "Deaprtment: " . $userOrgDepartmentId . " - " . implode(", ", array($orgDepartment, $organizationId)) . " inserted <br><br>";
            } else {
                $userOrgDepartmentId = $departmentRecord['id'];
            }
            
            //Inset a new job title if not exists and get job title id
            $orgJobTitle = mysql_real_escape_string($data[9]);
            $sql = "SELECT id FROM org_job_titles WHERE organization_id = " . $organizationId . " AND title = '" . $orgJobTitle . "' LIMIT 1";
            $result = mysql_query($sql);
            
            if (!$result) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $sql;
                die($message);
            }
            
            
            $jobTitleRecord = mysql_fetch_assoc($result);
            
            if(empty($jobTitleRecord)) {
                $sql = "INSERT INTO org_job_titles (title, organization_id, created, updated)
                    values (
                    '" . $orgJobTitle . "', 
                    '" . $organizationId . "', 
                    NOW(),
                    NOW()
                    )";
                    
                $result = mysql_query($sql);
                
                if (!$result) {
                    $message  = 'Invalid query: ' . mysql_error() . "\n";
                    $message .= 'Whole query: ' . $sql;
                    die($message);
                }
                
                $userOrgJobTitle = mysql_insert_id();
                echo "Job Title: " . $userOrgJobTitle . " - " . implode(", ", array($orgJobTitle, $organizationId)) . " inserted <br><br>";
            } else {
                $userOrgJobTitle = $jobTitleRecord['id'];
            }
            
            
            //check pool type
            $sql = "SELECT pool_type, COUNT(id) as count FROM user_organizations 
                    WHERE organization_id = " . $organizationId . " AND joined = 1 AND status IN (0, 1, 3)
                    GROUP BY pool_type
                    having pool_type = 'free'";
                    //echo $sql;die;
                    
            $result = mysql_query($sql);
            
            if (!$result) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $sql;
                die($message);
            }
            
            $freeCount = 0;
            $paidCount = 0;
            while ($record = mysql_fetch_assoc($result)) {
                $freeCount = $record['count'];
            }


            if ($freeCount >= 10) {
                $poolType = "paid";
            } else {
                $poolType = "free";
            }
            
            //save user organization
            $userOrg = array();
            $userOrg['user_id'] = $userId;
            $userOrg['organization_id'] = $organizationId;
            $userOrg['department_id'] = $userOrgDepartmentId;
            $userOrg['job_title_id'] = $userOrgJobTitle;
            $userOrg['status'] = !empty($data[14]) ? $data[14] == "true" ? 0 : 1 : 0;
            $userOrg['role'] = $user['role'];
            $userOrg['flow'] = 'web_invite';
            $userOrg['joined'] = 1;
            $userOrg['pool_type'] = $poolType;
            
            $sql = "INSERT INTO user_organizations (mongo_id, user_id, organization_id, department_id, job_title_id, status, user_role, flow, joined, pool_type, created, updated)
               values (
               '" . $user['mongo_id'] . "', 
               " . $userOrg['user_id'] . ", 
               " . $userOrg['organization_id'] . ", 
               " . $userOrg['department_id'] . ", 
               " . $userOrg['job_title_id'] . ", 
               " . $userOrg['status'] . ",
               " . $userOrg['role'] . ",
               '" . $userOrg['flow'] . "',
               " . $userOrg['joined'] . ",
               '" . $userOrg['pool_type'] . "',
               NOW(),
               NOW()
               )";
                
            $result = mysql_query($sql);
            
            if (!$result) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $sql;
                die($message);
            }
            
            $userOrgId = mysql_insert_id();
            
            echo "User organization:<br> User Org ID : " . $userOrgId . "<br>" .
                "Organization id : " . $userOrg['organization_id'] . "<br>".
                "status : " . $userOrg['status'] . "<br>".
                "pool_type : " . $userOrg['pool_type'] . "<br><br>";
            
            //insert into default organization
            
            //@TODo check default org exist or not
            $sql = "SELECT id FROM default_orgs WHERE user_id = '" . $userId . "' LIMIT 1";
            $result = mysql_query($sql);
            
            $defaultOrgRecord = mysql_fetch_assoc($result);
            
            if(empty($defaultOrgRecord)) {
            
                $sql = "INSERT INTO default_orgs (user_id, organization_id)
                        values (
                        " . $userId . ", 
                        " . $organizationId . "
                        )";
                        
                $result = mysql_query($sql);
                
                if (!$result) {
                    $message  = 'Invalid query: ' . mysql_error() . "\n";
                    $message .= 'Whole query: ' . $sql;
                    die($message);
                }
                
                $defaultOrgId = mysql_insert_id();
                echo "Default Org: " . $defaultOrgId . " - " . implode(", ", array($userId, $organizationId)) . " inserted <br><br>";
            }
        }
        
        
        
        echo "========================================================================================================================================<br><br>";
        
        //if($row == 3) {
        //    echo '3 die';
        //    die;
        //}
        //
        //echo $row . ' no';die;
        
        $row++;
        
    }
    
    if(!empty($multipleEmails)) {
        $sql = rtrim($multipleEmails, ", ");
        echo "Multiple emails : " . $multipleEmails;
    }
}

function getSecretCode() {
    $secretCode = "";
    while (1) {
        $secretCode = substr(md5(uniqid(mt_rand(), true)), 0, 5);
        $recordExist = mysql_query("SELECT count(*) FROM users WHERE secret_code = " . $secretCode);
        if (!$recordExist) {
            continue;
        } else {
            break;
        }
    }
    
    return $secretCode;   
}

function pr($array) {
    echo '<pre>';
    print_r($array);
    echo "</pre>";
}

?>