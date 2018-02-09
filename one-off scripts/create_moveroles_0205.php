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
$moduleInstance->name = 'MoveRoles';
$moduleInstance->save();

$moduleInstance->initTables();

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_MOVEROLES_INFORMATION';
$moduleInstance->addBlock($blockInstance);


$field1 = new Vtiger_Field();
$field1->label = 'LBL_MOVEROLES_ROLE';
$field1->name = 'moveroles_role';
$field1->table = 'vtiger_moveroles';
$field1->column = 'moveroles_role';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 15;
$field1->typeofdata = 'V~O';

$blockInstance->addField($field1);
$field1->setPicklistValues(array('Salesperson', 'Surveyor', 'Customer Service Cordinator', 'O/A Coordinator', 'D/A Coordinator', 'Packing', 'Contractor', 'Claims Rep', 'Billing Clerk'));
    
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
$field10->label = 'LBL_MOVEROLES_EMPLOYEES';
$field10->name = 'moveroles_employees';
$field10->table = 'vtiger_moveroles';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'moveroles_employees';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(255)';
$field10->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field10);
$field10->setRelatedModules(array('Employees'));

$field11 = new Vtiger_Field();
$field11->label = 'LBL_MOVEROLES_PROJECT';
$field11->name = 'moveroles_project';
$field11->table = 'vtiger_moveroles';  // This is the tablename from your database that the new field will be added to.
$field11->column = 'moveroles_project';   //  This will be the columnname in your database for the new field.
$field11->columntype = 'VARCHAR(255)';
$field11->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field11->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field11);
$field11->setRelatedModules(array('Project'));


$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1);


$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();
//START Add navigation link in module
;
