<?php

/**
 * Class OPList_Record_Model
 */
class OPList_Record_Model extends Vtiger_Record_Model
{

    /**
     * Gets the number of sections for this OP List
     *
     * @return int number of sections for this OP list
     */
    public function getNumSections()
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT MAX(section_id) FROM `vtiger_oplist_sections` WHERE oplist_id = ?";
        $result = $db->pquery($sql, [$this->getId()]);
        $row = $result->fetchRow();
        if ($row) {
            return $row[0];
        }
        return 0;
    }

    /**
     * Gets the number of options for the given question
     *
     * @param $section_id
     * @param $question_id
     *
     * @return int number of options for the given question
     */
    public function getNumOptions($section_id, $question_id)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT MAX(option_id) FROM `vtiger_oplist_multi_option` WHERE oplist_id = ? AND section_id = ? AND question_id = ?";
        $result = $db->pquery($sql, [$this->getId(), $section_id, $question_id]);
        $row = $result->fetchRow();
        if ($row) {
            return $row[0];
        }
        return 0;
    }

    /**
     * gets the related custom saved OP list data in the form of an array
     *
     * @return array array of the saved OP list fields
     */
    public function getOpListDataArray()
    {
        $db = PearDatabase::getInstance();
        $dataArray = [];
        $opListId = $this->getId();
        $sql = "SELECT * FROM  `vtiger_oplist_sections` WHERE oplist_id = ? ORDER BY section_order ASC";
        $result = $db->pquery($sql, [$opListId]);
        while ($row =& $result->fetchRow()) {
            $dataArray['sections'][] = ['section_id'=>$row['section_id'],
                                        'section_name'=>$row['section_name'],
                                        'num_questions'=>$row['num_questions'],
                                        'section_order'=>$row['section_order'],
                                        'questions'=>$this->getQuestions($row['section_id'])
                                       ];
        }
        return $dataArray;
    }

    /**
     * Gets corresponding questions for the given section
     *
     * @param $section_id
     *
     * @return array array of the saved question fields
     */
    public function getQuestions($section_id)
    {
        $db = PearDatabase::getInstance();
        $questions = [];
        $opListId = $this->getId();
        $sql = "SELECT * FROM `vtiger_oplist_questions` WHERE oplist_id = ? AND section_id = ? ORDER BY question_order ASC";
        $result = $db->pquery($sql, [$opListId, $section_id]);

        while ($row =& $result->fetchRow()) {
            $this_question =  ['oplist_id'=>$row['oplist_id'],
                               'section_id'=>$row['section_id'],
                               'question_id'=>$row['question_id'],
                               'question_type'=>$row['question_type'],
                               'question_order'=>$row['question_order'],
                               'question'=>$row['question'],
                               'text_answer'=>$row['text_answer'],
                               'bool_answer'=>$row['bool_answer'],
                               'date_answer'=>($row['date_answer'] != '0000-00-00' && !empty($row['date_answer']))
                                   ?DateTimeField::convertToUserFormat($row['date_answer']):null,
                               'datetime_answer'=>$row['datetime_answer'],
                               'datetime_answer_date'=>(explode(' ', $row['datetime_answer'])[0] != '0000-00-00' && !empty($row['datetime_answer']))
                                   ?DateTimeField::convertToUserFormat(explode(' ', $row['datetime_answer'])[0]):null,
                               'datetime_answer_time'=>(explode(' ', $row['datetime_answer'])[1] != '00:00:00' && !empty($row['datetime_answer']))
                                   ?Vtiger_Time_UIType::getDisplayTimeValue(explode(' ', $row['datetime_answer'])[1]):null,
                               'time_answer'=>($row['time_answer'] != '00:00:00')?Vtiger_Time_UIType::getDisplayTimeValue($row['time_answer']):null,
                               'use_dec'=>($row['dec_answer'])?true:false,
                               'int_answer'=>$row['int_answer'],
                               'dec_answer'=>$row['dec_answer'],
                               'multi_answer_id'=>$row['multi_answer_id'],
                               'allow_multiple_answers'=>$row['allow_multiple_answers'],
                               'multi_options'=>$this->getOptions($row['section_id'], $row['question_id']),
                               'num_options'=>$this->getNumOptions($row['section_id'], $row['question_id']),
                              ];
            $questions[] = $this_question;
        }
        return $questions;
    }

    public static function getVanlineManagerId($agentId) {
        $db = PearDatabase::getInstance();
        $sql = "SELECT vanline_id FROM vtiger_agentmanager WHERE agentmanagerid=?";
        $res = $db->pquery($sql, [$agentId]);
        if($res) {
            return $res->fetchRow()[0];
        }
    }

    public static function getVanlineId($recordId)
    {
        $db = PearDatabase::getInstance();
        $vanlineSql = "SELECT `vtiger_vanlinemanager`.vanline_id FROM `vtiger_oplist`
				LEFT JOIN `vtiger_crmentity` ON `vtiger_oplist`.oplistid = `vtiger_crmentity`.crmid
				LEFT JOIN `vtiger_vanlinemanager` ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_crmentity`.agentid
				WHERE `vtiger_oplist`.oplistid = ? AND `vtiger_vanlinemanager`.vanlinemanagerid IS NOT NULL";
        $result = $db->pquery($vanlineSql, [$recordId]);
        $row = $result->fetchRow();
        if ($row['vanline_id']) {
            return $row['vanline_id'];
        }
        $agentSql = "SELECT `vtiger_vanlinemanager`.vanline_id FROM `vtiger_oplist`
				LEFT JOIN `vtiger_crmentity` ON `vtiger_oplist`.oplistid = `vtiger_crmentity`.crmid
				LEFT JOIN `vtiger_agentmanager` ON `vtiger_agentmanager`.agentmanagerid = `vtiger_crmentity`.agentid
				LEFT JOIN `vtiger_vanlinemanager` ON `vtiger_vanlinemanager`.vanlinemanagerid = `vtiger_agentmanager`.vanline_id
				WHERE `vtiger_oplist`.oplistid = ? AND `vtiger_vanlinemanager`.vanlinemanagerid IS NOT NULL";
        $result = $db->pquery($agentSql, [$recordId]);
        $row = $result->fetchRow();
        if ($row['vanline_id']) {
            return $row['vanline_id'];
        }
    }

    /**
     * Gets corresponding multi options for the corresponding question and section
     *
     * @param $section_id
     * @param $question_id
     *
     * @return array array of the options for the given parameters
     */
    public function getOptions($section_id, $question_id)
    {
        $db = PearDatabase::getInstance();
        $options = [];
        $opListId = $this->getId();
        $sql = "SELECT * FROM `vtiger_oplist_multi_option` WHERE oplist_id = ? AND section_id = ? AND question_id = ? ORDER BY option_order ASC";
        $result = $db->pquery($sql, [$opListId, $section_id, $question_id]);
        while ($row =& $result->fetchRow()) {
            $this_option = ['oplist_id'=>$row['oplist_id'],
                            'section_id'=>$row['section_id'],
                            'question_id'=>$row['question_id'],
                            'option_id'=>$row['option_id'],
                            'option_order'=>$row['option_order'],
                            'selected'=>$row['selected'],
                            'answer'=>$row['answer']
                           ];
            $options[] = $this_option;
        }
        return $options;
    }
}
