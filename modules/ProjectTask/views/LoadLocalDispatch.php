<?php

class ProjectTask_LoadLocalDispatch_View extends Vtiger_ListAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('loadGanttData');
        $this->exposeMethod('updateTaskData');
        $this->exposeMethod('createTask');
        $this->exposeMethod('loadResourceData');
        $this->exposeMethod('addResourceToTask');
    }

    public function loadGanttData($request)
    {
        $projectList = $this->getQueryResult($request);
        $data        = [];
        foreach ($projectList as $projectId) {
            $taskArray = $this->getTaskArray($projectId, $request->get('for_module'));
            //$milestoneArray = $this->getMilestoneArray($projectId, $request->get('for_module'), $selected_ids);
            //$data = array_merge($data, $milestoneArray);
            if (count($taskArray) > 0) {
                $data         = array_merge($data, $taskArray);
                $projectArray = $this->getProjectArray($projectId);
                $data         = array_merge($data, $projectArray);
            }
        }
        usort($data,
            function ($a, $b) {
                return $a['id'] - $b['id'];
            });
        $dataGantt = [
            'data' => $data,
        ];
        echo json_encode($dataGantt);
    }

    public function updateTaskData($request)
    {
        global $adb;
        $taskId    = $request->get('task_id');
        $progress  = round($request->get('progress') * 100, -1).'%';
        $startDate = date('Y-m-d', $request->get('start_date'));
        $endDate   = date('Y-m-d', $request->get('end_date'));
        $setType   = getSalesEntityType($taskId);
        if ($setType == 'ProjectTask') {
            $query  = ("UPDATE vtiger_projecttask SET projecttaskprogress=?,startdate=?,enddate=? WHERE projecttaskid=?");
            $params = [$progress, $startDate, $endDate, $taskId];
            $adb->pquery($query, $params);
        } elseif ($setType == 'ProjectMilestone') {
            $query  = ("UPDATE vtiger_projectmilestone SET projectmilestonedate=? WHERE projectmilestoneid=?");
            $params = [$startDate, $taskId];
            $adb->pquery($query, $params);
        } else {
            $query  = ("UPDATE vtiger_project SET progress=?,startdate=?,actualenddate=? WHERE projectid=?");
            $params = [$progress, $startDate, $endDate, $taskId];
            $adb->pquery($query, $params);
        }
    }

    public function createTask($request)
    {
        global $adb;
        include_once 'modules/ProjectTask/ProjectTask.php';
        $projecttaskname                                  = $request->get('projecttaskname');
        $projectId                                        = $request->get('project_id');
        $progress                                         = round($request->get('progress') * 100, -1).'%';
        $startDate                                        = date('Y-m-d', $request->get('start_date') + 86400);
        $endDate                                          = date('Y-m-d', $request->get('end_date') + 86400);
        $focus_task                                       = new ProjectTask();
        $focus_task->column_fields['projecttaskname']     = $projecttaskname;
        $focus_task->column_fields['projectid']           = $projectId;
        $focus_task->column_fields['projecttaskprogress'] = $progress;
        $focus_task->column_fields['startdate']           = $startDate;
        $focus_task->column_fields['enddate']             = $endDate;
        $focus_task->column_fields['assigned_user_id']    = $current_user->id;
        $focus_task->save('ProjectTask');
        if ($focus_task->id != '') {
            echo 'Success::'.$focus_task->id;
        } else {
            echo json_encode('error');
        }
    }

    public function getQueryResult($request)
    {
        global $currentModule, $current_user, $adb;
        $forModule      = 'ProjectTask';
        $customView     = new CustomView($forModule);
        $viewid         = $customView->getViewId($forModule);
        $viewid         = 130; //Harcoded -- Fix!
        $queryGenerator = new QueryGenerator($forModule, $current_user);
        if ($viewid != "0") {
            $queryGenerator->initForCustomViewById($viewid);
        } else {
            $queryGenerator->initForDefaultCustomView();
        }
        // Enabling Module Search
        $url_string = '';
        if ($_REQUEST['query'] == 'true') {
            $queryGenerator->addUserSearchConditions($_REQUEST);
            $ustring = getSearchURL($_REQUEST);
            $url_string .= "&query=true$ustring";
            $smarty->assign('SEARCH_URL', $url_string);
        }
        $list_query = $queryGenerator->getQuery();
        $where      = $queryGenerator->getConditionalWhere();
        if (isset($where) && $where != '') {
            $_SESSION['export_where'] = $where;
        } else {
            unset($_SESSION['export_where']);
        }
        $selectedDate = $request->get('selected_date');
        if ($selectedDate != '') {
            $list_query .= " AND vtiger_projecttask.startdate = '$selectedDate' ";
        }
        // Sorting
        if (!empty($order_by)) {
            if ($order_by == 'smownerid') {
                $list_query .= ' ORDER BY user_name '.$sorder;
            } else {
                $tablename = getTableNameForField($forModule, $order_by);
                $tablename = ($tablename != '')?($tablename.'.'):'';
                $list_query .= ' ORDER BY '.$tablename.$order_by.' '.$sorder;
            }
        }
        $result = $adb->query($list_query);
        if ($adb->num_rows($result) > 0) {
            $projectList = [];
            for ($i = 0; $i < $adb->num_rows($result); $i++) {
                $projectList[] = $adb->query_result($result, $i, 'projectid');
            }
        }

        return array_unique($projectList);
    }

    public function getProjectArray($projectId)
    {
        $db          = PearDatabase::getInstance();
        $projectData = [];
        $result      = $db->query("SELECT * FROM vtiger_project
                        INNER JOIN vtiger_crmentity ON vtiger_project.projectid=vtiger_crmentity.crmid
                        WHERE vtiger_project.projectid = '$projectId'  AND vtiger_crmentity.deleted=0");
        if ($db->num_rows($result) > 0) {
            $data['id']         = $projectId;
            $data['start_date'] = $db->query_result($result, 0, 'startdate');
            $data['type']       = 'project';
            $data['duration']   = $this->calcTaskDuration($db->query_result($result, 0, 'startdate'), $db->query_result($result, 0, 'targetenddate'));
            $data['text']       = $db->query_result($result, 0, 'projectname');
            $data['progress']   = floatval(substr($db->query_result($result, 0, 'progress'), 0, 2)) / 100;
            $data['open']       = 'true';
            $data['parent']     = '';
            array_push($projectData, $data);
        }

        return $projectData;
    }

    public function getTaskArray($projectId, $forModule)
    {
        $db        = PearDatabase::getInstance();
        $tasksData = [];
        $sql       = 'SELECT * FROM vtiger_project
            INNER JOIN vtiger_projecttask ON vtiger_project.projectid=vtiger_projecttask.projectid
            INNER JOIN vtiger_crmentity ON vtiger_projecttask.projecttaskid=vtiger_crmentity.crmid
            WHERE vtiger_project.projectid = ? AND vtiger_crmentity.deleted=0';
        $result    = $db->pquery($sql, [$projectId]);
        if ($db->num_rows($result) > 0) {
            for ($i = 0; $i < $db->num_rows($result); $i++) {
                if ($db->query_result($result, $i, 'startdate') == '') {
                    continue;
                }
                $data['id']         = $db->query_result($result, $i, 'projecttaskid');
                $data['type']       = '';
                $data['start_date'] = $db->query_result($result, $i, 'startdate').' '.$db->query_result($result, $i, 'start_hour');
                $data['duration']   = $this->calcTaskDuration($db->query_result($result, $i, 'startdate'), $db->query_result($result, $i, 'enddate'));
                $data['end_date']   = $db->query_result($result, $i, 'enddate').' '.$db->query_result($result, $i, 'end_hour');
                $data['text']       = $db->query_result($result, $i, 'projecttaskname');
                $data['progress']   = floatval(substr($db->query_result($result, $i, 'projecttaskprogress'), 0, 2)) / 100;
                $data['open']       = 'true';
                $data['parent']     = $projectId;
                array_push($tasksData, $data);
            }
        }

        return $tasksData;
    }

    public function getMilestoneArray($projectId, $forModule)
    {
        $db            = PearDatabase::getInstance();
        $milestoneData = [];
        if ($forModule == 'Project') {
            $result = $db->query("SELECT * FROM vtiger_projectmilestone
            INNER JOIN vtiger_crmentity ON vtiger_projectmilestone.projectmilestoneid=vtiger_crmentity.crmid
              WHERE vtiger_projectmilestone.projectid = '$projectId'  AND vtiger_crmentity.deleted=0");
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

        return $duration + 1;
    }

    public function loadResourceData(Vtiger_Request $request)
    {
        $taskId    = $request->get('task_id');
        $html      = '';
        $employees = $this->getAvailableEmployees($taskId);
        if (count($employees) > 0) {
            $html .= '<h3>'.vtranslate('LBL_EMPLOYEE_AND_CONTRACTORS', 'ProjectTask').'</h3><br>';
            $html .= $this->getStatusSelect('employees', $employees).'<br><br>';
            $html .= '<table class="table table-bordered listViewEntriesTable">
                <thead>
                <tr class="listViewHeaders">
                <th>'.vtranslate('LBL_ASSIGNED', 'ProjectTask').'</th>
                <th>'.vtranslate('LBL_EMPLOYEE_NAME', 'ProjectTask').'</th>
                <th>'.vtranslate('LBL_EMPLOYEE_STATUS', 'ProjectTask').'</th>
                <th>'.vtranslate('LBL_EMPLOYEE_AVAILABLE', 'ProjectTask').'</th>
                <th>'.vtranslate('LBL_EMPLOYEE_SCHUDELED', 'ProjectTask').'</th>
                </tr>
                </thead>
                <tbody>';
            foreach ($employees as $employee) {
                $html .= '<tr  class="employees '.$employee['status'].' draggable-resource" id="'.$employee['id'].'">';
                $html .= '<td><input name="assigned" type="checkbox"></td>';
                $html .= '<td>'.$employee['name'].'</td>';
                $html .= '<td>'.$employee['status'].'</td>';
                $html .= '<td>'.$employee['id'].'</td>';
                $html .= '<td>'.$employee['id'].'</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        }
        $vehicles = $this->getAvailableVehicles($taskId);
        if (count($vehicles) > 0) {
            $html .= '<br><h3>'.vtranslate('LBL_VEHICLES_TRUCKS', 'ProjectTask').'</h3><br>';
            $html .= $this->getStatusSelect('vehicle', $vehicles).'<br><br>';
            $html .= '<table class="table table-bordered listViewEntriesTable">
                <thead>
                <tr class="listViewHeaders">
                <th>'.vtranslate('LBL_ASSIGNED', 'ProjectTask').'</th>
                <th>'.vtranslate('LBL_VEHICLES_NAME', 'ProjectTask').'</th>
                <th>'.vtranslate('LBL_VEHICLES_STATUS', 'ProjectTask').'</th>
                </tr>
                </thead>
                <tbody>';
            foreach ($vehicles as $vehicle) {
                $html .= '<tr class="vehicle '.$vehicle['status'].' draggable-resource" id="'.$vehicle['id'].'">';
                $html .= '<td><input name="assigned" type="checkbox"></td>';
                $html .= '<td>'.$vehicle['name'].'</td>';
                $html .= '<td>'.$vehicle['status'].'</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        }
        $result = [
            'result'      => 'OK',
            'result_date' => $html,
        ];
        $msg    = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }

    public function getStatusSelect($resourceType, $resourceArray)
    {
        foreach ($resourceArray as $key => $value) {
            $statusArray[] = $value['status'];
        }
        $statusArray = array_unique($statusArray);
        $html        = '<select class="status-select chzn-select" id="select-'.$resourceType.'">';
        $html .= '<option value="all">'.vtranslate('All').'</option>';
        foreach ($statusArray as $value) {
            $html .= '<option value="'.$value.'">'.$value.'</option>';
        }
        $html .= '</select>';
        $html .= '<input type="hidden" id="values-'.$resourceType.'" value="'.implode('::', $statusArray).'">';

        return $html;
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
        $projectTaskModel = Vtiger_Record_Model::getInstanceById($taskId, 'ProjectTask');
        $startDate        = $projectTaskModel->get('startdate');
        $endDate          = $projectTaskModel->get('enddate');
        $startHour        = $projectTaskModel->get('start_hour');
        $endHour          = $projectTaskModel->get('end_hour');
        $sql              = "SELECT vtiger_employees.employeesid, vtiger_employees.name, vtiger_crmentity.setype,vtiger_employees.employee_status
            FROM vtiger_employees 
            INNER JOIN vtiger_crmentity ON vtiger_employees.employeesid=vtiger_crmentity.crmid
            WHERE vtiger_crmentity.deleted=0 
            AND vtiger_employees.employeesid  NOT IN (
                                        SELECT resourceid FROM vtiger_resourcedashboard
                                            INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                                            INNER JOIN vtiger_employees ON vtiger_resourcedashboard.resourceid = vtiger_employees.employeesid
                                            WHERE vtiger_projecttask.enddate = ? AND  vtiger_projecttask.end_hour >= ? AND vtiger_projecttask.startdate = ? AND vtiger_projecttask.start_hour <= ? AND vtiger_projecttask.projecttaskid != ?";
        $sql .= " AND vtiger_projecttask.projecttaskid NOT IN (SELECT crmid FROM vtiger_crmentity WHERE setype='ProjectTask' AND deleted = 1))";
        $params    = [$startDate, $startHour, $endDate, $endHour, $taskId];
        $result    = $db->pquery($sql, $params);
        $employees = [];
        while ($row = $db->fetchByAssoc($result)) {
            $employees[$row['employeesid']]['id']       = $row['employeesid'];
            $employees[$row['employeesid']]['name']     = $row['name'];
            $employees[$row['employeesid']]['quantity'] = 1;
            $employees[$row['employeesid']]['type']     = $row['setype'];
            $employees[$row['employeesid']]['status']   = $row['employee_status'];
        }
        $availableEmployees      = $this->employeesAvailableTime($startDate, $startHour, $endDate, $endHour);
        $offEmployees            = $this->getOffEmployees($startDate);
        $employessScheduledHours = $this->employeesScheduledTime($startDate);
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
                $availableEmployee[$row['employeesid']] = $row[$fieldNameEnds] - $row[$fieldNameStarts];
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
    public function employeesScheduledTime($startDate)
    {
        $weekDays = $this->getStartAndEndDate($startDate);
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
        $projectTaskModel = Vtiger_Record_Model::getInstanceById($taskId, 'ProjectTask');
        $startDate        = $projectTaskModel->get('startdate');
        $endDate          = $projectTaskModel->get('enddate');
        $startHour        = $projectTaskModel->get('start_hour');
        $endHour          = $projectTaskModel->get('end_hour');
        $sql              = "SELECT vtiger_vehicles.vehiclesid, vtiger_vehicles.name, vtiger_vehicles.quantity,vtiger_vehicles.vehicle_status
            FROM vtiger_vehicles INNER JOIN vtiger_crmentity ON vtiger_vehicles.vehiclesid=vtiger_crmentity.crmid
            WHERE vtiger_crmentity.deleted=0 AND vehiclesid 
                                    NOT IN (
                                            SELECT vtiger_vehicles.vehiclesid
                                            FROM vtiger_resourcedashboard 
                                            INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                                            INNER JOIN vtiger_vehicles ON vtiger_resourcedashboard.resourceid = vtiger_vehicles.vehiclesid
                                           WHERE vtiger_projecttask.enddate = ? AND  vtiger_projecttask.end_hour >= ? AND vtiger_projecttask.startdate = ? AND vtiger_projecttask.start_hour <= ? AND vtiger_projecttask.projecttaskid != ?";
        $sql .= " AND vtiger_projecttask.projecttaskid NOT IN (SELECT crmid FROM vtiger_crmentity WHERE setype='ProjectTask' AND deleted = 1)
                                    )";
        //                    UNION
        //                SELECT vtiger_vehicles.vehiclesid, vtiger_vehicles.name,(vtiger_vehicles.quantity - quantitybooked) as quantity
        //FROM vtiger_vehicles INNER JOIN (
        // SELECT vtiger_resourcedashboard.resourceid, sum(vtiger_resourcedashboard.quantity) as quantitybooked
        //                    FROM vtiger_resourcedashboard
        //                    INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
        //                    INNER JOIN vtiger_crmentity ON vtiger_projecttask.projecttaskid=vtiger_crmentity.crmid
        //                    WHERE vtiger_crmentity.deleted=0 AND vtiger_projecttask.enddate >= ?
        //						  AND vtiger_projecttask.startdate <= ?";
        //
        //        $sql .= " AND vtiger_projecttask.projecttaskid != ? ";
        //
        //        $sql .= " GROUP by resourceid
        //						  ) bookedresources
        //
        //						  ON vtiger_vehicles.vehiclesid = bookedresources.resourceid";
        $params   = [$startDate, $startHour, $endDate, $endHour, $taskId];
        $result   = $db->pquery($sql, $params);
        $vehicles = [];
        while ($row = $db->fetchByAssoc($result)) {
            if ($row['quantity'] <= 0) {
                continue;
            }
            $vehicles[$row['vehiclesid']]['id']     = $row['vehiclesid'];
            $vehicles[$row['vehiclesid']]['name']   = $row['name'];
            $vehicles[$row['vehiclesid']]['status'] = $row['vehicle_status'];
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
        $taskId     = $request->get('task_id');
        $resourceId = $request->get('resource_id');
        $result     = [
            'result'      => 'OK',
            'result_date' => $html,
        ];
        $msg        = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }
}
