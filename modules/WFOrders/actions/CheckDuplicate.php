<?php

class WFOrders_CheckDuplicate_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();

        $moduleName  = $request->getModule();
        $account     = $request->get('account');
        $orderNumber = $request->get('orderNumber');
        $record      = $request->get('recordId');

        $result = array('duplicate'=>false);

        if ($account && $orderNumber) {
            $sql = 'SELECT `wfordersid` FROM `vtiger_wforders` WHERE `wforder_number` = ? AND `wforder_account` = ?';

            $params = [$orderNumber, $account];

            if($record){
                $sql .= ' AND `wfordersid` != ?';

                $params[] = $record;
            }

            $orders = $db->pquery($sql, $params);
            if($db->num_rows($orders)){
                $result = array('duplicate'=>true);
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
