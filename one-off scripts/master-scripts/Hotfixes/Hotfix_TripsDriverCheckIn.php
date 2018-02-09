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



// Remove some legacy fields from Trips

$tripsInstance = Vtiger_Module::getInstance('Trips');

$field = Vtiger_Field::getInstance('checkin', $tripsInstance);
if ($field) {
    $field->delete();
}

$field = Vtiger_Field::getInstance('checkin_notes', $tripsInstance);
if ($field) {
    $field->delete();
}

//Create a new module to handle the drivers checkins

$moduleInstance = Vtiger_Module::getInstance('TripsDriverCheckin');
$TripsDriverCheckinIsNew = false;
if ($moduleInstance) {
    echo "Module TripsDriverCheckin already present - Updating Fields<br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'TripsDriverCheckin';
    $moduleInstance->parent = '';
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    $TripsDriverCheckinIsNew = true;
}
// Field Setup
$blockName = 'LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION';
$block = Vtiger_Block::getInstance($blockName, $moduleInstance);
if (!$block) {
    $block = new Vtiger_Block();
    $block->label = $blockName;
    $moduleInstance->addBlock($block);
}

$field01 = Vtiger_Field::getInstance('tripsdrivercheckin_no', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'LBL_TRIPSDRIVERCHECKIN_NO';
    $field01->name = 'tripsdrivercheckin_no';
    $field01->table = 'vtiger_tripsdrivercheckin';
    $field01->column = $field01->name;
    $field01->columntype = 'VARCHAR(19)';
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';
    $block->addField($field01);
    
    $moduleInstance->setEntityIdentifier($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'TripsDriverCheckin', 'TDCI', 1, 1, 1));
}


$field2 = Vtiger_Field::getInstance('tripsdrivercheckin_currentlocation', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_TRIPSDRIVERCHECKIN_CURRENTLOCATION';
    $field2->name = 'tripsdrivercheckin_currentlocation';
    $field2->table = 'vtiger_tripsdrivercheckin';
    $field2->column = 'tripsdrivercheckin_currentlocation';
    $field2->columntype = 'VARCHAR(100)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~M';
    $field2->summaryfield = 1;
    $field2->quickcreate = 2;
    $block->addField($field2);
}


$field3 = Vtiger_Field::getInstance('tripsdrivercheckin_nextlocation', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_TRIPSDRIVERCHECKIN_NEXTLOCATION';
    $field3->name = 'tripsdrivercheckin_nextlocation';
    $field3->table = 'vtiger_tripsdrivercheckin';
    $field3->column = 'tripsdrivercheckin_nextlocation';
    $field3->columntype = 'VARCHAR(255)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';
    $field3->summaryfield = 1;
    $field3->quickcreate = 2;
    $block->addField($field3);
}

$field4 = Vtiger_Field::getInstance('tripsdrivercheckin_activity', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_TRIPSDRIVERCHECKIN_ACTIVITY';
    $field4->name = 'tripsdrivercheckin_activity';
    $field4->table = 'vtiger_tripsdrivercheckin';
    $field4->column = 'tripsdrivercheckin_activity';
    $field4->columntype = 'VARCHAR(255)';
    $field4->uitype = 16;
    $field4->typeofdata = 'V~M';
    $field4->quickcreate = 2;

    $block->addField($field4);
    
    $field4->setPicklistValues(['Broke Down', 'Off Dutty', 'Loading', 'Out of Service', 'Running', 'Unloading', 'Under Orders', 'Waiting Orders', 'Not Reported']);
}


$field5 = Vtiger_Field::getInstance('tripsdrivercheckin_tripsid', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_TRIPSDRIVERCHECKIN_TRIPSID';
    $field5->name = 'tripsdrivercheckin_tripsid';
    $field5->table = 'vtiger_tripsdrivercheckin';
    $field5->column = 'tripsdrivercheckin_tripsid';
    $field5->columntype = 'INT(19)';
    $field5->uitype = 10;
    $field5->typeofdata = 'I~O';
    $field5->quickcreate = 2;

    $block->addField($field5);
    
    $field5->setRelatedModules(['Trips']);
}


$field1 = Vtiger_Field::getInstance('tripsdrivercheckin_comments', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_TRIPSDRIVERCHECKIN_COMMENTS';
    $field1->name = 'tripsdrivercheckin_comments';
    $field1->table = 'vtiger_tripsdrivercheckin';
    $field1->column = 'tripsdrivercheckin_comments';
    $field1->columntype = 'text';
    $field1->uitype = 19;
    $field1->typeofdata = 'V~O';
    $field1->summaryfield = 1;
    $field1->quickcreate = 2;
    $block->addField($field1);
}
$block->save();

if ($TripsDriverCheckinIsNew) {
    
        // Recommended common fields every Entity module should have (linked to core table)
    $mfield1 = new Vtiger_Field();
    $mfield1->name = 'assigned_user_id';
    $mfield1->label = 'Assigned To';
    $mfield1->table = 'vtiger_crmentity';
    $mfield1->column = 'smownerid';
    $mfield1->uitype = 53;
    $mfield1->typeofdata = 'V~M';
    $block->addField($mfield1);


    $mfield2 = new Vtiger_Field();
    $mfield2->name = 'CreatedTime';
    $mfield2->label = 'Created Time';
    $mfield2->table = 'vtiger_crmentity';
    $mfield2->column = 'createdtime';
    $mfield2->uitype = 70;
    $mfield2->typeofdata = 'T~O';
    $mfield2->displaytype = 2;
    $block->addField($mfield2);

    $mfield3 = new Vtiger_Field();
    $mfield3->name = 'ModifiedTime';
    $mfield3->label = 'Modified Time';
    $mfield3->table = 'vtiger_crmentity';
    $mfield3->column = 'modifiedtime';
    $mfield3->uitype = 70;
    $mfield3->typeofdata = 'T~O';
    $mfield3->displaytype = 2;
    $block->addField($mfield3);

    $mfield4 = new Vtiger_Field();
    $mfield4->label = 'Owner';
    $mfield4->name = 'agentid';
    $mfield4->table = 'vtiger_crmentity';
    $mfield4->column = 'agentid';
    $mfield4->columntype = 'INT(11)';
    $mfield4->uitype = 1002;
    $mfield4->typeofdata = 'I~M';
    $block->addField($mfield4);

    // Filter Setup
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    
    $filter1->addField($field01)->addField($field2, 1)->addField($field3, 2)->addField($field4, 3)->addField($field7, 4);

    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();

    // Webservice Setup
    $moduleInstance->initWebservice();

    //Relate to Trips
    $tripsInstance = Vtiger_Module::getInstance('Trips');
    $tripsInstance->setRelatedList($moduleInstance, 'Driver Checkin', ['ADD'], 'get_dependents_list');
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";