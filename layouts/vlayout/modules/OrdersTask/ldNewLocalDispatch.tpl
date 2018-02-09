<div class="row parasplitter">
	<div id="leftPane" style="padding-left:3%;">
		{include file="ListViewHeader.tpl"|vtemplate_path:$MODULE}
		{include file="ListViewContents.tpl"|vtemplate_path:$MODULE}
    </div>
    
	<div id="rightPane" style="overflow-y:hidden">
        <div class="text-center resourceTableTitleDiv" style="padding:2%;background-color:#ccc;margin-bottom: 1%; font-size: 120%;"><span>Resource Table</span></div>
            <div class="accordion_ld borderless" style="width: 100%;float: left;overflow-y: hidden;margin-left:0px;">
            <input type="hidden" value="{$HIDE_VENDORS}" name="hide_vendors" id="hide_vendors">
            <div class="resources_crew accordion-group" id="accordion1">
                <span class="filterActionsDivCrew hide"><hr><ul class="filterActions"><li onclick="OrdersTask_LocalDispatch_Js.ldFilterCreate(this);" data-rigthtable="Crew" data-createurl="index.php?module=CustomView&view=EditAjax&source_module=Employees&customView=NewLocalDispatchCrew"><i class="icon-plus-sign"></i>{vtranslate('LBL_CREATE_NEW_FILTER')}</li></ul></span>
                            <div class="row-fluid span12 accordion-head">
                                <button id="crew_btn" class="span3 accordion" style="font-size: 135%;">Crew</button>
                                <span class="span9">{$CREW_FILTER}</span>
                            </div>
                            <div class="panel" id="panel-crew" style="overflow-y: auto;"></div>
            </div>
                    <div class="resources_equipment accordion-group" id="accordion2">
                <span class="filterActionsDivEquipment hide"><hr><ul class="filterActions"><li onclick="OrdersTask_LocalDispatch_Js.ldFilterCreate(this);" data-rigthtable="Equipment" data-createurl="index.php?module=CustomView&view=EditAjax&source_module=Vehicles&customView=NewLocalDispatchEquipment"><i class="icon-plus-sign"></i>{vtranslate('LBL_CREATE_NEW_FILTER')}</li></ul></span>
                            <div class="row-fluid span12 accordion-head">
                                <button id="vehicles_btn" class="span3 accordion" style="font-size: 135%;">Vehicles</button>
                                <span class="span9">{$EQUIPMENT_FILTER}</span>
                            </div>
                            <div class="panel" id="panel-vehicles" style="overflow-y: auto;"></div>
            </div>
            {if $HIDE_VENDORS neq 'yes'}
                <div class="resources_vendors accordion-group" id="accordion3">
                    <span class="filterActionsDivVendors hide"><hr><ul class="filterActions"><li onclick="OrdersTask_LocalDispatch_Js.ldFilterCreate(this);" data-rigthtable="Vendors" data-createurl="index.php?module=CustomView&view=EditAjax&source_module=Vendors&customView=NewLocalDispatchVendors"><i class="icon-plus-sign"></i>{vtranslate('LBL_CREATE_NEW_FILTER')}nsa</li></ul></span>
                    <div class="row-fluid span12 accordion-head">
                        <button id="vendors_btn" class="span3 accordion" style="font-size: 135%;">Vendors</button>
                        <span class="span9">{$VENDOR_FILTER}</span>

                    </div>
                                    <div class="panel" id="panel-vendors" style="overflow-y: auto;"></div>
                </div>
            {/if}
            <div class="resources_map accordion-group" id="accordion4">
                <div class="row-fluid span12 accordion-head">
                    <button id="map_btn" class="span12 accordion" style="font-size: 135%;">Map</button>
                </div>    
                <div class="panel" id="panel-map" style="height:200px;"></div>
            </div>
        </div>

    </div>
</div>
<style>
{literal}
button.accordion {
    background-color: #eee;
    color: #444;
    cursor: pointer;
    padding: 5px;
    margin: 0px;
    border: none;
    text-align: left;
    outline: none;
    font-size: 15px;
    transition: 0.4s;
}

.accordion-head{
    background-color: #eee;
    color: #444;
    margin: 0px;
    border: none;
}

div.panel {
    padding: 0 1px;
    display: none;
    background-color: white;
}

.accordion_ld {
    height: 100%;
    overflow-y: scroll;
    border: 1px solid #CCC;
}

.panel {
    overflow-y: none;
}
{/literal}
</style>
