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
 * Cubesheets Record Model Class
 */

require_once('libraries/opentok.phar');
use OpenTok\OpenTok;
use OpenTok\MediaMode;
use OpenTok\ArchiveMode;

class Cubesheets_Record_Model extends Inventory_Record_Model
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

    public function getTokboxSession()
    {
		$recordId = $this->getId();

		$db = PearDatabase::getInstance();
		$sql = "SELECT tokbox_sessionid FROM vtiger_cubesheets WHERE cubesheetsid=?";
		$result = $db->pquery($sql, array($recordId));
		$row = $result->fetchRow();
        if ($row == null) {
			return false;
		}

        if ($row[0] == null || $row[0] == '') {
			//No TokBox SessionId currently exists for this record - creating
            $sessionId = self::getNewTokboxSession();

            $sql = "UPDATE `vtiger_cubesheets` SET tokbox_sessionid=? WHERE cubesheetsid=?";
            $result = $db->pquery($sql, array($sessionId, $recordId));

            return $sessionId;
        } else {
            return $row[0];
        }
    }

    public static function getNewTokboxSession() {
        $openTok = new OpenTok(getenv('TOKBOX_API_KEY'), getenv('TOKBOX_API_SECRET'));
        $initParams = ['mediaMode' => MediaMode::ROUTED];
        if (getenv('VIDEO_SURVEY_ARCHIVING')) {
            //				$initParams['archiveMode'] = ArchiveMode::ALWAYS;
        }
        $session = $openTok->createSession($initParams);
        $sessionId = $session->getSessionId();
        return $sessionId;
    }

    public function getTokboxServerToken()
    {
		$recordId = $this->getId();

		$db = PearDatabase::getInstance();
		$sql = "SELECT tokbox_servertoken FROM vtiger_cubesheets WHERE cubesheetsid=?";
		$result = $db->pquery($sql, array($recordId));
		$row = $result->fetchRow();

        if ($row == null) {
			return false;
		}

        if ($row[0] == null || $row[0] == '') {
			//No TokBox token currently exists for this record - creating
			$sessionId = $this->getTokboxSession();

            $token = self::getNewTokboxToken($sessionId);

            if ($token) {
                $sql    = "UPDATE `vtiger_cubesheets` SET tokbox_servertoken=? WHERE cubesheetsid=?";
                $result = $db->pquery($sql, [$token, $recordId]);
			}

			return $token;
		} else {
			return $row[0];
		}
	}

    public static function getNewTokboxToken($sessionId)
    {
        $openTok = new OpenTok(getenv('TOKBOX_API_KEY'), getenv('TOKBOX_API_SECRET'));

        if (!$sessionId) {
            return false;
        }

        $token = $openTok->generateToken($sessionId, ['expireTime'=>time()+(60*60*24*30)]);

        return $token;
    }

    public function getTokboxClientToken()
    {
		$recordId = $this->getId();

		$db = PearDatabase::getInstance();
		$sql = "SELECT tokbox_clienttoken FROM vtiger_cubesheets WHERE cubesheetsid=?";
		$result = $db->pquery($sql, array($recordId));
		$row = $result->fetchRow();

        if ($row == null) {
			return false;
		}

        if ($row[0] == null || $row[0] == '') {
			//No TokBox token currently exists for this record - creating
			$sessionId = $this->getTokboxSession();

            $token = self::getNewTokboxToken($sessionId);

            if ($token) {
                $sql    = "UPDATE `vtiger_cubesheets` SET tokbox_clienttoken=? WHERE cubesheetsid=?";
                $result = $db->pquery($sql, [$token, $recordId]);
			}

			return $token;
		} else {
			return $row[0];
		}
	}

    public function getDeviceCode()
    {
		$recordId = $this->getId();

		$db = PearDatabase::getInstance();

        $deviceCode = self::getNewUniqueDeviceCode();

        $sql = "UPDATE `vtiger_cubesheets` SET tokbox_devicecode=? WHERE cubesheetsid=?";
        $result = $db->pquery($sql, array($deviceCode, $recordId));

        return $deviceCode;
    }

    public static function getNewUniqueDeviceCode()
    {
        $db = PearDatabase::getInstance();

		$uniqueCodeFound = false;

        while (!$uniqueCodeFound) {
            $deviceCode = getenv('TOKBOX_PREFIX').self::getNewCode();

			$sql = "SELECT * FROM `vtiger_cubesheets` WHERE tokbox_devicecode=?";
			$result = $db->pquery($sql, array($deviceCode));
			$row = $result->fetchRow();
            if ($row == null) {
				$uniqueCodeFound = true;
			}
		}

		return $deviceCode;
	}

    protected static function getNewCode()
    {
		$charset = "0123456789";
		$code = '';
		$count = strlen($charset);
        for ($i=0; $i<6; $i++) {
			$code .= $charset[mt_rand(0, $count-1)];
		}
		return $code;
	}

    public function setExpirationDate($expirationDateTime)
    {
		$recordId = $this->getId();
		$db = PearDatabase::getInstance();

		$sql = "UPDATE `vtiger_cubesheets` SET tokbox_code_expiration=? WHERE cubesheetsid=?";

		$result = $db->pquery($sql, array($expirationDateTime, $recordId));
	}

    // Significantly pared down function to get the total weight, cube, and total items
    public function getCubesheetDetails() {
        $id = $this->getId();

        $details = [];
        $cubesheetWeight = 0;
        $cubesheetCube = 0;
        $cubesheetItems = 0;
        // Still necessary to get _total_ item count
        $cubeSheetIds = [];

        require_once('libraries/nusoap/nusoap.php');
        require_once('includes/main/WebUI.php');
        require_once('include/Webservices/Create.php');
        require_once('modules/Users/Users.php');

        $soapclient = new \soapclient2(getenv('CUBESHEET_SERVICE_URL'), 'wsdl');
        $soapclient->setDefaultRpcParams(true);
        $soapProxy = $soapclient->getProxy();

        $soapResponse = $soapProxy->GetCubesheetDetailsByRelatedRecordId(['relatedRecordID' => (string)$id]);

        if(!isset($soapResponse['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'][0])) {
            $soapResponse['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'] = [0 => $soapResponse['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet']];
        }

        foreach ($soapResponse['GetCubesheetDetailsByRelatedRecordIdResult']['ExtendedCubesheet'] as $segment) {
            $cubesheetWeight += $segment['TotalWeight'];
            $cubesheetCube   += $segment['TotalCube'];
            $cubeSheetIds[]   = $segment['CubeSheetId'];
            $cubesheetItems  += $segment['ItemsShipping'];
        }

//        foreach ($cubeSheetIds as $cubesheetId) {
//            //Use CubesheetId to getSurveyedItems this gives us some basic item info and an ItemId
//            $soapResponse = $soapProxy->getSurveyedItems(['CubeSheetId' => $cubesheetId, 'CubeSheetIdSpecified' => true]);
//
//            $surveyedItems = $soapResponse['GetSurveyedItemsResult']['SurveyedItems'];
//
//            if (!array_key_exists('0', $surveyedItems)) {
//                $surveyedItems = [0 => $surveyedItems];
//            }
//            $cubesheetItems += count($surveyedItems);
//        }

        $details = array('weight' => $cubesheetWeight,
             'cube'   => $cubesheetCube,
             'items'  => $cubesheetItems);

    return $details;
  }

  public function getDetailViewUrl()
  {

      $module = $this->getModule();

      $url = 'index.php?module='.$this->getModuleName().'&view='.$module->getDetailViewName().'&record='.$this->getId();
      if(isset($_REQUEST['relatedModule']) && $_REQUEST['relatedModule'] == 'Cubesheets'){
            $request = New Vtiger_Request($_REQUEST, $_REQUEST);
            $sourceModule = $request->get('module');
            $url = $url . '&sourceModule=' . $sourceModule;
        }

      return $url;
  }

  /**
   * Function to get the complete Detail View url for the record
   * @return <String> - Record Detail View Url
   */
  public function getFullDetailViewUrl()
  {
      $module = $this->getModule();

      $url = 'index.php?module='.$this->getModuleName().'&view='.$module->getDetailViewName().'&record='.$this->getId().'&mode=showDetailViewByMode&requestMode=full';

      if(isset($_REQUEST['relatedModule']) && $_REQUEST['relatedModule'] == 'Cubesheets'){
        $request = New Vtiger_Request($_REQUEST, $_REQUEST);
        $sourceModule = $request->get('module');
        $url = $url . '&sourceModule=' . $sourceModule;
    }

      return $url;
  }
}
