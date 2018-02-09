<?php
if (function_exists("call_ms_function_ver")) {
    $version = 9;
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
global $adb, $current_user;
$adb = PearDatabase::getInstance();
$current_user = new Users();
$activeAdmin = $current_user->getActiveAdminUser();
$current_user->retrieve_entity_info($activeAdmin->id, 'Users');

// Refer to QuotingTool (Document Designer)
require_once 'modules/QuotingTool/QuotingTool.php';
$quotingTool = new QuotingTool();

$modulename = 'SignedRecord';
$moduleInstance = Vtiger_Module::getInstance($modulename);

if ($moduleInstance) {
    echo "<h2>Signed Documents module already exists</h2><br>";
} else {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = $modulename;
    $moduleInstance->label = "Signed Record";
    $moduleInstance->parent = 'Tools';
    $moduleInstance->isentitytype = true;
    $moduleInstance->version = '1.0';
    $moduleInstance->save();
    // initTables:

    // Create vtiger_signedrecord table
    if (!Vtiger_Utils::CheckTable('vtiger_signedrecord')) {
        $stmt = "CREATE TABLE IF NOT EXISTS `vtiger_signedrecord` (
						  `signedrecordid` int(19) NOT NULL,
						  `signedrecordno` varchar(255) NOT NULL,
						  `signature` text,
						  `signature_name` varchar(255) DEFAULT NULL COMMENT '// name of signature',
						  `signature_date` date DEFAULT NULL COMMENT '// date of signature',
						  `filename` text COMMENT '// file(signed or rejected document) to download',
						  `status` varchar(50) DEFAULT NULL COMMENT '// update this field with \"accept & sign\" or \"decline\" value(based on mapping)',
						  `related_to` int(19) DEFAULT NULL,
						  `signedrecord_type` VARCHAR(50) NULL DEFAULT NULL COMMENT '// *If user signs document - set type to \"Signed\"; *If user OPENS document - set type to \"Opened\"',
						  PRIMARY KEY (`signedrecordid`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $adb->pquery($stmt);
        echo "Created table vtiger_signedrecord success <br>";
    } else {
        echo 'Table vtiger_signedrecord already exists' . PHP_EOL . '<br>';
    }

    // Create vtiger_signedrecordcf table
    if (!Vtiger_Utils::CheckTable('vtiger_signedrecordcf')) {
        $stmt = "CREATE TABLE IF NOT EXISTS `vtiger_signedrecordcf` (
						  `signedrecordid` int(19) NOT NULL,
						  `cf_signature_time` varchar(100) DEFAULT NULL,
						  PRIMARY KEY (`signedrecordid`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
        $adb->pquery($stmt);
        echo "Created table vtiger_signedrecordcf success <br>";
    } else {
        echo 'Table vtiger_signedrecordcf already exists' . PHP_EOL . '<br>';
    }

    // Sharing Access Setup
    $moduleInstance->setDefaultSharing();
    // Webservice Setup
    $moduleInstance->initWebservice();
}

echo '<h3>Create blocks & fields for Signed Documents module</h3>';
echo '<ul>';
$tblEntity = 'vtiger_crmentity';
$tblPrimary = 'vtiger_signedrecord';
$tblCustom = 'vtiger_signedrecordcf';

$blockInstance1 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
if ($blockInstance1) {
    echo "<li>The LBL_CUSTOM_INFORMATION block already exists</li>";
} else {
    $blockInstance1 = new Vtiger_Block();
    $blockInstance1->label = 'LBL_CUSTOM_INFORMATION';
    $moduleInstance->addBlock($blockInstance1);
}

$blockInstance2 = Vtiger_Block::getInstance('LBL_DETAIL', $moduleInstance);
if ($blockInstance2) {
    echo "<li>The LBL_DETAIL block already exists</li>";
} else {
    $blockInstance2 = new Vtiger_Block();
    $blockInstance2->label = 'LBL_DETAIL';
    $moduleInstance->addBlock($blockInstance2);
}

$field1 = 'signedrecordno';
$field1block2 = Vtiger_Field::getInstance($field1, $moduleInstance);
if ($field1block2) {
    echo "<li>The '{$field1}' field already exists</li>";
} else {
    $field1block2 = new Vtiger_Field();
    $field1block2->name = $field1;
    $field1block2->label = 'Signed Record Number';
    $field1block2->table = $tblPrimary;
    $field1block2->column = $field1;
    $field1block2->uitype = 4;
    $field1block2->typeofdata = 'V~M';
    $field1block2->sequence = 0;
    $field1block2->summaryfield = 0;

    $blockInstance2->addField($field1block2);
    // identifier
    $moduleInstance->setEntityIdentifier($field1block2);

    echo "<li>The '{$field1}' field is created</li>";
}

$field2 = 'related_to';
$field2block2 = Vtiger_Field::getInstance($field2, $moduleInstance);
if ($field2block2) {
    echo "<li>The '{$field2}' field already exists</li>";
} else {
    $field2block2 = new Vtiger_Field();
    $field2block2->name = $field2;
    $field2block2->label = 'Related To';
    $field2block2->table = $tblPrimary;
    $field2block2->column = $field2;
    $field2block2->uitype = 10;
    $field2block2->typeofdata = 'V~O';
    $field2block2->sequence = 1;
    $field2block2->summaryfield = 1;

    $blockInstance2->addField($field2block2);

    echo "<li>The '{$field2}' field is created</li>";
}


// related modules
//    $relatedmodules1 = array('Quotes', 'HelpDesk', 'Potentials', 'Contacts', 'Leads', 'Accounts', 'Invoice', 'PurchaseOrder',
//        'SalesOrder', 'ServiceContracts', 'Project', 'ProjectTask', 'ProjectMilestone');
$quotingToolConfigs = $quotingTool->getQuotingToolConfigs();
$relatedmodules1 = $quotingToolConfigs['enableModules'];

$sql = "SELECT * FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=?";
$params = array($field2block2->id, $field2block2->getModuleName());
$result = $adb->pquery($sql, $params);

$dbRelatedmodules1 = array();
$relatedmodules1ToAdd = array();
$relatedmodules1ToRemove = array();

if ($adb->num_rows($result)) {
    while ($row = $adb->fetch_array($result)) {
        $dbRelatedmodules1[] = $row['relmodule'];
    }
}

// add relations
foreach ($relatedmodules1 as $item) {
    if (!in_array($item, $dbRelatedmodules1)) {
        $relatedmodules1ToAdd[] = $item;
    }
}

$field2block2->setRelatedModules($relatedmodules1ToAdd);

// remove relations
foreach ($dbRelatedmodules1 as $item) {
    if (!in_array($item, $relatedmodules1)) {
        $relatedmodules1ToRemove[] = $item;
    }
}

$field2block2->unsetRelatedModules($relatedmodules1ToRemove);



$field3 = 'signedrecord_type';
$field3block2 = Vtiger_Field::getInstance($field3, $moduleInstance);
if ($field3block2) {
    echo "<li>The '{$field3}' field already exists</li>";
} else {
    $field3block2 = new Vtiger_Field();
    $field3block2->name = $field3;
    $field3block2->label = 'Type';
    $field3block2->table = $tblPrimary;
    $field3block2->column = $field3;
    $field3block2->uitype = 16;
    $field3block2->typeofdata = 'V~O';
    $field3block2->sequence = 2;
    $field3block2->summaryfield = 1;

    $blockInstance2->addField($field3block2);
    // picklist values
    $picklistvalues3 = array('Signed', 'Opened');
    $field3block2->setPicklistValues($picklistvalues3);

    echo "<li>The '{$field3}' field is created</li>";
}

$field4 = 'signature';
$field4block2 = Vtiger_Field::getInstance($field4, $moduleInstance);
if ($field4block2) {
    echo "<li>The '{$field4}' field already exists</li>";
} else {
    $field4block2 = new Vtiger_Field();
    $field4block2->name = $field4;
    $field4block2->label = 'Signature';
    $field4block2->table = $tblPrimary;
    $field4block2->column = $field4;
    $field4block2->uitype = 19;
    $field4block2->typeofdata = 'V~O';
    $field4block2->sequence = 3;
    $field4block2->summaryfield = 0;

    $blockInstance2->addField($field4block2);

    echo "<li>The '{$field4}' field is created</li>";
}

$field5 = 'signature_name';
$field5block2 = Vtiger_Field::getInstance($field5, $moduleInstance);
if ($field5block2) {
    echo "<li>The '{$field5}' field already exists</li>";
} else {
    $field5block2 = new Vtiger_Field();
    $field5block2->name = $field5;
    $field5block2->label = 'Signature Name';
    $field5block2->table = $tblPrimary;
    $field5block2->column = $field5;
    $field5block2->uitype = 1;
    $field5block2->typeofdata = 'V~O';
    $field5block2->sequence = 4;
    $field5block2->summaryfield = 1;

    $blockInstance2->addField($field5block2);

    echo "<li>The '{$field5}' field is created</li>";
}

$field6 = 'signature_date';
$field6block2 = Vtiger_Field::getInstance($field6, $moduleInstance);
if ($field6block2) {
    echo "<li>The '{$field6}' field already exists</li>";
} else {
    $field6block2 = new Vtiger_Field();
    $field6block2->name = $field6;
    $field6block2->label = 'Signature Date';
    $field6block2->table = $tblPrimary;
    $field6block2->column = $field6;
    $field6block2->uitype = 5;
    $field6block2->typeofdata = 'D~O';
    $field6block2->sequence = 5;
    $field6block2->summaryfield = 1;

    $blockInstance2->addField($field6block2);

    echo "<li>The '{$field6}' field is created</li>";
}

$field7 = 'filename';
$field7block2 = Vtiger_Field::getInstance($field7, $moduleInstance);
if ($field7block2) {
    echo "<li>The '{$field7}' field already exists</li>";
} else {
    $field7block2 = new Vtiger_Field();
    $field7block2->name = $field7;
    $field7block2->label = 'File Name';
    $field7block2->table = $tblPrimary;
    $field7block2->column = $field7;
    $field7block2->uitype = 28;
    $field7block2->typeofdata = 'V~O';
    $field7block2->sequence = 6;
    $field7block2->summaryfield = 0;

    $blockInstance2->addField($field7block2);

    echo "<li>The '{$field7}' field is created</li>";
}

$field8 = 'status';
$field8block2 = Vtiger_Field::getInstance($field8, $moduleInstance);
if ($field8block2) {
    echo "<li>The '{$field8}' field already exists</li>";
} else {
    $field8block2 = new Vtiger_Field();
    $field8block2->name = $field8;
    $field8block2->label = 'Status';
    $field8block2->table = $tblPrimary;
    $field8block2->column = $field8;
    $field8block2->uitype = 16;
    $field8block2->typeofdata = 'V~O';
    $field8block2->sequence = 7;
    $field1block2->summaryfield = 1;

    $blockInstance2->addField($field8block2);
    // picklist values
    $picklistvalues8 = array('Accept and Sign', 'Decline');
    $field8block2->setPicklistValues($picklistvalues8);

    echo "<li>The '{$field8}' field is created</li>";
}

$field9 = 'createdtime';
$field9block2 = Vtiger_Field::getInstance($field9, $moduleInstance);
if ($field9block2) {
    echo "<li>The '{$field9}' field already exists</li>";
} else {
    $field9block2 = new Vtiger_Field();
    $field9block2->name = $field9;
    $field9block2->label = 'Created Time';
    $field9block2->table = $tblEntity;
    $field9block2->column = $field9;
    $field9block2->uitype = 70;
    $field9block2->typeofdata = 'T~O';
    $field9block2->sequence = 9;
    $field1block2->summaryfield = 0;
    $field1block2->displaytype = 3;

    $blockInstance2->addField($field9block2);

    echo "<li>The '{$field9}' field is created</li>";
}

$field10 = 'modifiedtime';
$field10block2 = Vtiger_Field::getInstance($field10, $moduleInstance);
if ($field10block2) {
    echo "<li>The '{$field10}' field already exists</li>";
} else {
    $field10block2 = new Vtiger_Field();
    $field10block2->name = $field10;
    $field10block2->label = 'Modified Time';
    $field10block2->table = $tblEntity;
    $field10block2->column = $field10;
    $field10block2->uitype = 70;
    $field10block2->typeofdata = 'T~O';
    $field10block2->sequence = 10;
    $field1block2->summaryfield = 0;
    $field1block2->displaytype = 3;

    $blockInstance2->addField($field10block2);

    echo "<li>The '{$field10}' field is created</li>";
}

$field11 = 'assigned_user_id';
$field11block2 = Vtiger_Field::getInstance($field11, $moduleInstance);
if ($field11block2) {
    echo "<li>The '{$field11}' field already exists</li>";
} else {
    $field11block2 = new Vtiger_Field();
    $field11block2->name = $field11;
    $field11block2->label = 'Assigned To';
    $field11block2->table = $tblEntity;
    $field11block2->column = 'smownerid';
    $field11block2->uitype = 70;
    $field11block2->typeofdata = 'V~O';
    $field11block2->sequence = 11;
    $field1block2->summaryfield = 1;

    $blockInstance2->addField($field11block2);

    echo "<li>The '{$field11}' field is created</li>";
}

$field12 = 'agentid';
$field12block2 = Vtiger_Field::getInstance($field12, $moduleInstance);
if ($field12block2) {
    echo "<li>The '{$field12}' field already exists</li>";
} else {
    $field12block2 = new Vtiger_Field();
    $field12block2->name = $field12;
    $field12block2->label = 'Owner';
    $field12block2->table = $tblEntity;
    $field12block2->column = $field12;
    $field12block2->uitype = 1002;
    $field12block2->typeofdata = 'I~O';
    $field12block2->sequence = 12;
    $field1block2->summaryfield = 1;

    $blockInstance2->addField($field12block2);

    echo "<li>The '{$field12}' field is created</li>";
}

echo '</ul>';
echo '<br>Done - Create blocks & fields for Signed Documents module<br>';

// Create default custom filter (mandatory)
echo '<h3>Create default filter for Signed Documents module</h3>';

echo '<ul>';
$filter1 = 'All';
$filter1Instance = Vtiger_Filter::getInstance($filter1, $moduleInstance);
if ($filter1Instance) {
    echo "<li>The '{$filter1}' filter already exists</li>";
} else {
    $filter1Instance = new Vtiger_Filter();
    $filter1Instance->name = $filter1;
    $filter1Instance->isdefault = true;
    $moduleInstance->addFilter($filter1Instance);
    // Add fields to the filter created
    $filter1Instance->addField($field1block2)
        ->addField($field6block2, 1)
        ->addField($field5block2, 2)
        ->addField($field2block2, 3);
    echo "<li>The '{$filter1}' filter is created</li>";
}

echo '</ul>';
echo '<br>Done - default filter for Signed Documents module<br>';

require_once "modules/SignedRecord/SignedRecord.php";
$signedRecord = new SignedRecord();
SignedRecord::addWidgetTo($moduleInstance->name);
$signedRecord->updateWsEntity($modulename);
$signedRecord->createDependentsList($modulename);
$signedRecord->createCustomFields($modulename);
$signedRecord->changeFieldNames($modulename);

echo '<br>Done - Create Signed Documents module<br><br>';

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";