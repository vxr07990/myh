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

//HOTFIX Create CapacityCalendarCounter Module

//$Vtiger_Utils_Log = true;
require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');
require_once('includes/runtime/LanguageHandler.php');

//needs these
require_once('include/Webservices/Create.php');
require_once('modules/Vtiger/uitypes/Date.php');

echo "<br>BEGINNING Create CapacityCalendarCounter Module<br>";

echo "<br>BEGINNING Creating Module<br>";

$tableConversion = false;
$oldStops = [];
$db = PearDatabase::getInstance();

$moduleInstance = Vtiger_Module::getInstance('CapacityCalendarCounter');
if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'CapacityCalendarCounter';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
}


$blockInstance = Vtiger_Block::getInstance('LBL_CAPACITYCALENDARCOUNTER_INFORMATION', $moduleInstance);
if (!$blockInstance) {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_CAPACITYCALENDARCOUNTER_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}
$blockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if (!$blockInstance2) {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

$field1 = Vtiger_Field::getInstance('calendar_code', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_CALENDAR_CODE';
    $field1->name = 'calendar_code';
    $field1->table = 'vtiger_capacitycalendarcounter';
    $field1->column = 'calendar_code';
    $field1->columntype = 'VARCHAR(2)';
    $field1->uitype = 2;
    $field1->typeofdata = 'V~O';
    $field1->summaryfield = 0;

    $blockInstance->addField($field1);
    $moduleInstance->setEntityIdentifier($field1);
}
$field2 = Vtiger_Field::getInstance('orders_task_id', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_ORDERS_TASK';
    $field2->name = 'orders_task_id';
    $field2->table = 'vtiger_capacitycalendarcounter';
    $field2->column = 'orders_task_id';
    $field2->columntype = 'int(11)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~O';
    $blockInstance->addField($field2);
    $field2->setRelatedModules(array('OrdersTask'));
}

$field3 = Vtiger_Field::getInstance('capacitycalendarcounter_relcrmid', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CAPACITYCALENDARCOUNTER_RELCRMID';
    $field3->name = 'capacitycalendarcounter_relcrmid';
    $field3->table = 'vtiger_capacitycalendarcounter';
    $field3->column = 'capacitycalendarcounter_relcrmid';
    $field3->columntype = 'INT(11)';
    $field3->uitype = 10;
    $field3->typeofdata = 'V~O';

    $blockInstance->addField($field3);
    $field3->setRelatedModules(array('AgentManager'));
}


//3465: Capacity Calendar Counter (Guest Block within Agent Manager) "Bugs"
$field2->delete();
$orderTasksField = Vtiger_Field::getInstance('order_task_field', $moduleInstance);
if (!$orderTasksField) {
    $orderTasksField = new Vtiger_Field();
    $orderTasksField->label = 'LBL_ORDERS_TASK';
    $orderTasksField->name = 'order_task_field';
    $orderTasksField->table = 'vtiger_capacitycalendarcounter';
    $orderTasksField->column = 'order_task_field';
    $orderTasksField->columntype = 'varchar(255)';
    $orderTasksField->uitype = 16;
    $orderTasksField->typeofdata = 'V~O';
    $blockInstance->addField($orderTasksField);
    $orderTasksField->setPicklistValues(['Record Count', 'Personnel Number', 'Vehicle Number']);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";