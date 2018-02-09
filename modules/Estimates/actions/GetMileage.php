<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 11/10/2016
 * Time: 1:28 PM
 */


class Estimates_GetMileage_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $error = 'An unknown error occured.';
        $data = false;

        // reusing this url
        $url = getenv('MILEAGE_LOOKUP_URL');
        if (!$url) {
            $response->setError($error);
            $response->emit();
            return;
        }

        $params = [];
        $params['PricingMode'] = \MoveCrm\ValuationUtils::MapPricingMode($request->get('EffectiveTariff'),
                                                                         $request->get('BusinessLine'));
        $params['VanlineID'] = \MoveCrm\ValuationUtils::GetVanlineID($request->get('Owner'));
        $params['EffectiveDate'] = $request->get('EffectiveDate');
        $params['OriginZip'] = $request->get('OriginZip');
        $params['DestinationZip'] = $request->get('DestinationZip');
        $data_string = json_encode($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                           'Content-Type: application/json',
                           'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        // Why??
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);

        if ($data) {
            $response->setResult($data);
        } else {
            $response->setError($error);
        }

        $response->emit();
    }
}
