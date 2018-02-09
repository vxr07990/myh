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

echo "<h1>Creating the vtiger_quotes_servicecost Table</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_quotes_servicecost')) {
    echo "<li>creating vtiger_quotes_servicecost </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_servicecost',
                              '(
							    estimateid INT(11),
							    serviceid INT(11),
								cost_service_total DECIMAL(12,2),
								cost_container_total DECIMAL(12,2),
								cost_packing_total DECIMAL(12,2),
								cost_unpacking_total DECIMAL(12,2),
								cost_crating_total DECIMAL(12,2),
								cost_uncrating_total DECIMAL(12,2)
								)', true);
}
//use this because it natively won't duplicate columns allowing for safe use without conditionalizing
Vtiger_Utils::AddColumn('vtiger_quotes_bulky', 'cost_bulky_item', 'DECIMAL(12,2)');

Vtiger_Utils::AddColumn('vtiger_quotes_crating', 'cost_crating', 'DECIMAL(12,2)');
Vtiger_Utils::AddColumn('vtiger_quotes_crating', 'cost_uncrating', 'DECIMAL(12,2)');

Vtiger_Utils::AddColumn('vtiger_quotes_packing', 'cost_container', 'DECIMAL(12,2)');
Vtiger_Utils::AddColumn('vtiger_quotes_packing', 'cost_packing', 'DECIMAL(12,2)');
Vtiger_Utils::AddColumn('vtiger_quotes_packing', 'cost_unpacking', 'DECIMAL(12,2)');

echo "<br><br><h1>END OF SCRIPT</h1>";
/*
/**
     * Add column to existing table
     * @param String tablename to alter
     * @param String columnname to add
     * @param String columntype (criteria like 'VARCHAR(100)')

    static function AddColumn($tablename, $columnname, $criteria) {
    */


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";