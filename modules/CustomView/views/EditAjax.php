<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class CustomView_EditAjax_View extends Vtiger_IndexAjax_View
{
    public function process(Vtiger_Request $request)
    {
        $viewer                  = $this->getViewer($request);
        $moduleName              = $request->get('source_module');
        $module                  = $request->getModule();
        $record                  = $request->get('record');
        $moduleModel             = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
        $current_user            = Users_Record_Model::getCurrentUserModel();

        $db = &PearDatabase::getInstance();
        $extraBlocks = [];
        if(QueryGenerator::isCustomViewGuestModulesEnabled($moduleName)) {
            $guests = self::getGuestBlocks($moduleName);
            $viewer->assign('GUEST_BLOCK_DATA', $guests);
        }

        if ($moduleName == 'Orders' && getenv('INSTANCE_NAME') == 'graebel') {
            $agentFields = [];
            $res = $db->pquery('SELECT agent_type FROM vtiger_agent_type');
            while ($row = $res->fetchRow()) {
                $data = [];
                $name = $row['agent_type'];
                $data['name'] = $name;
                $data['value'] = "vtiger_participatingagents:agents_id:rel_crmid:$name:V:agent_type:$name";
                $agentFields[] = $data;
            }
            $extraBlocks[] =
                [
                    'ParticipatingAgents' => [
                        'module' => 'ParticipatingAgents',
                        'fields' => $agentFields
                    ]
                ];
        }

		if($moduleName == "OrdersTask" && isset($_REQUEST['iscalendar']) && $_REQUEST['iscalendar'] == 'yes'){
			$viewer->assign('iscalendar', 'yes');
		}else{
			$viewer->assign('iscalendar', 'no');
		}

        $viewer->assign('EXTRA_BLOCKS', $extraBlocks);	
        $viewer->assign('EFFECTIVE_TARIFF_PICKLIST', Estimates_Record_Model::getAllowedTariffsForListView());
        $viewer->assign('SALES_PERSON_PICKLIST', Opportunities_Record_Model::getCleanInstance('Opportunities')->getSalesPeopleByOwner());
        if (!empty($record)) {
            $customViewModel = CustomView_Record_Model::getInstanceById($record);
			
			$view = $customViewModel->get("view");
		
			switch($view){
				case 'NewLocalDispatchCrew':
					$moduleName = "Employees";
					break;
				case 'NewLocalDispatchEquipment':
					$moduleName = "Vehicles";
					break;
				case 'NewLocalDispatchVendors':
					$moduleName = "Vendors";
					break;
			}
			
			$moduleModel             = Vtiger_Module_Model::getInstance($moduleName);
            $viewer->assign('MODE', 'edit');
        } else {
            $customViewModel = new CustomView_Record_Model();
            $customViewModel->setModule($moduleName);
            $viewer->assign('MODE', '');
        }
        $viewer->assign('ADVANCE_CRITERIA', $customViewModel->transformToNewAdvancedFilter());
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('DATE_FILTERS', Vtiger_Field_Model::getDateFilterTypes());
        if ($moduleName == 'Calendar') {
            $advanceFilterOpsByFieldType = Calendar_Field_Model::getAdvancedFilterOpsByFieldType();
        } else {
            $advanceFilterOpsByFieldType = Vtiger_Field_Model::getAdvancedFilterOpsByFieldType();
        }
        $viewer->assign('ADVANCED_FILTER_OPTIONS', Vtiger_Field_Model::getAdvancedFilterOptions());
        $viewer->assign('ADVANCED_FILTER_OPTIONS_BY_TYPE', $advanceFilterOpsByFieldType);
        $dateFilters = Vtiger_Field_Model::getDateFilterTypes();
        foreach ($dateFilters as $comparatorKey => $comparatorInfo) {
            $comparatorInfo['startdate'] = DateTimeField::convertToUserFormat($comparatorInfo['startdate']);
            $comparatorInfo['enddate']   = DateTimeField::convertToUserFormat($comparatorInfo['enddate']);
            $comparatorInfo['label']     = vtranslate($comparatorInfo['label'], $module);
            $dateFilters[$comparatorKey] = $comparatorInfo;
        }
        $viewer->assign('DATE_FILTERS', $dateFilters);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $recordStructure = $recordStructureInstance->getStructure();
        // for Inventory module we should now allow item details block
        if (in_array($moduleName, getInventoryModules())) {
            $itemsBlock = "LBL_ITEM_DETAILS";
            unset($recordStructure[$itemsBlock]);
        }
        $viewer->assign('RECORD_STRUCTURE', $recordStructure);
        // Added to show event module custom fields
        if ($moduleName == 'Calendar') {
            $relatedModuleName              = 'Events';
            $relatedModuleModel             = Vtiger_Module_Model::getInstance($relatedModuleName);
            $relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);
            $eventBlocksFields              = $relatedRecordStructureInstance->getStructure();
            $viewer->assign('EVENT_RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
            $viewer->assign('EVENT_RECORD_STRUCTURE', $eventBlocksFields);
        }

        //VGS - Conrado   OT16198 adding related module fields to the Local Dispatch filter creation.
        if ($moduleName == 'OrdersTask') {
            $relatedModuleName              = 'Orders';
            $relatedModuleModel             = Vtiger_Module_Model::getInstance($relatedModuleName);
            $relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel);
            $eventBlocksFields              = $relatedRecordStructureInstance->getStructure();

            $viewer->assign('ORDERS_RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
            $viewer->assign('ORDERS_RECORD_STRUCTURE', array_filter($eventBlocksFields));

            $relatedModuleName              = 'Trips';
            $relatedModuleModel             = Vtiger_Module_Model::getInstance($relatedModuleName);
            $relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel);
            $eventBlocksFields              = $relatedRecordStructureInstance->getStructure();

            $viewer->assign('TRIPS_RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
            $viewer->assign('TRIPS_RECORD_STRUCTURE', array_filter($eventBlocksFields));

            $relatedModuleName              = 'Estimates';
            $relatedModuleModel             = Vtiger_Module_Model::getInstance($relatedModuleName);
            $relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel);
            $eventBlocksFields              = $relatedRecordStructureInstance->getStructure();

            $viewer->assign('ESTIMATES_RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
            $viewer->assign('ESTIMATES_RECORD_STRUCTURE', array_filter($eventBlocksFields));
        }

        // Added to show Commission Plan Group fields
        if ($moduleName == 'CommissionPlans') {
            $relatedModuleName              = 'CommissionPlansFilter';
            $relatedModuleModel             = Vtiger_Module_Model::getInstance($relatedModuleName);
            $relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel);
            $eventBlocksFields              = $relatedRecordStructureInstance->getStructure();

            $viewer->assign('COMMISSIONPLANSFILTER_RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
            $viewer->assign('COMMISSIONPLANSFILTER_RECORD_STRUCTURE', $eventBlocksFields);
        }

        // Added to show ItemCodesMapping fields
        if ($moduleName == 'ItemCodes') {
            $relatedModuleName              = 'ItemCodesMapping';
            $relatedModuleModel             = Vtiger_Module_Model::getInstance($relatedModuleName);
            $relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel);
            $eventBlocksFields              = $relatedRecordStructureInstance->getStructure();

            $viewer->assign('ITEMCODESMAPPING_RECORD_STRUCTURE_MODEL', $relatedRecordStructureInstance);
            $viewer->assign('ITEMCODESMAPPING_RECORD_STRUCTURE', $eventBlocksFields);
        }


		//VGS -  OT17162 participating agents.
        $participantRows = [];
		if($moduleName == 'OrdersTask'){
            $accessibleAgents = Users_Record_Model::getCurrentUserModel()->getBothAccessibleOwnersIdsForUser();
			$result = $db->pquery("SELECT ag.* FROM vtiger_agents ag INNER JOIN vtiger_crmentity cr ON ag.agentsid = cr.crmid WHERE cr.deleted = 0 AND agentid IN (" . generateQuestionMarks($accessibleAgents) .  ")", [$accessibleAgents]);
			while ($arr = $db->fetch_array($result)){
				$participantRows[] = array("agentid" => $arr[agentsid], "agent_number" => $arr['agent_number'], "agentname" => $arr['agentname'], "agent" => $arr['agentname'] . " (" . $arr['agent_number'] . ")");
			}
		}
		$viewer->assign('HIDDEN_PARTICIPATING_AGENTS', htmlspecialchars(json_encode($participantRows)));

        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('IS_VANLINE_USER', $currentUser->isVanLineUser() ? 1 : 0);

        $viewer->assign('CUSTOMVIEW_MODEL', $customViewModel);
        $viewer->assign('ADMIN_USER', $current_user->is_admin);
        $viewer->assign('RECORD_ID', $record);
        $viewer->assign('MODULE', $module);
        $viewer->assign('SOURCE_MODULE', $moduleName);
        $viewer->assign('USER_MODEL', $currentUser);
        $viewer->assign('CV_PRIVATE_VALUE', CustomView_Record_Model::CV_STATUS_PRIVATE);
        $viewer->assign('CV_PENDING_VALUE', CustomView_Record_Model::CV_STATUS_PENDING);
        $viewer->assign('CV_PUBLIC_VALUE', CustomView_Record_Model::CV_STATUS_PUBLIC);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        //assemble agents with codes for assign to agent picklist
        $accessibleAgents = Users_Record_Model::getCurrentUserModel()->getAccessibleOwnersForUser(false);
        $availableAgents = [];
        foreach ($accessibleAgents as $agentId => $agentName) {
            if ($agentId != 'agents') {
                $availableAgents[$agentId] = $agentName;
            }
        }
        $viewer->assign('AVAILABLE_AGENTS', $availableAgents);
        $viewer->assign('IS_CUSTOMVIEWS', true);
        $permissionLevel = $this->getPermissionLevel();
        //file_put_contents('logs/test.log', date('Y-m-d H:i:s - ').print_r($permissionLevel, true)."\n", FILE_APPEND);
        $viewer->assign('DEPTH', $permissionLevel['Depth']);
        if ($moduleName == 'OrdersTask') {
            $resourceWidth = $customViewModel->getDefaultResourceWidth();
            $viewer->assign('DEFAULT_RESOURCE_WIDTH', $resourceWidth ? $resourceWidth : '25');
            $resourceCollapsed = $customViewModel->getDefaultResourceCollapsed();
            $viewer->assign('DEFAULT_RESOURCE_COLLAPSED', $resourceCollapsed == '1' ? 'yes' : 'no');
        }
        echo $viewer->view('EditView.tpl', $module, true);
    }
}
