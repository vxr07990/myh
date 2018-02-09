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


$accidentsInstance = Vtiger_Module::getInstance('Accidents');
if ($accidentsInstance) {
    echo "<br> module 'Accidents' already exists. <br>";
} else {
    $accidentsInstance = new Vtiger_Module();
    $accidentsInstance->name = 'Accidents';
    $accidentsInstance->save();
    $accidentsInstance->initTables();
    $accidentsInstance->setDefaultSharing();
    $accidentsInstance->initWebservice();
    ModTracker::enableTrackingForModule($accidentsInstance->id);
}

$accidentsblockInstance1 = Vtiger_Block::getInstance('LBL_ACCIDENTS_INFORMATION', $accidentsInstance);
if ($accidentsblockInstance1) {
    echo "<br> block 'LBL_ACCIDENTS_INFORMATION' already exists.<br>";
} else {
    $accidentsblockInstance1 = new Vtiger_Block();
    $accidentsblockInstance1->label = 'LBL_ACCIDENTS_INFORMATION';
    $accidentsInstance->addBlock($accidentsblockInstance1);
}

$accidentsblockInstance2 = Vtiger_Block::getInstance('LBL_ACCIDENTS_RECORDUPDATE', $accidentsInstance);
if ($accidentsblockInstance2) {
    echo "<br> block 'LBL_ACCIDENTS_RECORDUPDATE' already exists.<br>";
} else {
    $accidentsblockInstance2 = new Vtiger_Block();
    $accidentsblockInstance2->label = 'LBL_ACCIDENTS_RECORDUPDATE';
    $accidentsInstance->addBlock($accidentsblockInstance2);
}

$field1 = Vtiger_Field::getInstance('accidents_date', $accidentsInstance);
if ($field1) {
    echo "<br> Field 'accidents_date' is already present. <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_ACCIDENTS_DATE';
    $field1->name = 'accidents_date';
    $field1->table = 'vtiger_accidents';
    $field1->column = 'accidents_date';
    $field1->columntype = 'DATE';
    $field1->uitype = 5;
    $field1->typeofdata = 'D~M';


    $accidentsblockInstance1->addField($field1);

    $accidentsInstance->setEntityIdentifier($field1);
}

$fieldSafetyDate = Vtiger_Field::getInstance('date_reported_to_safety', $accidentsInstance);
if ($fieldSafetyDate) {
    echo "The date_reported_to_safety field already exists<br>\n";
} else {
    $fieldSafetyDate             = new Vtiger_Field();
    $fieldSafetyDate->label      = 'LBL_DATE_REPORTED_TO_SAFETY';
    $fieldSafetyDate->name       = 'date_reported_to_safety';
    $fieldSafetyDate->table      = 'vtiger_accidents';
    $fieldSafetyDate->column     = 'date_reported_to_safety';
    $fieldSafetyDate->columntype = 'DATE';
    $fieldSafetyDate->uitype     = 5;
    $fieldSafetyDate->typeofdata = 'D~O';
    $accidentsblockInstance1->addField($fieldSafetyDate);
}
$field6 = Vtiger_Field::getInstance('accidents_employees', $accidentsInstance);
if ($field6) {
    echo "<br> Field 'accidents_employees' is already present. <br>";
} else {
    $field6 = new Vtiger_Field();
    $field6->label = 'LBL_ACCIDENTS_EMPLOYEES';
    $field6->name = 'accidents_employees';
    $field6->table = 'vtiger_accidents';
    $field6->column = 'accidents_employees';
    $field6->columntype = 'VARCHAR(100)';
    $field6->uitype = 10;
    $field6->typeofdata = 'V~O';

    $accidentsblockInstance1->addField($field6);
    $field6->setRelatedModules(array('Employees'));
}

$fieldTakenBy = Vtiger_Field::getInstance('report_taken_by', $accidentsInstance);
if ($fieldTakenBy) {
    echo "The report_taken_by field already exists<br>\n";
} else {
    $fieldTakenBy             = new Vtiger_Field();
    $fieldTakenBy->label      = 'LBL_REPORT_TAKEN_BY';
    $fieldTakenBy->name       = 'report_taken_by';
    $fieldTakenBy->table      = 'vtiger_accidents';
    $fieldTakenBy->column     = 'report_taken_by';
    $fieldTakenBy->columntype = 'VARCHAR(100)';
    $fieldTakenBy->uitype     = 10;
    $fieldTakenBy->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldTakenBy);
    $fieldTakenBy->setRelatedModules(array('Employees'));
}

$fieldCity = Vtiger_Field::getInstance('accidents_city', $accidentsInstance);
if ($fieldCity) {
    echo "The accidents_city field already exists<br>\n";
} else {
    $fieldCity             = new Vtiger_Field();
    $fieldCity->label      = 'LBL_ACCIDENTS_CITY';
    $fieldCity->name       = 'accidents_city';
    $fieldCity->table      = 'vtiger_accidents';
    $fieldCity->column     = 'accidents_city';
    $fieldCity->columntype = 'VARCHAR(50)';
    $fieldCity->uitype     = 1;
    $fieldCity->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldCity);
}
$fieldState = Vtiger_Field::getInstance('accidents_state', $accidentsInstance);
if ($fieldState) {
    echo "The accidents_state field already exists<br>\n";
} else {
    $fieldState             = new Vtiger_Field();
    $fieldState->label      = 'LBL_ACCIDENTS_STATE';
    $fieldState->name       = 'accidents_state';
    $fieldState->table      = 'vtiger_accidents';
    $fieldState->column     = 'accidents_state';
    $fieldState->columntype = 'VARCHAR(50)';
    $fieldState->uitype     = 1;
    $fieldState->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldState);
}
$fieldOnDuty = Vtiger_Field::getInstance('on_duty', $accidentsInstance);
if ($fieldOnDuty) {
    echo "The on_duty field already exists<br>\n";
} else {
    $fieldOnDuty             = new Vtiger_Field();
    $fieldOnDuty->label      = 'LBL_ON_DUTY';
    $fieldOnDuty->name       = 'on_duty';
    $fieldOnDuty->table      = 'vtiger_accidents';
    $fieldOnDuty->column     = 'on_duty';
    $fieldOnDuty->columntype = 'VARCHAR(3)';
    $fieldOnDuty->uitype     = 16;
    $fieldOnDuty->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldOnDuty);
    $fieldOnDuty->setPicklistValues(array('Yes', 'No'));
}
$fieldAuthority = Vtiger_Field::getInstance('authority', $accidentsInstance);
if ($fieldAuthority) {
    echo "The authority field already exists<br>\n";
} else {
    $fieldAuthority             = new Vtiger_Field();
    $fieldAuthority->label      = 'LBL_AUTHORITY';
    $fieldAuthority->name       = 'authority';
    $fieldAuthority->table      = 'vtiger_accidents';
    $fieldAuthority->column     = 'authority';
    $fieldAuthority->columntype = 'VARCHAR(50)';
    $fieldAuthority->uitype     = 16;
    $fieldAuthority->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldAuthority);
    $fieldAuthority->setPicklistValues(array('IC - Independent Contractor', 'TSC - Terminal Service Contractor', 'IR - Other/Employee'));
}
$fieldDOTRecordable = Vtiger_Field::getInstance('dot_recordable', $accidentsInstance);
if ($fieldDOTRecordable) {
    echo "The dot_recordable field already exists<br>\n";
} else {
    $fieldDOTRecordable             = new Vtiger_Field();
    $fieldDOTRecordable->label      = 'LBL_DOT_RECORDABLE';
    $fieldDOTRecordable->name       = 'dot_recordable';
    $fieldDOTRecordable->table      = 'vtiger_accidents';
    $fieldDOTRecordable->column     = 'dot_recordable';
    $fieldDOTRecordable->columntype = 'VARCHAR(3)';
    $fieldDOTRecordable->uitype     = 16;
    $fieldDOTRecordable->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldDOTRecordable);
    $fieldDOTRecordable->setPicklistValues(array('Yes', 'No'));
}
$fieldFatalities = Vtiger_Field::getInstance('no_fatalities', $accidentsInstance);
if ($fieldFatalities) {
    echo "The no_fatalities field already exists<br>\n";
} else {
    $fieldFatalities             = new Vtiger_Field();
    $fieldFatalities->label      = 'LBL_NO_FATALITIES';
    $fieldFatalities->name       = 'no_fatalities';
    $fieldFatalities->table      = 'vtiger_accidents';
    $fieldFatalities->column     = 'no_fatalities';
    $fieldFatalities->columntype = 'INT(2)';
    $fieldFatalities->uitype     = 7;
    $fieldFatalities->typeofdata = 'I~O~MIN=0~MAX=50';
    $accidentsblockInstance1->addField($fieldFatalities);
}
$fieldPolice = Vtiger_Field::getInstance('police_involved', $accidentsInstance);
if ($fieldPolice) {
    echo "The police_involved field already exists<br>\n";
} else {
    $fieldPolice             = new Vtiger_Field();
    $fieldPolice->label      = 'LBL_POLICE_INVOLVED';
    $fieldPolice->name       = 'police_involved';
    $fieldPolice->table      = 'vtiger_accidents';
    $fieldPolice->column     = 'police_involved';
    $fieldPolice->columntype = 'VARCHAR(3)';
    $fieldPolice->uitype     = 16;
    $fieldPolice->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldPolice);
    $fieldPolice->setPicklistValues(array('Yes', 'No'));
}
$fieldInjuries = Vtiger_Field::getInstance('no_injuries', $accidentsInstance);
if ($fieldInjuries) {
    echo "The no_injuries field already exists<br>\n";
} else {
    $fieldInjuries             = new Vtiger_Field();
    $fieldInjuries->label      = 'LBL_NO_INJURIES';
    $fieldInjuries->name       = 'no_injuries';
    $fieldInjuries->table      = 'vtiger_accidents';
    $fieldInjuries->column     = 'no_injuries';
    $fieldInjuries->columntype = 'INT(2)';
    $fieldInjuries->uitype     = 7;
    $fieldInjuries->typeofdata = 'I~O~MIN=0~MAX=50';
    $accidentsblockInstance1->addField($fieldInjuries);
}
$fieldCitation = Vtiger_Field::getInstance('citation_issued', $accidentsInstance);
if ($fieldCitation) {
    echo "The citation_issued field already exists<br>\n";
} else {
    $fieldCitation             = new Vtiger_Field();
    $fieldCitation->label      = 'LBL_CITATION_ISSUED';
    $fieldCitation->name       = 'citation_issued';
    $fieldCitation->table      = 'vtiger_accidents';
    $fieldCitation->column     = 'citation_issued';
    $fieldCitation->columntype = 'VARCHAR(3)';
    $fieldCitation->uitype     = 16;
    $fieldCitation->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldCitation);
    $fieldCitation->setPicklistValues(array('Yes', 'No'));
}
$fieldTowed = Vtiger_Field::getInstance('no_towed', $accidentsInstance);
if ($fieldTowed) {
    echo "The no_towed field already exists<br>\n";
} else {
    $fieldTowed             = new Vtiger_Field();
    $fieldTowed->label      = 'LBL_NO_TOWED';
    $fieldTowed->name       = 'no_towed';
    $fieldTowed->table      = 'vtiger_accidents';
    $fieldTowed->column     = 'no_towed';
    $fieldTowed->columntype = 'INT(2)';
    $fieldTowed->uitype     = 7;
    $fieldTowed->typeofdata = 'I~O~MIN=0~MAX=50';
    $accidentsblockInstance1->addField($fieldTowed);
}

$fieldDUITestType = Vtiger_Field::getInstance('dui_test_type', $accidentsInstance);
if ($fieldDUITestType) {
    echo "The dui_test_type field already exists<br>\n";
} else {
    $fieldDUITestType             = new Vtiger_Field();
    $fieldDUITestType->label      = 'LBL_DUI_TEST_TYPE';
    $fieldDUITestType->name       = 'dui_test_type';
    $fieldDUITestType->table      = 'vtiger_accidents';
    $fieldDUITestType->column     = 'dui_test_type';
    $fieldDUITestType->columntype = 'VARCHAR(25)';
    $fieldDUITestType->uitype     = 16;
    $fieldDUITestType->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldDUITestType);
    $fieldDUITestType->setPicklistValues(array('D - Drug Test', 'D/A - Drug and Alcohol Test', 'N - None'));
}
$fieldDUITestDate = Vtiger_Field::getInstance('dui_test_date', $accidentsInstance);
if ($fieldDUITestDate) {
    echo "The dui_test_date field already exists<br>\n";
} else {
    $fieldDUITestDate             = new Vtiger_Field();
    $fieldDUITestDate->label      = 'LBL_DUI_TEST_DATE';
    $fieldDUITestDate->name       = 'dui_test_date';
    $fieldDUITestDate->table      = 'vtiger_accidents';
    $fieldDUITestDate->column     = 'dui_test_date';
    $fieldDUITestDate->columntype = 'DATE';
    $fieldDUITestDate->uitype     = 5;
    $fieldDUITestDate->typeofdata = 'D~O';
    $accidentsblockInstance1->addField($fieldDUITestDate);
}
$fieldDrugTestReceived = Vtiger_Field::getInstance('drug_test_received', $accidentsInstance);
if ($fieldDrugTestReceived) {
    echo "The drug_test_received field already exists<br>\n";
} else {
    $fieldDrugTestReceived             = new Vtiger_Field();
    $fieldDrugTestReceived->label      = 'LBL_DRUG_TEST_RECEIVED';
    $fieldDrugTestReceived->name       = 'drug_test_received';
    $fieldDrugTestReceived->table      = 'vtiger_accidents';
    $fieldDrugTestReceived->column     = 'drug_test_received';
    $fieldDrugTestReceived->columntype = 'DATE';
    $fieldDrugTestReceived->uitype     = 5;
    $fieldDrugTestReceived->typeofdata = 'D~O';
    $accidentsblockInstance1->addField($fieldDrugTestReceived);
}
$fieldBACTestReceived = Vtiger_Field::getInstance('bac_test_received', $accidentsInstance);
if ($fieldBACTestReceived) {
    echo "The bac_test_received field already exists<br>\n";
} else {
    $fieldBACTestReceived             = new Vtiger_Field();
    $fieldBACTestReceived->label      = 'LBL_BAC_TEST_RECEIVED';
    $fieldBACTestReceived->name       = 'bac_test_received';
    $fieldBACTestReceived->table      = 'vtiger_accidents';
    $fieldBACTestReceived->column     = 'bac_test_received';
    $fieldBACTestReceived->columntype = 'DATE';
    $fieldBACTestReceived->uitype     = 5;
    $fieldBACTestReceived->typeofdata = 'D~O';
    $accidentsblockInstance1->addField($fieldBACTestReceived);
}
$fieldDriverReport = Vtiger_Field::getInstance('driver_report_hrs', $accidentsInstance);
if ($fieldDriverReport) {
    echo "The driver_report_hrs field already exists<br>\n";
} else {
    $fieldDriverReport             = new Vtiger_Field();
    $fieldDriverReport->label      = 'LBL_DRIVER_REPORT_HRS';
    $fieldDriverReport->name       = 'driver_report_hrs';
    $fieldDriverReport->table      = 'vtiger_accidents';
    $fieldDriverReport->column     = 'driver_report_hrs';
    $fieldDriverReport->columntype = 'VARCHAR(3)';
    $fieldDriverReport->uitype     = 16;
    $fieldDriverReport->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldDriverReport);
    $fieldDriverReport->setPicklistValues(array('Yes', 'No'));
}
$fieldWrittenReport = Vtiger_Field::getInstance('written_report_hrs', $accidentsInstance);
if ($fieldWrittenReport) {
    echo "The written_report_hrs field already exists<br>\n";
} else {
    $fieldWrittenReport             = new Vtiger_Field();
    $fieldWrittenReport->label      = 'LBL_WRITTEN_REPORT_HRS';
    $fieldWrittenReport->name       = 'written_report_hrs';
    $fieldWrittenReport->table      = 'vtiger_accidents';
    $fieldWrittenReport->column     = 'written_report_hrs';
    $fieldWrittenReport->columntype = 'VARCHAR(3)';
    $fieldWrittenReport->uitype     = 16;
    $fieldWrittenReport->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldWrittenReport);
    $fieldWrittenReport->setPicklistValues(array('Yes', 'No'));
}
$fieldType = Vtiger_Field::getInstance('incident_type', $accidentsInstance);
if ($fieldType) {
    echo "The accidents_type field already exists<br>\n";
} else {
    $fieldType             = new Vtiger_Field();
    $fieldType->label      = 'LBL_INCIDENT_TYPE';
    $fieldType->name       = 'incident_type';
    $fieldType->table      = 'vtiger_accidents';
    $fieldType->column     = 'incident_type';
    $fieldType->columntype = 'VARCHAR(25)';
    $fieldType->uitype     = 16;
    $fieldType->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldType);
    $fieldType->setPicklistValues(array('A - Accident', 'I - Incident'));
}
$fieldPreventable = Vtiger_Field::getInstance('accidents_preventable', $accidentsInstance);
if ($fieldPreventable) {
    echo "The accidents_preventable field already exists<br>\n";
} else {
    $fieldPreventable             = new Vtiger_Field();
    $fieldPreventable->label      = 'LBL_ACCIDENTS_PREVENTABLE';
    $fieldPreventable->name       = 'accidents_preventable';
    $fieldPreventable->table      = 'vtiger_accidents';
    $fieldPreventable->column     = 'accidents_preventable';
    $fieldPreventable->columntype = 'VARCHAR(30)';
    $fieldPreventable->uitype     = 16;
    $fieldPreventable->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldPreventable);
    $fieldPreventable->setPicklistValues(array('N - Nonpreventable', 'P - Preventable'));
}
$fieldFaultVehicle = Vtiger_Field::getInstance('vehicle_causing_impact', $accidentsInstance);
if ($fieldFaultVehicle) {
    echo "The vehicle_causing_impact field already exists<br>\n";
} else {
    $fieldFaultVehicle             = new Vtiger_Field();
    $fieldFaultVehicle->label      = 'LBL_VEHICLE_CAUSING_IMPACT';
    $fieldFaultVehicle->name       = 'vehicle_causing_impact';
    $fieldFaultVehicle->table      = 'vtiger_accidents';
    $fieldFaultVehicle->column     = 'vehicle_causing_impact';
    $fieldFaultVehicle->columntype = 'VARCHAR(255)';
    $fieldFaultVehicle->uitype     = 1;
    $fieldFaultVehicle->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldFaultVehicle);
}
$fieldAccidentType = Vtiger_Field::getInstance('accident_type', $accidentsInstance);
if ($fieldAccidentType) {
    echo "The accident_type field already exists<br>\n";
} else {
    $fieldAccidentType             = new Vtiger_Field();
    $fieldAccidentType->label      = 'LBL_ACCIDENT_TYPE';
    $fieldAccidentType->name       = 'accident_type';
    $fieldAccidentType->table      = 'vtiger_accidents';
    $fieldAccidentType->column     = 'accident_type';
    $fieldAccidentType->columntype = 'VARCHAR(255)';
    $fieldAccidentType->uitype     = 1;
    $fieldAccidentType->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldAccidentType);
}
$fieldV1Doing = Vtiger_Field::getInstance('v_one_doing', $accidentsInstance);
if ($fieldV1Doing) {
    echo "The v_one_doing field already exists<br>\n";
} else {
    $fieldV1Doing             = new Vtiger_Field();
    $fieldV1Doing->label      = 'LBL_V_ONE_DOING';
    $fieldV1Doing->name       = 'v_one_doing';
    $fieldV1Doing->table      = 'vtiger_accidents';
    $fieldV1Doing->column     = 'v_one_doing';
    $fieldV1Doing->columntype = 'TEXT';
    $fieldV1Doing->uitype     = 19;
    $fieldV1Doing->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldV1Doing);
}
$fieldV2Doing = Vtiger_Field::getInstance('v_two_doing', $accidentsInstance);
if ($fieldV2Doing) {
    echo "The v_two_doing field already exists<br>\n";
} else {
    $fieldV2Doing             = new Vtiger_Field();
    $fieldV2Doing->label      = 'LBL_V_TWO_DOING';
    $fieldV2Doing->name       = 'v_two_doing';
    $fieldV2Doing->table      = 'vtiger_accidents';
    $fieldV2Doing->column     = 'v_two_doing';
    $fieldV2Doing->columntype = 'TEXT';
    $fieldV2Doing->uitype     = 19;
    $fieldV2Doing->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldV2Doing);
}
$fieldLateReportDate = Vtiger_Field::getInstance('late_reporting_sent', $accidentsInstance);
if ($fieldLateReportDate) {
    echo "The late_reporting_sent field already exists<br>\n";
} else {
    $fieldLateReportDate             = new Vtiger_Field();
    $fieldLateReportDate->label      = 'LBL_LATE_REPORTING_SENT';
    $fieldLateReportDate->name       = 'late_reporting_sent';
    $fieldLateReportDate->table      = 'vtiger_accidents';
    $fieldLateReportDate->column     = 'late_reporting_sent';
    $fieldLateReportDate->columntype = 'DATE';
    $fieldLateReportDate->uitype     = 5;
    $fieldLateReportDate->typeofdata = 'D~O';
    $accidentsblockInstance1->addField($fieldLateReportDate);
}
$fieldLetterNumber = Vtiger_Field::getInstance('letter_no', $accidentsInstance);
if ($fieldLetterNumber) {
    echo "The letter_no field already exists<br>\n";
} else {
    $fieldLetterNumber             = new Vtiger_Field();
    $fieldLetterNumber->label      = 'LBL_LETTER_NO';
    $fieldLetterNumber->name       = 'letter_no';
    $fieldLetterNumber->table      = 'vtiger_accidents';
    $fieldLetterNumber->column     = 'letter_no';
    $fieldLetterNumber->columntype = 'VARCHAR(50)';
    $fieldLetterNumber->uitype     = 1;
    $fieldLetterNumber->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldLetterNumber);
}
$fieldDriverLetterDate = Vtiger_Field::getInstance('driver_letter_date', $accidentsInstance);
if ($fieldDriverLetterDate) {
    echo "The driver_letter_date field already exists<br>\n";
} else {
    $fieldDriverLetterDate             = new Vtiger_Field();
    $fieldDriverLetterDate->label      = 'LBL_DRIVER_LETTER_DATE';
    $fieldDriverLetterDate->name       = 'driver_letter_date';
    $fieldDriverLetterDate->table      = 'vtiger_accidents';
    $fieldDriverLetterDate->column     = 'driver_letter_date';
    $fieldDriverLetterDate->columntype = 'DATE';
    $fieldDriverLetterDate->uitype     = 5;
    $fieldDriverLetterDate->typeofdata = 'D~O';
    $accidentsblockInstance1->addField($fieldDriverLetterDate);
}
$fieldCommitteeReview = Vtiger_Field::getInstance('committe_review', $accidentsInstance);
if ($fieldCommitteeReview) {
    echo "The committe_review field already exists<br>\n";
} else {
    $fieldCommitteeReview             = new Vtiger_Field();
    $fieldCommitteeReview->label      = 'LBL_COMMITTE_REVIEW';
    $fieldCommitteeReview->name       = 'committe_review';
    $fieldCommitteeReview->table      = 'vtiger_accidents';
    $fieldCommitteeReview->column     = 'committe_review';
    $fieldCommitteeReview->columntype = 'TEXT';
    $fieldCommitteeReview->uitype     = 19;
    $fieldCommitteeReview->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldCommitteeReview);
}
$field2 = Vtiger_Field::getInstance('assigned_user_id', $accidentsInstance);
if ($field2) {
    echo "<br> Field 'assigned_user_id' is already present. <br>";
} else {
    $field2 = new Vtiger_Field();
    $field2->label = 'LBL_ACCIDENTS_ASSIGNEDTO';
    $field2->name = 'assigned_user_id';
    $field2->table = 'vtiger_crmentity';
    $field2->column = 'smownerid';
    $field2->uitype = 53;
    $field2->typeofdata = 'V~M';
    $field2->displaytype =2;

    $accidentsblockInstance1->addField($field2);
}
$fieldNotes = Vtiger_Field::getInstance('notes', $accidentsInstance);
if ($fieldNotes) {
    echo "The notes field already exists<br>\n";
} else {
    $fieldNotes             = new Vtiger_Field();
    $fieldNotes->label      = 'LBL_NOTES';
    $fieldNotes->name       = 'notes';
    $fieldNotes->table      = 'vtiger_accidents';
    $fieldNotes->column     = 'notes';
    $fieldNotes->columntype = 'TEXT';
    $fieldNotes->uitype     = 19;
    $fieldNotes->typeofdata = 'V~O';
    $accidentsblockInstance1->addField($fieldNotes);
}



$hideFields = ['accidents_time', 'description'];

hideFields_AME($hideFields, $accidentsInstance);

$fieldSeq = [
    'accidents_date',
    'report_taken_by',
    'date_reported_to_safety',
    'accidents_employees',
    'accidents_city',
    'accidents_state',
    'on_duty',
    'authority',
    'dot_recordable',
    'no_fatalities',
    'police_involved',
    'no_injuries',
    'citation_issued',
    'no_towed',
    'dui_test_type',
    'dui_test_date',
    'drug_test_received',
    'bac_test_received',
    'driver_report_hrs',
    'written_report_hrs',
    'incident_type',
    'accidents_preventable',
    'vehicle_causing_impact',
    'accident_type',
    'v_one_doing',
    'v_two_doing',
    'late_reporting_sent',
    'letter_no',
    'driver_letter_date',
    'agentid',
    'committe_review',
    'notes'
];

reorderFieldsByBlockAME($fieldSeq, $accidentsblockInstance1->label, $accidentsInstance->name);

function reorderFieldsByBlockAME($fieldSeq, $blockLabel, $moduleName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $block = Vtiger_Block::getInstance($blockLabel, $module);
        if ($block) {
            $push_to_end = [];
            $seq = 1;
            foreach ($fieldSeq as $name) {
                if ($name && $field = Vtiger_Field::getInstance($name, $module)) {
                    $sql    = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
                    $result = $db->pquery($sql, [$seq, $block->id]);
                    if ($result) {
                        while ($row = $result->fetchRow()) {
                            $push_to_end[] = $row['fieldname'];
                        }
                    }
                    $updateStmt = 'UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldid` = ? AND `block` = ?';
                    $db->pquery($updateStmt, [$seq++, $field->id, $block->id]);
                }
                unset($field);
            }
            //push anything that might have gotten added and isn't on the list to the end of the block
            $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0] + 1;
            foreach ($push_to_end as $name) {
                //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
                if (!in_array($name, $fieldSeq)) {
                    $field = Vtiger_Field::getInstance($name, $module);
                    if ($field) {
                        $updateStmt = 'UPDATE `vtiger_field` SET `sequence` = ? WHERE `fieldid` = ? AND `block` = ?';
                        $db->pquery($updateStmt, [$max++, $field->id, $block->id]);
                        $max++;
                    }
                }
            }
        }
    }
    echo "Finished reordering fields<br>\n";
}

function hideFields_AME($fields, $module)
{
    if (is_array($fields)) {
        $db = PearDatabase::getInstance();
        foreach ($fields as $field_name) {
            $field0 = Vtiger_Field::getInstance($field_name, $module);
            if ($field0) {
                echo "<li>The $field_name field exists</li><br>";
                //update the presence
                if ($field0->presence != 1) {
                    echo "Updating $field_name to be a have presence = 1 <br />\n";
                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
                    $db->pquery($stmt, ['1', $field0->id]);
                }
            }
        }
    }
    return false;
}

$field3 = Vtiger_Field::getInstance('createdtime', $accidentsInstance);
if ($field3) {
    echo "<br> Field 'createdtime' is already present. <br>";
} else {
    $field3 = new Vtiger_Field();
    $field3->label = 'LBL_ACCIDENTS_CREATEDTIME';
    $field3->name = 'createdtime';
    $field3->table = 'vtiger_crmentity';
    $field3->column = 'createdtime';
    $field3->uitype = 70;
    $field3->typeofdata = 'T~O';
    $field3->displaytype =2;

    $accidentsblockInstance2->addField($field3);
}

$field4 = Vtiger_Field::getInstance('modifiedtime', $accidentsInstance);
if ($field4) {
    echo "<br> Field 'modifiedtime' is already present. <br>";
} else {
    $field4 = new Vtiger_Field();
    $field4->label = 'LBL_ACCIDENTS_MODIFIEDTIME';
    $field4->name = 'modifiedtime';
    $field4->table = 'vtiger_crmentity';
    $field4->column = 'modifiedtime';
    $field4->uitype = 70;
    $field4->typeofdata = 'T~O';
    $field4->displaytype =2;

    $accidentsblockInstance2->addField($field4);
}

print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
