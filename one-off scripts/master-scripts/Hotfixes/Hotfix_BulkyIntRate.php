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


//require_once 'vtlib/Vtiger/Menu.php';
//require_once 'vtlib/Vtiger/Module.php';
//require_once 'includes/main/WebUI.php';

echo "Altering table vtiger_tariffbulky. Updating column rate to DECIMAL(10,2)<br>";
Vtiger_Utils::ExecuteQuery('ALTER TABLE `vtiger_tariffbulky` MODIFY COLUMN rate DECIMAL(10,2)');
echo "Alter column complete.<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";