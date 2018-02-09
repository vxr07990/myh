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
include_once('modules/ModTracker/ModTracker.php');

$timesheetsInstance = Vtiger_Module::getInstance('TimeSheets');
    if ($timesheetsInstance) {
        echo "<br> module 'TimeSheets' already exists. <br>";
    } else {
        $timesheetsInstance = new Vtiger_Module();
        $timesheetsInstance->name = 'TimeSheets';
        $timesheetsInstance->save();
        $timesheetsInstance->initTables();
        $timesheetsInstance->setDefaultSharing();
        $timesheetsInstance->initWebservice();
        ModTracker::enableTrackingForModule($timesheetsInstance->id);
    }

$timesheetsblockInstance1 = Vtiger_Module::getInstance('LBL_TIMESHEETS_INFORMATION', $timesheetsInstance);
    if ($timesheetsblockInstance1) {
        echo "<br> block 'LBL_TIMESHEETS_INFORMATION' already exists.<br>";
    } else {
        $timesheetsblockInstance1 = new Vtiger_Block();
        $timesheetsblockInstance1->label = 'LBL_TIMESHEETS_INFORMATION';
        $timesheetsInstance->addBlock($timesheetsblockInstance1);
    }

$timesheetsblockInstance2 = Vtiger_Module::getInstance('LBL_CUSTOM_INFORMATION', $timesheetsInstance);
    if ($timesheetsblockInstance2) {
        echo "<br> block 'LBL_CUSTOM_INFORMATION' already exists.<br>";
    } else {
        $timesheetsblockInstance2 = new Vtiger_Block();
        $timesheetsblockInstance2->label = 'LBL_CUSTOM_INFORMATION';
        $timesheetsInstance->addBlock($timesheetsblockInstance2);
    }

//add equipment fields fields
$field1 = Vtiger_Field::getInstance('timesheet_id', $timesheetsInstance);
    if ($field1) {
        echo "<br> Field 'timesheet_id' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_TIMESHEET_ID';
        $field1->name = 'timesheet_id';
        $field1->table = 'vtiger_timesheets';
        $field1->column = 'timesheet_id';
        $field1->columntype = 'VARCHAR(100)';
        $field1->uitype = 4;
        $field1->typeofdata = 'V~M';
        $field1->summaryfield = 1;
    

        $timesheetsblockInstance1->addField($field1);
        
        $timesheetsInstance->setEntityIdentifier($field1);
    }


$field2 = Vtiger_Field::getInstance('assigned_user_id', $timesheetsInstance);
    if ($field2) {
        echo "<br> Field 'assigned_user_id' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'Assigned To';
        $field2->name = 'assigned_user_id';
        $field2->table = 'vtiger_crmentity';
        $field2->column = 'smownerid';
        $field2->uitype = 53;
        $field2->typeofdata = 'V~M';
    

        $timesheetsblockInstance1->addField($field2);
    }

$field3 = Vtiger_Field::getInstance('createdtime', $timesheetsInstance);
    if ($field3) {
        echo "<br> Field 'createdtime' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'Created Time';
        $field3->name = 'createdtime';
        $field3->table = 'vtiger_crmentity';
        $field3->column = 'createdtime';
        $field3->uitype = 70;
        $field3->typeofdata = 'T~O';
        $field3->displaytype =2;
    
        $timesheetsblockInstance2->addField($field3);
    }

$field4 = Vtiger_Field::getInstance('modifiedtime', $timesheetsInstance);
    if ($field4) {
        echo "<br> Field 'modifiedtime' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'Modified Time';
        $field4->name = 'modifiedtime';
        $field4->table = 'vtiger_crmentity';
        $field4->column = 'modifiedtime';
        $field4->uitype = 70;
        $field4->typeofdata = 'T~O';
        $field4->displaytype =2;
    
        $timesheetsblockInstance2->addField($field4);
    }

$field5 = Vtiger_Field::getInstance('employee_id', $timesheetsInstance);
    if ($field5) {
        echo "<br> Field 'accidents_time' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'LBL_EMPLOYEE';
        $field5->name = 'employee_id';
        $field5->table = 'vtiger_timesheets';
        $field5->column = 'employee_id';
        $field5->columntype = 'VARCHAR(100)';
        $field5->uitype = 10;
        $field5->typeofdata = 'V~M';
    
        $timesheetsblockInstance1->addField($field5);
        $field5->setRelatedModules(array('Employees'));
    }

$field6 = Vtiger_Field::getInstance('ordertask_id', $timesheetsInstance);
    if ($field6) {
        echo "<br> Field 'ordertask_id' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_TASK_ID';
        $field6->name = 'ordertask_id';
        $field6->table = 'vtiger_timesheets';
        $field6->column = 'ordertask_id';
        $field6->columntype = 'INT(19)';
        $field6->uitype = 10;
        $field6->typeofdata = 'V~M';
        $field6->summaryfield = 1;
    
        $timesheetsblockInstance1->addField($field6);
        $field6->setRelatedModules(array('OrdersTask'));
    }

$field7 = Vtiger_Field::getInstance('actual_start_date', $timesheetsInstance);
    if ($field7) {
        echo "<br> Field 'actual_start_date' is already present. <br>";
    } else {
        $field7 = new Vtiger_Field();
        $field7->label = 'LBL_ACTUAL_DATE';
        $field7->name = 'actual_start_date';
        $field7->table = 'vtiger_timesheets';
        $field7->column = 'actual_start_date';
        $field7->columntype = 'DATE';
        $field7->uitype = 5;
        $field7->typeofdata = 'D~O';
        $field7->summaryfield = 1;
    
        $timesheetsblockInstance1->addField($field7);
    }

    $field8 = Vtiger_Field::getInstance('actual_start_hour', $timesheetsInstance);
    if ($field8) {
        echo "<br> Field 'actual_start_date' is already present. <br>";
    } else {
        $field8 = new Vtiger_Field();
        $field8->label = 'LBL_ACTUAL_START_HOUR';
        $field8->name = 'actual_start_hour';
        $field8->table = 'vtiger_timesheets';
        $field8->column = 'actual_start_hour';
        $field8->columntype = 'TIME';
        $field8->uitype = 14;
        $field8->typeofdata = 'T~O';
        $field8->summaryfield = 1;
    
        $timesheetsblockInstance1->addField($field8);
    }

    $field9 = Vtiger_Field::getInstance('actual_end_hour', $timesheetsInstance);
    if ($field9) {
        echo "<br> Field 'actual_start_date' is already present. <br>";
    } else {
        $field9 = new Vtiger_Field();
        $field9->label = 'LBL_ACTUAL_END_HOUR';
        $field9->name = 'actual_end_hour';
        $field9->table = 'vtiger_timesheets';
        $field9->column = 'actual_end_hour';
        $field9->columntype = 'TIME';
        $field9->uitype = 14;
        $field9->typeofdata = 'T~O';
        $field9->summaryfield = 1;
    
        $timesheetsblockInstance1->addField($field9);
    }

    $field10 = Vtiger_Field::getInstance('total_hours', $timesheetsInstance);
    if ($field10) {
        echo "<br> Field 'actual_start_date' is already present. <br>";
    } else {
        $field10 = new Vtiger_Field();
        $field10->label = 'LBL_TOTAL_WORKED_HOURS';
        $field10->name = 'total_hours';
        $field10->table = 'vtiger_timesheets';
        $field10->column = 'total_hours';
        $field10->columntype = 'INT(19)';
        $field10->uitype = 7;
        $field10->typeofdata = 'I~O';
        $field10->summaryfield = 1;
    
        $timesheetsblockInstance1->addField($field10);
    }
    //START Add navigation link in module employees to timesheets
    $employeesInstance = Vtiger_Module::getInstance('Employees');
    $employeesInstance->setRelatedList(Vtiger_Module::getInstance('TimeSheets'), 'TimeSheets', array('ADD'), 'get_dependents_list');
    //END Add navigation link in module


    
    //add filter in accidents module
    $filter1 = Vtiger_Filter::getInstance('All', $timesheetsInstance);
    if ($filter1) {
        echo "<br> Filter exists <br>";
    } else {
        $filter1 = new Vtiger_Filter();
        $filter1->name = 'All';
        $filter1->isdefault = true;
        $timesheetsInstance->addFilter($filter1);

        $filter1->addField($field1)->addField($field5, 1);
    }
