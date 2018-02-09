<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_MenuCleaner_Save_Action extends Settings_Vtiger_Index_Action
{
    public function process(Vtiger_Request $request)
    {
        global $adb;
        $moduleName = $request->getModule(false);
        $menuCleanerModuleModel = Settings_Vtiger_Module_Model::getInstance($moduleName);
        $selectedModulesList = $request->get('selectedModulesList');
        $defaultParentTabs = array(
            'SALES_MARKETING_TAB' => array('Campaigns', 'Leads', 'Opportunities', 'Surveys', 'Estimates'),
            'OPERATIONS_TAB' => array('Orders', 'LocalDispatch', 'LongDistanceDispatch', 'Trips', 'Accounts', 'Contracts', 'MovePolicies'),
            'COMMON_SERVICES_TAB' => array('Contacts', 'Documents', 'Reports', 'Calendar', 'HelpDesk'),
            'FINANCE_TAB' => array('Actuals', 'Storage', 'Claims'),
            'SYSTEM_ADMIN_TAB' => array('AgentManager', 'VanlineManager', 'MailManager', 'TariffManager'),
            'TOOLS_TAB' => array('EmailTemplates', 'SMSNotifier','AdvancedReports','PDFMaker')
        );
        // Get removed select modules
        $rsRemove=$adb->pquery("SELECT * FROM vtiger_tab WHERE tabsequence = '-1' AND parent ='' AND `name` NOT IN (".generateQuestionMarks($selectedModulesList).")", array($selectedModulesList));

        if ($adb->num_rows($rsRemove)>0) {
            while ($rowRemove=$adb->fetch_array($rsRemove)) {
                $removeModule=$rowRemove['name'];
                $parentTab='';
                foreach ($defaultParentTabs as $parent => $tabs) {
                    $index=array_search('Campaigns', $tabs);
                    if ($index != -1) {
                        $parentTab = $parent;
                        break;
                    }
                }
                $adb->pquery("UPDATE `vtiger_tab` SET parent =? ,tabsequence = '-1' WHERE `name` =?", array($parentTab, $removeModule));
                $adb->pquery("DELETE FROM `vtiger_settings_field` WHERE `name`=?", array($removeModule));
            }
        }


        if ($selectedModulesList) {
            foreach ($selectedModulesList as $key => $val) {
                //update module in table vtiger_tab set parent ='' abd tabsequence = -1
                $adb->pquery("UPDATE vtiger_tab SET parent = '',tabsequence = '-1' WHERE `name` ='$val'");
            }
        }
        
        header("Location: index.php?module=MenuCleaner&view=List");
    }

    public function validateRequest(Vtiger_Request $request)
    {
        $request->validateWriteAccess();
    }
}