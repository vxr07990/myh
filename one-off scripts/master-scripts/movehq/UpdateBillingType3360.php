<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
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

echo "<h4>Update Picklist values of Billing Type fields</h4><br>";
$estimatesModuleModel = Vtiger_Module_Model::getInstance('Estimates');
Vtiger_Utils::ExecuteQuery("DELETE FROM vtiger_billing_type");
$fieldModel1 = Vtiger_Field_Model::getInstance('billing_type', $estimatesModuleModel);
$fieldModel1->setPicklistValues(array('COD', 'National Account', 'Military', 'GSA'));


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";