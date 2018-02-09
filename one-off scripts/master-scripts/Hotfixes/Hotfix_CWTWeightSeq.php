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
//include_once 'includes/main/WebUI.php';
echo "<br>begin new CWT by Weight seq hotfix<br>";
if (!Vtiger_Utils::CheckTable('vtiger_tariffcwtbyweight_seq')) {
    echo "<br>vtiger_tariffcwtbyweight_seq doesn't exist, creating it now<br>";
    Vtiger_Utils::CreateTable('vtiger_tariffcwtbyweight_seq',
                              '(
								id INT(11)
								)', true);
    Vtiger_Utils::ExecuteQuery('INSERT INTO `vtiger_tariffcwtbyweight_seq` VALUES (0)');
    echo "<br>vtiger_tariffcwtbyweight_seq table created<br>";
} else {
    echo "<br><h1 style='color:orange;'>WARNING: vtiger_tariffcwtbyrate_seq already exists, no action taken</h1><br>";
}
echo "<br>completed CWT by Weight seq hotfix<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";