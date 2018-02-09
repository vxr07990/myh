<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Surveys_Edit_View extends Inventory_Edit_View
{
    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray, $hiddenBlocksArrayField;
        $viewer       = $this->getViewer($request);
        $moduleName   = $request->getModule();
        $record       = $request->get('record');
        $sourceRecord = $request->get('sourceRecord');
        $sourceModule = $request->get('sourceModule');

        //$this->updateTimeFormValues($request, ['survey_time', 'survey_end_time']);

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
                                        || $sourceModule === 'Opportunities'
                                        || ($sourceModule === 'Vendors' && $moduleName === 'PurchaseOrder'))
            ) {
                $parentRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
                $recordModel->setParentRecordData($parentRecordModel);
                if (($sourceModule === 'Potentials' || $sourceModule === 'Opportunities')) {
                    $sales_person = $parentRecordModel->get('sales_person');
                    if ($sales_person) {
                        $recordModel->set('assigned_user_id', $sales_person);
                    }
                    if($sourceModule === 'Opportunities') {
                        $recordModel->set('address_desc', $parentRecordModel->get('origin_description'));
                    }
                }
            } elseif ($sourceModule === 'Orders') {
                $parentRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
                $recordModel->set('account_id', $parentRecordModel->get('orders_account'));
                $recordModel->set('contact_id', $parentRecordModel->get('orders_contacts'));
            
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

        if (!$this->record) {
            $this->record = $recordModel;
        }

        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel = $fieldList[$fieldName];
            if ($fieldModel->isEditable()) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        // OT18731 Survey Appt is Not Pulling in Origin Address
        // I put this here beacause for some reason if I put it down below the recordModel fields are not completed with the data
        if ($request->get('relationOperation') == 'true' && $sourceModule == 'Orders' && $request->get('order_id') && $request->get('order_id') != '') {
                $orderRecord = Vtiger_Record_Model::getInstanceById($request->get('order_id'), $sourceModule);
                $arr = [
                    'address1' => 'origin_address1',
                    'address2' => 'origin_address2',
                    'city' => 'origin_city',
                    'state' => 'origin_state',
                    'zip' => 'origin_zip',
                    'country' => 'origin_country',
                    'phone1' => 'origin_phone1',
                    'phone2' => 'origin_phone2',
                    'address_desc' => 'origin_description'
                ];
                foreach ($arr as $surveyFieldName => $orderFieldName) {
                    $recordModel->set($surveyFieldName, $orderRecord->get($orderFieldName));
                }
        }
        // End OT18731
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,
                                                                                            Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        //Convert survey_date, survey_time, and survey_end_time to current user's time zone
        //@NOTE: This should happen even if there is no "record" because an empty one is built above
        $this->convertSurveyTimeFormat($recordStructureInstance->getStructure());
        //End Time Zone Conversion
        $viewer->assign('VIEW_MODE', "fullForm");
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $sourceModule);
            $viewer->assign('SOURCE_RECORD', $sourceRecord);
            if ($sourceModule == 'Opportunities') {
                $db     = PearDatabase::getInstance();
                $sql    = "SELECT potentialname FROM `vtiger_potential` WHERE potentialid = ?";
                $result = $db->pquery($sql, [$sourceRecord]);
                $row    = $result->fetchRow();
                $viewer->assign('OPP_NAME', $row[0]);
            }
        }
        if (!empty($record) && $request->get('isDuplicate') == true) {
            $viewer->assign('IS_DUPLICATE', true);
        } else {
            $viewer->assign('IS_DUPLICATE', false);
        }
        $currencies                   = Inventory_Module_Model::getAllCurrencies();
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $userSettings = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('TIME_FORMAT', $userSettings->get('hour_format'));
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

        //@TODO: What is this for?
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
        //@TODO: maybe all this above can be removed?

        $viewer->view('EditView.tpl', 'Surveys');
    }

    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        //file_put_contents('logs/devLog.log', "\n This is getting called from : $moduleName", FILE_APPEND);
        //Added to remove the module specific js, as they depend on inventory files
        $modulePopUpFile  = 'modules.'.$moduleName.'.resources.Popup';
        $moduleEditFile   = 'modules.'.$moduleName.'.resources.Edit';
        $moduleDetailFile = 'modules.'.$moduleName.'.resources.Detail';
        unset($headerScriptInstances[$modulePopUpFile]);
        unset($headerScriptInstances[$moduleEditFile]);
        unset($headerScriptInstances[$moduleDetailFile]);
        $jsFileNames           = [
            "modules.Quotes.resources.Edit",
            "modules.Estimates.resources.Edit",
            "modules.$moduleName.resources.Edit",
        ];
        $jsFileNames[]         = $moduleEditFile;
        $jsFileNames[]         = $modulePopUpFile;
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        //file_put_contents('logs/devLog.log', "\n \$headerScriptInstances : ".print_r($headerScriptInstances,true), FILE_APPEND);
        return $headerScriptInstances;
    }

    private function updateTimeFormValues(Vtiger_Request $request, $timeFields) {
        if (!is_array($timeFields)) {
            return;
        }

        foreach ($timeFields as $field) {
            $correctedTime = $this->checkTimeFormValues($request->get($field),$request->get('timefield_'.$field));
            $request->setGlobal($field, $correctedTime);
        }
    }

    private function checkTimeFormValues($fieldValue, $possibleTimezone) {
        if (preg_match('/[ap]m/i', $fieldValue, $matches)) {
            $time24value = $this->reverseSurveyTime($fieldValue, strtolower($matches[0]), $possibleTimezone);
            if (!$time24value) {
                return date('H:i:s');
            } else {
                return $time24value;
            }
        }
        return $fieldValue;
    }

}
