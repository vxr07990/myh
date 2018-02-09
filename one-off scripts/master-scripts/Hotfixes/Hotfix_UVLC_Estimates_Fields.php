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

$moduleQuotes = Vtiger_Module::getInstance('Quotes');
$moduleEstimates = Vtiger_Module::getInstance('Estimates');

$blockQuotes = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $moduleQuotes);
$blockEstimates = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $moduleEstimates);

$newFields = [
    [
        'name'  => 'recycling_surcharge',
        'label' => 'LBL_RECYCLINGSURCHARGE',
    ],
    [
        'name'  => 'driver_surcharge',
        'label' => 'LBL_DRIVERSURCHARGE',
    ],
    [
        'name'  => 'carton_removal_charge',
        'label' => 'LBL_CARTONREMOVALCHARGE',
    ],
    [
        'name'  => 'rush_shipment',
        'label' => 'LBL_RUSHSHIPMENT',
    ],
    [
        'name'  => 'containerized_shipment',
        'label' => 'LBL_CONTAINERIZEDSHIPMENT',
    ],
];

foreach ($newFields as $field) {
    //Add to quotes
    if (Vtiger_Field::getInstance($field['name'], $moduleQuotes)) {
        echo '<br /> The ' . $field['name'] . ' field already exists.';
    } else {
        echo '<br /> The ' . $field['name'] . ' field does not exist; creating now.';

        $newField = new Vtiger_Field();

        $newField->label       = $field['label'];
        $newField->name        = $field['name'];
        $newField->table       = 'vtiger_quotes';
        $newField->column      = $field['name'];
        $newField->columntype  = 'VARCHAR(3)';
        $newField->uitype      = 56;
        $newField->typeofdata  = 'V~O';
        $newField->displaytype = 1;

        $blockQuotes->addField($newField);
    }

    //Add to estimates
    if (Vtiger_Field::getInstance($field['name'], $moduleEstimates)) {
        echo '<br /> The ' . $field['name'] . ' field already exists.';
    } else {
        echo '<br /> The ' . $field['name'] . ' field does not exist; creating now.';

        $newField = new Vtiger_Field();

        $newField->label       = $field['label'];
        $newField->name        = $field['name'];
        $newField->table       = 'vtiger_quotes';
        $newField->column      = $field['name'];
        $newField->columntype  = 'VARCHAR(3)';
        $newField->uitype      = 56;
        $newField->typeofdata  = 'V~O';
        $newField->displaytype = 1;

        $blockEstimates->addField($newField);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";