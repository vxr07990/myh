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
$Vtiger_Utils_Log = true;

$picklistvalues = array(
    'HHG - Interstate',
    'HHG - Intrastate',
    'HHG - Local',
    'HHG - International',
    'Electronics - Interstate',
    'Electronics - Intrastate',
    'Electronics - Local',
    'Electronics - International',
    'Display & Exhibits - Interstate',
    'Display & Exhibits - Intrastate',
    'Display & Exhibits - Local',
    'Display & Exhibits - International',
    'General Commodities - Interstate',
    'General Commodities - Intrastate',
    'General Commodities - Local',
    'General Commodities - International',
    'Auto - Interstate',
    'Auto - Intrastate',
    'Auto - Local',
    'Auto - International',
    'Commercial - Interstate',
    'Commercial - Intrastate',
    'Commercial - Local',
    'Commercial - International',
);

echo "<h4>Update Picklist values of Business Line 2 fields</h4><br>";
echo "<h3>Leads</h3>";
$leadModuleModel = Vtiger_Module_Model::getInstance('Leads');
if($leadModuleModel) {
    $fieldModel1 = Vtiger_Field_Model::getInstance('business_line', $leadModuleModel);
    if($fieldModel1) $fieldModel1->setPicklistValues(array('Local Move', 'Intrastate Move', 'Interstate Move'));

    $block = Vtiger_Block::getInstance("LBL_LEADS_INFORMATION", $leadModuleModel);
    if($block) {
        echo "<h4>Create new Business Line 2 field</h4><br>";
        $field = Vtiger_Field::getInstance('business_line2', $leadModuleModel);
        if ($field) {
            echo "<br> The Business Line 2 field already exists in Estimates <br>";
        } else {
            $field = new Vtiger_Field();
            $field->label = 'LBL_LEADS_BUSINESSLINE_2';
            $field->name = 'business_line2';
            $field->table = 'vtiger_leadscf';
            $field->column = 'business_line2';
            $field->columntype = 'varchar(255)';
            $field->uitype = 16;
            $field->typeofdata = 'V~0';
            $field->quickcreate = 0;
            $field->summaryfield = 1;
            $field->sequence = 1;
            $block->addField($field);
            $field->setPicklistValues($picklistvalues);
        }
    }
}

echo "<h3>Opportunities</h3>";
$OpportunitiesModuleModel = Vtiger_Module_Model::getInstance('Opportunities');
if($OpportunitiesModuleModel) {
    $fieldModel1 = Vtiger_Field_Model::getInstance('business_line', $OpportunitiesModuleModel);
    if($fieldModel1) $fieldModel1->setPicklistValues(array('Local Move', 'Intrastate Move', 'Interstate Move'));

    $block = Vtiger_Block::getInstance("LBL_POTENTIALS_INFORMATION", $OpportunitiesModuleModel);
    if($block) {
        echo "<h4>Create new Business Line 2 field</h4><br>";
        $field = Vtiger_Field::getInstance('business_line2', $OpportunitiesModuleModel);
        if ($field) {
            echo "<br> The Business Line 2 field already exists in Estimates <br>";
        } else {
            $field = new Vtiger_Field();
            $field->label = 'LBL_POTENTIALS_BUSINESSLINE_2';
            $field->name = 'business_line2';
            $field->table = 'vtiger_potentialscf';
            $field->column = 'business_line2';
            $field->columntype = 'varchar(255)';
            $field->uitype = 16;
            $field->typeofdata = 'V~0';
            $field->quickcreate = 0;
            $field->summaryfield = 1;
            $field->sequence = 1;
            $block->addField($field);
            $field->setPicklistValues($picklistvalues);
        }
    }
}


echo "<h3>Orders</h3>";
$OrdersModuleModel = Vtiger_Module_Model::getInstance('Orders');
if($OrdersModuleModel) {
    $fieldModel1 = Vtiger_Field_Model::getInstance('business_line', $OrdersModuleModel);
    if($fieldModel1) $fieldModel1->setPicklistValues(array('Local Move', 'Intrastate Move', 'Interstate Move'));

    $block = Vtiger_Block::getInstance("LBL_ORDERS_INFORMATION", $OrdersModuleModel);
    if($block) {
        echo "<h4>Create new Business Line 2 field</h4><br>";
        $field = Vtiger_Field::getInstance('business_line2', $OrdersModuleModel);
        if ($field) {
            echo "<br> The Business Line 2 field already exists in Estimates <br>";
        } else {
            $field = new Vtiger_Field();
            $field->label = 'LBL_ORDERS_BUSINESSLINE_2';
            $field->name = 'business_line2';
            $field->table = 'vtiger_orders';
            $field->column = 'business_line2';
            $field->columntype = 'varchar(255)';
            $field->uitype = 16;
            $field->typeofdata = 'V~0';
            $field->quickcreate = 0;
            $field->summaryfield = 1;
            $field->sequence = 1;
            $block->addField($field);
            $field->setPicklistValues($picklistvalues);
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";