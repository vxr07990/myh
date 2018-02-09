<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/**
 * Class QuotingTool_Record_Model
 */
class QuotingTool_Record_Model extends Vtiger_Record_Model
{
    /**
     * Function to get the Detail View url for the record
     * @return string - Record Detail View Url
     */
    public function getDetailViewUrl()
    {
        return 'index.php?module=QuotingTool&view=Edit&record=' . $this->getId();
    }

    /**
     * @return array
     */
    static function findAll()
    {
        $db = PearDatabase::getInstance();
        $instances = array();
        $rs = $db->pquery("SELECT * FROM `vtiger_quotingtool` WHERE `deleted` != 1");
        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return $instances;
    }

    /**
     * @param $id
     * @return Vtiger_Record_Model
     */
    public function getById($id)
    {
        $db = PearDatabase::getInstance();
        $instances = array();
        $sql = "SELECT * FROM vtiger_quotingtool AS quotingtool
                INNER JOIN vtiger_crmentity AS entity ON (entity.crmid = quotingtool.id) WHERE quotingtool.id=? AND entity.deleted = 0 LIMIT 1";
        $params = array($id);
        $rs = $db->pquery($sql, $params);
        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return (count($instances) > 0) ? $instances[0] : null;
    }

    /**
     * @param string $module
     * @param int $agentid
     * @return array
     */
    public function findByModule($module, $agentid = 0)
    {
        $db = PearDatabase::getInstance();
        $instances = array();
        $sqlSelect = "SELECT * FROM vtiger_quotingtool AS quotingtool
                INNER JOIN vtiger_crmentity AS entity ON (entity.crmid = quotingtool.id) ";
        $sqlWhere = " WHERE quotingtool.module LIKE ? AND entity.deleted = 0 ";
        $sqlOrder = " ORDER BY quotingtool.filename ";
        $params = array();
        $params[] = $module;

        if ($agentid) {
            $sqlWhere .= ' AND entity.agentid = ? ';
            $params[] = $agentid;
        }
        $sql = $sqlSelect . $sqlWhere . $sqlOrder;
        $rs = $db->pquery($sql, $params);

        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return $instances;
    }

    /**
     * @param int $entityId
     * @param array $fields
     * @param array $options
     * @return array
     */
    public function decompileRecord($entityId = 0, $fields = array(), $options = array()) {
        $quotingTool = new QuotingTool();

        if (!empty($fields)) {
            foreach ($fields as $field) {
                switch ($field) {
                    case 'header':
                    case 'content':
                    case 'footer':
                    case 'email_subject':
                    case 'email_content':
                        $tmp = $this->get($field);
                        $tmp = $tmp ? base64_decode($tmp) : '';
                        if ($entityId) {
                            $tmp = $quotingTool->parseTokens($tmp, $this->get('module'), $entityId);
                        }

                        $this->set($field, $tmp);
                        break;
                    default:
                        break;
                }
            }
        }

        return $this;
    }

}
