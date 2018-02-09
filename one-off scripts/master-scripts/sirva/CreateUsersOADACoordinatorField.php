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

echo '<h2>Create custom field "OA/DA Coordinator" on Users module</h2>';

// Create "OA/DA Coordinator" field for Users module
$moduleModel = Vtiger_Module_Model::getInstance('Users');
$field=Vtiger_Field_Model::getInstance("cf_oa_da_coordinator", $moduleModel);
if ($field) {
    echo "<li> the Record Id already exists on Users module</li><br>";
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_USERLOGIN_ROLE', $moduleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_oa_da_coordinator')
        ->set('table', 'vtiger_users')
        ->set('generatedtype', 1)
        ->set('uitype', 56)
        ->set('label', 'OA/DA Coordinator')
        ->set('typeofdata', 'V~O')
        ->set('displaytype', 1)
        ->set('columntype', "varchar(3)");
    $blockModel->addField($fieldModel);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";