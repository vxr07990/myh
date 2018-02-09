{strip}
    <input type="hidden" disabled id="allAvailableTariffs" value="{$AVAILABLE_TARIFFS}">
    <input type="hidden" id="tariff_customjs" value="{$EFFECTIVE_TARIFF_CUSTOMJS}">
    <input type="hidden" id="effective_tariff_custom_type" name="effective_tariff_custom_type" value="{$EFFECTIVE_TARIFF_CUSTOMTYPE}">
    <input type="hidden" id="isLocalRating" name="isLocalRating" value="{$IS_LOCAL_RATING}">
    <select class='chzn-select' name='effective_tariff'>
        {* set the picklist to just include the current value on page load, and JS will load the correct filtered options *}
        <option value>Select an Option</option>
        <option value="{$EFFECTIVE_TARIFF}" selected>{$EFFECTIVE_TARIFF_NAME}</option>
    </select>
{/strip}