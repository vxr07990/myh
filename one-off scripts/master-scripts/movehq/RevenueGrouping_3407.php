<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2;
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

$db = PearDatabase::getInstance();

function createFieldsAndBlocks_3407($moduleInstance,$listFieldsInfo){
    foreach($listFieldsInfo as $blockLabel => $listField){
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
                $fieldModel->table = $fieldInfo['tablename'];
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

echo "\nBEGINNING Create Revenue Grouping Module\n";
$revenueGroupingInstance = Vtiger_Module::getInstance('RevenueGrouping');
if (!$revenueGroupingInstance) {
    $revenueGroupingInstance = new Vtiger_Module();
    $revenueGroupingInstance->name = 'RevenueGrouping';
    $revenueGroupingInstance->save();
    $revenueGroupingInstance->initTables();
    $revenueGroupingInstance->setDefaultSharing();
    $revenueGroupingInstance->initWebservice();
} else {
    $agentManagerModule = Vtiger_Module::getInstance('AgentManager');
    $revenueGroupingInstance->unsetRelatedList($agentManagerModule, 'Agent Manager');
    $revenueGroupingInstance->unsetRelatedList($agentManagerModule, 'Agent Manager', 'get_dependents_list');
}


$arrModuleStructure = array(
    'LBL_REVENUEGROUPINGDETAIL'=>array(
        'agentid'=>array(
            'label'=>'Owner',
            'columntype'=>'int(11)',
            'uitype'=>1002,
            'typeofdata'=>'V~O',
            'tablename'=>'vtiger_crmentity',
            'isentityidentifier'=>true
        ),
    ),
    'LBL_REVENUE_GROUPING_ITEMS'=>array(),
);

createFieldsAndBlocks_3407($revenueGroupingInstance,$arrModuleStructure);

echo "\nBEGINNING Create Revenue Grouping Item Module\n";
$revenueGroupingItemInstance = Vtiger_Module::getInstance('RevenueGroupingItem');
if (!$revenueGroupingItemInstance) {
    $revenueGroupingItemInstance = new Vtiger_Module();
    $revenueGroupingItemInstance->name = 'RevenueGroupingItem';
    $revenueGroupingItemInstance->save();
    $revenueGroupingItemInstance->initTables();
    $revenueGroupingItemInstance->setDefaultSharing();
    $revenueGroupingItemInstance->initWebservice();
}

$arrModuleStructure = array(
    'LBL_REVENUEGROUPINGITEMSDETAIL'=>array(
        'revenuegroup'=>array(
            'label'=>'Revenue Group',
            'columntype'=>'varchar(100)',
            'uitype'=>1,
            'typeofdata'=>'V~M',
            'tablename'=>'vtiger_revenuegroupingitem',
            'isentityidentifier'=>true
        ),
        'invoicesequence'=>array(
            'label'=>'Invoice Sequence',
            'columntype'=>'int(3)',
            'uitype'=>7,
            'typeofdata'=>'I~M',
            'tablename'=>'vtiger_revenuegroupingitem',
        ),
        'revenuegroupingitem_relcrmid'=>array(
            'label'=>'Revenue Grouping',
            'columntype'=>'int(19)',
            'uitype'=>10,
            'typeofdata'=>'I~O',
            'tablename'=>'vtiger_revenuegroupingitem',
            'related_modules'=>array('RevenueGrouping'),
        )
    ),
);

createFieldsAndBlocks_3407($revenueGroupingItemInstance,$arrModuleStructure);

// Fix issue with table of Owner field
$db->pquery("UPDATE vtiger_field set tablename='vtiger_crmentity' WHERE tabid=? AND fieldname=?", array($revenueGroupingInstance->id, 'agentid'));
//Menu
$parentLabel = 'COMPANY_ADMIN_TAB';
if ($db) {
    $stmt = 'UPDATE vtiger_tab SET parent=? WHERE tabid=?';
    $db->pquery($stmt, [$parentLabel, $revenueGroupingInstance->id]);
} else {
    Vtiger_Utils::ExecuteQuery("UPDATE vtiger_tab SET parent='".$parentLabel."' WHERE tabid=".$revenueGroupingInstance->id);
}
// Add related module item for AgentManagerModule
$agentManagerModule = Vtiger_Module::getInstance('AgentManager');
if($agentManagerModule){
    $rsRelated = $db->pquery("SELECT * FROM vtiger_relatedlists WHERE tabid = ? AND related_tabid = ?",array(getTabid('AgentManager'),getTabid('RevenueGrouping')));
    if($db->num_rows($rsRelated) != 0){
        $agentManagerModule->unsetRelatedList($revenueGroupingInstance, 'Revenue Grouping');
        $agentManagerModule->unsetRelatedList($revenueGroupingInstance, 'Revenue Grouping', 'get_dependents_list');
    }
}

if(in_array('agentid', $adb->getColumnNames('vtiger_revenuegrouping'))) {
    $db->pquery("ALTER TABLE vtiger_revenuegrouping DROP COLUMN  agentid",array());
}

//Update Entity Name
$adb->pquery("UPDATE vtiger_entityname SET tablename = 'vtiger_crmentity', entityidfield = 'crmid', entityidcolumn = 'crmid' WHERE  tabid =?",array(getTabid('RevenueGrouping')));

// Add Revenue Grouping Field field for Agent Manager
$agentManagerModule = Vtiger_Module::getInstance('AgentManager');
if($agentManagerModule){
    $arrAgentNewField = [
        'LBL_CUSTOM_INFORMATION'=>[
            'revenuegroupingid'=>array(
                'label'=>'Revenue Grouping',
                'columntype'=>'int(11)',
                'uitype'=>10,
                'typeofdata'=>'I~O',
                'presence'=>1,
                'related_modules'=>['RevenueGrouping'],
            ),
        ]
    ];
    createFieldsAndBlocks_3407($agentManagerModule,$arrAgentNewField);
}

$adb->pquery("UPDATE vtiger_relatedlists SET `name` = 'get_dependents_list', actions = '' WHERE tabid =? AND related_tabid = ?",[getTabid('AgentManager'),getTabid('RevenueGrouping')]);
$adb->pquery("UPDATE vtiger_relatedlists SET `name` = 'get_dependents_list', actions = '' WHERE tabid =? AND related_tabid = ?",[getTabid('RevenueGrouping'),getTabid('AgentManager')]);

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
