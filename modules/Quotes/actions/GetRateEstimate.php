<?php
require_once('libraries/nusoap/nusoap.php');
class Quotes_GetRateEstimate_Action extends Vtiger_BasicAjax_Action
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
        $params['fuelPrice'] = $request->get('fuelPrice');
        $params['fullPackApplied'] = $request->get('fullPackApplied');
        $params['fullUnpackApplied'] = $request->get('fullUnpackApplied');
        $params['bottomLineDiscount'] = $request->get('bottomLineDiscount');
        $params['valDeductible'] = $request->get('valDeductible');
        $params['valuationAmount'] = str_replace(",", "", $request->get('valuationAmount'));
        
        $wsdl = 'https://aws.igcsoftware.com/RatingEngine/RatingService.svc?wsdl';
        
        //file_put_contents('logs/RateEstimate.log', date("Y-m-d H:i:s")." - "."Prior to SOAP call\n", FILE_APPEND);

        $soapclient = new soapclient2($wsdl, 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();
        //$soapProxy2 = $soapclient->getProxyClassCode();
        //file_put_contents('logs/soap.log', $soapProxy2."\n", FILE_APPEND);
        $soapResult = $soapProxy->RateEstimateSimpleFullReturn($params);
        
        $rateResult = $soapResult['RateEstimateSimpleFullReturnResult']['Totals'];
        
        //file_put_contents('logs/ratingsoap.log', date("Y-m-d- H:i:s")." - ".print_r($params, true)."\n", FILE_APPEND);

        //file_put_contents('logs/ratingsoap.log', date("Y-m-d H:i:s")." - ".print_r($soapResult, true)."\n", FILE_APPEND);

        $info['rateEstimate'] = $rateResult['TotalDiscounted'];
        
        $info['lineitems']['Transportation'] = $rateResult['Trans']['TotalDiscounted'];
        $info['lineitems']['Fuel Surcharge'] = $rateResult['FSDiscounted'];
        $info['lineitems']['Packing'] = $rateResult['Packing']['TotalPackingDiscounted'];
        $info['lineitems']['Unpacking'] = $rateResult['Packing']['TotalUnpackingDiscounted'];
        $info['lineitems']['Valuation'] = $rateResult['ValuationTotalDiscounted'];
        $info['lineitems']['Origin Accessorials'] = $rateResult['OriginAccessorials']['TotalDiscounted'];
        $info['lineitems']['Origin SIT'] = $rateResult['OriginSIT']['TotalDiscounted'];
        $info['lineitems']['Destination Accessorials'] = $rateResult['DestinationAccessorials']['TotalDiscounted'];
        $info['lineitems']['Destination SIT'] = $rateResult['DestinationSIT']['TotalDiscounted'];
        $info['mileage'] = $rateResult['Trans']['Miles'];
        
        $itemTotal = 0;
        if (is_array($rateResult['MiscItems'])) {
            if (array_key_exists('ChargeDiscounted', $rateResult['MiscItems']['MiscItemPricing'])) {
                $itemTotal = $itemTotal + $rateResult['MiscItems']['MiscItemPricing']['ChargeDiscounted'];
            } else {
                foreach ($rateResult['MiscItems']['MiscItemPricing'] as $item) {
                    $itemTotal = $itemTotal + $item['ChargeDiscounted'];
                }
            }
        }
        
        $bulkyTotal = 0;
        if (is_array($rateResult['Bulkies'])) {
            if (array_key_exists('TotalDiscounted', $rateResult['Bulkies']['BulkyItem'])) {
                $bulkyTotal = $bulkyTotal + $rateResult['Bulkies']['BulkyItem']['TotalDiscounted'];
            } else {
                foreach ($rateResult['Bulkies']['BulkyItem'] as $item) {
                    $bulkyTotal = $bulkyTotal + $item['TotalDiscounted'];
                }
            }
        }
        
        $info['lineitems']['Bulky Items'] = $bulkyTotal;
        $info['lineitems']['Miscellaneous Services'] = $itemTotal;
        $info['lineitems']['IRR'] = $rateResult['IRRDiscounted'];
        
        $info['lineitemids'] = $this->getServiceIds($info['lineitems']);
        
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
