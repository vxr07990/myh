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


/**
  *	This hotfix file is to add billing_type as defined in OT Defects 12997-99 and 13042.
  * The base creation script files have not been modified to add the correct values, so this
  * hotfix file is to add the field in all databases.
  */
//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

$quoModule = Vtiger_Module::getInstance('Quotes');
$estModule = Vtiger_Module::getInstance('Estimates');
$potModule = Vtiger_Module::getInstance('Potentials');
$oppModule = Vtiger_Module::getInstance('Opportunities');
$ordModule = Vtiger_Module::getInstance('Orders');

$quoBlock = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $quoModule);
$estBlock = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $estModule);
$potBlock = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $potModule);
if (!$potBlock) {
    $potBlock = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $potModule);
}
$oppBlock = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $oppModule);
if (!$oppBlock) {
    $oppBlock = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppModule);
}
$ordBlock = Vtiger_Block::getInstance('LBL_ORDERS_INFORMATION', $ordModule);

$field1 = Vtiger_Field::getInstance('billing_type', $potModule);
if ($field1) {
    echo "Field billing_type already exists in Potentials module<br />";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_POTENTIALS_BILLINGTYPE';
    $field1->name = 'billing_type';
    $field1->table = 'vtiger_potential';
    $field1->column = 'billing_type';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~O';
    
    $potBlock->addField($field1);
    
    $field1->setPicklistValues(array('COD', 'NAT', 'DP3', 'GSA'));
}

$field2 = Vtiger_Field::getInstance('billing_type', $oppModule);
if ($field2) {
    echo "Field billing_type already exists in Opportunities module<br />";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_POTENTIALS_BILLINGTYPE';
    $field2->name = 'billing_type';
    $field2->table = 'vtiger_potential';
    $field2->column = 'billing_type';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~O';
    
    $oppBlock->addField($field2);
}

$field3 = Vtiger_Field::getInstance('billing_type', $quoModule);
if ($field3) {
    echo "Field billing_type already exists in Quotes module<br />";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_QUOTES_BILLINGTYPE';
    $field3->name = 'billing_type';
    $field3->table = 'vtiger_quotes';
    $field3->column = 'billing_type';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 16;
    $field3->typeofdata = 'V~O';
    
    $quoBlock->addField($field3);
}

$field4 = Vtiger_Field::getInstance('billing_type', $estModule);
if ($field4) {
    echo "Field billing_type already exists in Estimates module<br />";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_QUOTES_BILLINGTYPE';
    $field4->name = 'billing_type';
    $field4->table = 'vtiger_quotes';
    $field4->column = 'billing_type';
    $field4->columntype = 'VARCHAR(255)';
    $field4->uitype = 16;
    $field4->typeofdata = 'V~O';
    
    $estBlock->addField($field4);
}

$field5 = Vtiger_Field::getInstance('billing_type', $ordModule);
if ($field5) {
    echo "Field billing_type already exists in Orders module<br />";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_ORDERS_BILLINGTYPE';
    $field5->name = 'billing_type';
    $field5->table = 'vtiger_orders';
    $field5->column = 'billing_type';
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 16;
    $field5->typeofdata = 'V~O';
    
    $ordBlock->addField($field5);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";