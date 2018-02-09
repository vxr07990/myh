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

//*/
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'modules/ModTracker/ModTracker.php';
include_once 'modules/ModComments/ModComments.php';
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
//*/
echo "<br><h1>Starting </h1><br>\n";
$db = PearDatabase::getInstance();

$moduleQuotes = Vtiger_Module::getInstance('Quotes');
$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$blockQuotes = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleQuotes);
$blockEstimates = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleEstimates);

$dayCertainPickup = Vtiger_Field::getInstance('acc_day_certain_pickup', $moduleEstimates);

if ($dayCertainPickup) {
    echo "The acc_day_certain_pickup field already exists<br>\n";
} else {
    $field1             = new Vtiger_Field();
    $field1->label      = 'LBL_QUOTES_ACCDAYCERTAINPICKUP';
    $field1->name       = 'acc_day_certain_pickup';
    $field1->table      = 'vtiger_quotes';
    $field1->column     = 'acc_day_certain_pickup';
    $field1->columntype = 'VARCHAR(3)';
    $field1->uitype     = 56;
    $field1->typeofdata = 'C~O';
    $blockEstimates->addField($field1);
}

$dayCertainFee = Vtiger_Field::getInstance('acc_day_certain_fee', $moduleEstimates);
if ($dayCertainFee) {
    echo "The acc_day_certain_fee field already exists<br>\n";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_QUOTES_ACCDAYCERTAINFEE';
    $field2->name       = 'acc_day_certain_fee';
    $field2->table      = 'vtiger_quotes';
    $field2->column     = 'acc_day_certain_fee';
    $field2->columntype = 'DECIMAL(12,2)';
    $field2->uitype     = 71;
    $field2->typeofdata = 'N~O';
    $blockEstimates->addField($field2);
}

$dayCertainPickup = Vtiger_Field::getInstance('acc_day_certain_pickup', $moduleQuotes);

if ($dayCertainPickup) {
    echo "The acc_day_certain_pickup field already exists<br>\n";
} else {
    $field1             = new Vtiger_Field();
    $field1->label      = 'LBL_QUOTES_ACCDAYCERTAINPICKUP';
    $field1->name       = 'acc_day_certain_pickup';
    $field1->table      = 'vtiger_quotes';
    $field1->column     = 'acc_day_certain_pickup';
    $field1->columntype = 'VARCHAR(3)';
    $field1->uitype     = 56;
    $field1->typeofdata = 'C~O';
    $blockQuotes->addField($field1);
}

$dayCertainFee = Vtiger_Field::getInstance('acc_day_certain_fee', $moduleQuotes);
if ($dayCertainFee) {
    echo "The acc_day_certain_fee field already exists<br>\n";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_QUOTES_ACCDAYCERTAINFEE';
    $field2->name       = 'acc_day_certain_fee';
    $field2->table      = 'vtiger_quotes';
    $field2->column     = 'acc_day_certain_fee';
    $field2->columntype = 'DECIMAL(12,2)';
    $field2->uitype     = 71;
    $field2->typeofdata = 'N~O';
    $blockQuotes->addField($field2);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";