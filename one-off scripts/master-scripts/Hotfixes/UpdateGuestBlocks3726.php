<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 2/17/2017
 * Time: 11:32 AM
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

Vtiger_Utils::AddColumn('vtiger_guestmodulerel', 'after_block', 'VARCHAR(255)');

if(getenv('IGC_MOVEHQ') && getenv('INSTANCE_NAME') != 'graebel')
{
    $db->pquery('UPDATE vtiger_guestmodulerel SET after_block=? WHERE hostmodule=? AND guestmodule=?',
                ['LBL_ORDERS_ORIGINADDRESS', 'Orders', 'ExtraStops']);
    $db->pquery('UPDATE vtiger_guestmodulerel SET after_block=? WHERE hostmodule=? AND guestmodule=?',
                ['LBL_POTENTIALS_ADDRESSDETAILS', 'Opportunities', 'ExtraStops']);
    $db->pquery('UPDATE vtiger_guestmodulerel SET after_block=? WHERE hostmodule=? AND guestmodule=?',
                ['LBL_ADDRESS_INFORMATION', 'Estimates', 'ExtraStops']);
    $db->pquery('UPDATE vtiger_guestmodulerel SET after_block=? WHERE hostmodule=? AND guestmodule=?',
                ['LBL_ADDRESS_INFORMATION', 'Actuals', 'ExtraStops']);

    $extraStops = Vtiger_Module::getInstance('ExtraStops');
    $relatedModule = Vtiger_Module::getInstance('Leads');
    if($relatedModule && $extraStops) {
        $relField = Vtiger_Field::getInstance('extrastops_relcrmid', $extraStops);
        $res = $db->pquery('SELECT 1 FROM vtiger_fieldmodulerel WHERE module=? AND relmodule=? AND fieldid=?',
                           ['ExtraStops','Leads',$relField->id]);
        if($db->num_rows($res) === 0) {
            if ($relField) {
                $relField->setRelatedModules(['Leads']);
                $relatedModule->setGuestBlocks('ExtraStops', ['LBL_EXTRASTOPS_INFORMATION'], 'LBL_LEADS_ADDRESSINFORMATION');
            }
        }
    }
}


print "\e[94mFINISHED: " . __FILE__ . "<br />\n\e[0m";