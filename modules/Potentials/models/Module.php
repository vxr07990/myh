<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class Potentials_Module_Model extends Vtiger_Module_Model
{
    public function isWorkflowSupported()
    {
        return false;
    }

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams)
    {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);

        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => 'LBL_DASHBOARD',
            'linkurl' => $this->getDashBoardUrl(),
            'linkicon' => '',
        );
        
        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        if ($permission) {
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }
        
        return $parentQuickLinks;
    }
    
    public function getPopupViewFieldsList()
    {
        //file_put_contents("logs/devLog.log", "\n POTENTIALS GETPOPUPVIEWFIELDSLIST!!!", FILE_APPEND);
        $summaryFieldsList = $this->getSummaryViewFieldsList();

        if (count($summaryFieldsList) > 0) {
            $popupFields = array_keys($summaryFieldsList);
        } else {
            $popupFields = array_values($this->getRelatedListFields());
        }
        //file_put_contents("logs/devLog.log", "\n POPUP FIELDS: ".print_r($popupFields, true), FILE_APPEND);
        return $popupFields;
    }

    /**
     * Function returns number of Open Potentials in each of the sales stage
     * @param <Integer> $owner - userid
     * @return <Array>
     */
    public function getPotentialsCountBySalesStage($owner, $dateFilter)
    {
        $db = PearDatabase::getInstance();

        if (!$owner) {
            $currenUserModel = Users_Record_Model::getCurrentUserModel();
            $owner = $currenUserModel->getId();
        } elseif ($owner === 'all') {
            $owner = '';
        }

        $params = array();
        if (!empty($owner)) {
            $ownerSql =  ' AND smownerid = ? ';
            $params[] = $owner;
        }
        if (!empty($dateFilter)) {
            $dateFilterSql = ' AND closingdate BETWEEN ? AND ? ';
            $params[] = $dateFilter['start'];
            $params[] = $dateFilter['end'];
        }

        $result = $db->pquery('SELECT COUNT(*) count, sales_stage FROM vtiger_potential
						INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
						AND deleted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()). $ownerSql . $dateFilterSql . ' AND sales_stage NOT IN ("Closed Won", "Closed Lost")
							GROUP BY sales_stage ORDER BY count desc', $params);
        
        $response = array();
        //$userModel = Users_Record_Model::getCurrentUserModel();
        for ($i=0; $i<$db->num_rows($result); $i++) {
            $saleStage = $db->query_result($result, $i, 'sales_stage');
        //	if($userModel::getExtraPermission($saleStage['crmid']) == 1){	//Check if user is a participating agent for this event
                $response[$i][0] = $saleStage;
            $response[$i][1] = $db->query_result($result, $i, 'count');
            $response[$i][2] = vtranslate($saleStage, $this->getName());
        //	}
        }
        return $response;
    }

    /**
     * Function returns number of Open Potentials for each of the sales person
     * @param <Integer> $owner - userid
     * @return <Array>
     */
    public function getPotentialsCountBySalesPerson()
    {
        $db = PearDatabase::getInstance();
        //TODO need to handle security
        $params = array();
        $result = $db->pquery('SELECT COUNT(*) AS count, concat(first_name," ",last_name) as last_name, vtiger_potential.sales_stage FROM vtiger_potential
						INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid AND vtiger_users.status="ACTIVE"
						AND vtiger_crmentity.deleted = 0'.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).'
						INNER JOIN vtiger_sales_stage ON vtiger_potential.sales_stage =  vtiger_sales_stage.sales_stage 
						GROUP BY smownerid, sales_stage ORDER BY vtiger_sales_stage.sortorderid', $params);

        $response = array();
        //$userModel = Users_Record_Model::getCurrentUserModel();
        for ($i=0; $i<$db->num_rows($result); $i++) {
            $row = $db->query_result_rowdata($result, $i);
            //if($userModel::getExtraPermission($row['crmid']) == 1){	//Check if user is a participating agent for this event
                $response[$i]['count'] = $row['count'];
            $response[$i]['last_name'] = decode_html($row['last_name']);
            $response[$i]['sales_stage'] = $row['sales_stage'];
                //$response[$i][2] = $row['']
            //}
        }
        return $response;
    }

    /**
     * Function returns Potentials Amount for each Sales Person
     * @return <Array>
     */
    public function getPotentialsPipelinedAmountPerSalesPerson()
    {
        $db = PearDatabase::getInstance();
        //TODO need to handle security
        $params = array();
        $result = $db->pquery('SELECT sum(amount) AS amount, concat(first_name," ",last_name) as last_name, vtiger_potential.sales_stage FROM vtiger_potential
						INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid AND vtiger_users.status="ACTIVE"
						AND vtiger_crmentity.deleted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).
                        'INNER JOIN vtiger_sales_stage ON vtiger_potential.sales_stage =  vtiger_sales_stage.sales_stage 
						WHERE vtiger_potential.sales_stage NOT IN ("Closed Won", "Closed Lost")
						GROUP BY smownerid, sales_stage ORDER BY vtiger_sales_stage.sortorderid', $params);
        //$userModel = Users_Record_Model::getCurrentUserModel();
        for ($i=0; $i<$db->num_rows($result); $i++) {
            $row = $db->query_result_rowdata($result, $i);
            //if($userModel::getExtraPermission($row['crmid']) == 1){	//Check if user is a participating agent for this event
                $row['last_name'] = decode_html($row['last_name']);
            $data[] = $row;
            //}
        }
        return $data;
    }

    /**
     * Function returns Total Revenue for each Sales Person
     * @return <Array>
     */
    public function getTotalRevenuePerSalesPerson($dateFilter)
    {
        $db = PearDatabase::getInstance();
        //TODO need to handle security
        $params = array();
        $params[] = 'Closed Won';
        if (!empty($dateFilter)) {
            $dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
            //client is not giving time frame so we are appending it
            $params[] = $dateFilter['start']. ' 00:00:00';
            $params[] = $dateFilter['end']. ' 23:59:59';
        }
        
        $result = $db->pquery('SELECT sum(amount) amount, concat(first_name," ",last_name) as last_name,vtiger_users.id as id,DATE_FORMAT(closingdate, "%d-%m-%Y") AS closingdate  FROM vtiger_potential
						INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_crmentity.smownerid AND vtiger_users.status="ACTIVE"
						AND vtiger_crmentity.deleted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).'WHERE sales_stage = ? '.' '.$dateFilterSql.' GROUP BY smownerid', $params);
        $data = array();
        //$userModel = Users_Record_Model::getCurrentUserModel();
        for ($i=0; $i<$db->num_rows($result); $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $row['last_name'] = decode_html($row['last_name']);
            $data[] = $row;
        }
        return $data;
    }

     /**
     * Function returns Top Potentials Header
     *
     */
    
    public function getTopPotentialsHeader()
    {
        $headerArray = array('potentialname' => 'Potential Name');
        $fieldsToDisplay=  array("amount","related_to");
        $moduleModel = Vtiger_Module_Model::getInstance('Potentials');
        foreach ($fieldsToDisplay as $value) {
            $fieldInstance = Vtiger_Field_Model::getInstance($value, $moduleModel);
            if ($fieldInstance->isViewable()) {
                $headerArray = array_merge($headerArray, array($value =>$fieldInstance->label));
            }
        }
        return $headerArray;
    }
    
    /**
     * Function returns Top Potentials
     * @return <Array of Vtiger_Record_Model>
     */
    public function getTopPotentials($pagingModel)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();
   
        $moduleModel = Vtiger_Module_Model::getInstance('Potentials');
        $fieldsToDisplay=  array("amount","related_to");
         
        $query = "SELECT crmid , potentialname " ;
        foreach ($fieldsToDisplay as $value) {
            $fieldInstance = Vtiger_Field_Model::getInstance($value, $moduleModel);

            if ($fieldInstance->isViewable()) {
                $query= $query. ', ' .$value;
            }
        }
        
        $query = $query . " FROM vtiger_potential
						INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
							AND deleted = 0 ".Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName())."
						WHERE sales_stage NOT IN ('Closed Won', 'Closed Lost') AND amount > 0
						ORDER BY amount DESC LIMIT ".$pagingModel->getStartIndex().", ".$pagingModel->getPageLimit()."";
        $result = $db->pquery($query, array());
        //$userModel = Users_Record_Model::getCurrentUserModel();
        $models = array();
        for ($i=0; $i<$db->num_rows($result); $i++) {
            //if($userModel::getExtraPermission($db->query_result($result, $i, 'crmid')) == 1){	//Check if user is a participating agent for this event
                $modelInstance = Vtiger_Record_Model::getCleanInstance('Potentials');
            $modelInstance->setId($db->query_result($result, $i, 'crmid'));
            $modelInstance->set('amount', $db->query_result($result, $i, 'amount'));
            $modelInstance->set('potentialname', $db->query_result($result, $i, 'potentialname'));
            $modelInstance->set('related_to', $db->query_result($result, $i, 'related_to'));
            $models[] = $modelInstance;
            //}
        }
        return $models;
    }

    /**
     * Function returns Potentials Forecast Amount
     * @return <Array>
     */
    public function getForecast($closingdateFilter, $dateFilter)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();

        $params = array();
        $params[] = $currentUser->getId();
        if (!empty($closingdateFilter)) {
            $closingdateFilterSql = ' AND closingdate BETWEEN ? AND ? ';
            $params[] = $closingdateFilter['start'];
            $params[] = $closingdateFilter['end'];
        }
        
        if (!empty($dateFilter)) {
            $dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
            //client is not giving time frame so we are appending it
            $params[] = $dateFilter['start']. ' 00:00:00';
            $params[] = $dateFilter['end']. ' 23:59:59';
        }
        
        $result = $db->pquery('SELECT forecast_amount, DATE_FORMAT(closingdate, "%m-%d-%Y") AS closingdate FROM vtiger_potential
					INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
					AND deleted = 0 AND smownerid = ? WHERE closingdate >= CURDATE() AND sales_stage NOT IN ("Closed Won", "Closed Lost")'.
                    ' '.$closingdateFilterSql.$dateFilterSql,
                    $params);

        $forecast = array();
        //$userModel = Users_Record_Model::getCurrentUserModel();
        for ($i=0; $i<$db->num_rows($result); $i++) {
            //if($userModel::getExtraPermission($db->query_result($result, $i, 'crmid')) == 1){	//Check if user is a participating agent for this event
                $row = $db->query_result_rowdata($result, $i);
            $forecast[] = $row;
            //}
        }
        return $forecast;
    }

    /**
     * Function to get relation query for particular module with function name
     * @param <record> $recordId
     * @param <String> $functionName
     * @param Vtiger_Module_Model $relatedModule
     * @return <String>
     */
    public function getRelationQuery($recordId, $functionName, $relatedModule)
    {
        if ($functionName === 'get_activities') {
            $userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

            $query = "SELECT CASE WHEN (vtiger_users.user_name not like '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name,
						vtiger_crmentity.*, vtiger_activity.activitytype, vtiger_activity.subject, vtiger_activity.date_start, vtiger_activity.time_start,
						vtiger_activity.recurringtype, vtiger_activity.due_date, vtiger_activity.time_end, vtiger_activity.visibility, vtiger_seactivityrel.crmid AS parent_id,
						CASE WHEN (vtiger_activity.activitytype = 'Task') THEN (vtiger_activity.status) ELSE (vtiger_activity.eventstatus) END AS status
						FROM vtiger_activity
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
						LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
						LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
							WHERE vtiger_crmentity.deleted = 0 AND vtiger_activity.activitytype <> 'Emails'
								AND vtiger_seactivityrel.crmid = ".$recordId;

            $relatedModuleName = $relatedModule->getName();
            $query .= $this->getSpecificRelationQuery($relatedModuleName);
            $nonAdminQuery = $this->getNonAdminAccessControlQueryForRelation($relatedModuleName);
            if ($nonAdminQuery) {
                $query = appendFromClauseToQuery($query, $nonAdminQuery);
            }
        } else {
            $query = parent::getRelationQuery($recordId, $functionName, $relatedModule);
        }

        return $query;
    }
    
    /**
     * Function returns Potentials Amount for each Sales Stage
     * @return <Array>
     */
    public function getPotentialTotalAmountBySalesStage()
    {
        //$currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();

        $picklistValues = Vtiger_Util_Helper::getPickListValues('sales_stage');
        $data = array();
        //$userModel = Users_Record_Model::getCurrentUserModel();
        foreach ($picklistValues as $key => $picklistValue) {
            $result = $db->pquery('SELECT SUM(amount) AS amount FROM vtiger_potential
								   INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
								   AND deleted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).' WHERE sales_stage = ?', array($picklistValue));
            $num_rows = $db->num_rows($result);
            for ($i=0; $i<$num_rows; $i++) {
                $values = array();
                //if($userModel::getExtraPermission($db->query_result($result, $i, 'crmid')) == 1){	//Check if user is a participating agent for this event
                    $amount = $db->query_result($result, $i, 'amount');
                if (!empty($amount)) {
                    $values[0] = $db->query_result($result, $i, 'amount');
                    $values[1] = vtranslate($picklistValue, $this->getName());
                    $data[] = $values;
                    //}
                }
            }
        }
        return $data;
    }

    /**
     * Function to get list view query for popup window
     * @param <String> $sourceModule Parent module
     * @param <String> $field parent fieldname
     * @param <Integer> $record parent id
     * @param <String> $listQuery
     * @return <String> Listview Query
     */
    public function getQueryByModuleField($sourceModule, $field, $record, $listQuery)
    {
        if (in_array($sourceModule, array('Products', 'Services'))) {
            if ($sourceModule === 'Products') {
                $condition = " vtiger_potential.potentialid NOT IN (SELECT crmid FROM vtiger_seproductsrel WHERE productid = '$record')";
            } elseif ($sourceModule === 'Services') {
                $condition = " vtiger_potential.potentialid NOT IN (SELECT relcrmid FROM vtiger_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM vtiger_crmentityrel WHERE relcrmid = '$record') ";
            }

            $pos = stripos($listQuery, 'where');
            if ($pos) {
                $split = spliti('where', $listQuery);
                $overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
            } else {
                $overRideQuery = $listQuery . ' WHERE ' . $condition;
            }
            return $overRideQuery;
        }
    }

    /**
     * Function returns query for module record's search
     * @param <String> $searchValue - part of record name (label column of crmentity table)
     * @param <Integer> $parentId - parent record id
     * @param <String> $parentModule - parent module name
     * @return <String> - query
     */
    public function getSearchRecordsQuery($searchValue, $parentId=false, $parentModule=false)
    {
        if ($parentId && in_array($parentModule, array('Accounts', 'Contacts'))) {
            $query = "SELECT * FROM vtiger_crmentity
						INNER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_crmentity.crmid
						WHERE deleted = 0 AND vtiger_potential.related_to = $parentId AND label like '%$searchValue%'";
            return $query;
        }
        return parent::getSearchRecordsQuery($parentId, $parentModule);
    }

    public function getClosingRatio($pagingModel, $user)
    {
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_potential.sales_stage, vtiger_users.first_name, vtiger_users.last_name FROM vtiger_potential JOIN vtiger_crmentity ON vtiger_potential.potentialid=vtiger_crmentity.crmid JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id WHERE vtiger_potential.sales_stage='Closed Won' OR vtiger_potential.sales_stage='Closed Lost'";
        $result = $db->pquery($sql, array());
        
        $oppsWon = array();
        $opps = array();
        $ratios = array();
        $names = array();
        
        if ($user == '') {
            $user = Users_Record_Model::getCurrentUserModel()->getId();
        }
        
        if ($user != 'all' && $user != '') {
            //file_put_contents('logs/WidgetTest.log', date('Y-m-d H:i:s - ')."UserId $user provided\n", FILE_APPEND);
            $userModel = Users_Record_Model::getInstanceById($user, 'Users');
            //file_put_contents('logs/WidgetTest.log', date('Y-m-d H:i:s - ')."User model created\n", FILE_APPEND);
            $subUserList = $userModel->getRoleBasedSubordinateUsers();
            //file_put_contents('logs/WidgetTest.log', date('Y-m-d H:i:s - ')."Subordinate Users retrieved\n", FILE_APPEND);
            //file_put_contents('logs/WidgetTest.log', date('Y-m-d H:i:s - ').print_r($subUserList, true)."\n", FILE_APPEND);
            while ($resultrow = $db->fetch_array($result)) {
                if ($user == $resultrow['smownerid'] || array_key_exists($resultrow['smownerid'], $subUserList)) {
                    $names[$resultrow['smownerid']] = $resultrow['first_name'].' '.$resultrow['last_name'];
                    if (!is_array($oppsWon[$resultrow['smownerid']])) {
                        $oppsWon[$resultrow['smownerid']] = array();
                    }
                    $opps[$resultrow['smownerid']][] = $resultrow;
                    if ($resultrow['sales_stage'] == 'Closed Won') {
                        $oppsWon[$resultrow['smownerid']][] = $resultrow;
                    }
                }
            }
            foreach ($oppsWon as $userId=>$oppId) {
                $ratios[$names[$userId]] = number_format((float)(count($oppsWon[$userId]) / count($opps[$userId]) * 100), 2, '.', '');
            }
            //file_put_contents('logs/WidgetTest.log', date('Y-m-d H:i:s - ').print_r($ratios, true)."\n", FILE_APPEND);

            return $ratios;
        } else {
            while ($resultrow = $db->fetch_array($result)) {
                $opps[] = $resultrow['crmid'];
                if ($resultrow['sales_stage'] == 'Closed Won') {
                    $oppsWon[] = $resultrow['crmid'];
                }
            }
            if (count($opps) > 0) {
                return number_format((float)(count($oppsWon) / count($opps) * 100), 2, '.', '');
            } else {
                return 100;
            }
        }
    }
}
