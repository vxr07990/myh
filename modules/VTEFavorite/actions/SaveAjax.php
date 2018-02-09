<?php
/* ********************************************************************************
 * The content of this file is subject to the VTEFavorite ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class VTEFavorite_SaveAjax_Action extends Vtiger_Action_Controller{

    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    function process(Vtiger_Request $request) {
        global $adb;

        $parrentId=$request->get('parrent');
		$children=$request->get('children');
		$childrenCheckedArray = array();
		$childrenLength = strlen($children);
		if($childrenLength>1)
		{
			$children = substr($children, 1 , $childrenLength-1); 
			$childrenCheckedArray = explode(',',$children);
		}
		
		$parrentRecord = VTEFavorite_Record_Model::getRecordByID($parrentId);
		$childrenRecords = $parrentRecord->getChildren();
		$childrenRecordKeys = array_keys($childrenRecords);					
		//Delete old
		foreach($childrenRecordKeys as $key){
			if(!in_array($key,$childrenCheckedArray)){
				//Delete
				$parrentRecord->deleteChildren($key);
			}			
		}
		//Add new
		foreach($childrenCheckedArray as $key){
			if(!in_array($key,$childrenRecordKeys)){
				//Add new
				$parrentRecord->addChildren($key);
			}			
		}
		//exit();
        
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('success'));
        $response->emit();
    }
}
