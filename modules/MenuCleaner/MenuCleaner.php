<?php

require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once 'vtlib/Vtiger/Module.php';
require_once('modules/com_vtiger_workflow/include.inc');

class MenuCleaner extends CRMEntity
{

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
     */
    public function vtlib_handler($modulename, $event_type)
    {
        if ($event_type == 'module.postinstall') {
        } elseif ($event_type == 'module.disabled') {
            // TODO Handle actions when this module is disabled.
        } elseif ($event_type == 'module.enabled') {
            // TODO Handle actions when this module is enabled.
        } elseif ($event_type == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } elseif ($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($event_type == 'module.postupdate') {
        }
    }
}
