<?php
/* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
 
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once 'vtlib/Vtiger/Module.php';

class RelatedRecordCount extends CRMEntity {
	/**
	 * Invoked when special actions are performed on the module.
	 * @param String Module name
	 * @param String Event Type (module.postinstall, module.disabled, module.enabled, module.preuninstall)
	 */
	function vtlib_handler($modulename, $event_type) {
		if($event_type == 'module.postinstall') {
            $this->createSampleData();
		} else if($event_type == 'module.disabled') {
			// TODO Handle actions when this module is disabled.
		} else if($event_type == 'module.enabled') {
			// TODO Handle actions when this module is enabled.

		} else if($event_type == 'module.preuninstall') {
			// TODO Handle actions when this module is about to be deleted.

		} else if($event_type == 'module.preupdate') {
			// TODO Handle actions before this module is updated.
		} else if($event_type == 'module.postupdate') {

		}
	}

    function createSampleData(){
        global $adb;
        $entityModules = $this->getEntityModules();
        $priority = 1;
        foreach($entityModules as $entityModule){
            $relatedModules = $this->getRelatedModules($entityModule['name']);
            foreach($relatedModules as $relatedModule){
                $params = array($entityModule['name'], $relatedModule['modulename'], '$count$', $priority, 'Active');
                $adb->pquery("INSERT INTO vte_related_record_count(`modulename`, `related_modulename`, `label`, `priority`, `status`) VALUES(?, ?, ?, ?, ?)", $params);
                $priority++;
            }
        }

    }

    function getEntityModules(){
        global $adb;
        $result = $adb->pquery('SELECT *
                                    FROM vtiger_tab
                                    WHERE vtiger_tab.isentitytype = 1 AND vtiger_tab.presence = 0
                                        AND vtiger_tab.parent IS NOT NULL
	                                    AND vtiger_tab.parent != ""
                                    ORDER BY vtiger_tab.name', array());
        $arr = array();

        if($adb->num_rows($result)){
            while($row = $adb->fetchByAssoc($result)){
                $row['tablabel'] = vtranslate($row['name'], $row['name']);
                $arr[] = $row;
            }
        }

        return $arr;
    }

    function getRelatedModules($moduleName){
        global $adb;
        $result = $adb->pquery('SELECT
                                        vtiger_relatedlists.*, vtiger_tab.name AS modulename
                                    FROM
                                        vtiger_relatedlists
                                    INNER JOIN vtiger_tab ON vtiger_relatedlists.related_tabid = vtiger_tab.tabid
                                    WHERE
                                        vtiger_relatedlists.tabid = ?
                                    AND related_tabid != 0
                                    AND vtiger_relatedlists.presence <> 1
                                    AND vtiger_tab.presence <> 1
                                    AND vtiger_relatedlists. NAME NOT IN ("get_history")
                                    ORDER BY vtiger_tab.name',
            array(getTabid($moduleName)));
        $arr = array();

        if($adb->num_rows($result)){
            while($row = $adb->fetchByAssoc($result)){
                $row['tablabel'] = vtranslate($row['modulename'], $row['modulename']);
                $arr[] = $row;
            }
        }

        return $arr;
    }
}
?>
