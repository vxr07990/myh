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



$adb = PearDatabase::getInstance();

$ordersModuleInstance = Vtiger_Module::getInstance('Orders');
$surveysModuleInstance = Vtiger_Module::getInstance('Surveys');

$result = $adb->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=? ORDER BY relation_id", array($ordersModuleInstance->id, $surveysModuleInstance->id));
$num_rows = $adb->num_rows($result);

if ($result && $num_rows == 0) {
    echo "Creating Related List.<br>";
    $ordersModuleInstance->setRelatedList($surveysModuleInstance, 'Surveys', array('SELECT'), 'get_related_list');
} elseif ($result && $num_rows > 1) {
    echo "There are " . $num_rows . " related lists, " . (intval($num_rows) - 1) . " will be deleted.<br>";
    $i = 1;
    while ($row = $adb->fetchByAssoc($result)) {
        if ($i < $num_rows) {
            $adb->pquery("DELETE FROM vtiger_relatedlists WHERE relation_id = ?", array($row[relation_id]));
        }
        $i++;
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";