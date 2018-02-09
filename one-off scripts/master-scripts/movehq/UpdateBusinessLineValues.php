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

echo "<h4>Update Picklist values of Business Line fields</h4><br>";
$estimatesModuleModel = Vtiger_Module_Model::getInstance('Estimates');
Vtiger_Utils::ExecuteQuery("DELETE FROM vtiger_business_line_est");
$fieldModel1 = Vtiger_Field_Model::getInstance('business_line_est', $estimatesModuleModel);
$fieldModel1->setPicklistValues($picklistvalues);


$oppModuleModel = Vtiger_Module_Model::getInstance('Opportunities');
Vtiger_Utils::ExecuteQuery("DELETE FROM vtiger_business_line");
$fieldModel2 = Vtiger_Field_Model::getInstance('business_line', $oppModuleModel);
$fieldModel2->setPicklistValues($picklistvalues);

echo "<h4>Update Picklist values of Business Line fields - Success</h4><br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";