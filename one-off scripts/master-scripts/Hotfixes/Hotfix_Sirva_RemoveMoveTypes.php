<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

echo '<h1>Begin Hotfix Remove Move Types</h1><br>';
$db = PearDatabase::getInstance();

function removeMoveTypes($type)
{
    echo 'Removing type: '.$type.'<br>';
    $db = PearDatabase::getInstance();

    $sql = 'DELETE FROM `vtiger_move_type` WHERE move_type = ?';
    $result = $db->pquery($sql, [$type]);
    if ($db->getAffectedRowCount($result)>0) {
        echo $type.' Removed from vtiger_move_type!<br>';
    } else {
        echo $type.' Not Found in vtiger_move_type!<br>';
    }
}

$removeMoveType = ['Max 3', 'Max 4', 'Alaska', 'Hawaii'];
foreach ($removeMoveType as $type) {
    removeMoveTypes($type);
}

echo '<h1>End Hotfix Remove Move Types</h1><br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";