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
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
include_once 'modules/Settings/Picklist/models/Module.php';
include_once 'modules/Settings/Picklist/models/Field.php';

$moduleInstance = Vtiger_Module::getInstance('Contracts');
$docsInstance = Vtiger_Module::getInstance('Documents');

$db = PearDatabase::getInstance();

$tabid = $moduleInstance->id;
$relatedTabId = $docsInstance->id;

echo "<h3>Starting Contracts makeOwnModule</h3>\n";

$sql = "SELECT * FROM `vtiger_tab` WHERE tabid = ? AND tablabel = ?";
$result = $db->pquery($sql, [$tabid, 'Contracts']);

$row = $result->fetchRow();
if ($row['parent'] == 'CUSTOMERS_TAB') {
    echo "Contracts is already its own module<br>\n";
} else {
    echo "Making contracts its own module<br>\n";
    $sql = "UPDATE `vtiger_tab` SET parent = ? WHERE tabid = ? AND tablabel = ? LIMIT 1";
    $result = $db->pquery($sql, ['OPERATIONS_TAB', $tabid, 'Contracts']);
    echo "Finished making contracts its own module<br>\n";
}

$sql = "SELECT label FROM `vtiger_relatedlists` WHERE related_tabid = ? and tabid = ? LIMIT 1";
$result = $db->pquery($sql, [$relatedTabId, $tabid]);

$row = $result->fetchRow();
if ($row) {
    echo "Documents already exists in contracts related lists<br>\n";
} else {
    echo "Documents doesn't exists in contracts related lists<br>\n";
    $docModule = Vtiger_Module::getInstance('Documents');
    $relationLabel  = 'Documents';
    $moduleInstance->setRelatedList($docModule, $relationLabel, array('ADD', 'SELECT'));
    echo "Added documents to contracts related list<br>\n";
}

echo "<h3>Ending Contracts makeOwnModule</h3>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";