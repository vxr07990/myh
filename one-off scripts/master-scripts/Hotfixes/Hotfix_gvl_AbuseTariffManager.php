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
 *Goals:
 * none at all.  just remove the custom_javascript if ti's "sirva"
 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

/*
 * It's purpose has been served.
    $db          = PearDatabase::getInstance();
    //hell you have to fix the created table!  ... sigh.
    $sql    = "SELECT * FROM `vtiger_tariffmanager` WHERE `custom_javascript` = ?";
    $result = $db->pquery($sql, ['Estimates_BaseSIRVA_Js']);
    if ($result) {
        print "SELECT WAS FINE!<br />";
        while ($row = $result->fetchRow()) {
            print "READING ROW: <br />\n";
            if ($row['tariffmanagerid']) {
                print "UPDATING TARIFF MANAGER!<br/>\n";
                $stmt = "UPDATE `vtiger_tariffmanager` SET `custom_javascript` = '' WHERE `tariffmanagerid`=?";
                $db->pquery($stmt, [$row['tariffmanagerid']]);
            }
        }
    } else {
        print "NOTHING FOUND TO UPDATE<Br/>\n";
    }
*/;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";