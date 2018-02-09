<?php
/* ********************************************************************************
 * The content of this file is subject to the Related Record Count ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */ 
 
class Settings_RelatedRecordCount_EditViewAjax_View extends Settings_Vtiger_Index_View {

    function __construct() {
        parent::__construct();
    }


    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);
        $record = $request->get('record', 0);
        $viewer = $this->getViewer($request);

        $settingModel = new Settings_RelatedRecordCount_EditViewAjax_Model();
        $entity = $settingModel->getData($record);
        $active_module = $entity['modulename'];
        $active_related_module = $entity['related_modulename'];

        $listModules = $settingModel->getEntityModules();
        if(empty($active_module)){
            $active_module = $listModules[0]['name'];
        }

        $listRelatedModules = $settingModel->getRelatedModules($active_module);
        if(empty($active_related_module)){
            $active_related_module = $listRelatedModules[0]['modulename'];
        }

        $moduleModel = Vtiger_Module_Model::getInstance($active_related_module);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);

        $recordStructure = $recordStructureInstance->getStructure();
        $viewer->assign('RECORD_STRUCTURE', $recordStructure);

        if(!empty($record)) {
            $advance_criteria = json_decode(html_entity_decode($entity['conditions'], ENT_QUOTES), true);
        }else {
            $advance_criteria = null;
        }
        $advanceFilterOpsByFieldType = Vtiger_Field_Model::getAdvancedFilterOpsByFieldType();
        $viewer->assign('ADVANCED_FILTER_OPTIONS', Vtiger_Field_Model::getAdvancedFilterOptions());
        $viewer->assign('ADVANCED_FILTER_OPTIONS_BY_TYPE', $advanceFilterOpsByFieldType);

        $dateFilters = Vtiger_Field_Model::getDateFilterTypes();
        foreach($dateFilters as $comparatorKey => $comparatorInfo) {
            $comparatorInfo['startdate'] = DateTimeField::convertToUserFormat($comparatorInfo['startdate']);
            $comparatorInfo['enddate'] = DateTimeField::convertToUserFormat($comparatorInfo['enddate']);
            $comparatorInfo['label'] = vtranslate($comparatorInfo['label'], $active_related_module);
            $dateFilters[$comparatorKey] = $comparatorInfo;
        }
        $viewer->assign('DATE_FILTERS', $dateFilters);
        $viewer->assign('ADVANCE_CRITERIA', $advance_criteria);

        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('ENTITY', $entity);
        $viewer->assign('LIST_MODULES', $listModules);
        $viewer->assign('LIST_RELATED_MODULES', $listRelatedModules);
        $viewer->assign('RECORD_ID', $record);
        $viewer->assign('ACTIVE_MODULE', $active_module);
		$viewer->assign('SOURCE_MODULE', $active_related_module);

        $viewer->view('EditViewAjax.tpl', $qualifiedModuleName);
    }
}