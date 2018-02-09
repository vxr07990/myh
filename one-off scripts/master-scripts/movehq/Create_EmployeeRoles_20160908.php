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

$moduleInstance = Vtiger_Module::getInstance('EmployeeRoles');

if ($moduleInstance) {
    echo "<h2>Employee Roles already exists </h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'EmployeeRoles';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $isNew = true;
}

$blockInstance = Vtiger_Block::getInstance('LBL_EMPLOYEEROLES_INFORMATION', $moduleInstance);

if ($blockInstance) {
    echo "<h3>The LBL_EMPLOYEEROLES_INFORMATION block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_EMPLOYEEROLES_INFORMATION';
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

//Description Field
$field2 = Vtiger_Field::getInstance('emprole_desc', $moduleInstance);
if ($field2) {
    echo "<br> The emprole_desc field already exists in EmployeeRoles <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_EMPLOYEEROLES_DESCRIPTION';
    $field2->name = 'emprole_desc';
    $field2->table = 'vtiger_employeeroles';
    $field2->column ='emprole_desc';
    $field2->columntype = 'varchar(100)';
    $field2->uitype = 1;
    $field2->typeofdata = 'V~M';
    $field2->quickcreate = 0;
    $field2->summaryfield = 1;
   
    $blockInstance->addField($field2);
    $moduleInstance->setEntityIdentifier($field2);
}

//Owner Field
$field3 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if ($field3) {
    echo "<br> The agentid field already exists in EmployeeRoles <br>";
} else {
    $field3             = new Vtiger_Field();
    $field3->label      = 'Owner';
    $field3->name       = 'agentid';
    $field3->table      = 'vtiger_crmentity';
    $field3->column     = 'agentid';
    $field3->columntype = 'INT(10)';
    $field3->uitype     = 1002;
    $field3->typeofdata = 'I~M';
    $field3->quickcreate = 0;
    $field3->summaryfield = 1;

    $blockInstance->addField($field3);
}

//Active Field
$field4 = Vtiger_Field::getInstance('emprole_active', $moduleInstance);
if ($field4) {
    echo "<br> The emprole_active field already exists in Employee Roles <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_EMPLOYEEROLES_ACTIVE';
    $field4->name = 'emprole_active';
    $field4->table = 'vtiger_employeeroles';
    $field4->column ='emprole_active';
    $field4->columntype = 'varchar(100)';
    $field4->uitype = 16;
    $field4->typeofdata = 'V~M';
    $field4->defaultvalue = 'Active';
    $field4->quickcreate = 0;
   
    $blockInstance->addField($field4);
    $field4->setPicklistValues(['Active', 'Inactive']);
}

// Classificaiton Type
$field5 = Vtiger_Field::getInstance('emprole_class_type', $moduleInstance);
if ($field5) {
    echo "<br> The emprole_class_type field already exists in Employee_Roles <br>";
} else {
    $field5 = new Vtiger_Field();
    $field5->label = 'LBL_EMPLOYEEROLES_CLASS_TYPE';
    $field5->name = 'emprole_class_type';
    $field5->table = 'vtiger_employeeroles';
    $field5->column ='emprole_class_type';
    $field5->columntype = 'varchar(20)';
    $field5->uitype = 16;
    $field5->typeofdata = 'V~M';
    $field5->quickcreate = 0;
    $field5->summaryfield = 1;
   
    $blockInstance->addField($field5);
    $field5->setPicklistValues(['Office', 'Operations']);
}

//Classification
$field6 = Vtiger_Field::getInstance('emprole_class', $moduleInstance);
if ($field6) {
    echo "<br> The emprole_class field already exists in Employee_Roles <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_EMPLOYEEROLES_CLASSIFICATION';
    $field6->name = 'emprole_class';
    $field6->table = 'vtiger_employeeroles';
    $field6->column ='emprole_class';
    $field6->columntype = 'varchar(20)';
    $field6->uitype = 16;
    $field6->typeofdata = 'V~M';
    $field6->quickcreate = 0;
    $field6->summaryfield = 1;
   
    $blockInstance->addField($field6);
    $field6->setPicklistValues(['Driver', 'Lead', 'Helper', 'Packer', 'Supervisor', 'Installer', 'Warehouse', 'Salesperson', 'Coordinator', 'Surveyor', 'Claims Adjuster', 'Biller', 'Collector', 'General Office']);
}

//Date Created
$field7 = Vtiger_Field::getInstance('createdtime', $moduleInstance);
if ($field7) {
    echo "<li>The createdtime field already exists</li><br> \n";
} else {
    $field7 = new Vtiger_Field();
    $field7->label = 'LBL_EMPROLE_CREATEDTIME';
    $field7->name = 'createdtime';
    $field7->table = 'vtiger_crmentity';
    $field7->column = 'createdtime';
    $field7->uitype = 70;
    $field7->typeofdata = 'T~O';
    $field7->displaytype = 2;

    $blockInstance2->addField($field7);
}

//Date Modified
$field8 = Vtiger_Field::getInstance('modifiedtime', $moduleInstance);
if ($field8) {
    echo "<li>The modifiedtime field already exists</li><br> \n";
} else {
    $field8 = new Vtiger_Field();
    $field8->label = 'LBL_EMPROLE_MODIFIEDTIME';
    $field8->name = 'modifiedtime';
    $field8->table = 'vtiger_crmentity';
    $field8->column = 'modifiedtime';
    $field8->uitype = 70;
    $field8->typeofdata = 'T~O';
    $field8->displaytype = 2;

    $blockInstance2->addField($field8);
}

//Created By
$field9 = Vtiger_Field::getInstance('createdby', $moduleInstance);
if ($field9) {
    echo "<li>The createdby field already exists</li><br> \n";
} else {
    $field9 = new Vtiger_Field();
    $field9->label = 'LBL_EMPROLE_CREATEDBY';
    $field9->name = 'createdby';
    $field9->table = 'vtiger_crmentity';
    $field9->column = 'smcreatorid';
    $field9->uitype = 52;
    $field9->typeofdata = 'V~O';
    $field9->displaytype = 2;

    $blockInstance2->addField($field9);
}

//Assigned To
$field10 = Vtiger_Field::getInstance('assigned_user_id', $moduleInstance);
if ($field10) {
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field10 = new Vtiger_Field();
    $field10->label = 'LBL_EMPROLE_ASSIGNED_TO';
    $field10->name = 'assigned_user_id';
    $field10->table = 'vtiger_crmentity';
    $field10->column = 'smownerid';
    $field10->uitype = 53;
    $field10->typeofdata = 'V~M';
    $field10->displaytype = 2;

    $blockInstance2->addField($field10);
}

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field2)->addField($field3, 1)->addField($field5, 2)->addField($field6, 3);

    $moduleInstance->setDefaultSharing();

    $moduleInstance->initWebservice();

    //Menu
    $parentLabel = 'COMPANY_ADMIN_TAB';
    $db = PearDatabase::getInstance();
    if ($db) {
        $stmt = 'UPDATE vtiger_tab SET parent=? WHERE tabid=?';
        $db->pquery($stmt, [$parentLabel, $moduleInstance->id]);
    } else {
        Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tab SET parent='".$parentLabel."' WHERE tabid=".$moduleInstance->id);
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";