<?php

/**
 * OT5695: Remove "trips" as a related module from the Trips module.
 */

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

$moduleInst = Vtiger_Module::getInstance('Trips');
$moduleInst->unsetRelatedList($moduleInst, 'Trips', 'get_trips');

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";