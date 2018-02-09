<?php
/* ********************************************************************************
 * The content of this file is subject to the VTEFavorite ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

class VTEFavorite_Record_Model extends Vtiger_Record_Model {
	/**
	 * Function to get the Id
	 * @return <Number> Id
	 */
	public function getId() {
		return $this->get('user_id');
	}

	/**
	 * Function to get the Node Name
	 * @return <String>
	 */
	public function getName() {
        if($this->getId()!=0){
            return $this->get('first_name').' '.$this->get('last_name');
        }else{
            return $this->get('name');
        }
	}

	/**
	 * Function to get the depth of the role
	 * @return <Number>
	 */
	public function getDepth() {
		return $this->get('depth');
	}
    /**
     * Function to get the imagename of the role
     * @return <String>
     */
    public function getImageName() {
        return $this->get('imagename');
    }
    /**
     * Function to get the path of the role
     * @return <String>
     */
    public function getPath() {
        return $this->get('path');
    }
	/**
	 * Function to get Parent Record hierarchy as a string
	 * @return <String>
	 */
	public function getParentString() {
		return $this->get('parent');
	}
	
	/**
	 * Function to get the immediate parent record
	 * @return <VTEFavorite_Record_Model> instance
	 */
	public function getParent() {
		if(!$this->parent) {
			$parentString = $this->getParentString();
			$parentComponents = explode('::', $parentString);
			$noOfRecords = count($parentComponents);
			// $currentRole = $parentComponents[$noOfRoles-1];
			if($noOfRecords > 1) {
				$this->parent = self::getRecordByID($parentComponents[$noOfRecords-2]);
			} else {
				$this->parent = null;
			}
		}
		return $this->parent;
	}
	
	/**
	 * Function to get the Create Child Record Url for the current record
	 * @return <String>
	 */
	public function getCreateChildUrl() {
		return '?module=VTEFavorite&parent=Settings&view=AddChildren&parent_id='.$this->getId();
	}
	
	/**
	 * Function to get the Delete Action Url for the current record
	 * @return <String>
	 */
	public function getDeleteActionUrl() {
		return '?module=VTEFavorite&parent=Settings&view=DeleteAjax&record='.$this->getId();
	}

	/**
	 * Function to get the instance of Record model from query result
	 * @param <Object> $result
	 * @param <Number> $rowNo
	 * @return Forecast_Record_Model instance
	 */
	public static function getInstanceFromQResult($result, $rowNo) {
		$db = PearDatabase::getInstance();
		$row = $db->query_result_rowdata($result, $rowNo);
		$role = new self();
		return $role->setData($row);
	}	
	/**
	 * Function to get the immediate children roles
	 * @return <Array> - List of Settings_Nodes_Record_Model instances
	 */
	public function getChildren() {
		$db = PearDatabase::getInstance();
		if(!$this->children) {
			$parentString = $this->getParentString();
			$currentRecordDepth = $this->getDepth();

			$sql = 'SELECT a.*, b.first_name, b.last_name, CONCAT(d.attachmentsid,\'_\',d.name) as imagename, d.path
			 FROM forecast_hierarchy a
			 LEFT JOIN vtiger_users b ON a.user_id = b.id
			 LEFT JOIN vtiger_salesmanattachmentsrel c ON a.user_id= c.smid
             LEFT JOIN vtiger_attachments d ON c.attachmentsid = d.attachmentsid
			 WHERE parent LIKE ? AND depth = ?';
			$params = array($parentString.'::%', $currentRecordDepth+1);
			$result = $db->pquery($sql, $params);
			$noOfRecords = $db->num_rows($result);
			$records = array();
			for ($i=0; $i<$noOfRecords; ++$i) {
				$record = self::getInstanceFromQResult($result, $i);
				$records[$record->getId()] = $record;
			}
			$this->children = $records;
		}
		return $this->children;
	}
	
	/**
	 * Function to get the instance of Record model by UserID
	 * @return Forecast_Record_Model instance, if exists. Null otherwise
	 */
	public static function getRecordByID($id) {
		$db = PearDatabase::getInstance();
		$sql = 'SELECT a.*, b.first_name, b.last_name,CONCAT(d.attachmentsid,\'_\',d.name) as imagename, d.path
		FROM forecast_hierarchy a
		LEFT JOIN vtiger_users b ON a.user_id = b.id
		LEFT JOIN vtiger_salesmanattachmentsrel c ON a.user_id= c.smid
        LEFT JOIN vtiger_attachments d ON c.attachmentsid = d.attachmentsid
		WHERE user_id=? LIMIT 1';
		$params = array($id);		
		$result = $db->pquery($sql, $params);
		if($db->num_rows($result) > 0) {
			return self::getInstanceFromQResult($result, 0);
		}
		return null;
	}
	
	/**
	 * Function to add children
	 * @return VTEFavorite_Record_Model instance, if exists. Null otherwise
	 */
	public function addChildren($childrenId) {
		$db = PearDatabase::getInstance();
		
		$user_name = $this->getUserNameById($childrenId);
		$parrentString = $this->getParentString()."::".$childrenId;
		$depth = $this->getDepth() + 1;		
		$sql = 'INSERT INTO forecast_hierarchy(user_id, name, parent, depth) VALUES (?,?,?,?)';
		// echo $sql;
		// exit();
		$params = array($childrenId, $user_name, $parrentString, $depth);
		
		$db->pquery($sql, $params);		
		return null;
	}
	/**
	 * Function to add children
	 * @return vTEForecast_Record_Model instance, if exists. Null otherwise
	 */
	public function deleteChildren($childrenId) {
		$db = PearDatabase::getInstance();		
		$db->pquery("DELETE FROM forecast_hierarchy WHERE parent like '%::$childrenId::%'", array());
		$db->pquery('DELETE FROM forecast_hierarchy WHERE user_id=?', array($childrenId));
		return null;
	}
	
	/**
	 * Function to get the instance of Base Record model
	 * @return VTEFavorite_Record_Model instance, if exists. Null otherwise
	 */
	public static function getBaseRecord() {
		$db = PearDatabase::getInstance();

		$sql = 'SELECT a.*, b.first_name, b.last_name, b.imagename,d.logoname as imagename, \'test/logo/\' as path
        FROM forecast_hierarchy a LEFT JOIN vtiger_users b ON a.user_id = b.id
LEFT JOIN vtiger_organizationdetails d ON d.organization_id=1
		WHERE depth=0 LIMIT 1';
		
		$params = array();
		$result = $db->pquery($sql, $params);
		if($db->num_rows($result) > 0) {
			return self::getInstanceFromQResult($result, 0);
		}
		return null;
	}
	/**
	 * Function to get all the record
	 * @param <Boolean> $baseRecord
	 * @return <Array> list of Record models <Settings_Nodes_Record_Model>
	 */
	public static function getAll($baseRecord = false) {
		global $adb;
		global $current_user;
		
		//Get param:
        $userid=$current_user->user_name;
		//Get favorite of record:
		$emparray = array();
		$result = $adb->pquery('select * from `vte_favorite_records` ORDER BY module,stars DESC,`update` DESC;', array());
		//$myrow = $adb->fetch_array($result);
		$i=0;//resultrow
		while($row =$adb->fetch_array($result))
		{
			$emparray[$i] =array('recordname'=>$row['module'].' > '.$row['view'].' > '.$row['recordname'],'url'=>$row['url'],'stars'=>$row['stars']) ;
			$i = $i+1;
		}
		return $emparray;
	}

	/**
    * Function to get UserNames by ID
    * @return string
    */
	public function getUserNameById($user_id) {
		$db = PearDatabase::getInstance();	   
		$params = array('Active',$user_id);	
		$result = $db->pquery('SELECT first_name, last_name FROM vtiger_users WHERE status=? AND id=?', $params);
		if($db->num_rows($result) > 0) {
			$ret = $db->query_result($result, 0, 'first_name').' '.$db->query_result($result, 0, 'last_name') ;
		}	   
		return $ret;
   }
   
   
   
   
   // public static function getFieldsOfModule($moduleName) {
		// $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
		// $fieldModelList = $moduleModel->getFields();       
		// return $fieldModelList;
	// }
}