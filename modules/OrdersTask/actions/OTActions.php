<?php

class OrdersTask_OTActions_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
		$result = 'ERROR';
        switch ($mode) {
            case 'saveCustomFilter':
                $result = $this->saveCustomFilter($request);
                break;
            case 'updateDefault':
                $result = $this->updateDefault($request);
                break;
        }
        $msg = new Vtiger_Response();
        $msg->setResult($result);
        $msg->emit();
    }

	function updateDefault(Vtiger_Request $request) {
        $db = PearDatabase::getInstance();

		$filterID = $request->get('filterID');
		$tableType = $request->get('tableType');
		$currentUser = Users_Record_Model::getCurrentUserModel();

		$db->pquery("UPDATE vtiger_localdispatch_selectedcolumns SET default_filter = '0' WHERE user_id = ? AND table_type = ?", array($currentUser->getId(),$tableType));
		$db->pquery("UPDATE vtiger_localdispatch_selectedcolumns SET default_filter = '1' WHERE user_id = ? AND table_type = ? AND id = ?", array($currentUser->getId(),$tableType,$filterID));
		
		return "Ok";
	}
	
	function saveCustomFilter(Vtiger_Request $request) {
        $db = PearDatabase::getInstance();
        $columsSelected = $request->get('columns_selected');
		$filterName = $request->get('filterName');
		$defaultFilter = $request->get('defaultFilter');
		$tableType = $request->get('tableType');
		$toEdit = $request->get('toEdit');
		$currentUser = Users_Record_Model::getCurrentUserModel();
		
		foreach($columsSelected as $column){
			$auxArr = explode(":", $column);
			$selected[] = $auxArr[1];
		}
		if($defaultFilter == "1"){
			$db->pquery("UPDATE vtiger_localdispatch_selectedcolumns SET default_filter = '0' WHERE user_id = ? AND table_type = ?", array($currentUser->getId(),$tableType));
		}
		
		if($toEdit != "0"){
			$db->pquery("UPDATE vtiger_localdispatch_selectedcolumns SET columns = ?, date_time = ?, default_filter = ? WHERE id = ?", array(implode(",", $selected),date("Y-m-d H:i:s"),$defaultFilter,$toEdit));
			$id = $toEdit;
		}else{
			$db->pquery("INSERT INTO vtiger_localdispatch_selectedcolumns (user_id, date_time, columns, filter_name, table_type, default_filter) VALUES (?,?,?,?,?,?)", array($currentUser->getId(), date("Y-m-d H:i:s"), implode(",", $selected),$filterName,$tableType,$defaultFilter));
			$id = $db->pquery("SELECT max(id) FROM vtiger_localdispatch_selectedcolumns",array())->FetchRow() [0];
			
		}
		return $id;
	}
}