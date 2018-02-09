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

//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting </h1><br>\n";
$db = PearDatabase::getInstance();

$usersModule = Vtiger_Module::getInstance('Users');
$usersInfo = Vtiger_Block::getInstance('LBL_USERLOGIN_ROLE', $usersModule);

$moveCoordinator = Vtiger_Field::getInstance('move_coordinator', $usersModule);

if ($usersModule && $usersInfo && $moveCoordinator == false) {
    $field             = new Vtiger_Field();
    $field->name       = 'move_coordinator';
    $field->label      = 'LBL_USERS_MOVE_COORDINATOR';
    $field->uitype     = 16;
    $field->column     = $field->name;
    $field->columntype = 'VARCHAR(255)';
    $field->typeofdata = 'V~O';
    $usersInfo->addField($field);

    echo 'Move Coordinator ID has been added';
} else {
    echo 'Move Coordinator ID is already a thing';
}
echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";