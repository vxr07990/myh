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
 * Created by PhpStorm.
 * User: Ian Overholt
 * Date: 5/17/2016
 * Time: 2:47 PM
 */
$db = PearDatabase::getInstance();


$leads = Vtiger_Module::getInstance('Leads');


$update_fields_array = array(
    array(
        'fieldBlock'=>'LBL_LEADS_ADDRESSINFORMATION',
        'fields'=>array(
            array('field'=>'origin_zip','typeofdata'=>'V~M', 'uitype'=>'1'),
            array('field'=>'destination_zip','typeofdata'=>'V~M', 'uitype'=>'1'),
            array('field'=>'destination_country','typeofdata'=>'V~M', 'uitype'=>'SKIP'),
            array('field'=>'destination_city','typeofdata'=>'V~M', 'uitype'=>'SKIP'),
            array('field'=>'destination_state','typeofdata'=>'V~M', 'uitype'=>'SKIP'),
        ),
    ),
);

if ($leads) {
    foreach ($update_fields_array as $field_block) {
        $block = Vtiger_Block::getInstance($field_block['fieldBlock'], $leads);
        if ($block) {
            foreach ($field_block['fields'] as $fields_row) {
                $field = Vtiger_Field::getInstance($fields_row['field'], $leads);
                if ($field) {
                    try {
                        $fieldId = $field->id;
                        if ($fields_row['uitype'] != 'SKIP') {
                            //we only need to update the uitype of certain arrays
                            if ($field->typeofdata!=$fields_row['typeofdata']) {
                                //ignore if the fields already match
                                $sql = "UPDATE `vtiger_field` SET typeofdata = ?, uitype = ? WHERE fieldid = ?";
                                $db->pquery($sql, [$fields_row['typeofdata'], $fields_row['uitype'], $fieldId]);
                            }
                        } else {
                            if ($field->typeofdata!=$fields_row['typeofdata']) {
                                //ignore if the fields already match
                                $sql = "UPDATE `vtiger_field` SET typeofdata = ? WHERE fieldid = ?";
                                $db->pquery($sql, [$fields_row['typeofdata'], $fieldId]);
                            }
                        }
                        echo "<br/>Updated the following field: ".$fields_row['field'];
                    } catch (Exception $e) {
                        echo "<br/><h1>".$e->getMessage()."</h1>";
                    }
                } else {
                    echo "<br/><h1>The field does not exist!</h1>";
                }
            }
        } else {
            echo "<br/><h1>The ".$field_block['fieldBlock']." block does does not exist!</h1>";
        }
    }
} else {
    echo "<br/><h1>The  does does not exist!</h1>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";