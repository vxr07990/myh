<?php

/**
 * Class Opportunities_GetAddressDetails_Action
 * It looks like this class is never used. Marked for potential deletion 02/07/2016
 */

class Opportunities_GetAddressDetails_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        file_put_contents('logs/devLog.log', "\n Opportunities_GetAddressDetails_Action Backtrace : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        $potential_id = $request->get('potential_id');
        
        $originColumns = array(
                            "cf_789",
                            "cf_793",
                            "cf_797",
                            "cf_801",
                            "cf_807",
                            "cf_811",
                            "cf_815");
        
        $destinationColumns = array(
                                "cf_791",
                                "cf_795",
                                "cf_799",
                                "cf_803",
                                "cf_809",
                                "cf_813",
                                "cf_817");
        
        $info['origin'] = array();
        $info['destination'] = array();
        
        $db = PearDatabase::getInstance();
        
        $queryResults = $db->query("SELECT * FROM `vtiger_potentialscf` WHERE `potentialid`='$potential_id'");
        
        $row = $queryResults->fetchRow(DB_FETCHMODE_ASSOC);
        
        foreach ($row as $column => $value) {
            if (in_array($column, $originColumns)) {
                $info['origin'][$column]=$value;
            } elseif (in_array($column, $destinationColumns)) {
                $info['destination'][$column]=$value;
            }
        }
        
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
