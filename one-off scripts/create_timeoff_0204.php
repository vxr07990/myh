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
$moduleInstance->name = 'TimeOff';
$moduleInstance->save();

$moduleInstance->initTables();

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_TIMEOFF_INFORMATION';
$moduleInstance->addBlock($blockInstance);


$field1 = new Vtiger_Field();
$field1->label = 'LBL_TIMEOFF_DATE';
$field1->name = 'timeoff_date';
$field1->table = 'vtiger_timeoff';
$field1->column = 'timeoff_date';
$field1->columntype = 'DATE';
$field1->uitype = 5;
$field1->typeofdata = 'D~O';

$blockInstance->addField($field1);
    
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

$field6 = new Vtiger_Field(); // needs to bechanged not saving data
$field6->label = 'LBL_TIMEOFF_DESCRIPTION';
$field6->name = 'description';
$field6->table = 'vtiger_crmentity';  // This is the tablename from your database that the new field will be added to.
$field6->column = 'description';   //  This will be the columnname in your database for the new field.
$field6->columntype = 'VARCHAR(255)';
$field6->uitype = 19; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field6);

$field7 = new Vtiger_Field(); // needs to bechanged not saving data
$field7->label = 'LBL_TIMEOFF_ALLDAY';
$field7->name = 'timeoff_allday';
$field7->table = 'vtiger_timeoff';  // This is the tablename from your database that the new field will be added to.
$field7->column = 'timeoff_allday';   //  This will be the columnname in your database for the new field.
$field7->columntype = 'VARCHAR(3)';
$field7->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field7->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field7);

$field8 = new Vtiger_Field();
$field8->label = 'LBL_TIMEOFF_HOURSTART';
$field8->name = 'timeoff_hourstart';
$field8->table = 'vtiger_timeoff';  // This is the tablename from your database that the new field will be added to.
$field8->column = 'timeoff_hourstart';   //  This will be the columnname in your database for the new field.
$field8->columntype = 'TIME';
$field8->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field8->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_TIMEOFF_HOURSEND';
$field9->name = 'timeoff_hoursend';
$field9->table = 'vtiger_timeoff';  // This is the tablename from your database that the new field will be added to.
$field9->column = 'timeoff_hoursend';   //  This will be the columnname in your database for the new field.
$field9->columntype = 'TIME';
$field9->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field9->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field9);

$field10 = new Vtiger_Field();
$field10->label = 'LBL_TIMEOFF_REASON';
$field10->name = 'timeoff_reason';
$field10->table = 'vtiger_timeoff';  // This is the tablename from your database that the new field will be added to.
$field10->column = 'timeoff_reason';   //  This will be the columnname in your database for the new field.
$field10->columntype = 'VARCHAR(255)';
$field10->uitype = 15; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
$field10->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData

$blockInstance->addField($field10);
$field10->setPicklistValues(array('Appointment', 'Sick', 'Time Off'));


$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1);

$moduleInstance->setDefaultSharing();

$moduleInstance->initWebservice();
//START Add navigation link in module
;
