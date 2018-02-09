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


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
$oppsModule = Vtiger_Module::getInstance('Opportunities');
$potsModule = Vtiger_Module::getInstance('Potentials');

$infoBlockOpps = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppsModule);
$infoBlockPots = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $potsModule);

$stsNumFieldOpps = Vtiger_Field::getInstance('register_sts_number', $oppsModule);
if ($stsNumFieldOpps) {
    echo '<br>opps register_sts_number already exists';
} else {
    $stsNumFieldOpps             = new Vtiger_Field();
    $stsNumFieldOpps->label      = 'Registration Number';
    $stsNumFieldOpps->name       = 'register_sts_number';
    $stsNumFieldOpps->table      = 'vtiger_potential';
    $stsNumFieldOpps->column     = 'register_sts_number';
    $stsNumFieldOpps->columntype = 'VARCHAR(50)';
    $stsNumFieldOpps->uitype = 1;
    $stsNumFieldOpps->typeofdata = 'V~O';
    $stsNumFieldOpps->displaytype = 1;
    $stsNumFieldOpps->quickcreate = 0;
    $stsNumFieldOpps->presence = 2;
    $stsNumFieldOpps->readonly = 1;
    $infoBlockOpps->addField($stsNumFieldOpps);
    echo '<br>potentials register_sts_number created';
}

$stsNumFieldPots = Vtiger_Field::getInstance('register_sts_number', $potsModule);
if ($stsNumFieldPots) {
    echo '<br>potentials register_sts_number already exists';
} else {
    $stsNumFieldPots             = new Vtiger_Field();
    $stsNumFieldPots->label      = 'Registration Number';
    $stsNumFieldPots->name       = 'register_sts_number';
    $stsNumFieldPots->table      = 'vtiger_potential';
    $stsNumFieldPots->column     = 'register_sts_number';
    $stsNumFieldPots->columntype = 'VARCHAR(50)';
    $stsNumFieldPots->uitype = 1;
    $stsNumFieldPots->typeofdata = 'V~O';
    $stsNumFieldPots->displaytype = 1;
    $stsNumFieldPots->quickcreate = 0;
    $stsNumFieldPots->presence = 2;
    $stsNumFieldPots->readonly = 1;
    $infoBlockPots->addField($stsNumFieldPots);
    echo '<br>potentials register_sts_number created';
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";