<?php

require_once('modules/Emails/mail.php');

class AgentManager_SaveCoordinators_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        /*$db = PearDatabase::getInstance();
        $coordinatorTotal = $request->get('numCoordinators');
        $srcRecord = $request->get('numCoordinators');
        for($coordinatorCount = 1; $coordinatorCount <= $coordinatorTotal; $coordinatorCount++){
            $coordinatorId = $request['coordinatorId'.$coordinatorCount];
            $deleted = $request['coordinatorDeleted'.$coordinatorCount];
            if($deleted != 'DELETE'){
                $salesPerson = $request->get('sales_person'.$coordinatorCount);
                $coordinators = $request->get('coordinators'.$coordinatorCount);
                if($salesPerson || !$coordinators){continue;}
                if($coordinatorId != 0 && $coordinatorId != '0'){
                    $result = $db->pquery('UPDATE `vtiger_coordinators` SET sales_person = ?, coordinators = ?, agentmanagerid = ? WHERE coordinatorsid = ?', array($salesPerson, $coordinators, $recordId, $coordinatorId);
                } else{
                    $result = $db->pquery('SELECT id from `vtiger_coordinators_seq`', array());
                    $row = $result->fetchRow();
                    if($row[0]){
                        $coordinatorId = $row[0];
                    } else{
                        $coordinatorId = 0;
                        $result = $db->pquery('INSERT INTO `vtiger_coordinators_seq` SET id = ?', array(0));
                    }
                    $coordinatorId++;
                    $result = $db->pquery('UPDATE `vtiger_coordinators_seq` SET id = ?', array($coordinatorId));
                    $result = $db->pquery('INSERT INTO `vtiger_coordinators` (coordinatorsid, sales_person, coordinators, agentmanagerid) VALUES (?,?,?,?)', array($coordinatorId, $salesPerson, $coordinators, $recordId));
                }
            }
        }*/
        
        file_put_contents('logs/devLog.log', "\n hit SAVE COORDINATOR DOT PHP", FILE_APPEND);
        
        file_put_contents('logs/devLog.log', "\n REQUEST POST: ".print_r($request, true), FILE_APPEND);
        
        $response = new Vtiger_Response();
        $response->setResult('this is a response');
        $response->emit();
    }
}
