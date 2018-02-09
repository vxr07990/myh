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



//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
//include_once('modules/ModTracker/ModTracker.php');

$equipmentInstance = Vtiger_Module::getInstance('Equipment');
    if ($equipmentInstance) {
        echo "<br> module 'Equipment' already exists. <br>";
    } else {
        $equipmentInstance = new Vtiger_Module();
        $equipmentInstance->name = 'Equipment';
        $equipmentInstance->save();
        $equipmentInstance->initTables();
        $equipmentInstance->setDefaultSharing();
        $equipmentInstance->initWebservice();
        ModTracker::enableTrackingForModule($equipmentInstance->id);
    }

$equipmentblockInstance1 = Vtiger_Module::getInstance('LBL_EQUIPMENT_INFORMATION', $equipmentInstance);
    if ($equipmentblockInstance1) {
        echo "<br> block 'LBL_EQUIPMENT_INFORMATION' already exists.<br>";
    } else {
        $equipmentblockInstance1 = new Vtiger_Block();
        $equipmentblockInstance1->label = 'LBL_EQUIPMENT_INFORMATION';
        $equipmentInstance->addBlock($equipmentblockInstance1);
    }

$equipmentblockInstance2 = Vtiger_Module::getInstance('LBL_CUSTOM_INFORMATION', $equipmentInstance);
    if ($equipmentblockInstance2) {
        echo "<br> block 'LBL_CUSTOM_INFORMATION' already exists.<br>";
    } else {
        $equipmentblockInstance2 = new Vtiger_Block();
        $equipmentblockInstance2->label = 'LBL_CUSTOM_INFORMATION';
        $equipmentInstance->addBlock($equipmentblockInstance2);
    }

//add equipment fields fields
$field1 = Vtiger_Field::getInstance('name', $equipmentInstance);
    if ($field1) {
        echo "<br> Field 'name' is already present. <br>";
    } else {
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_EQUIPMENT_NAME';
        $field1->name = 'name';
        $field1->table = 'vtiger_equipment';
        $field1->column = 'name';
        $field1->columntype = 'VARCHAR(255)';
        $field1->uitype = 2;
        $field1->typeofdata = 'V~M';
        $field1->quickcreate = 2;

        $equipmentblockInstance1->addField($field1);
        
        $equipmentInstance->setEntityIdentifier($field1);
    }


$field2 = Vtiger_Field::getInstance('assigned_user_id', $equipmentInstance);
    if ($field2) {
        echo "<br> Field 'assigned_user_id' is already present. <br>";
    } else {
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_EQUIPMENT_ASSIGNEDTO';
        $field2->name = 'assigned_user_id';
        $field2->table = 'vtiger_crmentity';
        $field2->column = 'smownerid';
        $field2->uitype = 53;
        $field2->typeofdata = 'V~M';
        $field2->displaytype =2;
        $field2->quickcreate = 1;

        $equipmentblockInstance1->addField($field2);
    }

$field3 = Vtiger_Field::getInstance('createdtime', $equipmentInstance);
    if ($field3) {
        echo "<br> Field 'createdtime' is already present. <br>";
    } else {
        $field3 = new Vtiger_Field();
        $field3->label = 'LBL_EQUIPMENT_CREATEDTIME';
        $field3->name = 'createdtime';
        $field3->table = 'vtiger_crmentity';
        $field3->column = 'createdtime';
        $field3->uitype = 70;
        $field3->typeofdata = 'T~O';
        $field3->displaytype =2;
    
        $equipmentblockInstance2->addField($field3);
    }

$field4 = Vtiger_Field::getInstance('modifiedtime', $equipmentInstance);
    if ($field4) {
        echo "<br> Field 'modifiedtime' is already present. <br>";
    } else {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_EQUIPMENT_MODIFIEDTIME';
        $field4->name = 'modifiedtime';
        $field4->table = 'vtiger_crmentity';
        $field4->column = 'modifiedtime';
        $field4->uitype = 70;
        $field4->typeofdata = 'T~O';
        $field4->displaytype =2;
    
        $equipmentblockInstance2->addField($field4);
    }

$field5 = Vtiger_Field::getInstance('quantity', $equipmentInstance);
    if ($field5) {
        echo "<br> Field 'quantity' is already present. <br>";
    } else {
        $field5 = new Vtiger_Field();
        $field5->label = 'LBL_EQUIPMENT_QUANTITY';
        $field5->name = 'quantity';
        $field5->table = 'vtiger_equipment';
        $field5->column = 'quantity';
        $field5->columntype = 'INT(10)';
        $field5->uitype = 7;
        $field5->typeofdata = 'I~O';
    
        $equipmentblockInstance1->addField($field5);
    }

$field6 = Vtiger_Field::getInstance('date_out', $equipmentInstance);
    if ($field6) {
        echo "<br> Field 'date_out' is already present. <br>";
    } else {
        $field6 = new Vtiger_Field();
        $field6->label = 'LBL_EQUIPMENT_DATEOUT';
        $field6->name = 'date_out';
        $field6->table = 'vtiger_equipment';
        $field6->column = 'date_out';
        $field6->columntype = 'DATE';
        $field6->uitype = 5;
        $field6->typeofdata = 'D~O';
    
        $equipmentblockInstance1->addField($field6);
    }

$field7 = Vtiger_Field::getInstance('time_out', $equipmentInstance);
    if ($field7) {
        echo "<br> Field 'time_out' is already present. <br>";
    } else {
        $field7 = new Vtiger_Field();
        $field7->label = 'LBL_EQUIPMENT_TIMEOUT';
        $field7->name = 'time_out';
        $field7->table = 'vtiger_equipment';
        $field7->column = 'time_out';
        $field7->columntype = 'TIME';
        $field7->uitype = 14;
        $field7->typeofdata = 'T~O';
    
        $equipmentblockInstance1->addField($field7);
    }

$field8 = Vtiger_Field::getInstance('date_in', $equipmentInstance);
    if ($field8) {
        echo "<br> Field 'date_in' is already present. <br>";
    } else {
        $field8 = new Vtiger_Field();
        $field8->label = 'LBL_EQUIPMENT_DATEIN';
        $field8->name = 'date_in';
        $field8->table = 'vtiger_equipment';
        $field8->column = 'date_in';
        $field8->columntype = 'DATE';
        $field8->uitype = 5;
        $field8->typeofdata = 'D~O';
    
        $equipmentblockInstance1->addField($field8);
    }

$field9 = Vtiger_Field::getInstance('time_in', $equipmentInstance);
    if ($field9) {
        echo "<br> Field 'time_in' is already present. <br>";
    } else {
        $field9 = new Vtiger_Field();
        $field9->label = 'LBL_EQUIPMENT_TIMEIN';
        $field9->name = 'time_in';
        $field9->table = 'vtiger_equipment';
        $field9->column = 'time_in';
        $field9->columntype = 'TIME';
        $field9->uitype = 14;
        $field9->typeofdata = 'T~O';
    
        $equipmentblockInstance1->addField($field9);
    }


$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$equipmentInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field2, 1);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";