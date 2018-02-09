<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1; // Need to add +1 every time you update that script
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
global $adb;

$moduleTariffSections = Vtiger_Module::getInstance('EffectiveDates');

if ($moduleTariffSections) {
    $tabid = getTabid('EffectiveDates');

    $query = $adb->pquery("SELECT * FROM vtiger_modtracker_tabs WHERE vtiger_modtracker_tabs.visible = 1
								   AND vtiger_modtracker_tabs.tabid=?", array($tabid));
    if($rows = $adb->num_rows($query) < 1){
        $adb->pquery("INSERT INTO `vtiger_modtracker_tabs` (`tabid`, `visible`) VALUES (?, ?)",array($tabid,1));

        echo "<br>Add module EffectiveDates on table vtiger_modtracker_tabs SUCCESS<br>";
    };

}
echo "<br>DONE!<br>";





print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";