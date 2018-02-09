<?php
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';

class TariffSections_Save_Action extends Vtiger_Save_Action
{
    //	public function process(Vtiger_Request $request) {
//		file_put_contents('logs/log.log', "\n In this one", FILE_APPEND);
//		parent::process($request);
//		$name = $request->get('section_name');
//		$db = PearDatabase::getInstance();
//		$sql = "SELECT service_no FROM `vtiger_service` WHERE servicename = ?";
//		$result = $db->pquery($sql, array($name));
//		$row = $result->fetchRow();
//		if(empty($row)){
//			$this->createService($request->get('section_name'));
//		}
//	}
//	protected function createService($name){
//		$db = PearDatabase::getInstance();
//
//		try {
//			$user = new Users();
//			$current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
//			$data = array(
//				'servicename'=>$name,
//				'assigned_user_id'=>'19x1',
//				'qty_per_unit'=>0,
//				'unit_price'=>0,
//				'discontinued'=>1,
//				'currency_id'=>1,
//				'commissionrate'=>0);
//			$service = vtws_create('Services', $data, $current_user);
//			$wsid = $service['id'];
//			$crmid = explode('x',$wsid)[1];
//			$sql = "INSERT INTO `vtiger_producttaxrel` (productid,taxid,taxpercentage) VALUES (?,?,?)";
//			for($i = 1; $i <= 3; $i++){
//				$result = $db->pquery($sql, array($crmid,$i,0));
//			}
//		} catch (WebServiceException $ex) {
//			echo $ex->getMessage();
//		}
//	}
}
/*
include_once 'includes/main/WebUI.php';
include_once 'include/Webservices/Create.php';
include_once 'modules/Users/Users.php';
$serviceList = array('Fuel Surcharge',
                'Packing',
                'Unpacking',
                'Valuation',
                'Transportation',
                'Origin Accessorials',
                'Destination Accessorials',
                'Miscellaneous Services',
                'Origin SIT',
                'Destination SIT',
                'IRR',
                'Bulky Items');
foreach($serviceList as $name){
    createService($name);
}
echo "file complete<br>";
function createService($name){
    $db = PearDatabase::getInstance();

    try {
    $user = new Users();
    $current_user = $user->retrieveCurrentUserInfoFromFile(Users::getActiveAdminId());
    $data = array(
        'servicename'=>$name,
        'assigned_user_id'=>'19x1',
        'qty_per_unit'=>0,
        'unit_price'=>0,
        'discontinued'=>1,
        'currency_id'=>1,
        'commissionrate'=>0);
    $service = vtws_create('Services', $data, $current_user);
    $wsid = $service['id'];
    $crmid = explode('x',$wsid)[1];
    $sql = "INSERT INTO `vtiger_producttaxrel` (productid,taxid,taxpercentage) VALUES (?,?,?)";
    for($i = 1; $i <= 3; $i++){
        $result = $db->pquery($sql, array($crmid,$i,0));
    }
    } catch (WebServiceException $ex) {
        echo $ex->getMessage();
    }
}
*/
