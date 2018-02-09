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

require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once ('includes/runtime/BaseModel.php');
require_once ('modules/Vtiger/models/Record.php');
require_once ('modules/Inventory/models/Record.php');
require_once ('modules/Quotes/models/Record.php');
require_once ('modules/Estimates/models/Record.php');


if (!function_exists('defaultBulkyList')) {
    function defaultBulkyList() {
        return [
            ['description' => '4x4 Vehicle', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '1'],
            ['description' => 'Airplanes, Gliders', 'weight' => '120', 'rate' => '0.00', 'CartonBulkyId' => '2'],
            ['description' => 'All Terrain Cycle', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '3'],
            ['description' => 'Animal House', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '4'],
            ['description' => 'Automobile', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '5'],
            ['description' => 'Bath', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '6'],
            ['description' => 'Bath &gt; 65 Cu Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '7'],
            ['description' => 'Boat Trailers', 'weight' => '1600', 'rate' => '0.00', 'CartonBulkyId' => '8'],
            ['description' => 'Boats &lt; 14 Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '9'],
            ['description' => 'Boats &gt; 14 Ft', 'weight' => '2500', 'rate' => '0.00', 'CartonBulkyId' => '10'],
            ['description' => 'Camper (Truckless)', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '11'],
            ['description' => 'Camper Shell', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '12'],
            ['description' => 'Camper Trailers', 'weight' => '7000', 'rate' => '0.00', 'CartonBulkyId' => '13'],
            ['description' => 'Canoe &lt; 14 Ft', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '14'],
            ['description' => 'Canoe &gt; 14 Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '15'],
            ['description' => 'Dinghy &lt; 14 Ft', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '16'],
            ['description' => 'Dinghy &gt; 14 Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '17'],
            ['description' => 'Doll House', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '18'],
            ['description' => 'Farm Equipment', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '19'],
            ['description' => 'Farm Implement', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '20'],
            ['description' => 'Farm Trailer', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '21'],
            ['description' => 'Go-Cart', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '22'],
            ['description' => 'Golf Cart', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '23'],
            ['description' => 'Horse Trailers', 'weight' => '7000', 'rate' => '0.00', 'CartonBulkyId' => '24'],
            ['description' => 'Hot Tub', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '25'],
            ['description' => 'Hot Tub &gt; 65 Cu Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '26'],
            ['description' => 'Jacuzzi', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '27'],
            ['description' => 'Jacuzzi &gt; 65 Cu Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '28'],
            ['description' => 'Jet Ski', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '29'],
            ['description' => 'Jet Ski &gt; 14 Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '30'],
            ['description' => 'Kayak &lt; 14 Ft', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '31'],
            ['description' => 'Kayak &gt; 14 Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '32'],
            ['description' => 'Kennel', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '33'],
            ['description' => 'Large Tv &gt; 40', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '34'],
            ['description' => 'Light/Bulky', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '35'],
            ['description' => 'Limousine', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '36'],
            ['description' => 'Mini Mobile Homes', 'weight' => '7000', 'rate' => '0.00', 'CartonBulkyId' => '37'],
            ['description' => 'Motorbike', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '38'],
            ['description' => 'Motorcycle', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '39'],
            ['description' => 'Piano', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '40'],
            ['description' => 'Piano, Concert', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '41'],
            ['description' => 'Piano, Grand', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '42'],
            ['description' => 'Piano, Spinet', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '43'],
            ['description' => 'Piano, Upright', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '44'],
            ['description' => 'Piano, Baby Grand', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '45'],
            ['description' => 'Pickup and Camper', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '46'],
            ['description' => 'Pickup Truck', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '47'],
            ['description' => 'Playhouse', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '48'],
            ['description' => 'Riding Mower &lt; 25hp', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '49'],
            ['description' => 'Rowboat &lt; 14 Ft', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '50'],
            ['description' => 'Rowboat &gt; 14 Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '51'],
            ['description' => 'Satellite Dish', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '52'],
            ['description' => 'Sculls &gt; 14 Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '53'],
            ['description' => 'Skiff &lt; 14 Ft', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '54'],
            ['description' => 'Skiff &gt; 14 Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '55'],
            ['description' => 'Snow Mobile', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '56'],
            ['description' => 'Spa', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '57'],
            ['description' => 'Spa &gt; 65 Cu Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '58'],
            ['description' => 'Tool Shed', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '59'],
            ['description' => 'Tractor &lt; 25hp', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '60'],
            ['description' => 'Tractor &gt; 25hp', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '61'],
            ['description' => 'Trailer &lt; 14 Ft', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '62'],
            ['description' => 'Trailer &gt; 14 Ft', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '63'],
            ['description' => 'TV/Radio Dish', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '64'],
            ['description' => 'Utility Shed', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '65'],
            ['description' => 'Utility Truck', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '66'],
            ['description' => 'Van', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '67'],
            ['description' => 'Whirlpool Bath', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '68'],
            ['description' => 'Whirlpool Bath &gt; 65 Cu Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '69'],
            ['description' => 'Windsurfer', 'weight' => '0', 'rate' => '90.28', 'CartonBulkyId' => '70'],
            ['description' => 'Windsurfer &gt; 14 Ft', 'weight' => '700', 'rate' => '0.00', 'CartonBulkyId' => '71']
        ];
    }
}

if (!$db) {
    $db = &PearDatabase::getInstance();
}

echo "<br /> Adding vtiger_local_bulky_defaults table<br />";

$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_local_bulky_defaults" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_local_bulky_defaults already exists'.PHP_EOL;
    $sql = 'DESCRIBE `vtiger_local_bulky_defaults` `id`';
    $row = $db->getOne($sql);
    if ($row['Extra'] != 'auto_increment') {
        $sql = 'ALTER TABLE `vtiger_local_bulky_defaults` MODIFY COLUMN `id` INT(11) AUTO_INCREMENT';
        $db->query($sql);
    }
} else {
    $db->query("CREATE TABLE `vtiger_local_bulky_defaults` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `CartonBulkyId` int(10) NOT NULL,
              `description` varchar(100) NOT NULL,
              `weight` varchar(50) NOT NULL,
              `rate` decimal(10,2) NOT NULL,
              `active` int(3) NOT NULL,
               PRIMARY KEY (`id`),
               KEY `bulkyid_index` (`CartonBulkyId`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
}

$insertSql = "INSERT INTO vtiger_local_bulky_defaults (`CartonBulkyId`, `description`, `weight`, `rate`, `active`) VALUES (?,?,?,?,1)";
$updateSql = "UPDATE `vtiger_local_bulky_defaults` SET description = ? WHERE CartonBulkyId = ?";

foreach(defaultBulkyList() as $item) {
    $checkSql = $db->pquery("SELECT * FROM `vtiger_local_bulky_defaults` WHERE CartonBulkyId = ?", [$item['CartonBulkyId']]);
    if ($db->num_rows($checkSql) > 0) {
        $db->pquery($updateSql, [$item['description'], $item['CartonBulkyId']]);
    } else {
        $db->pquery($insertSql, [$item['CartonBulkyId'], $item['description'], $item['weight'], $item['rate']]);
    }
}
$specificLabel = Estimates_Record_Model::getBulkyLabels();
foreach ($specificLabel as $id => $label) {
    $checkSql = $db->pquery("SELECT * FROM `vtiger_local_bulky_defaults` WHERE CartonBulkyId = ?", [$id]);
    if ($db->num_rows($checkSql) > 0) {
        $db->pquery($updateSql, [$label, $id]);
    } else {
        //$db->pquery("UPDATE `vtiger_local_bulky_defaults` SET active = 0 WHERE CartonBulkyId = ?", [$id]);
        $db->pquery($insertSql, [$id, $label, 0, 0]);
    }
}
echo "<br /> Done!  <br />";
print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";
