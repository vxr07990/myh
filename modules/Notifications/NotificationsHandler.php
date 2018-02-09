<?php
/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
require_once 'include/events/VTEventHandler.inc';

class NotificationsHandler extends VTEventHandler
{
    /**
     * @param string $eventName
     * @param Vtiger_Record_Model $entityData
     */
    function handleEvent($eventName, $entityData)
    {
        global $current_user;
        $moduleName = $entityData->getModuleName();
        $relatedTo = $entityData->get('related_to');

        // Validate the event target
        if ($moduleName != 'Notifications' || !$relatedTo || $_REQUEST['isNotificationHandler']) {
            return;
        }

        $relatedRecordModel = Vtiger_Record_Model::getInstanceById($entityData->get('related_to'));
        $assignedUserId = $relatedRecordModel->get('assigned_user_id');

        /*// Validate the event target
        if (!$current_user || !$assignedUserId || $current_user->id == $assignedUserId) {
            return;
        }*/

        switch ($eventName) {
            case 'vtiger.entity.aftersave':
                // Flag to mark read
                $_REQUEST['isNotificationHandler'] = true;

                $recordModel = Vtiger_Record_Model::getInstanceById($entityData->getId());
                $recordModel->set('id', $entityData->getId());
                $recordModel->set('mode', 'edit');
                $recordModel->set('assigned_user_id', $assignedUserId);
                $recordModel->save();  // return Id
                break;
            default:
                break;
        }
    }

}