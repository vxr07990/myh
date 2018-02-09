<?php

$languageStrings = array(
    'CapacityCalendarCounter' => 'Capacity Calendar Counter',
    'SINGLE_CapacityCalendarCounter' => 'Capacity Calendar Counter',

    'LBL_CAPACITYCALENDARCOUNTER_INFORMATION' => 'Capacity Calendar Counter Infomation',
    'LBL_CAPACITYCALENDARCOUNTER_INFORMATION' => 'Capacity Calendar Counter Infomation',

    'LBL_ADD_RECORD' => 'Add Capacity Calendar Counter',
    'LBL_RECORDS_LIST' => 'Capacity Calendar Counter List',

    'LBL_CALENDAR_CODE' => 'Calendar code',
    'LBL_ORDERS_TASK' => 'Count Type',
	'LBL_CAPACITYCALENDARCOUNTER_RELCRMID' => 'Owner',
);

if(getenv('IGC_MOVEHQ')) {
    $languageStrings['LBL_CAPACITYCALENDARCOUNTER_RELCRMID'] = 'Agent Owner';
}

