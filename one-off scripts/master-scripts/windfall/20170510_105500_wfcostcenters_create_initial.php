<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

global $adb;

print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
$isNew = false;
global $adb;


$moduleInstance = Vtiger_Module::getInstance('WFCostCenters');
if ($moduleInstance) {
    echo "CostCenters Module exists<br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "CostCenters";
    $moduleInstance->save();
    $moduleInstance->initTables();
    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();
    // Webservice Setup
    $moduleInstance->initWebservice();

    $isNew = true;
}

$filter1 = Vtiger_Filter::getInstance('All', $moduleInstance);
if($filter1) {
  $filter1->delete();
}
$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

$blockInstance = Vtiger_Block::getInstance('LBL_WFCOSTCENTERS_DETAILS', $moduleInstance);
if ($blockInstance) {
    echo "<li>The LBL_WFCOSTCENTERS_DETAILS block already exists</li><br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_WFCOSTCENTERS_DETAILS';
    $moduleInstance->addBlock($blockInstance);
}

$fieldName = 'division';
$fieldLabel = 'LBL_WFCOSTCENTERS_'.strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfcostcenters';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(20)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 2;
    $blockInstance->addField($field);
    $moduleInstance->setEntityIdentifier($field);
}


$fieldName = 'department';
$fieldLabel = 'LBL_WFCOSTCENTERS_'.strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfcostcenters';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(20)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 3;

    $blockInstance->addField($field);
    $filter1->addField($field,1);
}


$fieldName = 'unit';
$fieldLabel = 'LBL_WFCOSTCENTERS_'.strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfcostcenters';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(256)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 4;

    $blockInstance->addField($field);
    $filter1->addField($field,2);
}

$fieldName = 'boxlabelnumber';
$fieldLabel = 'LBL_WFCOSTCENTERS_'.strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfcostcenters';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(256)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 5;

    $blockInstance->addField($field);
    $filter1->addField($field,3);
}

$fieldName = 'accounts';
$fieldLabel = 'LBL_WFCOSTCENTERS_'.strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field){
    echo "<li> $fieldName already exists</li><br>";
}else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfcostcenters';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';

    $blockInstance->addField($field);

    $field->setRelatedModules(array('WFAccounts'));
}



$fieldName = 'agentid';
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
    $field = new Vtiger_Field();
    $field->label = 'Owner';
    $field->name = 'agentid';
    $field->table = 'vtiger_crmentity';
    $field->column = 'agentid';
    $field->uitype = 1002;
    $field->typeofdata = 'I~M';
    $field->sequence = 6;

    $blockInstance->addField($field);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_RECORD_UPDATE_INFORMATION', $moduleInstance);
if ($blockInstance2) {
    echo "<li>The LBL_RECORD_UPDATE_INFORMATION block already exists</li><br>";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_RECORD_UPDATE_INFORMATION';
    $moduleInstance->addBlock($blockInstance2);
}

$fieldName = 'createdtime';
$field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
if (!$field){
    $field = new Vtiger_Field();
    $field->label = 'LBL_DATECREATED';
    $field->name = 'createdtime';
    $field->table = 'vtiger_crmentity';
    $field->column = 'createdtime';
    $field->uitype = 70;
    $field->typeofdata = 'DT~O';
    $field->displaytype = 2;

    $blockInstance2->addField($field);
}

$fieldName = 'modifiedtime';
$field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
if (!$field){
    $field = new Vtiger_Field();
    $field->label = 'LBL_MODIFIEDTIME';
    $field->name = 'modifiedtime';
    $field->table = 'vtiger_crmentity';
    $field->column = 'createdtime';
    $field->uitype = 70;
    $field->typeofdata = 'DT~O';
    $field->displaytype = 2;

    $blockInstance2->addField($field);
}


$fieldName = 'assigned_user_id';
$field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
if (!$field) {
    $field = new Vtiger_Field();
    $field->label = 'LBL_WFCOSTCENTERS_ASSIGNED_TO';
    $field->name = 'assigned_user_id';
    $field->table = 'vtiger_crmentity';
    $field->column = 'smownerid';
    $field->uitype = 53;
    $field->typeofdata = 'V~O';
    $field->displaytype = 2;
    $blockInstance2->addField($field);
}

$fieldName = 'createdby';
$field = Vtiger_Field::getInstance($fieldName,$moduleInstance);
if (!$field) {
    $field = new Vtiger_Field();
    $field->label = 'LBL_WFCOSTCENTERS_CREATEDBY';
    $field->name = 'createdby';
    $field->table = 'vtiger_crmentity';
    $field->column = 'smownerid';
    $field->uitype = 52;
    $field->typeofdata = 'V~O';
    $field->displaytype = 2;
    $blockInstance2->addField($field);
}


$accountsModuleInstance = Vtiger_Module::getInstance('WFAccounts');
$tableid = $moduleInstance->getId();
$sql = "SELECT * FROM `vtiger_modtracker_tabs` WHERE `vtiger_modtracker_tabs`.`tabid` = ?";
$result = $adb->pquery($sql,array($tableid));
if ($adb->num_rows($result) == 0){
    $adb->pquery("insert into `vtiger_modtracker_tabs` ( `visible`, `tabid`) values (?, ?)",array('1', $tableid));
}


$sql = "Select * From `vtiger_relatedlists` WHERE `vtiger_relatedlists`.`tableid`=? AND WHERE `vtiger_relatedlists`.`related_tableid`=?";
$result = $adb->pquery($sql,array($accountsModuleInstance->getId(),$moduleInstance->getId()));
if ($adb->num_rows($result)==0){
    $accountsModuleInstance->setRelatedList($moduleInstance, 'Cost Center', array('ADD'), 'get_dependents_list');
}

$inventoryModuleInstance = Vtiger_Module::getInstance('Inventory2');
if ($inventoryModuleInstance){
    $sql = "Select * From `vtiger_relatedlists` WHERE `vtiger_relatedlists`.`tableid`=? AND WHERE `vtiger_relatedlists`.`related_tableid`=?";
    $result = $adb->pquery($sql,array($moduleInstance->getId(),$inventoryModuleInstance->getId()));
    if ($adb->num_rows($result)==0){
        $moduleInstance->setRelatedList($inventoryModuleInstance, 'Inventory', array('ADD'), 'get_dependents_list');
    }
}
