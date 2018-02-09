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

class TariffSections extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_tariffsections';
    public $table_index= 'tariffsectionsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_tariffsectionscf', 'tariffsectionsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_tariffsections', 'vtiger_tariffsectionscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_tariffsections' => 'tariffsectionsid',
        'vtiger_tariffsectionscf'=>'tariffsectionsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Section Name' => array('tariffsections', 'section_name'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Section Name' => 'section_name',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'section_name';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Section Name' => array('tariffsections', 'section_name'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Section Name' => 'section_name',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('section_name');

    // For Alphabetical search
    public $def_basicsearch_col = 'section_name';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'section_name';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('section_name','assigned_user_id');

    public $default_order_by = 'section_name';
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
    }

    public function saveentity($module, $fileid = '')
    {
        if ($_REQUEST['repeat'] === true) {
            return;
        }
        //does things twice, this stops it.
        $_REQUEST['repeat'] = true;

        //Check and assign owner if tariff is not set to admin_access
        $db = PearDatabase::getInstance();
        $sql = "SELECT agentid, admin_access FROM `vtiger_tariffs` JOIN `vtiger_crmentity` ON tariffsid=crmid WHERE crmid=?";
        $result = $db->pquery($sql, [$this->column_fields['related_tariff']]);
        $row = $result->fetchRow();
        if ($row != null && $row['admin_access'] != 1) {
            $this->column_fields['agentid'] = $row['agentid'];
        }
        //@NOTE: Server side validation because everything being JS only goes so far.
        if(empty($_REQUEST['tariffsection_sortorder']) || $_REQUEST['tariffsection_sortorder'] < 1) {
            $_REQUEST['tariffsection_sortorder'] = 1;
            $this->column_fields['tariffsection_sortorder'] = 1;
        }

        parent::saveentity($module, $fileid);
        $columns = array_merge($this->column_fields, $_REQUEST);
        if (empty($columns['record'])) {
            $columns['record'] = $columns['currentid'];
        }

        $name = $columns['section_name'];
        $sql = "SELECT service_no FROM `vtiger_service` WHERE servicename = ?";
        $result = $db->pquery($sql, array($name));
        $row = $result->fetchRow();
        if (empty($row)) {
            $this->createService($columns['section_name']);
        }
    }

    protected function createService($name)
    {
        $db = PearDatabase::getInstance();

        try {
            $user = new Users();
            $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
            $data = array(
                'servicename'=>$name,
                'assigned_user_id'=>'19x1',
                'qty_per_unit'=>0,
                'unit_price'=>0,
                'discontinued'=>1,
                'currency_id'=>1,
                'commissionrate'=>0);
            $service = vtws_create('Services', $data, $current_user);
            $wsid = $service['id'];
            $crmid = explode('x', $wsid)[1];
            $sql = "INSERT INTO `vtiger_producttaxrel` (productid,taxid,taxpercentage) VALUES (?,?,?)";
            for ($i = 1; $i <= 3; $i++) {
                $result = $db->pquery($sql, array($crmid, $i, 0));
            }
        } catch (WebServiceException $ex) {
            file_put_contents('logs/failedServiceCreates.log', date('Y-m-d H:i:s - ').print_r($ex)."\n", FILE_APPEND);
            echo $ex->getMessage();
        }
    }
}
