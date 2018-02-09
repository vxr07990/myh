<?php
/* ********************************************************************************
 * The content of this file is subject to the Hide Fields ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class DataExportTracking_SaveAjax_Action extends Vtiger_Action_Controller{

    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    function process(Vtiger_Request $request) {
        global $adb;

        $select_module=$request->get('select_module');
        $selectedFieldsList=$request->get('selectedFieldsList');
        if(in_array(0,$selectedFieldsList)){
            $selectedFieldsList = array();
            $selectedFieldsList[1] = 0;
        }
        $symbol = $request->get('symbol_array');
        $record = $request->get('record');
        $status = $request->get('status');
        $json = new Zend_Json();
        if(empty($record)) {
            $sql="INSERT INTO `vte_hide_fields` (`module`, `fields`,`symbol`, `status`) VALUES (?, ?, ?,?)";
            $adb->pquery($sql,array($select_module, $json->encode($selectedFieldsList),strtoupper($json->encode($symbol)),$status));
            $record=$adb->getLastInsertID();
        }else {
            $sql="UPDATE `vte_hide_fields` SET `module`=?, `fields`=?,`symbol`=?, `status`=?  WHERE `id`=?";
            $adb->pquery($sql,array($select_module, $json->encode($selectedFieldsList), strtoupper($json->encode($symbol)),$status,$record));
        }


        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('success'));
        $response->emit();
    }
}