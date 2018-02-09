<?php

class Opportunities_Edit_View extends Vtiger_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $db = PearDatabase::getInstance();
        $moduleName   = $request->getModule();
        $record       = $request->get('record');
        $recordModel  = Vtiger_Record_Model::getCleanInstance($moduleName);
        $sourceRecord = $request->get('sourceRecord');
        $sourceModule = $request->get('sourceModule');
        $current_user     = Users_Record_Model::getCurrentUserModel();
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        if($extraPermissions[0] != true){
            if($record != null){
                throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to write is denied");
            }
        }
        */

        $viewer = $this->getViewer($request);
        if (!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
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
            //old stops
           /* $stopsRows       = [];
            $db              = PearDatabase::getInstance();
            $sql    = 'SELECT * FROM `vtiger_extrastops` WHERE stop_opp = ?';
            $result = $db->pquery($sql, [$record]);
            $row    = $result->fetchRow();
            while ($row != NULL) {
                $sql2                     = 'SELECT firstname, lastname FROM `vtiger_contactdetails` WHERE contactid = ?';
                $result2                  = $db->pquery($sql2, [$row['stop_contact']]);
                $row2                     = $result2->fetchRow();
                $row['stop_contact_name'] = $row2[0].' '.$row2[1];
                $stopsRows[]              = $row;
                $row                      = $result->fetchRow();
            }
            $viewer->assign('STOPS_ROWS', Orders_Module_Model::getStops($record, 'Opportunities'));*/
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
            if ($sourceRecord) {
                //TFS22818 adding in account name when source of new opportunity is a Contact
                if ($sourceModule == 'Contacts') {
                    $parentRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
                    $accountID = $parentRecordModel->get('account_id');
                    if ($accountID && !in_array($recordModel->get('shipper_type'), ['MIL', 'GVT'])) {
                        $recordModel->set('related_to', $accountID);
                        $recordModel->set('shipper_type', 'NAT');
                        $recordModel->set('billing_type', 'NAT');
                        $recordModel->set('business_channel', 'Corporate');
                    }
                }
                //$recordModel->setParentRecordData($parentRecordModel);
            }
            //get current users primary agent and set self haul if they are a hauling agent
            $primaryAgentRecord = Vtiger_Record_Model::getInstanceById(getPermittedAccessible()[0], 'AgentManager');
            if ($primaryAgentRecord) {
                $recordModel->set('self_haul', $primaryAgentRecord->get('self_haul'));
                $haulingAgent = $primaryAgentRecord->get('self_haul_agentmanagerid');
                if ($haulingAgent) {
                    $agentInfo = Opportunities_GetParticipantIdFromAgentOwner_Action::getInfo($haulingAgent);
                    $viewer->assign('HAULING_AGENT_ID', $agentInfo['agentid']);
                    $viewer->assign('HAULING_AGENT_NAME', $agentInfo['agentName']);
                }
            }
        }
        if ($recordModel->getId() && getenv('INSTANCE_NAME') == 'sirva' && $recordModel->getPrimaryEstimateRecordModel(false)) {
            $db = PearDatabase::getInstance();
            $estimateRecordId = $recordModel->getPrimaryEstimateRecordModel(false)->getId();

            $viewer->assign('HAS_PRIMARY_ESTIMATE', true);

            $autoSpotQuoteModel = Vtiger_Module_Model::getInstance('AutoSpotQuote');

            if ($autoSpotQuoteModel && $autoSpotQuoteModel->isActive()) {
                $query = $db->pquery('SELECT * FROM `vtiger_autospotquote` WHERE `estimate_id` = ?', [$estimateRecordId]);
                while ($row =& $query->fetchRow()) {
                    $autoQuotes[] = $row;
                }
                $viewer->assign('AUTO_QUOTES', $autoQuotes);
                $viewer->assign('AUTO_SPOT_QUOTE_MODULE', $autoSpotQuoteModel);
            }

            $total = $db->pquery("SELECT total FROM `vtiger_quotes` WHERE quoteid = ?", [$estimateRecordId])->fetchRow()['total'];
            if ($total) {
                $recordModel->set('amount', $total);
            }
        }

        if ($request->get('isDuplicate') == true) {
            $recordModel->set('potentialname', $recordModel->get('potentialname').' Copy');
            $recordModel->set('register_sts', '');
            $recordModel->set('sts_response', '');
            $recordModel->set('register_sts_number', '');
            // We need to know so we can duplicate participating agents.
            $viewer->assign('ISDUPLICATE',$request->get('isDuplicate'));
            $viewer->assign('OLD_RECORD_ID', $record);
        }

        if (!$this->record) {
            $this->record = $recordModel;
        }

        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();

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
        //TODO: get this up in vtiger_edit_view to make it automagical
        $this->setViewerForGuestBlocks($moduleName, $record, $viewer);
        $viewer->assign('MODULE_QUICKCREATE_RESTRICTIONS', ['EmployeeRoles','Employees']);
        //stops block
        //logic to include extra stops
        $extraStopsModel = Vtiger_Module_Model::getInstance('ExtraStops');
        if ($extraStopsModel && $extraStopsModel->isActive()) {
            $extraStopsModel->setViewerForStops($viewer, $record);
        }
        //logic to include the participating agents block
        $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
            $viewer->assign('PARTICIPATING_AGENTS', true);
            $viewer->assign('PARTICIPANT_LIST', $participatingAgentsModel::getParticipants($record));
            if(!$record && getenv('INSTANCE_NAME') == 'graebel'){
                $viewer->assign('PARTICIPATING_CARRIER_DEFAULTS', $participatingAgentsModel::getGraebelDefaultParticipatingCarriers());
            }
            $requestsModel = Vtiger_Module_Model::getInstance('OASurveyRequests');
            if ($requestsModel && $requestsModel->isActive()) {
                $viewer->assign('USE_STATUS', true);
            }
            //logic to pass the primary estimate's tariff type to the template
            $db            = PearDatabase::getInstance();
            $sql = "SELECT `vtiger_tariffmanager`.custom_tariff_type FROM `vtiger_quotes`
			INNER JOIN `vtiger_tariffmanager` ON `vtiger_quotes`.effective_tariff = `vtiger_tariffmanager`.tariffmanagerid
			WHERE `vtiger_quotes`.is_primary = 1 AND `vtiger_quotes`.potentialid = ? LIMIT 1";
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
        $recordStructureInstance      = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $structuredValues = $recordStructureInstance->getStructure();
        if($record) {
            $this->convertSurveyTimeFormat($structuredValues);
        }

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
        $viewer->assign('BLFIELD', $hiddenBlocksArrayField[$moduleName]);
        /* VGS Global Business Line Blocks */
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_MODEL', $recordModel);
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
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', true);
//            $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($record));
        }
        $db            = PearDatabase::getInstance();
        $userModel     = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();
        //old securities
        /*
        $isAdmin = $userModel->isAdminUser();
        */
        $recordId     = $request->get('record');
        $defaultValue = false;
        //old securities
        /*
        if (!$isAdmin && !empty($recordId)) {
            $sql    = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid = ?";
            $result = $db->pquery($sql, [$recordId]);
            $row    = $result->fetchRow();
            if (!empty($row[0])) {
                $defaultValue = true;
            }
        }
        */
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */

        $showTransitGuide = false;
        /*
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $estimateRecordModel = $recordModel->getPrimaryEstimateRecordModel();
            if ($estimateRecordModel && $estimateRecordModel->get('effective_tariff')) {
                try {
                    if ($estimateRecordModel->get('load_date') || $recordModel->get('load_date')) {
                        $tariffManager = Vtiger_Record_Model::getInstanceById($estimateRecordModel->get('effective_tariff'), 'TariffManager');
                        //@TODO add something to the tariff manager to gui this stuff maybe a second table?  Still requires some thought.
                        switch ($tariffManager->get('custom_tariff_type')) {
                            case 'Allied Express':
                            case 'ALLV-2A':
                            case 'Blue Express':
                            case 'Intra - 400N':
                            case 'Intra - 400N':
                            case 'Local/Intra':
                            case 'Local/Intra':
                            case 'NAVL-12A':
                            case 'Pricelock':
                            case 'Pricelock GRR':
                            case 'TPG':
                            case 'TPG GRR':
                            case 'UAS':
                            case 'UAS':
                                $showTransitGuide = true;
                                break;
                            default:
                                $showTransitGuide = false;
                        }
                    }
                } catch (Exception $ex) {
                    //didn't find the tariffmanager record, means it's a local tariff so that's false!
                    $showTransitGuide = false;
                }
            }
            */
            /*
             * Estimate currently required, this may be changed in the future
            if (!$showTransitGuide) {
                //no estimate so we use the opportunity's settings
                if ($recordModel->get('load_date') &&
                    (
                        ($recordModel->get('business_line') == 'Intrastate Move') ||
                        ($recordModel->get('business_line') == 'Interstate Move')
                    )
                ) {
                    $showTransitGuide = true;
                }
            }
        }*/
        $viewer->assign('SHOW_TRANSIT_GUIDE', $showTransitGuide);

        if (getenv('INSTANCE_NAME') == 'sirva') {
            setDefaultCoordinator($recordModel, $viewer);
        }

        $viewer->assign('INSTANCE_NAME', getenv('INSTANCE_NAME'));
        $viewer->assign('DEFAULT_VALUE', $defaultValue);
        $viewer->assign('DEFAULT_GROUP', $defaultValue);
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));

        //logic to include Addresss List
        $addressListModule = Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->assignValueForAddressList($viewer,$record);
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
            $sql                           = "SELECT agent_id, permission FROM `vtiger_participating_agents` WHERE permission!=3 AND crmentity_id=?";
            $result                        = $db->pquery($sql, [$recordId]);
            $row                           = $result->fetchRow();
            while ($row != NULL) {
                $participatingAgents[] = [$row['agent_id'], $row['permission']];
                $row                   = $result->fetchRow();
            }
            foreach ($participatingAgents as $participatingAgent) {
                $sql                       = "SELECT agentname FROM `vtiger_agents` WHERE agentsid=?";
                $result                    = $db->pquery($sql, [$participatingAgent[0]]);
                $row                       = $result->fetchRow();
                $participatingAgentNames[] = [$row[0], $participatingAgent[1]];
            }
            //$sql = "SELECT participating_agents_full FROM `vtiger_potential` WHERE potentialid=?";
            //$result = $db->pquery($sql, array($recordId));
            //$row = $result->fetchRow();
            //$participatingAgentsFull = $row[0];
            //$participatingAgentsFull = explode(' |##| ', $participatingAgentsFull);
            //$sql = "SELECT participating_agents_no_rates FROM `vtiger_potential` WHERE potentialid=?";
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
        $participatingAgentModel    = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        $extraStopsModel    = Vtiger_Module_Model::getInstance('ExtraStops');
        $MoveRolesModel    = Vtiger_Module_Model::getInstance('MoveRoles');
        $jsFileNames = [];
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $jsFileNames[] = "modules.VehicleLookup.resources.Edit";
        }
        if ($participatingAgentModel && $participatingAgentModel->isActive()) {
            $jsFileNames[] = "modules.ParticipatingAgents.resources.Edit";
        }
        if ($extraStopsModel && $extraStopsModel->isActive()) {
            $jsFileNames[] = "modules.ExtraStops.resources.EditBlock";
        }
        if ($MoveRolesModel && $MoveRolesModel->isActive()) {
            $jsFileNames[] = "modules.MoveRoles.resources.EditBlock";
        }
        $jsFileNames[] = 'modules.Vtiger.resources.MoveType';
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $jsFileNames[] = "modules.Opportunities.resources.MilitaryFields";
            $jsFileNames[] = "modules.Opportunities.resources.STS";
        }
        $addressListModule=Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $jsFileNames[] = "modules.AddressList.resources.EditBlock";
        }
        $jsFileNames[] = 'modules.Vtiger.resources.DaysToMove';
        $jsFileNames[] = 'modules.Vtiger.resources.SalesPerson';
        $jsFileNames[] = 'modules.Opportunities.resources.SelfHaul';
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
