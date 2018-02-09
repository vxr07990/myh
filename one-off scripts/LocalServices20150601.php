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

echo "<h1>Creating Tables for Local Services</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_quotes_baseplus')) {
    echo "<li>creating vtiger_quotes_baseplus </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_baseplus',
                              '(
							    estimateid INT(11),
							    serviceid INT(11),
								mileage INT(11),
								weight INT(11),
								rate DECIMAL(12,3),
								excess DECIMAL(12,3)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_breakpoint')) {
    echo "<li>creating vtiger_quotes_breakpoint </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_breakpoINT',
                              '(estimateid INT(11),
							    serviceid INT(11),
								mileage INT(11),
								weight INT(11),
								rate DECIMAL(12,3),
								breakpoint INT(11)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_weightmileage')) {
    echo "<li>creating vtiger_quotes_weightmileage </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_weightmileage',
                              '(estimateid INT(11),
							    serviceid INT(11),
								mileage INT(11),
								weight INT(11),
								rate DECIMAL(12,3)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_countycharge')) {
    echo "<li>creating vtiger_quotes_countycharge </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_countycharge',
                              '(estimateid INT(11),
							    serviceid INT(11),
								county VARCHAR(50),
								rate DECIMAL(12,3)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_hourlyset')) {
    echo "<li>creating vtiger_quotes_hourlyset </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_hourlyset',
                              '(estimateid INT(11),
							    serviceid INT(11),
								men INT(11),
								vans INT(11),
								hours DECIMAL(12,2),
								traveltime DECIMAL(12,2),
								rate DECIMAL(12,3)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_perunit')) {
    echo "<li>creating vtiger_quotes_perunit </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_perunit',
                              '(estimateid INT(11),
							    serviceid INT(11),
								qty1 DECIMAL(12,3),
								qty2 DECIMAL(12,3),
								rate DECIMAL(12,3),
								ratetype VARCHAR(50)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_bulky')) {
    echo "<li>creating vtiger_quotes_bulky </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_bulky',
                              '(estimateid INT(11),
							    serviceid INT(11),
								description VARCHAR(75),
								qty INT(11),
								weight INT(11),
								rate DECIMAL(12,3),
								bulky_id INT(11)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_valuation')) {
    echo "<li>creating vtiger_quotes_valuation </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_valuation',
                              '(estimateid INT(11),
							    serviceid INT(11),
								released TINYINT(1),
								released_amount DECIMAL(4,3),
								amount INT(11),
								deductible INT(11),
								rate DECIMAL(12,3)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_packing')) {
    echo "<li>creating vtiger_quotes_packing </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_packing',
                              '(estimateid INT(11),
							    serviceid INT(11),
								name VARCHAR(50),
								container_qty INT(11),
								container_rate DECIMAL(12,3),
								pack_qty INT(11),
								pack_rate DECIMAL(12,3),
								unpack_qty INT(11),
								unpack_rate DECIMAL(12,3),
								packing_id INT(11)
								)', true);
}
if (!Vtiger_Utils::CheckTable('vtiger_quotes_crating')) {
    echo "<li>creating vtiger_quotes_crating </li><br>";
    Vtiger_Utils::CreateTable('vtiger_quotes_crating',
                              '(estimateid INT(11),
							    serviceid INT(11),
								crateid VARCHAR(50),
								description VARCHAR(50),
								crating_qty INT(11),
								crating_rate DECIMAL(12,3),
								uncrating_qty INT(11),
								uncrating_rate DECIMAL(12,3),
								length INT(11),
								width INT(11),
								height INT(11),
								inches_added INT(11),
								line_item_id INT(11)
								)', true);
}

echo "</ol>";
echo "<h1>Script Completed</h1>";
