<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */
include_once 'include/fields/DateTimeField.php';

class OrdersTask_LocalDispatchCapacityCalendar_View extends OrdersTask_List_View {

    public function __construct() {
	parent::__construct();
        $this->exposeMethod('ShowResourcesModal');
    }

    public function preProcess(Vtiger_Request $request, $display = true) {
	parent::preProcess($request, false);
	$viewer = $this->getViewer($request);
	$moduleName = $request->getModule();
        $this->viewName = $request->get('viewname');
        if (empty($this->viewName)) {
            //If not view name exits then get it from custom view
            //This can return default view id or view id present in session
            $customView                               = new CustomView();
            $viewid                                   = $customView->getViewId($moduleName);
            $this->viewName                           = $viewid;
        }
	$viewer->assign('CURRENT_USER', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('PAGETITLE', 'Capacity Calendar');
	if ($display) {
	    $this->preProcessDisplay($request);
	}
    }

    public function preProcessTplName(Vtiger_Request $request) {
	return 'CapacityCalendarPreProcess.tpl';
    }

    public function process(Vtiger_Request $request) {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
	$viewer = $this->getViewer($request);
	$moduleName = $request->getModule();
	$moduleModel = Vtiger_Module_Model::getInstance($moduleName);
	$viewer->assign('MODULE_MODEL', $moduleModel);
	$viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
	$viewer->view('CapacityCalendarContents.tpl', $moduleName);
    }

    public function postProcess(Vtiger_Request $request) {
	parent::postProcess($request);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
	$headerScriptInstances = parent::getHeaderScripts($request);
	$jsFileNames = [
	    //"~/libraries/fullcalendar/fullcalendar.js",
	    "~/libraries/jquery/colorpicker/js/colorpicker.js",
	    "modules.OrdersTask.resources.CapacityCalendar",
	    'modules.Vtiger.resources.List',
	    'modules.Vtiger.resources.RelatedList',
	    'modules.CustomView.resources.CustomView',
	    'modules.OrdersTask.resources.CustomView',
	    'modules.OrdersTask.resources.moment',
	    'modules.OrdersTask.resources.fullcalendar',
	];
	$jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
	$headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

	return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request) {
	$headerCssInstances = parent::getHeaderCss($request);
	$cssFileNames = [
	    '~/layouts/vlayout/modules/OrdersTask/resources/fullcalendar.min.css',
	    //'~/libraries/fullcalendar/fullcalendar.css',
	    //'~/libraries/fullcalendar/fullcalendar-bootstrap.css',
	    '~/libraries/jquery/colorpicker/css/colorpicker.css',
	];
	$cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
	$headerCssInstances = array_merge($headerCssInstances, $cssInstances);

	return $headerCssInstances;
    }
    
    public function ShowResourcesModal(Vtiger_Request $request)
    {
        $getEmployees = $request->get('getEmployees');
        $date = DateTimeField::convertToDBFormat($request->get('date'));
	$dateForDisplay = date('F d, Y', strtotime($date));
        $resource = $request->get('resource');
        $resourceType = $request->get('resourceType');
        $cvid = $request->get('cvid');

        //filter employees and vehicles
        $employeeRole = '';
        if($resource == 'employees' && $resourceType != '' && $resourceType != 'all' && $resourceType != 'null'){
            $employeeRole = $resourceType;
        }
        $vehicleType = '';
        if($resource == 'vehicles' && $resourceType != '' && $resourceType != 'all' && $resourceType != 'null'){
            $vehicleType = $resourceType;
        }
        
        $ordersTaskInstance = Vtiger_Module_Model::getInstance('OrdersTask');
        $getData = 'all';
        $roles = $ordersTaskInstance->getRoles();
        
        if($getEmployees == 'true'){
            $resources = $ordersTaskInstance->getAvailableEmployeeCapacity($date,$date,$employeeRole,$cvid,$getData);
            if ($employeeRole == "") {
                $resourceName = "Personnel";
            } else {
                $resourceName = $ordersTaskInstance->getPrimaryRoleDescription($employeeRole,$roles).'(s)';
            }
            $modalName = 'AvailablePersonnelModal.tpl';
        }else{
            $resources = $ordersTaskInstance->getAvailableVehiclesCapacity($date, $date, $vehicleType, $cvid, $getData);

            if ($vehicleType == "") {
                $resourceName = "Vehicles";
            } else {
                $resourceName = $vehicleType.'(s)';
            }
            $modalName = 'AvailableVehiclesModal.tpl';
        }
        
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULENAME', "OrdersTask");
        $viewer->assign('DATE', $dateForDisplay);
        $viewer->assign('RESOURCE_NAME', $resourceName);
        $viewer->assign('RESOURCES', $resources);

        echo $viewer->view($modalName, "OrdersTask", true);
    } 

}
