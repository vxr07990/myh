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

$db = &PearDatabase::getInstance();

$moduleName = 'Surveys';
$blockName = 'LBL_SURVEYS_INFORMATION';

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

//create self survey link text field that is seen on detail view and will contain the selfSurvey url.
$newFieldName = 'google_apt_id';
$fieldInstance = Vtiger_Field::getInstance($newFieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance               = new Vtiger_Field();
    $fieldInstance->label        = 'LBL_'.strtoupper($moduleName.'_'.$newFieldName);
    $fieldInstance->name         = $newFieldName;
    $fieldInstance->table        = 'vtiger_surveys';
    $fieldInstance->column       = $newFieldName;
    $fieldInstance->columntype   = 'VARCHAR(50)';
    $fieldInstance->uitype       = 1;
    $fieldInstance->typeofdata   = 'V~O';
    $fieldInstance->displaytype  = 1;
    $fieldInstance->readonly     = 0;
    $fieldInstance->presence     = 2;
    //$fieldInstance->defaultvalue = '';
    $blockInstance->addField($fieldInstance);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
