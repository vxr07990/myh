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



$module = Vtiger_Module::getInstance('Contracts');
if ($module) {
    echo "<h2>Updating Opportunities Fields</h2><br>";
    $block = Vtiger_Block::getInstance('LBL_CONTRACTS_BILLING', $module);
    if ($block) {
        echo "<br><h1>Reordering fields in the LBL_CONTRACTS_BILLING block</h1><br>";
        $fieldSeq = [
            'billing_contact'  => 1,
            'billing_address1' => 2,
            'billing_address2' => 3,
            'billing_city'     => 4,
            'billing_state'    => 5,
            'billing_zip'      => 6,
            'billing_pobox'    => 7,
            'billing_country'  => 8,
        ];
        $push_to_end = [];
        foreach ($fieldSeq as $name => $seq) {
            $field = Vtiger_Field::getInstance($name, $module);
            if ($field) {
                $sql    = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
                $result = $db->pquery($sql, [$seq, $block->id]);
                if ($result) {
                    while ($row = $result->fetchRow()) {
                        $push_to_end[] = $row[0];
                    }
                }
                Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.
                                           '" AND fieldid = '.$field->id);
                print 'UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.'" AND fieldid = '
                      .$field->id."<br />\n";
            }
            unset($field);
        }
        //@TODO: something is weird here I would expect it to use the sequnce from above, but it doesn't unless I run twice.
        //have to check when I've some time.
        //push anything that might have gotten added and isn't on the list to the end of the block
        $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0] + 1;
        foreach ($push_to_end as $name) {
            //foreach(reverse_array($push_to_end) as $name){
            //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
            if (!array_key_exists($name, $fieldSeq)) {
                $field = Vtiger_Field::getInstance($name, $module);
                if ($field) {
                    Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max.' WHERE fieldname= "'.$name.
                                               '" AND fieldid = '.$field->id);
                    $max++;
                }
            }
        }
    } else {
        echo "<h1>NO Contracts Information block</h1>";
    }
} else {
    echo "<h1>NO Contracts</h1>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";