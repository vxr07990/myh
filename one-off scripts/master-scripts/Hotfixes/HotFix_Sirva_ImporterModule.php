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



$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('modules/ModTracker/ModTracker.php');


$moduleInstance = Vtiger_Module::getInstance('SirvaImporter');

if (!$moduleInstance) {
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = "SirvaImporter";
    $moduleInstance->save();

    //Add module to settings

    $adb = PearDatabase::getInstance();
    $otherSettingsBlock = $adb->pquery('SELECT * FROM vtiger_settings_blocks WHERE label=?', array('LBL_OTHER_SETTINGS'));
    $otherSettingsBlockCount = $adb->num_rows($otherSettingsBlock);

    if ($otherSettingsBlockCount > 0) {
        $blockid = $adb->query_result($otherSettingsBlock, 0, 'blockid');
        $sequenceResult = $adb->pquery("SELECT max(sequence) as sequence FROM vtiger_settings_blocks WHERE blockid=", array($blockid));
        if ($adb->num_rows($sequenceResult)) {
            $sequence = $adb->query_result($sequenceResult, 0, 'sequence');
        }
    }

    $fieldid = $adb->getUniqueID('vtiger_settings_field');
    $adb->pquery("INSERT INTO vtiger_settings_field(fieldid, blockid, name, iconpath, description, linkto, sequence, active) 
                        VALUES(?,?,?,?,?,?,?,?)", array($fieldid, $blockid, 'Sirva Importer', '', 'Sirva Importer', 'index.php?module=SirvaImporter&view=ImporterStep1&parent=Settings', $sequence++, 0));
    
    
    //Create module table
    Vtiger_Utils::ExecuteQuery("CREATE TABLE `vtiger_sirvaimporter_ids` (
                                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                    `importid` varchar(25) DEFAULT NULL,
                                    `crmid` int(11) DEFAULT NULL,
                                    `module` varchar(50) DEFAULT NULL,
                                    PRIMARY KEY (`id`)
                                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    
    
    // Fix Menu
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_tab` SET `parent` = '' WHERE `name` = 'SirvaImporter';");
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";