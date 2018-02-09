<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_GetNotifications_Action extends Vtiger_Action_Controller
{
    public function __construct()
    {
        $this->exposeMethod('getReminders');
        $this->exposeMethod('blockReminders');
        $this->exposeMethod('postpone');
        $this->exposeMethod('resetReminders');
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

        if (!$permission) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();

        if (!empty($mode) && $this->isMethodExposed($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function getReminders(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $query = 'SELECT name FROM vtiger_tab WHERE notification_enabled = 1';
        $result = $db->pquery($query, array());
        $response = new Vtiger_Response();
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['block_notifications'])) {
            $_SESSION['block_notifications']=array();
        }
        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications']=array();
        }
        if (!isset($_SESSION['notifications'][$_SERVER["HTTP_REFERER"]])) {
            $_SESSION['notifications'][$_SERVER["HTTP_REFERER"]]=array();
        }


        while ($row =& $result->fetchRow()) {
            require_once 'modules/'.$row['name'].'/models/Module.php';
            $class = $row['name'].'_Module_Model';
            
            $notifications = $class::getReminder();
            if ($notifications!=null) {
                foreach ($notifications as $notifcation) {
                    $notifcation['module']=$row['name'];
                    if (!in_array($notifcation, $_SESSION['notifications'][$_SERVER["HTTP_REFERER"]]) && !in_array($notifcation['module'].$notifcation['id'], $_SESSION['block_notifications'])) {
                        $response->addResult($notifcation);
                        array_push($_SESSION['notifications'][$_SERVER["HTTP_REFERER"]], $notifcation);
                    }
                }
            }
        }
        $response->emit();
    }
    public function blockReminders(Vtiger_Request $request)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['block_notifications'])) {
            $_SESSION['block_notifications']=array();
        }
        if (!in_array($request->get('targetModule').$request->get('id'), $_SESSION['block_notifications'])) {
            array_push($_SESSION['block_notifications'], $request->get('targetModule').$request->get('id'));
        }
    }
    public function resetReminders(Vtiger_Request $request)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['notifications'][$_SERVER["HTTP_REFERER"]]=array();
    }
    public function postpone(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        $module = $request->getModule();
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $module);
        $recordModel->updateReminderStatus(0);
    }
}
