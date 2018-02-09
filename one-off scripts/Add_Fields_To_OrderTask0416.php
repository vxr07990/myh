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

 
// Turn on debugging level
$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$module = Vtiger_Module::getInstance('OrdersTask');

$block1 = new Vtiger_Block();
$block1->label = 'LBL_OPERATIVE_TASK_INFORMATION';
$module->addBlock($block1);

$field1 = new Vtiger_Field();
$field1->label = 'Service';
$field1->name = 'service';
$field1->table = 'vtiger_orderstask';
$field1->column = 'service';
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 2;
$field1->typeofdata = 'V~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Crew Number';
$field1->name = 'crew_number';
$field1->table = 'vtiger_orderstask';
$field1->column = 'crew_number';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Estimate Travel Time';
$field1->name = 'estimated_hours';
$field1->table = 'vtiger_orderstask';
$field1->column = 'estimated_hours';
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Estimate Travel Time';
$field1->name = 'estimate_travel';
$field1->table = 'vtiger_orderstask';
$field1->column = 'estimate_travel';
$field1->columntype = 'DECIMAL(10,2)';
$field1->uitype = 7;
$field1->typeofdata = 'N~O';


$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Stops Numbers';
$field1->name = 'stops_number';
$field1->table = 'vtiger_orderstask';
$field1->column = 'stops_number';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Agent Number';
$field1->name = 'agent_number';
$field1->table = 'vtiger_orderstask';
$field1->column = 'agent_number';
$field1->columntype = 'INT(10)';
$field1->uitype = 7;
$field1->typeofdata = 'I~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Service Date From';
$field1->name = 'service_date_from';
$field1->table = 'vtiger_orderstask';
$field1->column = 'service_date_from';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Service Date To';
$field1->name = 'service_date_to';
$field1->table = 'vtiger_orderstask';
$field1->column = 'service_date_to';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);
$field1 = new Vtiger_Field();
$field1->label = 'Preferred Date Service';
$field1->name = 'pref_date_service';
$field1->table = 'vtiger_orderstask';
$field1->column = 'pref_date_service';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$block1->addField($field1);

$field1 = new Vtiger_Field();
$field1->label = 'Drivers Notes';
$field1->name = 'drivers_notes';
$field1->table = 'vtiger_orderstask';
$field1->column = 'drivers_notes';
$field1->columntype = 'TEXT';
$field1->uitype = 19;
$field1->typeofdata = 'V~O';

$block1->addField($field1);


$block1->save($module);
