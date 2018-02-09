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
echo "<br>Begin adminAccess Hotfix...<br>";

$tariffsModule = Vtiger_Module::getInstance('Tariffs');

$tariffsBlock = Vtiger_Block::getInstance('LBL_TARIFFS_INFORMATION', $tariffsModule);
if ($tariffsBlock) {
    echo "<br> block 'LBL_TARIFFS_INFORMATION' exists, attempting to add Admin Only field<br>";
    $adminAccess = Vtiger_Field::getInstance('admin_access', $tariffsModule);
    if ($adminAccess) {
        echo "<br> adminAccess field already exists.<br>";
    } else {
        echo "<br> adminAccess field doesn't exist, adding it now.<br>";
        $adminAccess = new Vtiger_Field();
        $adminAccess->label = 'LBL_TARIFFS_ADMINACCESS';
        $adminAccess->name = 'admin_access';
        $adminAccess->table = 'vtiger_tariffs';
        $adminAccess->column = 'admin_access';
        $adminAccess->columntype = 'VARCHAR(3)';
        $adminAccess->uitype = 156;
        $adminAccess->typeofdata = 'V~O';
        $adminAccess->displaytype = 1;
        $adminAccess->quickcreate = 0;
        
        $tariffsBlock->addField($adminAccess);
        echo "<br> adminAccess field added.<br>";
    }
} else {
    echo "<br> block 'LBL_TARIFFS_INFORMATION' doesn't exist, no action taken.<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";