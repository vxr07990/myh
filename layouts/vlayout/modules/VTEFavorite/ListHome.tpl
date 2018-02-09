    {*/* ********************************************************************************
* The content of this file is subject to the VTEFavorite ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}

<div class="container-fluid">
	<div class="widget_header row-fluid">
		<div class="span4">
		<h3>{$MODULE}</h3>
		</div>
	</div>	
</div>
home {$RECORDS|@print_r} 
{foreach from=$RECORDS item=RECORD}
	<div>{$RECORD}</div>
{/foreach}		
{/strip}

