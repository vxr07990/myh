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
$moduleInstance->name = 'ServiceHours';
$moduleInstance->save();

$moduleInstance->initTables();


$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_SERVICEHOURS_INFORMATION';
$moduleInstance->addBlock($blockInstance);

$blockInstance2 = new Vtiger_Block();
$blockInstance2->label = 'LBL_CUSTOM_INFORMATION';
$moduleInstance->addBlock($blockInstance2);

$field1 = new Vtiger_Field();
$field1->label = 'LBL_SERVICEHOURS_ID';
$field1->name = 'servhours_id';
$field1->table = 'vtiger_servicehours';
$field1->column = 'servhours_id';
$field1->columntype = 'VARCHAR(100)';
$field1->uitype = 4;
$field1->typeofdata = 'V~M';

$blockInstance->addField($field1);

$moduleInstance->setEntityIdentifier($field1);

$field2 = new Vtiger_Field();
$field2->label = 'LBL_EMPLOYEE';
$field2->name = 'employee_id';
$field2->table = 'vtiger_servicehours';
$field2->column = 'employee_id';
$field2->columntype = 'INT(19)';
$field2->uitype = 10;
$field2->typeofdata = 'V~M';

$blockInstance->addField($field2);

$field2->setRelatedModules(array('Employees'));

$field3 = new Vtiger_Field();
$field3->label = 'LBL_TRIP_ID';
$field3->name = 'trips_id';
$field3->table = 'vtiger_servicehours';
$field3->column = 'trips_id';
$field3->columntype = 'INT(19)';
$field3->uitype = 10;
$field3->typeofdata = 'D~M';

$blockInstance->addField($field3);

$field3->setRelatedModules(array('Trips'));

$field4 = new Vtiger_Field();
$field4->label = 'LBL_ACTUAL_DATE';
$field4->name = 'actual_start_date';
$field4->table = 'vtiger_servicehours';
$field4->column = 'actual_start_date';
$field4->columntype = 'DATE';
$field4->uitype = 5;
$field4->typeofdata = 'D~O';

$blockInstance->addField($field4);


$field8 = new Vtiger_Field();
$field8->label = 'LBL_TOTAL_WORKED_HOURS';
$field8->name = 'total_hours';
$field8->table = 'vtiger_servicehours';
$field8->column = 'total_hours';
$field8->columntype = 'DECIMAL(5,2)';
$field8->uitype = 7;
$field8->typeofdata = 'NN~O';

$blockInstance->addField($field8);

$field9 = new Vtiger_Field();
$field9->label = 'LBL_DRIVER_MESSAGE';
$field9->name = 'driver_message';
$field9->table = 'vtiger_servicehours';
$field9->column = 'driver_message';
$field9->columntype = 'TEXT';
$field9->uitype = 19;
$field9->typeofdata = 'V~O';

$blockInstance->addField($field9);


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

$filter1->addField($field1)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field8, 4)->addField($field9, 5);

$moduleInstance->setDefaultSharing();
$moduleInstance->initWebservice();

$tripsInstance = Vtiger_Module::getInstance('Trips');
$tripsInstance->setRelatedList($moduleInstance, 'LBL_SERVICE_HOURS', array('ADD'), 'get_dependents_list');


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";