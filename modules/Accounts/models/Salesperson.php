<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Accounts_Salesperson_Model extends Vtiger_Record_Model
{

    /* Selects all the business lines in the vtiger_business_line table */
    public function getBusinessLines()
    {
        $db = PearDatabase::getInstance();
        $salesPersons = [];
        $result             = $db->pquery('SELECT business_lineid, business_line FROM `vtiger_business_line` WHERE presence = 1 ORDER BY sortorderid', []);
        $data = array();
        while ($row =& $result->fetchRow()) {
            $data[$row['business_lineid']] = $row['business_line'];
        }
//        $data = array(
//            'HHG - Interstate',
//            'HHG - Intrastate',
//            'HHG - Local',
//            'HHG - International',
//            'Electronics - Interstate',
//            'Electronics - Intrastate',
//            'Electronics - Local',
//            'Electronics - International',
//            'Display & Exhibits - Interstate',
//            'Display & Exhibits - Intrastate',
//            'Display & Exhibits - Local',
//            'Display & Exhibits - International',
//            'General Commodities - Interstate',
//            'General Commodities - Intrastate',
//            'General Commodities - Local',
//            'General Commodities - International',
//            'Auto - Interstate',
//            'Auto - Intrastate',
//            'Auto - Local',
//            'Auto - International',
//            'Commercial - Interstate',
//            'Commercial - Intrastate',
//            'Commercial - Local',
//            'Commercial - International',
//        );
        return $data;
    }

    public function getSalesPersonData($recordId)
    {
        $db = PearDatabase::getInstance();
        $result             = $db->pquery('SELECT * FROM `vtiger_account_salespersons` WHERE record_id = ?', [$recordId]);
        $data = array();
        /*committ*/
        while ($row =& $result->fetchRow()) {
            $data[] = array(
                'id'                    => $row['id'],
                'salesperson_id'        => $row['salesperson_id'],
                'booking_office_id'     => $row['booking_office_id'],
                'commodity'             => $row['commodity'],
                'sales_credit'          => $row['sales_credit'],
                'sales_comm'            => $row['sales_comm'],
                'effective_date_from'   => DateTimeField::convertToUserFormat($row['effective_date_from']),
                'effective_date_to'     => DateTimeField::convertToUserFormat($row['effective_date_to']),
                'record_id'             => $row['record_id'],
            );
        }
        return $data;
    }

    /* Grabs the sales person data for the detail view and formats the data before sending it to the view */
    public function getSalesPersonDetails($recordId)
    {
        $currentUser = Users_Record_Model::getCurrentUserModel();

        $vanlines = $currentUser->getAccessibleVanlinesForUser();
       // $agents = $currentUser->getAccessibleAgentsForUser();
        //OT 1976 - Added below select because $agents was being used for salespeople
        $salespeople = $currentUser->getAccessibleSalesPeople();

        //$businessLines = $this->getBusinessLines();

        $db = PearDatabase::getInstance();
        $result             = $db->pquery('SELECT * FROM `vtiger_account_salespersons` WHERE record_id = ?', [$recordId]);
        $data = array();
        while ($row =& $result->fetchRow()) {
            $data[] = array(
                'id'                    => $row['id'],
                'salesperson_id'        => $salespeople[$row['salesperson_id']],
                'booking_office_id'     => $vanlines[$row['booking_office_id']],
                'commodity'             => $row['commodity'],
                'sales_credit'          => $row['sales_credit'],
                'sales_comm'            => $row['sales_comm'],
                'effective_date_from'   => DateTimeField::convertToUserFormat($row['effective_date_from']),
                'effective_date_to'     => DateTimeField::convertToUserFormat($row['effective_date_to']),
                'record_id'             => $row['record_id'],
            );
        }
        return $data;
    }
}
