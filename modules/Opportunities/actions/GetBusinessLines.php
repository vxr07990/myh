<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Opportunities_GetBusinessLines_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray, $adb, $hiddenBlocksArrayField;

        $record = $request->get('record');
        $requiredByModule = $request->get('currentmodule');
        $requiredId = $request->get('current_id');

        if ($requiredByModule == 'SalesOrder') {
            $parentModule = 'Quotes';
        } elseif ($requiredByModule == 'Invoice') {
            $parentModule = 'SalesOrder';
        } else {
            $parentModule = 'Opportunities';
        }


        if (!empty($requiredId) && $requiredId != '') {
            $recordModel = Vtiger_Record_Model::getInstanceById($requiredId, $requiredByModule);
            $businessLinesSelected = $recordModel->entity->column_fields[$hiddenBlocksArrayField[$requiredByModule]];
            $businessLinesSelected = array_map('trim', explode('|##|', $businessLinesSelected));

            $recordModel = Vtiger_Record_Model::getInstanceById($record, $parentModule);
            $businessLines = $recordModel->entity->column_fields[$hiddenBlocksArrayField[$parentModule]];
            $businessLines = array_map('trim', explode('|##|', $businessLines));
        } else {
            $recordModel = Vtiger_Record_Model::getInstanceById($record, $parentModule);
            $businessLinesSelected = $recordModel->entity->column_fields[$hiddenBlocksArrayField[$parentModule]];
            $businessLinesSelected = array_map('trim', explode('|##|', $businessLinesSelected));
            $businessLines = $businessLinesSelected;
        }


        $info['show'] = $businessLines;
        $info['field'] = $hiddenBlocksArrayField[$requiredByModule];
        $info['selected'] = $businessLinesSelected;

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
