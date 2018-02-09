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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
$isNew = false;
global $adb;


$moduleInstance = Vtiger_Module::getInstance('WFWarehouses');
if ($moduleInstance) {
    echo "WFWarehouse Module exists<br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "WFWarehouses";
    $moduleInstance->save();
    $moduleInstance->initTables();
    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();
    // Webservice Setup
    $moduleInstance->initWebservice();

    $isNew = true;
}

if ($isNew) {
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
}


$blockInstance = Vtiger_Block::getInstance('LBL_WFWAREHOUSE_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<li>The LBL_WFWAREHOUSE_INFORMATION block already exists</li><br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_WFWAREHOUSE_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

$fieldName = 'agent';
$fieldLabel = 'LBL_WFWAREHOUSES_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 10;
    $field->typeofdata = 'V~M';

    $blockInstance->addField($field);

    $field->setRelatedModules(array('Agents'));
    $filter1->addField($field, 0);
}


$fieldName = 'code';
$fieldLabel = 'LBL_WFWAREHOUSES_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(20)';
    $field->uitype = 2;
    $field->typeofdata = 'V~M';
    $blockInstance->addField($field);
    $moduleInstance->setEntityIdentifier($field);
    $filter1->addField($field, 1);
}

$fieldName = 'name';
$fieldLabel = 'LBL_WFWAREHOUSES_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(20)';
    $field->uitype = 2;
    $field->typeofdata = 'V~M';

    $blockInstance->addField($field);
    $filter1->addField($field, 2);
}


$fieldName = 'address';
$fieldLabel = 'LBL_WFWAREHOUSES_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(256)';
    $field->uitype = 2;
    $field->typeofdata = 'V~M';

    $blockInstance->addField($field);
    $filter1->addField($field, 3);
}

$fieldName = 'address2';
$fieldLabel = 'LBL_WFWAREHOUSES_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(256)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';

    $blockInstance->addField($field);
    $filter1->addField($field, 4);
}


$fieldName = 'city';
$fieldLabel = 'LBL_WFWAREHOUSES_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(256)';
    $field->uitype = 2;
    $field->typeofdata = 'V~M';

    $blockInstance->addField($field);
    $filter1->addField($field, 5);
}

$fieldName = 'state';
$fieldLabel = 'LBL_WFWAREHOUSES_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(50)';
    $field->uitype = 2;
    $field->typeofdata = 'V~M';

    $blockInstance->addField($field);
    $filter1->addField($field, 6);
}

$fieldName = 'country';
$fieldLabel = 'LBL_WFWAREHOUSES_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(50)';
    $field->uitype = 2;
    $field->typeofdata = 'V~M';

    $blockInstance->addField($field);
    $filter1->addField($field, 7);
}


$fieldName = 'postal_code';
$fieldLabel = 'LBL_WFWAREHOUSES_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(50)';
    $field->uitype = 2;
    $field->typeofdata = 'V~M';

    $blockInstance->addField($field);
    $filter1->addField($field, 8);
}


$fieldName = 'square_footage';
$fieldLabel = 'LBL_WFWAREHOUSES_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfwarehouses';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 2;
    $field->typeofdata = 'I~O';

    $blockInstance->addField($field);
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
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    $field->delete();
}

$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
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
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    $field->delete();
}

$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
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
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    $field->delete();
}

$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
    $field = new Vtiger_Field();
    $field->label = 'LBL_WAREHOUSE_ASSIGNED_TO';
    $field->name = 'assigned_user_id';
    $field->table = 'vtiger_crmentity';
    $field->column = 'smownerid';
    $field->uitype = 53;
    $field->typeofdata = 'V~M';
    $field->displaytype = 2;
    $blockInstance2->addField($field);
}

$fieldName = 'createdby';
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    $field->delete();
}

$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
    $field = new Vtiger_Field();
    $field->label = 'LBL_WAREHOUSE_CREATEDBY';
    $field->name = 'createdby';
    $field->table = 'vtiger_crmentity';
    $field->column = 'smownerid';
    $field->uitype = 52;
    $field->typeofdata = 'V~O';
    $field->displaytype = 2;
    $blockInstance2->addField($field);
}


$tableid = $moduleInstance->getId();
$sql = "SELECT * FROM `vtiger_modtracker_tabs` WHERE `vtiger_modtracker_tabs`.`tabid` = ?";
$result = $adb->pquery($sql, array($tableid));
if ($adb->num_rows($result) == 0) {
    $adb->pquery("insert into `vtiger_modtracker_tabs` ( `visible`, `tabid`) values (?, ?)", array('1', $tableid));
}
