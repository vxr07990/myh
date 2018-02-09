<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Invoice_Module_Model extends Inventory_Module_Model
{
    function getRevenueDistribitionValues($actualId){
        $data = [];
        global $adb;
        $sql = "SELECT vtiger_revenue_distribution.* FROM vtiger_revenue_distribution WHERE crmid = ? ORDER BY rd_invoice_sequence ASC";
        $rs = $adb->pquery($sql,[$actualId]);
        $data['subtotal'] = 0;
        $data['total_discount'] = 0;
        $data['items'] = [];
        while ($row=$adb->fetchByAssoc($rs)){
            $rowNo = $row['rd_invoice_sequence'];
            $data['items'][$rowNo]['item_code'] = $row['rd_item_code'];
            $data['items'][$rowNo]['item_code_description'] = $row['rd_item_code_description'];
            $data['items'][$rowNo]['base_rate'] = $row['rd_base_rate'];
            $data['items'][$rowNo]['unit_rate'] = $row['rd_unit_rate'];
            $data['items'][$rowNo]['quantity'] = $row['rd_quantity'];
            $data['items'][$rowNo]['unit_measurement'] = $row['rd_unit_measurement'];

            $amount = empty($row['rd_gross_amount']) ? 0 : $row['rd_gross_amount'];
            $discount = empty($row['rd_invoice_discount']) ? 0: $row['rd_invoice_discount'];
            $total_after_discount=$amount-$discount*$amount/100;

            $data['items'][$rowNo]['amount'] = $amount;
            $data['items'][$rowNo]['discount'] = $discount;
            $data['items'][$rowNo]['total_after_discount'] = $total_after_discount;
            $data['items'][$rowNo]['net_amount'] = $row['rd_invoice_amount'];

            if(!empty($row['rd_invoice_amount'])) $data['subtotal'] +=$row['rd_invoice_amount'];
            $discountAmount = $amount-$total_after_discount;
            if($discountAmount >0) $data['total_discount'] += $discountAmount;
        }
        $data['total'] = $data['subtotal']-$data['total_discount'];
        $data['received'] = 0;
        $data['balance'] = $data['total'] - $data['received'];
        return $data;
    }
}
