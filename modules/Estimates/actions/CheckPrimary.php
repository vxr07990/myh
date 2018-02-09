<?php

class Estimates_CheckPrimary_Action extends Vtiger_BasicAjax_Action {
    public function process(Vtiger_Request $request) {
        $orderId = $request->get('orderid');
        $oppId = $request->get('potentialid');
        $module = $request->get('module');
        $db = PearDatabase::getInstance();
        if(!empty($oppId)) {
            $sql    = "SELECT COUNT(quoteid) as numRelated FROM `vtiger_quotes` JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid=`vtiger_quotes`.quoteid WHERE is_primary=1 AND deleted=0 AND setype=? AND potentialid=?";
            $result = $db->pquery($sql, [$module, $oppId]);
            if ($db->num_rows($result) > 0 && $result->fields['numRelated'] > 0) {
                $response = new Vtiger_Response();
                $response->setResult(['hasParent' => 1, 'parentModule' => 'Opportunities']);
                $response->emit();

                return;
            }
        }

        if(!empty($orderId)) {
            $sql    = "SELECT COUNT(quoteid) as numRelated FROM `vtiger_quotes` JOIN `vtiger_crmentity` ON `vtiger_crmentity`.crmid=`vtiger_quotes`.quoteid WHERE is_primary=1 AND deleted=0 AND setype=? AND orders_id=?";
            $result = $db->pquery($sql, [$module, $orderId]);
            if ($db->num_rows($result) > 0 && $result->fields['numRelated'] > 0) {
                $response = new Vtiger_Response();
                $response->setResult(['hasParent' => 1, 'parentModule' => 'Orders']);
                $response->emit();

                return;
            }
        }

        $response = new Vtiger_Response();
        $response->setResult(['hasParent' => 0]);
        $response->emit();
    }
}
