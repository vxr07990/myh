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

echo "<br>begin hotfix, add valuation discount<br>";

$moduleInstance = Vtiger_Module::getInstance('Estimates');

$block1 = Vtiger_Block::getInstance('LBL_QUOTES_VALUATION', $moduleInstance);

if ($block1) {
    echo "creating fields...";
    $field3 = Vtiger_Field::getInstance('valuation_discounted', $moduleInstance);
    if (!$field3) {
        $field3             = new Vtiger_Field();
        $field3->label      = 'LBL_QUOTES_VALUATIONDISCOUNTED';
        $field3->name       = 'valuation_discounted';
        $field3->table      = 'vtiger_quotes';
        $field3->column     = 'valuation_discounted';
        $field3->columntype = 'VARCHAR(3)';
        $field3->uitype     = 56;
        $field3->typeofdata = 'V~O';
        $block1->addField($field3);
    } else {
        echo "<br>valuation_discounted already exists<br>";
    }
    $field3 = Vtiger_Field::getInstance('valuation_discount_amount', $moduleInstance);
    if (!$field3) {
        $field3             = new Vtiger_Field();
        $field3->label      = 'LBL_QUOTES_VALUATIONDISCOUNTAMOUNT';
        $field3->name       = 'valuation_discount_amount';
        $field3->table      = 'vtiger_quotes';
        $field3->column     = 'valuation_discount_amount';
        $field3->columntype = 'DECIMAL(10,2)';
        $field3->uitype     = 71;
        $field3->typeofdata = 'V~O';
        $block1->addField($field3);
    } else {
        echo "<br>valuation_discount_amount already exists<br>";
    }
    $block1->save($moduleInstance);
    echo "done!";
} else {
    echo "<br>could not find LBL_QUOTES_VALUATION, no action taken";
}

echo "<br> hotfix complete";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";