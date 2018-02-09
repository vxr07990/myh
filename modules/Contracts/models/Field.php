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
 * OpList Field Model Class
 */
class Contracts_Field_Model extends Vtiger_Field_Model
{
    public function getDisplayValue($value, $record = false, $recordInstance = false)
    {
        $fieldName = $this->getFieldName();
//        if (($fieldName == 'nat_account_no' || $fieldName == 'billing_apn') && $value && getenv('INSTANCE_NAME') == 'sirva') {
//            try {
//                //ensure it's an Accounts record the try will let it not error horribly.
//                $actRecord = Vtiger_Record_Model::getInstanceById($value, 'Accounts');
//                return $actRecord->get('apn');
//            } catch (Exception $e) {
//                //don't error just let it do what it wants.
//            }
//        } else
            if ($fieldName == 'related_tariff' && $value) {
            try {
                $interstateTariff = Vtiger_Record_Model::getInstanceById($value, 'TariffManager');
                $localTariff      = Vtiger_Record_Model::getInstanceById($value, 'Tariffs');

                return ($interstateTariff->get('tariffmanagername')?$interstateTariff->get('tariffmanagername'):$localTariff->get('tariff_name'));
                //thought I could return the right model... misunderstood.
                //return ($interstateTariff->get('tariffmanagername') ? $interstateTariff : $localTariff);
            } catch (Exception $e) {
                //don't error let it fall back on regular getDisplayValue
            }
        }

        return parent::getDisplayValue($value, $record, $recordInstance);
    }

    public function getEditViewDisplayValue($value)
    {
        $fieldName = $this->getFieldName();
//        if (($fieldName == 'nat_account_no' || $fieldName == 'billing_apn') && $value && getenv('INSTANCE_NAME') == 'sirva') {
//            try {
//                //ensure it's an Accounts record the try will let it not error horribly.
//                $actRecord = Vtiger_Record_Model::getInstanceById($value, 'Accounts');
//                return $actRecord->get('apn');
//            } catch (Exception $e) {
//                //don't error just let it do what it wants.
//            }
//        } else
            if ($fieldName == 'related_tariff' && $value) {
            try {
                $interstateTariff = Vtiger_Record_Model::getInstanceById($value, 'TariffManager');
                $localTariff      = Vtiger_Record_Model::getInstanceById($value, 'Tariffs');

                return ($interstateTariff->get('tariffmanagername')?$interstateTariff->get('tariffmanagername'):$localTariff->get('tariff_name'));
                //thought I could return the right model... misunderstood.
                //return ($interstateTariff->get('tariffmanagername') ? $interstateTariff : $localTariff);
            } catch (Exception $e) {
                //don't error let it fall back on regular getDisplayValue
            }
        }

        return parent::getEditViewDisplayValue($value);
    }

    public function getPicklistValues()
    {
        if ($this->get('name') == 'business_line') {
            $db = PearDatabase::getInstance();

            $result = $db->pquery('SELECT business_line FROM vtiger_business_line ORDER by sortorderid', []);

            $options = array_column($result->GetAll(), 'business_line');

            return array_combine($options, $options);
        } else {
            return parent::getPicklistValues();
        }
    }
    /*
     * Don't think I need this. probably would have been better to use this maybe.
    public function getRelatedListDisplayValue($value) {
        return parent::getRelatedListDisplayValue($value);
    }
    */
}
