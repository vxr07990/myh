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


try {
    echo "<br>Start the SIT Item creation process<br>";
    // The vtiger_rate_type, vtiger_rate_type_seq, and vtiger_tariffservices tables are required to perform the section
    if (Vtiger_Utils::CheckTable('vtiger_rate_type')&&Vtiger_Utils::CheckTable('vtiger_rate_type_seq')&&Vtiger_Utils::CheckTable('vtiger_tariffservices')) {
        if (!$db) {
            $db = PearDatabase::getInstance();
        }
        $sql = "SELECT * FROM `vtiger_rate_type` WHERE `rate_type` = 'SIT Item'";
        $result = $db->pquery($sql, array());
        $row = $result->fetchRow();
        if (!is_array($row)) {
            //get next sequence, and update the table to next sequence
            $result = $db->pquery('SELECT id FROM `vtiger_rate_type_seq`', array());
            $row = $result->fetchRow();
            $uniqueId = $row[0];
            $uniqueId++;
            $sql = "UPDATE `vtiger_rate_type_seq` SET id = ?";
            $db->pquery($sql, array($uniqueId));


            //grab the highest sortorderid, to get next greatest value
            $result = $db->pquery('SELECT sortorderid FROM `vtiger_rate_type` ORDER BY sortorderid DESC LIMIT 1', array());
            $row = $result->fetchRow();
            $sortId = $row[0];
            if (!is_array($row)) {
                echo "<br/>We could not get the highest sort number</br>";
            } else {
                $sortId = $row[0]++;
                $sql    = 'INSERT INTO `vtiger_rate_type` (rate_typeid, rate_type, sortorderid, presence) VALUES (?,?,?,?)';
                $result = $db->pquery($sql, [$uniqueId, 'SIT Item', $sortId, 1]);
                echo "SIT Item has been added to the picklist<br/>";
            }
        } else {
            echo "<br>SIT Item already in the rate_type picklist<br>";
        }

        $tariffServicesModule = Vtiger_Module::getInstance('TariffServices');
        if ($tariffServicesModule) {
            $block = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_SIT_ITEM', $tariffServicesModule);
            if (!$block) {
                echo "<br> LBL_TARIFFSERVICES_SIT_ITEM block doesn't exist. creating it now.<br>";
                $newBlock        = new Vtiger_Block();
                $newBlock->label = 'LBL_TARIFFSERVICES_SIT_ITEM';
                $tariffServicesModule->addBlock($newBlock);
                echo "<br>LBL_TARIFFSERVICES_SIT_ITEM block creation complete.<br>";
            } else {
                echo "<br>LBL_TARIFFSERVICES_SIT_ITEM block already exists.<br>";
            }
            //recall for ensuring the block still does exist - for multi run
            $block = Vtiger_Block::getInstance('LBL_TARIFFSERVICES_SIT_ITEM', $tariffServicesModule);
            if ($block) {
                $items_to_create = [
                                        [
                                            'label'=>'LBL_TARIFFSERVICES_CARTAGE',
                                            'name'=>'cartage_cwt_rate',
                                            'table'=>'vtiger_tariffservices',
                                            'column'=>'cartage_cwt_rate',
                                            'columntype'=>'VARCHAR(100)',
                                            'uitype'=>'71',
                                            'typeofdata'=>'V~O',
                                        ],
                                        [
                                            'label'=>'LBL_TARIFFSERVICES_FIRST_DAY',
                                            'name'=>'first_day_rate',
                                            'table'=>'vtiger_tariffservices',
                                            'column'=>'first_day_rate',
                                            'columntype'=>'VARCHAR(100)',
                                            'uitype'=>'71',
                                            'typeofdata'=>'V~O',
                                        ],
                                        [
                                            'label'=>'LBL_TARIFFSERVICES_ADDITIONAL_DAY',
                                            'name'=>'additional_day_rate',
                                            'table'=>'vtiger_tariffservices',
                                            'column'=>'additional_day_rate',
                                            'columntype'=>'VARCHAR(100)',
                                            'uitype'=>'71',
                                            'typeofdata'=>'V~O',
                                        ],

                ];
                foreach ($items_to_create as $variable) {
                    $field = Vtiger_Field::getInstance($variable['name'], $tariffServicesModule);
                    if ($field) {
                        echo '<br>'.$variable['name'].' field exists.<br>';
                    } else {
                        echo "<br>Creating ".$variable['name']." field:<br>";
                        $field             = new Vtiger_Field();
                        $field->label      = $variable['label'];
                        $field->name       = $variable['name'];
                        $field->table      = $variable['table'];
                        $field->column     = $variable['column'];
                        $field->columntype = $variable['columntype'];
                        $field->uitype     = $variable['uitype'];
                        $field->typeofdata = $variable['typeofdata'];
                        $block->addField($field);
                        echo "<br>".$variable['name']." field created!<br>";
                    }
                }
                Vtiger_Utils::AddColumn('vtiger_quotes', 'apply_custom_sit_rate_override_dest', 'VARCHAR(3)');

                //checks the table to see if it is made. This is for the estimates section, and only applies to sirva
                if (!Vtiger_Utils::CheckTable('vtiger_quotes_sit')) {
                    echo "<li>creating vtiger_quotes_sit </li><br>";
                    //creates the table
                    Vtiger_Utils::CreateTable('vtiger_quotes_sit',
                                              '(
                                                estimateid INT(11),
                                                serviceid INT(11),
                                                cartage_cwt_rate VARCHAR(100),
                                                first_day_rate VARCHAR(100),
                                                additional_day_rate VARCHAR(100)                                                
                                                )', true);
                }
            } else {
                echo "<br/>We are having some serious issues, because the LBL_TARIFFSERVICES_SIT_ITEM block does not exist!<br/>";
            }
        } else {
            echo "<br>Tariff Services not found! No action taken<br>";
        }
    } else {
        echo "<br>The required tables cannot be found, stopping the execution<br>";
    }
} catch (Exception $e) {
    echo "<br>".$e->getMessage()."<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";