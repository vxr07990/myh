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



//createContract2Agent.php
//adds contract2agent table.

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<h1>Creating Table for contract2Agent</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_contract2agent')) {
    echo "<li>creating vtiger_contract2agent </li><br>";
    Vtiger_Utils::CreateTable('vtiger_contract2agent',
                              '(
							    agentid INT(19),
							    contractid INT(19)
								)', true);
}
echo "</ol>";
echo "<h1>Creating Table for contract2Vanline</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_contract2vanline')) {
    echo "<li>creating vtiger_contract2vanline </li><br>";
    Vtiger_Utils::CreateTable('vtiger_contract2vanline',
                              '(
							    vanlineid INT(19),
							    contractid INT(19),
								apply_to_all_agents TINYINT(1)
								)', true);
}
echo "</ol>";
echo "<h1>contract2agent Completed</h1>";



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";