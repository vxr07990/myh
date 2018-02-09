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
$moduleInstance = Vtiger_Module::getInstance('TimeCalculator');

if($moduleInstance)
{
    echo "<h2>TimeCalculator already exists </h2><br>";
}
else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'TimeCalculator';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $moduleInstance->setDefaultSharing();
    $moduleInstance->initWebservice();
    $isNew = true;
}

$blockInstance = Vtiger_Block::getInstance('LBL_TIMECALCULATOR_DETAILS',$moduleInstance);

if($blockInstance)
{
    echo "<h3>The LBL_TIMECALCULATOR_DETAILS block already exists</h3><br> \n";
}
else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_TIMECALCULATOR_DETAILS';
    $moduleInstance->addBlock($blockInstance);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_TIMECALCULATOR_RECORDUPDATE',$moduleInstance);

if($blockInstance2)
{
    echo "<h3>The LBL_TIMECALCULATOR_RECORDUPDATE block already exists</h3><br> \n";
}
else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_TIMECALCULATOR_RECORDUPDATE';
    $moduleInstance->addBlock($blockInstance2);
}


//Owner Field
$field2 = Vtiger_Field::getInstance('agentid', $moduleInstance);
if($field2) {
    echo "<br> The agentid field already exists in TimeCalculator <br>";
} else {
    $field2             = new Vtiger_Field();
    $field2->label      = 'Owner';
    $field2->name       = 'agentid';
    $field2->table      = 'vtiger_crmentity';
    $field2->column     = 'agentid';
    $field2->columntype = 'INT(10)';
    $field2->uitype     = 1002;
    $field2->typeofdata = 'I~M';

    $blockInstance->addField($field2);
}

//Default Points Field
$field3 = Vtiger_Field::getInstance('containers_desc', $moduleInstance);
if($field3) {
    echo "<br> The containers_desc field already exists in TimeCalculator <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_DEFAULT_POINTS';
    $field3->name = 'timecalculator_default_points';
    $field3->table = 'vtiger_timecalculator';
    $field3->column ='timecalculator_default_points';
    $field3->columntype = 'Decimal(5,2)';
    $field3->uitype = 7;
    $field3->typeofdata = 'N~O';
    $field3->summaryfield = '1';
    $field3->defaultvalue = '5.00';
    $blockInstance->addField($field3);
    $moduleInstance->setEntityIdentifier($field3);
}

//Date Created
$field26 = Vtiger_Field::getInstance('createdtime',$moduleInstance);
if($field26) {
    echo "<li>The createdtime field already exists in TimeCalculator </li><br> \n";
} else {
    $field26 = new Vtiger_Field();
    $field26->label = 'LBL_CREATEDTIME';
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
    echo "<li>The modifiedtime field already exists in TimeCalculator </li><br> \n";
} else {
    $field27 = new Vtiger_Field();
    $field27->label = 'LBL_MODIFIEDTIME';
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
    echo "<li>The createdby field already exists in TimeCalculator </li><br> \n";
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
    $field29->label = 'LBL_ASSIGNED_TO';
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

    $filter1->addField($field2)->addField($field3, 1)->addField($field26, 2)->addfield($field27,3);
    
    $AgentManger=Vtiger_Module_Model::getInstance('AgentManager');
    $AgentManger->setRelatedList($moduleInstance,'TimeCalculator','ADD','get_dependents_list');
}



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";