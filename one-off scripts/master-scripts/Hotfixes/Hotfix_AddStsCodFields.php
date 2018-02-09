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



//include_once('vtlib/Vtiger/Menu.php');
//include_once('vtlib/Vtiger/Module.php');

echo "begin hotfix for sts cod fields";

$oppsModule = Vtiger_Module::getInstance('Opportunities');

$stsBlock = Vtiger_Block::getInstance('LBL_OPPORTUNITY_REGISTERSTS', $oppsModule);

echo "<br>begin hotfix for sts cod fields";

echo "<br>creating agrmt picklist";

$agrmtPicklist = Vtiger_Field::getInstance('agrmt_cod', $oppsModule);
if ($agrmtPicklist) {
    echo "<br>agrmt_cod already exists.";
    $agrmtPicklist->setPicklistValues([
        'CGP',
        'GRR',
        'UAS',
        'TPG',
    ]);
} else {
    $agrmtPicklist = new Vtiger_Field();
    $agrmtPicklist->label = 'LBL_OPPORTUNITY_AGMTID';
    $agrmtPicklist->name = 'agrmt_cod';
    $agrmtPicklist->table = 'vtiger_potential';
    $agrmtPicklist->column = 'agrmt_cod';
    $agrmtPicklist->columntype = 'VARCHAR(100)';
    $agrmtPicklist->uitype = 16;
    $agrmtPicklist->typeofdata = 'V~O';
    $agrmtPicklist->setPicklistValues([
        'CGP',
        'GRR',
        'UAS',
        'TPG',
    ]);
    $stsBlock->addField($agrmtPicklist);
}

echo "<br>created";



echo "<br>creating subagrmt picklist";

$subagrmtPicklist = Vtiger_Field::getInstance('subagrmt_cod', $oppsModule);
if ($subagrmtPicklist) {
    echo "<br>subagrmt_cod already exists.";
} else {
    $subagrmtPicklist = new Vtiger_Field();
    $subagrmtPicklist->label = 'LBL_OPPORTUNITY_SUBAGMTNUMBER';
    $subagrmtPicklist->name = 'subagrmt_cod';
    $subagrmtPicklist->table = 'vtiger_potential';
    $subagrmtPicklist->column = 'subagrmt_cod';
    $subagrmtPicklist->columntype = 'VARCHAR(100)';
    $subagrmtPicklist->uitype = 16;
    $subagrmtPicklist->typeofdata = 'V~O';
    $subagrmtPicklist->setPicklistValues([
        '001',
        '002',
        '005',
        '007',
    ]);
}

$stsBlock->addField($subagrmtPicklist);

echo "<br>created";

echo "<br>Hotfix complete!";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";