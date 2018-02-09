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

$moduleInstance = Vtiger_Module::getInstance('Opportunities');
$potentialsModuleInstance = Vtiger_Module::getInstance('Potentials');

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_POTENTIALS_NATIONALACCOUNT';
$moduleInstance->addBlock($blockInstance);
$potentialsModuleInstance->addBlock($blockInstance);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_POTENTIALS_STREET';
$field1->name = 'street';
$field1->table = 'vtiger_potential';
$field1->column = 'street';
$field1->columntype = 'VARCHAR(250)';
$field1->uitype = 21;
$field1->typeofdata = 'V~O';

$blockInstance->addField($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_POTENTIALS_POBOX';
$field2->name = 'pobox';
$field2->table = 'vtiger_potential';
$field2->column = 'pobox';
$field2->columntype = 'VARCHAR(30)';
$field2->uitype = 1;
$field2->typeofdata = 'V~O';

$blockInstance->addField($field2);

$field3 = new Vtiger_Field();
$field3->label = 'LBL_POTENTIALS_CITY';
$field3->name = 'city';
$field3->table = 'vtiger_potential';
$field3->column = 'city';
$field3->columntype = 'VARCHAR(50)';
$field3->uitype = 1;
$field3->typeofdata = 'V~O';

$blockInstance->addField($field3);

$field4 = new Vtiger_Field();
$field4->label = 'LBL_POTENTIALS_STATE';
$field4->name = 'state';
$field4->table = 'vtiger_potential';
$field4->column = 'state';
$field4->columntype = 'VARCHAR(50)';
$field4->uitype = 1;
$field4->typeofdata = 'V~O';

$blockInstance->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_POTENTIALS_ZIP';
$field5->name = 'zip';
$field5->table = 'vtiger_potential';
$field5->column = 'zip';
$field5->columntype = 'VARCHAR(30)';
$field5->uitype = 1;
$field5->typeofdata = 'V~O';

$blockInstance->addField($field5);

$field6 = new Vtiger_Field();
$field6->label = 'LBL_POTENTIALS_COUNTRY';
$field6->name = 'country';
$field6->table = 'vtiger_potential';
$field6->column = 'country';
$field6->columntype = 'VARCHAR(50)';
$field6->uitype = 1;
$field6->typeofdata = 'V~O';

$blockInstance->addField($field6);
