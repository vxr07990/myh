<?php

class WFInventory_Module_Model extends Vtiger_Module_Model
{
    function getDuplicateCheckFields()
    {
        return Zend_Json::encode(array('inventory_number', 'agentid'));
    }
}


