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


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


$moduleInstance = Vtiger_Module::getInstance('IntlQuote');

echo '<br />Checking if International Quote module exists.<br />';

if ($moduleInstance) {
    echo '<br />International Quote already exists.<br />';
} else {
    echo '<br />International Quote does not exist. Creating it now:<br />';
    $moduleInstance = new Vtiger_Module();
    $moduleInstance->name = 'IntlQuote';
    $moduleInstance->save();
    $moduleInstance->initTables();
    $new_instance = true;
}
$new_fields_array = array(
    array(
        'fieldBlock'=>'LBL_OPPORTUNITY_AGENTINFO', 'fields'=>array(
        array('block'=>'LBL_TO_INTERNATIONAL','field'=>'to_international','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_TO_ATTENTION','field'=>'to_attention','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_TO_REQUEST_DATE','field'=>'to_request_date','uitype'=>'5','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FROM_AGENT_NAME','field'=>'from_agent_name','uitype'=>'1','typeofdata'=>'V~M','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FROM_AGENT_CODE','field'=>'from_agent_code','uitype'=>'1','typeofdata'=>'V~M','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_REQUESTED_BY','field'=>'requested_by','uitype'=>'1','typeofdata'=>'V~M','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FAX','field'=>'fax','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_RESPONSE_BY','field'=>'rate_response_needed_by','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_EMAIL','field'=>'email','uitype'=>'1','typeofdata'=>'V~M','columntype'=>'VARCHAR(100)')
    ),
    ),
    array(
        'fieldBlock'=>'LBL_OPPORTUNITY_QUOTEINFO', 'fields'=>array(
        array('block'=>'LBL_TRANSFEREE_NAME','field'=>'transferee_name','uitype'=>'1','typeofdata'=>'V~M','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FIRST_REQUEST','field'=>'first_rate_request','uitype'=>'56','typeofdata'=>'V~O','columntype'=>'VARCHAR(50)'),
        array('block'=>'LBL_PRIVATE_TRANSFEREE','field'=>'private_transferee','uitype'=>'56','typeofdata'=>'V~O','columntype'=>'VARCHAR(50)'),
        array('block'=>'LBL_REQUOTE','field'=>'re_quote','uitype'=>'56','typeofdata'=>'V~O','columntype'=>'VARCHAR(50)'),
        array('block'=>'LBL_RATE_QUOTE','field'=>'rate_quote','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_ACCOUNT_NAME','field'=>'account_name','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_INTLQUOTES_POTENTIALID','field'=>'potential_id','uitype'=>'3','typeofdata'=>'V~O','columntype'=>'INT(11)')
    ),
    ),
    array(
        'fieldBlock'=>'LBL_OPPORTUNITY_LOCATIONDETAILS', 'fields'=>array(
        array('block'=>'LBL_ORIGIN_TYPE','field'=>'origin_type','uitype'=>'16','typeofdata'=>'V~M','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_ORIGIN_CITY_COUNTRY','field'=>'origin_city_country','uitype'=>'1','typeofdata'=>'V~M','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_DESTINATION_TYPE','field'=>'destination_type','uitype'=>'16','typeofdata'=>'V~M','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_DESTINATION_CITY_COUNTRY','field'=>'destination_city_country','uitype'=>'1','typeofdata'=>'V~M','columntype'=>'VARCHAR(100)'),
    ),
    ),
    array(
        'fieldBlock'=>'LBL_OPPORTUNITY_OTHERINFO', 'fields'=>array(
        array('block'=>'LBL_AIR_WEIGHT','field'=>'air_weight','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_AIR_WEIGHT_TYPE','field'=>'air_weight_type','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_AIR_VOLUME','field'=>'air_volume','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_AIR_VOLUME_TYPE','field'=>'air_volume_type','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_AIR_PACKING_TYPE','field'=>'air_packing_type','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_AIR_PACKING_TYPE_OTHER','field'=>'air_packing_type_other','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_LCL_WEIGHT','field'=>'lcl_weight','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_LCL_WEIGHT_TYPE','field'=>'lcl_weight_type','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_LCL_VOLUME','field'=>'lcl_volume','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_LCL_VOLUME_TYPE','field'=>'lcl_volume_type','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_LCL_PACKING_TYPE','field'=>'lcl_packing_type','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_LCL_PACKING_TYPE_OTHER','field'=>'lcl_packing_type_other','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FCL_WEIGHT','field'=>'fcl_weight','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FCL_WEIGHT_TYPE','field'=>'fcl_weight_type','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FCL_VOLUME','field'=>'fcl_volume','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FCL_VOLUME_TYPE','field'=>'fcl_volume_type','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FCL_PACKING_TYPE','field'=>'fcl_packing_type','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FCL_PACKING_TYPE_2','field'=>'fcl_packing_type_2','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_FCL_PACKING_TYPE_OTHER','field'=>'fcl_packing_type_other','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_VEHICLE_WEIGHT','field'=>'vehicle_weight','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_VEHICLE_CUBE','field'=>'vehicle_cube','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_VEHICLE_MAKE','field'=>'vehicle_make','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_VEHICLE_MODEL','field'=>'vehicle_model','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_VEHICLE_YEAR','field'=>'vehicle_year','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_VEHICLE_PACKING_TYPE','field'=>'vehicle_packing_type','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_VEHICLE_PACKING_OTHER','field'=>'vehicle_packing_other','uitype'=>'1','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_SPECIAL_REQUIREMENTS','field'=>'special_requirements','uitype'=>'19','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
        array('block'=>'LBL_STORAGE','field'=>'storage','uitype'=>'19','typeofdata'=>'V~O','columntype'=>'VARCHAR(100)'),
    ),
    )
);
foreach ($new_fields_array as $new_field) {
    $block                = Vtiger_Block::getInstance($new_field['fieldBlock'], $moduleInstance);
    if (!$block) {
        echo "<br> block doesn't exist. creating it now.<br>";
        $newBlock        = new Vtiger_Block();
        $newBlock->label = $new_field['fieldBlock'];
        $moduleInstance->addBlock($newBlock);
        echo "<br>".$new_field['fieldBlock']." block creation complete.<br>";
    } else {
        echo "<br>".$new_field['fieldBlock']." already exists.<br>";
    }
    $block                = Vtiger_Block::getInstance($new_field['fieldBlock'], $moduleInstance);
    if ($block) {
        foreach ($new_field['fields'] as $fields) {
            $field1 = Vtiger_Field::getInstance($fields['field'], $moduleInstance);
            if ($field1) {
                echo '<br>'.$fields['field'].' field exists.<br>';
            } else {
                echo "<br>Creating ".$fields['field']." field:<br>";
                $field1             = new Vtiger_Field();
                $field1->label      = $fields['block'];
                $field1->name       = $fields['field'];
                $field1->table      = 'vtiger_intlquote';
                $field1->column     = $fields['field'];
                $field1->columntype = $fields['columntype'];
                $field1->uitype     = $fields['uitype'];
                $field1->typeofdata = $fields['typeofdata'];
                $block->addField($field1);
                $moduleInstance->setEntityIdentifier($field1);
                echo "<br>".$fields['field']." field created!<br>";
            }
        }
    } else {
        echo "<br>".$new_field['fieldBlock']." already exists.<br>";
    }
}
// Sharing Access Setup

if ($new_instance) {
    echo '<br />International Quote created!<br />';
    $filter1 = new Vtiger_Filter();
    $filter1->name = 'All';
    $filter1->isdefault = true;
    $moduleInstance->addFilter($filter1);
    $moduleInstance->setDefaultSharing();
    // Webservice Setup
    $moduleInstance->initWebservice();
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";