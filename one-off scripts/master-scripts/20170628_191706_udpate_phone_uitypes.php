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

//$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('include/database/PearDatabase.php');

vtiger_Utils::ExecuteQuery("
    UPDATE `vtiger_field` SET `uitype` = 11 WHERE
    (`tablename` = 'vtiger_vendor'         AND `fieldname` = 'phone') OR
    (`tablename` = 'vtiger_leadscf'        AND `fieldname` = 'destination_phone1') OR
    (`tablename` = 'vtiger_leadscf'        AND `fieldname` = 'origin_phone1') OR
    (`tablename` = 'vtiger_leadscf'        AND `fieldname` = 'origin_phone2') OR
    (`tablename` = 'vtiger_leadscf'        AND `fieldname` = 'destination_phone2') OR
    (`tablename` = 'vtiger_trips'          AND `fieldname` = 'trips_drivercellphone') OR
    (`tablename` = 'vtiger_leadscf'        AND `fieldname` = 'phone_estimate') OR
    (`tablename` = 'vtiger_leadscf'        AND `fieldname` = 'contact_phone') OR
    (`tablename` = 'vtiger_potential'      AND `fieldname` = 'phone_estimate') OR
    (`tablename` = 'vtiger_potential'      AND `fieldname` = 'contact_phone') OR
    (`tablename` = 'vtiger_leadscf'        AND `fieldname` = 'origin_fax') OR
    (`tablename` = 'vtiger_leadscf'        AND `fieldname` = 'destination_fax') OR
    (`tablename` = 'vtiger_potential'      AND `fieldname` = 'origin_fax') OR
    (`tablename` = 'vtiger_potential'      AND `fieldname` = 'destination_fax') OR
    (`tablename` = 'vtiger_intlquote'      AND `fieldname` = 'fax')
");

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
