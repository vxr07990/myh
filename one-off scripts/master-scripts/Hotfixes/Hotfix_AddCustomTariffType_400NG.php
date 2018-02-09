<?php
if (function_exists("call_ms_function_ver")) {
    $version = 5;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$moduleName = 'TariffManager';
$blockName = 'LBL_TARIFFMANAGER_ADMINISTRATIVE';
$module = Vtiger_Module::getInstance($moduleName);
$addedField = false;

$customTariffType = "400NG";

echo "<br>Starting updating custom tariff types picklist<br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    $field0 = Vtiger_Field::getInstance('custom_tariff_type', $module);
    if ($field0) {
        echo '<p>custom_tariff_type field exists</p>';
        $db = PearDatabase::getInstance();
        $sql = "SELECT custom_tariff_typeid FROM `vtiger_custom_tariff_type` WHERE custom_tariff_type=?";
        $result = $db->pquery($sql, [$customTariffType]);
        if($result && $db->num_rows($result) == 0) {
            $result = $db->query("SELECT MAX(sortorderid) AS sortorder FROM `vtiger_custom_tariff_type`");
            $db->pquery("INSERT INTO `vtiger_custom_tariff_type` (custom_tariff_type, sortorderid, presence) VALUES (?,?,?)", [$customTariffType, $result->fields['sortorder'], 1]);
        }
    } else {
        return;
    }
} else {
    echo "<br>Fields not added. $blockName not found.<br/>";
}

echo "<br>Finished<br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
