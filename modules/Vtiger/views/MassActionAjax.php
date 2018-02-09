<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('libraries/nusoap/nusoap.php');

class Vtiger_MassActionAjax_View extends Vtiger_IndexAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showMassEditForm');
        $this->exposeMethod('showAddCommentForm');
        $this->exposeMethod('showComposeEmailForm');
        $this->exposeMethod('showSendSMSForm');
        $this->exposeMethod('showDuplicatesSearchForm');
        $this->exposeMethod('transferOwnership');
        $this->exposeMethod('printRecords');
        $this->exposeMethod('duplicateRecords');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public function duplicateRecords(Vtiger_Request $request)
    {
        global $adb;

        // I guess.
        include_once 'include/Webservices/Retrieve.php';
        include_once 'include/Webservices/Create.php';

        $current_user = Users_Record_Model::getCurrentUserModel();

        $selectedIds = $request->get('selected_ids');
        $moduleName  = $request->getModule();

        $return = ['has_message' => false, 'consolidated' => '', 'logs' => []];
        foreach ($selectedIds as $selectedId) {
            $log = ['duplicated' => false, 'log' => "Record ID: ".$selectedId."<br/> Module: ".$moduleName."<br/>"];

            //retrieve the old record so that we can use it to make a copy
            $oldRecord   = $this->doRetreive($moduleName, $selectedId, $current_user, $log);
            //@TODO: consider appending ' - Copy' to the name, since the name might not be "name" this is a todo
            if($oldRecord !== false) {
                // I guess.
                $dupString = " - Copy";
                switch ($moduleName) {
                    case 'Opportunities':
                        $oldRecord['potentialname'] .= $dupString;
                        unset($oldRecord['register_sts']);
                        unset($oldRecord['sts_response']);
                        unset($oldRecord['register_sts_number']);
                        break;
                    case 'Estimates':
                        $oldRecord['subject'] .= $dupString;
                        break;
                    case 'Cubesheets':
                        $oldRecord['cubesheet_name'] .= $dupString;
                        break;
                }
                //create new record
                $newRecordId = $this->doCreate($moduleName, $oldRecord, $current_user, $log);

                //only handle the special cases if we managed to create a new duplicate.
                if ($newRecordId !== false) {
                    $log['duplicated'] = true;
                    $error = $this->handleCustomDuplication($moduleName, $selectedId, $newRecordId);
                    if($error !== false) {
                        $log['log'] .= $error.'<br/>';
                    }else {
                        $log['log'] .= 'New ID: '.$newRecordId.'<br/>';
                    }
                }
            }

            $return['logs'][$selectedId] = $log;
            $return['consolidated'] .= $this->buildConsolidatedPart($log);
        }
        // Log into the Duplication log for reference...
        MoveCrm\LogUtils::LogToFile('LOG_DUPLICATION',"User: ".$current_user->getID()." (Name: ".$current_user->get('user_name').")\n");
        MoveCrm\LogUtils::LogToFile('LOG_DUPLICATION',"Log: ".print_r($return['logs'], true)."\n");

        // Return the consolidated log to the client.
        if($return['consolidated']) {
            $return['has_message'] = true;
        }
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }

    public function buildConsolidatedPart($log) {
        // Not the best way in the world but trying to maintain consistency and improve the message returned, instead of
        // just removing it.
        $title = '<b>Duplication of record ' . ($log['duplicated'] ? 'succeeded' : 'failed') . '!</b><br/>';
        $log = '<div style="background:#eee;border-radius:8px;padding:12px;"><sup>'.$log['log'].'</sup></div><br/>';

        return $title.$log;
    }

    /* Terrible function to handle custom duplication logic.
     *
     * @argument string moduleName
     * @argument int selectedId
     * @argument int newRecordId
     *
     * @returns error
     */
    public function handleCustomDuplication($moduleName, $selectedId, $newRecordId) {
        global $adb;
        $error = false;

        //New record created - handle special cases after initial save
        switch ($moduleName) {
            case 'Cubesheets':
                try {
                    $wsdlURL    = getenv('CUBESHEET_SERVICE_URL');
                    $wsdlParams = ['originalRecordID' => $selectedId, 'newRecordID' => $newRecordId];
                    $soapclient = new soapclient2($wsdlURL, 'wsdl');
                    $soapclient->setDefaultRpcParams(true);
                    $soapProxy  = $soapclient->getProxy();
                    $soapResult = $soapProxy->DuplicateRecord($wsdlParams);
                    file_put_contents('logs/cubesheetDuplicate.log', date('Y-m-d H:i:s - ').print_r($soapResult, true)."\n", FILE_APPEND);
                } catch (WebServiceException $ex) {
                    $error .= $ex->getMessage().'<br/>';
                }
                break;
            case 'Tariffs':
                //We need to happily link the extra services ...
                //I need to name it more clearly just preference...
                $oldTariffId = $selectedId;
                $newTariffId = $newRecordId;
                // TariffReportSections
                $stmt = 'SELECT * FROM `vtiger_tariffreportsections` WHERE `tariff_orders_tariff` = ?';
                if ($result = $adb->pquery($stmt, [$oldTariffId])) {
                    while ($row = $result->fetchRow()) {
                        //retrieve and create a new tariff report section
                        $trsRecord = $this->doRetreive('TariffReportSections', $row['tariffreportsectionsid'], $current_user);
                        if ($trsRecord) {
                            //so we could retrieve the data so create with a new relationship.
                            $wsID = explode('x', $trsRecord['tariff_orders_tariff'])[0];
                            $trsRecord['tariff_orders_tariff'] = $wsID.'x'.$newTariffId;
                            $newRecordId = $this->doCreate('TariffReportSections', $trsRecord, $current_user);
                            if ($newRecordId) {
                                $relStmt = "INSERT INTO `vtiger_crmentityrel` VALUES (?,?,?,?)";
                                $adb->pquery($relStmt,
                                            [$newTariffId, $moduleName, $newRecordId, 'TariffReportSections']);
                            }
                        }
                    }
                }
                $tariffSections = [];
                // TariffSections
                $stmt = 'SELECT `tariffsectionsid` FROM `vtiger_tariffsections` WHERE `related_tariff` = ?';
                if ($result = $adb->pquery($stmt, [$oldTariffId])) {
                    while ($row = $result->fetchRow()) {
                        $tsRecord = $this->doRetreive('TariffSections', $row['tariffsectionsid'], $current_user);
                        if ($tsRecord) {
                            $wsID = explode('x', $tsRecord['related_tariff'])[0];
                            $tsRecord['related_tariff'] = $wsID.'x'.$newTariffId;
                            $newTSRecordId = $this->doCreate('TariffSections', $tsRecord, $current_user);
                            $tariffSections[$row['tariffsectionsid']] = $newTSRecordId;
                            if ($newTSRecordId) {
                                $relStmt = "INSERT INTO `vtiger_crmentityrel` VALUES (?,?,?,?)";
                                $adb->pquery($relStmt,
                                            [$newTariffId, $moduleName, $newTSRecordId, 'TariffSections']);
                            }
                        }
                    }
                }
                // EffectiveDates which has sub items Tariff Services
                $queryEffDates = 'SELECT `effectivedatesid` FROM `vtiger_effectivedates` WHERE `related_tariff` = ?';
                if ($effectiveDatesRes = $adb->pquery($queryEffDates, [$oldTariffId])) {
                    while ($effectiveDateRow = $effectiveDatesRes->fetchRow()) {
                        $effDateRec = $this->doRetreive('EffectiveDates', $effectiveDateRow['effectivedatesid'], $current_user);
                        if ($effDateRec) {
                            //make the new effective dates for the NEW record
                            $wsID = explode('x', $effDateRec['related_tariff'])[0];
                            $effDateRec['related_tariff'] = $wsID.'x'.$newTariffId;
                            $newEffectiveDateID = $this->doCreate('EffectiveDates', $effDateRec, $current_user);
                            if ($newEffectiveDateID) {
                                $relStmt = "INSERT INTO `vtiger_crmentityrel` VALUES (?,?,?,?)";
                                $adb->pquery($relStmt,
                                            [$newTariffId, $moduleName, $newEffectiveDateID, 'EffectiveDates']);
                            }
                            //Tariff Services which requires TariffSections
                            $selectTS = 'SELECT tariffservicesid,tariff_section FROM `vtiger_tariffservices` WHERE `effective_date` = ?';
                            if ($TSvcRes = $adb->pquery($selectTS, [$effectiveDateRow['effectivedatesid']])) {
                                while ($TSvcRow = $TSvcRes->fetchRow()) {
                                    //pull the old tariff services for the NEW record
                                    $svcRecord = $this->doRetreive('TariffServices', $TSvcRow['tariffservicesid'], $current_user);
                                    //make the new tariff services for the NEW record
                                    if ($svcRecord) {
                                        //A related tariff Section is required.
                                        if ($tariffSections[$TSvcRow['tariff_section']]) {
                                            //[tariff_section] => 42x616
                                            $wsID = explode('x', $svcRecord['tariff_section'])[0];
                                            $svcRecord['tariff_section'] = $wsID.'x'.$tariffSections[$TSvcRow['tariff_section']];
                                            //[effective_date] => 43x614
                                            $wsID = explode('x', $svcRecord['effective_date'])[0];
                                            $svcRecord['effective_date'] = $wsID.'x'.$newEffectiveDateID;
                                            //[related_tariff] => 41x613
                                            $wsID = explode('x', $svcRecord['related_tariff'])[0];
                                            $svcRecord['related_tariff'] = $wsID.'x'.$newTariffId;
                                            //TariffServices saveentity sets a no-dupe flag we need to undo it
                                            $_REQUEST['repeat'] = false;
                                            $newSvcId = $this->doCreate('TariffServices', $svcRecord, $current_user);
                                            if ($newSvcId) {
                                                $relStmt = "INSERT INTO `vtiger_crmentityrel` VALUES (?,?,?,?)";
                                                $adb->pquery($relStmt,
                                                            [$newEffectiveDateID, 'EffectiveDates', $newSvcId,
                                                             'TariffServices']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                break;
        }
        return $error;
    }

    /*
 * function doRetreive retrieves a record from a module by id as a particular user
 *
 * @params string $moduleName
 * @params int $selectedId
 * @params <userinfo> $current_user
 *
 * returns false if failed, otherwise expected result is an array of stuff
 */
    public function doRetreive($moduleName, $selectedId, $current_user, &$log)
    {
        $oldRecord = false;
        //retrieve the old record so that we can use it to make a copy
        try {
            $wsid      = vtws_getWebserviceEntityId($moduleName, $selectedId);
            $oldRecord = vtws_retrieve($wsid, $current_user);
        } catch (WebServiceException $ex) {
             $log['log'] .= 'Error retrieving record: '.$ex->getMessage().'<br/>';
        }

        return $oldRecord;
    }

    /*
     * function doCreate creates a new record based on the values in the oldRecord array.
     *
     * @params string $moduleName is what module this is to be
     * @params <array> $oldRecord is an array record
     * @params <userinfo> $current_user
     *
     * returns false if failed, otherwise expected result is an integer
     */
    public function doCreate($moduleName, $oldRecord, $current_user, &$log)
    {
        $newRecordId = false;
        //you need oldRecord to create a new record, so skip the try if no oldRecord
        if ($oldRecord) {
            //create the new record from the data of the old one.
            try {
                $newRecord   = vtws_create($moduleName, $oldRecord, $current_user);
                $newRecordId = explode('x', $newRecord['id'])[1];
            } catch (WebServiceException $ex) {
                $log['log'] .= 'Error duplicating record: '.$ex->getMessage().'<br/>';
            }
        }

        return $newRecordId;
    }

    public function printRecords(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $relatedModuleName = $request->get('relatedModule');
        if ($relatedModuleName == 'Emails') {
          $moduleName = $relatedModuleName;
        }
        /* 		$viewer = $this->getViewer($request);
                $printOutput = '';
                $selectedIds = $request->get('selected_ids');

                foreach($selectedIds as $selectedId){
                    $detailModelInstance = Vtiger_DetailView_Model::getInstance($moduleName, $selectedId);
                    //$detailInstance = $detailModelInstance::getInstance($moduleName, $selectedId);
                    $recordModel = $detailModelInstance->getRecord();
                    $detailInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, 'Detail');
                    file_put_contents('logs/devLog.log', '\n INSTANCE: '.print_r($detailInstance, true), FILE_APPEND);
                    //$detailView = $detailInstance::showModuleDetailView($request);
                }

                echo "<br><h1>Hello</h1><br>"; */
        //global $hiddenBlocksArray;
        $cvId                  = $request->get('viewname');
        $selectedIds           = $request->get('selected_ids');
        $excludedIds           = $request->get('excluded_ids');
        $recordModels          = [];
        $recordStructureModels = [];
        foreach ($selectedIds as $selectedId) {
            $recordModel               = Vtiger_DetailView_Model::getInstance($moduleName, $selectedId)->getRecord();
            $recordModels[$selectedId] = $recordModel;
            // file_put_contents('logs/devLog.log', "\n $selectedId STRUCTURED VALUES: ".print_r($structuredValues, true), FILE_APPEND);
        }

        $viewer      = $this->getViewer($request);
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        //$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $fieldInfo = [];
        $fieldList = $moduleModel->getFields();
        foreach ($fieldList as $fieldName => $fieldModel) {
            $fieldInfo[$fieldName] = $fieldModel->getFieldInfo();
        }
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('MODE', 'massedit');
        $viewer->assign('MODULE', $moduleName);
        if($relatedModuleName == 'Emails') {
          $viewer->assign('MODULE', $relatedModuleName);
        }
        $viewer->assign('CVID', $cvId);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        //$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORDS', $recordModels);
        $viewer->assign('RECORD_STRUCTURES', $recordStructureModels);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('MASS_EDIT_FIELD_DETAILS', $fieldInfo);
        //$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $searchKey   = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator    = $request->get('operator');
        if (!empty($operator)) {
            $viewer->assign('OPERATOR', $operator);
            $viewer->assign('ALPHABET_VALUE', $searchValue);
            $viewer->assign('SEARCH_KEY', $searchKey);
        }
        $searchParams = $request->get('search_params');
        if (!empty($searchParams)) {
            $viewer->assign('SEARCH_PARAMS', $searchParams);
        }
        echo $viewer->view('PrintRecords.tpl', $moduleName, true);
    }

    /**
     * Function returns the mass edit form
     *
     * @param Vtiger_Request $request
     */
    public function showMassEditForm(Vtiger_Request $request)
    {
        $moduleName              = $request->getModule();
        $cvId                    = $request->get('viewname');
        $selectedIds             = $request->get('selected_ids');
        $excludedIds             = $request->get('excluded_ids');
        $viewer                  = $this->getViewer($request);
        $moduleModel             = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_MASSEDIT);
        $fieldInfo               = [];
        $fieldList               = $moduleModel->getFields();
        foreach ($fieldList as $fieldName => $fieldModel) {
            $fieldInfo[$fieldName] = $fieldModel->getFieldInfo();
        }
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        if(getenv('INSTANCE_NAME') == 'sirva') {
            $viewer->assign('IS_MASS_EDIT_FORM', true);
        }
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('MODE', 'massedit');
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CVID', $cvId);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('MASS_EDIT_FIELD_DETAILS', $fieldInfo);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $searchKey   = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator    = $request->get('operator');
        if (!empty($operator)) {
            $viewer->assign('OPERATOR', $operator);
            $viewer->assign('ALPHABET_VALUE', $searchValue);
            $viewer->assign('SEARCH_KEY', $searchKey);
        }
        $searchParams = $request->get('search_params');
        if (!empty($searchParams)) {
            $viewer->assign('SEARCH_PARAMS', $searchParams);
        }
        echo $viewer->view('MassEditForm.tpl', $moduleName, true);
    }

    /**
     * Function returns the Add Comment form
     *
     * @param Vtiger_Request $request
     */
    public function showAddCommentForm(Vtiger_Request $request)
    {
        $sourceModule = $request->getModule();
        $moduleName   = 'ModComments';
        $cvId         = $request->get('viewname');
        $selectedIds  = $request->get('selected_ids');
        $excludedIds  = $request->get('excluded_ids');
        $viewer       = $this->getViewer($request);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CVID', $cvId);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $searchKey   = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator    = $request->get('operator');
        if (!empty($operator)) {
            $viewer->assign('OPERATOR', $operator);
            $viewer->assign('ALPHABET_VALUE', $searchValue);
            $viewer->assign('SEARCH_KEY', $searchKey);
        }
        $searchParams = $request->get('search_params');
        if (!empty($searchParams)) {
            $viewer->assign('SEARCH_PARAMS', $searchParams);
        }
        echo $viewer->view('AddCommentForm.tpl', $moduleName, true);
    }

    /**
     * Function returns the Compose Email form
     *
     * @param Vtiger_Request $request
     */
    public function showComposeEmailForm(Vtiger_Request $request)
    {
        $moduleName              = 'Emails';
        $sourceModule            = $request->getModule();
        $cvId                    = $request->get('viewname');
        $selectedIds             = $request->get('selected_ids');
        $excludedIds             = $request->get('excluded_ids');
        $step                    = $request->get('step');
        $selectedFields          = $request->get('selectedFields');
        $relatedLoad             = $request->get('relatedLoad');
        $moduleModel             = Vtiger_Module_Model::getInstance($sourceModule);
        $emailFields             = $moduleModel->getFieldsByType('email');
        $accesibleEmailFields    = [];
        $emailColumnNames        = [];
        $emailColumnModelMapping = [];
        foreach ($emailFields as $index => $emailField) {
            $fieldName = $emailField->getName();
            if ($emailField->isViewable()) {
                $accesibleEmailFields[]                              = $emailField;
                $emailColumnNames[]                                  = $emailField->get('column');
                $emailColumnModelMapping[$emailField->get('column')] = $emailField;
            }
        }
        $emailFields     = $accesibleEmailFields;
        $emailFieldCount = count($emailFields);
        $tableJoined     = [];
        if ($emailFieldCount > 1) {
            $recordIds           = $this->getRecordsListFromRequest($request);
            $moduleMeta          = $moduleModel->getModuleMeta();
            $wsModuleMeta        = $moduleMeta->getMeta();
            $tabNameIndexList    = $wsModuleMeta->getEntityTableIndexList();
            $queryWithFromClause = 'SELECT '.implode(',', $emailColumnNames).' FROM vtiger_crmentity ';
            foreach ($emailFields as $emailFieldModel) {
                $fieldTableName = $emailFieldModel->table;
                if (in_array($fieldTableName, $tableJoined)) {
                    continue;
                }
                $tableJoined[] = $fieldTableName;
                $queryWithFromClause .= ' INNER JOIN '.$fieldTableName.
                                        ' ON '.$fieldTableName.'.'.$tabNameIndexList[$fieldTableName].'= vtiger_crmentity.crmid';
            }
            $query = $queryWithFromClause.' WHERE vtiger_crmentity.deleted = 0 AND crmid IN ('.generateQuestionMarks($recordIds).') AND (';
            for ($i = 0; $i < $emailFieldCount; $i++) {
                for ($j = ($i + 1); $j < $emailFieldCount; $j++) {
                    $query .= ' ('.$emailFields[$i]->getName().' != \'\' and '.$emailFields[$j]->getName().' != \'\')';
                    if (!($i == ($emailFieldCount - 2) && $j == ($emailFieldCount - 1))) {
                        $query .= ' or ';
                    }
                }
            }
            $query .= ') LIMIT 1';
            $db       = PearDatabase::getInstance();
            $result   = $db->pquery($query, $recordIds);
            $num_rows = $db->num_rows($result);
            if ($num_rows == 0) {
                $query = $queryWithFromClause.' WHERE vtiger_crmentity.deleted = 0 AND crmid IN ('.generateQuestionMarks($recordIds).') AND (';
                foreach ($emailColumnNames as $index => $columnName) {
                    $query .= " $columnName != ''";
                    //add glue or untill unless it is the last email field
                    if ($index != ($emailFieldCount - 1)) {
                        $query .= ' or ';
                    }
                }
                $query .= ') LIMIT 1';
                $result = $db->pquery($query, $recordIds);
                if ($db->num_rows($result) > 0) {
                    //Expecting there will atleast one row
                    $row = $db->query_result_rowdata($result, 0);
                    foreach ($emailColumnNames as $emailColumnName) {
                        if (!empty($row[$emailColumnName])) {
                            //To send only the single email field since it is only field which has value
                            $emailFields = [$emailColumnModelMapping[$emailColumnName]];
                            break;
                        }
                    }
                } else {
                    //No Record which has email field value
                    foreach ($emailColumnNames as $emailColumnName) {
                        //To send only the single email field since it has no email value
                        $emailFields = [$emailColumnModelMapping[$emailColumnName]];
                        break;
                    }
                }
            }
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('VIEWNAME', $cvId);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        $viewer->assign('EMAIL_FIELDS', $emailFields);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $searchKey   = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator    = $request->get('operator');
        if (!empty($operator)) {
            $viewer->assign('OPERATOR', $operator);
            $viewer->assign('ALPHABET_VALUE', $searchValue);
            $viewer->assign('SEARCH_KEY', $searchKey);
        }
        $searchParams = $request->get('search_params');
        if (!empty($searchParams)) {
            $viewer->assign('SEARCH_PARAMS', $searchParams);
        }
        $to = $request->get('to');
        if (!$to) {
            $to = [];
        }
        $viewer->assign('TO', $to);
        $parentModule = $request->get('sourceModule');
        $parentRecord = $request->get('sourceRecord');
        if (!empty($parentModule)) {
            $viewer->assign('PARENT_MODULE', $parentModule);
            $viewer->assign('PARENT_RECORD', $parentRecord);
            $viewer->assign('RELATED_MODULE', $sourceModule);
        }
        if ($relatedLoad) {
            $viewer->assign('RELATED_LOAD', true);
        }
        if ($step == 'step1') {
            echo $viewer->view('SelectEmailFields.tpl', $moduleName, true);
            exit;
        }
    }

    /**
     * Function shows form that will lets you send SMS
     *
     * @param Vtiger_Request $request
     */
    public function showSendSMSForm(Vtiger_Request $request)
    {
        $sourceModule = $request->getModule();
        $moduleName   = 'SMSNotifier';
        $selectedIds  = $this->getRecordsListFromRequest($request);
        $excludedIds  = $request->get('excluded_ids');
        $cvId         = $request->get('viewname');
        $user         = Users_Record_Model::getCurrentUserModel();
        $moduleModel  = Vtiger_Module_Model::getInstance($sourceModule);
        $phoneFields  = $moduleModel->getFieldsByType('phone');
        $viewer       = $this->getViewer($request);
        if (count($selectedIds) == 1) {
            $recordId            = $selectedIds[0];
            $selectedRecordModel = Vtiger_Record_Model::getInstanceById($recordId, $sourceModule);
            $viewer->assign('SINGLE_RECORD', $selectedRecordModel);
        }
        $viewer->assign('VIEWNAME', $cvId);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        $viewer->assign('USER_MODEL', $user);
        $viewer->assign('PHONE_FIELDS', $phoneFields);
        $searchKey   = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator    = $request->get('operator');
        if (!empty($operator)) {
            $viewer->assign('OPERATOR', $operator);
            $viewer->assign('ALPHABET_VALUE', $searchValue);
            $viewer->assign('SEARCH_KEY', $searchKey);
        }
        $searchParams = $request->get('search_params');
        if (!empty($searchParams)) {
            $viewer->assign('SEARCH_PARAMS', $searchParams);
        }
        echo $viewer->view('SendSMSForm.tpl', $moduleName, true);
    }

    /**
     * Function returns the record Ids selected in the current filter
     *
     * @param Vtiger_Request $request
     *
     * @return integer
     */
    public function getRecordsListFromRequest(Vtiger_Request $request, $module = false)
    {
        $cvId        = $request->get('viewname');
        $selectedIds = $request->get('selected_ids');
        $excludedIds = $request->get('excluded_ids');
        if (empty($module)) {
            $module = $request->getModule();
        }
        if (!empty($selectedIds) && $selectedIds != 'all') {
            if (!empty($selectedIds) && count($selectedIds) > 0) {
                return $selectedIds;
            }
        }
        $sourceRecord = $request->get('sourceRecord');
        $sourceModule = $request->get('sourceModule');
        if ($sourceRecord && $sourceModule) {
            $sourceRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);

            return $sourceRecordModel->getSelectedIdsList($module, $excludedIds);
        }
        $customViewModel = CustomView_Record_Model::getInstanceById($cvId);
        if ($customViewModel) {
            $searchKey   = $request->get('search_key');
            $searchValue = $request->get('search_value');
            $operator    = $request->get('operator');
            if (!empty($operator)) {
                $customViewModel->set('operator', $operator);
                $customViewModel->set('search_key', $searchKey);
                $customViewModel->set('search_value', $searchValue);
            }
            $customViewModel->set('search_params', $request->get('search_params'));

            return $customViewModel->getRecordIds($excludedIds, $module);
        }
    }

    /**
     * Function shows the List of Mail Merge Templates
     *
     * @param Vtiger_Request $request
     */
    public function showMailMergeTemplates(Vtiger_Request $request)
    {
        $selectedIds = $request->get('selected_ids');
        $excludedIds = $request->get('excluded_ids');
        $cvId        = $request->get('viewname');
        $module      = $request->getModule();
        $templates   = Settings_MailMerge_Record_Model::getByModule($module);
        $viewer      = $this->getViewer($request);
        $viewer->assign('TEMPLATES', $templates);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        $viewer->assign('VIEWNAME', $cvId);
        $viewer->assign('MODULE', $module);

        return $viewer->view('showMergeTemplates.tpl', $module);
    }

    /**
     * Function shows the duplicate search form
     *
     * @param Vtiger_Request $request
     */
    public function showDuplicatesSearchForm(Vtiger_Request $request)
    {
        $module      = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($module);
        $fields      = $moduleModel->getFields();
        $viewer      = $this->getViewer($request);
        $viewer->assign('MODULE', $module);
        $viewer->assign('FIELDS', $fields);
        $viewer->view('showDuplicateSearch.tpl', $module);
    }

    public function transferOwnership(Vtiger_Request $request)
    {
        $module         = $request->getModule();
        $moduleModel    = Vtiger_Module_Model::getInstance($module);
        $relatedModules = $moduleModel->getRelations();
        //User doesn't have the permission to edit related module,
        //then don't show that module in related module list.
        foreach ($relatedModules as $key => $relModule) {
            if (!Users_Privileges_Model::isPermitted($relModule->get('relatedModuleName'), 'EditView')) {
                unset($relatedModules[$key]);
            }
        }
        $viewer      = $this->getViewer($request);
        $skipModules = ['Emails'];
        $viewer->assign('MODULE', $module);
        $viewer->assign('RELATED_MODULES', $relatedModules);
        $viewer->assign('SKIP_MODULES', $skipModules);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('TransferRecordOwnership.tpl', $module);
    }
}
