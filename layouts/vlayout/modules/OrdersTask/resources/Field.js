//custom reference base js, copied/modified from owner base js for participating_agent
Vtiger_Field_Js('Vtiger_Reference_Field_Js', {}, {
    getPickListValues: function (){
	var picklistValues = JSON.parse(jQuery('[name="hidden_participating_agents"]').val());
	var values = new Array();
	for(var k in picklistValues) {
	    var obj = new Object();
	    obj.agentid = picklistValues[k].agentid;
	    obj.agent_number = picklistValues[k].agent_number;
	    obj.agent = picklistValues[k].agent;
	    obj.agentname = picklistValues[k].agentname;
	    values.push(obj);
	}
	
	return values;
    },
    getUi: function () {
	if(this.getName() == "participating_agent"){
	    var html = '<select class="row-fluid chzn-select" name="' + this.getName() + '">';
	    var pickListValues = this.getPickListValues();
	    var selectedOption = this.getValue();

	    for (var option in pickListValues) {
		html += '<option value="' + pickListValues[option].agent + '" ';
		var customCompare = jQuery("<div/>").html(pickListValues[option].agentname + " (" + pickListValues[option].agent_number + ")").text();
		if (customCompare == selectedOption) {
		    html += ' selected ';
		}
		html += '>' + pickListValues[option].agent + '</option>';
	    }

	    html += '</select>';
	    var selectContainer = jQuery(html);
	    this.addValidationToElement(selectContainer);
	    return selectContainer;
	} else{
	    var html = '<input type="text" nae="' + this.getName() + '"  />';
	    html = jQuery(html).val(app.htmlDecode(this.getValue()));
	    return this.addValidationToElement(html);
	}
    }
});
