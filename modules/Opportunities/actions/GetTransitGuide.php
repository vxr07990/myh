<?php
/**
 * @author 			Ryan Paulson, Hacked by Louis Robinson
 * @file 			GetDetailedRate.php
 * @description 	Extended functionality from the Quotes module so we can add to
 *                  it without having to deal with changing the core vtiger code
 * @contact 		lrobinson@igcsoftware.com
 * @copyright		IGC Software
 */
/*
VanlineID - The Vanline ID to do the lookup for.  SIRVA has a VanlineID of 18.  For all other instances, you can pass the VanlineID saved in the Vanline Manager record for the Vanline on the Agent Manager record that owns the Estimate

LoadDate - Currently selected Load Date.  Cannot be null, and interface should enforce this be populated first before they can perform a lookup.
OriginZip / DestinationZip - The Origin/Destination zip codes.
OriginExtraStopZips / DestinationExtraStopZips - Array of Extra stop zip codes for Origin/Destination.  Position in array should match it's Sequence number.  Both values can be nil if no Extra Stops are present.

Weight - Weight populated by the user.  If less than the minimum is provided, it will be enforced by the lookup.
ExpressLoading - A true/false field (can be nil) that should be set to true when the Tariff selected is "Allied Express" or "Blue Express".
OriginExpressTruckload - A true/false field (can be nil) that would be set to the value populated from "Express Truckload" Option under Accessorials for tariffs "Allied Express" and "Blue Express".
CrossBorder - A true/false field (can be nil) that should be set to true if the Origin/Destination Zip Codes are "cross border" (one in Canada, one in US).
PricingMode - Possible Options:
INTERSTATE - Default Pricing Mode to use
SIRVA2A12A - Use this for the 2A or 12A tariff options
INTRA400N - Use this for any of the Intrastate tariff options
Canada options: CNCIV, CNCOR, CNGOV - Not applicable just yet, since Canada tariffs not currently supported in QIO 2.0.


//because you might want ot try "complex" format  which like literally does nothing that I expect.
:%s/$wsdlPostArray\['Weight']/$wsdlPostArray['q1:lookup:Weight']
:%s/$wsdlPostArray\['OriginExpressTruckload']/$wsdlPostArray['q1:lookup:OriginExpressTruckload']
:%s/$wsdlPostArray\['ExpressLoading']/$wsdlPostArray['q1:lookup:ExpressLoading']
:%s/$wsdlPostArray\['LoadDate']/$wsdlPostArray['q1:lookup:LoadDate']
:%s/$wsdlPostArray\['CrossBorder']/$wsdlPostArray['q1:lookup:CrossBorder']
:%s/$wsdlPostArray\['LoadDate']/$wsdlPostArray['q1:lookup:LoadDate']
:%s/$wsdlPostArray\['VanlineID']/$wsdlPostArray['q1:lookup:VanlineID']
:%s/$wsdlPostArray\['OriginZip']/$wsdlPostArray['q1:lookup:OriginZip']
:%s/$wsdlPostArray\['DestinationZip']/$wsdlPostArray['q1:lookup:DestinationZip']
:%s/$wsdlPostArray\['OriginExtraStopZips']/$wsdlPostArray['q1:lookup:OriginExtraStopZips']
:%s/$wsdlPostArray\['DestinationExtraStopZips']/$wsdlPostArray['q1:lookup:DestinationExtraStopZips']

$wsdlPostArray['q1:lookup:OriginExtraStopZips']['q2:string']
$wsdlPostArray['q1:lookup:DestinationExtraStopZips']['q1:string']
:%s/\['PricingMode']/['q3:PricingMode']

:%s/'lookup:/'q1:lookup:/g

:55,$s/'q1:/'/g
:55,$s/'q2:/'/g
:55,$s/'q3:/'/g
:55,$s/'lookup:/'/g


*/
require_once('libraries/nusoap/nusoap.php');
class Opportunities_GetTransitGuide_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $estimateID = $request->get('record');

        //origin_zip
        //destination_zip
        //extra_stops_origin
        //extra_stops_destination
        //origin_country
        //destination_country
        //business_line

        $wsdlPostArray = [];

        //set default positions.
        $wsdlPostArray['Weight'] = 1; //set weight to something so the rating engine will apply its default
        $wsdlPostArray['OriginExpressTruckload'] = 'false';
        $wsdlPostArray['ExpressLoading'] = 'false';
        $wsdlPostArray['PricingMode'] = 'INTERSTATE';

        //set the loadDate allowing this to eventually be set on the popup maybe.
        $wsdlPostArray['LoadDate'] = DateTimeField::convertToDBFormat($request->get('load_date'));
        $wsdlPostArray['Weight'] = ceil($request->get('weight'));
        $agentid = $request->get('agentId');
        $origin_country = $request->get('origin_country');
        $destination_country = $request->get('destination_country');
        $business_line = $request->get('business_line_est');
        /*
        try {
            $opportunityRecordModel = Vtiger_Record_Model::getInstanceById($opportunityID, 'Estimates');
        } catch (Exception $ex) {
            //stage 1: currently require an opportunity to exist to pull it's stuff.
            if (
                $request->get('origin_zip') &&
                $request->get('destination_zip') &&
                $request->get('load_date')
            ) {
                //go forward with a blank opportunity
                $opportunityRecordModel = Vtiger_Record_Model::getCleanInstance('Estimates');
            } else {
                //fail because we need at least the origin and destination zip and load_date
                throw new Exception('Opportunity Record not found.');
            }
        }
        */
        //send true to pull the primary record OR the first found record.
        //$estimateRecordModel = $opportunityRecordModel->getPrimaryEstimateRecordModel();
        //$estimateRecordModel = Vtiger_Record_Model::getInstanceById($estimateID, 'Estimates');

        //@TODO: make all the wsdl params some form of table or something it requires some thought and discussion.
        //if ($estimateRecordModel) {
            //$agentid = $estimateRecordModel->get('agentid');
            //set the weight variable.
            //$wsdlPostArray['Weight'] = $estimateRecordModel->get('weight');
            try {
                //I'm unclear why but estimateRecordModel doesn't have the effective_tariff?
                //so I just pulled it above when I got the quoteid.  but leave a way to override.
                if ($request->get('tariff')) {
                    $effectiveTariff = $request->get('tariff');
                }
                $tariffManager = Vtiger_Record_Model::getInstanceById($effectiveTariff, 'TariffManager');
                //set the OriginExpressTruckload and ExpressLoading variables.
                if (preg_match('/express/i', $tariffManager->get('custom_tariff_type'))) {
                    $wsdlPostArray['ExpressLoading'] = 'true';
                    if (preg_match('/truckload/i', $tariffManager->get('custom_tariff_type'))) {
                        $wsdlPostArray['OriginExpressTruckload'] = 'true';
                    }
                } else {
                    //shouldn't require sending a false.  unsent defaults to default.
                    $wsdlPostArray['ExpressLoading'] = 'false';
                }

                //@TODO: see above todo about making this a table or something
                if (
                    preg_match('/Intra/i', $tariffManager->get('custom_tariff_type')) ||
                    ($request->get('business_line_est') == 'Intrastate Move')
                ) {
                    $wsdlPostArray['PricingMode'] = 'INTRA400N';
                } elseif (
                    preg_match('/ALLV-2A/i', $tariffManager->get('custom_tariff_type')) ||
                    preg_match('/NAVL-12A/i', $tariffManager->get('custom_tariff_type'))
                ) {
                    $wsdlPostArray['PricingMode'] = 'SIRVA2A12A';
                } else {
                    $wsdlPostArray['PricingMode'] = 'INTERSTATE';
                }

                //@TODO add Canadian tariff exceptions.
                //$wsdlPostArray['PricingMode'] = 'CNCIV';
                //$wsdlPostArray['PricingMode'] = 'CNCOR';
                //$wsdlPostArray['PricingMode'] = 'CNGOV';

                //set CrossBorder flag.
                $wsdlPostArray['CrossBorder'] = $this->crossBorder($request->get('origin_country'), $request->get('destination_country'));

                //set LoadDate flag, unless it was passed in by the request.
                if (!$wsdlPostArray['LoadDate']) {
                    $wsdlPostArray['LoadDate'] = $request->get('load_date');
                }

                $wsdlURL = $tariffManager->get('rating_url');

                if(getenv('USE_DEV_RATING_ENGINE')) {
                    $wsdlURL = str_replace('RatingEngine','RatingEngineDev',$wsdlURL);
                    $wsdlURL = str_replace('sirva-win-qa','awsdev1',$wsdlURL);
                }
            } catch (Exception $ex) {
                //I'm not sure what this error might mean right now,
                //maybe I will need to handle it.  I'm not sure.
            }

        /*} else {
            if (!$agentid) {
                $agentid = $estimateRecordModel->get('agentid');
            }

            if (!$origin_country) {
                $origin_country = $estimateRecordModel->get('origin_country');
            }

            if (!$destination_country) {
                $destination_country = $estimateRecordModel->get('destination_country');
            }

            if (!$business_line) {
                $business_line = $estimateRecordModel->get('business_line');
            }

            //if us to canada or canada to us.
            $wsdlPostArray['CrossBorder'] = $this->crossBorder($origin_country, $destination_country);

            $wsdlPostArray['PricingMode'] = 'INTERSTATE';
            if ($business_line == 'Intrastate Move') {
                $wsdlPostArray['PricingMode'] = 'INTRA400N';
            }
            //@TODO potentially handle canada business line here?
            //Canada options: CNCIV, CNCOR, CNGOV
            //$wsdlPostArray['PricingMode'] = 'CNCIV';
            //$wsdlPostArray['PricingMode'] = 'CNCOR';
            //$wsdlPostArray['PricingMode'] = 'CNGOV';
        }*/
        //fall back on the opportunity if we don't have the load date by now.
        //if (!$wsdlPostArray['LoadDate']) {
        //	$wsdlPostArray['LoadDate'] = $estimateRecordModel->get('load_date');
        //}

        if (!$wsdlPostArray['LoadDate']) {
            throw new Exception('A load date is required.');
        }

        //pull the vanline ID from the assigned agency's assigned vanline
        if ($agentid) {
            //agentid is either the primary estimates assigned agency or the request's or the opportunity's.
            try {
                //pull the agentmanager
                $agentManager = Vtiger_Record_Model::getInstanceById($agentid, 'AgentManager');

                //agentmanager's vanline_id is the vanlinemanagerid.
                $vanlineManager = Vtiger_Record_Model::getInstanceById($agentManager->get('vanline_id'));

                //this is the vanline_id we want
                $vanlineId = $vanlineManager->get('vanline_id');

                if ($vanlineId == 1 || $vanlineId == 9) {
                    //Sirva is just 18
                    $wsdlPostArray['VanlineID'] = '18';
                }
            } catch (Exception $ex) {
                //invalid or no agency assigned
            }
        }

        if (!$wsdlPostArray['VanlineID']) {
            // we'll have to wing it for the vanline
            if (getenv('INSTANCE_NAME') == 'sirva') {
                // GO GO GO GO GO GO!
                $wsdlPostArray['VanlineID'] = '18';
            } else {
                throw new Exception('Failed to find assigned vanline.');
            }
        }

        //set origin and destination Zip
        $wsdlPostArray['OriginZip'] = $request->get('origin_zip');//$this->getOriginZip($estimateRecordModel, $estimateRecordModel, $request);
        $wsdlPostArray['DestinationZip'] = $request->get('destination_zip');//$this->getDestinatationZip($estimateRecordModel, $estimateRecordModel, $request);

        //set the extra stop zips
        //OriginZip / DestinationZip - The Origin/Destination zip codes.
        //OriginExtraStopZips / DestinationExtraStopZips - Array of Extra stop zip codes for Origin/Destination.  Position in array should match it's Sequence number.  Both values can be nil if no Extra Stops are present.
        $wsdlPostArray['OriginExtraStopZips']['string'] = $request->get('extra_stops_destination_Origin'); //$this->getExtraStopOriginZips($estimateRecordModel, $estimateRecordModel, $request);
        $wsdlPostArray['DestinationExtraStopZips']['string'] = $request->get('extra_stops_destination_Destination'); //$this->getExtraStopDestinationZips($estimateRecordModel, $estimateRecordModel, $request);

        //this is correctish
        $wsdlPost['lookup'] = $wsdlPostArray;

        if (!$wsdlURL) {
            //$wsdlURL = getenv('FALLBACK_TRANSIT_GUIDE_URL');
            throw new Exception('WSDL Url not found, have you set a tariff in your estimate?');
        }

        $soapclient = new soapclient2($wsdlURL, 'wsdl');
        $soapclient->setDefaultRpcParams(true);

        /*
         * screw it... burn this all.  for the life of me can't get it to set
        $soapProxy = $soapclient->getProxy();

        if (!method_exists($soapProxy,'LookupTransitGuide')) {
            $response = new Vtiger_Response();
            $response->setError('Error Processing Request', 'Lookup Transit Guide method not found.');
            $response->emit();
            exit;
        }

        if (method_exists($soapProxy, 'LookupTransitGuide')) {
            $soapResult = $soapProxy->LookupTransitGuide($wsdlPost);
        } else {
            throw new Exception('Error querying rating service, missing method.');
        }

        $soapRequest = '
        <LookupTransitGuide xmlns="igcrating.web">
              <lookup xmlns:d4p1="http://schemas.datacontract.org/2004/07/IGC.Rating.Engine.Model" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                <d4p1:CrossBorder>false</d4p1:CrossBorder>
                <d4p1:DestinationExtraStopZips xmlns:d5p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                </d4p1:DestinationExtraStopZips>
                <d4p1:DestinationZip>90210</d4p1:DestinationZip>
                <d4p1:ExpressLoading>false</d4p1:ExpressLoading>
                <d4p1:LoadDate>2016-05-10</d4p1:LoadDate>
                <d4p1:OriginExpressTruckload>false</d4p1:OriginExpressTruckload>
                <d4p1:OriginExtraStopZips xmlns:d5p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                    <d5p1:string>43068</d5p1:string>
                    <d5p1:string>43221</d5p1:string>
                </d4p1:OriginExtraStopZips>
                <d4p1:OriginZip>43068</d4p1:OriginZip>
                <d4p1:PricingMode>INTERSTATE</d4p1:PricingMode>
                <d4p1:VanlineID>18</d4p1:VanlineID>
                <d4p1:Weight>10000</d4p1:Weight>
            </lookup>
        </LookupTransitGuide>';

        $soapRequest = '
    <LookupTransitGuide xmlns="igcrating.web" xmlns:q1="http://schemas.datacontract.org/2004/07/IGC.Rating.Engine.Model">
      <q1:Lookup xmlns:d4p1="http://schemas.datacontract.org/2004/07/IGC.Rating.Engine.Model" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
        <d4p1:CrossBorder>false</d4p1:CrossBorder>
        <d4p1:DestinationExtraStopZips xmlns:d5p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
          <d5p1:string>90210</d5p1:string>
        </d4p1:DestinationExtraStopZips>
        <d4p1:DestinationZip>90210</d4p1:DestinationZip>
        <d4p1:ExpressLoading>false</d4p1:ExpressLoading>
        <d4p1:LoadDate>2016-04-26T09:57:00</d4p1:LoadDate>
        <d4p1:OriginExpressTruckload>false</d4p1:OriginExpressTruckload>
        <d4p1:OriginExtraStopZips xmlns:d5p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
          <d5p1:string>43068</d5p1:string>
        </d4p1:OriginExtraStopZips>
        <d4p1:OriginZip>43229</d4p1:OriginZip>
        <d4p1:PricingMode>INTERSTATE</d4p1:PricingMode>
        <d4p1:VanlineID>18</d4p1:VanlineID>
        <d4p1:Weight>1000</d4p1:Weight>
      </q1:Lookup>
    </LookupTransitGuide>';


        //$soapclient->setHeaders('<Action SOAP-ENV:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">igcrating.web/IRatingService/LookupTransitGuide</Action>');
        $msg = $soapclient->serializeEnvelope($soapRequest);
        */

        //<wsdl:input wsaw:Action="igcrating.web/RatingService/LookupTransitGuide" message="tns:RatingService_LookupTransitGuide_InputMessage"/>
        //<Action s:mustUnderstand="1" xmlns="http://www.w3.org/2006/05/addressing/wsdl">igcrating.web/IRatingService/LookupTransitGuide</Action>
        //<Action s:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">igcrating.web/IRatingService/LookupTransitGuide</Action>'

//		<s:Header>
//    <Action s:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">igcrating.web/RatingService/LookupTransitGuide</Action>
//  </s:Header>

        /*
        $msg = '<?xml version="1.0" encoding="UTF-8"?>
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
  <s:Body>
    <LookupTransitGuide xmlns="igcrating.web">
      <lookup xmlns:d4p1="http://schemas.datacontract.org/2004/07/IGC.Rating.Engine.Model" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
        <d4p1:CrossBorder>false</d4p1:CrossBorder>
        <d4p1:DestinationExtraStopZips xmlns:d5p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
          <d5p1:string>90210</d5p1:string>
        </d4p1:DestinationExtraStopZips>
        <d4p1:DestinationZip>90210</d4p1:DestinationZip>
        <d4p1:ExpressLoading>false</d4p1:ExpressLoading>
        <d4p1:LoadDate>2016-05-11T15:48:00</d4p1:LoadDate>
        <d4p1:OriginExpressTruckload>false</d4p1:OriginExpressTruckload>
        <d4p1:OriginExtraStopZips xmlns:d5p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
          <d5p1:string>43068</d5p1:string>
        </d4p1:OriginExtraStopZips>
        <d4p1:OriginZip>43229</d4p1:OriginZip>
        <d4p1:PricingMode>INTERSTATE</d4p1:PricingMode>
        <d4p1:VanlineID>18</d4p1:VanlineID>
        <d4p1:Weight>1000</d4p1:Weight>
      </lookup>
    </LookupTransitGuide>
  </s:Body>
</s:Envelope>';

$msg ='<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
    <SOAP-ENV:Body>
        <LookupTransitGuide xmlns="igcrating.web">
              <lookup xmlns:d4p1="http://schemas.datacontract.org/2004/07/IGC.Rating.Engine.Model">
                <d4p1:CrossBorder>false</d4p1:CrossBorder>
                <d4p1:DestinationExtraStopZips xmlns:d5p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                  <d5p1:string>90210</d5p1:string>
                </d4p1:DestinationExtraStopZips>
                <d4p1:DestinationZip>90210</d4p1:DestinationZip>
                <d4p1:ExpressLoading>false</d4p1:ExpressLoading>
                <d4p1:LoadDate>2016-04-27</d4p1:LoadDate>
                <d4p1:OriginExpressTruckload>false</d4p1:OriginExpressTruckload>
                <d4p1:OriginExtraStopZips xmlns:d5p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                  <d5p1:string>43044</d5p1:string>
                  <d5p1:string>43067</d5p1:string>
                </d4p1:OriginExtraStopZips>
                <d4p1:OriginZip>43068</d4p1:OriginZip>
                <d4p1:PricingMode>INTERSTATE</d4p1:PricingMode>
                <d4p1:VanlineID>18</d4p1:VanlineID>
                <d4p1:Weight>1000</d4p1:Weight>
            </lookup>
        </LookupTransitGuide>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
        */

        $opData = $soapclient->getOperationData('LookupTransitGuide');
        $endPoint = $opData['endpoint'];
        $soapAction = $opData['soapAction'];

        //@TODO: this... this... is disappointing.  I'm calling it on making nusoap work as expected,
        //it has the schema and complex type information, but it won't match them in the sent xml and it needs that
        //so I'm just using this function to build one from the example.
        $soapRequest = $this->buildSoapRequest($wsdlPostArray);

        $msg = $soapclient->serializeEnvelope($soapRequest);
        $soapResult = $soapclient->send($msg, $soapAction);

        if ($soapResult['faultcode']) {
            $errMessage = $soapResult['faultstring']['!'];
            //throw new Exception('Error querying rating service. ' . $errMessage);
            throw new Exception($errMessage);
        }

        $info = [];

        //put the results into array for handling.
        $lookupResult = [];

        if (!isset($soapResult['LookupTransitGuideResult']['TransitGuideResult']['0'])) {
            $lookupResult[] = $soapResult['LookupTransitGuideResult']['TransitGuideResult'];
        } else {
            $lookupResult = $soapResult['LookupTransitGuideResult']['TransitGuideResult'];
        }

        foreach ($lookupResult as $index => $dates) {
            $desc = $dates['TypeDescription'];
            if ($desc) {
                $info[strtolower($desc)] = [
                    'load_date'       => explode('T', $dates['LoadFrom'])[0],
                    'load_to_date'    => explode('T', $dates['LoadTo'])[0],
                    'deliver_date'    => explode('T', $dates['DeliverFrom'])[0],
                    'deliver_to_date' => explode('T', $dates['DeliverTo'])[0]
                ];
            }
        }

        if (!isset($info['standard']) && !isset($info['optional'])) {
            throw new Exception('Error querying service.');
        }

        if ($request->get('edit')) {
            $temp = [];
            foreach ($info as $label => $dataArray) {
                foreach ($dataArray as $type => $date) {
                    $temp[$label][$type] = DateTimeField::convertToUserFormat($date);
                }
            }
            $info = $temp;
        }

        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }

    private function crossBorder($country1, $country2)
    {
        $rv = 'false';
        if (
            ($country1 == 'United States' && $country2 == 'Canada') ||
            ($country1 == 'Canada' && $country2 == 'United States')
        ) {
            $rv = 'true';
        }
        return $rv;
    }

    private function getOriginZip($estimateRecordModel, $estimateRecordModel, $request)
    {
        return $this->getZip($estimateRecordModel, $estimateRecordModel, $request, 'origin');
    }

    private function getDestinatationZip($estimateRecordModel, $estimateRecordModel, $request)
    {
        return $this->getZip($estimateRecordModel, $estimateRecordModel, $request, 'destination');
    }

    /*
     * function getZip
     *
     * param mixed $estRecord => Estimate_Record_Model OR empty
     * param Opportunity_Record_Model $oppRecord
     * param string $mode => set as 'origin' | 'desintation'  default: 'origin'
     *
     * returns string zip  is the zipcode/postal code if found in estimate record or opp record or nothing.
     */
    private function getZip($estRecord, $oppRecord, $request, $mode = 'origin')
    {
        $zip = '';
        $key = 'origin_zip';

        if ($mode == 'destination') {
            $key = 'destination_zip';
        } elseif ($mode == 'origin') {
            $key = 'origin_zip';
        }

        //use input value first.
        if ($request->get($key)) {
            $zip = $request->get($key);
        }

        //check if there's an estimateRecord and the get method exists.
        if (!$zip && $estRecord && method_exists($estRecord, 'get')) {
            //@TODO: add extra input checking
            //getModule() returns the module name of the record, could use that to make sure it's Estimates.
            $zip = $estRecord->get($key);
        }

        if (!$zip && $oppRecord && method_exists($oppRecord, 'get')) {
            $zip = $oppRecord->get($key);
        }

        return $zip;
    }

    private function getExtraStopOriginZips($estimateRecordModel, $estimateRecordModel, $request)
    {
        return $this->getExtraStopZips($estimateRecordModel, $estimateRecordModel, $request, 'Origin');
    }

    private function getExtraStopDestinationZips($estimateRecordModel, $estimateRecordModel, $request)
    {
        return $this->getExtraStopZips($estimateRecordModel, $estimateRecordModel, $request, 'Destination');
    }

    private function getExtraStopZips($estimateRecordModel, $estimateRecordModel, $request, $mode = 'Origin')
    {
        $extraStopZips = [];
        if ($request->get('extra_stops_' . strtolower($mode))) {
            $tempArray = explode(',', $request->get('extra_stops_' . strtolower($mode)));
            foreach ($tempArray as $testZip) {
                if ($testZip) {
                    $extraStopZips[] = $testZip;
                }
            }
        }

        /*
        if (!$extraStopZips && $estimateRecordModel) {
            $extraStopZips = $this->getExtraStops($estimateRecordModel, $mode);
        }
        */

        if (!$extraStopZips) {
            $extraStopZips = $this->getExtraStops($estimateRecordModel, $mode);
        }

        return $extraStopZips;
    }

    //@TODO this will need updated based on talking to dirk since he rebuilt extra stops as module
    //ExtraStops extrastops extra stops Extra Stops
    private function getExtraStops($relatedRecordModel, $mode)
    {
        $extraStopZips = [];
        $rows = [];

        //because there is only opportunity extra stops
        //logic to include extra stops
        $extraStopsModel = Vtiger_Module_Model::getInstance('ExtraStops');
        if ($extraStopsModel && $extraStopsModel->isActive()) {
            $rows = $extraStopsModel->getStops($relatedRecordModel->getId(), ['type' => strtolower($mode), 'order' => true]);
        }

        foreach ($rows as $index => $extraStopData) {
            $extraStopZips[] = $extraStopData['extrastops_zip'];
        }
        return $extraStopZips;
    }

    //give in and hack this like a crazy person.
    private function buildSoapRequest($postArray)
    {
        $soapRequest = '<LookupTransitGuide xmlns="igcrating.web">
      		<lookup xmlns:d4p1="http://schemas.datacontract.org/2004/07/IGC.Rating.Engine.Model">
                <d4p1:CrossBorder>' . $postArray['CrossBorder'] . '</d4p1:CrossBorder>
                <d4p1:DestinationExtraStopZips xmlns:d5p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">';
        if (is_array($postArray['DestinationExtraStopZips']['string'])) {
            foreach ($postArray['DestinationExtraStopZips']['string'] as $index => $value) {
                $soapRequest .= '
                    <d5p1:string>'.$value.'</d5p1:string>';
            }
        }
        $soapRequest .= '
                </d4p1:DestinationExtraStopZips>
                <d4p1:DestinationZip>' . $postArray['DestinationZip'] . '</d4p1:DestinationZip>
                <d4p1:ExpressLoading>' . $postArray['ExpressLoading'] . '</d4p1:ExpressLoading>
                <d4p1:LoadDate>' . $postArray['LoadDate'] . '</d4p1:LoadDate>
                <d4p1:OriginExpressTruckload>' . $postArray['OriginExpressTruckload'] . '</d4p1:OriginExpressTruckload>
                <d4p1:OriginExtraStopZips xmlns:d5p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">';
        if (is_array($postArray['OriginExtraStopZips']['string'])) {
            foreach ($postArray['OriginExtraStopZips']['string'] as $index => $value) {
                $soapRequest .= '
                    <d5p1:string>'.$value.'</d5p1:string>';
            }
        }
        $soapRequest .= '
                </d4p1:OriginExtraStopZips>
                <d4p1:OriginZip>' . $postArray['OriginZip'] . '</d4p1:OriginZip>
                <d4p1:PricingMode>' . $postArray['PricingMode'] . '</d4p1:PricingMode>
                <d4p1:VanlineID>' . $postArray['VanlineID'] . '</d4p1:VanlineID>
                <d4p1:Weight>' . $postArray['Weight'] . '</d4p1:Weight>
            </lookup>
        </LookupTransitGuide>';

        return $soapRequest;
    }
}
