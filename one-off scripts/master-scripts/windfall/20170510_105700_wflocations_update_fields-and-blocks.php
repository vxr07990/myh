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


global $adb;
function createFieldsAndBlocks_4460($moduleInstance, $listFieldsInfo)
{
    global $adb;
    foreach ($listFieldsInfo as $blockLabel => $listField) {
        $blockInstance = Vtiger_Block::getInstance($blockLabel, $moduleInstance);
        if (!$blockInstance) {
            $blockInstance = new Vtiger_Block();
            $blockInstance->label = $blockLabel;
            $moduleInstance->addBlock($blockInstance);
        }
        foreach ($listField as $fieldName => $fieldInfo) {
            echo "<br>BEGINNING create $fieldName field";
            $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleInstance);
            if ($fieldModel) {
                echo "<br>$fieldName field already exists.";
            } else {
                $fieldModel = new Vtiger_Field();
                $fieldModel->table = $fieldInfo['table'];
                if ($fieldInfo['table'] == '') {
                    $fieldModel->table = 'vtiger_' . strtolower($moduleInstance->name);
                }
                $fieldModel->name = $fieldName;
                foreach ($fieldInfo as $option => $value) {
                    if (!in_array($option, array('picklistvalues', 'related_modules', 'isentityidentifier'))) {
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
                echo "<br>done!";
            }
        }
    }
}

$locationInstance = Vtiger_Module::getInstance('WFLocations');
if ($locationInstance) {
    $tableid = $locationInstance->getId();
    $sql = "SELECT * FROM `vtiger_modtracker_tabs` WHERE `vtiger_modtracker_tabs`.`tabid` = ?";
    $result = $adb->pquery($sql, array($tableid));
    if ($adb->num_rows($result) == 0) {
        $adb->pquery("insert into `vtiger_modtracker_tabs` ( `visible`, `tabid`) values (?, ?)", array('1', $tableid));
    }
    $moduleInfos = array(
        'LBL_WFLOCATIONS_INFORMATION' => array(
            'pre' => array(
                'label' => 'Pre',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
            ),
            'post' => array(
                'label' => 'Post',
                'columntype' => 'varchar(255)',
                'uitype' => 1,
                'typeofdata' => 'V~O',
            ),
            'agentid' => array(
                'label' => 'Owner',
                'columntype' => 'int(19)',
                'uitype' => 1002,
                'typeofdata' => 'I~M',
                'tablename' => 'vtiger_crmentity'
            )
        ),
        'LBL_RECORDUPDATEINFORMATION' => array(
            'createdtime' => array(
                'label' => 'Created Time',
                'columntype' => 'datetime',
                'uitype' => 70,
                'typeofdata' => 'T~O',
                'displaytype' => 2,
                'table' => 'vtiger_crmentity'
            ),
            'modifiedtime' => array(
                'label' => 'Modified Time',
                'columntype' => 'datetime',
                'uitype' => 70,
                'typeofdata' => 'T~O',
                'displaytype' => 2,
                'table' => 'vtiger_crmentity'
            ),
        )
    );
    createFieldsAndBlocks_4460($locationInstance, $moduleInfos);
    $warehouseField = Vtiger_Field_Model::getInstance('wflocation_warehouse', $locationInstance);
    if ($warehouseField) {
        vtws_addDefaultModuleTypeEntity('WFWarehouses');
        $warehouseField->unsetRelatedModules(array('Warehouse', 'WFWarehouses'));
        $warehouseField->setRelatedModules(array('WFWarehouses'));
    }
    $block1 = Vtiger_Block::getInstance('LBL_WFLOCATIONS_INFORMATION', $locationInstance);
    $block2 = Vtiger_Block::getInstance('LBL_WFLOCATIONS_DETAILS', $locationInstance);
    $fieldsBlock1 = array('wflocation_base', 'slot', 'reserved', 'wfslot_configuration',
                          'active', 'squarefeet', 'offsite', 'cubefeet');
    $fieldsBlock2 = array('wflocation_type', 'pre', 'name', 'post', 'create_multiple',
                          'tag', 'description', 'wflocation_combination', 'wflocation_warehouse', 'agent', 'agentid',
                          'cost', 'percentused', 'percentusedoverride', 'row',
                          'bay', 'level', 'double_high', 'container_capacity_on',
                          'container_capacity');
    $adb->pquery("UPDATE vtiger_field SET block = ? WHERE tabid = ? AND fieldname IN (" . generateQuestionMarks($fieldsBlock1) . ")", array($block1->id, $locationInstance->id, $fieldsBlock1));
    $adb->pquery("UPDATE vtiger_field SET block = ? WHERE tabid = ? AND fieldname IN (" . generateQuestionMarks($fieldsBlock2) . ")", array($block2->id, $locationInstance->id, $fieldsBlock2));
    //Update sequence
    for ($i = 0; $i < count($fieldsBlock1); $i++) {
        $adb->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldname = ? AND block = ?", array($i + 1, $fieldsBlock1[$i], $block1->id));
    }
    for ($i = 0; $i < count($fieldsBlock2); $i++) {
        $adb->pquery("UPDATE vtiger_field SET sequence = ? WHERE fieldname = ? AND block = ?", array($i + 1, $fieldsBlock2[$i], $block2->id));
    }
    $adb->pquery("UPDATE vtiger_field SET typeofdata = 'V~M' WHERE fieldname = 'wfslot_configuration' AND block = ?", array($block1->id));
    $adb->pquery("UPDATE vtiger_field SET presence = 1 WHERE fieldname = 'wflocation_combination' AND block = ?", array($block2->id));
    $locationTagField = Vtiger_Field::getInstance('tag', $locationInstance);
    $locationInstance->setEntityIdentifier($locationTagField);
}
