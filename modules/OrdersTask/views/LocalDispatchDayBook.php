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

class OrdersTask_LocalDispatchDayBook_View extends Vtiger_List_View
{
    public function __construct()
    {
        parent::__construct();
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        parent::preProcess($request, false);
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $viewer->assign('CURRENT_USER', Users_Record_Model::getCurrentUserModel());
        if ($display) {
            $this->preProcessDisplay($request);
        }
    }

    public function preProcessTplName(Vtiger_Request $request)
    {
        return 'ListViewPreProcessDB.tpl';
    }

    public function process(Vtiger_Request $request)
    {
        $viewer         = $this->getViewer($request);
        $moduleName     = $request->getModule();
        $moduleModel    = Vtiger_Module_Model::getInstance($moduleName);
        $this->viewName = $request->get('viewname');
        $this->getTableContents($request, $viewer);
        $this->getFilters($request, $viewer);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('ListViewContentsDB.tpl', $moduleName);
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
        $jsFileNames = [
            "modules.$moduleName.resources.LocalDispatchDayBook",
            "modules.$moduleName.resources.dhtmlx.dhtmlxgantt",
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames       = [
            '~/layouts/vlayout/modules/OrdersTask/resources/style.css',
            '~/layouts/vlayout/modules/OrdersTask/resources/dhtmlx/dhtmlxgantt.css',
        ];
        $cssInstances       = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }

    public function postProcess(Vtiger_Request $request)
    {
        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $viewer->view('GanttSection.tpl', $moduleName);
        parent::postProcess($request);
    }

    public function getTableContents(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $db     = PearDatabase::getInstance();
        $result = $this->getTableQuery($request);
        if ($db->num_rows($result) > 0) {
            $entries = [];
            while ($row = $db->fetch_row($result)) {
                if (Users_Privileges_Model::isPermitted('Orders', 'EditView', $row['ordersid']) == 'yes') {
                    $transfereeName = Vtiger_Functions::getCRMRecordLabels('Contacts', [$row['orders_contacts']]);
                    $accountName    = Vtiger_Functions::getCRMRecordLabels('Accounts', [$row['orders_account']]);
                    if ($row['related_employee'] != '' && $row['related_employee'] != 0) {
                        $ordersEmployee = Vtiger_Record_Model::getInstanceById($row['related_employee'], 'Employees');
                        $lastName       = $ordersEmployee->get('employee_lastname');
                        $firstName      = $ordersEmployee->get('name');
                        $driver         = $firstName.' '.$lastName;
                    } else {
                        $driver = '';
                    }
                    $entry['drivers_notes']        = (strlen($row['drivers_notes']) > 0)?$row['drivers_notes']:'No notes from this driver.';
                    $entry['orders_contacts']      = $transfereeName[$row['orders_contacts']];
                    $entry['orders_account']       = $accountName[$row['orders_account']];
                    $entry['orders_no']            = $row['orders_no'];
                    $entry['servicenameoptions']   = $row['servicenameoptions'];
                    $entry['origin_address1']      = $row['origin_address1'];
                    $entry['origin_city']          = $row['origin_city'];
                    $entry['origin_state']         = $row['origin_state'];
                    $entry['destination_address1'] = $row['destination_address1'];
                    $entry['destination_city']     = $row['destination_city'];
                    $entry['destination_state']    = $row['destination_state'];
                    $entry['service_date_from']    = DateTimeField::convertToUserFormat($row['service_date_from']);
                    $entry['service_date_to']      = DateTimeField::convertToUserFormat($row['service_date_to']);
                    $entry['disp_assigneddate']    = DateTimeField::convertToUserFormat($row['disp_assigneddate']);
                    $entry['orders_eweight']       = $row['orders_eweight'];
                    $entry['orders_ecube']         = $row['orders_ecube'];
                    $entry['orders_elinehaul']     = CurrencyField::convertToUserFormat($row['orders_elinehaul']);
                    $entry['crew_number']          = $row['crew_number'];
                    $entry['est_vehicle_number']   = $row['est_vehicle_number'];
                    $entry['move_coordinator']     = $this->getCoordinator($row['ordersid']);
                    $entry['orderstaskid']         = $row['orderstaskid'];
                    $entry['ordersid']             = $row['ordersid'];
                    $entry['related_employee']     = $driver;
                    $entry['origin_zip']           = $row['origin_zip'];
                    array_push($entries, $entry);
                }
            }
        }
        $viewer->assign('LISTVIEW_ENTRIES_COUNT', count($entries));
        $viewer->assign('LISTVIEW_ENTRIES', $entries);
        $viewer->assign('AUTHORITY', $request->get('authority'));
        $viewer->assign('COMMODODITY', $request->get('commodity'));
        $viewer->assign('TASK_STATUS', $request->get('filtro'));
    }

    public function getTableQuery(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $filter       = $request->get('filtro');
        $selectedDate = $request->get('selected_date');
        $daysNumber   = $request->get('days');
        $selectedCommodity = $request->get('commodity');
        $selectedAuthority = $request->get('authority');
        if (empty($filter)) {
            $filter = 'all';
        }
        if (empty($daysNumber) || $daysNumber == 1) {
            $daysNumber = 0;
        }
        if (empty($selectedDate) || $selectedDate == '') {
            $selectedDate    = date('Y-m-d');
            $selectedEndDate = date('Y-m-d', strtotime(date('Y-m-d')."+".$daysNumber." days"));
        } else {
            $selectedEndDate = date('Y-m-d', strtotime($selectedDate."+".$daysNumber." days"));
        }
        $sql = "SELECT vtiger_orders.*, vtiger_orderstask.*
                    FROM vtiger_orders
                    INNER JOIN vtiger_orderstask ON vtiger_orders.ordersid=vtiger_orderstask.ordersid
                    INNER JOIN vtiger_crmentity crm1 ON vtiger_orders.ordersid=crm1.crmid
                    INNER JOIN vtiger_crmentity crm2 ON vtiger_orderstask.ordersid=crm2.crmid
                    WHERE crm1.deleted = 0 AND crm2.deleted=0
                    AND disp_assigneddate >= ? AND disp_assigneddate <= ?";
        $params = [
            $selectedDate,
            $selectedEndDate,
        ];
        if (!empty($selectedCommodity) && $selectedCommodity != '--') {
            $sql .= " AND orders_commodity = ?";
            array_push($params, $selectedCommodity);
        }
        if (!empty($selectedAuthority) && $selectedAuthority != '--') {
            $sql .= " AND orderspriority = ?";
            array_push($params, $selectedAuthority);
        }
        switch ($filter) {
            case 'unassigned':
                $sql .= " AND (dispatch_status = ? OR dispatch_status = ?)";
                array_push($params, 'Unassigned');
                array_push($params, 'Accepted');
                break;
            case 'assigned':
                $sql .= " AND dispatch_status = ?";
                array_push($params, 'Assigned');
                break;
            case 'all':
                $sql .= " AND dispatch_status != ?";
                array_push($params, 'Rejected');
                break;
        }
        $result = $db->pquery($sql, $params);

        return $result;
    }

    public function getFilters(Vtiger_Request $request, Vtiger_Viewer $viewer)
    {
        $db           = PearDatabase::getInstance();
        $commodityarr = [];
        $autorityarr  = [];
        $picklistValues = Vtiger_Util_Helper::getPickListValues('orders_commodity');
        if (count($picklistValues) > 0) {
            foreach ($picklistValues as $picklistValue) {
                array_push($commodityarr, getTranslatedString($picklistValue, 'Orders'));
            }
        }
        $picklistValues = Vtiger_Util_Helper::getPickListValues('orderspriority');
        if (count($picklistValues) > 0) {
            foreach ($picklistValues as $picklistValue) {
                array_push($autorityarr, getTranslatedString($picklistValue, 'Orders'));
            }
        }
        $viewer->assign('COMMODITY_ARR', $commodityarr);
        $viewer->assign('AUTORITY_ARR', $autorityarr);
    }

    public function getCoordinator($orderId)
    {
        $db     = PearDatabase::getInstance();
        $result = $db->pquery('SELECT moveroles_employees FROM vtiger_moveroles WHERE moveroles_role=? AND moveroles_project=?', ['Customer Service Cordinator', $orderId]);
        if ($db->num_rows($result) > 0) {
            $employeeId   = $db->query_result($result, 0, 'moveroles_employees');
            $employeeName = Vtiger_Functions::getCRMRecordLabels('Employees', [$employeeId]);

            return $employeeName[$employeeId];
        }

        return '';
    }
}
