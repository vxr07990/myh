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



$module1 = Vtiger_Module::getInstance('AgentManager'); // The module1 your blocks and fields will be in.
if ($module1) {
    echo "<h2>Updating AgentManger Fields</h2><br>";
    $block0 = Vtiger_Block::getInstance('LBL_AGENTMANAGER_INFORMATION', $module1);
    if ($block0) {
        echo "<h3>The LBL_CUSTOM_INFORMATION block already exists</h3><br>";
        $field1 = Vtiger_Field::getInstance('imagename', $module1);
        if ($field1) {
            echo "<li>The imagename field already exists</li><br>";
        } else {
            $field1             = new Vtiger_Field();
            $field1->label      = 'LBL_AGENTMANAGER_IMAGENAME';
            $field1->name       = 'imagename';
            $field1->table      = 'vtiger_agentmanager';
            $field1->column     = 'imagename';
            $field1->columntype = 'VARCHAR(250)';
            $field1->uitype     = 69;
            $field1->typeofdata = 'V~0';
            $block0->addField($field1);
            //$block0->save($module1);
        }
    } else {
        echo "<h1>NO Agentmanager Information block, failing imagename</h1>";
    }
} else {
    echo "<h1>NO Agentmanager, failing adding imagename </h1>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";