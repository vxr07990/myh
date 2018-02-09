<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once('vtlib/Vtiger/Utils.php');
include_once('vtlib/Vtiger/FieldBasic.php');
require_once 'includes/runtime/Cache.php';

/**
 * Provides APIs to control vtiger CRM Field
 * @package vtlib
 */
class Vtiger_Field extends Vtiger_FieldBasic
{

    /**
     * Get unique picklist id to use
     * @access private
     */
    public function __getPicklistUniqueId()
    {
        global $adb;
        return $adb->getUniqueID('vtiger_picklist');
    }

    /**
     * Set values for picklist field (for all the roles)
     * @param Array List of values to add.
     *
     * @internal Creates picklist base if it does not exists
     */
    public function setPicklistValues($values)
    {
        global $adb,$default_charset;

        // Non-Role based picklist values
        // this probably needs to be updated, as it breaks picklists for other uitypes e.g. 3333, maybe others now too
        if ($this->uitype == '16' || $this->uitype == '3333' || $this->uitype == '1500') {
            $this->setNoRolePicklistValues($values);
            return;
        }

        $picklist_table = 'vtiger_'.$this->name;
        $picklist_idcol = $this->name.'id';
        if (!Vtiger_Utils::CheckTable($picklist_table)) {
            Vtiger_Utils::CreateTable(
                $picklist_table,
                "($picklist_idcol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				$this->name VARCHAR(200) NOT NULL,
				presence INT (1) NOT NULL DEFAULT 1,
				picklist_valueid INT NOT NULL DEFAULT 0,
                sortorderid INT DEFAULT 0)",
                true);
            $new_picklistid = $this->__getPicklistUniqueId();
            $adb->pquery("INSERT INTO vtiger_picklist (picklistid,name) VALUES(?,?)", array($new_picklistid, $this->name));
            self::log("Creating table $picklist_table ... DONE");
        } else {
            $picklistResult = $adb->pquery("SELECT picklistid FROM vtiger_picklist WHERE name=?", array($this->name));
            $new_picklistid = $adb->query_result($picklistResult, 0, 'picklistid');
        }

        $specialNameSpacedPicklists  = array(
            'opportunity_type'=>'opptypeid',
            'duration_minutes'=>'minutesid',
            'recurringtype'=>'recurringeventid'
        );

        // Fix Table ID column names
        $fieldName = (string)$this->name;
        if (in_array($fieldName.'_id', $adb->getColumnNames($picklist_table))) {
            $picklist_idcol = $fieldName.'_id';
        } elseif (array_key_exists($fieldName, $specialNameSpacedPicklists)) {
            $picklist_idcol = $specialNameSpacedPicklists[$fieldName];
        }
        // END

        // Add value to picklist now
        $sortid = 0; // TODO To be set per role
        foreach ($values as $value) {
            $new_picklistvalueid = getUniquePicklistID();
            $presence = 1; // 0 - readonly, Refer function in include/ComboUtil.php
            $new_id = $adb->getUniqueID($picklist_table);
            ++$sortid;

            $adb->pquery("INSERT INTO $picklist_table($picklist_idcol, $this->name, presence, picklist_valueid,sortorderid) VALUES(?,?,?,?,?)",
                array($new_id, $value, $presence, $new_picklistvalueid, $sortid));


            // Associate picklist values to all the role
            $adb->pquery("INSERT INTO vtiger_role2picklist(roleid, picklistvalueid, picklistid, sortid) SELECT roleid,
				$new_picklistvalueid, $new_picklistid, $sortid FROM vtiger_role", array());
        }
    }

    /**
     * Set values for picklist field (non-role based)
     * @param Array List of values to add
     *
     * @internal Creates picklist base if it does not exists
     * @access private
     */
    public function setNoRolePicklistValues($values)
    {
        global $adb;
        $pickListName_ids = array('recurring_frequency','payment_duration','sales_stage');
        $specialNameSpacedPicklists  = array(
            'opportunity_type'=>'opptypeid',
            'duration_minutes'=>'minutesid',
            'recurringtype'=>'recurringeventid'
        );
        $picklist_table = 'vtiger_'.$this->name;
        $picklist_idcol = $this->name.'id';
        if (in_array($this->name, $pickListName_ids)) {
            $picklist_idcol =  $this->name.'_id';
        } elseif (array_key_exists($this->name, $specialNameSpacedPicklists)) {
            $picklist_idcol = $specialNameSpacedPicklists[$this->name];
        }
        if (!Vtiger_Utils::CheckTable($picklist_table)) {
            Vtiger_Utils::CreateTable(
                $picklist_table,
                "($picklist_idcol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				$this->name VARCHAR(200) NOT NULL,
				sortorderid INT(11),
				presence INT (11) NOT NULL DEFAULT 1)",
                true);
            self::log("Creating table $picklist_table ... DONE");
        }

        $existingValueSet = [];
        $result = $adb->query("SELECT `$this->name` FROM `$picklist_table`");
        while($row =& $result->fetchRow()) {
            $existingValueSet[] = $row[$this->name];
        }

        $result = $adb->query("SELECT MAX(sortorderid) AS sortid FROM `$picklist_table`");

        // Add value to picklist now
        $sortid = $result->fields['sortid'] === NULL ? 0 : $result->fields['sortid'];
        foreach ($values as $value) {
            if(in_array($value, $existingValueSet)) {
                continue;
            }
            $presence = 1; // 0 - readonly, Refer function in include/ComboUtil.php
            $new_id = $adb->getUniqueId($picklist_table);
            $adb->pquery("INSERT INTO $picklist_table($picklist_idcol, $this->name, sortorderid, presence) VALUES(?,?,?,?)",
                array($new_id, $value, $sortid, $presence));

            $sortid = $sortid+1;
        }
    }

    public function setVanlineSpecificPicklistValues($values, $vanline)
    {
        if(!$vanline){
            $this->setPicklistValues($values);
        }
        global $adb;


        $picklist_table = 'vtiger_'.$this->name;
        $picklist_idcol = $this->name.'id';
        if (!Vtiger_Utils::CheckTable($picklist_table)) {
            Vtiger_Utils::CreateTable(
                $picklist_table,
                "($picklist_idcol INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				$this->name VARCHAR(200) NOT NULL,
				presence INT (1) NOT NULL DEFAULT 1,
				picklist_valueid INT NOT NULL DEFAULT 0,
				vanline VARCHAR(255),
                sortorderid INT DEFAULT 0)",
                true);
            $new_picklistid = $this->__getPicklistUniqueId();
            $adb->pquery("INSERT INTO vtiger_picklist (picklistid,name) VALUES(?,?)", array($new_picklistid, $this->name));
            self::log("Creating table $picklist_table ... DONE");
        } else {
            $picklistResult = $adb->pquery("SELECT picklistid FROM vtiger_picklist WHERE name=?", array($this->name));
            $new_picklistid = $adb->query_result($picklistResult, 0, 'picklistid');
        }


        // Add value to picklist now
        $sortid = 0; // TODO To be set per role
        foreach ($values as $value) {
            $new_picklistvalueid = getUniquePicklistID();
            $presence = 1; // 0 - readonly, Refer function in include/ComboUtil.php
            $new_id = $adb->getUniqueID($picklist_table);
            ++$sortid;

            $adb->pquery("INSERT INTO $picklist_table($picklist_idcol, $this->name, presence, picklist_valueid,sortorderid, vanline) VALUES(?,?,?,?,?,?)",
                         array($new_id, $value, $presence, $new_picklistvalueid, $sortid, $vanline));


            // Associate picklist values to all the role
            $adb->pquery("INSERT INTO vtiger_role2picklist(roleid, picklistvalueid, picklistid, sortid) SELECT roleid,
				$new_picklistvalueid, $new_picklistid, $sortid FROM vtiger_role", array());
        }
    }

    /**
     * Set special values for picklist field
     * @param Array List of existing values to set special (cant rename or delete a special value)
     * @param Boolean. True, set special value/s among all agents. False, set only default values.
     *
     * @internal Creates column 'special' if it does not exists
     * @access public
     */
    public function setPicklistSpecialValues($values, $forAllAgents = true)
    {
        if( !is_array($values) || empty($values))
        {
            return;
        }
        global $adb;
        $picklist_table = 'vtiger_'.$this->name;
        if ( ! Vtiger_Utils::CheckTable($picklist_table) ) {
            return;
        }
        if( ! Vtiger_Utils::CheckColumnExists($picklist_table, 'special')){
            $sqlquery = "ALTER TABLE `$picklist_table`  ADD COLUMN `special` INT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `sortorderid`";
            Vtiger_Utils::ExecuteQuery($sqlquery);
        }
        $queryFilter = "";
        if( ! $forAllAgents ){
            $picklistIdColumn = Vtiger_Util_Helper::getPicklistId($this->name);
            $queryFilter = " AND $picklistIdColumn NOT IN ( SELECT valueid from `vtiger_custompicklist` WHERE fieldid = '$this->name' )";
        }
        $sqlquery2 = "UPDATE `$picklist_table` SET `special`=1 WHERE $this->name IN (".generateQuestionMarks($values).") $queryFilter";
        $result = $adb->pquery($sqlquery2,array($values));
    }

    /**
     * Set special values for picklist field
     * @param Array List of existing values to set special (cant rename or delete a special value)
     * @param Boolean. Tru, unset special value/s among all agents. False, unset only default picklist values
     *
     * @internal Creates column 'special' if it does not exists
     * @access public
     */
    public function unsetPicklistSpecialValues($values, $forAllAgents = true)
    {
        if( !is_array($values) || empty($values) || $this->uitype != 1500)
        {
            return;
        }
        global $adb;
        $picklist_table = 'vtiger_'.$this->name;
        if ( ! Vtiger_Utils::CheckTable($picklist_table) ) {
            return;
        }
        if( ! Vtiger_Utils::CheckColumnExists($picklist_table, 'special')){
            $sqlquery = "ALTER TABLE `$picklist_table`  ADD COLUMN `special` INT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `sortorderid`";
            Vtiger_Utils::ExecuteQuery($sqlquery);
        }
        $queryFilter = "";
        if( ! $forAllAgents ){
            $picklistIdColumn = Vtiger_Util_Helper::getPicklistId($this->name);
            $queryFilter = " AND $picklistIdColumn NOT IN ( SELECT valueid from `vtiger_custompicklist` WHERE fieldid = '$this->name' )";
        }
        $sqlquery2 = "UPDATE `$picklist_table` SET `special`=0 WHERE $this->name IN (".generateQuestionMarks($values).") $queryFilter";
        $result = $adb->pquery($sqlquery2,array($values));
    }

    /**
     * Set relation between field and modules (UIType 10)
     * @param Array List of module names
     *
     * @internal Creates table vtiger_fieldmodulerel if it does not exists
     */
    public function setRelatedModules($moduleNames)
    {
        if(!is_array($moduleNames)){
            $moduleNames = [$moduleNames];
        }
        // We need to create core table to capture the relation between the field and modules.
        if (!Vtiger_Utils::CheckTable('vtiger_fieldmodulerel')) {
            Vtiger_Utils::CreateTable(
                'vtiger_fieldmodulerel',
                '(fieldid INT NOT NULL, module VARCHAR(100) NOT NULL, relmodule VARCHAR(100) NOT NULL, status VARCHAR(10), sequence INT)',
                true
            );
        }
        // END

        global $adb;
        foreach ($moduleNames as $relmodule) {
            $checkres = $adb->pquery('SELECT * FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=? AND relmodule=?',
                array($this->id, $this->getModuleName(), $relmodule));

            // If relation already exist continue
            if ($adb->num_rows($checkres)) {
                continue;
            }

            $adb->pquery('INSERT INTO vtiger_fieldmodulerel(fieldid, module, relmodule) VALUES(?,?,?)',
                array($this->id, $this->getModuleName(), $relmodule));

            self::log("Setting $this->name relation with $relmodule ... DONE");
        }
        return true;
    }

    /**
     * Remove relation between the field and modules (UIType 10)
     * @param Array List of module names
     */
    public function unsetRelatedModules($moduleNames)
    {
        global $adb;
        foreach ($moduleNames as $relmodule) {
            $adb->pquery('DELETE FROM vtiger_fieldmodulerel WHERE fieldid=? AND module=? AND relmodule = ?',
                array($this->id, $this->getModuleName(), $relmodule));

            Vtiger_Utils::Log("Unsetting $this->name relation with $relmodule ... DONE");
        }
        return true;
    }

    /**
     * Get Vtiger_Field instance by fieldid or fieldname
     * @param mixed fieldid or fieldname
     * @param Vtiger_Module Instance of the module if fieldname is used
     */
    public static function getInstance($value, $moduleInstance=false)
    {
        global $adb;
        $instance = false;
        $data = Vtiger_Functions::getModuleFieldInfo($moduleInstance->id, $value);
        if ($data) {
            $instance = new self();
            $instance->initialize($data, $moduleInstance);
        }
        return $instance;
    }

    /**
     * Get Vtiger_Field instances related to block
     * @param Vtiger_Block Instnace of block to use
     * @param Vtiger_Module Instance of module to which block is associated
     */
     public static function getAllForBlock($blockInstance, $moduleInstance=false)
     {
         $cache = Vtiger_Cache::getInstance();
         if ($cache->getBlockFields($blockInstance->id, $moduleInstance->id)) {
             return $cache->getBlockFields($blockInstance->id, $moduleInstance->id);
         } else {
             global $adb;
             $instances = false;
             $query = false;
             $queryParams = false;
             if ($moduleInstance) {
                 $query = "SELECT * FROM vtiger_field WHERE block=? AND tabid=? ORDER BY sequence";
                 $queryParams = array($blockInstance->id, $moduleInstance->id);
             } else {
                 $query = "SELECT * FROM vtiger_field WHERE block=? ORDER BY sequence";
                 $queryParams = array($blockInstance->id);
             }
             $result = $adb->pquery($query, $queryParams);
             for ($index = 0; $index < $adb->num_rows($result); ++$index) {
                 $instance = new self();
                 $instance->initialize($adb->fetch_array($result), $moduleInstance, $blockInstance);
                 $instances[] = $instance;
             }
             $cache->setBlockFields($blockInstance->id, $moduleInstance->id, $instances);
             return $instances;
         }
     }

    /**
     * Get Vtiger_Field instances related to module
     * @param Vtiger_Module Instance of module to use
     */
    public static function getAllForModule($moduleInstance)
    {
        global $adb;
        $instances = false;

        $query = "SELECT * FROM vtiger_field WHERE tabid=? ORDER BY sequence";
        $queryParams = array($moduleInstance->id);

        $result = $adb->pquery($query, $queryParams);
        for ($index = 0; $index < $adb->num_rows($result); ++$index) {
            $instance = new self();
            $instance->initialize($adb->fetch_array($result), $moduleInstance);
            $instances[] = $instance;
        }
        return $instances;
    }

    /**
     * Delete fields associated with the module
     * @param Vtiger_Module Instance of module
     * @access private
     */
    public static function deleteForModule($moduleInstance)
    {
        global $adb;
        $adb->pquery("DELETE FROM vtiger_field WHERE tabid=?", array($moduleInstance->id));
        self::log("Deleting fields of the module ... DONE");
    }
}
