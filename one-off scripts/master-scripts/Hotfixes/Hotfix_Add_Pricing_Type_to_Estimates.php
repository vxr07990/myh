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


$moduleQuotes = Vtiger_Module::getInstance('Quotes');
$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$interstateBlockQuote = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $moduleQuotes);
$interstateBlockEstimate = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $moduleEstimates);


$field = Vtiger_Field::getInstance('pricing_type', $moduleQuotes);
if ($field) {
    echo "<li>The pricing_type field already existsin quotes</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_QUOTES_PRICING';
    $field->name = 'pricing_type';
    $field->table = 'vtiger_quotes';
    $field->column = 'pricing_type';
    $field->columntype='VARCHAR(200)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;

    $interstateBlockQuote->addField($field);
}

$field = Vtiger_Field::getInstance('pricing_type', $moduleEstimates);
if ($field) {
    echo "<li>The pricing_type field already exists in estimates</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_QUOTES_PRICING';
    $field->name = 'pricing_type';
    $field->table = 'vtiger_quotes';
    $field->column = 'pricing_type';
    $field->columntype='VARCHAR(200)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->displaytype = 1;

    $interstateBlockEstimate->addField($field);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";