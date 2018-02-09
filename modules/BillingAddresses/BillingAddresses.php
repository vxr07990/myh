<?php
// Billing Addresses module

include_once 'modules/Vtiger/CRMEntity.php';
require_once('modules/Addresses/Addresses.php');

class BillingAddresses extends Addresses
{
    public function BillingAddresses()
    {
        $this->log           = LoggerManager::getLogger('Addresses');
        $this->db            = PearDatabase::getInstance();
        $this->column_fields = getColumnFields('BillingAddress');
        parent::__construct();
        $this->table_name                                  = 'vtiger_billingaddresses';
        $this->table_index                                 = 'billingaddressesid';
        $this->customFieldTable[]                          = 'vtiger_billingaddressescf';
        $this->customFieldTable[]                          = 'billingaddressesid';
        $this->tab_name_index['vtiger_billingaddresses']   = 'billingaddressesid';
        $this->tab_name_index['vtiger_billingaddressescf'] = 'billingaddressesid';
        $this->tab_name[]                                  = 'vtiger_billingaddresses';
        $this->tab_name[]                                  = 'vtiger_billingaddressescf';
        $this->list_fields['LBL_BILLINGADDRESSESID']       = ['billingaddresses', 'billingaddressesid'];
        $this->popup_fields                                = ['addressesid'];
    }
}
