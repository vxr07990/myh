<?php

class Settings_LayoutEditor_ExportScripts_Action extends Settings_Vtiger_Index_Action
{
    protected $outputString;
    protected $newBlockIds;
    protected $translations;

    public function __construct()
    {
        parent::__construct();
        $this->outputString = "";
        $this->newBlockIds = [];
        $this->translations = "Language Strings\n";
        $this->exposeMethod('downloadScript');
        $this->exposeMethod('checkTables');
        $this->exposeMethod('downloadReorderScript');
    }

    public function process(Vtiger_Request $request)
    {
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    public function checkTables(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT DISTINCT modulename FROM `newfields`";
        $result = $db->pquery($sql, []);
        $response = new Vtiger_Response();
        $response->setResult($result != null);
        $response->emit();
    }

    public function downloadScript(Vtiger_Request $request)
    {
        $blockOrder = $request->get('blockOrder');
        $fieldTable = "newfields";
        $blockTable = "newblocks";
        $modules = [];

        //Determine which modules need to be processed
        $db = PearDatabase::getInstance();
        $sql = "SELECT DISTINCT modulename FROM `$fieldTable`";
        $result = $db->pquery($sql, []);
        if ($result == null) {
            return;
        }
        while ($row =& $result->fetchRow()) {
            $modules[] = $row['modulename'];
        }
        $sql = "SELECT DISTINCT modulename FROM `$blockTable`";
        $result = $db->pquery($sql, []);
        while ($row =& $result->fetchRow()) {
            if (!in_array($row['modulename'], $modules)) {
                $modules[] = $row['modulename'];
            }
        }

        if (in_array('Potentials', $modules) && !in_array('Opportunities', $modules)) {
            $modules[] = 'Opportunities';
        } elseif (in_array('Opportunities', $modules) && !in_array('Potentials', $modules)) {
            $modules[] = 'Potentials';
        }

        if (in_array('Quotes', $modules) && !in_array('Estimates', $modules)) {
            $modules[] = 'Estimates';
        } elseif (in_array('Estimates', $modules) && !in_array('Quotes', $modules)) {
            $modules[] = 'Quotes';
        }

        //Begin script output
        $this->outputString .= '<?php

//*/
include_once(\'vtlib/Vtiger/Menu.php\');
include_once(\'vtlib/Vtiger/Module.php\');
include_once(\'modules/ModTracker/ModTracker.php\');
include_once(\'modules/ModComments/ModComments.php\');
//*/

';

        //Process modules and get a Vtiger_Module instance of each
        foreach ($modules as $moduleName) {
            $this->outputString .= "\$module$moduleName = Vtiger_Module::getInstance('$moduleName');\n";
        }

        //Get a listing of new blocks
        $sql = "SELECT $blockTable.blockid, blocklabel, label_translation, modulename, sequence FROM `$blockTable` JOIN `vtiger_blocks` ON $blockTable.blockid=vtiger_blocks.blockid";
        $result = $db->pquery($sql, []);

        //Process new blocks and add them to module
        while ($row =& $result->fetchRow()) {
            $moduleName = $row['modulename'];
            $this->addBlockToOutput($db, $row);
            if ($moduleName == 'Opportunities') {
                $row['modulename'] = 'Potentials';
                $this->addBlockToOutput($db, $row);
            } elseif ($moduleName == 'Potentials') {
                $row['modulename'] = 'Opportunities';
                $this->addBlockToOutput($db, $row);
            } elseif ($moduleName == 'Estimates') {
                $row['modulename'] = 'Quotes';
                $this->addBlockToOutput($db, $row);
            } elseif ($moduleName == 'Quotes') {
                $row['modulename'] = 'Estimates';
                $this->addBlockToOutput($db, $row);
            }
        }

        //Get listing of existing blocks that are needed
        $sql = "SELECT DISTINCT vtiger_blocks.blockid, modulename, blocklabel FROM `$fieldTable` JOIN `vtiger_blocks` ON $fieldTable.blockid=vtiger_blocks.blockid";
        $result = $db->pquery($sql, []);
        while ($row =& $result->fetchRow()) {
            if (in_array($row['blockid'], $this->newBlockIds)) {
                continue;
            }
            $moduleName = $row['modulename'];
            $this->addBlockToOutput($db, $row, false);
            if ($moduleName == 'Opportunities') {
                $row['modulename'] = 'Potentials';
                $this->addBlockToOutput($db, $row, false);
            } elseif ($moduleName == 'Potentials') {
                $row['modulename'] = 'Opportunities';
                $this->addBlockToOutput($db, $row, false);
            } elseif ($moduleName == 'Estimates') {
                $row['modulename'] = 'Quotes';
                $this->addBlockToOutput($db, $row, false);
            } elseif ($moduleName == 'Quotes') {
                $row['modulename'] = 'Estimates';
                $this->addBlockToOutput($db, $row);
            }
        }

        //Begin field processing
        //Get listing of new fields
        $sql = "SELECT vtiger_field.fieldid, label_translation, blockid, modulename, fieldname, tablename, uitype, fieldlabel, sequence, displaytype, typeofdata, quickcreate, summaryfield FROM `$fieldTable` JOIN `vtiger_field` ON $fieldTable.fieldid=vtiger_field.fieldid";
        $result = $db->pquery($sql, []);

        //Process new fields and add them to correct blocks
        while ($row =& $result->fetchRow()) {
            $moduleName = $row['modulename'];
            $this->addFieldToOutput($db, $row);
            if ($moduleName == 'Opportunities') {
                $row['modulename'] = 'Potentials';
                $this->addFieldToOutput($db, $row);
            } elseif ($moduleName == 'Potentials') {
                $row['modulename'] = 'Opportunities';
                $this->addFieldToOutput($db, $row);
            } elseif ($moduleName == 'Estimates') {
                $row['modulename'] = 'Quotes';
                $this->addFieldToOutput($db, $row);
            } elseif ($moduleName == 'Quotes') {
                $row['modulename'] = 'Estimates';
                $this->addFieldToOutput($db, $row);
            }
        }
        $db->pquery("DROP TABLE IF EXISTS $blockTable", []);
        $db->pquery("DROP TABLE IF EXISTS $fieldTable", []);
        $this->outputString .= "\n/*".$this->translations."\n*/";
        file_put_contents('logs/metaOutput.php', $this->outputString);
        $filename = "GeneratedScript_".date('Ymd_His').".php";
        header('Content-Type: text/php');
        header('Content-Disposition: attachment; filename='.$filename);
        $out = fopen('php://output', 'w');
        fwrite($out, $this->outputString);
        fclose($out);
    }

    protected function getColumnType($db, $fieldname, $tablename)
    {
        $dbName = getenv('DB_NAME');
        $sql = "select column_type from information_schema.columns
                where table_schema = ?
                and table_name = ?
                and column_name = ?";
        $result = $db->pquery($sql, [$dbName, $tablename, $fieldname]);
        if (!isset($result)) {
            return '';
        }
        $row = $result->fetchRow();
        if ($row == null) {
            return '';
        }

        return $row['column_type'];
    }

    protected function addBlockToOutput($db, $row, $newBlock=true)
    {
        $this->newBlockIds[] = $row['blockid'];
        $moduleName = $row['modulename'];
        $moduleInstance = '$module'.$moduleName;
        $blockInstance = '$block'.$moduleName.$row['blockid'];
        $blockLabel = $row['blocklabel'];
        if ($blockLabel == 'LBL_OPPORTUNITY_INFORMATION' && $moduleName == 'Opportunities') {
            $blockLabel = 'LBL_POTENTIALS_INFORMATION';
        } elseif ($blockLabel == 'LBL_POTENTIALS_INFORMATION' && $moduleName == 'Potentials') {
            $blockLabel = 'LBL_OPPORTUNITY_INFORMATION';
        }
        if ($newBlock) {
            $translatedLabel = $row['label_translation'];
            $this->translations .= "
    '$blockLabel' => '$translatedLabel',";
        }
        $this->outputString .= "
$blockInstance = Vtiger_Block::getInstance('$blockLabel', $moduleInstance);
if($blockInstance) {
    echo \"<br> The $blockLabel block already exists in $moduleName <br>\";
}
else {
    $blockInstance = new Vtiger_Block();
    ".$blockInstance."->label = '$blockLabel';
    ".$moduleInstance."->addBlock($blockInstance);
}
";
    }

    protected function addFieldToOutput($db, $row)
    {
        $fieldModel = Vtiger_Field_Model::getInstance($row['fieldid']);
        $moduleName = $row['modulename'];
        $moduleInstance = '$module'.$moduleName;
        $blockInstance = '$block'.$moduleName.$row['blockid'];
        $fieldname = $row['fieldname'];
        $tablename = $row['tablename'];
        $columntype = $this->getColumnType($db, $fieldname, $tablename);
        $fieldlabel = $row['fieldlabel'];
        $translatedLabel = $row['label_translation'];
        $uitype = $row['uitype'];
        $afterAddField = "";
        if ($uitype == 10 || $uitype == 15 || $uitype == 16) {
            if ($uitype == 10) {
                $valuesArray = $fieldModel->getReferenceList();
                $afterAddField = "
    \$field->setRelatedModules([";
            } else {
                $valuesArray = $fieldModel->getPicklistValues();
                $afterAddField = "
    \$field->setPicklistValues([";
            }
            $first = true;
            foreach ($valuesArray as $value) {
                if (!$first) {
                    $afterAddField .= ", ";
                } else {
                    $first = false;
                }
                $afterAddField .= "'$value'";
            }
            $afterAddField .= "]);";
        }
        $displaytype = $row['displaytype'];
        $typeofdata = $row['typeofdata'];
        $quickcreate = $row['quickcreate'];
        $summaryfield = $row['summaryfield'];
        $this->outputString .= "
\$field = Vtiger_Field::getInstance('$fieldname', $moduleInstance);
if(\$field) {
    echo \"<br> The $fieldname field already exists in $moduleName <br>\";
} else {
    \$field = new Vtiger_Field();
    \$field->label = '$fieldlabel';
    \$field->name = '$fieldname';
    \$field->table = '$tablename';
    \$field->column ='$fieldname';
    \$field->columntype = '$columntype';
    \$field->uitype = $uitype;
    \$field->typeofdata = '$typeofdata';
    \$field->displaytype = $displaytype;
    \$field->quickcreate = $quickcreate;
    \$field->summaryfield = $summaryfield;

    ".$blockInstance."->addField(\$field);$afterAddField
}";
        $this->translations .= "
    '$fieldlabel' => '$translatedLabel',";
    }

    public function downloadReorderScript($request)
    {
        file_put_contents('logs/metaLog.log', date('Y-m-d H:i:s - ')."Entering downloadReorderScript\n", FILE_APPEND);
        file_put_contents('logs/metaLog.log', date('Y-m-d H:i:s - ')."Request: ".print_r($request, true)."\n", FILE_APPEND);
        file_put_contents('logs/metaLog.log', date('Y-m-d H:i:s - ')."UpdatedFields: ".print_r($request->get('updatedFields'), true)."\n", FILE_APPEND);
        $updatedFields = $request->get('updatedFields');
        file_put_contents('logs/metaLog.log', date('Y-m-d H:i:s - ').json_last_error_msg()."\n", FILE_APPEND);
        file_put_contents('logs/metaLog.log', date('Y-m-d H:i:s - ').print_r($updatedFields, true)."\n", FILE_APPEND);

        $sequenceCase = "";
        $blockCase = "";
        $fieldIds = [];
        //Begin script output
        $this->outputString .= '<?php

//*/
include_once(\'vtlib/Vtiger/Menu.php\');
include_once(\'vtlib/Vtiger/Module.php\');
include_once(\'modules/ModTracker/ModTracker.php\');
include_once(\'modules/ModComments/ModComments.php\');
//*/

';
        file_put_contents('logs/metaLog.log', date('Y-m-d H:i:s - ')."Before addModuleInstances\n", FILE_APPEND);
        $this->addModuleInstances($updatedFields);
        file_put_contents('logs/metaLog.log', date('Y-m-d H:i:s - ')."After addModuleInstances\n", FILE_APPEND);

        $neededBlocks = [];
        foreach ($updatedFields as $fieldArray) {
            if (!in_array($fieldArray['block'], $neededBlocks)) {
                $neededBlocks[] = $fieldArray['block'];
            }
        }
        $db = PearDatabase::getInstance();
        foreach ($neededBlocks as $block) {
            $sql = "SELECT blocklabel, name AS tabname FROM `vtiger_blocks` JOIN `vtiger_tab` ON vtiger_blocks.tabid=vtiger_tab.tabid WHERE blockid=?";
            $result = $db->pquery($sql, [$block]);
            $row = $result->fetchRow();
            $this->outputString .= "
\$block".$block." = Vtiger_Block::getInstance('".$row['blocklabel']."', \$module".$row['tabname'].");";
        }

        foreach ($updatedFields as $fieldArray) {
            $this->addFieldInstance($fieldArray);
            $fieldIds[] = $fieldArray['fieldid'];
            $sequenceCase .= 'WHEN fieldid=".$field'.$fieldArray['fieldid'].'->id." THEN '.$fieldArray['sequence'].' ';
            $blockCase .= 'WHEN fieldid=".$field'.$fieldArray['fieldid'].'->id." THEN ".$block'.$fieldArray['block'].'->id." ';
        }

        //Query contains first ? for parameters
        $query = "UPDATE `vtiger_field` SET sequence= CASE ".$sequenceCase."END, block=CASE ".$blockCase."END WHERE fieldid IN (";
        $query .= '".$field'.$fieldIds[0].'->id."';
        for ($i=1; $i<count($fieldIds); $i++) {
            $query .= ',".$field'.$fieldIds[$i].'->id."';
        }
        $query .= ')';

        file_put_contents('logs/metaLog.log', date('Y-m-d H:i:s - ')."Generated Query: \"".$query."\"\n", FILE_APPEND);

        $this->outputString .= "
Vtiger_Utils::ExecuteQuery(\"".$query."\");
    ";

        file_put_contents('logs/reorder.php', $this->outputString);
        file_put_contents('logs/metaLog.log', date('Y-m-d H:i:s - ')."Exiting downloadReorderScript\n", FILE_APPEND);
        $filename = $request->get('moduleName')."ReorderScript_".date('Ymd_His').".php";
        header('Content-Type: text/php');
        header('Content-Disposition: attachment; filename='.$filename);
        $out = fopen('php://output', 'w');
        fwrite($out, $this->outputString);
        fclose($out);
    }

    protected function addModuleInstances($updatedFields)
    {
        $db = PearDatabase::getInstance();
        $blocks = [];
        foreach ($updatedFields as $fieldArray) {
            if (!in_array($fieldArray['block'], $blocks)) {
                $blocks[] = $fieldArray['block'];
            }
        }

        $sql = "SELECT DISTINCT `vtiger_tab`.name AS tabname FROM `vtiger_blocks` JOIN `vtiger_tab` ON vtiger_blocks.tabid=vtiger_tab.tabid WHERE blockid IN (?";
        $params = [];
        $params[] = $blocks[0];
        for ($i=1; $i<count($blocks); $i++) {
            $sql .= ",?";
            $params[] = $blocks[$i];
        }
        $sql .= ")";
        $result = $db->pquery($sql, $params);
        while ($row =& $result->fetchRow()) {
            $moduleName = $row['tabname'];
            $this->outputString .= "\$module$moduleName = Vtiger_Module::getInstance('$moduleName');\n";
        }
    }

    protected function addFieldInstance($fieldArray)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT fieldname, vtiger_tab.name AS tabname FROM `vtiger_field` JOIN `vtiger_tab` ON vtiger_field.tabid=vtiger_tab.tabid WHERE fieldid=?";
        $result = $db->pquery($sql, [$fieldArray['fieldid']]);
        $row = $result->fetchRow();
        if ($row == null) {
            return;
        }
        $fieldname = $row['fieldname'];
        $moduleName = $row['tabname'];
        $moduleInstance = '$module'.$moduleName;
        $this->outputString .= "
\$field".$fieldArray['fieldid']." = Vtiger_Field::getInstance('$fieldname', $moduleInstance);";
    }
}
