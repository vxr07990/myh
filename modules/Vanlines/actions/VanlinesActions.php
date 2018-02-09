<?php

class Vanlines_VanlinesActions_Action extends Vtiger_BasicAjax_Action
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
            $contactid = $request->get("vanlineid");
            
            $rel_table_arr =  array("Contacts"=>"vtiger_contactdetails",
                                    "Agents"=>"vtiger_agents",
                                    "Users"=>"vtiger_users",
                                    "VanlineManager"=>"vtiger_vanlinemanager",
                                    "Orders"=>"vtiger_orders");

            $tbl_field_arr =  array("vtiger_contactdetails"=>"vanlines",
                                    "vtiger_agents"=>"agent_vanline",
                                    "vtiger_users"=>"vanline",
                                    "vtiger_vanlinemanager"=>"vanline_id",
                                    "vtiger_orders"=>"orders_vanlineregnum");
            
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
