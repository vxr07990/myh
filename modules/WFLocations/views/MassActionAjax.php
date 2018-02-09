<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once('libraries/nusoap/nusoap.php');

class WFLocations_MassActionAjax_View extends Vtiger_MassActionAjax_View
{
    public function __construct()
    {
        parent::__construct();
        $this->exposeMethod('printBarCodes');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);

            return;
        }
    }

    public function printBarCodes(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $codeGenerator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $moduleName = $request->getModule();
        $numRows = $request->get('codeRows');

        $cvId                  = $request->get('viewname');
        $selectedIds           = $request->get('selected_ids');
        $excludedIds           = $request->get('excluded_ids');

        // We should only print locations of the same type. If multiple locaitons with different
        // types are selected, only include the ones of the first type selected
        try {
            $sql = 'SELECT `wflocationtypes_type`
                FROM `vtiger_wflocations`
                LEFT JOIN `vtiger_wflocationtypes`
                    ON `vtiger_wflocations`.`wflocation_type` = `vtiger_wflocationtypes`.`wflocationtypesid`
                WHERE `vtiger_wflocations`.`wflocationsid` = ?';
            $result = $db->pquery($sql, [$selectedIds[0]]);
            if($db->num_rows($result)){
                $limitedToType = $db->fetch_row($result)['wflocationtypes_type'];
            }else{
                throw new Exception("Error Processing Request", 1);

            }
        } catch (Exception $e) {
            //uuh..guess I'll die
            return;
        }

        // Select tags for locations which match the first location type and actually have a tag.
        // While all locations should have tags, we'll just do a double check.
        $sql = "SELECT `vtiger_wflocations`.`tag`
                FROM `vtiger_wflocations`
                LEFT JOIN `vtiger_wflocationtypes`
                    ON `vtiger_wflocations`.`wflocation_type` = `vtiger_wflocationtypes`.`wflocationtypesid`
                WHERE `vtiger_wflocations`.`wflocationsid`
                    IN (".generateQuestionMarks($selectedIds).")
                AND `wflocationtypes_type` = '$limitedToType'
                AND NULLIF(`tag`, '') IS NOT NULL";
        $result = $db->pquery($sql, $selectedIds);
        while($row = $db->fetch_row($result)){
            $tags[$row['tag']] =base64_encode($codeGenerator->getBarcode($row['tag'], $codeGenerator::TYPE_CODE_128));
        }

        $viewer = $this->getViewer($request);
        $viewer->assign('TAGS', $tags);
        $viewer->assign('NUM_ROWS', $numRows);

        echo $viewer->view('PrintCodes.tpl', $moduleName, true);
    }

    /**
     * Function returns the mass edit form
     *
     * @param Vtiger_Request $request
     */
    public function showMassEditForm(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $locationIds = $request->get('selected_ids');

        $sql = 'SELECT DISTINCT `vtiger_wflocationtypes`.`wflocationtypes_type`
                    FROM `vtiger_wflocations`
                    LEFT JOIN `vtiger_wflocationtypes`
                        ON `vtiger_wflocations`. `wflocation_type` = `vtiger_wflocationtypes`.`wflocationtypesid`
                    WHERE `wflocationsid` IN (' . generateQuestionMarks($locationIds) . ')';
        $result = $db->pquery($sql, $locationIds);
        //Mass edit should only be allowed for locations of the same type. OT5458
        if($db->num_rows($result) > 1){
            $moduleName = $request->getModule();

            $viewer = $this->getViewer($request);
            $viewer->assign('MODULE', $moduleName);

            return $viewer->view('MassEditFormFail.tpl', $moduleName);
        } else{
            $locationType = strtolower($result->fetchRow()['wflocationtypes_type']);

            $request->set('wflocationtypes_type', $locationType);
        }

        return $this->getLocationsMassEditForm($request);
    }

    /**
     * Function returns the mass edit form for WF locations
     *
     * @param Vtiger_Request $request
     */
    public function getLocationsMassEditForm(Vtiger_Request $request)
    {
        $moduleName              = $request->getModule();
        $cvId                    = $request->get('viewname');
        $selectedIds             = $request->get('selected_ids');
        $excludedIds             = $request->get('excluded_ids');
        $viewer                  = $this->getViewer($request);
        $moduleModel             = Vtiger_Module_Model::getInstance($moduleName);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_MASSEDIT);
        $fieldInfo               = [];
        $fieldList               = $moduleModel->getFields();
        foreach ($fieldList as $fieldName => $fieldModel) {
            $fieldInfo[$fieldName] = $fieldModel->getFieldInfo();
        }
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE', Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('MODE', 'massedit');
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CVID', $cvId);
        $viewer->assign('SELECTED_IDS', $selectedIds);
        $viewer->assign('EXCLUDED_IDS', $excludedIds);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('MASS_EDIT_FIELD_DETAILS', $fieldInfo);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure($request->get('wflocationtypes_type')));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $searchKey   = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $operator    = $request->get('operator');
        if (!empty($operator)) {
            $viewer->assign('OPERATOR', $operator);
            $viewer->assign('ALPHABET_VALUE', $searchValue);
            $viewer->assign('SEARCH_KEY', $searchKey);
        }
        $searchParams = $request->get('search_params');
        if (!empty($searchParams)) {
            $viewer->assign('SEARCH_PARAMS', $searchParams);
        }
        echo $viewer->view('MassEditForm.tpl', $moduleName, true);
    }
}
