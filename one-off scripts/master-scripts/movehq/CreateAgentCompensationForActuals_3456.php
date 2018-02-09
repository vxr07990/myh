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
error_reporting(E_ERROR);


require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('includes/main/WebUI.php');
require_once('includes/runtime/LanguageHandler.php');

//needs these
require_once('include/Webservices/Create.php');
require_once('modules/Vtiger/uitypes/Date.php');
$Vtiger_Utils_Log = true;

global $adb;

function createFieldsAndBlocks_3456($moduleInstance,$listFieldsInfo){
    foreach($listFieldsInfo as $blockLabel => $listField){
        $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
        if(!$blockInstance){
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blockLabel;
            $moduleInstance->addBlock($blockInstance);
        }
        foreach($listField as $fieldName =>$fieldInfo){
            echo "\nBEGINNING create $fieldName field\n";
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName,$moduleInstance);
            if($fieldModel){
                echo "\n$fieldName field already exists.\n";
            }else{
                $fieldModel = new Vtiger_Field();
                $fieldModel->table = $fieldInfo['tablename'];
                $fieldModel->columnname = $fieldName;
                $fieldModel->name = $fieldName;
                foreach ($fieldInfo as $option =>$value){
                    if(!in_array($option,array('picklistvalues','related_modules'))){
                        $fieldModel->$option = $value;
                    }
                }
                $blockInstance->addField($fieldModel);
                if(isset($fieldInfo['picklistvalues'])){
                    $fieldModel->setPicklistValues($fieldInfo['picklistvalues']);
                }
                if(isset($fieldInfo['related_modules'])){
                    $fieldModel->setRelatedModules($fieldInfo['related_modules']);
                }
                if(isset($fieldInfo['isentityidentifier'])){
                    $moduleInstance->setEntityIdentifier($fieldModel);
                }
                echo "done!\n";
            }
        }
    }
}

$listFields = [
    'LBL_AGENT_COMPENSATION_DISTRIBUTION'=>[
        'agentcompensationid' =>[
            'label'=>'Agent Compensation',
            'columntype'=>'int(11)',
            'uitype'=>10,
            'typeofdata'=>'I~O',
            'related_modules'=>['AgentCompensation'],
        ]
    ]
];
$moduleInstance = Vtiger_Module::getInstance('Actuals');
if($moduleInstance){
    createFieldsAndBlocks_3456($moduleInstance,$listFields);
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";