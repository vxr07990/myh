<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vendors_Record_Model extends Vtiger_Record_Model
{
    public function getCreatePurchaseOrderUrl()
    {
        $purchaseOrderModuleModel = Vtiger_Module_Model::getInstance('PurchaseOrder');

        return "index.php?module=".$purchaseOrderModuleModel->getName()."&view=".$purchaseOrderModuleModel->getEditViewName()."&vendor_id=".$this->getId();
    }

    public function getMappingFields($forModuleName) {
        $res = [];
        if($forModuleName == 'Estimates' || $forModuleName == 'Actuals')
        {
            foreach ($this->getInventoryMappingFields() as $field)
            {
                $res[$field['parentField']] = $field['inventoryField'];
            }
            return $res;
        }
        return $res;
    }

    /**
     * Function to get List of Fields which are related from Vendors to Inventyory Record
     * @return <array>
     */
    public function getInventoryMappingFields()
    {
        return array(
                //Billing Address Fields
                array('parentField'=>'city', 'inventoryField'=>'bill_city', 'defaultValue'=>''),
                array('parentField'=>'street', 'inventoryField'=>'bill_street', 'defaultValue'=>''),
                array('parentField'=>'state', 'inventoryField'=>'bill_state', 'defaultValue'=>''),
                array('parentField'=>'postalcode', 'inventoryField'=>'bill_code', 'defaultValue'=>''),
                array('parentField'=>'country', 'inventoryField'=>'bill_country', 'defaultValue'=>''),
                array('parentField'=>'pobox', 'inventoryField'=>'bill_pobox', 'defaultValue'=>''),

                //Shipping Address Fields
                array('parentField'=>'street', 'inventoryField'=>'ship_street', 'defaultValue'=>''),
                array('parentField'=>'city', 'inventoryField'=>'ship_city', 'defaultValue'=>''),
                array('parentField'=>'state', 'inventoryField'=>'ship_state', 'defaultValue'=>''),
                array('parentField'=>'postalcode', 'inventoryField'=>'ship_code', 'defaultValue'=>''),
                array('parentField'=>'country', 'inventoryField'=>'ship_country', 'defaultValue'=>''),
                array('parentField'=>'pobox', 'inventoryField'=>'ship_pobox', 'defaultValue'=>'')
        );
    }

    public function getAllVendorAgreements($record = false)
    {
        //we need to get the vendor agreements
        //@TODO try and do this better jez.
        $returnArray = [];

        if (!$record) {
            $record = $this->getId();
        }

        $vendorCRM = CRMEntity::getInstance('Vendors');
        $venderAgreementsModuleModel = Vtiger_Module_Model::getInstance('VendorAgreements');
        $relatedList = $vendorCRM->get_related_list($record, '', $venderAgreementsModuleModel->getId());

        $db = PearDatabase::getInstance();
        if ($db) {
            $res = $db->pquery($relatedList['query']);
            if (method_exists($res, 'fetchRow')) {
                while ($row = $res->fetchRow()) {
                    $returnArray[$row['crmid']] = Vtiger_Record_Model::getInstanceById($row['crmid']);
                }
            }
        }
        return $returnArray;
    }
}
