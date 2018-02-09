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

// 3412: Parent Agent / Child Agent Solution for assigning Records
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport ('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

ini_set("memory_limit", -1);
set_time_limit(0);

$initData = array(
    'AgentManager' => array(						// module name
        'LBL_AGENTMANAGER_INFORMATION' => array(	// block name
            'cf_agent_manager_parent_id' => array(	// field name
                'label' => 'Parent Agent',			// label
                'table' => 'vtiger_agentmanager', // table
                'uitype' => 10,						// type
                'typeofdata'=> 'V~O',
                'sequence'=> 5,
                'related_to_module'=>'AgentManager'
            )
        )
    )
);

foreach ($initData as $moduleName => $blocks) {
    foreach ($blocks as $blockName => $fields) {
        $module = Vtiger_Module::getInstance($moduleName);
        $block = Vtiger_Block::getInstance($blockName, $module);
        if (!$block && $blockName) {
            $block = new Vtiger_Block();
            $block->label = $blockName;
            $block->__create($module);
        }
        # else $block->__delete(true);
        $adb = PearDatabase::getInstance();
        $currFieldSeqRs = $adb->pquery("SELECT sequence FROM `vtiger_field` WHERE block = ? ORDER BY sequence DESC LIMIT 0,1",
            array($block->id));
        $sequence = $adb->query_result($currFieldSeqRs, 'sequence', 0);

        foreach ($fields as $name => $field) {
            $existField = Vtiger_Field::getInstance($name, $module);
            if (!$existField && $name && $field['table']) {
                $sequence++;
                $newField = new Vtiger_Field();
                $newField->name = $name;
                $newField->label = $field['label'];
                $newField->table = $field['table'];
                $newField->uitype = $field['uitype'];

                if ($field['uitype'] == 15 || $field['uitype'] == 16 || $field['uitype'] == '33') {
                    $newField->setPicklistValues($field['picklistvalues']);
                }

                if(isset($field['typeofdata'])){
                    $newField->typeofdata = $field['typeofdata'];
                }

                $newField->sequence = $sequence;
                if (isset($field['sequence'])){
                    $newField->sequence = $field['sequence'];
                }

                $newField->__create($block);
                if ($field['uitype'] == 10) {
                    $newField->setRelatedModules(array($field["related_to_module"]));
                }
            }
            # else $field->__delete(true);
        }
    }
}

echo "DONE CREATE FIELD cf_agent_manager_parent_id";

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";