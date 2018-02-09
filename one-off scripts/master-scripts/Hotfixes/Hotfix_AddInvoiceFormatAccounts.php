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


$moduleName = 'Accounts';
$picklistFieldName = 'invoice_format';
$picklistBlockName = 'LBL_ACCOUNT_INFORMATION';

$field1 = new Vtiger_Field();
$field1->label = 'LBL_ACCOUNTS_INVOICEFORMAT';
$field1->name = $picklistFieldName;
$field1->table = 'vtiger_account';
$field1->column = $picklistFieldName;
$field1->columntype = 'VARCHAR(255)';
$field1->uitype = '16';
$field1->typeofdata = 'V~M';

$picklistOptions = [
    'Bottom Line Invoice',
    'Gross and Net',
    'No discount',
    'Performance Based',
    'Gross Only w/o Remarks',
    'Gross Net and Gross Only',
    'GRSW Invoice',
    'Gross Only Invoice',
    'Permanent Storage Invoice',
    'Invoice with Payment',
    'Project One Line Invoice',
    'Event Item Invoice',
    'Event Total Invoice',
    'JLL Invoice',
    'CBRE invoice',
    'State Farm Invoice',
    'Asurion Invoice​',
];

echo '<h2>Starting - Adding Invoice Format to Accounts Module</h2>';

$module = Vtiger_Module::getInstance($moduleName);
$block = Vtiger_Block::getInstance($picklistBlockName, $module);
$fieldCheck = Vtiger_Field::getInstance($picklistFieldName, $module);

if ($block) {
    echo '<h4>Block exists now add it</h4>';
    if ($fieldCheck) {
        echo "<p>$picklistFieldName Field already exists!</p>";
    } else {
        $block->addField($field1);
        $field1->setPicklistValues($picklistOptions);
        echo "<p>$picklistFieldName has been added!";
    }
} else {
    echo '<h5 style="color: #f00000">Failed to find the block for LBL_ACCOUNT_INFORMATION</h5>';
}

echo '<h2>Ending - Adding invoice format to Accounts Module';

/////////////////////////////////////////////////////////////////

echo '<h2>Starting - Adding Invoice Package Format to Accounts Module</h2>';

$picklistFieldName2 = 'invoice_pkg_format';
$fieldCheck = Vtiger_Field::getInstance($picklistFieldName2, $module);

$picklistOptions2 = [
    'COD HHGs standard',
    'Corporate HHGs standard',
    'Corporate HHGs extended or audited account',
    'MMI HHGs standard',
    'MMI HHGs standard intrastate or local',
    'JLL workspace standard',
    'Military HHGs standard (entered in Syncada/powertrack for payment)',
    'Military HHGS interline',
    'GSA HHGs standard',
    'GSA HHGs WHR',
    'Cartus HHGs standard',
    'Graebel Movers International HHGs standard',
    'HHGs standard – SIRVA',
    'Cummins HHGs standard',
    'Relocation Management Worldwide HHGs standard',
    'Altair  HHGs standard',
    'JP Morgan Chase',
    'Chevron',
    'Chevron  intrastate or local',
    'Siemens Shared Services​​',
];

$field2 = new Vtiger_Field();
$field2->label = 'LBL_ACCOUNTS_INVOICE_PKG_FORMAT';
$field2->name = $picklistFieldName2;
$field2->table = 'vtiger_account';
$field2->column = $picklistFieldName2;
$field2->columntype = 'VARCHAR(255)';
$field2->uitype = '16';
$field2->typeofdata = 'V~M';

if ($block) {
    echo '<h4>Block exists now add it</h4>';
    if ($fieldCheck) {
        echo "<p>$picklistFieldName2 Field already exists!</p>";
    } else {
        $block->addField($field2);
        $field2->setPicklistValues($picklistOptions2);
        echo "<p>$picklistFieldName2 has been added!";
    }
} else {
    echo '<h5 style="color: #f00000">Failed to find the block for LBL_ACCOUNT_INFORMATION</h5>';
}

echo '<h2>Ending - Adding Invoice Package Format to Accounts Module</h2>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";