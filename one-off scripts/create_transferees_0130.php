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
$moduleInstance->name = 'Transferees';
$moduleInstance->save();

$moduleInstance->initTables();

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_TRANSFEREES_INFORMATION';
$moduleInstance->addBlock($blockInstance);


$field1 = new Vtiger_Field();
$field1->label = 'LBL_TRANSFEREES_FNAME';
$field1->name = 'transferees_fname';
$field1->table = 'vtiger_transferees';
$field1->column = 'transferees_fname';
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = 2;
$field1->typeofdata = 'V~M';

$blockInstance->addField($field1);
    
$moduleInstance->setEntityIdentifier($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_TRANSFEREES_ADDRESS1';
$field2->name = 'transferees_address1';
$field2->table = 'vtiger_transferees';
$field2->column = 'transferees_address1';
$field2->columntype = 'VARCHAR(255)';
$field2->uitype = 1;
$field2->typeofdata = 'V~O';

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

$field6 = new Vtiger_Field();
$field6->label = 'LBL_TRANSFEREES_P3';
$field6->name = 'transferees_p3';
$field6->table = 'vtiger_transferees';
$field6->column = 'transferees_p3';
$field6->columntype = 'INT(20)';
$field6->uitype = 7;
$field6->typeofdata = 'V~O';

$blockInstance->addField($field6);

$field7 = new Vtiger_Field();
$field7->label = 'LBL_TRANSFEREES_LNAME';
$field7->name = 'transferees_lname';
$field7->table = 'vtiger_transferees';
$field7->column = 'transferees_lname';
$field7->columntype = 'VARCHAR(255)';
$field7->uitype = 2;
$field7->typeofdata = 'V~M';

$blockInstance->addField($field7);

$field8 = new Vtiger_Field();
$field8->label = 'LBL_TRANSFEREES_PROJECT';
$field8->name = 'transferees_project';
$field8->table = 'vtiger_transferees';
$field8->column = 'transferees_project';
$field8->columntype = 'VARCHAR(255)';
$field8->uitype = 10;
$field8->typeofdata = 'V~O';

$blockInstance->addField($field8);
$field8->setRelatedModules(array('Project'));


$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field7, 1)->addField($field2, 2)->addField($field6, 3);

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();
//START Add navigation link in module
;
