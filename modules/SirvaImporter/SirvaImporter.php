<?php

include_once 'modules/Vtiger/CRMEntity.php';
include_once 'include/utils/utils.php';

class SirvaImporter extends Vtiger_CRMEntity
{
    public function __construct()
    {
    }

    

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    public function vtlib_handler($moduleName, $eventType)
    {
        if ($eventType == 'module.postinstall') {
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
        } elseif ($eventType == 'module.disabled') {
        } elseif ($eventType == 'module.preuninstall') {
            $adb = PearDatabase::getInstance();
            $adb->pquery("DELETE FROM vtiger_settings_field WHERE name=?", array('Sirva Importer'));
        } elseif ($eventType == 'module.preupdate') {
        } elseif ($eventType == 'module.postupdate') {
        }
    }
}
