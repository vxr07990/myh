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

$moduleName = 'Leads';
$blockName = 'LBL_LEADS_BLOCK_LMPDETAILS';
print "<h2>START add local carrier to $moduleName module. </h2>\n";
$module = Vtiger_Module::getInstance($moduleName);

if (!$module) {
    echo "<h2>FAILED TO LOAD Module: $moduleName </h2><br />";
} else {
    $block = Vtiger_Block::getInstance($blockName, $module);
    if (!$block) {
        echo "<li>BLOCK DOES NOT EXIST!</li><br>";
    } else {
        $field10 = Vtiger_Field::getInstance('source_name', $module);
        $fields = [
            'source_name' => [
                'label'             => 'LBL_LEADS_SOURCENAME',
                'name'              => 'source_name',
                'table'             => 'vtiger_leaddetails',
                'column'            => 'source_name',
                'columntype'        => 'VARCHAR(50)',
                'uitype'            => 10,
                'typeofdata'        => 'V~O',
                'summaryfield'      => 0,
                'block'             => $block,
                'setRelatedModules' => ['LeadSourceManager'],
                'replaceExisting'    => true,
            ]
        ];
        $rField = addFields_SLALSM($fields, $module, true);
    }
}
print "<h2>END add new Lead Source Manager to Estimates module. </h2>\n";

function addFields_SLALSM($fields, $module)
{
    $returnFields = [];
    foreach ($fields as $field_name => $data) {
        $field0 = Vtiger_Field::getInstance($field_name, $module);
        if ($field0) {
            echo "<li>The $field_name field already exists</li><br>";
            $returnFields[$field_name] = $field0;
            if ($data['replaceExisting']) {
                $db = PearDatabase::getInstance();
                $stmt = 'UPDATE `vtiger_field`
                        SET `uitype` = ?
                        WHERE `fieldid` = ? LIMIT 1';
                $db->pquery($stmt, [$data['uitype'], $field0->id]);
                if (
                    array_key_exists('setRelatedModules', $data) &&
                    $data['setRelatedModules'] &&
                    count($data['setRelatedModules']) > 0
                ) {
                    echo "<li> setting relation to existing $field_name</li>";
                    $field0->setRelatedModules($data['setRelatedModules']);
                }
            }
        } else {
            echo "<li> Attempting to add $field_name</li><br />";
            //@TODO: check data validity
            $field0               = new Vtiger_Field();
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
            $field0->displaytype  = ($data['displaytype']?$data['displaytype']:1);
            $field0->readonly     = ($data['readonly']?$data['readonly']:1);
            $field0->presence     = ($data['presence']?$data['presence']:2);

            $data['block']->addField($field0);
            if ($data['setEntityIdentifier'] == 1) {
                $module->setEntityIdentifier($field0);
            }
            //just completely ensure there's stuff in the array before doing it.
            if (
                array_key_exists('setRelatedModules', $data) &&
                $data['setRelatedModules'] &&
                count($data['setRelatedModules']) > 0
            ) {
                $field0->setRelatedModules($data['setRelatedModules']);
            }
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