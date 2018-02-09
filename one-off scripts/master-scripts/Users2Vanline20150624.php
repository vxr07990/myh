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


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
//Create Database Tables for Local Services

//static function CheckTable($tablename)
//static function CreateTable($tablename, $criteria, $suffixTableMeta=false)

echo "<h1>Creating Table for Users2Vanline</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_users2vanline')) {
    echo "<li>creating vtiger_users2vanline </li><br>";
    Vtiger_Utils::CreateTable('vtiger_users2vanline',
                              '(
							    userid INT(11),
							    vanlineid INT(11)
								)', true);
}
echo "</ol>";
echo "<h1>Script Completed</h1>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";