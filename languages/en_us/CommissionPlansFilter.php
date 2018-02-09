<?php

$languageStrings = array(
    'National Account' => 'National Account',
    'CommissionPlansFilter' => 'Commission Plan Group',
    'SINGLE_CommissionPlansFilter' => 'Commission Plan Group',

    'LBL_COMMISSIONPLANGROUP' => 'Commission Plan Group',
    'LBL_RECORDUPDATEINFORMATION' => 'Record Update Information',

    'LBL_ADD_RECORD' => 'Add Commission Plan Group',
    'Commission Plans Filter' =>'Commission Plan Group',
    'LBL_RECORDS_LIST' => 'Commission Plan Group List',

    'LBL_COMMISSIONPLAN' => 'Commission Plan',
    'Owner' => 'Owner',
    'LBL_BUSINESSLINE' => 'Business Line',
    'LBL_BILLINGTYPE' => 'Billing Type',
    'LBL_AUTHORITY' => 'Authority',
    'LBL_STATUS' => 'Status',
    'Tariff' => 'Tariff',
    'Contract' => 'Contract',
    'Miles From' => 'Miles From',
    'Miles To' => 'Miles To',
    'Weight From' => 'Weight From',
    'Weight To' => 'Weight To',
    'Effective Date From' => 'Effective Date From',
    'Effective Date To' => 'Effective Date To',

    'LBL_DATECREATED' => 'Date Created',
    'LBL_DATEMODIFIED' => 'Date Modified',
    'Created By' => 'Created By',
    'Assigned To' => 'Assigned To',
    'LBL_COMMODITIES' => 'Commodity'
);

if(getenv('IGC_MOVEHQ')) {
    $languageStrings['Owner'] = 'Agent Owner';
}

$jsLanguageStrings = [
    'JS_INVALID_MILES'  => 'Please ensure your Miles From is less than your Miles To.',
    'JS_INVALID_WEIGHT' => 'Please ensure your Weight From is less than your Weight To.',
    'JS_INVALID_EFFECTIVEDATE' => 'Please ensure your Effective Date From is before your Effective Date To',
];
