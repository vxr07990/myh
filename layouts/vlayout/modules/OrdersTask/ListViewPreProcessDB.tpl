{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}

{strip}
    {include file="Header.tpl"|vtemplate_path:$MODULE}
    {include file="BasicHeader.tpl"|vtemplate_path:$MODULE}

    <div class="bodyContents">
        <div class="mainContainer row-fluid">
            {assign var=LEFTPANELHIDE value=1} {* @conrado: Left pane hide by default *}
            <div class="span2 row-fluid {if $LEFTPANELHIDE eq '1'} hide {/if}" id="leftPanel" style="min-height:550px;">
                {include file="ListViewSidebar.tpl"|vtemplate_path:$MODULE}
            </div>

            <div class="contentsDiv {if $LEFTPANELHIDE neq '1'} span10 {/if}marginLeftZero" id="rightPanel" style="min-height:550px;">
                <div id="toggleButton" class="toggleButton" title="{vtranslate('LBL_LEFT_PANEL_SHOW_HIDE', 'Vtiger')}">
                    <i id="tButtonImage" class="{if $LEFTPANELHIDE neq '1'}icon-chevron-left{else}icon-chevron-right{/if}"></i>
                </div>
                {* include file="ListViewHeader.tpl"|vtemplate_path:$MODULE *}

                <div class="localDispatchDayBook row-fluid" style="padding-top: 1.5%;">

                    <div class="span2 input-append row-fluid">
                        <div class="span6 pull-right-db"><span class="db-button fc-button-prev fc-state-default"><span class="fc-button-inner"><span class="fc-button-content">&nbsp;◄&nbsp;</span><span class="fc-button-effect"><span></span></span></span></span></div>
                        <div class="span6"><span class="db-button fc-button-next fc-state-default"><span class="fc-button-inner"><span class="fc-button-content">&nbsp;►&nbsp;</span><span class="fc-button-effect"><span></span></span></span></span></div>
                    </div>
                    <div class="span3" style="text-align: center;"><h3><span id="selected_date"></span></h3></div>
                    <div class="span1" style="text-align: center;">

                    </div>
                    <div class="span6 input-append row-fluid" style="float: right;">
                        <div class="buttons span7 row-fluid" style="float: right;">
                            <div class="span3"><span class="btn" id="one-day">1 Day</span></div>
                            <div class="span3"><span class="btn" id="next-7-days">7 Days</span></div>
                            <div class="span3"><span class="btn" id="next-30-days">30 Days</span></div>

                        </div>
                                                <div class="date span5"  style="float: right;"><input id="filter_date" type="text" class="span2 dateField" name="filter" data-date-format="{$CURRENT_USER->date_format}" value=""><span class="add-on"><i class="icon-calendar"></i></span></div>

                    </div>
                   <!-- <div class="span2"><button class="btn exportExcel" name="Export Excel"><strong>{vtranslate('Export Excel',$MODULE)}</strong></button></div>-->

                </div>    
                <input type="hidden" id="selected_date_input">
                <input type="hidden" id="selected_task_id" value="">
                <input type="hidden" id="task_cache" value="">
                <input type="hidden" id="days" value="">


            {/strip}