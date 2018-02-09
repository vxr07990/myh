/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Vtiger_Edit_Js("MenuCreator_EditBlock_Js", {
    getInstance: function() { return new MenuCreator_EditBlock_Js(); }
}, {
    menuEditorMakeMenuItemsListSortable : function() {
	var thisInstance = this;
	var select2Element = app.getSelect2ElementFromSelect(jQuery('#menuListSelectElement'));
	var select2ChoiceElement = select2Element.find('ul.select2-choices');

	select2ChoiceElement.sortable({
	    'containment': select2ChoiceElement,
	    start: function() { 
		jQuery('#selectedMenus').select2("onSortStart");
	    },
	    update: function() {
		jQuery('#selectedMenus').select2("onSortEnd");
	    }
	});
    },
    registerEvents : function() {
	var thisInstance = this;

	this.menuEditorMakeMenuItemsListSortable();
    },
});

jQuery(document).ready(function() {
    var instance = MenuCreator_EditBlock_Js.getInstance();
    instance.registerEvents();
});