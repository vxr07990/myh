<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once 'includes/runtime/Cache.php';
include_once('vtlib/Vtiger/ModuleBasic.php');
/**
 * Provides API to work with vtiger CRM Modules
 * @package vtlib
 */
class Vtiger_Module extends Vtiger_ModuleBasic
{

        /**
     * Function to get the Module/Tab id
     * @return <Number>
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get unique id for related list
     * @access private
     */
    public function __getRelatedListUniqueId()
    {
        global $adb;
        return $adb->getUniqueID('vtiger_relatedlists');
    }

    /**
     * Get related list sequence to use
     * @access private
     */
    public function __getNextRelatedListSequence()
    {
        global $adb;
        $max_sequence = 0;
        $result = $adb->pquery("SELECT max(sequence) as maxsequence FROM vtiger_relatedlists WHERE tabid=?", array($this->id));
        if ($adb->num_rows($result)) {
            $max_sequence = $adb->query_result($result, 0, 'maxsequence');
        }
        return ++$max_sequence;
    }

    /**
     * Set related list information between other module
     * @param Vtiger_Module Instance of target module with which relation should be setup
     * @param String Label to display in related list (default is target module name)
     * @param Array List of action button to show ('ADD', 'SELECT')
     * @param String Callback function name of this module to use as handler
     *
     * @internal Creates table vtiger_crmentityrel if it does not exists
     */
    public function setRelatedList($moduleInstance, $label='', $actions=false, $function_name='get_related_list', $sequence=NULL)
    {
        global $adb;

        if (empty($moduleInstance)) {
            return;
        }

        if (!Vtiger_Utils::CheckTable('vtiger_crmentityrel')) {
            Vtiger_Utils::CreateTable(
                'vtiger_crmentityrel',
                '(crmid INT NOT NULL, module VARCHAR(100) NOT NULL, relcrmid INT NOT NULL, relmodule VARCHAR(100) NOT NULL)',
                true
            );
        }

        $relation_id = $this->__getRelatedListUniqueId();
        if($sequence == NULL) {
            $sequence = $this->__getNextRelatedListSequence();
        } else {
            $this->__adjustRelatedListSequences($sequence, $this->id, $moduleInstance->id, $function_name);
        }
        $presence = 0; // 0 - Enabled, 1 - Disabled

        if (empty($label)) {
            $label = $moduleInstance->name;
        }

        // Allow ADD action of other module records (default)
        if ($actions === false) {
            $actions = array('ADD');
        }

        $useactions_text = $actions;
        if (is_array($actions)) {
            $useactions_text = implode(',', $actions);
        }
        $useactions_text = strtoupper($useactions_text);

        // Add column to vtiger_relatedlists to save extended actions
        Vtiger_Utils::AddColumn('vtiger_relatedlists', 'actions', 'VARCHAR(50)');

        $adb->pquery("INSERT INTO vtiger_relatedlists(relation_id,tabid,related_tabid,name,sequence,label,presence,actions) VALUES(?,?,?,?,?,?,?,?)",
            array($relation_id, $this->id, $moduleInstance->id, $function_name, $sequence, $label, $presence, $useactions_text));

        self::log("Setting relation with $moduleInstance->name [$useactions_text] ... DONE");
    }

    /**
     * Function to recurse through related lists and increment sequence values where necessary to prevent duplicates
     * @param $sequence Integer sequence to check for duplicates
     * @param $tabid Tabid of the parent module
     * @param $related_tabid Tabid of the related module
     */
    private function __adjustRelatedListSequences($sequence, $tabid) {
        global $adb;
        $sql = "SELECT `relation_id` FROM `vtiger_relatedlists` WHERE `tabid`=? AND `sequence`=? ORDER BY `relation_id` DESC";
        $result = $adb->pquery($sql, [$tabid, $sequence]);
        while($result && $row =& $result->fetchRow()) {
            $this->__adjustRelatedListSequences($sequence+1, $tabid);
            $sql = "UPDATE `vtiger_relatedlists` SET `sequence`=`sequence`+1 WHERE `relation_id`=?";
            $adb->pquery($sql, [$row['relation_id']]);
        }
    }

    /**
     * Unset related list information that exists with other module
     * @param String hostModule module that will be hosting the guest block
     * @param String guestModule module to source the guest block from
     * @param Array guestBlocks array of blocks to be hosted
     */
    public function setGuestBlocks($guestModule, $guestBlocks, $afterBlock = null)
    {
        $hostModule = $this->name;
        //check for a link column
        $db = PearDatabase::getInstance();
        $linkColumn = $db->pquery("SELECT fieldname FROM `vtiger_field` INNER JOIN `vtiger_fieldmodulerel` ON `vtiger_field`.fieldid = `vtiger_fieldmodulerel`.fieldid WHERE module = ? AND relmodule = ?", [$guestModule, $hostModule])->fetchRow()['fieldname'];
        if (!$linkColumn && !empty($guestBlocks)) {
            $guestModuleModel = Vtiger_Module::getInstance($guestModule);
            $guestPrimaryBlock = Vtiger_Block::getInstance($guestBlocks[0], $guestModuleModel);
            //file_put_contents('logs/devLog.log', "\n GuestPrimaryBlock : ".print_r($guestPrimaryBlock, true), FILE_APPEND);
            $linkField = new Vtiger_Field();
            $linkField->label = 'LBL_' . strtoupper($guestModule) . '_' . strtoupper($hostModule) . '_AUTOGENERATEDLINK';
            $linkField->name = $guestModule . '_' . $hostModule . '_autogeneratedlink';
            $linkField->table = $guestModuleModel->basetable;
            $linkField->column = $guestModule . '_' . $hostModule . '_autogeneratedlink';
            $linkField->columntype = 'VARCHAR(255)';
            $linkField->uitype = 10;
            $linkField->displaytype = 3;
            $linkField->typeofdata = 'V~O';
            $guestPrimaryBlock->addField($linkField);
            $linkField->setRelatedModules(array($hostModule));
            //EMERGENCY QUIET-FAIL BACKUP
            //return;
        }
        //for each guest block insert/update
        foreach ($guestBlocks as $block) {
            $blockId = false;
            //handle multiple types block of input
            //strings for labels
            //int for blockids
            //objects for block instances
            if (is_string($block)) {
                $blockId = $db->pquery("
					SELECT `vtiger_blocks`.blockid FROM `vtiger_blocks` 
					INNER JOIN `vtiger_tab` ON `vtiger_blocks`.tabid = `vtiger_tab`.tabid 
					WHERE `vtiger_blocks`.blocklabel = ? AND `vtiger_tab`.name = ?", [$block, $guestModule])->fetchRow()['blockid'];
            } elseif (is_int($block)) {
                $blockId = $block;
            } elseif (is_object($block)) {
                $blockId = $block->id;
            }
            //file_put_contents('logs/devLog.log', "\n guestBlock Info : ".print_r(['blockid' => $blockId, 'hostModule' => $hostModule, 'guestModule' => $guestModule], true), FILE_APPEND);
            if ($blockId) {
                $existingRelation = $db->pquery("
					SELECT active FROM `vtiger_guestmodulerel` 
					WHERE hostmodule = ? AND guestmodule = ? AND blockid = ?", [$hostModule, $guestModule, $blockId])->fetchRow()['active'];
                //file_put_contents('logs/devLog.log', "\n existingRelation: $existingRelation", FILE_APPEND);
                if ($existingRelation === '0') {
                    file_put_contents('logs/devLog.log', "\n exists, updating", FILE_APPEND);
                    //if block id is found and relation is inactive: reactivate
                    $sql = 'UPDATE `vtiger_guestmodulerel` SET active = 1 WHERE hostmodule = ? AND guestmodule = ? AND blockid = ?';
                    $params = [$hostModule, $guestModule, $blockId];
                    if($afterBlock)
                    {
                        $sql .= 'AND after_block = ?';
                        $params[] = $afterBlock;
                    }
                    $db->pquery($sql, $params);
                } elseif ($existingRelation == null) {
                    file_put_contents('logs/devLog.log', "\n doesnt exist, inserting", FILE_APPEND);
                    $params = [$hostModule, $guestModule, $blockId];
                    if($afterBlock)
                    {
                        $add1 = ', after_block';
                        $add2 = ', ?';
                        $params[] = $afterBlock;
                    } else {
                        $add1 = '';
                        $add2 = '';
                    }
                    //if block id is found and relation doesn't already exist: insert relation
                    $db->pquery("INSERT INTO `vtiger_guestmodulerel` (hostmodule, guestmodule, blockid, active{$add1}) VALUES (?, ?, ?, 1{$add2})", $params);
                }
            }
        }
    }

    public function unsetGuestBlocks($guestModule, $guestBlocks)
    {
        $hostModule = $this->name;
        $db         = PearDatabase::getInstance();
        //for each guest block insert/update
        foreach ($guestBlocks as $block) {
            $blockId = false;
            //handle multiple types block of input
            //strings for labels
            //int for blockids
            //objects for block instances
            if (is_string($block)) {
                $blockId = $db->pquery("
					SELECT `vtiger_blocks`.blockid FROM `vtiger_blocks` 
					INNER JOIN `vtiger_tab` ON `vtiger_blocks`.tabid = `vtiger_tab`.tabid 
					WHERE `vtiger_blocks`.blocklabel = ? AND `vtiger_tab`.name = ?", [$block, $guestModule])->fetchRow()['blockid'];
            } elseif (is_int($block)) {
                $blockId = $block;
            } elseif (is_object($block)) {
                $blockId = $block->id;
            }
            //file_put_contents('logs/devLog.log', "\n guestBlock Info : ".print_r(['blockid' => $blockId, 'hostModule' => $hostModule, 'guestModule' => $guestModule], true), FILE_APPEND);
            if ($blockId) {
                $existingRelation = $db->pquery("
					SELECT active FROM `vtiger_guestmodulerel` 
					WHERE hostmodule = ? AND guestmodule = ? AND blockid = ?", [$hostModule, $guestModule, $blockId])->fetchRow()['active'];
                //file_put_contents('logs/devLog.log', "\n existingRelation: $existingRelation", FILE_APPEND);
                if ($existingRelation !== 0) {
                    //if block id is found and relation is active: deactivate
                    //file_put_contents('logs/devLog.log', "\n deactivating", FILE_APPEND);
                    $db->pquery("UPDATE `vtiger_guestmodulerel` SET active = 0 WHERE hostmodule = ? AND guestmodule = ? AND blockid = ?", [$hostModule, $guestModule, $blockId]);
                }
            }
            //remove link field from vtiger field
            $row = $db->pquery("SELECT fieldname, `vtiger_field`.fieldid FROM `vtiger_field` INNER JOIN `vtiger_fieldmodulerel` ON `vtiger_field`.fieldid = `vtiger_fieldmodulerel`.fieldid WHERE module = ? AND relmodule = ?", [$guestModule, $hostModule])->fetchRow();
            if ($row['fieldname'] == $guestModule . '_' . $hostModule . '_autogeneratedlink') {
                $db->pquery('DELETE FROM `vtiger_field` WHERE fieldid = ?', [$row['fieldid']]);
            }
        }
    }

    /**
     * Unset related list information that exists with other module
     * @param Vtiger_Module Instance of target module with which relation should be setup
     * @param String Label to display in related list (default is target module name)
     * @param String Callback function name of this module to use as handler
     */
    public function unsetRelatedList($moduleInstance, $label='', $function_name='get_related_list')
    {
        global $adb;

        if (empty($moduleInstance)) {
            return;
        }

        if (empty($label)) {
            $label = $moduleInstance->name;
        }

        $adb->pquery("DELETE FROM vtiger_relatedlists WHERE tabid=? AND related_tabid=? AND name=? AND label=?",
            array($this->id, $moduleInstance->id, $function_name, $label));

        self::log("Unsetting relation with $moduleInstance->name ... DONE");
    }

    /**
     * Add custom link for a module page
     * @param String Type can be like 'DETAILVIEW', 'LISTVIEW' etc..
     * @param String Label to use for display
     * @param String HREF value to use for generated link
     * @param String Path to the image file (relative or absolute)
     * @param Integer Sequence of appearance
     *
     * NOTE: $url can have variables like $MODULE (module for which link is associated),
     * $RECORD (record on which link is dispalyed)
     */
    public function addLink($type, $label, $url, $iconpath='', $sequence=0, $handlerInfo=null)
    {
        Vtiger_Link::addLink($this->id, $type, $label, $url, $iconpath, $sequence, $handlerInfo);
    }

    /**
     * Delete custom link of a module
     * @param String Type can be like 'DETAILVIEW', 'LISTVIEW' etc..
     * @param String Display label to lookup
     * @param String URL value to lookup
     */
    public function deleteLink($type, $label, $url=false)
    {
        Vtiger_Link::deleteLink($this->id, $type, $label, $url);
    }

    /**
     * Get all the custom links related to this module.
     */
    public function getLinks()
    {
        return Vtiger_Link::getAll($this->id);
    }


    /**
     * Get all the custom links related to this module for exporting.
     */
    public function getLinksForExport()
    {
        return Vtiger_Link::getAllForExport($this->id);
    }

    /**
     * Initialize webservice setup for this module instance.
     */
    public function initWebservice()
    {
        Vtiger_Webservice::initialize($this);
    }

    /**
     * De-Initialize webservice setup for this module instance.
     */
    public function deinitWebservice()
    {
        Vtiger_Webservice::uninitialize($this);
    }

    /**
     * Get instance by id or name
     * @param mixed id or name of the module
     */
    public static function getInstance($value)
    {
        $instance = false;
        $data = Vtiger_Functions::getModuleData($value);
        if ($data) {
            $instance = new self();
            $instance->initialize($data);
        }
        return $instance;
    }

    /**
     * Get instance of the module class.
     * @param String Module name
     */
    public static function getClassInstance($modulename)
    {
        if ($modulename == 'Calendar') {
            $modulename = 'Activity';
        }

        $instance = false;
        $filepath = "modules/$modulename/$modulename.php";
        if (Vtiger_Utils::checkFileAccessForInclusion($filepath, false)) {
            checkFileAccessForInclusion($filepath);
            include_once($filepath);
            if (class_exists($modulename)) {
                $instance = new $modulename();
            }
        }
        return $instance;
    }

    /**
     * Fire the event for the module (if vtlib_handler is defined)
     */
    public static function fireEvent($modulename, $event_type)
    {
        $instance = self::getClassInstance((string)$modulename);
        if ($instance) {
            if (method_exists($instance, 'vtlib_handler')) {
                self::log("Invoking vtlib_handler for $event_type ...START");
                $instance->vtlib_handler((string)$modulename, (string)$event_type);
                self::log("Invoking vtlib_handler for $event_type ...DONE");
            }
        }
    }

    public function getDuplicateCheckFields(){
        return false;
    }

//    public function generateDefaultRecords($moduleName, $agentid, $wareHouseId = 0){
//        $moduleInstance = Vtiger_Module_Model::getInstance($moduleName);
//        if(!$moduleInstance){
//            return;
//        }
//        $db = PearDatabase::getInstance();
//        $defaultsTable = 'vtiger_'.strtolower($moduleName).'_defaults';
//        $result = $db->pquery('SHOW TABLES LIKE ?', [$defaultsTable]);
//        if ($db->num_rows($result) == 0){
//            return;
//        }
//        $current_user = Users_Record_Model::getCurrentUserModel();
//        $result = $db->pquery('SELECT * FROM `'.$defaultsTable.'`', array());
//        $testColumn = $this->getTestColumnName($moduleName);
//        while($row = $result->fetchrow()){
//            $params = [];
//            foreach($row as $columnName=>$columnValue){
//                if(is_string($columnName) && !preg_match('/id$/',$columnName)) {
//                    $params[$columnName] = $columnValue;
//                }
//            }
//            if($this->checkDefaultExistence($moduleInstance, $testColumn, $agentid, $defaultsTable)){
//                continue;
//            }
//
//            if($moduleName == 'WFLocationTypes'){
//                $params['is_default'] = 'on';
//                $params['warehouse'] = vtws_getWebserviceEntityId('WFWarehouses', $wareHouseId);
//            }
//            $params['agentid'] = $agentid;
//            $params['assigned_user_id'] = vtws_getWebserviceEntityId('Users', $current_user->getId());
//            vtws_create($moduleName, $params, $current_user);
//        }
//    }

    public function getTestColumnName(){
        return false;
    }

    public function checkDefaultExistence($column, $agentid, $defaultsTable, $testVal){
        $exists = false;
        $tableName = $this->get('basetable');
        if(empty($tableName)){
            $tableName = 'vtiger_'.strtolower($this->get('name'));
        }
        $tableId = $this->get('basetableid');
        if(empty($tableId)){
            $tableId = strtolower($this->get('name')).'id';
        }
        $db = PearDatabase::getInstance();
        $sql = "SELECT * FROM `$tableName` LEFT JOIN `vtiger_crmentity` ON $tableId = crmid 
        LEFT JOIN `$defaultsTable` ON `$tableName`.$column = `$defaultsTable`.$column
        WHERE `vtiger_crmentity`.deleted = 0 AND `vtiger_crmentity`.agentid =? AND `$tableName`.$column = ?";
        $result = $db->pquery($sql, [$agentid, $testVal]);
        if($db->num_rows($result) > 0){
            $exists = true;
        }
        return $exists;
    }

}
