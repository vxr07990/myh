<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$db = &PearDatabase::getInstance();
$moduleName = 'Users';
$blockName = 'LBL_USERLOGIN_ROLE';

$moduleInstance = Vtiger_Module::getInstance($moduleName);

if (!$moduleInstance) {
    print "ERROR: No moduleName " . $moduleName . PHP_EOL;
    return;
}

$blockInstance = Vtiger_Block::getInstance($blockName, $moduleInstance);

if (!$blockInstance) {
    print "ERROR: No blockName " . $blockName . PHP_EOL;
    return;
}

//create registration number field
$newFieldName = 'vanline_sales_number';
$fieldInstance = Vtiger_Field::getInstance($newFieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance               = new Vtiger_Field();
    $fieldInstance->label        = 'LBL_'.strtoupper($moduleName.'_'.$newFieldName);
    $fieldInstance->name         = $newFieldName;
    $fieldInstance->table        = 'vtiger_users';
    $fieldInstance->column       = $newFieldName;
    $fieldInstance->columntype   = 'VARCHAR(255)';
    $fieldInstance->uitype       = 1;
    $fieldInstance->typeofdata   = 'V~O';
    $blockInstance->addField($fieldInstance);
    print "Added new field: $newFieldName".PHP_EOL;
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
