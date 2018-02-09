<?php

class Cubesheets_InCubesheetsRelation_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $db = PearDatabase::getInstance();
        $sql = "SELECT archiveid, created_at FROM `vtiger_tokbox_archives` JOIN `vtiger_cubesheets` ON sessionid=tokbox_sessionid WHERE cubesheetsid=?";
        $result = $db->pquery($sql, [$request->get('record')]);

        $html         = '<div class="relatedContainer">'
                        .'<input type="hidden" name="relatedModuleName" class="relatedModuleName" value="AgentManager" />'
                        .'<div class="relatedHeader">'
                        .'<div class="btn-toolbar row-fluid">'
                        .'<div class="span6">'
                        .'&nbsp;</div>'
                        .'</div>'
                        .'</div>'
                        .'<div class="relatedContents contents-bottomscroll">'
                        .'<div class="bottomscroll-div">'
                        .'<table class="table table-bordered listViewEntriesTable">'
                        .'<thead><tr class="listViewHeaders">'
                        .'<th nowrap="">Archive ID&nbsp;&nbsp;</th>'
                        .'<th nowrap="">Created Time&nbsp;&nbsp;</th>'
                        .'</tr></thead>'
                        .'<tbody>';
        while ($row =& $result->fetchRow()) {
            $html .= '<tr class="listViewEntries" data-id="'.$row['id'].'" data-recordurl="index.php?module=Cubesheets&action=ViewArchive&archive='.$row['archiveid'].'">';
            $html .= '<td>'.$row['archiveid'].'</td>';
            $html .= '<td>'.$row['created_at'].'</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></div></div></div>';

        return $html;
    }
}
