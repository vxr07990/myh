<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_QuickCreateAjax_View extends Vtiger_IndexAjax_View
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        if (!(Users_Privileges_Model::isPermitted($moduleName, 'EditView'))) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
    }

    public function process(Vtiger_Request $request)
    {
        // if this is a relation operation, pull the owner field
        $isRelationOperation = $request->get('relationOperation');
        $sourceModule = $request->get('sourceModule');
        $sourceRecord = $request->get('sourceRecord');
        if ($isRelationOperation && $sourceModule && $sourceRecord) {
            $src = Vtiger_Record_Model::getInstanceById($sourceRecord);
            if ($src) {
                $owner = $src->get('agentid');
                $request->set('agentid', $owner);
            }
        }
        $moduleName       = $request->getModule();
        $recordModel      = Vtiger_Record_Model::getCleanInstance($moduleName);
        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel = $fieldList[$fieldName];
            if ($fieldModel->isEditable()) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance      = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_QUICKCREATE);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $structuredValues             = $recordStructureInstance->getStructure();
        // @TODO: See if this works for other modules as well.
        if($moduleName == 'Contacts') {
            usort($structuredValues,
                function ($f1, $f2) {
                    return $f1->get('quicksequence') > $f2->get('quicksequence');
                });
        }
        //logic for including/discluding sirva specific fields
        if (getenv('INSTANCE_NAME') == 'sirva') {
            unset($structuredValues['primary_phone_ext']);
            unset($structuredValues['days_to_move']);
            unset($structuredValues['origin_phone1_ext']);
            unset($structuredValues['origin_phone2_ext']);
            unset($structuredValues['destination_phone1_ext']);
            unset($structuredValues['destination_phone2_ext']);
            unset($structuredValues['origin_phone1_type']);
            unset($structuredValues['origin_phone2_type']);
            unset($structuredValues['destination_phone1_type']);
            unset($structuredValues['destination_phone2_type']);
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('SINGLE_MODULE', 'SINGLE_'.$moduleName);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('SCRIPTS', $this->getHeaderScripts($request));
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        echo $viewer->view('QuickCreate.tpl', $moduleName, true);
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $moduleName        = $request->getModule();
        $jsFileNames       = [
            "modules.$moduleName.resources.Edit",
        ];
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);

        return $jsScriptInstances;
    }

    public function validateRequest(Vtiger_Request $request)
    {
        $request->validateWriteAccess();
    }
}
