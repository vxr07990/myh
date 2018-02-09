{strip}
{assign var=HAS_CONTENT value=(!$BLOCK_SUBLIST || $BLOCK_SUBLIST['GOOGLE_CALCULATOR'])}
<div id="contentHolder_GOOGLE_CALCULATOR" class="sectionContentHolder {$CONTENT_DIV_CLASS} {if !$ALWAYS_SHOW_CONTENT_DIV}hide{/if} {if !$HAS_CONTENT}inactive{/if}">
{if $HAS_CONTENT}
    <table name='{$BLOCK_LABEL}' id="googleTable" class="table table-bordered blockContainer showInlineTable">
        <thead>
            <tr>
                <th class="blockHeader" colspan="5">
                    <img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if} "  src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id="googleCalc">
                    <img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}"  src="{vimage_path('arrowDown.png')}" data-mode="show" data-id="googleCalc">
                    {vtranslate('LBL_QUOTES_GOOGLE_BLOCK', $MODULE)}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="interstateServiceChargeHeader">
                <td class="fieldLabel" style="text-align:left;margin:auto;width:80%;">{vtranslate('LBL_QUOTES_GOOGLE_ADDRESS', $MODULE)}</td>
                <td class="fieldLabel" style="text-align:right;margin:auto;width:10%;">{vtranslate('LBL_QUOTES_GOOGLE_MILES', $MODULE)}</td>
                <td class="fieldLabel" style="text-align:right;margin:auto;width:10%;">{vtranslate('LBL_QUOTES_GOOGLE_TIME', $MODULE)}</td>
            </tr>
            <tbody>
            {foreach item=ADDRESS key=KEY from=$GOOGLE_ADDRESSES}
            <tr>
                <input type="hidden" name="googleCalcAddress[]" value="{$ADDRESS.address}">
                <input type="hidden" name="googleCalcMiles[]" value="{$ADDRESS.miles}">
                <input type="hidden" name="googleCalcTime[]" value="{$ADDRESS.time}">
                <td class="fieldLabel" style="text-align:left;margin:auto;width:80%;">{$ADDRESS.address}</td>
                <td class="fieldLabel" style="text-align:right;margin:auto;width:10%;">{$ADDRESS.miles}</td>
                <td class="fieldLabel" style="text-align:right;margin:auto;width:10%;">{$ADDRESS.time}</td>
            </tr>
            {/foreach}
            {if count($GOOGLE_ADDRESSES)}
                <tr>
                    <input type="hidden" name="googleCalcMilesTotal" value="{$GOOGLE_TOTAL_MILES}">
                    <input type="hidden" name="googleCalcTimeTotal" value="{$GOOGLE_TOTAL_TIME}">
                    <td class="fieldLabel" style="text-align:left;margin:auto;width:80%;">
                        <b>Total</b>
                    </td>
                    <td class="fieldLabel" style="text-align:right;margin:auto;width:10%;">{$GOOGLE_TOTAL_MILES}</td>
                    <td class="fieldLabel" style="text-align:right;margin:auto;width:10%;">{$GOOGLE_TOTAL_TIME}</td>
                </tr>
            {/if}
            </tbody>
        {if $IS_EDIT_VIEW}
            <tr>
                <td colspan="3">
                    <button type="button" id="googleCalculatorButton">Update Miles/Time</button>
                </td>
            </tr>
        {/if}
        </tbody>
        </table>
    <br />
{/if}
</div>
{/strip}