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

$moduleInstance = new Vtiger_Module();
$moduleInstance->name = 'Storage';
$moduleInstance->save();

$moduleInstance->initTables();

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_STORAGE_INFORMATION';
$moduleInstance->addBlock($blockInstance);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_STORAGE_LOCATION';
$field1->name = 'storage_location';
$field1->table = 'vtiger_storage';
$field1->column = 'storage_location';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 16;
$field1->typeofdata = 'V~O';

$blockInstance->addField($field1);
$field1->setPicklistValues(array('Origin', 'Destination'));

$moduleInstance->setEntityIdentifier($field1);

$field3 = new Vtiger_Field();
$field3->label = 'Assigned To';
$field3->name = 'assigned_user_id';
$field3->table = 'vtiger_crmentity';
$field3->column = 'smownerid';
$field3->uitype = 53;
$field3->typeofdata = 'V~M';

$blockInstance->addField($field3);

$field4 = new Vtiger_Field();
$field4->label = 'Created Time';
$field4->name = 'CreatedTime';
$field4->table = 'vtiger_crmentity';
$field4->column = 'createdtime';
$field4->uitype = 70;
$field4->typeofdata = 'T~O';
$field4->displaytype = 2;

$blockInstance->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'Modified Time';
$field5->name = 'ModifiedTime';
$field5->table = 'vtiger_crmentity';
$field5->column = 'modifiedtime';
$field5->uitype = 70;
$field5->typeofdata = 'T~O';
$field5->displaytype = 2;

$blockInstance->addField($field5);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_STORAGE_AGENT';
$field10->name = 'storage_agent';
$field10->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'storage_agent';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(255)';
$field10->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field10);
$field10->setRelatedModules(array('Agents'));

$field6 = new Vtiger_Field();
$field6->label = 'LBL_STORAGE_SITDATEIN';
$field6->name = 'storage_datein';
$field6->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'storage_datein';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'DATE';
$field6->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_STORAGE_SITDATEOUT';
$field7->name = 'storage_dateout';
$field7->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'storage_dateout';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'DATE';
$field7->uitype = 5; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'D~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field7);

$field11 = new Vtiger_Field();
$field11->label = 'LBL_STORAGE_DAYSTORAGE';
$field11->name = 'storage_days';
$field11->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'storage_days';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'INT(25)';
$field11->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
$field11->displaytype = 2;

$blockInstance->addField($field11);

$field14 = new Vtiger_Field();
$field14->label = 'LBL_STORAGE_PROJECT';
$field14->name = 'storage_project';
$field14->table = 'vtiger_storage';  // This is the tablename from your database that the new field will be added to.
$field14->column = 'storage_project';   //  This will be the columnname in your database for the new field.
$field14->columntype = 'VARCHAR(100)';
$field14->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field14->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field14);
$field14->setRelatedModules(array('Project'));

$blockInstance->save($moduleInstance);

$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);
$filter1->addField($field1)->addField($field6, 1)->addField($field7, 2);


$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();
//START Add navigation link in module
;
