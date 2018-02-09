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


//$Vtiger_Utils_Log = true;
//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');
echo "<br>Begin tariff_type Hotfix...<br>";

$tariffsModule = Vtiger_Module::getInstance('Tariffs');
$tariffsBlock = Vtiger_Block::getInstance('LBL_TARIFFS_INFORMATION', $tariffsModule);

if ($tariffsBlock) {
    echo "<br> block 'LBL_TARIFFS_INFORMATION' exists, attempting to add tariff_type field<br>";
    $tariff_type = Vtiger_Field::getInstance('tariff_type', $tariffsModule);
    if ($tariff_type) {
        echo "<br> tariff_type field already exists.<br>";
    } else {
        echo "<br> tariff_type field doesn't exist, adding it now.<br>";
        $tariff_type = new Vtiger_Field();
        $tariff_type->label = 'LBL_TARIFFS_SPECIAL_TYPE';
        $tariff_type->name = 'tariff_type';
        $tariff_type->table = 'vtiger_tariffs';
        $tariff_type->column = 'tariff_type';
        $tariff_type->columntype = 'VARCHAR(25)';
        $tariff_type->uitype = 2;
        $tariff_type->typeofdata = 'V~O';
        $tariff_type->displaytype = 1;
        $tariff_type->quickcreate = 0;

        $tariffsBlock->addField($tariff_type);
        echo "<br> tariff_type field added.<br>";
    }
} else {
    echo "<br> block 'LBL_TARIFFS_INFORMATION' doesn't exist, no action taken.<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";