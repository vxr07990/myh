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
$moduleInstance->name = 'TimeSheets';
$moduleInstance->save();

$moduleInstance->initTables();


$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_TIMESHEETS_INFORMATION';
$moduleInstance->addBlock($blockInstance);

$blockInstance2 = new Vtiger_Block();
$blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
$moduleInstance->addBlock($blockInstance2);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_TIMESHEET_ID';
$field1->name = 'timesheet_id';
$field1->table = 'vtiger_timesheets';
$field1->column = 'timesheet_id';
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 4;
$field1->typeofdata = 'V~M';

$blockInstance->addField($field1);

$moduleInstance->setEntityIdentifier($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_EMPLOYEE';
$field2->name = 'employee_id';
$field2->table = 'vtiger_timesheets';
$field2->column = 'employee_id';
$field2->columntype = 'INT(19)';
$field2->uitype = 10;
$field2->typeofdata = 'V~M';

$blockInstance->addField($field2);

$field2->setRelatedModules(array('Employees'));

$field3 = new Vtiger_Field();
$field3->label = 'LBL_TASK_ID';
$field3->name = 'ordertask_id';
$field3->table = 'vtiger_timesheets';
$field3->column = 'ordertask_id';
$field3->columntype = 'INT(19)';
$field3->uitype = 10;
$field3->typeofdata = 'D~M';

$blockInstance->addField($field3);

$field3->setRelatedModules(array('OrdersTask'));

$field4 = new Vtiger_Field();
$field4->label = 'LBL_ACTUAL_DATE';
$field4->name = 'actual_start_date';
$field4->table = 'vtiger_timesheets';
$field4->column = 'actual_start_date';
$field4->columntype = 'DATE';
$field4->uitype = 5;
$field4->typeofdata = 'D~O';

$blockInstance->addField($field4);

$field5 = new Vtiger_Field();
$field5->label = 'LBL_ACTUAL_START_HOUR';
$field5->name = 'actual_start_hour';
$field5->table = 'vtiger_timesheets';
$field5->column = 'actual_start_hour';
$field5->columntype = 'TIME';
$field5->uitype = 14;
$field5->typeofdata = 'T~O';

$blockInstance->addField($field5);


$field7 = new Vtiger_Field();
$field7->label = 'LBL_ACTUAL_END_HOUR';
$field7->name = 'actual_end_hour';
$field7->table = 'vtiger_timesheets';
$field7->column = 'actual_end_hour';
$field7->columntype = 'TIME';
$field7->uitype = 14;
$field7->typeofdata = 'T~O';

$blockInstance->addField($field7);

$field8 = new Vtiger_Field();
$field8->label = 'LBL_TOTAL_WORKED_HOURS';
$field8->name = 'total_hours';
$field8->table = 'vtiger_timesheets';
$field8->column = 'total_hours';
$field8->columntype = 'INT(19)';
$field8->uitype = 7;
$field8->typeofdata = 'I~O';

$blockInstance->addField($field8);


$field36 = new Vtiger_Field();
$field36->label = 'Assigned To';
$field36->name = 'assigned_user_id';
$field36->table = 'vtiger_crmentity';
$field36->column = 'smownerid';
$field36->uitype = 53;
$field36->typeofdata = 'V~M';

$blockInstance->addField($field36);

$field37 = new Vtiger_Field();
$field37->label = 'Created Time';
$field37->name = 'CreatedTime';
$field37->table = 'vtiger_crmentity';
$field37->column = 'createdtime';
$field37->uitype = 70;
$field37->typeofdata = 'T~O';
$field37->displaytype = 2;

$blockInstance->addField($field37);

$field38 = new Vtiger_Field();
$field38->label = 'Modified Time';
$field38->name = 'ModifiedTime';
$field38->table = 'vtiger_crmentity';
$field38->uitype = 70;
$field38->typeofdata = 'T~O';
$field38->displaytype = 2;

$blockInstance->addField($field38);

$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field5, 4)->addField($field7, 5)->addField($field8, 6);

$moduleInstance->setDefaultSharing();
$moduleInstance->initWebservice();

$employessInstance = Vtiger_Module::getInstance('Employees');
$employessInstance->setRelatedList($moduleInstance, 'LBL_TIME_SHEETS', array('ADD'), 'get_dependents_list');
