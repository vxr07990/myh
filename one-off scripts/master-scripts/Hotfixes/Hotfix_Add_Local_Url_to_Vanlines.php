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


include_once('vtlib/Vtiger/Module.php');

if (!$db) {
    $db = PearDatabase::getInstance();
}

//Add Custom vanline URL for local reports
$vanlineManager = Vtiger_Module::getInstance('VanlineManager');

$blockInfo = Vtiger_Block::getInstance('LBL_VANLINEMANAGER_INFORMATION', $vanlineManager);

$field1 = Vtiger_Field::getInstance('local_report_url', $vanlineManager);

if ($field1) {
    echo "<br /> The local_report_url field already exists in Quotes/Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_LOCAL_REPORT_URL';
    $field1->name = 'local_report_url';
    $field1->table = 'vtiger_vanlinemanager';
    $field1->column = 'local_report_url';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 17;
    $field1->typeofdata = 'V~O';
    $field1->displaytype = 1;
    $field1->quickcreate = 0;
    $field1->presence = 2;
    
    $blockInfo->addField($field1);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";