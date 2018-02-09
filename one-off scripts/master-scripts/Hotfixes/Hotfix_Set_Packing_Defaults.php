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



require_once 'vtlib/Vtiger/Module.php';

//All colums need to default to 0
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_packing_items` CHANGE `pack_qty` `pack_qty` INT(10) NOT NULL;");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_packing_items` CHANGE `unpack_qty` `unpack_qty` INT(10) NOT NULL;");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_packing_items` CHANGE `ot_pack_qty` `ot_pack_qty` INT(10) NOT NULL;");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_packing_items` CHANGE `ot_unpack_qty` `ot_unpack_qty` INT(10) NOT NULL;");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_packing_items` CHANGE `custom_rate` `custom_rate` INT(10) NOT NULL;");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_packing_items` CHANGE `pack_rate` `pack_rate` INT(10) NOT NULL;");
Vtiger_Utils::ExecuteQuery("ALTER TABLE `vtiger_packing_items` CHANGE `pack_cont_qty` `pack_cont_qty` INT(10) NOT NULL;");


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";