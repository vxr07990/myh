<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: ".__FILE__."<br />\n\e[0m";

        return;
    }
}

print "\e[32mRUNNING: ".__FILE__."<br />\n\e[0m";
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$module = Vtiger_Module_Model::getInstance('WFActivityCodes');

if($module) {
  $field = Vtiger_Field::getInstance('shortdescription',$module);
  if($field) {
    $module->setEntityIdentifier($field);
  }
}
