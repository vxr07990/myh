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

$moduleQuotes = Vtiger_Module::getInstance('Quotes');
$moduleEstimates = Vtiger_Module::getInstance('Estimates');
$blockQuotes = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $moduleQuotes);
$blockEstimates = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $moduleEstimates);

$field = Vtiger_Field::getInstance('cost_per_mile', $blockQuotes);
if($field) {
    echo "<br />Field cost_per_mile already exists in Quotes module<br />";
} else {
    $field              = new Vtiger_Field();
    $field->label       = 'LBL_QUOTES_COST_PER_MILE';
    $field->name        = 'cost_per_mile';
    $field->table       = 'vtiger_quotes';
    $field->column      = 'cost_per_mile';
    $field->columntype  = 'DECIMAL(12,4)';
    $field->uitype      = 71;
    $field->typeofdata  = 'N~O';

    $blockQuotes->addField($field);
}

$field = Vtiger_Field::getInstance('cost_per_mile', $blockEstimates);
if($field) {
    echo "<br />Field cost_per_mile already exists in Estimates module<br />";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_QUOTES_COST_PER_MILE';
    $field->name       = 'cost_per_mile';
    $field->table      = 'vtiger_quotes';
    $field->column     = 'cost_per_mile';
    $field->columntype = 'DECIMAL(12,4)';
    $field->uitype     = 71;
    $field->typeofdata = 'N~O';

    $blockEstimates->addField($field);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";