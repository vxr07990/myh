<?php
/**
*
*
*/
require_once('libraries/nusoap/nusoap.php');
class Potentials_ReverseAddressLookup_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {

        $params = array();
        $wsdl = 'http://qlabmobile.sirva.com/IGCWebSync/IGCWebSyncService.asmx?wsdl';

        $params['city'] = $request->get('city');
        $params['state'] = $request->get('state');
        $params['zip'] = '';

        $soapclient = new soapclient2($wsdl, 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();
        $soapResult = $soapProxy->CityStateZipLookup($params);

        $info = array();
        $info['items'];
        $xml = base64_decode($soapResult[CityStateZipLookupResult]);

        $parser = xml_parser_create();
        $values = array();
        $index = array();
        xml_parse_into_struct($parser, $xml, $values, $index);

        $limit = 10000;
        $page = $request->get('page') ?: 0;
        $begin = $limit * $page;
        $values = array_slice($values, $begin, $limit);
        foreach($values as $tagInfo) {
            if ($tagInfo['tag'] == 'ZIP') {
                $info['items'][] = $tagInfo['value'];
            }
        }
        $isCanada = in_array(strtolower($params['state']), ['ontario', 'on', 'quebec city', 'qc', 'nova scotia', 'ns', 'new brunswick', 'nb', 'manitoba', 'mb', 'british columbia', 'bc', 'prince edward island', 'pe', 'saskatchewan', 'sk', 'alberta', 'ab', 'newfoundland and labrador', 'nl']);
        if($isCanada) {
            $info['country'] = "Canada";
        }else {
            $info['country'] = "United States";
        }

        //file_put_contents('logs/devLog.log', date("Y-m-d H:i:s")." - ".print_r($info, true)." \n", FILE_APPEND);

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }
}

/**
  * index.php?module=Potentials&action=ReverseAddressLookup&city=cty&state=stt
  *
  * p
  */
