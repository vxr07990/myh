<?php

class OrdersTask_LoadLocalDispatch_View extends Vtiger_ListAjax_View
{
    protected $forModule             = 'OrdersTask';
    protected $dayDuration           = 8;
    protected $default_start         = '08:00:00';
    protected $default_task_duration = 1;

    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('loadGanttData');
        $this->exposeMethod('updateTaskData');
        $this->exposeMethod('createTask');
        $this->exposeMethod('loadResourceData');
        $this->exposeMethod('addResourceToTask');
        $this->exposeMethod('checkResources');
        $this->exposeMethod('getTaskWithResources');
    }

    public function loadGanttData($request)
    {
        $projectArray = $this->getQueryResult($request);
        $data         = [];
        if (count($projectArray) > 0) {
            $projectList = "('".implode("', '", $projectArray)."')";
            $data        = array_merge($data, $this->getTaskArray($request), $this->getProjectArray($projectList));
            usort($data,
                function ($a, $b) {
                    return $a['id'] - $b['id'];
                });
        }
        $dataGantt = [
            'data' => $data,
        ];
        echo json_encode($dataGantt);
    }

    public function getQueryResult($request)
    {
        $db          = PearDatabase::getInstance();
        $result      = OrdersTask_LocalDispatchDayBook_View::getTableQuery($request);
        $projectList = [];
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                if (Users_Privileges_Model::isPermitted('Orders', 'EditView', $row['ordersid']) == 'yes') {
                    array_push($projectList, $row['ordersid']);
                }
            }
        }

        return array_unique($projectList);
    }

    public function getFilterQuery($selectedDate, $daysNumber)
    {
        if (empty($daysNumber) || $daysNumber == 1) {
            return " AND vtiger_orderstask.disp_assigneddate = '$selectedDate' AND vtiger_orderstask.dispatch_status != 'Rejected'";
        }
        if ($daysNumber > 1) {
            $selectedEndDate = date('Y-m-d', strtotime($selectedDate."+".$daysNumber." days"));

            return " AND vtiger_orderstask.disp_assigneddate >= '$selectedDate' AND vtiger_orderstask.disp_assigneddate <= '$selectedEndDate' AND vtiger_orderstask.dispatch_status != 'Rejected'";
        }
    }

    public function getProjectArray($projectList)
    {
        $db          = PearDatabase::getInstance();
        $projectData = [];
        $result      = $db->query("SELECT * FROM vtiger_orders
                        INNER JOIN vtiger_crmentity ON vtiger_orders.ordersid=vtiger_crmentity.crmid         
                        WHERE vtiger_orders.ordersid IN $projectList  AND vtiger_crmentity.deleted=0"
        );
        if ($db->num_rows($result) > 0) {
            for ($i = 0; $i < $db->num_rows($result); $i++) {
                $transfereeName = Vtiger_Functions::getCRMRecordLabels('Contacts', [$db->query_result($result, $i, 'orders_contacts')]);
                $data['id']     = $db->query_result($result, $i, 'ordersid');
                $data['type']   = 'project';
                $data['text']   = $transfereeName[$db->query_result($result, $i, 'orders_contacts')];
                $data['open']   = 'true';
                $data['parent'] = '';
                array_push($projectData, $data);
            }
        }

        return $projectData;
    }

    public function getTaskArray($request)
    {
        $db        = PearDatabase::getInstance();
        $tasksData = [];
        $result    = OrdersTask_LocalDispatchDayBook_View::getTableQuery($request);
        if ($db->num_rows($result) > 0) {
            for ($i = 0; $i < $db->num_rows($result); $i++) {
                $data['id']         = $db->query_result($result, $i, 'orderstaskid');
                $data['type']       = '';
                $data['start_date'] = $db->query_result($result, $i, 'disp_assigneddate').' '.$this->getTaskStartHour($db->query_result($result, $i, 'disp_assignedstart'));
                $data['duration']   = $this->calcTaskDuration($db->query_result($result, $i, 'disp_assigneddate'), $db->query_result($result, $i, 'disp_assigneddate'));
                $data['end_date']   =
                    $this->getTaskEndDate($db->query_result($result, $i, 'disp_assigneddate'), $db->query_result($result, $i, 'disp_assigneddate')).
                    ' '.
                    $this->getTaskHourLenght($db->query_result($result, $i, 'disp_assignedstart'), $db->query_result($result, $i, 'estimated_hours'));
                $data['text']       = $db->query_result($result, $i, 'servicenameoptions');
                $data['progress']   = floatval(substr($db->query_result($result, $i, 'orderstaskprogress'), 0, 2)) / 100;
                $data['open']       = 'true';
                $data['parent']     = $db->query_result($result, $i, 'ordersid');
                array_push($tasksData, $data);
            }
        }

        return $tasksData;
    }

    public function getTaskStartHour($startHour)
    {
        if (empty($startHour)) {
            return $this->default_start;
        } else {
            return $startHour;
        }
    }

    public function getTaskEndDate($start, $enddate)
    {
        if (strtotime($enddate) == 0 || strtotime($start) > strtotime($enddate)) {
            return $start;
        } else {
            return $enddate;
        }
    }

    public function getMilestoneArray($projectId, $forModule)
    {
        $db            = PearDatabase::getInstance();
        $milestoneData = [];
        if ($forModule == 'Project') {
            $result = $db->query("SELECT * FROM vtiger_ordersmilestone
            INNER JOIN vtiger_crmentity ON vtiger_ordersmilestone.projectmilestoneid=vtiger_crmentity.crmid
              WHERE vtiger_ordersmilestone.ordersid = '$projectId'  AND vtiger_crmentity.deleted=0");
            if ($db->num_rows($result) > 0) {
                for ($i = 0; $i < $db->num_rows($result); $i++) {
                    $data['id']         = $db->query_result($result, $i, 'projectmilestoneid');
                    $data['type']       = 'milestone';
                    $data['start_date'] = $db->query_result($result, $i, 'projectmilestonedate');
                    $data['text']       = $db->query_result($result, $i, 'projectmilestonename');
                    $data['open']       = 'true';
                    $data['progress']   = 0;
                    $data['parent']     = $projectId;
                    array_push($milestoneData, $data);
                }
            }
        }

        return $milestoneData;
    }

    public function calcTaskDuration($start, $end)
    {
        $start    = strtotime($start);
        $end      = strtotime($end);
        $duration = ($end - $start) / (24 * 60 * 60);
        if ($duration < 0) {
            $duration = 0;
        }

        return $duration + 1;
    }

    public function getTaskHourLenght($startHour, $taskEstDuration)
    {
        if (empty($startHour)) {
            $startHour = $this->default_start;
        }
        if (intval($taskEstDuration) == 0) {
            $taskEstDuration = $this->default_task_duration;
        }

        return date("H:i:s", strtotime($startHour) + intval($taskEstDuration) * 3600);
    }

    public function loadResourceData(Vtiger_Request $request)
    {
        $taskId    = $request->get('task_id');
        $taskModel = Vtiger_Record_Model::getInstanceById($taskId);
        if ($taskModel->getModuleName() === 'Orders') {
            $disabled      = '-disabled';
            $checkDisabled = ' disabled="disabled"';
        } else {
            $disabled      = '';
            $checkDisabled = '';
        }
        $html              = '';
        $assignedResources = $this->getAssignedResourcesIds($taskId, $taskModel->getModuleName());
        $html .= $this->getEmployeeTable($taskId, $assignedResources, $disabled, $checkDisabled);
        $html .= $this->getVehiclesTable($taskId, $assignedResources, $disabled, $checkDisabled);
        $result = [
            'result'      => 'OK',
            'result_date' => $html,
        ];
        $msg    = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }

    public function getEmployeeTable($taskId, $assignedResources, $disabled, $checkDisabled)
    {
        $employees = $this->getAvailableEmployees($taskId);
        $html      = '';
        if (count($employees) > 0) {
            $html .= '<div class="row-fluid" style="height:550px;"><div class="span6" style="height:100%;  overflow-x: auto; overflow-y: auto;">';
            $html .= '<h3>'.vtranslate('LBL_EMPLOYEE_AND_CONTRACTORS', 'ProjectTask').'</h3><br>';
            $html .= $this->getStatusSelect('employees', $employees).'<br><br>';
            $html .= $this->getTypeSelect('employees', $employees).'<br><br>';
            $html .= '<div class="employees-tables">';
            $html .= '<table class="table table-bordered listViewEntriesTable">
                <thead>
                <tr class="listViewHeaders">
                <th>'.vtranslate('LBL_ASSIGNED', 'OrdersTask').'</th>
                <th>'.vtranslate('LBL_EMPLOYEE_NAME', 'OrdersTask').'</th>
                <th>'.vtranslate('LBL_EMPLOYEE_STATUS', 'OrdersTask').'</th>
                <th>'.vtranslate('LBL_EMPLOYEE_TYPE', 'OrdersTask').'</th>
                <th>'.vtranslate('LBL_EMPLOYEE_AVAILABLE', 'OrdersTask').'</th>
                <th>'.vtranslate('LBL_EMPLOYEE_SCHUDELED', 'OrdersTask').'</th>
                </tr>
                </thead>
                <tbody>';
            foreach ($employees as $employee) {
                $checked = '';
                if (in_array($employee['id'], $assignedResources)) {
                    $checked = 'checked';
                }
                $html .= '<tr  class="employees '.
                         strtolower(str_replace(' ', '', $employee['status'])).
                         ' '.
                         strtolower(str_replace(' ', '', $employee['employee_type'])).
                         ' draggable-resource'.
                         $disable.
                         '" id="'.
                         $employee['id'].
                         '">';
                $html .= '<td><input  class="assigned_resource'.$disable.'" '.$checked.$checkDisabled.' id="assigned_'.$employee['id'].'" type="checkbox"></td>';
                $html .= '<td>'.$employee['name'].'</td>';
                $html .= '<td>'.$employee['status'].'</td>';
                $html .= '<td>'.$employee['employee_type'].'</td>';
                $html .= '<td>'.$employee['available_hours'].'</td>';
                $html .= '<td>'.$employee['busytime'].'</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            $html .= '</div></div>';
        }

        return $html;
    }

    public function getVehiclesTable($taskId, $assignedResources, $disabled, $checkDisabled)
    {
        $html     = '';
        $vehicles = $this->getAvailableVehicles($taskId);
        if (count($vehicles) > 0) {
            $html .= '<div class="span6"  style="height:100%; overflow-y:auto;">';
            $html .= '<h3>'.vtranslate('LBL_VEHICLES_TRUCKS', 'ProjectTask').'</h3><br>';
            $html .= $this->getStatusSelect('vehicle', $vehicles).'<br><br>';
            $html .= '<div class="vehicles-tables">';
            $html .= '<table class="table table-bordered listViewEntriesTable">
                <thead>
                <tr class="listViewHeaders">
                <th>'.vtranslate('LBL_ASSIGNED', 'OrdersTask').'</th>
                <th>'.vtranslate('LBL_VEHICLES_TYPE', 'OrdersTask').'</th>
                <th>'.vtranslate('LBL_VEHICLES_NUMBER', 'OrdersTask').'</th>
                <th>'.vtranslate('LBL_VEHICLES_STATUS', 'OrdersTask').'</th>
                </tr>
                </thead>
                <tbody>';
            foreach ($vehicles as $vehicle) {
                $checked = '';
                if (in_array($vehicle['id'], $assignedResources)) {
                    $checked = 'checked';
                }
                if ($vehicle['vehicle_type'] == '') {
                    continue;
                }
                $html .= '<tr class="vehicle '.strtolower(str_replace(' ', '', $vehicle['status'])).' draggable-resource'.$disable.'" id="'.$vehicle['id'].'">';
                $html .= '<td><input class="assigned_resource'.$disable.'" '.$checked.$checkDisabled.'  id="assigned_'.$vehicle['id'].'" type="checkbox"></td>';
                $html .= '<td>'.$vehicle['vehicle_type'].'</td>';
                $html .= '<td>'.$vehicle['vehicle_number'].'</td>';
                $html .= '<td>'.$vehicle['status'].'</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            $html .= '</div>';
            $html .= '</div></div>';
        }

        return $html;
    }

    public function getStatusSelect($resourceType, $resourceArray)
    {
        foreach ($resourceArray as $value) {
            $statusArray[] = $value['status'];
        }
        $statusArray = array_filter(array_unique($statusArray));
        if ($resourceType == 'employees') {
            $html = '<select data-placeholder="Filter by Status..." class="employee-status-select chzn-select" id="select-'.$resourceType.'">';
        } else {
            $html = '<select data-placeholder="Filter by Status..." class="status-select chzn-select" id="select-'.$resourceType.'">';
        }
        $html .= '<option value="" ></option>';
        $html .= '<option value="all">'.vtranslate('All').'</option>';
        foreach ($statusArray as $value) {
            $html .= '<option value="'.strtolower(str_replace(' ', '', $value)).'">'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '<input type="hidden" id="values-'.$resourceType.'" value="'.strtolower(str_replace(' ', '', implode('::', $statusArray))).'">';

        return $html;
    }

    public function getTypeSelect($resourceType, $resourceArray)
    {
        foreach ($resourceArray as $value) {
            $typesArray[] = $value['employee_type'];
        }
        $typesArray = array_filter(array_unique($typesArray));
        $html       = '<select data-placeholder="Filter by Type..." class="employee-type-select chzn-select" id="type-select-'.$resourceType.'">';
        $html .= '<option value="" ></option>';
        $html .= '<option value="all" >'.vtranslate('All').'</option>';
        foreach ($typesArray as $value) {
            $html .= '<option value="'.strtolower(str_replace(' ', '', $value)).'">'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '<input type="hidden" id="types-values-'.$resourceType.'" value="'.strtolower(str_replace(' ', '', implode('::', $typesArray))).'">';

        return $html;
    }

    public function getAssignedResourcesIds($taskId, $taskModuleName)
    {
        $db  = PearDatabase::getInstance();
        $sql = "SELECT relcrmid FROM vtiger_crmentityrel
                    INNER JOIN vtiger_orderstask ON vtiger_crmentityrel.crmid = vtiger_orderstask.orderstaskid
                    INNER JOIN  vtiger_crmentity ON vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid";
        if ($taskModuleName == 'OrdersTask') {
            $sql .= " WHERE vtiger_orderstask.orderstaskid = ? AND  deleted = 0";
        } else {
            $sql .= " WHERE vtiger_orderstask.ordersid = ? AND  deleted = 0";
        }
        $result         = $db->pquery($sql, [$taskId]);
        $resourcesArray = [];
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                array_push($resourcesArray, $row['relcrmid']);
            }
        }

        return $resourcesArray;
    }

    /**
     * Return a list of availables employees in a time range (duplicate funciton. not sure is we are keeping the resource module)
     *
     * @param type $startDate
     * @param type $endDate
     * @param type $taskid - If not null the id is not considered as usage. Use to calculate free resources while editing
     *
     * @return array of free resources
     * @author Conrado Maggi <cmaggi@vgsglobal.com>
     */
    public function getAvailableEmployees($taskId)
    {
        $db               = PearDatabase::getInstance();
        $projectTaskModel = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
        $startDate        = $projectTaskModel->get('disp_assigneddate');
        $endDate          = $this->getTaskEndDate($projectTaskModel->get('disp_assigneddate'), $projectTaskModel->get('service_date_to'));
        $startHour        = $this->getTaskStartHour($projectTaskModel->get('disp_assignedstart'));
        $endHour          = $this->getTaskHourLenght($projectTaskModel->get('disp_assignedstart'), $projectTaskModel->get('estimated_hours'));
        $sql              = "SELECT vtiger_employees.employeesid, vtiger_employees.name, vtiger_employees.employee_lastname, vtiger_crmentity.setype,vtiger_employees.employee_status,contractor_status, vtiger_employees.employee_type
            FROM vtiger_employees 
            INNER JOIN vtiger_crmentity ON vtiger_employees.employeesid=vtiger_crmentity.crmid
            WHERE vtiger_crmentity.deleted=0 
            AND vtiger_employees.employeesid  NOT IN (
                                        SELECT relcrmid FROM vtiger_crmentityrel
                                            INNER JOIN vtiger_orderstask ON vtiger_crmentityrel.crmid = vtiger_orderstask.orderstaskid
                                            INNER JOIN vtiger_employees ON vtiger_crmentityrel.relcrmid = vtiger_employees.employeesid
                                            WHERE vtiger_orderstask.disp_assigneddate = ? AND  ADDTIME(disp_assignedstart, CONCAT(CEIL(estimated_hours),':', LPAD(Floor(estimated_hours*60 % 60),2,'0'))) > ? AND vtiger_orderstask.disp_assigneddate = ? AND vtiger_orderstask.disp_assignedstart < ? AND vtiger_orderstask.orderstaskid != ?";
        $sql .= " AND vtiger_orderstask.orderstaskid NOT IN (SELECT crmid FROM vtiger_crmentity WHERE setype='OrdersTask' AND deleted = 1))";
        $sql .= " AND (contractor_prole IN ('Helper', 'Warehouse', 'Driver') OR employee_prole IN ('Helper', 'Warehouse', 'Driver') OR employee_srole IN ('Helper', 'Warehouse', 'Driver'))";
        $params    = [$startDate, $startHour, $endDate, $endHour, $taskId];
        $result    = $db->pquery($sql, $params);
        $employees = [];
        while ($row = $db->fetchByAssoc($result)) {
            $employees[$row['employeesid']]['id']            = $row['employeesid'];
            $employees[$row['employeesid']]['name']          = $row['name'].' '.$row['employee_lastname'];
            $employees[$row['employeesid']]['quantity']      = 1;
            $employees[$row['employeesid']]['type']          = $row['setype'];
            $employees[$row['employeesid']]['employee_type'] = $row['employee_type'];
            if ($row['employee_type'] == 'Contractor') {
                $employees[$row['employeesid']]['status'] = $row['contractor_status'];
            } else {
                $employees[$row['employeesid']]['status'] = $row['employee_status'];
            }
        }
        $availableEmployees = $this->employeesAvailableTime($startDate, $startHour, $endDate, $endHour);
        $offEmployees       = $this->getOffEmployees($startDate);
        foreach ($employees as $employeeId => $values) {
            //remove unavailble employees and add the available time
            if (array_key_exists($employeeId, $availableEmployees)) {
                $employees[$employeeId]['available_hours'] = $availableEmployees[$employeeId];
            } else {
                unset($employees[$employeeId]);
                continue;
            }
            //remove off employees
            if (array_key_exists($employeeId, $offEmployees)) {
                if ($offEmployees[$employeeId] == 'all-day' || ($offEmployees[$employeeId]['starts'] < $endHour && $offEmployees[$employeeId]['ends'] > $startHour)) {
                    unset($employees[$employeeId]);
                    continue;
                }
            }
        }
        $employees = $this->employeesScheduledTime($employees, $startDate);

        return $employees;
    }

    /**
     * Function that returns the employees availabled time
     */
    public function employeesAvailableTime($startDate, $startHour, $endDate, $endHour)
    {
        $dow               = strtolower(date('l', strtotime($startDate)));
        $fieldNameDay      = 'employees_'.$dow;
        $fieldNameAllDay   = 'employees_'.$dow.'all';
        $fieldNameStarts   = 'employees_'.$dow.'start';
        $fieldNameEnds     = 'employees_'.$dow.'end';
        $db                = PearDatabase::getInstance();
        $result            = $db->pquery("SELECT employeesid, $fieldNameDay, $fieldNameAllDay,$fieldNameStarts, $fieldNameEnds
                                 FROM vtiger_employees INNER JOIN vtiger_crmentity ON vtiger_employees.employeesid = vtiger_crmentity.crmid 
                           WHERE deleted=0 AND $fieldNameDay=1 
                           AND ($fieldNameAllDay=1 OR ($fieldNameStarts < ? AND $fieldNameEnds > ?))",
                                         [$startHour, $endHour]);
        $availableEmployee = [];
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetchByAssoc($result)) {
                $availableEmployee[$row['employeesid']] = $row[$fieldNameEnds] - $row[$fieldNameStarts] + $row[$fieldNameAllDay] * $this->dayDuration;
            }
        }

        return $availableEmployee;
    }

    /**
     * Function that returns an array of all the employeesid that are away in a given date
     *
     * @param type $startDate
     */
    public function getOffEmployees($startDate)
    {
        $db           = PearDatabase::getInstance();
        $result       = $db->pquery("SELECT vtiger_timeoff.* , vtiger_crmentityrel.crmid as employeesid FROM vtiger_timeoff
                                INNER JOIN vtiger_crmentity ON vtiger_timeoff.timeoffid = vtiger_crmentity.crmid 
                                INNER JOIN vtiger_crmentityrel ON vtiger_timeoff.timeoffid = vtiger_crmentityrel.relcrmid
                                WHERE deleted=0 AND timeoff_date=?",
                                    [$startDate]);
        $offEmployees = [];
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetchByAssoc($result)) {
                if ($row['timeoff_allday'] == '1') {
                    $offEmployees[$row['employeesid']] = 'all-day';
                } else {
                    $offEmployees[$row['employeesid']]['starts'] = $row['timeoff_hourstart'];
                    $offEmployees[$row['employeesid']]['ends']   = $row['timeoff_hoursend'];
                }
            }
        }

        return $offEmployees;
    }

    /**
     * Function that returns
     */
    public function employeesScheduledTime($availableEmployees, $startDate)
    {
        $db           = PearDatabase::getInstance();
        $weekDays     = $this->getStartAndEndDate($startDate);
        $employeesIds = "('".implode("', '", array_keys($availableEmployees))."')";
        $sql          = "SELECT vtiger_crmentityrel.relcrmid, service_date_to, disp_assigneddate, disp_assignedstart,estimated_hours FROM vtiger_orderstask
                    INNER JOIN vtiger_crmentity ON vtiger_orderstask.orderstaskid = vtiger_crmentity.crmid
                    INNER JOIN vtiger_crmentityrel ON vtiger_orderstask.orderstaskid = vtiger_crmentityrel.crmid
                    WHERE deleted = 0
                    AND vtiger_orderstask.disp_assigneddate >= ?
                    AND vtiger_orderstask.service_date_to <= ?
                    AND relcrmid IN $employeesIds";
        $result       = $db->pquery($sql, [$weekDays['week_start'], $weekDays['week_end']]);
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetchByAssoc($result)) {
                $startDate                                        = $row['disp_assigneddate'];
                $endDate                                          = $this->getTaskEndDate($row['disp_assigneddate'], $row['service_date_to']);
                $startHour                                        = $this->getTaskStartHour($row['disp_assignedstart']);
                $endHour                                          = $this->getTaskHourLenght($row['disp_assignedstart'], $row['estimated_hours']);
                $datetime1                                        = strtotime($startDate.$startHour);
                $datetime2                                        = strtotime($endDate.$endHour);
                $interval                                         = abs($datetime2 - $datetime1);
                $availableEmployees[$row['relcrmid']]['busytime'] = $availableEmployees[$row['relcrmid']]['busytime'] + round($interval / 3600);
            }
        }

        return $availableEmployees;
    }

    public function getStartAndEndDate($startDate)
    {
        $date              = new DateTime($startDate);
        $week              = $date->format("W");
        $year              = $date->format("Y");
        $dto               = new DateTime();
        $ret['week_start'] = $dto->setISODate($year, $week)->format('Y-m-d');
        $ret['week_end']   = $dto->modify('+6 days')->format('Y-m-d');

        return $ret;
    }

    /**
     * Return a list of available vehicles in a time range
     *
     * @param type $startDate
     * @param type $endDate
     * @param type $taskid - If not null the id is not considered as usage. Use to calculate free resources while editing
     *
     * @return array of free resources
     */
    public function getAvailableVehicles($taskId = '')
    {
        $db               = PearDatabase::getInstance();
        $projectTaskModel = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
        $startDate        = $projectTaskModel->get('disp_assigneddate');
        $endDate          = $this->getTaskEndDate($projectTaskModel->get('disp_assigneddate'), $projectTaskModel->get('service_date_to'));
        $startHour        = $this->getTaskStartHour($projectTaskModel->get('disp_assignedstart'));
        $endHour          = $this->getTaskHourLenght($projectTaskModel->get('disp_assignedstart'), $projectTaskModel->get('estimated_hours'));
        $sql              = "SELECT vtiger_vehicles.vehiclesid, vtiger_vehicles.vehicle_number,  vtiger_vehicles.vehicle_type,vtiger_vehicles.vehicle_status
            FROM vtiger_vehicles INNER JOIN vtiger_crmentity ON vtiger_vehicles.vehiclesid=vtiger_crmentity.crmid
            WHERE vtiger_crmentity.deleted=0 AND vehiclesid 
                                    NOT IN (
                                            SELECT vtiger_vehicles.vehiclesid
                                            FROM vtiger_crmentityrel 
                                            INNER JOIN vtiger_orderstask ON vtiger_crmentityrel.crmid = vtiger_orderstask.orderstaskid
                                            INNER JOIN vtiger_vehicles ON vtiger_crmentityrel.relcrmid = vtiger_vehicles.vehiclesid
                                            WHERE vtiger_orderstask.disp_assigneddate = ? AND  ADDTIME(disp_assignedstart, CONCAT(CEIL(estimated_hours),':', LPAD(Floor(estimated_hours*60 % 60),2,'0'))) > ? AND vtiger_orderstask.disp_assigneddate = ? AND vtiger_orderstask.disp_assignedstart < ? AND vtiger_orderstask.orderstaskid != ?";
        $sql .= " AND vtiger_orderstask.orderstaskid NOT IN (SELECT crmid FROM vtiger_crmentity WHERE setype='ProjectTask' AND deleted = 1)
                                    )";
        $params   = [$startDate, $startHour, $endDate, $endHour, $taskId];
        $result   = $db->pquery($sql, $params);
        $vehicles = [];
        if ($result) {
            while ($row = $db->fetchByAssoc($result)) {
                $vehicles[$row['vehiclesid']]['id']             = $row['vehiclesid'];
                $vehicles[$row['vehiclesid']]['vehicle_number'] = $row['vehicle_number'];
                $vehicles[$row['vehiclesid']]['vehicle_type']   = $row['vehicle_type'];
                $vehicles[$row['vehiclesid']]['status']         = $row['vehicle_status'];
            }
        }

        return $vehicles;
    }

    /**
     * Adds/Remove a new relationship between a resources and task
     *
     * @param Vtiger_Request $request
     */
    public function addResourceToTask(Vtiger_Request $request)
    {
        $taskId           = $request->get('task_id');
        $resourceId       = $request->get('resource_id');
        $resourceInstance = Vtiger_Record_Model::getInstanceById($resourceId);
        $resourceType     = $resourceInstance->getModuleName();
        $db               = PearDatabase::getInstance();
        if ($request->get('adding_mode') == 'add') {
            $sql = "INSERT INTO `vtiger_crmentityrel` (`crmid`, `module`, `relcrmid`, `relmodule`) VALUES (?, ?, ?, ?);";
        } else {
            $sql = "DELETE FROM `vtiger_crmentityrel` WHERE `crmid`=? AND `module`=? AND`relcrmid`=? AND`relmodule`=?";
        }
        if ($resourceType == 'Employees') {
            $recordModel      = Vtiger_Record_Model::getInstanceById($resourceId, 'Employees');
            $modelData        = $recordModel->getData();
            $driver_prole     = $modelData['employee_prole'];
            $contractor_prole = $modelData['contractor_prole'];
            if ($prole == 'Driver' or $contractor_prole == 'Driver') {
                if ($request->get('adding_mode') == 'add') {
                    $db->pquery("UPDATE vtiger_orderstask SET related_employee=? WHERE orderstaskid=?", [$resourceId, $taskId]);
                } else {
                    $db->pquery("UPDATE vtiger_orderstask SET related_employee=? WHERE orderstaskid=?", ['', $taskId]);
                }
            }
        }
        try {
            $db->pquery($sql, [$taskId, 'OrderTask', $resourceId, $resourceType]);
            $result = [
                'result'      => 'OK',
                'resource_id' => $resourceId,
            ];
            if ($request->get('adding_mode') == 'add') {
                $result['title']    = vtranslate('Resource assigned', 'OrdersTask');
                $result['message']  = vtranslate('Resource succesfully assigned to task', 'OrdersTask');
                $result['assigned'] = $this->tasksHasEmployeeVehicles($taskId);
                if ($result['assigned'] == 'true') {
                    $record = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
                    $record->set('mode', 'edit');
                    $record->set('dispatch_status', 'Assigned');
                    $record->save();
                }
            } else {
                $result['title']    = vtranslate('Resource unassigned', 'OrdersTask');
                $result['message']  = vtranslate('Resource succesfully remove from task', 'OrdersTask');
                $result['assigned'] = $this->tasksHasEmployeeVehicles($taskId);
                if ($result['assigned'] == 'false') {
                    $record = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
                    $record->set('mode', 'edit');
                    $record->set('dispatch_status', 'Accepted');
                    $record->save();
                }
            }
            $this->updateCrewCount($taskId);
        } catch (Exception $exc) {
            $exc->getTraceAsString();
            $result = [
                'result' => 'fail',
                'msg'    => $exc->message,
            ];
        }
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }

    public function updateCrewCount($taskId)
    {
        $db = PearDatabase::getInstance();
        $db->pquery("UPDATE vtiger_orderstask SET disp_assignedcrew = (SELECT COUNT(relcrmid) as crewmembers  FROM vtiger_crmentityrel INNER JOIN vtiger_crmentity
            ON vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid
            WHERE deleted=0 AND relmodule = 'Employees' AND vtiger_crmentityrel.crmid = ?) WHERE vtiger_orderstask.orderstaskid=?",
                    [$taskId, $taskId]);
    }

    public function tasksHasEmployeeVehicles($taskId)
    {
        $db        = PearDatabase::getInstance();
        $result    = $db->pquery("SELECT relcrmid, relmodule FROM vtiger_crmentityrel INNER JOIN vtiger_crmentity
            ON vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid
            WHERE deleted=0 AND vtiger_crmentityrel.crmid = ?",
                                 [$taskId]);
        $resources = [];
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                array_push($resources, $row['relmodule']);
            }
        }
        if (in_array('Employees', $resources) && in_array('Vehicles', $resources)) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function checkResources($request)
    {
        $taskId    = $request->get('task_id');
        $startDate = date('Y-m-d', $request->get('start_date'));
        $endDate   = date('Y-m-d', $request->get('end_date'));
        $startHour = date('H:i:s', $request->get('start_date') - $request->get('offset') * 60); // GMT
        $endHour   = date('H:i:s', $request->get('end_date') - $request->get('offset') * 60);  // GMT
        if (!$this->checkResourcesStatus($taskId, $startDate, $startHour, $endDate, $endHour)) {
            $result             = [];
            $result['response'] = 'conflict';
        } else {
            $result             = [];
            $result['response'] = 'no-conflict';
        }
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }

    public function updateTaskData(Vtiger_Request $request)
    {
        $taskId    = $request->get('task_id');
        $startDate = date('Y-m-d', $request->get('start_date'));
        $endDate   = date('Y-m-d', $request->get('end_date'));
        $startHour = date('H:i:s', $request->get('start_date') - $request->get('offset') * 60); // GMT
        $endHour   = date('H:i:s', $request->get('end_date') - $request->get('offset') * 60);  // GMT
        try {
            $taskModel = Vtiger_Record_Model::getInstanceById($taskId, 'OrdersTask');
            $taskModel->set('id', $taskId);
            $taskModel->set('mode', 'edit');
            $taskModel->set('disp_assigneddate', $startDate);
            $taskModel->set('service_date_to', $endDate);
            $taskModel->set('disp_assignedstart', $startHour);
            $taskModel->set('estimated_hours', round((strtotime($endHour) - strtotime($startHour)) / (60 * 60)));
            $taskModel->save();
            //If manage to save unlink the resources
            $this->unlinkConflictedResources($taskId, $startDate, $startHour, $endDate, $endHour);
            $result['result'] = 'OK';
            $result['msg']    = vtranslate('LBL_TASK_UPDATED', 'OrdersTask');
        } catch (Exception $exc) {
            $result['result'] = 'OK';
            $result['msg']    = vtranslate('LBL_TASK_UPDATED_ERROR', 'OrdersTask');
            $result['log']    = $exc->message();
        }
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }

    public function unlinkConflictedResources($taskId, $startDate, $startHour, $endDate, $endHour)
    {
        $db        = PearDatabase::getInstance();
        $resources = $this->getTaskResources($taskId);
        foreach ($resources as $resourceId) {
            if (!$this->isResourceAvailable($resourceId, $startDate, $startHour, $endDate, $endHour, $taskId)) {
                $sql = "DELETE FROM `vtiger_crmentityrel` WHERE `crmid`=? AND`relcrmid`=?";
                $db->pquery($sql, [$taskId, $resourceId]);
            }
        }
    }

    public function checkResourcesStatus($taskId, $startDate, $startHour, $endDate, $endHour)
    {
        $resources = $this->getTaskResources($taskId);
        foreach ($resources as $resourceId) {
            if (!$this->isResourceAvailable($resourceId, $startDate, $startHour, $endDate, $endHour, $taskId)) {
                return false;
            }
        }

        return true;
    }

    public function isResourceAvailable($resourceId, $startDate, $startHour, $endDate, $endHour, $taskId)
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT vtiger_orderstask.orderstaskid FROM vtiger_crmentityrel
                INNER JOIN vtiger_crmentity ON vtiger_crmentityrel.crmid = vtiger_crmentity.crmid
                INNER JOIN vtiger_orderstask ON vtiger_crmentityrel.crmid = vtiger_orderstask.orderstaskid
                WHERE deleted =0 AND relcrmid =? AND vtiger_orderstask.disp_assigneddate = ? AND  ADDTIME(disp_assignedstart, CONCAT(CEIL(estimated_hours),':', LPAD(Floor(estimated_hours*60 % 60),2,'0'))) > ? AND vtiger_orderstask.disp_assigneddate = ? AND vtiger_orderstask.disp_assignedstart < ? AND vtiger_orderstask.orderstaskid != ?";
        $params = [$resourceId, $startDate, $startHour, $endDate, $endHour, $taskId];
        $result = $db->pquery($sql, $params);
        if ($db->num_rows($result) > 0) {
            return false; //its busy
        } else {
            return true;
        }
    }

    public function getTaskResources($taskId)
    {
        $db        = PearDatabase::getInstance();
        $result    = $db->pquery("SELECT relcrmid FROM vtiger_crmentityrel INNER JOIN vtiger_crmentity
            ON vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid
            WHERE deleted=0 AND vtiger_crmentityrel.crmid = ?",
                                 [$taskId]);
        $resources = [];
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                array_push($resources, $row[0]);
            }
        }

        return $resources;
    }

    public function getTaskWithResources(Vtiger_Request $request)
    {
        $taskResourcesArray = [];
        $selectedDate       = $request->get('selected_date');
        if (empty($selectedDate) || $selectedDate == '') {
            $selectedDate = date('Y-m-d');
        }
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT DISTINCT vtiger_orderstask.orderstaskid, vtiger_crmentityrel.relmodule,
                    count(vtiger_crmentityrel.relmodule) as resourcecount FROM vtiger_orderstask 
                    INNER JOIN vtiger_crmentity ON vtiger_orderstask.orderstaskid = vtiger_crmentity.crmid
                    INNER JOIN vtiger_crmentityrel ON vtiger_orderstask.orderstaskid = vtiger_crmentityrel.crmid
                    WHERE deleted = 0
                    AND vtiger_orderstask.disp_assigneddate = ?
                    GROUP BY vtiger_orderstask.orderstaskid, vtiger_crmentityrel.relmodule";
        $result = $db->pquery($sql, [$selectedDate]);
        if ($db->num_rows($result) > 0) {
            while ($row = $db->fetch_row($result)) {
                $resourcesTasks[$row['orderstaskid']][$row['relmodule']] = $row['resourcecount'];
            }
        }
        if (count($resourcesTasks) > 0) {
            foreach ($resourcesTasks as $orderId => $resources) {
                if (count($resources) > 1) {
                    array_push($taskResourcesArray, $orderId);
                }
            }
        }
        $response['result']   = 'OK';
        $response['task_ids'] = $taskResourcesArray;
        $msg                  = new Vtiger_Response();
        $msg->setResult($response);
        $msg->emit();
    }
}
