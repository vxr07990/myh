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



//A_Create_WeeklyTimeSheets

//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

$moduleInstance = Vtiger_Module::getInstance('WeeklyTimeSheets');
if ($moduleInstance) {
    echo "<h2>Updating WeeklyTimeSheets Fields</h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'WeeklyTimeSheets';
    $moduleInstance->save();
    echo "<h2>Creating Module WeeklyTimeSheets and Updating Fields</h2><br>";
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
}

$blockInstance = Vtiger_Block::getInstance('LBL_WEEKLYTIMESHEETS_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<h3>The LBL_WEEKLYTIMESHEETS_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_WEEKLYTIMESHEETS_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if ($blockInstance2) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

$field1 = Vtiger_Field::getInstance('weeklytimesheet_id', $moduleInstance);
if ($field1) {
    echo "<li> the weeklytimesheet_id already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_WEEKLY_TIMESHEET_ID';
    $field1->name = 'weeklytimesheet_id';
    $field1->table = 'vtiger_weeklytimesheets';
    $field1->column = 'weeklytimesheet_id';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 4;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = 1;

    $blockInstance->addField($field1);

    $moduleInstance->setEntityIdentifier($field1);
    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $moduleInstance->name, 'WTS', 1);
}

$field2 = Vtiger_Field::getInstance('employee_id', $moduleInstance);
if ($field2) {
    echo "<li> the employee_id already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_EMPLOYEE';
    $field2->name = 'employee_id';
    $field2->table = 'vtiger_weeklytimesheets';
    $field2->column = 'employee_id';
    $field2->columntype = 'INT(19)';
    $field2->uitype = 10;
    $field2->typeofdata = 'V~M';
    $field2->summaryfield = 1;

    $blockInstance->addField($field2);

    $field2->setRelatedModules(array('Employees'));
}

$field4 = Vtiger_Field::getInstance('week_start_date', $moduleInstance);
if ($field4) {
    echo "<li> the week_start_date already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_WEEK_START_DATE';
    $field4->name = 'week_start_date';
    $field4->table = 'vtiger_weeklytimesheets';
    $field4->column = 'week_start_date';
    $field4->columntype = 'DATE';
    $field4->uitype = 5;
    $field4->typeofdata = 'D~O';
    $field4->summaryfield = 1;

    $blockInstance->addField($field4);
}

$field8 = Vtiger_Field::getInstance('monday_hours', $moduleInstance);
if ($field8) {
    echo "<li> the monday_hours already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_MONDAY_HOURS';
    $field8->name = 'monday_hours';
    $field8->table = 'vtiger_weeklytimesheets';
    $field8->column = 'monday_hours';
    $field8->columntype = 'decimal(3,2)';
    $field8->uitype = 7;
    $field8->typeofdata = 'I~O';

    $blockInstance->addField($field8);
}


$field9 = Vtiger_Field::getInstance('tuesday_hours', $moduleInstance);
if ($field9) {
    echo "<li> the tuesday_hours already exists</li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_TUESDAY_HOURS';
    $field9->name = 'tuesday_hours';
    $field9->table = 'vtiger_weeklytimesheets';
    $field9->column = 'tuesday_hours';
    $field9->columntype = 'decimal(3,2)';
    $field9->uitype = 7;
    $field9->typeofdata = 'I~O';

    $blockInstance->addField($field9);
}

$field10 = Vtiger_Field::getInstance('wednesday_hours', $moduleInstance);
if ($field10) {
    echo "<li> the wednesday_hours already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_WEDNESDAY_HOURS';
    $field10->name = 'wednesday_hours';
    $field10->table = 'vtiger_weeklytimesheets';
    $field10->column = 'wednesday_hours';
    $field10->columntype = 'decimal(3,2)';
    $field10->uitype = 7;
    $field10->typeofdata = 'I~O';

    $blockInstance->addField($field10);
}

$field11 = Vtiger_Field::getInstance('thursday_hours', $moduleInstance);
if ($field11) {
    echo "<li> the thursday_hours already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_THURSDAY_HOURS';
    $field11->name = 'thursday_hours';
    $field11->table = 'vtiger_weeklytimesheets';
    $field11->column = 'thursday_hours';
    $field11->columntype = 'decimal(3,2)';
    $field11->uitype = 7;
    $field11->typeofdata = 'I~O';

    $blockInstance->addField($field11);
}

$field12 = Vtiger_Field::getInstance('friday_hours', $moduleInstance);
if ($field12) {
    echo "<li> the friday_hours already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_FRIDAY_HOURS';
    $field12->name = 'friday_hours';
    $field12->table = 'vtiger_weeklytimesheets';
    $field12->column = 'friday_hours';
    $field12->columntype = 'decimal(3,2)';
    $field12->uitype = 7;
    $field12->typeofdata = 'I~O';

    $blockInstance->addField($field12);
}

$field13 = Vtiger_Field::getInstance('saturday_hours', $moduleInstance);
if ($field13) {
    echo "<li> the saturday_hours already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_SATURDAY_HOURS';
    $field13->name = 'saturday_hours';
    $field13->table = 'vtiger_weeklytimesheets';
    $field13->column = 'saturday_hours';
    $field13->columntype = 'decimal(3,2)';
    $field13->uitype = 7;
    $field13->typeofdata = 'I~O';

    $blockInstance->addField($field13);
}

$field14 = Vtiger_Field::getInstance('sunday_hours', $moduleInstance);
if ($field14) {
    echo "<li> the sunday_hours already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_SUNDAY_HOURS';
    $field14->name = 'sunday_hours';
    $field14->table = 'vtiger_weeklytimesheets';
    $field14->column = 'sunday_hours';
    $field14->columntype = 'decimal(3,2)';
    $field14->uitype = 7;
    $field14->typeofdata = 'I~O';

    $blockInstance->addField($field14);
}

$field36 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field36) {
    echo "<li> the assigned_user_id already exists</li><br>";
} else {
    $field36 = new Vtiger_Field();
    $field36->label = 'Assigned To';
    $field36->name = 'assigned_user_id';
    $field36->table = 'vtiger_crmentity';
    $field36->column = 'smownerid';
    $field36->uitype = 53;
    $field36->typeofdata = 'V~M';

    $blockInstance->addField($field36);
}

$field37 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field37) {
    echo "<li> the createdtime already exists</li><br>";
} else {
    $field37 = new Vtiger_Field();
    $field37->label = 'Created Time';
    $field37->name = 'createdtime';
    $field37->table = 'vtiger_crmentity';
    $field37->column = 'createdtime';
    $field37->uitype = 70;
    $field37->typeofdata = 'T~O';
    $field37->displaytype = 2;

    $blockInstance->addField($field37);
}

$field38 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if ($field38) {
    echo "<li> the modifiedtime already exists</li><br>";
} else {
    $field38 = new Vtiger_Field();
    $field38->label = 'Modified Time';
    $field38->name = 'modifiedtime';
    $field38->table = 'vtiger_crmentity';
    $field38->column = 'modifiedtime';
    $field38->uitype = 70;
    $field38->typeofdata = 'T~O';
    $field38->displaytype = 2;

    $blockInstance->addField($field38);
}

$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field2, 1)->addField($field4, 2);

$moduleInstance->setDefaultSharing();
$moduleInstance->initWebservice();

$employessInstance = Vtiger_Module::getInstance('Employees');
$employessInstance->setRelatedList($moduleInstance, 'LBL_WEEKLY_TIME_SHEETS', array('ADD'), 'get_dependents_list');

// ----- Adding a new workflow --- //

global $adb;

require_once 'include/utils/utils.php';
require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
$emm = new VTEntityMethodManager($adb);

//$emm->addEntityMethod("Module Name","Label", "Path to file" , "Method Name" );
$emm->addEntityMethod("TimeSheets", "SaveWTS", "modules/WeeklyTimeSheets/handlers/WeeklyTS_Workflow.php", "saveWeeklyTS");

// ---- Adding a new table --- //

$adb->query('CREATE TABLE IF NOT EXISTS `vtiger_weeklytimesheets_wts` (
 `wtsid` int(11) NOT NULL,
 `timesheetid` int(11) NOT NULL,
 `hours` decimal(3,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

// -- Done -- //




print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";