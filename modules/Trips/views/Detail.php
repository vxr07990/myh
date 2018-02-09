<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Trips_Detail_View extends Vtiger_Detail_View
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
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if ($currentUserModel->get('default_record_view') === 'Summary') {
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
        $moduleModel      = $recordModel->getModule();
        $viewer           = $this->getViewer($request);
        $fieldModel = Vtiger_Field_Model::getInstance('orders_otherstatus', Vtiger_Module_Model::getInstance('Orders'));
        $dispatchStatus = $fieldModel->getPicklistValues();
        $viewer->assign('ORDERS_STATUS', $dispatchStatus);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $ordersArray = $recordModel->getRelatedOrders($request);
        $viewer->assign('ORDERS_COUNT', count($ordersArray));
        $viewer->assign('ORDERS_ARRAY', $ordersArray);
        $serviceHoursArray = $this->getRelatedServiceHours($request);
        $viewer->assign('SERVICEHOURS_COUNT', count($serviceHoursArray));
        $viewer->assign('SERVICEHOURS_ARRAY', $serviceHoursArray);
        /*
         * Don't know why this was here, but was firing fatal error cause getTotalHours function doesn't exist and TOTAL_HOURS is not being used in the tpl file
         */
        //$totalHours = $this->getTotalHours($serviceHoursArray);
        //$viewer->assign('TOTAL_HOURS', $totalHours);

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    public function showModuleBasicView($request)
    {
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel          = $this->record->getRecord();
        $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
        $detailViewLinks      = $this->record->getDetailViewLinks($detailViewLinkParams);
        $viewer               = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('MODULE_NAME', $moduleName);
        $recordStrucure   = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();

	$ordersArray = $recordModel->getRelatedOrders();
	$fieldModel = Vtiger_Field_Model::getInstance('orders_otherstatus', Vtiger_Module_Model::getInstance('Orders'));
	$dispatchStatus = $fieldModel->getPicklistValues();
	$viewer->assign('ORDERS_STATUS', $dispatchStatus);
        $viewer->assign('ORDERS_COUNT', count($ordersArray));
        $viewer->assign('ORDERS_ARRAY', $ordersArray);
        $serviceHoursArray = $this->getRelatedServiceHours($request);
        $viewer->assign('SERVICEHOURS_COUNT', count($serviceHoursArray));
        $viewer->assign('SERVICEHOURS_ARRAY', $serviceHoursArray);
        $hoursAvailable = $this->getHoursAvailable($serviceHoursArray);
        $viewer->assign('HOURS_AVAILABLE', $hoursAvailable);
	//OT16508
	$driverCheckInArray = $this->getDriverChechIn($recordId);
	$viewer->assign('DRIVERCHECKIN_COUNT', count($driverCheckInArray));
        $viewer->assign('DRIVERCHECKIN_ARRAY', $driverCheckInArray);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }

	function getRelatedServiceHours(Vtiger_Request $request) {
        $serviceHoursArray = [];
        $db = PearDatabase::getInstance();
        $recordId = $request->get('record');
        $result = $db->pquery("SELECT vtiger_servicehours.* FROM vtiger_servicehours INNER JOIN vtiger_crmentity ON vtiger_servicehours.servicehoursid = vtiger_crmentity.crmid WHERE deleted = 0 AND actual_start_date >= DATE_SUB(NOW(), INTERVAL 6 DAY) AND trips_id = ? ", [$recordId]);
        if ($db->num_rows($result) > 0) {
            while ($arr = $db->fetch_array($result)) {
                if (Users_Privileges_Model::isPermitted('ServiceHours', 'EditView', $arr['servicehoursid']) == 'yes') {
                    $employee = Vtiger_Record_Model::getInstanceById($arr['employee_id'], 'Employees');
                    $serviceHoursArray[] = [
                        'servicehoursid' => $arr['servicehoursid'],
                        'servhours_id' => $arr['servhours_id'],
                        'employee_id' => $arr['employee_id'],
                        'employee' => $employee->get('name') . ' ' . $employee->get('employee_lastname'),
						'actual_start_date' => ($arr['actual_start_date'] != '' ? Vtiger_Date_UIType::getDisplayDateValue($arr['actual_start_date']) : ''),
                        'total_hours' => $arr['total_hours'],
                        'driver_message' => $arr['driver_message'],
                    ];
                }
            }
        }

        return $serviceHoursArray;
    }

	function filterArray($dates) {
        $lastDate = null;

        foreach ($dates as $key => $arr) {
            $date = new DateTime(DateTimeField::convertToDBFormat($arr['actual_start_date']));
            if (null !== $lastDate) {
                $interval = $date->diff($lastDate);
                if ($interval->days !== 1) {
                    unset($dates[$key]);
                    $date = $lastDate; //bugfix if date was 15-08-2016, next date 14-08-2016 must be compared with last from block not to 15-08-2016
                }
            }
            $lastDate = $date;
        }
        return $dates;
    }

	function getHoursAvailable($arr) {
        $totalHours = 70;
        $hoursUsed = 0;
        if (count($arr) > 0) {
        //Sorting array DESC with dates
            foreach ($arr as $key => $row) {
                $date[$key]  = $row['actual_start_date'];
            }
            array_multisort($date, SORT_DESC, $arr);
        //Get first block of consecutive dates
            $consecDates = $this->filterArray($arr);
        //Sum of hours
            foreach ($consecDates as $serviceHours) {
                $hoursUsed += $serviceHours['total_hours'];
            }

            $totalHours = (count($consecDates) === 7) ? 60 : 70;
        }

        return $totalHours-$hoursUsed;
    }
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $jsFileNames           = [
            "modules.Trips.resources.Trips",
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

    public function getDriverChechIn($tripsId)
    {
	$driverCheckInArray = [];
	if ($tripsId != '') {
	    $db = PearDatabase::getInstance();
	    $sql = 'SELECT t.*,c.createdtime,c.modifiedtime FROM vtiger_tripsdrivercheckin t JOIN vtiger_crmentity c ON t.tripsdrivercheckinid = c.crmid WHERE  c.deleted=0 AND t.tripsdrivercheckin_tripsid = ? ORDER BY c.createdtime DESC';
	    $result = $db->pquery($sql, [$tripsId]);
	    if ($result && $db->num_rows($result) > 0) {
		while ($row = $db->fetchByAssoc($result)) {
		    $row[createdtime] = ($row[createdtime] != '' ? Vtiger_Date_UIType::getDisplayDateTimeValue($row[createdtime]) : '');
		    $driverCheckInArray[] = $row;
		}
	    }
	}
	return $driverCheckInArray;
    }
}
