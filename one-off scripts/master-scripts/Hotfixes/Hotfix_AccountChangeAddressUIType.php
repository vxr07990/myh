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



/**
 *
 * Entire Object of this script is to change the UIType
 * for the billing address and shipping address
 * of the Accounts to 1 instead of 21
 *
 * in response to OT: 13391 google auto-populater doesn't work on accounts.
 *
 */

$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

print "<br /><h2>START Updating the accounts bill_street and ship_street to uitype = 1 </h2><br />";
$moduleInstance = Vtiger_Module::getInstance('Accounts');

if ($moduleInstance) {
    //change these core fields to uitype 1 so that address autofill will work on them
    $field6 = Vtiger_Field::getInstance('bill_street', $moduleInstance);
    if ($field6) {
        if ($field6->uitype != 1) {
            $query = "UPDATE `vtiger_field` SET uitype = 1 WHERE fieldid = ".$field6->id." LIMIT 1";
            Vtiger_Utils::ExecuteQuery($query);
            print "updateing Accounts bill_street to uitype 1.<br />";
        } else {
            print "Accounts bill_street already updated.<br />";
        }
    }

    $field7 = Vtiger_Field::getInstance('ship_street', $moduleInstance);
    if ($field7) {
        if ($field7->uitype != 1) {
            $query = "UPDATE `vtiger_field` SET uitype = 1 WHERE fieldid = " . $field7->id . " LIMIT 1";
            Vtiger_Utils::ExecuteQuery($query);
            print "updateing Accounts ship_street to uitype 1.<br />";
        } else {
            print "Accounts ship_street already updated.<br />";
        }
    }
}
print "<br /><h2>FINISHED Updating the accounts bill_street and ship_street to uitype = 1 </h2><br />";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";