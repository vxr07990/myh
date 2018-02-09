<?php

class Opportunities_AddCalendarView_View extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getSharedUsersList');
    }

    public function getSharedUsersList(Vtiger_Request $request)
    {
        global $adb;
        $viewer = $this->getViewer($request);
        $currentUser = Users_Record_Model::getCurrentUserModel();

        // Get added shared id
        $addedID=array();
        $rs=$adb->pquery("SELECT * from opportunities_added_calendar WHERE userid=?", array($currentUser->id));
        if ($adb->num_rows($rs)>0) {
            while ($row=$adb->fetch_array($rs)) {
                $addedID[]=$row['sharedid'];
            }
        }
        $moduleName = $request->getModule();
        $sharedUsers = Calendar_Module_Model::getSharedUsersOfCurrentUser($currentUser->id);
        $sharedUsersInfo = Calendar_Module_Model::getSharedUsersInfoOfCurrentUser($currentUser->id);
        if(getenv('IGC_MOVEHQ')  && getenv('INSTANCE_NAME') != 'graebel') {
            $activeSurveyors = Surveys_Record_Model::getEmployeesUsersId();
            foreach ($sharedUsers as $sharedUserId => $sharedUserName) {
                if (!in_array($sharedUserId, $activeSurveyors)) {
                    unset($sharedUsers[$sharedUserId]);
                }
            }
        }
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('ADDED_IDS', $addedID);
        $viewer->assign('SHAREDUSERS', $sharedUsers);
        $viewer->assign('SHAREDUSERS_INFO', $sharedUsersInfo);
        $viewer->assign('CURRENTUSER_MODEL', $currentUser);
        $viewer->view('CalendarSharedUsers.tpl', $moduleName);
    }
}
