<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/26/2016
 * Time: 11:57 AM
 */


class Estimates_GetMinimumFVPAmount_Action extends Vtiger_BasicAjax_Action
{
    public function process(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();
        $error = 'An unknown error occured.';
        $data = false;

        $url = getenv('VALUATION_LOOKUP_URL');
        if (!$url) {
            $response->setError($error);
            $response->emit();
            return;
        }

        $params = [];
        $params['PricingMode'] = \MoveCrm\ValuationUtils::MapPricingMode($request->get('EffectiveTariff'),
                                                                         $request->get('BusinessLine'));
        $params['VanlineID'] = \MoveCrm\ValuationUtils::GetVanlineID($request->get('Owner'));
        $params['EffectiveDate'] = $request->get('EffectiveDate') ?: date('m-d-Y');
        $params['Weight'] = $request->get('Weight');
        $params['Deductible'] = \MoveCrm\ValuationUtils::MapValuationDeductible($request->get('Deductible'), $request->get('DeductibleSubType'));
        $params['Arpin50Plus'] = false;
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
