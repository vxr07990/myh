
jQuery.Class("MenuCleaner_List_Js",{},{
    registerSettingsShortcutClickEvent : function() {
        jQuery('#settingsShortCutsContainer').on('click','.moduleBlock',function(e){
            var url = jQuery(e.currentTarget).data('url');
            window.location.href = url;
        });
    },
    registerEvents : function() {
        this.registerSettingsShortcutClickEvent();
    }
});
