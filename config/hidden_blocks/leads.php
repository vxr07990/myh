<?php

if(getenv('INSTANCE_NAME') == 'graebel')
{
    return [
        'Commercial Move'    => '',
        'International Move' => '',
        'Interstate Move'    => '',
        'Intrastate Move'    => '',
        'Local Move'         => '',
        'National Account'              => 'LBL_LEADS_NATIONALACCOUNT',
        'Commercial - Distribution'     => 'LBL_LEADS_NATIONALACCOUNT',
        'Commercial - Record Storage'   => 'LBL_LEADS_NATIONALACCOUNT',
        'Commercial - Storage'          => 'LBL_LEADS_NATIONALACCOUNT',
        'Commercial - Asset Management' => 'LBL_LEADS_NATIONALACCOUNT',
        'Work Space - MAC'              => 'LBL_LEADS_NATIONALACCOUNT',
        'Commercial - Project'          => 'LBL_LEADS_NATIONALACCOUNT',
        'Work Space - Special Services' => 'LBL_LEADS_NATIONALACCOUNT',
        'Work Space - Commodities'      => 'LBL_LEADS_NATIONALACCOUNT',
        'Auto Transportation'     => 'vehicleLookupTable',
        'HHG - International Sea' => '',
        'HHG - International'     => '',
    ];
}
 else {
return [
    'Commercial Move'                 => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'International Move'            => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'Interstate Move'                 => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'Intrastate Move'                => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'Local Move'                      => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'Commercial'                 => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'International'            => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'Interstate'                 => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'Intrastate'                => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'Local'                      => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'National Account'            => 'LBL_LEADS_NATIONALACCOUNT',
    'Commercial - Distribution'    => 'LBL_LEADS_NATIONALACCOUNT',
    'Commercial - Record Storage'   => 'LBL_LEADS_NATIONALACCOUNT',
    'Commercial - Storage'        => 'LBL_LEADS_NATIONALACCOUNT',
    'Commercial - Asset Management' => 'LBL_LEADS_NATIONALACCOUNT',
    'Work Space - MAC'                => 'LBL_LEADS_NATIONALACCOUNT',
    'Commercial - Project'            => 'LBL_LEADS_NATIONALACCOUNT',
    'Work Space - Special Services'    => 'LBL_LEADS_NATIONALACCOUNT',
    'Work Space - Commodities'        => 'LBL_LEADS_NATIONALACCOUNT',
    'Auto Transportation'            => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::VehicleLookupTable::ExtraStopsTable',
    'Auto'                          => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::VehicleLookupTable::ExtraStopsTable',
    'HHG - International Sea'        => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
    'HHG - International'        => 'LBL_LEADS_ADDRESSINFORMATION::LBL_LEADS_DATES::ExtraStopsTable',
];
 }
