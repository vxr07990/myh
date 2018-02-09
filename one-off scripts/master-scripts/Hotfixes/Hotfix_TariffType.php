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



// $Vtiger_Utils_Log = true;

// include_once('vtlib/Vtiger/Menu.php');
// include_once('vtlib/Vtiger/Module.php');
// include_once('modules/ModTracker/ModTracker.php');
// include_once('modules/ModComments/ModComments.php');
// include_once 'includes/main/WebUI.php';
// include_once 'include/Webservices/Create.php';
// include_once 'modules/Users/Users.php';


$module = Vtiger_Module::getInstance('TariffManager');

$block = Vtiger_Block::getInstance('LBL_TARIFFMANAGER_ADMINISTRATIVE', $module);
if ($block) {
    echo "<br> Block 'LBL_TARIFFMANAGER_ADMINISTRATIVE' is already present <br>";
} else {
    $block = new Vtiger_Block();
    $block->label = 'LBL_TARIFFMANAGER_ADMINISTRATIVE';
    $module->addBlock($block);
}

$field1 = Vtiger_Field::getInstance('custom_tariff_type', $module);
if ($field1) {
    echo "<br> Field 'custom_tariff_type' is already present <br>";
} else {
    $field1 = new Vtiger_Field();
    $field1->label = 'LBL_TARIFFMANAGER_CUSTOMTARIFFTYPE';
    $field1->name = 'custom_tariff_type';
    $field1->table = 'vtiger_tariffmanager';
    $field1->column = 'custom_tariff_type';
    $field1->columntype = 'VARCHAR(255)';
    $field1->uitype = 16;
    $field1->typeofdata = 'V~M';

    $block->addField($field1);

    $field1->setPicklistValues([
        'TPG',
        'Allied Express',
        'TPG GRR',
        'ALLV-2A',
        'Pricelock',
        'Blue Express',
        'Pricelock GRR',
        'NAVL-12A',
        '400N Base',
        '400N/104G',
        'Local/Intra',
        'Max 3',
        'Max 4',
        'Intra - 400N',
        'Canada Gov\'t',
        'Canada Non-Govt',
        'UAS',
        'Base',
    ]);
}
$fieldSeq = [
    'rating_url' => 1,
    'createdtime' => 2,
    'modifiedtime' => 3,
    'smownerid' => 4,
    'custom_tariff_type' => 5,
    'custom_javascript' => 6,
];

reorderBlock($fieldSeq, $block, $module);

function reorderBlock($fieldSeq, $block, $module)
{
    $db = PearDatabase::getInstance();
    $push_to_end = [];
    foreach ($fieldSeq as $name=>$seq) {
        $field = Vtiger_Field::getInstance($name, $module);
        if ($field) {
            $sql = 'SELECT fieldname FROM `vtiger_field` WHERE sequence = ? AND block = ?';
            $result = $db->pquery($sql, [$seq, $block->id]);
            if ($result) {
                while ($row = $result->fetchRow()) {
                    $push_to_end[] = $row[0];
                }
            }
            Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$seq.' WHERE fieldname= "'.$name.'" AND fieldid = '.$field->id);
        }
        unset($field);
    }
    //push anything that might have gotten added and isn't on the list to the end of the block
    $max = $db->pquery('SELECT MAX(sequence) FROM `vtiger_field` WHERE block = ?', [$block->id])->fetchRow()[0]+1;
    foreach ($push_to_end as $name) {
        //only push stuff that isn't in our array of things to position to prevent moving things that were in the right order to start
        if (!array_key_exists($name, $fieldSeq)) {
            $field = Vtiger_Field::getInstance($name, $module);
            if ($field) {
                Vtiger_Utils::ExecuteQuery('UPDATE `vtiger_field` SET sequence = '.$max.' WHERE fieldname= "'.$name.'" AND fieldid = '.$field->id);
                $max++;
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";