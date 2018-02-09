<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
require_once 'include/events/VTEventHandler.inc';

class Vtiger_RecordLabelUpdater_Handler extends VTEventHandler
{
    public function handleEvent($eventName, $data)
    {
        global $adb;

        if ($eventName == 'vtiger.entity.aftersave') {
            $module = $data->getModuleName();
            if ($module != "Users" && $module != "QuotingTool" && $module != "Notifications") {
                $labelInfo = getEntityName($module, $data->getId());
                if ($labelInfo) {
                    $label = decode_html($labelInfo[$data->getId()]);
                    if (getenv('INSTANCE_NAME') == 'sirva') {
                        $adb->pquery('UPDATE vtiger_crmentity SET label=?, cf_record_id=? WHERE crmid=?', [$label, $data->getId(), $data->getId()]);
                    } else {
                        $adb->pquery('UPDATE vtiger_crmentity SET label=? WHERE crmid=?', [$label, $data->getId()]);
                    }
                }
            }
        }
    }
}
