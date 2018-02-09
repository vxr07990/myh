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

$moduleInstance = Vtiger_Module::getInstance('WFWorkOrders');
if ($moduleInstance) {
    echo "WFWorkorders Module exists<br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "WFWorkOrders";
    $moduleInstance->save();
    $moduleInstance->initTables();
    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();
    // Webservice Setup
    $moduleInstance->initWebservice();

    $isNew = true;
}

if ($isNew){
    $filter = new Vtiger_Filter();
    $filter->name = 'All';
    $filter->isdefault = true;
    $moduleInstance->addFilter($filter);
}

// LBL_WFWORKORDERS_INFORMATION block
$blockInstance = Vtiger_Block::getInstance('LBL_WFWORKORDERS_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<li>The LBL_WFWORKORDERS_INFORMATION block already exists</li><br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_WFWORKORDERS_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

//Account: uitype10 field to WFAccounts
$fieldName = 'wfworkorder_account';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';
    $field->sequence = 1;
    $blockInstance->addField($field);

    $field->setRelatedModules(array('WFAccounts'));
    $moduleInstance->setEntityIdentifier($field);
    $filter->addField($field, 1);
}

//Warehouse: uitype10 field to WFWarehouse
$fieldName = 'wfworkorder_warehouse';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';
    $field->sequence = 2;
    $blockInstance->addField($field);

    $field->setRelatedModules(array('WFWarehouses'));
    $filter->addField($field, 2);
}


//Load Type
$fieldName = 'wfworkorder_loadtype';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->sequence = 3;
    $blockInstance->addField($field);

    $filter->addField($field, 3);
    $field->setPicklistValues(array(
        'Commercial', 'Display/Exhibition', 'Electronics', 'International', 'Office & Industrial', 'Special Commodities', 'Other'
    ));
}

//Status / History
$fieldName = 'wfworkorder_status_history';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->sequence = 4;
    $blockInstance->addField($field);
    $filter->addField($field, 4);
    $field->setPicklistValues(array(
        'Created', 'Denied', 'Waiting', 'Approve', 'Submitted', 'Processing', 'Completed', 'Closed'
    ));
}

//    Order # : uitype10 to WFOrders
$fieldName = 'wfworkorder_order';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';
    $field->sequence = 5;
    $blockInstance->addField($field);

    $field->setRelatedModules(array('WFOrders'));
    $filter->addField($field, 5);
}

//Tag Color
$fieldName = 'wfworkorder_tagcolor';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->sequence = 6;
    $blockInstance->addField($field);
    $filter->addField($field, 6);
    $field->setPicklistValues(array(
        'Blue', 'Green', 'MultiColor', 'None', 'Orange', 'Red', 'White'
    ));
}


//    Download to device: checkbox
$fieldName = 'wfworkorder_download';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 56;
    $field->typeofdata = 'C~O';
    $field->sequence = 7;
    $blockInstance->addField($field);
}



## Additional Information : LBL_ADDITIONAL_INFORMATION
$blockInstance = Vtiger_Block::getInstance('LBL_ADDITIONAL_INFORMATION', $moduleInstance);
if ($blockInstance) {
    echo "<li>The LBL_RECORD_UPDATE_INFORMATION block already exists</li><br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_ADDITIONAL_INFORMATION';
    $moduleInstance->addBlock($blockInstance);
}

//Priority
$fieldName = 'wfworkorder_priority';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->sequence = 1;
    $blockInstance->addField($field);
    $field->setPicklistValues(array(
        'Urgent', 'High', 'Medium', 'Low', 'Orange', 'Red', 'White'
    ));
}

// assigned user id
$fieldName = 'assigned_user_id';
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
    $field = new Vtiger_Field();
    $field->label = 'LBL_WFWORKORDERS_ASSIGNED_TO';
    $field->name = 'assigned_user_id';
    $field->table = 'vtiger_crmentity';
    $field->column = 'smownerid';
    $field->uitype = 53;
    $field->typeofdata = 'V~M';
    $field->sequence = 2;
    $blockInstance->addField($field);
}

//Approved By
$fieldName = 'wfworkorder_approved';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 3;
    $blockInstance->addField($field);
}

//BOL
$fieldName = 'wfworkorder_bol';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 4;
    $blockInstance->addField($field);
}

//PO
$fieldName = 'wfworkorder_po';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 5;
    $blockInstance->addField($field);
}

//Job
$fieldName = 'wfworkorder_job';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 6;
    $blockInstance->addField($field);
}

//Requested Name
$fieldName = 'wfworkorder_request_name';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 7;
    $blockInstance->addField($field);
}

//Requested Name
$fieldName = 'wfworkorder_request_phone';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 2;
    $field->typeofdata = 'V~O';
    $field->sequence = 8;
    $blockInstance->addField($field);
}

//Cost Center: uitype10 field to CostCenters
$fieldName = 'wfworkorder_costcenter';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 10;
    $field->typeofdata = 'V~O';
    $field->sequence = 9;
    $blockInstance->addField($field);

    $field->setRelatedModules(array('CostCenters'));
    $filter->addField($field, 10);
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
    $field->sequence = 10;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_comment';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 19;
    $field->typeofdata = 'V~O';
    $field->sequence = 11;
    $blockInstance->addField($field);
}

//Shipping Address Block
$blockInstance = Vtiger_Block::getInstance('LBL_WFWORKORDERS_SHIPPING_ADDRESS', $moduleInstance);
if ($blockInstance) {
    echo "<li>The LBL_WFWORKORDERS_INFORMATION block already exists</li><br>";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = 'LBL_WFWORKORDERS_SHIPPING_ADDRESS';
    $moduleInstance->addBlock($blockInstance);
}

$fieldName = 'wfworkorder_address_1';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->sequence = 1;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_address_2';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->sequence = 2;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_city';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->sequence = 3;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_state';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->sequence = 4;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_postalcode';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->sequence = 5;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_country';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->sequence = 6;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_firstname';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->sequence = 7;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_lastname';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->sequence = 8;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_phone';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(20)';
    $field->uitype = 11;
    $field->typeofdata = 'V~O';
    $field->sequence = 9;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_fax';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(20)';
    $field->uitype = 11;
    $field->typeofdata = 'V~O';
    $field->sequence = 10;
    $blockInstance->addField($field);
}

$fieldName = 'wfworkorder_email';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(20)';
    $field->uitype = 13;
    $field->typeofdata = 'V~O';
    $field->sequence = 11;
    $blockInstance->addField($field);
}

//Shipping Carrier
$fieldName = 'wfworkorder_shipping_carrier';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->sequence = 12;
    $blockInstance->addField($field);
    $field->setPicklistValues(array());
}

//Tracking
$fieldName = 'wfworkorder_tracking';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 1;
    $field->typeofdata = 'V~O';
    $field->sequence = 13;
    $blockInstance->addField($field);
}


//Weight
$fieldName = 'wfworkorder_weight';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'INT(19)';
    $field->uitype = 2;
    $field->typeofdata = 'I~O';
    $field->sequence = 14;
    $blockInstance->addField($field);
}

//Weight UOM
$fieldName = 'wfworkorder_weight_uom';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'VARCHAR(255)';
    $field->uitype = 16;
    $field->typeofdata = 'V~O';
    $field->sequence = 15;
    $blockInstance->addField($field);
    $field->setPicklistValues(array(
        'lb', 'oz', 'ton', 'g', 'kg'
    ));
}


//Scheduled Delivery
$fieldName = 'wfworkorder_firstday';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if($field)$field->delete();

$fieldName = 'wfworkorder_scheduled';
$fieldLabel = 'LBL_' . strtoupper($fieldName);
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if ($field) {
    echo "<li> $fieldName already exists</li><br>";
} else {
    $field = new Vtiger_Field();
    $field->label = $fieldLabel;
    $field->name = $fieldName;
    $field->table = 'vtiger_wfworkorders';
    $field->column = $fieldName;
    $field->columntype = 'DATE';
    $field->uitype = 5;
    $field->typeofdata = 'D~O';
    $field->sequence = 16;
    $blockInstance->addField($field);
}

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

// created by
$fieldName = 'createdby';
$field = Vtiger_Field::getInstance($fieldName, $moduleInstance);
if (!$field) {
    $field = new Vtiger_Field();
    $field->label = 'LBL_WFWORKORDERS_CREATEDBY';
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

