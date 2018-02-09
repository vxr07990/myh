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


// Creating addresses module

$isNew = false;
$Addresses = Vtiger_Module::getInstance('Addresses');
if ($Addresses) {
    echo "<h2>Addresses already exists, updating fields</h2><br>\n";
} else {
    $Addresses       = new Vtiger_Module();
    $Addresses->name = 'Addresses';
    $Addresses->save();
    echo "<h2>Creating module Addresses and updating fields</h2><br>\n";
    $Addresses->initTables();
}
$block = Vtiger_Block::getInstance('LBL_ADDRESSES_INFORMATION', $Addresses);
if ($block) {
    echo "<h3>The LBL_ADDRESSES_INFORMATION block already exists</h3><br> \n";
} else {
    $block        = new Vtiger_Block();
    $block->label = 'LBL_ADDRESSES_INFORMATION';
    $Addresses->addBlock($block);
    $isNew = true;
}
$fielddesc = Vtiger_Field::getInstance('description', $Addresses);
if ($fielddesc) {
    echo "The description field already exists<br>\n";
} else {
    $fielddesc             = new Vtiger_Field();
    $fielddesc->label      = 'LBL_DESCRIPTION';
    $fielddesc->name       = 'description';
    $fielddesc->table      = 'vtiger_addresses';
    $fielddesc->column     = 'description';
    $fielddesc->columntype = 'VARCHAR(255)';
    $fielddesc->uitype     = 1;
    $fielddesc->typeofdata = 'V~M';
    $block->addField($fielddesc);
    $Addresses->setEntityIdentifier($fielddesc);
    echo "The $fielddesc->name field created successfully<br>\n";

    //Setup auto numbering field default value
    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $moduleInstance->name, 'Addresses', 1);
}

$field0 = Vtiger_Field::getInstance('line1', $Addresses);
if ($field0) {
    echo "The line1 field already exists<br>\n";
} else {
    $field0             = new Vtiger_Field();
    $field0->label      = 'LBL_LINE_1';
    $field0->name       = 'line1';
    $field0->table      = 'vtiger_addresses';
    $field0->column     = 'line1';
    $field0->columntype = 'VARCHAR(100)';
    $field0->uitype     = 1;
    $field0->typeofdata = 'V~M';
    $block->addField($field0);
    echo "The $field0->name field created successfully<br>\n";
}

$field1 = Vtiger_Field::getInstance('line2', $Addresses);
if ($field1) {
    echo "The line2 field already exists<br>\n";
} else {
    $field1             = new Vtiger_Field();
    $field1->label      = 'LBL_LINE_2';
    $field1->name       = 'line2';
    $field1->table      = 'vtiger_addresses';
    $field1->column     = 'line2';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype     = 1;
    $field1->typeofdata = 'V~O';
    $block->addField($field1);
    echo "The $field1->name field created successfully<br>\n";
}

$field2 = Vtiger_Field::getInstance('city', $Addresses);
if ($field2) {
    echo "The city field already exists<br>\n";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'LBL_CITY';
    $field2->name       = 'city';
    $field2->table      = 'vtiger_addresses';
    $field2->column     = 'city';
    $field2->columntype = 'VARCHAR(60)';
    $field2->uitype     = 1;
    $field2->typeofdata = 'V~M';
    $block->addField($field2);
    echo "The $field2->name field created successfully<br>\n";
}
$field3 = Vtiger_Field::getInstance('state', $Addresses);
if ($field3) {
    echo "The state field already exists<br>\n";
} else {
    $field3             = new Vtiger_Field();
    $field3->label      = 'LBL_STATE';
    $field3->name       = 'state';
    $field3->table      = 'vtiger_addresses';
    $field3->column     = 'state';
    $field3->columntype = 'VARCHAR(60)';
    $field3->uitype     = 1;
    $field3->typeofdata = 'V~M';
    $block->addField($field3);
    echo "The $field3->name field created successfully<br>\n";
}

$field4 = Vtiger_Field::getInstance('zip', $Addresses);
if ($field4) {
    echo "The zip field already exists<br>\n";
} else {
    $field4             = new Vtiger_Field();
    $field4->label      = 'LBL_ZIP';
    $field4->name       = 'zip';
    $field4->table      = 'vtiger_addresses';
    $field4->column     = 'zip';
    $field4->columntype = 'VARCHAR(10)';
    $field4->uitype     = 1;
    $field4->typeofdata = 'V~M';
    $block->addField($field4);
    echo "The $field4->name field created successfully<br>\n";
}
$field5 = Vtiger_Field::getInstance('country', $Addresses);
if ($field5) {
    echo "The country field already exists<br>\n";
} else {
    $field5             = new Vtiger_Field();
    $field5->label      = 'LBL_COUNTRY';
    $field5->name       = 'country';
    $field5->table      = 'vtiger_addresses';
    $field5->column     = 'country';
    $field5->columntype = 'VARCHAR(60)';
    $field5->uitype     = 1;
    $field5->typeofdata = 'V~M';
    $block->addField($field5);
    echo "The $field5->name field created successfully<br>\n";
}

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $Addresses->addFilter($filter1);
    $filter1->addField($fielddesc)->addField($field0, 1)->addField($field2, 2)->addField($field3, 3)->addField($field5, 4);
    $Addresses->setDefaultSharing();
    $Addresses->initWebservice();

    //set the label you want here
    $parentLabel = 'COMPANY_ADMIN_TAB';

    $db = PearDatabase::getInstance();
    if ($db) {
        $stmt = 'UPDATE `vtiger_tab` SET parent=? WHERE tabid=?';
        $db->pquery($stmt, [$parentLabel, $Addresses->id]);
    } else {
        //This wasn't consistent, but leaving as a backup.
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent='".$parentLabel."' WHERE tabid=".$Addresses->id);
    }
}

echo "<h2>Finished creating module Addresses or updating fields</h2><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";