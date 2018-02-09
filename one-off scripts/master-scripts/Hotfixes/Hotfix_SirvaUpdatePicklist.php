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

if (!$db) {
    $db = PearDatabase::getInstance();
}
$remove_options = [
  'GF Clock Carton - CP',
  'Lamp Crt. - CP',
  'Office Tote Box - CP',
  'Twin Matt. - CP',
];
echo "Started removing the old packing types<br/>";
foreach ($remove_options as $pack_option) {
    $result = $db->pquery("SELECT * FROM vtiger_tariffpackingitems WHERE name LIKE ?", [$pack_option]);
    if ($result) {
        echo "Found Records for ".$pack_option."<br/>";
        $item_info = $result->fetchRow();
        if (count($item_info)>0) {
            echo "Deleting ".$pack_option." data<br/>";
            $db->pquery("DELETE FROM vtiger_packing_items WHERE itemid = ?", [$item_info['pack_item_id']]);
            $db->pquery("DELETE FROM vtiger_tariffpackingitems WHERE pack_item_id = ?", [$item_info['pack_item_id']]);
        }
    } else {
        echo "Cannot find ".$pack_option."<br/>";
    }
}
echo "Finished removing the old packing types<br/>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";