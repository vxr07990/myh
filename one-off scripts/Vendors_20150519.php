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


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
//required for modcomments
include_once('modules/ModComments/ModComments.php');
//required for updates tracker
include_once('modules/ModTracker/ModTracker.php');

$vendorsIsNew = false;

//create module Vendors if it doesn't already exist odds are it does
$module1 = Vtiger_Module::getInstance('Vendors');
if ($module1) {
    echo "<h2>Updating Vendors Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Vendors';
    $module1->save();
    echo "<h2>Creating Module Vendors and Updating Fields</h2><br>";
    $module1->initTables();
}
//add block LBL_VENDOR_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_VENDOR_INFORMATION', $module1);
if ($block1) {
    echo "<h3>The LBL_VENDOR_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_VENDOR_INFORMATION';
    $module1->addBlock($block1);
    $vendorsIsNew = true;
}
echo "<ul>";
//start block1 fields

$field1 = Vtiger_Field::getInstance('vendorname', $module1);
if ($field1) {
    echo "<li>The vendorname field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_VENDORS_VENDORNAME';
    $field1->name = 'vendorname';
    $field1->table = 'vtiger_vendor';
    $field1->column = 'vendorname';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~M';
    $field1->sequence = 1;
    
    $block1->addField($field1);
}

$field2 = Vtiger_Field::getInstance('category', $module1);
if ($field2) {
    echo "<li>The category field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_VENDORS_CATEGORY';
    $field2->name = 'category';
    $field2->table = 'vtiger_vendor';
    $field2->column = 'category';
    $field2->columntype = 'VARCHAR(100)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~O';
    $field2->sequence = 2;
    
    $block1->addField($field2);
}
$field3 = Vtiger_Field::getInstance('vendor_address1', $module1);
if ($field3) {
    echo "<li>The vendor_address1 field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_VENDOR_ADDRESS1';
    $field3->name = 'vendor_address1';
    $field3->table = 'vtiger_vendor';
    $field3->column = 'vendor_address1';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';
    $field3->sequence = 3;
    
    $block1->addField($field3);
}
$field4 = Vtiger_Field::getInstance('pobox', $module1);
if ($field4) {
    echo "<li>The pobox field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_VENDORS_POBOX';
    $field4->name = 'pobox';
    $field4->table = 'vtiger_vendor';
    $field4->column = 'pobox';
    $field4->columntype = 'VARCHAR(30)';
    $field4->uitype = 1;
    $field4->typeofdata = 'V~O';
    $field4->sequence = 4;
    
    $block1->addField($field4);
}
$field5 = Vtiger_Field::getInstance('city', $module1);
if ($field5) {
    echo "<li>The city field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_VENDORS_CITY';
    $field5->name = 'city';
    $field5->table = 'vtiger_vendor';
    $field5->column = 'city';
    $field5->columntype = 'VARCHAR(30)';
    $field5->uitype = 1;
    $field5->typeofdata = 'V~O';
    $field5->sequence = 5;
    
    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('state', $module1);
if ($field6) {
    echo "<li>The state field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_VENDORS_STATE';
    $field6->name = 'state';
    $field6->table = 'vtiger_vendor';
    $field6->column = 'state';
    $field6->columntype = 'VARCHAR(30)';
    $field6->uitype = 1;
    $field6->typeofdata = 'V~O';
    $field6->sequence = 6;
    
    $block1->addField($field6);
}
$field7 = Vtiger_Field::getInstance('postalcode', $module1);
if ($field7) {
    echo "<li>The postalcode field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_VENDORS_POSTALCODE';
    $field7->name = 'postalcode';
    $field7->table = 'vtiger_vendor';
    $field7->column = 'postalcode';
    $field7->columntype = 'VARCHAR(100)';
    $field7->uitype = 1;
    $field7->typeofdata = 'V~O';
    $field7->sequence = 7;
    
    $block1->addField($field7);
}
$field8 = Vtiger_Field::getInstance('country', $module1);
if ($field8) {
    echo "<li>The country field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_VENDORS_COUNTRY';
    $field8->name = 'country';
    $field8->table = 'vtiger_vendor';
    $field8->column = 'country';
    $field8->columntype = 'VARCHAR(100)';
    $field8->uitype = 1;
    $field8->typeofdata = 'V~O';
    $field8->sequence = 8;
    
    $block1->addField($field8);
}
$field9 = Vtiger_Field::getInstance('phone', $module1);
if ($field9) {
    echo "<li>The phone field alread exists</li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_VENDORS_PHONE';
    $field9->name = 'phone';
    $field9->table = 'vtiger_vendor';
    $field9->column = 'phone';
    $field9->columntype = 'VARHCHAR(100)';
    $field9->uitype = 1;
    $field9->typeofdata = 'V~O';
    $field9->sequence = 9;
    
    $block1->addField($field9);
}
$field10 = Vtiger_Field::getInstance('vendor_phone2', $module1);
if ($field10) {
    echo "<li>The vendor_phone2 field already exists<li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_VENDOR_PHONE2';
    $field10->name = 'vendor_phone2';
    $field10->table = 'vtiger_vendor';
    $field10->column = 'vendor_phone2';
    $field10->columntype = 'INT(100)';
    $field10->uitype = 7;
    $field10->typeofdata = 'I~O';
    $field10->sequence = 10;
    
    $block1->addField($field10);
}
$field11 = Vtiger_Field::getInstance('vendor_contact1', $module1);
if ($field11) {
    echo "<li>The vendor_contact1 field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_VENDOR_CONTACT1';
    $field11->name = 'vendor_contact1';
    $field11->table = 'vtiger_vendor';
    $field11->column = 'vendor_contact1';
    $field11->columntype = 'VARCHAR(255)';
    $field11->uitype = 1;
    $field11->typeofdata = 'V~O';
    $field11->sequence = 11;
    
    $block1->addField($field11);
}
$field12 = Vtiger_Field::getInstance('email', $module1);
if ($field12) {
    echo "<li>The email field already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_VENDORS_EMAIL';
    $field12->name = 'email';
    $field12->table = 'vtiger_vendor';
    $field12->column = 'email';
    $field12->columntype = 'VARCHAR(100)';
    $field12->uitype = 13;
    $field12->typeofdata = 'E~O';
    $field12->sequence = 12;
    
    $block1->addField($field12);
}
$field13 = Vtiger_Field::getInstance('vendor_contact2', $module1);
if ($field13) {
    echo "<li>The vendor_contact2 field already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_VENDOR_CONTACT2';
    $field13->name = 'vendor_contact2';
    $field13->table = 'vtiger_vendor';
    $field13->column = 'vendor_contact2';
    $field13->columntype = 'VARCHAR(255)';
    $field13->uitype = 1;
    $field13->typeofdata = 'V~O';
    $field13->sequence = 13;
    
    $block1->addField($field13);
}
$field14 = Vtiger_Field::getInstance('vendor_email2', $module1);
if ($field14) {
    echo "<li>The vendor_email2 field already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_VENDOR_EMAIL2';
    $field14->name = 'vendor_email2';
    $field14->table = 'vtiger_vendor';
    $field14->column = 'vendor_email2';
    $field14->columntype = 'VARCHAR(255)';
    $field14->uitype = 13;
    $field14->typeofdata = 'E~O';
    $field14->sequence = 14;
    
    $block1->addField($field14);
}
$field15 = Vtiger_Field::getInstance('vendor_status', $module1);
if ($field15) {
    echo "<li>The vendor_status field already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_VENDOR_STATUS';
    $field15->name = 'vendor_status';
    $field15->table = 'vtiger_vendor';
    $field15->column = 'vendor_status';
    $field15->columntype = 'VARCHAR(255)';
    $field15->uitype = 16;
    $field15->typeofdata = 'V~O';
    $field15->sequence = 15;
    
    $field15->setPicklistValues(array('Active', 'Inactive'));
    $block1->addField($field15);
}
$field16 = Vtiger_Field::getInstance('website', $module1);
if ($field16) {
    echo "<li>The website field already exists</li><br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_VENDORS_WEBSITE';
    $field16->name = 'website';
    $field16->table = 'vtiger_vendor';
    $field16->column = 'website';
    $field16->columntype = 'VARCHAR(100)';
    $field16->uitype = 17;
    $field16->typeofdata = 'V~O';
    $field16->sequence = 16;
    
    $block1->addField($field16);
}
$field17 = Vtiger_Field::getInstance('vendor_no', $module1);
if ($field17) {
    echo "<li>The vendor_no field already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_VENDORS_VENDORNO';
    $field17->name = 'vendor_no';
    $field17->table = 'vtiger_vendor';
    $field17->column = 'vendor_no';
    $field17->columntype = 'VARCHAR(100)';
    $field17->uitype = 4;
    $field17->typeofdata = 'V~O';
    $field17->sequence = 17;
    
    $block1->addField($field17);
}
//end block1 fields
echo "</ul>";
$block1->save($module1);
//end block1 : LBL_VENDOR_INFORMATION

//start block2 : LBL_DESCRIPTION_INFORMATION
$block2 = Vtiger_Block::getInstance('LBL_DESCRIPTION_INFORMATION', $module1);
if ($block2) {
    echo "<h3>The LBL_DESCRIPTION_INFORMATION block already exists</h3><br>";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_DESCRIPTION_INFORMATION';
    $module1->addBlock($block2);
}
//start block2 fields
echo "<ul>";

$field18 = Vtiger_Field::getInstance('createdtime', $module1);
if ($field18) {
    echo "<li>The createdtime field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_VENDORS_CREATEDTIME';
    $field18->name = 'createdtime';
    $field18->table = 'vtiger_crmentity';
    $field18->column = 'createdtime';
    $field18->columntype = 'datetime';
    $field18->uitype = 70;
    $field18->typeofdata = 'DT~O';
    $field18->sequence = 1;
    $field18->presence = 2;
    
    $block2->addField($field18);
}
$field19 = Vtiger_Field::getInstance('modifiedtime', $module1);
if ($field19) {
    echo "<li>The modifiedtime field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_VENDORS_MODIFIEDTIME';
    $field19->name = 'modifiedtime';
    $field19->table = 'vtiger_crmentity';
    $field19->column = 'modifiedtime';
    $field19->columntype = 'datetime';
    $field19->uitype = 70;
    $field19->typeofdata = 'DT~O';
    $field19->sequence = 2;
    $field19->presence = 2;
    
    $block2->addField($field19);
}
$field20 = Vtiger_Field::getInstance('created_user_id', $module1);
if ($field20) {
    echo "<li>The created_user_id field already exists</li><br>";
} else {
    $field20  = new Vtiger_Field();
    $field20->label = 'LBL_VENDORS_CREATEDBY';
    $field20->name = 'created_user_id';
    $field20->table = 'vtiger_crmentity';
    $field20->column = 'smcreatorid';
    $field20->columntype = 'int(19)';
    $field20->uitype = 53;
    $field20->typeofdata = 'V~O';
    $field20->sequence = 3;
    $field20->presence = 2;
    
    $block2->addField($field20);
}
//end block2 fields
echo "</ul>";
$block2->save($module1);
//end block2 : LBL_DESCRIPTION_INFORMATION


//add ModComments Widget
$commentsModule = Vtiger_Module::getInstance('ModComments');
$fieldInstance = Vtiger_Field::getInstance('related_to', $commentsModule);
$fieldInstance->setRelatedModules(array('Vendors'));
ModComments::removeWidgetFrom('Vendors');
ModComments::addWidgetTo('Vendors');
//end ModComments Widget
//add ModTracker Widget
ModTracker::enableTrackingForModule($module1->id);
//Products			ADD SELECT
$module2 = Vtiger_Module::getInstance('Products');
if ($module2) {
    $module1->unsetRelatedList($module2, 'Products', 'get_products');
    $module1->setRelatedList($module2, 'Products', array('ADD', 'SELECT'), 'get_products');
    echo "<h3>Set Related List of Vendors -> Products</h3><br>";
} else {
    echo "<h3>Could not set Related List of Vendors -> Products, as Products does not exist</h3><br>";
}
//Purchase Order	ADD
$module3 = Vtiger_Module::getInstance('PurchaseOrder');
if ($module3) {
    $module1->unsetRelatedList($module3, 'Purchase Order', 'get_purchase_orders');
    $module1->setRelatedList($module3, 'Purchase Order', array('ADD'), 'get_purchase_orders');
    echo "<h3>Set Related List of Vendors -> PurchaseOrder</h3><br>";
} else {
    echo "<h3>Could not set Related List of Vendors -> PurchaseOrder, as PurchaseOrder does not exist</h3><br>";
}
//Contacts 			SELECT
$module4 = Vtiger_Module::getInstance('Contacts');
if ($module4) {
    $module1->unsetRelatedList($module4, 'Contacts', 'get_contacts');
    $module1->setRelatedList($module4, 'Contacts', array('SELECT'), 'get_contacts');
    echo "<h3>Set Related List of Vendors -> Contacts </h3><br>";
} else {
    echo "<h3>Could not set Related List of Vendors -> Contacts, as Contacts does not exist</h3><br>";
}
//Emails 			ADD
$module5 = Vtiger_Module::getInstance('Emails');
if ($module5) {
    $module1->unsetRelatedList($module5, 'Emails', 'get_emails');
    $module1->setRelatedList($module5, 'Emails', array('ADD'), 'get_emails');
    echo "<h3>Set Related List of Vendors -> Emails</h3><br>";
} else {
    echo "<h3>Could not set Related List of Vendors -> Emails, as Emails does not exist</h3><br>";
}

if ($vendorsIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module1->addFilter($filter1);

    $filter1->addField($field2)->addField($field21, 1)->addField($field3, 2)->addField($field4, 3);

    $module1->setDefaultSharing();
    $module1->initWebservice();
}

//hide the random arbitrary Vtiger_Core fields
echo "<h3>Hiding Core_Vtiger fields</h3><br>";
$tempField = Vtiger_Field::getInstance('glacct', $module1);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET presence = 1 WHERE fieldid = '.$tempField->id);
$tempField = Vtiger_Field::getInstance('description', $module1);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET presence = 1 WHERE fieldid = '.$tempField->id);
$tempField = Vtiger_Field::getInstance('street', $module1);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET presence = 1 WHERE fieldid = '.$tempField->id);
$tempField = Vtiger_Field::getInstance('assigned_user_id', $module1);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET presence = 1 WHERE fieldid = '.$tempField->id);
$tempField = Vtiger_Field::getInstance('modifiedby', $module1);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET presence = 1 WHERE fieldid = '.$tempField->id);
//reorder and move things into the correct blocks, and set presences to the
//correct thing because better safe than sorry and Vtiger_Core seems to defualt to presence 2 for no good reason
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 1, presence = 0'. ' WHERE fieldid = ' . $field1->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 2, presence = 0'. ' WHERE fieldid = ' . $field2->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 3, presence = 0'. ' WHERE fieldid = ' . $field3->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 4, presence = 0'. ' WHERE fieldid = ' . $field4->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 5, presence = 0'. ' WHERE fieldid = ' . $field5->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 6, presence = 0'. ' WHERE fieldid = ' . $field6->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 7, presence = 0'. ' WHERE fieldid = ' . $field7->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 8, presence = 0'. ' WHERE fieldid = ' . $field8->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 9, presence = 0'. ' WHERE fieldid = ' . $field9->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 10, presence = 0'. ' WHERE fieldid = ' . $field10->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 11, presence = 0'. ' WHERE fieldid = ' . $field11->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 12, presence = 0'. ' WHERE fieldid = ' . $field12->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 13, presence = 0'. ' WHERE fieldid = ' . $field13->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 14, presence = 0'. ' WHERE fieldid = ' . $field14->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 15, presence = 0'. ' WHERE fieldid = ' . $field15->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 16, presence = 0'. ' WHERE fieldid = ' . $field16->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block1->id . ', sequence = 17, presence = 0'. ' WHERE fieldid = ' . $field17->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 1, presence = 2'. ' WHERE fieldid = ' . $field18->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 2, presence = 2'. ' WHERE fieldid = ' . $field19->id);
Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET block = ' . $block2->id . ', sequence = 3, presence = 2'. ' WHERE fieldid = ' . $field20->id);
