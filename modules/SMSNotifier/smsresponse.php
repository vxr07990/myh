<?php

include_once dirname(__FILE__) . '/SMSResponseRequest.php';
include_once dirname(__FILE__) . '/SMSResponseHandler.php';

function sms_response_process($user)
{
    $request = new Vtiger_Request($_REQUEST);

    $responseHandler = new SMSResponseHandler;
    return $responseHandler->processResponder($request, $user);
}
