<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Leads_ConvertLead_View extends Vtiger_Index_View
{
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName                 = $request->getModule();
        $moduleModel                = Vtiger_Module_Model::getInstance($moduleName);
        $currentUserPriviligesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if (!$currentUserPriviligesModel->hasModuleActionPermission($moduleModel->getId(), 'ConvertLead')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
    }

    public function process(Vtiger_Request $request)
    {
        $currentUserPriviligeModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $viewer                    = $this->getViewer($request);
        $recordId                  = $request->get('record');
        $moduleName                = $request->getModule();
        $recordModel               = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $moduleModel               = $recordModel->getModule();
        $convertFields             = $recordModel->getConvertLeadFields();

        $element = [
            'module' => 'Leads',
            'view' => 'SaveConvertLead',
            'record' => $recordId,
            'modules' => ['Contacts','Opportunities'],
            'assigned_user_id' => $recordModel->get('assigned_user_id') ?: $recordModel->get('created_user_id'),
            'transferModule' => 'Contacts',
            'potentialname' => $recordModel->get('firstname').' '.$recordModel->get('lastname'),
        ];
        $request2 = new Vtiger_Request($element,$element);
        foreach($convertFields as $mod => $fieldL)
        {
            foreach($fieldL as $field) {
                if($field->get('fieldvalue'))
                {
                    $request2->set($field->getName(), $field->get('fieldvalue'));
                }
            }
        }
        if(getenv('INSTANCE_NAME') != 'sirva'){
            $saveView = new Leads_SaveConvertLead_View();
            ob_start();
            $redir = $saveView->process($request2, false);
            if($redir)
            {
                ob_end_clean();
                $res = new Vtiger_Response();
                $res->setResult(['redirect' => $redir]);
                $res->emit();
                return;
            }
            ob_end_clean();
        }
        // $orderedFields = array();
        // foreach($convertFields['Opportunities'] as $key => $fieldModel){
        // if($fieldModel->name == 'destination_address1'){
        // $destAddr = $fieldModel;
        // } elseif($fieldModel->name == 'destination_country'){
        // $destCountry = $fieldModel;
        // } elseif($fieldModel->name == 'origin_country'){
        // $orderedFields[] = $fieldModel;
        // $orderedFields[] = $destAddr;
        // $orderedFields[] = $destCountry;
        // } else{
        // $orderedFields[] = $fieldModel;
        // }
        // }
        // $convertFields['Opportunities'] = $orderedFields;
        // file_put_contents('logs/devLog.log', "\n ORDEREDFIELDS: ".print_r($orderedFields, true), FILE_APPEND);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('CURRENT_USER_PRIVILEGE', $currentUserPriviligeModel);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('CONVERT_LEAD_FIELDS', $convertFields);
        $viewer->assign('POTIENTIAL_NAME', $recordModel->get('firstname').' '.$recordModel->get('lastname'));

        if(getenv('INSTANCE_NAME') == 'sirva') {
            setDefaultCoordinator($recordModel, $viewer);
        }

        file_put_contents('logs/myLog.log', "\n recordModel : ".print_r($recordModel, true), FILE_APPEND);

        //file_put_contents('logs/devLog.log', "\n CONVERT LEAD FIELDS: ".print_r($convertFields['Opportunities'], true), FILE_APPEND);
        // $convertFields = $recordModel->getConvertLeadFields();
        // $convertFields = $convertFields['Opportunities'];
        // file_put_contents('logs/devLog.log', "\n CONVERT LEAD: ".print_r($convertFields, true), FILE_APPEND);

        $viewer->assign('COMPANY_NAME', $recordModel->get('company'));
        $potentialModuleModel = Vtiger_Module_Model::getInstance('Potentials');
        $accountField         = Vtiger_Field_Model::getInstance('related_to', $potentialModuleModel);
        $contactField         = Vtiger_Field_Model::getInstance('contact_id', $potentialModuleModel);
        $viewer->assign('ACCOUNT_FIELD_MODEL', $accountField);
        $viewer->assign('CONTACT_FIELD_MODEL', $contactField);
        $contactsModuleModel = Vtiger_Module_Model::getInstance('Contacts');
        $accountField        = Vtiger_Field_Model::getInstance('account_id', $contactsModuleModel);
        $viewer->assign('CONTACT_ACCOUNT_FIELD_MODEL', $accountField);
        $vehicleLookupModel = Vtiger_Module_Model::getInstance('VehicleLookup');
        if ($vehicleLookupModel && $vehicleLookupModel->isActive()) {
            $viewer->assign('VEHICLE_LOOKUP', $vehicleLookupModel->isActive());
//            $viewer->assign('VEHICLE_LIST', $vehicleLookupModel::getVehicles($recordId));
        }

        $assignedToFieldModel = $moduleModel->getField('assigned_user_id');
        if ($assignedToFieldModel){
            $assignedToFieldModel->set('fieldvalue', $recordModel->get('assigned_user_id'));
            $viewer->assign('ASSIGN_TO', $assignedToFieldModel);
        }else{
            $assignedToFieldModel = $potentialModuleModel->getField('assigned_user_id');
            if (!$assignedToFieldModel) {
                $assignedToFieldModel = $contactsModuleModel->getField('assigned_user_id');
            }
            if($assignedToFieldModel) {
                $assignedToFieldModel->set('fieldvalue', $recordModel->get('created_user_id'));
                $viewer->assign('ASSIGN_TO', $assignedToFieldModel);
            }else{
                $viewer->assign('ASSIGN_TO', $recordModel->get('created_user_id'));
            }
        }

        $viewer->view('ConvertLead.tpl', $moduleName);
    }
}
