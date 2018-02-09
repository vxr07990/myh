<?php

$languageStrings = array(

    'SINGLE_MoveRoles' => 'Move Roles',
    
    'LBL_MOVEROLES_ROLE' => 'Role',
    'LBL_MOVEROLES_INFORMATION'=>'Move Roles',
    'LBL_MOVEROLES_EMPLOYEES' => 'Personnel',
    'LBL_MOVEROLES_PROJECT' => 'Order Name',
    'LBL_TIMEOFF_HOURSTART' => 'From',
    'LBL_TIMEOFF_REASON' => 'Reason',
    'LBL_MOVEROLES_ORDERS' => 'Orders',
    'LBL_SERVICE_PROVIDER' => 'Service Provider',
    'LBL_MOVEROLES_RELCRMID' =>'Leads'
    
);


if (getenv('INSTANCE_NAME') == 'graebel') {
    $languageStrings['LBL_MOVEROLES_EMPLOYEES'] = 'Associate';
    $languageStrings['LBL_MOVEROLES_COMMISSION'] = 'Commission';
}
