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

echo '<br>begin hotfix oplist question longtext<br>';
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_oplist_questions` MODIFY COLUMN question LONGTEXT");
echo '<br>end hotfix oplist question longtext<br>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";