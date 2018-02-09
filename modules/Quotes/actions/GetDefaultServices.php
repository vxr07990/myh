<?php
class Quotes_GetDefaultServices_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $services = array('Transportation', 'Fuel Surcharge', 'Packing', 'Unpacking', 'Valuation', 'Origin Accessorials', 'Origin SIT', 'Destination Accessorials', 'Destination SIT', 'Bulky Items', 'Miscellaneous Services', 'IRR');
        $db = PearDatabase::getInstance();
        
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
        
        $response = new Vtiger_Response();
        $response->setResult($serviceIds);
        $response->emit();
    }
}
