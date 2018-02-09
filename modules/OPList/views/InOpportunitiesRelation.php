<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class OPList_InOpportunitiesRelation_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $relatedModuleName = $request->get('relatedModule');
        $viewer            = $this->getViewer($request);
        $moduleName        = $request->getModule();
        $record            = $request->get('record');
        $viewer->assign('SOURCE_RECORD', $record);
        $sourceRecordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        //$viewer->assign('SOURCE',$sourceRecordModel,true);
        $recordOwner = $sourceRecordModel->get('agentid');
        $moveType    = getenv('INSTANCE_NAME') == 'sirva' ? $sourceRecordModel->get('move_type') : $sourceRecordModel->get('business_line');
        $viewer->assign('MOVE_TYPE', $moveType);
        //determine what OPList to show here
        $numSections = null;
        $dataArray   = [];
        $displayName = null;

        if ($this->checkForSavedAnswers($record)) {
            //we have saved answers
            $basicInfo   = $this->getBasicSavedAnswerInfo($record);
            $OPListId    = $basicInfo['oplist_id'];
            $numSections = $this->getNumSections($record, $OPListId);
            $dataArray   = $this->getOpListDataArray($record, $OPListId);
            $displayName = $basicInfo['display_name'];
        } else {
            //we don't have saved answers try and grab an appropriate OpList
            $OPListId = $this->getOpListByOwnerId($recordOwner, $moveType);
            if (empty($OPListId)) {
                //return early if we don't have anything useful to pass
                $viewer->assign('NON_FOUND', true);
                return $viewer->view('AnswerView.tpl', $relatedModuleName, 'true');
            }
            //get an instance of the OPList Record Model so we can get the OpListDataArray
            $recordModel = Vtiger_Record_Model::getInstanceById($OPListId, $relatedModuleName);
            $numSections = $recordModel->getNumSections();
            $dataArray   = $recordModel->getOpListDataArray();
            $displayName = $recordModel->getDisplayName();
        }
        //assign the stuff we need to make the OPList show up
        if ($request->get('saved')) {
            $viewer->assign('SAVED', true);
        }
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('NUM_SECTIONS', $numSections);
        $viewer->assign('OPLIST_ARRAY', $dataArray);
        $viewer->assign('MODULE', $relatedModuleName);
        $viewer->assign('DISPLAY_NAME', $displayName);
        $viewer->assign('SINGLE_MODULE_NAME', 'SINGLE_OPList');
        $viewer->assign('SOURCE_RECORD', $record);
        $viewer->assign('RECORD', $OPListId);

        return $viewer->view('AnswerView.tpl', $relatedModuleName, 'true');
    }

    public function checkForSavedAnswers($OppId)
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT * FROM `vtiger_oplist_answers` WHERE opp_id = ? LIMIT 1";
        $result = $db->pquery($sql, [$OppId]);
        $row    = $result->fetchRow();
        if ($row) {
            return true;
        }

        return false;
    }

    public function getBasicSavedAnswerInfo($OppId)
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT * FROM `vtiger_oplist_answers` WHERE opp_id = ? LIMIT 1";
        $result = $db->pquery($sql, [$OppId]);
        $row    = $result->fetchRow();
        if ($row) {
            return ['oplist_id' => $row['oplist_id'], 'display_name' => $row['display_name']];
        }

        return false;
    }

    public function getOpListId($ownerId, $moveType)
    {
        $db       = PearDatabase::getInstance();
        $OPListId = $this->getOpListIdByOwnerId($ownerId, $moveType);
        /*if (empty($OPListId)) {
            //there isn't a match or there might only be one at the vanline level
            $sql    = "SELECT `vtiger_groups`.groupid FROM `vtiger_groups`
                    JOIN `vtiger_vanlinemanager`
                    ON `vtiger_groups`.groupname = `vtiger_vanlinemanager`.vanline_name
                    JOIN `vtiger_agentmanager`
                    ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_agentmanager`.vanline_id
                    JOIN `vtiger_groups` AS `temp`
                    ON `vtiger_agentmanager`.agency_name = `temp`.groupname
                    WHERE `temp`.groupid = ?";
            $result = $db->pquery($sql, [$ownerId]);
            while ($row =& $result->fetchRow()) {
                $vanlineGroupId = $row['groupid'];
                $OPListId       = $this->getOpListIdByOwnerId($vanlineGroupId, $moveType);
                if ($OPListId) {
                    return $OPListId;
                }
            }
        }*/

        return $OPListId;
    }

    public function getOpListByOwnerId($ownerId, $moveType)
    {
        $db = PearDatabase::getInstance();
        //first try to get any oplist created by the owner agent's vanline
        $sql = "SELECT * FROM `vtiger_oplist`
                JOIN `vtiger_crmentity` ON `vtiger_oplist`.oplistid = `vtiger_crmentity`.crmid
                JOIN `vtiger_vanlinemanager` ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_crmentity`.agentid
				JOIN `vtiger_agentmanager` ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_agentmanager`.vanline_id
				WHERE `vtiger_agentmanager`.agentmanagerid = ? AND `vtiger_crmentity`.deleted = 0";

        $result = $db->pquery($sql, [$ownerId]);

        while ($row =& $result->fetchRow()) {
            $moveTypes = explode(' |##| ', getenv('INSTANCE_NAME') == 'sirva' ? $row['op_move_type'] : $row['business_line']);
            if (in_array($moveType, $moveTypes)) {
                return $row['oplistid'];
            }
        }

        //if that fails search for an agent level OP List
        $sql = "SELECT * FROM `vtiger_oplist`
                JOIN `vtiger_crmentity` ON `vtiger_oplist`.oplistid = `vtiger_crmentity`.crmid
                WHERE `vtiger_crmentity`.agentid = ? AND `vtiger_crmentity`.deleted = 0";
        $result = $db->pquery($sql, [$ownerId]);
        while ($row =& $result->fetchRow()) {
            $moveTypes = explode(' |##| ', getenv('INSTANCE_NAME') == 'sirva' ? $row['op_move_type'] : $row['business_line']);
            if (in_array($moveType, $moveTypes)) {
                return $row['oplistid'];
            }
        }
    }

    public function getOpListIdByGroupId($groupid, $moveType)
    {
        /*$db = PearDatabase::getInstance();
        //grab parents
        $params  = [$groupid];
        $parents = [];
        $sql     = "SELECT DISTINCT `vtiger_vanlinemanager`.groupid FROM `vtiger_vanlinemanager`
            LEFT JOIN `vtiger_agentmanager` ON `vtiger_agentmanager`.vanline_id = `vtiger_vanlinemanager`.vanlinemanagerid
            LEFT JOIN `vtiger_crmentity` ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_crmentity`.crmid
            WHERE `vtiger_crmentity`.deleted = 0 AND (`vtiger_agentmanager`.groupid = ? OR `vtiger_vanlinemanager`.is_parent = 1)";
        $result  = $db->pquery($sql, [$groupid]);
        $row     = $result->fetchRow();
        while ($row != NULL) {
            $parents[] = $row['groupid'];
            $row       = $result->fetchRow();
        }
        //find relevent OP List
        $sql = "SELECT * FROM `vtiger_oplist`
                JOIN `vtiger_crmentity` ON `vtiger_oplist`.oplistid = `vtiger_crmentity`.crmid
                WHERE `vtiger_crmentity`.smownerid = ?";
        file_put_contents('logs/devLog.log', "\n rel parents: ".print_r($parents, true), FILE_APPEND);
        foreach ($parents as $parentGroup) {
            $sql .= " OR `vtiger_crmentity`.smownerid = ?";
            $params[] = $parentGroup;
        }
        $result = $db->pquery($sql, $params);
        while ($row =& $result->fetchRow()) {
            //parse through op_move_types to see if they match with our Opportunity
            $moveTypes = explode(' |##| ', getenv('INSTANCE_NAME') == 'sirva' ? $row['op_move_type'] : $row['business_line']);
            if (in_array($moveType, $moveTypes)) {
                return $row['oplistid'];
            }
        }*/

        return false;
    }

    /**
     * Gets the number of sections for this OP List Answers
     *
     * @param $OppId    The id for the opportunity
     * @param $OpListId The id for the OPList we are looking at
     *
     * @return int number of sections for this OP list Answers
     */
    public function getNumSections($OppId, $OpListId)
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT MAX(section_id) FROM `vtiger_oplist_answers_sections` WHERE opp_id = ? AND oplist_id = ?";
        $result = $db->pquery($sql, [$OppId, $OpListId]);
        $row    = $result->fetchRow();
        if ($row) {
            return $row[0];
        }

        return 0;
    }

    /**
     * Gets the number of options for the given question
     *
     * @param $OppId    The id for the opportunity
     * @param $OpListId The id for the OPList we are looking at
     * @param $section_id
     * @param $question_id
     *
     * @return int number of options for the given question
     */
    public function getNumOptions($OppId, $OpListId, $section_id, $question_id)
    {
        $db     = PearDatabase::getInstance();
        $sql    = "SELECT MAX(option_id) FROM `vtiger_oplist_answers_multi_option`
                WHERE opp_id = ? AND oplist_id = ? AND section_id = ? AND question_id = ?";
        $result = $db->pquery($sql, [$OppId, $OpListId, $section_id, $question_id]);
        $row    = $result->fetchRow();
        if ($row) {
            return $row[0];
        }

        return 0;
    }

    /**
     * gets the related custom saved OP list data in the form of an array
     *
     * @param $OppId    The id for the opportunity
     * @param $OpListId The id for the OPList we are looking at
     *
     * @return array array of the saved OP list fields
     */
    public function getOpListDataArray($OppId, $OpListId)
    {
        $db        = PearDatabase::getInstance();
        $dataArray = [];
        $sql       = "SELECT * FROM  `vtiger_oplist_answers_sections` WHERE opp_id =? AND oplist_id = ? ORDER BY section_order ASC";
        $result    = $db->pquery($sql, [$OppId, $OpListId]);
        while ($row =& $result->fetchRow()) {
            $dataArray['sections'][] = ['section_id'    => $row['section_id'],
                                        'section_name'  => $row['section_name'],
                                        'num_questions' => $row['num_questions'],
                                        'section_order' => $row['section_order'],
                                        'questions'     => $this->getQuestions($OppId, $OpListId, $row['section_id']),
            ];
        }

        return $dataArray;
    }

    /**
     * Gets corresponding questions for the given section
     *
     * @param $OppId    The id for the opportunity
     * @param $OpListId The id for the OPList we are looking at
     * @param $section_id
     *
     * @return array array of the saved question fields
     */
    public function getQuestions($OppId, $OpListId, $section_id)
    {
        $db        = PearDatabase::getInstance();
        $questions = [];
        $sql       = "SELECT * FROM `vtiger_oplist_answers_questions`
                WHERE opp_id = ? AND oplist_id = ? AND section_id = ? ORDER BY question_order ASC";
        $result    = $db->pquery($sql, [$OppId, $OpListId, $section_id]);
        while ($row =& $result->fetchRow()) {
            $this_question = ['oplist_id'                    => $row['oplist_id'],
                              'section_id'                   => $row['section_id'],
                              'question_id'                  => $row['question_id'],
                              'question_type'                => $row['question_type'],
                              'question_order'               => $row['question_order'],
                              'question'                     => $row['question'],
                              //get our default answers that we saved in case something is empty
                              'default_text_answer'          => $row['default_text_answer'],
                              'default_bool_answer'          => $row['default_bool_answer'],
                              'default_date_answer'          => ($row['default_date_answer'] != '0000-00-00' && !empty($row['default_date_answer']))
                                  ?DateTimeField::convertToUserFormat($row['default_date_answer']):null,
                              'default_datetime_answer'      => $row['default_datetime_answer'],
                              'default_datetime_answer_date' => (explode(' ', $row['default_datetime_answer'])[0] !=
                                                                 '0000-00-00' && !empty($row['default_datetime_answer']))
                                  ?DateTimeField::convertToUserFormat(explode(' ', $row['default_datetime_answer'])[0]):null,
                              'default_datetime_answer_time' => !empty($row['default_datetime_answer'])
                                  ?Vtiger_Time_UIType::getDisplayTimeValue(explode(' ', $row['default_datetime_answer'])[1]):null,
                              'default_time_answer'          => !empty($row['default_time_answer'])?Vtiger_Time_UIType::getDisplayTimeValue($row['default_time_answer']):null,
                              'default_int_answer'           => $row['default_int_answer'],
                              'default_dec_answer'           => $row['default_dec_answer'],
                              //get our user inputed answers
                              'text_answer'                  => $row['text_answer'],
                              'bool_answer'                  => $row['bool_answer'],
                              'date_answer'                  => ($row['date_answer'] != '0000-00-00' && !empty($row['date_answer']))
                                  ?DateTimeField::convertToUserFormat($row['date_answer']):null,
                              'datetime_answer'              => $row['datetime_answer'],
                              'datetime_answer_date'         => (explode(' ', $row['datetime_answer'])[0] != '0000-00-00' &&
                                                                 !empty($row['datetime_answer']))?DateTimeField::convertToUserFormat(explode(' ',
                                                                                                                                             $row['datetime_answer'])[0]):null,
                              'datetime_answer_time'         => !empty($row['datetime_answer'])?Vtiger_Time_UIType::getDisplayTimeValue(explode(' ', $row['datetime_answer'])[1]):null,
                              'time_answer'                  => (!empty($row['time_answer']))?Vtiger_Time_UIType::getDisplayTimeValue($row['time_answer']):null,
                              'use_dec'                      => (!empty($row['dec_answer']) || !empty($row['default_dec_answer']))?true:false,
                              'int_answer'                   => $row['int_answer'],
                              'dec_answer'                   => $row['dec_answer'],
                              'multi_answer_id'              => $row['multi_answer_id'],
                              'allow_multiple_answers'       => $row['allow_multiple_answers'],
                              'multi_options'                => $this->getOptions($OppId,
                                                                                  $OpListId,
                                                                                  $row['section_id'],
                                                                                  $row['question_id']),
                              'num_options'                  => $this->getNumOptions($OppId,
                                                                                     $OpListId,
                                                                                     $row['section_id'],
                                                                                     $row['question_id']),
            ];
            $questions[]   = $this_question;
        }
        return $questions;
    }

    /**
     * Gets corresponding multi options for the corresponding question and section
     *
     * @param $OppId    The id for the opportunity
     * @param $OpListId The id for the OPList we are looking at
     * @param $section_id
     * @param $question_id
     *
     * @return array array of the options for the given parameters
     */
    public function getOptions($OppId, $OpListId, $section_id, $question_id)
    {
        $db      = PearDatabase::getInstance();
        $options = [];
        $sql     = "SELECT * FROM `vtiger_oplist_answers_multi_option`
                WHERE opp_id = ? AND oplist_id = ? AND section_id = ? AND question_id = ?
                ORDER BY option_order ASC";
        $result  = $db->pquery($sql, [$OppId, $OpListId, $section_id, $question_id]);
        while ($row =& $result->fetchRow()) {
            $this_option = ['oplist_id'        => $row['oplist_id'],
                            'section_id'       => $row['section_id'],
                            'question_id'      => $row['question_id'],
                            'option_id'        => $row['option_id'],
                            'option_order'     => $row['option_order'],
                            'default_selected' => $row['default_selected'],
                            'selected'         => $row['selected'],
                            'answer'           => $row['answer'],
            ];
            $options[]   = $this_option;
        }

        return $options;
    }
}
