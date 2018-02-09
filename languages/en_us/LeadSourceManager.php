<?php
$languageStrings = [
    'LeadSourceManager'        => 'Lead Sources',
    'SINGLE_LeadSourceManager' => 'Lead Source',

    'LBL_ADD_RECORD'   => 'Add Lead Source',
    'LBL_RECORDS_LIST' => 'Lead Source List',

    'LBL_COORDINATORS'        => 'Coordinators',
    'LBL_MANAGE_COORDINATORS' => 'Manage Coordinators',

    'LBL_LEADSOURCE_ADMINISTRATIVE' => 'Administrative Information',
    'LBL_LEADSOURCE_INFORMATION'    => 'Lead Source Information',
    'LBL_CUSTOM_INFORMATION'        => 'Lead Source Customer Information',

    'LBL_LEADSOURCE_ID'                => 'Lead Source ID',
    'LBL_LEADSOURCE_AGENCY_CODE'       => 'Agency Code',
    'LBL_LEADSOURCE_VANLINEID'         => 'Vanline ID',
    'LBL_LEADSOURCE_BRAND'             => 'Brand',
    'LBL_LEADSOURCE_LMP_PROGRAM_ID'    => 'LMP Program ID',
    'LBL_LEADSOURCE_LMP_SOURCE_ID'     => 'LMP Source ID',
    'LBL_LEADSOURCE_MARKETING_CHANNEL' => 'Marketing Channel',
    'LBL_LEADSOURCE_SOURCE_NAME'       => 'Source Name',
    'LBL_LEADSOURCE_PROGRAM_NAME'      => 'Program Name',
    'LBL_LEADSOURCE_PROGRAM_TERMS'     => 'Program Terms',
    'LBL_LEADSOURCE_SOURCE_TYPE'       => 'Source Type',
    'LBL_LEADSOURCE_ACTIVE'            => 'Is Active',
    'LBL_LEADSOURCE_NOTES'             => 'Notes',

    'Owner Agent' => 'Agency',
    'Owner'       => 'Agency',
];

if(getenv('IGC_MOVEHQ')) {
    $languageStrings['Owner Agent'] = 'Agent Owner';
    $languageStrings['Owner'] = 'Agent Owner';
}
