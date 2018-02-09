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
{foreach key=index item=jsModel from=$SCRIPTS}
	<script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
<div id="massPrintContainer" class='modelContainer'>
	<div class="modal-header contentsBackground">
		<button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="massPrintHeader">{vtranslate('LBL_PRINTRECORDS', $MODULE)} {vtranslate($MODULE, $MODULE)}</h3>
	</div>
	<div class="modal-header">
		<div class="pull-right cancelLinkContainer" style="margin-top:0px;">
			<a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
		</div>
		<a class="btn print-records" name="printButton"><strong>{vtranslate('LBL_PRINT', $MODULE)}</strong></a>
	</div>
	<form class="form-horizontal" id="massPrint" name="massPrint" method="post" action="index.php">
		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
		{/if}
		<link rel="stylesheet" href="layouts/vlayout/skins/bluelagoon/style.css?v=1.0" type="text/css" media="screen">
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" id="massPrintFieldsNameList" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($MASS_EDIT_FIELD_DETAILS))}' />
		<div style="min-height: 500px;display: flex;padding-bottom: 100px">
			<div class="modal-body tabbable" id="massPrintContents" style='min-width: 500px'>
            {if $NUM_ROWS eq '1'}
                {literal}
                    <style>
                        @page{size:A4 landscape;}
                        .barCode{width:100%;height:28%;padding:0 0 3% 0;position: relative}
                        .barCode:last-child{width:100%;height:28%;padding:0 0 0 0;}
                        .barCode img{width: 95%;height: 100%;float: left;}
                        .barCode span{
                            -webkit-transform: rotate(270deg);
                            -moz-transform: rotate(270deg);
                            -o-transform: rotate(270deg);
                            -ms-transform: rotate(270deg);
                            transform: rotate(270deg);
                            position: absolute;
                            top: 50%;
                            right: 0;
                        }
                    </style>
                {/literal}
                {foreach key=LOCATION_TAG_ID item=TAG from=$TAGS}
                <div class='barCode'>
                    <img src='data:image/png;base64,{$TAG}'><br />
                    <span>{$LOCATION_TAG_ID}</span>
                </div>
                {/foreach}
            {else}
				{literal}
					<style>
						@page{size:A4 portrait;}
                        .barCode{float:left;margin:0px 0px 5% 5px;text-align:center;max-width:32%;min-width: 31%;height:5%;}
                        .barCode img{height: 100%;}
                        .barCodeChunk{width: 100%}
					</style>
				{/literal}
				{foreach key=LOCATION_CHUNK_TAG_ID item=TAG_CHUNK from=array_chunk($TAGS, 3, true)}
                <div class='barCodeChunk'>
                    {foreach key=LOCATION_TAG_ID item=TAG from=$TAG_CHUNK}
                    <div class='barCode'>
                        <img src='data:image/png;base64,{$TAG}'><br />
                        {$LOCATION_TAG_ID}
                    </div>
                    {/foreach}
                </div>
				{/foreach}
            {/if}
			</div>
		</div>
		<div class="modal-footer">
			<div class="pull-right cancelLinkContainer" style="margin-top:0px;">
				<a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
			</div>
			<a class="btn print-records" name="printButton"><strong>{vtranslate('LBL_PRINT', $MODULE)}</strong></a>
		</div>
	</form>
</div>
{/strip}
