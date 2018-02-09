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

$module = Vtiger_Module::getInstance('TariffServices');
$field1 = Vtiger_Field::getInstance('bulky_chargeper', $module);

if ($field1) {
    echo '<h5>'.$field1->name.' Found now updating...</h5>';
    $sql    = "UPDATE `vtiger_field`
        SET   `typeofdata` = 'V~M'
        WHERE  `fieldid` = ?";
    $db    = PearDatabase::getInstance();
    $query = $db->pquery($sql, [$field1->id]);
    if ($db->getAffectedRowCount($query)>0) {
        echo '<h4>Updated '.$field1->name.' Successfully with a typeofdata V~M</h4>';
    } else {
        echo '<h4>'.$field1->name.' already has typeofdata set to V~M</h4>';
    }
} else {
    echo '<h3>FAILED TO FIND A FIELD WITH A NAME OF bulky_chargeper - Update failed</h3>';
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";