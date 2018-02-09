<?php
require_once('libraries/nusoap/nusoap.php');

use Carbon\Carbon;

class Estimates_GetServiceCharges_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        //Reach out to the rating engine endpoint to retrieve service charges based on zip
        $zip      = $request->get('zip');
        $is_dest  = $request->get('is_dest');
        $tariffId = $request->get('tariffid');
        $effDate  = $request->get('effective_date');
        $dateFormat = $request->get('date_format');

        if(getenv('INSTANCE_NAME') != 'sirva' && getenv('INSTANCE_NAME') != 'graebel') {
            $response = new Vtiger_Response();
            $response->setError("NOT_SUPPORTED", "This service is unavailable on this instance");
            $response->emit();
            return;
        }

        if (!$tariffId) {
            $response = new Vtiger_Response();
            $response->setError("NO_TARIFF", "No tariff selected.");
            $response->emit();
            return;
        }

        $serviceCharges = self::getServiceCharges($zip, $tariffId, $effDate, $dateFormat);
        if(!$serviceCharges['success']) {
            $response = new Vtiger_Response();
            $response->setError($serviceCharges['code'], $serviceCharges['error']);
            $response->emit();
            return;
        }

        $userRecordModel = Users_Record_Model::getCurrentUserModel();
        foreach ($serviceCharges['charges'] as $serviceCharge) {
            if(!is_array($serviceCharge))
            {
                continue;
            }
            if ($isGraebel) {
                $weightInputType = 'text';
                $weightInputTitle = 'Leave blank or as 0 to use full shipment weight';
            } else {
                $weightInputType = ($serviceCharge['MinWeight'] == 0 ? "hidden" : "text");
                $weightInputTitle = '';
            }
            $chargeRow = "<tr class=\"interstateServiceChargeRow\">
                         <input type=\"hidden\" name=\"serviceid\" value=\"" . $serviceCharge['ServiceID'] . "\" />
                         <input type=\"hidden\" name=\"is_dest\" value=\"" . $is_dest . "\" />
                         <input type=\"hidden\" name=\"minimum\" value=\"" . $serviceCharge['MinWeight'] . "\" />
                         <td style=\"text-align:center\">
                         <input type=\"checkbox\" name=\"applied\" />
                         <input type=\"checkbox\" name=\"always_used\" hidden class=\"hide\" />
                         </td>
                         <td style=\"text-align:center\">
                         <input type=\"" . $weightInputType . "\"" . ($weightInputTitle ? (' title="' . $weightInputTitle . '"') : '') . " min='".$serviceCharge['MinWeight']."' name=\"service_weight\" style=\"width:85%\" value=\"\" />
                         </td>
                         <td style=\"text-align:center\">" .
                         ($isGraebel ?
                             "<input type=\"hidden\" name=\"service_description\" style=\"width:85%\" readonly value=\"" .
                             $serviceCharge['Description'] . "\" /> <span>" . $serviceCharge['Description'] . "</span>"
                             : "<input type=\"text\" name=\"service_description\" style=\"width:85%\" readonly value=\"" .
                              $serviceCharge['Description'] . "\" />") .
                         "</td>
                         <td style=\"text-align:center\" class =\"hide\">
                         <div class=\"input-prepend input-prepend-centered\">
                         <span class=\"add-on\">" .
                         $userRecordModel->get('currency_symbol') . "</span>
                         <input type=\"text\" name=\"charge\" style=\"width:75%;float:left;\" readonly value=\"" .
                         $serviceCharge['Charge'] . "\" />
                         </div>
                         </td>
                         </tr>";
            $info .= $chargeRow;
        }

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }

    public static function getServiceCharges($zip, $tariffId, $effDate, $dateFormat = 'yyyy-mm-dd') {
        if(!$zip || !$tariffId || !$effDate) {
            return ['success' => false, 'code' => 'DATA_MISSING', 'error' => 'Missing required data.'];
        }

        $isGraebel = getenv('INSTANCE_NAME') == 'graebel';
        $isSirva = getenv('INSTANCE_NAME') == 'sirva';

        if ($effDate) {
            //Convert to m/d/Y format if needed
            $genericFormat = 'm/d/Y';
            if ($dateFormat == 'yyyy-mm-dd') {
                $effDate = Carbon::createFromFormat('Y-m-d', $effDate)->format($genericFormat);
            } elseif ($dateFormat == 'mm-dd-yyyy') {
                $effDate = Carbon::createFromFormat('m-d-Y', $effDate)->format($genericFormat);
            } elseif ($dateFormat == 'dd-mm-yyyy') {
                $effDate = Carbon::createFromFormat('d-m-Y', $effDate)->format($genericFormat);
            }
        }

        $effectiveTariff = Vtiger_Record_Model::getInstanceById($tariffId);
        $wsdlURL         = $effectiveTariff->get('rating_url');
        // use RatingEngineDev instead of RatingEngine if .env is set
        if (getenv('USE_DEV_RATING_ENGINE')) {
            $wsdlURL = str_replace('/RatingEngine/', '/RatingEngineDev/', $wsdlURL);
        }

        $tariffType      = $effectiveTariff->get('custom_tariff_type');

        $soapclient = new soapclient2($wsdlURL, 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();

        $soapParams = ["zip" => $zip, "effDate" => ($effDate ?: date('m/d/Y'))];

        if ($isSirva) {
            // @TODO: Map SIRVA stuff into MapPricingMode.
            $soapParams['pricingMode'] = 'Interstate';
        } else {
            $soapParams['pricingMode'] = \MoveCrm\ValuationUtils::MapPricingMode($tariffId, 'Interstate Move');
        }

        if ($isGraebel) {
            $soapParams['vanline'] = \MoveCrm\ValuationUtils::GetVanlineID($request->get('owner'));
            $methodName = 'GetServiceChargesVanline';
        } else {
            $methodName = 'GetServiceCharges';
        }

        if (!method_exists($soapProxy, $methodName)) {
            return ['success' => false, 'code' => 'SOAP_ERROR', 'error' => 'SOAP could not find specified method.'];
        }

        $soapResult = $soapProxy->$methodName($soapParams);
        if (!is_array($soapResult[$methodName.'Result']['ServiceChargeRecord'][0])) {
            $soapResult[$methodName.'Result']['ServiceChargeRecord'] = [$soapResult[$methodName.'Result']['ServiceChargeRecord']];
        }

        $info = [];

        foreach ($soapResult[$methodName . 'Result']['ServiceChargeRecord'] as $serviceCharge) {
            $info[] = $serviceCharge;
        }

        return ['success' => true, 'charges' => $info];
    }
}
