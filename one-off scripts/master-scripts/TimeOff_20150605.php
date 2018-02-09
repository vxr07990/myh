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

$timeOffIsNew = false; // flag for filters and relations

//Start TimeOff Module
$module1 = Vtiger_Module::getInstance('TimeOff');
if ($module1) {
    echo "<h2>Updating TimeOff Fields</h2><br>";
} else {
    $module1 = new Vtiger_Module();
    $module1->name = 'TimeOff';
    $module1->save();
    echo "<h2>Creating TimeOff Module and Updating Fields</h2>";
    $module1->initTables();
}

//start block1 : LBL_TIMEOFF_INFORMATION
$block1 = Vtiger_Block::getInstance('LBL_TIMEOFF_INFORMATION', $module1);
if ($block1) {
    echo "<h3> LBL_TIMEOFF_INFORMATION block already exists </h3><br>";
} else {
    $block1 = new Vtiger_Block();
    $block1->label = 'LBL_TIMEOFF_INFORMATION';
    $module1->addBlock($block1);
    $timeOffIsNew = true;
}
echo "<ul>";

$field1 = Vtiger_Field::getInstance('timeoff_date', $module1);
if ($field1) {
    echo "<li>The timeoff_date field already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_TIMEOFF_DATE';
    $field1->name = 'timeoff_date';
    $field1->table = 'vtiger_timeoff';
    $field1->column = 'timeoff_date';
    $field1->columntype = 'DATE';
    $field1->uitype = 5;
    $field1->typeofdata = 'D~O';
    
    $block1->addField($field1);
    
    $module1->setEntityIdentifier($field1);
}

$field3 = Vtiger_Field::getInstance('assigned_user_id', $module1);
if ($field3) {
    echo "<li>The assigned_user_id field already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'Assigned To';
    $field3->name = 'assigned_user_id';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'smownerid';
    $field3->uitype = 53;
    $field3->typeofdata = 'V~M';

    $block1->addField($field3);
}

$field4 = Vtiger_Field::getInstance('CreatedTime', $module1);
if ($field4) {
    echo "<li>The CreatedTime field already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'Created Time';
    $field4->name = 'createdtime';
    $field4->table = 'vtiger_crmentity';
    $field4->column = 'createdtime';
    $field4->uitype = 70;
    $field4->typeofdata = 'T~O';
    $field4->displaytype = 2;
    
    $block1->addField($field4);
}
$field5 = Vtiger_Field::getInstance('ModifiedTime', $module1);
if ($field5) {
    echo "<li>The ModifiedTime field already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'Modified Time';
    $field5->name = 'modifiedtime';
    $field5->table = 'vtiger_crmentity';
    $field5->column = 'modifiedtime';
    $field5->uitype = 70;
    $field5->typeofdata = 'T~O';
    $field5->displaytype = 2;
    
    $block1->addField($field5);
}
$field6 = Vtiger_Field::getInstance('description', $module1);
if ($field6) {
    echo "<li>The description field already exists<li><br>";
} else {
    $field6 = new Vtiger_Field(); // needs to bechanged not saving data
    $field6->label = 'LBL_TIMEOFF_DESCRIPTION';
    $field6->name = 'description';
    $field6->table = 'vtiger_crmentity';  // This is the tablename from your database that the new field will be added to.
    $field6->column = 'description';   //  This will be the columnname in your database for the new field.
    $field6->columntype = 'VARCHAR(255)';
    $field6->uitype = 19; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field6->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $field6->sequence = 7;
    
    $block1->addField($field6);
}
$field7 = Vtiger_Field::getInstance('timeoff_allday', $module1);
if ($field7) {
    echo "<li>The timeoff_allday field already exists<li><br>";
} else {
    $field7 = new Vtiger_Field(); // needs to bechanged not saving data
    $field7->label = 'LBL_TIMEOFF_ALLDAY';
    $field7->name = 'timeoff_allday';
    $field7->table = 'vtiger_timeoff';  // This is the tablename from your database that the new field will be added to.
    $field7->column = 'timeoff_allday';   //  This will be the columnname in your database for the new field.
    $field7->columntype = 'VARCHAR(3)';
    $field7->uitype = 56; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field7->typeofdata = 'C~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $field7->sequence = 2;

    $block1->addField($field7);
}
$field8 = Vtiger_Field::getInstance('timeoff_hourstart', $module1);
if ($field8) {
    echo "<li>The timeoff_hourstart field already exists<li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_TIMEOFF_HOURSTART';
    $field8->name = 'timeoff_hourstart';
    $field8->table = 'vtiger_timeoff';  // This is the tablename from your database that the new field will be added to.
    $field8->column = 'timeoff_hourstart';   //  This will be the columnname in your database for the new field.
    $field8->columntype = 'TIME';
    $field8->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field8->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $field8->sequence = 3;

    $block1->addField($field8);
}
$field9 = Vtiger_Field::getInstance('timeoff_hoursend', $module1);
if ($field9) {
    echo "<li>The timeoff_hoursend field already exists<li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_TIMEOFF_HOURSEND';
    $field9->name = 'timeoff_hoursend';
    $field9->table = 'vtiger_timeoff';  // This is the tablename from your database that the new field will be added to.
    $field9->column = 'timeoff_hoursend';   //  This will be the columnname in your database for the new field.
    $field9->columntype = 'TIME';
    $field9->uitype = 14; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field9->typeofdata = 'T~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $field9->sequence = 4;

    $block1->addField($field9);
}
$field10 = Vtiger_Field::getInstance('timeoff_reason', $module1);
if ($field10) {
    echo "<li>The timeoff_reason field already exists<li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_TIMEOFF_REASON';
    $field10->name = 'timeoff_reason';
    $field10->table = 'vtiger_timeoff';  // This is the tablename from your database that the new field will be added to.
    $field10->column = 'timeoff_reason';   //  This will be the columnname in your database for the new field.
    $field10->columntype = 'VARCHAR(255)';
    $field10->uitype = 16; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field10->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $field10->sequence =5;

    $block1->addField($field10);
    $field10->setPicklistValues(array('Appointment', 'Sick', 'Time Off'));
}
$field11 = Vtiger_Field::getInstance('timeoff_employees', $module1);
if ($field11) {
    echo "<li>The timeoff_employees field already exists<li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_TIMEOFF_EMPLOYEES';
    $field11->name = 'timeoff_employees';
    $field11->table = 'vtiger_timeoff';  // This is the tablename from your database that the new field will be added to.
    $field11->column = 'timeoff_employees';   //  This will be the columnname in your database for the new field.
    $field11->columntype = 'VARCHAR(100)';
    $field11->uitype = 10; // FIND uitype here: https://wiki.vtiger.com/index.php/UI_Types
    $field11->typeofdata = 'V~O'; // Find Type of data here: https://wiki.vtiger.com/index.php/TypeOfData
    $field11->sequence = 6;

    $block1->addField($field11);
    $field11->setRelatedModules(array('Employees'));
}
//end block1 fields
echo "</ul>";
$block1->save($module1);
//end block1 : LBL_TIMEOFF_INFORMATION

if ($timeOffIsNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $module1->addFilter($filter1);

    $filter1->addField($field1);

    $module1->setDefaultSharing();
    $module1->initWebservice();
    
    /*$module2 = Vtiger_Module::getInstance('Contractors');
    if($module2) {
        $module2->setRelatedList(Vtiger_Module::getInstance('TimeOff'), 'Time Off',Array('ADD','SELECT'),'get_related_list');
        echo "<h3>Set Related List of Contractors -> TimeOff</h3>";
    }
    else {
        echo "<h3>Could not set Related List of Contractors -> TimeOff, as Contractors does not exist</h3>";
    }*/
    $module3 = Vtiger_Module::getInstance('Employees');
    if ($module3) {
        $module3->setRelatedList(Vtiger_Module::getInstance('TimeOff'), 'Time Off', array('ADD', 'SELECT'), 'get_related_list');
        echo "<h3>Set Related List of Employees -> TimeOff</h3>";
    } else {
        echo "<h3>Could not set Related List of Employees -> TimeOff, as Employees does not exist</h3>";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";