<?php
class OPList_SaveOpListAnswers_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    public function process(Vtiger_Request $request)
    {
        //echo "<br> Hey we're here now";
        //echo "<br> <b>request : </b><br>".print_r($request,true);
        $this->save($request);
        //echo "<br> <b>finished</b>";
    }
    public function save(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $fieldList = $request->getAll();
        // file_put_contents('logs/devLog.log', "\n FieldList : ".print_r(json_encode($fieldList,JSON_PRETTY_PRINT), true), FILE_APPEND);

        $opListId = $fieldList['record'];

        $oppId = $fieldList['source_record'];
        $display_name = $fieldList['display_name'];
        $noRedirect = $fieldList['NoRedirect'];
        //save the display name
        $sql = "SELECT * FROM `vtiger_oplist_answers` WHERE opp_id = ? AND oplist_id = ?";
        $result = $db->pquery($sql, [$oppId, $opListId]);
        $row = $result->fetchRow();
        if ($row) {
            //update the record
            $sql = "UPDATE `vtiger_oplist_answers` SET display_name = ?
                    WHERE opp_id = ? AND oplist_id = ?";
            $db->pquery($sql, [$display_name, $oppId, $opListId]);
        } else {
            //add the record
            $sql = "INSERT INTO `vtiger_oplist_answers` (opp_id, oplist_id, display_name)
                        VALUES (?,?,?)";
            $db->pquery($sql, [$oppId, $opListId, $display_name]);
        }

        //Cache rows from vtiger_oplist_answers_sections
        $sectionsArray = [];
        $sql = "SELECT * FROM `vtiger_oplist_answers_sections` WHERE opp_id=? AND oplist_id=?";
        $result = $db->pquery($sql, [$oppId, $opListId]);
        while ($row =& $result->fetchRow()) {
            $sectionsArray[$row['section_id']] = 1;
        }

        //Cache rows from vtiger_oplist_answers_questions
        $questionsArray = [];
        $sql = "SELECT * FROM `vtiger_oplist_answers_questions` WHERE `opp_id`=? AND `oplist_id`=? GROUP BY `opp_id`, `oplist_id`, `section_id`, `question_id` ORDER BY section_id, question_id";
        $result = $db->pquery($sql, [$oppId, $opListId]);
        while ($row =& $result->fetchRow()) {
            $questionsArray[$row['section_id']][$row['question_id']] = 1;
        }

        $multiAnswersArray = [];
        $sql = "SELECT * FROM `vtiger_oplist_answers_multi_option` WHERE opp_id=? AND oplist_id=? GROUP BY `section_id`, `question_id`, `option_id` ORDER BY `section_id`";
        $result = $db->pquery($sql, [$oppId, $opListId]);
        while ($row =& $result->fetchRow()) {
            $multiAnswersArray[$row['section_id']][$row['question_id']][$row['option_id']] = 1;
        }

        $numSections = $fieldList['numSections'];
        for ($i = 1; $i <= $numSections; $i++) {
            //$sectionId    = $i;
            $sectionName  = $fieldList[ 'section_name_'.$i ];
            $numQuestions = $fieldList[ 'numQuestions_'.$i ];
            $sectionOrder = $fieldList[ 'sectionOrder_'.$i ];
            //            $sql    = "SELECT * FROM `vtiger_oplist_answers_sections` WHERE opp_id = ? AND oplist_id = ? AND section_id = ?";
            //            $result = $db->pquery($sql, [$oppId, $opListId, $i]);
            //            $row    = $result->fetchRow();
            if (array_key_exists($i, $sectionsArray)) {
                //update the record
                $sql = "UPDATE `vtiger_oplist_answers_sections` SET section_name = ?, num_questions = ?, section_order = ?
                        WHERE opp_id = ? AND oplist_id = ? AND section_id = ?";
                $db->pquery($sql, [$sectionName, $numQuestions, $sectionOrder, $oppId, $opListId, $i]);
            } else {
                //add the record
                $sql = "INSERT INTO `vtiger_oplist_answers_sections` (opp_id, oplist_id,section_id,section_name,num_questions,section_order)
                        VALUES (?,?,?,?,?,?)";
                $db->pquery($sql, [$oppId, $opListId, $i, $sectionName, $numQuestions, $sectionOrder]);
            }
            for ($j = 1; $j <= $numQuestions; $j++) {
                $questionType            = $fieldList['question_type_'.$i.'_'.$j];
                if (empty($questionType)) {
                    //if there isn't a question type we shouldn't save it
                    continue;
                }
                $questionOrder           = $fieldList['question_order_'.$i.'_'.$j];
                $question                = $fieldList['question_'.$i.'_'.$j];
                $default_text_answer     = null;
                $default_bool_answer     = null;
                $default_date_answer     = null;
                $default_datetime_answer = null;
                $default_time_answer     = null;
                $default_int_answer      = null;
                $default_dec_answer      = null;
                $text_answer             = null;
                $bool_answer             = null;
                $date_answer             = null;
                $datetime_answer         = null;
                $time_answer             = null;
                $int_answer              = null;
                $dec_answer              = null;
                $multi_answer_id         = null;
                $allow_multiple_answers  = null;
                switch ($questionType) {
                    case 'Text':
                        $typeStr             = 'text';
                        $text_answer         = $fieldList['answer_'.$typeStr.'_'.$i.'_'.$j];
                        $default_text_answer = $fieldList['default_answer_'.$typeStr.'_'.$i.'_'.$j];
                        break;
                    case 'Yes/No':
                        $typeStr             = 'bool';
                        $bool_answer         = ($fieldList['answer_'.$typeStr.'_'.$i.'_'.$j] == 'on')?1:0;
                        $default_bool_answer = ($fieldList['default_answer_'.$typeStr.'_'.$i.'_'.$j] == 'on')?1:0;
                        break;
                    case 'Date':
                        $typeStr                         = 'date';
                        $date_answer_user_format         = $fieldList['answer_'.$typeStr.'_'.$i.'_'.$j];
                        $date_answer                     = DateTimeField::convertToDBFormat($date_answer_user_format);
                        $default_date_answer_user_format = $fieldList['default_answer_'.$typeStr.'_'.$i.'_'.$j];
                        $default_date_answer             = DateTimeField::convertToDBFormat($default_date_answer_user_format);
                        break;
                    case 'Date and Time':
                        $typeStr         = 'datetime';
                        $datetime_answer_date_user_format = $fieldList[ 'answer_'.$typeStr.'_date_'.$i.'_'.$j ];
                        $datetime_answer_time_user_format = $fieldList[ 'answer_'.$typeStr.'_time_'.$i.'_'.$j ];
                        $datetime_answer_date = DateTimeField::convertToDBFormat($datetime_answer_date_user_format);
                        $datetime_answer_time = (($datetime_answer_time_user_format != null)
                            ?Vtiger_Time_UIType::getTimeValueWithSeconds($datetime_answer_time_user_format):'');
                        $datetime_answer = (!empty($datetime_answer_time)?($datetime_answer_date.' '.$datetime_answer_time):null);
                        $default_datetime_answer_date_user_format = $fieldList[ 'default_answer_'.$typeStr.'_date_'.$i.'_'.$j ];
                        $default_datetime_answer_time_user_format = $fieldList[ 'default_answer_'.$typeStr.'_time_'.$i.'_'.$j ];
                        $default_datetime_answer_date = DateTimeField::convertToDBFormat($default_datetime_answer_date_user_format);
                        $default_datetime_answer_time = (($default_datetime_answer_time_user_format != null)
                            ?Vtiger_Time_UIType::getTimeValueWithSeconds($default_datetime_answer_time_user_format):'');
                        $default_datetime_answer = (!empty($default_datetime_answer_time)?($default_datetime_answer_date.' '.$default_datetime_answer_time):null);
                        break;
                    case 'Time':
                        $typeStr     = 'time';
                        $time_answer_user_format = (isset($fieldList[ 'answer_'.$typeStr.'_'.$i.'_'.$j ])?$fieldList[ 'answer_'.$typeStr.'_'.$i.'_'.$j ]:null);
                        $time_answer = (($time_answer_user_format != null)?Vtiger_Time_UIType::getTimeValueWithSeconds($time_answer_user_format):'');
                        $default_time_answer_user_format = (isset($fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ])?$fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ]:null);
                        $default_time_answer = (($default_time_answer_user_format != null)?Vtiger_Time_UIType::getTimeValueWithSeconds($default_time_answer_user_format):'');
                        break;
                    case 'Quantity':
                        $typeStr    = 'number';
                        $int_answer = (!$fieldList[ 'answer_use_decimal_'.$i.'_'.$j ]) ? $fieldList[ 'answer_'
                                                                                                     .$typeStr.'_'.$i.'_'.$j ] : null;
                        $dec_answer = ($fieldList[ 'answer_use_decimal_'.$i.'_'.$j ]) ? $fieldList[ 'answer_'
                                                                                                    .$typeStr.'_'.$i.'_'.$j ] : null;
                        $default_int_answer = (!$fieldList[ 'default_answer_use_decimal_'.$i.'_'.$j ]) ?
                            $fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ] : null;
                        $default_dec_answer = ($fieldList[ 'default_answer_use_decimal_'.$i.'_'.$j ]) ?
                            $fieldList[ 'default_answer_'.$typeStr.'_'.$i.'_'.$j ] : null;
                        break;
                    case 'Multiple Choice':
                        $multi_answer_id = $j;
                        $numOptions      = $fieldList[ 'numOptions_'.$i.'_'.$j ];
                        $allow_multiple_answers = $fieldList['default_answer_select_multiple_'.$i.'_'.$j];
                        for ($k = 1; $k <= $numOptions; $k++) {
                            $optionOrder = $fieldList[ 'option_order_'.$i.'_'.$j.'_'.$k ];
                            $default_selected = $fieldList['default_multi_option_'.$i.'_'.$j.'_'.$k];
                            if ($allow_multiple_answers == 1) {
                                $selected = ($fieldList['multi_option_'.$i.'_'.$j.'_'.$k] == 'on')?1:0;
                                //file_put_contents('logs/devLog.log', "\n selected : ".$selected, FILE_APPEND);
                            } else {
                                $selected    = ($fieldList[ 'MultiOption_'.$i.'_'.$j ] == $k)?1:0;
                            }
                            $answer      = $fieldList[ 'multi_option_answer_'.$i.'_'.$j.'_'.$k ];
                            //                            $sql         = "SELECT * FROM `vtiger_oplist_answers_multi_option` WHERE oplist_id = ? AND section_id = ? AND question_id = ? AND option_id = ? AND opp_id = ?";
                            //                            $result      = $db->pquery($sql, [$opListId, $i, $j, $k, $oppId]);
                            //                            $row         = $result->fetchRow();
                            if (array_key_exists($i, $multiAnswersArray) && array_key_exists($j, $multiAnswersArray[$i]) && array_key_exists($k, $multiAnswersArray[$i][$j])) {
                                //update existing
                                $sql = "UPDATE `vtiger_oplist_answers_multi_option` SET option_order = ?, selected = ?, answer = ? WHERE oplist_id = ? AND section_id = ? AND question_id = ? AND option_id = ?";
                                $db->pquery($sql, [$optionOrder, $selected, $answer, $opListId, $i, $j, $k]);
                            } else {
                                //insert new
                                $sql = "INSERT INTO `vtiger_oplist_answers_multi_option` (opp_id,
                                                                                          oplist_id,
                                                                                          section_id,
                                                                                          question_id,
                                                                                          option_id,
                                                                                          option_order,
                                                                                          default_selected,
                                                                                          selected,
                                                                                          answer)
                                        VALUES (?,?,?,?,?,?,?,?,?)";
                                $db->pquery($sql, [$oppId,
                                                   $opListId,
                                                   $i,
                                                   $j,
                                                   $k,
                                                   $optionOrder,
                                                   $default_selected,
                                                   $selected,
                                                   $answer]);
                            }
                        }
                        break;
                    default:
                        break;
                }
                //                $sql = "SELECT * FROM `vtiger_oplist_answers_questions` WHERE opp_id=? AND oplist_id=? AND section_id=? AND question_id=?";
                //                $result = $db->pquery($sql, [$oppId,$opListId,$i,$j]);
                //                $row = $result->fetchRow();
                if (array_key_exists($i, $questionsArray) && array_key_exists($j, $questionsArray[$i])) {
                    //update existing question
                    $sql = "UPDATE `vtiger_oplist_answers_questions`
                            SET question_type = ?,
                                question_order = ?,
                                question = ?,
                                text_answer = ?,
                                bool_answer = ?,
                                date_answer = ?,
                                datetime_answer = ?,
                                time_answer = ?,
                                int_answer = ?,
                                dec_answer = ?,
                                multi_answer_id = ?,
                                allow_multiple_answers = ?
                            WHERE opp_id = ? AND oplist_id = ? AND section_id = ? AND question_id = ?";
                    $db->pquery($sql, [$questionType, $questionOrder, $question, $text_answer, $bool_answer, $date_answer,
                                       $datetime_answer, $time_answer, $int_answer, $dec_answer, $multi_answer_id,
                                       $allow_multiple_answers, $oppId, $opListId, $i, $j]);
                } else {
                    //insert new
                    $sql = "INSERT INTO `vtiger_oplist_answers_questions` (opp_id,
                                                                           oplist_id,
                                                                           section_id,
                                                                           question_id,
                                                                           question_type,
                                                                           question_order,
                                                                           question,
                                                                           default_text_answer,
                                                                           default_bool_answer,
                                                                           default_date_answer,
                                                                           default_datetime_answer,
                                                                           default_time_answer,
                                                                           default_int_answer,
                                                                           default_dec_answer,
                                                                           text_answer,
                                                                           bool_answer,
                                                                           date_answer,
                                                                           datetime_answer,
                                                                           time_answer,
                                                                           int_answer,
                                                                           dec_answer,
                                                                           multi_answer_id,
                                                                           allow_multiple_answers)
                                                                           VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    /*echo "<br><b>SQL : </b>".$sql;
                    echo "<br><b>Params : </b>".print_r([$oppId,
                                                         $opListId,
                                                         $i,
                                                         $j,
                                                         $questionType,
                                                         $questionOrder,
                                                         $question,
                                                         $default_text_answer,
                                                         $default_bool_answer,
                                                         $default_date_answer,
                                                         $default_datetime_answer,
                                                         $default_time_answer,
                                                         $default_int_answer,
                                                         $default_dec_answer,
                                                         $text_answer,
                                                         $bool_answer,
                                                         $date_answer,
                                                         $datetime_answer,
                                                         $time_answer,
                                                         $int_answer,
                                                         $dec_answer,
                                                         $multi_answer_id,
                                                         $allow_multiple_answers],true);*/
                    $db->pquery($sql, [$oppId,
                                       $opListId,
                                       $i,
                                       $j,
                                       $questionType,
                                       $questionOrder,
                                       $question,
                                       $default_text_answer,
                                       $default_bool_answer,
                                       $default_date_answer,
                                       $default_datetime_answer,
                                       $default_time_answer,
                                       $default_int_answer,
                                       $default_dec_answer,
                                       $text_answer,
                                       $bool_answer,
                                       $date_answer,
                                       $datetime_answer,
                                       $time_answer,
                                       $int_answer,
                                       $dec_answer,
                                       $multi_answer_id,
                                       $allow_multiple_answers]);
                }
            }
        }
        if (empty($noRedirect)) {
            header('Location:  index.php?module=Opportunities&relatedModule=OPList&view=Detail&record='.
                   $oppId.
                   '&mode=showRelatedList&saved=true&tab_label=OP%20Lists#');
        }
        //*/
    }
}
