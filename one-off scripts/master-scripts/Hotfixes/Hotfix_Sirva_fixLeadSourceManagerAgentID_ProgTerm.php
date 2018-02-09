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


//corrections from peer review that require module creation update.
$leadSourceManagerModule = Vtiger_Module::getInstance('LeadSourceManager');
if ($leadSourceManagerModule) {
    $db = PearDatabase::getInstance();
    echo "Lead Source Module exists checking agentid field<br />\n";
    $field0 = Vtiger_Field::getInstance('agentid', $leadSourceManagerModule);
    if ($field0) {
        if ($field0->summaryfield != 1) {
            echo "Updating agentid to summaryfield=1 for lead source module<br />\n";
            $stmt = 'UPDATE `vtiger_field` SET `summaryfield` = 1 WHERE `fieldid` = ?';
            $db->pquery($stmt, [$field0->id]);
        }
    } else {
        echo "NO agentid field in LEAD SOURCE MODULE?<br />\n";
    }

    $field1 = Vtiger_Field::getInstance('program_terms', $leadSourceManagerModule);
    if ($field1) {
        //Ensure it's uitype= 19
        if ($field1->uitype != 19) {
            echo "Updating program_terms to uitype=19 for lead source module<br />\n";
            $db   = PearDatabase::getInstance();
            $stmt = 'UPDATE `vtiger_field` SET `uitype` = 19 WHERE `fieldid` = ?';
            $db->pquery($stmt, [$field1->id]);
        }

        //update the typeofdata
        if ($field1->typeofdata != 'V~O') {
            echo "Updating program_terms to be a have typeofdata = 'V~O'.<br />\n";
            $stmt = 'UPDATE `vtiger_field` SET `typeofdata` = \'V~O\' WHERE `fieldid` = ?';
            $db->pquery($stmt, [$field1->id]);
        }

        //Update the table if necessary
        $stmt = 'EXPLAIN `vtiger_leadsourcemanager` `program_terms`';
        if ($res = $db->pquery($stmt)) {
            while ($value = $res->fetchRow()) {
                if ($value['Field'] == 'program_terms') {
                    if (strtolower($value['Type']) != 'text') {
                        echo "Updating program_terms to be TEXT in the table.<br />\n";
                        $db   = PearDatabase::getInstance();
                        $stmt = 'ALTER TABLE `vtiger_leadsourcemanager` MODIFY COLUMN `program_terms` TEXT';
                        $db->pquery($stmt);
                    }
                    //we're only affecting the program_terms so if we find it just break
                    break;
                }
            }
        } else {
            echo "NO program_terms column in The actual table?<br />\n";
        }
    } else {
        echo "NO program_terms field in LEAD SOURCE MODULE?<br />\n";
    }

    $field2 = Vtiger_Field::getInstance('lmp_source_id', $leadSourceManagerModule);
    if ($field2) {
        //update the uitype
        if ($field2->uitype != 1) {
            echo "Updating lmp_source_id to be a uitype = 1 type.<br />\n";
            $stmt = 'UPDATE `vtiger_field` SET `uitype` = 1 WHERE `fieldid` = ?';
            $db->pquery($stmt, [$field2->id]);
        }

        //update the typeofdata
        if ($field2->typeofdata != 'V~O') {
            echo "Updating lmp_source_id to be a have typeofdata = 'V~O'.<br />\n";
            $stmt = 'UPDATE `vtiger_field` SET `typeofdata` = \'V~O\' WHERE `fieldid` = ?';
            $db->pquery($stmt, [$field2->id]);
        }

        //hell you have to fix the created table!  ... sigh.
        $stmt = 'EXPLAIN `vtiger_leadsourcemanager` `lmp_source_id`';
        if ($res = $db->pquery($stmt)) {
            while ($value = $res->fetchRow()) {
                if ($value['Field'] == 'lmp_source_id') {
                    if (strtolower($value['Type']) != 'varchar(100)') {
                        echo "Updating lmp_source_id to be a varchar type.<br />\n";
                        $db   = PearDatabase::getInstance();
                        $stmt = 'ALTER TABLE `vtiger_leadsourcemanager` MODIFY COLUMN `lmp_source_id` VARCHAR(100) DEFAULT NULL';
                        $db->pquery($stmt);
                    }
                    //we're only affecting the lmp_source_id so if we find it just break
                    break;
                }
            }
        } else {
            echo "NO lmp_source_id column in The actual table?<br />\n";
        }
    } else {
        echo "NO lmp_source_id field in LEAD SOURCE module<br />\n";
    }

    $field3 = Vtiger_Field::getInstance('agency_code', $leadSourceManagerModule);
    if ($field3) {
        //update the uitype
        if ($field3->uitype != 1) {
            echo "Updating agency_code to be a uitype = 1 type.<br />\n";
            $stmt = 'UPDATE `vtiger_field` SET `uitype` = 1 WHERE `fieldid` = ?';
            $db->pquery($stmt, [$field3->id]);
        }

        //update the typeofdata
        if ($field3->typeofdata != 'V~M') {
            echo "Updating agency_code to have a typeofdata = 'V~M'.<br />\n";
            $stmt = 'UPDATE `vtiger_field` SET `typeofdata` = \'V~M\' WHERE `fieldid` = ?';
            $db->pquery($stmt, [$field3->id]);
        }

        //hell you have to fix the created table!  ... sigh.
        $stmt = 'EXPLAIN `vtiger_leadsourcemanager` `agency_code`';
        if ($res = $db->pquery($stmt)) {
            while ($value = $res->fetchRow()) {
                if ($value['Field'] == 'agency_code') {
                    if (strtolower($value['Type']) != 'varchar(50)') {
                        echo "Updating agency_code to be a varchar type.<br />\n";
                        $db   = PearDatabase::getInstance();
                        $stmt = 'ALTER TABLE `vtiger_leadsourcemanager` MODIFY COLUMN `agency_code` VARCHAR(50) DEFAULT NULL';
                        $db->pquery($stmt);
                    }
                    //we're only affecting the agency_code so if we find it just break
                    break;
                }
            }
        } else {
            echo "NO agency_code column in The actual table?<br />\n";
        }
    } else {
        echo "NO agency_code field in LEAD SOURCE module<br />\n";
    }


    echo "Fix lead source relationships<br />\n";

    $relationLabel = 'Lead Sources';

    //relate to Vanline Manager
    $vanlineManagerInstance = Vtiger_Module::getInstance('VanlineManager');

    //just destroy any possible relations
    $vanlineManagerInstance->unsetRelatedList($leadSourceManager, $relationLabel, 'get_related_list');
    $vanlineManagerInstance->unsetRelatedList($leadSourceManager, $relationLabel, 'get_dependents_list');
    $leadSourceManager->unsetRelatedList($vanlineManagerInstance, $relationLabel, 'get_related_list');
    $leadSourceManager->unsetRelatedList($vanlineManagerInstance, $relationLabel, 'get_dependents_list');

    //$vanlineManagerInstance->setRelatedList($leadSourceManager, $relationLabel, ['ADD','SELECT'], 'get_dependents_list');
    $vanlineManagerInstance->setRelatedList($leadSourceManager, $relationLabel, ['ADD'], 'get_dependents_list');


    //relate to Agent Manager
    $agentManagerInstance = Vtiger_Module::getInstance('AgentManager');

    //just destroy any possible relations
    $agentManagerInstance->unsetRelatedList($leadSourceManager, $relationLabel, 'get_related_list');
    $agentManagerInstance->unsetRelatedList($leadSourceManager, $relationLabel, 'get_dependents_list');
    $leadSourceManager->unsetRelatedList($agentManagerInstance, $relationLabel, 'get_related_list');
    $leadSourceManager->unsetRelatedList($agentManagerInstance, $relationLabel, 'get_dependents_list');

    //$agentManagerInstance->setRelatedList($leadSourceManager, $relationLabel, ['ADD','SELECT'], 'get_dependents_list');
    $agentManagerInstance->setRelatedList($leadSourceManager, $relationLabel, ['ADD'], 'get_dependents_list');
    echo "Done with Lead Source Manager fixes<br />\n";
} else {
    echo "NO LEAD SOURCE MODULE?<br />\n";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";