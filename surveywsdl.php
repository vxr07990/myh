<?php
include_once 'include/Webservices/Relation.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'includes/main/WebUI.php';
require_once('libraries/nusoap/nusoap.php');

if (!isset($_POST) || empty($_POST)) {
    return;
}

if (!isset($_POST['element']) || empty($_POST['element'])) {
    return;
}

//file_put_contents('logs/surveywsdlasync.log', date("Y-m-d H:i:s")." - POST data is present\n", FILE_APPEND);

$postdata = json_decode($_POST['element'], true);

$wsdl = $postdata['wsdl'];
$params = $postdata['params'];

if (!$wsdl) {
    //@TODO add failure handler
    return;
}
//file_put_contents('logs/surveywsdlasync.log', date("Y-m-d H:i:s")." - ".print_r($params, true)."\n", FILE_APPEND);

try {
    $soapClient = new soapclient2($wsdl, 'wsdl');
    $soapClient->setDefaultRpcParams(true);
    $soapProxy  = $soapClient->getProxy();
    $soapResult = $soapProxy->SurveyUpdateNotification($params);
} catch (Exception $ex) {
    //SOMETHING FAILED.
    //@TODO add failure handler
}

//file_put_contents('logs/surveywsdlasync.log', date("Y-m-d H:i:s")." - After SOAP call\n", FILE_APPEND);
