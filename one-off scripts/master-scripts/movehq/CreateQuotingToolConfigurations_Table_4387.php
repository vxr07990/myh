<?php
if (function_exists("call_ms_function_ver")) {
    $version = 2; // Need to add +1 every time you update that script
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

error_reporting(E_ERROR);
require_once 'include/utils/utils.php';
require_once 'include/utils/CommonUtils.php';

require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
require_once 'modules/QuotingTool/QuotingTool.php';
global  $adb;
if (!Vtiger_Utils::CheckTable('vtiger_quotingtool_configurations')) {
    $stmt = 'CREATE TABLE `vtiger_quotingtool_configurations`
    ( `id` int(11) NOT NULL AUTO_INCREMENT, `module` varchar(255) DEFAULT NULL, `related_modules` varchar(255) DEFAULT NULL, `guest_blocks` text DEFAULT NULL,`isactive` tinyint(1) DEFAULT 1,PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';
    $adb->pquery($stmt);
    echo "Created table vtiger_quotingtool_configurations success <br>";
} else {
    echo 'vtiger_quotingtool_configurations already exists' . PHP_EOL . '<br>';
}

// Set default modules, related modules and guest blocks
$guestBlocks = array(
    'Leads' => array(
        array(
            'id' => 1,
            'name' => 'ExtraStops',
            'label' => 'Extra Stops',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'extrastops_name',
                    'label' => 'Name'
                ),
                array(
                    'id' => 2,
                    'name' => 'extrastops_sequence',
                    'label' => 'Sequence'
                ),
                array(
                    'id' => 3,
                    'name' => 'extrastops_weight',
                    'label' => 'Weight'
                ),
                array(
                    'id' => 4,
                    'name' => 'extrastops_isprimary',
                    'label' => 'Is Primary'
                ),
                array(
                    'id' => 5,
                    'name' => 'extrastops_address1',
                    'label' => 'Address 1'
                ),
                array(
                    'id' => 6,
                    'name' => 'extrastops_address2',
                    'label' => 'Address 2'
                ),
                array(
                    'id' => 7,
                    'name' => 'extrastops_phone1',
                    'label' => 'Phone 1'
                ),
                array(
                    'id' => 8,
                    'name' => 'extrastops_phone2',
                    'label' => 'Phone 2'
                ),
                array(
                    'id' => 9,
                    'name' => 'extrastops_phonetype1',
                    'label' => 'Phone Type 1'
                ),
                array(
                    'id' => 10,
                    'name' => 'extrastops_phonetype2',
                    'label' => 'Phone Type 2'
                ),
                array(
                    'id' => 11,
                    'name' => 'extrastops_city',
                    'label' => 'City'
                ),
                array(
                    'id' => 12,
                    'name' => 'extrastops_state',
                    'label' => 'State'
                ),
                array(
                    'id' => 13,
                    'name' => 'extrastops_zip',
                    'label' => 'Zip'
                ),
                array(
                    'id' => 14,
                    'name' => 'extrastops_country',
                    'label' => 'Country'
                ),
                array(
                    'id' => 15,
                    'name' => 'extrastops_date',
                    'label' => 'Date'
                ),
                array(
                    'id' => 16,
                    'name' => 'extrastops_contact',
                    'label' => 'Contact'
                ),
                array(
                    'id' => 17,
                    'name' => 'extrastops_type',
                    'label' => 'Location Type'
                ),
                array(
                    'id' => 18,
                    'name' => 'extrastops_description',
                    'label' => 'Stop Description'
                )
            )
        ),
        array(
            'id' => 2,
            'name' => 'MoveRoles',
            'label' => 'Move Roles',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'moveroles_role',
                    'label' => 'Role'
                ),
                array(
                    'id' => 2,
                    'name' => 'moveroles_employees',
                    'label' => 'Personnel'
                )
            )
        )
    ),
    'Opportunities' => array(
        array(
            'id' => 1,
            'name' => 'ExtraStops',
            'label' => 'Extra Stops',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'extrastops_name',
                    'label' => 'Name'
                ),
                array(
                    'id' => 2,
                    'name' => 'extrastops_sequence',
                    'label' => 'Sequence'
                ),
                array(
                    'id' => 3,
                    'name' => 'extrastops_weight',
                    'label' => 'Weight'
                ),
                array(
                    'id' => 4,
                    'name' => 'extrastops_isprimary',
                    'label' => 'Is Primary'
                ),
                array(
                    'id' => 5,
                    'name' => 'extrastops_address1',
                    'label' => 'Address 1'
                ),
                array(
                    'id' => 6,
                    'name' => 'extrastops_address2',
                    'label' => 'Address 2'
                ),
                array(
                    'id' => 7,
                    'name' => 'extrastops_phone1',
                    'label' => 'Phone 1'
                ),
                array(
                    'id' => 8,
                    'name' => 'extrastops_phone2',
                    'label' => 'Phone 2'
                ),
                array(
                    'id' => 9,
                    'name' => 'extrastops_phonetype1',
                    'label' => 'Phone Type 1'
                ),
                array(
                    'id' => 10,
                    'name' => 'extrastops_phonetype2',
                    'label' => 'Phone Type 2'
                ),
                array(
                    'id' => 11,
                    'name' => 'extrastops_city',
                    'label' => 'City'
                ),
                array(
                    'id' => 12,
                    'name' => 'extrastops_state',
                    'label' => 'State'
                ),
                array(
                    'id' => 13,
                    'name' => 'extrastops_zip',
                    'label' => 'Zip'
                ),
                array(
                    'id' => 14,
                    'name' => 'extrastops_country',
                    'label' => 'Country'
                ),
                array(
                    'id' => 15,
                    'name' => 'extrastops_date',
                    'label' => 'Date'
                ),
                array(
                    'id' => 16,
                    'name' => 'extrastops_contact',
                    'label' => 'Contact'
                ),
                array(
                    'id' => 17,
                    'name' => 'extrastops_type',
                    'label' => 'Location Type'
                ),
                array(
                    'id' => 18,
                    'name' => 'extrastops_description',
                    'label' => 'Stop Description'
                )
            ),
        ),
        array(
            'id' => 2,
            'name' => 'MoveRoles',
            'label' => 'Move Roles',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'moveroles_role',
                    'label' => 'Role'
                ),
                array(
                    'id' => 2,
                    'name' => 'moveroles_employees',
                    'label' => 'Personnel'
                )
            ),
        ),
        array(
            'id' => 3,
            'name' => 'ParticipatingAgents',
            'label' => 'Participating Agents',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'agent_type',
                    'label' => 'Type'
                ),
                array(
                    'id' => 2,
                    'name' => 'agents_id',
                    'label' => 'Agent'
                ),
                array(
                    'id' => 3,
                    'name' => 'agent_permission',
                    'label' => 'Permission Level'
                ),
                array(
                    'id' => 4,
                    'name' => 'participating_status',
                    'label' => 'Status'
                )
            )
        )
    ),
    'Orders' => array(
        array(
            'id' => 1,
            'name' => 'ExtraStops',
            'label' => 'Extra Stops',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'extrastops_name',
                    'label' => 'Name'
                ),
                array(
                    'id' => 2,
                    'name' => 'extrastops_sequence',
                    'label' => 'Sequence'
                ),
                array(
                    'id' => 3,
                    'name' => 'extrastops_weight',
                    'label' => 'Weight'
                ),
                array(
                    'id' => 4,
                    'name' => 'extrastops_isprimary',
                    'label' => 'Is Primary'
                ),
                array(
                    'id' => 5,
                    'name' => 'extrastops_address1',
                    'label' => 'Address 1'
                ),
                array(
                    'id' => 6,
                    'name' => 'extrastops_address2',
                    'label' => 'Address 2'
                ),
                array(
                    'id' => 7,
                    'name' => 'extrastops_phone1',
                    'label' => 'Phone 1'
                ),
                array(
                    'id' => 8,
                    'name' => 'extrastops_phone2',
                    'label' => 'Phone 2'
                ),
                array(
                    'id' => 9,
                    'name' => 'extrastops_phonetype1',
                    'label' => 'Phone Type 1'
                ),
                array(
                    'id' => 10,
                    'name' => 'extrastops_phonetype2',
                    'label' => 'Phone Type 2'
                ),
                array(
                    'id' => 11,
                    'name' => 'extrastops_city',
                    'label' => 'City'
                ),
                array(
                    'id' => 12,
                    'name' => 'extrastops_state',
                    'label' => 'State'
                ),
                array(
                    'id' => 13,
                    'name' => 'extrastops_zip',
                    'label' => 'Zip'
                ),
                array(
                    'id' => 14,
                    'name' => 'extrastops_country',
                    'label' => 'Country'
                ),
                array(
                    'id' => 15,
                    'name' => 'extrastops_date',
                    'label' => 'Date'
                ),
                array(
                    'id' => 16,
                    'name' => 'extrastops_contact',
                    'label' => 'Contact'
                ),
                array(
                    'id' => 17,
                    'name' => 'extrastops_type',
                    'label' => 'Location Type'
                ),
                array(
                    'id' => 18,
                    'name' => 'extrastops_description',
                    'label' => 'Stop Description'
                )
            ),
        ),
        array(
            'id' => 2,
            'name' => 'MoveRoles',
            'label' => 'Move Roles',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'moveroles_role',
                    'label' => 'Role'
                ),
                array(
                    'id' => 2,
                    'name' => 'moveroles_employees',
                    'label' => 'Personnel'
                )
            ),
        ),
        array(
            'id' => 3,
            'name' => 'ParticipatingAgents',
            'label' => 'Participating Agents',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'agent_type',
                    'label' => 'Type'
                ),
                array(
                    'id' => 2,
                    'name' => 'agents_id',
                    'label' => 'Agent'
                ),
                array(
                    'id' => 3,
                    'name' => 'agent_permission',
                    'label' => 'Permission Level'
                ),
                array(
                    'id' => 4,
                    'name' => 'participating_status',
                    'label' => 'Status'
                )
            ),
        ),
        array(
            'id' => 4,
            'name' => 'VehicleTransportation',
            'label' => 'Vehicle Transportation',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'vehicletrans_ratingtype',
                    'label' => 'Rate Type'
                ),
                array(
                    'id' => 2,
                    'name' => 'vehicletrans_description',
                    'label' => 'VIN'
                ),
                array(
                    'id' => 3,
                    'name' => 'vehicletrans_make',
                    'label' => 'Make'
                ),
                array(
                    'id' => 4,
                    'name' => 'vehicletrans_modelyear',
                    'label' => 'Model Year'
                ),
                array(
                    'id' => 5,
                    'name' => 'vehicletrans_model',
                    'label' => 'Model'
                ),
                array(
                    'id' => 6,
                    'name' => 'vehicletrans_type',
                    'label' => 'Style'
                ),
                array(
                    'id' => 7,
                    'name' => 'vehicletrans_inoperable',
                    'label' => 'Inoperable'
                ),
                array(
                    'id' => 8,
                    'name' => 'vehicletrans_groundclearance',
                    'label' => 'Ground Clearance < 8 in.'
                ),
                array(
                    'id' => 9,
                    'name' => 'vehicletrans_oversized',
                    'label' => 'Oversized'
                ),
                array(
                    'id' => 10,
                    'name' => 'vehicletrans_weight',
                    'label' => 'Weight'
                ),
                array(
                    'id' => 11,
                    'name' => 'vehicletrans_cube',
                    'label' => 'Cube'
                )
            )
        )
    ),
    'OrdersTask' => array(
        array(
            'id' => 1,
            'name' => 'LBL_PERSONNEL',
            'label' => 'Personnel',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'num_of_personal',
                    'label' => 'Estimated Number of Personnel'
                ),
                array(
                    'id' => 2,
                    'name' => 'est_hours_personnel',
                    'label' => 'Est. Hours / Personnel'
                ),
                array(
                    'id' => 3,
                    'name' => 'personnel_type',
                    'label' => 'Personnel Type'
                )
            ),
        ),
        array(
            'id' => 2,
            'name' => 'LBL_VEHICLES',
            'label' => 'Vehicles',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'num_of_vehicle',
                    'label' => 'Estimated Number of Vehicles'
                ),
                array(
                    'id' => 2,
                    'name' => 'est_hours_vehicle',
                    'label' => 'Est. Hours / Vehicle'
                ),
                array(
                    'id' => 3,
                    'name' => 'vehicle_type',
                    'label' => 'Vehicle Type'
                )
            ),
        ),
        array(
            'id' => 3,
            'name' => 'LBL_CPU',
            'label' => 'CPU',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'carton_name',
                    'label' => 'Name'
                ),
                array(
                    'id' => 2,
                    'name' => 'cartonqty',
                    'label' => 'Carton Qty'
                ),
                array(
                    'id' => 3,
                    'name' => 'packingqty',
                    'label' => 'Packing Qty'
                ),
                array(
                    'id' => 4,
                    'name' => 'unpackingqty',
                    'label' => 'Unpacking Qty'
                )
            ),
        ),
        array(
            'id' => 4,
            'name' => 'LBL_EQUIPMENT',
            'label' => 'Equipment',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'equipment_name',
                    'label' => 'Name'
                ),
                array(
                    'id' => 2,
                    'name' => 'equipmentqty',
                    'label' => 'Qty Requested'
                )
            )
        )
    ),
    'Estimates' => array(
        array(
            'id' => 1,
            'name' => 'ExtraStops',
            'label' => 'Extra Stops',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'extrastops_name',
                    'label' => 'Name'
                ),
                array(
                    'id' => 2,
                    'name' => 'extrastops_sequence',
                    'label' => 'Sequence'
                ),
                array(
                    'id' => 3,
                    'name' => 'extrastops_weight',
                    'label' => 'Weight'
                ),
                array(
                    'id' => 4,
                    'name' => 'extrastops_isprimary',
                    'label' => 'Is Primary'
                ),
                array(
                    'id' => 5,
                    'name' => 'extrastops_address1',
                    'label' => 'Address 1'
                ),
                array(
                    'id' => 6,
                    'name' => 'extrastops_address2',
                    'label' => 'Address 2'
                ),
                array(
                    'id' => 7,
                    'name' => 'extrastops_phone1',
                    'label' => 'Phone 1'
                ),
                array(
                    'id' => 8,
                    'name' => 'extrastops_phone2',
                    'label' => 'Phone 2'
                ),
                array(
                    'id' => 9,
                    'name' => 'extrastops_phonetype1',
                    'label' => 'Phone Type 1'
                ),
                array(
                    'id' => 10,
                    'name' => 'extrastops_phonetype2',
                    'label' => 'Phone Type 2'
                ),
                array(
                    'id' => 11,
                    'name' => 'extrastops_city',
                    'label' => 'City'
                ),
                array(
                    'id' => 12,
                    'name' => 'extrastops_state',
                    'label' => 'State'
                ),
                array(
                    'id' => 13,
                    'name' => 'extrastops_zip',
                    'label' => 'Zip'
                ),
                array(
                    'id' => 14,
                    'name' => 'extrastops_country',
                    'label' => 'Country'
                ),
                array(
                    'id' => 15,
                    'name' => 'extrastops_date',
                    'label' => 'Date'
                ),
                array(
                    'id' => 16,
                    'name' => 'extrastops_contact',
                    'label' => 'Contact'
                ),
                array(
                    'id' => 17,
                    'name' => 'extrastops_type',
                    'label' => 'Location Type'
                ),
                array(
                    'id' => 18,
                    'name' => 'extrastops_description',
                    'label' => 'Stop Description'
                )
            )
        ),
    ),
    'Actuals' => array(
        array(
            'id' => 1,
            'name' => 'ExtraStops',
            'label' => 'Extra Stops',
            'fields' => array(
                array(
                    'id' => 1,
                    'name' => 'extrastops_name',
                    'label' => 'Name'
                ),
                array(
                    'id' => 2,
                    'name' => 'extrastops_sequence',
                    'label' => 'Sequence'
                ),
                array(
                    'id' => 3,
                    'name' => 'extrastops_weight',
                    'label' => 'Weight'
                ),
                array(
                    'id' => 4,
                    'name' => 'extrastops_isprimary',
                    'label' => 'Is Primary'
                ),
                array(
                    'id' => 5,
                    'name' => 'extrastops_address1',
                    'label' => 'Address 1'
                ),
                array(
                    'id' => 6,
                    'name' => 'extrastops_address2',
                    'label' => 'Address 2'
                ),
                array(
                    'id' => 7,
                    'name' => 'extrastops_phone1',
                    'label' => 'Phone 1'
                ),
                array(
                    'id' => 8,
                    'name' => 'extrastops_phone2',
                    'label' => 'Phone 2'
                ),
                array(
                    'id' => 9,
                    'name' => 'extrastops_phonetype1',
                    'label' => 'Phone Type 1'
                ),
                array(
                    'id' => 10,
                    'name' => 'extrastops_phonetype2',
                    'label' => 'Phone Type 2'
                ),
                array(
                    'id' => 11,
                    'name' => 'extrastops_city',
                    'label' => 'City'
                ),
                array(
                    'id' => 12,
                    'name' => 'extrastops_state',
                    'label' => 'State'
                ),
                array(
                    'id' => 13,
                    'name' => 'extrastops_zip',
                    'label' => 'Zip'
                ),
                array(
                    'id' => 14,
                    'name' => 'extrastops_country',
                    'label' => 'Country'
                ),
                array(
                    'id' => 15,
                    'name' => 'extrastops_date',
                    'label' => 'Date'
                ),
                array(
                    'id' => 16,
                    'name' => 'extrastops_contact',
                    'label' => 'Contact'
                ),
                array(
                    'id' => 17,
                    'name' => 'extrastops_type',
                    'label' => 'Location Type'
                ),
                array(
                    'id' => 18,
                    'name' => 'extrastops_description',
                    'label' => 'Stop Description'
                )
            )
        ),
    ),
);
$enableModuleWithRelated = array(
    'Leads' => array('Accounts'),
    'Opportunities' => array('Contacts', 'Accounts', 'Contracts'),
    'Orders' => array('Contacts', 'Accounts', 'Contracts'),
    'OrdersTask' => array('Orders'),
    'Estimates' => array('Contacts', 'Accounts', 'Contracts', 'Opportunities', 'Orders'),
    'Actuals' => array('Orders', 'Contacts', 'Accounts', 'Contracts')
);

foreach ($enableModuleWithRelated as $module => $relatedModule) {
    $encode_relatedModule = json_encode($relatedModule);
    foreach ($guestBlocks as $guestBlock=> $val) {
        if ($guestBlock == $module) {
            $encode_guestBlock = json_encode($val);
            $results = $adb->pquery("SELECT `module` from `vtiger_quotingtool_configurations` WHERE `module`= ?",array($guestBlock));
            if ($adb->num_rows($results) == 0){
                $adb->pquery("INSERT INTO `vtiger_quotingtool_configurations` (`module`, `related_modules`, `guest_blocks`) VALUES (?, ?, ?)", array($module, $encode_relatedModule, $encode_guestBlock));
            }
        }
    }
}
$sql = $adb->pquery("SELECT `module` FROM `vtiger_quotingtool_configurations`",array());
$enableModule = array();
if ($adb->num_rows($sql) > 0) {
    while ($row = $adb->fetchByAssoc($sql)) {
        $enableModule[] = $row['module'];
    }
}
$Document = Vtiger_Module::getInstance('Documents');
foreach ($enableModule as $module) {
    $moduleInstance = Vtiger_Module::getInstance("$module");
    $rs = $adb->pquery("SELECT vtiger_relatedlists.*,vtiger_tab.name as modulename FROM vtiger_relatedlists
					INNER JOIN vtiger_tab on vtiger_tab.tabid = vtiger_relatedlists.related_tabid AND vtiger_tab.presence != 1
					WHERE vtiger_relatedlists.tabid = ? AND related_tabid = ?",array($moduleInstance->id,$Document->id));
    if ($adb->num_rows($rs) == 0) {
        $moduleInstance->setRelatedList($Document, 'Documents', array('SELECT,ADD'), 'get_related_list');
    }
}

echo "<br>DONE!<br>";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";