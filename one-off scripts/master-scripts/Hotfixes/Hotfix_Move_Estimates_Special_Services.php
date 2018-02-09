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

$db = PearDatabase::getInstance();

$estimatesModule = Vtiger_Module::getInstance('Estimates');
$currentBlock = Vtiger_Block::getInstance('LBL_QUOTES_ACCESSORIALDETAILS', $estimatesModule);

echo "<p>Reordering fields with a block id of $currentBlock->id</p>\n";
$db->pquery("UPDATE vtiger_field SET `sequence` = ? WHERE `columnname` = ? OR `columnname` = ? AND `block` = ?", Array(50,'accesorial_exclusive_vehicle','accessorial_space_reserve_bool',$currentBlock->id));


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";