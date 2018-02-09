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
include_once('modules/ModTracker/ModTracker.php');
include_once('modules/ModComments/ModComments.php');
try {
    $module = Vtiger_Module::getInstance('Estimates');
    if (!$db) {
        $db = PearDatabase::getInstance();
    }
    if ($module) {
        $block1 = Vtiger_Block::getInstance('LBL_QUOTES_CONTACTDETAILS', $module);
        //Add our UI type 10 to Accounts

        if ($block1) {
            $field = Vtiger_Field::getInstance('billing_apn', $module);
            if ($field) {
                echo "The billing_apn field already exists<br>";
            } else {
                $field = new Vtiger_Field();
                $field->label = 'LBL_QUOTES_BILLING_APN';
                $field->name = 'billing_apn';
                $field->table = 'vtiger_quotes';
                $field->column = 'billing_apn';
                $field->columntype = 'VARCHAR(50)';
                $field->uitype = 10;
                $field->typeofdata = 'V~O';
                $field->displaytype = 1;
                $block1->addField($field);
                $field->setRelatedModules(array('Accounts'));
                echo "The billing_apn field created<br>";
            }
            $billing_apn = Vtiger_Field::getInstance('billing_apn', $module);
            $bill_street= Vtiger_Field::getInstance('bill_street', $module);
            if ($billing_apn&&$bill_street) {
                echo "<br/>Updating sequence<br/>";
                try {
                    $apn_seq = $billing_apn->sequence;
                    $street_seq = $bill_street->sequence;
                    if ($apn_seq > $street_seq) {
                        $bill_city = Vtiger_Field::getInstance('bill_city', $module);
                        $bill_state = Vtiger_Field::getInstance('bill_state', $module);
                        $bill_code = Vtiger_Field::getInstance('bill_code', $module);
                        $bill_pobox = Vtiger_Field::getInstance('bill_pobox', $module);
                        $bill_country = Vtiger_Field::getInstance('bill_country', $module);

                        $city_seq = $bill_city->sequence;
                        $state_seq = $bill_state->sequence;
                        $code_seq = $bill_code->sequence;
                        $pobox_seq = $bill_pobox->sequence;
                        $country_seq = $bill_country->sequence;

                        $db->pquery('UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?', [$street_seq, $billing_apn->id]);
                        $db->pquery('UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?', [$city_seq, $bill_street->id]);
                        $db->pquery('UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?', [$state_seq, $bill_city->id]);
                        $db->pquery('UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?', [$code_seq, $bill_state->id]);
                        $db->pquery('UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?', [$pobox_seq, $bill_code->id]);
                        $db->pquery('UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?', [$country_seq, $bill_pobox->id]);
                        $db->pquery('UPDATE vtiger_field SET sequence = ? WHERE fieldid = ?', [$apn_seq, $bill_country->id]);

                        echo "Updating sequence passed <br/>";
                    } else {
                        echo "The order is correct<br/>";
                    }
                } catch (Exception $e) {
                    echo "Updating sequence failed ";
                }
            } else {
                echo "Cannot update the sequence<br/>";
            }
        } else {
            echo "The Billing APN Block does not exist!<br/>";
        }
    } else {
        echo "The Estimates Module does not exist!<br/>";
    }
} catch (Exception $e) {
    echo "Failed to add the Billing APN field to Estimates!<br/>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";