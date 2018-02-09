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

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$moduleModel = Vtiger_Module_Model::getInstance('Vehicles');

// Delete duplicate fields
$field=Vtiger_Field_Model::getInstance("cf_costpermile", $moduleModel);
if ($field) {
    $field->delete();
}
$field=Vtiger_Field_Model::getInstance("cf_costperhour", $moduleModel);
if ($field) {
    $field->delete();
}
$field=Vtiger_Field_Model::getInstance("cf_liftgate", $moduleModel);
if ($field) {
    $field->delete();
}


echo '<h2>Create three new fields to the Vehicles Specifications Block of Vehicles Module </h2>';
$blockObject = Vtiger_Block::getInstance('LBL_VEHICLES_SPECS', $moduleModel);
$blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);

$field=Vtiger_Field_Model::getInstance("vehicles_costmile", $moduleModel);
if ($field) {
    echo "<li> the Cost Per Mile already exists on Vehicles module</li><br>";
} else {
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'vehicles_costmile')
        ->set('table', 'vtiger_vehicles')
        ->set('generatedtype', 2)
        ->set('uitype', 71)
        ->set('label', 'LBL_VEHICLES_COSTMILE')
        ->set('typeofdata', 'N~O')
        ->set('displaytype', 1)
        ->set('columntype', "decimal(20,3)");
    $blockModel->addField($fieldModel);
}

$field=Vtiger_Field_Model::getInstance("vehicles_costhour", $moduleModel);
if ($field) {
    echo "<li> the Cost Per Hour already exists on Vehicles module</li><br>";
} else {
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'vehicles_costhour')
        ->set('table', 'vtiger_vehicles')
        ->set('generatedtype', 2)
        ->set('uitype', 71)
        ->set('label', 'LBL_VEHICLES_COSTHOUR')
        ->set('typeofdata', 'N~O')
        ->set('displaytype', 1)
        ->set('columntype', "decimal(20,3)");
    $blockModel->addField($fieldModel);
}

$field=Vtiger_Field_Model::getInstance("vehicles_liftgate", $moduleModel);
if ($field) {
    echo "<li> the Lift Gate already exists on Vehicles module</li><br>";
} else {
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'vehicles_liftgate')
        ->set('table', 'vtiger_vehicles')
        ->set('generatedtype', 2)
        ->set('uitype', 16)
        ->set('label', 'LBL_VEHICLES_LIFTGATE')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 1)
        ->set('columntype', "VARCHAR(50)");
    $blockModel->addField($fieldModel);
    $fieldModel->setPicklistValues(array('Yes', 'No'));
}
echo '<h2>Create three new fields to the Vehicles Specifications Block of Vehicles Module - COMPLETE</h2>';


echo '<h2>Create two new fields to the Vehicle Information Block of Vehicles Module </h2>';
$blockObject = Vtiger_Block::getInstance('LBL_VEHICLES_INFORMATION', $moduleModel);
$blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);

// Delete checkbox fields
$field=Vtiger_Field_Model::getInstance("cf_available_local", $moduleModel);
if ($field) {
    $field->delete();
}
$field=Vtiger_Field_Model::getInstance("cf_available_longdistance", $moduleModel);
if ($field) {
    $field->delete();
}

// Create Picklist fields
$field=Vtiger_Field_Model::getInstance("vehicles_availlocal", $moduleModel);
if ($field) {
    echo "<li> the \"Available for Local\" already exists on Vehicles module</li><br>";
} else {
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'vehicles_availlocal')
        ->set('table', 'vtiger_vehicles')
        ->set('generatedtype', 2)
        ->set('uitype', 16)
        ->set('label', 'LBL_VEHICLES_AVAILLOCAL')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 1)
        ->set('columntype', "VARCHAR(50)");
    $blockModel->addField($fieldModel);
    $fieldModel->setPicklistValues(array('Yes', 'No'));
}

$field=Vtiger_Field_Model::getInstance("vehicles_availinter", $moduleModel);
if ($field) {
    echo "<li> the \"Available for Long Distance\" already exists on Vehicles module</li><br>";
} else {
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'vehicles_availinter')
        ->set('table', 'vtiger_vehicles')
        ->set('generatedtype', 2)
        ->set('uitype', 16)
        ->set('label', 'LBL_VEHICLES_AVAILINTER')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 1)
        ->set('columntype', "VARCHAR(50)");
    $blockModel->addField($fieldModel);
    $fieldModel->setPicklistValues(array('Yes', 'No'));
}
echo '<h2>Create two new fields to the Vehicle Information Block of Vehicles Module - COMPLETE</h2>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";