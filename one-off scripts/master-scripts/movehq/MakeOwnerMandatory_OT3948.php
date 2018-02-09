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

$tabid = getTabid('Tariffs');
if($tabid){
    $query = $adb->pquery("SELECT * FROM vtiger_field WHERE fieldname = ?
								   AND tabid=?", array('agentid',$tabid));
    if($adb->num_rows($query) > 0){
        $adb->pquery("UPDATE `vtiger_field` SET typeofdata= ? WHERE (fieldname= ?)",array('I~M','agentid'));
        echo "<br> Set Mandatory field agentid for module Tariffs SUCCESS <br>";
    }
}




print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";