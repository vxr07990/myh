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

require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

echo '<h2>Sync name of vtiger_tariffpackingitems table with PackingLabels on module</h2>';

/** @var Estimates_Record_Model $estimatesRecordModel */
$estimatesRecordModel = Vtiger_Record_Model::getCleanInstance('Estimates');
$packingLabels = $estimatesRecordModel->getPackingLabels();
$tariffPackingItems = array();

$rs = $adb->pquery("SELECT * FROM vtiger_tariffpackingitems");

if ($adb->num_rows($rs)) {
    while ($row = $adb->fetch_array($rs)) {
        $line_item_id = $row['line_item_id'];
        $packingLabel = isset($packingLabels[$row['pack_item_id']]) ? $packingLabels[$row['pack_item_id']] : $row['name'];
        $adb->pquery("UPDATE vtiger_tariffpackingitems SET name = ? WHERE line_item_id = ?", array($packingLabel, $line_item_id));
    }
}
echo '<h2>SUCCESS</h2>';


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";