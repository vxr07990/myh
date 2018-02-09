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


/*
 *
 * The goal is to make the end_date field non-mandatory
 * set parent_contract to display in the summaryfield
 * move parent_contract to position one of the list view.
 *
 */

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');


print "<h2>START add local carrier to Estimates module. </h2>\n";
$EstimatesModule = Vtiger_Module::getInstance('Estimates');
$QuotesModule = Vtiger_Module::getInstance('Quotes');

if (!$EstimatesModule || !$QuotesModule) {
    echo "<h2>FAILED TO LOAD Estimates</h2><br />";
} else {
    $estimateBlock = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $EstimatesModule);
    $quoteBlock = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $QuotesModule);
    if (!$quoteBlock || !$estimateBlock) {
        echo "<li>BLOCK DOES NOT EXIST!</li><br>";
    } else {
        $fields = [
            'local_carrier' => [
                'label'             => 'LBL_QUOTE_LOCALCARRIER',
                'name'              => 'local_carrier',
                'table'             => 'vtiger_quotes',
                'column'            => 'local_carrier',
                'columntype'        => 'VARCHAR(255)',
                'uitype'            => 10,
                'typeofdata'        => 'V~O',
                'summaryfield'      => 0,
                'block'             => $estimateBlock,
                'setRelatedModules' => ['LocalCarrier'],
            ]
        ];
        $rField = addFields_2($fields, $EstimatesModule);

        print "<h2>Begin modifications to Estimates module. </h2>\n";
        $fieldSeq = [
            'subject'            => 1,
            'potential_id'       => 2,
            'quote_no'           => 3,
            'quotestage'         => 4,
            'validtill'          => 5,
            'contact_id'         => 6,
            'account_id'         => 7,
            'assigned_user_id'   => 8,
            'createdtime'        => 9,
            'modifiedtime'       => 10,
            'business_line_est'  => 11,
            'is_primary'         => 12,
            'orders_id'          => 13,
            'pre_tax_total'      => 14,
            'modifiedby'         => 15,
            'conversion_rate'    => 16,
            'hdnDiscountAmount'  => 17,
            'hdnS_H_Amount'      => 18,
            'hdnSubTotal'        => 19,
            'txtAdjustment'      => 20,
            'hdnGrandTotal'      => 21,
            'hdnTaxType'         => 22,
            'hdnDiscountPercent' => 23,
            'currency_id'        => 24,
            'load_date'          => 25,
            'contract'           => 26,
            'parent_contract'    => 27,
            'nat_account_no'     => 28,
            'billing_type'       => 29,
            'agentid'            => 30,
            'shipper_type'       => 31,
            'move_type'          => 32,
            'local_carrier'      => '33',
            'lead_type'          => '34',
        ];
        print "Reording Block in Estimates<br />";

        $fields = [
            'local_carrier' => [
                'label'             => 'LBL_QUOTE_LOCALCARRIER',
                'name'              => 'local_carrier',
                'table'             => 'vtiger_quotes',
                'column'            => 'local_carrier',
                'columntype'        => 'VARCHAR(255)',
                'uitype'            => 10,
                'typeofdata'        => 'V~O',
                'summaryfield'      => 0,
                'block'             => $quoteBlock,
                'setRelatedModules' => ['LocalCarrier'],
            ]
        ];
        $rField = addFields_2($fields, $QuotesModule);
        reorderBlockEstimatesInformation_2($fieldSeq, 'LBL_QUOTE_INFORMATION', 'Estimates');
        $fieldSeq = [
            'quote_no'           => '3',
            'subject'            => '1',
            'potential_id'       => '2',
            'quotestage'         => '4',
            'validtill'          => '5',
            'contact_id'         => '6',
            'carrier'            => '8',
            'hdnSubTotal'        => '9',
            'assigned_user_id1'  => '11',
            'txtAdjustment'      => '20',
            'hdnGrandTotal'      => '14',
            'hdnTaxType'         => '14',
            'hdnDiscountPercent' => '14',
            'hdnDiscountAmount'  => '14',
            'hdnS_H_Amount'      => '14',
            'account_id'         => '16',
            'assigned_user_id'   => '17',
            'createdtime'        => '18',
            'modifiedtime'       => '19',
            'modifiedby'         => '22',
            'currency_id'        => '20',
            'conversion_rate'    => '21',
            'pre_tax_total'      => '23',
            'business_line_est'  => '24',
            'orders_id'          => '25',
            'is_primary'         => '26',
            'load_date'          => '27',
            'contract'           => '28',
            'parent_contract'    => 29,
            'nat_account_no'     => 30,
            'billing_type'       => '31',
            'agentid'            => '32',
            'shipper_type'       => '33',
            'move_type'          => '34',
            'local_carrier'      => '35',
            'lead_type'          => '36',
        ];
        print "Reording Block in Quotes<br />";
        reorderBlockEstimatesInformation_2($fieldSeq, 'LBL_QUOTE_INFORMATION', 'Quotes');
    }
}
print "<h2>END add local carrier to Estimates module. </h2>\n";

function reorderBlockEstimatesInformation_2($fieldSeq, $blockLabel, $moduleName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $block = Vtiger_Block::getInstance($blockLabel, $module);
        if ($block) {
            $push_to_end  = [];
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
                }
                unset($field);
            }
            //push anything that might have gotten added and isn't on the list to the end of the block
            $max =
                $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0] +
                1;
            foreach ($push_to_end as $name) {
                //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
                if (!array_key_exists($name, $fieldSeq)) {
                    $field = Vtiger_Field::getInstance($name, $module);
                    if ($field) {
                        Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max.' WHERE fieldname= "'.
                                                   $name.
                                                   '" AND fieldid = '.$field->id);
                        $max++;
                    }
                }
            }
        }
    }
}
function addFields_2($fields, $module)
{
    $returnFields = [];
    foreach ($fields as $field_name => $data) {
        $field0 = Vtiger_Field::getInstance($field_name, $module);
        if ($field0) {
            echo "<li>The $field_name field already exists</li><br>";
            $returnFields[$field_name] = $field0;
        } else {
            $field0               = new Vtiger_Field();
            $field0->label        = $data['label'];
            $field0->name         = $data['name'];
            $field0->table        = $data['table'];
            $field0->column       = $data['column'];
            $field0->columntype   = $data['columntype'];
            $field0->uitype       = $data['uitype'];
            $field0->typeofdata   = $data['typeofdata'];
            $field0->summaryfield = ($data['summaryfield'] ? 1 : 0);
            $field0->displaytype = ($data['displaytype'] ? $data['displaytype'] : 1);

            $data['block']->addField($field0);

            if ($data['setEntityIdentifier'] == 1) {
                $module->setEntityIdentifier($field0);
            }
            if (
                array_key_exists('setRelatedModules', $data) &&
                $data['setRelatedModules'] &&
                count($data['setRelatedModules']) > 0
            ) {
                $field0->setRelatedModules($data['setRelatedModules']);
            }
            $returnFields[$field_name] = $field0;
        }
    }
    return $returnFields;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";