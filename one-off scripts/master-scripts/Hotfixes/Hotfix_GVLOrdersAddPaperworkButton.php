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

echo "<br>start orders paperwork button<br>";

$db = PearDatabase::getInstance();

$linkExists = $db->pquery("SELECT linkid FROM `vtiger_links` WHERE linklabel='LBL_ORDERS_GENERATEPAPERWORK'", [])->fetchRow()['linkid'];

if ($linkExists) {
    echo "link exists, no action taken";
} else {
    $moduleInstance = Vtiger_Module::getInstance('Orders');
    $moduleInstance->addLink('DETAILVIEWCUSTOM', 'LBL_ORDERS_GENERATEPAPERWORK', 'javascript:Orders_Detail_Js.generatePaperwork();');
    echo 'link created';
}

echo "<br>end orders paperwork button";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";