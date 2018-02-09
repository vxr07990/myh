<?php
/* ********************************************************************************
* The content of this file is subject to the Notifications ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */

/**
 * Class Notifications_HeaderIcon_View
 */
class Notifications_HeaderIcon_View extends Vtiger_IndexAjax_View
{

    /**
     * @param Vtiger_Request $request
     */
    function process(Vtiger_Request $request)
    {
        global $adb;

        $rs = $adb->pquery("SELECT `enable` FROM `notifications_settings`;", array());
        $enable = $adb->query_result($rs, 0, 'enable');

        if ($enable != '1') {
            // Break if is disabled
            echo '';
            exit();
        }

        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);

        $currentUser = Users_Record_Model::getCurrentUserModel();
        $notifications = Notifications_Record_Model::getNotificationsByUser($currentUser->getId());
        $calendarDatetimeUIType = new Calendar_Datetime_UIType();

        $items = array();
        /** @var Notifications_Record_Model $n */
        foreach ($notifications as $n) {
            $relatedId = $n->get('related_to');
            if (!$relatedId || !isRecordExists($relatedId)) {
                continue;
            }

            $relatedRecordModel = Vtiger_Record_Model::getInstanceById($relatedId);
            $createdtime = new DateTimeField($n->get('createdtime'));

            $items[] = array(
                'id' => $n->get('notificationid'),
                'notificationno' => $n->get('notificationno'),
                'description' => $n->get('description'),
                'thumbnail' => 'layouts/vlayout/skins/images/summary_Leads.png',
                'createdtime' => $createdtime->getDisplayDateTimeValue($currentUser, true),
                'full_name' => $relatedRecordModel->getDisplayName(),
                'link' => $relatedRecordModel->getDetailViewUrl(),
                'rel_id' => $relatedId,
            );
        }

        $viewer->assign('LISTVIEW_ENTRIES', $items);
        $viewer->assign('LISTVIEW_ENTRIES_COUNT', count($items));

        echo $viewer->view('HeaderIcon.tpl', $moduleName, true);
    }

}