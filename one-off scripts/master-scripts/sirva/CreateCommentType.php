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

echo '<h2>Create Custom field "Type" on Comments module</h2>';
$moduleModel = Vtiger_Module_Model::getInstance('ModComments');
$field=Vtiger_Field_Model::getInstance("cf_comment_type", $moduleModel);
if ($field) {
    echo '<li> the "Type" already exists on Comments module</li><br>';
} else {
    $blockObject = Vtiger_Block::getInstance('LBL_MODCOMMENTS_INFORMATION', $moduleModel);
    $blockModel = Vtiger_Block_Model::getInstanceFromBlockObject($blockObject);
    $fieldModel = new Vtiger_Field_Model();
    $fieldModel->set('name', 'cf_comment_type')
        ->set('table', 'vtiger_modcomments')
        ->set('generatedtype', 2)
        ->set('uitype', 16)
        ->set('label', 'Type')
        ->set('typeofdata', 'V~O')
        ->set('quickcreate', 1)
        ->set('displaytype', 1)
        ->set('columntype', "varchar(3)");
    $blockModel->addField($fieldModel);
    $fieldModel->setPicklistValues(array('0', '1'));
}

// Get related field id
$relatedToFieldResult = $adb->pquery('SELECT fieldid FROM vtiger_field WHERE fieldname = ? AND tabid = ?',
    array('related_to', $moduleModel->getId()));
$fieldId = $adb->query_result($relatedToFieldResult, 0, 'fieldid');
echo '<h3>Enable Comments module on "Surveys" module.</h3>';
$relatedModuleResult = $adb->pquery('SELECT relmodule FROM vtiger_fieldmodulerel WHERE fieldid = ? AND relmodule = ?', array($fieldId, 'Cubesheets'));
$rows = $adb->num_rows($relatedModuleResult);
if ($rows >0) {
    echo '<li> the Comments is enabled on Surveys module</li><br>';
} else {
    // add comment block to Surveys module
    $adb->pquery("insert into `vtiger_fieldmodulerel` ( `relmodule`, `module`, `fieldid`) values (?, ?, ?);", array('Cubesheets', 'ModComments', $fieldId));
}

echo '<h3>Enable Comments module on "Estimates" module.</h3>';
$relatedModuleResult = $adb->pquery('SELECT relmodule FROM vtiger_fieldmodulerel WHERE fieldid = ? AND relmodule = ?', array($fieldId, 'Estimates'));
$rows = $adb->num_rows($relatedModuleResult);
if ($rows >0) {
    echo '<li> the Comments is enabled on Estimates module</li><br>';
} else {
    // add comment block to Estimates module
    $adb->pquery("insert into `vtiger_fieldmodulerel` ( `relmodule`, `module`, `fieldid`) values (?, ?, ?);", array('Estimates', 'ModComments', $fieldId));
}

echo '<h2>Create Custom field "Type" on Comments module - SUCCESS</h2>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";