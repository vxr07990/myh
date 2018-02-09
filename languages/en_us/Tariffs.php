<?php

$languageStrings = array(
    'Tariffs' => 'Tariffs',
    'SINGLE_Tariffs' => 'Local Tariff',

    'LBL_ADD_RECORD' => 'Add Local Tariff',
    'LBL_RECORDS_LIST' => 'Local Tariffs List',

    'LBL_TARIFFS_NAME' => 'Local Tariff Name',
    'LBL_TARIFFS_STATE' => 'Local Tariff State',
    'LBL_TARIFFS_RELATEDAGENT' => 'Agent',
    'LBL_TARIFFS_ADMINACCESS' => 'Admin Access',

    'LBL_TARIFFS_INFORMATION' => 'Local Tariff Information',
    'LBL_TARIFFS_SPECIAL_TYPE' => 'Special Case Tariff Selector',
    'LBL_TARIFFS_VANLINE_SPECIFIC_TARIFF_ID' => 'Vanline Specific Tariff ID',
    'LBL_DUPLICATETAIFF' => 'Duplicate Tariff',
    'LBL_RECORD_UPDATE_INFORMATION' => 'Record Update Information',
    'LBL_TARIFFS_CREATEDTIME' => 'Created Date / Time',
    'LBL_TARIFFS_MODIFIEDTIME' => 'Modified Date / Time',
    'LBL_TARIFFS_CREATEDBY' => 'Created By',
    'LBL_TARIFFS_ASSIGNED_TO' => 'Assigned To',
    'LBL_TARIFF_BUSINESS_LINE' => 'Allowed Business Lines',
    'LBL_TARIFFS_STATUS' => 'Status',
	'LBL_TARIFF_COMMODITIES' => 'Allowed Commodities',
);

if (getenv('INSTANCE_NAME') === 'uvlc') {
    $languageStrings['Tariff State'] = 'Tariff Province';
}

