<?php
if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


//*/
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('includes/main/WebUI.php');
//*/
echo "<br>Adding a column for the packing label so it's not in an array<br>";
Vtiger_Utils::AddColumn('vtiger_packing_items', 'label', 'VARCHAR(50)');
echo "<br>Adding a column for the bulky label so it's not in an array<br>";
Vtiger_Utils::AddColumn('vtiger_bulky_items', 'label', 'VARCHAR(50)');
echo "<br>Adding the names for everything that has already been saved for packing<br>";
$packingItems = [
                 '8'   => 'Dish Pack',
                 '5'   => 'Book',
                 '1'   => '1.5',
                 '2'   => '3.0',
                 '3'   => '4.5',
                 '4'   => '6.0',
                 '16'  => '6.5',
                 '15'  => 'Wardrobe',
                 '14'  => 'Single/Twin',
                 '6'   => 'Crib',
                 '9'   =>'Long',
                 '7'   => 'Double Bed',
                 '13'  => 'King/Queen',
                 '12'  => 'Mirror',
                 '9'   => 'Grandfather Clock',
                 '102' => 'TV Carton',
                 '15'  =>'Ironing Board',
                 '11'  => 'Lamp',
                 '17'  =>'Pole Lamp',
                 '18'  =>'Ski',
                 '19'  =>'Tea Chest',
                 '20'  =>'Double < 4 cu. ft.',
                 '21'  =>'Double > 4 < 7',
                 '22'  =>'Double > 7 < 15',
                 '17'  => 'Mattress Cover',
                 '509' => 'Other',
                 '510' => 'Heavy Duty'
                ];
foreach ($packingItems as $itemId => $name) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_packing_items` SET label = '$name' WHERE itemid = $itemId");
}
echo "<br>Adding the names for everything that has already been saved for bulkies<br>";
$bulkyItems = ['1'  => '4x4 Vehicle',
               '2'  => 'Airplane, Glider',
               '3'  => 'All Terrain Cycle',
               '4'  => 'Animal House',
               '5'  => 'Automobile',
               '6'  => 'Bath',
               '7'  => 'Bath > 65 Cu Ft',
               '8'  => 'Boat Trailer',
               '9'  => 'Boat < 14 Ft',
               '10' => 'Boat > 14 Ft',
               '11' => 'Camper, Truckless',
               '12' => 'Camper Shell',
               '13' => 'Camper Trailer',
               '14' => 'Canoe < 14 Ft',
               '15' => 'Canoe > 14 Ft',
               '16' => 'Dinghy < 14 Ft',
               '17' => 'Dinghy > 14 Ft',
               '18' => 'Doll House',
               '19' => 'Farm Equipment',
               '20' => 'Farm Implement',
               '21' => 'Farm Trailer',
               '22' => 'Go-Cart',
               '23' => 'Golf Cart',
               '24' => 'Horse Trailer',
               '25' => 'Hot Tub',
               '26' => 'Hot Tub > 65 Cu Ft',
               '27' => 'Jacuzzi',
               '28' => 'Jacuzzi > 65 Cu Ft',
               '29' => 'Jet Ski',
               '30' => 'Jet Ski > 14 Ft',
               '31' => 'Kayak < 14 Ft',
               '32' => 'Kayak > 14 Ft',
               '33' => 'Kennel',
               '34' => 'Large Tv > 40',
               '35' => 'Light/Bulky',
               '36' => 'Limousine',
               '37' => 'Mini Mobile Home',
               '38' => 'Motorbike',
               '39' => 'Motorcycle',
               '40' => 'Piano',
               '41' => 'Piano, Concert',
               '42' => 'Piano, Grand',
               '43' => 'Piano, Spinet',
               '44' => 'Piano, Upright',
               '45' => 'Piano, Baby Grand',
               '46' => 'Pickup & Camper',
               '47' => 'Pickup Truck',
               '48' => 'Playhouse',
               '49' => 'Riding Mower',
               '50' => 'Rowboat < 14 Ft',
               '51' => 'Rowboat > 14 Ft',
               '52' => 'Satellite Dish',
               '53' => 'Scull > 14 Ft',
               '54' => 'Skiff < 14 Ft',
               '55' => 'Skiff > 14 Ft',
               '56' => 'Snow Mobile',
               '57' => 'Spa',
               '58' => 'Spa > 65 Cu Ft',
               '59' => 'Tool Shed',
               '60' => 'Tractor < 25HP',
               '61' => 'Tractor > 25HP',
               '62' => 'Trailer < 14 Ft',
               '63' => 'Trailer > 14 Ft',
               '64' => 'TV/Radio Dish',
               '65' => 'Utility Shed',
               '66' => 'Utility Truck',
               '67' => 'Van',
               '68' => 'Whirlpool Bath',
               '69' => 'Whirlpool > 65 Cu',
               '70' => 'Windsurfer < 14 Ft',
               '71' => 'Windsurfer > 14 Ft'];
foreach ($bulkyItems as $itemId => $name) {
    Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_bulky_items` SET label = '$name' WHERE bulkyid = $itemId");
}
echo "<br>DONE!<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";