<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vehicles_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        $timeOut     = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('time_out'));
        $datetimeOut = DateTimeField::convertToDBTimeZone($request->get('date_out').' '.$timeOut);
        $request->set('time_out', $datetimeOut->format('H:i:s'));
        $request->set('date_out', $datetimeOut->format('Y-m-d'));
        $timeIn     = Vtiger_Time_UIType::getTimeValueWithSeconds($request->get('time_in'));
        $datetimeIn = DateTimeField::convertToDBTimeZone($request->get('date_in').' '.$timeIn);
        $request->set('time_in', $datetimeIn->format('H:i:s'));
        $request->set('date_in', $datetimeIn->format('Y-m-d'));
        parent::process($request);
    }
}
