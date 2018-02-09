<?php
class TariffServices_LocalHourlySetLookup_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $serviceid = $request->get('serviceid');
        $men = $request->get('men');
        $vans = $request->get('vans');
        
        $db = PearDatabase::getInstance();
        
        if (!empty($vans)) {
            $sql = "SELECT `vtiger_tariffhourlyset`.rate, `vtiger_tariffhourlyset`.men, `vtiger_tariffhourlyset`.vans, `vtiger_tariffservices`.hourlyset_addmanrate, `vtiger_tariffservices`.hourlyset_addvanrate  
					FROM `vtiger_tariffhourlyset` JOIN `vtiger_tariffservices` ON `vtiger_tariffhourlyset`.serviceid = `vtiger_tariffservices`.tariffservicesid 
					WHERE men <=? and vans <=? and serviceid=?";

            $result = $db->pquery($sql, array($men, $vans, $serviceid));
            
            $info = array();
            while ($row =& $result->fetchRow()) {
                $info[] = array('Rate'=>$row[0], 'Men'=>$row[1], 'Vans'=>$row[2], 'AddMan'=>$row[3], 'AddVan'=>$row[4]);
            }
            
            $closest = array();
            
            if ($info[0]['AddVan'] >= $info[0]['AddMan']) {
                $prevDiff = $men;
                foreach ($info as $line) {
                    $diff = $men - $line['Men'];
                    
                    if ($diff < $prevDiff) {
                        $closest[0] = $line;
                        $prevDiff = $diff;
                    }
                }
            } else {
                $prevDiff = $vans;
                foreach ($info as $line) {
                    $diff = $vans - $line['Vans'];
                    if ($diff < $prevDiff) {
                        $closest[0] = $line;
                        $prevDiff = $diff;
                    }
                }
            }
            $return = array();
            $return['rate'] = $closest[0]['Rate'] + (($vans-$closest[0]['Vans'])*($closest[0]['AddVan'])) + (($men-$closest[0]['Men'])*($closest[0]['AddMan']));
        } else {
            $sql = "SELECT `vtiger_tariffhourlyset`.rate, `vtiger_tariffhourlyset`.men, `vtiger_tariffservices`.hourlyset_addmanrate  
					FROM `vtiger_tariffhourlyset` JOIN `vtiger_tariffservices` ON `vtiger_tariffhourlyset`.serviceid = `vtiger_tariffservices`.tariffservicesid 
					WHERE men <=? and serviceid=?";
            $result = $db->pquery($sql, array($men, $serviceid));
            
            $info = array();
            while ($row =& $result->fetchRow()) {
                $info[] = array('Rate'=>$row[0], 'Men'=>$row[1], 'AddMan'=>$row[2]);
            }
            $closest = array();
            
            $prevDiff = $men;
            foreach ($info as $line) {
                $diff = $men - $line['Men'];
                
                if ($diff < $prevDiff) {
                    $closest[0] = $line;
                    $prevDiff = $diff;
                }
            }
            $return = array();
            $return['rate'] = $closest[0]['Rate'] + (($men-$closest[0]['Men'])*$closest[0]['AddMan']);
        }
        
        $response = new Vtiger_Response();
        $response->setResult($return);
        $response->emit();
    }
}
