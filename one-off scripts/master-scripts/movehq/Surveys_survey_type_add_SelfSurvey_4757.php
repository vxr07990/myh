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

$db = &PearDatabase::getInstance();
//$fieldName = 'survey_type';
$newPickList = ['On-site', 'Virtual', 'Self Survey'];

$moduleName = 'Surveys';
$blockName = 'LBL_SURVEYS_INFORMATION';

$moduleInstance = Vtiger_Module::getInstance($moduleName);

if (!$moduleInstance) {
    print "ERROR: No moduleName " . $moduleName . PHP_EOL;
    return;
}

$blockInstance = Vtiger_Block::getInstance($blockName, $moduleInstance);

if (!$blockInstance) {
    print "ERROR: No blockName " . $blockName . PHP_EOL;
    return;
}

//create self survey link text field that is seen on detail view and will contain the selfSurvey url.
$newFieldName = 'self_survey_url';
$fieldInstance = Vtiger_Field::getInstance($newFieldName, $moduleInstance);
if (!$fieldInstance) {
    $fieldInstance               = new Vtiger_Field();
    $fieldInstance->label        = 'LBL_SELF_SURVEY_URL';
    $fieldInstance->name         = $newFieldName;
    $fieldInstance->table        = 'vtiger_surveys';
    $fieldInstance->column       = $newFieldName;
    $fieldInstance->columntype   = 'VARCHAR(255)';
    $fieldInstance->uitype       = 17;
    $fieldInstance->typeofdata   = 'V~O';
    $fieldInstance->displaytype  = 1;
    $fieldInstance->readonly     = 0;
    $fieldInstance->presence     = 2;
    $fieldInstance->defaultvalue = '';
    $blockInstance->addField($fieldInstance);
}

//Update the survey_type field to have Self Survey option.

$surveyTypeField = Vtiger_Field::getInstance('survey_type', $moduleInstance);

if ($surveyTypeField) {
    echo "<h4>Field <b>survey_type</b> already exists!</h4>";
    if (Vtiger_Utils::CheckTable('vtiger_survey_type')) {
        $stmt = 'TRUNCATE TABLE `vtiger_survey_type`';
        $db->query($stmt);
    }
    $surveyTypeField->setPicklistValues($newPickList);
} else {
    $surveyTypeField             = new Vtiger_Field();
    $surveyTypeField->label      = 'LBL_SURVEYS_TYPE';
    $surveyTypeField->name       = 'survey_type';
    $surveyTypeField->table      = 'vtiger_surveys';
    $surveyTypeField->column     = 'survey_type';
    $surveyTypeField->columntype = 'VARCHAR(255)';
    $surveyTypeField->uitype     = 16;
    $surveyTypeField->typeofdata = 'V~M';
    $blockInstance->addField($surveyTypeField);
    $surveyTypeField->setPicklistValues($newPickList);
}

//Can't use the event handler
////Add before Save handler for the self survey option value.
//$stmt = 'SELECT 1 FROM vtiger_eventhandlers WHERE handler_class=?';
//$res = $db->pquery($stmt, ['selfSurveyEventHandler']);
//if($db->num_rows($res) == 0) {
//    Vtiger_Event::register($moduleInstance, 'vtiger.entity.beforesave', 'selfSurveyEventHandler', 'modules/Surveys/handlers/selfSurveyEventHandler.php');
//}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
