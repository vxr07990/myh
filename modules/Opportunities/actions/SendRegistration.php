<?php
/**
 * @author 			Ryan Paulson, Hacked by Louis Robinson
 * @contact 		lrobinson@igcsoftware.com
 * @copyright		IGC Software
 */

require_once('libraries/nusoap/nusoap.php');
include_once 'include/Webservices/Revise.php';
require_once ('modules/Estimates/actions/QuickEstimate.php');

class Opportunities_SendRegistration_Action extends Vtiger_BasicAjax_Action {

    //JS Handler expects to work with:
    // 'message' => 'GOOOOD',
    // 'registrationNumberField' => 'registration_number',
    // 'registrationNumber' => '01000093',

    public function process(Vtiger_Request $request) {
        $selectedIDs = $request->get('selected_ids');
        //So they can send in a list of ids... I'm not clear on why.
        //we're only working with 1.
        $returnResponse = [];
        foreach ($selectedIDs as $recordId) {
            if (!$recordId) {
                continue;
            }
            list($success, $returnResponse) = $this->sendRegistration($recordId);
            break;
        }

        $response = new Vtiger_Response();
        if ($success) {
            $this->postRegistrationProcess($returnResponse, $recordId);
            unset($returnResponse['registrationResponse']);
            $response->setResult($returnResponse);
        } else {
            $response->setError($returnResponse['code'], vtranslate($returnResponse['message'], $request->get('module')));
        }
        $response->emit();
    }

    protected function postRegistrationProcess($returnResponse, $recordId) {
        $user = Users_Record_Model::getCurrentUserModel();
        $opportunityData['id'] = vtws_getWebserviceEntityId('Opportunities', $recordId);
        $opportunityData[$returnResponse['registrationNumberField']] = $returnResponse['registrationNumber'];
        vtws_revise($opportunityData, $user);

        if (getenv('INSTANCE_NAME') == 'arpin') {
            $wsdlUrl                = getenv('ARPIN_REGISTRATION_SERVICE');
            $documentMethod         = getenv('ARPIN_REGISTRATION_DOCUMENT_METHOD');
            $documentMethodVariable = $documentMethod.'Result';
            $registrationReportName = 'Registration_Report';

            $params                 = [
                'RegNbr' => $returnResponse['registrationNumber']
            ];

            $fieldMap               = [
                'AgentId' => 'Opportunities:getParticipantInfo:Booking Agent,agent_number',
            ];

            foreach ($fieldMap as $apiFieldName => $rule) {
                $ruleSet = explode('|', $rule);
                $result  = $recordId;
                foreach ($ruleSet as $singleRule) {
                    $result = $this->processRule($singleRule, $result);
                }
                $params[$apiFieldName] = $result;
            }

            $response = $this->sendSoapRequest($wsdlUrl, $documentMethod, $params);
            if ($response[$documentMethodVariable]) {
                $docId = $this->saveReport($recordId, $response[$documentMethodVariable], $registrationReportName);
                if (!$docId) {
                    return false;
                }
            }
        }
        return true;
    }

    protected function sendRegistration($recordId) {
        if (!$recordId) {
            return [false, [
                'code' => null,
                'message' => 'LBL_CANNOT_REGISTER_THIS_ID'
            ]];
        }

        if (getenv('ENABLE_REGISTRATION')) {
            if (getenv('INSTANCE_NAME') == 'arpin') {
                $canSend = $this->canArpinSend($recordId);
                if (is_array($canSend)) {
                    return $canSend;
                }

                //@TODO: consider how this is...
                $wsdlUrl = getenv('ARPIN_REGISTRATION_SERVICE');
                $registrationMethod = getenv('ARPIN_REGISTRATION_METHOD');
                $responseVariable = $registrationMethod.'Result';

                list($success, $returnResponse) = $this->sendSoapRegistration($recordId, $wsdlUrl, $registrationMethod, $responseVariable);
                return [$success, $returnResponse];
            }
        }

        return [false, [
            'code' => null,
            'message' => 'LBL_REGISTRATION_NOT_SETUP'
        ]];
    }

    protected function canArpinSend($recordId) {
        try {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
            if ($recordModel->getModuleName() != 'Opportunities') {
                return [false, [
                    'code' => null,
                    'message' => 'Opportunity must be an Interstate COD shipment to register.'
                ]];
                throw new Exception('Opportunity must be an Interstate COD shipment to register.');
            }
        } catch (Exception $ex) {
            return [false, [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            ]];
            throw $ex;
        }
        $registration_number = $recordModel->get('registration_number');
        if ($registration_number) {
            return [false, [
                'code' => null,
                'message' => 'This record already has a registration number.'
            ]];
            throw new Exception('This record already has a registration number.');
        }

        $business_line = $recordModel->get('business_line');
        $billing_type  = $recordModel->get('billing_type');
        if (
            preg_match('/interstate/i', $business_line) &&
            preg_match('/cod/i', $billing_type)
        ) {
            return true;
        }

        return [false, [
            'code' => null,
            'message' => 'Opportunity must be an Interstate COD shipment to register.'
        ]];
        throw new Exception('Opportunity must be an Interstate COD shipment to register.');
    }


    protected function sendSoapRegistration($recordId, $wsdlUrl, $registrationMethod, $responseVariable) {
        $success  = false;
        $response = [
            //'message'                 => 'GOOOOD',
            //'registrationNumberField' => 'registration_number',
            //'registrationNumber'      => $recordId,
            'code' => null,
            'message' => 'LBL_REGISTRATION_FAILED'
        ];

        if (!$wsdlUrl) {
            return [false, [
                'code' => null,
                'message' => 'LBL_REGISTRATION_NO_WSDL'
            ]];
        }

        try {
            $params = $this->gatherRegistrationParams($recordId);
            if ($params) {
                $response = $this->sendSoapRequest($wsdlUrl, $registrationMethod, $params);
                return $this->processSoapResponse($response, $responseVariable);
            }
        } catch (Exception $ex) {
            return [false, [
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            ]];
        }

        return [$success, $response];
    }

    protected function processSoapResponse($response, $responseVariable) {
        $registrationNumberField = 'registration_number';

        if (!$response) {
            throw new Exception('No response returned from the registration service.');
        }

        if (!is_array($response)) {
            throw new Exception('Returned response is not the correct format.');
        }

        if (getenv('INSTANCE_NAME') == 'arpin') {
            if (preg_match('/ERROR/', $response[$responseVariable])) {
                throw new Exception('Registration Service ' . $response[$responseVariable]);
            }

            //@NOTE: if it's a success the return appears to be the registration number.

            return [true, [
                'message'                 => 'Successfully registered: '.$response[$responseVariable],
                'registrationNumberField' => $registrationNumberField,
                'registrationNumber'      => $response[$responseVariable],
                'registrationResponse'    => $response
            ]];
        }

        return [true, [
            'message'                 => print_r($response, true),
            'registrationNumberField' => $registrationNumberField,
            'registrationNumber'      => '',
            'registrationResponse'    => ''
        ]];
    }

    protected function returnRegistrationEstimateType($estType) {
        // B = Binding, N = Non-Binding, A = Not-To-Exceed. Max length 1.
        //{"Non-Binding":"Non-Binding","Binding":"Binding","Not To Exceed":"Not To Exceed"}
        if (getenv('INSTANCE_NAME') == 'arpin') {
            switch ($estType) {
                case 'Binding':
                    return 'B';
                    break;
                case 'Not To Exceed':
                    return 'A';
                    break;
                case 'Non-Binding':
                default:
                    return 'N';
                    break;
            }
        }
    }

    protected function returnRegistrationValuation($valuation) {
        if (getenv('INSTANCE_NAME') == 'arpin') {

            //@NOTE: valuation options from Arpin's registration doc:
            // If $.60/lbl/art, send 60.
            // If deductible = 0, send 0.
            // If deductible = 250, send 250.
            // If deductible = 500, send 500.

            $valuationFlag = call_user_func_array('ValuationUtils::MapValuationDeductible', $valuation);
            switch ($valuationFlag) {
                case 'SIXTY_CENTS':
                    return 60;
                    break;
                case 'ZERO':
                    return 0;
                    break;
                case 'TWO_FIFTY':
                    return 250;
                    break;
                case 'FIVE_HUNDRED':
                    return 500;
                    break;
                case 'SEVEN_FIFTY':
                case 'ONE_THOUSAND':
                default:
                    //default is 60 because it is for most vanlines, I cased 750 and 1000 because they are sometimes used and might as well have it prepared.
                    return 60;
                    break;
            }
        }
        return null;
    }

    protected function gatherRegistrationParams($recordId) {
        $params = [];
        try {
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
        } catch (Exception $ex) {
            //FROGS
        }
        if (!$recordModel) {
            return false;
        }

        $fieldMap = [
            'Contact'              => 'Opportunities:get:assigned_user_id|Users:get:user_name',

            'AgentId'              => 'Opportunities:getParticipantInfo:Booking Agent,agent_number',
            'CallBack'             => 'Opportunities:getParticipantInfo:Booking Agent,agent_phone',
            'SurveryAgentId'       => 'Opportunities:getParticipantInfo:Origin Agent,agent_number',
    //public function getParticipantInfo($agentType, $infoColumn)

            'EmailAddress'         => 'Opportunities:get:contact_id|Contacts:get:email',
            'FName'                => 'Opportunities:get:contact_id|Contacts:get:firstname',
            'LName'                => 'Opportunities:get:contact_id|Contacts:get:lastname',

            'Miles'                => 'Opportunities:getPrimaryEstimateRecordId:false|Estimates:get:interstate_mileage',
            'Weight'               => 'Opportunities:getPrimaryEstimateRecordId:false|Estimates:get:weight',
            'LhDiscPt'             => 'Opportunities:getPrimaryEstimateRecordId:false|Estimates:get:bottom_line_discount',
            'EstType'              => 'Opportunities:getPrimaryEstimateRecordId:false|Estimates:get:estimate_type|this:returnRegistrationEstimateType',
            // B = Binding, N = Non-Binding, A = Not-To-Exceed. Max length 1.
            //{"Non-Binding":"Non-Binding","Binding":"Binding","Not To Exceed":"Not To Exceed"}
            'EstCharges'           => 'Opportunities:getPrimaryEstimateRecordId:false|Estimates:getPricing:invoice_net',
            'EstLineHaulAmt'       => 'Opportunities:getPrimaryEstimateRecordId:false|Estimates:getPricing:invoice_net,Linehaul',
            'BulkyAuto'            => 'Opportunities:getPrimaryEstimateRecordId:false|Estimates:getVehicles:(count~MIN=0~MAX=9)', // int max value 9
            'Valuation'            => 'Opportunities:getPrimaryEstimateRecordId:false|Estimates:get:valuation_deductible|this:returnRegistrationValuation:true',
            //– If $.60/lbl/art, send 60. If deductible = 0, send 0. If deductible = 250, send 250. If deductible = 500, send 500.
            'AdditionalComments'   => 'Opportunities:getPrimaryEstimateRecordId:false|Estimates:get:description', //(300 char max)

            'OriginCity'           => 'Opportunities:get:origin_city',
            'OriginStateCode'      => 'Opportunities:get:origin_state',
            'OriginZip'            => 'Opportunities:get:origin_zip',
            'DestinationCity'      => 'Opportunities:get:destination_city',
            'DestStateCode'        => 'Opportunities:get:destination_state',
            'DestinationZip'       => 'Opportunities:get:destination_zip',

            'PickupPhone'          => 'Opportunities:get:origin_phone1+origin_phone2',
            'DeliveryPhone'        => 'Opportunities:get:destination_phone1+destination_phone2',
            'PickupAddressLine1'   => 'Opportunities:get:origin_address1',
            'PickupAddressLine2'   => 'Opportunities:get:origin_address2',
            'PickupCity'           => 'Opportunities:get:origin_city',
            'PickupStateCode'      => 'Opportunities:get:origin_state',
            'PickupZip'            => 'Opportunities:get:origin_zip',
            'DeliveryAddressLine1' => 'Opportunities:get:destination_address1',
            'DeliveryAddressLine2' => 'Opportunities:get:destination_address2',
            'DeliveryCity'         => 'Opportunities:get:destination_city',
            'DeliveryStateCode'    => 'Opportunities:get:destination_state',
            'DeliveryZip'          => 'Opportunities:get:destination_zip',

            'SalesNbr'             => 'Opportunities:get:assigned_user_id|Users:get:vanline_sales_number',
            'LineHaulMethod'       => 'Opportunities:get:self_haul|static:bool:', //self_haul_opp (send 1 for true)

            //if not date for any:get: then send “1/1/1900”
            'PackDate1'            => 'Opportunities:get:pack_date|static:date',
            'PackDate2'            => 'Opportunities:get:pack_to_date|static:date',
            'PickupDate1'          => 'Opportunities:get:load_date|static:date',
            'PickupDate2'          => 'Opportunities:get:load_to_date|static:date',
            'RDDDate1'             => 'Opportunities:get:deliver_date|static:date',
            'RDDDate2'             => 'Opportunities:get:deliver_to_date|static:date',
            //@NOTE:
            //'RDDDate2' > 'RDDDate1'
            //'RDDDate2' > today

            'AAASouthJersey'       => 'static:int:send=0', //unknown usage. 1 == true 0 == false, nobody knows what this is for.
            'ModelYear'            => 'static:string:send=', //unused send empty string.
            'UnitNbr'              => 'static:int:send=0', //unused send 0.
            'DriverNbr'            => 'static:int:send=0', //unused send 0.
        ];

        foreach ($fieldMap as $apiFieldName => $rule) {
            $ruleSet = explode('|',$rule);
            $result = $recordId;
            foreach ($ruleSet as $singleRule) {
                $result = $this->processRule($singleRule, $result);
            }
            $params[$apiFieldName] = $result;
        }

        return $params;
    }
    protected function processRule($singleRule, $recordIdToUse) {
        $value = null;

        list($moduleName, $handler, $fieldArgs) = explode(':', $singleRule);

        if ($moduleName == 'static') {
            if (preg_match('/send/i', $fieldArgs)) {
                $fieldArgs = preg_replace('/send=/i','',$fieldArgs);
            } else if (!$fieldArgs) {
                $fieldArgs = $recordIdToUse;
            }
            if ($handler == 'bool') {
                //@TODO: as seen here, bool's are different to different people... maybe?
                //Consider a function to make this overrideable.
                if (\MoveCrm\InputUtils::CheckboxToBool($fieldArgs)) {
                    return 1;
                    return 'true';
                    return true;
                } else {
                    return 0;
                    return 'false';
                    return false;
                }
            } else if ($handler == 'date') {
                return $this->handleRegistrationDates($fieldArgs);
            } else {
                //@NOTE: Allow static values with a type if we need that later.
                //return ($handler) $fieldArgs;
                $tempValue = $fieldArgs;
                //@NOTE: IFF this function FAILS it changes the value to 'FAIL'.
                //It is better that if it fails, we simply don't worry about type conversion.
                if (settype($tempValue, $handler)) {
                    $fieldArgs = $tempValue;
                }
            }
            return $fieldArgs;
        } else if ($moduleName == 'this') {
            return $this->returnValue($this, $handler, [$recordIdToUse]);
        }

        if (!$this->recordModels[$recordIdToUse]) {
            try {
                $recordModel = Vtiger_Record_Model::getInstanceById($recordIdToUse, $moduleName);
                if ($recordModel->getModuleName() != $moduleName) {
                    throw new Exception('RecordId: ' . $recordIdToUse . ' is not type: ' . $moduleName);
                }
                $this->recordModels[$recordIdToUse] = $recordModel;
            } catch (Exception $ex) {
                throw $ex;
            }
        }

        if (!method_exists($this->recordModels[$recordIdToUse], $handler)) {
            throw new Exception('Method: ' . $handler . ' does not exist for object: ' . get_class($this->recordModels[$recordIdToUse]));
        }

        if (preg_match('/\+/',$fieldArgs)) {
            //+ denotes multiple option fields, we want the first that has something in it.
            foreach (explode('+', $fieldArgs) as $tryArg) {
                if ($value = $this->returnValue($this->recordModels[$recordIdToUse], $handler, [$tryArg])) {
                    return $value;
                }
            }
            return null;
        } else if (preg_match('/,/',$fieldArgs)) {
            $fieldArgs = preg_replace('/[\'\"]/', '', $fieldArgs);
            $fieldArgs = explode(',', $fieldArgs);
        } else if (!is_array($fieldArgs)) {
            if (preg_match('/^[\(].*[\)]$/', $fieldArgs)) {
                //@TODO: rethink this for control things
                $fieldArgs = preg_replace('/^\(/','',$fieldArgs);
                $fieldArgs = preg_replace('/\)$/','',$fieldArgs);
                $variables = explode('~',$fieldArgs);

                if ($variables[0] == 'count') {
                    $min = false;
                    $max = false;

                    foreach ($variables as $variablePart) {
                        if (preg_match('/MIN/i', $variablePart)) {
                            //$min = preg_replace('/MIN[\=]*/i', '', $variablePart);
                            $min = preg_replace('/[^0-9]/', '', $variablePart);
                        } else if (preg_match('/MAX/i', $variablePart)) {
                            $max = preg_replace('/[^0-9]/', '', $variablePart);
                        }
                    }

                    $count = count($this->returnValue($this->recordModels[$recordIdToUse], $handler, []));

                    if (
                        ($min !== false) &&
                        $min > $count
                    ) {
                        return $min;
                    } else if (
                        ($max !== false) &&
                        $max < $count
                    ) {
                        return $max;
                    }

                    return $count;
                }
            }
            $fieldArgs = [$fieldArgs];
        }

        return $this->returnValue($this->recordModels[$recordIdToUse], $handler, $fieldArgs);
    }

    private function handleRegistrationDates($dateInput) {
        //@NOTE: lies from PHP
        //$format = DateTime::ISO8601;
        $format = 'c'; //ISO8601 format, generally what's used.
        if (getenv('INSTANCE_NAME') == 'arpin') {
            //@NOTE: lies from their document just use the 8601 'c' format.
            //$format = 'm/d/Y';

            if (!$dateInput) {
                $dateInput = '1900-01-01';
            }
        }
        return $this->convertDate($dateInput, $format);
    }

    public function convertDate($sqlDate, $format = 'Y-m-d') {
        if (!$sqlDate) {
            return null;
        }

        //@TODO: possibly need to try/catch here to correct any errors.
        $date = new DateTime($sqlDate);
        return $date->format($format);
    }

    private function returnValue($object, $handler, $arguments) {
        if (!is_array($arguments)) {
            $arguments = [$arguments];
        }
        //$value = $this->recordModels[$recordIdToUse]->$handler($fieldArgs);
        //$value = call_user_func_array([$this->recordModels[$recordIdToUse], $handler], $fieldArgs);
        return call_user_func_array([$object, $handler], $arguments);
    }

    protected function sendSoapRequest($wsdl, $method, $params) {
        if (!$wsdl) {
            throw new Exception('WSDL is required');
        }
        if (!$method) {
            throw new Exception('method is required');
        }
        $soapResult = [];
        try {
            $soapClient = new soapclient2($wsdl, 'wsdl');
            $soapClient->setDefaultRpcParams(true);
            $soapProxy = $soapClient->getProxy();
            if (!method_exists($soapProxy, $method)) {
                throw new Exception('method '.$method.' does not exist.');
            }
            $soapResult = $soapProxy->$method($params);
        } catch (Exception $ex) {
            throw $ex;
        }

        return $soapResult;
    }

    protected function saveReport($recordId, $getReportResult, $reportName = 'Registration_Report') {
        ob_start();
        $filepath = "/tmp";
        $tmp_filename = $filepath.'/temp_'.getmypid().'_'.$reportName.'.srsr.pdf';
        //$written = file_put_contents($tmp_filename, ($GetReportResultDecoded, 'rb'));
        $written = file_put_contents($tmp_filename, base64_decode($getReportResult));
        $docID = Estimates_QuickEstimate_Action::processReportsResponse($recordId, $reportName, base64_encode($tmp_filename));
        $return = ob_get_contents();
        ob_end_clean();

        if (
            !$docID &&
            !preg_match('/^[0-9]$/i',$docID)
        ) {
            return false;
        }

        return $docID;
    }
}
