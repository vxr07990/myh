<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

class Trips extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_trips';
    public $table_index= 'tripsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_driverscf', 'tripsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_trips', 'vtiger_tripscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_trips' => 'tripsid',
        'vtiger_tripscf'=>'tripsid');

    public function saveentity($module, $fileid = '')
    {
        $newSave = true;
        parent::saveentity($module, $fileid);
        if ($_REQUEST['record'] && $_REQUEST['calledby'] === "ldd") {
            $request = new Vtiger_Request($_REQUEST, $_REQUEST);
                
            $related_record_list = explode(",", $request->get("related_record_list"));
                
            $request->set("src_record", $_REQUEST['record']);
            $request->set("related_record_list", $related_record_list);
                
            $tripActions = new Trips_RelationAjax_Action();
            $tripActions->addRelation($request);
        }
    }
        
    /** Returns a list of the trips that have the same driver associated
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    public function get_trips($id)
    {
        global $adb, $singlepane_view,$currentModule,$current_user;

        $this_module = $related_module = $currentModule;

        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();

        if ($singlepane_view == 'true') {
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        } else {
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;
        }

        $result = $adb->pquery("SELECT driver_id FROM vtiger_trips WHERE tripsid = ?", array($id));
        $driverId = $adb->query_result($result, 0, "driver_id");
            
        $button = "";
        $query = "SELECT vtiger_trips.* FROM vtiger_trips INNER JOIN vtiger_crmentity  ON vtiger_trips.tripsid = vtiger_crmentity.crmid WHERE vtiger_crmentity.deleted = 0 AND vtiger_trips.driver_id = ".$driverId;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        return $return_value;
    }

    /**
    * Invoked when special actions are performed on the module.
    * @param String Module name
    * @param String Event Type
    */
    public function vtlib_handler($moduleName, $eventType)
    {
        global $adb;
        if ($eventType == 'module.postinstall') {
            // TODO Handle actions after this module is installed.
        } elseif ($eventType == 'module.disabled') {
            // TODO Handle actions before this module is being uninstalled.
        } elseif ($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } elseif ($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($eventType == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }
    }
}
