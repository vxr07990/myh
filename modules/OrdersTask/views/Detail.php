<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class OrdersTask_Detail_View extends Vtiger_Detail_View
{
    protected $record = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);

            return;
        }
        $currentUserModel   = Users_Record_Model::getCurrentUserModel();
        $moduleModelSummary = Vtiger_Module_Model::getInstance('OrdersTask');
        if ($currentUserModel->get('default_record_view') === 'Summary' && $moduleModelSummary->isSummaryViewSupported()) {
            echo $this->showModuleBasicView($request);
        } else {
            echo $this->showModuleDetailView($request);
        }
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
        $moduleModel = $recordModel->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $result = $adb->pquery("SELECT ts.*, CONCAT(name ,' ', employee_lastname) as employee_name,emprole_desc
                            FROM vtiger_timesheets ts 
                            INNER JOIN vtiger_crmentity cr ON ts.timesheetsid = cr.crmid
                            INNER JOIN vtiger_employees emp ON ts.employee_id = emp.employeesid
                            INNER JOIN vtiger_crmentity cr2 ON ts.employee_id = cr2.crmid
                            INNER JOIN vtiger_employeeroles er ON ts.timesheet_personnelroleid = er.employeerolesid
                             WHERE ordertask_id = ? AND cr.deleted = 0 AND cr2.deleted = 0",
                               [$recordId]);
        if ($adb->num_rows($result) > 0) {
            while ($arr = $adb->fetch_array($result)) {
                $arrayTS[] =
                    ["timesheetsid"      => $arr['timesheetsid'],
                     "timesheet_id"      => $arr['timesheet_id'],
                     "employee_role"     => $arr['emprole_desc'],
                     "employee_name"     => $arr['employee_name'],
                     "actual_start_date" => $arr['actual_start_date'],
                     "actual_start_hour" => $arr['actual_start_hour'],
                     "actual_end_hour"   => $arr['actual_end_hour'],
                     "timeoff"   => $arr['timeoff'],
                     "total_hours"       => $arr['total_hours']];
            }
            $viewer->assign('TieneRelatedTS', 'si');
            $viewer->assign('TimeSheets', $arrayTS);
        } else {
            $viewer->assign('TieneRelatedTS', 'no');
        }
        //Addresses blocks
        $OrdersTaskAddresses = Vtiger_Module_Model::getInstance('OrdersTaskAddresses');
        if($OrdersTaskAddresses && $OrdersTaskAddresses->isActive()){
            $OrdersTaskAddresses->assignValueForOrdersTaskAddresses($viewer,$recordId);
        }
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }
}
