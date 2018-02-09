<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OrdersTask_Edit_View extends Vtiger_Edit_View
{
    protected $record = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record     = $request->get('record');
        if ($record != null) {
            $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);
            if (!$recordPermission || !$recordModel->isTaskEditable()) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
            }
        }
    }

    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $record = $request->get('record');

        $viewer     = $this->getViewer($request);
        $moduleName = $request->getModule();
        $record     = $request->get('record');
        if (!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
            //While Duplicating record, If the related record is deleted then we are removing related record info in record model
            $mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
            foreach ($mandatoryFieldModels as $fieldModel) {
                if ($fieldModel->isReferenceField()) {
                    $fieldName = $fieldModel->get('name');
                    if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
                        $recordModel->set($fieldName, '');
                    }
                }
            }
        } elseif ($request->get('sourceModule') == 'Orders') {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);

            $ordersId = $request->get('ordersid');
            $recordModel->set('ordersid', $ordersId);
            $viewer->assign('ORDERS_ID', $ordersId);

            $ordersRecordModel = Vtiger_Record_Model::getInstanceById($ordersId, 'Orders');

            $recordModel->set('orderstask_business_line', $ordersRecordModel->get('business_line'));
            $recordModel->set('commodities', $ordersRecordModel->get('commodities'));
            // doing this because I don't know why the field is supposed to have a different name, and I don't know why it doesn't
            $recordModel->set('business_line', $ordersRecordModel->get('business_line'));
            if($ordersRecordModel->get('business_line2'))
            {
                $recordModel->set('business_line', $ordersRecordModel->get('business_line2'));
            }
        } elseif (!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
            $ordersId = $recordModel->get('ordersid');
            $viewer->assign('ORDERS_ID', $ordersId);
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }

        if (!$this->record) {
            $this->record = $recordModel;
        }

        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel   = $fieldList[$fieldName];
            $specialField = false;

            if ($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance      = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        /* VGS Global Business Line Blocks */
        if (!empty($record) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, $record);
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        } elseif (empty($record) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $blocksToHide = $this->loadHiddenBlocksEditView($moduleName, '');
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        } else {
            $blocksToHide = [];
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        }
        global $hiddenBlocksArrayField;
        $viewer->assign('BLFIELD', $hiddenBlocksArrayField[$moduleName]);
        /* VGS Global Business Line Blocks */
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_MODEL', $recordModel);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
            //pull contact from the related thing.
            //OT 13379
            $sourceRecord = $request->get('sourceRecord');
            $sourceModule = $request->get('sourceModule');
            if ($moduleName == 'Calendar') {
                if ($sourceRecord) {
                    if ($sourceModule == 'Opportunities' || $sourceModule == 'Potentials') {
                        $sourceRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
                        $sourceContactId = $sourceRecordModel->get('contact_id');
                        $existingRelatedContacts[] = array(
                            'name' => Vtiger_Util_Helper::getRecordName($sourceContactId),
                            'id' => $sourceContactId
                        );
                    }
                }
            }
        }

        //Addresses blocks
        $OrdersTaskAddresses = Vtiger_Module_Model::getInstance('OrdersTaskAddresses');
        if($OrdersTaskAddresses && $OrdersTaskAddresses->isActive()){
            $OrdersTaskAddresses->assignValueForOrdersTaskAddresses($viewer,$record);
        }

        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->assign('MODULE_QUICKCREATE_RESTRICTIONS',['Equipment'] );

        $viewer->view('EditView.tpl', $moduleName);
    }
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $jsFileNames           = [
            "modules.$moduleName.resources.EditExtraBlock",
			"modules.$moduleName.resources.Edit",
        ];
        $OrdersTaskAddresses = Vtiger_Module_Model::getInstance('OrdersTaskAddresses');
        if($OrdersTaskAddresses && $OrdersTaskAddresses->isActive()){
            $jsFileNames[] = "modules.OrdersTaskAddresses.resources.EditBlock";
        }
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
