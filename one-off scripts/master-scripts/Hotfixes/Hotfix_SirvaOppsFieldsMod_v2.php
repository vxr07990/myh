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


$db = PearDatabase::getInstance();

$oppsModule = Vtiger_Module::getInstance('Opportunities');

if ($oppsModule) {
    echo "<br>Opps module exists! Modifying fields.<br>";
    $oppsInfo = Vtiger_Block::getInstance('LBL_OPPORTUNITY_INFORMATION', $oppsModule);
    if (!$oppsInfo) {
        $oppsInfo = Vtiger_Block::getInstance('LBL_POTENTIALS_INFORMATION', $oppsModule);
    }

    if ($oppsInfo) {
        echo "<br>Opps information block exists<br>";
        $fix_these_fucking_rows = [
            [
                'name'=>'sales_stage',
                'label'=>'LBL_POTENTIALS_SALESSTAGE'
            ],
            [
                'name'=>'amount',
                'label'=>'LBL_POTENTIALS_AMOUNT'
            ],
            [
                'name'=>'campaignid',
                'label'=>'LBL_POTENTIALS_CAMPAIGNSOURCE'
            ],
            [
                'name'=>'contact_id',
                'label'=>'LBL_POTENTIALS_CONTACTNAME'
            ],
        ];
        foreach ($fix_these_fucking_rows as $fix_row) {
            try {
                $fieldFix = Vtiger_Field::getInstance($fix_row['name'], $oppsModule);
                if ($fieldFix) {
                    echo "<br>".$fix_row['name']." exists continuing with label swap<br>";
                    $db->pquery("UPDATE `vtiger_field` 
							SET fieldlabel = ? 
							WHERE fieldid = ?", [$fix_row['label'], $fieldFix->id]);
                    echo "<br>".$fix_row['name']." label swap done<br>";
                } else {
                    echo "<br>".$fix_row['name']." is not set!!<br>";
                }
            } catch (Exception $e) {
                echo "<br>".$fix_row['name'].": error detected!!<br>";
            }
        }
    } else {
        echo "<br>Opps information block does not exists<br>";
    }
} else {
    echo "<br>Opps module does not exists!<br>";
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";