<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Quotes Record Model Class
 */
class Quotes_Record_Model extends Inventory_Record_Model
{
    public function getCreateInvoiceUrl()
    {
        $invoiceModuleModel = Vtiger_Module_Model::getInstance('Invoice');

        return "index.php?module=".$invoiceModuleModel->getName()."&view=".$invoiceModuleModel->getEditViewName()."&quote_id=".$this->getId();
    }

    public function getCreateSalesOrderUrl()
    {
        $salesOrderModuleModel = Vtiger_Module_Model::getInstance('SalesOrder');

        return "index.php?module=".$salesOrderModuleModel->getName()."&view=".$salesOrderModuleModel->getEditViewName()."&quote_id=".$this->getId();
    }

    /**
     * Function to get this record and details as PDF
     */
    public function getPDF()
    {
        $recordId = $this->getId();
        $moduleName = $this->getModuleName();

        $controller = new Vtiger_QuotePDFController($moduleName);
        $controller->loadRecord($recordId);

        $fileName = $moduleName.'_'.getModuleSequenceNumber($moduleName, $recordId);
        $controller->Output($fileName.'.pdf', 'D');
    }
    
    public function getPackingLabels()
    {
        //so we want only one place for the labels.
        return Estimates_Record_Model::getPackingLabelsStatic();
        
//		//file_put_contents('logs/PackingLabelTest.log', "Entering getPackingLabels function\n", FILE_APPEND);
//		return array('8'=>'Dish Pack',				'5'=>'Book',
//					 '1'=>'1.5',					'2'=>'3.0',
//					 '3'=>'4.5',					'4'=>'6.0',
//					 '16'=>'6.5',					'15'=>'Wardrobe',
//					 '14'=>'Single/Twin',			'6'=>'Crib',//'9'=>'Long',
//					 '7'=>'Double Bed',				'13'=>'King/Queen',
//					 '12'=>'Mirror',				'9'=>'Grandfather Clock',
//					 '102'=>'TV Carton',			//'15'=>'Ironing Board',
//					 '11'=>'Lamp',					/*'17'=>'Pole Lamp',
//					 '18'=>'Ski',					'19'=>'Tea Chest',
//					 '20'=>'Double < 4 cu. ft.',	'21'=>'Double > 4 < 7',
//					 '22'=>'Double > 7 < 15',*/		'17'=>'Mattress Cover',
//					 '509'=>'Other',				'510'=>'Heavy Duty');
    }
    
    public function getPackingItems()
    {
        $recordId = $this->getId();
        $labels = $this->getPackingLabels();
        $pack = array();
        $unpack = array();
        $otpack = array();
        $otunpack = array();
        $bulkyItems = array();
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT * FROM `vtiger_packing_items` WHERE quoteid=?";
        $params[] = $recordId;
        
        $result = $db->pquery($sql, $params);
        unset($params);
        
        while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $pack[$row['itemid']] = $row['pack_qty'];
            $unpack[$row['itemid']] = $row['unpack_qty'];
            $otpack[$row['itemid']] = $row['ot_pack_qty'];
            $otunpack[$row['itemid']] = $row['ot_unpack_qty'];
        }
        
        foreach ($labels as $itemId=>$itemLabel) {
            $packQty = (array_key_exists($itemId, $pack)) ? $pack[$itemId]:'0';
            $unpackQty = (array_key_exists($itemId, $unpack)) ? $unpack[$itemId]:'0';
            $otpackQty = (array_key_exists($itemId, $otpack)) ? $otpack[$itemId]:'0';
            $otunpackQty = (array_key_exists($itemId, $otunpack)) ? $otunpack[$itemId]:'0';
            $packingItems[$itemId] = array('label'=>$itemLabel, 'pack'=>$packQty, 'unpack'=>$unpackQty, 'otpack'=>$otpackQty, 'otunpack'=>$otunpackQty);
        }
        
        return $packingItems;
    }
    
    public function getBulkyLabels()
    {
        return Estimates_Record_Model::getBulkyLabelsStatic();
//		return array('1'=>'4x4 Vehicle',		'2'=>'Airplane, Glider',		'3'=>'All Terrain Cycle',
//					 '4'=>'Animal House',		'5'=>'Automobile',				'6'=>'Bath',
//					 '7'=>'Bath > 65 Cu Ft',	'8'=>'Boat Trailer',			'9'=>'Boat < 14 Ft',
//					 '10'=>'Boat > 14 Ft',		'11'=>'Camper, Truckless',		'12'=>'Camper Shell',
//					 '13'=>'Camper Trailer',	'14'=>'Canoe < 14 Ft',			'15'=>'Canoe > 14 Ft',
//					 '16'=>'Dinghy < 14 Ft',	'17'=>'Dinghy > 14 Ft',			'18'=>'Doll House',
//					 '19'=>'Farm Equipment',	'20'=>'Farm Implement',			'21'=>'Farm Trailer',
//					 '22'=>'Go-Cart',			'23'=>'Golf Cart',				'24'=>'Horse Trailer',
//					 '25'=>'Hot Tub',			'26'=>'Hot Tub > 65 Cu Ft',		'27'=>'Jacuzzi',
//					 '28'=>'Jacuzzi > 65 Cu Ft','29'=>'Jet Ski',				'30'=>'Jet Ski > 14 Ft',
//					 '31'=>'Kayak < 14 Ft',		'32'=>'Kayak > 14 Ft',			'33'=>'Kennel',
//					 '34'=>'Large Tv > 40',		'35'=>'Light/Bulky',			'36'=>'Limousine',
//					 '37'=>'Mini Mobile Home',	'38'=>'Motorbike',				'39'=>'Motorcycle',
//					 '40'=>'Piano',				'41'=>'Piano, Concert',			'42'=>'Piano, Grand',
//					 '43'=>'Piano, Spinet',		'44'=>'Piano, Upright',			'45'=>'Piano, Baby Grand',
//					 '46'=>'Pickup & Camper',	'47'=>'Pickup Truck',			'48'=>'Playhouse',
//					 '49'=>'Riding Mower',		'50'=>'Rowboat < 14 Ft',		'51'=>'Rowboat > 14 Ft',
//					 '52'=>'Satellite Dish',	'53'=>'Scull > 14 Ft',			'54'=>'Skiff < 14 Ft',
//					 '55'=>'Skiff > 14 Ft',		'56'=>'Snow Mobile',			'57'=>'Spa',
//					 '58'=>'Spa > 65 Cu Ft',	'59'=>'Tool Shed',				'60'=>'Tractor < 25HP',
//					 '61'=>'Tractor > 25HP',	'62'=>'Trailer < 14 Ft',		'63'=>'Trailer > 14 Ft',
//					 '64'=>'TV/Radio Dish',		'65'=>'Utility Shed',			'66'=>'Utility Truck',
//					 '67'=>'Van',				'68'=>'Whirlpool Bath',			'69'=>'Whirlpool > 65 Cu',
//					 '70'=>'Windsurfer < 14 Ft','71'=>'Windsurfer > 14 Ft');
    }
    
    public function getBulkyItems()
    {
        $recordId = $this->getId();
        $labels = $this->getBulkyLabels();
        $quantities = array();
        $bulkyItems = array();
        $db = PearDatabase::getInstance();
        
        $sql = "SELECT * FROM `vtiger_bulky_items` WHERE quoteid=?";
        $params[] = $recordId;
        
        $result = $db->pquery($sql, $params);
        unset($params);
        
        while ($row =& $result->fetchRow(DB_FETCHMODE_ASSOC)) {
            $quantities[$row['bulkyid']] = $row['ship_qty'];
        }
        
        foreach ($labels as $itemId=>$itemLabel) {
            $quantity = (array_key_exists($itemId, $quantities)) ? $quantities[$itemId]:'0';
            $bulkyItems[$itemId] = array('label'=>$itemLabel, 'qty'=>$quantity);
        }
        
        return $bulkyItems;
    }
    
    public function getMiscCharges()
    {
        $recordId = $this->getId();
        $charges = array();
        $flatSequence = 0;
        $qtyRateSequence = 0;
        $type = '';
        
        $db = PearDatabase::getInstance();
        $sql = 'SELECT description, charge, qty, discounted, discount, charge_type, line_item_id FROM vtiger_misc_accessorials WHERE quoteid=?';
        $params[] = $recordId;
        $result = $db->pquery($sql, $params);
        
        while ($row =& $result->fetchRow()) {
            $type = $row[5];
            if ($type == 'flat') {
                $flatSequence++;
                $sequence = $flatSequence;
            } elseif ($type == 'qty') {
                $qtyRateSequence++;
                $sequence = $qtyRateSequence;
            }
            $charges[$type][$sequence]->description = $row[0];
            $charges[$type][$sequence]->charge = $row[1];
            $charges[$type][$sequence]->qty = $row[2];
            $charges[$type][$sequence]->discounted = $row[3];
            $charges[$type][$sequence]->discount = (float)$row[4];
            $charges[$type][$sequence]->lineItemId = $row[6];
        }
        
        return $charges;
    }
    
    public function getCrates()
    {
        $recordId = $this->getId();
        $crates = array();
        $sequence = 0;
        
        $db = PearDatabase::getInstance();
        $sql = "SELECT crateid, description, length, width, height, pack, unpack, ot_pack, ot_unpack, discount, line_item_id FROM `vtiger_crates` WHERE quoteid=?";
        $params[] = $recordId;
        
        $result = $db->pquery($sql, $params);
        
        while ($row =& $result->fetchRow()) {
            $sequence++;
            $crates[$sequence]->crateid = $row[0];
            $crates[$sequence]->description = $row[1];
            $crates[$sequence]->crateLength = $row[2];
            $crates[$sequence]->crateWidth = $row[3];
            $crates[$sequence]->crateHeight = $row[4];
            $crates[$sequence]->pack = $row[5];
            $crates[$sequence]->unpack = $row[6];
            $crates[$sequence]->otpack = $row[7];
            $crates[$sequence]->otunpack = $row[8];
            $crates[$sequence]->discount = $row[9];
            $crates[$sequence]->lineItemId = $row[10];
        }
        
        return $crates;
    }
}
