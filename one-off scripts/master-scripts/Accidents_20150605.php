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



/* $Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php'); */

$accidentsInstance = Vtiger_Module::getInstance('Accidents');
    if ($accidentsInstance) {
        echo "<br> module 'Accidents' already exists. <br>";
    } else {
        $accidentsInstance = new Vtiger_Module();
        $accidentsInstance->name = 'Accidents';
        $accidentsInstance->save();
        $accidentsInstance->initTables();
        $accidentsInstance->setDefaultSharing();
        $accidentsInstance->initWebservice();
        ModTracker::enableTrackingForModule($accidentsInstance->id);
    }

$accidentsblockInstance1 = Vtiger_Block::getInstance('LBL_ACCIDENTS_INFORMATION', $accidentsInstance);
    if ($accidentsblockInstance1) {
        echo "<br> block 'LBL_ACCIDENTS_INFORMATION' already exists.<br>";
    } else {
        $accidentsblockInstance1 = new Vtiger_Block();
        $accidentsblockInstance1->label = 'LBL_ACCIDENTS_INFORMATION';
        $accidentsInstance->addBlock($accidentsblockInstance1);
    }

$accidentsblockInstance2 = Vtiger_Block::getInstance('LBL_ACCIDENTS_RECORDUPDATE', $accidentsInstance);
    if ($accidentsblockInstance2) {
        echo "<br> block 'LBL_ACCIDENTS_RECORDUPDATE' already exists.<br>";
    } else {
        $accidentsblockInstance2 = new Vtiger_Block();
        $accidentsblockInstance2->label = 'LBL_ACCIDENTS_RECORDUPDATE';
        $accidentsInstance->addBlock($accidentsblockInstance2);
    }

//add equipment fields fields
$field1 = Vtiger_Field::getInstance('accidents_date', $accidentsInstance);
    if ($field1) {
        echo "<br> Field 'accidents_date' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_ACCIDENTS_DATE';
        $field1->name = 'accidents_date';
        $field1->table = 'vtiger_accidents';
        $field1->column = 'accidents_date';
        $field1->columntype = 'DATE';
        $field1->uitype = 5;
        $field1->typeofdata = 'D~M';
    

        $accidentsblockInstance1->addField($field1);
        
        $accidentsInstance->setEntityIdentifier($field1);
    }


$field2 = Vtiger_Field::getInstance('assigned_user_id', $accidentsInstance);
    if ($field2) {
        echo "<br> Field 'assigned_user_id' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_ACCIDENTS_ASSIGNEDTO';
        $field2->name = 'assigned_user_id';
        $field2->table = 'vtiger_crmentity';
        $field2->column = 'smownerid';
        $field2->uitype = 53;
        $field2->typeofdata = 'V~M';
        $field2->displaytype =2;

        $accidentsblockInstance1->addField($field2);
    }

$field3 = Vtiger_Field::getInstance('createdtime', $accidentsInstance);
    if ($field3) {
        echo "<br> Field 'createdtime' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'LBL_ACCIDENTS_CREATEDTIME';
        $field3->name = 'createdtime';
        $field3->table = 'vtiger_crmentity';
        $field3->column = 'createdtime';
        $field3->uitype = 70;
        $field3->typeofdata = 'T~O';
        $field3->displaytype =2;
    
        $accidentsblockInstance2->addField($field3);
    }

$field4 = Vtiger_Field::getInstance('modifiedtime', $accidentsInstance);
    if ($field4) {
        echo "<br> Field 'modifiedtime' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_ACCIDENTS_MODIFIEDTIME';
        $field4->name = 'modifiedtime';
        $field4->table = 'vtiger_crmentity';
        $field4->column = 'modifiedtime';
        $field4->uitype = 70;
        $field4->typeofdata = 'T~O';
        $field4->displaytype =2;
    
        $accidentsblockInstance2->addField($field4);
    }

$field5 = Vtiger_Field::getInstance('accidents_time', $accidentsInstance);
    if ($field5) {
        echo "<br> Field 'accidents_time' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'LBL_ACCIDENTS_TIME';
        $field5->name = 'accidents_time';
        $field5->table = 'vtiger_accidents';
        $field5->column = 'accidents_time';
        $field5->columntype = 'TIME';
        $field5->uitype = 14;
        $field5->typeofdata = 'T~O';
    
        $accidentsblockInstance1->addField($field5);
    }

$field6 = Vtiger_Field::getInstance('accidents_employees', $accidentsInstance);
    if ($field6) {
        echo "<br> Field 'accidents_employees' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_ACCIDENTS_EMPLOYEES';
        $field6->name = 'accidents_employees';
        $field6->table = 'vtiger_accidents';
        $field6->column = 'accidents_employees';
        $field6->columntype = 'VARCHAR(100)';
        $field6->uitype = 10;
        $field6->typeofdata = 'V~O';
    
        $accidentsblockInstance1->addField($field6);
        $field6->setRelatedModules(array('Employees'));
    }

$field7 = Vtiger_Field::getInstance('description', $accidentsInstance);
    if ($field7) {
        echo "<br> Field 'description' is already present. <br>";
    } else {
        $field7 = new Vtiger_Field();
        $field7->label = 'LBL_ACCIDENTS_DESCRIPTION';
        $field7->name = 'description';
        $field7->table = 'vtiger_crmentity';
        $field7->column = 'description';
        $field7->columntype = 'TEXT';
        $field7->uitype = 19;
        $field7->typeofdata = 'V~O';
    
        $accidentsblockInstance1->addField($field7);
    }
    //START Add navigation link in module employees to accidents
    $employeesInstance = Vtiger_Module::getInstance('Employees');
    $employeesInstance->setRelatedList(Vtiger_Module::getInstance('Accidents'), 'Accidents', array('ADD'), 'get_related_list');
    //END Add navigation link in module


    
    //add filter in accidents module
    $filter1 = Vtiger_Filter::getInstance('All', $accidentsInstance);
    if ($filter1) {
        echo "<br> Filter exists <br>";
    } else {
        $filter1 = new Vtiger_Filter();
        $filter1->name = 'All';
        $filter1->isdefault = true;
        $accidentsInstance->addFilter($filter1);

        $filter1->addField($field1)->addField($field5, 1);
    }


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";