<?php
$languageStrings = array(
    'CommissionPlans' => 'Commission Plans',
    'SINGLE_CommissionPlans' => 'Commission Plans',

    'LBL_COMMISSIONPLANDETAIL' => 'Commission Plan Detail',
    'LBL_RECORDUPDATEINFORMATION' => 'Record Update Information',

    'LBL_ADD_RECORD' => 'Add Commission Plan',
    'LBL_RECORDS_LIST' => 'Commission Plans List',

    'LBL_NAME'=>'Name',
    'LBL_DESCRIPTION'=>'Description',
    'Owner'=>'Owner',
    'LBL_STATUS'=>'Status',
    'LBL_DATECREATED'=>'Date Created',
    'LBL_DATEMODIFIED'=>'Date Modified',
    'Created By'=>'Created By',
    'Assigned To'=>'Assigned To',

);

if(getenv('IGC_MOVEHQ')) {
    $languageStrings['Owner'] = 'Agent Owner';
}

