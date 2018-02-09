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

// 2864: Creation of Item Codes Module
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

global $adb;

echo "<h1>Create Item Codes Module </h1><br>";
$isNew = false;
$moduleInstance = Vtiger_Module::getInstance('ItemCodes');

if ($moduleInstance) {
    echo "<h2>Item Codes already exists </h2><br>";
    // Delete duplicate fields
    $field = Vtiger_Field::getInstance('itemcodes_description', $moduleInstance);
    if ($field) {
        $field->delete();
    }
    $field = Vtiger_Field::getInstance('itemcodes_revenuegroup', $moduleInstance);
    if ($field) {
        $field->delete();
    }
    $field = Vtiger_Field::getInstance('itemcodes_igctariff_servicecode', $moduleInstance);
    if ($field) {
        $field->delete();
    }
    $field = Vtiger_Field::getInstance('itemcodes_vanlinecode', $moduleInstance);
    if ($field) {
        $field->delete();
    }
    // Delete block
    $blockInstance = Vtiger_Block::getInstance('LBL_ITEMCODES_INFORMATION', $moduleInstance);
    if ($blockInstance) {
        $blockInstance->delete();
    }
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'ItemCodes';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $isNew = true;
}


$blockInstance = Vtiger_Block::getInstance('LBL_ITEMCODES_DETAILS', $moduleInstance);

if ($blockInstance) {
    echo "<h3>The LBL_ITEMCODES_DETAILS block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_ITEMCODES_DETAILS';
    $moduleInstance->addBlock($blockInstance);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);

if ($blockInstance2) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

//Item Code Number (Agent Defined)
$field2 = Vtiger_Field::getInstance('itemcodes_number', $moduleInstance);
if ($field2) {
    echo "<br> The itemcodes_number field already exists in ItemCodes <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_ITEMCODES_NUMBER';
    $field2->name = 'itemcodes_number';
    $field2->table = 'vtiger_itemcodes';
    $field2->column ='itemcodes_number';
    $field2->columntype = 'INT(10)';
    $field2->uitype = 7;
    $field2->typeofdata = 'V~M';
    $field2->quickcreate = 0;
    $field2->summaryfield = 1;

    $blockInstance->addField($field2);
    $moduleInstance->setEntityIdentifier($field2); // This is correct, but dont know why on server it uses itemcodes_desc instead of itemcodes_number
}

// Description
$field3 = Vtiger_Field::getInstance('itemcodes_desc', $moduleInstance);
if ($field3) {
    echo "<br> The itemcodes_desc field already exists in ItemCodes <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_ITEMCODES_DESC';
    $field3->name = 'itemcodes_desc';
    $field3->table = 'vtiger_itemcodes';
    $field3->column ='itemcodes_desc';
    $field3->columntype = 'VARCHAR(100)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~M';
    $field3->quickcreate = 0;
    $field3->summaryfield = 1;

    $blockInstance->addField($field3);
}

//Status Field
$field4 = Vtiger_Field::getInstance('itemcodes_status', $moduleInstance);
if ($field4) {
    echo "<br> The itemcodes_status field already exists in Employee Roles <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_ITEMCODES_STATUS';
    $field4->name = 'itemcodes_status';
    $field4->table = 'vtiger_itemcodes';
    $field4->column ='itemcodes_status';
    $field4->columntype = 'varchar(100)';
    $field4->uitype = 16;
    $field4->typeofdata = 'V~M';
    $field4->defaultvalue = 'Active';
    $field4->quickcreate = 0;

    $blockInstance->addField($field4);
    $field4->setPicklistValues(['Active', 'Inactive']);
}

//Owner Field
$field5 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if ($field5) {
    echo "<br> The agentid field already exists in ItemCodes <br>";
} else {
    $field5             = new Vtiger_Field();
    $field5->label      = 'LBL_OWNER';
    $field5->name       = 'agentid';
    $field5->table      = 'vtiger_crmentity';
    $field5->column     = 'agentid';
    $field5->columntype = 'INT(10)';
    $field5->uitype     = 1002;
    $field5->typeofdata = 'I~M';
    $field5->quickcreate = 0;
    $field5->summaryfield = 1;

    $blockInstance->addField($field5);
}



//  Revenue Group
$field6 = Vtiger_Field::getInstance('itemcodes_group', $moduleInstance);
if ($field6) {
    echo "<br> The itemcodes_group field already exists in Employee_Roles <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_ITEMCODES_GROUP';
    $field6->name = 'itemcodes_group';
    $field6->table = 'vtiger_itemcodes';
    $field6->column ='itemcodes_group';
    $field6->columntype = 'varchar(100)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~M';
    $field6->quickcreate = 0;
    $field6->summaryfield = 1;

    $blockInstance->addField($field6);
    $field6->setPicklistValues(['Transportation', 'Transportation - Other', 'Containers', 'Packing', 'Accessorials', 'Unpacking', 'Bulkies', 'Storage', 'Valuation', 'Local Labor', 'Project Management', 'Charges and Fees', 'Drayage', 'Third Party Services']);
}

// IGC Tariff Service Code
$field7 = Vtiger_Field::getInstance('itemcodes_tariffservicecode', $moduleInstance);
if ($field7) {
    echo "<br> The itemcodes_tariffservicecode field already exists in Employee_Roles <br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_ITEMCODES_TARIFFSERVICECODE';
    $field7->name = 'itemcodes_tariffservicecode';
    $field7->table = 'vtiger_itemcodes';
    $field7->column ='itemcodes_tariffservicecode';
    $field7->columntype = 'varchar(100)';
    $field7->uitype = 1;
    $field7->typeofdata = 'V~O';
    $field7->quickcreate = 0;
    $field7->summaryfield = 1;

    $blockInstance->addField($field7);
}

//Vanline Code
$field8 = Vtiger_Field::getInstance('itemcodes_vancode', $moduleInstance);
if ($field8) {
    echo "<li>The itemcodes_vancode field already exists</li><br> \n";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_ITEMCODES_VANCODE';
    $field8->name = 'itemcodes_vancode';
    $field8->table = 'vtiger_itemcodes';
    $field8->column = 'itemcodes_vancode';
    $field8->columntype = 'varchar(100)';
    $field8->uitype = 1;
    $field8->typeofdata = 'V~O';
    $blockInstance->addField($field8);
}

//Remove Filter default
$allFilter = Vtiger_Filter::getInstance("All", $moduleInstance);
if($allFilter) {
    $allFilter->delete();
}
    //add filter
$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field2)->addField($field3, 1)->addField($field5, 2)->addField($field6, 3);

if ($isNew) {
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();

    //Menu
    $parentLabel = 'COMPANY_ADMIN_TAB';
    $db = PearDatabase::getInstance();
    if ($db) {
        $stmt = 'UPDATE vtiger_tab SET parent=? WHERE tabid=?';
        $db->pquery($stmt, [$parentLabel, $moduleInstance->id]);
    } else {
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tab SET parent='".$parentLabel."' WHERE tabid=".$moduleInstance->id);
    }
}


/***********************/
// Create Item Codes Mapping Module
echo "<h1>Create Item Codes Mapping Module </h1><br>";
$isNew = false;
$moduleInstance = Vtiger_Module::getInstance('ItemCodesMapping');

if ($moduleInstance) {
    echo "<h2>Item Codes Mapping already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'ItemCodesMapping';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $isNew = true;
}

$blockInstance = Vtiger_Block::getInstance('LBL_ITEMCODES_MAPPING', $moduleInstance);

if ($blockInstance) {
    echo "<h3>The LBL_ITEMCODES_MAPPING block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_ITEMCODES_MAPPING';
    $moduleInstance->addBlock($blockInstance);
}


//* Business Line
$field1 = Vtiger_Field::getInstance('itcmapping_businessline', $moduleInstance);
if ($field1) {
    echo "<br> The itcmapping_businessline field already exists in Employee Roles <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_BUSINESSLINE';
    $field1->name = 'itcmapping_businessline';
    $field1->table = 'vtiger_itemcodesmapping';
    $field1->column ='itcmapping_businessline';
    $field1->columntype = 'text';
    $field1->uitype = 3333;
    $field1->typeofdata = 'V~M';
    $field1->quickcreate = 0;
    $field1->defaultvalue = 'All';

    $blockInstance->addField($field1);
    $field1->setPicklistValues(['HHG - Interstate', 'HHG - Intrastate', 'HHG - Local', 'HHG - International', 'Electronics - Interstate', 'Electronics - Intrastate', 'Electronics - Local', 'Electronics - International', 'Display & Exhibits - Interstate', 'Display & Exhibits - Intrastate', 'Display & Exhibits - Local', 'Display & Exhibits - International', 'General Commodities - Interstate', 'General Commodities - Intrastate', 'General Commodities - Local', 'General Commodities - International', 'Auto - Interstate', 'Auto - Intrastate', 'Auto - Local', 'Auto - International', 'Commercial - Interstate', 'Commercial - Intrastate', 'Commercial - Local', 'Commercial - International']);
    $moduleInstance->setEntityIdentifier($field1);
}


// * Billing Type
$field2 = Vtiger_Field::getInstance('itcmapping_billingtype', $moduleInstance);
if ($field2) {
    echo "<br> The itcmapping_billingtype field already exists in Employee Roles <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_BILLING_TYPE';
    $field2->name = 'itcmapping_billingtype';
    $field2->table = 'vtiger_itemcodesmapping';
    $field2->column ='itcmapping_billingtype';
    $field2->columntype = 'text';
    $field2->uitype = 3333;
    $field2->typeofdata = 'V~M';
    $field2->quickcreate = 0;
    $field2->defaultvalue = 'All';

    $blockInstance->addField($field2);
    $field2->setPicklistValues(['COD', 'National Account', 'GSA', 'Military']);
    $moduleInstance->setEntityIdentifier($field2);
}

// * Authority
$field3 = Vtiger_Field::getInstance('itcmapping_authority', $moduleInstance);
if ($field3) {
    echo "<br> The itcmapping_authority field already exists in Employee Roles <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_AUTHORITY';
    $field3->name = 'itcmapping_authority';
    $field3->table = 'vtiger_itemcodesmapping';
    $field3->column ='itcmapping_authority';
    $field3->columntype = 'text';
    $field3->uitype = 3333;
    $field3->typeofdata = 'V~M';
    $field3->quickcreate = 0;
    $field3->defaultvalue = 'All';

    $blockInstance->addField($field3);
    $field3->setPicklistValues(['Van Line', 'Own Authority', 'Other Agent Authority']);
    $moduleInstance->setEntityIdentifier($field3);
}

// * G/L Code
$field4 = Vtiger_Field::getInstance('itcmapping_glcode', $moduleInstance);
if ($field4) {
    echo "<br> The itcmapping_glcode field already exists in Employee Roles <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_GLCODE';
    $field4->name = 'itcmapping_glcode';
    $field4->table = 'vtiger_itemcodesmapping';
    $field4->column ='itcmapping_glcode';
    $field4->columntype = 'varchar(100)';
    $field4->uitype = 1;
    $field4->typeofdata = 'V~M';
    $field4->quickcreate = 0;

    $blockInstance->addField($field4);
}

// Sales Expense Account
$field5 = Vtiger_Field::getInstance('itcmapping_salesexpense', $moduleInstance);
if ($field5) {
    echo "<br> The itcmapping_salesexpense field already exists in Employee Roles <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_SALES_EXPENSE';
    $field5->name = 'itcmapping_salesexpense';
    $field5->table = 'vtiger_itemcodesmapping';
    $field5->column ='itcmapping_salesexpense';
    $field5->columntype = 'varchar(100)';
    $field5->uitype = 1;
    $field5->typeofdata = 'V~O';
    $field5->quickcreate = 0;

    $blockInstance->addField($field5);
}

// Owner Operator Expense Account
$field6 = Vtiger_Field::getInstance('itcmapping_owner_operatorexpense', $moduleInstance);
if ($field6) {
    echo "<br> The itcmapping_owner_operatorexpense field already exists in Employee Roles <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_OWNER_OPERATOR_EXPENSE';
    $field6->name = 'itcmapping_owner_operatorexpense';
    $field6->table = 'vtiger_itemcodesmapping';
    $field6->column ='itcmapping_owner_operatorexpense';
    $field6->columntype = 'varchar(100)';
    $field6->uitype = 1;
    $field6->typeofdata = 'V~O';
    $field6->quickcreate = 0;

    $blockInstance->addField($field6);
}

// Company Driver Expense Account
$field7 = Vtiger_Field::getInstance('itcmapping_company_driverexpense', $moduleInstance);
if ($field7) {
    echo "<br> The itcmapping_company_driverexpense field already exists in Employee Roles <br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_COMPANY_DRIVER_EXPENSE';
    $field7->name = 'itcmapping_company_driverexpense';
    $field7->table = 'vtiger_itemcodesmapping';
    $field7->column ='itcmapping_company_driverexpense';
    $field7->columntype = 'varchar(100)';
    $field7->uitype = 1;
    $field7->typeofdata = 'V~O';
    $field7->quickcreate = 0;

    $blockInstance->addField($field7);
}


// Lease Driver Expense Account
$field8 = Vtiger_Field::getInstance('itcmapping_lease_driverexpense', $moduleInstance);
if ($field8) {
    echo "<br> The itcmapping_lease_driverexpense field already exists in Employee Roles <br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_LEASE_DRIVER_EXPENSE';
    $field8->name = 'itcmapping_lease_driverexpense';
    $field8->table = 'vtiger_itemcodesmapping';
    $field8->column ='itcmapping_lease_driverexpense';
    $field8->columntype = 'varchar(100)';
    $field8->uitype = 1;
    $field8->typeofdata = 'V~O';
    $field8->quickcreate = 0;

    $blockInstance->addField($field8);
}


// Packer Expense Account
$field9 = Vtiger_Field::getInstance('itcmapping_packer_expense', $moduleInstance);
if ($field9) {
    echo "<br> The itcmapping_packer_expense field already exists in Employee Roles <br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_PACKER_EXPENSE';
    $field9->name = 'itcmapping_packer_expense';
    $field9->table = 'vtiger_itemcodesmapping';
    $field9->column ='itcmapping_packer_expense';
    $field9->columntype = 'varchar(100)';
    $field9->uitype = 1;
    $field9->typeofdata = 'V~O';
    $field9->quickcreate = 0;

    $blockInstance->addField($field9);
}


// 3rd Party Service Expense Account
$field10 = Vtiger_Field::getInstance('itcmapping_3rdparty_serviceexpense', $moduleInstance);
if ($field10) {
    echo "<br> The itcmapping_3rdparty_serviceexpense field already exists in Employee Roles <br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_3RDPARTY_SERVICE_EXPENSE';
    $field10->name = 'itcmapping_3rdparty_serviceexpense';
    $field10->table = 'vtiger_itemcodesmapping';
    $field10->column ='itcmapping_3rdparty_serviceexpense';
    $field10->columntype = 'varchar(100)';
    $field10->uitype = 1;
    $field10->typeofdata = 'V~O';
    $field10->quickcreate = 0;

    $blockInstance->addField($field10);
}

// Item Codes
$field11 = Vtiger_Field::getInstance('itcmapping_itemcode', $moduleInstance);
if ($field11) {
    echo "<br> The itcmapping_itemcode field already exists in Employee Roles <br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'Item Code';
    $field11->name = 'itcmapping_itemcode';
    $field11->table = 'vtiger_itemcodesmapping';
    $field11->column ='itcmapping_itemcode';
    $field11->columntype = 'varchar(100)';
    $field11->uitype = 10;
    $field11->typeofdata = 'V~O';
    $field11->quickcreate = 0;

    $blockInstance->addField($field11);
    $field11->setRelatedModules(array('ItemCodes'));
}

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3);

    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
}

// Update label field of Item Code module and update label of vtiger_crmentity
$adb->pquery("update `vtiger_entityname` set `fieldname`='itemcodes_number' where `tabid`=?;", array(getTabid("ItemCodes")));
$adb->pquery("UPDATE vtiger_crmentity, vtiger_itemcodes SET vtiger_crmentity.label=vtiger_itemcodes.itemcodes_number WHERE vtiger_crmentity.crmid=vtiger_itemcodes.itemcodesid;", array());


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";