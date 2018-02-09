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
include_once 'modules/Accounts/Accounts.php';

class Contracts extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_contracts';
    public $table_index= 'contractsid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_contractscf', 'contractsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_contracts', 'vtiger_contractscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_contracts' => 'contractsid',
        'vtiger_contractscf'=>'contractsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Contract Number' => array('contracts', 'contract_no'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'Contract Number' => 'contract_no',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'contract_no';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Contract Number' => array('contracts', 'contract_no'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'Contract Number' => 'contract_no',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('contract_no');

    // For Alphabetical search
    public $def_basicsearch_col = 'contract_no';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'contract_no';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('contract_no','assigned_user_id');

    public $default_order_by = 'contract_no';
    public $default_sort_order='ASC';

    public function save_module()
    {
        //custom save
    }

    public function saveentity($module, $fileid = '')
    {
        /* $columns = $this->column_fields;
        $request = $_REQUEST;

        $recordId = $this->id;
        $db = PearDatabase::getInstance(); */
        //if($_REQUEST['repeat'] === true){
        //	return;
        ///}
        //does things twice, this stops it.
        //$_REQUEST['repeat'] = true;
        parent::saveentity($module, $fileid);
        $db = PearDatabase::getInstance();
        $request = array_merge($_REQUEST, $this->column_fields);
        if (empty($request['record'])) {
            if (!empty($request['currentid'])) {
                $request['record'] = $request['currentid'];
            } else {
                //NO this.
                //$sql    = "SELECT id FROM `vtiger_crmentity_seq`";
                //$result = $db->pquery($sql, []);
                //$row    = $result->fetchRow();
                //$request['record'] = $row[0]++;
                //$sql = "UPDATE `vtiger_crmentity_seq` SET id = ?";
                //$db->pquery($sql, [$request['record']]);
                $fieldList['record'] = $this->id;
            }
        }
        //file_put_contents('logs/devLog.log', "\n FieldList : ".print_r($request, true), FILE_APPEND);
        $recordId     = $request['record'];
        if (!$recordId) {
            //throw an error if we're here and recordId is not something.
            throw new Exception(vtranslate('LBL_RECORD_NOT_FOUND'), -1);
        }
        //file_put_contents('logs/devLog.log', "\n COLUMNS/REQUEST: ".print_r($request, true), FILE_APPEND);
        /*-----------------------Save annual rate increases----------------------------*/
        $annualRateTotal = $request['numAnnualRate'];
        //file_put_contents('logs/devLog.log', "\n ANN RATE TOTAL: ".$annualRateTotal, FILE_APPEND);
        for ($annualCount = 1; $annualCount <= $annualRateTotal; $annualCount++) {
            $annualRateId = $request['annualRateId'.$annualCount];
            $deleted = $request['annualRateDeleted'.$annualCount];
            if ($deleted != 'DELETE') {
                if ($request['annual_rate_increase'.$annualCount] == '' && $request['annual_rate_date'.$annualCount] == '') {
                    continue;
                }
                $date = $request['annual_rate_date'.$annualCount];
                if ($annualRateId != '0' && $annualRateId != 0) {
                    $result = $db->pquery('UPDATE `vtiger_annual_rate` SET date = ?, rate = ?, contractid = ? WHERE annualrateid = ?', array($date, $request['annual_rate_increase'.$annualCount], $recordId, $request['annualRateId'.$annualCount]));
                } else {
                    $result = $db->pquery('SELECT id from `vtiger_annual_rate_seq`', array());
                    $row = $result->fetchRow();
                    if ($row[0]) {
                        $annualId = $row[0];
                    } else {
                        $annualId = 0;
                        $result = $db->pquery('INSERT INTO `vtiger_annual_rate_seq` SET id = ?', array(0));
                    }
                    $annualId++;
                    $result = $db->pquery('UPDATE `vtiger_annual_rate_seq` SET id = ?', array($annualId));
                    $result = $db->pquery('INSERT INTO `vtiger_annual_rate` (annualrateid, date, rate, contractid) VALUES (?,?,?,?)', array($annualId, $date, $request['annual_rate_increase'.$annualCount], $recordId));
                }
                //file_put_contents('logs/devLog.log', "\n annual rate #$annualCount - ".print_r(array($date, $request['annual_rate_increase'.$annualCount], $annualId, $recordId), true), FILE_APPEND);
            } else {
                if ($annualRateId != '') {
                    //unassociate deleted row from contract
                    $db->pquery('UPDATE `vtiger_annual_rate` SET contractid = NULL WHERE annualrateid = ? AND contractid = ?', array($request['annualRateId'.$annualCount], $recordId));
                    //clear annual rates unassociated with any contracts or estimates
                    $db->pquery('DELETE FROM `vtiger_annual_rate` WHERE annualrateid = ? AND contractid IS NULL AND accountid IS NULL', array($request['annualRateId'.$annualCount]));
                }
            }
        }
        /*-----------------------End annual rate increases----------------------------*/
        /*-----------------------Save misc items---------------------------------*/
        $miscTotal = $request['numMisc'];
        for ($miscCount = 0; $miscCount <= $miscTotal; $miscCount++) {
            $miscId = $request['MiscId-'.$miscCount];
            //file_put_contents('logs/devLog.log', "\n miscId: $miscId", FILE_APPEND);
            $miscFlatOrQty = $request['MiscFlatChargeOrQtyRate-'.$miscCount];
            //file_put_contents('logs/devLog.log', "\n miscFlatOrQty: $miscFlatOrQty", FILE_APPEND);
            $miscDescription = $request['MiscDescription-'.$miscCount];
            //file_put_contents('logs/devLog.log', "\n miscDescription: $miscDescription", FILE_APPEND);
            $miscRate = $request['MiscRate-'.$miscCount];
            //file_put_contents('logs/devLog.log', "\n miscRate: $miscRate", FILE_APPEND);
            $miscQty = $request['MiscQty-'.$miscCount];
            //file_put_contents('logs/devLog.log', "\n miscQty: $miscQty", FILE_APPEND);
            $miscDiscounted = $request['MiscDiscounted-'.$miscCount];
            //file_put_contents('logs/devLog.log', "\n miscDiscounted: $miscDiscounted", FILE_APPEND);
            $miscDiscount = $request['MiscDiscount-'.$miscCount];
            //file_put_contents('logs/devLog.log', "\n MiscDiscount: $MiscDiscount", FILE_APPEND);
            if ($miscDescription == '' || $miscRate == '') {
                continue;
            }
            if ($miscId == "none") { //Save a new entry
                $sql = "INSERT INTO `vtiger_contracts_misc_items` (`contractsid`, `is_quantity_rate`, `description`, `rate`, `quantity`, `discounted`, `discount`) VALUES (?,?,?,?,?,?,?)";
                $result = $db->pquery($sql, array(
                                            $recordId,
                                            $miscFlatOrQty,
                                            $miscDescription,
                                            $miscRate,
                                            $miscQty,
                                            $miscDiscounted,
                                            $miscDiscount
                                        ));
            } else { //Update existing entry
                $sql = "UPDATE `vtiger_contracts_misc_items` SET `is_quantity_rate` = ?, `description` = ?, `rate` = ?, `quantity` = ?, `discounted` = ?, `discount` = ? WHERE `contracts_misc_id` = ?";
                $result = $db->pquery($sql, array(
                                                $miscFlatOrQty,
                                                $miscDescription,
                                                $miscRate,
                                                $miscQty,
                                                $miscDiscounted,
                                                $miscDiscount,
                                                $miscId
                                            ));
            }
        }
        /*-----------------------End misc items---------------------------------*/
        /*-----------------------Save tabled fuel----------------------------*/
        $fuelType = $request['fuel_surcharge_type'];
        $fuelTotal = $request['numFuel'];
        //don't need special row saving logic if it's a static percent
        if ($fuelType != 'Static Fuel Percentage') {
            for ($fuelCount = 0; $fuelCount <= $fuelTotal; $fuelCount++) {
                //grab row's values
                $fuelId = $request['FuelId-'.$fuelCount];
                $fromCost = $request['FuelTableFromCost-'.$fuelCount];
                $toCost = $request['FuelTableToCost-'.$fuelCount];
                $fuelPercent = $request['FuelTablePercent-'.$fuelCount];
                $fuelRate = $request['FuelTableRate-'.$fuelCount];
                //dump incomplete rows
                if ($fromCost == '' || $toCost == '') {
                    continue;
                }
                //if it's a new row
                if ($fuelId == '') {
                    //insert row
                    $result = $db->pquery('INSERT INTO `vtiger_contractfuel` (contractid, from_cost, to_cost, rate, percentage) VALUES (?,?,?,?,?)', array($recordId, $fromCost, $toCost, $fuelRate, $fuelPercent));
                } else {
                    //update existing row
                    $result = $db->pquery('UPDATE `vtiger_contractfuel` SET contractid = ?, from_cost = ?, to_cost = ?, rate = ?, percentage = ? WHERE line_item_id = ?', array($recordId, $fromCost, $toCost, $fuelRate, $fuelPercent, $fuelId));
                }
            }
        }
        /*-----------------------End tabled fuel----------------------------*/

        if (getenv('INSTANCE_NAME') != 'sirva') {
            /*-----------------------Save tabled Flat Auto rate----------------------------*/
            //don't need special row saving logic if it's a static percent
            if ($request['flat_rate_auto'] == 'on') {
                $autoTotal = $request['numFlatRateAuto'];
                for ($autoCount = 0; $autoCount <= $autoTotal; $autoCount++) {
                    //grab row's values
                    $autoId = $request['FlatRateAutoTableId-'.$autoCount];
                    $fromMileage = $request['FlatRateAutoTableFromMileage-'.$autoCount];
                    $toMileage = $request['FlatRateAutoTableToMileage-'.$autoCount];
                    $autoRate = $request['FlatRateAutoTableRate-'.$autoCount];
                    $autoDisc = $request['FlatRateAutoTableDiscount-'.$autoCount];
                    
                    //dump incomplete rows
                    if ($fromMileage == '' || $toMileage == '') {
                        continue;
                    }
                    
                    //if it's a new row
                    if ($autoId == 'none') {
                        //insert row
                        $insertStmt = 'INSERT INTO `vtiger_contract_flat_rate_auto`
									(contractid, from_mileage, to_mileage, rate, discount) VALUES 
									(?,?,?,?,?)';
                        $db->pquery($insertStmt, [$recordId, $fromMileage, $toMileage, $autoRate, $autoDisc]);
                    } else {
                        //update existing row
                        $updateStmt = 'UPDATE `vtiger_contract_flat_rate_auto` SET '
                                      .' contractid = ?,'
                                      .' from_mileage = ?,'
                                      .' to_mileage = ?,'
                                      .' rate = ?,'
                                      .' discount = ?'
                                      .' WHERE line_item_id = ?';
                        $db->pquery($updateStmt, [$recordId, $fromMileage, $toMileage, $autoRate, $autoDisc, $autoId]);
                    }
                }
            }
            /*-----------------------End tabled Flat Auto rate----------------------------*/
        }
        
        /*-----------------------Webservice Save----------------------------*/
        if ($_REQUEST['isWebserviceCreate']) {
            $accountIds = (array) $request['account_id'];
            $contractId = $request['parent_contract'];

            $accountFocus = new Accounts();
            $contractFocus = new Contracts();

            foreach ($accountIds as $accountId) {
                if ($accountId != '') {
                    $accountFocus->save_related_module('Accounts', $accountId, 'Contracts', $recordId);
                }
            }

            if ($contractId != '') {
                $contractFocus->save_related_module('Contracts', $contractId, 'Contracts', $recordId);
            }
        }
        /*---------------------End Webservice Save--------------------------*/
        file_put_contents('logs/devLog.log', "\n end of contracts custom save", FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n $module", FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n $fileid", FILE_APPEND);
        file_put_contents('logs/devLog.log', "\n $recordId", FILE_APPEND);
    }

    /**
     * Retrieve custom record information of the module
     * @param <Integer> $record - crmid of record
     */
    public function retrieve($record)
    {
        global $adb;
        $fieldList = [];
        //Annual Rate
        $sql = "SELECT * FROM `vtiger_annual_rate` WHERE `contractid` =?";
        $result = $adb->pquery($sql, [$record]);

        if ($adb->num_rows($result) > 0) {
            while ($row =& $result->fetchRow()) {
                isset($fieldList['numAnnualRate']) ? $fieldList['numAnnualRate']++ : $fieldList['numAnnualRate'] = 1;

                $fieldList['annual_rate_increase' . $fieldList['numAnnualRate']] = $row['rate'];
                $fieldList['annual_rate_date'     . $fieldList['numAnnualRate']] = $row['date'];
                $fieldList['annualRateId'         . $fieldList['numAnnualRate']] = $row['annualrateid'];
            }
        }



        if (getenv('INSTANCE_NAME') != 'sirva') {
            //Flat Rate Auto table
            $sql    = "SELECT * FROM `vtiger_contract_flat_rate_auto` WHERE `contractid` =?";
            $result = $adb->pquery($sql, [$record]);
            if ($adb->num_rows($result) > 0) {
                while ($row =& $result->fetchRow()) {
                    isset($fieldList['numFlatRateAuto'])?$fieldList['numFlatRateAuto']++:$fieldList['numFlatRateAuto'] = 1;
                    $fieldList['FlatRateAutoTableId-'.$fieldList['numFlatRateAuto']]          = $row['line_item_id'];
                    $fieldList['FlatRateAutoTableFromMileage-'.$fieldList['numFlatRateAuto']] = $row['from_mileage'];
                    $fieldList['FlatRateAutoTableToMileage-'.$fieldList['numFlatRateAuto']]   = $row['to_mileage'];
                    $fieldList['FlatRateAutoTableRate-'.$fieldList['numFlatRateAuto']]        = $row['rate'];
                    $fieldList['FlatRateAutoTableDiscount-'.$fieldList['numFlatRateAuto']]    = $row['discount'];
                }
            }
        }

        return $fieldList;
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
