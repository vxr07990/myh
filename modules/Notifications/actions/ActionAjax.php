<?php

/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/**
 * Class Notifications_ActionAjax_Action
 */
class Notifications_ActionAjax_Action extends Vtiger_Action_Controller
{

    /**
     * @param Vtiger_Request $request
     */
    function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    /**
     *
     */
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('enableModule');
        $this->exposeMethod('checkEnable');
        $this->exposeMethod('getNotifications');
        $this->exposeMethod('getNotificationNumber');
        $this->exposeMethod('markNotificationRead');
    }
    
    /**
     * @param Vtiger_Request $request
     * @throws Exception
     */
    function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * @param Vtiger_Request $request
     */
    function getNotificationNumber(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $data = array();

		$currentUser = Users_Record_Model::getCurrentUserModel();
		$data['count'] = Notifications_Record_Model::countNotificationsByUser($currentUser->getId());

		$response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     */
    function getNotifications(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $data = array();

		$currentUser = Users_Record_Model::getCurrentUserModel();
		$notifications = Notifications_Record_Model::getNotificationsByUser($currentUser->getId());
		$calendarDatetimeUIType = new Calendar_Datetime_UIType();

		$items = array();
		/** @var Notifications_Record_Model $n */
		foreach ($notifications as $n) {
			$relatedId = $n->get('related_to');
			if (!$relatedId || !isRecordExists($relatedId)) {
				continue;
			}

			$relatedRecordModel = Vtiger_Record_Model::getInstanceById($relatedId);
            $createdtime = new DateTimeField($n->get('createdtime'));

			$items[] = array(
				'id' => $n->get('notificationid'),
				'notificationno' => $n->get('notificationno'),
				'description' => $n->get('description'),
				'thumbnail' => 'layouts/vlayout/skins/images/summary_Leads.png',
				'createdtime' => $createdtime->getDisplayDateTimeValue($currentUser, true),
				'full_name' => $relatedRecordModel->getDisplayName(),
				'link' => $relatedRecordModel->getDetailViewUrl(),
				'rel_id' => $relatedId,
			);
		}

		$data['items'] = $items;
		$data['count'] = count($items);

		$response->setResult($data);
        $response->emit();
    }

    /**
     * @param Vtiger_Request $request
     */
    public function markNotificationRead(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $record = $request->get('record');
        $module = $request->getModule();

		$updated = Notifications_Record_Model::updateNotificationStatus($record, Notifications_Record_Model::NOTIFICATION_STATUS_YES);

		if (!$updated) {
			$code = 200;
			$response->setError($code, vtranslate('ERR_UNABLE_TO_CHANGE_STATUS', $module));
		}

        $response->emit();
    }

    function enableModule(Vtiger_Request $request) {
        global $adb;
        $value=$request->get('value');
        $rs = $adb->pquery("SELECT `enable` FROM `notifications_settings`;", array());
        if ($adb->num_rows($rs) == 0) {
            $adb->pquery("INSERT INTO `notifications_settings` (`enable`) VALUES ('1');", array());
        }else{
            $adb->pquery("UPDATE `notifications_settings` SET `enable`=?",array($value));
        }

        if ($value == 1) {
            $moduleName = $request->getModule();;
            $moduleInstance = Vtiger_Module::getInstance($moduleName);
            $field2block1 = Vtiger_Field::getInstance('related_to', $moduleInstance);
            // related modules
            $relatedmodules1 = Settings_LayoutEditor_Module_Model::getEntityModulesList();

            $sql = "SELECT * FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=?";
            $params = array($field2block1->id, $field2block1->getModuleName());
            $result = $adb->pquery($sql, $params);

            $dbRelatedmodules1 = array();
            $relatedmodules1ToAdd = array();
            $relatedmodules1ToRemove = array();

            if ($adb->num_rows($result)) {
                while ($row = $adb->fetch_array($result)) {
                    $dbRelatedmodules1[] = $row['relmodule'];
                }
            }

            // add relations
            foreach ($relatedmodules1 as $item) {
                if (!in_array($item, $dbRelatedmodules1)) {
                    $relatedmodules1ToAdd[] = $item;
                }
            }

            $field2block1->setRelatedModules($relatedmodules1ToAdd);

            // remove relations
            foreach ($dbRelatedmodules1 as $item) {
                if (!in_array($item, $relatedmodules1)) {
                    $relatedmodules1ToRemove[] = $item;
                }
            }

            $field2block1->unsetRelatedModules($relatedmodules1ToRemove);

        }

        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('result'=>'success'));
        $response->emit();
    }

    function checkEnable(Vtiger_Request $request) {
        global $adb;
        $rs=$adb->pquery("SELECT `enable` FROM `notifications_settings`;",array());
        $enable=$adb->query_result($rs,0,'enable');
        $response = new Vtiger_Response();
        $response->setEmitType(Vtiger_Response::$EMIT_JSON);
        $response->setResult(array('enable'=>$enable));
        $response->emit();
    }

}