<?php

/**
 * OT4776: Hotfix to change the data type of the "Competitive" field
 * to a checkbox and update existing DB values.
 */

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$moduleInst = Vtiger_Module::getInstance('Opportunities');
if (!$moduleInst) {
    return;
}

$competitiveField = Vtiger_Field::getInstance('is_competitive', $moduleInst);
if (!$competitiveField) {
    return;
}

$db = PearDatabase::getInstance();

$query1 = "UPDATE `vtiger_field` SET `typeofdata` = ? WHERE `fieldid` = ?";
$query2 = "UPDATE `vtiger_field` SET `uitype` = ? WHERE `fieldid` = ?";

// A typeofdata of "C~O" means a checkbox that is not required
$db->pquery($query1, ['C~O', $competitiveField->id]);

// A uitype of 56 is a checkbox
$db->pquery($query2, [56, $competitiveField->id]);

// Now that the data types have been updated, we must update existing records
$updateQuery = "UPDATE `vtiger_potential` SET `is_competitive` = IF(`is_competitive` IN('On', 'on', 'True', 'true' 'Yes', 'yes', 'y', 'Y', 1), 1, 0)";
$db->pquery($updateQuery);

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";