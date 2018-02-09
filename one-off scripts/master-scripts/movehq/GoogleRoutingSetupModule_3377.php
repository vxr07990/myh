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

$db = PearDatabase::getInstance();

function createFieldsAndBlocks3377($moduleInstance, $listFieldsInfo)
{
    foreach ($listFieldsInfo as $blockLabel => $listField) {
        $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
        if (!$blockInstance) {
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blockLabel;
            $moduleInstance->addBlock($blockInstance);
        }
        foreach ($listField as $fieldName =>$fieldInfo) {
            echo "\nBEGINNING create $fieldName field\n";
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleInstance);
            if ($fieldModel) {
                echo "\n$fieldName field already exists.\n";
            } else {
                $fieldModel = new Vtiger_Field();
                $fieldModel->table = 'vtiger_'.strtolower($moduleInstance->name);
                $fieldModel->columnname = $fieldName;
                $fieldModel->name = $fieldName;
                foreach ($fieldInfo as $option =>$value) {
                    if (!in_array($option, array('picklistvalues', 'related_modules'))) {
                        $fieldModel->$option = $value;
                    }
                }
                $blockInstance->addField($fieldModel);
                if (isset($fieldInfo['picklistvalues'])) {
                    $fieldModel->setPicklistValues($fieldInfo['picklistvalues']);
                }
                if (isset($fieldInfo['related_modules'])) {
                    $fieldModel->setRelatedModules($fieldInfo['related_modules']);
                }
                if (isset($fieldInfo['isentityidentifier'])) {
                    $moduleInstance->setEntityIdentifier($fieldModel);
                }
                echo "done!\n";
            }
        }
    }
}
$agentManagerInstance = Vtiger_Module::getInstance('AgentManager');
if (!$agentManagerInstance) {
    echo "\nAgent Manager module don't exist";
}else {
    $newBlocks = [
        'LBL_GOOGLE_ROUTING_SETUP_MODULE' => [
            'adjust_google_miles_by' => [
                'label' => 'Adjust Google Miles By',
                'columntype' => 'decimal(5,2)',
                'uitype' => 9,
                'typeofdata' => 'N~O',
            ],
            'add_miles_to' => [
                'label' => 'Add miles to Google Miles',
                'columntype' => 'int(10)',
                'uitype' => 7,
                'typeofdata' => 'I~O',
            ],
            'adjust_google_time_by' => [
                'label' => 'Adjust Google Time By',
                'columntype' => 'decimal(5,2)',
                'uitype' => 9,
                'typeofdata' => 'N~O',
            ],
            'add_minutes_to' => [
                'label' => 'Add minutes to Google Time',
                'columntype' => 'int(10)',
                'uitype' => 7,
                'typeofdata' => 'I~O',
            ],
            'round_google_time' => [
                'label' => 'Round Google Time',
                'columntype' => 'varchar(255)',
                'uitype' => 16,
                'typeofdata' => 'I~O',
                'defaultvalue' => 'No Rounding',
                'picklistvalues' => ['No Rounding', ' Round Time to Nearest 1/4 Hour', 'Round Time to Higher 1/4 Hour', 'Round Time to Lower 1/4 Hour']
            ],
        ]
    ];
    createFieldsAndBlocks3377($agentManagerInstance, $newBlocks);
}

//Update block sequence for GOOGLE ROUTING SETUP MODULE Block
$agentTabId = getTabid('AgentManager');
$aboveBlock  = Vtiger_Block::getInstance('LBL_AGENTMANAGER_DEFAULTS',$agentManagerInstance);
$adb->pquery("UPDATE vtiger_blocks SET sequence = (sequence + 1) WHERE tabid = ? AND sequence > ?",[$agentTabId,$aboveBlock->sequence]);
$adb->pquery("UPDATE vtiger_blocks SET sequence = ? WHERE tabid = ? AND blocklabel = 'LBL_GOOGLE_ROUTING_SETUP_MODULE' ",[$aboveBlock->sequence + 1,$agentTabId]);

$sqlBlock = "SELECT * FROM vtiger_blocks WHERE tabid = ? ORDER BY sequence ASC";
$rsBlocks = $adb->pquery($sqlBlock,[$agentTabId]);
$seq = 0;
while ($row = $adb->fetchByAssoc($rsBlocks)){
    $seq ++;
    $adb->pquery("UPDATE vtiger_blocks SET  sequence = ? WHERE blockid = ?",[$seq,$row['blockid']]);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";