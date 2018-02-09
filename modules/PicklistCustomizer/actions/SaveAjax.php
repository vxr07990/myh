<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class PicklistCustomizer_SaveAjax_Action extends Settings_Picklist_SaveAjax_Action
{

    public function checkPermission(Vtiger_Request $request)
    {
        $pickListName = $request->get('picklistName');
        $moduleName = $request->get('source_module');
        $idAgentManager = $request->get('id_lead_manager');

        $moduleModel = Settings_Picklist_Module_Model::getInstance($moduleName);
        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListName, $moduleModel);
        if($fieldModel->get('uitype') != '1500'){
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }


    public function add(Vtiger_Request $request)
    {
        $newValue = $request->getRaw('newValue');
        $fieldId = $request->get('fieldid');
        $agentid = $request->get('agentid');

        $fieldModel = Vtiger_Field_Model::getInstance($fieldId);

        $response = new Vtiger_Response();
        try {

            $id = PicklistCustomizer_Module_Model::addPickListValues($fieldModel, $newValue, $agentid);

            $response->setResult(array('id' => $id));
        } catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }

    public function remove(Vtiger_Request $request)
    {
        $moduleName = $request->get('source_module');
        $valueToDelete = $request->getRaw('delete_value');
        $replaceValue = $request->getRaw('replace_value');
        $pickListFieldName = $request->get('picklistName');
        $fieldId = $request->get('fieldid');
        $agentid = $request->get('agentid');

        $moduleModel = Vtiger_Module_Model::getInstance('PicklistCustomizer');
        $response = new Vtiger_Response();
        try {
            $status = $moduleModel->remove($pickListFieldName, $fieldId, $valueToDelete, $replaceValue, $agentid);
            $response->setResult(array('success', $status));
        } catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }

    public function rename(Vtiger_Request $request)
    {
        $moduleName = $request->get('source_module');

        $newValue = $request->getRaw('newValue');
        $pickListFieldName = $request->get('picklistName');
        $oldValue = $request->getRaw('oldValue');
        $id = $request->getRaw('id');
        $agentid = $request->getRaw('agentid');
        $fieldid = $request->getRaw('fieldid');

        $moduleModel = new PicklistCustomizer_Module_Model();
        $response = new Vtiger_Response();
        try {
            $status = $moduleModel->renamePickListValues($fieldid, $oldValue, $newValue, $id, $agentid);
            $response->setResult(array('success', $status));
        } catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }

}
