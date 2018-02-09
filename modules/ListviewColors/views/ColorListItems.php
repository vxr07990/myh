<?php
/* * *******************************************************************************
 * The content of this file is subject to the VTE List View Colors ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class ListviewColors_ColorListItems_View extends Vtiger_IndexAjax_View {

	public function checkPermission(Vtiger_Request $request) {
		return true;
	}
    function __construct() {
        parent::__construct();
    }

	public function process(Vtiger_Request $request) {
        $items = array();

        $records = $request->get('records');
        if(empty($records)){
            return $items;
        }

        $pmoduleName = $request->get('pmodule');
        $moduleModel = new ListviewColors_Module_Model();
        $conditions = $moduleModel->getConditionalColors($pmoduleName);

        if(!empty($conditions)){
            foreach($conditions as $condition){
                $recordsMatched = $moduleModel->getRecordsByCondition($condition, $records);
                if(!empty($recordsMatched)){
                    foreach($recordsMatched as $recordMatched){
                        $return[] = array(
                            'record'=>$recordMatched,
                            'text_color'=>$condition['text_color'],
                            'bg_color'=>$condition['bg_color'],
                            'related_record_color'=>$condition['related_record_color']
                        );
                    }
                }
            }
        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($return);
        $response->emit();
	}

        
    public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
}
