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


$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

echo "<br><h1>Starting </h1><br>\n";
$db = PearDatabase::getInstance();
$module1 = Vtiger_Module::getInstance('Leads');
$field1 = Vtiger_Field::getInstance('fulfillment_date', $module1);

$module2 = Vtiger_Module::getInstance('Estimates');
$field2 = Vtiger_Field::getInstance('pickup_date', $module2);
echo $field1->id;
echo ' '.$field2->id;
if ($field1 && $field2) {
    echo '<h4>Setting fulfillment_date and pickup_date to 0</h4>';
    $sql   = "UPDATE vtiger_field SET `displaytype` = ? WHERE `fieldid` = ? OR `fieldid` = ?";
    $query = $db->pquery($sql, array(0, $field1->id, $field2->id));
    if ($db->getAffectedRowCount($query) > 0) {
        echo '<p>Fulfillment and pickup date successfully hidden</p>';
    }
}

echo "<br><h1>Finished </h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";