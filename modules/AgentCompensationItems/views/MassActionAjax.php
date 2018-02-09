<?php

class AgentCompensationItems_MassActionAjax_View extends Vtiger_MassActionAjax_View {
    function __construct() {
        parent::__construct();
        $this->exposeMethod('showEditForm');
    }

    function showEditForm(Vtiger_Request $request) {
        global $adb;
        $module      = $request->getModule();
        $viewer      = $this->getViewer($request);
        $agentid = $request->get('agentId');
        $arrGroupingItems=array();
        $query="select vtiger_revenuegroupingitem.*
                from `vtiger_revenuegroupingitem`
                INNER JOIN vtiger_crmentity vtiger_crmentityItem ON vtiger_crmentityItem.crmid=vtiger_revenuegroupingitem.revenuegroupingitemid AND vtiger_crmentityItem.deleted=0
                INNER JOIN vtiger_revenuegrouping ON vtiger_revenuegrouping.revenuegroupingid = vtiger_revenuegroupingitem.revenuegroupingitem_relcrmid
                INNER JOIN vtiger_crmentity vtiger_crmentityGrp ON vtiger_crmentityGrp.crmid=vtiger_revenuegrouping.revenuegroupingid AND vtiger_crmentityGrp.deleted=0
                WHERE vtiger_crmentityGrp.agentid=? ORDER BY invoicesequence";
        $rs=$adb->pquery($query,array($agentid));
        if($adb->num_rows($rs)) {
            while($row=$adb->fetch_array($rs)) {
                $itemRecordModel=Vtiger_Record_Model::getCleanInstance('AgentCompensationItems');
                $itemRecordModel->set('agcomitem_name',$row['revenuegroup']);
                $arrGroupingItems[]=$itemRecordModel;
            }
        }
        $viewer->assign('MODULE', $module);
        $viewer->assign('ITEM_MODULE_MODEL', Vtiger_Module_Model::getInstance($module));
        $viewer->assign('GROUPING_ITEMS', $arrGroupingItems);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        echo $viewer->view('BlockEditFields.tpl', $module, true);
    }
}