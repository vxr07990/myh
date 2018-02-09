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


/**
 * Created by Ian Overholt.
 * User: Ian Overholt
 * Date: 5/17/2016
 * Time: 2:13 PM
 */
$db = PearDatabase::getInstance();

$oppsModule = Vtiger_Module::getInstance('Opportunities');

$update_fields_array = array(
    array(
        'fieldBlock'=>'LBL_POTENTIALS_ADDRESSDETAILS',
        'fields'=>array(
            array('field'=>'destination_country','typeofdata'=>'V~M'),
            array('field'=>'destination_city','typeofdata'=>'V~M'),
            array('field'=>'destination_state','typeofdata'=>'V~M'),
            array('field'=>'destination_zip','typeofdata'=>'V~M'),
            array('field'=>'destination_address1','typeofdata'=>'V~M'),
        ),
    ),
);
if (!$oppsModule) {
    //if opp module not loading check if Potentials is there
    $oppsModule = Vtiger_Module::getInstance('Potentials');
}

if ($oppsModule) {
    foreach ($update_fields_array as $field_block) {
        $block = Vtiger_Block::getInstance($field_block['fieldBlock'], $oppsModule);
        if ($block) {
            foreach ($field_block['fields'] as $fields_row) {
                $field = Vtiger_Field::getInstance($fields_row['field'], $oppsModule);
                if ($field) {
                    try {
                        $fieldId = $field->id;
                        if ($field->typeofdata!=$fields_row['typeofdata']) {
                            //ignore if the fields already match
                            $sql = "UPDATE `vtiger_field` SET typeofdata = ? WHERE fieldid = ?";
                            $db->pquery($sql, [$fields_row['typeofdata'], $fieldId]);
                            echo "<br/>Updated the following field: ".$fields_row['field'];
                        }
                    } catch (Exception $e) {
                        echo "<br/><h1>".$e->getMessage()."</h1>";
                    }
                } else {
                    echo "<br/><h1>The field does not exist!</h1>";
                }
            }
        } else {
            echo "<br/><h1>The ".$field_block['fieldBlock']." block does not exist</h1>";
        }
    }
} else {
    echo "<br/><h1>The Module could not be found please try again later</h1>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";