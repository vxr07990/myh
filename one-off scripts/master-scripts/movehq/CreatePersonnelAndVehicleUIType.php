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

$rsCheck1 = $adb->pquery("SELECT * FROM vtiger_ws_fieldtype WHERE fieldtype = 'personnelpicklist' AND uitype=1991", array());
if ($adb->num_rows($rsCheck1) == 0) {
    $adb->pquery("INSERT INTO vtiger_ws_fieldtype(fieldtype,uitype) VALUES('personnelpicklist',1991)", array());
}

$rsCheck2 = $adb->pquery("SELECT * FROM vtiger_ws_fieldtype WHERE fieldtype = 'vehiclepicklist' AND uitype=1992", array());
if ($adb->num_rows($rsCheck2) == 0) {
    $adb->pquery("INSERT INTO vtiger_ws_fieldtype(fieldtype,uitype) VALUES('vehiclepicklist',1992)", array());
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";