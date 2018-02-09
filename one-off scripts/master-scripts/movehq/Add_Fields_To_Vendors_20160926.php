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

require_once 'vtlib/Vtiger/Module.php';

$moduleVendors = Vtiger_Module::getInstance('Vendors');

$block = Vtiger_Block::getInstance('LBL_VENDOR_INFORMATION', $moduleVendors);
if ($block) {
    echo "<br> The LBL_VENDOR_INFORMATION block already exists in Vendors <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_VENDOR_INFORMATION';
    $moduleVendors->addBlock($block);
}

//Vendor Num
$field = Vtiger_Field::getInstance('vendors_vendornum', $moduleVendors);
if ($field) {
    echo "<br> The vendors_vendornum field already exists in Vendors <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_VENDORS_VENDORNUM';
    $field->name = 'vendors_vendornum';
    $field->table = 'vtiger_vendor';
    $field->column = 'vendors_vendornum';
    $field->columntype = 'varchar(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

$block = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleVendors);
if ($block) {
    echo "<br> The LBL_CUSTOM_INFORMATION block already exists in Vendors <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_CUSTOM_INFORMATION';
    $moduleVendors->addBlock($block);
}

//Primary Contact
$field = Vtiger_Field::getInstance('vendors_primcontact', $moduleVendors);
if ($field) {
    echo "<br> The vendors_primcontact field already exists in Vendors <br>";
} else {
    $field = new Vtiger_Field();
    $field->label = 'LBL_VENDORS_PRIMCONTACT';
    $field->name = 'vendors_primcontact';
    $field->table = 'vtiger_vendor';
    $field->column ='vendors_primcontact';
    $field->columntype = 'varchar(100)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';

    $block->addField($field);
}

//Remove Related List Insurance from Vendors Module
$moduleRel = Vtiger_Module::getInstance('Insurance');
if ($moduleRel) {
    $moduleVendors->unsetRelatedList($moduleRel, 'Insurance', 'get_dependents_list');
    echo "<h2>Insurance module successfully removed from Vendors Related List</h2><br>";
} else {
    echo "<h2>Unable to unset Related List Insurance from Vendors as the Insurance module does not exist</h2><br>";
}

//Remove Related List Vendor Agreements from Vendors Module
$moduleRel = Vtiger_Module::getInstance('VendorAgreements');
if ($moduleRel) {
    $moduleVendors->unsetRelatedList($moduleRel, 'Vendor Agreements', 'get_related_list');
    echo "<h2>Vendor Agreements module successfully removed from Vendors Related List</h2><br>";
} else {
    echo "<h2>Unable to unset Related List Vendor Agreements from Vendors as the Vendors Agreements module does not exist</h2><br>";
}

//Remove Related List Insurance from Vendors Module
$moduleRel = Vtiger_Module::getInstance('Employees');
if ($moduleRel) {
    $moduleVendors->unsetRelatedList($moduleRel, 'Employees', 'get_dependents_list');
    echo "<h2>Employees module successfully removed from Vendors Related List</h2><br>";
} else {
    echo "<h2>Unable to unset Related List Employees from Vendors as the Employees module does not exist</h2><br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";