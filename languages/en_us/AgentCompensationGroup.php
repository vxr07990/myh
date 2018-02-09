<?php
$languageStrings = array(
    'AgentCompensationGroup' => 'Distribution Rules Group',
    'SINGLE_AgentCompensationGroup' => 'Distribution Rules Group',

    'LBL_AGENTCOMPENSATION_GROUP' => 'Distribution Rules Group',
    'LBL_AGENTCOMPENSATION_DISTRIBUTION' => 'Agent Compensation Distribution',
    'LBL_RECORDUPDATEINFORMATION' => 'Record Update Information',

    'LBL_ADD_RECORD' => 'Add Distribution Rules Group',
    'LBL_RECORDS_LIST' => 'Distribution Rules Group List',

    'National Account'=>'National Account',
    'LBL_AGENTCOMPENSATION'=>'Distribution Rule',
    'LBL_OWNER'=>'Owner',
    'LBL_STATUS'=>'Status',
    'LBL_BUSINESSLINE'=>'Business Line',
    'LBL_BILLINGTYPE'=>'Billing Type',
    'LBL_AUTHORITY'=>'Authority',
    'LBL_TYPE'=>'Type',
    'LBL_TARIFF_CONTRACT'=>'Tariff / Contract',
    'LBL_MILESFROM'=>'Miles From',
    'LBL_MILESTO'=>'Miles To',
    'LBL_WEIGHTFROM'=>'Weight From',
    'LBL_WEIGHTTO'=>'Weight To',
    'LBL_EFFECTIVEDATE_FROM'=>'Effective Date From',
    'LBL_EFFECTIVEDATE_TO'=>'Effective Date To',

    'LBL_DATECREATED'=>'Date Created',
    'LBL_DATEMODIFIED'=>'Date Modified',
    'LBL_CREATEDBY'=>'Created By',
    'LBL_ASSIGNEDTO'=>'Assigned To',

);

if(getenv('IGC_MOVEHQ')) {
    $languageStrings['LBL_OWNER'] = 'Agent Owner';
}

