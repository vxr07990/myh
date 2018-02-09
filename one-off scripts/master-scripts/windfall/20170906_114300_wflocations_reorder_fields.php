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
$Vtiger_Utils_Log = true;
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

$module = Vtiger_Module_Model::getInstance('WFLocations');


// Reorder Fields
$orderOfFields = ['create_multiple', 'range_from', 'range_to', 'row', 'row_to', 'bay', 'bay_to',
                  'level', 'level_to', 'wfslot_configuration', 'reserved', 'offsite', 'squarefeet',
                  'cubefeet', 'cost', 'double_high', 'container_capacity_on', 'container_capacity', 'agentid', 'assigned_user_id'];


$db = PearDatabase::getInstance();

$count = 0;
foreach ($orderOfFields as $val) {
    $field = Vtiger_Field::getInstance($val, $module);
    if ($field) {
        $count++;
        $params = [$count, $field->id];
        $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, $params);
        echo '<p>UPDATED '.$val.' to the sequence</p>';
    } else {
        echo '<p>'.$val.' Field doesn\'t exists</p>';
    }
}
