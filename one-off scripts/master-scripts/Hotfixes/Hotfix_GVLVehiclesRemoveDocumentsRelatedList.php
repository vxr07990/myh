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


//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>start remove extra documents related list from vehicles<br>";

$db = PearDatabase::getInstance();

$badRelation = $db->pquery("SELECT relation_id FROM `vtiger_relatedlists` WHERE name = 'get_dependents_list' AND label = 'Documents' AND tabid = 67", [])->fetchRow()['relation_id'];

if ($badRelation) {
    $moduleInstance = Vtiger_Module::getInstance('Vehicles');
    $moduleInstance->unsetrelatedList(Vtiger_Module::getInstance('Documents'), 'Documents', 'get_dependents_list');
} else {
    echo "<br>could not find bad relation, no action taken";
}

echo "<br>end remove extra documents related list from vehicles";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";