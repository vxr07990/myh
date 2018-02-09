<?php

function initializeOutput(&$output)
{
    $output = '<?php
//*/
include_once(\'vtlib/Vtiger/Menu.php\');
include_once(\'vtlib/Vtiger/Module.php\');
include_once(\'modules/ModTracker/ModTracker.php\');
include_once(\'modules/ModComments/ModComments.php\');
//*/
';
}

function createModule(&$output, $moduleName)
{
    $output .= '
$module = Vtiger_Module::getInstance(\''.$moduleName.'\');
if(!$module) {
    $module = new Vtiger_Module();
	$module->name = \''.$moduleName.'\';
	$module->save();
	$module->initTables();
	ModTracker::enableTrackingForModule($module->id);
} else {
    echo "<br>'.$moduleName.' module already exists<br>";
}
';
}

function duplicateBlocks($db, &$output, $sourceModule)
{
    $sql = "SELECT `tabid` FROM `vtiger_tab` WHERE `name`=?";
    $result = $db->pquery($sql, [$sourceModule]);
    $row = $result->fetchRow();
    if ($row == null) {
        exit("Source Module ($sourceModule) not found in system. Exiting.");
    }
    $sourceModuleId = $row[0];

    $sql = "SELECT * FROM `vtiger_blocks` WHERE tabid=?";
    $result = $db->pquery($sql, [$sourceModuleId]);

    while ($row =& $result->fetchRow()) {
        $output .= '
$block'.$row['blockid'].' = Vtiger_Block::getInstance(\''.$row['blocklabel'].'\', $module);
if($block'.$row['blockid'].') {
    echo "<br> The '.$row['blocklabel'].' block already exists in ".$module->name." <br>";
}
else {
    $block'.$row['blockid'].' = new Vtiger_Block();
    $block'.$row['blockid'].'->label = \''.$row['blocklabel'].'\';
    $module->addBlock($block'.$row['blockid'].');
}
';
    }
}

function duplicateFields($db, &$output, $sourceModule, $tableMap)
{
    $ignorePicklistValues = ["hdntaxtype","email_flag"];
    $sql = "SELECT `tabid` FROM `vtiger_tab` WHERE `name`=?";
    $result = $db->pquery($sql, [$sourceModule]);
    $row = $result->fetchRow();
    if ($row == null) {
        exit("Source Module ($sourceModule) not found in system. Exiting.");
    }
    $sourceModuleId = $row[0];

    $sql = "SELECT fieldname FROM `vtiger_entityname` WHERE tabid=?";
    $result = $db->pquery($sql, [$sourceModuleId]);
    $row = $result->fetchRow();
    if ($row == null) {
        exit("Source Module ($sourceModule) has no entity identifier defined. Exiting.");
    }
    $entityFieldName = $row[0];

    $sql = "SELECT * FROM `vtiger_field` WHERE tabid=?";
    $result = $db->pquery($sql, [$sourceModuleId]);

    while ($row =& $result->fetchRow()) {
        $field = '$field'.$row['fieldid'];
        $name = $row['fieldname'];
        $column = $row['columnname'];
        $uitype = $row['uitype'];

        //Lookup columntype for $column
        $columnTypeSql = "SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ?";
        $columnTypeRes = $db->pquery($columnTypeSql, [$row['tablename'], $column]);
        $columnTypeRow = $columnTypeRes->fetchRow();
        if ($columnTypeRow == null) {
            exit("Error occurred finding column_type of $column. Exiting.");
        }
        $columnType = $columnTypeRow[0];

        //Specific functionality for uitypes
        switch ($uitype) {
            case 10: //Reference field
                //Get related module(s)
                $relatedModules = [];
                $fieldrelSql = "SELECT relmodule FROM `vtiger_fieldmodulerel` WHERE fieldid=?";
                $fieldrelRes = $db->pquery($fieldrelSql, [$row['fieldid']]);

                while ($fieldrelRow =& $fieldrelRes->fetchRow()) {
                    $relatedModules[] = $fieldrelRow['relmodule'];
                }

                //Build add-on string
                $uitypeAddon = '
    '.$field.'->setRelatedModules([';
                foreach ($relatedModules as $relatedModule) {
                    $uitypeAddon .= '\''.$relatedModule.'\',';
                }
                $uitypeAddon = rtrim($uitypeAddon, ',');
                $uitypeAddon .= ']);
';
                break;
            case 16: //Picklist field
                //Do nothing. Since we're duplicating fields, the picklist values already exist in the database.
            default:
                $uitypeAddon = '';
                break;
        }

        $output .= '
'.$field.' = Vtiger_Field::getInstance(\''.$name.'\', $module);
if('.$field.') {
    echo "<br> Field \''.$name.'\' is already present <br>";
} else {
    '.$field.' = new Vtiger_Field();
    '.$field.'->label = \''.$row['fieldlabel'].'\';
    '.$field.'->name = \''.$name.'\';
    '.$field.'->table = \''.(array_key_exists($row['tablename'], $tableMap) ? $tableMap[$row['tablename']] : $row['tablename']).'\';
    '.$field.'->column = \''.$column.'\';
    '.(array_key_exists($row['tablename'], $tableMap) ? '' : '//').$field.'->columntype = \''.$columnType.'\';
    '.$field.'->uitype = '.$uitype.';
    '.$field.'->typeofdata = \''.$row['typeofdata'].'\';
    '.$field.'->displaytype = '.$row['displaytype'].';
    '.$field.'->presence = '.$row['presence'].';
    '.$field.'->defaultvalue = \''.$row['defaultvalue'].'\';
    '.$field.'->quickcreate = '.$row['quickcreate'].';
    '.$field.'->summaryfield = '.$row['summaryfield'].';

    $block'.$row['block'].'->addField('.$field.');
'.$uitypeAddon.($name == $entityFieldName ? '
    $module->setEntityIdentifier('.$field.');
}
' : '}
');
    }
}

function duplicateFilters($db, &$output, $sourceModule)
{
    $fieldNames = [];
    $cvSetDefault = [];
    $sql = "SELECT viewname, columnname, setdefault FROM `vtiger_cvcolumnlist`
            JOIN `vtiger_customview` ON `vtiger_cvcolumnlist`.`cvid`=`vtiger_customview`.`cvid`
            WHERE entitytype=?";
    $result = $db->pquery($sql, [$sourceModule]);
    while ($row =& $result->fetchRow()) {
        $columnData = explode(':', $row['columnname']);
        $fieldNames[$row['viewname']][] = $columnData[2];
        $cvSetDefault[$row['viewname']] = $row['setdefault'];
    }

    $seq = 0;
    foreach ($fieldNames as $viewName => $fieldNameArray) {
        $seq++;
        $fields = [];
        foreach ($fieldNameArray as $fieldName) {
            $sql    = "SELECT fieldid FROM `vtiger_field`
            JOIN `vtiger_tab` ON `vtiger_field`.tabid=`vtiger_tab`.tabid
            WHERE fieldname=? AND name=?";
            $result = $db->pquery($sql, [$fieldName, $sourceModule]);
            $row    = $result->fetchRow();
            if ($row == null) {
                continue;
            }
            $fields[] = '$field'.$row[0];
        }
        $output .= '
$filter'.$seq.' = new Vtiger_Filter();
$filter'.$seq.'->name = \''.$viewName.'\';
$filter'.$seq.'->isdefault = '.($cvSetDefault[$viewName] == 1 ? 'true' : 'false').';
$module->addFilter($filter'.$seq.');

$filter'.$seq;
        $fieldSeq = 0;
        foreach ($fields as $field) {
            $output .= '->addField('.$field.($fieldSeq == 0 ? '' : ', '.$fieldSeq).')';
            $fieldSeq++;
        }
        $output .= ';
';
    }
}

function initSharingAndWebservices(&$output)
{
    $output .= '
$module->setDefaultSharing();
$module->initWebservice();
';
}

function duplicateRelatedLists($db, &$output, $sourceModule)
{
    $sql = "SELECT `tab2`.`name` as tabName, `vtiger_relatedlists`.`name` as name, `vtiger_relatedlists`.`label` as label, actions FROM `vtiger_relatedlists`
            JOIN `vtiger_tab` ON `vtiger_relatedlists`.`tabid`=`vtiger_tab`.`tabid`
            JOIN `vtiger_tab` AS tab2 ON `vtiger_relatedlists`.`related_tabid`=`tab2`.`tabid`
            WHERE `vtiger_tab`.`name`=?";
    $result = $db->pquery($sql, [$sourceModule]);
    while ($row =& $result->fetchRow()) {
        $actions = explode(',', $row['actions']);
        $output .= '
$module->setRelatedList(Vtiger_Module::getInstance(\''.$row['tabName'].'\'), \''.$row['label'].'\', [';
        foreach ($actions as $action) {
            $output .= '\''.$action.'\',';
        }
        $output = rtrim($output, ',');
        $output .= '], \''.$row['name'].'\');
';
    }
}

function downloadScript($output, $filename)
{
    if (empty($filename)) {
        $filename = "GeneratedScript_".date('Ymd_His').".php";
    }
    header('Content-Type: text/php');
    header('Content-Disposition: attachment; filename='.$filename);
    $out = fopen('php://output', 'w');
    fwrite($out, $output);
    fclose($out);
}
