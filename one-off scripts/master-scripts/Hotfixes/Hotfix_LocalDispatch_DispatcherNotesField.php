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



//include this stuff to run independent of master script
$Vtiger_Utils_Log = true;
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';
include_once 'vtlib/Vtiger/Field.php';
include_once 'vtlib/Vtiger/Block.php';

echo "<h2>Starting to add Dispatcher Notes field to LocalDispatch</h2> ";

$moduleInstance = Vtiger_Module::getInstance('OrdersTask');
if (!$moduleInstance) {
    echo "<h3>The OrdersTask module DONT exists </h3>";
} else {
    $block = Vtiger_Block::getInstance('LBL_OPERATIVE_TASK_INFORMATION', $moduleInstance);
    if (!$block) {
        echo "<h3>The LBL_OPERATIVE_TASK_INFORMATION block DONT exists </h3> ";
    } else {
        $field_note = Vtiger_Field::getInstance('notes_to_dispatcher', $moduleInstance);
        if ($field_note) {
            echo "Field notes_to_dispatcher already present.<br>";
        } else {
            $field1 = new Vtiger_Field();
            $field1->name = 'notes_to_dispatcher';
            $field1->label = 'LBL_NOTES_TO_DISPATCHER';
            $field1->uitype = 21;
            $field1->table = 'vtiger_orderstask';
            $field1->column = $field1->name;
            $field1->columntype = 'text';
            $field1->typeofdata = 'V~O';
            $block->addField($field1);
        }
        
        $field_at = Vtiger_Field::getInstance('agent_type', $moduleInstance);
        if ($field_at) {
            $field_at->delete();
        } else {
            echo "Field agent_type NOT present.<br>";
        }
        
        $field_a = Vtiger_Field::getInstance('agent', $moduleInstance);
        if ($field_a) {
            $field_a->delete();
        } else {
            echo "Field agent NOT present.<br>";
        }
        
        $field_pa = Vtiger_Field::getInstance('participant_agent', $moduleInstance);
        if ($field_pa) {
            echo "Field participant_agent already present.";
        } else {
            $field2 = new Vtiger_Field();
            $field2->name = 'participant_agent';
            $field2->label = 'LBL_PARTICIPATING_AGENT';
            $field2->uitype = 15;
            $field2->table = 'vtiger_orderstask';
            $field2->column = $field2->name;
            $field2->columntype = 'VARCHAR(255)';
            $field2->typeofdata = 'V~O';
            $block->addField($field2);
        }
    }
    echo 'OK<br>';
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";