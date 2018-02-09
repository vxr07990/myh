<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


$Vtiger_Utils_Log = true;
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

echo "<h1>Creating Tables for OP Answers List saving</h1>";
echo "<ol>";
if (!Vtiger_Utils::CheckTable('vtiger_oplist_answers_sections')) {
    echo "<li>creating vtiger_oplist_answers_sections </li><br>";
    Vtiger_Utils::CreateTable('vtiger_oplist_answers_sections',
                              '(
							  	opp_id INT(19),
							    oplist_id INT(19),
								section_id INT(19),
								section_name VARCHAR(50),
								num_questions INT(19),
								section_order INT(19)
								)', true);
} else {
    echo "<li>vtiger_oplist_answers_sections table already exists</li>";
}

if (!Vtiger_Utils::CheckTable('vtiger_oplist_answers_questions')) {
    echo "<li>creating vtiger_oplist_answers_questions </li><br>";
    Vtiger_Utils::CreateTable('vtiger_oplist_answers_questions',
                              '(
							    opp_id INT(19),
							    oplist_id INT(19),
								section_id INT(19),
								question_id INT(19),
								question_type VARCHAR(50),
								question_order INT(19),
								question VARCHAR(255),
								default_text_answer VARCHAR(255),
								default_bool_answer TINYINT(1),
								default_date_answer DATE,
								default_datetime_answer DATETIME,
								default_time_answer TIME,
								default_int_answer INT(19),
								default_dec_answer DECIMAL(10,2),
								text_answer VARCHAR(255),
								bool_answer TINYINT(1),
								date_answer DATE,
								datetime_answer DATETIME,
								time_answer TIME,
								int_answer INT(19),
								dec_answer DECIMAL(10,2),
								multi_answer_id INT(19),
								num_options INT(19),
								allow_multiple_answers TINYINT(1)
								)', true);
} else {
    echo "<li>vtiger_oplist_answers_questions table already exists</li>";
}

if (!Vtiger_Utils::CheckTable('vtiger_oplist_answers_multi_option')) {
    echo "<li>creating vtiger_oplist_answers_multi_option </li><br>";
    Vtiger_Utils::CreateTable('vtiger_oplist_answers_multi_option',
                              '(
							    opp_id INT(19),
								oplist_id INT(19),
								section_id INT(19),
								question_id INT(19),
								option_id INT(19),
								option_order INT(19),
								default_selected TINYINT(1),
								selected TINYINT(1),
								answer VARCHAR(255)
								)', true);
} else {
    echo "<li>vtiger_oplist_answers_multi_option table already exists</li>";
}

if (!Vtiger_Utils::CheckTable('vtiger_oplist_answers')) {
    echo "<li>creating vtiger_oplist_answers </li><br>";
    Vtiger_Utils::CreateTable('vtiger_oplist_answers',
                              '(
							    opp_id INT(19),
								oplist_id INT(19),
								display_name VARCHAR(255)
								)', true);
} else {
    echo "<li>vtiger_oplist_answers table already exists</li>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";