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
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<br>begin hotfix GVL estimates crating discount<br>";

$moduleInstance = Vtiger_Module::getInstance('Estimates');
$block2 = Vtiger_Block::getInstance('LBL_QUOTES_INTERSTATEMOVEDETAILS', $moduleInstance);

if ($block2) {
    $field4 = Vtiger_Field::getInstance('crating_disc', $moduleInstance);
    if (!$field4) {
        $field4 = new Vtiger_Field();
        $field4->label = 'LBL_QUOTES_CRATING_DISC';
        $field4->name = 'crating_disc';
        $field4->table = 'vtiger_quotes';
        $field4->column = 'crating_disc';
        $field4->columntype = 'DECIMAL(7,2)';
        $field4->uitype = 9;
        $field4->typeofdata = 'N~O';

        $block2->addField($field4);
    } else {
        echo "<br>crate_disc already exists<br>";
    }
} else {
    echo "<br>couldn't find block 'LBL_QUOTES_INTERSTATEMOVEDETAILS'";
}

echo "<br>end hotfix GVL estimates crating discount";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";