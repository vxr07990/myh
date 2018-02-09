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

$module = Vtiger_Module::getInstance('Opportunities');
$field1 = Vtiger_Field::getInstance('converted_from', $module);

if ($field1) {
    $sql    = "UPDATE `vtiger_field`
        SET   `presence` = '1'
        WHERE  `fieldid` = ?";
    $db    = PearDatabase::getInstance();
    $query = $db->pquery($sql, [$field1->id]);
    echo $db->getAffectedRowCount($query).' Rows where updated in vtiger_field where label = '.$field1->label;
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";