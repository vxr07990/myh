<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Orders_SaveAjax_Action extends Vtiger_SaveAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        if ($request->get('field') == 'ordersstatus') {
            $db = PearDatabase::getInstance();
            $result = $db->pquery("SELECT ordersstatus FROM vtiger_orders WHERE ordersid=?", array($request->get('record')));
            $row = $result->fetchRow();
            $old_status = $row[0];
            if ($old_status !== $request->get('value')) {
                $orderModel = Vtiger_Module_Model::getInstance('Orders');
                            
                $orderModel->createMilestone($request->get('record'), $old_status, $request->get('value'));
            }
        }
        
        $recordModel = $this->saveRecord($request);

        $fieldModelList = $recordModel->getModule()->getFields();
        $result = array();
        foreach ($fieldModelList as $fieldName => $fieldModel) {
            $recordFieldValue = $recordModel->get($fieldName);
            if (is_array($recordFieldValue) && ($fieldModel->getFieldDataType() == 'multipicklist' || $fieldModel->getFieldDataType() == 'multiagent')) {
                $recordFieldValue = implode(' |##| ', $recordFieldValue);
            }
            $fieldValue = $displayValue = Vtiger_Util_Helper::toSafeHTML($recordFieldValue);
            if ($fieldModel->getFieldDataType() !== 'currency' && $fieldModel->getFieldDataType() !== 'datetime' && $fieldModel->getFieldDataType() !== 'date') {
                $displayValue = $fieldModel->getDisplayValue($fieldValue, $recordModel->getId());
            }
            $result[$fieldName] = array('value' => $fieldValue, 'display_value' => $displayValue);
        }



        $result['_recordLabel'] = $recordModel->getName();
        $result['_recordId'] = $recordModel->getId();

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($result);
        $response->emit();
    }
}
