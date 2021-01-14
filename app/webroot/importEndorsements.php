<?php
ini_set("display_errors",1);
ini_set ('max_execution_time', 999999999);

ini_set("log_errors", 1);
ini_set("error_log", dirname(__FILE__) . DIRECTORY_SEPARATOR . "ndorse_data_backup" . DIRECTORY_SEPARATOR ."endorse.log");


//update admin id in organization if role is orgadmin
//save in UserOrganization // check subscription before inserting
//save in defaut_orgs
//send emails for updated password
//save in org_departments

require_once('ws.php');

//MYSQL connection
$conn = mysql_connect("stonex36","ndorse_user","ndorse_pass");
mysql_selectdb("ndorse");


$row = 1;
$fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . "ndorse_data_backup" . DIRECTORY_SEPARATOR . "Endorsements.csv";

$userIds = array();
$userMongoIds = array();

if (($handle = fopen($fileName, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        if($row <= 1) {
            $row++;
            continue;
        }
        
        $userMongoIds["'" . $data[2] . "'"] = 1;
        $userMongoIds["'" . $data[3] . "'"] = 1;
    }
    
    $userMongoIds = array_keys($userMongoIds);
    
    $sql = "SELECT users.id, user_organizations.mongo_id, user_organizations.organization_id FROM users
            LEFT JOIN user_organizations ON (users.id = user_organizations.user_id)
            WHERE user_organizations.mongo_id IN (" . implode(",", $userMongoIds) . ")";
    
    $result =  mysql_query($sql);
    
    if (!$result) {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $sql;
        die($message);
    }
    
    $userRecords = array();
    
    while ($record = mysql_fetch_assoc($result)) {
        $userRecords[$record['mongo_id']]['id'] = $record['id'];
        $userRecords[$record['mongo_id']]['organization_id'] = $record['organization_id'];
    }
    //pr($userRecords);die;
    
    rewind($handle);
    $row = 1;
    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        //pr($data);die;
        if($row <= 1) {
            $row++;
            continue;
        }
        
        echo $row . "<br><br>--------------------------------------------------------------------------------------------------------------<br><br>";
        
        if(!isset($userRecords[$data[2]]) || !isset($userRecords[$data[3]])) {
            echo $data[0] . " - not inserted<br><br>";
            error_log("$row --- Invalid users" . $data[0] . "\n");
            $row++;
            continue;
        }
        
        if($data[5] == 'true' || $data[6] == 'true' || $data[7] == 'true' || $data[8] == 'true' || $data[9] == 'true' || $data[10] == 'true' || $data[11] == 'true' || $data[12] == 'true' ) {
        
            $dateTimeArray = explode(".", $data[4]);
            $created = $dateTimeArray[0];
            
            if(!isset($userRecords[$data[3]]['organization_id'])) {
                echo "Organiztion doesn't exist for " . $data[2] . "<br><br>";
                error_log("$row --- Organiztion doesn't exist for" . $data[2] . "\n");
                $row++;
                continue;
            }
            
            $organizationId = $userRecords[$data[3]]['organization_id'];
            
            $sql = "INSERT INTO endorsements (mongo_id, endorser_id, endorsed_id, organization_id, email_sent, message, created)
                    values (
                    '" . $data[0] . "', 
                    " . $userRecords[$data[2]]['id'] . ", 
                    " . $userRecords[$data[3]]['id'] . ", 
                    " . $organizationId . ", 
                    1, 
                    '" . mysql_real_escape_string($data[15]) . "',
                    '" . $created . "'
                    )";
                    
                    //echo $sql;die;
                    
            $result = mysql_query($sql);
            
            if (!$result) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $sql;
                die($message);
            }
            
            $endorsementId = mysql_insert_id();
            
            $cvKeys = array();
            
            if($data[5] == 'true') {
                $cvKeys[] = "'cv1'";
            }
            
            if($data[6] == 'true') {
                $cvKeys[] = "'cv2'";
            }
            
            if($data[7] == 'true') {
                $cvKeys[] = "'cv3'";
            }
            
            if($data[10] == 'true') {
                $cvKeys[] = "'cv4'";
            }
            
            if($data[8] == 'true') {
                $cvKeys[] = "'cv5'";
            }
            
            if($data[9] == 'true') {
                $cvKeys[] = "'cv6'";
            }
            
            if($data[11] == 'true') {
                $cvKeys[] = "'cv7'";
            }
            
            if($data[12] == 'true') {
                $cvKeys[] = "'cv8'";
            }
            
            
            $sql = "SELECT * FROM org_core_values WHERE `key` IN (" . implode(",", $cvKeys) . ") AND organization_id = " . $organizationId;
            
            $result = mysql_query($sql);
            
            if (!$result) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $sql;
                die($message);
            }
            
            
            
            $values = "";
            $valuesName = "";
            while ($record = mysql_fetch_assoc($result)) {
                $values .= "(" . $endorsementId . ", " . $record['id'] . "), ";
                $valuesName .= $record['name'] . ", ";
            }
            
            $valuesName = rtrim($valuesName, ", ");
            
            $sql = "INSERT INTO endorse_core_values (endorsement_id, value_id) VALUES " . $values;
            
            
            $sql = rtrim($sql, ", ");
            
            $result = mysql_query($sql);
            
            if (!$result) {
                $message  = 'Invalid query: ' . mysql_error() . "\n";
                $message .= 'Whole query: ' . $sql;
                die($message);
            }
                    
            echo "Endorsement : <br> Mongo ID : " . $data[0] . " <br> ".
                "endorsement ID : " . $endorsementId . " <br> ".
                "endorser ID : " . $data[2] . " - " . $userRecords[$data[2]]['id'] . " <br> ".
                "endorsed ID : " . $data[3] . " - " . $userRecords[$data[3]]['id'] . " <br> " .
                "Date : " . $created . " <br> " .
                "values : " . $valuesName . " <br><br> ";
        } else {
            echo $data[0] . " - not inserted ... no core value<br><br>";
            error_log("$row --- no core value" . $data[0] . "\n");
        }
        
        echo "=======================================================================================================================================<br><br>";
        
        $row++;
        
    }
}

function pr($array) {
    echo '<pre>';
    print_r($array);
    echo "</pre>";
}

?>