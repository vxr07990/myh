<?php
//if (function_exists("call_ms_function_ver")) {
//    $version = 1;
//    if (call_ms_function_ver(__FILE__, $version)) {
//        //already ran
//        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
//        return;
//    }
//}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Module.php');

$moduleName = 'Agents';
$moduleInstance = Vtiger_Module::getInstance($moduleName);
if (!$moduleInstance) {
    return;
}
$blockInstance = Vtiger_Block::getInstance('LBL_AGENTS_INFORMATION', $moduleInstance);
if (!$blockInstance) {
    return;
}

// Reorder Fields
$orderOfFields = [
    'agentname',
    'agent_number',
    'agents_status',
    'agentid',
    'agents_custnum',
    'agents_vendornum',
    'agent_type_picklist',
    'agents_grade',
    'agent_address1',
    'agent_address2',
    'agent_city',
    'agent_state',
    'agent_zip',
    'agent_country',
    'agent_phone',
    'agent_fax',
    'agent_email',
    'agents_website',
    'agents_servradius',
    'agent_puc',
    'agents_mc_number',
    'agents_dot_number',
    'agentmanager_id',
    'agent_vanline',
];

$count = 0;
$db = &PearDatabase::getInstance();
foreach ($orderOfFields as $val) {
    $field = Vtiger_Field::getInstance($val, $moduleInstance);
    if ($field) {
        $count++;
        $params = [$count, $field->id];
        $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, $params);
        echo '<p>UPDATED '.$val.' to the sequence</p>';
    } else {
        echo '<p>'.$val.' Field don\'t exists</p>';
    }
}
print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";

