<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
vimport('~~/include/Webservices/ConvertLead.php');

class Leads_SaveConvertLead_View extends Vtiger_View_Controller
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName                 = $request->getModule();
        $moduleModel                = Vtiger_Module_Model::getInstance($moduleName);
        $currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if (!$currentUserPrivilegesModel->hasModuleActionPermission($moduleModel->getId(), 'ConvertLead')) {
            throw new AppException(vtranslate('LBL_CONVERT_LEAD_PERMISSION_DENIED', $moduleName));
        }
    }

    public function preProcess(Vtiger_Request $request)
    {
    }

    public function process(Vtiger_Request $request, $doRedirect = true)
    {
        $recordId = $request->get('record');
        $modules  = $request->get('modules');
        foreach ($modules as $key => $item) {
            //file_put_contents('logs/devLog.log', "\n item : ".$item, FILE_APPEND);
            if ($item == 'Potentials') {
                $modules[$key] = 'Opportunities';
            }
        }
        $assignId                                 = $request->get('assigned_user_id');
        $currentUser                              = Users_Record_Model::getCurrentUserModel();
        $entityValues                             = [];
        $entityValues['transferRelatedRecordsTo'] = $request->get('transferModule');
        $entityValues['assignedTo']               = vtws_getWebserviceEntityId(vtws_getOwnerType($assignId), $assignId);
        $entityValues['leadId']                   = vtws_getWebserviceEntityId($request->getModule(), $recordId);
        $recordModel                              = Vtiger_Record_Model::getInstanceById($recordId, $request->getModule());
        $convertLeadFields                        = $recordModel->getConvertLeadFields();
        $availableModules                         = ['Accounts', 'Contacts', 'Opportunities'];
        foreach ($availableModules as $module) {
            if (vtlib_isModuleActive($module) && in_array($module, $modules)) {
                //file_put_contents('logs/devLog.log', "\n in the if", FILE_APPEND);
                $entityValues['entities'][$module]['create'] = true;
                $entityValues['entities'][$module]['name']   = $module;
                foreach ($convertLeadFields[$module] as $fieldModel) {
                    $fieldName  = $fieldModel->getName();
                    $fieldValue = $request->get($fieldName);
                    //Potential Amount Field value converting into DB format
                    if ($fieldModel->getFieldDataType() === 'currency') {
                        $fieldValue = Vtiger_Currency_UIType::convertToDBFormat($fieldValue);
                    } elseif ($fieldModel->getFieldDataType() === 'date') {
                        $fieldValue = DateTimeField::convertToDBFormat($fieldValue);
                    } elseif ($fieldModel->getFieldDataType() === 'reference' && $fieldValue) {
                        $ids = vtws_getIdComponents($fieldValue);
                        if (count($ids) === 1) {
                            $fieldValue = vtws_getWebserviceEntityId(getSalesEntityType($fieldValue), $fieldValue);
                        }
                    }
                    $entityValues['entities'][$module][$fieldName] = $fieldValue;
                }
            }
        }
        //plopping in business line for sirva specific instances
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $moveType = $entityValues['entities']['Opportunities']['move_type'];
            switch ($moveType) {
                case 'Local Canada':
                case 'Local US':
                    $entityValues['entities']['Opportunities']['business_line'] = "Local Move";
                    break;
                case 'Interstate':
                case 'Inter-Provincial':
                case 'Cross Border':
                    $entityValues['entities']['Opportunities']['business_line'] = "Interstate Move";
                    break;
                case 'O&I':
                    $entityValues['entities']['Opportunities']['business_line'] = "Commercial Move";
                    break;
                case 'Intrastate':
                case 'Intra-Provincial':
                    $entityValues['entities']['Opportunities']['business_line'] = "Intrastate Move";
                    break;
                case 'Alaska':
                case 'Hawaii':
                case 'International':
                    $entityValues['entities']['Opportunities']['business_line'] = "International Move";
                    break;
                default:
                    break;
            }
        }
        try {
            //file_put_contents('logs/devLog.log', "\n entityValues : ".print_r($entityValues,true), FILE_APPEND);
            $result = vtws_convertlead($entityValues, $currentUser);
        } catch (Exception $e) {
            $this->showError($request, $e);
            return false;
        }
        if (!empty($result['Accounts'])) {
            $accountIdComponents = vtws_getIdComponents($result['Accounts']);
            $accountId           = $accountIdComponents[1];
        }
        if (!empty($result['Contacts'])) {
            $contactIdComponents = vtws_getIdComponents($result['Contacts']);
            $contactId           = $contactIdComponents[1];
        }
        if (!empty($result['Opportunities'])) {
            $potentialIdComponents = vtws_getIdComponents($result['Opportunities']);
            $potentialId           = $potentialIdComponents[1];
            $vehicleLookup         = Vtiger_Module_Model::getInstance('VehicleLookup');
            if ($vehicleLookup && $vehicleLookup->isActive()) {
                $vehicleLookup::transferVehicles($request->get('record'), $potentialId);
            }

            if (vtlib_isModuleActive('AddressList')) {
                $focus = CRMEntity::getInstance('AddressList');
                $focus->transferAddresses($request->get('record'), $potentialId);
            }
        }
        if (!empty($potentialId)) {
            $res = "index.php?view=Detail&module=Opportunities&record=$potentialId";
        } elseif (!empty($accountId)) {
            $res = "index.php?view=Detail&module=Accounts&record=$accountId";
        } elseif (!empty($contactId)) {
            $res = "index.php?view=Detail&module=Contacts&record=$contactId";
        } else {
            $this->showError($request);
            return false;
        }
        if($doRedirect) {
            header('Location: '.$res);
        }
        return $res;
    }

    public function showError($request, $exception = false)
    {
        //file_put_contents('logs/devLog.log', "\n REALLY DOESN'T MATTER WHAT YOU PUT IN HERE", FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n REQ: ".print_r($request, true), FILE_APPEND);
        //file_put_contents('logs/devLog.log', "\n EXC: ".print_r($exception, true), FILE_APPEND);
        $viewer = $this->getViewer($request);
        if ($exception != false) {
            $viewer->assign('EXCEPTION', $exception->getMessage());
        }
        $moduleName  = $request->getModule();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('CURRENT_USER', $currentUser);
        $viewer->assign('MODULE', $moduleName);
        $viewer->view('ConvertLeadError.tpl', $moduleName);
    }

    public function validateRequest(Vtiger_Request $request)
    {
        $request->validateWriteAccess();
    }
}
