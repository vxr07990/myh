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

$salesPersonId = Vtiger_Field::getInstance('amc_salesperson_id', $usersModule);

if ($usersModule && $usersInfo && $salesPersonId == false) {
    $field             = new Vtiger_Field();
    $field->name       = 'amc_salesperson_id';
    $field->label      = 'LBL_USERS_AMC_SALESPERSONID';
    $field->uitype     = 2;
    $field->column     = $field1->name;
    $field->columntype = 'VARCHAR(255)';
    $field->typeofdata = 'V~O';
    $usersInfo->addField($field);

    echo 'AMC Sales Person ID has been added';
} else {
    echo 'AMC Sales Person ID is already a thing';
}
echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";