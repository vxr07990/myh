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
include_once('modules/ModComments/ModComments.php');
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

$isNew = false;

$moduleInstance = Vtiger_Module::getInstance('ContainerTypes');

if ($moduleInstance) {
    echo "<h2>Container Types already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'ContainerTypes';
    $moduleInstance->save();
    $moduleInstance->initTables();
}

$blockInstance = Vtiger_Block::getInstance('LBL_CONTAINERTYPES_DETAILS', $moduleInstance);

if ($blockInstance) {
    echo "<h3>The LBL_CONTAINERTYPES_DETAILS block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_CONTAINERTYPES_DETAILS';
    $moduleInstance->addBlock($blockInstance);
    $isNew = true;
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_CONTAINERTYPES_DIMENSIONS', $moduleInstance);

if ($blockInstance2) {
    echo "<h3>The LBL_CONTAINERTYPES_DIMENSIONS block already exists</h3><br> \n";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_CONTAINERTYPES_DIMENSIONS';
    $moduleInstance->addBlock($blockInstance2);
}

$blockInstance3 = Vtiger_Block::getInstance('LBL_CONTAINERTYPES_COSTS', $moduleInstance);

if ($blockInstance3) {
    echo "<h3>The LBL_CONTAINERTYPES_COSTS block already exists</h3><br> \n";
} else {
    $blockInstance3 = new Vtiger_Block();
    $blockInstance3->label = 'LBL_CONTAINERTYPES_COSTS';
    $moduleInstance->addBlock($blockInstance3);
}

$blockInstance4 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);

if ($blockInstance4) {
    echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance4 = new Vtiger_Block();
    $blockInstance4->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance4);
}

//Name Field
$field1 = Vtiger_Field::getInstance('containertypes_name', $moduleInstance);
if ($field1) {
    echo "<br> The containertypes_name field already exists <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_CONTAINERTYPES_NAME';
    $field1->name = 'containertypes_name';
    $field1->table = 'vtiger_containertypes';
    $field1->column = 'containertypes_name';
    $field1->columntype = 'varchar(100)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~M';
    $field1->quickcreate = 0;
    $field1->summaryfield = 1;

    $blockInstance->addField($field1);
    $moduleInstance->setEntityIdentifier($field1);
}

//Owner Field
$field2 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if ($field2) {
    echo "<br> The agentid field already exists <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'Owner';
    $field2->name = 'agentid';
    $field2->table = 'vtiger_crmentity';
    $field2->column = 'agentid';
    $field2->columntype = 'INT(10)';
    $field2->uitype = 1002;
    $field2->typeofdata = 'I~M';
    $field2->quickcreate = 0;
    $field2->summaryfield = 1;

    $blockInstance->addField($field2);
}

//Description Field
$field3 = Vtiger_Field::getInstance('containertypes_desc', $moduleInstance);
if ($field3) {
    echo "<br> The containertypes_desc field already exists <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_CONTAINERTYPES_DESC';
    $field3->name = 'containertypes_desc';
    $field3->table = 'vtiger_containertypes';
    $field3->column = 'containertypes_desc';
    $field3->columntype = 'varchar(255)';
    $field3->uitype = 1;
    $field3->typeofdata = 'V~O';
    $field3->quickcreate = 1;
    $field3->summaryfield = 1;

    $blockInstance->addField($field3);
}

//Active Field
$field4 = Vtiger_Field::getInstance('containertypes_status', $moduleInstance);
if ($field4) {
    echo "<br> The containertypes_status field already exists <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_CONTAINERTYPES_STATUS';
    $field4->name = 'containertypes_status';
    $field4->table = 'vtiger_containertypes';
    $field4->column = 'containertypes_status';
    $field4->columntype = 'varchar(10)';
    $field4->uitype = 16;
    $field4->typeofdata = 'V~M';
    $field4->defaultvalue = 'Active';
    $field4->quickcreate = 0;

    $blockInstance->addField($field4);
    $field4->setPicklistValues(['Active', 'Inactive']);
}

// Content Field
$field5 = Vtiger_Field::getInstance('containertypes_content', $moduleInstance);
if ($field5) {
    echo "<br> The containertypes_content field already exists <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_CONTAINERTYPES_CONTENT';
    $field5->name = 'containertypes_content';
    $field5->table = 'vtiger_containertypes';
    $field5->column = 'containertypes_content';
    $field5->columntype = 'varchar(255)';
    $field5->uitype = 1;
    $field5->typeofdata = 'V~O';
    $field5->quickcreate = 1;
    $field5->summaryfield = 0;

    $blockInstance->addField($field5);
}

//Length Field
$field6 = Vtiger_Field::getInstance('containertypes_length', $moduleInstance);
if ($field6) {
    echo "<br> The containertypes_length field already exists <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_CONTAINERTYPES_LENGTH';
    $field6->name = 'containertypes_length';
    $field6->table = 'vtiger_containertypes';
    $field6->column = 'containertypes_length';
    $field6->columntype = 'INT(10)';
    $field6->uitype = 7;
    $field6->typeofdata = 'I~O';
    $field6->quickcreate = 1;
    $field6->summaryfield = 0;

    $blockInstance2->addField($field6);
}

//Width Field
$field7 = Vtiger_Field::getInstance('containertypes_width', $moduleInstance);
if ($field7) {
    echo "<br> The containertypes_width field already exists <br>";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_CONTAINERTYPES_WIDTH';
    $field7->name = 'containertypes_width';
    $field7->table = 'vtiger_containertypes';
    $field7->column = 'containertypes_width';
    $field7->columntype = 'INT(10)';
    $field7->uitype = 7;
    $field7->typeofdata = 'I~O';
    $field7->quickcreate = 1;
    $field7->summaryfield = 0;

    $blockInstance2->addField($field7);
}

//Height Field
$field8 = Vtiger_Field::getInstance('containertypes_height', $moduleInstance);
if ($field8) {
    echo "<br> The containertypes_height field already exists <br>";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_CONTAINERTYPES_HEIGHT';
    $field8->name = 'containertypes_height';
    $field8->table = 'vtiger_containertypes';
    $field8->column = 'containertypes_height';
    $field8->columntype = 'INT(10)';
    $field8->uitype = 7;
    $field8->typeofdata = 'I~O';
    $field8->quickcreate = 1;
    $field8->summaryfield = 0;

    $blockInstance2->addField($field8);
}

//Empty Weight Field
$field9 = Vtiger_Field::getInstance('containertypes_emptywt', $moduleInstance);
if ($field9) {
    echo "<br> The containertypes_emptywt field already exists <br>";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_CONTAINERTYPES_EMPTYWT';
    $field9->name = 'containertypes_emptywt';
    $field9->table = 'vtiger_containertypes';
    $field9->column = 'containertypes_emptywt';
    $field9->columntype = 'INT(10)';
    $field9->uitype = 7;
    $field9->typeofdata = 'I~O';
    $field9->quickcreate = 1;
    $field9->summaryfield = 0;

    $blockInstance2->addField($field9);
}

//Container Cost Field
$field10 = Vtiger_Field::getInstance('containertypes_contcost', $moduleInstance);
if ($field10) {
    echo "<br> The containertypes_contcost field already exists <br>";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_CONTAINERTYPES_CONTCOST';
    $field10->name = 'containertypes_contcost';
    $field10->table = 'vtiger_containertypes';
    $field10->column = 'containertypes_contcost';
    $field10->columntype = 'DECIMAL(13,2)';
    $field10->uitype = 71;
    $field10->typeofdata = 'N~O';
    $field10->quickcreate = 1;
    $field10->summaryfield = 0;

    $blockInstance3->addField($field10);
}

//Seal Cost Field
$field11 = Vtiger_Field::getInstance('containertypes_sealcost', $moduleInstance);
if ($field11) {
    echo "<br> The containertypes_sealcost field already exists <br>";
} else {
    $field11 = new Vtiger_Field();
    $field11->label = 'LBL_CONTAINERTYPES_SEALCOST';
    $field11->name = 'containertypes_sealcost';
    $field11->table = 'vtiger_containertypes';
    $field11->column = 'containertypes_sealcost';
    $field11->columntype = 'DECIMAL(13,2)';
    $field11->uitype = 71;
    $field11->typeofdata = 'N~O';
    $field11->quickcreate = 1;
    $field11->summaryfield = 0;

    $blockInstance3->addField($field11);
}

//Repair / Recoupt Cost Field
$field12 = Vtiger_Field::getInstance('containertypes_repaircost', $moduleInstance);
if ($field12) {
    echo "<br> The containertypes_repaircost field already exists <br>";
} else {
    $field12 = new Vtiger_Field();
    $field12->label = 'LBL_CONTAINERTYPES_REPAIRCOST';
    $field12->name = 'containertypes_repaircost';
    $field12->table = 'vtiger_containertypes';
    $field12->column = 'containertypes_repaircost';
    $field12->columntype = 'DECIMAL(13,2)';
    $field12->uitype = 71;
    $field12->typeofdata = 'N~O';
    $field12->quickcreate = 1;
    $field12->summaryfield = 0;

    $blockInstance3->addField($field12);
}

//Date Created
$field13 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field13) {
    echo "<li>The createdtime field already exists</li><br> \n";
} else {
    $field13 = new Vtiger_Field();
    $field13->label = 'LBL_CONTAINERTYPES_CREATEDTIME';
    $field13->name = 'createdtime';
    $field13->table = 'vtiger_crmentity';
    $field13->column = 'createdtime';
    $field13->uitype = 70;
    $field13->typeofdata = 'T~O';
    $field13->displaytype = 2;

    $blockInstance4->addField($field13);
}

//Date Modified
$field14 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if ($field14) {
    echo "<li>The modifiedtime field already exists</li><br> \n";
} else {
    $field14 = new Vtiger_Field();
    $field14->label = 'LBL_CONTAINERTYPES_MODIFIEDTIME';
    $field14->name = 'modifiedtime';
    $field14->table = 'vtiger_crmentity';
    $field14->column = 'modifiedtime';
    $field14->uitype = 70;
    $field14->typeofdata = 'T~O';
    $field14->displaytype = 2;

    $blockInstance4->addField($field14);
}

//Created By
$field15 = Vtiger_Field::getInstance('createdby', $moduleInstance);
if ($field15) {
    echo "<li>The createdby field already exists</li><br> \n";
} else {
    $field15 = new Vtiger_Field();
    $field15->label = 'LBL_CONTAINERTYPES_CREATEDBY';
    $field15->name = 'createdby';
    $field15->table = 'vtiger_crmentity';
    $field15->column = 'smcreatorid';
    $field15->uitype = 52;
    $field15->typeofdata = 'V~O';
    $field15->displaytype = 2;

    $blockInstance4->addField($field15);
}

//Assigned To
$field16 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field16) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field16 = new Vtiger_Field();
    $field16->label = 'LBL_CONTAINERTYPES_ASSIGNED_TO';
    $field16->name = 'assigned_user_id';
    $field16->table = 'vtiger_crmentity';
    $field16->column = 'smownerid';
    $field16->uitype = 53;
    $field16->typeofdata = 'V~M';
    $field16->displaytype = 2;

    $blockInstance4->addField($field16);
}

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field3, 1)->addField($field2, 2);

    $moduleInstance->setDefaultSharing();

    $moduleInstance->initWebservice();

    //Menu
    $parentLabel = 'COMPANY_ADMIN_TAB';
    $db = PearDatabase::getInstance();
    if ($db) {
        $stmt = 'UPDATE vtiger_tab SET parent=? WHERE tabid=?';
        $db->pquery($stmt, [$parentLabel, $moduleInstance->id]);
    } else {
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tab SET parent='" . $parentLabel . "' WHERE tabid=" . $moduleInstance->id);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";