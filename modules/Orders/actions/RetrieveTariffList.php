<?php

class Orders_RetrieveTariffList_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $assignedTo = $request->get('assigned_to');
        $business_line  = $request->get('business_line');
		$commodity = $request->get('commodity');
        $agentid  = $request->get('agentid');
        $assignedUserModel = Users_Record_Model::getInstanceById($assignedTo, 'Users');

        if(!$agentid) {
            $userAgents        = $assignedUserModel->getAccessibleOwnersForUser();
        } else {
            $userAgents = $assignedUserModel->getAccessibleVanlinesForUser();
            $userAgents[$agentid] = '';
        }

	    $workspaceArray = ['Commercial - Distribution', 'Commercial - Record Storage','Commercial - Storage', 'Commercial - Asset Management','Commercial - Project', 'Work Space - MAC','Work Space - Special Services', 'Work Space - Commodities'];


	if(getenv('INSTANCE_NAME') == 'graebel' && in_array($business_line, $workspaceArray)){
	    $tariffsModel = new Tariffs_Module_Model;
		$tariffInfo = $tariffsModel->retrieveTariffsByAgencies($userAgents, $business_line);
	//}  elseif ($business_line == 'Local Move') {
	//}  elseif (!in_array($business_line, $workspaceArray)) {
        //@TODO:  review if this is correct formating for calling this module_model
        //done the same in modules/Orders/views/Edit.php
    //    $tariffsModel = new Tariffs_Module_Model;
	//	$tariffInfo = $tariffsModel->retrieveTariffsByAgencies($userAgents, $business_line,$commodity);
    } else {
	//if not graebel, then merge LocalTariffs & TariffManagers records :) - Only when Business Line not local
		$localTariffsModel = new Tariffs_Module_Model;
		$localTariffInfo = $localTariffsModel->retrieveTariffsByAgencies($userAgents, $business_line,$commodity);
        if($business_line != 'Local'){
            $tariffsManagerModel = new TariffManager_Module_Model;
		    $tariffManagerInfo = $tariffsManagerModel->retrieveTariffsByAgencies($userAgents, $business_line);
        }else{
            $tariffManagerInfo['tariffNames'] = $tariffManagerInfo['tariffTypes'] = $tariffManagerInfo['tariffJS'] =  [];
        }


		$tariffInfo['tariffNames'] = $localTariffInfo['tariffNames'] + $tariffManagerInfo['tariffNames'];
		$tariffInfo['tariffTypes'] = $localTariffInfo['tariffTypes'] + $tariffManagerInfo['tariffTypes'];
		$tariffInfo['tariffJS'] = $localTariffInfo['tariffJS'] + $tariffManagerInfo['tariffJS'];
    }

        $info                  = [];
        $info['tariffs']       = $tariffInfo['tariffNames'];
        $info['tariffTypes']   = $tariffInfo['tariffTypes'];
        $info['tariffScripts'] = $tariffInfo['tariffJS'];
        //$tariffIds = $tariffInfo['tariffIds'];
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
