<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class Opportunities_Module_Model extends Potentials_Module_Model
{
    public function isWorkflowSupported()
    {
		return true;
	}

    /**
     * Function returns number of Open Potentials in each of the sales stage
     * @param <Integer> $owner - userid
     * @return <Array>
     */
    public function getPotentialsCountBySalesStage($owner, $dateFilter)
    {
        //file_put_contents('logs/devLog.log', "\n In the Opportunities Version", FILE_APPEND);
        $db = PearDatabase::getInstance();
        $allFilter = false;
        //file_put_contents('logs/devLog.log',"\n owner : {$owner}",FILE_APPEND);
        if (empty($owner)) {
            $userModel = Users_Record_Model::getCurrentUserModel();
            $owner = $userModel->getId();
        } elseif ($owner == 'all') {
            $userModel = Users_Record_Model::getCurrentUserModel();
            $owner = $userModel->getId();
            $allFilter = true;
            //file_put_contents('logs/devLog.log',"\n all filter Owner : {$owner}", FILE_APPEND);
        }
        $params = [];
        $ownerSql = '';
        if (vtws_getOwnerType($owner) == 'Users') {
            //handle users here
            //find out their depth to handle them appropriately
			if($allFilter){
				if(!$userModel->isAdminUser()){
					$accessibleUsers = array_keys($userModel->getAccessibleUsers());
					$ownerSql .= ' AND smownerid IN ('.generateQuestionMarks($accessibleUsers).')';
					$params = $params + $accessibleUsers;
				}
			}else{
				$ownerSql = ' AND ( smownerid = ? ) ';
				$params[] = $owner;
			}
        } elseif (vtws_getOwnerType($owner) == 'Groups') {
            //handle groups here
            $ownerSql .= " AND vtiger_crmentity.smownerid = ?";
            $params[] = $owner;
        }
        if (!empty($dateFilter)) {
            $dateFilterSql = ' AND closingdate BETWEEN ? AND ? ';
            $params[] = $dateFilter['start'];
            $params[] = $dateFilter['end'];
        }
        $agentSql = $this->generateAgentSql();

        $permissionSql = Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName());
        if(getenv('INSTANCE_NAME') == 'sirva') {
            $sql = 'SELECT COUNT(*) count, sales_stage FROM vtiger_potential
                            INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
                            AND deleted = 0 '. $agentSql . $permissionSql . $ownerSql . $dateFilterSql . ' AND sales_stage NOT IN ("Closed Won", "Closed Lost")
                                GROUP BY sales_stage ORDER BY count desc';
        }else {
            $sql = 'SELECT COUNT(*) count, opportunitystatus FROM vtiger_potential
                            INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
                            AND deleted = 0 '.$ownerSql . $dateFilterSql . $permissionSql .' AND opportunitystatus NOT IN ("Closed Won", "Closed Lost")
                                GROUP BY opportunitystatus ORDER BY count desc';
        }

        $result = $db->pquery($sql, $params);
        $response = array();
        $response['owner'] = $owner;
        for ($i=0; $i<$db->num_rows($result); $i++) {
            $saleStage = $db->query_result($result, $i, 'opportunitystatus');
            $response[$i][0] = $saleStage;
            $response[$i][1] = $db->query_result($result, $i, 'count');
			//Amazingly this vtranslate IS perfectly fine!  see search params uses [0]...
            $response[$i][2] = vtranslate($saleStage, $this->getName());
        }
        //file_put_contents('logs/devLog.log', "\n response : ".print_r($response,true)."\n owner : {$owner} \n dateFilter : {$dateFilter}", FILE_APPEND);
        return $response;
    }

    /**
     * Function returns number of Open Potentials for each of the sales person
     * @param <Integer> $owner - userid
     * @return <Array>
     */
    public function getPotentialsCountBySalesPerson($userId = false)
    {
        //file_put_contents('logs/devLog.log',"\n in the Opp version of CountBySalesPerson", FILE_APPEND);
        $db = PearDatabase::getInstance();
        if(empty($userId)) {
            $userModel = Users_Record_Model::getCurrentUserModel();
            $userId = $userModel->getId();
        }else{
			$userModel = Users_Record_Model::getInstanceById($userId);
		}
		
        $limitSql = '';
        $sql = "SELECT depth FROM `vtiger_role` JOIN `vtiger_user2role` ON `vtiger_role`.roleid = `vtiger_user2role`.roleid AND `vtiger_user2role`.userid = ?";
        $result = $db->pquery($sql, [$userId]);
        $depth = $result->fetchRow()[0];
        if ($depth > 5) {
            //this will probably change but for now agency users only see stuff assigned to them, vanline gets everything, which also needs to change
            $limitSql = 'AND vtiger_potential.sales_person = ?';
        }
		
		if(!$userModel->isAdminUser()){
			$accesibleAgents = array_keys($userModel->getAccessibleAgentsForUser());
			$agestSQL = ' AND vtiger_crmentity.agentid IN ( '. generateQuestionMarks($accesibleAgents).' ) ';
			$params = $accesibleAgents;
		}else{
			$agestSQL = '';
			$params = array();
		}
	//Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()) EMPTY FUNCTION
        $sql = 'SELECT COUNT(*) AS count, vtiger_crmentity.smownerid as owner,vtiger_potential.opportunitystatus FROM vtiger_potential
						INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
						AND vtiger_crmentity.deleted = 0 '.$agestSQL.' AND 
						vtiger_potential.opportunitystatus IS NOT NULL '.$limitSql;
						//INNER JOIN vtiger_sales_stage ON vtiger_potential.sales_stage =  vtiger_sales_stage.sales_stage
		$sql .= 'GROUP BY opportunitystatus'; 
        //if (!empty($limitSql)) {
        //    $result = $db->pquery($sql, [$userId]);
        //} else {
        //    $result = $db->pquery($sql, []);
        //}
		if (!empty($limitSql))
			array_push ($params, $userId);
		$result = $db->pquery($sql, $params);
        //$result = $db->pquery($sql, [$userId]);
        //file_put_contents('logs/devLog.log',"\n this query : \n".$sql, FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n this result : ".print_r($result,true),FILE_APPEND);

        $response = array();

		for ($i=0; $i<$db->num_rows($result); $i++) {
            $row = $db->query_result_rowdata($result, $i);
            $response[$i][0] = $row['count'];
            $salesStageVal = $row['opportunitystatus'];
            if ($salesStageVal == '') {
                $salesStageVal = 'LBL_BLANK';
            }
            $response[$i][1] = vtranslate($salesStageVal, $this->getName());
            $response[$i][2] = $salesStageVal;
			$response[$i]['opportunitystatus'] = $salesStageVal;
			
			$auxUserModel = Users_Record_Model::getInstanceById($row['owner']);
			
			$response[$i]['last_name'] = $auxUserModel->get('last_name');
        }
		
        //file_put_contents('logs/devLog.log',"\n response from Opp getPotentialsCountBySalesPerson : ".print_r($response,true),FILE_APPEND);
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
		//$params = array();
		$userModel = Users_Record_Model::getCurrentUserModel();
		$accesibleAgents = array_keys($userModel->getAccessibleAgentsForUser());
		$params = array();
		$sql = 'SELECT vtiger_crmentity.crmid as crmid, sum(amount) AS amount, vtiger_potential.opportunitystatus FROM vtiger_potential
			INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
			'.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).' INNER JOIN vtiger_opportunitystatus ON vtiger_potential.opportunitystatus =  vtiger_opportunitystatus.opportunitystatus
			WHERE vtiger_potential.opportunitystatus NOT IN ("Closed Won", "Closed Lost") AND vtiger_crmentity.deleted = 0';
				
		if(!$userModel->isAdminUser()){
			$sql .=	' AND agentid IN ( '. generateQuestionMarks($accesibleAgents).' )';
			$params = array($accesibleAgents);
		}
				
		$sql .=	' GROUP BY opportunitystatus ORDER BY vtiger_opportunitystatus.sortorderid';
		//file_put_contents('logs/devLog.log',"\n getPotentialsPipelinedAmountPerSalesPerson sql : {$sql}", FILE_APPEND);
		
		$result = $db->pquery($sql, $params);
		
        while ($row =& $result->fetchRow()) {
			//$row['last_name'] = decode_html($row['last_name']);
			//don't vtranslate this here, because of the search params.
			$row['sales_stage'] = vtranslate('Forecast Amount', $this->getName());
			$data[] = $row;
		}
		return $data;
	}

	/**
	 * Function returns Total Revenue for each Sales Person
	 * @return <Array>
	 */
    public function getTotalRevenuePerSalesPerson($dateFilter)
    {
		//file_put_contents('logs/devLog.log',"\n in TotalRevPerSP",FILE_APPEND);
		$db = PearDatabase::getInstance();
        $userModel = Users_Record_Model::getCurrentUserModel();

		//TODO need to handle security
        // Better safe than sorry.
        $agentSql = $this->generateAgentSql();


		$params = array();
		$params[] = 'Closed Won';
        if (!empty($dateFilter)) {
			$dateFilterSql = ' AND createdtime BETWEEN ? AND ? ';
			//client is not giving time frame so we are appending it
			$params[] = $dateFilter['start']. ' 00:00:00';
			$params[] = $dateFilter['end']. ' 23:59:59';
		}
		$sql = 'SELECT sum(amount) amount, concat(first_name," ",last_name) as last_name,vtiger_users.id as id,DATE_FORMAT(closingdate, "%d-%m-%Y") AS closingdate  FROM vtiger_potential
						INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_potential.sales_person AND vtiger_users.status="ACTIVE"
						AND vtiger_crmentity.deleted = 0 '.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).'WHERE sales_stage = ? '.$agentSql.' '.$dateFilterSql.' GROUP BY vtiger_potential.sales_person';
		$result = $db->pquery($sql, $params);
		$data = array();
		$userModel = Users_Record_Model::getCurrentUserModel();
        for ($i=0; $i<$db->num_rows($result); $i++) {
			$row = $db->query_result_rowdata($result, $i);
			$row['last_name'] = decode_html($row['last_name']);
			$data[] = $row;
		}
		//file_put_contents('logs/devLog.log',"\n data return totalRevPerSP : ".print_r($data,true),FILE_APPEND);
		return $data;
	}

	/**
	 * Function returns Top Potentials
	 * @return <Array of Vtiger_Record_Model>
	 */
    public function getTopPotentials($pagingModel)
    {
		$currentUser = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();
        $agentSql = $this->generateAgentSql();

		$query = "SELECT crmid, amount, potentialname, related_to FROM vtiger_potential
						INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
							AND deleted = 0 ".$agentSql.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName())."
						WHERE sales_stage NOT IN ('Closed Won', 'Closed Lost') AND amount > 0
						ORDER BY amount DESC LIMIT ".$pagingModel->getStartIndex().", ".$pagingModel->getPageLimit()."";
		$result = $db->query($query);
		$userModel = Users_Record_Model::getCurrentUserModel();
		$models = array();
        for ($i=0; $i<$db->num_rows($result); $i++) {
            // Whatever that thing is, is completely commented out. So although I get the idea, frankly, maybe another time.
            //if ($userModel::getExtraPermission($db->query_result($result, $i, 'crmid')) == 1) {    //Check if user is a participating agent for this event
            $modelInstance = Vtiger_Record_Model::getCleanInstance('Opportunities');
            $modelInstance->setId($db->query_result($result, $i, 'crmid'));
            $modelInstance->set('amount', $db->query_result($result, $i, 'amount'));
            $modelInstance->set('potentialname', $db->query_result($result, $i, 'potentialname'));
            $modelInstance->set('related_to', $db->query_result($result, $i, 'related_to'));
            $models[] = $modelInstance;
			//}
		}
		return $models;
	}

    public function generateAgentSql($user) {
        if($user) {
            $userModel = Users_Record_Model::getInstanceById($user, "Users");
        }else {
            $userModel = Users_Record_Model::getCurrentUserModel();
        }

        if($userModel->isAdminUser()) {
            return "";
        }

        $agentSql = " AND (";
        if($userModel) {
            $agents = explode('|##|', $userModel->get('agent_ids'));
            for($i = 0; $i < count($agents); $i++) {
                $agent = trim($agents[$i]);
                
                $agentSql .= "(agentid = '{$agent}')";
                if($i + 1 < count($agents)) {
                    $agentSql .= " OR ";
                }
            }
        }
        if($agentSql == " AND (") {
            $agentSql = "";
        }else {
            $agentSql .= ")";
        }

        return $agentSql;
    }

	/**
	 * Function returns Potentials Amount for each Sales Stage
	 * @return <Array>
	 */
    public function getPotentialTotalAmountBySalesStage()
    {
		//$currentUser = Users_Record_Model::getCurrentUserModel();
		$db = PearDatabase::getInstance();

        $agentSql = $this->generateAgentSql();
		$picklistValues = Vtiger_Util_Helper::getPickListValues('sales_stage');
		$data = array();
		$userModel = Users_Record_Model::getCurrentUserModel();
		foreach ($picklistValues as $key => $picklistValue) {
            $sql = 'SELECT vtiger_crmentity.crmid AS crmid, SUM(amount) AS amount FROM vtiger_potential
								   INNER JOIN vtiger_crmentity ON vtiger_potential.potentialid = vtiger_crmentity.crmid
								   AND deleted = 0 '.$agentSql.Users_Privileges_Model::getNonAdminAccessControlQuery($this->getName()).' WHERE sales_stage = ?';
			$result = $db->pquery($sql, array($picklistValue));
			$num_rows = $db->num_rows($result);
            for ($i=0; $i<$num_rows; $i++) {
				$values = array();
                // This whole function is commented out so this is just breaking it now.
                //if ($userModel::getExtraPermission($db->query_result($result, $i, 'crmid')) == 1) {    //Check if user is a participating agent for this event
                $amount = $db->query_result($result, $i, 'amount');
                if (!empty($amount)) {
                    $values[0] = $db->query_result($result, $i, 'amount');
                    //don't vtranslate this here, because of the search params.
                    //$values[1] = vtranslate($picklistValue, $this->getName());
                    $values[1] = $picklistValue;
                    $data[] = $values;
                }
				//}
			}
		}
		return $data;
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
			return number_format((float)(count($oppsWon) / count($opps) * 100), 2, '.', '');
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

	/**
	 * Function searches the records in the module, if parentId & parentModule
	 * is given then searches only those records related to them.
	 * @param <String> $searchValue - Search value
	 * @param <Integer> $parentId - parent recordId
	 * @param <String> $parentModule - parent module name
	 * @return <Array of Vtiger_Record_Model>
	 */
    public function searchRecord($searchValue, $parentId=false, $parentModule=false, $relatedModule=false)
    {
        if (!empty($searchValue) && empty($parentId) && empty($parentModule)) {
			$matchingRecords = Opportunities_Record_Model::getSearchResult($searchValue, $this->getName());
        } elseif ($parentId && $parentModule) {
			$db = PearDatabase::getInstance();
			$result = $db->pquery($this->getSearchRecordsQuery($searchValue, $parentId, $parentModule), array());
			$noOfRows = $db->num_rows($result);

			$moduleModels = array();
			$matchingRecords = array();
            for ($i=0; $i<$noOfRows; ++$i) {
				$row = $db->query_result_rowdata($result, $i);

				//file_put_contents("logs/Module.log",  print_r($row, true), FILE_APPEND);

                if (Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
					$row['id'] = $row['crmid'];
					$moduleName = $row['setype'];
                    if (!array_key_exists($moduleName, $moduleModels)) {
						$moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
					}
					$moduleModel = $moduleModels[$moduleName];
					$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
					$recordInstance = new $modelClassName();
					$matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
				}
			}
		}

		return $matchingRecords;
	}

    /**
     * @param $sourceModule
     * @param $field
     * @param $record
     * @param $listQuery
     * @return string
     */
    public function getQueryByModuleField($sourceModule, $field, $record, $listQuery)
    {
        $overRideQuery = '';
        $db = PearDatabase::getInstance();
        if ($sourceModule == 'Opportunities') {
            $request = new Vtiger_Request($_REQUEST, $_REQUEST);
            if (getenv('INSTANCE_NAME') == 'sirva' && $request->get('module') == 'Opportunities' && $request->get('search_value') != '') {
                //                    $split = spliti('where', $listQuery);
                //                $split[0] .= ' INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
                //							LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_potential.contact_id
                //                            LEFT JOIN vtiger_contactsubdetails ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_potential.contact_id';
                $searchValue = $db->sql_escape_string($request->get('search_value'));
                $condition   = "(vtiger_contactdetails.phone LIKE '%$searchValue%' 
                                    OR vtiger_contactdetails.mobile LIKE '%$searchValue%' 
                                    OR vtiger_contactsubdetails.homephone LIKE '%$searchValue%' 
                                    OR vtiger_contactsubdetails.otherphone LIKE '%$searchValue%')";
                // Begin - Find duplicate conditions
                $appendCondition = '';
                if ($record && isRecordExists($record)) {
                    $recordModel            = Vtiger_Record_Model::getInstanceById($record, 'Opportunities');
                    $recordRegistrationDate = $recordModel->get('registration_date');
                    if ($recordRegistrationDate == NULL) {
                        $recordRegistrationDate = '';
                    }
                    // Don't know why this was/is here but I'm commenting it out to fix an issue with related/duplicate opps.
                    // if ($recordModel->get('sales_stage') == 'Closed Won' && $recordModel->get('register_sts_number')) {
                        // // First 4 chars of last name
                        // $first4CharsOfContactLastName = '';
                        // if ($recordModel->get('contact_id') && isRecordExists($recordModel->get('contact_id'))) {
                            // $contactRecordModel           = Vtiger_Record_Model::getInstanceById($recordModel->get('contact_id'), 'Contacts');
                            // $first4CharsOfContactLastName = $contactRecordModel->get('lastname');
                            // if (strlen($first4CharsOfContactLastName) > 4) {
                                // $first4CharsOfContactLastName = substr($first4CharsOfContactLastName, 0, 4);
                            // }
                        // }
                        // // created time
                        // $dateFilter = DateTimeField::convertToUserFormat(date('Y-m-d', strtotime("-15 days")));
                        // $appendCondition = " AND (
							// vtiger_potential.brand = '{$recordModel->get('brand')}' 
							// AND vtiger_potentialscf.origin_zip = '{$recordModel->get('origin_zip')}' 
							// AND vtiger_potentialscf.origin_state = '{$recordModel->get('origin_state')}' 
							// AND vtiger_potentialscf.destination_state = '{$recordModel->get('destination_state')}' 
							// AND IF( LENGTH(vtiger_contactdetails.lastname) > 4, LEFT(vtiger_contactdetails.lastname, 4), vtiger_contactdetails.lastname) = '{$first4CharsOfContactLastName}' 
							// AND IFNULL(vtiger_potential.receive_date, '') <= '{$recordRegistrationDate}' 
							// AND vtiger_crmentity.createdtime > '{$dateFilter}' 
						// ) ";
                    // }
                }
                // End - Find duplicate conditions
                // Don't know what this do, so not just wiping it.
                $someExtraConditions = "";
                if(strpos($listQuery, "JOIN vtiger_potentialscf") === false) {
                    $someExtraConditions .= " INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid ";
                }
                if(strpos($listQuery, "JOIN vtiger_contactdetails") === false) {
                    $someExtraConditions .= " LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_potential.contact_id ";
                }
                if(strpos($listQuery, "JOIN vtiger_contactsubdetails") === false) {
                    $someExtraConditions .= " LEFT JOIN vtiger_contactsubdetails ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_potential.contact_id ";
                }
                $pregReplace         = "$1".$someExtraConditions.' WHERE '.$condition.$appendCondition.' AND ';
                $overRideQuery       = preg_replace('/^(.*? )WHERE /i', $pregReplace, $listQuery);
            }
        }
        return $overRideQuery;
        }
}
