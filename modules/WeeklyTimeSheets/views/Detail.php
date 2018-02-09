<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class WeeklyTimeSheets_Detail_View extends Vtiger_Detail_View
{
    protected $record = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        echo $this->showModuleDetailView($request);
    }

    /**
     * Function shows the entire detail for the record
     *
     * @param Vtiger_Request $request
     *
     * @return <type>
     */
    public function showModuleDetailView(Vtiger_Request $request)
    {
        global $hiddenBlocksArray, $adb;
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel    = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
            $recordModel->hiddenBlocks = $hiddenBlocks;
        }
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        //remove the fields we will display in a custom table
        unset($structuredValues['LBL_WEEKLYTIMESHEETS_INFORMATION']['monday_hours']);
        unset($structuredValues['LBL_WEEKLYTIMESHEETS_INFORMATION']['tuesday_hours']);
        unset($structuredValues['LBL_WEEKLYTIMESHEETS_INFORMATION']['wednesday_hours']);
        unset($structuredValues['LBL_WEEKLYTIMESHEETS_INFORMATION']['thursday_hours']);
        unset($structuredValues['LBL_WEEKLYTIMESHEETS_INFORMATION']['friday_hours']);
        unset($structuredValues['LBL_WEEKLYTIMESHEETS_INFORMATION']['saturday_hours']);
        unset($structuredValues['LBL_WEEKLYTIMESHEETS_INFORMATION']['sunday_hours']);
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $result =
            $adb->pquery("SELECT monday_hours, tuesday_hours, wednesday_hours, thursday_hours, friday_hours, saturday_hours, sunday_hours FROM vtiger_weeklytimesheets wts INNER JOIN vtiger_crmentity cr ON wts.weeklytimesheetsid = cr.crmid WHERE deleted = 0 AND weeklytimesheetsid = ?",
                         [$recordId]);
        while ($arr = $adb->fetch_array($result)) {
            $arrayWTS[] =
                ["monday_hours"    => $arr['monday_hours'],
                 "tuesday_hours"   => $arr['tuesday_hours'],
                 "wednesday_hours" => $arr['wednesday_hours'],
                 "thursday_hours"  => $arr['thursday_hours'],
                 "friday_hours"    => $arr['friday_hours'],
                 "saturday_hours"  => $arr['saturday_hours'],
                 "sunday_hours"    => $arr['sunday_hours']];
        }
        $viewer->assign('WeeklyTimeSheets', $arrayWTS);

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }
}
