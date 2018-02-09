<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */

include_once 'modules/Vtiger/CRMEntity.php';

class AWSDocs extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_awsdocs';
    public $table_index = 'awsdocsid';

    /**

      /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_awsdocscf', 'awsdocsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_awsdocs', 'vtiger_awsdocscf', 'vtiger_awsdocsattach');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_awsdocs' => 'awsdocsid',
        'vtiger_awsdocscf' => 'awsdocsid',
        'vtiger_awsdocsattach' => 'awsdoc_id'
    );

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        '<entityfieldlabel>' => array('awsdocs', '<entitycolumn>'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        '<entityfieldlabel>' => '<entityfieldname>',
        'Assigned To' => 'assigned_user_id',
    );
    // Make the field link to detail view
    public $list_link_field = '<entityfieldname>';
    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        '<entityfieldlabel>' => array('awsdocs', '<entitycolumn>'),
        'Assigned To' => array('vtiger_crmentity', 'assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        '<entityfieldlabel>' => '<entityfieldname>',
        'Assigned To' => 'assigned_user_id',
    );
    // For Popup window record selection
    public $popup_fields = array('<entityfieldname>');
    // For Alphabetical search
    public $def_basicsearch_col = '<entityfieldname>';
    // Column value to use on detail view record text display
    public $def_detailview_recname = '<entityfieldname>';
    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('<entityfieldname>', 'assigned_user_id');
    public $default_order_by = '<entityfieldname>';
    public $default_sort_order = 'ASC';

    public function save_module($module)
    {
        global $log, $adb, $upload_badext;
        $insertion_mode = $this->mode;

        if ($_FILES['awsdocs_filename']['name'] != '') {
            $errCode = $_FILES['awsdocs_filename']['error'];
            if ($errCode == 0) {
                $AWSRecodModel = Vtiger_Record_Model::getInstanceById($this->id);

                foreach ($_FILES as $fileindex => $files) {
                    if ($files['name'] != '' && $files['size'] > 0) {
                        $filename = $_FILES['awsdocs_filename']['name'];
                        $filename = from_html(preg_replace('/\s+/', '_', $filename));
                        $filetype = $_FILES['awsdocs_filename']['type'];
                        $filesize = $_FILES['awsdocs_filename']['size'];
                        $binFile = sanitizeUploadFileName($filename, $upload_badext);
                        $filename = ltrim(basename(" " . $binFile)); //allowed filename like UTF-8 characters

                        $filetmpName = $_FILES['awsdocs_filename']['tmp_name'];

                        $AWSRecodModel->uploadAWSSaveFile($this->id, $this->id . '_' . $filename, $filetmpName);
                    }
                }
            }
        }
    }

    /**
     * Customizing the Delete procedure.
     */
    public function trash($module, $recordId)
    {
        $AWSDocsModel = Vtiger_Record_Model::getInstanceById($recordId, $module);

        $AWSDocsModel->deleteOldFile($recordId);


        parent::trash($module, $recordId);
    }

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    public function vtlib_handler($moduleName, $eventType)
    {
        global $adb;
        if ($eventType == 'module.postinstall') {
            // TODO Handle actions after this module is installed.

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
                        VALUES(?,?,?,?,?,?,?,?)", array($fieldid, $blockid, 'AWS Docs Settings', '', 'AWS Docs Settings', 'index.php?module=AWSDocs&view=SettingsDetail&parent=Settings', $sequence++, 0));
        } elseif ($eventType == 'module.disabled') {
            // TODO Handle actions before this module is being uninstalled.
        } elseif ($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } elseif ($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } elseif ($eventType == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }
    }
}
