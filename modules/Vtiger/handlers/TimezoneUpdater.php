<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
require_once 'include/events/VTEventHandler.inc';

class Vtiger_TimezoneUpdater_Handler extends VTEventHandler {

	function handleEvent($eventName, $data) {
		global $adb;

		if ($eventName == 'vtiger.entity.aftersave') {
			$module = $data->getModuleName();
			$time_fields = $_REQUEST['time_fields'];
			if(count($time_fields)>0) {
                foreach ($time_fields as $fieldid) {
                    $rsCheck=$adb->pquery("SELECT * FROM vtiger_fieldtimezonerel WHERE crmid=? AND fieldid=?",array($data->getId(), $fieldid));
                    if($adb->num_rows($rsCheck)>0) {
                        $adb->pquery("UPDATE `vtiger_fieldtimezonerel` SET `timezone` =? WHERE crmid=? AND fieldid=?", array($_REQUEST["timefield_" . $fieldid], $data->getId(), $fieldid));
                    }else {
                        $adb->pquery("insert into `vtiger_fieldtimezonerel` ( `crmid`, `fieldid`, `timezone`) values ( ?, ?, ?)", array($data->getId(), $fieldid, $_REQUEST["timefield_" . $fieldid]));
                    }
                }
			}
		}
	}
}
