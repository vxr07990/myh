<?php

class OrdersTask_ListView_Model extends Vtiger_ListView_Model
{
    public function getQuery()
    {
        $queryGenerator = $this->get('query_generator');
        $listQuery = $queryGenerator->getQuery();

        $request = new Vtiger_Request($_REQUEST, $_REQUEST);
        if ($request->get('view') == 'NewLocalDispatch' || $request->get('view') == 'NewLocalDispatchActuals') {
            $dateFilterFrom = $request->get("from_date");
            $dateFilterTo = $request->get("to_date");

            if ($dateFilterFrom == '' && !isset($_SESSION['lvs']['OrdersTask']["ld_date_from"])) {
                $dateFilterFrom = date('Y-m-d');
            } elseif ($dateFilterFrom == '' && isset($_SESSION['lvs']['OrdersTask']["ld_date_from"]) && $_SESSION['lvs']['OrdersTask']["ld_date_from"] != '') {
                $dateFilterFrom = vtlib_purify($_SESSION['lvs']['OrdersTask']["ld_date_from"]);
            }

            if ($dateFilterTo == '' && !isset($_SESSION['lvs']['OrdersTask']["service_date_to"])) {
                $dateFilterTo = date('Y-m-d');
            } elseif ($dateFilterTo == '' && isset($_SESSION['lvs']['OrdersTask']["service_date_to"]) && $_SESSION['lvs']['OrdersTask']["service_date_to"] != '') {
                $dateFilterTo = vtlib_purify($_SESSION['lvs']['OrdersTask']["service_date_to"]);
            }

            $str = "(( vtiger_orderstask.service_date_from >= '$dateFilterFrom'))  and (( vtiger_orderstask.service_date_from <= '$dateFilterTo'))  and (( vtiger_orderstask.disp_assigneddate IS NULL))  and (( vtiger_orderstask.date_spread = '0'))";

            $queryParts = explode('WHERE', $listQuery);
            $queryPartsGroup = explode('GROUP BY', $queryParts[1]);
            $where = $queryPartsGroup[0];
            
        //Adding dispatch assigned Dates filter

            $localDispatchLQWhere2 = "(( vtiger_orderstask.disp_assigneddate >= '$dateFilterFrom')  and ( vtiger_orderstask.disp_assigneddate <= '$dateFilterTo')) ";

	        $dispatchDatesWhere = str_replace($str, $localDispatchLQWhere2, $where);

        
        

        //Adding date spread orders filter

            $localDispatchLQWhere2 = "((date_spread=1 AND service_date_from >= '$dateFilterFrom' AND service_date_to <= '$dateFilterTo' AND disp_assigneddate IS NULL)";
            $localDispatchLQWhere2 .= " OR (date_spread=1 AND service_date_from <= '$dateFilterFrom' AND service_date_to >= '$dateFilterFrom' AND disp_assigneddate IS NULL)";
            $localDispatchLQWhere2 .= " OR (date_spread=1 AND service_date_from <= '$dateFilterTo' AND service_date_to >= '$dateFilterTo' AND disp_assigneddate IS NULL))";

            
            $dateSpreadWhere = str_replace($str, $localDispatchLQWhere2, $where);



            $listQuery = $queryParts[0] . ' WHERE ((' . $where . ') OR (' . $dispatchDatesWhere . ') OR (' .  $dateSpreadWhere . '))' . ' ORDER BY ' .  $queryPartsGroup[1];


        } elseif (($request->get('popup_type') != '' && $request->get('popup_type') == 'local_dispatch_related')) {
            //VGS - Conrado Filtering the same order while building the copy resources popup

            $recordId = $request->get('src_record');
            
            $localDispatchLQ = explode('GROUP BY', $listQuery);
            $groupBy = $localDispatchLQ[1];

            $localDispatchLQ = explode('WHERE', $localDispatchLQ[0]);
            $localDispatchLQSelect = $localDispatchLQ[0];
            $localDispatchLQWhere = $localDispatchLQ[1];
            $localDispatchLQWhere .= " AND vtiger_orderstask.orderstaskid <> $recordId";
            
            $listQuery = $localDispatchLQSelect . ' WHERE ' . $localDispatchLQWhere . ' GROUP BY ' . $groupBy;
        } elseif ($request->get('view') == 'PopupAjax' && ($request->get('search_key') == 'assigned_employee' || $request->get('search_key') == 'assigned_vehicles')) {
            //@VGS - Conrado: Since assigned employee and vehicle are "special" fields we need to hack the query and create the custom join :(
            $localDispatchLQ = explode('AND', $listQuery);
            $newQuery = '';
            foreach ($localDispatchLQ as $queryPieces) {
                if ($queryPieces == $localDispatchLQ[0]) {
                    $newQuery = $queryPieces;
                    continue;
                }

                if (strpos($queryPieces, 'assigned_employee') === false && strpos($queryPieces, 'assigned_vehicle') === false) {
                    $newQuery .= 'AND ' . $queryPieces;
                }
            }

            $listQuery = $newQuery;
        }

        //@TODO: fix this better.
        //@NOTE: What is happening here is we're going to group by the ordertaskid, so that we don't have duplicate orderstasks.
        //we won't if there is a GROUP BY and only if vtiger_orderstask.orderstaskid is listed in the field list that is normally
        //in these queries.  This is because the query does not return unique entries and it combines the entries to make an array
        //of uniques, this can make a query that returns 178 rows display only 4, because there are actually only 28 uniques and
        //when they use the limit 0,21 there are only 4 in the first 20.
        if (!preg_match('/GROUP BY/', $listQuery) && preg_match('/vtiger_orderstask[`\.`]+orderstaskid/i', $listQuery)) {
            if (preg_match('/ORDER BY/i', $listQuery)) {
                $listQuery = preg_replace('/(ORDER BY.*?$)/i', ' GROUP BY `vtiger_orderstask`.`orderstaskid` $1', $listQuery);
            } else if (preg_match('/LIMIT /i', $listQuery)) {
                $listQuery = preg_replace('/LIMIT /i', ' GROUP BY `vtiger_orderstask`.`orderstaskid` LIMIT ', $listQuery);
            } else {
                $listQuery .= ' GROUP BY `vtiger_orderstask`.`orderstaskid` ';
            }
        }
        return $listQuery;
    }

    /**
     * Function to get the list view entries
     * @param Vtiger_Paging_Model $pagingModel
     * @return <Array> - Associative array of record id mapped to Vtiger_Record_Model instance.
     */
    public function getListViewEntries($pagingModel)
    {
        $db = PearDatabase::getInstance();

        $moduleName = $this->getModule()->get('name');
        $moduleFocus = CRMEntity::getInstance($moduleName);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $queryGenerator = $this->get('query_generator');
        $listViewContoller = $this->get('listview_controller');

        $searchParams = $this->get('search_params');
        if (empty($searchParams)) {
            $searchParams = array();
        }
        $glue = "";
        if (count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
            $glue = QueryGenerator::$AND;
        }
        $queryGenerator->parseAdvFilterList($searchParams, $glue);

        $searchKey = $this->get('search_key');
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if (!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
        }


        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

    //List view will be displayed on recently created/modified records
    if (empty($orderBy) && empty($sortOrder) && $moduleName != "Users" && vtlib_purify($_REQUEST['view']) != 'NewLocalDispatch' && vtlib_purify($_REQUEST['view']) != 'NewLocalDispatchActuals') {
        $orderBy = 'modifiedtime';
        $sortOrder = 'DESC';
    }

        if (!empty($orderBy)) {
            $columnFieldMapping = $moduleModel->getColumnFieldMapping();
            $orderByFieldName = $columnFieldMapping[$orderBy];
            $orderByFieldModel = $moduleModel->getField($orderByFieldName);
        
        //Hack to use in local - We are displaying multiples modules in one listview

        if (!$orderByFieldModel) {
            $ordersModel = Vtiger_Module_Model::getInstance('Orders');
            $orderByFieldModel = $ordersModel->getField($orderByFieldName);
        }
        
            if (!$orderByFieldModel) {
                $ordersModel = Vtiger_Module_Model::getInstance('Trips');
                $orderByFieldModel = $ordersModel->getField($orderByFieldName);
            }
        
            if (!$orderByFieldModel) {
                $ordersModel = Vtiger_Module_Model::getInstance('Estimates');
                $orderByFieldModel = $ordersModel->getField($orderByFieldName);
            }
        
        
            if ($orderByFieldModel && $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE) {
                //IF it is reference add it in the where fields so that from clause will be having join of the table
        $queryGenerator = $this->get('query_generator');
                $queryGenerator->addWhereField($orderByFieldName);
            }
        }
        $listQuery = $this->getQuery();


        $sourceModule = $this->get('src_module');
        if (!empty($sourceModule)) {
            if (method_exists($moduleModel, 'getQueryByModuleField')) {
                $overrideQuery = $moduleModel->getQueryByModuleField($sourceModule, $this->get('src_field'), $this->get('src_record'), $listQuery);
                if (!empty($overrideQuery)) {
                    $listQuery = $overrideQuery;
                }
            }
        }

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

    
        if (!empty($orderBy)) {
            //so an orderBy was passed in well dump the old one that appears to be tacked on always.
            $listQuery = preg_replace('/ORDER BY\s+\`vtiger_crmentity\`.crmid\s+$/i','',$listQuery);
            if ($orderByFieldModel && $orderByFieldModel->isReferenceField()) {
                $referenceModules = $orderByFieldModel->getReferenceList();
                $referenceNameFieldOrderBy = array();
                foreach ($referenceModules as $referenceModuleName) {
                    $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModuleName);
                    $referenceNameFields = $referenceModuleModel->getNameFields();

                    $columnList = array();
                    foreach ($referenceNameFields as $nameField) {
                        $fieldModel = $referenceModuleModel->getField($nameField);
                        $columnList[] = $fieldModel->get('table') . $orderByFieldModel->getName() . '.' . $fieldModel->get('column');
                    }
                    if (count($columnList) > 1) {
                        $referenceNameFieldOrderBy[] = getSqlForNameInDisplayFormat(array('first_name' => $columnList[0], 'last_name' => $columnList[1]), 'Users', '') . ' ' . $sortOrder;
                    } else {
                        $referenceNameFieldOrderBy[] = implode('', $columnList) . ' ' . $sortOrder;
                    }
                }
                if (vtlib_purify($_REQUEST['view']) != 'NewLocalDispatch' && vtlib_purify($_REQUEST['view']) != 'NewLocalDispatchActuals') {
                    $listQuery .= ' ORDER BY ' . implode(',', $referenceNameFieldOrderBy);
                } else {
                    //Need to do this because UNION Queries required the order column to be in the select

                $referenceNameFieldOrderBy = $referenceNameFieldOrderBy[0] . ' AS queryorders_column';
                    $referenceNameFieldOrderBy = str_replace($sortOrder, '', $referenceNameFieldOrderBy);
                    $newSelect = 'SELECT ' . $referenceNameFieldOrderBy . ', ';
                    $listQuery = str_replace('SELECT', $newSelect, $listQuery);
                
                    $listQuery .= ' ORDER BY queryorders_column ' . $sortOrder;
                }
            } elseif (!empty($orderBy) && $orderBy === 'smownerid') {
                $fieldModel = Vtiger_Field_Model::getInstance('assigned_user_id', $moduleModel);
                if ($fieldModel->getFieldDataType() == 'owner') {
                    $orderBy = 'COALESCE(CONCAT(vtiger_users.first_name,vtiger_users.last_name),vtiger_groups.groupname)';
                }

                if (vtlib_purify($_REQUEST['view']) != 'NewLocalDispatch' && vtlib_purify($_REQUEST['view']) != 'NewLocalDispatchActuals') {
                    $listQuery .= ' ORDER BY ' . $orderBy . ' ' . $sortOrder;
                } else {
                    //Need to do this because UNION Queries required the order column to be in the select

                $referenceNameFieldOrderBy = $orderBy . ' AS queryorders_column';
                    $newSelect = 'SELECT ' . $referenceNameFieldOrderBy . ', ';
                    $listQuery = str_replace('SELECT', $newSelect, $listQuery);
                
                    $listQuery .= ' ORDER BY queryorders_column ' . $sortOrder;
                }
            } else {
                $listQuery .= ' ORDER BY ' . $orderBy . ' ' . $sortOrder;
            }
        }
    
        if($moduleName == 'OrdersTask' && $_GET['view'] == 'NewLocalDispatch'){
            $listQuery = str_replace('INNER JOIN' , 'LEFT JOIN', $listQuery);
        }

        $viewid = ListViewSession::getCurrentView($moduleName);
        if (empty($viewid)) {
            $viewid = $pagingModel->get('viewid');
        }
        $_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);

        $listQuery .= " LIMIT $startIndex," . ($pageLimit + 1);

        $listResult = $db->pquery($listQuery, array());

        $listViewRecordModels = array();
        $listViewEntries = $this->getListViewRecords($moduleFocus, $moduleName, $listResult);

        $request = new Vtiger_Request($_REQUEST, $_REQUEST);
        if ($request->get('view') == 'PopupAjax' && ($request->get('search_key') == 'assigned_employee' || $request->get('search_key') == 'assigned_vehicles')) {
            $listViewEntries = $this->filterByRelatedRecords($listViewEntries, $request->get('search_key'), $request->get('search_value'));
        }

        //this is done at the return for different versions
        $pagingModel->calculatePageRange($listViewEntries);

        if ($request->get('view') !== 'PopupAjax') {
            if ($db->num_rows($listResult) > $pageLimit) {
                array_pop($listViewEntries);
                $pagingModel->set('nextPageExists', true);
            } else {
                $pagingModel->set('nextPageExists', false);
            }
        }

        $index = 0;
        foreach ($listViewEntries as $recordId => $record) {
            $rawData = $db->query_result_rowdata($listResult, $index++);
            $record['id'] = $recordId;
            //agent owner display
            $sql = "SELECT agency_name, agency_code FROM `vtiger_agentmanager` WHERE agentmanagerid=?";
            $result = $db->pquery($sql, array($rawData['agentid']));
            $row = $result->fetchRow();
            if ($row != null) {
                $record['agentid'] = $row['agency_name'] . ' (' . $row['agency_code'] . ')';
            } else {
                $record['agentid'] = '--';
            }
            $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
        }

        return $listViewRecordModels;
    }

    public function getListViewHeaders()
    {
        $listViewContoller = $this->get('listview_controller');
        $module = $this->getModule();
        $headerFieldModels = array();
        $headerFields = $this->getListViewHeaderFields();
        foreach ($headerFields as $fieldName => $webserviceField) {
            if ($webserviceField && !in_array($webserviceField->getPresence(), array(0, 2))) {
                continue;
            }

            //VGS - Add this new if to show the fields from related module.
            if ($webserviceField->getTabId() != $module->id) {
                $headerFieldModels[$fieldName] = Vtiger_Field_Model::getInstance($fieldName, Vtiger_Module_Model::getInstance(getTabModuleName($webserviceField->getTabId())));
            } else {
                $headerFieldModels[$fieldName] = Vtiger_Field_Model::getInstance($fieldName, $module);
            }
        }
        return $headerFieldModels;
    }

    public function getListViewHeaderFields()
    {
        $this->queryGenerator = $this->get('query_generator');
        $meta = $this->queryGenerator->getMeta($this->queryGenerator->getModule());
        $moduleFields = $this->queryGenerator->getModuleFields();
        $fields = $this->queryGenerator->getFields();

        $ordersMeta = $this->queryGenerator->getMeta('Orders');
        $ordersModuleFieldList = $ordersMeta->getModuleFields();
        /** array_merge:
            If the input arrays have the same string keys, then the later value for that key will overwrite the previous one.
        **/
        $moduleFields = array_merge($ordersModuleFieldList, $moduleFields);
        $headerFields = array();
        foreach ($fields as $fieldName) {
            if (array_key_exists($fieldName, $moduleFields)) {
                $headerFields[$fieldName] = $moduleFields[$fieldName];
            }
        }
        return $headerFields;
    }
    
    
     //VGS - Conrado   OT16198 adding some orders fields to the Local Dispatch. We need to display multiples modules info in one query.
    // I think this could be extended to all the module but for now I will encapsulate in OrdersTasks. This is just a carbon copy of another function
    // the only way I found not to hack everything.

    
    public function getListViewRecords($focus, $module, $result)
    {
        global $listview_max_textlength, $theme, $default_charset;

        $listViewContoller = $this->get('listview_controller');
        
        $currentUser = Users_Record_Model::getCurrentUserModel();

        $currentUserId = $currentUser->id;
        require ('include/utils/LoadUserPrivileges.php');

        $queryGenerator = $this->get('query_generator');
        $request = new Vtiger_Request($_REQUEST, $_REQUEST);
        $is_local = false;
        if ($request->get('view') == 'NewLocalDispatch') {
            $is_local = true;
        }
       
        
        $fields = $queryGenerator->getFields();
        $meta = $queryGenerator->getMeta($queryGenerator->getModule());

        $moduleFields = $queryGenerator->getModuleFields();
        $accessibleFieldList = array_keys($moduleFields);
        $listViewFields = array_intersect($fields, $accessibleFieldList);

        $referenceFieldList = $queryGenerator->getReferenceFieldList();
        foreach ($referenceFieldList as $fieldName) {
            if (in_array($fieldName, $listViewFields)) {
                $field = $moduleFields[$fieldName];
                $listViewContoller->fetchNameList($field, $result);
            }
        }

        $db = PearDatabase::getInstance();
        $rowCount = $db->num_rows($result);
        $ownerFieldList = $queryGenerator->getOwnerFieldList();
        foreach ($ownerFieldList as $fieldName) {
            if (in_array($fieldName, $listViewFields)) {
                $field = $moduleFields[$fieldName];
                $idList = array();
                for ($i = 0; $i < $rowCount; $i++) {
                    $id = $db->query_result($result, $i, $field->getColumnName());
                    if (!isset($this->ownerNameList[$fieldName][$id])) {
                        $idList[] = $id;
                    }
                }
                if (count($idList) > 0) {
                    if (!is_array($this->ownerNameList[$fieldName])) {
                        $this->ownerNameList[$fieldName] = getOwnerNameList($idList);
                    } else {
                        //array_merge API loses key information so need to merge the arrays
                        // manually.
                        $newOwnerList = getOwnerNameList($idList);
                        foreach ($newOwnerList as $id => $name) {
                            $this->ownerNameList[$fieldName][$id] = $name;
                        }
                    }
                }
            }
        }

        foreach ($listViewFields as $fieldName) {
            $field = $moduleFields[$fieldName];
            if (!$is_admin && ($field->getFieldDataType() == 'picklist' ||
                    $field->getFieldDataType() == 'multipicklist')) {
                $listViewContoller->setupAccessiblePicklistValueList($fieldName);
            }
        }

        $moduleInstance = Vtiger_Module_Model::getInstance("PBXManager");
        if ($moduleInstance && $moduleInstance->isActive()) {
            $outgoingCallPermission = PBXManager_Server_Model::checkPermissionForOutgoingCall();
        }

        $useAsterisk = get_use_asterisk($currentUser->id);

        $data = array();
        for ($i = 0; $i < $rowCount; ++$i) {
            //Getting the recordId

            $baseTable = $meta->getEntityBaseTable();
            $moduleTableIndexList = $meta->getEntityTableIndexList();
            $baseTableIndex = $moduleTableIndexList[$baseTable];

            $recordId = $db->query_result($result, $i, $baseTableIndex);

            $row = array();

            foreach ($listViewFields as $fieldName) {
                $field = $moduleFields[$fieldName];
                $uitype = $field->getUIType();
                $rawValue = $db->query_result($result, $i, $field->getColumnName());

                if (in_array($uitype, array(15, 33, 16))) {
                    $value = html_entity_decode($rawValue, ENT_QUOTES, $default_charset);
                } else {
                    $value = $rawValue;
                }

                if ($field->getFieldDataType() == 'picklist') {
                    //not check for permissions for non admin users for status and activity type field
                    if ($module == 'Calendar' && ($fieldName == 'taskstatus' || $fieldName == 'eventstatus' || $fieldName == 'activitytype')) {
                        $value = Vtiger_Language_Handler::getTranslatedString($value, $module);
                        $value = textlength_check($value);
                    } else {
                        $value = Vtiger_Language_Handler::getTranslatedString($value, $module);
                        $value = textlength_check($value);
                    }
                } elseif ($field->getFieldDataType() == 'date' || $field->getFieldDataType() == 'datetime') {
                    if ($value != '' && $value != '0000-00-00') {
                        $fieldDataType = $field->getFieldDataType();
                        if ($module == 'Calendar' && ($fieldName == 'date_start' || $fieldName == 'due_date')) {
                            if ($fieldName == 'date_start') {
                                $timeField = 'time_start';
                            } elseif ($fieldName == 'due_date') {
                                $timeField = 'time_end';
                            }
                            $timeFieldValue = $db->query_result($result, $i, $timeField);
                            if (!empty($timeFieldValue)) {
                                $value .= ' ' . $timeFieldValue;
                                //TO make sure it takes time value as well
                                $fieldDataType = 'datetime';
                            }
                        }
                        if ($fieldDataType == 'datetime') {
                            $value = Vtiger_Datetime_UIType::getDateTimeValue($value);
                        } elseif ($fieldDataType == 'date') {
                            $date = new DateTimeField($value);
                            $value = $date->getDisplayDate();
                        }
                    } elseif ($value == '0000-00-00') {
                        $value = '';
                    }
                } elseif ($field->getFieldDataType() == 'time') {
                    if (!empty($value)) {
			
			if($fieldName == 'disp_assignedstart' || $fieldName == 'disp_actualend'){
			    //We need to display in user Timezone. These two fields are store in DB time
			    $value = Vtiger_Time_UIType::getDisplayTimeValue($value);
			} else {
                        $userModel = Users_Privileges_Model::getCurrentUserModel();
                        if ($userModel->get('hour_format') == '12') {
                            $value = Vtiger_Time_UIType::getTimeValueInAMorPM($value);
			    }
                        }
                    }
                } elseif ($field->getFieldDataType() == 'currency') {
                    if ($value != '') {
                        if ($field->getUIType() == 72) {
                            if ($fieldName == 'unit_price') {
                                $currencyId = getProductBaseCurrency($recordId, $module);
                                $cursym_convrate = getCurrencySymbolandCRate($currencyId);
                                $currencySymbol = $cursym_convrate['symbol'];
                            } else {
                                $currencyInfo = getInventoryCurrencyInfo($module, $recordId);
                                $currencySymbol = $currencyInfo['currency_symbol'];
                            }
                            $value = CurrencyField::convertToUserFormat($value, null, true);
                            $row['currencySymbol'] = $currencySymbol;
//							$value = CurrencyField::appendCurrencySymbol($currencyValue, $currencySymbol);
                        } else {
                            if (!empty($value)) {
                                $value = CurrencyField::convertToUserFormat($value);
                            }
                        }
                    }
                } elseif ($field->getFieldDataType() == 'url') {
                    $matchPattern = "^[\w]+:\/\/^";
                    preg_match($matchPattern, $rawValue, $matches);
                    if (!empty($matches[0])) {
                        $value = '<a class="urlField cursorPointer" href="' . $rawValue . '" target="_blank">' . textlength_check($value) . '</a>';
                    } else {
                        $value = '<a class="urlField cursorPointer" href="http://' . $rawValue . '" target="_blank">' . textlength_check($value) . '</a>';
                    }
                } elseif (!$is_local && $field->getFieldDataType() == 'assignedemployee') {
                    if (is_array($rawValue)) {
                        $value = implode(' |##| ', $rawValue);
                    }

                    $employeeIds = explode(' |##| ', $rawValue);
                    $value = '';

                    $employeeCount = 0;
                    foreach ($employeeIds as $employeeId) {
                        if ($employeeId == '') {
                            continue;
                        }
                        $employeeCount = $employeeCount + 1;

                        $employeeRecordModel = Vtiger_Record_Model::getInstanceById($employeeId, 'Employees');
                        $value .= $employeeRecordModel->get('name') . ' ' . $employeeRecordModel->get('employee_lastname');
                        $value .= ' (' . $employeeRecordModel->get('employee_type') ;
                        
                        $resultRole = $db->pquery("SELECT emprole_desc AS role FROM vtiger_employeeroles WHERE employeerolesid IN (SELECT role FROM vtiger_orderstasksemprel WHERE taskid=? AND employeeid=?)", array($recordId, $employeeId));
                        if ($resultRole && $db->num_rows($resultRole) > 0) {
                            $value .=  ' - ' .  $db->query_result($resultRole, 0, 'role');
                        }
                        
                        $value .=  ')';

                        if ($employeeId != end($employeeIds)) {
                            $value .= ', ';
                        }

                        if($employeeCount % 2 == 0 && $employeeId != end($employeeIds)){
                            $value .= '<br>';
                        }

                    }
                } elseif (!$is_local && $field->getFieldDataType() == 'assignedvehicles') {
                    if (is_array($rawValue)) {
                        $rawValue = implode(' |##| ', $rawValue);
                    }

                    $vehiclesIds = explode(' |##| ', $rawValue);
                    $value = '';

                    foreach ($vehiclesIds as $vehicleId) {
                        if ($vehicleId == '') {
                            continue;
                        }

                        $vehicleRecordModel = Vtiger_Record_Model::getInstanceById($vehicleId, 'Vehicles');
                        $value .= $vehicleRecordModel->get('vechiles_unit') . ' (' . $vehicleRecordModel->get('vehicle_type') . ')';

                        if ($vehicleId != end($vehiclesIds)) {
                            $value .= ', ';
                        }
                    }
                } elseif ($field->getFieldDataType() == 'email') {
                    global $current_user;
                    if ($current_user->internal_mailer == 1) {
                        //check added for email link in user detailview
                        $value = "<a class='emailField' onclick=\"Vtiger_Helper_Js.getInternalMailer($recordId," .
                                "'$fieldName','$module');\">" . textlength_check($value) . "</a>";
                    } else {
                        $value = '<a class="emailField" href="mailto:' . $rawValue . '">' . textlength_check($value) . '</a>';
                    }
                } elseif ($field->getFieldDataType() == 'boolean') {
                    if ($value === 'on') {
                        $value = 1;
                    } elseif ($value == 'off') {
                        $value = 0;
                    }
                    if ($value == 1) {
                        $value = getTranslatedString('yes', $module);
                    } elseif ($value == 0 && !is_null($value)) {
                        $value = getTranslatedString('no', $module);
                    } else {
                        $value = '--';
                    }
                } elseif ($field->getUIType() == 98) {
                    $value = '<a href="index.php?module=Roles&parent=Settings&view=Edit&record=' . $value . '">' . textlength_check(getRoleName($value)) . '</a>';
                } elseif ($field->getFieldDataType() == 'multipicklist') {
                    $value = ($value != "") ? str_replace(' |##| ', ', ', $value) : "";
                    if (!$is_admin && $value != '') {
                        $valueArray = ($rawValue != "") ? explode(' |##| ', $rawValue) : array();
                        $notaccess = '<font color="red">' . getTranslatedString('LBL_NOT_ACCESSIBLE', $module) . "</font>";
                        $tmp = '';
                        $tmpArray = array();
                        foreach ($valueArray as $index => $val) {
                            if (!$listview_max_textlength ||
                                    !(strlen(preg_replace("/(<\/?)(\w+)([^>]*>)/i", "", $tmp)) >
                                    $listview_max_textlength)) {
                                $tmpArray[] = $val;
                                $tmp .= ', ' . $val;
                            } else {
                                $tmpArray[] = '...';
                                $tmp .= '...';
                            }
                        }
                        $value = implode(', ', $tmpArray);
                        $value = textlength_check($value);
                    }
                } elseif ($field->getFieldDataType() == 'skype') {
                    $value = ($value != "") ? "<a href='skype:$value?call'>" . textlength_check($value) . "</a>" : "";
                } elseif ($field->getUIType() == 11) {
                    if ($outgoingCallPermission && !empty($value)) {
                        $phoneNumber = preg_replace('/[-()\s+]/', '', $value);
                        $value = '<a class="phoneField" data-value="' . $phoneNumber . '" record="' . $recordId . '" onclick="Vtiger_PBXManager_Js.registerPBXOutboundCall(\'' . $phoneNumber . '\', ' . $recordId . ')">' . textlength_check($value) . '</a>';
                    } else {
                        $value = textlength_check($value);
                    }
                } elseif ($field->getFieldDataType() == 'reference') {
                    $referenceFieldInfoList = $queryGenerator->getReferenceFieldInfoList();
                    $moduleList = $referenceFieldInfoList[$fieldName];
                    if (count($moduleList) == 1) {
                        $parentModule = $moduleList[0];
                    } else {
                        if ($rawValue == $value) {
                            $parentModule = getSalesEntityType($rawValue);
                            $value = getEntityName($parentModule, array($rawValue));
                            $value = textlength_check($value[$rawValue]);
                        }
                    }
                    if (!empty($value) && !empty($parentModule)) {
                        $parentMeta = $queryGenerator->getMeta($parentModule);
                        
                        if ($rawValue == $value) {
                            $value = getEntityName($parentModule, array($rawValue));
                            $value = textlength_check($value[$rawValue]);
                        }
                        
                        
                        if ($parentMeta->isModuleEntity() && $parentModule != "Users") {
                            $value = "<a href='?module=$parentModule&view=Detail&" .
                                    "record=$rawValue' title='" . getTranslatedString($parentModule, $parentModule) . "'>$value</a>";
                        }
                    } else {
                        $value = '--';
                    }
                } elseif ($field->getFieldDataType() == 'owner') {
                    $value = textlength_check($this->ownerNameList[$fieldName][$value]);
                } elseif ($field->getUIType() == 25) {
                    //TODO clean request object reference.
                    $contactId = $_REQUEST['record'];
                    $emailId = $db->query_result($result, $i, "activityid");
                    $result1 = $db->pquery("SELECT access_count FROM vtiger_email_track WHERE " .
                            "crmid=? AND mailid=?", array($contactId, $emailId));
                    $value = $db->query_result($result1, 0, "access_count");
                    if (!$value) {
                        $value = 0;
                    }
                } elseif ($field->getUIType() == 8) {
                    if (!empty($value)) {
                        $temp_val = html_entity_decode($value, ENT_QUOTES, $default_charset);
                        $json = new Zend_Json();
                        $value = vt_suppressHTMLTags(implode(',', $json->decode($temp_val)));
                    }
                } elseif (in_array($uitype, array(7, 9, 90))) {
                    $value = "<span align='right'>" . textlength_check($value) . "</div>";
                } else {
                    $value = textlength_check($value);
                }


                $row[$fieldName] = $value;
            }
            $data[$recordId] = $row;
        }
        return $data;
    }

    public function filterByRelatedRecords($listViewEntries, $searchField, $searchValue)
    {
        $db = PearDatabase::getInstance();

        foreach ($listViewEntries as $recordId => $entryInfo) {
            $entityFieldValue = strtolower($entryInfo[$searchField]);
            if (strpos($entityFieldValue, $searchValue) === false) {
                unset($listViewEntries[$recordId]);
            }
        }

        return $listViewEntries;
    }

    public function getListViewCount()
    {
        $db = PearDatabase::getInstance();

        $queryGenerator = $this->get('query_generator');

        $searchParams = $this->get('search_params');
        if (empty($searchParams)) {
            $searchParams = array();
        }
        
        $glue = "";
        if (count($queryGenerator->getWhereFields()) > 0 && (count($searchParams)) > 0) {
            $glue = QueryGenerator::$AND;
        }
        $queryGenerator->parseAdvFilterList($searchParams, $glue);
        
        $searchKey = $this->get('search_key');
        $searchValue = $this->get('search_value');
        $operator = $this->get('operator');
        if (!empty($searchKey)) {
            $queryGenerator->addUserSearchConditions(array('search_field' => $searchKey, 'search_text' => $searchValue, 'operator' => $operator));
        }
        $moduleName = $this->getModule()->get('name');
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $listQuery = $this->getQuery();

        $listResult = $db->pquery($listQuery, array());

        // not sure if this is the right thing to do here
        if (strpos($listQuery, 'GROUP BY ') === false) {
            $queryResult = $db->query_result($listResult, 0, 'count');
            if (!$queryResult && $queryResult !== '0') {
                $queryResult = $db->num_rows($listResult);
            }
        } else {
            $queryResult = $db->num_rows($listResult);
        }
        return $queryResult;
    }

        /**
     * Function to get the list of Mass actions for the module
     * @param <Array> $linkParams
     * @return <Array> - Associative array of Link type to List of  Vtiger_Link_Model instances for Mass Actions
     */
     public function getListViewMassActions($linkParams)
     {
         $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
         $moduleModel = $this->getModule();
 
         $linkTypes = array('LISTVIEWMASSACTION');
         $links = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);
        
         $request = new Vtiger_Request($_REQUEST, $_REQUEST);

         if($request->get('view') == 'List'){
            
        
            $massActionLinks = array();
            if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'EditView')) {
                $massEditLink = 'javascript:Vtiger_List_Js.triggerMassEdit("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");';
                if($moduleModel->isCheckBeforeEditDeleteRequired()) {
                    $massEditLink = 'javascript:Vtiger_List_Js.checkAndTriggerMassEdit("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");';
                }
                $massActionLinks[] = array(
                    'linktype' => 'LISTVIEWMASSACTION',
                    'linklabel' => 'LBL_EDIT',
                    'linkurl' => $massEditLink,
                    'linkicon' => ''
                );
            }
            if ($currentUserModel->hasModuleActionPermission($moduleModel->getId(), 'Delete')) {
                $massDeleteLink = 'javascript:Vtiger_List_Js.massDeleteRecords("index.php?module='.$moduleModel->get('name').'&action=MassDelete");';
                if($moduleModel->isCheckBeforeEditDeleteRequired()) {
                    $massDeleteLink = 'javascript:Vtiger_List_Js.checkAndTriggerMassDelete("index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showMassEditForm");';
                }
                $massActionLinks[] = array(
                    'linktype' => 'LISTVIEWMASSACTION',
                    'linklabel' => 'LBL_DELETE',
                    'linkurl' => $massDeleteLink,
                    'linkicon' => ''
                );
            }
    
            $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
            if ($moduleModel->isCommentEnabled() && $modCommentsModel->isPermitted('EditView')) {
                $massActionLinks[] = array(
                    'linktype' => 'LISTVIEWMASSACTION',
                    'linklabel' => 'LBL_ADD_COMMENT',
                    'linkurl' => 'index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=showAddCommentForm',
                    'linkicon' => ''
                );
            }
    
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_PRINT',
                'linkurl' => 'index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=printRecords',
                'linkicon' => ''
            );
    
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_DUPLICATE',
                'linkurl' => 'index.php?module='.$moduleModel->get('name').'&view=MassActionAjax&mode=duplicateRecords',
                'linkicon' => ''
            );

        }

        if($request->get('view') == 'NewLocalDispatch'){
            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_CREATE_LOCAL_TASK',
                'linkurl' => 'javascript:OrdersTask_LocalDispatch_Js.triggerCreateLocalTask()',
                'linkicon' => ''
            );

            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_COPY_RESOURCES',
                'linkurl' => 'javascript:OrdersTask_LocalDispatch_Js.showCopyModal()',
                'linkicon' => ''
            );

            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_DELETE_ALL_CREW',
                'linkurl' => 'javascript:OrdersTask_LocalDispatch_Js.customActionButtons(\'Crew\')',
                'linkicon' => ''
            );

            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_DELETE_ALL_EQUIPMENTS',
                'linkurl' => 'javascript:OrdersTask_LocalDispatch_Js.customActionButtons(\'Vehicle\')',
                'linkicon' => ''
            );

            $massActionLinks[] = array(
                'linktype' => 'LISTVIEWMASSACTION',
                'linklabel' => 'LBL_DELETE_ALL_VENDORS',
                'linkurl' => 'javascript:OrdersTask_LocalDispatch_Js.customActionButtons(\'Vendor\')',
                'linkicon' => ''
            );
           
        }

 
         $massActionLinks[] = array(
             'linktype' => 'LISTVIEWMASSACTION',
             'linklabel' => 'LBL_EDITFILTER',
             'linkurl' => 'javascript:triggerEditFilter()',
             'linkicon' => ''
         );
 
         $massActionLinks[] = array(
             'linktype' => 'LISTVIEWMASSACTION',
             'linklabel' => 'LBL_DELETEFILTER',
             'linkurl' => 'javascript:triggerDeleteFilter()',
             'linkicon' => ''
         );
 
         $massActionLinks[] = array(
             'linktype' => 'LISTVIEWMASSACTION',
             'linklabel' => 'LBL_CREATEFILTER',
			 'linkurl' => 'javascript:triggerCreateFilter()',
             'linkicon' => ''
         );


         

 
         foreach ($massActionLinks as $massActionLink) {
             $links['LISTVIEWMASSACTION'][] = Vtiger_Link_Model::getInstanceFromValues($massActionLink);
         }
 
         return $links;
     }
}
