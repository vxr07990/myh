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



include_once('modules/ModComments/ModComments.php');
include_once('modules/ModTracker/ModTracker.php');


$claimsInstance = Vtiger_Module::getInstance('Claims');
if ($claimsInstance) {
    //remove comments module from claims

    ModComments::removeWidgetFrom('Claims');
    echo 'ModComments removed from Claims<br>';
}
unset($commentsModule);
unset($fieldInstance);

$summaryInstance = Vtiger_Module::getInstance('ClaimsSummary');
if ($summaryInstance) {
    
    //OT17170 - add ModTracker Widget to ClaimsSummary
    ModComments::removeWidgetFrom('ClaimsSummary'); //remove it before adding it because we can't tell if it already exists
    ModComments::addWidgetTo('ClaimsSummary');
    echo 'ModComments added to ClaimsSummary<br>';
    
    
    //OT17171 - add ModTracker Widget to ClaimsSummary
    ModTracker::enableTrackingForModule($summaryInstance->id);
    echo 'ModTracker added to ClaimsSummary<br>';
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";