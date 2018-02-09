<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class PicklistCustomizer_IndexAjax_View extends Settings_Picklist_IndexAjax_View {

    public function checkPermission(Vtiger_Request $request)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        if(!$currentUser->isAdminUser() && !$currentUser->isVanlineUser()){
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }

        return true;
    }

    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('getDependencyGraph');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public function getPickListValueForField(Vtiger_Request $request) {
        $sourceModule = $request->get('source_module');
        $pickFieldId = $request->get('pickListFieldId');
        $idAgentManager = $request->get('idAgentManager');
        $pickListFieldName= $request->get('pickListFieldName');
//        $result = $this->getPicklistResultObject($pickListFieldName,$sourceModule);
//        $pickFieldId = $result->fields['fieldid'];

        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickFieldId);

		$moduleName = $request->getModule();
        $qualifiedName = $request->getModule(false);

        $picklistValues = Vtiger_Util_Helper::getCustomPicklistValues($fieldModel->getName(),$fieldModel->getId(), $idAgentManager);
        $targetFieldOptions = PicklistCustomizer_Module_Model::getFieldsForModule($sourceModule,false);

        $viewer = $this->getViewer($request);
        $viewer->assign('SELECTED_PICKLIST_FIELDMODEL',$fieldModel);
        $viewer->assign('SELECTED_MODULE_NAME',$sourceModule);
        $viewer->assign('MODULE',$moduleName);
        $viewer->assign('AGENT_MANAGER_ID',$idAgentManager);
        $viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        $viewer->assign('ROLES_LIST', Settings_Roles_Record_Model::getAll());
        $viewer->assign('SELECTED_PICKLISTFIELD_ALL_VALUES',$picklistValues);
        $viewer->assign('TARGET_FIELD_OPTIONS',$targetFieldOptions);
        $viewer->assign('FIELD_ID',$pickFieldId);
        $viewer->view('PickListValueDetail.tpl',$qualifiedName);
    }

    public function showEditView(Vtiger_Request $request) {
        $module = $request->get('source_module');
        $pickListFieldId = $request->get('pickListFieldId');
        $idAgentManager = $request->get('idAgentManager');

        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListFieldId);
        $valueToEdit = $request->getRaw('fieldValue');

        //	$selectedFieldAllPickListValues =  array_map('Vtiger_Util_Helper::toSafeHTML', $selectedFieldAllPickListValues);
        $selectedFieldEditablePickListValues = Vtiger_Util_Helper::getCustomPicklistValues($fieldModel->getName(), $fieldModel->getId(), $idAgentManager);
        foreach ($selectedFieldEditablePickListValues as $id => $value) {
            if($valueToEdit == $value){
                $valueToEditId = $id;
            }
        }
        $qualifiedName = $request->getModule(false);
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $viewer->assign('SOURCE_MODULE', $module);
        $viewer->assign('SOURCE_MODULE_NAME',$module);
        $viewer->assign('FIELD_MODEL',$fieldModel);
        $viewer->assign('FIELD_VALUE',$valueToEdit);
        $viewer->assign('FIELD_VALUE_ID',$valueToEditId);
        $viewer->assign('SELECTED_PICKLISTFIELD_EDITABLE_VALUES',$selectedFieldEditablePickListValues);
        $viewer->assign('SELECTED_PICKLISTFIELD_NON_EDITABLE_VALUES',$selectedFieldNonEditablePickListValues);
        $viewer->assign('MODULE',$moduleName);
        $viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        echo $viewer->view('EditView.tpl', $qualifiedName, true);
    }

    public function showDeleteView(Vtiger_Request $request) {

        $module = $request->get('source_module');
        $pickListFieldId = $request->get('pickListFieldId');
        $fieldModel = Settings_Picklist_Field_Model::getInstance($pickListFieldId);
        $valueToDelete = $request->get('fieldValue')[0];
        $idAgentManager = $request->get('idAgentManager');


        $selectedFieldEditablePickListValues = Vtiger_Util_Helper::getCustomPicklistValues($fieldModel->getName(), $fieldModel->getId(), $idAgentManager);
//        $selectedFieldEditablePickListValues =  array_map('Vtiger_Util_Helper::toSafeHTML', $selectedFieldEditablePickListValues);

        $valuesForReplacement = $selectedFieldEditablePickListValues;


        if(!empty($selectedFieldNonEditablePickListValues)) {
            $selectedFieldNonEditablePickListValues =  array_map('Vtiger_Util_Helper::toSafeHTML', $selectedFieldNonEditablePickListValues);
        }


        foreach ($selectedFieldEditablePickListValues as $id => $value) {
            if($valueToDelete == $value){
                $valueToDeleteId = $id;
            }
        }

        unset($valuesForReplacement[$valueToDeleteId]);

        $qualifiedName = $request->getModule(false);
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $viewer->assign('SOURCE_MODULE', $module);
        $viewer->assign('SOURCE_MODULE_NAME',$module);
        $viewer->assign('FIELD_MODEL',$fieldModel);

        $viewer->assign('MODULE',$moduleName);
        $viewer->assign('QUALIFIED_MODULE',$qualifiedName);
        $viewer->assign('AGENT_MANAGER_ID',$idAgentManager);
        $viewer->assign('SELECTED_PICKLISTFIELD_EDITABLE_VALUES',$selectedFieldEditablePickListValues);
        $viewer->assign('PICKLIST_VALUES_REPLACEMENT',$valuesForReplacement);
        $viewer->assign('FIELD_VALUE',$valueToDeleteId);
        $viewer->assign('FIELD_VALUE_HTML',Vtiger_Util_Helper::toSafeHTML($valueToDelete));
        $viewer->assign('FIELD_ID',$pickListFieldId);

        echo $viewer->view('DeleteView.tpl', $qualifiedName, true);
    }

    public function getDependencyGraph(Vtiger_Request $request)
    {
        $qualifiedName = $request->getModule(false);
        $module = $request->get('sourceModule');
        $sourceField = $request->get('sourcefield');
        $targetField = $request->get('targetfield');
        $agentManagerId = $request->get('agentmanagerid');
        $recordModel = Settings_PickListDependency_Record_Model::getInstance($module, $sourceField, $targetField);
        $valueMapping = $recordModel->getPickListDependency($agentManagerId);
        $nonMappedSourceValues = $recordModel->getNonMappedSourcePickListValues();

        $viewer = $this->getViewer($request);
        $viewer->assign('MAPPED_VALUES', $valueMapping);
        $viewer->assign('SOURCE_PICKLIST_VALUES', Vtiger_Util_Helper::getPickListValues($sourceField, true));
        $viewer->assign('TARGET_PICKLIST_VALUES', Vtiger_Util_Helper::getPickListValues($targetField, true));
        $viewer->assign('NON_MAPPED_SOURCE_VALUES', $nonMappedSourceValues);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedName);
        $viewer->assign('RECORD_MODEL', $recordModel);

        return $viewer->view('DependencyGraph.tpl', $qualifiedName, true);
    }

    //@TODO: unused
    protected function getPicklistResultObject($picklistTabname, $module = false)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT fieldid FROM `vtiger_field` JOIN `vtiger_tab` ON `vtiger_tab`.tabid=`vtiger_field`.tabid WHERE `vtiger_tab`.`name`=? AND fieldname=?";
        if($module)
        {
            $result = $db->pquery($sql, [$module, $picklistTabname]);
        }
        else{
            $result = $db->pquery($sql, ['Leads', $picklistTabname]);
        }
        return $result;
    }
}
