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

// OT19127 - Estimates - BUG: Pricing (Estimates) - Old records / SurveyHHG not setting "Pricing Type" value which breaks conditionalizing

$module = Vtiger_Module::getInstance('Estimates');

$db    = PearDatabase::getInstance();

$field1 = Vtiger_Field::getInstance('pricing_mode', $module);
if ($field1) {
    $sql    = "UPDATE `vtiger_quotes`
        SET   `pricing_mode` = 'Estimate'
        WHERE  `pricing_mode` IS NULL OR `pricing_mode` = ''";
    $query = $db->pquery($sql);
    echo '<li> '.$db->getAffectedRowCount($query).' Rows where updated from NULL or "" to Estimate in vtiger_quotes column pricing_mode';
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";