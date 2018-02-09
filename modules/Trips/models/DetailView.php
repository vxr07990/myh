<?php

class Trips_DetailView_Model extends Vtiger_DetailView_Model
{
    /**
     * Function to get the detail view links (links and widgets)
     * @param <array> $linkParams - parameters which will be used to calicaulate the params
     * @return <array> - array of link models in the format as below
     *                   array('linktype'=>list of link models);
     */
    public function getDetailViewLinks($linkParams)
    {
        $links = parent::getDetailViewLinks($linkParams);
        
        foreach ($links['DETAILVIEW'] as $key => $link) {
           if($link->linklabel ==  'LBL_DUPLICATE'){
               unset($links['DETAILVIEW'][$key]);
           }
        }
        
        return $links;
    }

}