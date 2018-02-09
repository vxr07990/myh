<?php
require_once('libraries/nusoap/nusoap.php');
class Estimates_GetRateEstimate_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        //file_put_contents('logs/RateEstimate.log', date("Y-m-d H:i:s")." - "."Entering GetRateEstimate action\n", FILE_APPEND);
        $params = array();

        $params['caller'] = "VnbZ1BjT4xtFyCKj21Xr";
        $params['weight'] = str_replace(",", "", $request->get('weight'));
        $params['pickupDate'] = $request->get('pickupDateTime');
        $params['originZip'] = $request->get('originZip');
        $params['destinationZip'] = $request->get('destinationZip');
        $params['fuelPrice'] = str_replace(",", "", $request->get('fuelPrice'));
        if(!$params['fuelPrice']) {
            $params['fuelPrice'] = 0;
        }
        $params['fullPackApplied'] = $request->get('fullPackApplied');
        $params['fullUnpackApplied'] = $request->get('fullUnpackApplied');
        $params['bottomLineDiscount'] = $request->get('bottomLineDiscount');
        $params['valDeductible'] = MoveCrm\arrayBuilder::getValuationDed($request->get('valDeductible'));
        if(!$params['valDeductible']) {
            $params['valDeductible'] = 'ZERO';
        }
        $params['valuationAmount'] = str_replace(",", "", $request->get('valuationAmount'));

        $effectiveTariff = Vtiger_Record_Model::getInstanceById($request->get('effective_tariff'));

        if(getenv("INSTANCE_NAME") == 'sirva') {
            $params['flatSMF'] = $request->get('flat_smf');
            if(!$params['flatSMF']) {
                $params['flatSMF'] = 0;
            }
            $params['percentSMF'] = $request->get('percent_smf');
            if(!$params['percentSMF']) {
                $params['percentSMF'] = 0;
            }
        }

        $wsdl = $effectiveTariff->get('rating_url');
        if (isset($wsdl) && substr($wsdl, 0, 4) != 'http') {
            $response = new Vtiger_Response();
            $response->setError("Invalid URL provided for rating", "Please contact IGC Support for assistance.");
            $response->emit();
            return;
        }
        file_put_contents('logs/devLog.log', "\n".date('Y-m-d H:i:s - ')."wsdl : ".$wsdl."\n", FILE_APPEND);
        file_put_contents('logs/devLog.log', date('Y-m-d H:i:s - ')."params : ".print_r($params, true)."\n", FILE_APPEND);

        $soapclient = new soapclient2($wsdl, 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();
        //$soapProxy2 = $soapclient->getProxyClassCode();
        //file_put_contents('logs/soap.log', $soapProxy2."\n", FILE_APPEND);
        $soapResult = $soapProxy->RateEstimateSimple($params);

        $info = $soapResult["RateEstimateSimpleResult"];

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }

    protected function getServiceIds($services)
    {
        $db = PearDatabase::getInstance();

        $serviceIds = array();
        foreach ($services as $service=>$rate) {
            $sql = "SELECT serviceid FROM `vtiger_service` WHERE servicename=?";
            $params[] = $service;

            $result = $db->pquery($sql, $params);
            unset($params);

            $row = $result->fetchRow();

            if ($row == null) {
                continue;
            }

            $serviceIds[$service] = $row[0];
        }

        return $serviceIds;
    }
}
