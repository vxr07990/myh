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

$customTariffType = [
    "TPG",
    "Allied Express",
    "TPG GRR",
    "ALLV-2A",
    "Pricelock",
    "Blue Express",
    "Pricelock GRR",
    "NAVL-12A",
    "400N Base",
    "400N/104G",
    "Local/Intra",
    "Max 3",
    "Max 4",
    "Intra - 400N",
    "Canada Gov't",
    "Canada Non-Govt",
    "UAS",
    "Base",
    "1950-B",
    "MSI",
    "MMI",
    "400NG",
    "AIReS",
    "RMX400",
    "RMW400",
    "ISRS200-A",
    "09CapRelo",
    "GSA01",
    "GSA-500A"
];

echo "<br>Starting updating custom tariff types picklist<br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    $field0 = Vtiger_Field::getInstance('custom_tariff_type', $module);
    if ($field0) {
        echo '<p>custom_tariff_type field exists</p>';
        $db = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_custom_tariff_type`";
        print "$sql<br />\n";
        $db->pquery($sql, array());
        $field0->setPicklistValues($customTariffType);
        echo "<p>Updated $fieldName picklist.</p>";
    } else {
        return;
    }
} else {
    echo "<br>Fields not added. $blockName not found.<br/>";
}

echo "<br>Finished<br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";