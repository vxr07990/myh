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


/*
 *
 * The goal is to make the end_date field non-mandatory
 * set parent_contract to display in the summaryfield
 * move parent_contract to position one of the list view.
 *
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$contracts = Vtiger_Module::getInstance('Contracts');

if ($contracts) {
    $db     = PearDatabase::getInstance();
    $stmt   = 'SELECT *FROM `vtiger_relatedlists` WHERE `label` = ? AND `tabid`=? LIMIT 1';
    $result = $db->pquery($stmt, ['Sub-contracts', $contracts->getId()]);
    if ($result) {
        $row  = $result->fetchRow();
        $stmt = 'UPDATE `vtiger_relatedlists` SET `label`=? WHERE `label` = ? AND `tabid`=? LIMIT 1';
        $db->pquery($stmt, ['Sub-Agreements', 'Sub-contracts', $contracts->getId()]);
    } else {
        print "no label: Sub-contracts found<br />\n";
    }
} else {
    print "No Contracts module?<br />";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";