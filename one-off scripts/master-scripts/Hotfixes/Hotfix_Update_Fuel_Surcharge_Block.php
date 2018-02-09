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
$Estimates = Vtiger_Module::getInstance('Estimates');

$sql = "SELECT fieldname  FROM `vtiger_field` WHERE fieldlabel = 'LBL_FUEL_SURCHARGE'";
$result  = $db->pquery($sql);

while ($row = $result->fetchRow()) {
    $fieldName = $row['fieldname'];
    $field1 = Vtiger_Field::getInstance($fieldName, $Estimates);
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET block = '194' WHERE fieldid = ".$field1->id);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";