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

require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');
require_once('includes/runtime/LanguageHandler.php');

//needs these
require_once('include/Webservices/Create.php');
require_once('modules/Vtiger/uitypes/Date.php');
$Vtiger_Utils_Log = true;

$adb = PearDatabase::getInstance();

$moduleInstance = Vtiger_Module::getInstance('AgentCompensation');
$isNew=false;
if($moduleInstance)
{
    echo "<h2>Agent Compensation already exists </h2><br>";
}
else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'AgentCompensation';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $isNew = true;
}


$blockInstance1 = Vtiger_Block::getInstance('LBL_AGENTCOMPENSATION',$moduleInstance);

if($blockInstance1)
{
    echo "<h3>The LBL_AGENTCOMPENSATION block already exists</h3><br> \n";
}
else {
    $blockInstance1 = new Vtiger_Block();
    $blockInstance1->label = 'LBL_AGENTCOMPENSATION';
    $moduleInstance->addBlock($blockInstance1);

}

$blockInstance2 = Vtiger_Block::getInstance('LBL_RECORDUPDATEINFORMATION',$moduleInstance);

if($blockInstance2)
{
    echo "<h3>The LBL_RECORDUPDATEINFORMATION block already exists</h3><br> \n";
}
else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_RECORDUPDATEINFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

// Agent Compensation Detail
//Name Field
$field1 = Vtiger_Field::getInstance('agentcompensation_name', $moduleInstance);
if($field1) {
    echo "<br> The agentcompensation_name field already exists in Agent Compensation <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_NAME';
    $field1->name = 'agentcompensation_name';
    $field1->table = 'vtiger_agentcompensation';
    $field1->column ='agentcompensation_name';
    $field1->columntype = 'varchar(100)';
    $field1->uitype = 1;
    $field1->typeofdata = 'V~M';
    $field1->summaryfield = '1';

    $blockInstance1->addField($field1);
    $moduleInstance->setEntityIdentifier($field1);
}

//Owner Field
$field2 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if($field2) {
    echo "<br> The agentid field already exists in Agent Compensation <br>";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'Owner';
    $field2->name       = 'agentid';
    $field2->table      = 'vtiger_crmentity';
    $field2->column     = 'agentid';
    $field2->columntype = 'INT(10)';
    $field2->uitype     = 1002;
    $field2->typeofdata = 'I~M';

    $blockInstance1->addField($field2);
}


//Status Field
$field3 = Vtiger_Field::getInstance('agentcompensation_status', $moduleInstance);
if($field3) {
    echo "<br> The agentcompensation_status field already exists in Agent Compensation <br>";
    // Update default value
    $adb->pquery("update `vtiger_field` set `defaultvalue`='Active' where `fieldid`=?;", array($field3->id));
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_STATUS';
    $field3->name = 'agentcompensation_status';
    $field3->table = 'vtiger_agentcompensation';
    $field3->column ='agentcompensation_status';
    $field3->columntype = 'varchar(10)';
    $field3->uitype = 16;
    $field3->typeofdata = 'V~M';
    $field3->defaultvalue = 'Active';
    $blockInstance1->addField($field3);
    $field3->setPicklistValues(array('Active','Inactive'));

}


// Record Update Information
//Date Created
$field26 = Vtiger_Field::getInstance('createdtime',$moduleInstance);
if($field26) {
    echo "<li>The createdtime field already exists in Agent Compensation </li><br> \n";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_DATECREATED';
    $field26->name = 'createdtime';
    $field26->table = 'vtiger_crmentity';
    $field26->column = 'createdtime';
    $field26->uitype = 70;
    $field26->typeofdata = 'T~O';
    $field26->displaytype = 2;

    $blockInstance2->addField($field26);
}

//Date Modified
$field27 = Vtiger_Field::getInstance('modifiedtime',$moduleInstance);
if($field27) {
    echo "<li>The modifiedtime field already exists in Agent Compensation </li><br> \n";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_DATEMODIFIED';
    $field27->name = 'modifiedtime';
    $field27->table = 'vtiger_crmentity';
    $field27->column = 'modifiedtime';
    $field27->uitype = 70;
    $field27->typeofdata = 'T~O';
    $field27->displaytype = 2;

    $blockInstance2->addField($field27);
}

//Created By
$field28 = Vtiger_Field::getInstance('createdby',$moduleInstance);
if($field28) {
    echo "<li>The createdby field already exists in Agent Compensation </li><br> \n";
} else {
    $field28 = new Vtiger_Field();
    $field28->label = 'LBL_CREATEDBY';
    $field28->name = 'createdby';
    $field28->table = 'vtiger_crmentity';
    $field28->column = 'smcreatorid';
    $field28->uitype = 52;
    $field28->typeofdata = 'V~O';
    $field28->displaytype = 2;

    $blockInstance2->addField($field28);
}

//Assigned To
$field29 = Vtiger_Field::getInstance('assigned_user_id',$moduleInstance);
if($field29){
    echo "<li>The assigned_user_id field already exists</li><br> \n";
} else {
    $field29 = new Vtiger_Field();
    $field29->label = 'LBL_ASSIGNEDTO';
    $field29->name = 'assigned_user_id';
    $field29->table = 'vtiger_crmentity';
    $field29->column = 'smownerid';
    $field29->uitype = 53;
    $field29->typeofdata = 'V~M';
    $field29->displaytype = 2;

    $blockInstance2->addField($field29);
}


if($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);

    $filter1->addField($field1)->addField($field2, 1)->addField($field3, 2);

    // Add Agent Compensation to Admin Table / CRM Settings (OT Item 3319)
    $adb->pquery("UPDATE vtiger_tab SET parent = '',tabsequence = '-1' WHERE `name` ='AgentCompensation'");
    $max_id = $adb->getUniqueID('vtiger_settings_field');
    $adb->pquery("INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `description`, `linkto`, `sequence`,`pinned`) VALUES (?, ?, ?, ?, ?, ?,?)", array($max_id, '4', 'AgentCompensation', 'Agent Compensation', 'index.php?module=AgentCompensation&view=List', $max_id, '1'));
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";