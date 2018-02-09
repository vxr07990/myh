<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Leads_LeadsByStatus_Dashboard extends Vtiger_IndexAjax_View
{
    public function getSearchParams($value, $assignedto, $dates)
    {
        $listSearchParams = array();
        $conditions = array(array('leadstatus','e',$value));
        if ($assignedto != '') {
            array_push($conditions, array('assigned_user_id', 'e', getUserFullName($assignedto)));
        }
        if (!empty($dates)) {
            array_push($conditions, array('createdtime', 'bw', $dates['start'].' 00:00:00,'.$dates['end'].' 23:59:59'));
        }
        $listSearchParams[] = $conditions;
        return '&search_params='. json_encode($listSearchParams);
    }

    /**
     * @param int $userId
     * @return int[] A collection of user group IDs.
     */
    private static function _getUserGroupIds($userId)
    {
        $userGroupsObj = new GetUserGroups();
        $userGroupsObj->getAllUserGroups($userId);

        return $userGroupsObj->user_groups;
    }

    /**
     * @param  int[] $groupIds
     * @param  string[] $users
     * @return string[]
     */
    private static function _getUsersInSharedGroups(array $groupIds, array $users)
    {
        $filteredUsers = array();

        foreach ($users as $id => $user) {
            $userGroupIds = self::_getUserGroupIds($id);
            $sharedGroupIds = array_intersect($groupIds, $userGroupIds);

            if (!empty($sharedGroupIds)) {
                $filteredUsers[$id] = trim($user);
            }
        }

        return $filteredUsers;
    }

    /**
     * @param  Users_Record_Model $user
     * @return string[]
     */
    private static function _getAccessibleUsers(Users_Record_Model $user)
    {
        $accessibleUsers = $user->getAccessibleUsersForModule('Leads');
        $currentUserGroups = self::_getUserGroupIds($user->getId());

        return self::_getUsersInSharedGroups($currentUserGroups, $accessibleUsers);
    }

    /**
     * @param  int $userId
     * @param  string[] $users
     * @return string[] A single key/value pair containing the user's ID and name.
     */
    private static function _getUserFromUsersCollection($userId, array $users)
    {
        $userName = $users[$userId];

        return array($userId => $userName);
    }

    public function process(Vtiger_Request $request)
    {
        file_put_contents('logs/devLog.log', "\n Request : ".print_r($request, true), FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n Debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //$createdTime = $request->get('createdtime');
        $currentUser = Users_Record_Model::getCurrentUserModel();
        //$linkId = $request->get('linkid');
        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);

        $linkId = $request->get('linkid');
        //$data = $request->get('data');

        $createdTime = $request->get('createdtime');
        
        //Date conversion from user to database format
        if (!empty($createdTime)) {
            $dates['start'] = Vtiger_Date_UIType::getDBInsertedValue($createdTime['start']);
            $dates['end'] = Vtiger_Date_UIType::getDBInsertedValue($createdTime['end']);
        }

        $accessibleUsers = $currentUser->getAccessibleUsers();
        //file_put_contents('logs/devLog.log', "\n AccessibleUsers : ".print_r($accessibleUsers, true), FILE_APPEND);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $data = $moduleModel->getLeadsByStatus($request->get('smownerid'), $dates);
        $listViewUrl = $moduleModel->getListViewUrl();

        if (empty($data)) {
            $accessibleUsers = self::_getUserFromUsersCollection($currentUser->getId(), $accessibleUsers);
        }

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['links'] = $listViewUrl . $this->getSearchParams($data[$i][1], $currentUser->getGroupId(), $dates);
        }

        $widget = Vtiger_Widget_Model::getInstance($linkId, $currentUser->getId());

        $viewer->assign('CURRENTUSER', $currentUser);
        $viewer->assign('DATA', $data);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('WIDGET', $widget);

        $accessibleUsers = $currentUser->getAccessibleUsersForModule('Leads');
        $viewer->assign('ACCESSIBLE_USERS', $accessibleUsers);

        $content = $request->get('content');
        if (!empty($content)) {
            $viewer->view('dashboards/DashBoardWidgetContents.tpl', $moduleName);
        } else {
            $viewer->view('dashboards/LeadsByStatus.tpl', $moduleName);
        }
    }
}
