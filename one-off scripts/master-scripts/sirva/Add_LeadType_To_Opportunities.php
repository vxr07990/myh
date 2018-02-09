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
$oppBlock = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppModule);
if(!$oppBlock) {
    echo "Opportunities base block does not exist, cannot add field. Frankly, I don't know how this happened.<br/>\n";
    return;
}

$fields = [
    'lead_type' => [
        'label' => 'LBL_OPPS_LEADTYPE',
        'uitype' => 16,
        'datatype' => 'V~M',
        'table' => 'vtiger_potential',
        'columntype' => 'VARCHAR(200)'
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
        $oppBlock->addField($fieldInstance);

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