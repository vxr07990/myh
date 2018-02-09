<?php

class PushNotifications_InVanlineManagerRelation_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $pnModuleModel = Vtiger_Module_Model::getInstance('PushNotifications');
        $vmModuleModel = Vtiger_Module_Model::getInstance('VanlineManager');
        $vanlineManagerId = $request->get('record');
        $userModel = Users_Record_Model::getCurrentUserModel();
        $userId = $userModel->getId();
        $isAdmin = $userModel->isAdminUser();
        $db = PearDatabase::getInstance();
        $sql = "SELECT pushnotificationsid, notification_no, smownerid, createdtime, message FROM `vtiger_pushnotifications`
                JOIN `vtiger_crmentity` ON pushnotificationsid=crmid
                WHERE agentid=?";
        $result = $db->pquery($sql, [$vanlineManagerId]);

        $addButton    =
            ($userModel->isVanLineUser() || $isAdmin)
                ?'<div class="btn-group"><button type="button" class="btn addButton" data-name="vanline_id" data-url="index.php?module=PushNotifications&view=Edit&sourceModule=VanlineManager&sourceRecord='.
                 $vanlineManagerId.
                 '&relationOperation=true&vanline_id='.
                 $vanlineManagerId.
                 '" name="addButton"><i class="icon-plus icon-white"></i>&nbsp;<strong>Add Notification</strong></button></div>':'';

        $html  = '<div class="relatedContainer">'
                .'<input type="hidden" name="relatedModuleName" class="relatedModuleName" value="AgentManager" />'
                .'<div class="relatedHeader">'
                .'<div class="btn-toolbar row-fluid">'
                .'<div class="span6">'
                .$addButton
                .'&nbsp;</div>'
                .'</div>'
                .'</div>'
                .'<div class="relatedContents contents-bottomscroll">'
                .'<div class="bottomscroll-div">'
                .'<table class="table table-bordered listViewEntriesTable">'
                .'<thead><tr class="listViewHeaders">'
                .'<th nowrap="" style="width:15%">'.vtranslate('LBL_PUSHNOTIFICATIONS_NOTIFICATIONNUMBER', 'PushNotifications').'&nbsp;&nbsp;</th>'
                .'<th nowrap="" style="width:15%">'.vtranslate('LBL_PUSHNOTIFICATIONS_ASSIGNEDUSER', 'PushNotifications').'&nbsp;&nbsp;</th>'
                .'<th nowrap="" style="width:15%">'.vtranslate('LBL_PUSHNOTIFICATIONS_CREATEDTIME', 'PushNotifications').'&nbsp;&nbsp;</th>'
                .'<th nowrap="" style="width:55%">'.vtranslate('LBL_PUSHNOTIFICATIONS_MESSAGE', 'PushNotifications').'&nbsp;&nbsp;</th>'
                .'</tr></thead>'
                .'<tbody>';

        while ($row =& $result->fetchRow()) {
            $recordModel = Vtiger_Record_Model::getInstanceById($row['pushnotificationsid'], 'PushNotifications');
            $html .= '<tr class="listViewEntries" data-id="'.$row['pushnotificationsid'].'" data-recordurl="index.php?module=PushNotifications&view=Detail&record='.$row['pushnotificationsid'].'">';
            $html .= '<td>'.$row['notification_no'].'</td>';
            $html .= '<td>'.$recordModel->getDisplayValue('assigned_user_id').'</td>';
            $html .= '<td>'.$row['createdtime'].'</td>';
            $html .= '<td>'.$row['message'].'</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div></div></div>';

        return $html;
    }
}
