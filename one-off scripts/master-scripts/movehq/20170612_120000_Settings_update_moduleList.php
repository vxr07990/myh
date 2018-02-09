<?php

if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

global $adb;

//Set module list for CRM Settings

//Add new block for user-accessible config modules so that they aren't all grouped under Other Settings
$blockLabel = 'LBL_CONFIG_MODULES';

$query = "SELECT * FROM `vtiger_settings_blocks` WHERE label=?";
$res = $adb->pquery($query, [$blockLabel]);
$blockId = 0;

if($res && $adb->num_rows($res) == 0) {
    $query = "INSERT INTO `vtiger_settings_blocks` VALUES (?,?,?)";
    $blockId = $adb->getUniqueID('vtiger_settings_blocks');
    $adb->pquery($query, [$blockId, $blockLabel, $blockId]);
} elseif ($res) {
    $blockId = $res->fields['blockid'];
}

if($blockId) {
    $moduleList = [
        'AgentManager',
        'VanlineManager',
        'TariffManager',
        'MenuCreator',
        'PicklistCustomizer',
        'Webforms',
        'Workflows',
        'CapacityCalendarCounter',
        'ItemCodes',
        'OPList',
        'ZoneAdmin',
        'TooltipManager',
    ];

    $modulesToRemoveFromCrmSettings = [
        'CommissionPlans',
        'ContainerTypes',
        'MenuCleaner',
        'AgentCompensation',
        'Escrows',
        'Agents',
        'Equipment',
        'Tariffs',
        'Vanlines',
        'Vehicles',
        'Vendors',
        'Users',
        'Webforms',
        'Tooltip Manager', // Old name
    ];

    $adb->pquery("DELETE FROM `vtiger_settings_field` WHERE `name` IN (".generateQuestionMarks($modulesToRemoveFromCrmSettings).")", $modulesToRemoveFromCrmSettings);

    foreach($moduleList as $sequence => $moduleName) {
        $query = "SELECT * FROM `vtiger_settings_field` WHERE `name`=?";
        $res   = $adb->pquery($query, [$moduleName]);
        unset($query, $param);
        if($res && $adb->num_rows($res) == 0) {
            //Insert new row
            $field = $adb->getUniqueID('vtiger_settings_field');
            $query = "INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `iconpath`, `description`, `linkto`, `sequence`, `active`, `pinned`) VALUES (?,?,?,?,?,?,?,?,?)";
            $param = [$field, $blockId, $moduleName, '', $moduleName, 'index.php?module='.$moduleName.(in_array($moduleName, array('Webforms','TooltipManager')) ? '&parent=Settings' : '').($moduleName == 'PicklistCustomizer' ? '&view=Edit' : ($moduleName == 'TooltipManager') ? '&view=Settings' : '&view=List'), $sequence, 0, 0];
        } elseif ($res) {
            //Update existing row
            $query = "UPDATE `vtiger_settings_field` SET blockid=?, sequence=?, description=? WHERE `name`=?";
            $param = [$blockId, $sequence, $moduleName, $moduleName];
        }
        if($query) {
            $adb->pquery($query, $param);
        }
    }
}

$adb->query("UPDATE `vtiger_settings_field` SET active=1 WHERE `name`='LBL_CONFIG_EDITOR'");

if(!Vtiger_Utils::CheckTable('vtiger_settings_userpins')) {
    Vtiger_Utils::CreateTable('vtiger_settings_userpins',
                              '(
                                    `fieldid` INT(19) NOT NULL,
                                    `userid` INT(19) NOT NULL,
                                    `pinned` TINYINT(1) NOT NULL,
                                    PRIMARY KEY(`fieldid`,`userid`)
                              )', true);
}



print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

