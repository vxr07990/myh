<?php

require_once('includes/main/WebUI.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');

echo '<h1>Begin Generate Sirva Max 3/4 Tariffs.</h1><br>';

function generateLocal($name, $state, $userId = '19x1', $adminAccess = 'on')
{
    $db = PearDatabase::getInstance();
    
    $sql = 'SELECT tariffsid FROM `vtiger_tariffs` WHERE tariff_name = ?';
    $result = $db->pquery($sql, [$name]);
    $row = $result->fetchRow();
    if ($row != null) {
        echo '<h1 style="color:orange;">WARNING: a local tariff already exists with the name '.$name.'</h1><br>';
        return;
    }
    
    //file_put_contents('logs/devLog.log', "\n Begin generation of $name", FILE_APPEND);
    echo "<h1>Begin generation of $name.</h1><br>";
    
    $data = array(
        'tariff_name' => $name,
        'tariff_state' => $state,
        'admin_access' => $adminAccess,
        'assigned_user_id' => $userId,
    );
    
    echo "<h1>$name DATA: ".print_r($data, true)."</h1><br>";
    
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    $newService = vtws_create('Tariffs', $data, $current_user);
    
    echo "<h1>$name generated.</h1><br>";
}

generateLocal('Max 3', 'TX');
generateLocal('Max 4', 'CA');

echo '<h1>attempting to remove Max 3/4 Allied/North American</h1><br>';

$db = PearDatabase::getInstance();

$sql = 'SELECT tariffmanagerid, tariffmanagername FROM `vtiger_tariffmanager` WHERE tariffmanagername = ? OR tariffmanagername = ? OR tariffmanagername = ? OR tariffmanagername = ?';
$result = $db->pquery($sql, ['Max 3 Allied', 'Max 4 Allied', 'Max 3 North American', 'Max 4 North American']);
$row = $result->fetchRow();

if ($row == null) {
    echo '<h1>No garbage max 3/4 tariffs found</h1><br>';
}

while ($row != null) {
    echo "<h1>".$row[1]." found! removing...</h1><br>";
    $deleteSql = 'DELETE FROM `vtiger_tariffmanager` WHERE tariffmanagerid = ? AND tariffmanagername = ?';
    $deleteResult = $db->pquery($deleteSql, [$row[0], $row[1]]);
    $deleteSql = 'DELETE FROM `vtiger_crmentity` WHERE crmid = ?';
    $deleteResult = $db->pquery($deleteSql, [$row[0]]);
    echo "<h1>".$row[1]." removed!</h1><br>";
    $row = $result->fetchRow();
}

echo '<h1>completed attempt to remove Max 3/4 Allied/North American</h1><br>';

echo '<h1>Through Generate Sirva Max 3/4 Tariffs.</h1><br>';
