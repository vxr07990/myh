<?php
/**
 * Created by PhpStorm.
 * User: mmuir
 * Date: 9/7/2017
 * Time: 12:26 PM
 */
class WFArticles_Module_Model extends Vtiger_Module_Model {

    function getDuplicateCheckFields() {
        return Zend_Json::encode(['wfaccount', 'article_num']);
    }

}

