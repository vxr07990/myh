<?php

class Estimates_UpdateLocalTariffs_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $assignedTo = $request->get('assigned_to');
        $businessLine = $request->get('business_line');
        $userAgents = array();
        $row = array();

        //file_put_contents('logs/devLog.log', "\n In UpdateLocalTariffs Action and assigned_to : ".$assignedTo, FILE_APPEND);
        $module = $request->getModule();
        $db = PearDatabase::getInstance();

        /*
         // this doesn't work with the new securities

        $sql = "SELECT groupname FROM `vtiger_groups` WHERE groupid=?";
        $result = $db->pquery($sql, array($assignedTo));
        $row = $result->fetchRow();
        //file_put_contents('logs/devLog.log', "\n After first SQL : ".$row[0], FILE_APPEND);
        $sql = "SELECT agentmanagerid FROM `vtiger_agentmanager` WHERE agency_name=?";
        $result = $db->pquery($sql, array($row[0]));
        $row = $result->fetchRow();
        //file_put_contents('logs/devLog.log', "\n After second SQL : ".$row[0], FILE_APPEND);
        $userAgents[] = $row[0];
        */

        $assignedUserModel = Users_Record_Model::getInstanceById($assignedTo, 'Users');
        $userAgents        = $assignedUserModel->getAccessibleAgentsForUser();

        $info = array();
        $info['userAgents'] = $userAgents;
        //file_put_contents('logs/devLog.log', "\n UpdateLocalTariff \$info : ".print_r($info,true), FILE_APPEND);
        //$info['currentTariffBlocks'] = $currentTariffBlocks;
        //$info['inactiveTariffBlocks'] = $blocksToHide;

        //OT 13515 about estimates showing extraneous options,
        // however trying to test as a user I got a permission denied in pre-save estimate caused hereish.
        //By doing this here we are bypassing the second call which hits the permission issue.
        // this also saves us from doing a second call.
        $estimatesModule = new Estimates_Edit_View();
        $request->set('view', 'Edit');
        $request->set('edit', 'true');
        $request->set('mode', 'updateLocalTariff');
        $request->set('userAgents', $userAgents);
        $request->set('current_business_line', $businessLine);
        $viewer = $estimatesModule->getViewer($request);
        // $estimatesModule->assignVars($viewer, $request);
        $viewer->assign('UPDATE_LOCAL', true);
        $viewer->assign('EDIT_VIEW', $request->get('edit'));
        //THIS is echoed from way deep down in smarty_internal_templatebase.php.
//        echo "{\"success\":true,\"result\":\"";
//        $estimatesModule->process($request);
//        echo "\"}";
        $response = new Vtiger_Response();
        $response->setResult($viewer->view('UpdateLocalTariffPicklist.tpl', $request->get('module'), true));
        $response->emit();
    }
}
