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

$moduleProducts = Vtiger_Module::getInstance('Opportunities');

$sql = "SELECT * FROM `vtiger_relatedlists` WHERE tabid=? AND related_tabid=?";
$result = $db->pquery($sql, [$moduleProducts->id, $moduleProducts->id]);
if ($db->num_rows($result) == 0) {
    $moduleProducts->setRelatedList(Vtiger_Module::getInstance('Opportunities'), 'Duplicate Opportunities', ['SELECT'], 'get_potentials');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";