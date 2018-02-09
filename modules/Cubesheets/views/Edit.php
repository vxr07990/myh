<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Cubesheets_Edit_View extends Inventory_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray, $hiddenBlocksArrayField;
        $viewer       = $this->getViewer($request);
        $moduleName   = $request->getModule();
        $record       = $request->get('record');
        $sourceRecord = $request->get('sourceRecord');
        $sourceModule = $request->get('sourceModule');
        if (!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = Inventory_Record_Model::getInstanceById($record, $moduleName);
            //$currencyInfo = $recordModel->getCurrencyInfo();
            //$taxes = $recordModel->getProductTaxes();
            //$shippingTaxes = $recordModel->getShippingTaxes();
            //$relatedProducts = $recordModel->getProducts();
            $viewer->assign('MODE', '');
        } elseif (!empty($record)) {
            $recordModel = Inventory_Record_Model::getInstanceById($record, $moduleName);
            //$currencyInfo = $recordModel->getCurrencyInfo();
            //$taxes = $recordModel->getProductTaxes();
            //$shippingTaxes = $recordModel->getShippingTaxes();
            //$relatedProducts = $recordModel->getProducts();
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } elseif ($request->get('salesorder_id') || $request->get('quote_id')) {
            if ($request->get('salesorder_id')) {
                $referenceId = $request->get('salesorder_id');
            } else {
                $referenceId = $request->get('quote_id');
            }
            $parentRecordModel = Inventory_Record_Model::getInstanceById($referenceId);
            //$currencyInfo = $parentRecordModel->getCurrencyInfo();
            //$taxes = $parentRecordModel->getProductTaxes();
            //$shippingTaxes = $parentRecordModel->getShippingTaxes();
            //$relatedProducts = $parentRecordModel->getProducts();
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $recordModel->setRecordFieldValues($parentRecordModel);
            $sourceRecord = $referenceId;
            $sourceModule = $parentRecordModel->getModuleName();
        } else {
            //$taxes = Inventory_Module_Model::getAllProductTaxes();
            //$shippingTaxes = Inventory_Module_Model::getAllShippingTaxes();
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
            //The creation of Inventory record from action and Related list of product/service detailview the product/service details will calculated by following code
            if ($request->get('product_id') || $sourceModule === 'Products') {
                if ($sourceRecord) {
                    $productRecordModel = Products_Record_Model::getInstanceById($sourceRecord);
                } else {
                    $productRecordModel = Products_Record_Model::getInstanceById($request->get('product_id'));
                }
                $relatedProducts = $productRecordModel->getDetailsForInventoryModule($recordModel);
            } elseif ($request->get('service_id') || $sourceModule === 'Services') {
                if ($sourceRecord) {
                    $serviceRecordModel = Services_Record_Model::getInstanceById($sourceRecord);
                } else {
                    $serviceRecordModel = Services_Record_Model::getInstanceById($request->get('service_id'));
                }
                $relatedProducts = $serviceRecordModel->getDetailsForInventoryModule($recordModel);
            } elseif ($sourceRecord && ($sourceModule === 'Accounts'
                                        || $sourceModule === 'Contacts'
                                        || $sourceModule === 'Potentials'
                                        || $sourceModule === 'Project'
                                        || ($sourceModule === 'Vendors' && $moduleName === 'PurchaseOrder')
                                        || $sourceModule === 'Orders')
            ) {
                $parentRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
                $recordModel->setParentRecordData($parentRecordModel);
            }
        }
        if (array_key_exists($moduleName, $hiddenBlocksArray)) {
            if ($sourceRecord) {
                $hiddenBlocks = $this->loadHiddenBlocksEditView($moduleName, $sourceRecord, $sourceModule);
            } else {
                $hiddenBlocks = $this->loadHiddenBlocksEditView($moduleName, $record, '');
            }
            $viewer->assign('HIDDEN_BLOCKS', $hiddenBlocks);
        } else {
            $blocksToHide = [];
            $viewer->assign('HIDDEN_BLOCKS', $blocksToHide);
        }
        $viewer->assign('BLFIELD', $hiddenBlocksArrayField[$moduleName]);
        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        //get the inventory terms and conditions
        $inventoryRecordModel = Inventory_Record_Model::getCleanInstance($moduleName);
        $termsAndConditions   = $inventoryRecordModel->getInventoryTermsandConditions();
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel = $fieldList[$fieldName];
            if ($fieldModel->isEditable()) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,
                                                                                            Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        //Convert survey_date, survey_time, and survey_end_time to current user's time zone
        foreach ($recordStructureInstance->getStructure() as $blockName => $blockFields) {
            $surveyTime = '';
            foreach ($blockFields as $fieldNameTest => $fieldModelTest) {
                if (($fieldNameTest === 'survey_time' || $fieldNameTest === 'survey_end_time') && $fieldModelTest->get('fieldvalue') !== '') {
                    $time = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue'))->format('H:i:s');
                    if ($fieldNameTest === 'survey_time') {
                        $surveyTime = $fieldModelTest->get('fieldvalue');
                    }
                    $fieldModelTest->set('fieldvalue', $time);
                }
                if ($fieldNameTest === 'survey_date' && $fieldModelTest->get('fieldvalue') !== '') {
                    if ($surveyTime === '') {
                        $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.$blockFields['survey_time']->get('fieldvalue'))->format('Y-m-d');
                    } else {
                        $date = DateTimeField::convertToUserTimeZone($fieldModelTest->get('fieldvalue').' '.$surveyTime)->format('Y-m-d');
                    }
                    $fieldModelTest->set('fieldvalue', $date);
                }
                if($fieldNameTest === 'survey_appointment_id') {
                    if($fieldModelTest->get('fieldvalue') == '' || $fieldModelTest->get('fieldvalue') == 0) {
                        $hasLinkedAppointment = false;
                    } else {
                        $hasLinkedAppointment = true;
                    }
                }
            }
        }
        //End Time Zone Conversion

        //Get current user model
        $userModel = Users_Record_Model::getCurrentUserModel();

        //Get all available tariffs
        $data = Estimates_Record_Model::getAllowedTariffsForUser();
        $viewer->assign('AVAILABLE_TARIFFS',Vtiger_Util_Helper::toSafeHTML(json_encode($data)));

        if($record && $recordModel) {
            $sourceRecord = $recordModel->get('potential_id');
            $sourceModule = 'Opportunities';

            //@TODO: Consider for Orders and such later.
        }

        if (!empty($sourceRecord) && $parentRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule)) {
            $viewer->assign('MOVE_TYPE', $parentRecordModel->get('business_line'));
        }

        $viewer->assign('VIEW_MODE', "fullForm");
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $sourceModule);
            $viewer->assign('SOURCE_RECORD', $sourceRecord);
        }
        if (!empty($record) && $request->get('isDuplicate') == true) {
            $viewer->assign('IS_DUPLICATE', true);
        } else {
            $viewer->assign('IS_DUPLICATE', false);
        }
        $currencies                   = Inventory_Module_Model::getAllCurrencies();
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', $userModel);
        $viewer->assign('RELATED_PRODUCTS', $relatedProducts);
        $viewer->assign('SHIPPING_TAXES', $shippingTaxes);
        $viewer->assign('TAXES', $taxes);
        $viewer->assign('CURRENCINFO', $currencyInfo);
        $viewer->assign('CURRENCIES', $currencies);
        $viewer->assign('TERMSANDCONDITIONS', $termsAndConditions);
        $productModuleModel = Vtiger_Module_Model::getInstance('Products');
        $viewer->assign('PRODUCT_ACTIVE', $productModuleModel->isActive());
        $serviceModuleModel = Vtiger_Module_Model::getInstance('Services');
        $viewer->assign('SERVICE_ACTIVE', $serviceModuleModel->isActive());
        $viewer->assign('HAS_LINKED_APPOINTMENT', $hasLinkedAppointment);
        $viewer->view('EditView.tpl', 'Cubesheets');
    }
}
