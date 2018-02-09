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


/*$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');*/
//Create Database Tables for Local Services

//static function CheckTable($tablename)
//static function CreateTable($tablename, $criteria, $suffixTableMeta=false)

echo "<h1>Creating the database_version Table</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('database_version')) {
    echo "<li>creating database_version </li><br>";
    Vtiger_Utils::CreateTable('database_version',
                              '(
							    movehq TINYINT(1),
							    db_version VARCHAR(10)
								)', true);
}
echo "<br><br><h1>END OF SCRIPT</h1>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";