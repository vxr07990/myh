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

class ExtraStops extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_extrastops';
    public $table_index= 'extrastopsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_extrastopscf', 'extrastopsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_extrastops', 'vtiger_extrastopscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_extrastops' => 'extrastopsid',
        'vtiger_extrastopscf'=>'extrastopsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_EXTRASTOPS_ID' => array('extrastops', 'extrastops_id'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_EXTRASTOPS_ID' => 'extrastops_id',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'extrastops_id';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_EXTRASTOPS_ID' => array('extrastops', 'extrastops_id'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_EXTRASTOPS_ID' => 'extrastops_id',
        'Assigned To' => 'assigned_user_id',
    );
    // For Popup window record selection
    public $popup_fields = array('extrastops_id');

    // For Alphabetical search
    public $def_basicsearch_col = 'extrastops_id';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'extrastops_id';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('extrastops_id','assigned_user_id');

    public $default_order_by = 'extrastops_id';
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

    public function save_module()
    {
        //saveentity needs this
    }

    public function saveentity($module, $fileid = '')
    {
        //custom save logic
        parent::saveentity($module, $fileid);
        $pseudoSave = $_REQUEST['pseudoSave'];
        if ($pseudoSave) {
            $tablePrefix = session_id() . '_';
        } else {
            $tablePrefix = '';
        }

        // sort out the packing data from the request for each stop
        global $_extraStopPreSave;
        if (!$_extraStopPreSave) {
            $stopPackingData       = [];
            $notRelevantFieldNames = ['cost_packing_total',
                                      'cost_unpacking_total',
                                      'packRate',
                                      'unpackRate',
                                      'full_pack',
                                      'full_unpack',
                                      'packing_disc',
                                      'apply_full_pack_rate_override',
                                      'full_pack_rate_override',
                                      'apply_custom_pack_rate_override',
                                      'UnpackingCost',
                                      'packQty',
                                      'unpackQty',
                                      'crateUnpack',
                                      'crateOTUnpack',
                                      '',
                                      'crateUnpack',
                                      'crateOTUnpack',
            ];
            foreach ($_REQUEST as $fieldName => $value) {
                if (strpos($fieldName, 'pack') === false) {
                    continue;
                }
                preg_match('/\d/', $fieldName, $m, PREG_OFFSET_CAPTURE);
                $itemType = substr($fieldName, 0, $m[0][1]);
                if (in_array($itemType, $notRelevantFieldNames)) {
                    continue;
                }
                preg_match('/\d/', $fieldName, $m, PREG_OFFSET_CAPTURE);
                $itemId   = substr($fieldName, $m[0][1]);
                $itemType = substr($fieldName, 0, $m[0][1]);
                if (strpos($itemId, '_') === false) {
                    continue;
                }
                $info = explode('_', $itemId);
                // stop number, item id, item type
                $stopPackingData[$info[0]][$info[1]][$itemType] = $value;
            }
            //$defaultLabels     = $this->getPackingLabels($vanlineId, $tariffForWebService);
            $_extraStopPreSave = $stopPackingData;
        } else {
            $stopPackingData = $_extraStopPreSave;
        }

        $estimateID = $this->column_fields['extrastops_relcrmid'];
        $labels = ExtraStops_Module_Model::getDefaultPackingItems($estimateID);

        $id = $this->id;
        global $_currentGuestRecordIndex;
        $i = $_currentGuestRecordIndex;
        $db = PearDatabase::getInstance();
        if ($id && $stopPackingData[$i]) {
            foreach ($stopPackingData[$i] as $itemId => $values) {
                foreach ($values as $itemType => $num) {
                    if ($itemType == 'containers_pack') {
                        $itemType = 'containers';
                    } else {
                        $itemType .= '_qty';
                    }
                    $sql    = "SELECT * FROM `".$tablePrefix."packing_items_extrastops` WHERE stopid=? AND itemid=?";
                    $params = [$id, $itemId];
                    $result = $db->pquery($sql, $params);
                    $row = $result->fetchRow();
                    if ($row == null) {
                        if ($num == 0) {
                            continue;
                        }
                        $sql      =
                            "INSERT INTO `".$tablePrefix."packing_items_extrastops` (stopid, itemid, ".$itemType.
                            ", label) VALUES (?,?,?,?)";
                        $params = [$id, $itemId, $num, $labels[$itemId]];
                        $db->pquery($sql, $params);
                    } else {
                        $sql = "UPDATE `".$tablePrefix."packing_items_extrastops` SET ".$itemType.
                               "=? WHERE stopid=? AND itemid=?";
                        $params = [$num, $id, $itemId];
                        $db->pquery($sql, $params);
                    }
                }
            }
        }
    }
}
