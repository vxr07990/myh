<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}


print "[RUNNING: " . __FILE__ . "<br />\n\e[0m";
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$Vtiger_Utils_Log = true;
$isNew = false;
global $adb;

$moduleInstance = Vtiger_Module::getInstance('WFOrders');
if ($moduleInstance) {
    echo "WFOrders Module exists<br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "WFOrders";
    $moduleInstance->save();
    $moduleInstance->initTables();
    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();
    // Webservice Setup
    $moduleInstance->initWebservice();

    $isNew = true;
}

if ($isNew) {
    $filter = new Vtiger_Filter();
    $filter->name = 'All';
    $filter->isdefault = true;
    $moduleInstance->addFilter($filter);
}

$blockInstance = Vtiger_Block::getInstance('LBL_WFORDER_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<li>The LBL_WFORDER_INFORMATION block already exists</li><br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_WFORDER_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

//*Order Number: free text field/name
$fieldName = 'wforder_number';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 2;
    $field->typeofdata = 'V~M';
    $field->sequence = 1;
    $blockInstance->addField($field);
    $moduleInstance->setEntityIdentifier($field);
    $filter->addField($field, 1);
}


//Storage Type
$fieldName = 'wforder_storagetype';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->sequence = 2;
    $blockInstance->addField($field);
    $filter->addField($field, 2);
    $field->setPicklistValues(array(
        'International', 'Office', 'Hotel', 'Display/Exhibition', 'Electronics', 'Hospital', 'Logistics', 'None', 'Office & Industrial', 'Special Commodities', 'Other'
    ));
}

//First Day In Storage
$fieldName = 'wforder_firstday';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'DATE';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->sequence = 3;
    $blockInstance->addField($field);
    $filter->addField($field, 3);
}

//Comments
$fieldName = 'wforder_comment';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 19;
    $field->typeofdata = 'V~O';
    $field->sequence = 4;
    $blockInstance->addField($field);
}

//Weight
$fieldName = 'wforder_weight';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 2;
    $field->typeofdata = 'I~O';
    $field->sequence = 5;
    $blockInstance->addField($field);
    $filter->addField($field, 4);
}

//Consignee
$fieldName = 'wforder_consignee';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 6;
    $blockInstance->addField($field);
    $filter->addField($field, 5);
}

// discount
$fieldName = 'wforder_discount';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 71;
    $field->typeofdata = 'N~O';
    $field->sequence = 7;
    $blockInstance->addField($field);
    $filter->addField($field, 6);
}

//Days Authorized
$fieldName = 'wforder_days_authorized';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 2;
    $field->typeofdata = 'I~O';
    $field->sequence = 8;
    $blockInstance->addField($field);
    $filter->addField($field, 7);
}

//Control Number
$fieldName = 'wforder_control_number';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 9;
    $blockInstance->addField($field);
    $filter->addField($field, 8);
}

//Overage Days
$fieldName = 'wforder_overage_days';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 2;
    $field->typeofdata = 'I~O';
    $field->sequence = 10;
    $blockInstance->addField($field);
    $filter->addField($field, 9);
}

//WFAccounts
$fieldName = 'wforder_account';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';
    $field->sequence = 11;
    $blockInstance->addField($field);

    $field->setRelatedModules(array('WFAccounts'));
    $filter1->addField($field, 10);
}

//Valuation Type
$fieldName = 'wforder_valuation_type';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->sequence = 12;
    $blockInstance->addField($field);
    $filter->addField($field, 11);
    $field->setPicklistValues(array(
        'Declared Value', 'Depreciated Value', 'Government Val', 'High Val - No Cert', 'High Value', 'Legal Liability', 'Local Val', 'Opt A - No Deduct', 'Opt B - $250 Deduct', 'Opt C - $500 Deduct', 'Released', 'Replacement Val'
    ));
}

// amount
$fieldName = 'wforder_amount';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 71;
    $field->typeofdata = 'N~O';
    $field->sequence = 13;
    $blockInstance->addField($field);
}

//Unit
$fieldName = 'wforder_unit';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 14;
    $blockInstance->addField($field);
}

// agent
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
    $field->sequence = 15;
    $blockInstance->addField($field);
}

// download
$fieldName = 'wforder_download';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wforders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 56;
    $field->typeofdata = 'C~O';
    $field->sequence = 16;
    $blockInstance->addField($field);
}

// keep active
$fieldName = 'wforder_keep_active';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence = 1 WHERE fieldid = '$field->id'");
    echo "<li> $fieldName have already remove</li><br>";
}
//    else {
//        $field = new Vtiger_Field();
//        $field->label = $fieldLabel;
//        $field->name = $fieldName;
//        $field->table = 'vtiger_wforders';
//        $field->column = $fieldName;
//        $field->columntype = 'VARCHAR(255)';
//        $field->uitype = 56;
//        $field->typeofdata = 'C~O';
//        $field->sequence = 17;
//        $blockInstance->addField($field);
//    }

//LBL_RECORD_UPDATE_INFORMATION
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

// modified time
$fieldName = 'modifiedtime';
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


// assigned user id
$fieldName = 'assigned_user_id';
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
    $field = new Vtiger_Field();
    $field->label = 'LBL_WFORDER_ASSIGNED_TO';
    $field->name = 'assigned_user_id';
    $field->table = 'vtiger_crmentity';
    $field->column = 'smownerid';
    $field->uitype = 53;
    $field->typeofdata = 'V~M';
    $field->displaytype = 2;
    $blockInstance2->addField($field);
}

// created by
$fieldName = 'createdby';
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
    $field = new Vtiger_Field();
    $field->label = 'LBL_WFORDER_CREATEDBY';
    $field->name = 'createdby';
    $field->table = 'vtiger_crmentity';
    $field->column = 'smownerid';
    $field->uitype = 52;
    $field->typeofdata = 'V~O';
    $field->displaytype = 2;
    $blockInstance2->addField($field);
}


// tracker update
$tableid = $moduleInstance->getId();
$sql = "SELECT * FROM `vtiger_modtracker_tabs` WHERE `vtiger_modtracker_tabs`.`tabid` = ?";
$result = $adb->pquery($sql, array($tableid));
if ($adb->num_rows($result) == 0) {
    $adb->pquery("insert into `vtiger_modtracker_tabs` ( `visible`, `tabid`) values (?, ?)", array('1', $tableid));
}
