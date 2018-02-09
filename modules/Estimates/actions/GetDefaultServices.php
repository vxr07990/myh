<?php
/**
 * @author 			Louis Robinson
 * @file 			GetDefaultServices.php
 * @description 	Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code
 * @contact 		lrobinson@igcsoftware.com
 * @company			IGC Software
 */
class Estimates_GetDefaultServices_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        //file_put_contents('logs/devLog.log', "\n request : ".print_r($request,true), FILE_APPEND);
        //$services = array('Transportation', 'Fuel Surcharge', 'Packing', 'Unpacking', 'Valuation', 'Origin Accessorials', 'Origin SIT', 'Destination Accessorials', 'Destination SIT', 'Bulky Items', 'Miscellaneous Services', 'IRR');
        $services = array();
        $db = PearDatabase::getInstance();
        $sql = "SELECT DISTINCT `vtiger_service`.servicename FROM `vtiger_service` JOIN `vtiger_crmentity` ON `vtiger_service`.serviceid = `vtiger_crmentity`.crmid WHERE `vtiger_crmentity`.smownerid = 1 AND `vtiger_crmentity`.deleted = 0";
        $result = $db->pquery($sql, array());
        
        while ($row =& $result->fetchRow()) {
            $services[] = $row[0];
        }
        //file_put_contents('logs/devLog.log', "\n services : ".print_r($services,true), FILE_APPEND);

        $serviceIds = array();
        foreach ($services as $service) {
            $sql = "SELECT serviceid FROM `vtiger_service` WHERE servicename=?";
            $params[] = $service;
            
            $result = $db->pquery($sql, $params);
            unset($params);
            
            $row = $result->fetchRow();
            
            if ($row == null) {
                continue;
            }
            
            $serviceIds[$service] = $row[0];
        }
        //file_put_contents('logs/devLog.log', "\n serviceIds : ".print_r($serviceIds,true), FILE_APPEND);

        $response = new Vtiger_Response();
        $response->setResult($serviceIds);
        $response->emit();
    }
}
