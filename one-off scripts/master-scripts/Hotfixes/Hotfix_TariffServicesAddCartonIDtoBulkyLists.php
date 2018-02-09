<?php
/**
 * Created by PhpStorm.
 * User: jgriffin
 * Date: 5/3/2017
 * Time: 3:30 PM
 */

if (!Vtiger_Utils::CheckColumnExists('vtiger_tariffbulky', 'standardItem')) {
    return;
}

if (function_exists("call_ms_function_ver")) {
    $version = 1;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}

if (!function_exists('HTSPIASF_correctSomeBulkyItems')) {
    function HTSPIASF_correctSomeBulkyItems() {
        return $tryAndCorrectSome = [
            'Airplanes, Gliders'           => '2',
            'Boat Trailers'                => '8',
            'Boats > 14 ft.'               => '10',
            'Camper (Truckless)'           => '11',
            'Camper Trailers'              => '13',
            'Canoe > 14 ft.'               => '15',
            'Dinghy > 14 ft.'              => '17',
            'Horse Trailers'               => '24',
            'Kayak > 14 ft.'               => '32',
            'Pickup and Camper'            => '46',
            'Rowboat > 14 ft.'             => '51',
            'Sailboat > 14 ft.'            => '74',
            'Sculls > 14 ft.'              => '53',
            'Skiff > 14 ft.'               => '55',
            'Trailer < 14 ft.'             => '62',
            'Trailer >14 ft.'              => '63',
            'Boats < 14 ft.'               => '9',
            'Canoe <14 ft.'                => '14',
            'Dinghies > 14 ft.'            => '17',
            'Dingie <14 ft.'               => '16',
            'Gym System'                   => '73',
            'Kayak <14 ft.'                => '31',
            'Mini Mobile Homes'            => '37',
            'Pick Up Truck'                => '47',
            'Row Boat <14 ft.'             => '50',
            'Skiff <14 ft.'                => '54',
            'Tractor <25hp'                => '60',
            'Tractor >25hp'                => '61',
            'Trailer <14 ft.'              => '62',
            'Canoe < 14 ft.'               => '14',
            'Dinghy < 14 ft.'              => '16',
            'Jet Ski > 14 ft.'             => '30',
            'Kayak < 14 ft.'               => '31',
            'Piano,Baby Grand'             => '45',
            'Rowboat < 14 ft.'             => '50',
            'Skiff < 14 ft.'               => '54',
            'Whirlpool Bath > 65 Cu'       => '69',
            'Windsurfer > 14 ft.'          => '71',
            'Boats < 14 Ft'                => '9',
            'Boats > 14 Ft'                => '10',
            'Sculls > 14 Ft'               => '53',
            'Whirlpool Bath > 65 Cu Ft'    => '69',
            'Camper, not mounted on truck' => '11',
        ];
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

//@TODO JG HERE fix.
require_once 'vtlib/Vtiger/Menu.php';
require_once 'vtlib/Vtiger/Module.php';
require_once ('includes/runtime/BaseModel.php');
require_once ('modules/Vtiger/models/Record.php');
require_once ('modules/Inventory/models/Record.php');
require_once ('modules/Quotes/models/Record.php');
require_once ('modules/Estimates/models/Record.php');

$defaultBulkyLabels = Estimates_Record_Model::getBulkyLabels();
$db                   = &PearDatabase::getInstance();
$stmt                 = 'SELECT * FROM `vtiger_tariffbulky` WHERE (`CartonBulkyId` IS NULL OR `CartonBulkyId` = 0) AND `description` = ?';
foreach($defaultBulkyLabels as $id=>$label){
    $res = $db->pquery($stmt,array($label));
    if ($res && method_exists($res, 'fetchRow')) {
        while ($row =& $res->fetchRow()) {
            $updateStmt = 'UPDATE `vtiger_tariffbulky` SET `standardItem` = ?, `CartonBulkyId` = ? WHERE `line_item_id` = ?';
            $db->pquery($updateStmt, [1, $id, $row['line_item_id']]);
        }
    }
}

foreach(HTSPIASF_correctSomeBulkyItems() as $name=>$newId) {
    $res = $db->pquery($stmt,array($name));
    if ($res && method_exists($res, 'fetchRow')) {
        while ($row =& $res->fetchRow()) {
            $updateStmt = 'UPDATE `vtiger_tariffbulky` SET `description` = ?, `standardItem` = ?, `CartonBulkyId` = ? WHERE `line_item_id` = ?';
            $db->pquery($updateStmt, [$name,1, $newId, $row['line_item_id']]);
        }
    }
}

print "\e[36mFINISHED: " . __FILE__ . "<br />\n\e[0m";
