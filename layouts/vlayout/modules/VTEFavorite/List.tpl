    {*/* ********************************************************************************
* The content of this file is subject to the VTEFavorite ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
<div class="vteFavDiv" >
{assign var=ARR_MODULES value=$RECORDS.arrModules}
{if not $ARR_MODULES}
No favorite.
{/if}
{foreach from=$ARR_MODULES item=MODULE}
		{assign var=ARR_RECORDS value=$MODULE.arrRecords}
		{assign var=ARR_FIELDS value=$MODULE.arrFields}
		<div class="container-fluid">
			<div class="widget_header row-fluid">
				<div class="span4">
				<h3>{$MODULE.name}</h3>
				</div>
			</div>	
		</div>
		   <table  class="table  table-bordered table-condensed ">
						<tr>
						{foreach from=$ARR_FIELDS item=FIELD}
							<th>{$FIELD->get('label')}</th>
						{/foreach}
							
							<th style="width: 110px;"></th>
						
							
						</tr>
						{foreach from=$ARR_RECORDS item=RECORD}
						<tr class="vteFavdropdown">
							{foreach from=$ARR_FIELDS item=FIELD}
								<td> <a href="{$RECORD['Metadata']['url']}"> {$RECORD['Record']->get($FIELD->get('name'))} </a> </td>
							{/foreach}
								
							<td align="right"  style="text-align: right;"> 
								<div >
										<div class="vteFavStars  pull-right" style="width:{$RECORD['Metadata']['stars']*16}px">			
										
										<span class="vteFavdropdown-content">
										<i onclick="delFavorite({$RECORD['Metadata']['id']});" title="Delete" class="icon-trash alignMiddle"></i>
										{if $RECORD['Metadata']['view']=='Edit'}
										
										&nbsp;<i onclick="window.location.href ='{$RECORD['Metadata']['url']}';" class="icon-pencil" title="Edit"></i>
										{else}
										&nbsp;<i onclick="window.location.href ='{$RECORD['Metadata']['url']}';" title="Complete Details" class="icon-th-list alignMiddle"></i>
										{/if}
										</span>					
										</div>
										
										
								</div>
							</td>
							
						</tr>
						{/foreach}
					</table>
			

{/foreach}			
</div >
{/strip}

