<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Opportunities_Detail_View extends Potentials_Detail_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('showRelatedRecords');
        $this->exposeMethod('showChildComments');
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

    public function showModuleSummaryView($request)
    {
        global $hiddenBlocksArray;
        $currentUser = Users_Record_Model::getCurrentUserModel();
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
        if ($recordModel->getId() && getenv('INSTANCE_NAME') == 'sirva' && $recordModel->getPrimaryEstimateRecordModel(false)) {
            $db = PearDatabase::getInstance();
            $estimateRecordId = $recordModel->getPrimaryEstimateRecordModel(false)->getId();
            $total = $db->pquery("SELECT total FROM `vtiger_quotes` WHERE quoteid = ?", [$estimateRecordId])->fetchRow()['total'];
            if ($total) {
                $recordModel->set('amount', $total);
            }
        }
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
        $moduleModel = $recordModel->getModule();
        $viewer      = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));
        $viewer->assign('ENABLE_CALENDAR', true);
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */

        $sharedUsers = Calendar_Module_Model::getSharedUsersOfCurrentUser($currentUser->id);
        $sharedUsersInfo = Calendar_Module_Model::getSharedUsersInfoOfCurrentUser($currentUser->id);
        if(getenv('IGC_MOVEHQ')  && getenv('INSTANCE_NAME') != 'graebel') {
            $activeSurveyors = Surveys_Record_Model::getEmployeesUsersId();
            foreach ($sharedUsers as $sharedUserId => $sharedUserName) {
                if (!in_array($sharedUserId, $activeSurveyors)) {
                    unset($sharedUsers[$sharedUserId]);
                }
            }
        }
        $viewer->assign('SHAREDUSERS', $sharedUsers);
        $viewer->assign('SHAREDUSERS_INFO', $sharedUsersInfo);
        return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
    }

    public function showModuleBasicView($request)
    {
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        if ($recordModel->getId() && getenv('INSTANCE_NAME') == 'sirva' && $recordModel->getPrimaryEstimateRecordModel(false)) {
            $db = PearDatabase::getInstance();
            $estimateRecordId = $recordModel->getPrimaryEstimateRecordModel(false)->getId();
            $total = $db->pquery("SELECT total FROM `vtiger_quotes` WHERE quoteid = ?", [$estimateRecordId])->fetchRow()['total'];
            if ($total) {
                $recordModel->set('amount', $total);
            }
        }
        $detailViewLinkParams = ['MODULE' => $moduleName, 'RECORD' => $recordId];
        $detailViewLinks      = $this->record->getDetailViewLinks($detailViewLinkParams);
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('MODULE_NAME', $moduleName);
        $recordStrucure   = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel = $recordModel->getModule();
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
//            $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($recordId));
        }
        $participatingAgentModel = Vtiger_Module_Model::getInstance('ParticipatingAgents');
        if ($participatingAgentModel && $participatingAgentModel->isActive()) {
            $viewer->assign('PARTICIPATING_AGENTS', $participatingAgentModel->isActive());
            $viewer->assign('PARTICIPANT_LIST', $participatingAgentModel::getParticipants($recordId));
        }
        //stops block
        $extraStopsModel = Vtiger_Module_Model::getInstance('ExtraStops');
        if ($extraStopsModel && $extraStopsModel->isActive()) {
            $extraStopsModel->setViewerForStops($viewer, $recordId);
        }
        $this->setViewerForGuestBlocks($moduleName, $recordId, $viewer);
        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }

    public function getExtraPermissions($request)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*
        $db = PearDatabase::getInstance();

        $userModel = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();

        $isAdmin = $userModel->isAdminUser();

        $recordId = $request->get('record');

        $creatorPermissions = false;
        $memberOfParentVanline = false;

        $sql = "SELECT vanlineid, is_parent FROM `vtiger_users2vanline` JOIN `vtiger_vanlinemanager` ON vanlineid=vanlinemanagerid WHERE userid=?";
        $result = $db->pquery($sql, array($currentUserId));
        while($row =& $result->fetchRow()) {
            $validVanlines[] = $row[0];
            if($row['is_parent'] == 1) {
                //One of the vanlines the user is associated with is the parent. Display all records
                $memberOfParentVanline = true;
            }
        }

        if($isAdmin || $memberOfParentVanline){
            $creatorPermissions = true;
        }else{
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
            foreach($groupOwned as $owned){
                if($owned == $recordId){
                    $creatorPermissions = true;
                }
            }
        }

        if($creatorPermissions == false){
            $participatingAgentPermissions = 'none';
            $participatingAgents = array();
            $participatingAgentNames = array();
            $sql = "SELECT agent_id, permission FROM `vtiger_participating_agents` WHERE crmentity_id=? AND permission!=3 AND status=1";
            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();
            while($row != null){
                $participatingAgents[] = array($row[0], $row[1]);
                $row = $result->fetchRow();
            }
            //file_put_contents('logs/devLog.log', "\n participatingAgents: ".print_r($participatingAgents, true), FILE_APPEND);
            foreach($participatingAgents as $participatingAgent){
                $sql = "SELECT agentname FROM `vtiger_agents` WHERE agentsid=?";
                $result = $db->pquery($sql, array($participatingAgent[0]));
                $row = $result->fetchRow();
                $participatingAgentNames[] = array($row[0], $participatingAgent[1]);
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
            foreach($participatingAgentNames as $participatingAgentName){
                foreach($userGroupNames as $groupName){
                    if($groupName == $participatingAgentName[0]){
                        if($participatingAgentName[1] == 0){
                            $creatorPermissions = true;
                            $participatingAgentPermissions = 'edit';
                        } elseif($participatingAgentName[1] == 1 && $creatorPermissions == false && $participatingAgentPermissions != 'edit'){
                            $participatingAgentPermissions = 'full';
                        } elseif($participatingAgentName[1] == 2 && $creatorPermissions == false && $participatingAgentPermissions != 'full'){
                            $participatingAgentPermissions = 'no_rates';
                        }
                    }
                }
            }
        }
    //sales person securities piece
    $userRole = $userModel->getRole();
    $sql = "SELECT rolename FROM `vtiger_role` WHERE roleid=?";
    $result = $db->pquery($sql, array($userRole));
    $row = $result->fetchRow();
    $roleName = $row[0];
    if(strpos($roleName, 'Sales Person') !== false){
        $creatorPermissions = false;
        $participatingAgentPermissions = 'none';
        $sql = "SELECT sales_person FROM `vtiger_potential` WHERE potentialid=?";
        $result = $db->pquery($sql, array($recordId));
        $row = $result->fetchRow();
        $salesPerson = $row[0];
        if($salesPerson == $currentUserId){
            $creatorPermissions = true;
        }
        //file_put_contents('logs/devLog.log', "\n IN OPP IF STATEMENT", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n SALES PERSON: $salesPerson", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n CURRENT USER: $currentUserId", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n RECORDID: $recordId", FILE_APPEND);
    }
    return array($creatorPermissions, $participatingAgentPermissions);
    */
    }

    public function showModuleDetailView(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $viewer = $this->getViewer($request);
        $recordModel     = $this->record->getRecord();
        if ($recordModel->getId() && getenv('INSTANCE_NAME') == 'sirva' && $recordModel->getPrimaryEstimateRecordModel(false)) {
            $db = PearDatabase::getInstance();
            $estimateRecordId = $recordModel->getPrimaryEstimateRecordModel(false)->getId();

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
        if ($recordModel->getId() && getenv('INSTANCE_NAME') == 'graebel') {
            $salesStageVal = $recordModel->get('sales_stage');
            $reasonVal = $recordModel->get('opportunities_reason');
        }
        $recordStructure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        if (!empty($hiddenBlocksArray)) {
            if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
                $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
                $recordModel->hiddenBlocks = $hiddenBlocks;
            }
        }
        //file_put_contents('logs/devLog.log', "\n RecordModel->hiddenBlocks2 : ".print_r($recordModel->hiddenBlocks, true), FILE_APPEND);
        $structuredValues = $recordStructure->getStructure();
        if($recordId) {
            $this->convertSurveyTimeFormat($structuredValues, $recordId);
        }
        $moduleModel = $recordModel->getModule();
        $viewer = $this->getViewer($request);
        if($recordModel->get('billing_type') == 'NAT')
        {
            $viewer->assign('HIDE_EMPLOYEE_ASSISTING', 1);
        }
		
		$viewer->assign("OPP_STATUS",$recordModel->get('opportunitystatus'));
		
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('SALES_STAGE_VAL', $salesStageVal);
        $viewer->assign('REASON_VAL', $reasonVal);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('STOPS_ROWS', Orders_Module_Model::getStops($record, 'Opportunities'));
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
//            $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($recordId));
        }
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
        // $extraStopsModel = Vtiger_Module_Model::getInstance('ExtraStops');
        // if ($extraStopsModel && $extraStopsModel->isActive()) {
        //     $extraStopsModel->setViewerForStops($viewer, $recordId);
        // }

        $showTransitGuide = false;
        //commented out because detail view has an issue re registering events.
//        if (getenv('INSTANCE_NAME') == 'sirva') {
//            $estimateRecordModel = $recordModel->getPrimaryEstimateRecordModel();
//            if ($estimateRecordModel && $estimateRecordModel->get('effective_tariff')) {
//                try {
//                    if ($estimateRecordModel->get('load_date')) {
//                        $tariffManager = Vtiger_Record_Model::getInstanceById($estimateRecordModel->get('effective_tariff'), 'TariffManager');
//                        //@TODO add something to the tariff manager to gui this stuff maybe a second table?  Still requires some thought.
//                        switch ($tariffManager->get('custom_tariff_type')) {
//                            case 'Allied Express':
//                            case 'ALLV-2A':
//                            case 'Blue Express':
//                            case 'Intra - 400N':
//                            case 'Intra - 400N':
//                            case 'Local/Intra':
//                            case 'Local/Intra':
//                            case 'NAVL-12A':
//                            case 'Pricelock':
//                            case 'Pricelock GRR':
//                            case 'TPG':
//                            case 'TPG GRR':
//                            case 'UAS':
//                            case 'UAS':
//                                $showTransitGuide = true;
//                                break;
//                            default:
//                                $showTransitGuide = false;
//                        }
//                    }
//                } catch (Exception $ex) {
//                    //didn't find the tariffmanager record, means it's a local tariff so that's false!
//                    $showTransitGuide = false;
//                }
//            }
//
//            /*
//             *@TODO: Only allow if there is an estimate with a tariff at this point.
//            if (!$showTransitGuide) {
//                //no estimate so we use the opportunity's settings
//                if ($recordModel->get('load_date') &&
//                    (
//                        ($recordModel->get('business_line') == 'Intrastate Move') ||
//                        ($recordModel->get('business_line') == 'Interstate Move')
//                    )
//                ) {
//                    $showTransitGuide = true;
//                }
//            }
//            */
//        }
        $viewer->assign('SHOW_TRANSIT_GUIDE', $showTransitGuide);
        $viewer->assign('OPP_TYPE', $recordModel->get('lead_type'));

        //logic to include Addresss List
        $addressListModule = Vtiger_Module_Model::getInstance('AddressList');
        if ($addressListModule && $addressListModule->isActive()) {
            $addressListModule->assignValueForAddressList($viewer,$recordId);
        }

        $this->setViewerForGuestBlocks($moduleName, $recordId, $viewer);
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    public function preProcessDisplay(Vtiger_Request $request)
    {
        $viewer           = $this->getViewer($request);
        //old securities
        /*
        $extraPermissions = $this::getExtraPermissions($request);
        $viewer->assign('CREATOR_PERMISSIONS', $extraPermissions[0]);
        $viewer->assign('AGENT_PERMISSIONS', $extraPermissions[1]);
        */
        $displayed = $viewer->view($this->preProcessTplName($request), $request->getModule());
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
        $moduleName  = 'Calendar';
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if ($currentUserPriviligesModel->hasModulePermission($moduleModel->getId())) {
            $moduleName = $request->getModule();
            $recordId   = $request->get('record');
            $pageNumber = $request->get('page');
            if (empty($pageNumber)) {
                $pageNumber = 1;
            }
            $pagingModel = new Vtiger_Paging_Model();
            $pagingModel->set('page', $pageNumber);
            $pagingModel->set('limit', 10);
            if (!$this->record) {
                $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
            }
            $recordModel = $this->record->getRecord();
            $moduleModel = $recordModel->getModule();
            $relatedActivities = $moduleModel->getCalendarActivities('', $pagingModel, 'all', $recordId);
            $viewer = $this->getViewer($request);
            $viewer->assign('RECORD', $recordModel);
            $viewer->assign('MODULE_NAME', $moduleName);
            $viewer->assign('MODULE', $request->getModule());
            $viewer->assign('PAGING_MODEL', $pagingModel);
            $viewer->assign('PAGE_NUMBER', $pageNumber);
            $viewer->assign('ACTIVITIES', $relatedActivities);

            return $viewer->view('RelatedActivities.tpl', $moduleName, true);
        }
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $jsFileNames           = [
            "~/libraries/fullcalendar/fullcalendar.js",
            "~/libraries/jquery/colorpicker/js/colorpicker.js",
            "modules.Opportunities.resources.OpportunitiesCalendarView",
            "modules.VehicleLookup.resources.Detail",
            "modules.ParticipatingAgents.resources.Detail",
        ];

        if(getenv("INSTANCE_NAME") == 'sirva') {
            $jsFileNames[] = "modules.Opportunities.resources.STS";
        }

        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
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
        $moduleName = $request->get('module');
        $parentCommentId    = $request->get('commentid');
        $parentCommentModel = ModComments_Record_Model::getInstanceById($parentCommentId);
        $childComments      = $parentCommentModel->getChildComments();
        $currentUserModel   = Users_Record_Model::getCurrentUserModel();
        $modCommentsModel   = Vtiger_Module_Model::getInstance('ModComments');
        $viewer             = $this->getViewer($request);
        $viewer->assign('PARENT_COMMENTS', $childComments);
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
        $viewer->assign('MODULE_NAME', $moduleName);

        return $viewer->view('CommentsList.tpl', $moduleName, 'true');
    }
}
