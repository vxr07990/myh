<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

$moduleName = 'Estimates';
$blockName = 'LBL_QUOTES_INTERSTATEMOVEDETAILS';
$fieldName = 'accesorial_fuel_surcharge';

$module = Vtiger_Module::getInstance($moduleName);

if (!$module) {
    print "Failed: no $moduleName\n";
    return;
}

echo "<br><h1>Starting To add $fieldName in estimates</h1><br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);
if (!$block) {
    print "Failed: no $blockName\n";
    return;
}

$fieldCheck = Vtiger_Field::getInstance($fieldName, $module);

if ($fieldCheck) {
    print "field: " . $fieldCheck->getBlockId() . "\n";
    print "BLOCK: " . $block->id . "\n";
    if ($fieldCheck->getBlockId() != $block->id) {
        $db = &PearDatabase::getInstance();
        $sql = 'UPDATE `vtiger_field` SET `block`=? WHERE `fieldid`=? LIMIT 1';
        print "$sql\n" . print_r([$block->id,$fieldCheck->id], true) . PHP_EOL;
        $db->pquery($sql,[$block->id,$fieldCheck->id]);
    }
} else {
    $fieldCheck              = new Vtiger_Field();
    $fieldCheck->label       = 'LBL_FUEL_SURCHARGE';
    $fieldCheck->name        = 'accesorial_fuel_surcharge';
    $fieldCheck->table       = 'vtiger_quotes';
    $fieldCheck->column      = 'accesorial_fuel_surcharge';
    $fieldCheck->columntype  = 'decimal(10,3)';
    $fieldCheck->uitype      = 9;
    $fieldCheck->typeofdata  = 'V~O';
    $fieldCheck->displaytype = 1;
    $fieldCheck->quickcreate = 0;
    $fieldCheck->presence    = 2;
    $block->addField($fieldCheck);
    echo '<p>Added estimate_type Field</p>'.PHP_EOL;
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";