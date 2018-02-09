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

echo "<br>Begin quickswapping funded to be a text field (In both leads & Opps)<br>";

Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_leadscf` MODIFY_COLUMN funded VARCHAR(255)');
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_potential` MODIFY_COLUMN funded VARCHAR(255)');
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET uitype = 1 WHERE fieldlabel = "LBL_LEADS_FUNDED" OR fieldlabel = "LBL_OPPORTUNITY_FUNDED"');

echo "<br>Quickswap Complete<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";