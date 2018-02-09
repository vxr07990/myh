{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
{assign var="COLORS" value=['#DD7373', '#ce93d8', '#9fa8da', '#64b5f6', '#4dd0e1', '#80cbc4', '#81c784', '#dce775', '#fff176', '#ffc107', '#ffb74d', '#ff7043', '#b0bec5', '#f48fb1', '#8575cd']}
<style>
	.treeView li .toolbar-handle a:before{
		background: none;
	}
	.treeView li .toolbar-handle .depth0:before{
		background: {$COLORS[0]};
	}
	a.depth0{
		background-color: {$COLORS[0]};
		color: #FFFFFF;
	}
	li.depth0{
		border-left: 4px solid {$COLORS[0]};
	}
</style>
<div class="container-fluid">
	<div class="widget_header row-fluid">
		<div class="span8">
			<h3>{vtranslate($MODULE, $QUALIFIED_MODULE)}</h3>
	</div>	
	</div>
	<hr>
	<div class="clearfix treeView">
		<ul>
			<li data-role="{$ROOT_ROLE->getParentRoleString()}" data-roleid="{$ROOT_ROLE->getId()}">
				<div class="toolbar-handle">
					<a href="javascript:;" class="btn btn-inverse draggable droppable">{$ROOT_ROLE->getName()}</a>
					<div class="toolbar" title="{vtranslate('LBL_ADD_RECORD', $QUALIFIED_MODULE)}">
						&nbsp;<a href="{$ROOT_ROLE->getCreateChildUrl()}" data-url="{$ROOT_ROLE->getCreateChildUrl()}" data-action="modal"><span class="icon-plus-sign"></span></a>
					</div>
				</div>
				{assign var="ROLE" value=$ROOT_ROLE}
				{include file=vtemplate_path("RoleTree.tpl", "Settings:Roles")}
			</li>
		</ul>
	</div>
</div>
{/strip}