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


//Creating Billing Addresses module that will be extended from Addresses

$isNew = false;
$BillingAddresses = Vtiger_Module::getInstance('BillingAddresses');
$requiredModule = Vtiger_Module::getInstance('Addresses');
if (!$requiredModule) {
    echo "Addresses module is required for Billing Addresses module. Unable to create Billing Addresses.<br>\n";
    return;
}
if ($BillingAddresses) {
    echo "<h2>BillingAddresses already exists, updating fields</h2><br>\n";
} else {
    $BillingAddresses       = new Vtiger_Module();
    $BillingAddresses->name = 'BillingAddresses';
    $BillingAddresses->save();
    echo "<h2>Creating module BillingAddresses and updating fields</h2><br>\n";
    $BillingAddresses->initTables();
}
$block0 = Vtiger_Block::getInstance('LBL_BILLING_ADDRESS_INFORMATION', $BillingAddresses);
if ($block0) {
    echo "<h3>The LBL_BILLING_ADDRESS_INFORMATION block already exists</h3><br> \n";
} else {
    $block0        = new Vtiger_Block();
    $block0->label = 'LBL_BILLING_ADDRESS_INFORMATION';
    $BillingAddresses->addBlock($block0);
    $isNew = true;
}
$fieldCompany = Vtiger_Field::getInstance('company', $BillingAddresses);
if ($fieldCompany) {
    echo "The company field already exists<br>\n";
} else {
    $fieldCompany             = new Vtiger_Field();
    $fieldCompany->label      = 'LBL_COMPANY';
    $fieldCompany->name       = 'company';
    $fieldCompany->table      = 'vtiger_billingaddresses';
    $fieldCompany->column     = 'company';
    $fieldCompany->columntype = 'VARCHAR(100)';
    $fieldCompany->uitype     = 1;
    $fieldCompany->typeofdata = 'V~M';
    $block0->addField($fieldCompany);
    $BillingAddresses->setEntityIdentifier($fieldCompany);
    echo "The $fieldCompany->name field created successfully<br> \n";
    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $moduleInstance->name, 'Billing Addresses', 1);
}

$fieldBL = Vtiger_Field::getInstance('business_line', $BillingAddresses);
if ($fieldBL) {
    echo "The business_line field already exists<br>\n";
} else {
    $fieldBL             = new Vtiger_Field();
    $fieldBL->label      = 'LBL_BUSINESS_LINE';
    $fieldBL->name       = 'business_line';
    $fieldBL->table      = 'vtiger_billingaddresses';
    $fieldBL->column     = 'business_line';
    $fieldBL->columntype = 'text';
    $fieldBL->uitype     = 33;
    $fieldBL->typeofdata = 'V~M';
    $block0->addField($fieldBL);
    //$fieldBL->setPicklistValues(Array('Temp Value'));
    echo "The $fieldBL->name field created successfully<br> \n";
}
$fieldActive = Vtiger_Field::getInstance('active', $BillingAddresses);
if ($fieldActive) {
    echo "The active field already exists<br>\n";
} else {
    $fieldActive             = new Vtiger_Field();
    $fieldActive->label      = 'LBL_ACTIVE';
    $fieldActive->name       = 'active';
    $fieldActive->table      = 'vtiger_billingaddresses';
    $fieldActive->column     = 'active';
    $fieldActive->columntype = 'VARCHAR(3)';
    $fieldActive->uitype     = 56;
    $fieldActive->typeofdata = 'V~O';
    $block0->addField($fieldActive);
    echo "The $fieldActive->name field created successfully<br> \n";
}

$fielddesc = Vtiger_Field::getInstance('description', $BillingAddresses);
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
    $block0->addField($fielddesc);
}

$field0 = Vtiger_Field::getInstance('line1', $BillingAddresses);
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
    $block0->addField($field0);
    echo "The $field0->name field created successfully<br>\n";
}

$field1 = Vtiger_Field::getInstance('line2', $BillingAddresses);
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
    $block0->addField($field1);
    echo "The $field1->name field created successfully<br>\n";
}

$field2 = Vtiger_Field::getInstance('city', $BillingAddresses);
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
    $block0->addField($field2);
    echo "The $field2->name field created successfully<br>\n";
}
$field3 = Vtiger_Field::getInstance('state', $BillingAddresses);
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
    $block0->addField($field3);
    echo "The $field3->name field created successfully<br>\n";
}

$field4 = Vtiger_Field::getInstance('zip', $BillingAddresses);
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
    $block0->addField($field4);
    echo "The $field4->name field created successfully<br>\n";
}
$field5 = Vtiger_Field::getInstance('country', $BillingAddresses);
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
    $block0->addField($field5);
    echo "The $field5->name field created successfully<br>\n";
}

//Adding as a guest block to Accounts
$accountsInstance = Vtiger_Module::getInstance('Accounts');
$accountsInstance->setGuestBlocks('BillingAddresses', ['LBL_BILLING_ADDRESS_INFORMATION']);


if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $BillingAddresses->addFilter($filter1);
    $filter1->addField($fieldCompany, 0)->addField($fielddesc, 1)->addField($fieldBL, 2)->addField($fieldActive, 3);
    $BillingAddresses->setDefaultSharing();
    $BillingAddresses->initWebservice();

    //set the label you want here
    $parentLabel = 'COMPANY_ADMIN_TAB';

    $db = PearDatabase::getInstance();
    if ($db) {
        $stmt = 'UPDATE `vtiger_tab` SET parent=? WHERE tabid=?';
        $db->pquery($stmt, [$parentLabel, $BillingAddresses->id]);
    } else {
        //This wasn't consistent, but leaving as a backup.
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET parent='".$parentLabel."' WHERE tabid=".$BillingAddresses->id);
    }
}

echo "<h2>Finished creating module BillingAddresses or updating fields</h2><br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";