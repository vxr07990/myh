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
$moduleInstance->name = 'Accidents';
$moduleInstance->save();

$moduleInstance->initTables();

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_ACCIDENTS_INFORMATION';
$moduleInstance->addBlock($blockInstance);


$field1 = new Vtiger_Field();
$field1->label = 'LBL_ACCIDENTS_DATE';
$field1->name = 'accidents_date';
$field1->table = 'vtiger_accidents';
$field1->column = 'accidents_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$blockInstance->addField($field1);
    
$moduleInstance->setEntityIdentifier($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_ACCIDENTS_TIME';
$field2->name = 'accidents_time';
$field2->table = 'vtiger_accidents';
$field2->column = 'accidents_time';
$field2->columntype = 'DATE';
$field2->uitype = 14;
$field2->typeofdata = 'T~O';

$blockInstance->addField($field2);

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

$field6 = new Vtiger_Field(); // needs to bechanged not saving data
$field6->label = 'LBL_ACCIDENTS_DESCRIPTION';
$field6->name = 'description';
$field6->table = 'vtiger_crmentity';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'description';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'VARCHAR(100)';
$field6->uitype = 19; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field6);

$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field2, 1);

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();
//START Add navigation link in module
;
