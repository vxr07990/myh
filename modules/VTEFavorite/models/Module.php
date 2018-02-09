<?php

/* ********************************************************************************
 * The content of this file is subject to the VTEFavorite ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class VTEFavorite_Module_Model extends Vtiger_Module_Model
{
    public static $supportedModules = false;
    public static $restrictedModules = array('Calendar', 'Webmails', 'SMSNotifier', 'Emails', 'Integration', 'Dashboard', 'ModComments', 'vtmessages', 'vttwitter');

    function getSettingLinks()
    {
        $settingsLinks[] = array(
            'linktype' => 'MODULESETTING',
            'linklabel' => 'Settings',
            'linkurl' => 'index.php?module=VTEFavorite&parent=Settings&view=Settings',
            'linkicon' => ''
        );
        
        return $settingsLinks;
    }

    /**
     * Function to get Entity module names list
     * @return <Array> List of Entity modules
     */
    public static function getEntityModulesListOfFav()
    {
        global $current_user;
        $db = PearDatabase::getInstance();
        self::preModuleInitialize2();
        $userid = $current_user->user_name;
        $presence = array(0, 2);
        //$restrictedModules = array('Calendar','Webmails', 'SMSNotifier', 'Emails', 'Integration', 'Dashboard', 'ModComments', 'vtmessages', 'vttwitter','Calendar');

        $query = 'SELECT  id,`name`,`tablabel`,`active`,`fields`   FROM vtiger_tab tab inner join vte_favorite_config_module cfg on tab.name=cfg.module  WHERE
						presence IN (' . generateQuestionMarks($presence) . ')
						AND isentitytype = ?
						AND name NOT IN (' . generateQuestionMarks(self::$restrictedModules) . ')
						AND cfg.`userid` = ?  ORDER BY `cfg`.`order` ASC';
        $result = $db->pquery($query, array($presence, 1, self::$restrictedModules, $userid));
        $numOfRows = $db->num_rows($result);

        $modulesList = array();

        for ($i = 0; $i < $numOfRows; $i++) {
            $moduleName = $db->query_result($result, $i, 'name');

            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            $fieldModelList = $moduleModel->getFields();
            $customlistid = $db->query_result($result, $i, 'id');
            $tablabel = $db->query_result($result, $i, 'tablabel');
            $active = $db->query_result($result, $i, 'active');
            $fields = explode(",", $db->query_result($result, $i, 'fields'));

            $row = array('customlistid' => $customlistid,'moduleName' => $moduleName, 'tablabel' => $tablabel, 'active' => $active, 'fields' => $fields);
            $modulesList[$i] = $row;
        }
        return $modulesList;
    }

    public static function getEntityModulesListOfRec()
    {
        global $current_user;
        $db = PearDatabase::getInstance();
        self::preModuleInitialize2();
        $userid = $current_user->user_name;
        $presence = array(0, 2);
        //$restrictedModules = array('Calendar','Webmails', 'SMSNotifier', 'Emails', 'Integration', 'Dashboard', 'ModComments', 'vtmessages', 'vttwitter','Calendar');

        $query = 'SELECT  id,`name`,`tablabel`,`active`,`fields`,limitrecord   FROM vtiger_tab tab inner join vte_recently_config_module cfg on tab.name=cfg.module  WHERE
						presence IN (' . generateQuestionMarks($presence) . ')
						AND isentitytype = ?
						AND name NOT IN (' . generateQuestionMarks(self::$restrictedModules) . ')
						AND cfg.`userid` = ?  ORDER BY `cfg`.`order` ASC';
        $result = $db->pquery($query, array($presence, 1, self::$restrictedModules, $userid));
        $numOfRows = $db->num_rows($result);

        $modulesList = array();

        for ($i = 0; $i < $numOfRows; $i++) {
            $moduleName = $db->query_result($result, $i, 'name');

            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            $fieldModelList = $moduleModel->getFields();
            $customlistid = $db->query_result($result, $i, 'id');
            $tablabel = $db->query_result($result, $i, 'tablabel');
            $active = $db->query_result($result, $i, 'active');
            $fields = explode(",", $db->query_result($result, $i, 'fields'));
            $limitrecord = $db->query_result($result, $i, 'limitrecord');
            $row = array('customlistid' => $customlistid,'moduleName' => $moduleName, 'tablabel' => $tablabel, 'active' => $active, 'fields' => $fields, 'limitrecord' => $limitrecord);
            $modulesList[$i] = $row;
        }
        return $modulesList;
    }

    public static function getEntityModulesListOfClt()
    {
        global $current_user;
        $db = PearDatabase::getInstance();
        self::preModuleInitialize2();
        $userid = $current_user->user_name;
        $presence = array(0, 2);
        //$restrictedModules = array('Calendar','Webmails', 'SMSNotifier', 'Emails', 'Integration', 'Dashboard', 'ModComments', 'vtmessages', 'vttwitter','Calendar');

        $query = 'SELECT  id,`name`,`tablabel`,`active`,`fields`,limitrecord,cvname   FROM vtiger_tab tab inner join vte_customlist_config_module cfg on tab.name=cfg.module  WHERE
						presence IN (' . generateQuestionMarks($presence) . ')
						AND isentitytype = ?
						AND name NOT IN (' . generateQuestionMarks(self::$restrictedModules) . ')
						AND cfg.`userid` = ?  ORDER BY `cfg`.`order` ASC';
        $result = $db->pquery($query, array($presence, 1, self::$restrictedModules, $userid));
        $numOfRows = $db->num_rows($result);

        $modulesList = array();

        for ($i = 0; $i < $numOfRows; $i++) {
            $moduleName = $db->query_result($result, $i, 'name');

            $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
            $fieldModelList = $moduleModel->getFields();

            $customlistid = $db->query_result($result, $i, 'id');
            $tablabel = $db->query_result($result, $i, 'tablabel');
            $active = $db->query_result($result, $i, 'active');
            $fields = explode(",", $db->query_result($result, $i, 'fields'));
            $limitrecord = $db->query_result($result, $i, 'limitrecord');
            $cvname = $db->query_result($result, $i, 'cvname');
            $row = array('customlistid' => $customlistid,'cvname' => $cvname, 'moduleName' => $moduleName, 'tablabel' => $tablabel, 'active' => $active, 'fields' => $fields, 'limitrecord' => $limitrecord);
            $modulesList[$i] = $row;
        }
        return $modulesList;
    }

    /**
     * Function to get Entity module names list
     * @return <Array> List of Entity modules
     */
    public static function getEntityModulesListAllOfFav()
    {
        global $current_user;
        $db = PearDatabase::getInstance();
        self::preModuleInitialize2();

        $userid = $current_user->user_name;
        $presence = array(0, 2);

        $query = 'SELECT name FROM vtiger_tab WHERE
						presence IN (' . generateQuestionMarks($presence) . ')
						AND isentitytype = ?
						AND name NOT IN (' . generateQuestionMarks(self::$restrictedModules) . ')
						AND name NOT IN (select module from vte_favorite_config_module where userid=?)
						';
        $result = $db->pquery($query, array($presence, 1, self::$restrictedModules, $userid));
        $numOfRows = $db->num_rows($result);

        $modulesList = array();
        for ($i = 0; $i < $numOfRows; $i++) {
            $moduleName = $db->query_result($result, $i, 'name');
            $modulesList[$moduleName] = $moduleName;
        }

        return $modulesList;
    }

    public static function getEntityModulesListAllOfRec()
    {
        global $current_user;
        $db = PearDatabase::getInstance();
        self::preModuleInitialize2();

        $userid = $current_user->user_name;
        $presence = array(0, 2);

        $query = 'SELECT name FROM vtiger_tab WHERE
						presence IN (' . generateQuestionMarks($presence) . ')
						AND isentitytype = ?
						AND name NOT IN (' . generateQuestionMarks(self::$restrictedModules) . ')
						AND name NOT IN (select module from vte_recently_config_module where userid=?)
						';
        $result = $db->pquery($query, array($presence, 1, self::$restrictedModules, $userid));
        $numOfRows = $db->num_rows($result);

        $modulesList = array();
        for ($i = 0; $i < $numOfRows; $i++) {
            $moduleName = $db->query_result($result, $i, 'name');
            $modulesList[$moduleName] = $moduleName;
        }
        return $modulesList;
    }

    public static function getEntityModulesListAllOfClt()
    {
        global $current_user;
        $db = PearDatabase::getInstance();
        self::preModuleInitialize2();

        $userid = $current_user->user_name;
        $presence = array(0, 2);

        $query = 'SELECT name FROM vtiger_tab WHERE
						presence IN (' . generateQuestionMarks($presence) . ')
						AND isentitytype = ?
						AND name NOT IN (' . generateQuestionMarks(self::$restrictedModules) . ')
						';
        //AND name NOT IN (select module from vte_customlist_config_module where userid=?)
        //$result = $db->pquery($query, array($presence, 1, self::$restrictedModules, $userid));
        $result = $db->pquery($query, array($presence, 1, self::$restrictedModules));
        $numOfRows = $db->num_rows($result);

        $modulesList = array();
        for ($i = 0; $i < $numOfRows; $i++) {
            $moduleName = $db->query_result($result, $i, 'name');
            $modulesList[$moduleName] = $moduleName;
        }
        return $modulesList;
    }

    public static function getSupportedModules($type)
    {

        switch ($type) {
            case "AllFav":
                return self::getEntityModulesListAllOfFav();
                break;
            case "Fav":
                return self::getEntityModulesListOfFav();
                break;
            case "AllRec":
                return self::getEntityModulesListAllOfRec();
                break;
            case "Rec":
                return self::getEntityModulesListOfRec();
                break;
            case "AllClt":
                return self::getEntityModulesListAllOfClt();
                break;
            case "Clt":
                return self::getEntityModulesListOfClt();
                break;
        }
    }

    public static function getFieldsOfModule($moduleName)
    {
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $fieldModelList = $moduleModel->getFields();
        return $fieldModelList;
    }

    public static function getRecords($moduleName, $strIDs, $arrRows, $mode, $userid)
    {
        $adb = PearDatabase::getInstance();
        //getRecords($k,$v,$rows,$mode,$userid
        // $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        // $fieldModelList = $moduleModel->getFields();

        $result = $adb->pquery('SELECT IFNULL(limitrecord,0) limitrecord FROM `vte_' . $mode . '_config_module` WHERE `userid`=? and `module`=? ;', array($userid, $moduleName));
        $row = $adb->fetch_array($result);
        $limitrecord = $row['limitrecord'];

        $arrIDs = explode(',', $strIDs);
        $arrRet = array();
        $index = 0;
        foreach ($arrIDs as $id) {
            $index = $index + 1;
            $arrRet[$id] = array('Record' => Vtiger_Record_Model::getInstanceById($arrRows[$id]['record'], $moduleName), 'Metadata' => $arrRows[$id]); //$arrRows[$id];
            //$arrRet[$id]=array('Record'=> Vtiger_DetailView_Model::getInstance($moduleName,$arrRows[$id]['record']),'Metadata'=>$arrRows[$id]);//$arrRows[$id];
            // $arrRet[$id]=array('moduleName'=>$moduleName,'id'=>$arrRows[$id]['record']) ;//$arrRows[$id];
            if ($mode == 'recently' && $index >= $limitrecord) {
                break;
            }
        }

        return $arrRet;
    }

    public static function getRecords_CustomList($moduleName, $arrIDs, $userid)
    {
        $adb = PearDatabase::getInstance();

        $result = $adb->pquery('SELECT IFNULL(limitrecord,0) limitrecord FROM `vte_customlist_config_module` WHERE `userid`=? and `module`=? ;', array($userid, $moduleName));
        $row = $adb->fetch_array($result);
        $limitrecord = $row['limitrecord'];

        //$arrIDs = explode(',' , $strIDs);
        $arrRet = array();
        $index = 0;
        foreach ($arrIDs as $id) {
            $index = $index + 1;
            $arrRet[$id] = array('Record' => Vtiger_Record_Model::getInstanceById($id, $moduleName), 'Metadata' => array()); //$arrRows[$id];
            //$arrRet[$id]=array('Record'=> Vtiger_DetailView_Model::getInstance($moduleName,$arrRows[$id]['record']),'Metadata'=>$arrRows[$id]);//$arrRows[$id];
            // $arrRet[$id]=array('moduleName'=>$moduleName,'id'=>$arrRows[$id]['record']) ;//$arrRows[$id];
            if ($index >= $limitrecord) {
                break;
            }
        }

        return $arrRet;
    }

    public static function getArrFields($moduleName, $strFields)
    {
        $arrRet = array();
        if (empty($strFields)) return $arrRet;

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $fieldModelList = $moduleModel->getFields();

        $arrFields = explode(',', $strFields);

        foreach ($arrFields as $id) {
            $arrRet[$id] = $fieldModelList[$id];
        }

        return $arrRet;
    }

    public static function getRecordList($mode)
    {
        global $current_user;
        $adb = PearDatabase::getInstance();
        self::preModuleInitialize2();
        $userid = $current_user->user_name;

        $arrModules = array();
        $arrModules_Temp = array();
        if ($mode == 'favorite') {
            $result = $adb->pquery('SELECT rc.*,cfg.`fields`,crm.label FROM vte_favorite_records rc
                                 INNER JOIN vtiger_crmentity crm ON rc.record=crm.crmid AND crm.deleted=0
                                 INNER JOIN vte_favorite_config_module cfg ON rc.module=cfg.module AND rc.userid=cfg.userid
                                 WHERE rc.userid=? and cfg.active=1 ORDER BY cfg.order ,stars DESC,`update` DESC;', array($userid));
        } elseif ($mode == 'recently') {
            $result = $adb->pquery('select rc.*,cfg.`fields` from vte_recently_records rc JOIN vte_recently_config_module cfg on rc.module=cfg.module and rc.userid=cfg.userid where rc.userid=?  and cfg.active=1 ORDER BY cfg.order ,`update` DESC ;', array($userid));
        }

        $i = 0; //resultrow
        $rows = array();
        while ($row = $adb->fetch_array($result)) {

            if (array_key_exists($row['module'], $arrModules_Temp)) {
                $arrModules_Temp[$row['module']] .= ',' . (string)$row['id'];
            } else {
                $arrModules_Temp[$row['module']] = (string)$row['id'];
            }
            $rows[$row['id']] = $row;
            $i = $i + 1;
        }
        foreach ($arrModules_Temp as $k => $v) {
            $id = explode(',', $v);
			$id = $id[0];
            $arrModules[$k] = array('name' => $k, 'arrFields' => self::getArrFields($k, $rows[$id]['fields']), 'ids' => $v, 'arrRecords' => self::getRecords($k, $v, $rows, $mode, $userid)); //'arrRecords'=>self::getRecords($v,$rows)
            if($arrModules[$k]['name']=='Contacts' || $arrModules[$k]['name']=='Leads'){
                $fullNameField = new Vtiger_Field_Model();
                $fullNameField->set('name','fullname');
                $fullNameField->set('label','Full Name');
                if (strpos($rows[$id]['fields'], 'fullname') !== false) {
                    $arrModules[$arrModules[$k]['name']]['arrFields']['fullname']=$fullNameField;
                }
            }
        }
        $arrReturn = array('arrModules' => $arrModules);

        return $arrReturn;
    }

    public static function getRecordList_CustomList()
    {
        global $current_user;
        $adb = PearDatabase::getInstance();
        self::preModuleInitialize2();
        $userid = $current_user->user_name;
        $arrModules = array();
        $result = $adb->pquery('select * from vte_customlist_config_module where userid=? and active=1 ORDER BY `order` ;', array($userid));

        while ($row = $adb->fetch_array($result)) {
            $customViewModel = CustomView_Record_Model::getInstanceById($row['cvid']);
            $records = $customViewModel->getRecordIds('', $row['module']);
            $ids = join(',', $records);

            if ($records) {
                $arrModules[$row['cvid'].'_'.$row['module']] = array('name' => $row['module'],'cvid'=>$row['cvid'],'cvname' => $row['cvname'], 'arrFields' => self::getArrFields($row['module'], $row['fields']), 'ids' => $ids, 'arrRecords' => self::getRecords_CustomList($row['module'], $records, $userid)); //'arrRecords'=>self::getRecords($v,$rows)
            }
        }
        $arrReturn = array('arrModules' => $arrModules);
        return $arrReturn;
    }
}