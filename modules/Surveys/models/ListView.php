<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Surveys_ListView_Model extends Inventory_ListView_Model
{
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

        $queryGeneratorFields = $this->get('query_generator')->getFields();
        if(in_array('survey_date', $queryGeneratorFields) && !in_array('survey_time', $queryGeneratorFields)) {
            $queryGeneratorFields[] = 'survey_time';
            $this->get('query_generator')->setFields($queryGeneratorFields);
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
        $idPotential = $this->get('potential_id');
        if ($idPotential) {
            $position = stripos($listQuery, ' WHERE ');
            if ($position) {
                $split = spliti(' WHERE ', $listQuery);
                $conditor = "vtiger_surveys.potential_id = ?";
                $listQuery = $split[0]. ' WHERE ' .$conditor.' AND '. $split[1];
            }
            $listResult = $db->pquery($listQuery, array($idPotential));
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
            if (isset($rawData['agentid'])) {
                try {
                    $agentRecordModel   = Vtiger_Record_Model::getInstanceById($rawData['agentid'], 'AgentManager');
                    $vanlineRecordModel = Vtiger_Record_Model::getInstanceById($rawData['agentid'], 'VanlineManager');
                    $displayValue       =
                        $agentRecordModel->get('agency_name')?$agentRecordModel->get('agency_name').' ('.$agentRecordModel->get('agency_code').')':$vanlineRecordModel->get('vanline_name');
                    if ($displayValue != null) {
                        $record['agentid'] = $displayValue;
                    } else {
                        $record['agentid'] = '--';
                    }
                } catch (Exception $e) {
                    $record['agentid'] = '--';
                }
            }
            $listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $rawData);
        }

        $userModel = Users_Record_Model::getCurrentUserModel();

        //Loop corrects timezone changes to displayed date on list items

        //Moving this logic into the ListViewController so that it works in other modules with survey_date and survey_time

//        foreach ($listViewRecordModels as $recordId => $record) {
//            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
//            $surveyTime = $recordModel->get('survey_time');
//
//            $surveyDate = $record->get('survey_date');
//            file_put_contents('logs/ListView.log', date('Y-m-d H:i:s - ').(strtotime(str_replace('-', '/', $surveyDate)) === false)."\n", FILE_APPEND);
//            $surveyDate = date("Y-m-d", strtotime(str_replace('-', '/', $surveyDate))); //Format date for DateTimeField object
//
//            file_put_contents('logs/ListView.log', date('Y-m-d H:i:s - ').$surveyDate.' '.$surveyTime."\n", FILE_APPEND);
//            $date = new DateTimeField($surveyDate.' '.$surveyTime);
//            $convertedDate = $date->getDisplayDate();
//            $convertedTime = $date->getDisplayTime();
//
//            $record->set('survey_date', $convertedDate);
//            $record->set('survey_time', $convertedTime);
//        }

        return $listViewRecordModels;
    }
}
