<?php

/****************************************************************************************
 * @author             Louis Robinson
 * @file               List.php
 * @description        Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code.
 * @contact        lrobinson@igcsoftware.com
 * @company            IGC Software
 ****************************************************************************************/
class Estimates_List_View extends Quotes_List_View
{
    protected $listViewEntries = false;
    protected $listViewCount   = false;
    protected $listViewLinks   = false;
    protected $listViewHeaders = false;
    protected $listViewIdToName = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $modulePopUpFile       = 'modules.'.$moduleName.'.resources.Popup';
        $moduleEditFile        = 'modules.'.$moduleName.'.resources.Edit';
        unset($headerScriptInstances[$modulePopUpFile]);
        unset($headerScriptInstances[$moduleEditFile]);
        //Updated this order for Actuals List view to load all the required in first.
        $jsFileNames           = [
            'modules.Quotes.resources.Edit',
            $modulePopUpFile,
            'modules.Estimates.resources.Edit',
            'modules.Estimates.resources.BaseTariff',
            $moduleEditFile,
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }

    public function process(Vtiger_Request $request)
    {
        //old securities
        /*$viewer = $this->getViewer ($request);
        $moduleName = $request->getModule();
        $cvId = $request->get('viewname');
        $pagingModel = new Vtiger_Paging_Model();
        $pageNumber = $request->get('page');
        if(empty ($pageNumber)){
            $pageNumber = '1';
        }
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('viewid', $request->get('viewname'));
        $listViewModel = Vtiger_ListView_Model::getInstance($moduleName, $cvId);
        if(!$this->listViewHeaders){
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
        }
        $tempListViewEntries = array();
        $tempListViewEntries = $listViewModel->getListViewEntries($pagingModel);

        $extraPermissions = array();
        foreach($tempListViewEntries as $entry){
            $extraPermissions[] = $this::getExtraPermissions($request, $entry->get('id'));
        }
        $viewer->assign('EXTRA_PERMISSIONS', $extraPermissions);*/
        parent::process($request);
    }

    public function getExtraPermissions($request, $estimateId)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        /*$db = PearDatabase::getInstance();

        $userModel = Users_Record_Model::getCurrentUserModel();
        $currentUserId = $userModel->getId();

        $isAdmin = $userModel->isAdminUser();

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

        $sql = "SELECT orders_id, potentialid FROM `vtiger_quotes` WHERE quoteid=?";
        $result = $db->pquery($sql, array($estimateId));
        $row = $result->fetchRow();

        //file_put_contents("logs/devLog.log", "\n ORDERID EMPTY: ".empty($row[1]), FILE_APPEND);
        //file_put_contents("logs/devLog.log", "\n POTENTIALNAME EMPTY: ".empty($row[0]), FILE_APPEND);

        $noOrdersOrOpportunities = false;

        if((!empty($row[0]) && empty($row[1])) ||(!empty($row[0]) && !empty($row[1]))){
            $recordId = $row[0];
            $tableName = 'vtiger_orders_participatingagents';
            $columnName = 'ordersid';
        }
        elseif(empty($row[0]) && !empty($row[1])){
            $recordId = $row[1];
            $tableName = 'vtiger_potential_participatingagents';
            $columnName = 'opportunityid';
        }
        elseif(empty($row[0]) && empty($row[1])){
            $noOrdersOrOpportunities = true;
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
                if($owned == $estimateId){
                    $creatorPermissions = true;
                }
            }
        }
        $participatingAgentPermissions = 'none';
        if($creatorPermissions == false && $noOrdersOrOpportunities == false){
            $participatingAgents = array();
            $participatingAgentNames = array();
            $sql = "SELECT agentid, permissions FROM `".$tableName."` WHERE ".$columnName."=? AND permissions!=3";
            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();
            while($row != null){
                $participatingAgents[] = array($row[0], $row[1]);
                $row = $result->fetchRow();
            }
            foreach($participatingAgents as $participatingAgent){
                $sql = "SELECT agentname FROM `vtiger_agents` WHERE agentsid=?";
                $result = $db->pquery($sql, array($participatingAgent[0]));
                $row = $result->fetchRow();
                $participatingAgentNames[] = array($row[0], $participatingAgent[1]);
            }
            /*$sql = "SELECT participating_agents_full FROM `".$tableName."` WHERE ".$columnName."=?";
            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();
            $participatingAgentsFull = $row[0];
            $participatingAgentsFull = explode(' |##| ', $participatingAgentsFull);
            $sql = "SELECT participating_agents_no_rates FROM `".$tableName."` WHERE ".$columnName."=?";
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
    return array($creatorPermissions, $participatingAgentPermissions);*/
    }
    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
        parent::initializeListViewContents($request, $viewer);

        $tariffNames = [];
        foreach($this->listViewHeaders as $header) {
            if($header->get('name') == 'effective_tariff') {
                $field = $header->get('name');
                foreach($this->listViewEntries as $entry) {
                    $id = $entry->get($field);
                    $model = Vtiger_Record_Model::getInstanceById($id);
                    $name = $model->get('tariffmanagername');
                    if(!$name) {
                        $name = $model->get('tariff_name');
                    }
                    $tariffNames[$id] = $name;
                }
                break;
            }
        }

        $viewer->assign('TARIFFNAMES', $tariffNames);
    }
}
