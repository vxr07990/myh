<?php

/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class AgentManager_Record_Model extends Vtiger_Record_Model
{

    /**
     * Function to get Images Data
     * @return <Array> list of Image names and paths
     */
    public function getImageDetails()
    {
        $db               = PearDatabase::getInstance();
        $imageDetails     = [];
        $recordId         = $this->getId();
        if ($recordId) {
            $query     = "SELECT vtiger_attachments.* FROM vtiger_attachments
            LEFT JOIN vtiger_salesmanattachmentsrel ON vtiger_salesmanattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
            WHERE vtiger_salesmanattachmentsrel.smid=?";
            $result    = $db->pquery($query, [$recordId]);
            $imageId   = $db->query_result($result, 0, 'attachmentsid');
            $imagePath = $db->query_result($result, 0, 'path');
            $imageName = $db->query_result($result, 0, 'name');
            //decode_html - added to handle UTF-8 characters in file names
            $imageOriginalName = decode_html($imageName);
            $imageDetails[]    = [
                'id'      => $imageId,
                'orgname' => $imageOriginalName,
                'path'    => $imagePath.$imageId,
                'name'    => $imageName,
            ];
        }

        //so this an array of images... even though there can ONLY be one.
        //But that's what the tpl expects and well so it goes.
        return $imageDetails;
    }

    /**
     * Function to delete corresponding image
     *
     * @param <type> $imageId
     */
    public function deleteImage($imageId)
    {
        $db          = PearDatabase::getInstance();
        $checkResult = $db->pquery('SELECT smid FROM vtiger_salesmanattachmentsrel WHERE attachmentsid = ?', [$imageId]);
        $smId        = $db->query_result($checkResult, 0, 'smid');
        if ($this->getId() === $smId) {
            $db->pquery('DELETE FROM vtiger_attachments WHERE attachmentsid = ?', [$imageId]);
            $db->pquery('DELETE FROM vtiger_salesmanattachmentsrel WHERE attachmentsid = ?', [$imageId]);
            return true;
        }
        return false;
    }

    public function getBrand($vanlineID)
    {
        $brand = null;

        if (!$vanlineID) {
            $vanlineID = $this->get('vanline_id');
        }

        $db = PearDatabase::getInstance();
        $sql = "SELECT vtiger_vanlinemanager.vanline_id, vtiger_vanlinemanager.vanline_name FROM `vtiger_vanlinemanager`
           JOIN `vtiger_crmentity` ON vanlinemanagerid=crmid
           WHERE `vtiger_vanlinemanager`.vanlinemanagerid=? AND deleted=0";
        $result      = $db->pquery($sql, [$vanlineID]);
        $row         = $result->fetchRow();
        $retVanlineName = $row['vanline_name'];
        $retVanlineId   = $row['vanline_id'];

        if (getenv('INSTANCE_NAME') == 'sirva') {
            //@TODO: this is a bold assumption...
            if ($retVanlineName == 'Allied' || $retVanlineId == 1) {
                $brand = 'AVL';
            } elseif ($retVanlineName == 'North American Van Lines' || $retVanlineId == 9) {
                $brand = 'NAVL';
            }
        }

        return $brand;
    }

    public function getCoordinators()
    {
        $db = PearDatabase::getInstance();
        $agentManagerId = $this->getId();
        $sql    = "SELECT vtiger_users.id, vtiger_users.first_name, vtiger_users.last_name
                            FROM `vtiger_users`
                            JOIN `vtiger_user2role` ON vtiger_users.id=vtiger_user2role.userid
                            WHERE (agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids = ?)
                            AND vtiger_users.deleted = 0
                            AND vtiger_users.status = 'Active'
                            AND roleid IN (?,?,?)";
        if (getenv('INSTANCE_NAME') == 'sirva') {
            //@TODO: 2017-04-28 7p request to "reverse sort on firstname"
            $sql .= ' ORDER BY vtiger_users.first_name DESC';
        }
        $result = $db->pquery($sql, ['% '.$agentManagerId, '% '.$agentManagerId.' %', $agentManagerId.' %', $agentManagerId, 'H9', 'H10', 'H11']);

        while ($row =& $result->fetchRow()) {
            $coordinators[] = ['id' => $row['id'], 'first_name' => $row['first_name'], 'last_name' => $row['last_name']];
        }

        return $coordinators;
    }

    public function getCostCenters()
    {
      $coordinators = [];

      if(!checkIsWindfallActive()) {
        return $coordinators;
      }

      $db = PearDatabase::getInstance();
      $agentManagerId = $this->getId();
      $sql = "SELECT vtiger_wfcostcenters.* FROM `vtiger_wfcostcenters`
              JOIN `vtiger_crmentity` ON `vtiger_wfcostcenters`.wfcostcentersid = `vtiger_crmentity`.crmid
              WHERE `vtiger_crmentity`.agentid = ?";

      $result = $db->pquery($sql,[$agentManagerId]);

      while ($row =& $result->fetchRow()) {
        $coordinators[$row['id']] = $row['code'];
      }
      return $coordinators;
    }
    public function getUsersByAgency() {
      $db = PearDatabase::getInstance();
      $agentManagerId = $this->getId();
      // I'm pretty genuinely sorry about this
      $sql = "SELECT * FROM vtiger_users WHERE (agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids LIKE ? OR agent_ids = ?) AND status = 'Active'";
      $results = $db->pquery($sql,['% '.$agentManagerId, '% '.$agentManagerId.' %', $agentManagerId.' %', $agentManagerId]);
      $users = [];

      if($db->num_rows($results) > 0) {
        while($row = $results->fetchRow()){
            $users[$row['id']] = [
                                  "id"         => $row['id'],
                                  "first_name" => $row['first_name'],
                                  "last_name"  => $row['last_name']
                                 ];
        }
      }
      return $users;
    }

    public function getAddress() {
      return $this->get('address1') . ', ' . $this->get('city') . ', ' . $this->get('state') . ', ' . $this->get('zip');
    }
}
