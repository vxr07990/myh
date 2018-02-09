<?php

class Contracts_Detail_View extends Vtiger_Detail_View
{
    public function showModuleDetailView(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ')."Entering showModuleDetailView function\n", FILE_APPEND);
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel    = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
            $recordModel->hiddenBlocks = $hiddenBlocks;
        }
        $structuredValues = $recordStrucure->getStructure();
        $moduleModel      = $recordModel->getModule();
        $viewer           = $this->getViewer($request);

        $blocks = $moduleModel->getBlocks();

        $isParent = false;
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $parentContractFieldModel = Vtiger_Field_Model::getInstance('parent_contract', $recordModel->getModule());
            $parent_contract = $parentContractFieldModel->get('fieldvalue');
            if ($parent_contract == '--' || !$parent_contract) {
                //we're a parent yeah!
                $isParent   = true;
                $tempBlocks = [];
                foreach ($blocks as $blockName => $block) {
                    $label = $block->get('label');
                    if (
                        $label == 'LBL_CONTRACTS_ADMINISTRATIVE' ||
                        $label == 'LBL_CONTRACTS_INFORMATION' ||
                        $label == 'LBL_CONTRACTS_ADMIN'
                    ) {
                        $tempBlocks[$blockName] = $block;
                    }
                }
                $blocks      = $tempBlocks;
                $moduleModel = $recordModel->getModule();
                $fieldList   = $moduleModel->getFields();
                $fieldList['move_type']->set('presence', 1);
            }
        }

        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('IS_PARENT', $isParent);
        $viewer->assign('BLOCK_LIST', $blocks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        //Doubled up because some things expected MODULE instead of MODULE_NAME
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('MISC_CHARGES', $recordModel->getMiscCharges($request));

        $viewer->assign('FUEL_TABLE', $recordModel->getFuelLookupTable());

        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('ASSIGNED_RECORDS', $recordModel->getAssignedRecords());
        $vanlineRecords = VanlineManager_Module_Model::getAllRecords();
        $viewer->assign('VANLINES', $vanlineRecords);
        $viewer->assign('AGENTS', AgentManager_Module_Model::getAllRecords());
        $vanlineNames = [];
        foreach ($vanlineRecords as $vanlineRecord) {
            $vanlineNames[$vanlineRecord->get('id')] = $vanlineRecord->get('vanline_name');
        }
        $viewer->assign('VANLINE_NAMES', $vanlineNames);
        /*-----------------------------Grab annual rate increases---------------------------------*/
        $db                 = PearDatabase::getInstance();
        $annualRateIncrease = [];
        $result             = $db->pquery('SELECT * FROM `vtiger_annual_rate` WHERE contractid = ?', [$recordId]);
        $row                = $result->fetchRow();
        while ($row != null) {
            $annualRateIncrease[] = $row;
            //file_put_contents('logs/devLog.log', "\n ROW: ".print_r($row, true), FILE_APPEND);
            $row = $result->fetchRow();
        }
        $viewer->assign('ANNUAL_RATES', $annualRateIncrease);
        /*-----------------------------End annual rate increases----------------------------------*/
        /*-----------------------------Grab fuel table---------------------------------*/
        //@TODO: why is this conditionalized in edit and detail view but not save_entitiy?
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $fuelTable = [];
            $result    = $db->pquery('SELECT * FROM `vtiger_contractfuel` WHERE contractid = ?', [$recordId]);
            $row       = $result->fetchRow();
            while ($row != null) {
                $fuelTable[] = $row;
                //file_put_contents('logs/devLog.log', "\n ROW: ".print_r($row, true), FILE_APPEND);
                $row = $result->fetchRow();
            }
            $viewer->assign('FUEL_TABLE', $fuelTable);
            $subAgreementLabel = strrpos($request->get('tab_label'), 'Sub-');
            if ($subAgreementLabel !== false || $recordModel->get('parent_contract')) {
                $viewer->assign('SUB', true);
            } else {
                $viewer->assign('SUB', false);
            }
        }
        /*-----------------------------End fuel table----------------------------------*/
        /*-----------------------BEGIN tabled Flat Auto rate----------------------------*/
        if (getenv('INSTANCE_NAME') != 'sirva') {
            //Flat Rate Auto table
            $sql    = "SELECT * FROM `vtiger_contract_flat_rate_auto` WHERE `contractid` =?";
            $result    = $db->pquery($sql, [$recordId]);
            $row       = $result->fetchRow();
            while ($row != null) {
                $flatRateAutoTable[] = $row;
                $row = $result->fetchRow();
            }
            $viewer->assign('FLAT_RATE_AUTO_TABLE', $flatRateAutoTable);
        }
        /*-----------------------End tabled Flat Auto rate----------------------------*/
        $vanlineOwner = false;
        $db           = PearDatabase::getInstance();
        $sql          = "SELECT vtiger_groups.grouptype FROM `vtiger_crmentity` INNER JOIN `vtiger_groups` ON vtiger_crmentity.smownerid = vtiger_groups.groupid WHERE vtiger_crmentity.crmid = ?";
        $result       = $db->pquery($sql, [$recordId]);
        $row          = $result->fetchRow();
        if ($row[0] == 1) {
            $vanlineOwner = true;
        }
        $viewer->assign('VANLINE_OWNER', $vanlineOwner);

        //file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ')."Preparing to view template file\n", FILE_APPEND);
        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }
}
