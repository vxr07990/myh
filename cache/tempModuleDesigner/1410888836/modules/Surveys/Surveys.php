<?php
/***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

class Surveys extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_surveys';
    public $table_index= 'surveysid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_surveyscf', 'surveysid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_surveys', 'vtiger_surveyscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_surveys' => 'surveysid',
        'vtiger_surveyscf'=>'surveysid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(

);
    public $list_fields_name = array(

);

    // Make the field link to detail view
    public $list_link_field = 'surveyid';

    // For Popup listview and UI type support
    public $search_fields = array(

);
    public $search_fields_name = array(

);

    // For Popup window record selection
    public $popup_fields = array('surveyid');

    // For Alphabetical search
    public $def_basicsearch_col = 'surveyid';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'surveyid';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('createdtime', 'modifiedtime', 'surveyid');

    public $default_order_by = 'surveyid';
    public $default_sort_order='ASC';

    /**
    * Invoked when special actions are performed on the module.
    * @param String Module name
    * @param String Event Type
    */
    public function vtlib_handler($moduleName, $eventType)
    {
        if ($eventType == 'module.postinstall') {
            //Delete duplicates from all picklist
            static::deleteDuplicatesFromAllPickLists($moduleName);
        } elseif ($eventType == 'module.disabled') {
            // TODO Handle actions before this module is being uninstalled.
        } elseif ($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } elseif ($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($eventType == 'module.postupdate') {
            //Delete duplicates from all picklist
            static::deleteDuplicatesFromAllPickLists($moduleName);
        }
    }
    
    /**
     * Delete doubloons from all pick list from module
     */
    public static function deleteDuplicatesFromAllPickLists($moduleName)
    {
        global $adb,$log;

        $log->debug("Invoking deleteDuplicatesFromAllPickList(".$moduleName.") method ...START");

        //Deleting doubloons
        $query = "SELECT columnname FROM `vtiger_field` WHERE uitype in (15,16,33) "
                . "and tabid in (select tabid from vtiger_tab where name = '$moduleName')";
        $result = $adb->pquery($query, array());

        $a_picklists = array();
        while ($row = $adb->fetchByAssoc($result)) {
            $a_picklists[] = $row["columnname"];
        }
        
        foreach ($a_picklists as $picklist) {
            static::deleteDuplicatesFromPickList($picklist);
        }
        
        $log->debug("Invoking deleteDuplicatesFromAllPickList(".$moduleName.") method ...DONE");
    }
    
    public static function deleteDuplicatesFromPickList($pickListName)
    {
        global $adb,$log;
        
        $log->debug("Invoking deleteDuplicatesFromPickList(".$pickListName.") method ...START");
    
        //Deleting doubloons
        $query = "SELECT {$pickListName}id FROM vtiger_{$pickListName} GROUP BY {$pickListName}";
        $result = $adb->pquery($query, array());
    
        $a_uniqueIds = array();
        while ($row = $adb->fetchByAssoc($result)) {
            $a_uniqueIds[] = $row[$pickListName.'id'];
        }
    
        if (!empty($a_uniqueIds)) {
            $query = "DELETE FROM vtiger_{$pickListName} WHERE {$pickListName}id NOT IN (".implode(",", $a_uniqueIds).")";
            $adb->pquery($query, array());
        }
        
        $log->debug("Invoking deleteDuplicatesFromPickList(".$pickListName.") method ...DONE");
    }
}
