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

echo '<br />Checking if estimate type field exists:<br />';

$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$blockEstimates = Vtiger_Block::getInstance('LBL_QUOTES_LOCALMOVEDETAILS', $moduleEstimates);

$field1 = Vtiger_Field::getInstance('local_estimate_type', $moduleEstimates);
if ($field1) {
    echo "<br /> The estimate type field already exists in Estimates <br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ESTIMATE_TYPE';
    $field1->name = 'local_estimate_type';
    $field1->table = 'vtiger_quotes';
    $field1->column = 'local_estimate_type';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';

    $blockEstimates->addField($field1);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";