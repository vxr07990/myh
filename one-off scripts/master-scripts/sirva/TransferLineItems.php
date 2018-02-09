<?php
/**
 * Created by PhpStorm.
 * User: DBOlin
 * Date: 3/6/2017
 * Time: 4:47 PM
 */

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

$db->startTransaction();

$res = $db->pquery('SELECT estimate_id,amount,quantity,description,rate,ratingitem FROM vtiger_rating_line_item_details');

$i=0;
while($res && $row = $res->fetchRow())
{
    $i++;
    $db->pquery('INSERT INTO vtiger_detailed_lineitems (dli_return_section_name,dli_description,dli_quantity,dli_gross,dli_invoice_net,dli_base_rate,dli_relcrmid)
                  VALUES (?,?,?,?,?,?,?)',
                [
                    explode('_', $row['ratingitem'])[0],
                    $row['description'],
                    $row['quantity'],
                    $row['amount'],
                    $row['amount'],
                    $row['rate'],
                    $row['estimate_id'],
                ]);
    if(($i % 1000) == 0)
    {
        echo $i . PHP_EOL;
    }
}

$db->completeTransaction();



print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";