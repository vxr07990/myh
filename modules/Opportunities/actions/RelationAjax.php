<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Opportunities_RelationAjax_Action extends Vtiger_RelationAjax_Action
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('addRelation');
    }
    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }
    public function addRelation($request)
    {
        $sourceModule = $request->getModule();
        $sourceRecordId = $request->get('src_record');
        $relatedModule = $request->get('related_module');
        $relatedRecordIdList = $request->get('related_record_list');

        $sourceModuleModel = Vtiger_Module_Model::getInstance($sourceModule);
        $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModule);
        $relationModel = Vtiger_Relation_Model::getInstance($sourceModuleModel, $relatedModuleModel);
        foreach ($relatedRecordIdList as $relatedRecordId) {
            $relationModel->addRelation($sourceRecordId, $relatedRecordId);

            // Sirva - Updating the related Opportunities
            if (getenv('INSTANCE_NAME') == 'sirva' && $relatedModule == 'Opportunities') {
                $oppRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecordId, 'Opportunities');
                $oppRecordModel->updateOppFields($sourceRecordId, $relatedRecordId);
            }
        }
        $msg = new Vtiger_Response();
        $msg->setResult('ok');
        $msg->emit();
    }
}
