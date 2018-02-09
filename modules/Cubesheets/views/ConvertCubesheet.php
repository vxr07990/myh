<?php

/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class Cubesheets_ConvertCubesheet_View extends Vtiger_Popup_View
{
    public function process(Vtiger_Request $request)
    {
        $db            = PearDatabase::getInstance();
        $viewer         = $this->getViewer($request);
        $moduleName     = $request->getModule();
        $recordId       = $request->get('record');
        if (!$recordId) {
            return;
        }
        $recordModel    = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        if (!$recordModel) {
            return;
        }

        if($recordModel->get('effective_tariff')) {
            try {
                $isLocal     = Estimates_Record_Model::isLocalTariff($recordModel->get('effective_tariff'));
                $tariffModel = Vtiger_Record_Model::getInstanceById($recordModel->get('effective_tariff'), 'Tariffs');
                if ($tariffModel && method_exists($tariffModel, 'getAllPackingServices')) {
                    $eDate         = $tariffModel->getEffectiveDate($recordModel->get('effective_date'));
                    $packingOption = $tariffModel->getAllPackingServices($eDate);
                }
            } catch(Exception $e)
            {}

        }
        $sourceModule = $request->get('sourceModule');
        if($sourceModule == 'Orders' && $recordModel->get('cubesheets_orderid') != 0) {
            $pullFromOrders = true;
        }

        // So much array manipulation
        foreach($packingOption as $key => $option) {
          foreach($option as $eKey => $val) {
            if(is_int($eKey)) {
              unset($option[$eKey]);
            }
          }
          $packingOption[$option['tariffservicesid']] = $option;
          unset($packingOption[$key]);
        }
        $estFields = Vtiger_Module_Model::getInstance('Estimates')->getFields();
        $accountFields = Vtiger_Module_Model::getInstance('Accounts')->getQuickCreateFields();
        //$localTariffFields = Vtiger_Module_Model::getInstance('Tariffs')->getQuickCreateFields();
        //$tariffSectionFields = Vtiger_Module_Model::getInstance('TariffSections')->getFields();
        //$effectiveDateFields = Vtiger_Module_Model::getInstance('EffectiveDates')->getFields();
        //$tariffServiceFields = Vtiger_Module_Model::getInstance('TariffServices')->getFields();
        //pull related opp & cubesheet info
        if(!$pullFromOrders) {
            $sql = "SELECT * FROM `vtiger_cubesheets`
                    LEFT JOIN `vtiger_potential` ON `vtiger_potential`.potentialid = `vtiger_cubesheets`.potential_id
                    LEFT JOIN `vtiger_potentialscf` ON `vtiger_potential`.potentialid = `vtiger_potentialscf`.potentialid
                    LEFT JOIN `vtiger_crmentity` ON `vtiger_cubesheets`.cubesheetsid = `vtiger_crmentity`.crmid
                    WHERE cubesheetsid = ?";
        } else {
            $sql = "SELECT * FROM `vtiger_cubesheets`
                    LEFT JOIN `vtiger_orders` ON `vtiger_orders`.ordersid = `vtiger_cubesheets`.cubesheets_orderid
                    LEFT JOIN `vtiger_orderscf` ON `vtiger_orders`.ordersid = `vtiger_orderscf`.ordersid
                    LEFT JOIN `vtiger_crmentity` ON `vtiger_cubesheets`.cubesheetsid = `vtiger_crmentity`.crmid
                    WHERE cubesheetsid = ?";
        }
        $result = $db->pquery($sql, [$recordId]);
        $relatedInfo = $result->fetchRow();

        foreach ($accountFields as $key => $accountField) {
            /*if($relatedInfo[$key]){
                //TODO: set fieldvalues from related info
            }*/
            if ($key == 'accountname') {
                $accountField->set('fieldvalue', $relatedInfo['cubesheet_name']);
            }else if($key == 'agentid') {
                //@NOTE: The way CreateEstimate gathers fields, this is redundant and causing issues when converting as a dual brand agent.
                //@NOTE: Converting a cubesheet will still work since this is a duplicated field, in fact, it will work with less problems.
                unset($accountFields[$key]);
            }
        }
        $relatedInfo['quotestage'] = 'Created';
        //array holds opp fieldname as key and equivelent est fieldname as value
        $conversionArray = [
            "business_line_est2" => "business_line2",
            "business_line_est" => "business_line",
            "potential_id" => "potentialid",
            'orders_id' => 'ordersid'
        ];

        //if the agentid is not set, but the potential_id is set
        if (!$relatedInfo['agentid']&&$relatedInfo['potential_id']) {
            //get the row matching the potential
            $result = $db->pquery("SELECT * FROM vtiger_crmentity WHERE crmid = ?", [$relatedInfo['potential_id']]);
            if ($result) {
                $correct_agent_row =  $result->fetchRow();
                $relatedInfo['agentid'] = $correct_agent_row['agentid'];//update to correct agent id
            }
        }
        //STRUCTURE COURTESY OF THE WIZARD
        $allowedEstFields = [
            "assigned_user_id"=>0,
            "agentid"=>0,
            "authority"=>0,
            "subject"=>0,
            "potential_id"=>0,
            "quotestage"=>0,
            "account_id"=>0,
            "business_line_est" => 0,
            "effective_tariff" => 0,
            "cp_schedule" => 0,
            "u_schedule" => 0,
            "potential_id" => 0,
            "shipper_type" => 0,
            "billing_type" => 0,
            "origin_address1" => 0,
            "origin_address2" => 0,
            "destination_address1" => 0,
            "destination_address2" => 0,
            "origin_city" => 0,
            "destination_city" => 0,
            "origin_state" => 0,
            "destination_state" => 0,
            "origin_zip" => 0,
            "destination_zip" => 0,
            "estimates_origin_country" => 0,
            "estimates_destination_country" => 0,
            "origin_phone1" => 0,
            "origin_phone2" => 0,
            "destination_phone1" => 0,
            "destination_phone2" => 0,
            "contact_id" => 0,
            "commodities" => 0,
        ];
        if(getenv('IGC_MOVEHQ')) {
            $allowedEstFields['orders_id'] = 0;
        }

//        if(array_key_exists('business_line_est2', $estFields))
//        {
//            $allowedEstFields['business_line_est2'] = 0;
//            unset($allowedEstFields['business_line_est']);
//        }

        /* $allowedLocalTariffFields = [
            "tariff_name" => 0,
            "assigned_user_id" => 0,
            "tariff_state" => 0,
            "agentid" => 0,
        ];
        $allowedTariffSectionFields = [
            "section_name" => 0,
            "assigned_user_id" => 0,
            "related_tariff" => 0,
            "is_discountable" => 0,
            "agentid" => 0,
        ];
        $allowedEffectiveDateFields = [
            "effective_date" => 0,
            "assigned_user_id" => 0,
            "related_tariff" => 0,
            "agentid" => 0,
        ];
        $allowedTariffServiceFields = [
            "service_name" => 0,
            "assigned_user_id" => 0,
            "tariff_section" => 0,
            "agentid" => 0,
            "rate_type" => 0,
            "applicability" => 0,
        ]; */
        if (getenv('INSTANCE_NAME') == 'sirva') {
            $allowedEstFields['move_type'] = 0;
        }

        //@NOTE: Create the container/packing and unpacking selector for the local tariff.
        //@TODO: why isn't this done instead of that isLocal unset?
        $estFields['cp_schedule'] = new Vtiger_Field_Model();
        $estFields['cp_schedule']->set('name', 'cp_schedule');
        $estFields['cp_schedule']->set('uitype', 16);
        $estFields['cp_schedule']->set('label', 'Container/Packing Rate');
        $estFields['cp_schedule']->set('typeofdata', 'V~O');
        $estFields['u_schedule'] = new Vtiger_Field_Model();
        $estFields['u_schedule']->set('name', 'u_schedule');
        $estFields['u_schedule']->set('uitype', 16);
        $estFields['u_schedule']->set('label', 'Unpacking Rate');
        $estFields['u_schedule']->set('typeofdata', 'V~O');

        $estFields = array_intersect_key($estFields, $allowedEstFields);
        //Frustrating reorder function?
        $neworder = array();
        foreach($estFields as $field => $val) {
          $neworder[$field] = $estFields[$field];
        }
        $estFields = $neworder;
        unset($neworder);
        // Don't show the packing options for non-local moves
        if(!$isLocal) {
          unset($estFields['cp_schedule'],$estFields['u_schedule']);
        }
        //$localTariffFields = array_intersect_key($localTariffFields, $allowedLocalTariffFields);
        //$tariffSectionFields = array_intersect_key($tariffSectionFields, $allowedTariffSectionFields);
        //$effectiveDateFields = array_intersect_key($effectiveDateFields, $allowedEffectiveDateFields);
        //$tariffServiceFields = array_intersect_key($tariffServiceFields, $allowedTariffServiceFields);
        //add custom local_tariff field to est field models array
        foreach ($estFields as $key => $estField) {
            if ($relatedInfo[$key]) {
                $estField->set('fieldvalue', $relatedInfo[$key]);
            } elseif ($relatedInfo[$conversionArray[$key]]) {
                //for things that have different names in opps/est
                $estField->set('fieldvalue', $relatedInfo[$conversionArray[$key]]);
            }
            if ($key == 'account_id') {
                if($relatedInfo['related_to']){
                    $estField->set('fieldvalue', $relatedInfo['related_to']);
                }else if($relatedInfo['orders_account']){
                    $estField->set('fieldvalue', $relatedInfo['orders_account']);
                }
            }
            // if ($key == 'business_line_est') {
            //     unset($estFields[$key]);
            //     //$estFields['business_line_est'] = $estField;
            // }
            else if ($key == 'subject') {
                $estField->set('fieldvalue', $relatedInfo['cubesheet_name']);
            }
            else if ($key == 'estimates_origin_country') {
                $estField->set('fieldvalue', $relatedInfo['origin_country']);
            }
            else if ($key == 'estimates_destination_country') {
                $estField->set('fieldvalue', $relatedInfo['destination_country']);
            }
            else if ($key == 'move_type') {
              $estField->set('uitype',1);
              $estField->set('fieldvalue', vtranslate($estField->get('fieldvalue'), 'Estimates'));
            }
            else if ($pullFromOrders && $key == 'potential_id') {
                if($relatedInfo['orders_opportunities']){
                    $estField->set('fieldvalue', $relatedInfo['orders_opportunities']);
                }
            }
            else if($key == 'effective_tariff'){
                $estField->set('fieldvalue', $relatedInfo['effective_tariff']);
            }
        }

        $conversionFields = [
            'LBL_CUBESHEETS_ESTIMATE' => $estFields,
            'LBL_CUBESHEETS_ACCOUNT' => $accountFields,
        ];
        //$cubesheetsModel    = $recordModel->getModule();
        $userModel = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('MODULE', $moduleName);
        $data = Estimates_Record_Model::getAllowedTariffsForUser();
        $viewer->assign('AVAILABLE_TARIFFS', Vtiger_Util_Helper::toSafeHTML(json_encode($data)));
        //$viewer->assign('BUSINESS_LINE', $estFields['business_line_est']);
        $viewer->assign('USER_MODEL', $userModel);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('CONVERT_CUBESHEET_FIELDS', $conversionFields);
        $viewer->assign('CONVERT_CUBESHEET', true);
        $viewer->assign('PACKING_OPTIONS',Vtiger_Util_Helper::toSafeHTML(json_encode($packingOption)));
        //$viewer->assign('LOCAL_TARIFFS', Estimates_Record_Model::getCurrentUserTariffs(true, Users_Record_Model::getCurrentUserModel()->getAccessibleAgentsForUser()));
        $viewer->view('ConvertCubesheet.tpl', $moduleName);
    }

    /**
     * Function to get the list of Script models to be included
     *
     * @param Vtiger_Request $request
     *
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    /*function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName            = $request->getModule();
        $jsFileNames           = [
        ];
        $jsScriptInstances     = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);

        return $headerScriptInstances;
    }*/
}
