<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


include_once('vtlib/Vtiger/Users.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/TariffManager/models/Module.php');

// Need to remove the truckload field anymore.
$sql = "SELECT * FROM `vtiger_field` WHERE fieldname = 'express_truckload'";
$result = $db->query($sql);
while($row = $result->fetchRow()) {
    $id = $row['fieldid'];
    $sql = "UPDATE `vtiger_field` SET `displaytype`=0 WHERE `fieldid`=?";
    if(!$db->pquery($sql, [$id])) {
        echo "Error turning off express truckload field of ID $id<br/>\n";
    }
}

Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_custom_tariff_type` (custom_tariff_typeid, custom_tariff_type, sortorderid, presence) SELECT id + 2, 'Express Truckload', id + 2, 1 FROM `vtiger_custom_tariff_type_seq` WHERE NOT EXISTS (SELECT * FROM `vtiger_custom_tariff_type` WHERE custom_tariff_type = 'Express Truckload')");

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tariffmanager` SET `custom_javascript` = 'Estimates_TPGTariff_Js' WHERE `vtiger_tariffmanager`.`tariffmanagername` = 'Express Truckload'");

$sql = "SELECT * FROM vtiger_tariffmanager WHERE tariffmanagername = 'Express Truckload'";
$result = $db->query($sql);
if($result && $db->num_rows($result)) {
    echo "Express Truckload is already a tariff, skipping...<br/>\n";
}else{
    echo "Creating Express Truckload tariffmanager entry...<br/>\n";
    $sql = "SELECT vanlinemanagerid FROM vtiger_vanlinemanager WHERE vanline_name = 'North American Van Lines'";
    $res = $db->query($sql);
    $vanlineId = $res->fetchRow();
    $vanlineId = $vanlineId['vanlinemanagerid'];
    $data = [
        'currentid' => '',
        'tariffmanagername' => 'Express Truckload',
        'tariff_type' => 'Interstate',
        'rating_url' => 'https://sirva-win-qa.movecrm.com/RatingEngine/SIRVA/RatingService.svc?wsdl',
        'custom_tariff_type' => 'Express Truckload',
        'custom_javascript' => 'Estimates_TPGTariff_Js',
        'assigned_user_id' => Users::getActiveAdminId(),
        'Vanline'.$vanlineId.'State' => 'assigned',
        'assignVanline'.$vanlineId.'Agents' => 'on'
    ];
    $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE vanline_id = ?";
    $result = $db->pquery($sql, array($vanlineId));
    $row = $result->fetchRow();
    while ($row != null) {
        $data['assignAgent'.$row[0]] = 'on';
        $row = $result->fetchRow();
    }
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    $newService = vtws_create('TariffManager', $data, $current_user);
}
echo 'Done<br/>\n';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";