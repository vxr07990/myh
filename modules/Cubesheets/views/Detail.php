<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Cubesheets_Detail_View extends Vtiger_Detail_View
{
    public function showModuleDetailView(Vtiger_Request $request)
    {
        global $hiddenBlocksArray;
        $recordId   = $request->get('record');
        $moduleName = $request->getModule();
        $sourceModule   = $request->get('sourceModule');
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel     = $this->record->getRecord();
        $recordStructure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        if (!empty($recordId) && array_key_exists($moduleName, $hiddenBlocksArray)) {
            $hiddenBlocks              = $this->loadHiddenBlocksDetailView($moduleName, $recordId);
            $recordModel->hiddenBlocks = $hiddenBlocks;
        }
        $structuredValues = $recordStructure->getStructure();
        $moduleModel      = $recordModel->getModule();
        //Convert survey_date, survey_time, and survey_end_time to current user's time zone
        foreach ($structuredValues as $blockName => $blockFields) {
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
                if ($fieldNameTest == 'survey_type') {
                    $surveyType = $fieldModelTest->get('fieldvalue');
                }
            }
        }
        //End Time Zone Conversion
        $userModel = Users_Record_Model::getCurrentUserModel();
        $tariffs = Estimates_Record_Model::getAllowedTariffsForUser();
        $viewer = $this->getViewer($request);
        $viewer->assign('TARIFFS',$tariffs);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORDID', $recordId);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', $userModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('DOMAIN', getenv('SITE_DOMAIN'));
        $viewer->assign('SURVEY_TYPE', $surveyType);
        $db     = PearDatabase::getInstance();
        $sql    =
            "SELECT vtiger_contactdetails.firstname, vtiger_contactdetails.lastname FROM `vtiger_contactdetails` JOIN `vtiger_cubesheets` ON vtiger_cubesheets.contact_id=vtiger_contactdetails.contactid WHERE cubesheetsid=?";
        $result = $db->pquery($sql, [$recordId]);
        $row    = $result->fetchRow();
        if ($row != null) {
            $viewer->assign('FIRSTNAME', $row[0]);
            $viewer->assign('LASTNAME', $row[1]);
        } else {
            $viewer->assign('FIRSTNAME', '');
            $viewer->assign('LASTNAME', '');
        }
        $recordId      = $request->get('record');
        $sql           = "SELECT potential_id FROM `vtiger_cubesheets` WHERE cubesheetsid=?";
        $result        = $db->pquery($sql, [$recordId]);
        $row           = $result->fetchRow();
        $potentialId   = $row[0];
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $sql           = "SELECT potentialname, preferred_language, move_type FROM `vtiger_potential` WHERE potentialid=?";
        }else{
            //@TODO - correct the below query to add a language field if customer language fields are added for cubesheets for other instances
            $sql           = "SELECT potentialname, business_line as move_type FROM `vtiger_potential` LEFT JOIN `vtiger_potentialscf` USING(`potentialid`) WHERE potentialid=?";
        }
        $result        = $db->pquery($sql, [$potentialId]);
        $row           = $result->fetchRow();
        $potentialName = $row['potentialname'];
        $potentialLink = '<a href="index.php?module=Opportunities&amp;view=Detail&amp;record='.$potentialId.'" data-original-title="Opportunities">'.$potentialName.'</a>';
        $viewer->assign('POTENTIAL_LINK', $potentialLink);
        $viewer->assign('POTENTIAL_LANGUAGE', rawurlencode($row['preferred_language']));
        $viewer->assign('POTENTIAL_MOVETYPE', rawurlencode($row['move_type']));

        return $viewer->view('DetailViewFullContents.tpl', $moduleName, true);
    }

    public function showModuleBasicView($request)
    {
        return $this->showModuleDetailView($request);
    }
}
