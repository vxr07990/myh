<?php

class TariffManager_Module_Model extends Vtiger_Module_Model
{
    public function isSummaryViewSupported()
    {
        return false;
    }
    
    public function getDetailViewUrl($id)
    {
        return 'index.php?module='. $this->get('name').'&view='.$this->getDetailViewName().'&record='.$id.'&mode=showDetailViewByMode&requestMode=full';
    }

    //remove module from quickcreate dropdown list
    public function isQuickCreateSupported()
    {
        return false;
    }

    //so we can build a TariffManager or Tariffs module_model and call the one function name.
    public function retrieveTariffsByAgencies($userAgents, $businessLine)
    {
        return $this->retrieveInterstateTariffsByAgencies($userAgents, $businessLine);
    }
    
    //I think this is where it should go as it's not related to a particular record or the base object, but an active module.
    //Interstate is added is so when you trace you see it's not Local
    public function retrieveInterstateTariffsByAgencies($userAgents, $businessLine)
    {
        $tariffNames = [];
        $tariffTypes = [];
        $tariffJS    = [];
        $tariffIds     = [];
        $db = PearDatabase::getInstance();
        if (is_array($userAgents)) {
            foreach ($userAgents as $agent => $agentName) {
                $sql    = "SELECT tariffid FROM `vtiger_tariff2agent` JOIN `vtiger_crmentity` ON tariffid=crmid WHERE `vtiger_tariff2agent`.agentid = ? AND deleted=0";
                $result = $db->pquery($sql, [$agent]);
                while ($row =& $result->fetchRow()) {
                    $tariffIds[$row[0]] = 1;
                }
            }
            foreach ($tariffIds as $tariff => $blah) {
                $sql                  = "SELECT tariffmanagername, custom_tariff_type, custom_javascript FROM `vtiger_tariffmanager` WHERE tariffmanagerid = ?";
                $result               = $db->pquery($sql, [$tariff]);
                $row                  = $result->fetchRow();
                $tariffNames[$tariff] = $row['tariffmanagername'];
                if ($row['custom_tariff_type']) {
                    $tariffTypes[$tariff] = $row['custom_tariff_type'];
                }
                if ($row['custom_javascript']) {
                    $tariffJS[$tariff] = $row['custom_javascript'];
                }
            }

            $add = (getenv('INSTANCE_NAME') == 'graebel') ? ' ,`vtiger_tariffs`.`business_line` ' : '';
            foreach ($userAgents as $agent => $agentName) {
                $sql    = "SELECT `vtiger_tariffs`.`tariffsid`,
                                  `vtiger_tariffs`.`tariff_name`,
                                  `vtiger_tariffs`.`tariff_type`
                                  $add
                    FROM `vtiger_tariffs`
                    JOIN `vtiger_crmentity` ON `vtiger_tariffs`.`tariffsid` = `vtiger_crmentity`.`crmid`
                    JOIN `vtiger_agentmanager` ON `vtiger_crmentity`.`agentid` = `vtiger_agentmanager`.`agentmanagerid`
                    WHERE `vtiger_agentmanager`.`agentmanagerid`=? AND deleted=0";
                $result = $db->pquery($sql, [$agent]);
                while ($row =& $result->fetchRow()) {
                    $lines = $row['business_line'];
                    if($lines && $businessLine)
                    {
                        $lines = explode(' |##| ', $lines);
                        if(!in_array($businessLine, $lines))
                        {
                            continue;
                        }
                    } else if($businessLine != 'Local Move')
                    {
                        continue;
                    }
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
            'tariffJS'    => $tariffJS,
            'tariffIds'     => $tariffIds,
        ];
    }
}
