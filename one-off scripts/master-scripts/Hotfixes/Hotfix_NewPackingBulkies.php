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



//require_once 'vtlib/Vtiger/Menu.php';
//require_once 'vtlib/Vtiger/Module.php';
//require_once 'includes/main/WebUI.php';

echo "<br>begin new packing & bulkies hotfix<br>";

$oldBulkies = [
                '1'  => '4x4 Vehicle',//
                '2'  => 'Airplane, Glider',//
                '3'  => 'All Terrain Cycle',//
                '4'  => 'Animal House',//
                '5'  => 'Automobile',//
                '6'  => 'Bath',//
                '7'  => 'Bath > 65 Cu Ft',//
                '8'  => 'Boat Trailer',//
                '9'  => 'Boat < 14 Ft',//
                '10' => 'Boat > 14 Ft',//
                '11' => 'Camper, Truckless',//
                '12' => 'Camper Shell',//
                '13' => 'Camper Trailer',//
                '14' => 'Canoe < 14 Ft',//
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
                '71' => 'Windsurfer > 14 Ft'
            ];
            
$newBulkies = [
            '9' => '4x4 Vehicle',
            '16' => 'Airplanes, Gliders',
            '17' => 'All Terrain Cycle',
            '18' => 'Animal House',
            '20' => 'Automobile',
            '27' => 'Bath',
            '28' => 'Bath > 65 Cu Ft',
            '47' => 'Boat Trailers',
            '48' => 'Boats < 14 ft.',
            '49' => 'Boats > 14 ft.',
            '58' => 'Bulky Article > 400 lbs',
            '67' => 'Camper (Truckless)',
            '68' => 'Camper Shell',
            '69' => 'Camper Trailers',
            '70' => 'Canoe < 14 ft.',
            '71' => 'Canoe > 14 ft.',
            '121' => 'Dinghy < 14 ft.',
            '122' => 'Dinghy > 14 ft.',
            '126' => 'Doll House',
            '140' => 'Farm Equipment',
            '141' => 'Farm Implement',
            '142' => 'Farm Trailer',
            '182' => 'Go-Cart',
            '183' => 'Golf Cart',
            '188' => 'Home Gym Equipment',
            '189' => 'Horse Trailers',
            '190' => 'Hot Tub',
            '191' => 'Hot Tub > 65 Cu Ft',
            '196' => 'Jacuzzi',
            '197' => 'Jacuzzi > 65 Cu Ft',
            '198' => 'Jet Ski',
            '199' => 'Jet Ski > 14 ft.',
            '202' => 'Kayak < 14 ft.',
            '203' => 'Kayak > 14 ft.',
            '204' => 'Kennel',
            '212' => 'Large TV > 40',
            '216' => 'Light/Bulky',
            '217' => 'Limousine',
            '235' => 'Mini Mobile Homes',
            '263' => 'Motorbike',
            '264' => 'Motorcycle',
            '281' => 'Piano',
            '282' => 'Piano, Concert',
            '283' => 'Piano, Grand',
            '285' => 'Piano, Spinet', //piano spinet
            '286' => 'Piano, Upright', //piano upright
            '287' => 'Piano, Baby Grand', //piano baby grand
            '288' => 'Pickup & Camper', // pickup and camper
            '289' => 'Pickup Truck', //pickup truck
            '308' => 'Playhouse', //playhouse
            '326' => 'Riding Mower < 25hp', //riding mower <25hp
            '327' => 'Rowboat < 14 ft.', //rowboat <14 ft
            '328' => 'Rowboat > 14 ft.', //rowboat >14 ft
            '333' => 'Sailboat > 14 ft.', //sailboat>14ft
            '334' => 'Satellite Dish', //satellitle dish
            '336' => 'Sculls > 14 ft.', //Sculls >14ft
            '348' => 'Skiff < 14 ft.', //Skiff <14 ft
            '349' => 'Skiff > 14 ft.', //Skiff >14 ft
            '353' => 'Snow Mobile', //snow mobile
            '362' => 'Spa', //spa
            '363' => 'Spa > 65 Cu Ft', //spa >65 cuFt
            '392' => 'Tool Shed', //tool shed
            '401' => 'Tractor < 25hp', //tractor <25 hp
            '402' => 'Tractor > 25hp', //tractor >25hp
            '403' => 'Trailer < 14 ft.', //trailer < 14hp
            '404' => 'Trailer > 14 ft.', //trailer >14ft
            '419' => 'TV/Radio Dish', //Tv/Radio Dish
            '423' => 'Utility Shed', //Utility Shed
            '424' => 'Utility Truck', //Utility Truck
            '426' => 'Van', //Van
            '435' => 'Whirlpool Bath', //Whirlpool Bath
            '436' => 'Whirlpool Bath > 65 Cu', //Whirlpool Bath > 65 Cu
            '438' => 'Windsurfer', //Windsurfer
            '439' => 'Windsurfer > 14 ft.', //Windsurfer
        ];
        
$bulkyMap =[
    '1' => '9', //4x4 vehicle
    '2' => '16', //plane/glider
    '3' => '17', //AT Cycle
    '4' => '18', //Animal House
    '5' => '20', //automobile
    '6' => '27', //bath
    '7' => '28', //bath >65 cuFt
    '8' => '47', //boat trailer
    '9' => '48', //boat <14 ft
    '10' => '49', //boat >14 ft
    '11' => '67', //camper (truckless)
    '12' => '68', //camper shell
    '13' => '69', //camper trailer
    '14' => '70', //canoe <14 ft
    '15' => '71', //canoe >14 ft
    '16' => '121', //dinghy <14 ft
    '17' => '122', //dinghy >14 ft
    '18' => '126', //doll house
    '19' => '140', //farm equipment
    '20' => '141', //farm implement
    '21' => '142', //farm trailer
    '22' => '182', //go-cart
    '23' => '183', //golf-cart
    '24' => '189', //horse trailer
    '25' => '190', //hot-tub
    '26' => '191', //hut-tub >65 cuFt
    '27' => '196', //jaccuzi
    '28' => '197', //jaccuzi >65 cuFt
    '29' => '198', //jet-ski
    '30' => '199', //jet ski >14 ft
    '31' => '202', //kayak <14 ft
    '32' => '203', //kayak >14ft
    '33' => '204', //kennel
    '34' => '212', //large TV > 40
    '35' => '216', //light/bulky
    '36' => '217', //limousine
    '37' => '235', //mini mobile home
    '38' => '263', //motorbike
    '39' => '264', //motorcycle
    '40' => '281', //piano
    '41' => '282', //piano concert
    '42' => '283', //piano grand
    '43' => '285', //piano spinet
    '44' => '286', //piano upright
    '45' => '287', //piano baby grand
    '46' => '288', // pickup and camper
    '47' => '289', //pickup truck
    '48' => '308', //playhouse
    '49' => '326', //riding mower <25hp
    '50' => '327', //rowboat <14 ft
    '51' => '328', //rowboat >14 ft
    '52' => '334', //satellitle dish
    '53' => '336', //Sculls >14ft
    '54' => '348', //Skiff <14 ft
    '55' => '349', //Skiff >14 ft
    '56' => '353', //snow mobile
    '57' => '362', //spa
    '58' => '363', //spa >65 cuFt
    '59' => '392', //tool shed
    '60' => '401', //tractor <25 hp
    '61' => '402', //tractor >25hp
    '62' => '403', //trailer < 14hp
    '63' => '404', //trailer >14ft
    '64' => '419', //Tv/Radio Dish
    '65' => '423', //Utility Shed
    '66' => '424', //Utility Truck
    '67' => '426', //Van
    '68' => '435', //Whirlpool Bath
    '69' => '436', //Whirlpool Bath > 65 Cu
    '70' => '438', //Windsurfer <14
    '71' => '439', //Windsurfer >14
];

$bulkyLabelMap = [
                    'Airplane, Glider' => 'Airplanes, Gliders',
                    'Boat Trailer' => 'Boat Trailers',
                    'Boat < 14 Ft' => 'Boats < 14 Ft',
                    'Boat > 14 Ft' => 'Boats > 14 Ft',
                    'Camper, Truckless' => 'Camper (Truckless)',
                    'Camper Trailer' => 'Camper Trailers',
                    'Canoe < 14 Ft' => 'Canoe < 14 ft.',
                    'Canoe > 14 Ft' => 'Canoe > 14 ft.',
                    'Dinghy < 14 Ft' => 'Dinghy < 14 ft.',
                    'Dinghy < 14 Ft' => 'Dinghy > 14 ft.',
                    'Horse Trailer' => 'Horse Trailers',
                    'Jet Ski > 14 Ft' => 'Jet Ski > 14 ft.',
                    'Kayak < 14 Ft' => 'Kayak < 14 ft.',
                    'Kayak > 14 Ft' => 'Kayak > 14 ft.',
                    'Large Tv > 40' => 'Large TV > 40',
                    'Mini Mobile Home' => 'Mini Mobile Homes',
                    'Riding Mower' => 'Riding Mower < 25hp',
                    'Rowboat < 14 Ft' => 'Rowboat < 14 ft.',
                    'Rowboat > 14 Ft' => 'Rowboat > 14 ft.',
                    'Scull > 14 Ft' => 'Sculls > 14 ft.',
                    'Skiff < 14 Ft' => 'Skiff < 14 ft.',
                    'Skiff > 14 Ft' => 'Skiff > 14 ft.',
                    'Tractor < 25HP' => 'Tractor < 25hp',
                    'Tractor > 25HP' => 'Tractor > 25hp',
                    'Trailer < 14 Ft' => 'Trailer < 14 ft.',
                    'Trailer > 14 Ft' => 'Trailer > 14 ft.',
                    'Whirlpool > 65 Cu' => 'Whirlpool Bath > 65 Cu',
                    'Windsurfer < 14 Ft' => 'Windsurfer',
                    'Windsurfer > 14 Ft' => 'Windsurfer > 14 ft.',
                ];

$oldPacking = [
                '8'   => 'Dish Pack',
                '5'   => 'Book',
                '1'   => '1.5',
                '2'   => '3.0',
                '3'   => '4.5',
                '4'   => '6.0',
                '16'  => '6.5',
                '15'  => 'Wardrobe',
                '14'  => 'Single/Twin',
                '6'   => 'Crib',//'9'=>'Long',
                '7'   => 'Double Bed',
                '13'  => 'King/Queen',
                '12'  => 'Mirror',
                '9'   => 'Grandfather Clock',
                '102' => 'TV Carton',
                '11'  => 'Lamp',
                '17'  => 'Mattress Cover',
                '509' => 'Other',
                '510' => 'Heavy Duty',
                //'15'=>'Ironing Board',
                //'17'=>'Pole Lamp',
                //'18'=>'Ski',
                //'19'=>'Tea Chest',
                //'20'=>'Double < 4 cu. ft.',	'21'=>'Double > 4 < 7',
                //'22'=>'Double > 7 < 15',
            ];
            
$newPacking = [
                '1' => '1.5 - CP',
                '4' => '3.0 - CP',
                '7' => '4.5 - CP',
                '10' => '6.0 - CP',
                '12' => '6.5 - CP',
                '50' => 'Book Carton - CP',
                '108' => 'Crib Matt. - CP',
                '111' => 'Dbl. Matt. - CP',
                '123' => 'Dish Pack - CP',
                '170' => 'GF Clock Carton - CP',
                '200' => 'K/Q Split  - CP',
                '208' => 'Lamp Crt. - CP',
                '232' => 'Mattress Cover - CP',
                '245' => 'Mirror Crt. - CP',
                '271' => 'Office Tote Box - CP',
                '313' => 'Qn/Kn Matt. - CP',
                '345' => 'Single Matt. - CP',
                '419' => 'Twin Matt. - CP',
                '427' => 'Wardrobe - CP',
                '1311' => 'Other - CP',
                '1309' => 'TV Carton - CP',
                '1313' => 'Heavy Duty - CP',
            ];
            
$packingMap = [
                '8' => '123', //dish pack
                '5' => '50', //book
                '1' => '1', //1.5 - CP
                '2' => '4', //3.0 - CP
                '3' => '7', //4.5 - CP
                '4' => '10', //6.0 - CP
                '16' => '12', //6.5 - CP
                '15' => '427', //Wardrobe
                '14' => '419', //single/twin
                '6' => '108', //crib
                '7' => '111', //double bed
                '13' => '313', //king/queen
                '12' => '245', //mirror
                '9' => '170', //grandfather clock
                '102' => '1309', //tv carton
                '11' => '208', //lamp
                '17' => '232', //mattress cover
                '509' => '1311', //other
                '510' => '1313', //heavy duty
            ];
            
$packingLabelMap =[
                    'Dish Pack' => 'Dish Pack - CP',
                    'Book' => 'Book Carton - CP',
                    '1.5' => '1.5 - CP',
                    '3.0' => '3.0 - CP',
                    '4.5' => '4.5 - CP',
                    '6.0' => '6.0 - CP',
                    '6.5' => '6.5 - CP',
                    'Wardrobe' => 'Wardrobe - CP',
                    'Single/Twin' => 'Twin Matt. - CP',
                    'Crib' => 'Crib Matt. - CP',
                    'Double Bed' => 'Dbl. Matt. - CP',
                    'King/Queen' => 'Qn/Kn Matt. - CP',
                    'Mirror' => 'Mirror Crt. - CP',
                    'Grandfather Clock' => 'GF Clock Carton - CP',
                    'TV Carton' => 'TV Carton - CP',
                    'Lamp' => 'Lamp Crt. - CP',
                    'Mattress Cover' => 'Mattress Cover - CP',
                    'Other' => 'Other - CP',
                    'Heavy Duty' => 'Heavy Duty - CP',
                ];

echo "<br><h1>Begin Bulky Conversion</h1><br>";

$db = PearDatabase::getInstance();

$bulkies = [];

$bulkySql = 'SELECT * FROM `vtiger_bulky_items`';
$result = $db->pquery($bulkySql, []);
while ($row =& $result->fetchRow()) {
    $bulkies[] = $row;
}

echo "";

foreach ($bulkies as $bulky) {
    $originalLabel = $bulky['label'];
    $originalId = $bulky['bulkyid'];
    //convert old to new labels
    if (array_key_exists($bulky['label'], $bulkyLabelMap)) {
        echo "<br>Old label (" . $bulky['label'] . ")...";
        $bulky['label'] = $bulkyLabelMap[$bulky['label']];
        echo "CONVERTED TO: ".$bulky['label'];
    }
    //if the id is mapped and the label isn't what we're expecting we need to update the bulky
    if (array_key_exists($bulky['bulkyid'], $bulkyMap) && $newBulkies[$bulky['bulkyid']] != $bulky['label']) {
        echo "<br> Mappable bulky discovered!" . print_r($bulky, true);
        echo "<br>--------------------------------------------------";
        $bulky['bulkyid'] = $bulkyMap[$bulky['bulkyid']];
        $bulky['label'] = $newBulkies[$bulky['bulkyid']];
        echo "<br><b> New Id: (".$bulky['bulkyid'].") - New Label: (".$bulky['label'].")</b><br><br>";
    }
    if ($originalLabel != $bulky['label'] || $originalId != $bulky['bulkyid']) {
        $updateSql = "UPDATE `vtiger_bulky_items` SET label = ?, bulkyid = ? WHERE quoteid = ? AND bulkyid = ? AND label = ?";
        echo "<br>UPDATESQL - UPDATE `vtiger_bulky_items` SET label = '".$bulky['label']."', bulkyid = ".$bulky['bulkyid']." WHERE quoteid = ".$bulky['quoteid']." AND bulkyid = $originalId AND label = '$originalLabel'<br><br>";
        $db->pquery($updateSql, [$bulky['label'], $bulky['bulkyid'], $bulky['quoteid'], $originalId, $originalLabel]);
    }
}

echo "<br><h1>End Bulky Conversion</h1><br>";

echo "<br><h1>Begin Packing Conversion</h1><br>";

$packs = [];

$packingSql = 'SELECT * FROM `vtiger_packing_items`';
$result = $db->pquery($packingSql, []);
while ($row =& $result->fetchRow()) {
    $packs[] = $row;
}

foreach ($packs as $pack) {
    $originalLabel = $pack['label'];
    $originalId = $pack['itemid'];
    //convert old to new labels
    if (array_key_exists($pack['label'], $packingLabelMap)) {
        echo "<br>Old label (" . $pack['label'] . ")...";
        $pack['label'] = $packingLabelMap[$pack['label']];
        echo "CONVERTED TO: ".$pack['label'];
    }
    //if the id is mapped and the label isn't what we're expecting we need to update the pack
    if (array_key_exists($pack['itemid'], $packingMap) && $newPacking[$pack['itemid']] != $pack['label']) {
        echo "<br> Mappable pack discovered: ".print_r($pack, true);
        echo "<br>--------------------------------------------------";
        $pack['itemid'] = $packingMap[$pack['itemid']];
        $pack['label'] = $newPacking[$pack['itemid']];
        echo "<br><b> New Id: (".$pack['itemid'].") - New Label: (".$pack['label'].")</b><br><br>";
    }
    if ($originalLabel != $pack['label'] || $originalId != $pack['itemid']) {
        $updateSql = "UPDATE `vtiger_packing_items` SET label = ?, itemid = ? WHERE quoteid = ? AND itemid = ? AND label = ?";
        echo "<br>UPDATESQL - UPDATE `vtiger_packing_items` SET label = '".$pack['label']."', itemid = ".$pack['itemid']." WHERE quoteid = ".$pack['quoteid']." AND itemid = $originalId AND label = '$originalLabel'<br><br>";
        $db->pquery($updateSql, [$pack['label'], $pack['itemid'], $pack['quoteid'], $originalId, $originalLabel]);
    }
    //fix OT packing null columns
    if ($pack['ot_pack_qty'] == null || $pack['ot_unpack_qty'] == null) {
        echo "<br> Blank OT pack discovered: ".print_r($pack, true);
        if ($pack['ot_pack_qty'] == null) {
            $otPackAmt = 0;
        } else {
            $otPackAmt = $pack['ot_pack_qty'];
        }
        if ($pack['ot_unpack_qty'] == null) {
            $otUnpackAmt = 0;
        } else {
            $otUnpackAmt = $pack['ot_unpack_qty'];
        }
        $otPackSql = "UPDATE `vtiger_packing_items` SET ot_pack_qty = $otPackAmt, ot_unpack_qty = $otUnpackAmt WHERE quoteid = ? AND itemid = ? AND label = ?";
        $db->pquery($otPackSql, [$pack['quoteid'], $pack['itemid'], $pack['label']]);
    }
}

echo "<br><h1>End Packing Conversion</h1><br>";

echo "<br>Conversion Complete!!!<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";