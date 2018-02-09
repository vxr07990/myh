<?php
vimport('~~/vtlib/Vtiger/Module.php');
require_once('include/Webservices/Revise.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('includes/main/WebUI.php');

class AgentCompensationItems_Module_Model extends Vtiger_Module_Model {
    public function setViewerForAgentCompensationItems(&$viewer, $recordId = false){
        if ($recordId) {
            $viewer->assign('GROUPING_ITEMS', $this->getAgentCompensationItems($recordId));
        }
        $viewer->assign('ITEM_MODULE_MODEL', $this);
    }

    public function getAgentCompensationItems($recordId){
        $itemCodesMapping=array();
        $adb = PearDatabase::getInstance();

        $rs=$adb->pquery("SELECT vtiger_agentcompensationitems.agentcompensationitemsid
                FROM vtiger_agentcompensationitems
                INNER JOIN vtiger_crmentity ON vtiger_agentcompensationitems.agentcompensationitemsid=vtiger_crmentity.crmid
                WHERE deleted=0 AND agcomitem_agentcompgr=?",array($recordId));
        if($adb->num_rows($rs)>0) {
            while($row=$adb->fetch_array($rs)) {
                $recordModel = Vtiger_Record_Model::getInstanceById($row['agentcompensationitemsid']);
                if($_REQUEST['isDuplicate'] == true) {
                    $recordModel->set('id','');
                }
                $itemCodesMapping[$row['agentcompensationitemsid']]=$recordModel;
            }
        }

        return $itemCodesMapping;
    }

    public function isSummaryViewSupported() {
        return false;
    }

    public function saveAgentCompensationItems($request, $relId) {
        for($index = 1; $index <= $request['numAgentItems']; $index++){
            $itemsid = $request['itemsid_'.$index];
            if($itemsid == ''){
                $recordModel=Vtiger_Record_Model::getCleanInstance("AgentCompensationItems");
                $recordModel->set('mode','');
            }else{
                $recordModel=Vtiger_Record_Model::getInstanceById($itemsid);
                $recordModel->set('id',$itemsid);
                $recordModel->set('mode','edit');
            }
            $recordModel->set('agcomitem_name',$request['agcomitem_name_'.$index]);
            $recordModel->set('agcomitem_bookerdistribution',$request['agcomitem_bookerdistribution_'.$index]);
            $recordModel->set('agcomitem_origindistribution',$request['agcomitem_origindistribution_'.$index]);
            $recordModel->set('agcomitem_haulingdistribution',$request['agcomitem_haulingdistribution_'.$index]);
            $recordModel->set('agcomitem_general_officedistribution',$request['agcomitem_general_officedistribution_'.$index]);
            $recordModel->set('agcomitem_distribution',$request['agcomitem_distribution_'.$index]);
            $recordModel->set('agcomitem_agentcompgr',$relId);
            $recordModel->save();
        }
    }
}
