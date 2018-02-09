<?php

class Cubesheets_SaveAjax_Action extends Inventory_SaveAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        if ($request->get('field') == 'is_primary' && $request->get('value') == 'on') {
            $db = PearDatabase::getInstance();
            $sql = "SELECT `potential_id` FROM `vtiger_cubesheets` WHERE `cubesheetsid`=?";
            $params[] = $request->get('record');
            
            $result = $db->pquery($sql, $params);
            unset($params);
            $row = $result->fetchRow();
            $potentialid = $row[0];
            
            if ($potentialid != null) {
                $sql = "UPDATE `vtiger_cubesheets` SET `is_primary`=0 WHERE `potential_id`=?";
                $params[] = $potentialid;
                
                $result = $db->pquery($sql, $params);
                unset($params);
            }
        }
        parent::process($request);
    }
}
