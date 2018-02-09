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




$module1 = Vtiger_Module::getInstance('Employees'); // The module1 your blocks and fields will be in.
$block10 = Vtiger_Block::getInstance('LBL_DRIVER_INFORMATION', $module1);
if ($block10) {
    echo "<h3>The LBL_DRIVER_INFORMATION block already exists</h3><br>";
} else {
    $block10 = new Vtiger_Block();
    $block10->label = 'LBL_DRIVER_INFORMATION';
    $module1->addBlock($block10);
}

echo "<ul>";
$field78 = Vtiger_Field::getInstance('driver_no', $module1);
if ($field78) {
    echo "<li>The driver_no field already exists</li><br>";
} else {
    $field78 = new Vtiger_Field();
    $field78->label = 'LBL_EMPLOYEES_DRIVER_NO';
    $field78->name = 'driver_no';
    $field78->table = 'vtiger_employees';
    $field78->column = 'driver_no';
    $field78->columntype = 'VARCHAR(15)';
    $field78->uitype = 2;
    $field78->typeofdata = 'V~O';

    $block10->addField($field78);
}
$block10->save($module1);


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";