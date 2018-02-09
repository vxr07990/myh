<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Vtiger_SharedCalendarUserList_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('addCalendarView');
        $this->exposeMethod('deleteCalendarView');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        } else {
            global $adb;
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $activeSurveyors = Surveys_Record_Model::getEmployeesUsersId();
            if (
                !getenv('IGC_MOVEHQ') ||
                getenv('INSTANCE_NAME') == 'graebel' ||
                in_array($currentUser->id, $activeSurveyors)
            ) {
                $HTMLResponse = '<label class="checkbox addedCalendars"> <input type="checkbox"  data-calendar-sourcekey="Events33_'
                                . $currentUser->id .
                                '" data-calendar-feed="Events" data-calendar-userid="'
                                . $currentUser->id .
                                '"  checked="checked"> <span class="label" style="text-shadow: none;">Mine</span> </label>';
            }

            if ($tableName = $this->verifySharedCalendarTable($request)) {
                $rs = $adb->pquery("SELECT * from $tableName WHERE userid=?", [$currentUser->id]);
                if ($adb->num_rows($rs) > 0) {
                    while ($row = $adb->fetch_array($rs)) {
                        if (
                            !getenv('IGC_MOVEHQ') ||
                            getenv('INSTANCE_NAME') == 'graebel' ||
                            in_array($row['sharedid'], $activeSurveyors)
                        ) {
                            $HTMLResponse .= '<label class="checkbox addedCalendars">  <input type="checkbox"  data-calendar-sourcekey="Events33_'.
                                             $row['sharedid'].
                                             '" data-calendar-feed="Events" data-calendar-userid="'.
                                             $row['sharedid'].
                                             '" checked="checked"> <span class="label" style="text-shadow: none;">'.
                                             getUserFullName($row['sharedid']).
                                             '</span>&nbsp;<i class="icon-trash cursorPointer actionImage deleteCalendarView" title="Delete Calendar"></i></label>';
                        }
                    }
                }
            }
            $response = new Vtiger_Response();
            $response->setResult($HTMLResponse);
            $response->emit();
        }
    }

    public function addCalendarView(Vtiger_Request $request) {
        global $adb;
        $response = new Vtiger_Response();
        if ($tableName = $this->verifySharedCalendarTable($request)) {
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $color       = $request->get('viewColor');
            $sharedid    = $request->get('viewfieldname');
            $adb->pquery("INSERT INTO $tableName (userid,sharedid,color) VALUES (?,?,?);", [$currentUser->id, $sharedid, $color]);
        } else {
            $response->setError('Failed to delete from shared calendar.', 'Failed to delete from shared calendar.');
        }
        $response->emit();
    }

    public function deleteCalendarView(Vtiger_Request $request) {
        global $adb;
        $response   = new Vtiger_Response();
        if ($tableName = $this->verifySharedCalendarTable($request)) {
            $sharedid    = $request->get('viewfieldname');
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $adb->pquery("DELETE FROM $tableName WHERE (userid=? AND sharedid=?);", [$currentUser->id, $sharedid]);
            $response->setResult(['sharedid' => $sharedid]);
        } else {
            $response->setError('Failed to delete from shared calendar.', 'Failed to delete from shared calendar.');
        }
        $response->emit();
    }

    protected function verifySharedCalendarTable (Vtiger_Request $request) {
        $moduleName = $request->getModule();
        if (!$moduleName) {
            return false;
        }
        $tableName  = strtolower($moduleName).'_added_calendar';
        if (!Vtiger_Utils::CheckTable($tableName)) {
            return false;
        }
        return $tableName;
    }
}
