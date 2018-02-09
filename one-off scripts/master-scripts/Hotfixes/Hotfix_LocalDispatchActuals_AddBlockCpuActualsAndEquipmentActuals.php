<?php

//Hotfix_LocalDispatchActuals_AddBlockCpuActualsAndEquipmentActuals.php

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


echo "<br><h1>Starting </h1><br>\n";

$moduleInstance = Vtiger_Module::getInstance('OrdersTask');
$blockName = 'LBL_CPU_ACTUALS';
$blockInstance = Vtiger_Block::getInstance($blockName, $moduleInstance);
if ($blockInstance) {
    echo "<h3>The $blockName block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = $blockName;
    $moduleInstance->addBlock($blockInstance);
    echo "Block $blockName added<br>\n";
}

$blockName = 'LBL_EQUIPMENT_ACTUALS';
$blockInstance = Vtiger_Block::getInstance($blockName, $moduleInstance);
if ($blockInstance) {
    echo "<h3>The $blockName block already exists</h3><br> \n";
} else {
    $blockInstance = new Vtiger_Block();
    $blockInstance->label = $blockName;
    $moduleInstance->addBlock($blockInstance);
    echo "Block $blockName added<br>\n";
}
echo "<br><h1>Finished </h1><br>\n";