<?php
class TariffSections_Record_Model extends Vtiger_Record_Model
{
    public static function getDiscount($estimateId, $sectionId)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT discount_percent FROM `vtiger_quotes_sectiondiscount` WHERE estimateid=? AND sectionid=?";
        $result = $db->pquery($sql, array($estimateId, $sectionId));

        $row = $result->fetchRow();
        if ($row == null) {
            return null;
        }
        $sectionDiscount = $row[0];

        return $sectionDiscount;
    }

    public static function getDiscounts($estimateId) {
        $db = PearDatabase::getInstance();

        $sql = "SELECT `sectionid`, `discount_percent` FROM `vtiger_quotes_sectiondiscount` WHERE `estimateid`=?";
        $result = $db->pquery($sql, array($estimateId));

        $sectionDiscount = [];
        while($row = $result->fetchRow()) {
            $sectionDiscount[] = $row;
        }

        return $sectionDiscount;
    }
}
