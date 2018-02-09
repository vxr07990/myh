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

$estimatesModule = Vtiger_Module::getInstance('Estimates');
$weightInfo = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $estimatesModule);

$localWeight = Vtiger_Field::getInstance('local_weight', $estimatesModule);

if ($localWeight) {
    echo "The local_weight field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_LOCAL_WEIGHT';
    $field->name       = 'local_weight';
    $field->table      = 'vtiger_quotes';
    $field->column     = 'local_weight';
    $field->columntype = 'INT(11)';
    $field->uitype     = 1;
    $field->typeofdata = 'V~O';
    $weightInfo->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";