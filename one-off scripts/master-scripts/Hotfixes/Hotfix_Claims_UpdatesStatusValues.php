<?php

if (function_exists("call_ms_function_ver")) {
    $version = 2;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$claimsInstance = Vtiger_Module::getInstance('Claims');
if (!$claimsInstance) {
    echo 'Module Claims not found<br>';
} else {
    $statusBlock = Vtiger_Block::getInstance('LBL_STATUS_INFORMATION', $claimsInstance);

    if ($statusBlock) {
        $field_11 = Vtiger_Field::getInstance('claims_status_statusgrid', $claimsInstance);
        if ($field_11) {
            $sqlquery = "UPDATE vtiger_claims_status_statusgrid SET claims_status_statusgrid='Form Sent' WHERE claims_status_statusgrid='From Sent'";
            Vtiger_Utils::ExecuteQuery($sqlquery);

            $sqlquery = "UPDATE vtiger_picklist_dependency SET sourcevalue='Form Sent', targetvalues='[\"Form Sent\"]' WHERE sourcevalue='From Sent'";
            Vtiger_Utils::ExecuteQuery($sqlquery);
        }

        $fieldReasons = Vtiger_Field::getInstance('claims_reason_statusgrid', $claimsInstance);
        if ($fieldReasons) {
            $sqlquery = "UPDATE vtiger_claims_reason_statusgrid SET claims_reason_statusgrid='In-Shop Repairs in Process'  WHERE claims_reason_statusgrid='InShop Repairs in Process'";
            Vtiger_Utils::ExecuteQuery($sqlquery);
        
            $sqlquery = "UPDATE vtiger_claims_reason_statusgrid SET claims_reason_statusgrid='In-Shop Repairs Completed'  WHERE claims_reason_statusgrid='InShop Repairs Completed'";
            Vtiger_Utils::ExecuteQuery($sqlquery);
        
            $sqlquery = "UPDATE vtiger_claims_reason_statusgrid SET claims_reason_statusgrid='Self-Insured Client'  WHERE claims_reason_statusgrid='SelfInsured Client'";
            Vtiger_Utils::ExecuteQuery($sqlquery);
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";