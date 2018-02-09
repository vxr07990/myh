<?php

class Quotes_SaveAjax_Action extends Inventory_SaveAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        if ($request->get('field') == 'is_primary' && $request->get('value') == 'on') {
            $db = PearDatabase::getInstance();
            $sql = "SELECT `potentialid` FROM `vtiger_quotes` WHERE `quoteid`=?";
            $params[] = $request->get('record');
            
            $result = $db->pquery($sql, $params);
            unset($params);
            $row = $result->fetchRow();
            $potentialid = $row[0];
            
            file_put_contents('logs/primary_save.log', "$potentialid\n", FILE_APPEND);
            
            if ($potentialid != null) {
                $sql = "UPDATE `vtiger_quotes` SET `is_primary`=0 WHERE potentialid=?";
                $params[] = $potentialid;
                
                $result = $db->pquery($sql, $params);
                unset($params);
            }
        }
        
        parent::process($request);
    }
}
