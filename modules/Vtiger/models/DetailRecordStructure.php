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
 * Vtiger Detail View Record Structure Model
 */
class Vtiger_DetailRecordStructure_Model extends Vtiger_RecordStructure_Model
{

    /**
     * Function to get the values in stuctured format
     * @return <array> - values in structure array('block'=>array(fieldinfo));
     */
    public function getStructure()
    {
        if (!empty($this->structuredValues)) {
            return $this->structuredValues;
        }

        $values = array();
        $recordModel = $this->getRecord();
        $recordExists = !empty($recordModel);
        $moduleModel = $this->getModule();
        $blockModelList = $moduleModel->getBlocks();
        foreach ($blockModelList as $blockLabel=>$blockModel) {
            if (isset($this->record->hiddenBlocks) && in_array($blockLabel, $this->record->hiddenBlocks)) {
                //continue;
                            $blockModel->set('hideblock', true);
            } else {
                $blockModel->set('hideblock', false);
            }
            $fieldModelList = $blockModel->getFields();
            //if (!empty ($fieldModelList)) {
                $values[$blockLabel] = array();
            foreach ($fieldModelList as $fieldName=>$fieldModel) {
                if (isset($this->record->hiddenFields) && in_array($fieldName, $this->record->hiddenFields)) {
                    continue;
                }
                if ($fieldModel->isViewableInDetailView()) {
                    if ($recordExists) {
                        if(strtolower($fieldName) == 'createdtime' || strtolower($fieldName) == 'modifiedtime') {
                            $fieldModel->set('fieldvalue', DateTimeField::convertToUserTimeZone($recordModel->get($fieldName))->format('Y-m-d H:i:s'));
                        } else {
                        $fieldModel->set('fieldvalue', $recordModel->get($fieldName));
                        }
                    }
                    $values[$blockLabel][$fieldName] = $fieldModel;
                }
            }
            //}
        }
        
        //file_put_contents('logs/BlockTest.log', print_r($values, true)."\n", FILE_APPEND);
        $this->structuredValues = $values;
        return $values;
    }
}
