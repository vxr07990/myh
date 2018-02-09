<?php

class TariffManager_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        /*
         * Now that the below is also commented we can probably comment this entire extend.
        $db = PearDatabase::getInstance();
        $recordId = $request->get('record');

        if(!isset($recordId) || strlen($recordId) < 1) {
            //No recordId provided in $request - New Record
            $sql = "SELECT id FROM `vtiger_crmentity_seq`";
            $result = $db->pquery($sql, array());
            $row = $result->fetchRow();
            $expectedId = intval($row[0]) + 1;
        }
        */
        parent::process($request);

        /*
         * This doesn't seem to be used.  Commenting it out to be sure.
        $sql = "SELECT tariffmanagerid FROM `vtiger_tariffmanager` ORDER BY tariffmanagerid DESC LIMIT 1";
        $result = $db->pquery($sql, array());
        $row = $result->fetchRow();
        if(isset($expectedId) && $row[0] != $expectedId) {
            //Error case:
            //This is a new record and the expected record id does not match the id of the newly
            //saved record in the database, so do not proceed with custom save functionality
            return;
        } else if(isset($expectedId)) {
            //Expected record id matches the id of the newly saved record, so proceed with custom
            //save functionality
            $recordId = $row[0];
        }
        */

        //file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').print_r($request, true)."\n", FILE_APPEND);
        /* foreach($request->getAll() as $fieldName=>$value) {
            file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').$fieldName.' - '.$value."\n", FILE_APPEND);
            if(substr($fieldName, 0, 7) == 'Vanline') {
                $vanlineId = strstr(substr($fieldName, 7), 'State', true);
                $applyToAllAgents = $request->get('assignVanline'.$vanlineId.'Agents') == 'on' ? 1 : 0;

                $sql = "SELECT vanlineid, tariffid FROM `vtiger_tariff2vanline` WHERE vanlineid=? AND tariffid=?";
                $result = $db->pquery($sql, array($vanlineId, $recordId));
                $row = $result->fetchRow();

                $params = array();

                if($row != NULL && $value == 'unassigned') {
                    //Assignment exists, but should be removed
                    $sql = "DELETE FROM `vtiger_tariff2vanline` WHERE vanlineid=? AND tariffid=?";
                    $params[] = $vanlineId;
                    $params[] = $recordId;
                } else if($row == NULL && $value == 'assigned') {
                    //Assignment does not exist, but should be added
                    $sql = "INSERT INTO `vtiger_tariff2vanline` (vanlineid, tariffid, apply_to_all_agents) VALUES (?,?,?)";
                    $params[] = $vanlineId;
                    $params[] = $recordId;
                    $params[] = $applyToAllAgents;
                } else if($row != NULL) {
                    //Assignment exists and should - update apply_to_all_agents column
                    $sql = "UPDATE `vtiger_tariff2vanline` SET apply_to_all_agents=? WHERE vanlineid=? AND tariffid=?";
                    $params[] = $applyToAllAgents;
                    $params[] = $vanlineId;
                    $params[] = $recordId;
                } else {
                    //Assignment is already in correct state
                    $sql = NULL;
                }

                if(isset($sql)) {
                file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').$sql."\n".print_r($params, true)."\n", FILE_APPEND);
                    $result = $db->pquery($sql, $params);
                }
            } else if(substr($fieldName, 0, 11) == 'assignAgent') {
                preg_match('/\d/', $fieldName, $m, PREG_OFFSET_CAPTURE);
                //file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').print_r($m, true)."\n", FILE_APPEND);
                $agentId = substr($fieldName, $m[0][1]);

                $sql = "SELECT agentid, tariffid FROM `vtiger_tariff2agent` WHERE agentid=? AND tariffid=?";
                $result = $db->pquery($sql, array($agentId, $recordId));
                $row = $result->fetchRow();

                if($row != NULL && $value == '0') {
                    //Assignment exists, but should be removed
                    $sql = "DELETE FROM `vtiger_tariff2agent` WHERE agentid=? AND tariffid=?";
                } else if($row == NULL && $value == 'on') {
                    //Assignment does not exist, but should be added
                    $sql = "INSERT INTO `vtiger_tariff2agent` (agentid, tariffid) VALUES (?,?)";
                } else {
                    //Assignment is already in correct state
                    $sql = NULL;
                }

                //file_put_contents('logs/TariffManagerSave.log', date('Y-m-d H:i:s - ').$sql."\n", FILE_APPEND);

                if(isset($sql)) {
                    $result = $db->pquery($sql, array($agentId, $recordId));
                }
            }
        } */
    }
}
