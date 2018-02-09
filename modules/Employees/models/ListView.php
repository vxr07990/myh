<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Employees_ListView_Model extends Vtiger_ListView_Model
{
    public function getQuery()
    {
        $queryGenerator = $this->get('query_generator');
        $listQuery = $queryGenerator->getQuery();

        $request = new Vtiger_Request($_REQUEST, $_REQUEST);
		
        if (($request->get('popup_type') != '' && $request->get('popup_type') == 'get_drivers') || $request->get('src_module') == 'Trips') {
            $recordId = $request->get('src_record');

            $newQuery = explode('GROUP BY', $listQuery);
            $groupBy = $newQuery[1];

            $newQuery = explode('WHERE', $newQuery[0]);
            $newQuerySelect = $newQuery[0];
            $newQueryWhere = $newQuery[1];
            $newQueryWhere .= " AND vtiger_employees.employeesid NOT IN (
            SELECT outofservice_employeesid FROM vtiger_outofservice WHERE outofservice_status = 'Out of Service' AND outofservice_satisfieddate IS NULL AND outofservice_employeesid IS NOT NULL
            )";
			
		if (getenv('INSTANCE_NAME') != "graebel"){
			$newQuerySelect .= " LEFT JOIN vtiger_employeeroles r1 ON vtiger_employeescf.employee_primaryrole=r1.employeerolesid ";
			$newQuerySelect .= " LEFT JOIN vtiger_employeeroles r2 ON vtiger_employeescf.employee_secondaryrole=r2.employeerolesid ";

			$newQueryWhere .= " AND isqualify=1 AND employee_available_longdispatch = 'Yes' AND ((r1.emprole_class = 'Owner Operator' OR r1.emprole_class = 'Lease Driver' OR r1.emprole_class = 'Driver') OR (r2.emprole_class = 'Owner Operator' OR r2.emprole_class = 'Lease Driver' OR r2.emprole_class = 'Driver'))";
		}
			
            $listQuery = $newQuerySelect . ' WHERE ' . $newQueryWhere . ' GROUP BY ' . $groupBy;
        }
        return $listQuery;
    }
    /**
     * Static Function to get the Instance of Vtiger ListView model for a given module and custom view
     * @param <String> $value - Module Name
     * @param <Number> $viewId - Custom View Id
     * @return Vtiger_ListView_Model instance
     */
    public static function getInstanceForPopup($value, $viewId)
    {
        $db = PearDatabase::getInstance();
        $currentUser = vglobal('current_user');

        $modelClassName = Vtiger_Loader::getComponentClassName('Model', 'ListView', $value);
        $instance = new $modelClassName();
        $moduleModel = Vtiger_Module_Model::getInstance($value);

        $queryGenerator = new QueryGenerator($moduleModel->get('name'), $currentUser);
        if ($viewId != 0 && $viewId != 'Trips') {
            $queryGenerator->initForCustomViewById($viewId);
        } else {
            /* Since employee popups are all over. I harcode the columns for driver in here so they
                can use the sumary fields for other employee types         */
            if ($viewId == 'Trips') {
                $listFields = array(
                    'employee_lastname',
                    'name',
                    'driver_no'
                );
            } else {
                $listFields = $moduleModel->getPopupViewFieldsList();
            }
            $listFields[] = 'id';
            $queryGenerator->setFields($listFields);
        }
        // Making sure the table for these fields is included so that the inserted condition on these won't break the query later on
        $listFields = $queryGenerator->getFields();
        if(!in_array('employee_primaryrole', $listFields)) {
            $listFields[] = 'employee_primaryrole';
        }
        if(!in_array('employee_secondaryrole', $listFields)) {
            $listFields[] = 'employee_secondaryrole';
        }
        $queryGenerator->setFields($listFields);

        $controller = new ListViewController($db, $currentUser, $queryGenerator);

        return $instance->set('module', $moduleModel)->set('query_generator', $queryGenerator)->set('listview_controller', $controller);
    }

    public function getListViewEntries($pagingModel)
    {
        $db = PearDatabase::getInstance();

        $moduleName = $this->getModule()->get('name');
        $moduleFocus = CRMEntity::getInstance($moduleName);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $queryGenerator = $this->get('query_generator');
        $listViewContoller = $this->get('listview_controller');
        $idMoveRole = $this->get('employeeRoles');
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
        if (empty($orderBy) && empty($sortOrder) && $moduleName != "Users") {
            $orderBy = 'modifiedtime';
            $sortOrder = 'DESC';
        }

        if (!empty($orderBy)) {
            $columnFieldMapping = $moduleModel->getColumnFieldMapping();
            $orderByFieldName = $columnFieldMapping[$orderBy];
            $orderByFieldModel = $moduleModel->getField($orderByFieldName);
            if ($orderByFieldModel && $orderByFieldModel->getFieldDataType() == Vtiger_Field_Model::REFERENCE_TYPE) {
                //IF it is reference add it in the where fields so that from clause will be having join of the table
                $queryGenerator = $this->get('query_generator');
                $queryGenerator->addWhereField($orderByFieldName);
                //$queryGenerator->whereFields[] = $orderByFieldName;
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
            if ($orderByFieldModel && $orderByFieldModel->isReferenceField()) {
                $referenceModules = $orderByFieldModel->getReferenceList();
                $referenceNameFieldOrderBy = array();
                foreach ($referenceModules as $referenceModuleName) {
                    $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModuleName);
                    $referenceNameFields = $referenceModuleModel->getNameFields();

                    $columnList = array();
                    foreach ($referenceNameFields as $nameField) {
                        $fieldModel = $referenceModuleModel->getField($nameField);
                        $columnList[] = $fieldModel->get('table').$orderByFieldModel->getName().'.'.$fieldModel->get('column');
                    }
                    if (count($columnList) > 1) {
                        $referenceNameFieldOrderBy[] = getSqlForNameInDisplayFormat(array('first_name'=>$columnList[0], 'last_name'=>$columnList[1]), 'Users', '').' '.$sortOrder;
                    } else {
                        $referenceNameFieldOrderBy[] = implode('', $columnList).' '.$sortOrder ;
                    }
                }
                $listQuery .= ' ORDER BY '. implode(',', $referenceNameFieldOrderBy);
            } elseif (!empty($orderBy) && $orderBy === 'smownerid') {
                $fieldModel = Vtiger_Field_Model::getInstance('assigned_user_id', $moduleModel);
                if ($fieldModel->getFieldDataType() == 'owner') {
                    $orderBy = 'COALESCE(CONCAT(vtiger_users.first_name,vtiger_users.last_name),vtiger_groups.groupname)';
                }
                $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
            } else {
                $listQuery .= ' ORDER BY '. $orderBy . ' ' .$sortOrder;
            }
        }

        $viewid = ListViewSession::getCurrentView($moduleName);
        if (empty($viewid)) {
            $viewid = $pagingModel->get('viewid');
        }
        $_SESSION['lvs'][$moduleName][$viewid]['start'] = $pagingModel->get('page');

        ListViewSession::setSessionQuery($moduleName, $listQuery, $viewid);

        $listQuery .= " LIMIT $startIndex,".($pageLimit+1);

        if ($idMoveRole && getenv('INSTANCE_NAME') != 'graebel') {
            $position = stripos($listQuery, ' WHERE ');
            if ($position) {

                $split = spliti(' WHERE ', $listQuery);
                if(!stripos($split[0], ' JOIN vtiger_employeescf ')){
                    $split[0] .= " INNER JOIN vtiger_employeescf ON vtiger_employeescf.employeesid = vtiger_employees.employeesid ";
                }
                $conditor = "(vtiger_employeescf.employee_primaryrole = ? OR CONCAT(\",\",vtiger_employeescf.employee_secondaryrole,\",\") LIKE ?) ";
                $listQuery = $split[0]. ' WHERE ' .$conditor.' AND '. $split[1];
            }

            $listResult = $db->pquery($listQuery, array($idMoveRole, "%,$idMoveRole,%"));
        } else {
            $listResult = $db->pquery($listQuery, array());
        }

        $listViewRecordModels = array();
        $listViewEntries =  $listViewContoller->getListViewRecords($moduleFocus, $moduleName, $listResult);

        //this is done at the return for different versions
        $pagingModel->calculatePageRange($listViewEntries);

        if ($db->num_rows($listResult) > $pageLimit) {
            array_pop($listViewEntries);
            $pagingModel->set('nextPageExists', true);
        } else {
            $pagingModel->set('nextPageExists', false);
        }

        $index = 0;
        foreach ($listViewEntries as $recordId => $record) {
            $rawData = $db->query_result_rowdata($listResult, $index++);
            $record['id'] = $recordId;
            //agent owner display
            try {
                $agentRecordModel   = Vtiger_Record_Model::getInstanceById($rawData['agentid'], 'AgentManager');
                $vanlineRecordModel = Vtiger_Record_Model::getInstanceById($rawData['agentid'], 'VanlineManager');
                $displayValue       = $agentRecordModel->get('agency_name')?' ('.$agentRecordModel->get('agency_code').') '.$agentRecordModel->get('agency_name'):$vanlineRecordModel->get('vanline_name');
                if ($displayValue != null) {
                    $record['agentid'] = $displayValue;
                } else {
                    $record['agentid'] = '--';
                }
            } catch (Exception $e) {
                $record['agentid'] = '--';
            }
            $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
        }

        return $listViewRecordModels;
    }
}
