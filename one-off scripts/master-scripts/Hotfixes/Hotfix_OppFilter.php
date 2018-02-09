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


//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
$opportunitiesInstance = Vtiger_Module::getInstance('Opportunities');
$opportunitiesInfo = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $opportunitiesInstance);
$opportunitiesSalesPerson = Vtiger_Field::getInstance('sales_person', $opportunitiesInstance);
$filter1 = Vtiger_Filter::getInstance('ALL', $opportunitiesInstance);

if ($filter1 && opportunitiesSalesPerson) {
    echo "<br> opp filter exists. adding sales person field<br>";
    $filter1->addField($opportunitiesSalesPerson, 6);
    echo "<br> filter modified<br>";
} else {
    echo "<br> opp filter or sales person field not found. no action taken<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";