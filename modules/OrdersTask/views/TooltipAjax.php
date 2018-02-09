<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class OrdersTask_TooltipAjax_View extends Vtiger_TooltipAjax_View {

	function preProcess(Vtiger_Request $request) {
		return true;
	}

	function postProcess(Vtiger_Request $request) {
		return true;
	}

	function process (Vtiger_Request $request) {
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();

		if(isset($_REQUEST['customTooltip'])){
			$this->initializeListViewContents($request, $viewer);
		}else{
			parent::initializeListViewContents($request, $viewer);
		}
		echo $viewer->view('TooltipContents.tpl', $moduleName, true);
	}
	
	public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
		$db = PearDatabase::getInstance();
		$moduleName = $this->getModule($request);
		
		$recordId = $request->get('record');
		$type = $request->get('customTooltip');
		if($type == "personnel"){
			$customType = 'Personnel Type';
			$blockLabel = 'LBL_PERSONNEL';
			$fieldNameNum = 'num_of_personal';
			$fieldNameType = 'personnel_type';
		}else{
			$customType = 'Vehicle Type';
			$blockLabel = 'LBL_VEHICLES';
			$fieldNameNum = 'num_of_vehicle';
			$fieldNameType = 'vehicle_type';
		}
		$customTooltips = array();
		$result = $db->pquery("SELECT fieldname, fieldvalue FROM vtiger_orderstask_extra WHERE orderstaskid = ? AND blocklabel = ? ORDER BY sequence",array($recordId,$blockLabel));
		if ($db->num_rows($result) > 0) {
			while($row = $db->fetch_row($result)){
				if($row['fieldname'] != "est_hours_personnel" &&  $row['fieldvalue'] != "")
					$aux[$row['fieldname']] = $row['fieldvalue'];
				if(count($aux) == 2){
					if($aux[$fieldNameNum] != "" && $aux[$fieldNameType] != ""){
						if($fieldNameType == "personnel_type"){
							if ($aux[$fieldNameType] == -1) {
								$aux[$fieldNameType] ='Any Personnel Type';
							}else{
								$auxRes = $db->pquery("SELECT emprole_desc FROM vtiger_employeeroles WHERE employeerolesid = ?", array($aux[$fieldNameType]));
								$aux[$fieldNameType] = $db->query_result($auxRes, 0, 'emprole_desc');
							}
						}
						$customTooltips[] = array("estimatedNumber" => $aux[$fieldNameNum], "type" => $aux[$fieldNameType]);
					}
					$aux = array();
				}
			}
		}
		
		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('CUSTOM_TOOLTIPS',$customTooltips);
		$viewer->assign('customType', $customType);
	}
}