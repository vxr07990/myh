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


include_once('vtlib/Vtiger/Module.php');

//Set up the tab/module
unset($moduleInstance);
$moduleInstance = Vtiger_Module::getInstance('Opportunities');

$field = Vtiger_Field::getInstance('ref_type', $moduleInstance);
if ($field) {
    //Redo the options since random stuff was put in here.
    Vtiger_Utils::ExecuteQuery("TRUNCATE TABLE `vtiger_ref_type`");

    $field->setPicklistValues(['ABL', 'AC', 'ADN', 'BDG', 'BLN', 'CCN', 'CHG', 'CID', 'CLI', 'CLK', 'CO', 'DEC', 'DIV', 'EMP', 'ETA', 'FDO', 'FMO', 'FN', 'FPP', 'GBL', 'GEE', 'ID', 'LMP', 'LOA', 'MHG', 'MO', 'MTN', 'NC', 'OA', 'ORD', 'ORG', 'PN', 'PO', 'RA', 'RAA', 'RDC', 'REQ', 'RGA', 'RIN', 'SBL', 'SC', 'SEC', 'SO', 'SRN', 'SSN', 'SYS', 'TFC', 'VIT']);

}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";