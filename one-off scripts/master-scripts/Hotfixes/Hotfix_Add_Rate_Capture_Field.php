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

$moduleInstance = Vtiger_Module::getInstance('Estimates');
$blockInstance = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $moduleInstance);

$field = Vtiger_Field::getInstance('pack_rates', $moduleInstance);
if ($field) {
    echo "<br> The pack_rates field already exists in Estimates <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_ESTIMATES_PACKRATES';
    $field->name = 'pack_rates';
    $field->table = 'vtiger_quotes';
    $field->column ='pack_rates';
    $field->columntype = 'TEXT';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->displaytype = 3;

    $blockInstance->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";