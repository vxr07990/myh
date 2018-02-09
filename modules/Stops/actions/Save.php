<?php



class Stops_Save_Action extends Vtiger_Save_Action
{
    public function process(Vtiger_Request $request)
    {
        parent::process($request);

        file_put_contents('logs/SequenceTest.log', date('Y-m-d H:i:s - ')."Parent process call completed\n", FILE_APPEND);

        $db = PearDatabase::getInstance();

        $recordid = $request->get('record');

        $orderid = $request->get('stop_order');
        $oppid = $request->get('stop_opp');
        $estid = $request->get('stop_est');

    /*	if($request->get('stop_order') != null){
            $var = $request->get('stop_order');
        }elseif($request->get('stop_opp') != null){
            $var = $request->get('stop_opp');
        }elseif($request->get('stop_est') != null){
            $var = $request->get('stop_est');
        }*/
        



        $sequence = $request->get('stop_sequence');

        if ($recordid == null || $recordid == '') {
            file_put_contents('logs/SequenceTest.log', date('Y-m-d H:i:s - ')."Empty record found1\n", FILE_APPEND);

            $result = $db->pquery("SELECT stopsid FROM vtiger_stops WHERE stop_order=? AND stop_opp =? AND stop_est =? ORDER BY stopsid DESC LIMIT 1", array($orderid, $oppid, $estid));

            $row = $result->fetchRow();

            $recordid = $row[0];
        }

        file_put_contents('logs/SequenceTest.log', date('Y-m-d H:i:s - ')."Preparing to call adjustStopSequence with $orderid, $sequence, $recordid, true\n", FILE_APPEND);

        $this->adjustStopSequence($db, $orderid, $oppid, $estid, $sequence, $recordid, true);

        $this->adjustStopSequence($db, $orderid, $oppid, $estid, $sequence, $recordid, false);
    }

    

    public function adjustStopSequence($db, $orderid, $oppid, $estid, $sequence, $stopid, $increment)
    {
        file_put_contents('logs/SequenceTest.log', date('Y-m-d H:i:s - ')."Entering adjustStopSequence function with $orderid, $sequence, $stopid, $increment\n", FILE_APPEND);

        $stops = $db->pquery("SELECT stopsid FROM vtiger_stops WHERE stop_order=? AND stop_opp =? AND stop_est =? AND stop_sequence=?", array($orderid, $oppid, $estid, $sequence));

        while ($row =& $stops->fetchRow()) {
            $id = $row[0];

            file_put_contents('logs/SequenceTest.log', date('Y-m-d H:i:s - ')."Stop $id being processed\n", FILE_APPEND);

            if ($id != $stopid) {
                file_put_contents('logs/SequenceTest.log', date('Y-m-d H:i:s - ')."Preparing to change stop_sequence value of $id\n", FILE_APPEND);

                if ($increment) {
                    file_put_contents('logs/SequenceTest.log', date('Y-m-d H:i:s - ')."Preparing to increment stop_sequence value of $id\n", FILE_APPEND);

                    $result = $db->pquery("UPDATE vtiger_stops SET stop_sequence=stop_sequence+1 WHERE stopsid=?", array($id));

                    file_put_contents('logs/SequenceTest.log', date('Y-m-d H:i:s - ')."Preparing to call adjustStopSequence with $orderid, $sequence, $recordid, $increment\n", FILE_APPEND);

                    $this->adjustStopSequence($db, $orderid, $oppid, $estid, $sequence+1, $id, $increment);
                } else {
                    file_put_contents('logs/SequenceTest.log', date('Y-m-d H:i:s - ')."Preparing to decrement stop_sequence value of $id\n", FILE_APPEND);

                    $result = $db->pquery("UPDATE vtiger_stops SET stop_sequence=stop_sequence-1 WHERE stopsid=?", array($id));

                    file_put_contents('logs/SequenceTest.log', date('Y-m-d H:i:s - ')."Preparing to call adjustStopSequence with $orderid, $sequence, $recordid, $increment\n", FILE_APPEND);

                    $this->adjustStopSequence($db, $orderid, $oppid, $estid, $sequence-1, $id, $increment);
                }
            }
        }
    }
}
