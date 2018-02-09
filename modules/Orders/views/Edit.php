<?php

class Orders_Edit_View extends Vtiger_Edit_View
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName       = $request->getModule();
        $record           = $request->get('record');
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);

        if ($this->record) {
	    $recordModel = $this->record;
        } elseif (!empty($record) && $record != '') {
	    $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
	}
        if ($recordModel) {
            $ordersStatus = $recordModel->get("ordersstatus");
        }

        if ((!$recordPermission) || ($ordersStatus == "Cancelled")) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }

    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $moduleName   = $request->getModule();
        $record       = $request->get('record');
        $recordModel  = Vtiger_Record_Model::getCleanInstance($moduleName);
        $sourceRecord = $request->get('sourceRecord');
        $sourceModule = $request->get('sourceModule');

        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
		if ($extraPermissions[0] != true) {
			if ($record != NULL) {
				throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to write is denied");
			}
		}
		*/
        $viewer = $this->getViewer($request);

        global $adb;
        if (!empty($record) && $request->get('isDuplicate') == true) {
           /* $participantRows = [];
            $db              = PearDatabase::getInstance();
            $sql             = 'SELECT * FROM `vtiger_orders_participatingagents` WHERE ordersid = ?';
            $result          = $db->pquery($sql, [$record]);
            if ($result) {
                $row = $result->fetchRow();
                while ($row != NULL) {
                    $sql2              = 'SELECT agentname FROM `vtiger_agents` WHERE agentsid = ?';
                    $result2           = $db->pquery($sql2, [$row[1]]);
                    $row2              = $result2->fetchRow();
                    $row['agentName']  = $row2[0];
                    $participantRows[] = $row;
                    $row               = $result->fetchRow();
                }
            }
            $viewer->assign('PARTICIPANT_ROWS', $participantRows);*/
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $recordModel->set('orders_otherstatus', '');
            $recordModel->set('received_date', date('Y-m-d'));
            $viewer->assign('MODE', '');
            //While Duplicating record, If the related record is deleted then we are removing related record info in record model
            $mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
            foreach ($mandatoryFieldModels as $fieldModel) {
                if ($fieldModel->isReferenceField()) {
                    $fieldName = $fieldModel->get('name');
                    if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
                        $recordModel->set($fieldName, '');
                    }
                }
            }
        } elseif (!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
            /*$participantRows = [];
            $db              = PearDatabase::getInstance();
            $sql             = 'SELECT * FROM `vtiger_orders_participatingagents` WHERE ordersid = ?';
            $result          = $db->pquery($sql, [$record]);
            if ($result) {
                while ($row != NULL) {
                    $row               = $result->fetchRow();
                    $sql2              = 'SELECT agentname FROM `vtiger_agents` WHERE agentsid = ?';
                    $result2           = $db->pquery($sql2, [$row[1]]);
                    $row2              = $result2->fetchRow();
                    $row['agentName']  = $row2[0];
                    $participantRows[] = $row;
                    $row               = $result->fetchRow();
                }
            }
            $viewer->assign('PARTICIPANT_ROWS', $participantRows);*/
            $ordersNo = $recordModel->get('orders_no');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $recordModel->set('received_date', date('Y-m-d'));
            $viewer->assign('MODE', '');
        }

        if (!$this->record) {
            $this->record = $recordModel;
        }
        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();

        $assignedUserModel = Users_Record_Model::getCurrentUserModel();
        //Lock the field except for roles below customer service Coordinator.  coordinator is role 6 so roles bigger are locked.
        if ($assignedUserModel->getUserRoleDepth() > 6) {
            $viewer->assign('LOCK_RECEIVED_DATE', 'lock');
        }

        $tariffList = [];
        if ($business_line = $recordModel->get('business_line')) {
            $userAgents        = $assignedUserModel->getAccessibleAgentsForUser();
            if ($business_line == 'Local Move') {
                //@TODO:  review if this is correct formating for calling this module_model
                //done the same in modules/Orders/actions/RetrieveTariffList.php
                $tariffsModel = new Tariffs_Module_Model;
            } else {
                $tariffsModel = new TariffManager_Module_Model;
            }
            $tariffInfo = $tariffsModel->retrieveTariffsByAgencies($userAgents, $business_line);
            $tariffList = $tariffInfo['tariffNames'];
        } else {
            $userAgents        = $assignedUserModel->getAccessibleAgentsForUser();
            //@TODO Maybe do this better?
            //OK the better way is a related list check Base Tariff in contracts.
            $tariffsModel = new Tariffs_Module_Model;
            $tariffInfo = $tariffsModel->retrieveTariffsByAgencies($userAgents, $business_line);
            $tariffListLocal = $tariffInfo['tariffNames'];

            $tariffsModel = new TariffManager_Module_Model;
            $tariffInfo = $tariffsModel->retrieveTariffsByAgencies($userAgents, $business_line);
            $tariffList = $tariffInfo['tariffNames'];
            foreach ($tariffListLocal as $tariffId => $tariffName) {
                $tariffList[$tariffId] = $tariffName;
            }
        }

        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);

        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel   = $fieldList[$fieldName];
            $specialField = false;
            // We collate date and time part together in the EditView UI handling
            // so a bit of special treatment is required if we come from QuickCreate
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) {
                $specialField = true;
                // Convert the incoming user-picked time to GMT time
                // which will get re-translated based on user-time zone on EditForm
                $fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i");
            }

            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) {
                $startTime     = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
                list($startDate, $startTime) = explode(' ', $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }

            if ($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }

        if ($sourceRecord && $sourceModule == 'Opportunities') {
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
            if($parentRecordModel) {
                $billingType         = $parentRecordModel->get('billing_type');
                $estimateRecordModel = $parentRecordModel->getPrimaryEstimateRecordModel();
                $surveyRecordModel   = $parentRecordModel->getPrimarySurveyRecordModel();
            }
            if($estimateRecordModel) {
                $effectiveTariff = $estimateRecordModel->get('effective_tariff');
                $parentRecordModel->set('effective_tariff', $effectiveTariff);
                //TODO remove business_line2 related logic
                $parentBusinessLine = $parentRecordModel->get('business_line2');
                if(!$parentBusinessLine) {
                    $parentBusinessLine = $parentRecordModel->get('business_line');
                }
                if ($parentBusinessLine == 'HHG - Interstate' || $parentBusinessLine == 'Interstate') {
                    if ($interstateMileage = $estimateRecordModel->get('interstate_mileage')) {
                        $parentRecordModel->set('mileage', $interstateMileage);
                    }
                    $lineHaulRow = $estimateRecordModel->getLineItemResultsByType('Transportation', 'LineHaul');
                    if ($netLineHaulCost = $lineHaulRow['InvoiceCostNet']) {
                        $parentRecordModel->set('linehaul_net', $netLineHaulCost);
                    }
                }
            }
            if($surveyRecordModel){
                $surveyFields = $surveyRecordModel->getCubesheetDetails();
                if($surveyFields){
                    $parentRecordModel->set('survey_weight', $surveyFields['weight']);
                    $parentRecordModel->set('survey_cube', $surveyFields['cube']);
                    $parentRecordModel->set('survey_item_count', $surveyFields['items']);
                }
            }
    	    //VGS - Juan - Credit hold check OT1986
    	    $accountId = $parentRecordModel->get('related_to');
            if (!empty($accountId) && $accountId != '' && $billingType != 'Consumer/COD' && getenv('INSTANCE_NAME') == 'graebel') {
    			$isOnHold = Accounts_Record_Model::checkCreditHold($accountId);
                    if ($isOnHold) {
    		    	throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "New Move cannot be created as there is a Credit Hold placed on the Account.");
    			}
    	    }

    	    $recordModel->setParentRecordData($parentRecordModel);
            if ($parentRecordModel->get('is_competitive') == 1) {
                $recordModel->set('competitive', 'yes');
            }
            //should be handled in function: setParentRecordData
            //$recordModel->set('billing_type', $parentRecordModel->get('billing_type'));
        }

	   if ($sourceRecord && ($sourceModule == 'Accounts') && getenv('INSTANCE_NAME') == 'graebel') {

    	    //VGS - Juan - Credit hold check OT1986
    	    $accountId = $request->get('orders_account');
                if (!empty($accountId) && $accountId != '') {
    			$isOnHold = Accounts_Record_Model::checkCreditHold($accountId);
                    if ($isOnHold) {
    		    	throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "New Move cannot be created as there is a Credit Hold placed on the Account.");
    			}
    	    }

        }



        $recordStructureInstance      = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        //Convert survey_date, survey_time, and survey_end_time to current user's time zone
        //$this->convertSurveyTimeFormat($recordStructureInstance->getStructure());
        //End Time Zone Conversion
        /* VGS Global Business Line Blocks */
        if (!empty($record) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, $record);
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        } elseif (empty($record) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, '');
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        } else {
            $blocksToHide = [];
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        }
        global $hiddenBlocksArrayField;

        $data = Estimates_Record_Model::getAllowedTariffsForUser();
        $viewer->assign('AVAILABLE_TARIFFS_DATA', $data);
        $viewer->assign('AVAILABLE_TARIFFS', Vtiger_Util_Helper::toSafeHTML(json_encode($data)));
        $tariffID = $recordModel->get('tariff_id');
        $tariffInfo2 = Estimates_Record_Model::getTariffInfo($tariffID);
        $viewer->assign('EFFECTIVE_TARIFF_CUSTOMTYPE', $tariffInfo2['custom_type']);

        $viewer->assign('TARIFF_LIST', $tariffList);
        $viewer->assign('BLFIELD', $hiddenBlocksArrayField[$moduleName]);
        /* VGS Global Business Line Blocks */
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }
        //reference.tpl uses this to remove quick create form UI type 10's
        if (getenv('INSTANCE_NAME') == 'graebel') {
            $viewer->assign('MODULE_QUICKCREATE_RESTRICTIONS', ['Accounts']);
        } elseif (getenv('IGC_MOVEHQ')) {
            $viewer->assign('MODULE_QUICKCREATE_RESTRICTIONS', ['Accounts']);
        }
        //old securities
        /*
		$extraPermissions = $this::getExtraPermissions($request);
		$viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
		$viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
		*/
        /*$viewer->assign('STOPS_ROWS', Orders_Module_Model::getStops($record, 'Orders'));
        if ($isRelationOperation) {
            $viewer->assign('STOPS_ROWS', Orders_Module_Model::getStops($sourceRecord, $sourceModule));
        }*/
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        //vehicles block
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
//            if ($isRelationOperation) {
//                $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($request->get('sourceRecord'), true));
//            } else {
//                $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($record));
//            }
        }
        //participants block
//		$participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
//        if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
//            $viewer->assign('PARTICIPATING_AGENTS', true);
//            $viewer->assign('PARTICIPANT_LIST', $participatingAgentsModel::getParticipants($record));
//            if(!$record && getenv('INSTANCE_NAME') == 'graebel'){
//                $viewer->assign('PARTICIPATING_CARRIER_DEFAULTS', $participatingAgentsModel::getGraebelDefaultParticipatingCarriers());
//            }
//			$requestsModel = Vtiger_Module_Model::getInstance('OASurveyRequests');
//            if ($requestsModel && $requestsModel->isActive()) {
//				$viewer->assign('USE_STATUS', true);
//			}
//            //grab primary owner for initializing participants
//            $sql = "SELECT `vtiger_agents`.agentsid, `vtiger_agents`.agentname
//			FROM `vtiger_agentmanager` INNER JOIN `vtiger_agents` ON `vtiger_agentmanager`.agency_code = `vtiger_agents`.agent_number
//			WHERE `vtiger_agentmanager`.agentmanagerid = `vtiger_agents`.agentmanager_id
//			AND `vtiger_agentmanager`.agentmanagerid = ? LIMIT 1";
//            $result = $adb->pquery($sql, [getPermittedAccessible()[0]]);
//            $row = $result->fetchRow();
//            $viewer->assign('PRIMARY_OWNER_AGENT', $row['agentsid']);
//            $viewer->assign('PRIMARY_OWNER_AGENT_NAME', $row['agentname']);
//        }
        //TODO: get this up in vtiger_edit_view to make it automagical
        $record = empty($record)?$sourceRecord:$record;
        $this->setViewerForGuestBlocks($moduleName, $record, $viewer);
        $viewer->assign('MODULE_QUICKCREATE_RESTRICTIONS', ['EmployeeRoles','Employees']);

        //stops block
        $extraStopsModel = Vtiger_Module_Model::getInstance('ExtraStops');
        if ($extraStopsModel && $extraStopsModel->isActive()) {
            $extraStopsModel->setViewerForStops($viewer, $record);
        }

        // I hate this hacked in way but what can you do?
        $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
            $record = empty($record)?$sourceRecord:$record;
            $viewer->assign('PARTICIPATING_AGENTS', true);
            $participants = $participatingAgentsModel::getParticipants($record);
            if ($isRelationOperation) {
                // make sure new records are created, otherwise this won't work
                foreach ($participants as &$participantData) {
                    $participantData['participatingagentsid'] = 'none';
                }
            }
            $viewer->assign('PARTICIPANT_LIST', $participants);
            $requestsModel = Vtiger_Module_Model::getInstance('OASurveyRequests');
            if ($requestsModel && $requestsModel->isActive()) {
                $viewer->assign('USE_STATUS', true);
            }
            //logic to pass the primary estimate's tariff type to the template
            $db            = PearDatabase::getInstance();
            if(getenv('INSTANCE_NAME') == 'graebel') {
                $sql = "SELECT `vtiger_tariffmanager`.custom_tariff_type FROM `vtiger_quotes` 
            LEFT JOIN vtiger_contracts ON(contract=contractsid AND related_tariff<>0) INNER JOIN `vtiger_tariffmanager` ON COALESCE(related_tariff,effective_tariff) = `vtiger_tariffmanager`.tariffmanagerid
			WHERE `vtiger_quotes`.is_primary = 1 AND `vtiger_quotes`.potentialid = ? LIMIT 1";
            } else {
                $sql = "SELECT `vtiger_tariffmanager`.custom_tariff_type FROM `vtiger_quotes` 
                INNER JOIN `vtiger_tariffmanager` ON effective_tariff = `vtiger_tariffmanager`.tariffmanagerid
			  WHERE `vtiger_quotes`.is_primary = 1 AND `vtiger_quotes`.potentialid = ? LIMIT 1";
            }
            $result = $db->pquery($sql, [$record]);
            $viewer->assign('PRIMARY_EST_TARIFF_TYPE', $result->fetchRow()['custom_tariff_type']);
            $sql = "SELECT `vtiger_agents`.agentsid, `vtiger_agents`.agentname 
			FROM `vtiger_agentmanager` INNER JOIN `vtiger_agents` ON `vtiger_agentmanager`.agency_code = `vtiger_agents`.agent_number
			WHERE `vtiger_agentmanager`.agentmanagerid = `vtiger_agents`.agentmanager_id 
			AND `vtiger_agentmanager`.agentmanagerid = ? LIMIT 1";
            $result = $db->pquery($sql, [getPermittedAccessible()[0]]);
            $row = $result->fetchRow();
            $viewer->assign('PRIMARY_OWNER_AGENT', $row['agentsid']);
            $viewer->assign('PRIMARY_OWNER_AGENT_NAME', $row['agentname']);
            //logic to lock the owner/salesperson fields for participants ONLY (not the record owner)
            $current_user     = Users_Record_Model::getCurrentUserModel();
            $userAccessible = getPermittedAccessible();
            $recordAgentOwner = getRecordAgentOwner($record);
            $recordAssignedTo = getRecordOwnerId($record);
            $recordParticipants = getParticipantsForRecord($record);
            //file_put_contents('logs/devLog.log', "\n user accessible: " . print_r($userAccessible, true), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n record agent owner: " . print_r($recordAgentOwner, true), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n record assigned user: " . print_r($recordAssignedTo, true), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n record participants: " . print_r($recordParticipants, true), FILE_APPEND);
            if (!in_array($recordAgentOwner, $userAccessible) && !$current_user->isAdminUser() && !in_array($current_user->getId(), $recordAssignedTo) && !empty(array_intersect($userAccessible, $recordParticipants)) && $record) {
                $participantFieldFilter = $moduleModel->getFields();
                $participantFieldFilter['assigned_user_id'] = $participantFieldFilter['assigned_user_id']->set('disabled', true);
                $participantFieldFilter['agentid'] = $participantFieldFilter['agentid']->set('disabled', true);
                //$participantFieldFilter['sales_person'] = $participantFieldFilter['sales_person']->set('disabled', true); //disables the ability to change sales person
                $moduleModel->setFields($participantFieldFilter);
                $recordModel->setModuleFromInstance($moduleModel);
                $viewer->assign('IS_PARTICIPANT', true);
            }
        }

        if (isset($ordersNo) && $ordersNo != '') {
            $viewer->assign('ORDERID', $ordersNo);
        }
//        } else {
//            $viewer->assign('ORDERID', $this->getOrderNo());
//        }

        //logic to include Addresss List
        $addressListModule = Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->assignValueForAddressList($viewer,$record,$sourceRecord);
        }
        $viewer->view('EditView.tpl', $moduleName);
    }

    public function getExtraPermissions($request)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*
        $db = PearDatabase::getInstance();
		$userModel     = Users_Record_Model::getCurrentUserModel();
		$currentUserId = $userModel->getId();
		$isAdmin = $userModel->isAdminUser();
		$recordId = $request->get('record');
		$creatorPermissions    = false;
		$memberOfParentVanline = false;
		$sql    = "SELECT vanlineid, is_parent FROM `vtiger_users2vanline` JOIN `vtiger_vanlinemanager` ON vanlineid=vanlinemanagerid WHERE userid=?";
		$result = $db->pquery($sql, [$currentUserId]);
		while ($row =& $result->fetchRow()) {
			$validVanlines[] = $row[0];
			if ($row['is_parent'] == 1) {
				//One of the vanlines the user is associated with is the parent. Display all records
				$memberOfParentVanline = true;
			}
		}
		if ($isAdmin || $memberOfParentVanline) {
			$creatorPermissions = true;
		} else {
			$userGroups = [];
			$sql        = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
			$result     = $db->pquery($sql, [$currentUserId]);
			$row        = $result->fetchRow();
			while ($row != NULL) {
				$userGroups[] = $row[0];
				$row          = $result->fetchRow();
			}
			$userGroupNames = [];
			foreach ($userGroups as $group) {
				$sql              = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
				$result           = $db->pquery($sql, [$group]);
				$row              = $result->fetchRow();
				$userGroupNames[] = $row[0];
			}
			$groupOwned = [];
			foreach ($userGroups as $group) {
				$sql    = "SELECT crmid FROM `vtiger_crmentity` WHERE smownerid=?";
				$result = $db->pquery($sql, [$group]);
				$row    = $result->fetchRow();
				while ($row != NULL) {
					$groupOwned[] = $row[0];
					$row          = $result->fetchRow();
				}
			}
			foreach ($groupOwned as $owned) {
				if ($owned == $recordId) {
					$creatorPermissions = true;
				}
			}
		}
		if ($creatorPermissions == false) {
			$participatingAgentPermissions = 'none';
			$participatingAgents           = [];
			$participatingAgentNames       = [];
			$sql                           = "SELECT agentid, permissions FROM `vtiger_orders_participatingagents` WHERE ordersid=? AND permissions!=3";
			$result                        = $db->pquery($sql, [$recordId]);
			$row                           = $result->fetchRow();
			while ($row != NULL) {
				$participatingAgents[] = [$row[0], $row[1]];
				$row                   = $result->fetchRow();
			}
			//file_put_contents('logs/devLog.log', "\n participatingAgents: ".print_r($participatingAgents, true), FILE_APPEND);
			foreach ($participatingAgents as $participatingAgent) {
				$sql                       = "SELECT agentname FROM `vtiger_agents` WHERE agentsid=?";
				$result                    = $db->pquery($sql, [$participatingAgent[0]]);
				$row                       = $result->fetchRow();
				$participatingAgentNames[] = [$row[0], $participatingAgent[1]];
			}
			//$sql = "SELECT participating_agents_full FROM `vtiger_orders` WHERE ordersid=?";
			//$result = $db->pquery($sql, array($recordId));
			//$row = $result->fetchRow();
			//$participatingAgentsFull = $row[0];
			//$participatingAgentsFull = explode(' |##| ', $participatingAgentsFull);
			//$sql = "SELECT participating_agents_no_rates FROM `vtiger_orders` WHERE ordersid=?";
			//$result = $db->pquery($sql, array($recordId));
			//$row = $result->fetchRow();
			//$participatingAgentsNoRates = $row[0];
			//$participatingAgentsNoRates = explode(' |##| ', $participatingAgentsNoRates);
			//foreach($participatingAgentsFull as $participatingAgent){
			//	foreach($userGroups as $group){
			//		if($group == $participatingAgent){
			//			$participatingAgentPermissions = 'full';
			//		}
			//	}
			//}
			//foreach($participatingAgentsNoRates as $participatingAgent){
			//	foreach($userGroups as $group){
			//		if($group == $participatingAgent && $participatingAgentPermissions != 'full'){
			//			$participatingAgentPermissions = 'no_rates';
			//		}
			//	}
			//}
			foreach ($participatingAgentNames as $participatingAgentName) {
				foreach ($userGroupNames as $groupName) {
					if ($groupName == $participatingAgentName[0]) {
						if ($participatingAgentName[1] == 0) {
							$creatorPermissions            = true;
							$participatingAgentPermissions = 'edit';
						} elseif ($participatingAgentName[1] == 1 && $creatorPermissions == false && $participatingAgentPermissions != 'edit') {
							$participatingAgentPermissions = 'full';
						} elseif ($participatingAgentName[1] == 2 && $creatorPermissions == false && $participatingAgentPermissions != 'full') {
							$participatingAgentPermissions = 'no_rates';
						}
					}
				}
			}
		}
		//sales person securities piece
		$moduleName   = $request->getModule();
		$userRole     = $userModel->getRole();
		$sql          = "SELECT rolename FROM `vtiger_role` WHERE roleid=?";
		$result       = $db->pquery($sql, [$userRole]);
		$row          = $result->fetchRow();
		$roleName     = $row[0];
		$oppRelated   = [
			'Potentials'    => ['vtiger_potential', 'potentialid', 'potentialid'],
			'Opportunities' => ['vtiger_potential', 'potentialid', 'potentialid'],
			'Estimates'     => ['vtiger_quotes', 'quoteid', 'potentialid'],
			'Calendar'      => ['vtiger_seactivityrel', 'activityid', 'crmid'],
			'Documents'     => ['vtiger_senotesrel', 'notesid', 'crmid'],
			'Stops'         => ['vtiger_stops', 'stopsid', 'stop_opp'],
			'Surveys'       => ['vtiger_surveys', 'surveysid', 'potential_id'],
			'Cubesheets'    => ['vtiger_cubesheets', 'cubesheetsid', 'potential_id'],
		];
		$orderRelated = [
			'Orders'          => ['vtiger_orders', 'ordersid', 'ordersid'],
			'Estimates'       => ['vtiger_quotes', 'quoteid', 'orders_id'],
			'Calendar'        => ['vtiger_seactivityrel', 'activityid', 'crmid'],
			'Documents'       => ['vtiger_senotesrel', 'notesid', 'crmid'],
			'HelpDesk'        => ['vtiger_crmentityrel', 'relcrmid', 'crmid'],
			'Claims'          => ['vtiger_claims', 'claimsid', 'claims_order'],
			'Stops'           => ['vtiger_stops', 'stopsid', 'stop_order'],
			'OrdersMilestone' => ['vtiger_ordersmilestone', 'ordersmilestoneid', 'ordersid'],
			'OrdersTask'      => ['vtiger_orderstask', 'orderstaskid', 'ordersid'],
			'Storage'         => ['vtiger_storage', 'storageid', 'storage_orders'],
			'Trips'           => ['vtiger_crmentityrel', 'relcrmid', 'crmid'],
		];
		$leadsRelated = [
			'Leads'     => ['vtiger_leaddetails', 'leadid', 'leadid'],
			'Calendar'  => ['vtiger_seactivityrel', 'activityid', 'crmid'],
			'Documents' => ['vtiger_senotesrel', 'notesid', 'crmid'],
		];
		if (strpos($roleName, 'Sales Person') !== false &&
			(array_key_exists($moduleName, $orderRelated) || array_key_exists($moduleName, $oppRelated) || array_key_exists($moduleName, $leadsRelated))
		) {
			$creatorPermissions            = false;
			$participatingAgentPermissions = 'none';
			//salesPerson for modules related to orders
			if (array_key_exists($moduleName, $orderRelated)) {
				if ($moduleName == 'Orders') {
					$sql = "SELECT sales_person FROM `vtiger_orders` WHERE ordersid=?";
				} else {
					$sql =
						"SELECT vtiger_orders.sales_person FROM `vtiger_orders` INNER JOIN ".
						$orderRelated[$moduleName][0].
						" ON vtiger_orders.ordersid = ".
						$orderRelated[$moduleName][0].
						".".
						$orderRelated[$moduleName][2].
						" WHERE ".
						$orderRelated[$moduleName][0].
						".".
						$orderRelated[$moduleName][1].
						"=?";
				}
				//file_put_contents('logs/devLog.log', "\n ORDER SQL: $sql", FILE_APPEND);
				$result      = $db->pquery($sql, [$recordId]);
				$row         = $result->fetchRow();
				$salesPerson = $row[0];
				//file_put_contents('logs/devLog.log', "\n ORDER SALES PERSON: $salesPerson", FILE_APPEND);
				if ($salesPerson == $currentUserId) {
					$creatorPermissions = true;
				}
			}
			//salesPerson for modules related to opps
			if (array_key_exists($moduleName, $oppRelated)) {
				if ($moduleName == 'Potentials' || $moduleName == 'Opportunities') {
					$sql = "SELECT sales_person FROM `vtiger_potential` WHERE potentialid=?";
				} else {
					$sql =
						"SELECT vtiger_potential.sales_person FROM `vtiger_potential` INNER JOIN ".
						$oppRelated[$moduleName][0].
						" ON vtiger_potential.potentialid = ".
						$oppRelated[$moduleName][0].
						".".
						$oppRelated[$moduleName][2].
						" WHERE ".
						$oppRelated[$moduleName][0].
						".".
						$oppRelated[$moduleName][1].
						"=?";
				}
				//file_put_contents('logs/devLog.log', "\n OPP SQL: $sql", FILE_APPEND);
				$result      = $db->pquery($sql, [$recordId]);
				$row         = $result->fetchRow();
				$salesPerson = $row[0];
				//file_put_contents('logs/devLog.log', "\n OPP SALES PERSON: $salesPerson", FILE_APPEND);
				if ($salesPerson == $currentUserId) {
					$creatorPermissions = true;
				}
			}
			if (array_key_exists($moduleName, $leadsRelated)) {
				if ($moduleName == 'Leads') {
					$sql = "SELECT sales_person FROM `vtiger_leaddetails` WHERE leadid=?";
				} else {
					$sql =
						"SELECT vtiger_leaddetails.sales_person FROM `vtiger_leaddetails` INNER JOIN ".
						$leadsRelated[$moduleName][0].
						" ON vtiger_leaddetails.leadid = ".
						$leadsRelated[$moduleName][0].
						".".
						$leadsRelated[$moduleName][2].
						" WHERE ".
						$leadsRelated[$moduleName][0].
						".".
						$leadsRelated[$moduleName][1].
						"=?";
				}
				//file_put_contents('logs/devLog.log', "\n LEAD SQL: $sql", FILE_APPEND);
				$result      = $db->pquery($sql, [$recordId]);
				$row         = $result->fetchRow();
				$salesPerson = $row[0];
				//file_put_contents('logs/devLog.log', "\n LEAD SALES PERSON: $salesPerson", FILE_APPEND);
				if ($salesPerson == $currentUserId) {
					$creatorPermissions = true;
				}
			}
			if (array_key_exists($moduleName, $oppRelated) || array_key_exists($moduleName, $orderRelated)) {
				$assignedOpp   = false;
				$assignedOrder = false;
				//extra logic to allow sales persons to see any record with no assigned order or opportunity person
				if (array_key_exists($moduleName, $oppRelated)) {
					$sql         = "SELECT ".$oppRelated[$moduleName][2]." FROM ".$oppRelated[$moduleName][0]." WHERE ".$oppRelated[$moduleName][1]."=?";
					$result      = $db->pquery($sql, [$recordId]);
					$row         = $result->fetchRow();
					$assignedOpp = $row[0];
				}
				if (array_key_exists($moduleName, $orderRelated)) {
					$sql           = "SELECT ".$orderRelated[$moduleName][2]." FROM ".$orderRelated[$moduleName][0]." WHERE ".$orderRelated[$moduleName][1]."=?";
					$result        = $db->pquery($sql, [$recordId]);
					$row           = $result->fetchRow();
					$assignedOrder = $row[0];
				}
				//file_put_contents('logs/devLog.log', "\n assopp: $assignedOpp, assord: $assignedOrder", FILE_APPEND);
				if (!$assignedOpp && !$assignedOrder) {
					$creatorPermissions = true;
				}
			}
			//file_put_contents('logs/devLog.log', "\n IN OPP IF STATEMENT", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n SALES PERSON: $salesPerson", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n CURRENT USER: $currentUserId", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n RECORDID: $recordId", FILE_APPEND);
		} //end sales person securities piece
		return [$creatorPermissions, $participatingAgentPermissions];
	    */
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
        $vehicleLookupModel    = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $jsFileNames = [
                "modules.VehicleLookup.resources.Edit",
            ];
        } else {
            $jsFileNames = [];
        }
        $participatingAgentModel    = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        if ($participatingAgentModel && $participatingAgentModel->isActive()) {
            $jsFileNames[] = "modules.ParticipatingAgents.resources.Edit";
        }
        $extraStopsModel    = Vtiger_Module_Model::getInstance('ExtraStops');
        if ($extraStopsModel && $extraStopsModel->isActive()) {
            $jsFileNames[] = "modules.ExtraStops.resources.EditBlock";
        }
        $MoveRolesModel    = Vtiger_Module_Model::getInstance('MoveRoles');
        if ($MoveRolesModel && $MoveRolesModel->isActive()) {
            $jsFileNames[] = "modules.MoveRoles.resources.EditBlock";
        }
        $jsFileNames[] = "modules.Valuation.resources.Common";
        $moveRolesModel = Vtiger_Module_Model::getInstance('MoveRoles');
        if($moveRolesModel && $moveRolesModel->isActive()) {
            $jsFileNames[] = "modules.MoveRoles.resources.EditBlock";
        }
        //old guest blocks
        /*$ordersMilestoneModel = Vtiger_Module_Model::getInstance('OrdersMilestone');
        if($ordersMilestoneModel && $ordersMilestoneModel->isActive()) {
            $jsFileNames[] = "modules.OrdersMilestone.resources.EditBlock";
        }*/
        $addressListModule=Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $jsFileNames[] = "modules.AddressList.resources.EditBlock";
        }
        /*OT3370*/
        $jsFileNames[] = "~/portal/js/plugins/input-mask/jquery.inputmask.js";
        /*OT3370*/
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }



    public function getOrderNo()
    {
        $new_order_id = CRMEntity::setModuleSeqNumber('increment', 'Orders');
        return $new_order_id;

        //@NOTE: this shouldn't get hit anymore. and hte padding won't be done, but should be done in the right place.
//        $adb = PearDatabase::getInstance();
//        /* don't do this because it's not set
//        if (true === file_exists('cache/orders_no.txt')) {
//            $cacheLastId = file_get_contents('cache/orders_no.txt');
//        }
//        */
//        $result      = $adb->pquery("SELECT cur_id,prefix FROM vtiger_modentity_num WHERE semodule=?", ['Orders']);
//        $currentSeq  = floatval($adb->query_result($result, 0, 'cur_id'));
//        $cacheLastId = explode($adb->query_result($result, 0, 'prefix'), $cacheLastId)[1];
//        if ($cacheLastId > $currentSeq) {
//            $new_seq_number = $cacheLastId + 1;
//        } else {
//            $new_seq_number = $currentSeq + 1;
//        }
//        $new_seq_number = str_pad($new_seq_number, 5, '0', STR_PAD_LEFT);
//        $new_order_id = $adb->query_result($result, 0, 'prefix').$new_seq_number;
//
//        //file_put_contents('cache/orders_no.txt', $new_order_id);
//        return $new_order_id;
    }
}
