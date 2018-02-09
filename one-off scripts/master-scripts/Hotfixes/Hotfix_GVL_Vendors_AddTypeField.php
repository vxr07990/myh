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

$moduleInstance = Vtiger_Module::getInstance('Vendors');
$block = Vtiger_Block::getInstance('LBL_VENDOR_INFORMATION', $moduleInstance);

$field = Vtiger_Field::getInstance('type', $moduleInstance);
if ($field) {
    echo "The type field already exists<br>\n";
} else {
    $field             = new Vtiger_Field();
    $field->label      = 'LBL_VENDOR_TYPE';
    $field->name       = 'type';
    $field->table      = 'vtiger_vendor';
    $field->column     = 'type';
    $field->columntype = 'VARCHAR(255)';
    $field->uitype     = 16;
    $field->typeofdata = 'V~O';
    $block->addField($field);

    $field->setPicklistValues(['Vehicle Maintenance', 'Move Contractor', 'Labor Provider', 'Surveyor', 'Furniture Repair', 'Inspection Firm', 'Third Party Move Services']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";