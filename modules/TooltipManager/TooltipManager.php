<?php
/* ********************************************************************************
 * The content of this file is subject to the Tooltip Manager ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once 'vtlib/Vtiger/Module.php';

class TooltipManager extends CRMEntity {
    function vtlib_handler($modulename, $event_type) {
        if($event_type == 'module.postinstall') {
        } else if($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
        } else if($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
        } else if($event_type == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } else if($event_type == 'module.preupdate') {
            // TODO Handle actions when this module is about to be deleted.
        } else if($event_type == 'module.postupdate') {

        }
    }
    
}
?>
