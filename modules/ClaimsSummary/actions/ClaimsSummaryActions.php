<?php

include_once 'include/Webservices/Revise.php';
include_once 'include/Webservices/Delete.php';

class ClaimsSummary_ClaimsSummaryActions_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');

        switch ($mode) {
        case 'getOrderInfo':
        $result = $this->getOrderInfo($request);
        break;
        default:
        $result = 'ERROR';
        break;
    }
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }

    public function getOrderInfo(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $orderId = $request->get('orderId');

        $orderRecodModel = Vtiger_Record_Model::getInstanceById($orderId, 'Orders');
    
        $contactName =  getEntityName('Contacts', $orderRecodModel->get('orders_contacts'));
        $accountName =  getEntityName('Accounts', $orderRecodModel->get('orders_account'));

        $orderArr = array(
        'claimssummary_valuationtype' => $orderRecodModel->get('valuation_deductible'),
        'claimssummary_declaredvalue' => $orderRecodModel->get('total_valuation'),
        'claimssummary_contactid' => $orderRecodModel->get('orders_contacts'),
        'claimssummary_accountid' => $orderRecodModel->get('orders_account'),
        'claimssummary_contactid_display' => $contactName[$orderRecodModel->get('orders_contacts')],
        'claimssummary_accountid_display' => $accountName[$orderRecodModel->get('orders_account')],
        'business_line' => $orderRecodModel->get('business_line'),
       
        );

        return json_encode($orderArr);
    }
}
