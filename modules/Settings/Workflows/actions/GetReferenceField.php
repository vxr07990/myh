<?php

class Settings_Workflows_GetReferenceField_Action extends Settings_Vtiger_Basic_Action {

    public function process(Vtiger_Request $request) {
        $module = $request->get('fieldModule');
        $fieldName = $request->get('fieldName');
        $workflowId = $request->get('workflow');

        $db = PearDatabase::getInstance();
        $sql = "SELECT fieldid, `vtiger_field`.tabid FROM `vtiger_field` JOIN `vtiger_tab` ON `vtiger_field`.tabid=`vtiger_tab`.tabid WHERE fieldname=? AND `vtiger_tab`.name=?";
        $res = $db->pquery($sql, [$fieldName, $module]);

        $fieldid = $res->fields['fieldid'];
        $tabid   = $res->fields['tabid'];

        $fieldModelList = Vtiger_Field_Model::getInstanceFromFieldId($fieldid, $tabid);
        $fieldModel = $fieldModelList[0];

        $viewer = $this->getViewer($request);

        if($workflowId) {
            $workflow = Settings_Workflows_Record_Model::getInstance($workflowId);
            $conditions = $workflow->get('conditions');
            foreach($conditions as $condition) {
                if($condition['fieldname'] == $fieldName) {
                    $fieldModel->set('fieldvalue', $condition['value']);
                }
            }
        }

        $viewer->assign('FIELD_MODEL', $fieldModel);
        $viewer->assign('MODULE', $module);
        $viewer->assign('FROM_WORKFLOWS', true);

        $templateView = $viewer->view($fieldModel->getUITypeModel()->getTemplateName(), $module, true);

        $response = new Vtiger_Response();
        $response->setResult($templateView);
        $response->emit();
    }
}
