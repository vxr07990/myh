<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

require_once('libraries/nusoap/nusoap.php');
require_once('include/Webservices/Revise.php');

class Actuals_Save_Action extends Estimates_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        //OT16352 - Calculate Net Weight
        if (getenv('INSTANCE_NAME') == 'graebel') {
            $user        = Users_Record_Model::getCurrentUserModel();
            $netWeight   = $request->get('weight');
            $tareWeight  = $request->get('tweight');
            $grossWeight = $request->get('gweight');
            $orderId     = $request->get('orders_id');
            if ($orderId != '') {
                $orderArray = [
                    'id'               => vtws_getWebserviceEntityId('Orders', $orderId),
                    'orders_gweight'   => $grossWeight,
                    'orders_tweight'   => $tareWeight,
                    'orders_netweight' => $netWeight
                ];
                try {
                    //@NOTE: vtws_revice ALTERS the _REQUEST super global which is still used later in the run now with weird stuff in it.
                    //@NOTE: altered revise oh my god. instead of here.
                    vtws_revise($orderArray, $user);
                } catch (Exception $exc) {
                    global $log;
                    $log->debug('Error updating order from actual:'.$exc->getMessage());
                }
            }
        }
        parent::process($request);
    }
}
