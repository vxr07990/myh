{strip}
	<div style="padding-top: 3%;">
		<div>
			<h4> {vtranslate('LBL_RESOURCES_DASHBOARD',$MODULE_NAME)}	</h4>
			<hr>
		</div>

                <div class="row" style="margin-bottom: 1%;margin-top: 1%;width:1376px !important">
                    <div class="span5" style="margin-left: 0%;"> Resource Type:
                        <select id="resourceSel" class="selector" style="margin-left: 10%;">
                            <option>All</option>
                            <option>Employees</option>
                            <option>Equipment</option>
                            <option>Vehicles</option>
                            
                        </select>    
                    </div>
                    <div class="span3"> Month:
                        <select id="monthSel" class="selector" style="margin-left: 10%;width: 70%;">
                                <option value="---">---</option>
                            {foreach key=MONTHN item=MONTH from=$MONTHS}
                                <option value="{$MONTHN}">{$MONTH}</option>
                                
                             {/foreach}   
                        </select>    
                    </div>
                    <div class="span3"> Year:
                        <select id="yearSel" class="selector" style="margin-left: 10%;width: 70%;">
                             {foreach item=YEAR from=$YEARS}
                                <option value="{$YEAR}" {if $YEAR eq {$smarty.now|date_format:"%Y"}} selected {/if} >{$YEAR}</option>
                                
                             {/foreach}
                        </select>    
                    </div>
                    <div class="span1">
                        <span id="search" class="btn">Update</span>
                              
                    </div>
                        
                </div>
                       
                <div class="resourcedash" style="width:1376px !important">
                  {$DASHTABLE}
                </div>
                        
	</div>
{/strip}
