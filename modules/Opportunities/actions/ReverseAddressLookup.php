<?php
/**
 * @File ReverseAddressLookup.php
 * @Author Louis Robinson
 * @Description This file takes an HTTP Request via the process function with a parameter of type Vtiger_Request.
 *              From the Vtiger_Request object we then pass it to the WebSync app at sirva.com as an associative array
 *              and accept an xml response from sirva. If this file recieves proper city and state data in the 
 *				Vtiger_Request HTTP Object, sirva will then take it and return an XML list of associated zip codes 
 * 			    relative to the city and state data.
 *				
 * @Company IGC Software
 */
require_once('libraries/nusoap/nusoap.php');
class Opportunities_ReverseAddressLookup_Action extends Vtiger_BasicAjax_Action {
	
	function __construct() {
		parent::__construct();
	}

	
	// return data from the zip code data from the WebSync app at Sirva
	function process(Vtiger_Request $request) {
		$params = array();
		$wsdl = getenv('REVERSE_ZIP_URL');

		$params['city'] = $request->get('city');
		$params['state'] = $request->get('state');
		$params['zip'] = '';

		$soapclient = new soapclient2($wsdl, 'wsdl');
		$soapclient->setDefaultRpcParams(true);
		$soapProxy = $soapclient->getProxy();
		//$soapProxy2 = $soapclient->getProxyClassCode();
		//file_put_contents('logs/soap.log', $soapProxy2."\n", FILE_APPEND);
		$soapResult = $soapProxy->CityStateZipLookup($params);

		//file_put_contents('ral.log', date("Y-m-d H:i:s")." - "."Prior to SOAP call\n", FILE_APPEND);
		
		$info = array();
		$info['items'];
		$xml = base64_decode($soapResult[CityStateZipLookupResult]);
		//file_put_contents('ral.log', date("Y-m-d H:i:s")." - ".$xml." \n", FILE_APPEND);


		$parser = xml_parser_create();
		$values = array();
		$index = array();
		xml_parse_into_struct($parser, $xml, &$values, &$index);

		foreach($values as $tagInfo) {
			if($tagInfo['tag'] == 'ZIP') {
				$info['items'][] = $tagInfo['value'];
			} 
		}

		//file_put_contents('ral.log', date("Y-m-d H:i:s")." - ".print_r($info, true)." \n", FILE_APPEND);
		
		// setup a Vtiger_response object, set it to the info we got back from sirva then emit it to jQuery/Javascript
		$response = new Vtiger_Response();
		$response->setResult($info);
		$response->emit();		
	}

}

?>

