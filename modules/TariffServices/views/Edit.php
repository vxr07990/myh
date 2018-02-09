<?php

class TariffServices_Edit_View extends Vtiger_Edit_View
{

    /**
     * Does the processing, that assigns variables to the TPL for TariffServices Edit View
     *
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $viewer       = $this->getViewer($request);
        $moduleName   = $request->getModule();
        $record       = $request->get('record');
        $sourceRecord = $request->get('sourceRecord');
        $sourceModule = $request->get('sourceModule');
        if ($sourceModule == 'EffectiveDates')
        {
            $recordModelOwner = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
            $Owner = $recordModelOwner->get('agentid');
            $request->set('agentid',$Owner);
        }

        if (!empty($record) && $request->get('isDuplicate') == true) {
            $removeLineItemIds = true;
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
            $viewer->assign('DEFAULTCOUNTIES', $recordModel->getDefaultCountiesExistingRecord());
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
        } elseif (!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
            $viewer->assign('DEFAULTCOUNTIES', $recordModel->getDefaultCountiesExistingRecord());
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
            if ($sourceRecord) {
                $parentRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord, $sourceModule);
                $recordModel->setParentRecordData($parentRecordModel);
                $viewer->assign('DEFAULTCOUNTIES', $recordModel->getDefaultCounties($parentRecordModel->get('related_tariff')));
            }
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }

        $itemsArray['baseplus']          = $recordModel->getEntries('baseplus');
        $itemsArray['breakpoint']        = $recordModel->getEntries('breakpoint');
        $itemsArray['weightmileage']     = $recordModel->getEntries('weightmileage');
        $itemsArray['servicebasecharge'] = $recordModel->getEntries('servicebasecharge');
        $itemsArray['cwtbyweight']       = $recordModel->getEntries('cwtbyweight');
        $itemsArray['bulky']             = $recordModel->getEntries('bulky');
        $itemsArray['flatratebyweight']  = $recordModel->getEntries('flatratebyweight');
        $itemsArray['chargeperhundred']  = $recordModel->getEntries('chargeperhundred');
        $itemsArray['countycharge']      = $recordModel->getEntries('countycharge');
        $itemsArray['hourlyset']         = $recordModel->getEntries('hourlyset');
        $itemsArray['packingitems']      = $recordModel->getEntries('packingitems');
        $itemsArray['valuations']        = $recordModel->getEntries('valuations');

        if($removeLineItemIds) {
            foreach($itemsArray as &$itemArray) {
                foreach ($itemArray as &$item) {
                    unset($item['line_item_id']);
                }
            }
        }

        $viewer->assign('RELEASED_VALUATION', ["has_released"=>$recordModel->get('valuation_released'),"released_amount"=>$recordModel->get('valuation_releasedamount')]);
        $viewer->assign('EFFECTIVE_DATE_ID', $request->get('sourceRecord'));
        $viewer->assign('BASEPLUS', $itemsArray['baseplus']);
        $viewer->assign('BREAKPOINT', $itemsArray['breakpoint']);
        $viewer->assign('WEIGHTMILEAGE', $itemsArray['weightmileage']);
        $viewer->assign('SERVICECHARGEMATRIX', $recordModel->get('service_base_charge_matrix'));
        $viewer->assign('SERVICECHARGE', $itemsArray['servicebasecharge']);
        $viewer->assign('CWTBYWEIGHT', $itemsArray['cwtbyweight']);
        //$viewer->assign('DEFAULTBULKIES', $recordModel->getDefaultBulkies());
        $viewer->assign('BULKYITEMS', $itemsArray['bulky']);
        $viewer->assign('FLATRATEBYWEIGHT', $itemsArray['flatratebyweight']);
        $viewer->assign('CHARGESPERHUNDRED', $itemsArray['chargeperhundred']);
        $viewer->assign('COUNTYCHARGES', $itemsArray['countycharge']);
        $viewer->assign('HOURLYSET', $itemsArray['hourlyset']);
        $viewer->assign('HASVANS', $recordModel->hasCheck('hourlyset_hasvan'));
        $viewer->assign('HASCONTAINER', $recordModel->hasCheck('packing_containers'));
        $viewer->assign('HASPACKING', $recordModel->hasCheck('packing_haspacking'));
        $viewer->assign('HASUNPACKING', $recordModel->hasCheck('packing_hasunpacking'));
        //$viewer->assign('DEFAULTPACKING', $recordModel->getDefaultPacking());
        $viewer->assign('PACKINGITEMS', $itemsArray['packingitems']);
        $viewer->assign('VALUATIONITEMS', $itemsArray['valuations']);
        // $viewer->assign('DEDUCTIBLES', $recordModel->getDistinct('deductible'));
        // $viewer->assign('VALAMOUNTS', $recordModel->getDistinct('amount'));
        //assemble amounts & deductibles
        $amountRows     = $recordModel->getDistinct('amount_row');
        $deductibleRows = $recordModel->getDistinct('deductible_row');
        $usedRows       = [];
        $rowRelation    = [];
        $amounts        = [];
        $deductibles    = [];
        $db             = PearDatabase::getInstance();
        if ($amountRows) {
            foreach ($amountRows as $currentRow) {
                if (!in_array($currentRow, $usedRows)) {
                    $sql                      = 'SELECT amount from `vtiger_tariffvaluations` WHERE serviceid=? AND amount_row = ?';
                    $result                   = $db->pquery($sql, [$recordModel->getId(), $currentRow]);
                    $row                      = $result->fetchRow();
                    $amounts[]                = $row[0];
                    $usedRows[]               = $currentRow;
                    $rowRelation[$currentRow] = count($amounts);
                }
            }
        }
        if ($deductibleRows) {
            foreach ($deductibleRows as $key => $currentRow) {
                if (!in_array($currentRow, $usedRows)) {
                    $sql                      = 'SELECT deductible from `vtiger_tariffvaluations` WHERE serviceid=? AND deductible_row = ?';
                    $result                   = $db->pquery($sql, [$recordModel->getId(), $currentRow]);
                    $row                      = $result->fetchRow();
                    $deductibles[]            = $row[0];
                    $usedRows[]               = $currentRow;
                    $rowRelation[$currentRow] = count($deductibles);
                }
            }
        }
        $viewer->assign('DEDUCTIBLES', $deductibles);
        $viewer->assign('VALAMOUNTS', $amounts);
        $viewer->assign('ROWRELATION', $rowRelation);
        //file_put_contents('logs/devLog.log', "\n row associations: ".print_r($rowRelation, true), FILE_APPEND);
        $moduleModel      = $recordModel->getModule();
        $fieldList        = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel   = $fieldList[$fieldName];
            $specialField = false;
            // We collate date and time part together in the EditView UI handling
            // so a bit of special treatment is required if we come from QuickCreate
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) {
                $specialField = true;
                // Convert the incoming user-picked time to GMT time
                // which will get re-translated based on user-time zone on EditForm
                $fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i");
            }
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) {
                $startTime     = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
                list($startDate, $startTime) = explode(' ', $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }
            if ($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }

        if (!$this->isDiscountSelectable($recordModel->get('tariff_section')) && $this->isField($fieldList['tariffservices_discountable'])) {
            $fieldList['tariffservices_discountable']->set('readonly', 0);
            $fieldList['tariffservices_discountable']->set('fieldvalue', 0);
            $recordModel->set('tariffservices_discountable', false);
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
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        /*-------------------- parent admin_only status ----------------------*/
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $db            = PearDatabase::getInstance();
            $srcRecord     = $request->get('sourceRecord');
            $sql           =
                "SELECT admin_access FROM `vtiger_tariffs` JOIN `vtiger_effectivedates` ON vtiger_effectivedates.related_tariff = vtiger_tariffs.tariffsid WHERE vtiger_effectivedates.effectivedatesid = ?";
            $result        = $db->pquery($sql, [$srcRecord]);
            $row           = $result->fetchRow();
            $parentIsAdmin = $row[0];
            $viewer->assign('PARENT_IS_ADMIN', $parentIsAdmin);
        }
        /*-------------------- END parent admin_only status ----------------------*/
        $isRelationOperation = $request->get('relationOperation');
        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->view('EditView.tpl', $moduleName);
    }

    private function isField($field) {
        return is_object($field) && $field instanceof Vtiger_Field_Model;
    }

    /**
     * return false if the section is NOT discountable
     *
     * @param $tariffSectionID
     *
     * @return bool
     */
    private function isDiscountSelectable($tariffSectionID) {
        if (!$tariffSectionID) {
            return true;
        }
        try {
            $recordModel = Vtiger_Record_Model::getInstanceById($tariffSectionID);
            if (!$recordModel) {
                return true;
            }
            if ($recordModel->getModuleName() != 'TariffSections') {
                return true;
            }
            if (!\MoveCrm\InputUtils::CheckboxToBool($recordModel->get('is_discountable'))) {
                return false;
            }
        } catch (Exception $e) {
            return true;
        }

        return true;
    }
}
