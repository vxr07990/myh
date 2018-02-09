<?php
vimport('~~/vtlib/Vtiger/Module.php');
require_once('include/Webservices/Revise.php');
require_once('include/Webservices/Create.php');
require_once('modules/Users/Users.php');
require_once('includes/main/WebUI.php');

class MenuGroups_Module_Model extends Vtiger_Module_Model {
    public function setViewerForMenuGroups(&$viewer, $recordId = false){
        $moduleFields = $this->getFields('LBL_MENUGROUPS_INFORMATION');

        $viewer->assign('MENUGROUPS_LIST', $this->getMenuGroups($recordId));

        $viewer->assign('MENUGROUPS_BLOCK_FIELDS', $moduleFields);
    }

    /**
     * @param $recordId (menucreator record id)
     *
     * @return array
     */
    public function getMenuGroupsByRelatedID($recordId){
        $groupNames = [];

        if (!$recordId) {
            return $groupNames;
        }

        $adb = &PearDatabase::getInstance();
        $stmt = 'SELECT vtiger_menugroups.menugroupsid
                FROM vtiger_menugroups
                INNER JOIN vtiger_crmentity ON vtiger_menugroups.menugroupsid=vtiger_crmentity.crmid
                WHERE deleted=0
                AND menucreator_id=?';

        $rs = $adb->pquery($stmt,[$recordId]);
        if (method_exists($rs, 'fetchRow')) {
            while ($row = $rs->fetchRow()) {
                try {
                    $recordModel = Vtiger_Record_Model::getInstanceById($row['menugroupsid']);
                    if ($recordModel->getModuleName() == 'MenuGroups') {
                        $groupNames[$row['menugroupsid']] = Vtiger_Record_Model::getInstanceById($row['menugroupsid']);
                    }
                } catch (Exception $e) {
                    //suppress don't care hide it.
                }
            }
        }
        return $groupNames;
    }

    public function getMenuGroups($recordId){
        $MenuGroups=array();

        if($recordId) {
            $MenuGroups = self::getMenuGroupsByRelatedID($recordId);
        }else{
            // get default module list for new module record

            //@NOTE: getSearchable checks for isActive, and detail view permissions. but we need to check for list view privileges
            //$allModelsList     = Vtiger_Module_Model::getSearchableModules();
            $allModelsList = Vtiger_Module_Model::getAll(array('0','2'));
            $group_module = MenuGroups_Module_Model::returnMenuModels($allModelsList, true);

            $menuGroupRecordModel= Vtiger_Record_Model::getCleanInstance('MenuGroups');
            $menuGroupRecordModel->set('menugroupsid','');
            $menuGroupRecordModel->set('group_name','Menu Shortcuts');
            $menuGroupRecordModel->set('group_sequence','0');
            $menuGroupRecordModel->set('group_module',implode(' |##| ', $group_module));
            $MenuGroups[]=$menuGroupRecordModel;
        }

        return $MenuGroups;
    }

    public function isSummaryViewSupported() {
        return false;
    }

    public function saveMenuGroups($request,$relId) {
		$isDuplicate = ($request['isDuplicate'] == "true") ? true : false;
        for($index = 1; $index <= $request['numMapping']; $index++){

            $group_module_selected_orders = array();
            $group_module_selected_order = $request['group_module_' . $index . '_selected_order'];
            if ($group_module_selected_order) {
                $group_module_selected_orders = json_decode($group_module_selected_order, true);
            }

            $deleted = $request['menugroup_deleted_'.$index];
            $MenuGroupsid = $request['menugroupid_'.$index];
            if ($MenuGroupsid ==0){
                $MenuGroupsid = '';
            }
            if($deleted == 'deleted'){
                $recordModel=Vtiger_Record_Model::getInstanceById($MenuGroupsid);
                $recordModel->delete();
            }else{
                if($MenuGroupsid == '' || $isDuplicate){
                    $recordModel=Vtiger_Record_Model::getCleanInstance("MenuGroups");
                    $recordModel->set('mode','');
                }else{
                    $recordModel=Vtiger_Record_Model::getInstanceById($MenuGroupsid);
                    $recordModel->set('id',$MenuGroupsid);
                    $recordModel->set('mode','edit');
                }

                $recordModel->set('group_name',$request['group_name_'.$index]);
                $recordModel->set('group_sequence',$request['group_sequence_'.$index]);
                $recordModel->set('group_module', implode(' |##| ', $group_module_selected_orders));
                $recordModel->set('menucreator_id',$relId);
                $recordModel->save();
            }
        }
    }

    /**
     * @param array $moduleNonSeqs
     * @param bool $noParents
     *
     * @return array
     */
    public function returnMenuModels($moduleNonSeqs, $noParents = false) {
        $userPrivModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        //@TODO: Should this list be here? (moved here where it's used and broke out lines for better seeing)
        $restrictedModulesList = [
            //existing restricted modules not in a new group.
            'Emails',
            'ModComments',
            'ExtensionStore',
            'ExtensionStorePro',
            'Integration',
            'Dashboard',
            'Home',
            'vtmessages',
            'vttwitter',
            'Events',

            //Available as a guest block within a module
            'ExtraStops',
            'MoveRoles',
            'ParticipatingAgents',
            'MenuGroups',
            'MenuCleaner',
            'Holiday',
            'DailyNotes',
            'RevenueGroupingItem',
            'CommissionPlansItem',
            'AgentCompensationItems',
            'Escrows',
            'OrdersTaskAddresses',

            //accessible as related module.
            'Containers',
            'OrdersMilestone',
            'ProjectMilestone',
            'ProjectTask',
            'Claims',
            'ClaimItems',
            'ServiceDefaults',
            'TariffSections',
            'EffectiveDates',
            'TariffReportSections',
            'TariffServices',
            'Cubesheets',
            'Media',
            'CommissionPlansFilter',
            'AgentCompensationGroup',

            //Admin Only:
            'Google',
            'SMSNotifier',
            'SMSResponder',
            'Webforms',
            'Import',
            'MailManager',
            'ModTracker',
            'Exchange',
            'LeadCompanyLookup',

            //unused, left on because of the fear.
            'Potentials',
            'Quotes',
            'Services',
            'OPList',
            'PDFMaker',
            'BranchDefaults',

            //Moved to Settings
            'Users',
            'AgentManager',
            'VanlineManager',
            'TariffManager',
            'MenuCreator',
            'PicklistCustomizer',
            'Webforms',
            'Workflows',
            'CapacityCalendarCounter',
            'ItemCodes',
            'OPList',
            'ZoneAdmin',
            'ListviewColors',
            'VTEFavorite',
            'DataExportTracking',
            'RelatedRecordCount',
            'RecordProtection',

            //Old Windfall
            'LocationTypes',
            'Locations',
            'LocationHistory',
            'SlotConfiguration',
            'LocationOrders',
            'Configuration',
            'Articles',
            'CostCenters',
            'Status',
            'Conditions',
            'Inventory2',
            'InventoryLocations',
            'InventoryHistory',
            'WorkOrders',
            'LineItems',
            'LocationTags',
            'Transactions',
            'SyncCenters',
            'ItemCodesMapping',
        ];
        $adminOnlyList = [
            'TariffManager',
            'Webforms'
        ];
        $dependentTabsList = [
            'LocalDispatch' => 'OrdersTask',
            'CapacityCalendar' => 'OrdersTask',
            'MovePolicies' => 'Accounts'
        ];
        $menuModels = [];
        $inactive = 0;
        $noIndex = 0;
        $restricted = 0;
        $modulePerms = 0;
        $adminOnly = 0;
        $someOther = 0;
        foreach ($moduleNonSeqs as $module) {
            if (!$module || !$module->isActive()) {
                continue;
            }

            if(array_key_exists($module->getName(), $dependentTabsList)) {
                $moduleToCheck = Vtiger_Module_Model::getInstance($dependentTabsList[$module->getName()]);
            } else {
                $moduleToCheck = $module;
            }

            if (!$userPrivModel->hasModuleActionPermission($moduleToCheck->getId(), 'index')) {
                //@TODO: consider skipping modules that don't have index / list view
                continue;
            }

            //@NOTE broke this to lines to see
            if (in_array($module->getName(), $adminOnlyList)) {
                //Only show this module if it's an admin user. otherwise we don't, but not in an elseif
                if ($userPrivModel->isAdminUser()) {
                    $menuModels[] = $module->getName();
                }
            } else if (
                $userPrivModel->hasModulePermission($module->getId()) &&
                !in_array($module->getName(), $restrictedModulesList)
            ) {
                //@TODO: t_(ツ)_t
                $menuModels[] = $module->getName();
                //@NOTE: This is the old way that explicitly skips non-parents unless it's set to not.
//                if (!$noParents) {
//                    $menuModels[] = $module->getName();
//                } else if ($module->get('parent')) {
//                    $menuModels[] = $module->getName();
//                }
            }
        }

        return $menuModels;
    }
}
