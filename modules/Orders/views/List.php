<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 10/20/2016
 * Time: 10:13 AM
 */

class Orders_List_View extends Vtiger_List_View
{
    public function getCustomViewDisplayName($rawValue, $fieldData)
    {
        $displayValue = $rawValue;
        if ($fieldData['fieldname'] == 'agents_id') {
            if ($displayValue != '0') {
                $agentRecordModel = Vtiger_Record_Model::getInstanceById($displayValue, 'Agents');
                $displayValue     =
                    $agentRecordModel->get('agentname').' ('.$agentRecordModel->get('agent_number').')';
                if (!$displayValue) {
                    $displayValue = $rawValue;
                }
            } else {
                $displayValue = '';
            }
        }
        return $displayValue;
    }
}
