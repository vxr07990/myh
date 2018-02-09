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

$module = Vtiger_Module_Model::getInstance('WFWarehouses');
$field = Vtiger_Field_Model::getInstance('agent',$module);

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `presence` = 1 WHERE `fieldid` = $field->id");

$blocks = ['LBL_WFWAREHOUSE_INFORMATION' =>
               [
                   'code' => 1,
                   'wfwarehouse_status' => 2,
                   'name' => 3,
                   'translation' => 4,
                   'address' => 5,
                   'address2' => 6,
                   'city' =>7,
                   'state' => 8,
                   'country' => 9,
                   'postal_code' => 10,
                   'square_footage' => 11,
                   'license_level' => 12,
                   'agentid' => 13,
                   'assigned_user_id' => 14,
               ],
        ];
foreach($blocks as $blockLabel=>$fields) {
    $blockInstance = Vtiger_Block::getInstance($blockLabel, $module);
    if($blockInstance) {
        foreach($fields as $fieldName=>$seq) {
            $field = Vtiger_Field_Model::getInstance($fieldName, $module);
            if($field) {
                Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `block` = $blockInstance->id, `sequence` = $seq WHERE `fieldid` = $field->id");
            }
        }
    }
}
