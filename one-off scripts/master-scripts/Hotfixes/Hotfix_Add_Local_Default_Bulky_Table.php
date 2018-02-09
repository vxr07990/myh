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


if (!$db) {
    $db = PearDatabase::getInstance();
}

echo "<br /> Adding vtiger_local_bulky_defaults table<br />";


$stmt = 'SELECT * FROM information_schema.tables WHERE table_schema = "' . getenv('DB_NAME') . '" AND table_name = "vtiger_local_bulky_defaults" LIMIT 1';
$res = $db->pquery($stmt);
if ($db->num_rows($res) > 0) {
    echo 'vtiger_local_bulky_defaults already exists'.PHP_EOL;
} else {

$db->pquery("CREATE TABLE `vtiger_local_bulky_defaults` (
  `id` int(11) NOT NULL PRIMARY KEY,
  `CartonBulkyId` int(10) NOT NULL,
  `description` varchar(100) NOT NULL,
  `weight` varchar(50) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `active` int(3) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;", array());
$db->query("CREATE INDEX bulkyid_index ON `vtiger_local_bulky_defaults` (CartonBulkyId)");

$sql = "INSERT INTO vtiger_local_bulky_defaults values(?,?,?,?,?,1)";
$count = 1;
foreach(defaultBulkyList() as $item) {
  $db->pquery($sql,array($count,$item['CartonBulkyId'],$item['description'],$item['weight'],$item['rate']));
  $count++;
}
if(getenv('INSTANCE_NAME') == 'sirva' || getenv('INSTANCE_NAME') == 'arpin') {
  $specificLabel = Estimates_Record_Model::getBulkyLabels();
  $sql = "UPDATE `vtiger_local_bulky_defaults` SET description = ? WHERE CartonBulkyId = ?";

  foreach($specificLabel as $id=>$label) {
    $check = $db->pquery("SELECT * FROM `vtiger_local_bulky_defaults` WHERE CartonBulkyId = ?",array($id));

    if($db->num_rows($check) > 0) {
      $db->pquery($sql, array($label,$id));
    } else {
      $db->pquery("UPDATE `vtiger_local_bulky_defaults` SET active = 0 WHERE CartonBulkyId = ?",array($id));
    }
  }
}

}


echo "<br /> Done!  <br />";
print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";

function defaultBulkyList() {
  return array(
    array('description'=>'4x4 Vehicle', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'1'),
    array('description'=>'Airplanes, Gliders', 'weight'=>'120', 'rate'=>'0.00', 'CartonBulkyId'=>'2'),
    array('description'=>'All Terrain Cycle', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'3'),
    array('description'=>'Animal House', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'4'),
    array('description'=>'Automobile', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'5'),
    array('description'=>'Bath', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'6'),
    array('description'=>'Bath &gt; 65 Cu Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'7'),
    array('description'=>'Boat Trailers', 'weight'=>'1600', 'rate'=>'0.00', 'CartonBulkyId'=>'8'),
    array('description'=>'Boats &lt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'9'),
    array('description'=>'Boats &gt; 14 Ft', 'weight'=>'2500', 'rate'=>'0.00', 'CartonBulkyId'=>'10'),
    array('description'=>'Camper (Truckless)', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'11'),
    array('description'=>'Camper Shell', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'12'),
    array('description'=>'Camper Trailers', 'weight'=>'7000', 'rate'=>'0.00', 'CartonBulkyId'=>'13'),
    array('description'=>'Canoe &lt; 14 Ft', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'14'),
    array('description'=>'Canoe &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'15'),
    array('description'=>'Dinghy &lt; 14 Ft', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'16'),
    array('description'=>'Dinghy &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'17'),
    array('description'=>'Doll House', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'18'),
    array('description'=>'Farm Equipment', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'19'),
    array('description'=>'Farm Implement', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'20'),
    array('description'=>'Farm Trailer', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'21'),
    array('description'=>'Go-Cart', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'22'),
    array('description'=>'Golf Cart', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'23'),
    array('description'=>'Horse Trailers', 'weight'=>'7000', 'rate'=>'0.00', 'CartonBulkyId'=>'24'),
    array('description'=>'Hot Tub', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'25'),
    array('description'=>'Hot Tub &gt; 65 Cu Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'26'),
    array('description'=>'Jacuzzi', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'27'),
    array('description'=>'Jacuzzi &gt; 65 Cu Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'28'),
    array('description'=>'Jet Ski', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'29'),
    array('description'=>'Jet Ski &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'30'),
    array('description'=>'Kayak &lt; 14 Ft', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'31'),
    array('description'=>'Kayak &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'32'),
    array('description'=>'Kennel', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'33'),
    array('description'=>'Large Tv &gt; 40', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'34'),
    array('description'=>'Light/Bulky', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'35'),
    array('description'=>'Limousine', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'36'),
    array('description'=>'Mini Mobile Homes', 'weight'=>'7000', 'rate'=>'0.00', 'CartonBulkyId'=>'37'),
    array('description'=>'Motorbike', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'38'),
    array('description'=>'Motorcycle', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'39'),
    array('description'=>'Piano', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'40'),
    array('description'=>'Piano, Concert', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'41'),
    array('description'=>'Piano, Grand', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'42'),
    array('description'=>'Piano, Spinet', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'43'),
    array('description'=>'Piano, Upright', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'44'),
    array('description'=>'Piano, Baby Grand', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'45'),
    array('description'=>'Pickup and Camper', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'46'),
    array('description'=>'Pickup Truck', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'47'),
    array('description'=>'Playhouse', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'48'),
    array('description'=>'Riding Mower &lt; 25hp', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'49'),
    array('description'=>'Rowboat &lt; 14 Ft', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'50'),
    array('description'=>'Rowboat &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'51'),
    array('description'=>'Satellite Dish', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'52'),
    array('description'=>'Sculls &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'53'),
    array('description'=>'Skiff &lt; 14 Ft', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'54'),
    array('description'=>'Skiff &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'55'),
    array('description'=>'Snow Mobile', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'56'),
    array('description'=>'Spa', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'57'),
    array('description'=>'Spa &gt; 65 Cu Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'58'),
    array('description'=>'Tool Shed', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'59'),
    array('description'=>'Tractor &lt; 25hp', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'60'),
    array('description'=>'Tractor &gt; 25hp', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'61'),
    array('description'=>'Trailer &lt; 14 Ft', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'62'),
    array('description'=>'Trailer &gt; 14 Ft', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'63'),
    array('description'=>'TV/Radio Dish', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'64'),
    array('description'=>'Utility Shed', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'65'),
    array('description'=>'Utility Truck', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'66'),
    array('description'=>'Van', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'67'),
    array('description'=>'Whirlpool Bath', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'68'),
    array('description'=>'Whirlpool Bath &gt; 65 Cu Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'69'),
    array('description'=>'Windsurfer', 'weight'=>'0', 'rate'=>'90.28', 'CartonBulkyId'=>'70'),
    array('description'=>'Windsurfer &gt; 14 Ft', 'weight'=>'700', 'rate'=>'0.00', 'CartonBulkyId'=>'71')
  );
}
