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
	{include file='DetailViewBlockView.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}

    <table class='table table-bordered blockContainer showInlineTable'>
    <tbody>
        <tr>
            <th><center>Pricing Options<center></th>
            <th>Load To</th>
            <th>Deliver From</th>
            <th>Deliver To</th>
            <th>Price</th>
            <th>&nbsp;</th>
        </tr>
        <tr id='10_day_row'>
            <th><span class='pull-right'>10 Day Load</span></th>
            <td>{$AUTO_QUOTE_10_load}</td>
            <td>{$AUTO_QUOTE_10_from}</td>
            <td>{$AUTO_QUOTE_10_to}</td>
            <td>${$AUTO_QUOTE_10_price}</td>
            <td><input type='radio' name='auto_quote_select' disabled="disabled" value='1' {if $AUTO_QUOTE_SELECT eq 1}checked{/if}/></td>
        </tr>
        <tr id='7_day_row'>
            <th><span class='pull-right'>7 Day Load</span></th>
            <td>{$AUTO_QUOTE_7_load}</td>
            <td>{$AUTO_QUOTE_7_from}</td>
            <td>{$AUTO_QUOTE_7_to}</td>
            <td>${$AUTO_QUOTE_7_price}</td>
            <td><input type='radio' name='auto_quote_select' disabled="disabled" value='2' {if $AUTO_QUOTE_SELECT eq 2}checked{/if}/></td>
        </tr>
        <tr id='4_day_row'>
            <th><span class='pull-right'>4 Day Load</span></th>
            <td>{$AUTO_QUOTE_4_load}</td>
            <td>{$AUTO_QUOTE_4_from}</td>
            <td>{$AUTO_QUOTE_4_to}</td>
            <td>${$AUTO_QUOTE_4_price}</td>
            <td><input type='radio' name='auto_quote_select' disabled="disabled" value='3' {if $AUTO_QUOTE_SELECT eq 3}checked{/if}/></td>
        </tr>
        <tr id='2_day_row'>
            <th><span class='pull-right'>2 Day Load</span></th>
            <td>{$AUTO_QUOTE_2_load}</td>
            <td>{$AUTO_QUOTE_2_from}</td>
            <td>{$AUTO_QUOTE_2_to}</td>
            <td>${$AUTO_QUOTE_2_price}</td>
            <td><input type='radio' name='auto_quote_select' disabled="disabled" value='4' {if $AUTO_QUOTE_SELECT eq 4}checked{/if}/></td>
        </tr>
    </tbody>
</table>
<input type='hidden' value='{$AUTO_QUOTE_INFO}' name='auto_quote_info' id='auto_quote_info' />
<input type='hidden' value='{$AUTO_QUOTE_ID}' name='auto_quote_id' id='auto_quote_id' />
<br />
<table class="table table-bordered equalSplit detailview-table ">
    <thead>
        <tr>
            <th class="blockHeader" colspan="4">
                <img class="cursorPointer alignMiddle blockToggle  hide  " src="layouts/vlayout/skins/tightview/images/arrowRight.png" data-mode="hide" data-id="327">
                <img class="cursorPointer alignMiddle blockToggle " src="layouts/vlayout/skins/tightview/images/arrowDown.png" data-mode="show" data-id="327">&nbsp;&nbsp;{vtranslate("LBL_AUTOSPOTQUOTE_QUOTEDETAILS", $MODULE_NAME)}
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="fieldLabel medium narrowWidthType">
                <label class="muted pull-right marginRight10px">{vtranslate("LBL_AUTOSPOTQUOTE_QUOTENUMBER", $MODULE_NAME)}</label>
            </td>
            <td class="fieldValue medium narrowWidthType">
                <span class="value">{$AUTO_QUOTE_ID}</span>
            </td>
            <td class="fieldLabel medium narrowWidthType">
                <label class="muted pull-right marginRight10px">{vtranslate("LBL_AUTOSPOTQUOTE_DEFAULTWEIGHT", $MODULE_NAME)}</label>
            </td>
            <td class="fieldValue medium narrowWidthType">
                <span class="value">3,500</span>
            </td>
        </tr>
        <tr>
            <td class="fieldLabel medium narrowWidthType">
                <label class="muted pull-right marginRight10px">{vtranslate("LBL_AUTOSPOTQUOTE_DEFULTCUBE", $MODULE_NAME)}</label>
            </td>
            <td class="fieldValue medium narrowWidthType">
                <span class="value">700</span>
            </td>
            <td class="fieldLabel medium narrowWidthType">
                <label class="muted pull-right marginRight10px">{vtranslate("LBL_AUTOSPOTQUOTE_SERVICE", $MODULE_NAME)}</label>
            </td>
            <td class="fieldValue medium narrowWidthType">
                <span class="value">Auto Spot Quote</span>
            </td>
        </tr>
        <tr>
            <td class="fieldLabel medium narrowWidthType">
                <label class="muted pull-right marginRight10px">{vtranslate("LBL_AUTOSPOTQUOTE_VALIDTHROUGH", $MODULE_NAME)}</label>
            </td>
            <td class="fieldValue medium narrowWidthType">
                <span class="value">{$AUTO_QUOTE_expire}</span>
            </td>
            <td class="fieldLabel medium narrowWidthType">
                <label class="muted pull-right marginRight10px">{vtranslate("LBL_AUTOSPOTQUOTE_EFFECTIVEDATE", $MODULE_NAME)}</label>
            </td>
            <td class="fieldValue medium narrowWidthType">
                <span class="value">{$AUTO_QUOTE_effective}</span>
            </td>
        </tr>
        <tr>
            <td class="fieldLabel medium narrowWidthType">
                <label class="muted pull-right marginRight10px">
                    {vtranslate("LBL_AUTOSPOTQUOTE_APPLYRUSH", $MODULE_NAME)}
                </label>
            </td>
            <td class="fieldValue medium narrowWidthType">
                <span class="value">{if $RECORD->get('auto_rush_fee') != 0}Yes{else}No{/if}</span>
            </td>
            <td class="fieldLabel medium narrowWidthType">
                <label class="muted pull-right marginRight10px">&nbsp;</label>
            </td>
            <td class="fieldValue medium narrowWidthType">
                <span class="value">&nbsp;</span>
            </td>
        </tr>
    </tbody>
</table>
{/strip}
