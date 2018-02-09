<?php
/**
 * Created by PhpStorm.
 * User: dbolin
 * Date: 1/12/2017
 * Time: 2:43 PM
 */

class Estimates_GoogleMilesCalculator_Action extends Vtiger_BasicAjax_Action {

    private function encodeURIComponent($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }

    private function formatTime($time)
    {
        $days = floor($time / 86400);
        $time -= $days * 86400;
        $fmt = $days > 0 ? sprintf('%02d:', $days) : '';
        $fmt .= gmdate('H:i', $time);
        return $fmt;
    }

    public function process(Vtiger_Request $request) {

        $key = getenv('GOOGLE_MAPS_DISTANCE_MATRIX_API_KEY');
        $addresses = [];
        $db = &PearDatabase::getInstance();
        $res = $db->pquery('SELECT address1,city,state,zip FROM vtiger_agentmanager WHERE agentmanagerid=?',
                    [$request->get('agent')]);
        $row = $res->fetchRow();
        if($row)
        {
            $agentAddr = $row['address1'];
            if(strlen($row['city']) > 0) {
                if (strlen($agentAddr) > 0) {
                    $agentAddr .= ', ';
                }
                $agentAddr .= $row['city'];
            }
            if(strlen($row['state']) > 0) {
                if (strlen($agentAddr) > 0) {
                    $agentAddr .= ', ';
                }
                $agentAddr .= $row['state'];
            }
            if(strlen($row['zip']) > 0) {
                if (strlen($agentAddr) > 0) {
                    $agentAddr .= ', ';
                }
                $agentAddr .= $row['zip'];
            }
        }
        $addresses[] = [
            'type' => 'Office',
            'address' => $agentAddr,
            ];

        $list = $request->get('list');
        usort($list, function($a,$b) {
            if($a['sequence'] < $b['sequence'])
            {
                return -1;
            } else if ($a['sequence'] > $b['sequence'])
            {
                return 1;
            }
            return 0;
        });

        foreach($list as $addr)
        {
            $addresses[] = [
                'type' => $addr['type'],
                'address' => $addr['address']
            ];
        }
        //$addresses[] = $agentAddr;

        $url = '';
        $and = false;
        foreach($addresses as $addr)
        {
            if($and)
            {
                $url .= '|';
            } else {
                $and = true;
            }
            $url .= $this->encodeURIComponent($addr['address']);
        }
        $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins='.$url.'&destinations='.$url.'&key='.$key;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        $response = new Vtiger_Response();

        $arr = [
            [
                'address' => 'Office: ' . $result['origin_addresses'][0],
                'miles' => '0',
                'time' => '00:00',
            ]
        ];
        $max = count($addresses);
        $totalMiles = 0;
        $totalTime = 0;

        // TODO: Handle the Google Routing Setup Module, which doesn't exist right now

        for($i=1;$i<$max;++$i)
        {
            $status = $result['rows'][$i-1]['elements'][$i]['status'];
            if($status != 'OK')
            {
                $response->setError('Travel time / distance lookup failed.');
                $response->emit();
                return;
            }
            $miles = (float)$result['rows'][$i-1]['elements'][$i]['distance']['value'] / 1609.34;
            $time = (int)$result['rows'][$i-1]['elements'][$i]['duration']['value'];
            $totalMiles += $miles;
            $totalTime += $time;
            $arr[] = [
                'address' => $addresses[$i]['type'] . ': ' . $addresses[$i]['address'],
                'miles' => round($miles, 1),
                'time' => $this->formatTime($time),
            ];
        }
        $status = $result['rows'][$max-1]['elements'][0]['status'];
        if($status != 'OK')
        {
            $response->setError('Travel time / distance lookup failed.');
            $response->emit();
            return;
        }
        $miles = (float)$result['rows'][$max-1]['elements'][0]['distance']['value'] / 1609.34;
        $time = (int)$result['rows'][$max-1]['elements'][0]['duration']['value'];
        $totalMiles += $miles;
        $totalTime += $time;
        $arr[] = [
            'address' => $addresses[0]['type'] . ': ' . $addresses[0]['address'],
            'miles' => round($miles, 1),
            'time' => $this->formatTime($time),
        ];

        $viewer = new Vtiger_Viewer();
        $viewer -> assign('GOOGLE_ADDRESSES', $arr);
        $viewer -> assign('IS_EDIT_VIEW', 1);
        $viewer -> assign('GOOGLE_TOTAL_MILES', round($totalMiles, 1));
        $viewer -> assign('GOOGLE_TOTAL_TIME', $this->formatTime($totalTime));
        $viewer -> assign('MODULE', 'Estimates');
        ob_start();
        $viewer->view('GoogleMilesCalculator.tpl', 'Estimates');
        $res = ob_get_contents();
        ob_end_clean();
        $response->setResult($res);
        $response->emit();
    }
}
