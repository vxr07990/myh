<?php
use \Dropbox as dbx;

class Users_GetMoveCoordinator_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();

        $current_user_id = $request->get('current_user_id');
        $agent_ids_order = $request->get('agent_ids_order');

        if (empty($agent_ids_order)) {
            $sql    = "SELECT agent_ids FROM `vtiger_users` WHERE id = ?";
            $result = $db->pquery($sql, [$current_user_id]);
            $row    = $result->fetchRow();
            $agent_ids_order = explode(" |##| ", $row['agent_ids']);
        } else {
            $agent_ids_order = explode(",", $agent_ids_order);
        }

        if (!empty($agent_ids_order)) {
            $salesManagerRole = "H9";
            $suppManagerRole = "H10";
            $coordRole = "H11";

            foreach ($agent_ids_order as $agentManagerId) {
                $sql    = "SELECT vtiger_users.id, vtiger_users.first_name, vtiger_users.last_name
                           FROM `vtiger_users`
                           JOIN `vtiger_user2role` ON vtiger_users.id=vtiger_user2role.userid
                           WHERE (agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids = ?)
                            AND vtiger_users.deleted = 0
                            AND vtiger_users.status = 'Active'
                           AND   vtiger_user2role.roleid IN (?,?,?)";
                $result = $db->pquery($sql, ['% '.$agentManagerId, '% '.$agentManagerId.' %', $agentManagerId.' %', $agentManagerId, $salesManagerRole, $suppManagerRole, $coordRole]);

                while ($row =& $result->fetchRow()) {
                    $coordinators[$row['id']] = ['id' => $row['id'], 'first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'agent_id' => $agentManagerId];
                }
            }

            $info = [];

            foreach ($coordinators as $user) {
                if (!empty($user['id'])) {
                    $sql    = "SELECT agent_ids FROM `vtiger_users` WHERE id=?";
                    $result = $db->pquery($sql, [$user['id']]);
                    $row    = $result->fetchRow();
                    $agent_ids = explode(" |##| ", $row['agent_ids']);
                    $agent_ids = array_intersect($agent_ids, $agent_ids_order);
                    //  file_put_contents('logs/devLog.log', "\n info ".print_r($agent_ids, true), FILE_APPEND);
                    $vanlineIds = [];
                    foreach ($agent_ids as $agent_id) {
                        $sql2    = "SELECT vanline_id FROM vtiger_agentmanager WHERE agentmanagerid = ?";
                        $result2 = $db->pquery($sql2, [$agent_id]);
                        $row2    = $result2->fetchRow();
                        $vanlineIds[$row2['vanline_id']] = $row2['vanline_id'];
                    }
                    foreach ($vanlineIds as $vanline_id) {
                        $info[] = ['id' => $user['id'], 'first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'vanline_id' => $vanline_id];
                    }
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
