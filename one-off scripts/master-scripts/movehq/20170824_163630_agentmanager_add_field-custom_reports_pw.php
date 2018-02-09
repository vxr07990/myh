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

$moduleInstance = Vtiger_Module::getInstance('AgentManager');
$blockInstance = Vtiger_Block::getInstance('LBL_AGENTMANAGER_INFORMATION', $moduleInstance);
$fieldName = 'custom_reports_pw';

$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<br> The $fieldName field already exists in Estimates <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_AGENTMANAGER_'.strtoupper($fieldName);
    $field->name = $fieldName;
    $field->table = 'vtiger_agentmanager';
    $field->column =$fieldName;
    $field->columntype = 'varchar(100)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $blockInstance->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
