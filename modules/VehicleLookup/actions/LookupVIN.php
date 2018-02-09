<?php
class VehicleLookup_LookupVIN_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $vin = $request->get('vin');
        $api_key = '3eb9fwzjjgdsvmyeqkdarzga';
        $url = "https://api.edmunds.com/api/vehicle/v2/vins/$vin?fmt=json&api_key=$api_key";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $curlResult = curl_exec($ch);
        curl_close($ch);
        file_put_contents('logs/vinLookup.log', date('Y-m-d H:i:s - '.__LINE__. '-').print_r($curlResult, true)."\n", FILE_APPEND);

        $responseArray = [];
        if ($curlResult) {
            $responseArray = json_decode($curlResult, true);
        }
        // NOT fully adequate.
        //if($responseArray['status'] === 'NOT_FOUND')
        if(!$responseArray || $responseArray['status'])
        {
            $url = "https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVinExtended/$vin?format=json";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $curlResult = curl_exec($ch);
            $curlError = curl_error($ch);
            curl_close($ch);

            file_put_contents('logs/vinLookup.log', date('Y-m-d H:i:s - ').print_r($curlResult, true)."\n", FILE_APPEND);

            if (!$curlResult) {
                $responseArray['status'] = 'BAD_REQUEST';
                //$responseArray['message']  = $curlError;
                $responseArray['message']  = 'Error Looking up VIN';
            } else {
                $responseArray = json_decode($curlResult, true);
                $results       = $responseArray['Results'];
                $data          = [];
                $copyArray     = [
                    'Make',
                    'Manufacturer Name',
                    'Model',
                    'Model Year',
                    'Note',
                    'Body Class',
                    'Gross Vehicle Weight Rating',
                    'Drive Type',
                    'Brake System Type',
                    'Engine Model',
                    'NCSA Body Type',
                    'Wheel Base (inches)',
                    'Cab Type',
                    'Number of Wheels',
                    'Wheel Size Front (inches)',
                    'Wheel Size Rear (inches)',
                    'Axles',
                    'Axle Configuration',
                    'Fuel Type - Primary',
                ];
                foreach ($results as $result) {
                    if (in_array($result['Variable'], $copyArray)) {
                        $data[$result['Variable']] = $result['Value'];
                    }
                }
                $responseArray            = $data;
                $responseArray['isTruck'] = true;
            }
        }

        file_put_contents('logs/vinLookup.log', date('Y-m-d H:i:s - ').print_r($responseArray, true)."\n", FILE_APPEND);

        if (isset($responseArray['status'])) {
            $response = new Vtiger_Response();
            $response->setError($responseArray['status'], $responseArray['message']);
            $response->emit();
        } else {
            $response = new Vtiger_Response();
            $response->setResult($responseArray);
            $response->emit();
        }
    }
}
