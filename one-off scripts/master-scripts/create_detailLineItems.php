<?php
if (function_exists("call_ms_function_ver")) {
    $version = 4;
    if (call_ms_function_ver(__FILE__, $version)) {
        //already ran
        print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
        return;
    }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";


include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$db   = PearDatabase::getInstance();
//named slightly different in case I work out how to make in a module.
$tableName = 'vtiger_detailed_lineitems';
if (!Vtiger_Utils::CheckTable($tableName)) {
    $stmt = 'CREATE TABLE IF NOT EXISTS `'.$tableName.'` (
      `detaillineitemsid` int(11) NOT NULL AUTO_INCREMENT,
      `agentid` int(11) DEFAULT NULL,
      `assigned_user_id` int(19) DEFAULT NULL,
      `smownerid` int(19) DEFAULT NULL,
      `modifiedby` int(19) DEFAULT NULL,
      `modifiedtime` datetime DEFAULT NULL,
      `createdtime` datetime DEFAULT NULL,
      `dli_tariff_item_number` varchar(255) DEFAULT NULL,
      `dli_tariff_schedule_section` varchar(255) DEFAULT NULL,
      `dli_return_section_name` varchar(255) DEFAULT NULL,
      `dli_tariff_item_name` varchar(255) DEFAULT NULL,
      `dli_description` varchar(255) DEFAULT NULL,
      `dli_provider_role` varchar(55) DEFAULT NULL,
      `dli_base_rate` decimal(13,2) DEFAULT NULL,
      `dli_quantity` decimal(13,2) DEFAULT NULL,
      `dli_unit_of_measurement` varchar(55) DEFAULT NULL,
      `dli_unit_rate` decimal(15,4) DEFAULT NULL,
      `dli_gross` decimal(13,2) DEFAULT NULL,
      `dli_invoice_discount` decimal(13,2) DEFAULT NULL,
      `dli_invoice_net` decimal(13,2) DEFAULT NULL,
      `dli_distribution_discount` decimal(13,2) DEFAULT NULL,
      `dli_distribution_net` decimal(13,2) DEFAULT NULL,
      `dli_tariff_move_policy` varchar(255) DEFAULT NULL,
      `dli_approval` varchar(55) DEFAULT NULL,
      `dli_service_provider` int(10) DEFAULT NULL,
      `dli_invoiceable` varchar(3) DEFAULT NULL,
      `dli_distributable` varchar(3) DEFAULT NULL,
      `dli_invoiced` varchar(3) DEFAULT NULL,
      `dli_distributed` varchar(3) DEFAULT NULL,
      `dli_invoice_number` int(10) DEFAULT NULL,
      `dli_relcrmid` int(10) DEFAULT NULL,
      PRIMARY KEY (`detaillineitemsid`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
    $db->query($stmt);
}

//So whatever.
if (Vtiger_Utils::CheckTable($tableName)) {
    //if the table was created with the initial build we need to add these two columns.
    $fieldsToUpdate = [
        'dli_ready_to_distribute' => 'varchar(3)',
        'dli_ready_to_invoice' => 'varchar(3)',
        'dli_participant_role' => 'varchar(55)',
        'dli_participant_role_id' => 'int(10)',
        'dli_date_performed'     => 'date',
        'dli_quantity'           => 'decimal(13,2)',
    ];
    foreach ($fieldsToUpdate as $field_name => $columntype) {
        if (!$field_name || !$columntype) {
            continue;
        }
        $stmt = 'EXPLAIN `'.$tableName.'` `'.$field_name.'`';
        if ($res = $db->pquery($stmt)) {
            //sigh, sorry, I dislike this as much in theory as in practice.
            if ($value = $res->fetchRow()) {
                do {
                    if ($value['Field'] == $field_name) {
                        if (strtolower($value['Type']) != strtolower($columntype)) {
                            echo "Updating $field_name to be a ".$columntype." type.<br />\n";
                            $stmt = 'ALTER TABLE `'.$tableName.'` MODIFY COLUMN `'.$field_name.'` '.$columntype.' DEFAULT NULL';
                            $db->pquery($stmt);
                        }
                        //we're only affecting the $field_name so if we find it just break
                        break;
                    }
                } while ($value = $res->fetchRow());
            } else {
                $stmt = 'ALTER TABLE `'.$tableName.'` ADD COLUMN `'.$field_name.'` '.$columntype.' DEFAULT NULL';
                echo "ADDING: $field_name to $tableName with $columntype <br />\n";
                $db->pquery($stmt);
            }
        } else {
            echo "NO $field_name column in The actual table?<br />\n";
        }
    }
}

// forget modules ... maybe another day.
//foreach (['DetailLineItems'] as $moduleName) {
//    echo "<br>begin create module script for $moduleName <br>";
//    $moduleInstance = Vtiger_Module::getInstance($moduleName);
//    $new_module     = false;
//    if (!$moduleInstance) {
//        echo "module doesn't exist";
//        $moduleInstance       = new Vtiger_Module();
//        $moduleInstance->name = $moduleName;
//        $moduleInstance->save();
//        $moduleInstance->initTables();
//        $new_module = true;
//    }
//    echo "<br>creating blocks...";
//    $block1 = Vtiger_Block::getInstance('LBL_DETAILLINEITEMS_INFORMATION', $moduleInstance);
//    if (!$block1) {
//        $block1        = new Vtiger_Block();
//        $block1->label = 'LBL_DETAILLINEITEMS_INFORMATION';
//        $moduleInstance->addBlock($block1);
//    }
//    $block2 = Vtiger_Block::getInstance('LBL_CUSTOM_INFORMATION', $moduleInstance);
//    if (!$block2) {
//        $block2        = new Vtiger_Block();
//        $block2->label = 'LBL_CUSTOM_INFORMATION';
//        $moduleInstance->addBlock($block2);
//    }
//    echo "done!<br> creating fields...";
//    $fields = [
//        'agentid'          => [
//            'label'      => 'Owner',
//            'name'       => 'agentid',
//            //'table'      => 'vtiger_crmentity',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'agentid',
//            'columntype' => 'INT(11)',
//            'uitype'     => 1002,
//            'typeofdata' => 'I~M',
//            'block'      => $block1,
//        ],
//        'assigned_user_id' => [
//            'label'      => 'Assigned To',
//            'name'       => 'assigned_user_id',
//            //'table'      => 'vtiger_crmentity',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'smownerid',
//            'columntype' => 'INT(19)',
//            'uitype'     => 53,
//            'typeofdata' => 'V~M',
//            'block'      => $block1,
//        ],
//        //$sql    = 'INSERT INTO `'.$table_name.'` SET smownerid=?,modifiedby=?,description=?, createdtime=?, modifiedtime=?, agentid=?';
//        'modifiedby' => [
//            'label'      => 'modifiedby',
//            'name'       => 'modifiedby',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'modifiedby',
//            'columntype' => 'INT(19)',
//            'uitype'     => 53,
//            'typeofdata' => 'V~O',
//            'presence' => '1',
//            'block'      => $block1,
//        ],
//        'modifiedtime' => [
//            'label'      => 'modifiedtime',
//            'name'       => 'modifiedtime',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'modifiedtime',
//            'columntype' => 'DATETIME',
//            'uitype'     => 70,
//            'typeofdata' => 'T~O',
//            'presence' => '1',
//            'block'      => $block1,
//        ],
//        'createdtime' => [
//            'label'      => 'createdtime',
//            'name'       => 'createdtime',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'createdtime',
//            'columntype' => 'DATETIME',
//            'uitype'     => 70,
//            'typeofdata' => 'T~O',
//            'presence' => '1',
//            'block'      => $block1,
//        ],
//        'dli_tariff_item_number'  => [
//            'label'               => 'LBL_DETAILLINEITEMS_TARIFF_ITEM_NUMBER',
//            'name'                => 'dli_tariff_item_number',
//            'table'               => 'vtiger_detaillineitems',
//            'column'              => 'dli_tariff_item_number',
//            'columntype'          => 'VARCHAR(255)',
//            'uitype'              => 1,
//            'typeofdata'          => 'V~O',
//            'block'               => $block1,
//        ],
//        'dli_tariff_schedule_section'  => [
//            'label'               => 'LBL_DETAILLINEITEMS_TARIFF_SCHEDULE_SECTION',
//            'name'                => 'dli_tariff_schedule_section',
//            'table'               => 'vtiger_detaillineitems',
//            'column'              => 'dli_tariff_schedule_section',
//            'columntype'          => 'VARCHAR(255)',
//            'uitype'              => 1,
//            'typeofdata'          => 'V~O',
//            'block'               => $block1,
//        ],
//        'dli_return_section_name'  => [
//            'label'               => 'LBL_DETAILLINEITEMS_RETURN_SECTION_NAME',
//            'name'                => 'dli_return_section_name',
//            'table'               => 'vtiger_detaillineitems',
//            'column'              => 'dli_return_section_name',
//            'columntype'          => 'VARCHAR(255)',
//            'uitype'              => 1,
//            'typeofdata'          => 'V~O',
//            'block'               => $block1,
//        ],
//        'dli_tariff_item_name'  => [
//            'label'               => 'LBL_DETAILLINEITEMS_TARIFF_ITEM_NAME',
//            'name'                => 'dli_tariff_item_name',
//            'table'               => 'vtiger_detaillineitems',
//            'column'              => 'dli_tariff_item_name',
//            'columntype'          => 'VARCHAR(255)',
//            'uitype'              => 1,
//            'typeofdata'          => 'V~O',
//            'block'               => $block1,
//        ],
//        /*  @NOTE: might need this to pull from our products table?  to map simple to detail?  instead of dli_tariff_item_name?
//        'dli_productid'    => [
//            'label'      => 'LBL_DETAILLINEITEMS_PRODUCT_ID',
//            'name'       => 'dli_productid',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_productid',
//            'columntype' => 'INT(10)',
//            'uitype'     => 7,
//            'typeofdata' => 'I~O',
//            'block'      => $block1,
//        ],
//        */
//        'dli_description'  => [
//            'label'               => 'LBL_DETAILLINEITEMS_DESCRIPTION',
//            'name'                => 'dli_description',
//            'table'               => 'vtiger_detaillineitems',
//            'column'              => 'dli_description',
//            'columntype'          => 'VARCHAR(255)',
//            'uitype'              => 1,
//            'typeofdata'          => 'V~O',
//            'block'               => $block1,
//            'setEntityIdentifier' => 1
//        ],
//        'dli_provider_role'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_PROVIDER_ROLE',
//            'name'       => 'dli_provider_role',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_provider_role',
//            'columntype' => 'VARCHAR(55)',
//            'uitype'     => 15,
//            'typeofdata' => 'V~O',
//            //'picklist' => ['HA','OA','DA','BA'],  //BLAH forget abbreviations.
//            'picklist' => ['Hauling Agent','Origin Agent','Destination Agent','Booking Agent'],
//            'block'      => $block1,
//        ],
//        'dli_base_rate'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_BASE_RATE',
//            'name'       => 'dli_base_rate',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_base_rate',
//            'columntype' => 'DECIMAL(13,2)',
//            'uitype'     => 71,
//            'typeofdata' => 'N~O',
//            'block'      => $block1,
//        ],
//        'dli_quantity'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_QUANTITY',
//            'name'       => 'dli_quantity',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_quantity',
//            'columntype' => 'INT(10)',
//            'uitype'     => 7,
//            'typeofdata' => 'I~O',
//            'block'      => $block1,
//        ],
//        'dli_unit_of_measurement'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_UNIT_OF_MEASUREMENT',
//            'name'       => 'dli_unit_of_measurement',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_unit_of_measurement',
//            'columntype' => 'VARCHAR(55)',
//            'uitype'     => 15,
//            'typeofdata' => 'V~O',
//            'picklist' => ['PCT','CWT','EA'],
//            //'picklist' => ['Percentage','Carton Weight','Each'],  //Hate on spelling it out.
//            'block'      => $block1,
//        ],
//        'dli_unit_rate'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_UNIT_RATE',
//            'name'       => 'dli_unit_rate',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_unit_rate',
//            'columntype' => 'DECIMAL(15,4)',
//            'uitype'     => 71,
//            'typeofdata' => 'N~O',
//            'block'      => $block1,
//        ],
//        'dli_gross'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_GROSS',
//            'name'       => 'dli_gross',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_gross',
//            'columntype' => 'DECIMAL(13,2)',
//            'uitype'     => 71,
//            'typeofdata' => 'N~O',
//            'block'      => $block1,
//        ],
//        'dli_invoice_discount'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_INVOICE_DISCOUNT',
//            'name'       => 'dli_invoice_discount',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_invoice_discount',
//            'columntype' => 'DECIMAL(13,2)',
//            'uitype'     => 71,
//            'typeofdata' => 'N~O',
//            'block'      => $block1,
//        ],
//        'dli_invoice_net'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_INVOICE_NET',
//            'name'       => 'dli_invoice_net',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_invoice_net',
//            'columntype' => 'DECIMAL(13,2)',
//            'uitype'     => 71,
//            'typeofdata' => 'N~O',
//            'block'      => $block1,
//        ],
//        'dli_distribution_discount'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_DISTRIBUTION_DISCOUNT',
//            'name'       => 'dli_distribution_discount',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_distribution_discount',
//            'columntype' => 'DECIMAL(13,2)',
//            'uitype'     => 71,
//            'typeofdata' => 'N~O',
//            'block'      => $block1,
//        ],
//        'dli_distribution_net'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_DISTRIBUTION_NET',
//            'name'       => 'dli_distribution_net',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_distribution_net',
//            'columntype' => 'DECIMAL(13,2)',
//            'uitype'     => 71,
//            'typeofdata' => 'N~O',
//            'block'      => $block1,
//        ],
//        'dli_tariff_move_policy'  => [
//            'label'               => 'LBL_DETAILLINEITEMS_TARIFF_MOVE_POLICY',
//            'name'                => 'dli_tariff_move_policy',
//            'table'               => 'vtiger_detaillineitems',
//            'column'              => 'dli_tariff_move_policy',
//            'columntype'          => 'VARCHAR(255)',
//            'uitype'              => 1,
//            'typeofdata'          => 'V~O',
//            'block'               => $block1,
//        ],
//        'dli_approval'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_APPROVAL',
//            'name'       => 'dli_approval',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_approval',
//            'columntype' => 'VARCHAR(55)',
//            'uitype'     => 15,
//            'typeofdata' => 'V~O',
//            //'picklist' => ['CA','TA','CB'],
//            'picklist' => ['Client Approved','Transferee Approved','Charge Back'],  //Hate on spelling it out.
//            'block'      => $block1,
//        ],
//        'dli_service_provider'     => [
//            'label'             => 'LBL_DETAILLINEITEMS_SERVICE_PROVIDER',
//            'name'              => 'dli_service_provider',
//            'table'             => 'vtiger_detaillineitems',
//            'column'            => 'dli_service_provider',
//            'columntype'        => 'INT(10)',
//            'uitype'            => 10,
//            'typeofdata'        => 'V~O',
//            'block'             => $block1,
//            'setRelatedModules' => ['Employees']
//        ],
//        'dli_invoiceable'   => [
//            'label'      => 'LBL_DETAILLINEITEMS_INVOICEABLE',
//            'name'       => 'dli_invoiceable',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_invoiceable',
//            'columntype' => 'VARCHAR(3)',
//            'uitype'     => 56,
//            'typeofdata' => 'V~O',
//            'block'      => $block1,
//        ],
//        'dli_distributable'   => [
//            'label'      => 'LBL_DETAILLINEITEMS_DISTRIBUTABLE',
//            'name'       => 'dli_distributable',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_distributable',
//            'columntype' => 'VARCHAR(3)',
//            'uitype'     => 56,
//            'typeofdata' => 'V~O',
//            'block'      => $block1,
//        ],
//        'dli_invoiced'   => [
//            'label'      => 'LBL_DETAILLINEITEMS_INVOICED',
//            'name'       => 'dli_invoiced',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_invoiced',
//            'columntype' => 'VARCHAR(3)',
//            'uitype'     => 56,
//            'typeofdata' => 'V~O',
//            'block'      => $block1,
//        ],
//        'dli_distributed'   => [
//            'label'      => 'LBL_DETAILLINEITEMS_DISTRIBUTED',
//            'name'       => 'dli_distributed',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_distributed',
//            'columntype' => 'VARCHAR(3)',
//            'uitype'     => 56,
//            'typeofdata' => 'V~O',
//            'block'      => $block1,
//        ],
//        'dli_invoice_number'     => [
//            'label'      => 'LBL_DETAILLINEITEMS_INVOICE_NUMBER',
//            'name'       => 'dli_invoice_number',
//            'table'      => 'vtiger_detaillineitems',
//            'column'     => 'dli_invoice_number',
//            'columntype' => 'INT(10)',
//            'uitype'     => 7,
//            'typeofdata' => 'I~O',
//            'block'      => $block1,
//        ],
//        //REQUIRED FOR GUEST BLOCK
//        'dli_relcrmid'     => [
//            'label'             => 'LBL_DETAILLINEITEMS_RELCRMID',
//            'name'              => 'dli_relcrmid',
//            'table'             => 'vtiger_detaillineitems',
//            'column'            => 'dli_relcrmid',
//            'columntype'        => 'INT(10)',
//            'uitype'            => 10,
//            'typeofdata'        => 'V~O',
//            'block'             => $block1,
//            'setRelatedModules' => ['Estimates']
//        ],
//    ];
//    addFields_CDLI($fields, $moduleInstance);
//    if ($new_module) {
//        $moduleInstance->setDefaultSharing();
//        $moduleInstance->initWebservice();
//        $estInstance = Vtiger_Module::getInstance('Estimates');
//        $estInstance->setGuestBlocks($moduleName, ['LBL_DETAILLINEITEMS_INFORMATION']);
//        //override standard behavior by adding a primary key and making it auto inc
//        $db   = PearDatabase::getInstance();
//        $stmt = 'ALTER TABLE `vtiger_detaillineitems` ADD PRIMARY KEY(`detaillineitemsid`)';
//        print "DOING THIS: $stmt<br />\n";
//        $db->pquery($stmt);
//        $stmt = 'ALTER TABLE `vtiger_detaillineitems` MODIFY COLUMN `detaillineitemsid` INT NOT NULL AUTO_INCREMENT';
//        print "DOING THIS: $stmt<br />\n";
//        $db->pquery($stmt);
//    }
//    echo "done!<br> module creation script complete";
//}
//
//function addFields_CDLI($fields, $module) {
//    $returnFields = [];
//    foreach ($fields as $field_name => $data) {
//        $createBlock = true;
//        $field0 = Vtiger_Field::getInstance($field_name, $module);
//        if ($field0) {
//            echo "<li>The $field_name field already exists</li><br>";
//            $returnFields[$field_name] = $field0;
//            if ($data['replaceExisting'] && $data['block']->id == $field0->getBlockId()) {
//                $createBlock = false;
//                $db          = PearDatabase::getInstance();
//                if ($data['uitype'] && $field0->uitype != $data['uitype']) {
//                    echo "Updating $field_name to uitype=".$data['uitype']." for lead source module<br />\n";
//                    $stmt = 'UPDATE `vtiger_field` SET `uitype` = ? WHERE `fieldid` = ?';
//                    $db->pquery($stmt, [$data['uitype'], $field0->id]);
//                }
//
//                //update the typeofdata
//                if ($data['typeofdata'] && $field0->typeofdata != $data['typeofdata']) {
//                    echo "Updating $field_name to be a have typeofdata = '".$data['typeofdata']."'.<br />\n";
//                    $stmt = 'UPDATE `vtiger_field` SET `typeofdata` = ? WHERE `fieldid` = ?';
//                    $db->pquery($stmt, [$data['typeofdata'], $field0->id]);
//                }
//
//                //update the presence
//                if ($data['presence'] && $field0->presence != $data['presence']) {
//                    echo "Updating $field_name to be a have presence = '".$data['presence']."'.<br />\n";
//                    $stmt = 'UPDATE `vtiger_field` SET `presence` = ? WHERE `fieldid` = ?';
//                    $db->pquery($stmt, [$data['presence'], $field0->id]);
//                }
//
//                if (
//                    array_key_exists('setRelatedModules', $data) &&
//                    $data['setRelatedModules'] &&
//                    count($data['setRelatedModules']) > 0
//                ) {
//                    echo "<li> setting relation to existing $field_name</li>";
//                    $field0->setRelatedModules($data['setRelatedModules']);
//                }
//                if ($data['updateDatabaseTable'] && $data['columntype']) {
//                    //hell you have to fix the created table!  ... sigh.
//                    $stmt = 'EXPLAIN `'.$field0->table.'` `'.$field_name.'`';
//                    if ($res = $db->pquery($stmt)) {
//                        while ($value = $res->fetchRow()) {
//                            if ($value['Field'] == $field_name) {
//                                if (strtolower($value['Type']) != strtolower($data['columntype'])) {
//                                    echo "Updating $field_name to be a " . $data['columntype'] . " type.<br />\n";
//                                    $db   = PearDatabase::getInstance();
//                                    $stmt = 'ALTER TABLE `' . $field0->table . '` MODIFY COLUMN `' . $field_name . '` ' . $data['columntype'] . ' DEFAULT NULL';
//                                    $db->pquery($stmt);
//                                }
//                                //we're only affecting the $field_name so if we find it just break
//                                break;
//                            }
//                        }
//                    } else {
//                        echo "NO $field_name column in The actual table?<br />\n";
//                    }
//                }
//            } else if ($data['block']->id == $field0->getBlockId()) {
//                //already exists in this block
//                $createBlock = false;
//            } else {
//                //need to add to a new block.
//                $createBlock = true;  //even though it already is.
//            }
//        }
//
//        if ($createBlock) {
//            echo "<li> Attempting to add $field_name</li><br />";
//            //@TODO: check data validity
//            $field0 = new Vtiger_Field();
//            //these are assumed to be filled.
//            $field0->label        = $data['label'];
//            $field0->name         = $data['name'];
//            $field0->table        = $data['table'];
//            $field0->column       = $data['column'];
//            $field0->columntype   = $data['columntype'];
//            $field0->uitype       = $data['uitype'];
//            $field0->typeofdata   = $data['typeofdata'];
//            $field0->summaryfield = ($data['summaryfield']?1:0);
//            $field0->defaultvalue = $data['defaultvalue'];
//            //these three MUST have values or it doesn't pop vtiger_field.
//            $field0->displaytype = ($data['displaytype']?$data['displaytype']:1);
//            $field0->readonly    = ($data['readonly']?$data['readonly']:1);
//            $field0->presence    = ($data['presence']?$data['presence']:2);
//            $data['block']->addField($field0);
//            if ($data['setEntityIdentifier'] == 1) {
//                $module->setEntityIdentifier($field0);
//            }
//            //just completely ensure there's stuff in the array before doing it.
//            if (
//                array_key_exists('setRelatedModules', $data) &&
//                $data['setRelatedModules'] &&
//                count($data['setRelatedModules']) > 0
//            ) {
//                $field0->setRelatedModules($data['setRelatedModules']);
//            }
//            if (
//                array_key_exists('picklist', $data) &&
//                $data['picklist'] &&
//                count($data['picklist']) > 0
//            ) {
//                $field0->setPicklistValues($data['picklist']);
//            }
//            $returnFields[$field_name] = $field0;
//        }
//    }
//
//    return $returnFields;
//}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";