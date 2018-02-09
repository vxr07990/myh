<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Vtiger MenuStructure Model
 */
class Vtiger_MenuStructure_Model extends Vtiger_Base_Model
{
    protected $limit = 5; // Max. limit of persistent top-menu items to display.
    protected $enableResponsiveMode = true; // Should the top-menu items be responsive (width) on UI?

    const TOP_MENU_INDEX = 'top';
    const MORE_MENU_INDEX = 'more';

    /**
     * Function to get all the top menu models
     * @return <array> - list of Vtiger_Menu_Model instances
     */
    public function getTop()
    {
        return $this->get(self::TOP_MENU_INDEX);
    }

    /**
     * Function to get all the more menu models
     * @return <array> - Associate array of Parent name mapped to Vtiger_Menu_Model instances
     */
    public function getMore()
    {
        $moreTabs = $this->get(self::MORE_MENU_INDEX);
        foreach ($moreTabs as $key => $value) {
            if (!$value) {
                unset($moreTabs[$key]);
            }
        }
        return $moreTabs;
    }

    /**
     * Function to get the limit for the number of menu models on the Top list
     * @return <Number>
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Function to determine if the structure should support responsive UI.
     */
    public function getResponsiveMode()
    {
        return $this->enableResponsiveMode;
    }

    /**
     * Function to get an instance of the Vtiger MenuStructure Model from list of menu models
     * @param <array> $menuModelList - array of Vtiger_Menu_Model instances
     * @return Vtiger_MenuStructure_Model instance
     */
    public static function getInstanceFromMenuList($menuModelList, $selectedMenu = '')
    {
        $structureModel = new self();
        $topMenuLimit = $structureModel->getResponsiveMode() ? 0 : $structureModel->getLimit();
        $currentTopMenuCount = 0;

        $menuListArray = array();
        $menuListArray[self::TOP_MENU_INDEX] = array();
        $menuListArray[self::MORE_MENU_INDEX] = $structureModel->getEmptyMoreMenuList();

        $moduleCreator = Vtiger_MenuStructure_Model::getModuleCreatorRecord();
        if (count($moduleCreator) > 0) {
			$MenuCreatorId = Vtiger_MenuStructure_Model::getModuleCreatorID();
			$MenuCreatorModel = Vtiger_Module_Model::getInstance('MenuCreator');
			$menuEditorModules = $MenuCreatorModel->getMenuEditorModules(intval($MenuCreatorId[0]));
			if(count($MenuCreatorId) > 0 && count($menuEditorModules) > 0){
				foreach ($menuEditorModules as $menuModel) {
					if (!$topMenuLimit || $currentTopMenuCount < $topMenuLimit) {
						$menuListArray[self::TOP_MENU_INDEX][$menuModel->get('name')] = $menuModel;
						$currentTopMenuCount++;
					}
				}
			}else{ //this would be the default TOP_MENU_INDEX
				foreach ($menuModelList as $menuModel) {
					if (!$menuModel->isActive()) {
						continue;
					}
					if (($menuModel->get('tabsequence') != -1 && (!$topMenuLimit || $currentTopMenuCount < $topMenuLimit))) {
						$menuListArray[self::TOP_MENU_INDEX][$menuModel->get('name')] = $menuModel;
						$currentTopMenuCount++;
					}
				}
			}
            foreach ($moduleCreator as $parent => $modulesList) {
                foreach ($modulesList as $moduleName) {
                    $moduleModel=Vtiger_Module_Model::getInstance($moduleName);
                    if($moduleModel) {
                        if (!$moduleModel->isActive()) {
                            continue;
                        }
                        $menuListArray[self::MORE_MENU_INDEX][$parent][$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
                    }

                }
            }

            return $structureModel->setData($menuListArray);

        } else {
            foreach ($menuModelList as $menuModel) {
                if (!$menuModel->isActive()) {
                    continue;
                }
                if (($menuModel->get('tabsequence') != -1 && (!$topMenuLimit || $currentTopMenuCount < $topMenuLimit))) {
                    $menuListArray[self::TOP_MENU_INDEX][$menuModel->get('name')] = $menuModel;
                    $currentTopMenuCount++;
                }

                $parent = $menuModel->get('parent');
                if ($parent == 'Sales' || $parent == 'Marketing') {
                    $parent = 'MARKETING_AND_SALES';
                }
                $menuListArray[self::MORE_MENU_INDEX][strtoupper('lbl_' . $parent)][$menuModel->get('name')] = $menuModel;
            }

            if (!empty($selectedMenu) && !array_key_exists($selectedMenu, $menuListArray[self::TOP_MENU_INDEX])) {
                $selectedMenuModel = $menuModelList[$selectedMenu];
                if ($selectedMenuModel) {
                    $menuListArray[self::TOP_MENU_INDEX][$selectedMenuModel->get('name')] = $selectedMenuModel;
                }
            }

            // Apply custom comparator
            foreach ($menuListArray[self::MORE_MENU_INDEX] as $parent => &$values) {
                uksort($values, array('Vtiger_MenuStructure_Model', 'sortMenuItemsByProcess'));
            }
            //uksort($menuListArray[self::TOP_MENU_INDEX], array('Vtiger_MenuStructure_Model', 'sortMenuItemsByProcess'));

            return $structureModel->setData($menuListArray);
        }
    }

    /**
     * Custom comparator to sort the menu items by process.
     * Refer: http://php.net/manual/en/function.uksort.php
     */
    public static function sortMenuItemsByProcess($a, $b)
    {
        static $order = null;
        if ($order == null) {
            $order = array(
                //Opperations
                'Calendar',
                'LocalDispatch',
                'LongDistancePlanning',
                'ResourceDashboard',
                'TimeSheets',
                //Sales and Marketing
                'Campaigns',
                'Leads',
                'Opportunities',
                'Surveys',
                'Estimates',

                //Opperations
                'Orders',
                'LocalDispatch',
                'LongDistanceDispatch',
                'Trips',
                'Accounts',
                'Contracts',
                'MovePolicies',
                //Common Services
                'Contacts',
                'Documents',
                'Reports',
                'Calendar',
                'HelpDesk',
                // Accounting & Financial Services
                'Actuals',
                'Storage',
                'Claims',
                //System Admin
                'AgentManager',
                'VanlineManager',
                'MailManager',
                'TariffManager',

                //tools
                'EmailTemplates',
                'SMSNotifier',
                'AdvancedReports',
                'PDFMaker',

                //Customer Service
                'Project',
                'Claims',
                'Documents',
                //Customers
                'Accounts',
                'Contacts',

                //Company Admin
                'Agents',
                'Vehicles',
                'Facilities',
                'Partners',
                'Vendors',
                'Equipment',
                'Employees',
                'Contractors',
                'TariffManager',
                //Finance
                'Rating',
                'Export',
                'Payroll',
                'Invoice',
                'Deposits',
                'Distribution',
                //Storage
                'Assets',
                'Warehouses',
                'WarehouseInventory',

                //Unused
                'SalesOrder',
                'HelpDesk',
                'Faq',
                'Project',
                'Assets',
                'ServiceContracts',
                'Products',
                'Services',
                'PriceBooks',
                'Vendors',
                'PurchaseOrder',
                'MailManager',
                'Calendar',
                'Documents',
                'SMSNotifier',
                'RecycleBin'
            );
        }
        $apos = array_search($a, $order);
        $bpos = array_search($b, $order);

        if ($apos === false) {
            return PHP_INT_MAX;
        }
        if ($bpos === false) {
            return -1 * PHP_INT_MAX;
        }

        return ($apos - $bpos);
    }


    private function getEmptyMoreMenuList()
    {

        global $adb;
        $results = $this->getModuleCreatorRecord();

        if (count($results) > 0) {
            $emptyArray = array();
            foreach ($results as $blocklbl => $modulelist) {
                $emptyArray[$blocklbl] = array();
            }
            return $emptyArray;
        } else {
            return array('SALES_MARKETING_TAB' => array(), 'OPERATIONS_TAB' => array(), 'COMMON_SERVICES_TAB' => array(), 'FINANCE_TAB' => array(), 'SYSTEM_ADMIN_TAB' => array(), 'TOOLS_TAB' => array());
        }
    }

	function getModuleCreatorID(){
        global $adb, $current_user;
        $currentUser = Users_Record_Model::getInstanceById($current_user->id, 'Users');
        $MenuCreatorId = Vtiger_Cache::get('menu_creator','user_menu_id_' . $currentUser->id);

        if($MenuCreatorId && !is_null($MenuCreatorId) && !empty($MenuCreatorId)){
            return $MenuCreatorId;
        }
        
        //Conrado: I think this does not apply to SIRVA. Dont break their menu
        $MenuCreatorId = [];

        if (getenv('INSTANCE_NAME') != 'sirva') {
            if($currentUser->isVanlineUser()){
                //Look by vanline id

                $vanlinesList = array_keys($currentUser->getAccessibleVanlinesForUser());                
                $query = "SELECT * FROM `vtiger_menucreator` a INNER JOIN `vtiger_crmentity` b ON a.`menucreatorid` = b.`crmid` WHERE (b.`description` !=?  OR ISNULL(`description`)) AND b.`deleted` = 0 AND  b.`agentid` IN (" . generateQuestionMarks($vanlinesList) . ") LIMIT 1";
                $rsParent = $adb->pquery($query, array('DEFAULT_MENU',$vanlinesList));
                if ($adb->num_rows($rsParent) > 0) {
                    $MenuCreatorId[] = $adb->query_result($rsParent, 0, 'menucreatorid');
                }
                                
            }else{
                //Look by the assigned agents and if not present fallback to vanline
                $agentsVanlinesList = $currentUser->getBothAccessibleOwnersIdsForUser();
                $vanlinesList = array_keys($currentUser->getAccessibleVanlinesForUser());

                //Let search first by agents id

                $agentsList = array_diff($agentsVanlinesList, $vanlinesList);

                $query = "SELECT * FROM `vtiger_menucreator` a INNER JOIN `vtiger_crmentity` b ON a.`menucreatorid` = b.`crmid` WHERE (b.`description` !=?  OR ISNULL(`description`)) AND b.`deleted` = 0 AND  b.`agentid` IN (" . generateQuestionMarks($agentsList) . ") LIMIT 1";
                $rsParent = $adb->pquery($query, array('DEFAULT_MENU',$agentsList));
                if ($adb->num_rows($rsParent) > 0) {
                    $MenuCreatorId[] = $adb->query_result($rsParent, 0, 'menucreatorid');
                }

                //Search by vanline id
                if(empty($MenuCreatorId)){
                    $query = "SELECT * FROM `vtiger_menucreator` a INNER JOIN `vtiger_crmentity` b ON a.`menucreatorid` = b.`crmid` WHERE (b.`description` !=?  OR ISNULL(`description`)) AND b.`deleted` = 0 AND  b.`agentid` IN (" . generateQuestionMarks($vanlinesList) . ") LIMIT 1";                    
                    $rsParent = $adb->pquery($query, array('DEFAULT_MENU',$vanlinesList));
                    if ($adb->num_rows($rsParent) > 0) {
                        $MenuCreatorId[] = $adb->query_result($rsParent, 0, 'menucreatorid');
                    }
                }


            }
        }else{
            $agentidList = $currentUser->getAccessibleOwnersForUser("MenuCreator");
            unset($agentidList['agents']);
            unset($agentidList['vanlines']);

            $agentid = array_keys($agentidList);
            $query = "SELECT * FROM `vtiger_agentmanager` WHERE `agentmanagerid` IN ( " . generateQuestionMarks($agentid) . ")";
            $parentAgent = $adb->pquery($query, array($agentid));
            $resultsParentAgent = [];
            if ($adb->num_rows($parentAgent) > 0) {
                while ($rowParent = $adb->fetchByAssoc($parentAgent)) {
                    if ($rowParent['cf_agent_manager_parent_id'] != null) {
                        $resultsParentAgent[] = $rowParent['cf_agent_manager_parent_id'];
                    }
                }
            }
            if (!empty($resultsParentAgent)){
                foreach ($resultsParentAgent as $parentId) {
                    if(($key = array_search($parentId, $agentid)) !== false) {
                        unset($agentid[$key]);
                    }
                }
            }
    
            
            if(empty($MenuCreatorId) && !empty($resultsParentAgent))
            {
                $query = "SELECT * FROM `vtiger_menucreator` a INNER JOIN `vtiger_crmentity` b ON a.`menucreatorid` = b.`crmid` WHERE (`description` !=? OR ISNULL(`description`)) AND b.`deleted` = 0 AND b.`agentid` IN (" . generateQuestionMarks($resultsParentAgent) . ") LIMIT 1";
                $rsParent = $adb->pquery($query, array('DEFAULT_MENU',$resultsParentAgent));
                if ($adb->num_rows($rsParent) > 0) {
                    $MenuCreatorId[] = $adb->query_result($rsParent, 0, 'menucreatorid');
                }
            }
        }


        if($MenuCreatorId && !is_null($MenuCreatorId) && !empty($MenuCreatorId)){
            Vtiger_Cache::set('menu_creator','user_menu_id_' . $currentUser->id, $MenuCreatorId);
        }
		
		return $MenuCreatorId;
	}
	
    //@TODO: This appears to be very similar to: modules/MenuCleaner/views/List.php::process()
    function getModuleCreatorRecord(){
		global $adb, $current_user;
        if (!getenv('IGC_MOVEHQ')) {
            return [];
        }
        if (getenv('INSTANCE_NAME') == 'graebel') {
            return [];
        }

		$MenuCreatorId = Vtiger_MenuStructure_Model::getModuleCreatorID(); //can't use $this gives fatal error for non object context or something like that

        $results = [];
        if(getenv('IGC_MOVEHQ') == 1) {
            $sql = "SELECT a.group_name, a.group_sequence, a.group_module, agentid FROM `vtiger_menugroups` a";
            $sql .= " INNER JOIN `vtiger_crmentity` c ON c.crmid = a.menugroupsid ";
            $sql .= " WHERE group_name !=? AND deleted = 0 AND a.`menucreator_id` = ? AND (`description`!=? OR ISNULL(`description`)) ORDER BY group_sequence";
            $rs = $adb->pquery($sql, array('Menu Shortcuts', $MenuCreatorId[0],'DEFAULT_MENU'));
            if ($adb->num_rows($rs) > 0) {
                while ($row = $adb->fetchByAssoc($rs)) {
                    $results[$row['group_name']] = explode(' |##| ', $row['group_module']);
                }
            }
            if(count($results) == 0) {
                $results= Vtiger_MenuStructure_Model::getDefaultMenu();
            }
        }

        return $results;
    }
	
    function getDefaultMenu(){
        global $adb;
        $CreatorId = $adb->pquery("SELECT * FROM `vtiger_crmentity` WHERE `description` =? LIMIT 1 ",array("DEFAULT_MENU"));
        if ($adb->num_rows($CreatorId)){
            $rsCreatorId = $adb->query_result($CreatorId,0,'crmid');
        }
        $rsMenuGroup = [];
        if(getenv('IGC_MOVEHQ') == 1) {
            $menuGroups= $adb->pquery("SELECT * FROM `vtiger_menugroups` WHERE `group_name` !=? AND `menucreator_id`=? ORDER BY group_sequence ",array('Menu Shortcuts',$rsCreatorId));
            if ($adb->num_rows($menuGroups)){
                while ($row = $adb->fetchByAssoc($menuGroups)){
                    $rsMenuGroup[$row['group_name']] = explode(' |##| ', $row['group_module']);
                }
            }
        }
        return $rsMenuGroup;
    }

}
