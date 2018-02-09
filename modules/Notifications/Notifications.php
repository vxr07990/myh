<?php

/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

include_once 'modules/Vtiger/CRMEntity.php';

/**
 * Class Notifications
 */
class Notifications extends Vtiger_CRMEntity
{
    /**
     * const
     */
    const MODULE_NAME = 'Notifications';
    var $table_name = 'vtiger_notifications';
    var $table_index = 'notificationid';

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array('vtiger_notificationscf', 'notificationid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_crmentity', 'vtiger_notifications', 'vtiger_notificationscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_notifications' => 'notificationid',
        'vtiger_notificationscf' => 'notificationid');

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Notification Number' => Array('notifications', 'notificationno'),
        'Related To' => Array('crmentity', 'related_to')
    );
    var $list_fields_name = Array(
        /* Format: Field Label => fieldname */
        'Notification Number' => 'notificationno',
        'Related To' => 'related_to',
    );

    // Make the field link to detail view
    var $list_link_field = 'notificationno';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Notification Number' => Array('notifications', 'notificationno'),
        'Related To' => Array('vtiger_crmentity', 'related_to'),
    );
    var $search_fields_name = Array(
        /* Format: Field Label => fieldname */
        'Notification Number' => 'notificationno',
        'Related to' => 'related_to',
    );

    // For Popup window record selection
    var $popup_fields = Array('notificationno');

    // For Alphabetical search
    var $def_basicsearch_col = 'notificationno';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'notificationno';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('notificationno', 'related_to');

    var $default_order_by = 'notificationno';
    var $default_sort_order = 'ASC';
    var $supportModules = array('Leads', 'Contacts', 'Accounts', 'HelpDesk', 'Potentials', 'Quotes', 'Invoice', 'SalesOrder',
        'Calendar', 'Project', 'ProjectTask', 'Events', 'Campaigns', 'ProjectMilestone', 'PurchaseOrder', 'Products', 'Vendors');

    /**
     * Invoked when special actions are performed on the module.
     * @param String $modulename - Module name
     * @param String $event_type - Event Type
     */
    function vtlib_handler($modulename, $event_type)
    {
        if ($event_type == 'module.postinstall') {
            self::addWidgetTo($modulename);
            $this->createRelatedModules($modulename, $this->supportModules);
            $this->createRelatedTo($modulename, $this->supportModules);
            $this->createHandle($modulename);

            self::checkEnable();
            self::resetValid();
        } else if ($event_type == 'module.disabled') {
            self::removeWidgetTo($modulename);
            $this->removeRelatedModules($modulename, $this->supportModules);
            $this->removeRelatedTo($modulename, $this->supportModules);
            $this->removeHandle($modulename);
        } else if ($event_type == 'module.enabled') {
            self::addWidgetTo($modulename);
            $this->createRelatedModules($modulename, $this->supportModules);
            $this->createRelatedTo($modulename, $this->supportModules);
            $this->createHandle($modulename);
        } else if ($event_type == 'module.preuninstall') {
            self::removeWidgetTo($modulename);
            self::removeWsEntityModule($modulename);
//            $this->removeRelatedModules($modulename, $this->supportModules);
//            $this->removeRelatedTo($modulename, $this->supportModules);
            $this->removeHandle($modulename);

            self::removeValid();
            self::checkEnable();

        } else if ($event_type == 'module.preupdate') {
            self::removeWidgetTo($modulename);
            $this->removeRelatedModules($modulename, $this->supportModules);
            $this->removeRelatedTo($modulename, $this->supportModules);
            $this->removeHandle($modulename);
        } else if ($event_type == 'module.postupdate') {
            self::addWidgetTo($modulename);
            $this->createRelatedModules($modulename, $this->supportModules);
            $this->createRelatedTo($modulename, $this->supportModules);
            $this->createHandle($modulename);

            self::checkEnable();
            self::resetValid();
        }
    }

    static function resetValid()
    {
        global $adb;
        $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array('Notifications'));
        $adb->pquery("INSERT INTO `vte_modules` (`module`, `valid`) VALUES (?, ?);", array('Notifications', '0'));
    }

    static function removeValid()
    {
        global $adb;
        $adb->pquery("DELETE FROM `vte_modules` WHERE module=?;", array('Notifications'));
    }

    static function checkEnable()
    {
        global $adb;
        $rs = $adb->pquery("SELECT `enable` FROM `notifications_settings`;", array());
        if ($adb->num_rows($rs) == 0) {
            $adb->pquery("INSERT INTO `notifications_settings` (`enable`) VALUES ('0');", array());
        }
    }

    /**
     * Fn - addWidgetTo
     * Add header script to other module.
     * @param $moduleName
     */
    static function addWidgetTo($moduleName)
    {
        // Disable because we add to getHeaderScripts and getHeaderCsss in modules/Vtiger/views/Basic.php
//        $css_widgetType = 'HEADERCSS';
//        $css_widgetName = 'Notifications';
//        $css_link = "layouts/vlayout/modules/{$moduleName}/resources/{$moduleName}CSS.css";
//
//        $js_widgetType = 'HEADERSCRIPT';
//        $js_widgetName = 'Notifications';
//        $js_link = "layouts/vlayout/modules/{$moduleName}/resources/{$moduleName}JS.js";
//
//        $module = Vtiger_Module::getInstance($moduleName);
//        if ($module) {
//            // css
//            $module->addLink($css_widgetType, $css_widgetName, $css_link);
//            // js
//            $module->addLink($js_widgetType, $js_widgetName, $js_link);
//        }

        self::addWidgetTo($moduleName);
    }

    /**
     * Fn - removeWidgetTo
     * @param $moduleName
     */
    static function removeWidgetTo($moduleName)
    {
        $css_widgetType = 'HEADERCSS';
        $css_widgetName = 'Notifications';
        $css_link = "layouts/vlayout/modules/{$moduleName}/resources/{$moduleName}CSS.css";

        $js_widgetType = 'HEADERSCRIPT';
        $js_widgetName = 'Notifications';
        $js_link = "layouts/vlayout/modules/{$moduleName}/resources/{$moduleName}JS.js";

        $module = Vtiger_Module::getInstance($moduleName);
        if ($module) {
            // css
            $module->deleteLink($css_widgetType, $css_widgetName, $css_link);
            // js
            $module->deleteLink($js_widgetType, $js_widgetName, $js_link);
        }
    }

    /**
     * Add this module to vtiger_ws_entity table when install
     *
     * @param string $moduleName - this module name
     */
    static function addWsEntityModule($moduleName)
    {
        global $adb;

        // Check module
        $rs = $adb->pquery("SELECT * FROM `vtiger_ws_entity` WHERE `name` = ?", array($moduleName));
        if ($adb->num_rows($rs) == 0) {
            $adb->pquery("INSERT INTO `vtiger_ws_entity` (`name`, `handler_path`, `handler_class`, `ismodule`)
            VALUES (?, 'include/Webservices/VtigerModuleOperation.php', 'VtigerModuleOperation', '1');", array($moduleName));
        }
    }

    /**
     * Remove this module from vtiger_ws_entity table when uninstall
     *
     * @param string $moduleName - this module name
     */
    static function removeWsEntityModule($moduleName) {
        global $adb;

        // Check module
        $adb->pquery("DELETE FROM `vtiger_ws_entity` WHERE `name` = ?", array($moduleName));
    }

    /**
     * Create related modules with it
     * @param $modulename
     * @param $moduleNames
     * @return bool
     */
    function createRelatedModules($modulename, $moduleNames) {
        $moduleInstance = Vtiger_Module::getInstance($modulename);
        $fieldModel = Vtiger_Field::getInstance('related_to', $moduleInstance);
        $result = $fieldModel->setRelatedModules($moduleNames);

        return $result;
    }

    /**
     * @param string $modulename
     * @param array $moduleNames
     * @return bool
     */
    function removeRelatedModules($modulename, $moduleNames)
    {
        $moduleInstance = Vtiger_Module::getInstance($modulename);
        $fieldModel = Vtiger_Field::getInstance('related_to', $moduleInstance);
        $result = $fieldModel->unsetRelatedModules($moduleNames);

        return $result;
    }

    /**
     * @link modules/Project/Project.php:330
     *
     * @param string $modulename
     * @param array $moduleNames
     */
    function createRelatedTo($modulename, $moduleNames = null)
    {
        include_once('vtlib/Vtiger/Module.php');

        if (!$moduleNames || empty($moduleNames)) {
            $moduleNames = Settings_LayoutEditor_Module_Model::getSupportedModules();
        }

        $moduleInstance = Vtiger_Module::getInstance($modulename);

        // Add this module to the related list of support modules
        foreach ($moduleNames as $moduleName) {
            $relatedModuleInstance = Vtiger_Module::getInstance($moduleName);
            $relatedModuleInstance->setRelatedList($moduleInstance, $modulename, array('ADD'), 'get_dependents_list');
        }
    }

    /**
     * @param string $modulename
     * @param array $moduleNames
     */
    function removeRelatedTo($modulename, $moduleNames = null)
    {
        include_once('vtlib/Vtiger/Module.php');

        if (!$moduleNames || empty($moduleNames)) {
            $moduleNames = Settings_LayoutEditor_Module_Model::getSupportedModules();
        }

        $moduleInstance = Vtiger_Module::getInstance($modulename);

        // Add this module to the related list of support modules
        foreach ($moduleNames as $moduleName) {
            $relatedModuleInstance = Vtiger_Module::getInstance($moduleName);
            $relatedModuleInstance->unsetRelatedList($moduleInstance, $modulename, 'get_dependents_list');
        }
    }

    /**
     * @param string $moduleName
     */
    private function createHandle($moduleName)
    {
        global $adb;
        $em = new VTEventsManager($adb);
        $em->registerHandler("vtiger.entity.aftersave", "modules/{$moduleName}/{$moduleName}Handler.php", "{$moduleName}Handler");
    }

    /**
     * @param string $moduleName
     */
    private function removeHandle($moduleName)
    {
        global $adb;
        $em = new VTEventsManager($adb);
        $em->unregisterHandler("{$moduleName}Handler");
    }

}