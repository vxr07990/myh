<?php
/**
 * Created by PhpStorm.
 * User: jgriffin
 * Date: 5/3/2017
 * Time: 3:26 PM
 */
if (!Vtiger_Utils::CheckColumnExists('vtiger_tariffpackingitems', 'standardItem')) {
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

if (!function_exists('HTSPIASF_correctSomePackingItems')) {
    function HTSPIASF_correctSomePackingItems($existingRow) {
        $tryAndCorrectSome = [
            '1.5'                           => '1',
            '1.5 Carton'                    => '1',
            '1.5 cf'                        => '1',
            '1.5 Container'                 => '1',
            '3.0'                           => '2',
            '3.0 Carton'                    => '2',
            '3.0 cf'                        => '2',
            '3.0 Container'                 => '2',
            '4.5'                           => '3',
            '4.5 Carton'                    => '3',
            '4.5 cf'                        => '3',
            '4.5 Container'                 => '3',
            '6.0'                           => '4',
            '6.0 Carton'                    => '4',
            '6.0 cf'                        => '4',
            '6.0 Container'                 => '4',
            '6.5 Carton'                    => '16',
            '6.5 cf'                        => '16',
            '6.5 Container'                 => '16',
            'Book Carton'                   => '5',
            'Crib'                          => '6',
            'Crib Mattress Container'       => '6',
            'Dish Pack'                     => '8',
            'Dish Pack Container'           => '8',
            'Double'                        => '7',
            'Double Mattress Container'     => '7',
            'K/Q'                           => '13',
            'K/Q Split'                     => '14',
            'King/Queen'                    => '13',
            'King/Queen Mattress Container' => '13',
            'Mattress Cover'                => '17',
            'Mirror Carton'                 => '12',
            'Single'                        => '14',
            'TV Carton'                     => '102',
            'TV Container'                  => '102',
            'Wardrobe'                      => '15',
            'Wardrobe Carton'               => '15',
            'Wardrobe Container'            => '15'
        ];

        return $tryAndCorrectSome[$existingRow['name']];
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

//@TODO JG HERE fix.
require_once ('includes/runtime/BaseModel.php');
require_once ('modules/Vtiger/models/Record.php');
require_once ('modules/Inventory/models/Record.php');
require_once ('modules/Quotes/models/Record.php');
require_once ('modules/Estimates/models/Record.php');

$storeEnv = getenv('INSTANCE_NAME');
putenv('INSTANCE_NAME=NOTHING');
$defaultPackingLabels = Estimates_Record_Model::getPackingLabelsStatic();
$db                   = &PearDatabase::getInstance();
$stmt                 = 'SELECT * FROM `vtiger_tariffpackingitems` WHERE `pack_item_id` IS NOT NULL';
$res = $db->query($stmt);
if ($res && method_exists($res, 'fetchRow')) {
    while ($row =& $res->fetchRow()) {
        if (!$defaultPackingLabels[$row['pack_item_id']]) {
            continue;
        }
        $updateStmt = 'UPDATE `vtiger_tariffpackingitems` SET `name` = ?, `standardItem` = ? WHERE `line_item_id` = ?';
        $db->pquery($updateStmt, [$defaultPackingLabels[$row['pack_item_id']], 1, $row['line_item_id']]);
    }
}
putenv('INSTANCE_NAME='.$storeEnv);
$stmt                 = 'SELECT * FROM `vtiger_tariffpackingitems` WHERE (`pack_item_id` IS NULL OR `pack_item_id` = 0)';
$res = $db->query($stmt);
if ($res && method_exists($res, 'fetchRow')) {
    while ($row =& $res->fetchRow()) {
        if ($pack_item_id = HTSPIASF_correctSomePackingItems($row)) {
            $updateStmt = 'UPDATE `vtiger_tariffpackingitems` SET `name` = ?, `standardItem` = ?, `pack_item_id` = ? WHERE `line_item_id` = ?';
            $db->pquery($updateStmt, [$defaultPackingLabels[$pack_item_id], 1, $pack_item_id, $row['line_item_id']]);
        }
    }
}

print "\e[36mFINISHED: " . __FILE__ . "<br />\n\e[0m";
