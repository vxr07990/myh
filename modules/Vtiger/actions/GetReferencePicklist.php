<?php
class Vtiger_GetReferencePicklist_Action extends Vtiger_Action_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }
    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $fieldName = $request->get('fieldname', false);
        $relatedModule = $request->get('related_module', false);
        $seachValue = $request->get('search_value', false);
        $results = array();
        if ($fieldName && $relatedModule) {
            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleModel);
            $results = $fieldModel->getReferenceValues($relatedModule, $seachValue);
        }
        $response = new Vtiger_Response();
        $response->setResult($results);
        $response->emit();
    }
}
