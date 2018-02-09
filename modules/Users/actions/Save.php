<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Users_Save_Action extends Vtiger_Save_Action
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $record = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if (
            !Users_Privileges_Model::isPermitted($moduleName, 'Save', $record) ||
            (
                $recordModel->isAccountOwner() &&
                $currentUserModel->get('id') != $recordModel->getId() &&
                !$currentUserModel->isAdminUser()
            )
        ) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
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
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if (!empty($recordId)) {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
            $modelData = $recordModel->getData();
            $recordModel->set('id', $recordId);
            $sharedType = $request->get('sharedtype');
            if (!empty($sharedType)) {
                $recordModel->set('calendarsharedtype', $request->get('sharedtype'));
            }
            $recordModel->set('mode', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $modelData = $recordModel->getData();
            $recordModel->set('mode', '');
        }

        foreach ($modelData as $fieldName => $value) {
            $requestFieldExists = $request->has($fieldName);
            if (!$requestFieldExists) {
                continue;
            }
            $fieldValue = $request->get($fieldName, null);

            if ($fieldName === 'is_admin') {
                if (!$currentUserModel->isAdminUser() && (!$fieldValue)) {
                    $fieldValue = 'off';
                } elseif ($currentUserModel->isAdminUser() && ($fieldValue || $fieldValue === 'on')) {
                    $fieldValue = 'on';
                    $recordModel->set('is_owner', 1);
                } else {
                    $fieldValue = 'off';
                    $recordModel->set('is_owner', 0);
                }
            }
            if ($fieldValue !== null) {
                if (!is_array($fieldValue)) {
                    $fieldValue = trim($fieldValue);
                }
                $recordModel->set($fieldName, $fieldValue);
            }
        }
        $homePageComponents = $recordModel->getHomePageComponents();
        $selectedHomePageComponents = $request->get('homepage_components', array());
        foreach ($homePageComponents as $key => $value) {
            if (in_array($key, $selectedHomePageComponents)) {
                $request->setGlobal($key, $key);
            } else {
                $request->setGlobal($key, '');
            }
        }

        // Tag cloud save
        $tagCloud = $request->get('tagcloudview');
        if ($tagCloud == "on") {
            $recordModel->set('tagcloud', 0);
        } else {
            $recordModel->set('tagcloud', 1);
        }
        return $recordModel;
    }

    public function process(Vtiger_Request $request)
    {
        $result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
        $_FILES = $result['imagename'];

        $recordModel = $this->saveRecord($request);
        $this->updateRecordOnPersonelModule($request);

        if ($request->get('relationOperation')) {
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($request->get('sourceRecord'), $request->get('sourceModule'));
            $loadUrl = $parentRecordModel->getDetailViewUrl();
        } elseif ($request->get('isPreference')) {
            $loadUrl =  $recordModel->getPreferenceDetailViewUrl();
        } else {
            $loadUrl = $recordModel->getDetailViewUrl();
        }

        header("Location: $loadUrl");
    }

    public function updateRecordOnPersonelModule($request){
        if(getenv('INSTANCE_NAME') == 'graebel')
        {
            return;
        }

        $recordId = $request->get('record');

        $arrMappings = [
            'first_name'         => 'name',
            'last_name'          => 'employee_lastname',
            'email1'             => 'employee_email',
            'phone_mobile'       => 'employee_mphone',
            'phone_home'         => 'employee_hphone',
            'address_street'     => 'address1',
            'address_city'       => 'city',
            'address_state'      => 'state',
            'address_postalcode' => 'zip',
            'address_country'    => 'country',
            'title'              => 'employees_title',
            'imagename'          => 'imagename',
            'status'             => 'employee_status',
        ];

        global $adb;

        $sql = "SELECT *
                FROM `vtiger_employees`            
                INNER JOIN `vtiger_crmentity`
                ON `vtiger_crmentity`.`crmid` = `vtiger_employees`.`employeesid`
                WHERE `vtiger_crmentity`.`deleted` = 0
                AND `vtiger_employees`.`userid` = ?";

        $result = $adb->pquery($sql,array($recordId));
        if ($adb->num_rows($result)){
            $dataRecord = $adb->fetchByAssoc($result);
            $employeeId = $dataRecord['employeesid'];

            $UsersModuleInstance = Vtiger_Module::getInstance('Users');
            $UsersModuleFields = Vtiger_Field::getAllForModule($UsersModuleInstance);

            $EmployeesModuleInstance = Vtiger_Module::getInstance('Employees');
            $EmployeesModuleFields = Vtiger_Field::getAllForModule($EmployeesModuleInstance);

            $UsersRecordModel = Vtiger_Record_Model::getInstanceById($recordId,'Users');
            $EmployeesRecordModel = Vtiger_Record_Model::getInstanceById($employeeId,'Employees');
            $EmployeesRecordModel->set('mode','edit');
            foreach($UsersModuleFields as $kUserField => $UserField){
                foreach ($EmployeesModuleFields as $kEmployeesField => $EmployeesField){
                    if ($UserField->name == $EmployeesField->name && $EmployeesField->presence != 1 && $UserField->presence != 1){
                        if ($request->get($EmployeesField->name)){
                            $EmployeesRecordModel->set($EmployeesField->name, $request->get($EmployeesField->name));
                        }
                    }
                }
            }

            foreach ($arrMappings as $UsersField => $EmployeesField){
                $EmployeesRecordModel->set($EmployeesField, $request->get($UsersField));
            }
            $EmployeesRecordModel->save();
        }
    }
}
