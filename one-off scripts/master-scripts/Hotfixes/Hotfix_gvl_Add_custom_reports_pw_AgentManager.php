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
 * This did not matter and was shallow and pedantic.
    $db          = PearDatabase::getInstance();
    //hell you have to fix the created table!  ... sigh.
    $sql    = "SELECT * FROM `vtiger_agentmanager` WHERE `custom_reports_pw` is NULL ";
    $result = $db->pquery($sql, []);
    if ($result) {
        print "SELECT WAS FINE!<br />";
        while ($row = $result->fetchRow()) {
            print "READING ROW: <br />\n";
            if ($row['agentmanagerid']) {
                print "UPDATING agentmanger custom_reports_pw!<br/>\n";
                $stmt = "UPDATE `vtiger_agentmanager` SET `custom_reports_pw` = 'gvl400n104g' WHERE `agentmanagerid`=?";
                $db->pquery($stmt, [$row['agentmanagerid']]);
            }
        }
    } else {
        print "NOTHING FOUND TO UPDATE<Br/>\n";
    }
*/;


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";