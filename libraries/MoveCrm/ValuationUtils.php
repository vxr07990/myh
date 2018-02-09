<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 9/26/2016
 * Time: 12:39 PM
 */

namespace MoveCrm;

use PearDatabase;
use DateTimeField;
use Exception;
use Vtiger_Record_Model;
use Tariffs_Record_Model;

class ValuationUtils
{

    // Free FVP?
    protected static $deductibleMap = [
        '60&cent;/lb' => 'SIXTY_CENTS',
        '60&cent;/lb.' => 'SIXTY_CENTS',
        '60&cent; /lb' => 'SIXTY_CENTS',
        '60&cent; /lb.' => 'SIXTY_CENTS',
        '60&cent; Released' => 'SIXTY_CENTS',
        '$0.60/lb' => 'SIXTY_CENTS',
        '$0.60 Released' => 'SIXTY_CENTS',
        'Released Value' => 'SIXTY_CENTS',
        'MSI Released Value' => 'SIXTY_CENTS',
        'MMI Released Value' => 'SIXTY_CENTS',
        'Carrier Based Liability' => 'SIXTY_CENTS',
        'MMI RVP' => 'OPT_A_RVP',
        'CapRelo FVP' => 'ZERO',
        'GSA500A FVP' => 'ZERO',
        // FVP, FRV, RVP
        'Replacement Value Protection' => 'ZERO',
        '$0' => 'ZERO',
        '$250' => 'TWO_FIFTY',
        '$500' => 'FIVE_HUNDRED',
        '$750' => 'SEVEN_FIFTY',
        '$1000' => 'ONE_THOUSAND',
        '$1.25/lb' => 'DECL_ONE_TWENTYFIVE',
        'Per 100 Declared' => 'PER100_DECLARED',
        'Option A' => 'OPT_A_RVP',
        'Option B ($1.75/lb/art)' => 'OPT_B_BASE_LIABILITY',
        'Option C ($1.25/lb/art)' => 'OPT_C_BASE_LIABILITY',
        'Full Value Replacement' => 'FULL_VALUE_REPLACEMENT',
        'MSI FVR' => 'FULL_VALUE_REPLACEMENT',
        // should only be used by contract
        'Full Replacement Value' => 'FULL_VALUE_REPLACEMENT',
        ];

    public static function MapValuationDeductible($deductible, $deductibleSubType)
    {
        if (!$deductible) {
            return '';
        }
        if (in_array($deductible, ['Full Value Protection', 'Full Replacement Value', 'Replacement Value Protection'])
            && array_key_exists($deductibleSubType, ValuationUtils::$deductibleMap)) {
            return ValuationUtils::$deductibleMap[$deductibleSubType];
        }
        if (array_key_exists($deductible, ValuationUtils::$deductibleMap)) {
            return ValuationUtils::$deductibleMap[$deductible];
        }
        return 'CUSTOM';
    }

    public static function MapPricingMode($effectiveTariff, $businessLine)
    {
        // probably only works for GVL right now
        $effectiveTariffRecordModel = Vtiger_Record_Model::getInstanceById($effectiveTariff);
        $customTariffType = $effectiveTariffRecordModel->get('custom_tariff_type');
        $tariffManagerName = $effectiveTariffRecordModel->get('tariffmanagername');

        $pricingMode = 'Interstate';
        $tariffPricingModeMap = [
            '400N Base' => '_400N',
            '400N/104G' => '_400N_104G',
            '400NG' => '_400NG',
            'Intra - 400N' => 'INTRA_400N',
            'MSI' => 'MSI',
            'MMI' => 'MMI',
            'AIReS' => 'AIReS',
            'RMX400' => 'RMX400',
            'RMW400' => 'RMW400',
            'ISRS200-A' => 'ISRS_200_A',
            '09CapRelo' => '_09_CAP_RELO',
            'GSA-500A' => 'GSA500A',
            '400DOE' => '_400DOE',
        ];

        if (array_key_exists($customTariffType, $tariffPricingModeMap)
        ) {
            $pricingMode = $tariffPricingModeMap[$customTariffType];
        } elseif ($customTariffType == 'Base' && $tariffManagerName == '400N') {
            $pricingMode = '_400N';
        } elseif ($businessLine == 'Interstate Move' || $businessLine == 'Intrastate Move') {
            $pricingMode = 'Interstate';
        } elseif ($businessLine == 'Commercial Move') {
            //TODO: Fill in appropriate values for commercial moves
            $pricingMode = '';
        }
        return $pricingMode;
    }

    public static function GetVanlineID($owner)
    {
        $db = PearDatabase::getInstance();
        $sql = 'SELECT vtiger_vanlinemanager.vanline_id FROM vtiger_crmentity INNER JOIN vtiger_agents ON(vtiger_crmentity.crmid=vtiger_agents.agentsid) 
                RIGHT JOIN vtiger_agentmanager ON (vtiger_agents.agentmanager_id=vtiger_agentmanager.agentmanagerid)
                INNER JOIN vtiger_vanlinemanager ON (vtiger_agentmanager.vanline_id=vtiger_vanlinemanager.vanlinemanagerid) WHERE agentsid=? OR agentmanagerid=?';
        $res = $db->pquery($sql, [$owner, $owner]);
        if ($res && ($row = $res->fetchRow())) {
            return $row['vanline_id'];
        }
        throw new \Exception('Failed to find vanline ID');
    }
}
