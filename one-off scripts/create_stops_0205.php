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
$moduleInstance->name = 'Stops';
$moduleInstance->save();

$moduleInstance->initTables();

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_STOPS_INFORMATION';
$moduleInstance->addBlock($blockInstance);


$field1 = new Vtiger_Field();
$field1->label = 'LBL_STOPS_NAME';
$field1->name = 'stops_name';
$field1->table = 'vtiger_stops';
$field1->column = 'stops_name';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 1;
$field1->typeofdata = 'V~O';

$blockInstance->addField($field1);

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
$field10->label = 'LBL_STOPS_ADDRESS1';
$field10->name = 'stops_address1';
$field10->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'stops_address1';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(255)';
$field10->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field10);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_STOPS_CITY';
$field6->name = 'stops_city';
$field6->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'stops_city';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'VARCHAR(255)';
$field6->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_STOPS_STATE';
$field7->name = 'stops_state';
$field7->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'stops_state';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'VARCHAR(255)';
$field7->uitype = 1; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field7);


$field11 = new Vtiger_Field();
$field11->label = 'LBL_STOPS_P1';
$field11->name = 'stops_p1';
$field11->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'stops_p1';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'INT(25)';
$field11->uitype = 7; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'I~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field11);

$field14 = new Vtiger_Field();
$field14->label = 'LBL_STOPS_PROJECT';
$field14->name = 'stops_project';
$field14->table = 'vtiger_stops';  // This is the tablename from your database that the new field will be added to.
$field14->column = 'stops_project';   //  This will be the columnname in your database for the new field.
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
$filter1->addField($field1)->addField($field10, 1)->addField($field6, 2)->addField($field7, 3)->addField($field11, 4);


$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();
//START Add navigation link in module
;
