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
error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

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

echo "<h4>Update Picklist values of Business Line EST fields</h4><br>";
$estimatesModuleModel = Vtiger_Module_Model::getInstance('Estimates');
if ($estimatesModuleModel) {
    $fieldModel1 = Vtiger_Field_Model::getInstance('business_line_est', $estimatesModuleModel);
    if ($fieldModel1) {
        $fieldModel1->setPicklistValues(array('Local Move', 'Intrastate Move', 'Interstate Move'));
    }

    $block = Vtiger_Block::getInstance("LBL_QUOTE_INFORMATION", $estimatesModuleModel);
    if ($block) {
        echo "<h4>Create new Business Line EST field</h4><br>";
//Mailing Address 1
        $field = Vtiger_Field::getInstance('business_line_est2', $estimatesModuleModel);
        if ($field) {
            echo "<br> The business_line_est2 field already exists in Estimates <br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET summaryfield=0 WHERE fieldid=".$field->id);
        } else {
            $field = new Vtiger_Field();
            $field->label = 'LBL_QUOTES_BUSINESSLINE_2';
            $field->name = 'business_line_est2';
            $field->table = 'vtiger_quotescf';
            $field->column = 'business_line_est2';
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


$ActualsModuleModel = Vtiger_Module_Model::getInstance('Actuals');
if ($ActualsModuleModel) {
    $fieldModel1 = Vtiger_Field_Model::getInstance('business_line_est', $ActualsModuleModel);
    if ($fieldModel1) {
        $fieldModel1->setPicklistValues(array('Local Move', 'Intrastate Move', 'Interstate Move'));
    }

    $block = Vtiger_Block::getInstance("LBL_QUOTE_INFORMATION", $ActualsModuleModel);
    if ($block) {
        echo "<h4>Create new Business Line EST field</h4><br>";
//Mailing Address 1
        $field = Vtiger_Field::getInstance('business_line_est2', $ActualsModuleModel);
        if ($field) {
            echo "<br> The business_line_est2 field already exists in Actuals <br>";
            Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET summaryfield=0 WHERE fieldid=".$field->id);
        } else {
            $field = new Vtiger_Field();
            $field->label = 'LBL_QUOTES_BUSINESSLINE_2';
            $field->name = 'business_line_est2';
            $field->table = 'vtiger_quotescf';
            $field->column = 'business_line_est2';
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