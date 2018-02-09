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


//OT 3105 and OT 3106, Adding fields to Vendors module. FEIN and I.Code
include_once('vtlib/Vtiger/Module.php');

echo '<br />Starting AddFeinAndIcodeFields<br />';

$moduleVendors = Vtiger_Module::getInstance('Vendors');


$block = Vtiger_Block::getInstance('LBL_VENDOR_INFORMATION', $moduleVendors);

$field0 = Vtiger_Field::getInstance('fein', $moduleVendors);
if ($field0) {
    echo "<br /> The FEIN field already exists in Vendors <br />";
} else {
    echo "<br /> Adding FEIN field to Vendors <br />";
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_VENDOR_FEIN';
    $field0->name = 'fein';
    $field0->table = 'vtiger_vendor';
    $field0->column = 'fein';
    $field0->columntype = 'VARCHAR(15)';
    $field0->uitype = 1;
    $field0->typeofdata = 'V~O';

    $block->addField($field0);
}

$field1 = Vtiger_Field::getInstance('icode', $moduleVendors);
if ($field1) {
    echo "<br /> The I.Code field already exists in Vendors <br />";
} else {
    echo "<br /> Adding I.Code field to Vendors <br />";
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_VENDOR_ICODE';
    $field1->name = 'icode';
    $field1->table = 'vtiger_vendor';
    $field1->column = 'icode';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~O';

    $block->addField($field1);
}

echo '<br />Ending AddFeinAndIcodeFields<br />';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";