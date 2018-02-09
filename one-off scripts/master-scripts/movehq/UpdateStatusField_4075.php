<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2; // Need to add +1 every time you update that script
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
    global $adb;
    $moduleInstance = Vtiger_Module::getInstance('Employees');
    $field1 = Vtiger_Field::getInstance('employee_status',$moduleInstance);
    if ($field1){
        $arrExistedPicklists = getAllPickListValues('employee_status');
        if(!in_array('Inactive', $arrExistedPicklists)) {
            $field1->setPicklistValues(['Inactive']);
        }
    }
echo "<br>DONE!<br>";

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";