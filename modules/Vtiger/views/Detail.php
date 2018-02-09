<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_Detail_View extends Vtiger_Index_View
{
    protected $record = false;

    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showDetailViewByMode');
        $this->exposeMethod('showModuleDetailView');
        $this->exposeMethod('showModuleSummaryView');
        $this->exposeMethod('showModuleBasicView');
        $this->exposeMethod('showRecentActivities');
        $this->exposeMethod('showRecentComments');
        $this->exposeMethod('showRelatedList');
        $this->exposeMethod('showChildComments');
        $this->exposeMethod('showAllComments');
        $this->exposeMethod('getActivities');
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName       = $request->getModule();
        $recordId         = $request->get('record');
        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
        if (!$recordPermission) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }

        return true;
    }

    public function preProcess(Vtiger_Request $request, $display = true)
    {
        parent::preProcess($request, false);
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel    = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $summaryInfo    = [];
        // Take first block information as summary information
        $stucturedValues = $recordStrucure->getStructure();
        foreach ($stucturedValues as $blockLabel => $fieldList) {
            $summaryInfo[$blockLabel] = $fieldList;
            break;
        }
        $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
        $detailViewLinks      = $this->record->getDetailViewLinks($detailViewLinkParams);
        $navigationInfo       = ListViewSession::getListViewNavigation($recordId);
        $viewer               = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('NAVIGATION', $navigationInfo);
        $this->setViewerForGuestBlocks($moduleName, $recordId, $viewer);
        //Intially make the prev and next records as null
        $prevRecordId = null;
        $nextRecordId = null;
        $found        = false;
        if ($navigationInfo) {
            foreach ($navigationInfo as $page => $pageInfo) {
                foreach ($pageInfo as $index => $record) {
                    //If record found then next record in the interation
                    //will be next record
                    if ($found) {
                        $nextRecordId = $record;
                        break;
                    }
                    if ($record == $recordId) {
                        $found = true;
                    }
                    //If record not found then we are assigning previousRecordId
                    //assuming next record will get matched
                    if (!$found) {
                        $prevRecordId = $record;
                    }
                }
                //if record is found and next record is not calculated we need to perform iteration
                if ($found && !empty($nextRecordId)) {
                    break;
                }
            }
        }
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if (!empty($prevRecordId)) {
            $viewer->assign('PREVIOUS_RECORD_URL', $moduleModel->getDetailViewUrl($prevRecordId));
        }
        if (!empty($nextRecordId)) {
            $viewer->assign('NEXT_RECORD_URL', $moduleModel->getDetailViewUrl($nextRecordId));
        }
        $viewer->assign('MODULE_MODEL', $this->record->getModule());
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('IS_EDITABLE', $this->record->getRecord()->isEditable($moduleName));
        $viewer->assign('IS_DELETABLE', $this->record->getRecord()->isDeletable($moduleName));
        $linkParams = ['MODULE' => $moduleName, 'ACTION' => $request->get('view')];
        $linkModels = $this->record->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $linkModels);
        $viewer->assign('MODULE_NAME', $moduleName);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('USER_MODEL', $currentUserModel);
        $viewer->assign('DEFAULT_RECORD_VIEW', $currentUserModel->get('default_record_view'));
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $viewer->assign('PICKLIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        if ($display) {
            $this->preProcessDisplay($request);
        }
        /*$moduleName = $request->getModule();
        $record = $request->get('record');
        $viewPermissions = $this::getDetailReadPermissions($request);

        if($viewPermissions != true && $moduleName != 'Opportunities'  && $moduleName != 'Orders'){
            if($record != null){
                throw new WebServiceException(WebServiceErrorCode::$ACCESSDENIED, "Permission to read is denied");
            }
        }
        parent::preProcess($request, false);

        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $summaryInfo = array();
        // Take first block information as summary information
        $stucturedValues = $recordStrucure->getStructure();
        foreach($stucturedValues as $blockLabel=>$fieldList) {
            $summaryInfo[$blockLabel] = $fieldList;
            break;
        }

        $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);

        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
        $navigationInfo = ListViewSession::getListViewNavigation($recordId);

        $navigationMerged = array();
        $ownedRecords = array();

        for($i=1; $i<=count($navigationInfo); $i++){
            $infoPiece = $navigationInfo[$i];
            $navigationMerged = array_merge($navigationMerged, $infoPiece);
        }

        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('NAVIGATION', $navigationInfo);

        $extraPermissions = $this::getExtraPermissions($request);

        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);

        $db = PearDatabase::getInstance();

        $userModel = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();

        $isAdmin = $userModel->isAdminUser();

        //Intially make the prev and next records as null
        $prevRecordId = null;
        $nextRecordId = null;
        if($isAdmin){
            $found = false;
            if ($navigationInfo) {
                foreach($navigationInfo as $page=>$pageInfo) {
                    foreach($pageInfo as $index=>$record) {
                        //If record found then next record in the interation
                        //will be next record
                        if($found) {
                            $nextRecordId = $record;
                            break;
                        }
                        if($record == $recordId) {
                            $found = true;
                        }
                        //If record not found then we are assiging previousRecordId
                        //assuming next record will get matched
                        if(!$found) {
                            $prevRecordId = $record;
                        }
                    }
                    //if record is found and next record is not calculated we need to perform iteration
                    if($found && !empty($nextRecordId)) {
                        break;
                    }
                }
            }
        } elseif(!$isAdmin && $moduleName != 'Cubesheets' && $moduleName != 'Calendar' && $moduleName != 'Events' && $moduleName != 'VanlineManager' && $moduleName != 'AgentManager' && $moduleName != 'Estimates'){

            $userEntries = array();

            $userGroups = array();
            $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();

            while($row != NULL){
                $userGroups[] = $row[0];
                $row = $result->fetchRow();
            }

            $userGroupNames = array();

            foreach($userGroups as $group){
                $sql = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                $userGroupNames[] = $row[0];
            }

            $groupOwned = array();
            foreach($userGroups as $group){
                $sql = "SELECT crmid FROM `vtiger_crmentity` WHERE smownerid=?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                while($row != NULL){
                    $groupOwned[] = $row[0];
                    $row = $result->fetchRow();
                }
            }
            foreach($navigationMerged as $recordModel){
                //add entries owned by current users agent group to list
                foreach($groupOwned as $ownedEntity){
                    if($ownedEntity == $recordModel  && !in_array($recordModel, $userEntries)){
                        $userEntries[] = $recordModel;
                    }
                }
                //include entries where users agent group is a participating agent
                $participatingAgents = array();
                $participatingAgentNames = array();
                /*$sql = "SELECT agentid FROM `vtiger_potential_participatingagents` WHERE opportunityid=? AND permissions!=3";
                $result = $db->pquery($sql, array($potentialId));
                $row = $result->fetchRow();
                while($row != null){
                    $participatingAgents[] = $row[0];
                    $row = $result->fetchRow();
                }
                /*$sql = "SELECT participating_agents_full FROM `vtiger_potential` WHERE potentialid=?";
                $result = $db->pquery($sql, array($recordModel));
                $row = $result->fetchRow();
                $participatingAgentsFull = $row[0];
                $participatingAgentsFull = explode(' |##| ', $participatingAgentsFull);
                $sql = "SELECT participating_agents_no_rates FROM `vtiger_potential` WHERE potentialid=?";
                $result = $db->pquery($sql, array($recordModel));
                $row = $result->fetchRow();
                $participatingAgentsNoRates = $row[0];
                $participatingAgentsNoRates = explode(' |##| ', $participatingAgentsNoRates);
                $participatingAgents = array_merge($participatingAgentsFull, $participatingAgentsNoRates);

                foreach($participatingAgents as $participatingAgent){
                    $sql = "SELECT agentname FROM `vtiger_agents` WHERE agentsid=?";
                    $result = $db->pquery($sql, array($participatingAgent));
                    $row = $result->fetchRow();
                    $participatingAgentNames[] = $row[0];
                }

                foreach($participatingAgentNames as $participatingAgentName){
                    foreach($userGroupNames as $groupName){
                        if($groupName == $participatingAgentName && !in_array($recordModel, $userEntries)){
                            $userEntries[] = $recordModel;
                        }
                    }
                }
            }
            foreach($userEntries as $key => $currentEntry){
                if($currentEntry == $recordId){
                    if(array_key_exists($key-1, $userEntries)){$prevRecordId = $userEntries[$key-1];}
                    if(array_key_exists($key+1, $userEntries)){$nextRecordId = $userEntries[$key+1];}
                }
            }
        } elseif(!$isAdmin && $moduleName == 'Estimates'){
            $userEntries = array();

            $userGroups = array();
            $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();

            while($row != NULL){
                $userGroups[] = $row[0];
                $row = $result->fetchRow();
            }

            $userGroupNames = array();

            foreach($userGroups as $group){
                $sql = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                $userGroupNames[] = $row[0];
            }

            $groupOwned = array();
            foreach($userGroups as $group){
                $sql = "SELECT crmid FROM `vtiger_crmentity` WHERE smownerid=?";
                $result = $db->pquery($sql, array($group));
                $row = $result->fetchRow();
                while($row != NULL){
                    $groupOwned[] = $row[0];
                    $row = $result->fetchRow();
                }
            }
            foreach($navigationMerged as $recordModel){
                //add entries owned by current users agent group to list
                foreach($groupOwned as $ownedEntity){
                    if($ownedEntity == $recordModel  && !in_array($recordModel, $userEntries)){
                        $userEntries[] = $recordModel;
                    }
                }
                //include entries where users agent group is a participating agent
                $sql = "SELECT potentialid, orders_id FROM `vtiger_quotes` WHERE quoteid = ?";
                $result = $db->pquery($sql, array($recordModel));
                $row = $result->fetchRow();
                $potentialId = $row[0];
                $orderId = $row[1];

                //include entries where users agent group is a participating agent
                $participatingAgents = array();
                $participatingAgentNames = array();
                /*$sql = "SELECT agentid FROM `vtiger_potential_participatingagents` WHERE opportunityid=? AND permissions!=3";
                $result = $db->pquery($sql, array($potentialId));
                $row = $result->fetchRow();
                while($row != null){
                    $participatingAgents[] = $row[0];
                    $row = $result->fetchRow();
                }
                $sql = "SELECT agentid FROM `vtiger_orders_participatingagents` WHERE ordersid=? AND permissions!=3";
                $result = $db->pquery($sql, array($orderId));
                $row = $result->fetchRow();
                while($row != null){
                    $participatingAgents[] = $row[0];
                    $row = $result->fetchRow();
                }

                /*$sql = "SELECT participating_agents_full FROM `vtiger_potential` WHERE potentialid=?";
                $result = $db->pquery($sql, array($potentialId));
                $row = $result->fetchRow();
                $participatingAgentsFullOpportunities = $row[0];
                $participatingAgentsFullOpportunities = explode(' |##| ', $participatingAgentsFullOpportunities);
                $sql = "SELECT participating_agents_no_rates FROM `vtiger_potential` WHERE potentialid=?";
                $result = $db->pquery($sql, array($potentialId));
                $row = $result->fetchRow();
                $participatingAgentsNoRatesOpportunities = $row[0];
                $participatingAgentsNoRatesOpportunities = explode(' |##| ', $participatingAgentsNoRatesOpportunities);
                $sql = "SELECT participating_agents_full FROM `vtiger_orders` WHERE ordersid=?";
                $result = $db->pquery($sql, array($orderId));
                $row = $result->fetchRow();
                $participatingAgentsFullOrders = $row[0];
                $participatingAgentsFullOrders = explode(' |##| ', $participatingAgentsFullOrders);
                $sql = "SELECT participating_agents_no_rates FROM `vtiger_orders` WHERE ordersid=?";
                $result = $db->pquery($sql, array($orderId));
                $row = $result->fetchRow();
                $participatingAgentsNoRatesOrders = $row[0];
                $participatingAgentsNoRatesOrders = explode(' |##| ', $participatingAgentsNoRatesOrders);
                $participatingAgents = array_merge($participatingAgentsFullOpportunities, $participatingAgentsNoRatesOpportunities, $participatingAgentsFullOrders, $participatingAgentsNoRatesOrders);

                foreach($participatingAgents as $participatingAgent){
                    $sql = "SELECT agentname FROM `vtiger_agents` WHERE agentsid=?";
                    $result = $db->pquery($sql, array($participatingAgent));
                    $row = $result->fetchRow();
                    $participatingAgentNames[] = $row[0];
                }

                foreach($participatingAgentNames as $participatingAgentName){
                    foreach($userGroupNames as $groupName){
                        if($grouNamep == $participatingAgentName && !in_array($recordModel, $userEntries)){
                            $userEntries[] = $recordModel;
                        }
                    }
                }
            }
            foreach($userEntries as $key => $currentEntry){
                if($currentEntry == $recordId){
                    if(array_key_exists($key-1, $userEntries)){$prevRecordId = $userEntries[$key-1];}
                    if(array_key_exists($key+1, $userEntries)){$nextRecordId = $userEntries[$key+1];}
                }
            }
        } elseif(!$isAdmin && $moduleName == 'VanlineManager'){
            $userEntries = array();

            $userLines = array();
                $vanlineUsers = array();
                $sql = "SELECT userid FROM `vtiger_users2vanline` WHERE vanlineid=?";
                $result = $db->pquery($sql, array($recordId));
                $row = $result->fetchRow();
                while($row != null){
                    $vanlineUsers[] = $row[0];
                    $row = $result->fetchRow();
                }
                if(in_array($currentUserId, $vanlineUsers)){
                    $userLines[$key] = $recordModel;
                }

            foreach($navigationMerged as $recordModel){
                //add entries owned by current users agent group to list
                foreach($userLines as $userLine){
                    if($userLine == $recordModel  && !in_array($recordModel, $userEntries)){
                        $userEntries[] = $recordModel;
                    }
                }
            }
            foreach($userEntries as $key => $currentEntry){
                if($currentEntry == $recordId){
                    if(array_key_exists($key-1, $userEntries)){$prevRecordId = $userEntries[$key-1];}
                    if(array_key_exists($key+1, $userEntries)){$nextRecordId = $userEntries[$key+1];}
                }
            }
        } elseif(!$isAdmin && ($moduleName == 'Cubesheets' || $moduleName == 'Calendar' || $moduleName == 'Events' || $moduleName == 'AgentManager' || $moduleName == 'Surveys')){
            $userEntries = array();
            $userGroups = array();
            $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result = $db->pquery($sql, array($currentUserId));
            $row = $result->fetchRow();
            while($row != NULL){
                $userGroups[] = $row[0];
                $row = $result->fetchRow();
            }
            $ownerGroups = array();

            $sql = "SELECT smownerid FROM `vtiger_crmentity` WHERE crmid=?";
            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();
            $ownerUserId = $row[0];

            $sql = "SELECT groupid FROM `vtiger_users2group` WHERE userid=?";
            $result = $db->pquery($sql, array($ownerUserId));
            $row = $result->fetchRow();
            while($row != null){
                $ownerGroups[] = $row[0];
                $row = $result->fetchRow();
            }

            foreach($navigationMerged as $recordModel){
                //add entries owned by current users agent group to list
                foreach($userGroups as $userGroup){
                    if(in_array($userGroup, $ownerGroups) && !in_array($recordModel, $userEntries)){
                        $userEntries[] = $recordModel;
                    }
                }
            }
            foreach($userEntries as $key => $currentEntry){
                if($currentEntry == $recordId){
                    if(array_key_exists($key-1, $userEntries)){$prevRecordId = $userEntries[$key-1];}
                    if(array_key_exists($key+1, $userEntries)){$nextRecordId = $userEntries[$key+1];}
                }
            }
        }
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if(!empty($prevRecordId)) {
            $viewer->assign('PREVIOUS_RECORD_URL', $moduleModel->getDetailViewUrl($prevRecordId));
        }
        if(!empty($nextRecordId)) {
            $viewer->assign('NEXT_RECORD_URL', $moduleModel->getDetailViewUrl($nextRecordId));
        }

        $viewer->assign('MODULE_MODEL', $this->record->getModule());
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

        $viewer->assign('IS_EDITABLE', $this->record->getRecord()->isEditable($moduleName));
        $viewer->assign('IS_DELETABLE', $this->record->getRecord()->isDeletable($moduleName));

        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
        $linkModels = $this->record->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $linkModels);
        $viewer->assign('MODULE_NAME', $moduleName);

        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('DEFAULT_RECORD_VIEW', $currentUserModel->get('default_record_view'));

                $picklistDependencyDatasource=  Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
                $viewer->assign('PICKLIST_DEPENDENCY_DATASOURCE',Zend_Json::encode($picklistDependencyDatasource));
        if($display) {
            $this->preProcessDisplay($request);
        }
    */
    }

    public function preProcessTplName(Vtiger_Request $request)
    {
        return 'DetailViewPreProcess.tpl';
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

    public function postProcess(Vtiger_Request $request)
    {
        $recordId         = $request->get('record');
        $moduleName       = $request->getModule();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $moduleModel      = Vtiger_Module_Model::getInstance($moduleName);
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
        $detailViewLinks      = $this->record->getDetailViewLinks($detailViewLinkParams);
        $selectedTabLabel     = $request->get('tab_label');
        if (empty($selectedTabLabel)) {
            if ($currentUserModel->get('default_record_view') === 'Detail') {
                $selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '.vtranslate('LBL_DETAILS', $moduleName);
            } else {
                if ($moduleModel->isSummaryViewSupported()) {
                    $selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '.vtranslate('LBL_SUMMARY', $moduleName);
                } else {
                    $selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '.vtranslate('LBL_DETAILS', $moduleName);
                }
            }
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('SELECTED_TAB_LABEL', $selectedTabLabel);
        $viewer->assign('MODULE_MODEL', $this->record->getModule());
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */
        $viewer->view('DetailViewPostProcess.tpl', $moduleName);
        parent::postProcess($request);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $jsFileNames           = [
            'modules.Vtiger.resources.Detail',
            'modules.Vtiger.resources.DetailBlock',
            'modules.Vtiger.resources.List',
            'modules.Settings.Vtiger.resources.List',  //This is needed for the next js.
            "modules.$moduleName.resources.List",
            'modules.CustomView.resources.CustomView',
            "modules.$moduleName.resources.CustomView",
            'modules.Vtiger.resources.RelatedList',
            "modules.$moduleName.resources.RelatedList",
            "modules.$moduleName.resources.Detail",
            'libraries.jquery.jquery_windowmsg',
            "libraries.jquery.ckeditor.ckeditor",
            "libraries.jquery.ckeditor.adapters.jquery",
            "modules.Emails.resources.MassEdit",
            "modules.Vtiger.resources.CkEditor",
            "modules.Users.resources.Detail",
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

    public function showDetailViewByMode($request)
    {
        $requestMode = $request->get('requestMode');
        if ($requestMode == 'full') {
            return $this->showModuleDetailView($request);
        }

        return $this->showModuleBasicView($request);
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
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel    = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
            $recordModel->hiddenBlocks = $hiddenBlocks;
        }
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer           = $this->getViewer($request);
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        if ($moduleName == 'Users') {
            $viewer->assign('IS_OI_ENABLED', $this->record->getRecord()->getOIEnabled());
        }
        if ($moduleName == "Quotes") {
            $viewer->assign('BULKY_LABELS', $this->record->getRecord()->getBulkyLabels());
            $viewer->assign('BULKY_ITEMS', $this->record->getRecord()->getBulkyItems());
            $viewer->assign('PACKING_LABELS', $this->record->getRecord()->getPackingLabels());
            $viewer->assign('PACKING_ITEMS', $this->record->getRecord()->getPackingItems());
            $viewer->assign('MISC_CHARGES', $this->record->getRecord()->getMiscCharges($request));
            $viewer->assign('CRATES', $this->record->getRecord()->getCrates($request));
        }

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    public function showModuleSummaryView($request)
    {
        global $hiddenBlocksArray;
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
            $recordModel->hiddenBlocks = $hiddenBlocks;
        }
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
        $moduleModel    = $recordModel->getModule();
        $viewer         = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));

        return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
    }

    /**
     * Function shows basic detail for the record
     *
     * @param <type> $request
     */
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

    /**
     * Function returns recent changes made on the record
     *
     * @param Vtiger_Request $request
     */
    public function showRecentActivities(Vtiger_Request $request)
    {
        $parentRecordId = $request->get('record');
        $pageNumber     = $request->get('page');
        $limit          = $request->get('limit');
        $moduleName     = $request->getModule();
        if (empty($pageNumber)) {
            $pageNumber = 1;
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if (!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
        $recentActivities = ModTracker_Record_Model::getUpdates($parentRecordId, $pagingModel);
        $pagingModel->calculatePageRange($recentActivities);
        if ($pagingModel->getCurrentPage() == ModTracker_Record_Model::getTotalRecordCount($parentRecordId) / $pagingModel->getPageLimit()) {
            $pagingModel->set('nextPageExists', false);
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('RECENT_ACTIVITIES', $recentActivities);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);
        if ( $request->get('tab_label') && $request->get('tab_label') == 'LBL_UPDATES' ) {
            echo $viewer->view('AllRecentActivities.tpl', $moduleName, 'true');
        } else {
            echo $viewer->view('RecentActivities.tpl', $moduleName, 'true');
        }
    }
    /**
     * Function returns latest comments
     *
     * @param Vtiger_Request $request
     *
     * @return <type>
     */
    public function showRecentComments(Vtiger_Request $request)
    {
        $parentId   = $request->get('record');
        $pageNumber = $request->get('page');
        $limit      = $request->get('limit');
        $moduleName = $request->getModule();
        if (empty($pageNumber)) {
            $pageNumber = 1;
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if (!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
        $recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel);
        $pagingModel->calculatePageRange($recentComments);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
        $viewer           = $this->getViewer($request);
        $viewer->assign('COMMENTS', $recentComments);
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);

        return $viewer->view('RecentComments.tpl', $moduleName, 'true');
    }

    /**
     * Function returns related records
     *
     * @param Vtiger_Request $request
     *
     * @return <type>
     */
    public function showRelatedList(Vtiger_Request $request)
    {
        $moduleName            = $request->getModule();
        $relatedModuleName     = $request->get('relatedModule');
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

            return $targetController->process($request);
        }
    }

    /**
     * Function sends the child comments for a comment
     *
     * @param Vtiger_Request $request
     *
     * @return <type>
     */
    public function showChildComments(Vtiger_Request $request)
    {
        $parentCommentId    = $request->get('commentid');
        $parentCommentModel = ModComments_Record_Model::getInstanceById($parentCommentId);
        $childComments      = $parentCommentModel->getChildComments();
        $currentUserModel   = Users_Record_Model::getCurrentUserModel();
        $modCommentsModel   = Vtiger_Module_Model::getInstance('ModComments');
        $viewer             = $this->getViewer($request);
        $viewer->assign('PARENT_COMMENTS', $childComments);
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);

        return $viewer->view('CommentsList.tpl', $moduleName, 'true');
    }

    /**
     * Function sends all the comments for a parent(Accounts, Contacts etc)
     *
     * @param Vtiger_Request $request
     *
     * @return <type>
     */
    public function showAllComments(Vtiger_Request $request)
    {
        $parentRecordId      = $request->get('record');
        $commentRecordId     = $request->get('commentid');
        $moduleName          = $request->getModule();
        $currentUserModel    = Users_Record_Model::getCurrentUserModel();
        $modCommentsModel    = Vtiger_Module_Model::getInstance('ModComments');
        $parentCommentModels = ModComments_Record_Model::getAllParentComments($parentRecordId);
        if (!empty($commentRecordId)) {
            $currentCommentModel = ModComments_Record_Model::getInstanceById($commentRecordId);
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
        $viewer->assign('PARENT_COMMENTS', $parentCommentModels);
        $viewer->assign('CURRENT_COMMENT', $currentCommentModel);

        return $viewer->view('ShowAllComments.tpl', $moduleName, 'true');
    }

    /**
     * Function to get Ajax is enabled or not
     *
     * @param Vtiger_Record_Model record model
     *
     * @return <boolean> true/false
     */
    public function isAjaxEnabled($recordModel)
    {
        return false;
        //return $recordModel->isEditable();
    }

    /**
     * Function to get activities
     *
     * @param Vtiger_Request $request
     *
     * @return <List of activity models>
     */
    public function getActivities(Vtiger_Request $request)
    {
        return '';
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
        $moduleName        = $request->getModule();
        if (empty($pageNumber)) {
            $pageNumber = 1;
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if (!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }
        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
        $relationListView  = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName);
        $models            = $relationListView->getEntries($pagingModel);
        $header            = $relationListView->getHeaders();
        $viewer            = $this->getViewer($request);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RELATED_RECORDS', $models);
        $viewer->assign('RELATED_HEADERS', $header);
        $viewer->assign('RELATED_MODULE', $relatedModuleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);

        return $viewer->view('SummaryWidgets.tpl', $moduleName, 'true');
    }

    public function loadHiddenBlocksDetailView($moduleName, $record)
    {
        global $hiddenBlocksArray, $hiddenBlocksArrayField;
        $blocksToHide               = [];
        $businessLinesToHideFlatten = [];
        $businessLinesToHide        = [];
        $businessLinesToShow        = [];
        $recordModel                = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        $businessLines              = $recordModel->entity->column_fields[$hiddenBlocksArrayField[$moduleName]];
        $businessLines              = array_map('trim', explode('|##|', $businessLines));
        foreach ($hiddenBlocksArray[$moduleName] as $businessLine => $blocks) {
            if (!in_array($businessLine, $businessLines)) {
                $businessLinesToHide = array_merge($businessLinesToHide, explode('::', $hiddenBlocksArray[$moduleName][$businessLine]));
            } else {
                $businessLinesToShow = array_merge($businessLinesToShow, explode('::', $hiddenBlocksArray[$moduleName][$businessLine]));
            }
        }

        return array_diff($businessLinesToHide, $businessLinesToShow);
    }
}
