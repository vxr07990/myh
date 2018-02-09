<?php

class Contacts_ContactsActions_Action extends Vtiger_BasicAjax_Action
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
            
            $rel_table_arr =  array("Quotes"=>"vtiger_quotes",
                                    "Invoice"=>"vtiger_invoice",
                                    "SalesOrder"=>"vtiger_salesorder",
                                    "PurchaseOrder"=>"vtiger_purchaseorder",
                                    "Agents"=>"vtiger_agents",
                                    "Potentials"=>"vtiger_potential",
                                    "HelpDesk"=>"vtiger_troubletickets",
                                    "Cubesheets"=>"vtiger_cubesheets",
                                    "Orders"=>"vtiger_orders",
                                    "Contracts"=>"vtiger_contracts",
                                    "Project"=>"vtiger_project",
                                    "Assets"=>"vtiger_assets");

            $tbl_field_arr =  array("vtiger_quotes"=>"contactid",
                                    "vtiger_invoice"=>"contactid",
                                    "vtiger_salesorder"=>"contactid",
                                    "vtiger_purchaseorder"=>"contactid",
                                    "vtiger_agents"=>"agent_contact",
                                    "vtiger_potential"=>"contact_id",
                                    "vtiger_troubletickets"=>"contact_id",
                                    "vtiger_cubesheets"=>"contact_id",
                                    "vtiger_orders"=>"orders_contacts",
                                    "vtiger_contracts"=>"contact_id",
                                    "vtiger_project"=>"linktoaccountscontacts",
                                    "vtiger_assets"=>"contact");
            
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
