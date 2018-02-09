<?php
if (function_exists("call_ms_function_ver")) {
    $version = 3;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');
include_once('vtlib/Vtiger/Block.php');

// Opps Module
$oppModule = Vtiger_Module::getInstance('Opportunities');

// Vehicles block
$oppVehicles = Vtiger_Block::getInstance('LBL_OPPS_VEHICLES', $oppModule);
if(!$oppVehicles) {
    echo "Adding Opps Vehicle block...<br/>\n";
    $oppVehicles = new VTiger_Block();
    $oppVehicles->label = 'LBL_OPPS_VEHICLES';
    $oppModule->addBlock($oppVehicles);
}else {
    echo "Opportunities Vehicles block already exists.<br/>\n";
}

$fields = [
    'moving_vehicle' => [
        'label' => 'LBL_OPPS_MOVINGVEHICLE',
        'uitype' => 56,
        'datatype' => 'C~O',
        'table' => 'vtiger_potential',
        'columntype' => 'VARCHAR(3)'
    ],
    'number_of_vehicles' => [
        'label' => 'LBL_OPPS_NUMBEROFVEHICLES',
        'uitype' => 7,
        'datatype' => 'I~O',
        'table' => 'vtiger_potential',
        'columntype' => 'INT(19)'
    ],
    'vehicle_year' => [
        'label' => 'LBL_OPPS_VEHICLEYEAR',
        'uitype' => 7,
        'datatype' => 'I~O',
        'table' => 'vtiger_potential',
        'columntype' => 'INT(19)'
    ],
    'vehicle_make' => [
        'label' => 'LBL_OPPS_VEHICLEMAKE',
        'uitype' => 1,
        'datatype' => 'V~O',
        'table' => 'vtiger_potential',
        'columntype' => 'VARCHAR(50)'
    ],
    'vehicle_model' => [
        'label' => 'LBL_OPPS_VEHICLEMODEL',
        'uitype' => 1,
        'datatype' => 'V~O',
        'table' => 'vtiger_potential',
        'columntype' => 'VARCHAR(50)'
    ]
];

foreach($fields as $field => $info) {
    $fieldInstance = VTiger_Field::getInstance($field, $oppModule);
    if(!$fieldInstance) {
        echo "Adding ".$info['label']." field...<br/>\n";
        $fieldInstance = new VTiger_Field();
        $fieldInstance->label = $info['label'];
        $fieldInstance->name = $field;
        $fieldInstance->table = $info['table'];
        $fieldInstance->column = $field;
        $fieldInstance->columntype = $info['columntype'];
        $fieldInstance->uitype = $info['uitype'];
        $fieldInstance->typeofdata = $info['datatype'];
        $oppVehicles->addField($fieldInstance);

        // Add Lead Convert mapping.
        $sql = "SELECT fieldid FROM vtiger_field WHERE fieldname = '".$field."' AND tablename = '".$info['table']."'";
        $pfId = $db->query($sql)->fetchRow()[0];
        $sql = "SELECT fieldid FROM vtiger_field WHERE fieldname = '".$field."' AND tablename = 'vtiger_leaddetails'";
        $lfId = $db->query($sql)->fetchRow()[0];

        if($lfId && $pfId) {
            echo "Adding `vtiger_convertleadmapping` entry...<br/>\n";
            $sql = "INSERT INTO vtiger_convertleadmapping (leadfid, accountfid, contactfid, potentialfid, editable) VALUES ($lfId, 0, 0, $pfId, 1)";
            $res = $db->query($sql);
            if(!$res) {
                echo "Error occurred while saving `vtiger_convertleadmapping` entry. Check mySQL fail log.<br/>\n";
            }
        }else {
            echo "Cannot add `vtiger_convertleadmapping` entry, no ids to supply.<br/>\n";
        }
    }else {
        echo "Field ".$info['label']." already exists.<br/>\n";
    }
}
echo "Done.<br/>\n";



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";