<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger Summary View Record Structure Model
 */
class Orders_SummaryRecordStructure_Model extends Vtiger_DetailRecordStructure_Model
{

    /**
     * Function to get the values in stuctured format
     * @return <array> - values in structure array('block'=>array(fieldinfo));
     */
    public function getStructure()
    {
        $summaryFieldsList = $this->getModule()->getSummaryViewFieldsList();
        $recordModel = $this->getRecord();
        $blockSeqSortSummaryFields = array();
        if ($summaryFieldsList) {
            foreach ($summaryFieldsList as $fieldName => $fieldModel) {
                if (isset($this->record->hiddenBlocks) && in_array($fieldModel->block->label, $this->record->hiddenBlocks)) {
                    continue;
                }
                                        
                if ($fieldModel->isViewableInDetailView()) {
					$fieldValue = ($fieldName == "origin_zone" || $fieldName == "empty_zone") ? $this->getDisplayVal($recordModel->get($fieldName)) : $recordModel->get($fieldName);

                    $fieldModel->set('fieldvalue', $fieldValue);
                    $blockSequence = $fieldModel->block->sequence;
                    $blockSeqSortSummaryFields[$blockSequence]['SUMMARY_FIELDS'][$fieldName] = $fieldModel;
                }
            }
        }
        $summaryFieldModelsList = array();
        ksort($blockSeqSortSummaryFields);
        foreach ($blockSeqSortSummaryFields as $blockSequence => $summaryFields) {
            $summaryFieldModelsList = array_merge_recursive($summaryFieldModelsList, $summaryFields);
        }
        return $summaryFieldModelsList;
    }
	function getDisplayVal($zoneAdminID){
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT za_zone FROM vtiger_zoneadmin WHERE zoneadminid = ?",array($zoneAdminID));
		
		if($db->num_rows($result)){
			$returnVar = $db->query_result($result, 0, "za_zone");
		}else{
			$returnVar = "";
		}
		return $returnVar;
	}
}
