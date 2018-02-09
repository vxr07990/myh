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

$wsName = 'retrieve_related';
$handlerFilePath ='include/Webservices/RetrieveRelated.php';
$handlerMethodName ='vtws_retrieve_related';
$requestType = 'POST';

$OpParams = [
    'id' => [
        'name' => 'id',
        'type' => 'String',
        'sequence' => 1
    ],
    'relatedType' => [
        'name' => 'relatedType',
        'type' => 'String',
        'sequence' => 2
    ],
    'relatedLabel' => [
        'name' => 'relatedLabel',
        'type' => 'String',
        'sequence' => 3
    ],
];

$operationId = vtws_addWebserviceOperation($wsName, $handlerFilePath, $handlerMethodName, $requestType);
foreach ($OpParams as $paramName => $paramInfo) {
    vtws_addWebserviceOperationParam($operationId, $paramName, $paramInfo['type'], $paramInfo['sequence']);
}

print "\e[32mFinished: " . __FILE__ . "<br />\n\e[0m";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";