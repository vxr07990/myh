<?php
    echo "<br>Beginning of file<br>";
/*	include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';*/
//require_once('libraries/nusoap/nusoap.php');
include_once('customWebserviceFunctions.php');
require_once('NTLMSoapClient.php');
require_once('ExchangeNTLMSoapClient.php');

libxml_disable_entity_loader(false);

//$wsdl = 'https://outlook.office365.com/EWS/Exchange.asmx';
//$wsdl = 'https://blupr07mb692.namprd07.prod.outlook.com:444/EWS/Services.wsdl';
$wsdl = 'Services.wsdl';

/*$curlResult = curlGET('', $wsdl);
echo "<pre>";
print_r($curlResult);
echo "</pre>";*/

echo "<br> Prior to soapClient initialization <br>";
$soapClient = new ExchangeNTLMSoapClient($wsdl);
//$soapClient->setDefaultRpcParams(true);
//$soapClient->setCredentials('rpaulson@igcsoftware.com', 'ZebraTophat198');
//echo "<br>".$soapClient->getHeaders()."<br>";
echo "After setCredentials call<br>";
//$soapProxy = $soapClient->getProxy();

echo "After soapProxy definition<br>";

$FindItem->Traversal = "Shallow";
$FindItem->ItemShape->BaseShape = "AllProperties";
$FindItem->ParentFolderIds->DistinguishedFolderId->Id = "calendar";
$FindItem->ParentFolderIds->DistinguishedFolderId->Mailbox->EmailAddress = "bmcneill@igcsoftware.com";
$FindItem->CalendarView->StartDate = "2015-06-01T00:00:00Z";
$FindItem->CalendarView->EndDate = "2015-06-05T00:00:00Z";

/*echo "<pre>";
print_r($soapClient);
echo "</pre>";

/*echo "<pre>";
print_r($soapClient->response());
echo "</pre>";*/

echo "After FindItem definition<br>";

print_r($FindItem);

//print_r($soapClient->__getFunctions());

$soapResult = $soapClient->FindItem($FindItem);

echo "<pre>";
print_r($soapResult);
echo "</pre>";

/*$calendarItems = $soapResult->ResponseMessages->FindItemResponseMessage->RootFolder->Items->CalendarItem;
foreach($calendarItems as $calendarItem) {
    echo $calendarItem->Subject."<br>";
}*/

/*print_r($soapClient->__getFunctions());

print_r($soapClient->__getTypes());*/;
