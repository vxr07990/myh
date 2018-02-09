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

$module = Vtiger_Module::getInstance('Employees');

$block1 = new Vtiger_Block();
$block1->label = 'LBL_EMPLOYEES_TYPE';
$module->addBlock($block1);


$field = new Vtiger_Field();
$field->label = 'LBL_EMPLOYEES_TYPE';
$field->name = 'employee_type';
$field->table = 'vtiger_employees';
$field->column = 'employee_type';
$field->columntype = 'VARCHAR(100)';
$field->uitype = 16;
$field->typeofdata = 'V~O';
$field->setPicklistValues(array('Office Employee', 'Crew Employee', 'Contractor'));

$block1->addField($field);

$block2 = Vtiger_Block::getInstance('LBL_EMPLOYEES_INFORMATION', $module);

$field = new Vtiger_Field();
$field->label = 'LBL_EMPLOYEES_ADDRESS2';
$field->name = 'address2';
$field->table = 'vtiger_employees';
$field->column = 'address2';
$field->columntype = 'VARCHAR(100)';
$field->uitype = 1;
$field->typeofdata = 'V~O';

$block2->addField($field);

$block3 = Vtiger_Block::getInstance('LBL_EMPLOYEES_DETAILINFO', $module);

$field = new Vtiger_Field();
$field->label = 'LBL_EMPLOYEES_NUMBER';
$field->name = 'employee_no';
$field->table = 'vtiger_employees';
$field->column = 'employee_no';
$field->columntype = 'VARCHAR(100)';
$field->uitype = 1;
$field->typeofdata = 'V~O';

$block3->addField($field);

$block4 = new Vtiger_Block();
$block4->label = 'LBL_CONTRACTORS_DETAILINFO';
$module->addBlock($block4);


$field = new Vtiger_Field();
$field->label = 'LBL_CONTRACTORS_ENUM';
$field->name = 'contractor_enum';
$field->table = 'vtiger_employees';
$field->column = 'contractor_enum';
$field->columntype = 'INT(10)';
$field->uitype = 7;
$field->typeofdata = 'I~O';

$block4->addField($field);

$field = new Vtiger_Field();
$field->label = 'LBL_CONTRACTORS_PROLE';
$field->name = 'contractor_prole';
$field->table = 'vtiger_employees';
$field->column = 'contractor_prole';
$field->columntype = 'VARCHAR(100)';
$field->uitype = 15;
$field->typeofdata = 'V~O';
$field->setPicklistValues(array('Driver', 'Packer', 'Warehouse'));

$block4->addField($field);

$field = new Vtiger_Field();
$field->label = 'LBL_CONTRACTORS_HDATE';
$field->name = 'contractor_hdate';
$field->table = 'vtiger_employees';
$field->column = 'contractor_hdate';
$field->columntype = 'DATE';
$field->uitype = 5;
$field->typeofdata = 'D~O';

$block4->addField($field);

$field = new Vtiger_Field();
$field->label = 'LBL_CONTRACTORS_TDATE';
$field->name = 'contractor_tdate';
$field->table = 'vtiger_employees';
$field->column = 'contractor_tdate';
$field->columntype = 'DATE';
$field->uitype = 5;
$field->typeofdata = 'D~O';

$block4->addField($field);

$field = new Vtiger_Field();
$field->label = 'LBL_CONTRACTORS_STATUS';
$field->name = 'contractor_status';
$field->table = 'vtiger_employees';
$field->column = 'contractor_status';
$field->columntype = 'VARCHAR(100)';
$field->uitype = 15;
$field->typeofdata = 'V~O';
$field->setPicklistValues(array('Active', 'Terminated', 'Suspended'));

$block4->addField($field);

$field = new Vtiger_Field();
$field->label = 'LBL_CONTRACTORS_RDATE';
$field->name = 'contractor_rdate';
$field->table = 'vtiger_employees';
$field->column = 'contractor_rdate';
$field->columntype = 'DATE';
$field->uitype = 5;
$field->typeofdata = 'D~O';

$block4->addField($field);

$field = new Vtiger_Field();
$field->label = 'LBL_CONTRACTORS_CEDATE';
$field->name = 'contractor_cedate';
$field->table = 'vtiger_employees';
$field->column = 'contractor_cedate';
$field->columntype = 'DATE';
$field->uitype = 5;
$field->typeofdata = 'D~O';

$block4->addField($field);

$field = new Vtiger_Field();
$field->label = 'LBL_CONTRACTORS_TRUCKNUMBER';
$field->name = 'contractor_trucknumber';
$field->table = 'vtiger_employees';
$field->column = 'contractor_trucknumber';
$field->columntype = 'VARCHAR(100)';
$field->uitype = 1;
$field->typeofdata = 'V~O';

$block4->addField($field);

$field = new Vtiger_Field();
$field->label = 'LBL_CONTRACTORS_TRAILERNUMBER';
$field->name = 'contractor_trailernumber';
$field->table = 'vtiger_employees';
$field->column = 'contractor_trailernumber';
$field->columntype = 'VARCHAR(100)';
$field->uitype = 1;
$field->typeofdata = 'V~O';

$block4->addField($field);

$block5 = new Vtiger_Block();
$block5->label = 'LBL_EMPLOYEES_PHOTO';
$module->addBlock($block5);

$field = new Vtiger_Field();
$field->label = 'LBL_EMPLOYEES_IMAGENAME';
$field->name = 'imagename';
$field->table = 'vtiger_employees';
$field->column = 'imagename';
$field->columntype = 'VARCHAR(255)';
$field->uitype = 69;
$field->typeofdata = 'V~O';

$block5->addField($field);
