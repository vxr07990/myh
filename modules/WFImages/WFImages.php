<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

class WFImages extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_wfimages';
    public $table_index= 'wfimagesid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_wfimagescf', 'wfimagesid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_wfimages', 'vtiger_wfimagescf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_wfimages' => 'wfimagesid',
        'vtiger_wfimagescf'=>'wfimagesid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_WFIMAGES_IMAGENAME' => array('wfimages', 'imagename'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_WFIMAGES_IMAGENAME' => 'imagename',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'imagename';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_WFIMAGES_IMAGENAME' => array('wfimages', 'imagename'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_WFIMAGES_IMAGENAME' => 'imagename',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('imagename');

    // For Alphabetical search
    public $def_basicsearch_col = 'imagename';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'imagename';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('imagename','assigned_user_id');

    public $default_order_by = 'imagename';
    public $default_sort_order='ASC';

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

    function save_module($module)
    {
        $this->insertIntoAttachment($this->id,$module);
    }
    function insertIntoAttachment($id,$module)
    {
        global $log, $adb,$upload_badext;
        $log->debug("Entering into insertIntoAttachment($id,$module) method.");

        $file_saved = false;
        //This is to added to store the existing attachment id of the contact where we should delete this when we give new image
        $old_attachmentid = $adb->query_result($adb->pquery("select vtiger_crmentity.crmid from vtiger_seattachmentsrel inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_seattachmentsrel.attachmentsid where  vtiger_seattachmentsrel.crmid=?", array($id)),0,'crmid');
        $result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
        $_FILES = $result['imagename'];
        foreach($_FILES as $fileindex => $files)
        {
            if($files['name'] != '' && $files['size'] > 0)
            {
                $files['expected_attachment_type'] = 'Image';
                $files['original_name'] = vtlib_purify($_REQUEST[$fileindex.'_hidden']);
                $file_saved = $this->uploadAndSaveFile($id,$module,$files);
            }
        }

        $imageNameSql = 'SELECT name FROM vtiger_seattachmentsrel
                          INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
                          LEFT JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_seattachmentsrel.crmid
                          WHERE vtiger_seattachmentsrel.crmid = ?';
        $imageNameResult = $adb->pquery($imageNameSql,array($id));
        $imageName = decode_html($adb->query_result($imageNameResult, 0, "name"));
        //Inserting image information of record into base table
        $adb->pquery('UPDATE vtiger_wfimages SET imagename = ? WHERE wfimagesid = ?',array($imageName,$id));
        //This is to handle the delete image for contacts
        if($file_saved)
        {
            if($old_attachmentid != '')
            {
                $setype = $adb->query_result($adb->pquery("select setype from vtiger_crmentity where crmid=?", array($old_attachmentid)),0,'setype');
                if($setype == 'WFImages Attachment')
                {
                    $del_res1 = $adb->pquery("delete from vtiger_attachments where attachmentsid=?", array($old_attachmentid));
                    $del_res2 = $adb->pquery("delete from vtiger_seattachmentsrel where attachmentsid=?", array($old_attachmentid));
                }
            }
        }

        $log->debug("Exiting from insertIntoAttachment($id,$module) method.");
    }
}
