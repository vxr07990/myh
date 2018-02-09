<?php
use Carbon\Carbon;

require_once('libraries/nusoap/nusoap.php');
class Opportunities_RegisterSTSAutoCheck_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
    *  Check if any autos require rush, but are not applied.
    */
    public function process(Vtiger_Request $request)
    {
        $db = $db ?: PearDatabase::getInstance();

        //Opportunity
        $opportunitiesId = $request->get('recordId');

        //Check if we have unregistered autos
        $autoQuery   = $db->pquery('SELECT * FROM `vtiger_autospotquote` WHERE `estimate_id` = (SELECT `quoteid` FROM `vtiger_quotes` WHERE `potentialid` = ? AND `is_primary` = 1 LIMIT 1) AND NULLIF(`registration_number`, "") IS NULL', [$opportunitiesId]);
        $autoCount   = $db->num_rows($autoQuery);
        $applicableQuotes = [];
        if($autoCount){
            while ($row =& $autoQuery->fetchRow()) {
                $timeDiff = Carbon::today()->diffInDays(Carbon::createFromFormat('Y-m-d', $row['auto_load_from']), false);
                if($timeDiff > 0 && $timeDiff <= 7 && intval(($row['auto_rush_fee']) < 100)){
                    $applicableQuotes[$row['autospotquoteid']] = [
                        'make' => $row['auto_make'],
                        'model' => $row['auto_model'],
                        'year' => $row['auto_year'],
                        'load_from' => $row['auto_load_from'],
                    ];
                }
            }
        }

        if(!empty($applicableQuotes)){
            $response = new Vtiger_Response();
            $response->setResult($applicableQuotes);
            $response->emit();
            exit();
        }

        //No autos require user input
        $response = new Vtiger_Response();
        $response->setResult('Skip');
        $response->emit();
    }

}
