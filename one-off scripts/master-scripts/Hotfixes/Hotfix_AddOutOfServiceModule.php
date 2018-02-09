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



$moduleInstance = Vtiger_Module::getInstance('OutOfService');
$is_new = false;
if ($moduleInstance) {
    echo "Module OutOfService already present - Updating Fields";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'OutOfService';
    $moduleInstance->parent = '';
    $moduleInstance->save();

    // Schema Setup
    $moduleInstance->initTables();
    $is_new = true;
}
// Field Setup
$blockName = 'LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION';
$block = Vtiger_Block::getInstance($blockName, $moduleInstance);
if (!$block) {
    $block = new Vtiger_Block();
    $block->label = $blockName;
    $moduleInstance->addBlock($block);
}

$field01 = Vtiger_Field::getInstance('outofservice_no', $moduleInstance);
if (!$field01) {
    $field01 = new Vtiger_Field();
    $field01->label = 'Out Of Service Id';
    $field01->name = 'outofservice_no';
    $field01->table = 'vtiger_outofservice';
    $field01->column = 'outofservice_no';
    $field01->summaryfield = 1;
    $field01->columntype = 'VARCHAR(10)';
    $field01->uitype = 4;
    $field01->typeofdata = 'V~M';
    $block->addField($field01);
    $moduleInstance->setEntityIdentifier($field01);

    global $adb;
    $numid = $adb->getUniqueId("vtiger_modentity_num");
    $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)", array($numid, 'OutOfService', 'OOS', 1, 1, 1));
}

$field02 = Vtiger_Field::getInstance('outofservice_employeesid', $moduleInstance);
if (!$field02) {
    $field02 = new Vtiger_Field();
    $field02->label = 'Employees';
    $field02->name = 'outofservice_employeesid';
    $field02->table = 'vtiger_outofservice';
    $field02->column = 'outofservice_employeesid';
    $field02->summaryfield = 1;
    $field02->columntype = 'INT(19)';
    $field02->uitype = 10;
    $field02->typeofdata = 'I~O';
    $block->addField($field02);
    $field02->setRelatedModules(array('Employees'));
}

$field1 = Vtiger_Field::getInstance('outofservice_status', $moduleInstance);
if (!$field1) {
    $field1 = new Vtiger_Field();
    $field1->name = 'outofservice_status';
    $field1->label = 'Status';
    $field1->uitype = 16;
    $field1->table = 'vtiger_outofservice';
    $field1->column = $field1->name;
    $field1->summaryfield = 1;
    $field1->columntype = 'VARCHAR(255)';
    $field1->typeofdata = 'V~O';
    $field1->setPicklistValues(array('Out of Service', 'On Notice'));
    $block->addField($field1);
}

$field2 = Vtiger_Field::getInstance('outofservice_servicestatus', $moduleInstance);
if (!$field2) {
    $field2 = new Vtiger_Field();
    $field2->name = 'outofservice_servicestatus';
    $field2->label = 'Service Status';
    $field2->uitype = 1;
    $field2->table = 'vtiger_outofservice';
    $field2->column = $field2->name;
    $field2->columntype = 'VARCHAR(255)';
    $field2->typeofdata = 'D~O';
    $block->addField($field2);
}

$field3 = Vtiger_Field::getInstance('outofservice_type', $moduleInstance);
if (!$field3) {
    $field3 = new Vtiger_Field();
    $field3->name = 'outofservice_type';
    $field3->label = 'Type';
    $field3->uitype = 16;
    $field3->table = 'vtiger_outofservice';
    $field3->column = $field3->name;
    $field3->summaryfield = 1;
    $field3->columntype = 'VARCHAR(255)';
    $field3->typeofdata = 'V~O';
    $field3->setPicklistValues($arrayTypes = array(
        'Accident',
        'Annual Review',
        'Citation',
        'Contract',
        'Contract ',
        'DOT',
        'Driver disqualified',
        'Driver reinstated',
        'Driver´s License',
        'Drug and Alcohol Program',
        'Fuel Tax',
        'Insurance',
        'Insurance ',
        'Logs',
        'Logs ',
        'MVR',
        'No Current Affiliation',
        'Orientation',
        'Personal Leave',
        'Physical ',
        'Qualification',
        'Safety',
        'Terminated'));
    $block->addField($field3);
}

$field4 = Vtiger_Field::getInstance('outofservice_typeofreason', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->name = 'outofservice_typeofreason';
    $field4->label = 'Type Of Reason';
    $field4->uitype = 16;
    $field4->table = 'vtiger_outofservice';
    $field4->column = $field4->name;
    $field4->columntype = 'VARCHAR(255)';
    $field4->summaryfield = 1;
    $field4->typeofdata = 'V~O';
    $field4->setPicklistValues($arraySubTypes = array(
        'All Reasons',
        'accident report due from driver',
        'Compliance Letter due',
        'Investigation',
        'Safety Needs to talk to driver',
        'Suspension',
        'Expired',
        'Probation',
        'Violated law, penalty not resolved',
        'I/C 30 day notice to cancel',
        'I/C contract cancelled - call safety to clear',
        'TSC 30 day notice to cancel',
        'Paperwork Incomplete',
        'Roadside Inspection - Need proof of repair',
        'Expired',
        'Invalid',
        'Multiple Licenses',
        'Suspended',
        'Training Due',
        'Fuel Report missing',
        'Report Incomplete',
        'Auto liability expired',
        'auto liability incomplete',
        'General Liability expired',
        'General Liability incomplete',
        'non-trucking liability expired',
        'occ/acc insurance expired',
        'physical damage expired',
        'umbrella expired',
        'Worker´s Comp expired',
        'Worker´s Comp incomplete',
        'Cancel 90 days',
        'Cancel over 90 days must reapply',
        'inactive driver (emergency driver only)',
        'Incomplete or inaccurate',
        'missing',
        'still missing some logs',
        'missing - local shuttle driver only',
        'Temporarily inactive - excused',
        'violation letter due to safety',
        'Conviction Pending',
        'driver must obtain from state',
        'need notice of moving violation form',
        'driver orientation registration - due',
        'due',
        'due (local non-regulated driver)',
        'blood pressure check due',
        'Cert. Card expired - 90 day blood pressure',
        'Exam failed or incomplete',
        'Expired',
        'Follow-up required',
        'Injury',
        'Medical Restriction',
        '7 day prior logs due',
        'Forms Incomplete',
        'Investigation',
        'Misc.',
        'notice of state violation - unsatisfied',
        'Suspension'));
    $block->addField($field4);
}

$field5 = Vtiger_Field::getInstance('outofservice_effectivedate', $moduleInstance);
if (!$field5) {
    $field5 = new Vtiger_Field();
    $field5->name = 'outofservice_effectivedate';
    $field5->label = 'Effective Date';
    $field5->uitype = 5;
    $field5->table = 'vtiger_outofservice';
    $field5->summaryfield = 1;

    $field5->column = $field5->name;
    $field5->columntype = 'date';
    $field5->typeofdata = 'D~O';
    $block->addField($field5);
}

$field6 = Vtiger_Field::getInstance('outofservice_satisfieddate', $moduleInstance);
if (!$field6) {
    $field6 = new Vtiger_Field();
    $field6->name = 'outofservice_satisfieddate';
    $field6->label = 'Satisfied Date';
    $field6->uitype = 5;
    $field6->table = 'vtiger_outofservice';
    $field6->column = $field6->name;
    $field6->summaryfield = 1;
    $field6->columntype = 'date';
    $field6->typeofdata = 'D~O';
    $block->addField($field6);
}

$field7 = Vtiger_Field::getInstance('outofservice_comment', $moduleInstance);
if (!$field7) {
    $field7 = new Vtiger_Field();
    $field7->name = 'outofservice_comment';
    $field7->label = 'Comment';
    $field7->uitype = 19;
    $field7->table = 'vtiger_outofservice';
    $field7->column = $field7->name;
    $field7->columntype = 'TEXT';
    $field7->typeofdata = 'V~O';
    $block->addField($field7);
}

// Recommended common fields every Entity module should have (linked to core table)
$mfield1 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if (!$mfield1) {
    $mfield1 = new Vtiger_Field();
    $mfield1->name = 'assigned_user_id';
    $mfield1->label = 'Assigned To';
    $mfield1->table = 'vtiger_crmentity';
    $mfield1->column = 'smownerid';
    $mfield1->uitype = 53;
    $mfield1->typeofdata = 'V~M';
    $block->addField($mfield1);
}

$mfield2 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if (!$mfield2) {
    $mfield2 = new Vtiger_Field();
    $mfield2->name = 'createdtime';
    $mfield2->label = 'Created Time';
    $mfield2->table = 'vtiger_crmentity';
    $mfield2->column = 'createdtime';
    $mfield2->uitype = 70;
    $mfield2->typeofdata = 'T~O';
    $mfield2->displaytype = 2;
    $block->addField($mfield2);
}

$mfield3 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if (!$mfield3) {
    $mfield3 = new Vtiger_Field();
    $mfield3->name = 'modifiedtime';
    $mfield3->label = 'Modified Time';
    $mfield3->table = 'vtiger_crmentity';
    $mfield3->column = 'modifiedtime';
    $mfield3->uitype = 70;
    $mfield3->typeofdata = 'T~O';
    $mfield3->displaytype = 2;
    $block->addField($mfield3);
}

$mfield4 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if (!$mfield4) {
    $mfield4 = new Vtiger_Field();
    $mfield4->label = 'Owner';
    $mfield4->name = 'agentid';
    $mfield4->table = 'vtiger_crmentity';
    $mfield4->column = 'agentid';
    $mfield4->columntype = 'INT(11)';
    $mfield4->uitype = 1002;
    $mfield4->typeofdata = 'I~M';
    $block->addField($mfield4);
}


if ($is_new) {

    // Filter Setup
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $filter1->addField($field01)->addField($field02, 1)->addField($field1, 2)->addField($field3, 3)->addField($mfield1, 4);

    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();

    // Webservice Setup
    $moduleInstance->initWebservice();

    //Relate to Vendors
    $employeesInstance = Vtiger_Module::getInstance('Employees');
    $employeesInstance->setRelatedList($moduleInstance, 'Out Of Service', array('ADD'), 'get_dependents_list');

    //------ Updating Picklist Dependency ----

    $db = PearDatabase::getInstance();
    $sql = 'SELECT MAX(id) AS id FROM vtiger_picklist_dependency';
    $result = $db->pquery($sql);

    if ($result && $db->num_rows($result) > 0) {
        while ($row = $db->fetch_row($result)) {
            echo 'row[id]' . $row['id'] . '<br>';
            $id = $row['id'];
        }
    } else {
        $id = 0;
    }

    $tabid = $moduleInstance->id;

    $values[] = $id + 1 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Accident",\'["All Reasons","accident report due from driver","Compliance Letter due","Investigation","Safety Needs to talk to driver","Suspension"]\'';
    $values[] = $id + 2 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Annuel Review",\'["All Reasons","Expired","Probation"]\'';
    $values[] = $id + 3 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Citation",\'["Violated law, penalty not resolved"]\'';
    $values[] = $id + 4 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Contract",\'["All Reasons","I\/C 30 day notice to cancel","I\/C contract cancelled - call safety to clear","TSC 30 day notice to cancel","Paperwork Incomplete"]\'';
    $values[] = $id + 5 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","DOT",\'["All Reasons","Roadside Inspection - Need proof of repair"]\'';
    $values[] = $id + 6 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Driver disqualified",\'[""]\'';
    $values[] = $id + 7 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Driver reinstated",\'[""]\'';
    $values[] = $id + 8 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Driver´s License",\'["All Reasons","Expired","Invalid","Multiple Licenses","Suspended"]\'';
    $values[] = $id + 9 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Drug and Alcohol Program",\'["Training Due"]\'';
    $values[] = $id + 10 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Fuel Tax",\'["All Reasons","Fuel Report missing","Report Incomplete"]\'';
    $values[] = $id + 11 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Insurance",\'["All Reasons","Auto liability expired","auto liability incomplete","General Liability expired","General Liability incomplete","non-trucking liability expired","occ\/acc insurance expired","physical damage expired","umbrella expired","Worker\u00b4s Comp expired","Worker\u00b4s Comp incomplete","Cancel 90 days","Cancel over 90 days must reapply"]\'';
    $values[] = $id + 12 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Logs",\'["All Reasons","inactive driver (emergency driver only)","Incomplete or inaccurate","missing","still missing some logs","missing - local shuttle driver only","Temporarily inactive - excused","violation letter due to safety"]\'';
    $values[] = $id + 13 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","No Current Affiliation",\'["All Reasons","Conviction Pending","driver must obtain from state","need notice of moving violation form","driver orientation registration - due"]\'';
    $values[] = $id + 14 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Orientation",\'["All Reasons","driver orientation registration - due","due","due (local non-regulated driver)"]\'';
    $values[] = $id + 15 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Personal Leave",\'[""]\'';
    $values[] = $id + 16 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Physical ",\'["blood pressure check due","Cert. Card expired - 90 day blood pressure","Exam failed or incomplete","Follow-up required","Injury","Medical Restriction"]\'';
    $values[] = $id + 17 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Safety",\'["All Reasons","Investigation","Suspension","Misc.","notice of state violation - unsatisfied"]\'';
    $values[] = $id + 18 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Qualification",\'["All Reasons","7 day prior logs due","Forms Incomplete"]\'';
    $values[] = $id + 19 . ',' . $tabid . ', "outofservice_type","outofservice_typeofreason","Terminated",\'[""]\'';

    foreach ($values as $value) {
        $result = $db->pquery("INSERT INTO vtiger_picklist_dependency (id, tabid, sourcefield, targetfield, sourcevalue, targetvalues) VALUES ($value)");
    }

    echo "OK\n";
}

//Hidden OutOfService employee fields
$employeesInstance = Vtiger_Module::getInstance('Employees');

$fields = array('date_oos', 'date_reinstated', 'oos_reason', 'oos_comments');
foreach ($fields as $field) {
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_field SET presence=1 WHERE fieldname='$field' AND tabid=$employeesInstance->id");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";