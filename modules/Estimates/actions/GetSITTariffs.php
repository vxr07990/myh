<?php

class Estimates_GetSITTariffs_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $assignedTo = $request->get('assigned_to');
        $db = PearDatabase::getInstance();
        $assignedUserModel = Users_Record_Model::getInstanceById($assignedTo, 'Users');
        $userAgents        = $assignedUserModel->getAccessibleAgentsForUser();

        $recordModel = Vtiger_Record_Model::getCleanInstance('Estimates');
        $recordModel->getCurrentUserTariffs(true, $userAgents);

        $sql = "SELECT vtiger_tariffs.tariffsid,vtiger_tariffs.tariff_name
                FROM vtiger_tariffs
		          JOIN `vtiger_crmentity`
		            ON `vtiger_tariffs`.`tariffsid` = `vtiger_crmentity`.`crmid`
		          JOIN `vtiger_agentmanager`
		            ON `vtiger_crmentity`.`agentid` = `vtiger_agentmanager`.agentmanagerid
                  JOIN vtiger_tariffservices
                    ON vtiger_tariffservices.related_tariff = vtiger_tariffs.tariffsid
                WHERE `vtiger_agentmanager`.`agentmanagerid` = ?
                  AND rate_type like '%SIT%'
                  AND deleted=0";
        $select = "<select class='chzn-select' name='local_tariff'>
		                <option value='0' selected>Select an Option</option>";
        $tariffs = [];
        foreach ($userAgents as $agencyId => $agencyName) {
            $result = $db->pquery($sql, [$agencyId]);
            while ($row =& $result->fetchRow()) {
                if (!isset($tariffs[$row['tariffsid']])) {
                    $tariffs[$row['tariffsid']] = $row['tariff_name'];
                    $select .= "<option value='".$row['tariffsid']."'>".$row['tariff_name']."</option>";
                }
            }
        }
        $select .= "</select>";

        $result = ['success'=>true,'result'=>$select];
        echo json_encode($result);
    }
}
