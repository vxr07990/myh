<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Orders_Detail_View extends Vtiger_Detail_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showRelatedRecords');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);

            return;
        }
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if ($currentUserModel->get('default_record_view') === 'Summary') {
            echo $this->showModuleBasicView($request);
        } else {
            echo $this->showModuleDetailView($request);
        }
    }

    public function showRelatedList(Vtiger_Request $request)
    {
        $record            = $request->get('record');
        $moduleName        = $request->getModule();
        $relatedModuleName = $request->get('relatedModule');

        $targetControllerClass = null;
        // Added to support related list view from the related module, rather than the base module.
        try {
            $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'In'.$moduleName.'Relation', $relatedModuleName);
        } catch (AppException $e) {
            try {
                // If any module wants to have same view for all the relation, then invoke this.
                $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'InRelation', $relatedModuleName);
            } catch (AppException $e) {
                // Default related list
                $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'RelatedList', $moduleName);
            }
        }
        if ($targetControllerClass) {
            $targetController = new $targetControllerClass();
            //file_put_contents('logs/devLog.log', "\n T-CONTROLLER: ".$targetControllerClass, FILE_APPEND);
        return $targetController->process($request);
        }
    }

    public function getExtraPermissions($request)
    {
        /*
        $db                    = PearDatabase::getInstance();
        $userModel             = Users_Record_Model::getCurrentUserModel();
        $currentUserId         = $userModel->getId();
        $isAdmin               = $userModel->isAdminUser();
        $recordId              = $request->get('record');
        $creatorPermissions    = false;
        $memberOfParentVanline = false;
        $sql                   = "SELECT vanlineid, is_parent FROM `vtiger_users2vanline` JOIN `vtiger_vanlinemanager` ON vanlineid=vanlinemanagerid WHERE userid=?";
        $result                = $db->pquery($sql, [$currentUserId]);
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
            //old securities
            /*$sql = "SELECT participating_agents_full FROM `vtiger_orders` WHERE ordersid=?";
            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();
            $participatingAgentsFull = $row[0];
            $participatingAgentsFull = explode(' |##| ', $participatingAgentsFull);
            $sql = "SELECT participating_agents_no_rates FROM `vtiger_orders` WHERE ordersid=?";
            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();
            $participatingAgentsNoRates = $row[0];
            $participatingAgentsNoRates = explode(' |##| ', $participatingAgentsNoRates);
            foreach($participatingAgentsFull as $participatingAgent){
                foreach($userGroups as $group){
                    if($group == $participatingAgent){
                        $participatingAgentPermissions = 'full';
                    }
                }
            }
            foreach($participatingAgentsNoRates as $participatingAgent){
                foreach($userGroups as $group){
                    if($group == $participatingAgent && $participatingAgentPermissions != 'full'){
                        $participatingAgentPermissions = 'no_rates';
                    }
                }
            }
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
        $userRole = $userModel->getRole();
        $sql      = "SELECT rolename FROM `vtiger_role` WHERE roleid=?";
        $result   = $db->pquery($sql, [$userRole]);
        $row      = $result->fetchRow();
        $roleName = $row[0];
        if (strpos($roleName, 'Sales Person') !== false) {
            $creatorPermissions            = false;
            $participatingAgentPermissions = 'none';
            $sql                           = "SELECT sales_person FROM `vtiger_orders` WHERE ordersid=?";
            $result                        = $db->pquery($sql, [$recordId]);
            $row                           = $result->fetchRow();
            $salesPerson                   = $row[0];
            if ($salesPerson == $currentUserId) {
                $creatorPermissions = true;
            }
            //file_put_contents('logs/devLog.log', "\n IN OPP IF STATEMENT", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n SALES PERSON: $salesPerson", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n CURRENT USER: $currentUserId", FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n RECORDID: $recordId", FILE_APPEND);
        }

        //file_put_contents('logs/devLog.log', "\n Creator Perms: $creatorPermissions & Agent Perms: $participatingAgentPermissions", FILE_APPEND);
        return [$creatorPermissions, $participatingAgentPermissions];
        */
    }

    public function preProcessDisplay(Vtiger_Request $request)
    {
        $viewer           = $this->getViewer($request);
        //$extraPermissions = $this::getExtraPermissions($request);
        //$viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        //$viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        $displayed = $viewer->view($this->preProcessTplName($request), $request->getModule());
    }

    /**
     * Function shows the entire detail for the record
     *
     * @param Vtiger_Request $request
     *
     * @return <type>
     */
    public function showModuleDetailView(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $recordId         = $request->get('record');
        $moduleName       = $request->getModule();
        //$extraPermissions = $this::getExtraPermissions($request);
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel    = $this->record->getRecord();
        $addrId = $recordModel->get('bill_addrdesc');
        if ($addrId) {
            $db               = PearDatabase::getInstance();
            $sql              = 'SELECT address_desc FROM `vtiger_accounts_billing_addresses` WHERE id = ?';
            $res              = $db->pquery($sql, [$addrId]);
            while ($row = $res->fetchrow()) {
                $recordModel->set('bill_addrdesc', $row['address_desc']);
            }
        }
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
            $recordModel->hiddenBlocks = $hiddenBlocks;
        }
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer           = $this->getViewer($request);

        $tariffID = $recordModel->get('tariff_id');
        $tariffInfo = Estimates_Record_Model::getTariffInfo($tariffID);
        $viewer->assign('EFFECTIVE_TARIFF_CUSTOMTYPE', $tariffInfo['custom_type']);

        //$result           = $db->pquery($sql, [$recordId]);
        ////file_put_contents('logs/devLog.log', "RESULT: ".$result, FILE_APPEND);
        //$row = $result->fetchRow();
        //while ($row != NULL) {
        //    $sql2              = 'SELECT agentname FROM `vtiger_agents` WHERE agentsid = ?';
        //    $result2           = $db->pquery($sql2, [$row[1]]);
        //    $row2              = $result2->fetchRow();
        //    $row['agentName']  = $row2[0];
        //    $agentsLink        = '<a href="index.php?module=Agents&amp;view=Detail&amp;record='.$row[1].'" data-original-title="Agents">'.$row2[0].'</a>';
        //    $row['agentsLink'] = $agentsLink;
        //    $participantRows[] = $row;
        //    $row               = $result->fetchRow();
        //}
       // $viewer->assign('PARTICIPANT_ROWS', []);//$viewer->assign('PARTICIPANT_ROWS', $participantRows);
        $viewer->assign('RECORD', $recordModel);
		
		$billing_type_flag = ($recordModel->get('billing_type') == "GSA") ? true : false;
		
		$viewer->assign('BILLING_TYPE_FLAG',$billing_type_flag);
		
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        /*
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */
        if ($moduleName == 'Users') {
            $viewer->assign('IS_OI_ENABLED', $this->record->getRecord()->getOIEnabled());
        }
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
//            $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($recordId));
        }
        //logic to include the participating agents block
        $participatingAgentsModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        if ($participatingAgentsModel && $participatingAgentsModel->isActive()) {
            $viewer->assign('PARTICIPATING_AGENTS', true);
            $viewer->assign('PARTICIPANT_LIST', $participatingAgentsModel::getParticipants($recordId));
            $requestsModel = Vtiger_Module_Model::getInstance('OASurveyRequests');
            if ($requestsModel && $requestsModel->isActive()) {
                $viewer->assign('USE_STATUS', true);
            }
        }
        //stops block
        $extraStopsModel = Vtiger_Module_Model::getInstance('ExtraStops');
        if ($extraStopsModel && $extraStopsModel->isActive()) {
            $extraStopsModel->setViewerForStops($viewer, $recordId);
        }
        $this->setViewerForGuestBlocks($moduleName, $recordId, $viewer);
        /*$stopsRows = [];
        $db        = PearDatabase::getInstance();
        $sql       = 'SELECT * FROM `vtiger_extrastops` WHERE extrastops_relcrmid = ?';
        $result    = $db->pquery($sql, [$recordId]);
        $row       = $result->fetchRow();
        while ($row != NULL) {
            $sql2                     = 'SELECT firstname, lastname FROM `vtiger_contactdetails` WHERE contactid = ?';
            $result2                  = $db->pquery($sql2, [$row['stop_contact']]);
            $row2                     = $result2->fetchRow();
            $contactName              = $row2[0].' '.$row2[1];
            $contactRecord            = $row['stop_contact'];
            $contactLink              = '<a href="index.php?module=Contacts&amp;view=Detail&amp;record='.$contactRecord.'" data-original-title="Contacts">'.$contactName.'</a>';
            $row['stop_contact_link'] = $contactLink;
            $stopsRows[]              = $row;
            $row                      = $result->fetchRow();
        }
        $viewer->assign('EXTRASTOPS_LIST', $stopsRows);*/
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */

        $cancellationLog = $this->getCancellationLog($recordId);
        $viewer->assign('CANCELATION_LOG_ARRAY', $cancellationLog);

        //logic to include Addresss List
        $addressListModule = Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->assignValueForAddressList($viewer,$recordId);
        }
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    public function getCancellationLog($recordId)
    {
        $db = PearDatabase::getInstance();
        $cancellationLog = [];

        $result = $db->pquery("SELECT * FROM vtiger_orders_cancelation_log WHERE ordersid = ?", array($recordId));
        while ($arr = $db->fetch_array($result)) {
            $user_record = Vtiger_Record_Model::getInstanceById($arr["user"], 'Users');
            $userName = $user_record->get('first_name') . " " . $user_record->get('last_name');
            $cancellationLog[] = array("id" => $arr["id"], "action" => $arr["action"], "reason" => $arr["reason"], "user" => $userName, "datetime" => $arr["datetime"]);
        }
        return $cancellationLog;
    }

    public function showModuleSummaryView($request)
    {
        $moduleName       = $request->getModule();
        $recordId         = $request->get('record');
        $recordModel      = Vtiger_Record_Model::getInstanceById($recordId);
        $recordStrucure   = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
        //$extraPermissions = $this::getExtraPermissions($request);
        $viewer           = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('SUMMARY_INFORMATION', $recordModel->getSummaryInfo());
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ENABLE_CALENDAR', true);
        /*
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $sharedUsers = Calendar_Module_Model::getSharedUsersOfCurrentUser($currentUser->id);
        $sharedUsersInfo = Calendar_Module_Model::getSharedUsersInfoOfCurrentUser($currentUser->id);
        $viewer->assign('SHAREDUSERS', $sharedUsers);
        $viewer->assign('SHAREDUSERS_INFO', $sharedUsersInfo);
        return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
    }

    /**
     * Function returns related records based on related moduleName
     *
     * @param Vtiger_Request $request
     *
     * @return <type>
     */
    public function showRelatedRecords(Vtiger_Request $request)
    {
        $parentId          = $request->get('record');
        $pageNumber        = $request->get('page');
        $limit             = $request->get('limit');
        $relatedModuleName = $request->get('relatedModule');
        $orderBy           = $request->get('orderby');
        $sortOrder         = $request->get('sortorder');
        $whereCondition    = $request->get('whereCondition');
        $moduleName        = $request->getModule();
        if ($sortOrder == "ASC") {
            $nextSortOrder = "DESC";
            $sortImage     = "icon-chevron-down";
        } else {
            $nextSortOrder = "ASC";
            $sortImage     = "icon-chevron-up";
        }
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
        $relationListView  = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName);
        if (!empty($orderBy)) {
            $relationListView->set('orderby', $orderBy);
            $relationListView->set('sortorder', $sortOrder);
        }
        if (empty($pageNumber)) {
            $pageNumber = 1;
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if (!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
        if ($whereCondition) {
            $relationListView->set('whereCondition', $whereCondition);
        }
        $models           = $relationListView->getEntries($pagingModel);
        //OT4219
        //They require more information than is available on  the relation list model.
        if($relatedModuleName == 'OrdersTask'){
            foreach ($models as $key => $listRecord) {
                $models[$key] = Vtiger_Record_Model::getInstanceById($key, 'OrdersTask');
            }
        }
        $header           = $relationListView->getHeaders();
        //$extraPermissions = $this::getExtraPermissions($request);
        $viewer           = $this->getViewer($request);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RELATED_RECORDS', $models);
        $viewer->assign('RELATED_HEADERS', $header);
        $viewer->assign('RELATED_MODULE', $relatedModuleName);
        $viewer->assign('RELATED_MODULE_MODEL', Vtiger_Module_Model::getInstance($relatedModuleName));
        $viewer->assign('PAGING_MODEL', $pagingModel);

        /*
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */

        return $viewer->view('SummaryWidgets.tpl', $moduleName, 'true');
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $vehicleLookupModel    = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $jsFileNames = [
                "modules.VehicleLookup.resources.Detail",
                "modules.ParticipatingAgents.resources.Detail",
            ];
        } else {
            $jsFileNames = [];
        }
        array_push($jsFileNames, "~/libraries/fullcalendar/fullcalendar.js");
        array_push($jsFileNames, "~/libraries/jquery/colorpicker/js/colorpicker.js");
        array_push($jsFileNames, "modules.Orders.resources.OrdersCalendarView");

        $jsFileNames[] = "modules.Valuation.resources.Common";

        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        if(getenv('INSTANCE_NAME') == 'graebel') {
            //@TODO: I think this should be in: modules/Documents/views/InOrdersRelation.php but that getHeaderScripts doesn't get called.
            //For now this works, but will have to revisit
            if (
                $request->get('mode') == 'showRelatedList' &&
                $request->get('relatedModule') == 'Documents'
            ) {
                $jsFileNames           = [
                    'modules.Documents.resources.GVL',
                ];
                $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
                $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
            }
        }

        return $headerScriptInstances;
    }

    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames       = [
            '~/libraries/fullcalendar/fullcalendar.css',
            '~/libraries/fullcalendar/fullcalendar-bootstrap.css',
            '~/libraries/jquery/colorpicker/css/colorpicker.css',
        ];
        $cssInstances       = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);

        return $headerCssInstances;
    }
	
    public function showModuleBasicView($request)
    {
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel          = $this->record->getRecord();
        $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
        $detailViewLinks      = $this->record->getDetailViewLinks($detailViewLinkParams);
        $viewer               = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('MODULE_NAME', $moduleName);
        $recordStrucure   = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }
}
