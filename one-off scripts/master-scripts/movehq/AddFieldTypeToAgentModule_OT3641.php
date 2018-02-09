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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';
include_once 'vtlib/Vtiger/Menu.php';
include_once 'vtlib/Vtiger/Module.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');

global $adb;

$AgentsInstance = Vtiger_Module::getInstance('Agents');

if ($AgentsInstance){
    $block = Vtiger_Block::getInstance('LBL_AGENTS_INFORMATION',$AgentsInstance);
    if ($block){
        $fieldName = 'agent_type_picklist';
        $fieldLabel = 'LBL_AGENT_TYPE_PICKLIST';
        $blockID = $block->id;
        $tabid = $AgentsInstance->id;
        $field = Vtiger_Field::getInstance($fieldName,$AgentsInstance);
        if (!$field){
            $selectQUERY = "SELECT * FROM vtiger_field
                            WHERE sequence = 4
                            AND block = $blockID
                            AND tabid = $tabid";
            $result = $adb->pquery($selectQUERY);
            if ($adb->num_rows($result) > 0){
                $updateQUERY = "UPDATE vtiger_field
                            SET sequence = sequence+1
                            WHERE sequence >= 4
                            AND block = $blockID
                            AND tabid = $tabid";
                $adb->pquery($updateQUERY);
            }

            $field = new Vtiger_Field();
            $field->label = $fieldLabel;
            $field->name = $fieldName;
            $field->table = 'vtiger_agents';
            $field->column = $fieldName;
            $field->columntype = 'VARCHAR(100)';
            $field->uitype = 16;
            $field->typeofdata = 'V~O';
            $field->sequence = 4;
            $field->setPicklistValues(array('Full Service', 'Government 409', 'Military Agent', 'Non Domestic Agent', 'Third Proviso', 'Logistics Only'));

            $block->addField($field);
            echo "<li>The '$fieldName' field created done</li><br>";
        }
        else{
            echo "<li>The '$fieldName' has already exists</li><br>";
        }
    }
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";