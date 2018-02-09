<?php

//@TODO: Why is this extended from here instead of Vtiger_List_View?
class MenuCleaner_List_View extends Vtiger_Index_View
{
    public function preProcessTplName(Vtiger_Request $request)
    {
        return 'ListViewPreProcess.tpl';
    }

    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();

        global $adb, $current_user;
        
		$MenuCreatorId = Vtiger_MenuStructure_Model::getModuleCreatorID()[0];

        //Now menuCreatorID is either the menucreator results of the first found DEFAULT_MENU of the group of permissible agents
        // OR if there wasn't one, then the first found DEFAULT_MENU of a parent agent.
        // OR none.

        //@TODO: Should this be done if menuCreatorID is empty?
        $results = [];
        $sql = "SELECT a.group_name, a.group_sequence, a.group_module, agentid FROM `vtiger_menugroups` a";
        $sql .= " INNER JOIN `vtiger_crmentity` c ON c.crmid = a.menugroupsid ";
        $sql .= " WHERE group_name =? AND deleted = 0 AND a.`menucreator_id` = ? AND (`description`!=? OR ISNULL(`description`)) ORDER BY group_sequence";

        $rs = $adb->pquery($sql, array('Menu Shortcuts', $MenuCreatorId, 'DEFAULT_MENU'));
        if ($adb->num_rows($rs) > 0) {
            while ($row = $adb->fetchByAssoc($rs)) {
                $results[] = explode(' |##| ', $row['group_module']);
            }
        }
        if (count($results) == 0) {
            /*
MariaDB [prod_gvlHQ]> select * from vtiger_crmentity where `description`='DEFAULT_MENU' ;
Empty set (23.25 sec)
            vs:
MariaDB [prod_gvlHQ]> select * from vtiger_crmentity where `setype`='MenuCreator' and `description`='DEFAULT_MENU' ;
Empty set (0.00 sec)
            description is not indexed once that table gets huge it can't search effectively.
            */
            //@TODO: This can only be correct by luck.  We'll have to select a particular "agents" as the defualt at some later point.
            $CreatorId = $adb->pquery("SELECT * FROM `vtiger_crmentity` WHERE `setype`='MenuCreator' AND `deleted` = 0 AND `description` =? LIMIT 1 ", ["DEFAULT_MENU"]);
            if ($adb->num_rows($CreatorId)) {
                $rsCreatorId = $adb->query_result($CreatorId, 0, 'crmid');
            }

            $rsMenuGroup          = [];
            //Grab all the menu groups related to the "default menu" record
            $allMenuGroupsRecords = MenuGroups_Module_Model::getMenuGroups($rsCreatorId);
            foreach ($allMenuGroupsRecords as $menuGroupRecord) {
                if ($menuGroupRecord->get('group_name') == 'Menu Shortcuts') {
                    //Skip Menu Shortcuts, it's the one we want, it's not there or wrong.
                    continue;
                }
                $rsMenuGroup = array_merge($rsMenuGroup, explode(' |##| ', $menuGroupRecord->get('group_module')));
            }

            $allModelsList     = Vtiger_Module_Model::getSearchableModules();
            $arrayMenuShortcut = [];
            foreach ($allModelsList as $moduleModel) {
                if(!in_array($moduleModel->getName(),$rsMenuGroup)){
                    //only display modules not defined in a group.
                    $arrayMenuShortcut[] = $moduleModel->getName();
                }
            }
            $results[0] = $arrayMenuShortcut;
        }
        //@TODO: While for rsMenuGroup seems irrelevant given we're only using the first one. <-- added limit 1 above
        $moduleFromMenuCreator = $results[0];
        //@TODO: figure out if the [0] is really what it's supposed to be.  I think they meant something different... maybe array_merge() instead of array_push
        if (!empty($moduleFromMenuCreator)) {
            foreach ($moduleFromMenuCreator as $moduleSort) {
                $moduleNonSeqs[] = Vtiger_Module_Model::getInstance($moduleSort);
            }
            $menuModels = MenuGroups_Module_Model::returnMenuModels($moduleNonSeqs);
        } else {
            //@NOTE: getSearchable checks for isActive, and detail view permissions. but we need to check for list view privileges
            //$allModelsList     = Vtiger_Module_Model::getSearchableModules();
            $allModelsList = Vtiger_Module_Model::getAll(array('0', '2'));
            foreach ($allModelsList as $module) {
                if ($module->get('tabsequence') == -1) {
                    $moduleNonSeqs[] = $module;
                }
            }
            $menuModels = MenuGroups_Module_Model::returnMenuModels($moduleNonSeqs, true);
        }
        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('MODULES_SHORTCUTS', $menuModels);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->view('ListViewContents.tpl', $moduleName);
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = [
            "modules.$moduleName.resources.List",
        ];
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }
}
