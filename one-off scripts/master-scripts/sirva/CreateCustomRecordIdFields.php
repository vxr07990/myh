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

/**
 * Created by PhpStorm.
 * User: dinhnguyen
 * Date: 9/19/16
 * Time: 2:19 PM
 */

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

echo '<h2>Create Custom field "Record ID" on Leads, Opportunities, Estimates, Survey Appointments, Accounts, Contacts modules</h2>';
// Create Record Id field for Leads module
$leadModuleModel = Vtiger_Module_Model::getInstance('Leads');
$leadField=Vtiger_Field_Model::getInstance("cf_record_id", $leadModuleModel);
if ($leadField) {
    echo "<li> the Record Id already exists on Leads module</li><br>";
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_LEADS_INFORMATION', $leadModuleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_record_id')
        ->set('table', 'vtiger_crmentity')
        ->set('generatedtype', 1)
        ->set('uitype', 1)
        ->set('label', 'Record ID')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 2)
        ->set('columntype', "int(19)");
    $blockModel->addField($fieldModel);
}

// Create Record Id field for Opportunities module
$moduleModel = Vtiger_Module_Model::getInstance('Potentials');
$field=Vtiger_Field_Model::getInstance("cf_record_id", $moduleModel);
if ($field) {
    echo "<li> the Record Id already exists on Opportunities module</li><br>";
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $moduleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_record_id')
        ->set('table', 'vtiger_crmentity')
        ->set('generatedtype', 1)
        ->set('uitype', 1)
        ->set('label', 'Record ID')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 2)
        ->set('columntype', "int(19)");
    $blockModel->addField($fieldModel);
}


// Create Record Id field for Estimates module
$moduleModel = Vtiger_Module_Model::getInstance('Quotes');
$field=Vtiger_Field_Model::getInstance("cf_record_id", $moduleModel);
if ($field) {
    echo "<li> the Record Id already exists on Estimates module</li><br>";
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $moduleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_record_id')
        ->set('table', 'vtiger_crmentity')
        ->set('generatedtype', 1)
        ->set('uitype', 1)
        ->set('label', 'Record ID')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 2)
        ->set('columntype', "int(19)");
    $blockModel->addField($fieldModel);
}


// Create Record Id field for Survey Appointments module
$moduleModel = Vtiger_Module_Model::getInstance('Surveys');
$field=Vtiger_Field_Model::getInstance("cf_record_id", $moduleModel);
if ($field) {
    echo "<li> the Record Id already exists on Survey Appointments module</li><br>";
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_SURVEYS_INFORMATION', $moduleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_record_id')
        ->set('table', 'vtiger_crmentity')
        ->set('generatedtype', 1)
        ->set('uitype', 1)
        ->set('label', 'Record ID')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 2)
        ->set('columntype', "int(19)");
    $blockModel->addField($fieldModel);
}


// Create Record Id field for Accounts module
$moduleModel = Vtiger_Module_Model::getInstance('Accounts');
$field=Vtiger_Field_Model::getInstance("cf_record_id", $moduleModel);
if ($field) {
    echo "<li> the Record Id already exists on Accounts module</li><br>";
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_ACCOUNT_INFORMATION', $moduleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_record_id')
        ->set('table', 'vtiger_crmentity')
        ->set('generatedtype', 1)
        ->set('uitype', 1)
        ->set('label', 'Record ID')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 2)
        ->set('columntype', "int(19)");
    $blockModel->addField($fieldModel);
}



// Create Record Id field for Contacts module
$moduleModel = Vtiger_Module_Model::getInstance('Contacts');
$field=Vtiger_Field_Model::getInstance("cf_record_id", $moduleModel);
if ($field) {
    echo "<li> the Record Id already exists on Contacts module</li><br>";
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_CONTACT_INFORMATION', $moduleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_record_id')
        ->set('table', 'vtiger_crmentity')
        ->set('generatedtype', 1)
        ->set('uitype', 1)
        ->set('label', 'Record ID')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 2)
        ->set('columntype', "int(19)");
    $blockModel->addField($fieldModel);
}


// Create Record Id field for Opportunities module
$moduleModel = Vtiger_Module_Model::getInstance('Opportunities');
$field=Vtiger_Field_Model::getInstance("cf_record_id", $moduleModel);
if ($field) {
    echo "<li> the Record Id already exists on Opportunities module</li><br>";
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $moduleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_record_id')
        ->set('table', 'vtiger_crmentity')
        ->set('generatedtype', 1)
        ->set('uitype', 1)
        ->set('label', 'Record ID')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 2)
        ->set('columntype', "int(19)");
    $blockModel->addField($fieldModel);
}


// Create Record Id field for Estimates module
$moduleModel = Vtiger_Module_Model::getInstance('Estimates');
$field=Vtiger_Field_Model::getInstance("cf_record_id", $moduleModel);
if ($field) {
    echo "<li> the Record Id already exists on Estimates module</li><br>";
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $moduleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_record_id')
        ->set('table', 'vtiger_crmentity')
        ->set('generatedtype', 1)
        ->set('uitype', 1)
        ->set('label', 'Record ID')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 2)
        ->set('columntype', "int(19)");
    $blockModel->addField($fieldModel);
}

// Create Record Id field for Cubesheets module
$moduleModel = Vtiger_Module_Model::getInstance('Cubesheets');
$field=Vtiger_Field_Model::getInstance("cf_record_id", $moduleModel);
if ($field) {
    echo "<li> the Record Id already exists on Cubesheets module</li><br>";
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_CUBESHEETS_INFORMATION', $moduleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_record_id')
        ->set('table', 'vtiger_crmentity')
        ->set('generatedtype', 1)
        ->set('uitype', 1)
        ->set('label', 'Record ID')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 2)
        ->set('columntype', "int(19)");
    $blockModel->addField($fieldModel);
}

echo "<h3>Update Record ID value of records block already exists</h3><br> \n";
$query="UPDATE vtiger_crmentity SET cf_record_id=crmid;";
$adb->pquery($query);
echo "<h3>Update Record ID complete</h3><br> \n";

echo "SUCCESS";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";