<?php

class Google_MilesCalculator_Action extends Vtiger_BasicAjax_Action {

    public function process(Vtiger_Request $request) {

        $key = getenv('GOOGLE_MAPS_DISTANCE_MATRIX_API_KEY');
        $addresses = [];
        $db = &PearDatabase::getInstance();

        $agentId = $request->get('agent');

        $agent = Vtiger_Record_Model::getInstanceById($request->get('agent'));

        if(!$agent || !method_exists($agent, 'getAddress')) {
          $response->setError('Travel time / distance lookup failed.');
          $response->emit();
          return;
        }

        $addresses[] = [
            'type' => 'Office',
            'address' => $agent->getAddress(),
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

        // This is currently very limited, but all the data
        // is still here, and can be utilized in the future
        $response->setResult($arr);
        $response->emit();
    }

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
}
