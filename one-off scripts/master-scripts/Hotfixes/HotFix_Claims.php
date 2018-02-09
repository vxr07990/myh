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


//Vtiger_Utils_Log = true;

//require_once 'vendor/autoload.php';
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
//include_once('modules/ModTracker/ModTracker.php');
//include_once('modules/ModComments/ModComments.php');

$db = PearDatabase::getInstance();

$sql = 'SELECT tabid FROM vtiger_tab WHERE name = "Claims"';
$tabid = sprintf('%u', $db->getOne($sql));

//claims
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 1 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_NUMBER'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_ACCOUNT'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_STATUS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_VALUATIONTYPE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_DECLAREDVALUE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_CLAIMTYPE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_FILEDBY'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_DATECREATED'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_DATESUBMITTED'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_DATECLOSED'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_TOTALCLAIM'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2, block = 278 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_AMOUNTPC'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2, block = 278 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_AMOUNTPV'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2, block = 278 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_CHARGEDC'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 1 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_CHARGEDCOMP'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'Assigned To'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2, sequence = 1 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_ORDER'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_TRANSFEREES'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_NO'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_VALUATIONDEDUCTIBLE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_FROMSENT'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_RECEIVED'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_FROMSENT'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_REPRESENTATIVE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_NUMBERDAYSOPEN'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_AMOUNTSBOOKINGAGENTS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMS_AMOUNTSSERVICINGAGENTS'");

$sql = 'SELECT tabid FROM vtiger_tab WHERE name = "ClaimItems"';
$tabid = sprintf('%u', $db->getOne($sql));

//claimitems
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_INVENTORY'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_ITEMSTATUS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_ITEMDESCRIPTION'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_CARRIEREXCEPTION'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_SHIPPEREXCEPTION'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_TAGCOLOR'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_ORIGINALCOST'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_DATEPURCHASED'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_ITEMCLAIMA'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_CLAIMDESCRIPTION'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_CLAIM'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'Assigned To'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_AGENTS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_EMPLOYEES'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_VENDORS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_APVENDOR'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_CHARGECONTRACTORS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_APCLAIMANT'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_CHARGECOMPANY'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIM_ITEMS_TYPE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_EXCEPTIONS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_WEXCEPTIONS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_ITEMWEIGHT'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIM_ITEMS_STATUS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_IADATE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_IDATE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_IIDATE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_RADATE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_RDATE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_RIDATE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_DATECLOSED'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_REASON'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_RVENDOR'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_ARVENDOR'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  presence = 2 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_ABAGENT'");

//blocks claimitems
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET  sequence = 1 WHERE tabid = $tabid AND blocklabel = 'LBL_CLAIMITEMS_TYPE'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET  sequence = 2 WHERE tabid = $tabid AND blocklabel = 'LBL_CLAIMITEMS_DETAIL'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET  sequence = 3 WHERE tabid = $tabid AND blocklabel = 'LBL_CLAIMITEMS_INFORMATION'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET  sequence = 4 WHERE tabid = $tabid AND blocklabel = 'LBL_CLAIMITEMS_STATUS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET  sequence = 5 WHERE tabid = $tabid AND blocklabel = 'LBL_CLAIMITEMS_SERVICEPROVIDER'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET  sequence = 6 WHERE tabid = $tabid AND blocklabel = 'LBL_CLAIMITEMS_PAYMENTS'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_blocks` SET  sequence = 7 WHERE tabid = $tabid AND blocklabel = 'LBL_CLAIMITEMS_RECORDUPDATE'");

Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  block = 279 WHERE tabid = $tabid AND fieldlabel = 'Assigned To'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  block = 280 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_ITEMCLAIMA'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  block = 280 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_CLAIMDESCRIPTION'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  block = 280 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_ORIGINALCOST'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  block = 280 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_DATEPURCHASED'");
Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  block = 281 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_VENDORS'");
//Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET  block = 243 WHERE tabid = $tabid AND fieldlabel = 'LBL_CLAIMITEMS_DATEPURCHASED'");
echo "<h1> Updated Claims & ClaimItems </h1>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";