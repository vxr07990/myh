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

//Hotfix_Remove_ClaimsClaimItem_RelatedList.php

$db = PearDatabase::getInstance();

$claimsInstance = Vtiger_Module::getInstance('Claims');
$claimItemsInstance = Vtiger_Module::getInstance('ClaimItems');

$db->pquery("DELETE FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?", array($claimsInstance->getId(), $claimItemsInstance->getId()));


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";