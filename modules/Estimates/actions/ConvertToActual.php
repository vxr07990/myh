<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

include_once 'include/Webservices/Create.php';
include_once 'include/Webservices/Retrieve.php';
include_once 'include/Webservices/Revise.php';
include_once 'modules/Users/Users.php';
include_once 'include/Webservices/Utils.php';

class Estimates_ConvertToActual_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        //Retrieve provided Estimates record and pass it into vtws_create for Actuals
        $user = new Users();
        $current_user = $user->retrieveCurrentUserInfoFromFile(Users_Record_Model::getCurrentUserModel()->getId());
        $estimateId = $request->get('record');

        $estimateRecord = Vtiger_Record_Model::getInstanceById($estimateId, 'Estimates');
        if(empty($estimateRecord->get('orders_id'))) {
            $response = new Vtiger_Response();
            $response->setError('3040', 'Cannot create an Actual without the Estimate being related to an Order.');
            $response->emit();
            return;
        }

        try {
            $wsid     = vtws_getWebserviceEntityId('Estimates', $estimateId);
            $estimate = vtws_retrieve($wsid, $current_user);
            //I added this because seeing estimate when it was actual annoyed me.
            if (preg_match('/estimate/i', $estimate['subject'])) {
                $estimate['subject'] = preg_replace('/estimate/i', 'Actual', $estimate['subject']);
            }
            for ($i = 0; $i <= $estimate['detailLineItemCount']; $i++) {
                unset($estimate['detaillineitemid'.$i]);
            }
        foreach($estimate as $key => $value)
        {
            if(strpos($key, 'serviceProviderID') === 0)
            {
                unset($estimate[$key]);
            }
        }

            // Set actuals_stage, since it is required
            $estimate['actuals_stage'] = 'Created';
            // set related estimate/actual
            $estimate['related_record_self'] = '45x' . $estimateId;
            unset($estimate['id']);
            unset($estimate['record']);
            unset($_REQUEST['id']);
            unset($_REQUEST['record']);
            $actual = vtws_create('Actuals', $estimate, $current_user);
        } catch (Exception $ex) {
            $response = new Vtiger_Response();
            $response->setError($ex->getCode(), $ex->getMessage());
            $response->emit();
            return;
        }

        if ($actual['id']) {
            $actualsId = substr(strstr($actual['id'], 'x'), 1);
            // fill in related actual in estimate
            $db =& PearDatabase::getInstance();
            $db->pquery('UPDATE `vtiger_quotes` SET related_record_self=? WHERE quoteid=?', [$actualsId, $estimateId]);
            $response = new Vtiger_Response();
            $response->setResult($actualsId);
            $response->emit();
//            header('Location: index.php?module=Actuals&view=Detail&record='.$actualsId);
        } else {
            $response = new Vtiger_Response();
            $response->setError('3050', 'Failed to update new Actual.');
            $response->emit();
            return;
        }
    }

    //commenting to see what might use this.
//    protected function duplicateLineItems($estimateId, $actualId)
//    {
//        $db = PearDatabase::getInstance();
//        $db->startTransaction();
//        $sql    = "SELECT * FROM `vtiger_inventoryproductrel` WHERE id=?";
//        $result = $db->pquery($sql, [$estimateId]);
//        while ($row =& $result->fetchRow()) {
//            $sql = "INSERT INTO `vtiger_inventoryproductrel`
//                                (`id`,`productid`,`sequence_no`,`quantity`,`listprice`,
//                                `discount_percent`,`discount_amount`,`comment`,`description`,
//                                `incrementondel`,`tax1`,`tax2`,`tax3`)
//                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
//            $db->pquery($sql,
//                        [$actualId,
//                         $row['productid'],
//                         $row['sequence_no'],
//                         $row['quantity'],
//                         0,
//                         $row['discount_percent'],
//                         $row['discount_amount'],
//                         $row['comment'],
//                         $row['description'],
//                         $row['incrementondel'],
//                         $row['tax1'],
//                         $row['tax2'],
//                         $row['tax3']]);
//        }
//        $db->completeTransaction();
//
//        if (getenv('INSTANCE_NAME') == 'graebel') {
//            //copy the detailed line items if they exist.
//            $db->startTransaction();
//            $sql    = 'SELECT * FROM `vtiger_detailed_lineitems` WHERE `dli_relcrmid`=?';
//            $result = $db->pquery($sql, [$estimateId]);
//            if (method_exists($result, 'fetchRow')) {
//                $date_var         = date("Y-m-d H:i:s");
//                while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
//                    //sigh, db fetchmode doesn't pass through pear... stupid
//                    $element = [
//                        'dli_tariff_item_number'      => $row['dli_tariff_item_number'],
//                        'dli_tariff_item_name'        => $row['dli_tariff_item_name'],
//                        'dli_tariff_schedule_section' => $row['dli_tariff_schedule_section'],
//                        'dli_return_section_name'     => $row['dli_return_section_name'],
//                        'dli_description'             => $row['dli_description'],
//                        'dli_provider_role'           => $row['dli_provider_role'],
//                        'dli_participant_role'        => $row['dli_participant_role'],
//                        'dli_participant_role_id'     => $row['dli_participant_role_id'],
//                        'dli_base_rate'               => $row['dli_base_rate'],
//                        'dli_quantity'                => $row['dli_quantity'],
//                        'dli_unit_of_measurement'     => $row['dli_unit_of_measurement'],
//                        'dli_unit_rate'               => $row['dli_unit_rate'],
//                        'dli_gross'                   => $row['dli_gross'],
//                        'dli_invoice_discount'        => $row['dli_invoice_discount'],
//                        'dli_invoice_net'             => $row['dli_invoice_net'],
//                        'dli_distribution_discount'   => $row['dli_distribution_discount'],
//                        'dli_distribution_net'        => $row['dli_distribution_net'],
//                        'dli_tariff_move_policy'      => $row['dli_tariff_move_policy'],
//                        'dli_approval'                => $row['dli_approval'],
//                        'dli_service_provider'        => $row['dli_service_provider'],
//                        'dli_invoiceable'             => $row['dli_invoiceable'],
//                        'dli_distributable'           => $row['dli_distributable'],
//                        'dli_invoiced'                => $row['dli_invoiced'],
//                        'dli_distributed'             => $row['dli_distributed'],
//                        'dli_invoice_number'          => $row['dli_invoice_number'],
//                        'dli_invoice_sequence'        => $row['dli_invoice_sequence'],
//                        'dli_phase'                   => $row['dli_phase'],
//                        'dli_event'                   => $row['dli_event'],
//                        'dli_distribution_sequence'   => $row['dli_distribution_sequence'],
//                        'dli_ready_to_invoice'        => $row['dli_ready_to_invoice'],
//                        'dli_ready_to_distribute'     => $row['dli_ready_to_distribute'],
//                        'dli_date_performed'          => $row['dli_date_performed'],
//                        'dli_location'                => $row['dli_location'],
//                        'dli_gcs_flag'                => $row['dli_gcs_flag'],
//                        'dli_metro_flag'              => $row['dli_metro_flag'],
//                        'dli_item_weight'              => $row['dli_item_weight'],
//                        //normally built in the crmentity table function
//                        'assigned_user_id'            => $row['assigned_user_id'],
//                        'agentid'                     => $row['agentid'],
//                        'smownerid'                   => $row['smownerid'],
//                        'modifiedby'                  => $row['modifiedby'],
//                        'dli_relcrmid'                => $actualId,
//                        'createdtime'                 => $db->formatDate($date_var, true),
//                        'modifiedtime'                => $db->formatDate($date_var, true)
//                    ];
//                    try {
//                        $params     = [];
//                        $tabList    = '';
//                        foreach ($element as $key => $value) {
//                            $tabList .= ($tabList?',':'').' `'.$key.'`';
//                            if ($value) {
//                                $params[] = $value;
//                            } else {
//                                $params[] = null;
//                            }
//                        }
//                        $new_sql  = "INSERT INTO `vtiger_detailed_lineitems` (".$tabList.") VALUES (".generateQuestionMarks($params).')';
//                        $db->pquery($new_sql, $params);
//                        $idList[] = $db->getLastInsertID();
//                    } catch (Exception $e) {
//                        file_put_contents('logs/devLog.log', "\n Save Exception saving detailed line items when creating Actual! : ".$e->getMessage()."\n line".$e->getLine()."\n", FILE_APPEND);
//                    }
//                }
//            }
//            $db->completeTransaction();
//        } elseif (getenv('IGC_MOVEHQ')) {
//            //copy the detailed line items if they exist.
//            $db->startTransaction();
//            $sql    = 'SELECT * FROM `vtiger_detailed_lineitems` WHERE `dli_relcrmid`=?';
//            $result = $db->pquery($sql, [$estimateId]);
//            if (method_exists($result, 'fetchRow')) {
//                $date_var         = date("Y-m-d H:i:s");
//                while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
//                    //sigh, db fetchmode doesn't pass through pear... stupid
//                    $element = [
//                        'dli_tariff_item_number'      => $row['dli_tariff_item_number'],
//                        'dli_tariff_item_name'        => $row['dli_tariff_item_name'],
//                        'dli_tariff_schedule_section' => $row['dli_tariff_schedule_section'],
//                        'dli_return_section_name'     => $row['dli_return_section_name'],
//                        'dli_description'             => $row['dli_description'],
//                        'dli_provider_role'           => $row['dli_provider_role'],
//                        'dli_participant_role'        => $row['dli_participant_role'],
//                        'dli_participant_role_id'     => $row['dli_participant_role_id'],
//                        'dli_base_rate'               => $row['dli_base_rate'],
//                        'dli_quantity'                => $row['dli_quantity'],
//                        'dli_unit_of_measurement'     => $row['dli_unit_of_measurement'],
//                        'dli_unit_rate'               => $row['dli_unit_rate'],
//                        'dli_gross'                   => $row['dli_gross'],
//                        'dli_invoice_discount'        => $row['dli_invoice_discount'],
//                        'dli_invoice_net'             => $row['dli_invoice_net'],
//                        'dli_distribution_discount'   => $row['dli_distribution_discount'],
//                        'dli_distribution_net'        => $row['dli_distribution_net'],
//                        'dli_tariff_move_policy'      => $row['dli_tariff_move_policy'],
//                        'dli_approval'                => $row['dli_approval'],
//                        'dli_service_provider'        => $row['dli_service_provider'],
//                        'dli_invoiceable'             => $row['dli_invoiceable'],
//                        'dli_distributable'           => $row['dli_distributable'],
//                        'dli_invoiced'                => $row['dli_invoiced'],
//                        'dli_distributed'             => $row['dli_distributed'],
//                        'dli_invoice_number'          => $row['dli_invoice_number'],
//                        'dli_ready_to_invoice'        => $row['dli_ready_to_invoice'],
//                        'dli_ready_to_distribute'     => $row['dli_ready_to_distribute'],
//                        'dli_date_performed'          => $row['dli_date_performed'],
//                        //normally built in the crmentity table function
//                        'assigned_user_id'            => $row['assigned_user_id'],
//                        'agentid'                     => $row['agentid'],
//                        'smownerid'                   => $row['smownerid'],
//                        'modifiedby'                  => $row['modifiedby'],
//                        'dli_relcrmid'                => $actualId,
//                        'createdtime'                 => $db->formatDate($date_var, true),
//                        'modifiedtime'                => $db->formatDate($date_var, true)
//                    ];
//                    try {
//                        $params     = [];
//                        $tabList    = '';
//                        foreach ($element as $key => $value) {
//                            $tabList .= ($tabList?',':'').' `'.$key.'`';
//                            if ($value) {
//                                $params[] = $value;
//                            } else {
//                                $params[] = null;
//                            }
//                        }
//                        $new_sql  = "INSERT INTO `vtiger_detailed_lineitems` (".$tabList.") VALUES (".generateQuestionMarks($params).')';
//                        $db->pquery($new_sql, $params);
//                        $idList[] = $db->getLastInsertID();
//                    } catch (Exception $e) {
//                        file_put_contents('logs/devLog.log', "\n Save Exception saving detailed line items when creating Actual! : ".$e->getMessage()."\n line".$e->getLine()."\n", FILE_APPEND);
//                    }
//                }
//            }
//            $db->completeTransaction();
//        }
//    }

    protected function pullTariffInfo($estimateId)
    {
        $db = PearDatabase::getInstance();
        $db->startTransaction();
        $sql = "SELECT effective_tariff FROM `vtiger_quotes` WHERE quoteid=?";
        $result = $db->pquery($sql, [$estimateId]);
        $row = $result->fetchRow();
        $db->completeTransaction();
        return $row;
    }

    protected function populateTariff($estimateId, $actualId)
    {
        $db = PearDatabase::getInstance();
        $db->startTransaction();
        $sql = "SELECT effective_tariff FROM `vtiger_quotes` WHERE quoteid=?";
        $result = $db->pquery($sql, [$estimateId]);
        $row = $result->fetchRow();
        if ($row['effective_tariff']) {
            $sql = "UPDATE `vtiger_quotes` SET `effective_tariff`=? WHERE quoteid=?";
            $db->pquery($sql, [$row['effective_tariff'], $actualId]);
        }
        $db->completeTransaction();
    }
}
