<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'vtlib/Vtiger/Field.php';

/**
 * Vtiger Field Model Class
 */
class AddressList_Field_Model extends Vtiger_Field_Model
{
    public function getPicklistValues(){
        $fieldName = $this->getName();
        if($fieldName == 'address_type'){
            return [
                'Origin'=>vtranslate('Origin', $this->getModuleName()),
                'Destination'=>vtranslate('Destination',$this->getModuleName()),
                'Extra Pickup'=>vtranslate('Extra Pickup',$this->getModuleName()),
                'Extra Delivery'=>vtranslate('Extra Delivery',$this->getModuleName()),
                'Customer Billing'=>vtranslate('Customer Billing',$this->getModuleName()),
                'Customer Mailing'=>vtranslate('Customer Mailing',$this->getModuleName()),
                'Customer Shipping'=>vtranslate('Customer Shipping',$this->getModuleName()),
                'Customer Other Contact'=>vtranslate('Customer Other Contact',$this->getModuleName()),
            ];
        }
        return parent::getPicklistValues();
    }
}