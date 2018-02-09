<?php

/*
 * OT5703: Change the entity identifier of the header on the ZoneAdmin module as well
 * as hide the zoneadmin_id field since it is not needed.
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

$moduleInst = Vtiger_Module::getInstance('ZoneAdmin');
if (!$moduleInst) {
    return;
}

$zoneTypeField = Vtiger_Field::getInstance('za_zone', $moduleInst);
$zoneIdField = Vtiger_Field::getInstance('zoneadmin_id', $moduleInst);
if (!$zoneIdField || !$zoneTypeField) {
    return;
}

// Unset the current entity identifier
$moduleInst->unsetEntityIdentifier();
// and replace it with the zone type
$moduleInst->setEntityIdentifier($zoneTypeField);

// Set the presence to 1 to HIDE the unneeded zone id field
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET `presence` = 1 WHERE `fieldid` = $zoneIdField->id");

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";