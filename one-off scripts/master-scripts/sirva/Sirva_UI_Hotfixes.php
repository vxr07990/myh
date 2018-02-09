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

$db = PearDatabase::getInstance();

$sql = 'SELECT tabid FROM vtiger_tab WHERE name = "Opportunities"';
$tabid = sprintf('%u', $db->getOne($sql));

// Opportunities

// Hide Detail Disposition
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence = 1 WHERE tabid = $tabid AND fieldname = 'opportunity_detail_disposition'");

// Hide Fax fields
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence = 1 WHERE tabid = $tabid AND fieldname = 'origin_fax'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET presence = 1 WHERE tabid = $tabid AND fieldname = 'destination_fax'");

// Get Lead Details Block
$sql = "SELECT blockid FROM vtiger_blocks WHERE blocklabel = 'LBL_OPPORTUNITIES_BLOCK_LEADDETAILS' AND tabid = $tabid";
$ldBlock = sprintf('%u', $db->getOne($sql));
// Get Opportunity Details Block
$sql = "SELECT blockid FROM vtiger_blocks WHERE blocklabel = 'LBL_POTENTIALS_INFORMATION' AND tabid = $tabid";
$oBlock = sprintf('%u', $db->getOne($sql));
// Get Lead Details Fields
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET block = $oBlock, sequence = sequence+31 WHERE tabid = $tabid AND block = $ldBlock");

echo "<h1> Updated Opportunities</h1>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";