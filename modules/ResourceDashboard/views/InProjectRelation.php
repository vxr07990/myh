<?php

/**
 * Resource Management Module
 * @package        ResourceDashboard Module
 * @author         Conrado Maggi - www.vgsglobal.com
 */
class ResourceDashboard_InProjectRelation_View extends Vtiger_Index_View
{
    public function process(Vtiger_Request $request)
    {
        $projectId      = $request->get('record');
        $moduleModel    = Vtiger_Module_Model::getInstance('ResourceDashboard');
        $resourcesArray = $moduleModel->getResourceRelatedProject($projectId);
        $html           = '<div class="relatedContents contents-bottomscroll">'
                          .'<div class="bottomscroll-div" style="width: 1098px;">'
                          .'<table class="table table-bordered listViewEntriesTable">'
                          .'<thead><tr class="listViewHeaders">'
                          .'<th nowrap="">Resource Type&nbsp;&nbsp;</th>'
                          .'<th nowrap="">Resource Name&nbsp;&nbsp;</th>'
                          .'<th nowrap="">Allocated Quantity&nbsp;&nbsp;</th>'
                          .'</tr></thead>'
                          .'<tbody>';
        foreach ($resourcesArray as $resource) {
            $html .= '<tr>';
            $html .= '<td>'.$resource['setype'].'</td>';
            $html .= '<td>'.$resource['name'].'</td>';
            $html .= '<td>'.$resource['quantity'].'</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></div></div>';

        return $html;
    }
}
