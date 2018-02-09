<?php

class TariffManager_GetAllowedServices_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            header('Location: index.php?module=Home&view=DashBoard');
        }
        //file_put_contents('logs/AllowedServices.log', date('Y-m-d H:i:s - ')."Entering GetAllowedServices.php\n", FILE_APPEND);
        $db = PearDatabase::getInstance();
        $tariffId = $request->get('tariffid');
        $viewType = $request->get('viewType');
        if ($viewType != 'edit' && empty($tariffId)) {
            $recordId = $request->get('record');
            if (empty($recordId)) {
                $response = new Vtiger_Response();
                $response->setResult(array());
                $response->emit();
                return;
            }
        }

        if (isset($recordId)) {
            $sql = "SELECT business_line_est, effective_tariff FROM `vtiger_quotes`
					JOIN `vtiger_quotescf` ON `vtiger_quotes`.`quoteid`=`vtiger_quotescf`.`quoteid`
					WHERE `vtiger_quotes`.`quoteid`=?";

            $result = $db->pquery($sql, array($recordId));
            $row = $result->fetchRow();
            if ($row == null || ($row[0] != 'Interstate Move' && $row[0] != 'Intrastate Move')) {
                $response = new Vtiger_Response();
                $response->setResult(array());
                $response->emit();
                return;
            }

            $tariffId = $row[1];
        }

        if (empty($tariffId)) {
            file_put_contents('logs/AllowedServices.log', date('Y-m-d H:i:s - ')."Preparing to retrieve clean instance\n", FILE_APPEND);
            $tariffRecord = TariffManager_Record_Model::getCleanInstance('TariffManager');
            file_put_contents('logs/AllowedServices.log', date('Y-m-d H:i:s - ')."Clean instance retrieved\n", FILE_APPEND);
        } else {
            $sql = "SELECT setype FROM `vtiger_crmentity` WHERE crmid=?";
            $result = $db->pquery($sql, [$tariffId]);
            if ($result->fields['setype'] == 'TariffManager') {
                $tariffRecord = TariffManager_Record_Model::getInstanceById($tariffId);
            } else {
                $response = new Vtiger_Response();
                $response->setError("No services retrieved", "The selected tariff does not support this functionality.");
                $response->emit();
                return;
            }
        }
        $services = $tariffRecord->getAllowedServices();
        
        //@TODO: it's supposed to use a rating service wsdl that does this right,
        //@TODO: but 400NG doesn't so remove this exception when it does
        if ($tariffRecord->get('custom_tariff_type') == '400NG' || $tariffRecord->get('custom_tariff_type') == 'GSA-500A') {
            foreach ($services as $locationType => $serviceList) {
                foreach ($serviceList as $serviceName => $isAllowed) {
                    //instead of just finding IRR unload all the IRR
                    //if ($serviceName == 'IRR') {
                    $matches = preg_match('/irr/i', $serviceName);
                    if ($matches) {
                        $services[$locationType][$serviceName] = 0;
                    }
                }
            }
        }

        $response = new Vtiger_Response();
        //add this error check because getAllowedServices may not return services and then we can't process them.
        if ($services) {
            $services = $this->getServicesArray($services, $viewType);
            $response->setResult($services);
        } else {
            $response->setError("Failed to pull services", "Please contact IGC Support for assistance.");
        }

        $response->emit();
    }

    protected function getServicesArray($services, $viewType)
    {
        $fieldMap = array();
        $packEnabled = array();
        $accEnabled = array();
        $serviceEnabled = array();

        if (getenv('INSTANCE_NAME') == 'uvlc') {
            $fieldMap['uvlc_valuation'] = 1;
            $fieldMap['acc_wait_ot_origin_hours'] = 0;
            $fieldMap['acc_wait_ot_dest_hours'] = 0;
        }

        $regexStart = ($viewType == 'edit') ? '^Estimates_editView_fieldName_' : '^Estimates_detailView_(fieldLabel_|fieldValue_)';

        foreach ($services as $locationType=>$serviceList) {
            foreach ($serviceList as $serviceName=>$isAllowed) {
                switch ($serviceName) {
                    case 'Packing':
                        $packEnabled['Pack'] = $isAllowed;
                        $fieldMap[$regexStart.'pack\d+'] = $isAllowed;
                        break;
                    case 'Unpacking - 25%':
                        $packEnabled['Unpack'] = $isAllowed;
                        $fieldMap[$regexStart.'unpack\d+'] = $isAllowed;
                        break;
                    case 'Packing OT':
                        $packEnabled['OT Pack'] = $isAllowed;
                        $fieldMap[$regexStart.'ot_pack\d+'] = $isAllowed;
                        break;
                    case 'Unpacking OT':
                        $packEnabled['OT Unpack'] = $isAllowed;
                        $fieldMap[$regexStart.'ot_unpack\d+'] = $isAllowed;
                        break;
                    case 'Bulky Items':
                        $fieldMap['^bulky_table'] = $isAllowed;
                        break;
                    case 'Crating':
                        $fieldMap['^crating_table'] = $isAllowed;
                        break;
                    case 'Miscellaneous Items':
                        $fieldMap['^flat_charge_table'] = $isAllowed;
                        $fieldMap['^qty_rate_table'] = $isAllowed;
                        break;
                    case 'Extra Labor':
                        $accEnabled['Extra Labor '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'acc_exlabor_'.$locationType.'_hours'] = $isAllowed;
                        $fieldMap[$regexStart.'(apply_)?exlabor_rate_'.$locationType] = $isAllowed;
                        break;
                    case 'Extra Labor OT':
                        $accEnabled['Extra Labor OT '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'acc_exlabor_ot_'.$locationType.'_hours'] = $isAllowed;
                        $fieldMap[$regexStart.'(apply_)?exlabor_ot_rate_'.$locationType] = $isAllowed;
                        break;
                    case 'OT Service':
                        $accEnabled['OT Service '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'acc_ot_'.$locationType] = $isAllowed;
                        break;
                    case 'SIT Pu/Del':
                        $accEnabled['SIT Pu/Del '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'sit_'.$locationType.'_zip'] = $isAllowed;
                        break;
                    case 'SIT Fuel':
                        $accEnabled['SIT Fuel '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'sit_'.$locationType.'_fuel_percent'] = $isAllowed;
                        break;
                    case 'SIT First Day':
                        $accEnabled['SIT First Day '.$locationType] = $isAllowed;
                        break;
                    case 'SIT Additional Days':
                        $accEnabled['SIT Additional Days '.$locationType] = $isAllowed;
                        break;
                    case 'Mini Storage':
                        $accEnabled['Self Stg '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'acc_selfstg_'.$locationType] = $isAllowed;
                        break;
                    case 'Shuttle':
                        $accEnabled['Shuttle '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'acc_shuttle_'.$locationType] = $isAllowed;
                        break;
                    case 'Wait Time':
                        $accEnabled['Wait Time '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'acc_wait_'.$locationType.'_hours'] = $isAllowed;
                        break;
                    case 'Wait Time OT':
                        $accEnabled['Wait Time OT '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'acc_wait_ot_'.$locationType.'_hours'] = $isAllowed;
                        break;
                    case 'Bulky Article Changes':
                        $accEnabled['Bulky Article Changes'] = $isAllowed;
                        $fieldMap['^bulkyArticleRow_\d+'] = $isAllowed;
                        break;
                    case 'Full Pack (per CWT)':
                        $packEnabled['Full Pack'] = $isAllowed;
                        $fieldMap[$regexStart.'full_pack'] = $isAllowed;
                        break;
                    case 'Full Unpack (per CWT)':
                        $packEnabled['Full Unpack'] = $isAllowed;
                        $fieldMap[$regexStart.'full_unpack'] = $isAllowed;
                        break;
                        /*
                    case 'Valuation':
                        $fieldMap['^valuation_table'] = $isAllowed;
                        break;
                        */
                    case 'Vehicle Weights (Standard)':
                        $fieldMap['^standard_vehicles_table'] = $isAllowed;
                        break;
                    //UNITED CN UNIQUE ITEMS
                    case 'SIT (United CN)':
                        $fieldMap['^sit2_table'] = $isAllowed;
                        break;
                    case 'Stairs':
                        $serviceEnabled['Stairs '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'stair_'.$locationType] = $isAllowed;
                        break;
                    case 'Elevators':
                        $serviceEnabled['Elevator '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'elevator_'.$locationType] = $isAllowed;
                        break;
                    case 'Long Carries':
                        $serviceEnabled['Long Carry '.$locationType] = $isAllowed;
                        $fieldMap[$regexStart.'longcarry_'.$locationType] = $isAllowed;
                        break;
                    case 'Rush Shipment Fee':
                        $serviceEnabled['Rush Shipment'] = $isAllowed;
                        $fieldMap[$regexStart.'rush_shipment_fee'] = $isAllowed;
                        break;
                    case 'IRR':
                        $serviceEnabled['IRR'] = $isAllowed;
                        $fieldMap[$regexStart.'irr_charge'] = $isAllowed;
                        break;
                    case 'Overtime Loading':
                        $serviceEnabled['OT Loading'] = $isAllowed;
                        $fieldMap[$regexStart.'accesorial_ot_loading'] = $isAllowed;
                        break;
                    case 'Overtime Unloading':
                        $serviceEnabled['OT Unloading'] = $isAllowed;
                        $fieldMap[$regexStart.'accesorial_ot_unloading'] = $isAllowed;
                        break;
                    case 'Fuel Surcharge':
                        $serviceEnabled['Fuel Surcharge'] = $isAllowed;
                        $fieldMap[$regexStart.'accesorial_fuel_surcharge'] = $isAllowed;
                        break;
                    case 'Extra Stops':
                        $fieldMap['^extra_stops'] = $isAllowed;
                        break;
                    case 'Extra Stops':
                        $fieldMap['^extra_stops'] = $isAllowed;
                        break;
                    case 'Auto Trans (United CN)':
                        $fieldMap['^standard_vehicles_table'] = $isAllowed;
                        break;
                    default:
                        break;
                }
            }
        }

        $fieldMap[$regexStart.'sit_origin_number_days'] = ($accEnabled['SIT First Day origin'] && $accEnabled['SIT Additional Days origin']);
        $fieldMap[$regexStart.'sit_dest_number_days']   = ($accEnabled['SIT First Day dest'] && $accEnabled['SIT Additional Days dest']);
        $fieldMap['^stair_table']                       = ($serviceEnabled['Stairs origin'] || $serviceEnabled['Stairs dest']);
        $fieldMap['^longcarry_table']                   = ($serviceEnabled['Long Carry origin'] || $serviceEnabled['Long Carry dest']);
        $fieldMap['^elevator_table']                    = ($serviceEnabled['Elevator origin'] || $serviceEnabled['Elevator dest']);
        $fieldMap['^full_pack_unpack_row']              = ($packEnabled['Full Pack'] || $packEnabled['Full Unpack']);
        $fieldMap['^pack_table']                        = ($packEnabled['Pack'] || $packEnabled['OT Pack']);
        $fieldMap['^unpack_table']                      = ($packEnabled['Unpack'] || $packEnabled['OT Unpack']);
        $fieldMap['^sit_table']                         = ($accEnabled['SIT First Day origin'] && $accEnabled['SIT Additional Days origin']) ||
                                                          ($accEnabled['SIT First Day dest'] && $accEnabled['SIT Additional Days dest']) ||
                                                           $accEnabled['SIT Pu/Del origin'] || $accEnabled['SIT Pu/Del dest'] ||
                                                           $accEnabled['SIT Fuel origin'] || $accEnabled['SIT Fuel dest'];
        $fieldMap['^shuttleRow_\d+']                    = ($accEnabled['Shuttle origin'] || $accEnabled['Shuttle dest']);
        $fieldMap['^otServiceRow_\d+']                  = ($accEnabled['OT Service origin'] || $accEnabled['OT Service dest']);
        $fieldMap['^selfStgRow_\d+']                    = ($accEnabled['Self Stg origin'] || $accEnabled['Self Stg dest']);
        $fieldMap['^exLaborRow_\d+']                    = ($accEnabled['Extra Labor origin'] || $accEnabled['Extra Labor dest']);
        $fieldMap['^waitRow_\d+']                       = ($accEnabled['Wait Time origin'] || $accEnabled['Wait Time dest']);
        $fieldMap['^acc_table']                         = ($accEnabled['Extra Labor origin'] || $accEnabled['Extra Labor OT origin'] ||
                                                           $accEnabled['Extra Labor dest'] || $accEnabled['Extra Labor OT dest'] ||
                                                           $accEnabled['OT Service origin'] || $accEnabled['OT Service dest'] ||
                                                           $accEnabled['Self Stg origin'] || $accEnabled['Self Stg dest'] ||
                                                           $accEnabled['Shuttle origin'] || $accEnabled['Shuttle dest'] ||
                                                           $accEnabled['Wait Time origin'] || $accEnabled['Wait Time dest'] ||
                                                           $accEnabled['Bulky Article Changes']);
        $fieldMap['^pricingRow_\d+']                    = ($serviceEnabled['Rush Shipment'] || $serviceEnabled['IRR'] ||
                                                           $serviceEnabled['OT Loading'] || $serviceEnabled['OT Unloading'] ||
                                                           $serviceEnabled['Fuel Surcharge']);

        return $fieldMap;
    }
}
