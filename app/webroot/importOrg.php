<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<?php

ini_set("display_errors",1);
ini_set ('max_execution_time', 999999999);

require_once('ws.php');

//MYSQL connection
$conn = mysql_connect("stonex36","ndorse_user","ndorse_pass");
mysql_selectdb("ndorse");

//App::uses('AuthComponent', 'Controller/Component');
//$password = AuthComponent::password("123456789");
//echo $password;die;


$row = 1;

$fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . "ndorse_data_backup" . DIRECTORY_SEPARATOR . "Organizations.csv";
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
        //pr($data);
        $orgData['mongo_id'] = $data[0];
        $orgData['short_name'] = $data[2];
        $orgData['name'] = $data[3];
        $orgData['status'] = $data[5] == "true" ? 1 : 0;
        $orgData['allow_comments'] = $data[31] == "true" ? 1 : 0;
        $orgData['secret_code'] = getSecretCode();
        
        $sql = "INSERT INTO organizations (mongo_id, short_name, name, status, allow_comments, secret_code, created, updated)
                values (
                '" . $orgData['mongo_id'] . "', 
                '" . $orgData['short_name'] . "', 
                '" . $orgData['name'] . "', 
                " . $orgData['status'] . ", 
                " . $orgData['allow_comments'] . ",
                '" . $orgData['secret_code'] . "',
                NOW(),
                NOW()
                )";
                
                
        $result = mysql_query($sql);
        //$result = 1;
        
        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $sql;
            die($message);
        }
        
        $organizationId = mysql_insert_id();
        //$organizationId = 339;
        echo "organization id : " . $organizationId .
            "<br> mongo id : " . $orgData['mongo_id'] . " <br>" .
            "<br> name : " . $orgData['name'] . " <br>";
        $orgCoreValueStr = "";
        if(!empty($data[22]) || !empty($data[6])) {
            $name = !empty($data[22]) ? $data[22] : $data[6];
            $short_name = !empty($data[6]) ? $data[6] : $data[22];
            $orgCoreValues[] = array("organization_id" => $organizationId, "short_name" => "'" . mysql_real_escape_string($short_name) . "'", "name" => "'" . mysql_real_escape_string($name) . "'", "key" =>"'cv1'", "status" => $data[12] == "true" ? 1 : 0, "color_code" => "'#FFFFFF'", "NOW()", "NOW()");
            $orgCoreValueStr .= "cv1 - " .$name . " --- " . $short_name . " <br>";
        }
        
        if(!empty($data[23]) || !empty($data[7])) {
            $name = !empty($data[23]) ? $data[23] : $data[7];
            $short_name = !empty($data[7]) ? $data[7] : $data[23];
            $orgCoreValues[] = array("organization_id" => $organizationId, "short_name" => "'" . mysql_real_escape_string($short_name) . "'", "name" => "'" . mysql_real_escape_string($name) . "'", "key" =>"'cv2'", "status" => $data[13] == "true" ? 1 : 0, "color_code" => "'#FFFFFF'", "NOW()", "NOW()");
            $orgCoreValueStr .= "cv2 - " .$name . " --- " . $short_name . " <br>";
        }
        
        
        if(!empty($data[24]) || !empty($data[8])) {
            $name = !empty($data[24]) ? $data[24] : $data[8];
            $short_name = !empty($data[8]) ? $data[8] : $data[24];
            $orgCoreValues[] = array("organization_id" => $organizationId, "short_name" => "'" . mysql_real_escape_string($short_name) . "'", "name" => "'" . mysql_real_escape_string($name) . "'", "key" =>"'cv3'", "status" => $data[14] == "true" ? 1 : 0, "color_code" => "'#FFFFFF'", "NOW()", "NOW()");
            $orgCoreValueStr .= "cv3 - " .$name . " --- " . $short_name . " <br>";
        }
        
        
        if(!empty($data[25]) || !empty($data[9])) {
            $name = !empty($data[25]) ? $data[25] : $data[9];
            $short_name = !empty($data[9]) ? $data[9] : $data[25];
            $orgCoreValues[] = array("organization_id" => $organizationId, "short_name" => "'" . mysql_real_escape_string($short_name) . "'", "name" => "'" . mysql_real_escape_string($name) . "'", "key" =>"'cv4'", "status" => $data[15] == "true" ? 1 : 0, "color_code" => "'#FFFFFF'", "NOW()", "NOW()");
            $orgCoreValueStr .= "cv4 - " .$name . " --- " . $short_name . " <br>";
        }
        
        
        if(!empty($data[26]) || !empty($data[10])) {
            $name = !empty($data[26]) ? $data[26] : $data[10];
            $short_name = !empty($data[10]) ? $data[10] : $data[26];
            $orgCoreValues[] = array("organization_id" => $organizationId, "short_name" => "'" . mysql_real_escape_string($short_name) . "'", "name" => "'" . mysql_real_escape_string($name) . "'", "key" =>"'cv5'", "status" => $data[16] == "true" ? 1 : 0, "color_code" => "'#FFFFFF'", "NOW()", "NOW()");
            $orgCoreValueStr .= "cv5 - " .$name . " --- " . $short_name . " <br>";
        }
        
        
        if(!empty($data[27]) || !empty($data[11])) {
            $name = !empty($data[27]) ? $data[27] : $data[11];
            $short_name = !empty($data[11]) ? $data[11] : $data[27];
            $orgCoreValues[] = array("organization_id" => $organizationId, "short_name" => "'" . mysql_real_escape_string($short_name) . "'", "name" => "'" . mysql_real_escape_string($name) . "'", "key" =>"'cv6'", "status" => $data[17] == "true" ? 1 : 0, "color_code" => "'#FFFFFF'", "NOW()", "NOW()");
            $orgCoreValueStr .= "cv6 - " .$name . " --- " . $short_name . " <br>";
        }
        
        if(!empty($data[28]) || !empty($data[18])) {
            $name = !empty($data[28]) ? $data[28] : $data[18];
            $short_name = !empty($data[18]) ? $data[18] : $data[28];
            $orgCoreValues[] = array("organization_id" => $organizationId, "short_name" => "'" . mysql_real_escape_string($short_name) . "'", "name" => "'" . mysql_real_escape_string($name) . "'", "key" =>"'cv7'", "status" => $data[20] == "true" ? 1 : 0, "color_code" => "'#FFFFFF'", "NOW()", "NOW()");
            $orgCoreValueStr .= "cv7 - " .$name . " --- " . $short_name . " <br>";
        }
        
        if(!empty($data[29]) || !empty($data[19])) {
            $name = !empty($data[29]) ? $data[29] : $data[19];
            $short_name = !empty($data[19]) ? $data[19] : $data[29];
            $orgCoreValues[] = array("organization_id" => $organizationId, "short_name" => "'" . mysql_real_escape_string($short_name) . "'", "name" => "'" . mysql_real_escape_string($name) . "'", "key" =>"'cv8'", "status" => $data[21] == "true" ? 1 : 0, "color_code" => "'#FFFFFF'", "NOW()", "NOW()");
            $orgCoreValueStr .= "cv8 - " .$name . " --- " . $short_name . " <br>";
        }
        
        $values = "";
        
       //pr($orgCoreValues) ;die;
        
        foreach ($orgCoreValues as $orgCoreValue) {
            $values .= "(" . implode(",", array_values($orgCoreValue)) . "),";
        }
        
        if(!empty($values)) {
            $sql = "INSERT INTO org_core_values (`organization_id`, `short_name`, `name`, `key`, `status`, `color_code`, `created`, `updated`) VALUES " . $values;
            $sql = rtrim($sql, ", ");
            //echo $sql;die;
            $result = mysql_query($sql);
            
            if (!$result) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $sql;
                die($message);
            }
            
            echo $orgCoreValueStr  . " <br>";
        } else {
            echo "No core values" . "<br>";
        }
        
        
        
        echo "==========================================================================================================================================<br><br>";
        
        $row++;
    }
    fclose($handle);
}


function getSecretCode() {
    $secretCode = "";
    while (1) {
        $secretCode = substr(md5(uniqid(mt_rand(), true)), 0, 5);
        $recordExist = mysql_query("SELECT count(*) FROM organizations WHERE secret_code = " . $secretCode);
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