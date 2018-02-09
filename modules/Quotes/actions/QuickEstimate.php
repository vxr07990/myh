<?php
require_once('libraries/nusoap/nusoap.php');
class Quotes_QuickEstimate_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function process(Vtiger_Request $request)
    {
        file_put_contents('logs/devLog.log', "\n In QuickEstimate", FILE_APPEND);
        $recordId = $request->get('record');
        //file_put_contents('logs/QuickEstimate.log', date("Y-m-d H:i:s")." - ".print_r($request, true)."\n", FILE_APPEND);
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT weight, pickup_date, pickup_time, origin_zip, destination_zip, full_pack, full_unpack, bottom_line_discount, valuation_deductible, valuation_amount FROM `vtiger_quotes` JOIN `vtiger_quotescf` ON vtiger_quotes.quoteid=vtiger_quotescf.quoteid WHERE vtiger_quotes.quoteid=?";
        $params[] = $recordId;
        
        $result = $db->pquery($sql, $params);
        unset($params);
        $row = $result->fetchRow();
        
        if ($row == null) {
            return;
        }
        
        $fullPack = ($row[5] == 1 ? "true" : "false");
        $fullUnpack = ($row[6] == 1 ? "true" : "false");
        
        $valDeductible = '';
        file_put_contents('logs/devLog.log', "\n row[8] : ".$row[8], FILE_APPEND);
        if ($row[8] == "60¢ /lb.") {
            $valDeductible = "SIXTY_CENTS";
        } elseif ($row[8] == "Zero") {
            $valDeductible = "ZERO";
        } elseif ($row[8] == "$250") {
            $valDeductible = "TWO_FIFTY";
        } elseif ($row[8] == "$500") {
            $valDeductible = "FIVE_HUNDRED";
        }
        
        $params = array();
        
        $params['caller'] = "VnbZ1BjT4xtFyCKj21Xr";
        $params['weight'] = str_replace(",", "", $row[0]);
        $params['pickupDate'] = $row[1]."T00:00:00";
        $params['originZip'] = $row[3];
        $params['destinationZip'] = $row[4];
        $params['fuelPrice'] = 0;
        $params['fullPackApplied'] = $fullPack;
        $params['fullUnpackApplied'] = $fullUnpack;
        $params['bottomLineDiscount'] = $row[7];
        $params['valDeductible'] = $valDeductible;
        $params['valuationAmount'] = str_replace(",", "", $row[9]);
        
        //$rateEstimateURL = "index.php?module=Quotes&action=GetRateEstimate&record=".$request->get('record')."&weight=".$row[0]."&pickupDateTime=".$row[1]."T00:00:00&originZip=".$row[3]."&destinationZip=".$row[4]."&fuelPrice=0&fullPackApplied=".$fullPack."&fullUnpackApplied=".$fullUnpack."&bottomLineDiscount=".$row[7]."&valDeductible=".$valDeductible."&valuationAmount=".$row[9];

        //file_put_contents('logs/QuickEstimate.log', $rateEstimateURL."\n", FILE_APPEND);

        $wsdl = 'https://aws.igcsoftware.com/RatingEngine/RatingService.svc?wsdl';
        
        $soapclient = new soapclient2($wsdl, 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();
        //$soapProxy2 = $soapclient->getProxyClassCode();
        //file_put_contents('logs/soap.log', $soapProxy2."\n", FILE_APPEND);
        $soapResult = $soapProxy->RateEstimateSimpleFullReturn($params);
        
        $rateResult = $soapResult['RateEstimateSimpleFullReturnResult']['Totals'];
        
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
        
        $this->updateServices($recordId, $info['lineitems']);
        
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
        
        file_put_contents('logs/QuickEstimate.log', print_r($rateResult, true)."\n", FILE_APPEND);
        
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
    
    protected function updateServices($record, $services)
    {
        $db = PearDatabase::getInstance();
        
        foreach ($services as $service=>$rate) {
            $sql = "SELECT serviceid FROM `vtiger_service` WHERE servicename=?";
            $params[] = $service;
            
            $result = $db->pquery($sql, $params);
            unset($params);
            
            $row = $result->fetchRow();
            
            if ($row == null) {
                continue;
            }
            
            $serviceId = $row[0];
            
            $sql = "UPDATE `vtiger_inventoryproductrel` SET listprice=? WHERE id=? AND productid=?";
            $params[] = $rate;
            $params[] = $record;
            $params[] = $serviceId;
            
            $result = $db->pquery($sql, $params);
            unset($params);
        }
    }
}
