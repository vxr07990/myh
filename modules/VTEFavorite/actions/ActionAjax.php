<?php

/* ********************************************************************************
 * The content of this file is subject to the VTEFavorite ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class VTEFavorite_ActionAjax_Action extends Vtiger_Action_Controller
{
    public $_columnNameAmountField;
    public $_columnNameDateField;

    /**
     * @return mixed
     */
    public function getColumnNameAmountField()
    {
        if (empty($this->_columnNameAmountField)) {
            $CONFIG_PARAMS = VTEFavorite_Util_Helper::modelGetConfigArray();
            $this->_columnNameAmountField = $CONFIG_PARAMS['amount_columnname'];
        }
        return $this->_columnNameAmountField;
    }

    /**
     * @return mixed
     */
    public function getColumnNameDateField()
    {
        if (empty($this->_columnNameDateField)) {
            $CONFIG_PARAMS = VTEFavorite_Util_Helper::modelGetConfigArray();
            $this->_columnNameDateField = $CONFIG_PARAMS['date_columnname'];
        }
        return $this->_columnNameDateField;
    }

    /**
     * @param Vtiger_Request $request
     */
    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('addFavorite');
        $this->exposeMethod('getFavorite');
        $this->exposeMethod('getlistFavorite');
        $this->exposeMethod('getlistField');
        $this->exposeMethod('addModule');
        $this->exposeMethod('deleModule');
        $this->exposeMethod('saveFields');
        $this->exposeMethod('updateSequenceModule');
        $this->exposeMethod('delRecord');
        $this->exposeMethod('CheckAndGetModulesActive');
        $this->exposeMethod('activeModuleFavorite');
        $this->exposeMethod('addRecently');
        $this->exposeMethod('getCustomView');


        /*    $this->exposeMethod('saveConfig');
           $this->exposeMethod('saveCategory');
           $this->exposeMethod('saveCategoryOpptType');
           $this->exposeMethod('deleteCategory');
           $this->exposeMethod('deleteCategoryOpptType');
           $this->exposeMethod('loadSummaryChart');
           $this->exposeMethod('loadSummary');
           $this->exposeMethod('loadDashboardFrom');
           $this->exposeMethod('loadDashboardFromChildren');
           $this->exposeMethod('loadDashboardSaleStageDetail');
           $this->exposeMethod('loadTargetTab');
           $this->exposeMethod('saveTarget');
           $this->exposeMethod('removeNode');		 */
    }

    function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }


    }

    //
    function delRecord(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        //Get param:
        $userid = $current_user->user_name;

        $id = $request->get('id');
        $type = $request->get('type');
        //get record name


        $adb->pquery('Delete from `vte_' . $type . '_records` where `userid`=? and `id`=?;', array($userid, $id));

        //response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('userid' => $userid));
        $response->emit();


    }

    function addFavorite(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        //Get param:
        $userid = $current_user->user_name;

        $smodule = $request->get('smodule');
        $sview = $request->get('sview');
        $srecord = $request->get('srecord');
        $surl = $request->get('surl');
        $stars = $request->get('stars');
        //get record name
        $recordModel = Vtiger_Record_Model::getInstanceById($srecord, $smodule);
        $recordName = $recordModel->getName();

        $adb->pquery('Delete from `vte_favorite_records` where `userid`=? and `module`=? and `record`=?;', array($userid, $smodule, $srecord));
        $adb->pquery('INSERT INTO `vte_favorite_records`(`userid`,`module`,`view`,`record`,`url`,`stars`,`recordname`,`update`)VALUES(?,?,?,?,?,?,?,NOW());', array($userid, $smodule, $sview, $srecord, $surl, $stars, $recordName));

        //response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('userid' => $userid));
        $response->emit();


    }

    function activeModuleFavorite(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        //Get param:
        $userid = $current_user->user_name;

        $smodule = $request->get('smodule');
        $active = $request->get('active');
        $type = $request->get('type');
        //get record name

        $adb->pquery('UPDATE `vte_' . $type . '_config_module` set `active`=? where `module`=? and `userid` =?;', array($active, $smodule, $userid));

        //response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('success'));
        $response->emit();


    }

    function getFavorite(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        $isshow = 1;
        $stars = 0;
        //Get param:
        $userid = $current_user->user_name;

        $smodule = $request->get('smodule');
        $sview = $request->get('sview');
        $srecord = $request->get('srecord');
        $surl = $request->get('surl');

        //2. Get stars of record:
        $result = $adb->pquery('select stars from `vte_favorite_records` where `userid`=? and `module`=? and `record`=?;', array($userid, $smodule, $srecord));
        $myrow = $adb->fetch_array($result);

        $stars = $myrow['stars'];

        //3. Response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('stars' => $stars, 'isshow' => $isshow, 'recordName' => $recordName));
        $response->emit();


    }

    function CheckAndGetModulesActive(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        $isshow = 1;
        $favisactive = 0;
        $recisactive = 0;
        //Get param:
        $userid = $current_user->user_name;

        $smodule = $request->get('smodule');

        //1. Check setting fav for module:
        $result = $adb->pquery('select count(*) as isactive from `vte_favorite_config_module` where `active`= 1 and `userid`=? and `module`=?;', array($userid, $smodule));
        $myrow = $adb->fetch_array($result);

        $favisactive = $myrow['isactive'];

        //2. Check setting rec for module:
        $result = $adb->pquery('select count(*) as isactive from `vte_recently_config_module` where `active`= 1 and `userid`=? and `module`=?;', array($userid, $smodule));
        $myrow = $adb->fetch_array($result);

        $recisactive = $myrow['isactive'];


        //3. Response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('favisactive' => $favisactive, 'recisactive' => $recisactive));
        $response->emit();


    }

    function getCustomView(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;

        //Get param:
        $smodule = $request->get('smodule');
        //Get favorite of record:
        $emparray = array();
        $result = $adb->pquery('SELECT * FROM vtiger_customview where `entitytype`=?;', array($smodule));
        $i = 0; //resultrow
        while ($row = $adb->fetch_array($result)) {
            $emparray[$i] = $row;
            $i = $i + 1;
        }


        //3. Response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult($emparray); //
        $response->emit();


    }

    function getlistFavorite(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;

        //Get param:
        $userid = $current_user->user_name;
        //Get favorite of record:
        $emparray = array();
        $result = $adb->pquery('select rc.* from vte_favorite_records rc JOIN vte_favorite_config_module cfg on rc.module=cfg.module and rc.userid=cfg.userid where rc.userid=? ORDER BY cfg.order ,stars DESC,`update` DESC;', array($userid));
        //$myrow = $adb->fetch_array($result);
        $i = 0; //resultrow
        while ($row = $adb->fetch_array($result)) {
            $emparray[$i] = array('recordname' => $row['module'] . ' > ' . $row['view'] . ' > ' . $row['recordname'], 'url' => $row['url'], 'stars' => $row['stars']);
            $i = $i + 1;
        }

        //3. Response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('num_rows' => $i, 'records' => $emparray)); //
        $response->emit();


    }

    function addModule(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        $isshow = 1;
        $stars = 0;
        //Get param:
        $userid = $current_user->user_name;

        $smodule = $request->get('smodule');
        $sview = $request->get('sview');
        $srecord = $request->get('srecord');
        $surl = $request->get('surl');
        $type = $request->get('type');
        $viewid = $request->get('viewid');

        //2. Add record:
        if ($type != 'customlist') {
            $sql = 'DELETE FROM vte_' . $type . '_config_module WHERE `userid`=? AND `module`=? ';
            $params = array($userid, $smodule);
            $adb->pquery($sql, $params);
        }
        $result = $adb->pquery('SELECT IFNULL(max(`order`)+1,1) maxorder FROM `vte_' . $type . '_config_module` WHERE `userid`=?;', array($userid));
        $row = $adb->fetch_array($result);
        $maxorder = $row['maxorder'];

        $sql = 'INSERT INTO vte_' . $type . '_config_module(`userid`,`module`, `order`,`active`,limitrecord) VALUES (?,?,?,?,10)';
        $params = array($userid, $smodule, $maxorder, 1);
        $adb->pquery($sql, $params);
        $maxId = $adb->getLastInsertID();
        if ($type == 'customlist') {

            $defaultFields = '';
            $sSQL = "select vtiger_cvcolumnlist.* from vtiger_cvcolumnlist";
            $sSQL .= " inner join vtiger_customview on vtiger_customview.cvid = vtiger_cvcolumnlist.cvid";
            $sSQL .= " where vtiger_customview.cvid =? order by vtiger_cvcolumnlist.columnindex";
            $result = $adb->pquery($sSQL, array($viewid));
            while ($columnrow = $adb->fetch_array($result)) {
                $value = $columnrow['columnname'];
                if ($value != "") {
                    $list = explode(":", $value);
                    if ($defaultFields != "") {
                        $defaultFields = $defaultFields . ',';
                    }
                    $defaultFields = $defaultFields . $list[2];
                }
            }

            $customViewModel = CustomView_Record_Model::getInstanceById($viewid);

            $sql = 'update  vte_' . $type . '_config_module SET `fields`=?,`cvid`=?,cvname=? where  `userid`=? and `module`=? and `id`=?';
            $params = array($defaultFields, $viewid, $customViewModel->get('viewname'), $userid, $smodule,$maxId);
            $adb->pquery($sql, $params);
        }
        //3. Response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('defaultFields' => $defaultFields, 'sql' => $sql, 'viewid' => $viewid));
        $response->emit();


    }

    function getColumnsListByCvid($cvid)
    {
        global $adb;

        $sSQL = "select vtiger_cvcolumnlist.* from vtiger_cvcolumnlist";
        $sSQL .= " inner join vtiger_customview on vtiger_customview.cvid = vtiger_cvcolumnlist.cvid";
        $sSQL .= " where vtiger_customview.cvid =? order by vtiger_cvcolumnlist.columnindex";
        $result = $adb->pquery($sSQL, array($cvid));
        while ($columnrow = $adb->fetch_array($result)) {
            $columnlist[$columnrow['columnindex']] = $columnrow['columnname'];
        }
        return $columnlist;
    }

    function addRecently(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        //Get param:
        $userid = $current_user->user_name;

        $smodule = $request->get('smodule');
        $sview = $request->get('sview');
        $srecord = $request->get('srecord');
        $surl = $request->get('surl');

        //get record name
        $recordModel = Vtiger_Record_Model::getInstanceById($srecord, $smodule);
        $recordName = $recordModel->getName();
        $adb->pquery('Delete from `vte_recently_records` where `userid`=? and `module`=? and `record`=?;', array($userid, $smodule, $srecord));
        //$adb->pquery('Delete from `vte_recently_records` where `userid`=? and `url`=?;', array($userid,$surl));
        $adb->pquery('INSERT INTO `vte_recently_records`(`userid`,`module`,`view`,`record`,`url`,`recordname`,`update`)VALUES(?,?,?,?,?,?,NOW());', array($userid, $smodule, $sview, $srecord, $surl, $recordName));

        //response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('userid' => $userid));
        $response->emit();


    }

    function updateSequenceModule(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        $query = '';
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        // try{
        $sequenceList = $request->get('sequence');
        $type = $request->get('type');

        $query = 'UPDATE vte_' . $type . '_config_module SET `order` = CASE `id` ';
        foreach ($sequenceList as $blockId => $sequence) {
            $query .= " WHEN '" . $blockId . "' THEN " . $sequence;
        }
        $query .= ' END ';
        $adb->pquery($query, array());

        $response->setResult(array('success', $query));
        $response->emit();
    }

    function deleModule(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;

        $userid = $current_user->user_name;

        $smodule = $request->get('smodule');
        $type = $request->get('type');

        //2. Add record:
        $sql = 'DELETE FROM vte_' . $type . '_config_module WHERE `userid`=? AND `id`=? ';
        $params = array($userid, $smodule);
        $adb->pquery($sql, $params);

        //3. Response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('success'));
        $response->emit();
    }

    function saveFields(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        $isshow = 1;
        $stars = 0;
        //Get param:
        $userid = $current_user->user_name;

        $smodule = $request->get('smodule');
        $id = $request->get('id');
        $fields = $request->get('fields');
        $type = $request->get('type');
        if ($type == '') {
            $type = 'favorite';
        }
        $limit = $request->get('limit');
        //1. Update record:
        $sql = 'UPDATE  vte_' . $type . '_config_module SET `fields`=?,limitrecord=? WHERE `userid`=? AND `module`=? AND `id`=?';
        $params = array($fields, $limit, $userid, $smodule,$id);
        $adb->pquery($sql, $params);

        //2. Response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('success', 'fields' => $fields));
        $response->emit();


    }

    //-------------------------------------------Test---------------------------------
    function getlistField(Vtiger_Request $request)
    {
        // global $adb;
        // global $current_user;
        // $smodule=$request->get('smodule');
        // $moduleModel = Vtiger_Module_Model::getInstance($smodule);
        // $fieldModelList = $moduleModel->getFields();

        $allFields = array();

        $supportedModulesList = VTEFavorite_Module_Model::getSupportedModules();
        foreach ($supportedModulesList as $mmodule) {
            $f = VTEFavorite_Module_Model::getFieldsOfModule($mmodule);
            $allFields[$mmodule] = $f; //array('label'=> $f.label,'name'=>$f.name);
            break;
        }

        //3. Response:
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('allFields' => $allFields)); //
        $response->emit();


    }
}