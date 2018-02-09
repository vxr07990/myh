<?php 
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class MovePolicies_Record_Model extends Vtiger_Record_Model
{
    public function getTariffItems()
    {
        $recordId = $this->get('id');

        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM vtiger_movepolicies_items WHERE policies_id=? AND tariff_id!=99999999', array($recordId));
        $tariffItems = array();

        if ($result && $db->num_rows($result) > 0) {
            $i = 0;
            while ($row = $db->fetchByAssoc($result)) {
                $i++;
                $row['tmp_id'] = $i;
                $tariffItems[$row['tariff_section']][] = $row;
            }
        }

        ksort($tariffItems);

        $tariffItems['items_count'] = $i;

        return $tariffItems;
    }

    public function getMiscTariffItems()
    {
        $recordId = $this->get('id');

        $db = PearDatabase::getInstance();
        $result = $db->pquery('SELECT * FROM vtiger_movepolicies_items WHERE policies_id=? AND tariff_id=99999999', array($recordId));
        $tariffItems = array();

        if ($result && $db->num_rows($result) > 0) {
            $i = 0;
            while ($row = $db->fetchByAssoc($result)) {
                $i++;
                $row['tmp_id'] = $i;
                $tariffItems[] = $row;
            }
        }

        ksort($tariffItems);
        
        return $tariffItems;
    }
}
