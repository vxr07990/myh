{*/* ********************************************************************************
* The content of this file is subject to the Summary Report ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
    <div class="container-fluid settingsIndexPage">
        <div class="widget_header row-fluid" style="margin-top: 10px;">
            <div class="span8"><h3>Module Shortcuts</h3></div>
        </div>
        <hr>
        <div class="row-fluid">
            <div class="span1">&nbsp;</div>
            <div id="settingsShortCutsContainer" class="span11">
                <div  class="row-fluid">
                    {assign var=SPAN_COUNT value=1}
                    {foreach item=MODULE_NAME from=$MODULES_SHORTCUTS  key=NO name=shortcuts}
						<span id="shortcut_{$NO}" data-actionurl="index.php?module={$MODULE_NAME}&view=List" class="span3 contentsBackground well cursorPointer moduleBlock" data-url="index.php?module={$MODULE_NAME}&view=List">
                            <a href="index.php?module={$MODULE_NAME}&view=List">
							<h5 class="themeTextColor">{vtranslate($MODULE_NAME,$MODULE_NAME)}</h5>
							<div>{vtranslate($MODULE_NAME,$MODULE_NAME)}</div>
                            </a>
						</span>
						{if $SPAN_COUNT==3}</div>{$SPAN_COUNT=1}{if not $smarty.foreach.shortcuts.last}<div class="row-fluid">{/if}{continue}{/if}
						{$SPAN_COUNT=$SPAN_COUNT+1}
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
    </div>
{/strip}
