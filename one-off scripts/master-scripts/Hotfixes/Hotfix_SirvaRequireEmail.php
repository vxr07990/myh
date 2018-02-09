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

echo "<br>Begin Sirva require customer email hotfix<br>";

$leadsModule = Vtiger_Module::getInstance('Leads');
if ($leadsModule) {
    $leadsInfo = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadsModule);
    if ($leadsInfo) {
        echo "<br> block 'LBL_LEADS_INFORMATION' exists, attempting to alter lead email to be mandatory<br>";
        $leadEmail = Vtiger_Field::getInstance('email', $leadsModule);
        if ($leadEmail) {
            echo "<br>lead email exists converting to mandatory<br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata = 'E~M' WHERE fieldlabel = 'LBL_LEADS_EMAIL'");
            echo "<br>lead email mandatory swap done<br>";
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";