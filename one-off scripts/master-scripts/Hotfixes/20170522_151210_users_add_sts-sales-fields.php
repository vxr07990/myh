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

$field = Vtiger_Field::getInstance('sts_salesperson_avl', $usersModule);

if (!$field) {
    $field             = new Vtiger_Field();
    $field->name       = 'sts_salesperson_avl';
    $field->label      = 'LBL_USERS_STSSALESPERSONAVL';
    $field->uitype     = 1;
    $field->column     = $field->name;
    $field->columntype = 'VARCHAR(255)';
    $field->typeofdata = 'V~O';
    $usersInfo->addField($field);

    echo 'STS salesperson avl field has been added';
} else {
    echo 'STS salesperson avl field already exists';
}

$field = Vtiger_Field::getInstance('sts_salesperson_navl', $usersModule);

if (!$field) {
    $field             = new Vtiger_Field();
    $field->name       = 'sts_salesperson_navl';
    $field->label      = 'LBL_USERS_STSSALESPERSONNAVL';
    $field->uitype     = 1;
    $field->column     = $field->name;
    $field->columntype = 'VARCHAR(255)';
    $field->typeofdata = 'V~O';
    $usersInfo->addField($field);

    echo 'STS salesperson navl field has been added';
} else {
    echo 'STS salesperson navl field already exists';
}
echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
