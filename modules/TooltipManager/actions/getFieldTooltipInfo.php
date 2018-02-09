<?php
/* ********************************************************************************
 * The content of this file is subject to the Tooltip Manager ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
class TooltipManager_getFieldTooltipInfo_Action extends Vtiger_Action_Controller {
    function __construct() {
        parent::__construct();
    }

    public function checkPermission() {
        return true;
    }
    public function process(Vtiger_Request $request) {
        $pmodule = $request->get('pmodule');
        if (!$pmodule) {
            $response = new Vtiger_Response();
            $response->setError(1, 'Failed to provide: '. $pmodule);
            $response->emit();
            return;
        }
        $fields = array();

        $moduleInstance = Vtiger_Module::getInstance($pmodule);
        if (!$moduleInstance) {
            $response = new Vtiger_Response();
            $response->setError(2, 'Failed to find: '. $pmodule);
            $response->emit();
            return;
        }
        foreach ($moduleInstance->getFields() as $row) {
            if ($row->name != 'imagename'
                && !is_null($row->helpinfo)
                    && !in_array($row->uitype,array(61, 122))
                        && in_array($row->presence,array(0, 2))
                        && $row->previewtype != 0){
                $fields[] = array(
                    'icon' => $row->icon ? $row->icon : 'layouts/vlayout/modules/TooltipManager/resources/info_icon.png',
                    "preview_type"=> $row->previewtype,
                    'helpinfo' => $row->helpinfo,
                    'fieldid' => $row->id,
                    'fieldname' => $row->name,
                    'fieldlabel' => vtranslate($row->label,$pmodule)
                );
            }
        }

        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $default_record_view = ($currentUserModel->get('default_record_view') === 'Summary') ? 'summary' : 'detail';

        $result = array('success'=>true,'data'=>$fields,'default_record_view'=>$default_record_view);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

}
