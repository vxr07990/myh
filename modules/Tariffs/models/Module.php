<?php

class Tariffs_Module_Model extends Vtiger_Module_Model
{
    public function isSummaryViewSupported()
    {
        return false;
    }

    //remove module from quickcreate dropdown list
    public function isQuickCreateSupported()
    {
        return false;
    }

    //so we can call same from tariffs or tariffmanager
    public function retrieveTariffsByAgencies($userAgents,$businessLine,$commodity=NULL)
    {
        return $this->retrieveLocalTariffsByAgencies($userAgents,$businessLine,$commodity);
    }

    public function retrieveLocalTariffsByAgencies($userAgents,$businessLine,$commodity)
    {
        $tariffNames = [];
        $tariffTypes = [];
        $tariffJS = [];
        $tariffIds = [];
        $db = &PearDatabase::getInstance();
        //$add = (getenv('INSTANCE_NAME') == 'graebel') ? ' ,`vtiger_tariffs`.`business_line` ' : '';

        $params = [];

        $sql = "SELECT vtiger_tariffs.* FROM vtiger_tariffs
				JOIN vtiger_crmentity ON vtiger_tariffs.tariffsid = vtiger_crmentity.crmid
                WHERE deleted=0 AND vtiger_crmentity.agentid IN (" . generateQuestionMarks(array_keys($userAgents)) . ")";
        
        $params[] = array_keys($userAgents);

        $result = $db->pquery($sql, $params);

        //Not sure why this is done this way but I will keep it

        if($result && $db->num_rows($result) > 0){
            if(!is_array($businessLine)){
				    $businessLine = array($businessLine);
			}

            if(!is_array($commodity)){
                    $commodity = array($commodity);
            }

            $allCommodities = $this->getAllCommodities(); //Need to do this because the ALL value is replaced by each value joined by |##|

            while ($row =& $result->fetchRow()) {
                $ok = true;
                $lines = $row['business_line'];//$row['business_line'];
                if($lines && count($businessLine) > 0){
                    $lines = explode(' |##| ', $lines);
					foreach($businessLine as $bl){
						if(!in_array($bl, $lines) && $lines != "All"){
							$ok = false;
						}
					}
                }else{ //for example, $lines = NULL
					$ok = false;
				}

                $tariff_commodity = $row['commodities'];
				if($tariff_commodity && $commodity){
					$tariff_commodity = explode(' |##| ', $tariff_commodity);

                    $diff = array_diff($allCommodities, $tariff_commodity);
                    if(count($diff) == 0){
                       $tariff_commodity = 'All';
                    }

					foreach($commodity as $c){
						if(!in_array($c, $tariff_commodity) && $tariff_commodity != "All"){
							$ok = false;
						}
					}
				}else{ //for example, $tariff_commodity = NULL
					$ok = false;
				}
                

                if($ok){
                    $tariffsid = $row['tariffsid'];
                    $tariffIds[$tariffsid]     = 1;
                    $tariffNames[$tariffsid] = $row['tariff_name'];
                    $tariffTypes[$tariffsid] = $row['tariff_type'];
                    $tariffJS[$tariffsid]    = ''; //$row['custom_javascript'];
                }
            }
        }

        return [
            'tariffNames' => $tariffNames,
            'tariffTypes' => $tariffTypes,
            'tariffJS' => $tariffJS,
            'tariffIds' => $tariffIds,
        ];
    }

    function getAllCommodities(){
        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT DISTINCT commodities FROM vtiger_commodities');
        $commodities = [];
        if($result && $db->num_rows($result) > 0){
            while ($row =& $result->fetchRow()) {
                $commodities[]=$row['commodities'];
            }
        }

        return $commodities;
    }

}
