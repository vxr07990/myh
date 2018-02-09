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

class OPList extends Vtiger_CRMEntity
{
    public $table_name = 'vtiger_oplist';
    public $table_index= 'oplistid';

    /**
     * Mandatory table for supporting custom fields.
     */
    public $customFieldTable = array('vtiger_oplistcf', 'oplistid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    public $tab_name = array('vtiger_crmentity', 'vtiger_oplist', 'vtiger_oplistcf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    public $tab_name_index = array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_oplist' => 'oplistid',
        'vtiger_oplistcf'=>'oplistid');

    /**
     * Mandatory for Listing (Related listview)
     */
    public $list_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_OPLIST_OPNAME' => array('oplist', 'op_name'),
        'Assigned To' => array('crmentity', 'smownerid')
    );
    public $list_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_OPLIST_OPNAME' => 'op_name',
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    public $list_link_field = 'op_name';

    // For Popup listview and UI type support
    public $search_fields = array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_OPLIST_OPNAME' => array('oplist', 'op_name'),
        'Assigned To' => array('vtiger_crmentity','assigned_user_id'),
    );
    public $search_fields_name = array(
        /* Format: Field Label => fieldname */
        'LBL_OPLIST_OPNAME' => 'op_name',
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    public $popup_fields = array('op_name');

    // For Alphabetical search
    public $def_basicsearch_col = 'op_name';

    // Column value to use on detail view record text display
    public $def_detailview_recname = 'op_name';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    public $mandatory_fields = array('op_name','assigned_user_id');

    public $default_order_by = 'op_name';
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
    /**
     * Retrieve custom record information of the module
     * @param <Integer> $record - crmid of record
     */
    public function retrieve($record)
    {
        //file_put_contents('logs/devLog.log', "\n Record : ".print_r($record, true), FILE_APPEND);
        $fieldList = [];
        $recordModel = Vtiger_Record_Model::getInstanceById($record, 'OPList');
        $fieldList['numSections'] = $recordModel->getNumSections();
        $opListArray = $recordModel->getOpListDataArray();
        //file_put_contents('logs/devLog.log', "\n OpListArray : ".print_r($opListArray, true), FILE_APPEND);
        foreach ($opListArray['sections'] as $section) {
            $fieldList['sectionOrder_'.$section['section_id']] = $section['section_order'];
            $fieldList['section_name_'.$section['section_id']] = $section['section_name'];
            $fieldList['numQuestions_'.$section['section_id']] = $section['num_questions'];
            foreach ($section['questions'] as $question) {
                $fieldList['question_type_'.$section['section_id'].'_'.$question['question_id']] = $question['question_type'];
                $fieldList['question_order_'.$section['section_id'].'_'.$question['question_id']] = $question['question_order'];
                $fieldList['question_'.$section['section_id'].'_'.$question['question_id']] = $question['question'];
                switch ($question['question_type']) {
                    case 'Text':
                        $typeStr     = 'text';
                        $fieldList['default_answer_'.$typeStr.'_'.$section['section_id'].'_'.$question['question_id']] = $question[$typeStr.'_answer'];
                        break;
                    case 'Yes/No':
                        $typeStr     = 'bool';
                        $fieldList['default_answer_'.$typeStr.'_'.$section['section_id'].'_'
                                   .$question['question_id']] = ($question[$typeStr.'_answer']==1)?'on':false;
                        break;
                    case 'Date':
                        $typeStr     = 'date';
                        $fieldList['default_answer_'.$typeStr.'_'.$section['section_id'].'_'.$question['question_id']] = $question[$typeStr.'_answer'];
                        break;
                    case 'Date and Time':
                        $typeStr     = 'datetime';
                        $fieldList['default_answer_'.$typeStr.'_date_'.$section['section_id'].'_'
                                   .$question['question_id']] = $question[$typeStr.'_answer_date'];
                        $fieldList['default_answer_'.$typeStr.'_time_'.$section['section_id'].'_'
                                   .$question['question_id']] = $question[$typeStr.'_answer_time'];
                        break;
                    case 'Time':
                        $typeStr     = 'time';
                        $fieldList['default_answer_'.$typeStr.'_'.$section['section_id'].'_'.$question['question_id']] = $question[$typeStr.'_answer'];
                        break;
                    case 'Quantity':
                        $typeStr    = 'number';
                        $fieldList['default_answer_use_decimal_'.$section['section_id'].'_'.$question['question_id']] =
                            !empty($question['use_dec'])?'on':false;
                        $fieldList['default_answer_'.$typeStr.'_'.$section['section_id'].'_'.$question['question_id']] =
                            $question['use_dec']?$question['dec_answer']:$question['int_answer'];
                        break;
                    case 'Multiple Choice':
                        $fieldList['default_answer_select_multiple_'.$section['section_id'].'_'.$question['question_id']] =
                            ($question['allow_multiple_answers']==1)?'on':false;
                        $fieldList['numOptions_'.$section['section_id'].'_'.$question['question_id']] =
                            $question['num_options'];
                        $fieldList['defaultMultiOption_prev_'.$section['section_id'].'_'.$question['question_id']] =
                            'none';
                        foreach ($question['multi_options'] as $option) {
                            $fieldList['option_order_'.$section['section_id'].'_'.$question['question_id']
                                       .'_'.$option['option_id']] = $option['option_order'];
                            $fieldList['default_multi_option_'.$section['section_id'].'_'.$question['question_id']
                                       .'_'.$option['option_id']] = ($option['selected']==1)?'on':false;
                            $fieldList['multi_option_answer_'.$section['section_id'].'_'.$question['question_id']
                                       .'_'.$option['option_id']] = $option['answer'];
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return $fieldList;
    }
    public function saveentity($module, $fileid = '')
    {
        /*if($_REQUEST['repeat'] === true){
            return;
        }
        //does things twice, this stops it.
        $_REQUEST['repeat'] = true;*/
        //this is how to handle custom save logic to make it work for both VTWS and normal saving.
        parent::saveentity($module, $fileid);
        $db = PearDatabase::getInstance();
        $fieldList = array_merge($_REQUEST, $this->column_fields);

        if (empty($fieldList['record'])) {
            //new records will have an empty record but currentid gets set correctly in parent
            if (!empty($fieldList['currentid'])) {
                $fieldList[ 'record' ] = $fieldList[ 'currentid' ];
            } else {
                $sql = "SELECT crmid FROM `vtiger_crmentity` WHERE setype = ? AND createdtime = ? AND modifiedtime = ? AND label = ?";
                $result = $db->pquery($sql, ['OPList', $this->column_fields['createdtime'], $this->column_fields['modifiedtime'], $this->column_fields['op_name']]);
                $row = $result->fetchRow();
                $fieldList['record'] = $row[0];
            }
        }

        // file_put_contents('logs/devLog.log', "\n fieldList : ".print_r($fieldList,true)."\n module : ".print_r($module,true)."\n fileid : ".print_r($fileid,true)."\n", FILE_APPEND);
        $opListId = $fieldList['record'];
        $numSections = $fieldList['numSections'];
        for ($i = 1; $i <= $numSections; $i++) {
            if ($fieldList['delete_section_'.$i] == 1) {
                //delete this section and all its trickle downs stuff
                $sql = "SELECT * FROM `vtiger_oplist_sections` WHERE oplist_id = ? AND section_id = ?";
                $result = $db->pquery($sql, [$opListId, $i]);
                $row = $result->fetchRow();
                if ($row) { //if we have this row delete it from anywhere it could be
                    $sql = "DELETE FROM `vtiger_oplist_sections` WHERE oplist_id = ? AND section_id = ?";
                    $db->pquery($sql, [$opListId, $i]);
                    $sql = "DELETE FROM `vtiger_oplist_questions` WHERE oplist_id = ? AND section_id = ?";
                    $db->pquery($sql, [$opListId, $i]);
                    $sql = "DELETE FROM `vtiger_oplist_multi_option` WHERE oplist_id = ? AND section_id = ?";
                    $db->pquery($sql, [$opListId, $i]);
                }
            } else {
                //$sectionId    = $i;
                $sectionName  = $fieldList[ 'section_name_'.$i ];
                $numQuestions = $fieldList[ 'numQuestions_'.$i ];
                $sectionOrder = $fieldList[ 'sectionOrder_'.$i ];
                $sql    = "SELECT * FROM `vtiger_oplist_sections` WHERE oplist_id = ? AND section_id = ?";
                $result = $db->pquery($sql, [$opListId, $i]);
                $row    = $result->fetchRow();
                if ($row) {
                    //update the record
                    $sql = "UPDATE `vtiger_oplist_sections` SET section_name = ?, num_questions = ?, section_order = ? WHERE oplist_id = ? AND section_id = ?";
                    $db->pquery($sql, [$sectionName, $numQuestions, $sectionOrder, $opListId, $i]);
                } else {
                    if ($sectionName != "") {
                        //add the record
                        $sql = "INSERT INTO `vtiger_oplist_sections` (oplist_id,section_id,section_name,num_questions,section_order) VALUES (?,?,?,?,?)";
                        $db->pquery($sql, [$opListId, $i, $sectionName, $numQuestions, $sectionOrder]);
                    }
                }
                for ($j = 1; $j <= $numQuestions; $j++) {
                    if ($fieldList['delete_question_'.$i.'_'.$j]) {
                        //delete it and kill the orphans from multi
                        $sql = "SELECT * FROM `vtiger_oplist_questions` WHERE oplist_id = ? AND section_id = ? AND question_id = ?";
                        $result = $db->pquery($sql, [$opListId, $i, $j]);
                        $row = $result->fetchRow();
                        if ($row) { //if we have this row delete it from anywhere it could be
                            $sql = "DELETE FROM `vtiger_oplist_questions` WHERE oplist_id = ? AND section_id = ? AND question_id = ?";
                            $db->pquery($sql, [$opListId, $i, $j]);
                            $sql = "DELETE FROM `vtiger_oplist_multi_option` WHERE oplist_id = ? AND section_id = ? AND question_id = ?";
                            $db->pquery($sql, [$opListId, $i, $j]);
                        }
                    } else {
                        $questionType           = $fieldList[ 'question_type_'.$i.'_'.$j ];
                        $questionOrder          = $fieldList[ 'question_order_'.$i.'_'.$j ];
                        $question               = $fieldList[ 'question_'.$i.'_'.$j ];
                        if (empty($questionType) && empty($question)) {
                            //don't save it if we don't have a type or a question
                            continue;
                        }
                        $text_answer            = null;
                        $bool_answer            = null;
                        $date_answer            = null;
                        $datetime_answer        = null;
                        $time_answer            = null;
                        $int_answer             = null;
                        $dec_answer             = null;
                        $multi_answer_id        = null;
                        $allow_multiple_answers = null;
                        switch ($questionType) {
                            case 'Text':
                                $typeStr     = 'text';
                                $text_answer = $fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ];
                                break;
                            case 'Yes/No':
                                $typeStr     = 'bool';
                                $bool_answer = ($fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ] == 'on') ? 1 : 0;
                                break;
                            case 'Date':
                                $typeStr     = 'date';
                                $date_answer_user_format = $fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ];
                                $date_answer = DateTimeField::convertToDBFormat($date_answer_user_format);
                                break;
                            case 'Date and Time':
                                $typeStr         = 'datetime';
                                $datetime_answer_date_user_format = $fieldList[ 'default_answer_'.$typeStr.'_date_'.$i.'_'.$j ];
                                $datetime_answer_time_user_format = $fieldList[ 'default_answer_'.$typeStr.'_time_'.$i.'_'.$j ];
                                $datetime_answer_date = DateTimeField::convertToDBFormat($datetime_answer_date_user_format);
                                $datetime_answer_time = ((!empty($datetime_answer_time_user_format))
                                    ?DateTimeField::convertToDBTimeZone(Vtiger_Time_UIType::getTimeValueWithSeconds($datetime_answer_time_user_format))->format('H:i:s'):'');
                                $datetime_answer = (!empty($datetime_answer_time)?($datetime_answer_date.' '.$datetime_answer_time):null);
                                break;
                            case 'Time':
                                $typeStr     = 'time';
                                $time_answer_user_format = (isset($fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ])?$fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ]:null);
                                $time_answer = (($time_answer_user_format != null)?DateTimeField::convertToDBTimeZone(Vtiger_Time_UIType::getTimeValueWithSeconds($time_answer_user_format))->format('H:i:s'):'');
                                break;
                            case 'Quantity':
                                $typeStr    = 'number';
                                $int_answer = ($fieldList[ 'default_answer_use_decimal_'.$i.'_'.$j ] != 'on') ? $fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ] : null;
                                $dec_answer = ($fieldList[ 'default_answer_use_decimal_'.$i.'_'.$j ] == 'on') ? $fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ] : null;
                                break;
                            case 'Multiple Choice':
                                $multi_answer_id = $j;
                                $numOptions      = $fieldList[ 'numOptions_'.$i.'_'.$j ];
                                $allow_multiple_answers = ($fieldList['default_answer_select_multiple_'.$i.'_'.$j] == 'on')?1:0;
                                for ($k = 1; $k <= $numOptions; $k++) {
                                    //check if it's deleted and delete the option otherwise do the normal stuff
                                    if ($fieldList[ 'delete_option_'.$i.'_'.$j.'_'.$k ] == 1) {
                                        //delete the option
                                        $sql = "DELETE FROM `vtiger_oplist_multi_option` WHERE oplist_id = ? AND section_id = ? AND question_id = ? AND option_id = ?";
                                        $db->pquery($sql, [$opListId, $i, $j, $k]);
                                    } else {
                                        $optionOrder = $fieldList[ 'option_order_'.$i.'_'.$j.'_'.$k ];
                                        if ($allow_multiple_answers == 1) {
                                            $selected = ($fieldList['default_multi_option_'.$i.'_'.$j.'_'.$k] == 'on')?1:0;
                                            //file_put_contents('logs/devLog.log', "\n selected : ".$selected, FILE_APPEND);
                                        } else {
                                            //file_put_contents('logs/devLog.log', "\n I: $i - J: $j - K: - $k", FILE_APPEND);
                                            $selected    = ($fieldList[ 'defaultMultiOption_'.$i.'_'.$j ] == $k)?1:0;
                                        }
                                        $answer      = $fieldList[ 'multi_option_answer_'.$i.'_'.$j.'_'.$k ];
                                        $sql         = "SELECT * FROM `vtiger_oplist_multi_option` WHERE oplist_id = ? AND section_id = ? AND question_id = ? AND option_id = ?";
                                        $result      = $db->pquery($sql, [$opListId, $i, $j, $k]);
                                        $row         = $result->fetchRow();
                                        if ($row) {
                                            //update existing
                                            $sql = "UPDATE `vtiger_oplist_multi_option` SET option_order = ?, selected = ?, answer = ? WHERE oplist_id = ? AND section_id = ? AND question_id = ? AND option_id = ?";
                                            $db->pquery($sql, [$optionOrder, $selected, $answer, $opListId, $i, $j, $k]);
                                        } else {
                                            //insert new
                                            $sql = "INSERT INTO `vtiger_oplist_multi_option` (oplist_id,section_id,question_id,option_id,option_order,selected,answer) VALUES (?,?,?,?,?,?,?)";
                                            $db->pquery($sql, [$opListId, $i, $j, $k, $optionOrder, $selected, $answer]);
                                        }
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                        $sql = "SELECT * FROM `vtiger_oplist_questions` WHERE oplist_id = ? AND section_id = ? AND question_id = ?";
                        $result = $db->pquery($sql, [$opListId, $i, $j]);
                        $row = $result->fetchRow();
                        if ($row) {
                            //update existing question
                            $sql = "UPDATE `vtiger_oplist_questions` SET question_type = ?, question_order = ?, question = ?, text_answer = ?, bool_answer = ?, date_answer = ?, datetime_answer = ?, time_answer = ?, int_answer = ?, dec_answer = ?, multi_answer_id = ?, allow_multiple_answers = ? WHERE oplist_id = ? AND section_id = ? AND question_id = ?";
//                            file_put_contents('logs/devLog.log', $sql, FILE_APPEND);
                          //  file_put_contents('logs/devLog.log', print_r([$questionType, $questionOrder, $question, $text_answer, $bool_answer, $date_answer, $datetime_answer, $time_answer, $int_answer, $dec_answer, $multi_answer_id, $allow_multiple_answers, $opListId, $i, $j], true), FILE_APPEND);
                            $db->pquery($sql, [$questionType, $questionOrder, $question, $text_answer, $bool_answer, $date_answer, $datetime_answer, $time_answer, $int_answer, $dec_answer, $multi_answer_id, $allow_multiple_answers, $opListId, $i, $j]);
                        } else {
                            //insert new
                            $sql = "INSERT INTO `vtiger_oplist_questions` (oplist_id,section_id,question_id,question_type,question_order,question,text_answer,bool_answer,date_answer,datetime_answer,time_answer,int_answer,dec_answer,multi_answer_id,allow_multiple_answers) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
//                            file_put_contents('logs/devLog.log', $sql, FILE_APPEND);
                          //  file_put_contents('logs/devLog.log', print_r([$opListId, $i, $j, $questionType, $questionOrder, $question, $text_answer, $bool_answer, $date_answer, $datetime_answer, $time_answer, $int_answer, $dec_answer, $multi_answer_id, $allow_multiple_answers], true), FILE_APPEND);
                            $db->pquery($sql, [$opListId, $i, $j, $questionType, $questionOrder, $question, $text_answer, $bool_answer, $date_answer, $datetime_answer, $time_answer, $int_answer, $dec_answer, $multi_answer_id, $allow_multiple_answers]);
                        }
                    }
                }
            }
        }
    }
}
