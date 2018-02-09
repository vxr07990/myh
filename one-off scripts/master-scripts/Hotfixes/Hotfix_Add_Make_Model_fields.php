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

//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
//Create Database Tables for Local Services

//static function CheckTable($tablename)
//static function CreateTable($tablename, $criteria, $suffixTableMeta=false)


Vtiger_Utils::AddColumn('vtiger_quotes_vehicles', 'make', 'VARCHAR(255)');
Vtiger_Utils::AddColumn('vtiger_quotes_vehicles', 'model', 'VARCHAR(255)');
Vtiger_Utils::AddColumn('vtiger_quotes_vehicles', 'year', 'VARCHAR(255)');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";