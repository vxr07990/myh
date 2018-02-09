<?php

class Contracts_Record_Model extends Vtiger_Record_Model
{
    public function getAssignedRecords()
    {
        $recordId = $this->getId();
        $records = array('Vanlines'=>array(), 'Agents'=>array(), 'ApplyToAll'=>array());
        if ($recordId == null) {
            return $records;
        }
        
        $db = PearDatabase::getInstance();
        $sql = "SELECT vanlineid, apply_to_all_agents FROM `vtiger_contract2vanline` WHERE contractid=?";
        $result = $db->pquery($sql, array($recordId));
        
        while ($row =& $result->fetchRow()) {
            $records['Vanlines'][] = Vtiger_Record_Model::getInstanceById($row[0]);
            if ($row[1] == 1) {
                $records['ApplyToAll'][] = $row[0];
            }
        }
        
        $sql = "SELECT agentid FROM `vtiger_contract2agent` WHERE contractid=?";
        $result = $db->pquery($sql, array($recordId));
        
        while ($row =& $result->fetchRow()) {
            $records['Agents'][] = $row[0];
        }
        
        return $records;
    }
    
    public function getDetailViewUrl()
    {
        $module = $this->getModule();
        return 'index.php?module='.$this->getModuleName().'&view='.$module->getDetailViewName().'&record='.$this->getId().'&mode=showDetailViewByMode&requestMode=full';
    }

    public function getMiscCharges($request)
    {
        $recordId = $this->getId();
        
        if (!$db) {
            $db = PearDatabase::getInstance();
        }
        $sql = 'SELECT `contracts_misc_id`, `is_quantity_rate`, `description`, `rate`, `quantity`, `discounted`, `discount` FROM `vtiger_contracts_misc_items` WHERE contractsid=?';
        $result = $db->pquery($sql, array($recordId));
        
        $items = array();
        
        //file_put_contents('logs/devLog.log', print_r($result->fetchRow(), true), FILE_APPEND);
        $counter = 0;
        while ($row =& $result->fetchRow()) {
            if($request->get('isDuplicate')) {
                $row[0] = 'none';
            }
            $items[$counter]['contracts_misc_id'] = $row[0];
            $items[$counter]['is_quantity_rate'] = $row[1];
            $items[$counter]['description'] = $row[2];
            $items[$counter]['rate'] = $row[3];
            $items[$counter]['quantity'] = $row[4];
            $items[$counter]['discounted'] = $row[5];
            $items[$counter]['discount'] = $row[6];
            $counter++;
        }
        
        return $items;
    }
    
    //TODO: Populate functions to pull correct data
    public function getFuelLookupTable($duplicate)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT * FROM `vtiger_contractfuel` WHERE contractid=?";
        $result = $db->pquery($sql, array($this->getId()));

        $fuelTable = array();
        while ($row =& $result->fetchRow()) {
            $fuelItem = array();
            if($duplicate)
            {
                $row['line_item_id'] = '';
            }
            $fuelItem['line_item_id'] = $row['line_item_id'];
            $fuelItem['from_cost'] = $row['from_cost'];
            $fuelItem['to_cost'] = $row['to_cost'];
            $fuelItem['rate'] = $row['rate'];
            $fuelItem['percentage'] = $row['percentage'];

            $fuelTable[] = $fuelItem;
        }
        return $fuelTable;
    }
    
    public function getAnnualRateIncreases()
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT date, rate FROM `vtiger_annual_rate` WHERE contractid=?";
        $result = $db->pquery($sql, [$this->getId()]);
        
        $ratesArray = array();
        
        while ($row =& $result->fetchRow()) {
            $ratesArray[] = $row;
        }
        
        return $ratesArray;
    }
}
