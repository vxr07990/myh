<?php

class VanlineManager_Save_Action extends Vtiger_Save_Action
{
    public function addNewRole($id, $rolename, $parentroles, $depth, $allowassignedrecordsto, $directparentid, $profileid)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*$db = PearDatabase::getInstance();
        $sql = 'INSERT INTO vtiger_role(roleid, rolename, parentrole, depth, allowassignedrecordsto) VALUES (?,?,?,?,?)';
        $db->pquery($sql, array($id, $rolename, $parentroles, $depth, $allowassignedrecordsto));
        $picklist2RoleSQL = "INSERT INTO vtiger_role2picklist SELECT '".$id."',picklistvalueid,picklistid,sortid
        FROM vtiger_role2picklist WHERE roleid = ?";
        $db->pquery($picklist2RoleSQL, array($directparentid));
        $sql = 'INSERT INTO vtiger_role2profile(roleid, profileid) VALUES (?,?)';
        $params = array($id, $profileid);
        $db->pquery($sql, $params);*/
    }
    
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');

        if (!Users_Privileges_Model::isPermitted($moduleName, 'Save', $record)) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
    }
    
    public function addRoleToGroup2role($memberId, $groupId)
    {
        file_put_contents('logs/devLog.log', "\n Old Security Called : ".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), FILE_APPEND);
        //old securities
        /*$db = PearDatabase::getInstance();
        $sql = 'INSERT INTO `vtiger_group2role`(roleid, groupid) VALUES (?,?)';
        $db->pquery($sql, array($memberId, $groupId));*/
    }

    public function process(Vtiger_Request $request)
    {
        $recordId = $request->get('record');
        
        $db = PearDatabase::getInstance();

        $currentUser = Users_Record_Model::getCurrentUserModel();
        if (!$currentUser->isAdminUser()){
           unset($_REQUEST['vanline_id']);  // Just in case an user remove the readonly with the browser dev console. 
        }

        $request = new Vtiger_Request($_REQUEST, $_REQUEST);

        $recordModel = $this->saveRecord($request);
        if ($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentRecordId = $request->get('sourceRecord');
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentRecordId, $parentModuleName);
            //TODO : Url should load the related list instead of detail view of record
            $detailViewLinkParams = array('MODULE'=>$parentModuleName,'RECORD'=>$parentRecordId);
            //file_put_contents('logs/devLog.log', "\n PARENT MOD: ".$parentModuleName, FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n PARENT RECORD: ".$parentRecordId, FILE_APPEND);
            $detailViewModel = Vtiger_DetailView_Model::getInstance($parentModuleName, $parentRecordId);
            $detailViewLinks = $detailViewModel->getDetailViewLinks($detailViewLinkParams);
            //file_put_contents('logs/devLog.log', "DETAIL VIEW LINKS: ".print_r($detailViewLinks, true), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "RECORD MODEL: ".print_r($recordModel, true), FILE_APPEND);
            //file_put_contents('logs/devLog.log', "\n CURRENT MODULE: ".$request->get('module'), FILE_APPEND);
            $currentModuleName = $request->get('module');
            //file_put_contents('logs/devLog.log', "\n CURRENT MODULE: ".$currentModuleName, FILE_APPEND);
            $relatedLinks = $detailViewLinks['DETAILVIEWRELATED'];
            $loadUrl = null;
            foreach ($relatedLinks as $relatedLink) {
                if ($relatedLink->relatedModuleName == $currentModuleName) {
                    $loadUrl = $relatedLink->linkurl;
                    $tabLabel = $relatedLink->linklabel;
                    $loadUrl = "index.php?".$loadUrl."&tab_label=".$tabLabel;
                    //file_put_contents('logs/devLog.log', "\n LINK: ".$loadUrl, FILE_APPEND);
                }
            }
            if (empty($loadUrl)) {
                $loadUrl = $parentRecordModel->getDetailViewUrl();
            }
        } elseif ($request->get('returnToList')) {
            $loadUrl = $recordModel->getModule()->getListViewUrl();
        } else {
            $loadUrl = $recordModel->getDetailViewUrl();
        }
        header("Location: $loadUrl");
    }

    /**
     * Function to save record
     * @param <Vtiger_Request> $request - values of the record
     * @return <RecordModel> - record Model of saved record
     */
    public function saveRecord($request)
    {
        //file_put_contents('logs/SaveTest.log', date("Y-m-d H:i:s")." - Entering Vtiger_Save::saveRecord\n", FILE_APPEND);
        //file_put_contents('logs/SaveLog.log', date("Y-m-d H:i:s")." - ".print_r($request, true)."\n", FILE_APPEND);
        $recordModel = $this->getRecordModelFromRequest($request);
        $recordModel->save();
        if ($request->get('relationOperation')) {
            $parentModuleName = $request->get('sourceModule');
            $parentModuleModel = Vtiger_Module_Model::getInstance($parentModuleName);
            $parentRecordId = $request->get('sourceRecord');
            $relatedModule = $recordModel->getModule();
            $relatedRecordId = $recordModel->getId();

            $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModule);
            $relationModel->addRelation($parentRecordId, $relatedRecordId);
        }
        if ($request->get('imgDeleted')) {
            $imageIds = $request->get('imageid');
            foreach ($imageIds as $imageId) {
                $status = $recordModel->deleteImage($imageId);
            }
        }
        //file_put_contents('logs/SaveTest.log', date("Y-m-d H:i:s")." - Exiting Vtiger_Save::saveRecord\n", FILE_APPEND);
        return $recordModel;
    }

    /**
     * Function to get the record model based on the request parameters
     * @param Vtiger_Request $request
     * @return Vtiger_Record_Model or Module specific Record Model instance
     */
    protected function getRecordModelFromRequest(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        if (!empty($recordId)) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $modelData = $recordModel->getData();
            $recordModel->set('id', $recordId);
            $recordModel->set('mode', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $modelData = $recordModel->getData();
            $recordModel->set('mode', '');
        }

        $fieldModelList = $moduleModel->getFields();
        foreach ($fieldModelList as $fieldName => $fieldModel) {
            $fieldValue = $request->get($fieldName, null);
            $fieldDataType = $fieldModel->getFieldDataType();
            if ($fieldDataType == 'time') {
                $fieldValue = Vtiger_Time_UIType::getTimeValueWithSeconds($fieldValue);
            }
            if ($fieldValue !== null) {
                if (!is_array($fieldValue)) {
                    $fieldValue = trim($fieldValue);
                }
                $recordModel->set($fieldName, $fieldValue);
            }
        }
        return $recordModel;
    }
        
    public function validateRequest(Vtiger_Request $request)
    {
        return $request->validateWriteAccess();
    }
}
