<?php

class Tariffs_Record_Model extends Vtiger_Record_Model
{
    public function getTariffDetails($currentDate = null)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT tariffsectionsid FROM `vtiger_tariffsections`
                  JOIN `vtiger_crmentity` ON tariffsectionsid=crmid
                  WHERE related_tariff=? AND deleted=0
                  ORDER BY tariffsection_sortorder,tariffsectionsid";
        $result = $db->pquery($sql, array($this->getId()));

        $tariffDetails = array('sections'=>array(), 'effectiveDate'=>null);

        while ($row =& $result->fetchRow()) {
            try {
                $tariffSectionRecord         = Vtiger_Record_Model::getInstanceById($row['tariffsectionsid']);
                $tariffDetails['sections'][] =
                    [
                        'id'                           => $tariffSectionRecord->getId(),
                        'name'                         => $tariffSectionRecord->get('section_name'),
                        'is_discountable'              => $tariffSectionRecord->get('is_discountable'),
                        'bottomline_discount_override' => $tariffSectionRecord->get('bottomline_discount_override'),
                        'services'                     => []
                    ];
            } catch (Exception $ex) {
                //skip this invalid record.
            }
        }
        if (empty($currentDate)) {
            $currentDate = new DateTime('now');//we can just use now. It will not matter to the system. Now is also the correct format
            //for getting the current date - 5/31/2016
        } else {
            $nums = explode('-', $currentDate);
            $year = $nums[0];
            $month = $nums[1];
            $day = $nums[2];
            $tempDate = new DateTime();//we can just use now. It will not matter to the system. Now is also the correct format
            //for getting the current date - 5/31/2016
            $tempDate->setDate((float)$year, (float)$month, (float)$day);
            $currentDate = $tempDate;
        }

        $effectiveDate = null;

        $sql = "SELECT effectivedatesid, effective_date FROM `vtiger_effectivedates`
                JOIN `vtiger_crmentity` ON effectivedatesid=crmid
                WHERE  related_tariff=? and deleted=0";
        $result = $db->pquery($sql, array($this->getId()));

        while ($row =& $result->fetchRow()) {
            $date = new DateTime($row['effective_date']);

            if ((empty($tariffDetails['effectiveDate']) && $date < $currentDate) || ($date < $currentDate && $date > $effectiveDate)) {
                $tariffDetails['effectiveDate'] = $row['effectivedatesid'];
                $effectiveDate = $date;
            }
        }
        $numServices = 0;
        foreach ($tariffDetails['sections'] as $index=>$section) {
            $sql = "SELECT tariffservicesid FROM `vtiger_tariffservices`
                    JOIN `vtiger_crmentity` ON tariffservicesid=crmid
                    WHERE tariff_section=? AND effective_date=? AND deleted=0";
            $result = $db->pquery($sql, array($section['id'], $tariffDetails['effectiveDate']));

            while ($row =& $result->fetchRow()) {
                $numServices++;
                $tariffDetails['sections'][$index]['services'][] = TariffServices_Record_Model::getInstanceById($row['tariffservicesid']);
            }
        }
        if($numServices == 0) {
            $tariffDetails['no_service'] = true;
        }

        return $tariffDetails;
    }

    public function getServiceIds($effectiveDateId = null)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT tariffsectionsid FROM `vtiger_tariffsections`
                  JOIN `vtiger_crmentity` ON tariffsectionsid=crmid
                  WHERE related_tariff=? AND deleted=0
                  ORDER BY tariffsection_sortorder,tariffsectionsid";
        $result = $db->pquery($sql, array($this->getId()));

        $sectionIds = array();
        $serviceIds = array();

        while ($row =& $result->fetchRow()) {
            $sectionIds[] = $row[0];
        }

        if ($effectiveDateId === null) {
            foreach ($sectionIds as $section) {
                $sql = "SELECT tariffservicesid FROM `vtiger_tariffservices` JOIN `vtiger_crmentity` ON tariffservicesid=crmid WHERE tariff_section=? AND deleted=0";
                $result = $db->pquery($sql, array($section));
                $serviceIds[$section] = array();

                while ($row =& $result->fetchRow()) {
                    $serviceIds[$section][] = $row[0];
                }
            }
        } else {
            foreach ($sectionIds as $section) {
                $sql = "SELECT tariffservicesid FROM `vtiger_tariffservices` JOIN `vtiger_crmentity` ON tariffservicesid=crmid WHERE tariff_section=? AND effective_date=? AND deleted=0";
//                file_put_contents('logs/devLog.log', "\n sql : ".print_r($sql, true), FILE_APPEND);
//                file_put_contents('logs/devLog.log', "\n section : ".$section." effectiveDateId : ".$effectiveDateId, FILE_APPEND);
                $result = $db->pquery($sql, array($section, $effectiveDateId));
                $serviceIds[$section] = array();

                while ($row =& $result->fetchRow()) {
                    $serviceIds[$section][] = $row[0];
                }
            }
        }
        return $serviceIds;
    }

    public function getServiceDetails($id) {
      $db = PearDatabase::getInstance();

      $sql = "SELECT * FROM `vtiger_tariffservices` WHERE tariffservicesid = ?";
      $result = $db->pquery($sql,array($id));
      return $result->fetchRow();
    }

    private function getService($date, $serviceType) {

      $db = PearDatabase::getInstance();

      $sql = "SELECT * FROM `vtiger_tariffservices`
              WHERE `vtiger_tariffservices`.related_tariff = ?
              AND `vtiger_tariffservices`.effective_date = ?
              AND `vtiger_tariffservices`.rate_type = ?
              LIMIT 1";

      $result = $db->pquery($sql,array($this->getId(), $date, $serviceType));

      $result = $result->fetchRow();
      return $result['tariffservicesid'];
    }
    // Like above, but it returns all services of that type
    private function getServices($date, $serviceType) {

      $db = PearDatabase::getInstance();

      $sql = "SELECT * FROM `vtiger_tariffservices`
              WHERE `vtiger_tariffservices`.related_tariff = ?
              AND `vtiger_tariffservices`.effective_date = ?
              AND `vtiger_tariffservices`.rate_type = ?";

      $result = $db->pquery($sql,array($this->getId(), $date, $serviceType));
      while($row =&$result->fetchRow()) {
        $results[] = $row;
      }
      return $results;
    }
    /**
     * Takes a date ('Y-m-d') or uses today to get the current working
     * effective date id for a tariff
     *
     * @param     String $date - the (formatted or non) date
     * @return    Integer
    */
    public function getEffectiveDate($date = null) {
      if ($date == null) {
        $date = date('Y-m-d');
      } else {
        $date = new DateTime($date);
        $date = $date->format('Y-m-d');
      }


      $sql = "SELECT effectivedatesid, effective_date FROM `vtiger_effectivedates`
              JOIN `vtiger_crmentity` ON effectivedatesid=crmid
              WHERE  related_tariff=?
              AND effective_date <= ?
              AND deleted=0
              ORDER BY effective_date DESC
              LIMIT 1";
      $db = PearDatabase::getInstance();
      $result = $db->pquery($sql,array($this->getId(),$date));
      return $result->fetchRow()['effectivedatesid'];
    }

    public function getBulkyService($date = null) {
      if($date == null) {
        $date = $this->getEffectiveDate();
      }
      return $this->getService($date, 'Bulky List');
    }

    public function getPackingService($date = null) {
      if($date == null) {
        $date = $this->getEffectiveDate();
      }
      return $this->getService($date, 'Packing Items');
    }

    public function getAllPackingServices($date = null) {
      if($date == null) {
        $date = $this->getEffectiveDate();
      }
      return $this->getServices($date, 'Packing Items');
    }

    public function getCratingService($date = null) {
      if($date == null) {
        $date = $this->getEffectiveDate();
      }
      return $this->getService($date, 'Crating Item');
    }
}
