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

$db = &PearDatabase::getInstance();
$moduleNames = ['Estimates', 'Actuals'];

foreach ($moduleNames as $moduleName) {
    $module = Vtiger_Module::getInstance($moduleName);
    if (!$module) {
        print "Module $moduleName not found. Skipping.<br/>\n";
        continue;
    }
    $block = Vtiger_Block::getInstance('LBL_QUOTE_INFORMATION', $module);
    if (!$block) {
        print "Block not found for $moduleName. Skipping. <br/>\n";
        continue;
    }

    $field1 = Vtiger_Field::getInstance('gross_total', $module);
    if ($field1) {
        echo "The grossTotal field already exists<br>\n";
    } else {
        $field1             = new Vtiger_Field();
        $field1->label      = 'LBL_GROSS_TOTAL';
        $field1->name       = 'gross_total';
        $field1->table      = 'vtiger_quotes';
        $field1->column     = 'gross_total';
        $field1->columntype = 'decimal(22,2)';
        $field1->presence   = 2;
        $field1->displaytype = 3;
        $field1->uitype     = 71;
        $field1->typeofdata = 'N~O';
        $field1->summaryfield = 1;
        $block->addField($field1);
        echo "The $field1->name field added to $moduleName<br>\n";
    }
    $field2 = Vtiger_Field::getInstance('invoice_net_total', $module);
    if ($field2) {
        echo "The invoiceNetTotal field already exists<br>\n";
    } else {
        $field2             = new Vtiger_Field();
        $field2->label      = 'LBL_INVOICE_NET_TOTAL';
        $field2->name       = 'invoice_net_total';
        $field2->table      = 'vtiger_quotes';
        $field2->column     = 'invoice_net_total';
        $field2->columntype = 'decimal(22,2)';
        $field2->presence   = 2;
        $field2->uitype     = 71;
        $field2->displaytype = 3;
        $field2->typeofdata = 'N~O';
        $field2->summaryfield = 1;
        $block->addField($field2);
        echo "The $field2->name field added to $moduleName<br>\n";
    }
    $field3 = Vtiger_Field::getInstance('dist_net_total', $module);
    if ($field3) {
        echo "The distNetTotal field already exists<br>\n";
    } else {
        $field3             = new Vtiger_Field();
        $field3->label      = 'LBL_DIST_NET_TOTAL';
        $field3->name       = 'dist_net_total';
        $field3->table      = 'vtiger_quotes';
        $field3->column     = 'dist_net_total';
        $field3->columntype = 'decimal(22,2)';
        $field3->presence   = 2;
        $field3->displaytype = 3;
        $field3->uitype     = 71;
        $field3->typeofdata = 'N~O';
        $field3->summaryfield = 1;
        $block->addField($field3);
        echo "The $field3->name field added to $moduleName<br>\n";
    }
    $field4 = Vtiger_Field::getInstance('hdnGrandTotal', $module);
    if ($field4) {
        echo "The hdnGrandTotal field already exists<br/>\n";
        removeSummaryFieldANSF($field4);
    } else {
        $field4              = new Vtiger_Field();
        $field4->label       = 'LBL_QUOTES_HDNGRANDTOTAL';
        $field4->name        = 'hdnGrandTotal';
        $field4->table       = 'vtiger_quotes';
        $field4->column      = 'total';
        $field4->uitype      = 72;
        $field4->typeofdata  = 'N~O';
        $field4->displaytype = 3;

        $blockInstance->addField($field4);
    }

    $currentSummaryFields = ['quote_no', 'subject', 'quotestage', 'potential_id', 'account_id', 'assigned_user_id'];
    foreach ($currentSummaryFields as $summaryField) {
        addSummaryFieldANSF($summaryField, $moduleName);
    }

    $toRemove = ['hdnGrandTotal'];
    $toAdd = ['gross_total','invoice_net_total','dist_net_total'];
    $viewRes = $db->pquery('SELECT * FROM vtiger_customview WHERE entitytype=? AND userid=1 AND setdefault=1',
                           [$moduleName]);
    while ($row = $viewRes->fetchRow()) {
        foreach ($toRemove as $removeName) {
            $cRes = $db->pquery('SELECT * FROM vtiger_cvcolumnlist WHERE cvid=? AND columnname LIKE ?',
                                [$row['cvid'], '%:'.$removeName.':%']);
            $row2 = $cRes->fetchRow();
            if ($row2) {
                $index = $row2['columnindex'];
                $db->pquery('DELETE FROM vtiger_cvcolumnlist WHERE cvid=? AND columnname LIKE ?',
                            [$row['cvid'], '%:'.$removeName.':%']);
                $db->pquery('UPDATE vtiger_cvcolumnlist SET columnindex=columnindex-1 WHERE cvid=? AND columnindex > ?',
                            [$row['cvid'], $index]);
            }
        }

        $filterInstance = Vtiger_Filter::getInstance($row['cvid']);
        if ($filterInstance) {
            $index = 0;
            $cRes = $db->pquery('SELECT columnindex FROM vtiger_cvcolumnlist WHERE cvid=? ORDER BY columnindex DESC LIMIT 1',
                        [$row['cvid']]);
            if ($row2 = $cRes->fetchRow()) {
                $index = $row2['columnindex']+1;
            }
            foreach ($toAdd as $addName) {
                $cRes = $db->pquery('SELECT * FROM vtiger_cvcolumnlist WHERE cvid=? AND columnname LIKE ?',
                                    [$row['cvid'], '%:'.$addName.':%']);
                $row2 = $cRes->fetchRow();
                if ($row2) {
                    continue;
                }
                $field = Vtiger_Field::getInstance($addName, $module);
                if ($field) {
                    $filterInstance->addField($field, $index);
                    $index++;
                }
            }
        }
    }
}

$res = $db->pquery('UPDATE vtiger_quotes q 
                            INNER JOIN (
                                SELECT dli_relcrmid, SUM(dli_gross) as grossSum, SUM(dli_invoice_net) as netSum, SUM(dli_distribution_net) as distSum
                                    FROM vtiger_detailed_lineitems
                                    GROUP BY dli_relcrmid
                            ) i ON q.quoteid=i.dli_relcrmid
                            SET q.gross_total=i.grossSum, q.invoice_net_total=i.netSum, q.dist_net_total=i.distSum'
);

//Removing Grand Total as a summary field.
function removeSummaryFieldANSF($field)
{
    $db = PearDatabase::getInstance();
    if (!$field->summaryfield != 0) {
        echo "The $field->name field has already been updated.<br/>\n";
    } else {
        $stmt = "UPDATE `vtiger_field` SET `summaryfield` = ?"
                ." WHERE `fieldid` = ? LIMIT 1";
        $db->pquery($stmt, [0, $field->id]);
        echo "The $field->name field is no longer a summary field.<br/>\n";
    }
}

//Explicitly identifying summary fields for related list views
function addSummaryFieldANSF($fieldName, $moduleName)
{
    $db = PearDatabase::getInstance();
    if ($module = Vtiger_Module::getInstance($moduleName)) {
        $summaryField = Vtiger_Field::getInstance($fieldName, $module);
        if ($summaryField) {
            if ($summaryField->summaryfield == 1) {
                print "$fieldName in $moduleName is already a summary field <br/>\n";
            } else {
                print "<br>$moduleName $fieldName needs converting to summary field<br>\n";
                $stmt = "UPDATE `vtiger_field` SET `summaryfield` = ?"
                        //. " `quickcreate` = 1"
                        ." WHERE `fieldid` = ? LIMIT 1";
                $db->pquery($stmt, [1, $summaryField->id]);
                print "$moduleName $fieldName is converted to summary field<br>\n";
            }
        } else {
            print "failed to find: $fieldName in $moduleName<br />\n";
        }
    } else {
        print "failed to load module $moduleName<br />\n";
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";