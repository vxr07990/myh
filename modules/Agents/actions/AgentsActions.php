<?php

class Agents_AgentsActions_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        switch ($mode) {
            case 'checkToDelete':
                $result = $this->checkToDelete($request);

                $msg = new Vtiger_Response();
                $msg->setResult($result);
                $msg->emit();
                break;
            default:
                break;
        }
    }
    public function checkToDelete(Vtiger_Request $request)
    {
        global $adb;
        try {
            $contactid = $request->get("contactid");

            $rel_table_arr =  array("Contacts"=>"vtiger_contactdetails",
                                    "Trips"=>"vtiger_trips",
                                    "OrdersTask"=>"vtiger_orderstask",
                                    "ClaimItems"=>"vtiger_claimitems",
                                    "Potentials"=>"vtiger_potential",
                                    "Orders"=>"vtiger_orders");

            $tbl_field_arr =  array("vtiger_contactdetails"=>"agents",
                                    "vtiger_trips"=>"agent_unit",
                                    "vtiger_orderstask"=>"participating_agent",
                                    "vtiger_claimitems"=>"claims_agents",
                                    "vtiger_potential"=>"participating_agents_full",
                                    "vtiger_orders"=>"participating_agents_full");

            foreach ($rel_table_arr as $rel_module=>$rel_table) {
                $id_field = $tbl_field_arr[$rel_table];
                $sel_result =  $adb->pquery("select $id_field from $rel_table where $id_field=?", array($contactid));
                $res_cnt = $adb->num_rows($sel_result);
                if ($res_cnt > 0) {
                    return 'ERROR';
                }
            }
            return 'OK';
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
}
