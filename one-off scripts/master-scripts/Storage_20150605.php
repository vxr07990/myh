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


//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
$storageIsNew = false;

$module1 = Vtiger_Module::getInstance('Storage');
if ($module1) {
    echo "<h2>Updating Storage Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'Storage';
    $module1->save();
    echo "<h2>Creating Module Storage and Updating Fields</h2><br>";
    $module1->initTables();
    ModTracker::enableTrackingForModule($module1->id);
}

//start block 1 : LBL_STORAGE_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_STORAGE_INFORMATION', $module1);
if ($block1) {
    echo "<h3>The LBL_STORAGE_INFORMATION block already exists</h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_STORAGE_INFORMATION';
    $module1->addBlock($block1);
    $storageIsNew = true;
}
//start block1 fields
echo "<ul>";
$field1 = Vtiger_Field::getInstance('storage_location', $module1);
if ($field1) {
    echo "<li>The storage_location field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_STORAGE_LOCATION';
    $field1->name = 'storage_location';
    $field1->table = 'vtiger_storage';
    $field1->column = 'storage_location';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~M';

    $block1->addField($field1);
    $field1->setPicklistValues(array('Origin', 'Destination'));
    $module1->setEntityIdentifier($field1);
}
$field2 = Vtiger_Field::getInstance('storage_agent', $module1);
if ($field2) {
    echo "<li>The storage_agent field already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_STORAGE_AGENT';
    $field2->name = 'storage_agent';
    $field2->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
    $field2->column = 'storage_agent';   //  This will be the columnname in your database for the new field.
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field2->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field2);
    $field2->setRelatedModules(array('Agents'));
}
//field related Orders
$field0 = Vtiger_Field::getInstance('storage_orders', $module1);
if ($field0) {
    echo "<li>The storage_agent field already exists</li><br>";
} else {
    $field0 = new Vtiger_Field();
    $field0->label = 'LBL_STORAGE_ORDERS';
    $field0->name = 'storage_orders';
    $field0->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
    $field0->column = 'storage_orders';   //  This will be the columnname in your database for the new field.
    $field0->columntype = 'VARCHAR(255)';
    $field0->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field0->typeofdata = 'V~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block1->addField($field0);
    $field0->setRelatedModules(array('Orders'));
}

$field3 = Vtiger_Field::getInstance('assigned_user_id', $module1);
if ($field3) {
    echo "<li>The assigned_user_id field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'Assigned To';
    $field3->name = 'assigned_user_id';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smownerid';
    $field3->uitype = 53;
    $field3->typeofdata = 'V~M';

    $block1->addField($field3);
}

//end block1 fields
echo "</ul>";
$block1->save($module1);
//end block1 : LBL_STORAGE_INFORMATION

//start block2 : LBL_STORAGE_SITDETAILS
$block2 = Vtiger_Block::getInstance('LBL_STORAGE_SITDETAILS', $module1);
if ($block2) {
    echo "<h3>The LBL_STORAGE_SITDETAILS block already exists</h3><br>";
} else {
    $block2 = new Vtiger_Block();
    $block2->label = 'LBL_STORAGE_SITDETAILS';
    $module1->addBlock($block2);
}

//start block2 fields
echo "<ul>";
$field6 = Vtiger_Field::getInstance('storage_datein', $module1);
if ($field6) {
    echo "<li>The storage_datein field already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_STORAGE_SITDATEIN';
    $field6->name = 'storage_datein';
    $field6->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
    $field6->column = 'storage_datein';   //  This will be the columnname in your database for the new field.
    $field6->columntype = 'DATE';
    $field6->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field6);
}
$field7 = Vtiger_Field::getInstance('storage_dateout', $module1);
if ($field7) {
    echo "<li>The storage_dateout field already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_STORAGE_SITDATEOUT';
    $field7->name = 'storage_dateout';
    $field7->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
    $field7->column = 'storage_dateout';   //  This will be the columnname in your database for the new field.
    $field7->columntype = 'DATE';
    $field7->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field7->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block2->addField($field7);
}
$field8 = Vtiger_Field::getInstance('storage_days', $module1);
if ($field8) {
    echo "<li>The storage_days field already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_STORAGE_DAYSTORAGE';
    $field8->name = 'storage_days';
    $field8->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
    $field8->column = 'storage_days';   //  This will be the columnname in your database for the new field.
    $field8->columntype = 'INT(25)';
    $field8->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field8->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData


    $block2->addField($field8);
}

//end block2 fields
echo "</ul>";
$block2->save($module1);
//end block2 : LBL_STORAGE_SITDETAILS

//start block3 : LBL_STORAGE_AUTHORIZATION
$block3 = Vtiger_Block::getInstance('LBL_STORAGE_AUTHORIZATION', $module1);
if ($block3) {
    echo "<h3>The LBL_STORAGE_AUTHORIZATION block already exists</h3><br>";
} else {
    $block3 = new Vtiger_Block();
    $block3->label = 'LBL_STORAGE_AUTHORIZATION';
    $module1->addBlock($block3);
}
//start block3 fields
echo "<ul>";
$field9 = Vtiger_Field::getInstance('storage_authorization', $module1);
if ($field9) {
    echo "<li>The storage_authorization field already exists</li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_STORAGE_SITAUTHORIZATION';
    $field9->name = 'storage_authorization';
    $field9->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
    $field9->column = 'storage_authorization';   //  This will be the columnname in your database for the new field.
    $field9->columntype = 'INT(100)';
    $field9->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field9->typeofdata = 'I~M'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field9);
}
$field10 = Vtiger_Field::getInstance('storage_adays', $module1);
if ($field10) {
    echo "<li>The storage_adays field already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_STORAGE_ADAYS';
    $field10->name = 'storage_adays';
    $field10->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
    $field10->column = 'storage_adays';   //  This will be the columnname in your database for the new field.
    $field10->columntype = 'INT(25)';
    $field10->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field10->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field10);
}
$field11 = Vtiger_Field::getInstance('storage_cpsdate', $module1);
if ($field11) {
    echo "<li>The storage_cpsdate field already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_STORAGE_CPSDATE';
    $field11->name = 'storage_cpsdate';
    $field11->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
    $field11->column = 'storage_cpsdate';   //  This will be the columnname in your database for the new field.
    $field11->columntype = 'DATE';
    $field11->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field11->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

    $block3->addField($field11);
}
//end block3 fields
echo "</ul>";
$block3->save($module1);
//end block3 : LBL_STORAGE_AUTHORIZATION

//end block2 : LBL_STORAGE_SITDETAILS

//start block3 : LBL_STORAGE_AUTHORIZATION
$block4 = Vtiger_Block::getInstance('LBL_STORAGE_RECORDUPDATE', $module1);
if ($block4) {
    echo "<h3>The LBL_STORAGE_RECORDUPDATE block already exists</h3><br>";
} else {
    $block4 = new Vtiger_Block();
    $block4->label = 'LBL_STORAGE_RECORDUPDATE';
    $module1->addBlock($block4);
}
echo "<ul>";
$field4 = Vtiger_Field::getInstance('CreatedTime', $module1);
if ($field4) {
    echo "<li>The CreatedTime field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'Created Time';
    $field4->name = 'createdtime';
    $field4->table = 'vtiger_crmentity';
    $field4->column = 'createdtime';
    $field4->uitype = 70;
    $field4->typeofdata = 'T~O';
    $field4->displaytype = 2;

    $block4->addField($field4);
}
$field5 = Vtiger_Field::getInstance('ModifiedTime', $module1);
if ($field5) {
    echo "<li>The ModifiedTime field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'Modified Time';
    $field5->name = 'modifiedtime';
    $field5->table = 'vtiger_crmentity';
    $field5->column = 'modifiedtime';
    $field5->uitype = 70;
    $field5->typeofdata = 'T~O';
    $field5->displaytype = 2;

    $block4->addField($field5);
}

if ($storageIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module1->addFilter($filter1);
    $filter1->addField($field1)->addField($field6, 1)->addField($field7, 2);

    $module1->setDefaultSharing();
    $module1->initWebservice();
}
echo "</ul>";
$block4->save($module1);
//START Add navigation link in module orders to storage
$ordersInstance = Vtiger_Module::getInstance('Orders');
$ordersInstance->setRelatedList(Vtiger_Module::getInstance('Storage'), 'Storage', array('ADD'), 'get_dependents_list');
$ordersInstance->setRelatedList(Vtiger_Module::getInstance('Orders'), 'Related Orders', array('ADD', 'SELECT'), 'get_related_list');
$ordersInstance->setRelatedList(Vtiger_Module::getInstance('Contacts'), 'Contacts', array('ADD'), 'get_related_list');
//END Add navigation link in module
;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";