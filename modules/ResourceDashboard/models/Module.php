<?php

/**
 * Resource Management Module
 *
 *
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class ResourceDashboard_Module_Model extends Vtiger_Module_Model
{
    public function getSideBarLinks($linkParams)
    {
        $linkTypes = array('SIDEBARLINK', 'SIDEBARWIDGET');
        $links = parent::getSideBarLinks($linkParams);


        $quickLinks = array(
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_EMPLOYEE_LIST',
                'linkurl' => $this->getEmployeeListUrl(),
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_VEHICLES_LIST',
                'linkurl' => $this->getVehiclesListUrl(),
                'linkicon' => '',
            ),
            array(
                'linktype' => 'SIDEBARLINK',
                'linklabel' => 'LBL_EQUIPMENT_LIST',
                'linkurl' => $this->getEquipmentListUrl(),
                'linkicon' => '',
            ),
        );
        foreach ($quickLinks as $quickLink) {
            $links['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $links;
    }

    public function getEmployeeListUrl()
    {
        $employeeModel = Vtiger_Module_Model::getInstance('Employees');
        return $employeeModel->getListViewUrl();
    }

    public function getVehiclesListUrl()
    {
        $vehiclesModel = Vtiger_Module_Model::getInstance('Vehicles');
        return $vehiclesModel->getListViewUrl();
    }

    public function getEquipmentListUrl()
    {
        $equipmentModel = Vtiger_Module_Model::getInstance('Equipment');
        return $equipmentModel->getListViewUrl();
    }

    /**
     * Return a list of available vehicles in a time range
     *
     * @param type $startDate
     * @param type $endDate
     * @param type $taskid - If not null the id is not considered as usage. Use to calculate free resources while editing
     * @return array of free resources
     */
    
    public function getAvailableVehicles($startDate, $endDate, $taskid = '')
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT vtiger_vehicles.vehiclesid, vtiger_vehicles.name, vtiger_vehicles.quantity 
            FROM vtiger_vehicles INNER JOIN vtiger_crmentity ON vtiger_vehicles.vehiclesid=vtiger_crmentity.crmid
            WHERE vtiger_crmentity.deleted=0 AND vehiclesid 
                                    NOT IN (
                                            SELECT vtiger_vehicles.vehiclesid
                                            FROM vtiger_resourcedashboard 
                                            INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                                            INNER JOIN vtiger_vehicles ON vtiger_resourcedashboard.resourceid = vtiger_vehicles.vehiclesid
                                            WHERE vtiger_projecttask.enddate > ? AND vtiger_projecttask.startdate < ?";
        if ($taskid != '') {
            $sql .= " AND vtiger_projecttask.projecttaskid != ? ";
        }

        $sql .= " AND vtiger_projecttask.projecttaskid NOT IN (SELECT crmid FROM vtiger_crmentity WHERE setype='ProjectTask' AND deleted = 1)
                                    )
                    UNION
                SELECT vtiger_vehicles.vehiclesid, vtiger_vehicles.name,(vtiger_vehicles.quantity - quantitybooked) as quantity
FROM vtiger_vehicles INNER JOIN (
 SELECT vtiger_resourcedashboard.resourceid, sum(vtiger_resourcedashboard.quantity) as quantitybooked
                    FROM vtiger_resourcedashboard 
                    INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                    INNER JOIN vtiger_crmentity ON vtiger_projecttask.projecttaskid=vtiger_crmentity.crmid
                    WHERE vtiger_crmentity.deleted=0 AND vtiger_projecttask.enddate >= ?
						  AND vtiger_projecttask.startdate <= ?";
        if ($taskid != '') {
            $sql .= " AND vtiger_projecttask.projecttaskid != ? ";
        }
        $sql .= " GROUP by resourceid
						  ) bookedresources
						  
						  ON vtiger_vehicles.vehiclesid = bookedresources.resourceid";

        if ($taskid != '') {
            $params = array($startDate, $endDate, $taskid, $startDate, $endDate,$taskid);
        } else {
            $params = array($startDate, $endDate, $startDate, $endDate);
        }

        $result = $db->pquery($sql, $params);
        $vehicles = array();

        while ($row = $db->fetchByAssoc($result)) {
            if ($row['quantity'] <= 0) {
                continue;
            }
            $vehicles[$row['vehiclesid']]['id'] = $row['vehiclesid'];
            $vehicles[$row['vehiclesid']]['name'] = $row['name'];
            $vehicles[$row['vehiclesid']]['quantity'] = $row['quantity'];
            $vehicles[$row['vehiclesid']]['type'] = 'Vehicles';
        }


        return $vehicles;
    }
    
    /**
     * Return a list of available equiptment in a time range
     *
     * @param type $startDate
     * @param type $endDate
     * @param type $taskid - If not null the id is not considered as usage. Use to calculate free resources while editing
     * @return array of free resources
     */
    public function getAvailableEquiptment($startDate, $endDate, $taskid = '')
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT vtiger_equipment.equipmentid, vtiger_equipment.name, vtiger_equipment.quantity 
            FROM vtiger_equipment INNER JOIN vtiger_crmentity ON vtiger_equipment.equipmentid=vtiger_crmentity.crmid
            WHERE vtiger_crmentity.deleted=0 
            AND equipmentid 
                                    NOT IN (
                                            SELECT vtiger_equipment.equipmentid
                                            FROM vtiger_resourcedashboard 
                                            INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                                            INNER JOIN vtiger_equipment ON vtiger_resourcedashboard.resourceid = vtiger_equipment.equipmentid
                                            WHERE vtiger_projecttask.enddate >= ? AND vtiger_projecttask.startdate <= ?";
        if ($taskid != '') {
            $sql .= " AND vtiger_projecttask.projecttaskid != ? ";
        }
        $sql .= " AND vtiger_projecttask.projecttaskid NOT IN (SELECT crmid FROM vtiger_crmentity WHERE setype='ProjectTask' AND deleted = 1)
                                    )
                    UNION
                    SELECT vtiger_equipment.equipmentid, vtiger_equipment.name,(vtiger_equipment.quantity - quantitybooked) as quantity
FROM vtiger_equipment INNER JOIN (
 SELECT vtiger_resourcedashboard.resourceid, sum(vtiger_resourcedashboard.quantity) as quantitybooked
                    FROM vtiger_resourcedashboard 
                    INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                    INNER JOIN vtiger_crmentity ON vtiger_projecttask.projecttaskid=vtiger_crmentity.crmid
                    WHERE vtiger_crmentity.deleted=0 AND vtiger_projecttask.enddate >= ?
						  AND vtiger_projecttask.startdate <= ?";
        if ($taskid != '') {
            $sql .= " AND vtiger_projecttask.projecttaskid != ? ";
        }
        $sql .= " GROUP by resourceid
						  ) bookedresources
						  
						  ON vtiger_equipment.equipmentid = bookedresources.resourceid";

        if ($taskid != '') {
            $params = array($startDate, $endDate, $taskid, $startDate, $endDate,$taskid);
        } else {
            $params = array($startDate, $endDate, $startDate, $endDate);
        }
        $result = $db->pquery($sql, $params);
        $equipment = array();

        while ($row = $db->fetchByAssoc($result)) {
            if ($row['equipmentid'] == '') {
                continue;
            }
            if ($row['quantity'] <= 0) {
                continue;
            }
            $equipment[$row['equipmentid']]['id'] = $row['equipmentid'];
            $equipment[$row['equipmentid']]['name'] = $row['name'];
            $equipment[$row['equipmentid']]['quantity'] = $row['quantity'];
            $equipment[$row['equipmentid']]['type'] = 'Equipment';
        }


        return $equipment;
    }
    
   /**
     * Return a list of availables employees in a time range
     *
     * @param type $startDate
     * @param type $endDate
     * @param type $taskid - If not null the id is not considered as usage. Use to calculate free resources while editing
     * @return array of free resources
     * @author Conrado Maggi <cmaggi@vgsglobal.com>
     */
    public function getAvailableEmployee($startDate, $endDate, $taskId = '')
    {
        $db = PearDatabase::getInstance();

        $sql = "SELECT vtiger_employees.employeesid, vtiger_employees.name, vtiger_crmentity.setype 
            FROM vtiger_employees 
            INNER JOIN vtiger_crmentity ON vtiger_employees.employeesid=vtiger_crmentity.crmid
            WHERE vtiger_crmentity.deleted=0 
            AND vtiger_employees.employeesid  NOT IN (
                                        SELECT resourceid FROM vtiger_resourcedashboard
                                            INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                                            INNER JOIN vtiger_employees ON vtiger_resourcedashboard.resourceid = vtiger_employees.employeesid
                                            WHERE vtiger_projecttask.enddate >= ? AND vtiger_projecttask.startdate <= ?";

        if ($taskId != '') {
            $sql .= " AND vtiger_projecttask.projecttaskid != ?";
        }

        $sql .= " AND vtiger_projecttask.projecttaskid NOT IN (SELECT crmid FROM vtiger_crmentity WHERE setype='ProjectTask' AND deleted = 1))";


        if ($taskId != '') {
            $params = array($startDate, $endDate, $taskId);
        } else {
            $params = array($startDate, $endDate);
        }
        $result = $db->pquery($sql, $params);
        $employees = array();

        while ($row = $db->fetchByAssoc($result)) {
            $employees[$row['employeesid']]['id'] = $row['employeesid'];
            $employees[$row['employeesid']]['name'] = $row['name'];
            $employees[$row['employeesid']]['quantity'] = 1;
            $employees[$row['employeesid']]['type'] = $row['setype'];
        }


        return $employees;
    }

   /**
    * Returns HTML Resource Dashboard code
    *
    * @param type $year
    * @param type $resourceType
    * @param type $calStarts
    * @return string HTML code
    * @author Conrado Maggi <cmaggi@vgsglobal.com>
    */
    
    public function getDashboard($year = '', $resourceType = 'All', $calStarts = '')
    {
        if ($year == '') {
            $year = date('Y');
        }

        $dashArray = $this->getDashboardQuery($year, $resourceType);

        $dDaysOnPage = 37;

        $html = '<div class="listViewEntriesDiv contents-bottomscroll">
        <table class="table table-bordered listViewEntriesTable" cellspacing="0" cellpadding="0">
           <thead>
            <tr class="listViewHeaders">
                <th>' . $year . '</th>
                <th>--</th>
                <th>Mo</th>
                <th>Tu</th>
                <th>We</th>
                <th>Th</th>
                <th>Fr</th>
                <th>Sa</th>
                <th>Su</th>
                <th>Mo</th>
                <th>Tu</th>
                <th>We</th>
                <th>Th</th>
                <th>Fr</th>
                <th>Sa</th>
                <th>Su</th>
                <th>Mo</th>
                <th>Tu</th>
                <th>We</th>
                <th>Th</th>
                <th>Fr</th>
                <th>Sa</th>
                <th>Su</th>
                <th>Mo</th>
                <th>Tu</th>
                <th>We</th>
                <th>Th</th>
                <th>Fr</th>
                <th>Sa</th>
                <th>Su</th>
                <th>Mo</th>
                <th>Tu</th>
                <th>We</th>
                <th>Th</th>
                <th>Fr</th>
                <th>Sa</th>
                <th>Su</th>
                <th>Mo</th>
                <th>Tu</th>
            </tr>
            </thead><tbody>';


        $resources = array_keys($dashArray);
        $rowspan = count($resources) + 1;
        if ($calStarts == '' && $year == date('Y') || $calStarts == '---' && $year == date('Y')) {
            $calStarts = date('n');
            $calEnds = 12;
        } elseif ($calStarts == '' && $year != date('Y') || $calStarts == '---' && $year != date('Y')) {
            $calStarts = 1;
            $calEnds = 12;
        } else {
            $calEnds = $calStarts;
        }


        for ($mC = $calStarts; $mC <= $calEnds; $mC++) {
            $currentDT = mktime(0, 0, 0, $mC, 1, $year);
            $html .= '<tr><td class=\'monthName\' rowspan="' . $rowspan . '"><div>' . date("F", $currentDT) . '</div></td>';
            $html .= '<td>Day</td>';
            $daysInMonth = date("t", $currentDT);

            $html .= $this->InsertBlankTd(date("N", $currentDT) - 1);

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $exactDT = mktime(0, 0, 0, $mC, $i, $year);
                if ($i == date("d") && date("m", $currentDT) == date("m")) {
                    $class = "currentDay";
                } else {
                    $class = "";
                }
                $html .= "<td class='" . $class . " days day" . date("N", $exactDT) . "'>" . $i . "</td>";
            }

            $html .= $this->InsertBlankTd($dDaysOnPage - $daysInMonth - date("N", $currentDT) + 1);
            $html .= "</tr>";

            foreach ($resources as $resource) {
                $html .= '<tr>';
                $html .= '<td>' . $resource . '</td>';
                $html .= $this->InsertBlankTd(date("N", $currentDT) - 1);

                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $exactDT = mktime(0, 0, 0, $mC, $i, $year);
                    if ($i == date("d") && date("m", $currentDT) == date("m")) {
                        $class = "currentDay";
                    } else {
                        $class = "";
                    }
                    $html .= "<td class='" . $class . " days day" . date("N", $exactDT) . "'> " . $dashArray[$resource][$exactDT] . "</td>";
                }
                $html .= $this->InsertBlankTd($dDaysOnPage - $daysInMonth - date("N", $currentDT) + 1);
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table></div>';

        return $html;
    }

    public function InsertBlankTd($numberOfTdsToAdd)
    {
        for ($i = 1; $i <= $numberOfTdsToAdd; $i++) {
            $tdString .= "<td></td>";
        }
        return $tdString;
    }
    
    /**
     * Function that build the query use to build the dashboard HTML code
     *
     * @param type $year
     * @param type $resourceType
     * @return array of resources in a given timeframe
     * @author Conrado Maggi <cmaggi@vgsglobal.com>
     */

    public function getDashboardQuery($year, $resourceType = 'All')
    {
        $db = PearDatabase::getInstance();

        $params = array(
            $year,
            $year
        );

        $sql = "SELECT vtiger_projecttask.projecttaskid, vtiger_projecttask.startdate, vtiger_projecttask.enddate, vtiger_crmentity.setype,vtiger_resourcedashboard.quantity
                    FROM vtiger_resourcedashboard 
                    INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                    INNER JOIN vtiger_crmentity ON vtiger_resourcedashboard.resourceid = vtiger_crmentity.crmid
                    WHERE vtiger_crmentity.deleted=0
                    AND vtiger_projecttask.projecttaskid NOT IN (SELECT crmid FROM vtiger_crmentity WHERE setype='ProjectTask' AND deleted = 1)
                    AND (YEAR(vtiger_projecttask.startdate)=? OR YEAR(vtiger_projecttask.enddate)=?)";

        if ($resourceType != 'All') {
            array_push($params, $resourceType);
            $sql .=" AND vtiger_crmentity.setype = ?";
        }


        $result = $db->pquery($sql, $params);
        $dashArray = array();
        if ($db->num_rows($result) > 0) {
            for ($i = 0; $i < $db->num_rows($result); $i++) {
                $startDate = $db->query_result($result, $i, 'startdate');
                $endDate = $db->query_result($result, $i, 'enddate');
                $datediff = (strtotime($endDate) - strtotime($startDate)) / 86400;

                for ($j = 0; $j <= $datediff; $j++) {
                    $date = strtotime($db->query_result($result, $i, 'startdate') . ' + ' . $j . ' day');
                    $resourceType = $db->query_result($result, $i, 'setype');
                    $dashArray[$resourceType][$date] = $dashArray[$resourceType][$date] + $db->query_result($result, $i, 'quantity');
                }
            }
        }

        if (count($dashArray) == 0 && $resourceType == 'All') {
            $dashArray = array('Employee' => array(), 'Equipment' => array(), 'Vehicles' => array());
        } elseif (count($dashArray) == 0 && $resourceType != 'All') {
            $dashArray = array($resourceType => array());
        }

        return $dashArray;
    }

    /**
     * Function to get Resources related to project tasks
     * @param Vtiger_Request $request
     * @return <html of resources list>
     * @author Conrado Maggi <cmaggi@vgsglobal.com>
     */
    public function getResources(Vtiger_Request $request)
    {
        $adb = PearDatabase::getInstance();
        $moduleName = $request->getModule();
        $parentId = $request->get('record');
        $taksName = $request->get('project_tasks');

        if ($taksName != '' && $moduleName == 'Project') {
            $taskId = $this->getTaskIdByName($parentId, $taksName);
        } elseif ($moduleName == 'ProjectTask') {
            $taskId = $parentId;
        }

        if (!$taskId) {
            $html = 'No Resources Assigned';
        }

        $resultList = $this->getResourcesQuery($taskId);

        if (!$resultList) {
            $html = 'No Resources Assigned';
        } else {
            $html = '<input type="hidden" id="task_' . $taksName . '">
            <div class="row-fluid">
            <span class="span4"><strong>Resource Name</strong></span><span class="span2"><span class="pull-right"><strong>Qty</strong></span></span><span class="span3"><span class="pull-right"><strong>Action</strong></span></span></div>';

            for ($i = 0; $i < $adb->num_rows($resultList); $i++) {
                $html .= '<div class="recentActivitiesContainer">
            <ul class="unstyled"><li><div class="row-fluid">
                <span class="span4 textOverflowEllipsis"><a href="index.php?module=' . $adb->query_result($resultList, $i, 'setype') . '&amp;view=Detail&amp;record=' . $adb->query_result($resultList, $i, 'crmid') . '">' . $adb->query_result($resultList, $i, 'name') . '</a></span><span class="span2 horizontalLeftSpacingForSummaryWidgetContents"><span class="pull-right">' . $adb->query_result($resultList, $i, 'quantity') . '</span></span><span class="span3 horizontalLeftSpacingForSummaryWidgetContents"><span class="pull-right"><a href="#" onclick="editAssoc(' . $adb->query_result($resultList, $i, 'crmid') . ',' . $taskId . ',\'' . $adb->query_result($resultList, $i, 'quantity') . '\') ">Edit</a>   | <a href="#" onclick="deleteAssoc(' . $adb->query_result($resultList, $i, 'crmid') . ',' . $taskId . ',\'' . $taksName . '\') ">Delete</a></span></span></div></li>
            </ul></div>';
            }


            $html .='</div>';
        }

        return $html;
    }

    /**
     * Function that returns all related resources to a task
     *
     * @param type $taskId
     * @return type result object
     */
    
    public function getResourcesQuery($taskId)
    {
        $adb = PearDatabase::getInstance();
        $sql = 'SELECT  vtiger_equipment.name, vtiger_crmentity.setype, sum(vtiger_resourcedashboard.quantity) as quantity,vtiger_crmentity.crmid
                        FROM vtiger_equipment 
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_equipment.equipmentid 
                        INNER JOIN vtiger_resourcedashboard ON vtiger_equipment.equipmentid = vtiger_resourcedashboard.resourceid 
                        WHERE vtiger_crmentity.deleted = 0 
                                                      AND vtiger_resourcedashboard.projecttaskid = ?
                                                      GROUP BY vtiger_equipment.equipmentid 
                                                      LIMIT 0,10
               UNION
                    SELECT  vtiger_vehicles.name, vtiger_crmentity.setype, sum(vtiger_resourcedashboard.quantity) as quantity,vtiger_crmentity.crmid
                        FROM vtiger_vehicles 
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vehicles.vehiclesid 
                        INNER JOIN vtiger_resourcedashboard ON vtiger_vehicles.vehiclesid = vtiger_resourcedashboard.resourceid 
                        WHERE vtiger_crmentity.deleted = 0 
                                                      AND vtiger_resourcedashboard.projecttaskid = ?
                                                      GROUP BY vtiger_vehicles.vehiclesid 
                                                      LIMIT 0,10
                UNION
                    SELECT  vtiger_employees.name, vtiger_crmentity.setype, vtiger_resourcedashboard.quantity,vtiger_crmentity.crmid
                        FROM vtiger_employees 
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_employees.employeesid 
                        INNER JOIN vtiger_resourcedashboard ON vtiger_employees.employeesid  = vtiger_resourcedashboard.resourceid 
                        WHERE vtiger_crmentity.deleted = 0 
                                                      AND vtiger_resourcedashboard.projecttaskid = ?
                                                      LIMIT 0,10';

        $resultList = $adb->pquery($sql, array($taskId, $taskId, $taskId));
        return $resultList;
    }
    
    /**
     * Get the task ID for a given task name and project id
     *
     * @param type $parentId - Project Id
     * @param type $taksName - Tasks Name
     * @return task crm id or false if task do not exists
     */
  
    public function getTaskIdByName($parentId, $taksName)
    {
        $adb = PearDatabase::getInstance();

        $result = $adb->pquery('SELECT projecttaskid, projecttaskname FROM vtiger_projecttask WHERE projectid=? AND projecttaskname=?', array($parentId, $taksName));

        if (!$result) {
            return false;
        } else {
            $taskId = $adb->query_result($result, 0, 'projecttaskid');

            if ($taskId == '') {
                return false;
            }
        }

        return $taskId;
    }
    
    /**
     * Function that deletes the relationship between a task and resource
     *
     * @param Vtiger_Request $request
     */

    public function deleteResourceFromTask(Vtiger_Request $request)
    {
        $adb = PearDatabase::getInstance();
        $moduleName = $request->getModule();
        $resourceId = $request->get('resourceid');
        $taskId = $request->get('taskid');
        $taksName = $request->get('project_tasks');

        $adb->pquery('DELETE FROM vtiger_resourcedashboard WHERE projecttaskid=? AND resourceid=?', array($taskId, $resourceId));
    }

    /**
     * Function that returns an array of resources related to a task
     *
     * @param type $taskid
     * @return array of resources
     */
    
    public function getResourceRelatedProjectTask($taskid)
    {
        $adb = PearDatabase::getInstance();
        $resourceArray = array();
        $resultList = $this->getResourcesQuery($taskid);

        for ($i = 0; $i < $adb->num_rows($resultList); $i++) {
            $resource['resourceid'] = $adb->query_result($resultList, $i, 'crmid');
            $resource['name'] = $adb->query_result($resultList, $i, 'name');
            $resource['setype'] = $adb->query_result($resultList, $i, 'setype');
            $resource['quantity'] = $adb->query_result($resultList, $i, 'quantity');
            array_push($resourceArray, $resource);
        }

        return $resourceArray;
    }
    
    /**
     * Function that returns an array of resources related to a project
     *
     * @param type $projectId
     * @return array of resources
     */

    public function getResourceRelatedProject($projectId)
    {
        $resourceArray = array();
        $adb = PearDatabase::getInstance();
        $sql = 'SELECT  vtiger_equipment.name, vtiger_crmentity.setype, sum(vtiger_resourcedashboard.quantity) as quantity,vtiger_crmentity.crmid
                        FROM vtiger_equipment 
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_equipment.equipmentid 
                        INNER JOIN vtiger_resourcedashboard ON vtiger_equipment.equipmentid = vtiger_resourcedashboard.resourceid
                        INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                        WHERE vtiger_crmentity.deleted = 0 
                                                      AND vtiger_projecttask.projectid = ?
                                                      GROUP BY vtiger_equipment.equipmentid 
                                                      LIMIT 0,10
               UNION
                    SELECT  vtiger_vehicles.name, vtiger_crmentity.setype, sum(vtiger_resourcedashboard.quantity) as quantity,vtiger_crmentity.crmid
                        FROM vtiger_vehicles 
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vehicles.vehiclesid 
                        INNER JOIN vtiger_resourcedashboard ON vtiger_vehicles.vehiclesid = vtiger_resourcedashboard.resourceid
                        INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                        WHERE vtiger_crmentity.deleted = 0 
                                                      AND vtiger_projecttask.projectid = ?
                                                      GROUP BY vtiger_vehicles.vehiclesid 
                                                      LIMIT 0,10
                UNION
                    SELECT  vtiger_employees.name, vtiger_crmentity.setype, vtiger_resourcedashboard.quantity,vtiger_crmentity.crmid
                        FROM vtiger_employees 
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_employees.employeesid 
                        INNER JOIN vtiger_resourcedashboard ON vtiger_employees.employeesid  = vtiger_resourcedashboard.resourceid
                        INNER JOIN vtiger_projecttask ON vtiger_resourcedashboard.projecttaskid = vtiger_projecttask.projecttaskid
                        WHERE vtiger_crmentity.deleted = 0 
                                                      AND vtiger_projecttask.projectid = ?
                                                      LIMIT 0,10';

        $resultList = $adb->pquery($sql, array($projectId, $projectId, $projectId));

        for ($i = 0; $i < $adb->num_rows($resultList); $i++) {
            $resource['name'] = $adb->query_result($resultList, $i, 'name');
            $resource['setype'] = $adb->query_result($resultList, $i, 'setype');
            $resource['quantity'] = $adb->query_result($resultList, $i, 'quantity');
            array_push($resourceArray, $resource);
        }

        return $resourceArray;
    }

    /**
     * Function that validates if assigned resources as free in a given time frame. Use to
     * validate the resource assigment before updating a task
     *
     * @param type $recordId taskid
     * @param type $startDate
     * @param type $endDate
     * @return boolean
     */
    
    public function validateTaskResources($recordId, $startDate, $endDate)
    {
        $resourcesArray = $this->getResourceRelatedProjectTask($recordId);
        $available = $this->getAvailableResources($startDate, $endDate, $recordId);

        foreach ($resourcesArray as $resource) {
            if ($this->isBusy($resource, $available)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Function that return false is atleast one of the task resources are busy
     *
     * @param type $resource
     * @param type $availableResources
     * @return boolean
     */
    
    public function isBusy($resource, $availableResources)
    {
        foreach ($availableResources as $aResource) {
            if ($resource['resourceid'] == $aResource['id']) {
                if ($resource['quantity'] <= $aResource['quantity']) {
                    return false;
                }
            }
        }

        return true;
    }
    
    /**
     * Removes the busy resources from a tasks before updating the dates.
     *
     * @param type $recordId
     * @param type $startDate
     * @param type $endDate
     */

    public function unsetBusyResources($recordId, $startDate, $endDate)
    {
        $db = PearDatabase::getInstance();
        $resourcesArray = $this->getResourceRelatedProjectTask($recordId);
        $available = $this->getAvailableResources($startDate, $endDate, $recordId);

        foreach ($resourcesArray as $resource) {
            if ($this->isBusy($resource, $available)) {
                $sql = "DELETE FROM vtiger_resourcedashboard WHERE projecttaskid=? AND resourceid=?";
                $db->pquery($sql, array($recordId, $resource['resourceid']));
            }
        }
    }
    

    public function getAvailableResources($startDate, $endDate, $recordId)
    {
        $startDate = DateTimeField::convertToDBFormat($startDate);
        $endDate = DateTimeField::convertToDBFormat($endDate);

        $availableVehicles = $this->getAvailableVehicles($startDate, $endDate, $recordId);
        $availableEmployee = $this->getAvailableEmployee($startDate, $endDate, $recordId);
        $availableEquipment = $this->getAvailableEquiptment($startDate, $endDate, $recordId);

        return array_merge($availableVehicles, $availableEmployee, $availableEquipment);
    }
    
    /**
     * Return an array of free qty for each resourceid
     *
     * @param type $resourceid
     * @param type $taskId
     * @param type $allocatedQty
     * @return type
     */

    public function getResourceFreeQtyArray($resourceid, $taskId, $allocatedQty)
    {
        $db = PearDatabase::getInstance();

        $resourceModel = Vtiger_Record_Model::getInstanceById($resourceid);
        $resourceType = $resourceModel->getModuleName();

        $projectTask = Vtiger_Record_Model::getInstanceById($taskId);
        $startDate = $projectTask->get('startdate');
        $endDate = $projectTask->get('enddate');


        switch ($resourceType) {
            case 'Vehicles':
                $availableResources = $this->getAvailableVehicles($startDate, $endDate, $taskId);

                break;
            case 'Equipment':
                $availableResources = $this->getAvailableEquiptment($startDate, $endDate, $taskId);
                break;

            default:
                $availableResources = $this->getAvailableEmployee($startDate, $endDate, $taskId);
                break;
        }

        $resource = array();
        foreach ($availableResources as $resource) {
            if ($resource['id'] == $resourceid) {
                $resources[0] = $resource;
                $resources[0]['allocatedqty'] = $allocatedQty;
                return $resources;
            }
        }
    }
}
