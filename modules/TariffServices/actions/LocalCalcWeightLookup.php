<?php
class TariffServices_LocalCalcWeightLookup_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }
    public function process(Vtiger_Request $request)
    {
        $response = new Vtiger_Response();

        $mileage = $request->get('miles');
        $rate = $request->get('rate');
        $weight = $request->get('weight');
        $selectedId = $request->get('serviceid');

        $info = self::lookup($mileage, $weight, $rate, $selectedId);

        $response->setResult($info);
        $response->emit();
    }

    public static function lookup($mileage, $weight, $rate, $selectedId) {
        $db = PearDatabase::getInstance();
        $rate_type = 'Break Point Trans.';
        $tempBracketMax;
        $tempMileage;
        $tempRate;
        $tempBreakpoint;
        $tempBracketMax;
        $tempWeight;

        $checkBreakPoint = true;

        $info = array();
        $info['rate'] = false;
        $info['calcWeight'] = false;

        $sql = "SELECT base_rate, break_point, to_weight FROM `vtiger_tariffbreakpoint` WHERE ? >= from_miles AND ? <= to_miles and ? >= from_weight and ? <= to_weight and serviceid=?";
        $result = $db->pquery($sql, array($mileage, $mileage, $weight, $weight, $selectedId));
        if ($result->numRows() > 0) { //Check if record exists for weight and mileage
            $row = $result->fetchRow();
            $tempRate = $row[0];
            $tempBreakpoint = $row[1];
            $tempBracketMax = $row[2];
            $tempWeight = $weight;
            $tempMileage = $mileage;
        } else { //If record does not exist
            $sql = "SELECT to_weight FROM `vtiger_tariffbreakpoint` WHERE ? >= from_weight and ? <= to_weight and serviceid=?";
            $result = $db->pquery($sql, array($weight, $weight, $selectedId));
            if ($result->numRows() > 0) { //Check if weight is withing a record
                $tempWeight = $weight;
            } else { //Check if weight is higher than max
                $sql = "SELECT MAX(to_weight), from_weight FROM `vtiger_tariffbreakpoint` WHERE serviceid=?";
                $result = $db->pquery($sql, array($selectedId));
                $row = $result->fetchRow();
                if ($weight > $row[0]) { //Weight is higher than max, set to max weight bracket
                    $tempWeight = $row[0];
                    $checkBreakPoint  = false; //We know we are in the max weight bracket, no need to check break point
                } else { //Weight is lower than lowest, set to lowest weight bracket
                    $sql = "SELECT MIN(from_weight) FROM `vtiger_tariffbreakpoint` WHERE serviceid=?";
                    $result = $db->pquery($sql, array($selectedId));
                    $row = $result->fetchRow();
                    $tempWeight = $row[0];
                    $checkBreakPoint  = false; //We know we are lower than the lowest weight bracket, no need to check break point
                }
            }

            $sql = "SELECT base_rate FROM `vtiger_tariffbreakpoint` WHERE ? >= from_miles AND ? <= to_miles and serviceid=?";
            $result = $db->pquery($sql, array($mileage, $mileage, $selectedId));
            if ($result->numRows() > 0) { //Check if mileage is withing a record
                $tempMileage = $mileage;
            } else { //Mileage is higher than max
                $sql = "SELECT MAX(to_miles) FROM `vtiger_tariffbreakpoint` WHERE serviceid=?";
                $result = $db->pquery($sql, array($selectedId));
                $row = $result->fetchRow();
                $tempMileage = $row[0];
            }

            $sql = "SELECT base_rate, break_point, to_weight FROM `vtiger_tariffbreakpoint` WHERE ? >= from_miles AND ? <= to_miles and ? >= from_weight and ? <= to_weight and serviceid=?";
            $result = $db->pquery($sql, array($tempMileage, $tempMileage, $tempWeight, $tempWeight, $selectedId));
            $row = $result->fetchRow();
            $tempRate = $row[0];
            $tempBreakpoint = $row[1];
            $tempBracketMax = $row[2];
        }

        if ($checkBreakPoint && ($tempBreakpoint < $tempWeight)) { //If breakpoint is greater than our weight
            $sql = "SELECT base_rate, from_weight, break_point FROM `vtiger_tariffbreakpoint` WHERE ? >= from_miles AND ? <= to_miles and ? >= from_weight and ? <= to_weight and serviceid=?";
            $result = $db->pquery($sql, array($tempMileage, $tempMileage, $tempBracketMax + 1, $tempBracketMax + 1, $selectedId));
            if ($result->numRows() > 0) { //If record exists, set params
                $row = $result->fetchRow();
                $info['rate'] = $row[0];
                $info['calcWeight'] = $row[1];
                $tempBreakpoint = $row[2];
            } else { //We already have the highest bracket
                $info['rate'] = $tempRate;
                $info['calcWeight'] = $tempweight;
            }
        } else { //Set params
            $info['rate'] = $tempRate;
            $info['calcWeight'] = $weight;
        }

        return $info;
    }
}
