<?php


// No longer used by Sirva
class Estimates_LoadValuationOptions_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $data   = [];

        if (getenv('INSTANCE_NAME') == 'graebel') {
        $sql = "SELECT custom_tariff_type FROM `vtiger_tariffmanager` WHERE tariffmanagerid=?";
        $result = $db->pquery($sql, [$request->get('tariffid')]);
        if (!$result) {
            $response = new Vtiger_Response();
            $response->setError('Failed to query TariffManager table');
            $response->emit();
            return;
        }

        $row = $result->fetchRow();
        if (!$row) {
            $response = new Vtiger_Response();
            $response->setError('Failed to locate tariff');
            $response->emit();
            return;
        }

        if ($row['custom_tariff_type'] == 'MMI') {
                $data = [
                    [
                    'id' => 1,
                    'related_id' => $request->get('tariffid'),
                    'valuation_name' => 'Option A',
                    'per_pound' => 0
                    ],
                    [
                    'id' => 2,
                    'related_id' => $request->get('tariffid'),
                    'valuation_name' => 'Option B ($1.75/lb/art)',
                    'per_pound' => 0
                    ],
                    [
                    'id' => 3,
                    'related_id' => $request->get('tariffid'),
                    'valuation_name' => 'Option C ($1.25/lb/art)',
                    'per_pound' => 0
                    ],
                ];
        } elseif ($row['custom_tariff_type'] == 'MSI') {
                $data = [
                    [
                    'id' => 1,
                    'related_id' => $request->get('tariffid'),
                    'valuation_name' => '$0.60 Released',
                    'per_pound' => 0
                    ],
                    [
                    'id' => 2,
                    'related_id' => $request->get('tariffid'),
                    'valuation_name' => 'Free FVP',
                    'per_pound' => 0
                    ],
                    [
                    'id' => 3,
                    'related_id' => $request->get('tariffid'),
                    'valuation_name' => 'Full Value Replacement',
                    'per_pound' => 0
                    ]
                ];
        } else {
            $sql    = 'SELECT * FROM `vtiger_valuation_tariff_types` WHERE related_id = ? AND active = ?';
            $result = $db->pquery($sql, [$request->get('tariffid'), 'y']);
            $data   = [];
            while ($row = $result->fetchRow()) {
                $data[] = [
                    'id'             => $row['id'],
                    'related_id'     => $row['related_id'],
                    'valuation_name' => $row['valuation_name'],
                    'per_pound'      => $row['per_pound'],
                ];
            }
        }
        }
        if (!$data) {
            $sql = "SELECT * FROM `vtiger_valuation_deductible`";
            $result = $db->query($sql);
            $data = [];
            while ($row =& $result->fetchRow()) {
                $valDed = $row['valuation_deductible'];
                if($request->get('brand') == 'NVL') {
                    $valDed = str_replace('FVP','MVP',$valDed);
                }elseif($request->get('brand') == 'AVL') {
                    $valDed = str_replace('FVP','ECP',$valDed);
                }
                $data[] = [
                    'id' => $row['valuation_deductibleid'],
                    'related_id' => $request->get('tariffid'),
                    'valuation_name' => $valDed,
                    'per_pound' => 0
                ];
            }
        }

        $response = new Vtiger_Response();
        if ($data) {
            $response->setResult($data);
        } else {
            $response->setError("Failed to find valuation options");
        }

        $response->emit();
    }
}
