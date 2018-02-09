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


$module = Vtiger_Module::getInstance('TariffManager');
$block = Vtiger_Block::getInstance('LBL_TARIFFMANAGER_ADMINISTRATIVE', $module);
if ($block) {
    echo "<br> block 'LBL_TARIFFMANAGER_ADMINISTRATIVE' exists, attempting to add custom_javascript field<br>";
    $custom_javascript = Vtiger_Field::getInstance('custom_javascript', $module);
    if ($custom_javascript) {
        echo "<br> custom_javascript field already exists.<br>";
    } else {
        echo "<br> custom_javascript field doesn't exist, adding it now.<br>";
        $custom_javascript = new Vtiger_Field();
        $custom_javascript->label = 'LBL_TARIFFMANAGER_CUSTOMJAVASCRIPT';
        $custom_javascript->name = 'custom_javascript';
        $custom_javascript->table = 'vtiger_tariffmanager';
        $custom_javascript->column = 'custom_javascript';
        $custom_javascript->columntype = 'varchar(255)';
        $custom_javascript->uitype = 1;
        $custom_javascript->typeofdata = 'V~O';
        $custom_javascript->displaytype = 1;
        $custom_javascript->quickcreate = 0;

        $block->addField($custom_javascript);
        echo "<br> custom_javascript field added.<br>";
    }
} else {
    echo "<br> block 'LBL_TARIFFMANAGER_ADMINISTRATIVE' doesn't exist, no action taken<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";