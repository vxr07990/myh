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
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

// remove all data from vtiger_orderspriority
$removeDataQuery = "TRUNCATE vtiger_orderspriority";
$adb->pquery($removeDataQuery);


// pikclist values for business_line
$picklistvalues = array(
    'Van Line',
    'Own Authority',
    'Other Agent Authority'
);

//insert values for table vtiger_orderspriority
foreach ($picklistvalues as $keySort => $itemValue){
    $sqlInsertQuery = "INSERT INTO `vtiger_orderspriority` (`orderspriority`,`sortorderid`,`presence`) VALUES (?,?,?)";
    $adb->pquery($sqlInsertQuery,array($itemValue,$keySort+1,1));
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";