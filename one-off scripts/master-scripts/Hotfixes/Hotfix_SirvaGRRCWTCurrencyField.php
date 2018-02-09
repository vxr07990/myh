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


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('includes/main/WebUI.php');

echo "<br>begin hotfix turn GRR CWT into currency field<br>";

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET uitype = 71 WHERE fieldname = 'grr' AND `vtiger_field`.`tablename` = 'vtiger_quotes' AND uitype = 9");

echo "<br>end hotfix turn GRR CWT into currency field<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";