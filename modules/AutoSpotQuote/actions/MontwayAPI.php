<?php
use Carbon\Carbon;

require_once('libraries/nusoap/nusoap.php');
class AutoSpotQuote_MontwayAPI_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    private $key = '';

    /*
    * Handle communication with montway API
    */
    public function process(Vtiger_Request $request)
    {
        $db = $db ?: PearDatabase::getInstance();

        //Connect to Montway and retrieve auth key
        $this->key = $this->curlGetKey();

        $action = $request->get('ajaxAction');

        switch ($action) {
            case 'getRates':
            print_r(json_decode($request->get('formData')));
                $info = $request->get('formData');
                $result = $this->curlGetRates($info);
                break;
            case 'updateAllVehicles':
                $result = $this->curlUpdateAllVehicles();
                break;
            
            default:
                break;
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

    /*
    if NAVL
        postValues["client_id"] = "aeb224d24";
        postValues["client_secret"] = "9f4220d72548b6073af9210c3fc41043e64e50c3";
    else //Allied
        postValues["client_id"] = "ae0afc137";
        postValues["client_secret"] = "4ffcad3c5c298ebac37ead0d7b9dd31f53b7e0d7";
    */
    public function curlGetKey()
    {
        $ch = curl_init();

        $postValues["client_id"] = "aeb224d24";
        $postValues["client_secret"] = "9f4220d72548b6073af9210c3fc41043e64e50c3";

        $key = base64_encode($postValues["client_id"].':'.$postValues["client_secret"]);
        $headers = [
            'Authorization: Basic ' . $key,
            'Content-Type: application/json',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_URL, getenv('MONTWAY_ADDRESS') . '/login/oauth/access_token');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"grant_type" : "client_credentials"}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        return json_decode($curlResult)->access_token;
    }

    public function curlUpdateAllVehicles()
    {
        $db = $db ?: PearDatabase::getInstance();

        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->key,
            'Accept: application/json',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_URL, getenv('MONTWAY_ADDRESS') . '/v1/vehicles');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);
        $dependencyMap['sourcefield'] = 'auto_make';
        $dependencyMap['targetfield'] = 'auto_model';
        foreach (json_decode($curlResult)->vehicles as $make) {
            $newDependency = ['sourcevalue' => $make->vehicle_label];

            Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_auto_make` (auto_makeid , auto_make, sortorderid, presence) SELECT id + 1, '$make->vehicle_label', id + 1, 1 FROM `vtiger_auto_make_seq` WHERE NOT EXISTS (SELECT * FROM `vtiger_auto_make` WHERE auto_make = '$make->vehicle_label')");
            Vtiger_Utils::ExecuteQuery("UPDATE vtiger_auto_make_seq SET id = (SELECT MAX(auto_makeid) FROM `vtiger_auto_make`)");

            foreach ($make->models as $model) {
                Vtiger_Utils::ExecuteQuery("INSERT INTO `vtiger_auto_model` (auto_modelid , auto_model, sortorderid, presence) SELECT id + 1, '$model->model_label', id + 1, 1 FROM `vtiger_auto_model_seq` WHERE NOT EXISTS (SELECT * FROM `vtiger_auto_model` WHERE auto_model = '$model->model_label')");
                Vtiger_Utils::ExecuteQuery("UPDATE vtiger_auto_model_seq SET id = (SELECT MAX(auto_modelid) FROM `vtiger_auto_model`)");

                $newDependency['targetvalues'][] = $model->model_label;
            }

            $dependencyMap['valuemapping'][] = $newDependency;
        }

        Vtiger_DependencyPicklist::savePickListDependencies('AutoSpotQuote', $dependencyMap);

        return 'Vehicle list updated';
    }

    public function curlGetRates($data)
    {
        $db = $db ?: PearDatabase::getInstance();
        $estimateInfo = Vtiger_Record_Model::getInstanceById($data['estimate_id'], 'Estimates')->getData();

        $quoteObject =  new stdClass();
        $quoteObject->transport->origin_location = $estimateInfo['origin_city'] . ', ' . $estimateInfo['origin_state'] . ' ' . $estimateInfo['origin_zip'];
        $quoteObject->transport->destination_location = $estimateInfo['destination_city'] . ', ' . $estimateInfo['destination_state'] . ' ' . $estimateInfo['destination_zip'];
        $quoteObject->transport->trailer_type = $data['auto_transport_type'] == 'Open Trailer' ? 'OPEN' : 'ENCLOSED';

        $quoteObject->vehicle->make = $data['auto_make'];
        $quoteObject->vehicle->model = $data['auto_model'];
        $quoteObject->vehicle->year = $data['auto_year'];
        $quoteObject->vehicle->condition = $data['auto_condition'] == 'Running' ? 'RUNNING' : 'NONRUNNING';

        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($quoteObject));
        curl_setopt($ch, CURLOPT_URL, getenv('MONTWAY_ADDRESS') . '/v1/quote');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
        $curlResult = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($curlResult);

        $minTime = $response->transit_time->min;
        $maxTime = $response->transit_time->max;


        $dateHelper = $carbon = Carbon::createFromFormat('m-d-Y', $data['auto_load_from']);

        $response->rates->ten_day_pickup->load_to_date = $dateHelper->copy()->addDays(10)->format('m-d-Y');
        $response->rates->seven_day_pickup->load_to_date = $dateHelper->copy()->addDays(7)->format('m-d-Y');
        $response->rates->four_day_pickup->load_to_date = $dateHelper->copy()->addDays(4)->format('m-d-Y');
        $response->rates->two_day_pickup->load_to_date = $dateHelper->copy()->addDays(2)->format('m-d-Y');

        $response->rates->ten_day_pickup->deliver_from_date = $dateHelper->copy()->addDays($minTime)->format('m-d-Y');
        $response->rates->seven_day_pickup->deliver_from_date = $dateHelper->copy()->addDays($minTime)->format('m-d-Y');
        $response->rates->four_day_pickup->deliver_from_date = $dateHelper->copy()->addDays($minTime)->format('m-d-Y');
        $response->rates->two_day_pickup->deliver_from_date = $dateHelper->copy()->addDays($minTime)->format('m-d-Y');

        $response->rates->ten_day_pickup->deliver_to_date = $dateHelper->copy()->addDays(10 + $maxTime)->format('m-d-Y');
        $response->rates->seven_day_pickup->deliver_to_date = $dateHelper->copy()->addDays(7 + $maxTime)->format('m-d-Y');
        $response->rates->four_day_pickup->deliver_to_date = $dateHelper->copy()->addDays(4 + $maxTime)->format('m-d-Y');
        $response->rates->two_day_pickup->deliver_to_date = $dateHelper->copy()->addDays(2 + $maxTime)->format('m-d-Y');

        return json_encode($response);
    }
}
