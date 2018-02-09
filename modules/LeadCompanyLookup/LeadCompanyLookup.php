<?php
/* ********************************************************************************
 * The content of this file is subject to the Lead Company Lookup ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once 'vtlib/Vtiger/Module.php';

class LeadCompanyLookup extends CRMEntity {
    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    function vtlib_handler($modulename, $event_type) {
        if($event_type == 'module.postinstall') {
            self::resetValid();
        } else if($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
        } else if($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
        } else if($event_type == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
            self::removeValid();
        } else if($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($event_type == 'module.postupdate') {
        }
    }

    static function resetValid() {
        global $adb;
        $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;",array('LeadCompanyLookup'));
        $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);",array('LeadCompanyLookup','1'));
    }
    static function removeValid() {
        global $adb;
        $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;",array('LeadCompanyLookup'));
    }


}
?>
