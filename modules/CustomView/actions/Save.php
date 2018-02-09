<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class CustomView_Save_Action extends Vtiger_Action_Controller
{
    public function process(Vtiger_Request $request)
    {
		$sourceModule = $request->get('source_module');
        $moduleModel = Vtiger_Module_Model::getInstance($sourceModule);
        $customViewModel = $this->getCVModelFromRequest($request);
        $customViewModel->set('assignToAgent', $request->get('assignToAgent'));
        $customViewModel->set('assignedAgent', $request->get('assignedAgent'));
        $customViewModel->set('sort_field', $request->get('sort_field'));
        $customViewModel->set('sort_order', $request->get('sort_order'));
        $response = new Vtiger_Response();

        if (!$customViewModel->checkDuplicate()) {

            $customViewModel->save();
            $cvId = $customViewModel->getId();

            if($cvId && $request->get('setdefault') == '1'){
                $currentUserModel = Users_Record_Model::getCurrentUserModel();
                $db = PearDatabase::getInstance();
                $result = $db->pquery('UPDATE vtiger_customview SET setdefault=0 WHERE cvid!=? AND entitytype=? AND view=? AND userid=? AND agentmanager_id=?', [$cvId, $request->get('source_module'), $customViewModel->get('sourceModuleView'), $currentUserModel->getId(),$customViewModel->get('assignedAgent')]);
            }



            if($request->get('fromRelatedList') == 1) {
                $pieces = explode('&', $_SERVER['HTTP_REFERER']);
                foreach($pieces as &$piece) {
                    if(substr($piece, 0, 8) == 'viewname') {
                        $piece = 'viewname='.$cvId;
                    }
                }
                $url = implode('&', $pieces);
            }else if(isset($_REQUEST["source_module_view"]) && $_REQUEST["source_module_view"] == "NewLocalDispatch" && isset($_REQUEST["rightTable"]) && $_REQUEST["rightTable"] !== ""){
				$url = "index.php?module=OrdersTask&view=NewLocalDispatch";
			}elseif(!isset($_REQUEST["source_module_view"]) && isset($_REQUEST['sourceModuleView']) && $_REQUEST['sourceModuleView'] != ''){
				$url = "index.php?module=" . $sourceModule . "&view=" . $_REQUEST['sourceModuleView'] . "&viewname=".$cvId;
                                unset($_SESSION['lvs'][$sourceModule.'-'.$request->get('sourceModuleView')]["viewname"]);
			}else{
				$url = $moduleModel->getListViewUrl().'&viewname='.$cvId;
			}
            $response->setResult(array('id'=>$cvId, 'listviewurl'=>$url));
        } else {
            $response->setError(vtranslate('LBL_CUSTOM_VIEW_NAME_DUPLICATES_EXIST', $sourceModule));
        }

        $response->emit();
    }

    /**
     * Function to get the custom view model based on the request parameters
     * @param Vtiger_Request $request
     * @return CustomView_Record_Model or Module specific Record Model instance
     */
    private function getCVModelFromRequest(Vtiger_Request $request)
    {
        $cvId = $request->get('record');

        if (!empty($cvId)) {
            $customViewModel = CustomView_Record_Model::getInstanceById($cvId);
        } else {
            $customViewModel = CustomView_Record_Model::getCleanInstance();
            $customViewModel->setModule($request->get('source_module'));
        }

        $customViewData = array(
                    'cvid' => $cvId,
                    'viewname' => $request->get('viewname'),
                    'setdefault' => $request->get('setdefault'),
                    'setmetrics' => $request->get('setmetrics'),
                    'status' => $request->get('status'),
					'sourceModuleView' => $request->get('sourceModuleView'),
        );
        $selectedColumnsList = $request->get('columnslist');
        if (!empty($selectedColumnsList)) {
            $customViewData['columnslist'] = $selectedColumnsList;
        }
        $stdFilterList = $request->get('stdfilterlist');
        if (!empty($stdFilterList)) {
            $customViewData['stdfilterlist'] = $stdFilterList;
        }
        $advFilterList = $request->get('advfilterlist');
        if (!empty($advFilterList)) {
            $customViewData['advfilterlist'] = $advFilterList;
        }
        $resourcewidth = $request->get('resourcewidth');
        if (!empty($resourcewidth)) {
            $customViewData['resourcewidth'] = $resourcewidth;
        }
        $resourcecollapsed = $request->get('resourcecollapsed');
        if (!empty($resourcecollapsed)) {
            $customViewData['resourcecollapsed'] = $resourcecollapsed;
        }
        return $customViewModel->setData($customViewData);
    }

    public function validateRequest(Vtiger_Request $request)
    {
        $request->validateWriteAccess();
    }
}
