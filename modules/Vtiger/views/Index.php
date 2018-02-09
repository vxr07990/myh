<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
use Carbon\Carbon;
class Vtiger_Index_View extends Vtiger_Basic_View
{
    public function __construct()
    {
        parent::__construct();
    }

    public function checkPermission(Vtiger_Request $request)
    {
        //Return true as WebUI.php is already checking for module permission
        return true;
    }

    /*
    * Takes in the recordStructure and fixes the survey_<x> values to the user set time format.
    */
    public function convertSurveyTimeFormat($structureArray, $recordId = 0) {
        //@TODO: Remove this function and it's usage.
        if (!$recordId) {
            if (
                $this->record &&
                method_exists($this->record, 'getId')
            ) {
                $recordId = $this->record->getId();
            }
        }
        //This.. this... rarargh I stab thee in the eye.
        //Convert survey_date, survey_time, and survey_end_time to current user's time zone
        foreach ($structureArray as $blockName => $blockFields) {
            // need to get this before it is converted
            if ($blockFields['survey_time']) {
                $timeComponent = Vtiger_Time_UIType::getTimeValueWithSeconds($blockFields['survey_time']->get('fieldvalue'));
            }
            foreach ($blockFields as $fieldNameTest => $fieldModelTest) {
                $fieldValue = $fieldModelTest->get('fieldvalue');
                if (!empty($fieldValue)) {
                    if ($fieldNameTest == 'survey_time' || $fieldNameTest == 'survey_end_time') {
                        if (preg_match('/[ap]m/i', $fieldValue, $matches)) {
                            //@NOTE, The survey_time can come in with the AM/PM on it, if it's passed from quick create, so fix it.
                            $time24value = $this->reverseSurveyTime($fieldValue, strtolower($matches[0]), $blockFields['timefield_'.$fieldNameTest]);
                            if ($time24value) {
                                $fieldModelTest->set('fieldvalue', $time24value);
                            } else {
                                $fieldModelTest->set('fieldvalue', date('H:i:s'));
                            }
                        }
                    } else if ($fieldNameTest == 'survey_date') {
                        //timeComponent can be empty. although this does assume it's a valid value.
                        $timeZone = getFieldTimeZoneValue($fieldNameTest, $recordId);
                        if ($timeZone) {
                            $carbonTime = Carbon::createFromFormat('Y-m-d H:i:s', $fieldValue.' '.$timeComponent, DateTimeField::getDBTimeZone());
                            $carbonTime->setTimezone($timeZone);
                            $fieldModelTest->set('fieldvalue', $carbonTime->format('Y-m-d'));
                        } else {
                            $date = ((!empty($fieldValue))?DateTimeField::convertToUserTimeZone($fieldValue.' '. $timeComponent)->format('Y-m-d'):'');
                            $fieldModelTest->set('fieldvalue', $date);
                        }
                    }
                }
            }
        }
    }

    protected function reverseSurveyTime($requestTimeValue, $AMorPM, $timeZone = null) {
        $requestTimeValue = preg_replace('/\s+.*/', '', $requestTimeValue);

        list($hours, $minutes, $seconds) = explode(':', $requestTimeValue);

        if (!$seconds) {
            $seconds = '00';
        }

        if ($AMorPM == 'pm') {
            if ($hours != 12) {
                $hours += 12;
            }
        } else {
            if ($hours == 12) {
                $hours = '00';
            }
        }

        $requestTimeValue = "$hours:$minutes:$seconds";

        if (!$timeZone) {
            $user = Users_Record_Model::getCurrentUserModel();
            $timeZone = $user->time_zone;
        }

        try {
            $dbTimeValue = DateTimeField::convertTimeZone($requestTimeValue, DateTimeField::getDBTimeZone(), $timeZone);

            return $dbTimeValue->format('H:i:s');
        } catch (Exception $ex) {
            //If the conversion fails, they will have to enter their time again.
            return date('H:i:s');
        }
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        parent::preProcess($request, false);
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        if (!empty($moduleName) && $moduleName != 'MailManager') {
            $moduleModel         = Vtiger_Module_Model::getInstance($moduleName);
            $currentUser         = Users_Record_Model::getCurrentUserModel();
            $userPrivilegesModel = Users_Privileges_Model::getInstanceById($currentUser->getId());
            $permission          = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
            $viewer->assign('MODULE', $moduleName);
            if (!$permission) {
                $viewer->assign('MESSAGE', 'LBL_PERMISSION_DENIED');
                $viewer->view('OperationNotPermitted.tpl', $moduleName);
                exit;
            }
            $linkParams = ['MODULE' => $moduleName, 'ACTION' => $request->get('view')];
            $linkModels = $moduleModel->getSideBarLinks($linkParams);
            $viewer->assign('QUICK_LINKS', $linkModels);
        }
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('CURRENT_VIEW', $request->get('view'));
        if ($display) {
            $this->preProcessDisplay($request);
        }
    }

    protected function preProcessTplName(Vtiger_Request $request)
    {
        return 'IndexViewPreProcess.tpl';
    }

    //Note : To get the right hook for immediate parent in PHP,
    // specially in case of deep hierarchy
    /*function preProcessParentTplName(Vtiger_Request $request) {
        return parent::preProcessTplName($request);
    }*/
    public function postProcess(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $viewer     = $this->getViewer($request);
        $viewer->view('IndexPostProcess.tpl', $moduleName);
        parent::postProcess($request);
    }

    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $viewer     = $this->getViewer($request);
        $viewer->view('Index.tpl', $moduleName);
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $jsFileNames           = [
            'modules.Vtiger.resources.Vtiger',
            "modules.$moduleName.resources.$moduleName",
            "modules.$moduleName.resources.Common",
            "modules.$moduleName.resources.Edit",
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

    /**
     * Get Permission Level of the currently logged in user as well as some other helpful information pertaining to
     * permissions such as depth and role name
     * @return array of permission information
     */
    public function getPermissionLevel()
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*
        $db            = PearDatabase::getInstance();
        $userModel     = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();
        $sql           = "SELECT * FROM `vtiger_user2role` JOIN `vtiger_role` ON `vtiger_user2role`.roleid=`vtiger_role`.roleid WHERE userid=?";
        $result        = $db->pquery($sql, [$currentUserId]);
        $row           = $result->fetchRow();
        $role          = $row['rolename'];
        $depth         = $row['depth'];
        if ($currentUserId == 1) {
            return ["PermissionLevel" => "IGCAdmin", "Role" => $role, "Depth" => $depth];
        }
        if ($userModel->isAdminUser()) {
            return ["PermissionLevel" => "SysAdmin", "Role" => $role, "Depth" => $depth];
        }
        $vanlines = [];
        $sql      = "SELECT vanlineid, is_parent FROM `vtiger_users2vanline` JOIN `vtiger_vanlinemanager` ON vanlineid=vanlinemanagerid WHERE userid=?";
        $result   = $db->pquery($sql, [$currentUserId]);
        while ($row =& $result->fetchRow()) {
            if ($row['is_parent'] == 1) {
                return ["PermissionLevel" => "ParentVanline", "for" => $row['vanlineid'], "Role" => $role, "Depth" => $depth];
            } else {
                $vanlines[] = $row['vanlineid'];
            }
        }
        if (count($vanlines) > 0) {
            return ["PermissionLevel" => "Vanline", "for" => $vanlines, "Role" => $role, "Depth" => $depth];
        }
        $agents = [];
        $sql    = "SELECT `vtiger_agentmanager`.agentmanagerid FROM `vtiger_user2agency` JOIN `vtiger_agentmanager`
                ON `vtiger_user2agency`.agency_code=`vtiger_agentmanager`.agentmanagerid WHERE userid=?";
        $result = $db->pquery($sql, [$currentUserId]);
        while ($row =& $result->fetchRow()) {
            $agents[] = $row['agentmanagerid'];
        }
        if (count($agents) > 0) {
            return ["PermissionLevel" => "Agent", "for" => $agents, "Role" => $role, "Depth" => $depth];
        }
        */
    }

    public static function getGuestBlocks($hostModule, $getStructure=true)
    {
        $db =& PearDatabase::getInstance();
        $result = $db->pquery("SELECT guestmodule FROM `vtiger_guestmodulerel` WHERE active = 1 AND hostmodule = ? GROUP BY guestmodule", [$hostModule]);
        $guestModules = [];
        while ($row =& $result->fetchRow()) {
            $moduleName = $row['guestmodule'];
            $guestModuleModel  = Vtiger_Module_Model::getInstance($moduleName);
            if(!$guestModuleModel || !$guestModuleModel->isActive())
            {
                continue;
            }
            $data = [];
            $row = $db->pquery("
			SELECT entityidfield, tablename FROM `vtiger_entityname`
			WHERE modulename = ?", [$moduleName]
            )->fetchRow();
            $data['idColumn'] = $row['entityidfield'];
            $data['blockTable'] = $row['tablename'];
            if($getStructure) {
                $structureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($guestModuleModel);
                $blocksFields      = $structureInstance->getStructure();
            }
            $data['guestBlocks'] = $blocksFields;
            //hit the database again to grab the UI type 10 that links the 2 modules
            $data['linkColumn'] = $db->pquery("SELECT fieldname FROM `vtiger_field` INNER JOIN `vtiger_fieldmodulerel` ON `vtiger_field`.fieldid = `vtiger_fieldmodulerel`.fieldid WHERE module = ? AND relmodule = ?", [$moduleName, $hostModule])->fetchRow()['fieldname'];
            $guestModules[$moduleName] = $data;
        }
        return $guestModules;
    }

    public function setViewerForGuestBlocks($moduleName, $record, &$viewer)
    {
        //guest blocks
        $db = PearDatabase::getInstance();
        $result = $db->pquery("SELECT guestmodule,IFNULL(after_block,'_default_') AS after_block FROM `vtiger_guestmodulerel` WHERE active = 1 AND hostmodule = ? GROUP BY guestmodule", [$moduleName]);
        $guestModules = [];
        while ($row =& $result->fetchRow()) {
            $guestModuleModel = Vtiger_Module_Model::getInstance($row['guestmodule']);
            if ($guestModuleModel && $guestModuleModel->isActive()) {
                $guestModuleModel->setPropertiesForBlock($moduleName);
                $guestModuleModel->setViewerForBlock($viewer, $record);
                $guestModules[$row['after_block']][] = $row['guestmodule'];
            }
        }
        $viewer->assign('GUEST_MODULES', $guestModules);
        //end guest blocks
    }

    public function validateRequest(Vtiger_Request $request)
    {
        $request->validateReadAccess();
    }
}
