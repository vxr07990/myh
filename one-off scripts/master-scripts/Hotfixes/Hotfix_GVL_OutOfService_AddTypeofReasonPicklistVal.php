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

$moduleInstance = Vtiger_Module::getInstance('OutOfService');
$is_new = false;
if ($moduleInstance) {
    echo "Module OutOfService already present - Updating Field<br>\n";
} else {
    echo "Module OutOfService missing. Exiting.<br>\n";
    return;
}
// Field Setup
$blockName = 'LBL_' . strtoupper($moduleInstance->name) . '_INFORMATION';
$block = Vtiger_Block::getInstance($blockName, $moduleInstance);
if (!$block) {
    echo "Block $blockName not found. Exiting. <br>\n";
    return;
}


$reasonPicklist = [
    'All Reasons',
    'accident report due from driver',
    'Compliance Letter due',
    'Investigation',
    'Safety Needs to talk to driver',
    'Suspension',
    'Expired',
    'Probation',
    'Violated law, penalty not resolved',
    'I/C 30 day notice to cancel',
    'I/C contract cancelled - call safety to clear',
    'TSC 30 day notice to cancel',
    'Paperwork Incomplete',
    'Roadside Inspection - Need proof of repair',
    'Expired',
    'Invalid',
    'Multiple Licenses',
    'Suspended',
    'Training Due',
    'Fuel Report missing',
    'Report Incomplete',
    'Auto liability expired',
    'auto liability incomplete',
    'General Liability expired',
    'General Liability incomplete',
    'non-trucking liability expired',
    'occ/acc insurance expired',
    'physical damage expired',
    'umbrella expired',
    'Worker´s Comp expired',
    'Worker´s Comp incomplete',
    'Cancel 90 days',
    'Cancel over 90 days must reapply',
    'inactive driver (emergency driver only)',
    'Incomplete or inaccurate',
    'missing',
    'still missing some logs',
    'missing - local shuttle driver only',
    'Temporarily inactive - excused',
    'violation letter due to safety',
    'Conviction Pending',
    'Court Date Passed',
    'driver must obtain from state',
    'need notice of moving violation form',
    'driver orientation registration - due',
    'due',
    'due (local non-regulated driver)',
    'blood pressure check due',
    'Cert. Card expired - 90 day blood pressure',
    'Exam failed or incomplete',
    'Expired',
    'Follow-up required',
    'Injury',
    'Medical Restriction',
    '7 day prior logs due',
    'Forms Incomplete',
    'Investigation',
    'Misc.',
    'notice of state violation - unsatisfied',
    'Suspension'
];

foreach($reasonPicklist as $key=>$value){
    $reasonPicklist[$key] = ucfirst($value);
}


$field4 = Vtiger_Field::getInstance('outofservice_typeofreason', $moduleInstance);
if (!$field4) {
    $field4 = new Vtiger_Field();
    $field4->name = 'outofservice_typeofreason';
    $field4->label = 'Type Of Reason';
    $field4->uitype = 16;
    $field4->table = 'vtiger_outofservice';
    $field4->column = $field4->name;
    $field4->columntype = 'VARCHAR(255)';
    $field4->summaryfield = 1;
    $field4->typeofdata = 'V~O';
    $field4->setPicklistValues($reasonPicklist);
    $block->addField($field4);
} else {
    echo '<p>moveroles_role field exists</p>';
    updatePicklistValuesATORPV($field4, $reasonPicklist);
}

function updatePicklistValuesATORPV($field, $pickList)
{
    $fieldName = $field->name;
    $tableName = 'vtiger_'.$fieldName;
    $db = PearDatabase::getInstance();
    $sql = "TRUNCATE TABLE `$tableName`";
    $db->pquery($sql, array());
    $field->setPicklistValues($pickList);
    echo "<p>Updated $fieldName picklist.</p>";
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
