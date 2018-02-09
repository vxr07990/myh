<?php

$languageStrings = array(
        'Trips' => 'Trips',
        'LBL_TRIPS_INFORMATION'=>'Trips Information',
        'LBL_TRIPS_ID'=>'Trips ID',
        'LBL_IN_TRANSIT_ZONE'=>'In Transit Zone',
        'LBL_ORIGIN_ZONE'=>'Origin Zone',
        'LBL_ORIGIN_STATE'=>'Origin State',
        'LBL_EMPTY_ZONE'=>'Empty Zone',
        'LBL_EMPTY_STATE'=>'Empty State',
        'LBL_EMPTY_DATE'=>'Empty Date',
        'LBL_AGENT_UNIT'=>'Agent Name',
        'LBL_PLANNING_NOTES'=>'Planning Notes',
        'LBL_DISPATCH_NOTES'=>'Dispatch Notes',
        'LBL_DRIVER_ID'=>'Driver Name',
        'LBL_TOTAL_LINE_HAUL'=>'Total Line-Haul',
        'LBL_TOTAL_WEIGHT'=>'Total Weight',
        'SINGLE_Trips'=>'Trip',
        'LBL_TRIPS'=>'Trips',
        'LBL_NO_ORDERS'=>'No Orders related to this trip. Please choose at least one',
        'LBL_TRIPS_CURRENTZONE'=>'Current Zone',
        'LBL_TRIPS_STATUS'=>'Status',
        'LBL_TRIPS_UNITNUMBER'=>'Unit Number',
        'LBL_TRIPS_DRIVER_LAST'=>'Driver Last Name',
        'LBL_TRIPS_DRIVER_NO'=>'Driver No',
        'LBL_TRIPS_DRIVEREMAIL'=>'Driver Email',
        'LBL_TRIPS_CHECKINNOTES'=>'Check-in Notes',
        'LBL_TRIPS_CHECKIN'=>'Check-in',
        'LBL_TRIPS_FIRSTLOAD'=>'First Load Date',
        'LBL_TRIPS_DRIVER_FIRST'=>'Driver First name',
        'LBL_TRIPS_DRIVERCELL'=>'Driver Cellphone',
        'LBL_TRIPS_SHIPMENT_COUNT' =>'Shipments Count',
        'LBL_TRIPS_TOTAL_MILES' => 'Total Miles',
        'LBL_TRIPS_TOTAL_CUBE' => 'Total Estimate Cube',
        'LBL_TRIPS_CUBE_AVAILABLE' => 'Cube Available',
        'LBL_TRIPS_NUMBER_AUTOS' => 'Total number of autos',
        'LBL_TRIPS_DAYS' => 'Trip Days',
        'LBL_TRIPS_RATE_DAY' => 'Est. Rate per day',
        'LBL_TRIPS_RATE_MILE' => 'Est. Rate per mile ',
        'LBL_TRIPS_FUEL_SURCHARGE' => 'Fuel Surcharge',
        'LBL_TRIPS_VEHICLE' => 'Vehicle Truck',
        'LBL_TRIPS_CSARATING' => 'CSA Rating',
        'LBL_TRIPS_CSARANKING' => 'CSA Ranking',
        'LBL_TRIPS_PERFORMANCERATING' => 'Performance Rating',
        'LBL_TRIPS_PQCRATING' => 'PQC Rating',
        'LBL_TRIPS_DRIVERCLAIMRATIO' => 'Driver Claims Ratio',
        'LBL_ADD_RECORD' => 'Add Trip',
        'LBL_RECORDS_LIST' => 'Trips List',



    'LBL_TRIPS_DRIVER'=>'Driver Information',
        'LBL_TRIPS_VEHICLE_CUBE'=>'Tractor Vehicle Cube',
        'LBL_TRIPS_AGENT'=>'Agent',
        'LBL_TRIPS_FLEET_MANAGER_EMAIL'=>'Fleet Manager Email',
        'LBL_TRIPS_VEHICLE_LENGTH'=>'Tractor Vehicle Length',
        'LBL_TRIPS_FLEET_MANAGER'=>'Fleet Manager Name',
        'LBL_TRIPS_EQUIPMENT_INFORMATION'=>'Vehicle Information',
        'LBL_TRIPS_TRAILER' => 'Trailer',
        'LBL_TRIPS_TRAILER_CUBE' => 'Trailer Cube',
        'LBL_TRIPS_TRAILER_LENGTH' => 'Trailer Length',
        'LBL_FLEET_STATUS'=> 'Fleet Status',
        'LBL_HAULING_RADIUS'=> 'Hauling radius',
        'LBL_NO_SERVICEHOURS_RECORDS' => 'No Service Hours Related to this Trip.',
        'JS_UPDATING_ORDER' => 'Updating Order',
);

$jsLanguageStrings = array(
    'JS_UPDATING_ORDER' => 'Updating Order',
    'JS_REMOVE_ORDER_TRIP'=>'Are you sure you want to remove this order from the trip?',
);

if (getenv('IGC_MOVEHQ')) {
    $languageStrings['LBL_TRIPS_INFORMATION'] = 'Trip Information';
    $languageStrings['LBL_TRIPS_STATUS'] = 'Trip Status';
    $languageStrings['LBL_TRIPS_VEHICLE'] = 'Tractor Number';
    $languageStrings['LBL_TRIPS_TRAILER'] = 'Trailer Number';
    
}


