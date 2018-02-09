<?php
require_once('libraries/nusoap/nusoap.php');
class Quotes_GetDetailedRate_Action extends Quotes_QuickEstimate_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        $requestType = $request->get('type');
        $recordId = $request->get('record');
        
        //file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ').print_r($allFields, true)."\n", FILE_APPEND);

        include_once('generatexml.php'); //Generates $xml variable using values contained in $allFields

        //file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ').$xml."\n", FILE_APPEND);

        $wsdlParams['caller'] = 'VnbZ1BjT4xtFyCKj21Xr';
        $wsdlParams['ratingInput'] = base64_encode($xml);
        
        //file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ').$wsdlParams['ratingInput']."\n", FILE_APPEND);

        $wsdlURL = 'https://aws.igcsoftware.com/RatingEngine/RatingService.svc?wsdl';
        
        $soapclient = new soapclient2($wsdlURL, 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();
        $soapResult = $soapProxy->RateEstimate($wsdlParams);
        
        $rateResult = $soapResult['RateEstimateResult']['Totals'];
        
        $info['rateEstimate'] = $rateResult['TotalDiscounted'];
        
        $info['lineitems']['Transportation'] = $rateResult['Trans']['TotalDiscounted'];
        $info['lineitems']['Fuel Surcharge'] = $rateResult['FSDiscounted'];
        $info['lineitems']['Packing'] = $rateResult['Packing']['TotalPackingDiscounted'];
        $info['lineitems']['Unpacking'] = $rateResult['Packing']['TotalUnpackingDiscounted'];
        $info['lineitems']['Valuation'] = $rateResult['ShipmentValuationDiscounted'];
        $info['lineitems']['Origin Accessorials'] = $rateResult['OriginAccessorials']['TotalDiscounted'];
        $info['lineitems']['Origin SIT'] = $rateResult['OriginSIT']['TotalDiscounted'];
        $info['lineitems']['Destination Accessorials'] = $rateResult['DestinationAccessorials']['TotalDiscounted'];
        $info['lineitems']['Destination SIT'] = $rateResult['DestinationSIT']['TotalDiscounted'];
        
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
        $info['mileage'] = $rateResult['Trans']['Miles'];
        
        //file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ')."Before UpdateServices call\n", FILE_APPEND);

        if ($requestType != 'editview') {
            $this->updateServices($recordId, $info['lineitems']);
            
            //file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ')."After UpdateServices call\n", FILE_APPEND);

            $sql = "UPDATE `vtiger_quotes` SET subtotal=? WHERE quoteid=?";
            unset($params);
            $params[] = $info['rateEstimate'];
            $params[] = $recordId;
            
            $result = $db->pquery($sql, $params);
            
            $sql = "UPDATE `vtiger_quotes` SET total=? WHERE quoteid=?";
            $result = $db->pquery($sql, $params);
            
            $sql = "UPDATE `vtiger_quotes` SET pre_tax_total=? WHERE quoteid=?";
            $result = $db->pquery($sql, $params);
            
            $sql = "UPDATE `vtiger_quotes` SET rate_estimate=? WHERE quoteid=?";
            $result = $db->pquery($sql, $params);
            unset($params);
        }
        
        //file_put_contents('logs/SaveLog.log', date('Y-m-d H:i:s - ').print_r($soapResult, true)."\n", FILE_APPEND);

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}
