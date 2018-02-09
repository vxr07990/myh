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

$moduleOTPK = Vtiger_Module::getInstance('Estimates');
$blockOTPK = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $moduleOTPK);
$field1OTPK = Vtiger_Field::getInstance('overtime_pack', $moduleOTPK);
$field2OTPK = Vtiger_Field::getInstance('overtime_unpack', $moduleOTPK);

if(!$db) {
    $db = PearDatabase::getInstance();
}

$sqlOTPK = "UPDATE `vtiger_field` SET block=? WHERE fieldid IN (?,?)";
$db->pquery($sqlOTPK, [$blockOTPK->id, $field1OTPK->id, $field2OTPK->id]);

print "Completed updating vtiger_field to move overtime_pack and overtime_unpack to LBL_QUOTES_ACCESSORIALDETAILS block<br />\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";