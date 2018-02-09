<?php

class ItemCodes_MassActionAjax_View extends Vtiger_MassActionAjax_View
{
    public function duplicateRecords(Vtiger_Request $request)
    {
        $selectedIds = $request->get('selected_ids');
        if(count($selectedIds) != 1) {
            //Double check that we have exactly one record selected
            return;
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($selectedIds[0], 'ItemCodes');
        if ($recordModel) {
            $response = new Vtiger_Response();
            $response->setResult($recordModel->getDuplicateRecordUrl());
            $response->emit();
        }
    }
}
