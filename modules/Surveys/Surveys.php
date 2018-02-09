<?php
/***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';
use MoveCrm\InputUtils;

class Surveys extends Vtiger_CRMEntity
{
    protected static $MOVE_EASY_ACCOUNT_VARIABLE = 'moveEasyAccount';
    protected static $MOVE_EASY_PASS_THROUGH_VARIABLE = 'x_hash';
    protected static $CHECK_SURVEY_TYPE = 'Self Survey';
    protected static $ALLOWED_MODULES = [
        'Surveys'
    ];
    public $table_name = 'vtiger_surveys';
    public $table_index= 'surveysid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_surveyscf', 'surveysid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_surveys', 'vtiger_surveyscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_surveys' => 'surveysid',
        'vtiger_surveyscf'=>'surveysid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        'Survey Date'=>array('surveys'=>'survey_date'),
        'Survey Time'=>array('surveys'=>'survey_time'),
        'Surveyor'=>array('crmentity'=>'smownerid'),
        'Survey Status'=>array('surveys'=>'survey_status'),
        'Account Name'=>array('surveys'=>'account_id'),
        'Contact Name'=>array('surveys'=>'contact_id'),
        'Opportunity Name'=>array('surveys'=>'potential_id')

);
    public $list_fields_name = array(
        'Survey Date'=>'survey_date',
        'Survey Time'=>'survey_time',
        'Surveyor'=>'smownerid',
        'Survey Status'=>'survey_status',
        'Account Name'=>'account_id',
        'Contact Name'=>'contact_id',
        'Opportunity Name'=>'potential_id'

);

    // Make the field link to detail view
    public $list_link_field = 'survey_no';

    // For Popup listview and UI type support
    public $search_fields = array(

);
    public $search_fields_name = array(

);

    // For Popup window record selection
    public $popup_fields = array('survey_no');

    // For Alphabetical search
    public $def_basicsearch_col = 'survey_no';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'survey_no';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('createdtime', 'modifiedtime', 'survey_no');

    public $default_order_by = 'survey_no';
    public $default_sort_order='ASC';

    /**
    * Invoked when special actions are performed on the module.
    * @param String Module name
    * @param String Event Type
    */
    public function vtlib_handler($moduleName, $eventType)
    {
        if ($eventType == 'module.postinstall') {
            //Delete duplicates from all picklist
            static::deleteDuplicatesFromAllPickLists($moduleName);
        } elseif ($eventType == 'module.disabled') {
            // TODO Handle actions before this module is being uninstalled.
        } elseif ($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } elseif ($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($eventType == 'module.postupdate') {
            //Delete duplicates from all picklist
            static::deleteDuplicatesFromAllPickLists($moduleName);
        }
    }

    /*
     * Function to get the relation tables for related modules
     * @param - $secmodule secondary module name
     * returns the array with table names and fieldnames storing relations between module and this module
     */
    public function setRelationTables($secmodule)
    {
        $rel_tables = array(
            "Accounts" => array("vtiger_surveys"=>array("surveysid", "account_id")),
            "Contacts" => array("vtiger_surveys"=>array("surveysid", "contact_id")),
            "Potentials" => array("vtiger_surveys"=>array("surveysid", "potential_id")),
        );
        return $rel_tables[$secmodule];
    }

    // Function to unlink an entity with given Id from another entity
    public function unlinkRelationship($id, $return_module, $return_id)
    {
        global $log;
        if (empty($return_module) || empty($return_id)) {
            return;
        }

        if ($return_module == 'Accounts' || $return_module == "Opportunities") {
            $this->trash('Surveys', $id);
        } elseif ($return_module == 'Potentials' || $return_module == "Opportunities") {
            $relation_query = 'UPDATE vtiger_surveys SET potential_id=? WHERE surveysid=?';
            $this->db->pquery($relation_query, array(null, $id));
        } elseif ($return_module == 'Contacts') {
            $relation_query = 'UPDATE vtiger_surveys SET contact_id=? WHERE surveysid=?';
            $this->db->pquery($relation_query, array(null, $id));
        } else {
            $sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
            $params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
            $this->db->pquery($sql, $params);
        }
    }

    /**
     * Delete doubloons from all pick list from module
     */
    public static function deleteDuplicatesFromAllPickLists($moduleName)
    {
        global $adb,$log;

        $log->debug("Invoking deleteDuplicatesFromAllPickList(".$moduleName.") method ...START");

        //Deleting doubloons
        $query = "SELECT columnname FROM `vtiger_field` WHERE uitype in (15,16,33) "
                . "and tabid in (select tabid from vtiger_tab where name = '$moduleName')";
        $result = $adb->pquery($query, array());

        $a_picklists = array();
        while ($row = $adb->fetchByAssoc($result)) {
            $a_picklists[] = $row["columnname"];
        }

        foreach ($a_picklists as $picklist) {
            static::deleteDuplicatesFromPickList($picklist);
        }

        $log->debug("Invoking deleteDuplicatesFromAllPickList(".$moduleName.") method ...DONE");
    }

    public static function deleteDuplicatesFromPickList($pickListName)
    {
        global $adb,$log;

        $log->debug("Invoking deleteDuplicatesFromPickList(".$pickListName.") method ...START");

        //Deleting doubloons
        $query = "SELECT {$pickListName}id FROM vtiger_{$pickListName} GROUP BY {$pickListName}";
        $result = $adb->pquery($query, array());

        $a_uniqueIds = array();
        while ($row = $adb->fetchByAssoc($result)) {
            $a_uniqueIds[] = $row[$pickListName.'id'];
        }

        if (!empty($a_uniqueIds)) {
            $query = "DELETE FROM vtiger_{$pickListName} WHERE {$pickListName}id NOT IN (".implode(",", $a_uniqueIds).")";
            $adb->pquery($query, array());
        }

        $log->debug("Invoking deleteDuplicatesFromPickList(".$pickListName.") method ...DONE");
    }

    public function saveentity($module, $fileid = '') {
        parent::saveentity($module, $fileid);
        $this->saveSurveyUrl($this->updateSelfSurveyURL());
        if($this->column_fields['survey_type'] == 'LiveSurvey') {
            $this->generateNewVideoSurvey();
        }
        if (!getenv('SYNC_CALENDAR_ID')) {
            return;
        }

        $current_user = Users_Record_Model::getCurrentUserModel();
        $db = &PearDatabase::getInstance();

        $stmt = 'SELECT * FROM vtiger_wsapp_sync_state WHERE userid=?';
        $res = $db->pquery($stmt, [$current_user->getId()]);
        //| id | name                  | stateencodedvalues                                                    | userid |
        //| 44 | Vtiger_GoogleCalendar | {"synctrackerid":"59400bfd2f276","synctoken":1497372542,"more":false} |    127 |

        $stateValues = [];
        if (
            $res &&
            method_exists($res, 'fetchRow')
        ) {
            $row = $res->fetchRow();
            $stateValues = json_decode($row['stateencodedvalues'], true);
        }

        if (!$stateValues['synctrackerid']) {
            return;
        }

        $appId = false;
        $stmt = 'SELECT * FROM vtiger_wsapp WHERE appkey=?';
        //| appid | name                     | appkey        | type |
        //|    81 | Google_vtigerSyncHandler | 59400bfd2f276 | user |
        $res = $db->pquery($stmt, [$stateValues['synctrackerid']]);
        if (
            $res &&
            method_exists($res, 'fetchRow')
        ) {
            $row = $res->fetchRow();
            $appId = $row['appid'];
        }

        if (!$appId) {
            return;
        }

        $stmt = 'SELECT *FROM vtiger_wsapp_recordmapping WHERE appid=? AND clientid=?';
        $res = $db->pquery($stmt, [$appId, $this->column_fields['google_apt_id']]);
        //| id    | serverid  | clientid                   | clientmodifiedtime  | appid | servermodifiedtime  | serverappid |
        //| 34825 | 18x280690 | sj2sk30mpf6pl88silfdumgpa0 | 2017-06-13 16:52:55 |    81 | 2017-06-13 16:49:02 |           1 |
        if (
            $res &&
            method_exists($res, 'fetchRow')
        ) {
            $row = $res->fetchRow();
            if ($row['id']) {
                //Already synced so it's ok?
                return;
            }
        }

        //include_once ('modules/WSAPP/SyncServer.php');
        $serverKey = wsapp_getAppKey("vtigerCRM");
        $serverAppId = SyncServer::appid_with_key($serverKey);

        $values = [
            'serverid' => vtws_getWebserviceEntityId('Surveys', $this->id),
            'clientid' => $this->column_fields['google_apt_id'],
            'appid' => $appId,
            'serverappid' => $serverAppId
        ];
        $stmt = 'INSERT INTO vtiger_wsapp_recordmapping (clientmodifiedtime,servermodifiedtime,'.
                implode(',',array_keys($values))
                .') VALUES (NOW(),NOW(),'.
                generateQuestionMarks($values)
                . ')';
        $db->pquery($stmt, $values);
    }

    protected function generateNewVideoSurvey() {
        //Appointment is for a virtual survey - automatically creating Cubesheet record with TokBox data
        $assignedUser = $this->column_fields['assigned_user_id'];
        $contactId = $this->column_fields['contact_id'];
        $opportunityId = $this->column_fields['potential_id'];
        $orderId = $this->column_fields['order_id'];
        $surveyDateTime = strtotime($this->column_fields['survey_date'].' '.$this->column_fields['survey_time']);
        //Set code expiration to 2 days after survey appointment
        $expirationDateTime = $surveyDateTime + (60 * 60 * 24 * 2);
        file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ').'Survey Datetime: '.$surveyDateTime.'; Expiration Datetime: '.$expirationDateTime."\n", FILE_APPEND);

        $contactRecord = Contacts_Record_Model::getInstanceById($contactId);
        try {
            $sessionId = Cubesheets_Record_Model::getNewTokboxSession();
            $user = new Users();
            //$adminid = '19x1';
            //$currentuserid = Users_Record_Model::getCurrentUserModel()->getId();
            $admin_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            $data = array(
                'cubesheet_name' => $contactRecord->get('lastname').' LiveSurvey',
                'contact_id' => vtws_getWebserviceEntityId('Contacts', $contactId),
                'potential_id' => vtws_getWebserviceEntityId('Opportunities', $opportunityId),
                'cubesheets_orderid' => vtws_getWebserviceEntityId('Orders', $orderId),
                'assigned_user_id' => '19x'.$assignedUser,
                'survey_type' => 'LiveSurvey',
                'survey_appointment_id' => vtws_getWebserviceEntityId('Surveys', $this->id),
                'tokbox_sessionid' => $sessionId,
                'tokbox_servertoken' => Cubesheets_Record_Model::getNewTokboxToken($sessionId),
                'tokbox_clienttoken' => Cubesheets_Record_Model::getNewTokboxToken($sessionId),
                'tokbox_devicecode' => Cubesheets_Record_Model::getNewUniqueDeviceCode(),
                'tokbox_code_expiration' => $expirationDateTime
            );
            if(empty($opportunityId)) {
                unset($data['potential_id']);
            }
            if(empty($orderId)) {
                unset($data['cubesheets_orderid']);
            }
            $cubesheet_record = vtws_create('Cubesheets', $data, $admin_user);

            $cubesheetid = substr(strstr($cubesheet_record['id'], 'x'), 1);
            $this->column_fields['cubesheetid'] = $cubesheetid;

            file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ').'Cubesheet record successfully created with id '.$cubesheetid."\n", FILE_APPEND);

            //				$recordModel = Cubesheets_Record_Model::getInstanceById($cubesheetid);
            //				$recordModel->getTokboxServerToken();
            //				$recordModel->getTokboxClientToken();
            //				$recordModel->getDeviceCode();
            //				$recordModel->setExpirationDate($expirationDateTime);
        } catch (WebServiceException $ex) {
            file_put_contents('logs/AutocreateCubesheets.log', date('Y-m-d H:i:s - ').$ex->getMessage()."\n", FILE_APPEND);
        }
    }

    protected function saveSurveyUrl($url) {
        if (!$url) {
            return;
        }
        $db = &PearDatabase::getInstance();

        $stmt = 'UPDATE '.$this->table_name.' SET self_survey_url=? where ' . $this->table_index . '=?';
        $db->pquery($stmt, [$url, $this->id]);
        return $url;
    }

    protected function updateSelfSurveyURL() {
        $this->fieldList = array_merge($_REQUEST, $this->column_fields);
        $survey_type = $this->fieldList['survey_type'];
        if ($survey_type != self::$CHECK_SURVEY_TYPE) {
            return;
        }

        $self_survey_url = $this->fieldList['self_survey_url'];

        $moduleModel = Vtiger_Module_Model::getInstance($this->moduleName);
        if (!$moduleModel) {
            return;
        }

        $fieldModel = $moduleModel->getField('self_survey_url');
        if (!$fieldModel) {
            return;
        }

        if (
            $self_survey_url &&
            $self_survey_url != $fieldModel->defaultvalue
        ) {
            return;
        }

        $url = $this->getSurveyUrl();
        $_REQUEST['self_survey_url'] = $this->column_fields['self_survey_url'] = $url;
        return $url;
    }

    protected function getSurveyUrl() {
        $parsed_url = parse_url(getenv('MOVE_EASY_LINK_URL'));

        $url = isset($parsed_url['scheme'])?$parsed_url['scheme'].'://':'http://';
        $user = isset($parsed_url['user'])?$parsed_url['user']:'';
        $pass = isset($parsed_url['pass'])?':'.$parsed_url['pass']:'';
        $url .= ($user || $pass)?$user.$pass.'@':'';
        $url .= isset($parsed_url['host'])?$parsed_url['host']:'';
        $url .= isset($parsed_url['port'])?':'.$parsed_url['port']:'';
        $url .= isset($parsed_url['path'])?$parsed_url['path']:'';
        $url .= isset($parsed_url['query'])?'?'.$parsed_url['query'].'&':'?';
        //$url  .= isset($parsed_url['fragment'])?'#'.$parsed_url['fragment']:'';

        //This will need to include an account Id parameter which will indicate which sync service the Move Easy system will send the data
        $url .= self::$MOVE_EASY_ACCOUNT_VARIABLE . '='. getenv('MOVE_EASY_ACCOUNT_IDENTIFIER');
        $url .= self::$MOVE_EASY_PASS_THROUGH_VARIABLE . '='. $this->getPassThroughHash();

        return $url;
    }

    protected function getPassThroughHash() {
        if (!is_array($this->fieldList)) {
            return;
        }

        //It will also include a hash that will indicate the environment,
        //instance key and
        //opportunity Id
        //so we can verify that the data is being sent to the correct instance
        //and attach the survey data to the correct opportunity in MoveCRM.
        $relatedRecords = $this->getSurveyRelatedRecords();
        $array = [
            'host' => $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'],
            //'host' => $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'],
            'moveHQ' => InputUtils::CheckboxToBool(getenv('IGC_MOVEHQ')) ? true : false,
            'instance_name' => getenv('INSTANCE_NAME'),
            'id' => vtws_getWebserviceEntityId($this->moduleName, $this->id),
            'related_records' => $relatedRecords
        ];
        $jsonString = json_encode($array, true);
        $base64String = base64_encode($jsonString);

        return $base64String;
    }

    protected function getSurveyRelatedRecords() {
        //@TODO: there must be an existing thing to do this without being explicit
        $returnArray    = [];
        $relatedRecords = [
            'Opportunities' => 'potential_id',
            'Orders'        => 'order_id',
            'Contacts'      => 'contact_id',
            'Accounts'      => 'account_id',
        ];

        foreach ($relatedRecords as $module => $field) {
            $singleRecord = $this->getSingleRelatedRecord($module, $field);
            if ($singleRecord) {
                $returnArray[] = [$module => $singleRecord];
            }
        }

        return $returnArray;
    }

    protected function getSingleRelatedRecord($module, $field) {
        if (!is_array($this->fieldList)) {
            return;
        }
        if (!array_key_exists($field, $this->fieldList)) {
            return;
        }
        if (!$this->fieldList[$field]) {
            return;
        }
        if (!$module) {
            return;
        }
        return vtws_getWebserviceEntityId($module, $this->fieldList[$field]);
    }
}
