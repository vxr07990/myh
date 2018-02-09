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
 *Goals:
 * Updates to Documents to have a Document Type field of:
Rate Sheet
Origin Bill of Lading
Destination Bill of Lading
Estimate of Charges
Weight Tickets
Origin Accessorial Forms
Destination Accessorial Forms
3rd Party Invoices
Pack Per Inventory Count
Table of Measurements
Billing Release Form
Tariff Pages
Government Voucher Form
Government Origin Bill of Lading Form
Government Destination Bill of Lading Form​
DD619 Accessorial Form
DD619-1 Origin Accessorial Form 
DD619-1 Destination Accessorial Form 
833 Form 
833A Form

 *
 */
include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$blockName = 'LBL_FILE_INFORMATION';
foreach (['Documents'] as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
    } else {
        $block = Vtiger_Block::getInstance($blockName, $module);
        if ($block) {
            //add these fields:
            $addFields = [
                'invoice_document_type' => [
                    'label'           => 'LBL_DOCUMENTS_INVOICE_DOCUMENT_TYPE',
                    'name'            => 'invoice_document_type',
                    'table'           => 'vtiger_notes',
                    'column'          => 'invoice_document_type',
                    'columntype'      => 'varchar(100)',
                    'uitype'          => 16,
                    'typeofdata'      => 'V~O',
                    'displaytype'     => '1',
                    'block'           => $block,
                    'replaceExisting' => false,
                    'picklist'        => [
                        'Rate Sheet',
                        'Origin Bill of Lading',
                        'Destination Bill of Lading',
                        'Estimate of Charges',
                        'Weight Tickets',
                        'Origin Accessorial Forms',
                        'Destination Accessorial Forms',
                        '3rd Party Invoices',
                        'Pack Per Inventory Count',
                        'Table of Measurements',
                        'Billing Release Form',
                        'Tariff Pages',
                        'Government Voucher Form',
                        'Government Origin Bill of Lading Form',
                        'Government Destination Bill of Lading Form​',
                        'DD619 Accessorial Form',
                        'DD619-1 Origin Accessorial Form ',
                        'DD619-1 Destination Accessorial Form ',
                        '833 Form ',
                        '833A Form'
                    ],
                ],
            ];
            addDocumentTypeField($addFields, $module);
        }
        print "<h2>finished add fields to $moduleName module. </h2>\n";
    }
}

function addDocumentTypeField($fields, $module)
{
    $returnFields = [];
    foreach ($fields as $field_name => $data) {
        $field0 = Vtiger_Field::getInstance($field_name, $module);
        if (!$field0) {
            echo "<li> Attempting to add $field_name</li><br />";
            //@TODO: check data validity
            $field0 = new Vtiger_Field();
            //these are assumed to be filled.
            $field0->label        = $data['label'];
            $field0->name         = $data['name'];
            $field0->table        = $data['table'];
            $field0->column       = $data['column'];
            $field0->columntype   = $data['columntype'];
            $field0->uitype       = $data['uitype'];
            $field0->typeofdata   = $data['typeofdata'];
            $field0->summaryfield = ($data['summaryfield']?1:0);
            $field0->defaultvalue = $data['defaultvalue'];
            //these three MUST have values or it doesn't pop vtiger_field.
            $field0->displaytype = ($data['displaytype']?$data['displaytype']:1);
            $field0->readonly    = ($data['readonly']?$data['readonly']:1);
            $field0->presence    = ($data['presence']?$data['presence']:2);
            $data['block']->addField($field0);

            if (
                array_key_exists('picklist', $data) &&
                $data['picklist'] &&
                count($data['picklist']) > 0
            ) {
                $field0->setPicklistValues($data['picklist']);
            }
            $returnFields[$field_name] = $field0;
        }
    }

    return $returnFields;
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";