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
echo "<br><h1>Starting Hotfix Removing Required From Dest. Address in Opps</h1><br>\n";

$opps = Vtiger_Module::getInstance('Opportunities');
$field = Vtiger_Field::getInstance('destination_address1', $opps);

if ($field) {
    $db = PearDatabase::getInstance();
    $sql = 'UPDATE `vtiger_field` SET `typeofdata` = "V~O" WHERE `fieldid` = ? LIMIT 1';

    $query = $db->pquery($sql, array($field->id));
    echo '<p>destination_address1 updated to not mandatory</p>';
} else {
    echo '<p>Field destination_address1 not found</p>';
}

echo "<br><h1>Finished Hotfix Removing Required From Dest. Address in Opps</h1><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";