<?php

require_once('includes/main/WebUI.php');
require_once('include/Webservices/Delete.php');
require_once('modules/Users/Users.php');

echo "Start Agent Manager Import <br>";

$db = PearDatabase::getInstance();

$instance = 'sirva';

if ($instance == 'sirva') {
    $filePath = 'agent_rosters/SirvaAgentRoster.csv';
    $headerMapping = [
        'Name' => 'agency_name',
        'Code' => 'agency_code',
        'CA Agency Code' => false,
        'Address' => 'address1',
        'City' => 'city',
        'State' => 'state',
        'Zip' => 'zip',
        'Phone' => 'phone1',
        'Fax' => 'fax',
        'Email' => 'email',
        'Brand' => false,
        'VanLineID' => false,
    ];
    $sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_name = 'North American Van Lines'";
    $result = $db->pquery($sql, array());
    $row = $result->fetchRow();
    $northAmericanId = $row[0];
    $sql = "SELECT vanlinemanagerid FROM `vtiger_vanlinemanager` WHERE vanline_name = 'Allied'";
    $result = $db->pquery($sql, array());
    $row = $result->fetchRow();
    $alliedId = $row[0];
    $sql = "SELECT id FROM `vtiger_users` WHERE first_name = 'Allied' AND last_name = 'Admin'";
    $result = $db->pquery($sql, array());
    $row = $result->fetchRow();
    $alliedAdminId = $row[0];
    $sql = "SELECT id FROM `vtiger_users` WHERE first_name = 'NA' AND last_name = 'Admin'";
    $result = $db->pquery($sql, array());
    $row = $result->fetchRow();
    $naAdminId = $row[0];
    $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name = 'VanlineManager'";
    $result = $db->pquery($sql, array());
    $row = $result->fetchRow();
    $vanlineWsId = $row[0];
    $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name = 'Users'";
    $result = $db->pquery($sql, array());
    $row = $result->fetchRow();
    $UsersWsId = $row[0];
}

$values = array();

$requiredFields = array('agency_code', 'agency_name', 'assigned_user_id', 'address1', 'city', 'zip', 'phone1', 'state', 'email');

$rosterList = fopen($filePath, 'r');

$headers = fgetcsv($rosterList);

$csv = fgetcsv($rosterList);

while (!empty($csv)) {
    $values[] = $csv;
    $csv = fgetcsv($rosterList);
}

//map headers
foreach ($headers as $key => $header) {
    $headers[$key] = $headerMapping[$header];
}


foreach ($values as $valuesKey => $value) {
    //echo "<br>".print_r($value, true)."<br>";
    $brand = $value[10];
    //echo "<br>".$brand."<br>";
    $data = array();
    foreach ($value as $fieldKey => $fieldValue) {
        if ($headers[$fieldKey]) {
            // echo "FIELD VAL: ".$fieldValue." <br>";
            // echo "FIELD KEY: ".$headers[$fieldKey]." <br>";
            // echo "FIELDVAL EXISTS? ".($fieldValue == null ? 'true' : 'false')."<br>";
            // echo "KEY IN ARRAY?? ".(in_array($headers[$fieldKey], $requiredFields) ? 'true' : 'false')."<br>";
            if ($fieldValue == null && in_array($headers[$fieldKey], $requiredFields)) {
                echo "<h1>REQUIRED = ?????</h1>";
                $fieldValue = '????';
            }
            $data[$headers[$fieldKey]] = htmlspecialchars($fieldValue);
            //$data[$headers[$fieldKey]] = $fieldValue;
        }
    }
    foreach ($requiredFields as $requiredField) {
        if (!array_key_exists($requiredField, $data)) {
            if ($requiredField == 'assigned_user_id') {
                if ($brand == 'AVL') {
                    $data[$requiredField] = $UsersWsId.'x'.$alliedAdminId;
                    $data['vanline_id'] = $vanlineWsId.'x'.$alliedId;
                }
                if ($brand == 'NAVL') {
                    $data[$requiredField] = $UsersWsId.'x'.$naAdminId;
                    $data['vanline_id'] = $vanlineWsId.'x'.$northAmericanId;
                }
            }
        }
    }
    $originalAgentName = $data['agency_name'];
    $agentIncrement = 1;
    $sql = "SELECT * FROM `vtiger_agentmanager` WHERE agency_name = ?";
    $result = $db->pquery($sql, array($data['agency_name']));
    $row = $result->fetchRow();
    while ($row == null) {
        $agentIncrement++;
        echo "$agentIncrement";
        $data['agency_name'] = $originalAgentName.' '.$agentIncrement;
        $sql = "SELECT * FROM `vtiger_agentmanager` WHERE agency_name = ?";
        $result = $db->pquery($sql, array($data['agency_name']));
        $row = $result->fetchRow();
        if ($agentIncrement > 50) {
            break;
        }
    }
    //echo "<br> ".print_r($data, true)." <br><br>";
    try {
        echo $data['agency_name']."<br>";
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
        $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE agency_name = ?";
        $result = $db->pquery($sql, array($data['agency_name']));
        $row = $result->fetchRow();
        $currentAgentId = $row[0];
        $sql = "SELECT id FROM `vtiger_ws_entity` WHERE name = 'AgentManager'";
        $result = $db->pquery($sql, array());
        $row = $result->fetchRow();
        $agentWsId = $row[0];
        $wsid = $agentWsId.'x'.$currentAgentId;
        echo "WSID: ".$wsid."<br>";
        $sql = "DELETE FROM `vtiger_agentmanager` WHERE agency_name = ?";
        $result = $db->pquery($sql, array($data['agency_name']));
        vtws_delete($wsid, $current_user);
        //file_put_contents('logs/devLog.log', "\n newAgent: ".print_r($newAgent, true), FILE_APPEND);
    } catch (WebServiceException $ex) {
        echo $ex->getMessage();
        echo "<br><br>";
    }
    echo "agents completed: $valuesKey <br>";
}

fclose($rosterList);

echo "<br> End Agent Manager Import<br>";
