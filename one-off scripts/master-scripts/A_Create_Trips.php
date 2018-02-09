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



// A_Create_Trips.php

//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

$moduleInstance = Vtiger_Module::getInstance('Trips');
if ($moduleInstance) {
    echo "<h2>Updating Trips Fields</h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'Trips';
    $moduleInstance->save();
    echo "<h2>Creating Module Trips and Updating Fields</h2><br>";
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
}

//start blockInstance : LBL_TRIPS_INFORMATION
$blockInstance = Vtiger_Block::getInstance('LBL_TRIPS_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<h3>The LBL_TRIPS_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TRIPS_INFORMATION';
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

$field1 = Vtiger_Field::getInstance('trips_id', $moduleInstance);
if ($field1) {
    echo "<li> the trips_id already exists</li><br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_TRIPS_ID';
    $field1->name = 'trips_id';
    $field1->table = 'vtiger_trips';
    $field1->column = 'trips_id';
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 4;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = 1;

    $blockInstance->addField($field1);
    $moduleInstance->setEntityIdentifier($field1);
    $entity = new CRMEntity();
    $entity->setModuleSeqNumber('configure', $moduleInstance->name, 'TR', 1);
}

$field2 = Vtiger_Field::getInstance('intransitzone', $moduleInstance);
if ($field2) {
    echo "<li> the intransitzone already exists</li><br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_IN_TRANSIT_ZONE';
    $field2->name = 'intransitzone';
    $field2->table = 'vtiger_trips';
    $field2->column = 'intransitzone';
    $field2->columntype = 'VARCHAR(255)';
    $field2->uitype = 16;
    $field2->typeofdata = 'V~O';

    $blockInstance->addField($field2);
    $field2->setPicklistvalues(array('None'));
}

$field3 = Vtiger_Field::getInstance('origin_zone', $moduleInstance);
if ($field3) {
    echo "<li> the origin_zone already exists</li><br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_ORIGIN_ZONE';
    $field3->name = 'origin_zone';
    $field3->table = 'vtiger_trips';
    $field3->column = 'origin_zone';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 16;
    $field3->typeofdata = 'V~O';

    $blockInstance->addField($field3);
    $field3->setPicklistvalues(array('None'));
}

$field4 = Vtiger_Field::getInstance('origin_state', $moduleInstance);
if ($field4) {
    echo "<li> the origin_state already exists</li><br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_ORIGIN_STATE';
    $field4->name = 'origin_state';
    $field4->table = 'vtiger_trips';
    $field4->column = 'origin_state';
    $field4->columntype = 'VARCHAR(255)';
    $field4->uitype = 16;
    $field4->typeofdata = 'V~O';

    $blockInstance->addField($field4);
    $field4->setPicklistvalues(array('AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'));
}

$field5 = Vtiger_Field::getInstance('empty_zone', $moduleInstance);
if ($field5) {
    echo "<li> the empty_zone already exists</li><br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_EMPTY_ZONE';
    $field5->name = 'empty_zone';
    $field5->table = 'vtiger_trips';
    $field5->column = 'empty_zone';
    $field5->columntype = 'VARCHAR(255)';
    $field5->uitype = 16;
    $field5->typeofdata = 'V~O';

    $blockInstance->addField($field5);
    $field5->setPicklistvalues(array('None'));
}


$field6 = Vtiger_Field::getInstance('empty_state', $moduleInstance);
if ($field6) {
    echo "<li> the empty_state already exists</li><br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EMPTY_STATE';
    $field6->name = 'empty_state';
    $field6->table = 'vtiger_trips';
    $field6->column = 'empty_state';
    $field6->columntype = 'VARCHAR(255)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~O';

    $blockInstance->addField($field6);
    $field6->setPicklistvalues(array('AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY'));
}

$field7 = Vtiger_Field::getInstance('empty_date', $moduleInstance);
if ($field7) {
    echo "<li> the empty_date already exists</li><br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_EMPTY_DATE';
    $field7->name = 'empty_date';
    $field7->table = 'vtiger_trips';
    $field7->column = 'empty_date';
    $field7->columntype = 'DATE';
    $field7->uitype = 5;
    $field7->typeofdata = 'D~O';

    $blockInstance->addField($field7);
}

$field8 = Vtiger_Field::getInstance('agent_unit', $moduleInstance);
if ($field8) {
    echo "<li> the agent_unit already exists</li><br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_AGENT_UNIT';
    $field8->name = 'agent_unit';
    $field8->table = 'vtiger_trips';
    $field8->column = 'agent_unit';
    $field8->columntype = 'INT(19)';
    $field8->uitype = 10;
    $field8->typeofdata = 'V~M';

    $blockInstance->addField($field8);

    $field8->setRelatedModules(array('Agents'));
}

$field9 = Vtiger_Field::getInstance('planning_notes', $moduleInstance);
if ($field9) {
    echo "<li> the planning_notes already exists</li><br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_PLANNING_NOTES';
    $field9->name = 'planning_notes';
    $field9->table = 'vtiger_trips';
    $field9->column = 'planning_notes';
    $field9->columntype = 'text';
    $field9->uitype = 19;
    $field9->typeofdata = 'V~O';

    $blockInstance->addField($field9);
}

$field10 = Vtiger_Field::getInstance('dispatch_notes', $moduleInstance);
if ($field10) {
    echo "<li> the dispatch_notes already exists</li><br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_DISPATCH_NOTES';
    $field10->name = 'dispatch_notes';
    $field10->table = 'vtiger_trips';
    $field10->column = 'dispatch_notes';
    $field10->columntype = 'text';
    $field10->uitype = 19;
    $field10->typeofdata = 'V~O';

    $blockInstance->addField($field10);
}

$field11 = Vtiger_Field::getInstance('driver_id', $moduleInstance);
if ($field11) {
    echo "<li> the driver_id already exists</li><br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_DRIVER_ID';
    $field11->name = 'driver_id';
    $field11->table = 'vtiger_trips';
    $field11->column = 'driver_id';
    $field11->columntype = 'INT(19)';
    $field11->uitype = 10;
    $field11->typeofdata = 'V~M';

    $blockInstance->addField($field11);

    $field11->setRelatedModules(array('Employees'));
}

$field12 = Vtiger_Field::getInstance('total_line_haul', $moduleInstance);
if ($field12) {
    echo "<li> the total_line_haul already exists</li><br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_TOTAL_LINE_HAUL';
    $field12->name = 'total_line_haul';
    $field12->table = 'vtiger_trips';
    $field12->column = 'total_line_haul';
    $field12->columntype = 'VARCHAR(225)';
    $field12->uitype = 2;
    $field12->typeofdata = 'V~O';

    $blockInstance->addField($field12);
}

$field13 = Vtiger_Field::getInstance('total_weight', $moduleInstance);
if ($field13) {
    echo "<li> the total_weight already exists</li><br>";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_TOTAL_WEIGHT';
    $field13->name = 'total_weight';
    $field13->table = 'vtiger_trips';
    $field13->column = 'total_weight';
    $field13->columntype = 'VARCHAR(225)';
    $field13->uitype = 2;
    $field13->typeofdata = 'V~O';

    $blockInstance->addField($field13);
}

$field14 = Vtiger_Field::getInstance('trips_firstload', $moduleInstance);
if ($field14) {
    echo "<li> the total_weight already exists</li><br>";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_TRIPS_FIRSTLOAD';
    $field14->name = 'trips_firstload';
    $field14->table = 'vtiger_trips';
    $field14->column = 'trips_firstload';
    $field14->columntype = 'DATE';
    $field14->uitype = 5;
    $field14->typeofdata = 'D~O';

    $blockInstance->addField($field14);
}

$field15 = Vtiger_Field::getInstance('currentzone', $moduleInstance);
if ($field15) {
    echo "<li>the currentzone already exists</li><br>";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_TRIPS_CURRENTZONE';
    $field15->name = 'currentzone';
    $field15->table = 'vtiger_trips';
    $field15->column = 'currentzone';
    $field15->columntype = 'VARCHAR(50)';
    $field15->uitype = 16;
    $field15->typeofdata = 'V~O';
    
    $blockInstance->addField($field15);
    $field15->setPicklistValues(array('none'));
}

$field155 = Vtiger_Field::getInstance('unitnumber', $moduleInstance);
if ($field155) {
    echo "<li>the unitnumber already exists</li><br>";
} else {
    $field155 = new Vtiger_Field();
    $field155->label = 'LBL_TRIPS_UNITNUMBER';
    $field155->name = 'unitnumber';
    $field155->table = 'vtiger_trips';
    $field155->column = 'unitnumber';
    $field155->columntype = 'VARCHAR(225)';
    $field155->uitype = 1;
    $field155->typeofdata = 'V~O';
    
    $blockInstance->addField($field155);
}

$field16 = Vtiger_Field::getInstance('trips_status', $moduleInstance);
if ($field16) {
    echo "<li>the status already exists</li><br>";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_TRIPS_STATUS';
    $field16->name = 'trips_status';
    $field16->table = 'vtiger_trips';
    $field16->column = 'trips_status';
    $field16->columntype = 'VARCHAR(225)';
    $field16->uitype = 16;
    $field16->typeofdata = 'V~O';
    
    $blockInstance->addField($field16);
    $field16->setPicklistValues(array('Assigned', 'Completed', 'Current', 'Packaged', 'Planned', 'Void'));
}

$blockInstance = Vtiger_Block::getInstance('LBL_TRIPS_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<h3>The LBL_TRIPS_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TRIPS_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}


$field17 = Vtiger_Field::getInstance('trips_status', $moduleInstance);
if ($field17) {
    echo "<li>the status already exists</li><br>";
} else {
    $field17 = new Vtiger_Field();
    $field17->label = 'LBL_TRIPS_SHIPMENT_COUNT';
    $field17->name = 'trips_shipmentcount';
    $field17->table = 'vtiger_trips';
    $field17->column = 'trips_shipmentcount';
    $field17->columntype = 'VARCHAR(10)';
    $field17->uitype = 2;
    $field17->typeofdata = 'V~O';
    
    $blockInstance->addField($field17);
}


$moduleInstance3 = Vtiger_Block::getInstance('LBL_TRIPS_DRIVER', $moduleInstance);
if ($blockInstance3) {
    echo "<h3>The LBL_TRIPS_DRIVER block already exists</h3><br> \n";
} else {
    $blockInstance3 = new Vtiger_Block();
    $blockInstance3->label = 'LBL_TRIPS_DRIVER';
    $moduleInstance->addBlock($blockInstance3);
}

echo "<ul>";
$field18 = Vtiger_Field::getInstance('trips_driverlastname', $moduleInstance);
if ($field18) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field18 = new Vtiger_Field();
    $field18->label = 'LBL_TRIPS_DRIVER_LAST';
    $field18->name = 'trips_driverlastname';
    $field18->table = 'vtiger_trips';
    $field18->column = 'trips_driverlastname';
    $field18->columntype = 'VARCHAR(50)';
    $field18->uitype = 2;
    $field18->typeofdata = 'V~O';

    $blockInstance->addField($field18);
}

echo "<ul>";
$field19 = Vtiger_Field::getInstance('trips_driverfirstname', $moduleInstance);
if ($field19) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field19 = new Vtiger_Field();
    $field19->label = 'LBL_TRIPS_DRIVER_FIRST';
    $field19->name = 'trips_driverfirstname';
    $field19->table = 'vtiger_trips';
    $field19->column = 'trips_driverfirstname';
    $field19->columntype = 'VARCHAR(50)';
    $field19->uitype = 2;
    $field19->typeofdata = 'V~O';

    $blockInstance->addField($field19);
}

echo "<ul>";
$field20 = Vtiger_Field::getInstance('trips_driverno', $moduleInstance);
if ($field20) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field20 = new Vtiger_Field();
    $field20->label = 'LBL_TRIPS_DRIVER_NO';
    $field20->name = 'trips_driverno';
    $field20->table = 'vtiger_trips';
    $field20->column = 'trips_driverno';
    $field20->columntype = 'VARCHAR(15)';
    $field20->uitype = 2;
    $field20->typeofdata = 'V~O';

    $blockInstance->addField($field20);
}



echo "<ul>";
$field21 = Vtiger_Field::getInstance('trips_drivercellphone', $moduleInstance);
if ($field21) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field21 = new Vtiger_Field();
    $field21->label = 'LBL_TRIPS_DRIVERCELL';
    $field21->name = 'trips_drivercellphone';
    $field21->table = 'vtiger_trips';
    $field21->column = 'trips_drivercellphone';
    $field21->columntype = 'VARCHAR(25)';
    $field21->uitype = 2;
    $field21->typeofdata = 'V~O';

    $blockInstance->addField($field21);
}


echo "<ul>";
$field23 = Vtiger_Field::getInstance('trips_driversemail', $moduleInstance);
if ($field23) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field23 = new Vtiger_Field();
    $field23->label = 'LBL_TRIPS_DRIVEREMAIL';
    $field23->name = 'trips_driversemail';
    $field23->table = 'vtiger_trips';
    $field23->column = 'trips_driversemail';
    $field23->columntype = 'VARCHAR(75)';
    $field23->uitype = 13;
    $field23->typeofdata = 'V~O';

    $blockInstance->addField($field23);
}


echo "<ul>";
$field24 = Vtiger_Field::getInstance('checkin_notes', $moduleInstance);
if ($field24) {
    echo "<li>The checkin_notes field already exists</li><br>";
} else {
    $field24 = new Vtiger_Field();
    $field24->label = 'LBL_TRIPS_CHECKINNOTES';
    $field24->name = 'checkin_notes';
    $field24->table = 'vtiger_trips';
    $field24->column = 'checkin_notes';
    $field24->columntype = 'TEXT';
    $field24->uitype = 19;
    $field24->typeofdata = 'V~O';

    $blockInstance->addField($field24);
}

echo "<ul>";
$field25 = Vtiger_Field::getInstance('checkin', $moduleInstance);
if ($field25) {
    echo "<li>The checkin field already exists</li><br>";
} else {
    $field25 = new Vtiger_Field();
    $field25->label = 'LBL_TRIPS_CHECKIN';
    $field25->name = 'checkin';
    $field25->table = 'vtiger_trips';
    $field25->column = 'checkin';
    $field25->columntype = 'VARCHAR(3)';
    $field25->uitype = 56;
    $field25->typeofdata = 'C~O';

    $blockInstance->addField($field25);
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
    $field37->column = 'modifiedtime';
    $field38->uitype = 70;
    $field38->typeofdata = 'T~O';
    $field38->displaytype = 2;

    $blockInstance->addField($field38);
}



$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$filter1->addField($field1)->addField($field17, 1)->addField($field18, 2)->addField($field19, 3)->addField($field20, 4)->addField($field15, 5)->addField($field2, 6)->addField($field5, 7)->addField($field7, 8)->addField($field6, 9)->addField($field155, 10)->addField($field14, 11)->addField($field21, 12)->addField($field23, 13);

$agentsInstance = Vtiger_Module::getInstance('Agents');
$agentsInstance->setRelatedList($moduleInstance, 'LBL_TRIPS', array('ADD'), 'get_dependents_list');

$employeesInstance = Vtiger_Module::getInstance('Employees');
$employeesInstance->setRelatedList($moduleInstance, 'LBL_TRIPS', array('ADD'), 'get_dependents_list');

$ordersInstance = Vtiger_Module::getInstance('Orders');
$ordersInstance->setRelatedList($moduleInstance, 'LBL_TRIPS', array('ADD'), 'get_trips');





print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";