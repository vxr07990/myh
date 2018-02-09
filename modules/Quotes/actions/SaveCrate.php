<?php
class Quotes_SaveCrate_Action extends Vtiger_BasicAjax_Action
{
    public function __construct()
    {
		parent::__construct();
	}
	
    public function process(Vtiger_Request $request)
    {
		$db = PearDatabase::getInstance();
		$params = array();
        $row = null;
		$line_item_id = $request->get('line_item_id');
		
        if ($line_item_id != '') {
			$sql = "SELECT line_item_id FROM `vtiger_crates` WHERE line_item_id=?";
			$params[] = $line_item_id;
			
			$result = $db->pquery($sql, $params);
			unset($params);
			
			$row = $result->fetchRow();
		}
        if ($row == null) {
			//Update line_item_id from vtiger_misc_accessorials_seq and increment id value in table
			$sql = "UPDATE `vtiger_crates_seq` SET id=id+1";
			$result = $db->pquery($sql, $params);
			
			$sql = "SELECT id FROM `vtiger_crates_seq`";
			$result = $db->pquery($sql, $params);
			$row = $result->fetchRow();
			$line_item_id = $row[0];
			
			//Create new crate record
			$sql = "INSERT INTO `vtiger_crates` (quoteid, crateid, description, length, width, height, pack, unpack, ot_pack, ot_unpack, discount, cube, line_item_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
        } else {
			//Update existing record
			$sql = "UPDATE `vtiger_crates` SET quoteid=?, crateid=?, description=?, length=?, width=?, height=?, pack=?, unpack=?, ot_pack=?, ot_unpack=?, discount=?, cube=? WHERE line_item_id=?";
		}
		
		$length = $request->get('length');
		$width = $request->get('width');
		$height = $request->get('height');
		$padding = 4;
		if(getenv('INSTANCE_NAME') == 'graebel'){
			$padding = 0;
		}
		
		$params[] = $request->get('record');
		$params[] = $request->get('crateid');
		$params[] = $request->get('description');
		$params[] = $length;
		$params[] = $width;
		$params[] = $height;
		$params[] = $request->get('pack');
		$params[] = $request->get('unpack');
		$params[] = $request->get('otpack');
		$params[] = $request->get('otunpack');
		$params[] = $request->get('discount');
		$params[] = ceil(($length+$padding)*($width+$padding)*($height+$padding)/(12*12*12));
		$params[] = $line_item_id;
		
		$result = $db->pquery($sql, $params);
		
		$response = new Vtiger_Response();
		$response->setResult($line_item_id);
		$response->emit();
	}
}
