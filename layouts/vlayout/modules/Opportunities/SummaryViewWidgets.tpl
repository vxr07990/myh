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
{foreach item=DETAIL_VIEW_WIDGET from=$DETAILVIEW_LINKS['DETAILVIEWWIDGET']}
	{if ($DETAIL_VIEW_WIDGET->getLabel() eq 'Documents') }
		{assign var=DOCUMENT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'LBL_RELATED_CONTACTS')}
		{assign var=CONTACT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'LBL_RELATED_PRODUCTS')}
		{assign var=PRODUCT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
    {elseif (($DETAIL_VIEW_WIDGET->getLabel() eq 'Move Roles'))}
        {assign var=MOVEROLES_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'LBL_RELATED_ESTIMATES')}
		{assign var=ESTIMATES_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'ModComments')}
		{assign var=COMMENTS_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'LBL_UPDATES')}
		{assign var=UPDATES_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{/if}
{/foreach}

<div class="row-fluid">
	<div class="span7">
		{* Module Summary View*}
			<div class="summaryView row-fluid">
				{$MODULE_SUMMARY}
			</div>
		{* Module Summary View Ends Here*}

		{* Summary View Comments Widget*}
		{if $COMMENTS_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_comments" data-url="{$COMMENTS_WIDGET_MODEL->getUrl()}" data-name="{$COMMENTS_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<input type="hidden" name="relatedModule" value="{$COMMENTS_WIDGET_MODEL->get('linkName')}" />
						<span class="span9 margin0px"><h4>{vtranslate($COMMENTS_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>
						<span class="span3">
							<span class="pull-right">
								{if $COMMENTS_WIDGET_MODEL->get('action')}
									<button class="btn addButton createRecord" type="button" data-url="{$COMMENTS_WIDGET_MODEL->get('actionURL')}">
										<strong>{vtranslate('LBL_ADD',$MODULE_NAME)}</strong>
									</button>
								{/if}
							</span>
						</span>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Comments Widget Ends Here *}
		{* {$MODULE}<br>
		{$RECORD->getId()} *}
		{* Summary View Products Widget*}
		{if $PRODUCT_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_products" data-url="{$PRODUCT_WIDGET_MODEL->getUrl()}" data-name="{$PRODUCT_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<input type="hidden" name="relatedModule" value="{$PRODUCT_WIDGET_MODEL->get('linkName')}" />
						<span class="span9 margin0px"><h4>{vtranslate($PRODUCT_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>
						<span class="span3">
							<span class="pull-right">
								{if $PRODUCT_WIDGET_MODEL->get('action') && isPermitted($MODULE, 'EditView', $RECORD->getId()) eq 'yes'}
									<button class="btn addButton createRecord" type="button" data-url="{$PRODUCT_WIDGET_MODEL->get('actionURL')}">
										<strong>{vtranslate('LBL_ADD',$MODULE_NAME)}</strong>
									</button>
								{/if}
							</span>
						</span>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Products Widget Ends Here*}
	</div>
	<div class='span5' style="overflow: hidden">
		{* Summary View Related Activities Widget *}
			<div id="relatedActivities">
				{$RELATED_ACTIVITIES}
			</div>
		{* Summary View Related Activities Widget Ends Here *}
		{* Summary View Contacts Widget *}
		{if $CONTACT_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_contacts" data-url="{$CONTACT_WIDGET_MODEL->getUrl()}" data-name="{$CONTACT_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<input type="hidden" name="relatedModule" value="{$CONTACT_WIDGET_MODEL->get('linkName')}" />
						<span class="span9 margin0px"><h4>{vtranslate($CONTACT_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>
						<span class="span3">
							<span class="pull-right">
								{if $CONTACT_WIDGET_MODEL->get('action') && isPermitted($MODULE, 'EditView', $RECORD->getId()) eq 'yes'}
									<button class="btn addButton createRecord" type="button" data-url="{$CONTACT_WIDGET_MODEL->get('actionURL')}">
										<strong>{vtranslate('LBL_ADD',$MODULE_NAME)}</strong>
									</button>
								{/if}
							</span>
						</span>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Contacts Widget Ends Here *}

		{* Summary View Estimates Widget *}
		{if $ESTIMATES_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_estimates" data-url="{$ESTIMATES_WIDGET_MODEL->getUrl()}" data-name="{$ESTIMATES_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<input type="hidden" name="relatedModule" value="{$ESTIMATES_WIDGET_MODEL->get('linkName')}" />
						<span class="span9 margin0px"><h4>{vtranslate($ESTIMATES_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>
						<span class="span3">
							<span class="pull-right">
								{if $ESTIMATES_WIDGET_MODEL->get('action') && isPermitted($MODULE, 'EditView', $RECORD->getId()) eq 'yes'}
									<button class="btn addButton createRecord" type="button" data-url="{$ESTIMATES_WIDGET_MODEL->get('actionURL')}">
										<strong>{vtranslate('LBL_ADD',$MODULE_NAME)}</strong>
									</button>
								{/if}
							</span>
						</span>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Contacts Estimates Ends Here *}


		{* Summary View Documents Widget*}
		{if $DOCUMENT_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_documents" data-url="{$DOCUMENT_WIDGET_MODEL->getUrl()}" data-name="{$DOCUMENT_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<input type="hidden" name="relatedModule" value="{$DOCUMENT_WIDGET_MODEL->get('linkName')}" />
						<span class="span9 margin0px"><h4>{vtranslate($DOCUMENT_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>
						<span class="span3">
							<span class="pull-right">
								{if $DOCUMENT_WIDGET_MODEL->get('action') && isPermitted($MODULE, 'EditView', $RECORD->getId()) eq 'yes'}
									<button class="btn pull-right addButton createRecord" type="button" data-url="{$DOCUMENT_WIDGET_MODEL->get('actionURL')}">
										<strong>{vtranslate('LBL_ADD',$MODULE_NAME)}</strong>
									</button>
								{/if}
							</span>
						</span>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Documents Widget Ends Here *}
        {* Summary View Move Roles Widget*}
        {if $MOVEROLES_WIDGET_MODEL}
            <div class="summaryWidgetContainer">
				<div class="widgetContainer_moveroles"  data-name="{$MOVEROLES_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<span class="span9"><h4>{vtranslate($MOVEROLES_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>
					</div>
					<div class="moveroles_widget_contents">
                        {include file=vtemplate_path('MoveRolesSummaryWidgetContents.tpl', 'Vtiger') GUEST_MODULE='MoveRoles'}
					</div>
				</div>
			</div>
        {/if}
        {* Summary View Move Roles Widget Ends Here*}
		{* Summary View Updates Widget *}
		{if $UPDATES_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_updates" data-url="{$UPDATES_WIDGET_MODEL->getUrl()}" data-name="{$UPDATES_WIDGET_MODEL->getLabel()}">
					<div class="widget_header row-fluid">
						<input type="hidden" name="relatedModule" value="{$UPDATES_WIDGET_MODEL->get('linkName')}" />
						<span class="span9 margin0px"><h4>{vtranslate($UPDATES_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4></span>
						<span class="span3">
							<span class="pull-right">
								{if $UPDATES_WIDGET_MODEL->get('action')}
									<button class="btn pull-right addButton createRecord" type="button" data-url="{$UPDATES_WIDGET_MODEL->get('actionURL')}">
										<strong>{vtranslate('LBL_ADD',$MODULE_NAME)}</strong>
									</button>
								{/if}
							</span>
						</span>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Summary View Updates Widget Ends Here *}
	</div>
</div>
{if $ENABLE_CALENDAR}
 {literal}
        <style type="text/css">
            .fc-content td:hover {
                background: none;
            }

            .calendar-wrapper{
                margin-top: 5%;
            }

            .user-column{
                margin-top: 5%;
            }
            #calendarview-feeds{
                padding: 5%;
            }
        </style>
    {/literal}
    <div class="row-fluid calendar-wrapper">
        <div class="quickWidgetContainer accordion span2 user-column">
            <div class="quickWidget">
                <div class="accordion-heading accordion-toggle quickWidgetHeader" data-label="LBL_ADDED_CALENDARS">{vtranslate('LBL_AVAILABLE_SURVEYORS',$MODULE_NAME)}
                    <span class="pull-right"><i class="icon-plus addCalendarView" title="Add Calendar View"></i></span>
                </div>
                <div id="calendarview-feeds">
                </div>
            </div>
        </div>

        <input type="hidden" id="currentView" value="{$smarty.request.view}" />
        <input type="hidden" id="activity_view" value="{$USER_MODEL->get('activity_view')}" />
        <input type="hidden" id="time_format" value="{$USER_MODEL->get('hour_format')}" />
        <input type="hidden" id="start_hour" value="{$USER_MODEL->get('start_hour')}" />
        <input type="hidden" id="date_format" value="{$USER_MODEL->get('date_format')}" />

        <div id="calendarview" class="span10"></div>
    </div>
{/if}
{/strip}

{if $ENABLE_CALENDAR}
<script>
    {literal}
        jQuery(document).ready(function () {
            var instance = Opportunities_CalendarView_Js.getInstanceByView();
            instance.registerEvents();
            instance.loadSharedCalendarUsers();

            Opportunities_CalendarView_Js.currentInstance = instance;

            //Hiding the buttons that do not need to be shown
            $('.calAddButton').hide();
            $('#calendarSettings').parent().hide();
        });
    {/literal}
</script>
{/if}
