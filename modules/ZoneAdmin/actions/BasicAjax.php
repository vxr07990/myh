<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class ZoneAdmin_BasicAjax_Action extends Vtiger_BasicAjax_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    public function process(Vtiger_Request $request)
    {
        $searchValue = $request->get('search_value');
        $searchModule = $request->get('search_module');

        $relatedModule = $request->get('module');


        $zipList = $this->getZip3Array($searchValue);

        $result = array();
        if (is_array($zipList)) {
            foreach ($zipList as $zip) {
                $result[] = array('label' => decode_html($zip), 'value' => decode_html($zip));
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

	//this are area codes (for example, North Carolina area code is 282, that means, all zip codes for North Carolina starts with 282
    public function getZip3Array($start = 0)
    {
        $notInUse = array(2,3,4,99,213,269,343,345,348,419,428,429,517,518,
            519,529,533,536,552,568,578,579,589,621,632,642,643,659,663,682,
            694,695,696,697,698,699,702,709,715,732,742,771,817,818,819,839
            ,848,849,854,858,861,862,866,867,868,869,876,886,887,888,892,896,899,
            909,929,987);
        $zipList = array();
        
        

        for ($i = $start; $i < 1000; $i++) {
            if (in_array($i, $notInUse)) {
                continue;
            }

            if (strpos(strval($i), $start) !== false) {
                array_push($zipList, substr("000000" . strval($i), -3));
            }
        }
        
        return $zipList;
    }
}
