<?php

class Users_InVanlineManagerRelation_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $vanlineManagerId    = $request->get('record');
        $userModel           = Users_Record_Model::getCurrentUserModel();
        $currentUserModel    = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $vanlineManagerModel = Vtiger_Module_Model::getInstance('VanlineManager');
        $userId              = $userModel->getId();
        $db                  = PearDatabase::getInstance();
        $query               = "SELECT vtiger_users.id, vtiger_users.first_name, vtiger_users.last_name, vtiger_users.email1, vtiger_users.phone_work FROM `vtiger_users`
		JOIN `vtiger_users2vanline` ON `vtiger_users`.id=`vtiger_users2vanline`.userid WHERE `vtiger_users2vanline`.vanlineid=".$id;
        $result              = $db->pquery($query, [$vanlineManagerId]);
        $usersArray          = [];
        for ($i = 0; $i < $db->num_rows($result); $i++) {
            $user['id']    = $db->query_result($result, $i, 'id');
            $user['name']  = $db->query_result($result, $i, 'first_name').' '.$db->query_result($result, $i, 'last_name');
            $user['email'] = $db->query_result($result, $i, 'email1');
            $user['phone'] = $db->query_result($result, $i, 'phone_work');
            array_push($usersArray, $user);
        }
        $selectButton =
            ($userModel->isAdminUser())?'<div class="btn-group"><button type="button" class="btn addButton selectRelation" data-modulename="Users">&nbsp;<strong>Select User(s)</strong></button></div>'
                :'';
        $canAdd       = false;
        $canAdd       = $userModel->isAdminUser();
        $userId       = $userModel->get('id');
        $sql          = "SELECT  `vtiger_role`.depth
				FROM  `vtiger_role` 
				JOIN  `vtiger_user2role` ON  `vtiger_role`.roleid =  `vtiger_user2role`.roleid
				WHERE  `vtiger_user2role`.userid =?";
        $result       = $db->pquery($sql, [$userId]);
        $row          = $result->fetchRow();
        if ($row[0] <= 3) {
            $canAdd = true;
        }
        $addButton =
            $canAdd?'<button type="button" class="btn addButton" data-url="index.php?module=VanlineManager&view=AddVanlineUser&record='.
                    $vanlineManagerId.
                    '&user='.
                    $userId.
                    '" name="addUserButton" id="addUserButton"><i class="icon-plus icon-white"></i>&nbsp;<strong>Add User</strong></button>':'';
        $html      = '<div class="relationContainer">'
                     .'<input type="hidden" name="relatedModuleName" class="relatedModuleName" value="Users" />'
                     .'<div class="relatedHeader">'
                     .'<div class="btn-toolbar row-fluid">'
                     .'<div class="span6">'
                     .$selectButton
                     .'<div class="btn-group">'
                     .$addButton
                     .'</div>'
                     .'&nbsp;</div>'
                     .'</div>'
                     .'</div>'
                     .'<div class="relatedContents contents-bottomscroll">'
                     .'<div class="bottomscroll-div">'
                     .'<table class="table table-bordered listViewEntriesTable">'
                     .'<thead><tr class="listViewHeaders">'
                     .'<th nowrap="">Name&nbsp;&nbsp;</th>'
                     .'<th nowrap="">Email&nbsp;&nbsp;</th>'
                     .'<th colspan="2" nowrap>Office Phone&nbsp;&nbsp;</th>'
                     .'</tr></thead>'
                     .'<tbody>';
        foreach ($usersArray as $user) {
            $html .= '<tr class="listViewEntries" data-id="'.$user['id'].'">';
            $html .= '<td>'.$user['name'].'</td>';
            $html .= '<td>'.$user['email'].'</td>';
            $html .= '<td>'.$user['phone'].'</td>';
            $html .= '<td nowrap class>';
            $html .= '<div class="pull-right actions">';
            $html .= '<span class="actionImages"><a class="relationDelete"><i title="Delete" class="icon-trash alignMiddle"></i></span>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></div></div></div>';

        return $html;
    }
}
