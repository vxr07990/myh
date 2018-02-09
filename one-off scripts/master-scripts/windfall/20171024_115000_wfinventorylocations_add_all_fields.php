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
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
$Vtiger_Utils_Log = true;

$create = ['WFInventoryLocations' => [
    'LBL_WFINVENTORYLOCATIONS_DETAILS' => [
        'LBL_WFINVENTORYLOCATIONS_INVENTORY' => [
            'name' => 'inventory',
            'table' => 'vtiger_wfinventorylocations',
            'column' => 'inventory',
            'columntype' => 'varchar(100)',
            'uitype' => 10,
            'typeofdata' => 'V~M',
            'quickcreate' => 0,
            'summaryfield' => 1,
            'displaytype' => 3,
            'setRelatedModules' => ['WFInventory'],
        ],
        'LBL_WFINVENTORYLOCATIONS_WAREHOUSE' => [
            'name' => 'warehouse',
            'table' => 'vtiger_wfinventorylocations',
            'column' => 'warehouse',
            'columntype' => 'varchar(100)',
            'uitype' => 10,
            'typeofdata' => 'V~M',
            'quickcreate' => 0,
            'summaryfield' => 1,
            'sequence' => 1,
            'filterSequence' => 1,
            'setRelatedModules' => ['WFWarehouses'],
        ],
        'LBL_WFINVENTORYLOCATIONS_LOCATION' => [
            'name' => 'location',
            'table' => 'vtiger_wfinventorylocations',
            'column' => 'location',
            'columntype' => 'varchar(100)',
            'uitype' => 10,
            'typeofdata' => 'V~M',
            'quickcreate' => 0,
            'summaryfield' => 1,
            'sequence' => 2,
            'filterSequence' => 2,
            'setRelatedModules' => ['WFLocations'],
        ],
        'LBL_WFINVENTORYLOCATIONS_LOCATION_TYPE' => [
            'name' => 'location_type',
            'table' => 'vtiger_wfinventorylocations',
            'column' => 'location_type',
            'columntype' => 'varchar(100)',
            'uitype' => 1,
            'typeofdata' => 'V~O',
            'quickcreate' => 0,
            'sequence' => 3,
            'filterSequence' => 3,
        ],
        'LBL_WFINVENTORYLOCATIONS_SLOT' => [
            'name' => 'wfinventorylocations_slot',
            'table' => 'vtiger_wfinventorylocations',
            'column' => 'wfinventorylocations_slot',
            'columntype' => 'varchar(100)',
            'uitype' => 16,
            'typeofdata' => 'V~M',
            'quickcreate' => 0,
            'sequence' => 4,
            'filterSequence' => 4,
            'setPicklistValues' => ["L", "LC", "C", "CR", "R", "LCR"],
        ],
        'LBL_WFINVENTORYLOCATIONS_OPERATIONS_TASKS' => [
            'name' => 'operations_tasks',
            'table' => 'vtiger_wfinventorylocations',
            'column' => 'operations_tasks',
            'columntype' => 'varchar(100)',
            'uitype' => 10,
            'typeofdata' => 'V~M',
            'quickcreate' => 0,
            'summaryfield' => 1,
            'sequence' => 5,
            'filterSequence' => 5,
            'setRelatedModules' => ['WFOperationsTasks'],
        ],
        'LBL_WFINVENTORYLOCATIONS_QUANTITY' => [
            'name' => 'quantity',
            'table' => 'vtiger_wfinventorylocations',
            'column' => 'quantity',
            'columntype' => 'INT(10)',
            'uitype' => 7,
            'typeofdata' => 'N~M~MIN=1',
            'quickcreate' => 0,
            'sequence' => 6,
            'filterSequence' => 6,
        ],
    ],
],
];

multicreate($create);



$inventoryLocations = Vtiger_Module::getInstance('WFInventoryLocations');
$inventory          = Vtiger_Module::getInstance('WFInventory');
if ($inventoryLocations && $inventory) {
    $entityIdField = Vtiger_Field_Model::getInstance('location', $inventoryLocations);
    if($entityIdField){
        $inventoryLocations->unsetEntityIdentifier();
        $inventoryLocations->setEntityIdentifier($entityIdField);
    }
    $inventory->setRelatedList($inventoryLocations, 'Inventory Locations', ['ADD', 'SELECT'], 'get_related_list');
}

$filter = Vtiger_Filter::getInstance('All', $inventoryLocations);
if($filter) {
    $filter->delete();
}

$filter = new Vtiger_Filter();
$filter->name = 'All';

$inventoryLocations->addFilter($filter);

$fieldOrder = [
    'warehouse',
    'location',
    'location_type',
    'wfinventorylocations_slot',
    'quantity',
    'assigned_user_id',
    'inventory',
];


$db = PearDatabase::getInstance();
foreach ($fieldOrder as $key => $field) {
    $fieldInstance = Vtiger_Field::getInstance($field, $inventoryLocations);
    $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
    $db->pquery($sql, [$key+1, $fieldInstance->id]);
    if(!in_array($field, ['wfinventorylocations_slot', 'assigned_user_id', 'inventory'])){
        $filter->addField($fieldInstance, $key);
    }
}

$changingField = Vtiger_Field::getInstance('location', $inventoryLocations);
if ($changingField) {
    $id = $changingField->id;
    $typeOfData = 'V~M';
    $stmt = "UPDATE `vtiger_field` SET `typeofdata` = ?"
            ." WHERE `fieldid` = ? LIMIT 1";
    $db->pquery($stmt, [$typeOfData, $changingField->id]);

}
