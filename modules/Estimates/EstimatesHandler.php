<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once 'include/Webservices/Revise.php';
include_once 'include/Webservices/Retrieve.php';

class EstimatesHandler extends VTEventHandler
{
    public function handleEvent($eventName, $entityData)
    {
        return;
//         if we decide to make rating an after-save operation for a saved estimate,
//          which makes some sense.  I am just including this file and note of what I
//          put in the database that made it work, but is possibly not entirely correct.
//
//           SELECT * FROM `vtiger_eventhandlers` where eventhandler_id=24;
//+-----------------+-------------------------+----------------------------------------+------------------+------+-----------+--------------+
//| eventhandler_id | event_name              | handler_path                           | handler_class    | cond | is_active | dependent_on |
//+-----------------+-------------------------+----------------------------------------+------------------+------+-----------+--------------+
//|              24 | vtiger.entity.aftersave | modules/Estimates/EstimatesHandler.php | EstimatesHandler |      |         1 | []           |
//+-----------------+-------------------------+----------------------------------------+------------------+------+-----------+--------------+
//1 row in set (0.00 sec)
//
//        $moduleName = $entityData->getModuleName();
//
//        // Validate the event target
//        if ($moduleName != 'Estimates') {
//            return;
//        }
//
//        /**
//         * Adjust the balance amount against total & received amount
//         * NOTE: beforesave the total amount will not be populated in event data.
//         */
//        if ($eventName == 'vtiger.entity.aftersave') {
//            // Trigger from other module (due to indirect save) need to be ignored - to avoid inconsistency.
//            if ($moduleName != 'Estimates') {
//                return;
//            }
//
//            //$entityDelta = new VTEntityDelta();
//            //$oldCurrency = $entityDelta->getOldValue($entityData->getModuleName(), $entityData->getId(), 'currency_id');
//
//            //so it's in focus...
//            //$fieldList = array_merge($entityData->column_fields, $_REQUEST);
//            $fieldList = array_merge($entityData->focus->column_fields, $_REQUEST);
//
//            //if it's a syncEstimate and the flag is set to rate we need to rate it.
//            if ($fieldList['syncwebservice'] && $fieldList['syncrate']) {
//                $ratingObject = false;
//                //do a rating, but because this is madness we need to know what to do.
//                if ($fieldList['business_line_est'] == 'Local Move') {
//                    //we do to the GetLocalRate for local
//                    if($fieldList['local_tariff']) {
//                        $ratingObject = new Estimates_GetLocalRate_Action;
//                    }
//                } else {
//                    //it's interstate or something else entirely.
//                    if ($fieldList['effective_tariff']) {
//                        $tariffRecordModel   = TariffManager_Record_Model::getInstanceById($fieldList['effective_tariff'], 'TariffManager');
//                        //$sql    = "SELECT tariffmanagername, custom_tariff_type, custom_javascript FROM `vtiger_tariffmanager` WHERE tariffmanagerid = ?";
//                        //$result = $db->pquery($sql, [$tariff]);
//                        //$row    = $result->fetchRow();
//                        $custom_js = $tariffRecordModel->get('custom_javascript');
//                        //@TODO: we might need to add a rating handler to the database for tariffmanager.
//                        if (
//                            (getenv('INSTANCE_NAME') == 'sirva') &&
//                            ($custom_js == 'Estimates_TPGTariff_Js')
//                        ) {
//                            $ratingObject = new Estimates_GetTPGPricelockRateEstimate_Action;
//                        } else {
//                            $ratingObject = new Estimates_GetDetailedRate_Action;
//                        }
//                    }
//                }
//
//
//                if (empty($fieldList['record'])) {
//                    if (!empty($fieldList['currentid'])) {
//                        $fieldList['record'] = $fieldList['currentid'];
//                    }
//                }
//                $quoteid = $fieldList['record'];
//
//                //only rate if we have a rating object
//                if ($quoteid && $ratingObject) {
//                    //create an array to pass to rating.
//                    $ratingStuff = [
//                        'pseudoSave'       => 0,
//                        'record'           => $quoteid,
//                        'effective_tariff' => $fieldList['effective_tariff'],
//                        'local_tariff'     => $fieldList['local_tariff'],
//                    ];
//                    $vt_request = new Vtiger_Request($ratingStuff);
//                    //capture the output buffer because the emit does an echo
//                    ob_start();
//                    //for reasons!
//                    require_once('libraries/MoveCrm/arrayBuilder.php');
//                    require_once('libraries/MoveCrm/xmlBuilder.php');
//                    $ratingObject->process($vt_request);
//                    $return = ob_get_contents();
//                    //$return is the json that rating returns. we could parse this for errors or just like do nothing.
//                    //stop output buffering.
//                    ob_end_clean();
//                }
//            }
//        }
    }
}
