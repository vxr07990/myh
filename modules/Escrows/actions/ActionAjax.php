<?php

class Escrows_ActionAjax_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getRevenueGroupingItem');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public function getRevenueGroupingItem(Vtiger_Request $request)
    {
        global $adb;
        $fieldPickListValues=array();
        $fieldPickListValues['All']='All';
        // get Revenue Grouping Items
        $params=array();
        $query="select DISTINCT vtiger_revenuegroupingitem.revenuegroup from `vtiger_revenuegroupingitem`
                INNER JOIN vtiger_crmentity vtiger_crmentityItem ON vtiger_crmentityItem.crmid=vtiger_revenuegroupingitem.revenuegroupingitemid
                INNER JOIN vtiger_crmentity vtiger_crmentityGroup ON vtiger_crmentityGroup.crmid=vtiger_revenuegroupingitem.revenuegroupingitem_relcrmid
                WHERE vtiger_crmentityItem.deleted=0";
        if(!getenv('DISABLE_REFERENCE_FIELD_LIMIT_BY_OWNER')) {
            if($request->get('agentId')) {
                $query .= " AND vtiger_crmentityGroup.agentid=?";
                $params[]=$_REQUEST['agentId'];
            }
        }
        $rs=$adb->pquery($query,$params);
        if($adb->num_rows($rs)>0) {
            while($row=$adb->fetch_array($rs)) {
                $fieldPickListValues[$row['revenuegroup']]=$row['revenuegroup'];
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($fieldPickListValues);
        $response->emit();
    }
}
