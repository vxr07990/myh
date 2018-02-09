<?php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$name = 'media.retrieve';
$handler_path = 'modules/Media/Media.php';
$handler_method = 'retrieveMediaUrl';
$type = 'POST';
$prelogin = '0';

/*
 * Add the custom handler operation to the webservice.
 *
 * @param $name name of the webservice to be added with namespace.
 * @param $handlerFilePath file to be include which provides the handler method for the given webservice.
 * @param $handlerMethodName name of the function to the called when this webservice is invoked.
 * @param $requestType type of request that this operation should be, if in doubt give it as GET,
 *	general rule of thumb is that, if the operation is adding/updating data on server then it must be POST
 *	otherwise it should be GET.
 * @param $preLogin 0 if the operation need the user to authorised to access the webservice and
 *	1 if the operation is called before login operation hence the there will be no user authorisation happening
 *	for the operation.
 * @return Integer operationId of successful or null upon failure.
 */
if ($opId = vtws_addWebserviceOperation($name, $handler_path, $handler_method, $type, $prelogin)) {
    print "Created the webservice operation. <br />\n";
    vtws_addWebserviceOperationParam($opId, 'element', 'Encoded', 1);
} else {
    print "FAILED to add the webservice operation. <br />\n";
    //Vtiger_Utils doesn't have an overlay for pquery... so just using this other style.
    $stmt = "INSERT IGNORE INTO `vtiger_ws_operation` SET "
            . "`name` = '" . Vtiger_Utils::SQLEscape($name) . "'"
            . ", `handler_path` = '" . Vtiger_Utils::SQLEscape($handler_path) . "'"
            . ", `handler_method` = '" . Vtiger_Utils::SQLEscape($handler_method) . "'"
            . ", `type` = '" . Vtiger_Utils::SQLEscape($type) . "'"
            . ", `prelogin` = '" . Vtiger_Utils::SQLEscape($prelogin) . "'";
    Vtiger_Utils::ExecuteQuery($stmt);
    print "Running:  $stmt;\n";
    print "\n<br />\n";
}
