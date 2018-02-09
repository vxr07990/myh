<?php
/* ********************************************************************************
 * The content of this file is subject to the Lead Company Lookup ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class LeadCompanyLookup_ActionAjax_Action extends Vtiger_Action_Controller {

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    function __construct() {
        parent::__construct();
    }

    function process(Vtiger_Request $request) {
        global $adb;
        $account_list = array();
        $key_search = $request->get('key_search');

        $sql_1 = "SELECT accountname,accountid FROM `vtiger_account` WHERE accountname LIKE ? LIMIT 0,20";
        $res_1 = $adb->pquery($sql_1,array('%'.$key_search.'%'));
        if($adb->num_rows($res_1)) {
            while($row_1 = $adb->fetch_array($res_1)) {
                $name = ($row_1['accountname']);
                $account_list[] = array(
                    'label' => $name,
                    'value' => $row_1['accountname']
                );
            }
        }
        echo json_encode($account_list);
    }
}