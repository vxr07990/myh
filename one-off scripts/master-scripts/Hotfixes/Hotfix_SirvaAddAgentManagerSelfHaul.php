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
        $field2 = Vtiger_Field::getInstance('self_haul', $module1);
        if ($field2) {
            echo "<li>The self_haul field already exists</li><br>";
        } else {
            $field2             = new Vtiger_Field();
            $field2->label      = 'LBL_AGENTMANAGER_SELF_HAUL';
            $field2->name       = 'self_haul';
            $field2->table      = 'vtiger_agentmanager';
            $field2->column     = 'self_haul';
            $field2->columntype = 'VARCHAR(3)';
            $field2->uitype = 56;
            $field2->typeofdata = 'V~O';
            $field2->displaytype = 1;
            $field2->quickcreate = 0;
            $field2->presence = 2;
            $block0->addField($field2);
        }

        $field2 = Vtiger_Field::getInstance('self_haul_agentmanagerid', $module1);
        if ($field2) {
            echo "<li>The self_haul_agentmanger field already exists</li><br>";
        } else {
            $field2             = new Vtiger_Field();
            $field2->label      = 'LBL_AGENTMANAGER_SELF_HAUL_AGENTMANAGERID';
            $field2->name       = 'self_haul_agentmanagerid';
            $field2->table      = 'vtiger_agentmanager';
            $field2->column     = 'self_haul_agentmanagerid';
            $field2->columntype = 'VARCHAR(255)';
            $field2->uitype = 10;
            $field2->typeofdata = 'V~O';
            $field2->displaytype = 1;
            $field2->quickcreate = 0;
            $field2->presence = 2;
            $block0->addField($field2);
            $field2->setRelatedModules(['AgentManager']);
        }
    } else {
        echo "<h1>NO Agentmanager Information block, failing self_haul_agentmanger</h1>";
    }
} else {
    echo "<h1>NO Agentmanager, failing adding self_haul </h1>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";