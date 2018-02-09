<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

$languageStrings = array(
    'Surveys' => 'Survey Appointments',
    'SINGLE_Surveys' => 'Survey Appointment',
    
    
    'LBL_SURVEYS_DATE' => 'Survey Appointment Date',
    'LBL_SURVEYS_NO' => 'Survey Appointment No',
    'LBL_SURVEYS_ACCOUNTID' => 'Account Name',
    'LBL_SURVEYS_CONTACTID' => 'Contact Name',
    'LBL_SURVEYS_POTENTIALID' => 'Opportunity Name',
    'LBL_SURVEYS_SURVEYOR' => 'Surveyor',
    'LBL_SURVEYS_STATUS' => 'Survey Appointment Status',
    'LBL_SURVEYID' => 'Survey ID',
    'LBL_SURVEY_CONTACT' => 'Contact Name',
    'LBL_SURVEY_POTENTIAL' => 'Opportunity Name',
    'LBL_RELATED_TO' => 'Account Name',
    'LBL_ADD_RECORD' => 'Add Survey Appointment',
    'LBL_RECORDS_LIST' => 'Survey Appointments List',
    
    'LBL_SURVEYS_ASSIGNEDTO' => 'Assigned To',
    'LBL_SURVEYS_CREATEDTIME' => 'Created Time',
    'LBL_SURVEYS_MODIFIEDTIME' => 'Modified Time',
    'LBL_SURVEYS_SURVEYTIME' => 'Survey Time',
    'LBL_SURVEYS_MOBILEPUSH' => 'Mobile Push',
    'LBL_SURVEYS_ORDERS' => 'Order Name',
    'LBL_SURVEYS_ADDRESS1' => 'Address 1',
    'LBL_SURVEYS_ADDRESS2' => 'Address 2',
    'LBL_SURVEYS_CITY' => 'City',
    'LBL_SURVEYS_STATE' => 'State',
    'LBL_SURVEYS_ZIP' => 'Zip',
    'LBL_SURVEYS_COUNTRY' => 'Country',
    'LBL_SURVEYS_PHONE1' => 'Phone 1',
    'LBL_SURVEYS_PHONE2' => 'Phone 2',
    'LBL_SURVEYS_ADDRESSDESCRIPTION' => 'Address Description',
    'LBL_SURVEYS_COMMERCIALORRESIDENTIAL' => 'Commercial or Residential',
    'LBL_SURVEYS_PACKING' => 'Include Packing',
    'LBL_SURVEYS_SURVEYENDTIME' => 'Survey Appointment End Time',
    'LBL_SURVEYS_NOTES' => 'Notes',
    'LBL_SURVEYS_TYPE' => 'Survey Type',
    'LBL_SURVEYS_GOOGLE_APT_ID' => 'Google Calendar ID',
    'LBL_SELF_SURVEY_URL' => 'Self Survey URL',


    //blocks
    'LBL_SURVEYS_INFORMATION' => 'Survey Appointment Details',
    'LBL_BLOCK_SYSTEM_INFORMATION' => 'Administration Details',
    'LBL_SURVEYS_LOCALMOVEDETAILS' => 'Local Move Details',
    'LBL_SURVEYS_INTERSTATEMOVEDETAILS' => 'Intersate Move Details',
    'LBL_SURVEYS_COMMERCIALMOVEDETAILS' => 'Commercial Move Details',
);

if (getenv('INSTANCE_NAME') === 'uvlc') {
    $languageStrings['LBL_SURVEYS_ZIP'] = 'Postal Code';
}
