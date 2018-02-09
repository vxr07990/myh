<?php

class WFLocations_Save_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        $duplicateTags = [];
        $multiSave = 0;
        if ($request->get('create_multiple') && !empty($request->get('range_from'))) {
            $request->set('create_multiple', 0);
            $rangeArray = $this->generateRangeArray($request->get('range_from'), $request->get('range_to'));
            foreach ($rangeArray as $index => $name) {
                $tag = $this->generateLocationTag($request, $name);
                if(!$this->checkTagAvailability($tag, $request)) {
                    $duplicateTags[] = $tag;
                    $_SESSION['responseString'] = $this->buildResponseString($multiSave, $duplicateTags);
                    continue;
                }
                $request->set('name', $name);
                $request->set('tag', $tag);
                $request->set('returnToList', 1);
                $request->set('record', '');
                $multiSave++;
                $_SESSION['responseString'] = $this->buildResponseString($multiSave, $duplicateTags);
                parent::process($request);
            }
        } else if ($request->get('create_multiple') && !empty($request->get('row_to'))) {
            $request->set('create_multiple', 0);
            $rows         = $this->generateRangeArray($request->get('row'), $request->get('row_to'));
            $bays         = $this->generateRangeArray($request->get('bay'), $request->get('bay_to'));
            $levels       = $this->generateRangeArray($request->get('level'), $request->get('level_to'));
            foreach ($rows as $row) {
                foreach ($bays as $bay) {
                    foreach ($levels as $level) {
                        $name = $row.$bay.$level;
                        $request->set('name', $name);
                        $tag = $this->generateLocationTag($request, $name);
                        if(!$this->checkTagAvailability($tag, $request)) {
                            $duplicateTags[] = $tag;
                            $_SESSION['responseString'] = $this->buildResponseString($multiSave, $duplicateTags);
                            continue;
                        }
                        $request->set('tag', $tag);
                        $request->set('row', $row);
                        $request->set('bay', $bay);
                        $request->set('level', $level);
                        $request->set('returnToList', 1);
                        $request->set('record', '');
                        $multiSave++;
                        $_SESSION['responseString'] = $this->buildResponseString($multiSave, $duplicateTags);
                        parent::process($request);
                    }
                }
            }
        } else {
            parent::process($request);
        }
    }

    private function generateRangeArray($firstVal, $lastVal){
        $length = strlen($firstVal);
        if($length > 1 && substr($firstVal, 0, 1) == 0){
            $param = "%'0".$length."d";
            $returnArray = array_map(function($firstVal) use ($param) {return sprintf($param, $firstVal);}, range($firstVal, $lastVal));
        } else {
            $returnArray = range($firstVal, $lastVal);
        }
        return $returnArray;
    }

    private function generateLocationTag($request, $name) {
        $tag = $request->get('locationPrefix').$request->get('pre').$name.$request->get('post');
        if($request->get('wflocation_base_display')){
            $tag = $tag.'@'.$request->get('wflocation_base_display');
        }
        return $tag;
    }

    private function checkTagAvailability($tag, $request){
        $db = PearDatabase::getInstance();

        $sql = "SELECT * FROM `vtiger_wflocations` AS a
                JOIN `vtiger_crmentity` AS b ON a.wflocationsid = b.crmid 
                WHERE a.tag = ? AND a.wflocation_warehouse = ? AND b.deleted = 0";

        $check = $db->pquery($sql,[$tag, $request->get('wflocation_warehouse')]);
        $rows = $db->num_rows($check);
        if ($rows == 0) {
            return true;
        } else {
            return false;
        }
    }

    private function buildResponseString ($multiSave, $duplicateTags) {
        if($multiSave == 1) {
            $saveResultString = "<p><strong>".$multiSave." Location has been created. </strong></p>";
        } else {
            $saveResultString = "<p><strong>".$multiSave." Locations have been created. </strong></p>";
        }
        if(!empty($duplicateTags)){
            $saveResultString .= "<p>The Location(s) with the following tags cannot be created as the tag(s) are duplicates: </p>";
            $saveResultString .= implode(", ", $duplicateTags);
        }
        return $saveResultString;
    }


}
