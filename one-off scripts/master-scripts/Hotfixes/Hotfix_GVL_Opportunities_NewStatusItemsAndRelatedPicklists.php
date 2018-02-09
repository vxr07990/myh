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


//OT 2969, adding additional items to Status picklist and adding additional picklists for selected choices

$moduleName = 'Opportunities';
$blockName = 'LBL_POTENTIALS_INFORMATION';
$module = Vtiger_Module::getInstance($moduleName);

$salesStagePickList = [
    'Qualified Prospect',
    'Developing Proposal',
    'Submitted Proposal',
    'Best and Final',
    'Closed Won',
    'Closed Trading',
    'Closed Lost',
    'Closed Abandoned',
    'New',
    'Attempted Contact',
    'Survey Scheduled',
    'Pending',
    'Booked',
    'Inactive',
    'Lost',
    'Duplicate'
];

$reasonPickList = [
    'Move date has passed',
    'Capacity/Scheduling',
    'Pricing',
    'No longer moving',
    'Moving themselves',
    'No contact',
    'Past experience',
    'National account move',
    'Incomplete customer info',
    'Out of time',
    'Appointment cancelled',
    'Not serviceable',
    'Move too small',
    'Other'
];

$vanlinePickList = [
    'Allied',
    'Atlas',
    'Mayflower',
    'North American',
    'United',
    'Independent'
];

echo "<br>Starting NewStatusItemsAndRelatedPicklists<br>\n";

$block = Vtiger_Block::getInstance($blockName, $module);
if ($block) {
    $field0 = Vtiger_Field::getInstance('sales_stage', $module);
    if ($field0) {
        echo '<p>sales_stage field exists</p>';
        updatePicklistValuesNSARP($field0, $salesStagePickList);
    } else {
        $db = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_sales_stage`";
        $db->pquery($sql, array());
        $field0 = new Vtiger_Field();
        $field0->label = 'LBL_SALES_STAGE';
        $field0->name = 'sales_stage';
        $field0->table = 'vtiger_potential';
        $field0->column = 'sales_stage';
        $field0->columntype = 'VARCHAR(200)';
        $field0->uitype = '16';
        $field0->typeofdata = 'V~M';
        $block->addField($field0);
        $field0->setPicklistValues($salesStagePickList);
        echo '<p>Added Opportunities Special Status field</p>';
    }

    $field1 = Vtiger_Field::getInstance('opportunities_reason', $module);
    if ($field1) {
        echo '<p>reason field exists</p>';
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata='V~O' WHERE fieldname='opportunities_reason' AND typeofdata='V~M'");
    } else {
        $db = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_opportunities_reason`";
        $db->pquery($sql, array());
        $field1 = new Vtiger_Field();
        $field1->label = 'LBL_OPPORTUNITIES_REASON';
        $field1->name = 'opportunities_reason';
        $field1->table = 'vtiger_potential';
        $field1->column = 'opportunities_reason';
        $field1->columntype = 'VARCHAR(200)';
        $field1->uitype = '16';
        $field1->typeofdata = 'V~O';
        $block->addField($field1);
        $field1->setPicklistValues($reasonPickList);
        echo '<p>Added Opportunities reason field</p>';
    }

    $field2 = Vtiger_Field::getInstance('opportunities_vanline', $module);
    if ($field2) {
        echo '<p>opportunities_vanline field exists</p>';
        Vtiger_Utils::ExecuteQuery("UPDATE `vtiger_field` SET typeofdata='V~O' WHERE fieldname='opportunities_vanline' AND typeofdata='V~M'");
    } else {
        $db = PearDatabase::getInstance();
        $sql = "TRUNCATE TABLE `vtiger_opportunites_vanline`";
        $db->pquery($sql, array());
        $field2 = new Vtiger_Field();
        $field2->label = 'LBL_OPPORTUNITIES_VANLINE';
        $field2->name = 'opportunities_vanline';
        $field2->table = 'vtiger_potential';
        $field2->column = 'opportunities_vanline';
        $field2->columntype = 'VARCHAR(200)';
        $field2->uitype = '16';
        $field2->typeofdata = 'V~O';
        $block->addField($field2);
        $field2->setPicklistValues($vanlinePickList);
        echo '<p>Added Opportunities vanline field</p>';
    }

    echo "<p>Reordering fields in opportunities information</p>\n";
    $fieldOrder = [
            'potentialname',      'billing_type',
            'business_line',        'sales_stage',
            'opportunities_reason',     'opportunities_vanline',
            'contact_id',       'related_to',
            'leadsource',       'closingdate',
            'agentid',      'assigned_user_id'
        ];

    $db = PearDatabase::getInstance();

    foreach ($fieldOrder as $key => $field) {
        $fieldInstance = Vtiger_Field::getInstance($field, $module);

        $sql = 'UPDATE `vtiger_field` SET sequence = ? WHERE fieldid = ?';
        $db->pquery($sql, [$key+1, $fieldInstance->id]);
    }
    echo "<p>Done reordering fields in opportunities information</p>\n";

    echo "<p>Setting picklist dependencies</p>";
    $sourceField = 'billing_type';
    $targetField = 'sales_stage';
    setPicklistDependenciesNSARP($module, $sourceField, $targetField);
} else {
    echo "<br>Fields not added. $blockName not found.<br/>";
}


function updatePicklistValuesNSARP($field, $picklist)
{
    $fieldName = $field->name;
    $tableName = 'vtiger_'.$fieldName;
    $keyField = $fieldName.'_id';
    $db = PearDatabase::getInstance();
    $id = 24; //specific to sales_stage picklist
    foreach ($picklist as $index => $value) {
        $presenceValue = 1;
        $selectSql = 'SELECT * FROM `'.$tableName.'` WHERE '
                     . ' `'.$fieldName.'` = ? '
                     . ' LIMIT 1';
        $selectResult = $db->pquery($selectSql, array($value));

        if ($selectResult && $selectRow = $selectResult->fetchRow()) {
            $updateSql = 'UPDATE `'.$tableName.'` SET '
                         . ' `sortorderid` = ?'
                         . ' WHERE `'.$fieldName.'` = ?'
                         . ' LIMIT 1';
            $db->pquery($updateSql, array($index+1, $value));
        } else {
            //Weird presence values on existing sales_stage picklist items. Needed for some reason.
            //Should be safe to remove this conditional anywhere else
            if ($value == 'Closed Won' || $value == 'Closed Lost') {
                $presenceValue = 0;
            } elseif ($value == 'Qualified Prospect' || $value == 'Closed Trading' || $value == 'Closed Abandoned') {
                $presenceValue = 8;
            }
            $insertSql = 'INSERT INTO `'.$tableName.'` SET 
                    `presence` = ?,
                    `'.$keyField.'` = ?,
                    `'.$fieldName.'` = ?,
                    `sortorderid` = ?';
            $db->pquery($insertSql, array($presenceValue, $id, $value, $index+1));
            $id++;
        }
    }
}

function setPicklistDependenciesNSARP($module, $sourceField, $targetField)
{
    //Note: These are strings, not arrays.
    $targetVal1 = '["Developing Proposal","Best and Final","Submitted Proposal","Closed Won","Closed Lost","Qualified Prospect","Closed Trading","Closed Abandoned"]';
    $targetVal2 = '["New","Attempted Contact","Survey Scheduled","Pending","Booked","Inactive","Lost","Duplicate"]';
    $db = PearDatabase::getInstance();
    $selectSql = 'SELECT * FROM `vtiger_picklist_dependency` WHERE '
                 .' `tabid` = ? AND '
                .' `sourcefield`= ?';
    $selectResult = $db->pquery($selectSql, array($module->id, $sourceField));
    //Only going to update this thing if the rows aren't there already.
    if ($selectResult->numRows() > 0) {
        echo "<p>Dependencies already exist. Not modifying. </p>\n";
    } else {
        //Gotta figure out the minimum ID I'm allowed.
        $idCheckSql = 'SELECT MAX(`id`) AS `maxID` FROM `vtiger_picklist_dependency` LIMIT 1';
        $idCheckResult = $db->pquery($idCheckSql);
        if ($idCheckResult && $idCheckRow = $idCheckResult->fetchRow()) {
            $minID = $idCheckRow['maxID'] + 1;
        } else {
            $minID = 1;
        }

        $dependencyID = $minID;
        $sourceFieldSql = 'SELECT `'.$sourceField.'` FROM `vtiger_'.$sourceField.'`';
        $sourceChoices = $db->pquery($sourceFieldSql);
        while ($choiceRow = $sourceChoices->fetchRow()) {
            if ($choiceRow[$sourceField] == 'Consumer/COD') {
                $targetList = $targetVal2;
            } else {
                $targetList = $targetVal1;
            }
            $insertSql = 'INSERT INTO `vtiger_picklist_dependency` SET
                `id` = ?,
                `tabid` = ?,
                `sourcefield` = ?,
                `targetfield` = ?,
                `sourcevalue` = ?,
                `targetvalues` = ?,
                `criteria` = ?';
            $db->pquery($insertSql, array($dependencyID++, $module->id, $sourceField, $targetField, $choiceRow[$sourceField], $targetList, null));
        }
        //One more insert to add a null value

        $insertSql = 'INSERT INTO `vtiger_picklist_dependency` SET
                `id` = ?,
                `tabid` = ?,
                `sourcefield` = ?,
                `targetfield` = ?,
                `sourcevalue` = ?,
                `targetvalues` = ?,
                `criteria` = ?';
        $db->pquery($insertSql, array($dependencyID, $module->id, $sourceField, $targetField, '', '[]', null));
    }
}

echo "<br>Finished NewStatusItemsAndRelatedPicklists<br>\n";


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";