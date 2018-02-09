(function($) {
  "use strict";
  $.fn.ldfilter = function(options) {
    var settings = $.extend( {
      'target'   : null,
      'available': null
    }, options);

    jQuery.expr[":"].Contains = function(a, i, m) {
      return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };

    if(settings.available != null && $(settings.target).length == 0){
      alert('Target table ' + settings.target + ' or tbody not found');
      return false;
    }

    if(settings.available != null && $(settings.available).length == 0){
      alert('Element ' + settings.available + ' not found');
      return false;
    }

    var rem;
    $(settings.available).on('click', function(){
      if(!rem){
        rem = $(settings.target + ' tbody')
         .find('.unavailable')
         .closest('tr')
         .detach();
      } else {
        rem.appendTo(settings.target + ' tbody');
        rem = null;
      }
    });

    this.each(function() {
      var $this = $(this);
      var container = $(settings.target);
      var row_tag = 'tr';
      var item_tag = 'td';
      var rows = container.find($(row_tag));
      var col = container.find('th:Contains(' + $this.data('col') + ')');
      var col_index = container.find($('thead th')).index(col);

      $this.change(function() {
        var filter = $this.val();
        rows.each(function() {
          var row = $(this);
          var cell = $(row.children(item_tag)[col_index]);
          if (filter) {
            if (cell.text().toLowerCase().indexOf(filter.toLowerCase()) !== -1) {
              cell.attr('data-filtered', 'positive');
            } else {
              cell.attr('data-filtered', 'negative');
            }

            if (row.find(item_tag + "[data-filtered=negative]").size() > 0) {
               row.hide();
            } else {
              if (row.find(item_tag + "[data-filtered=positive]").size() > 0) {
                row.show();
              }
            }
          } else {
            cell.attr('data-filtered', 'positive');
            if (row.find(item_tag + "[data-filtered=negative]").size() > 0) {
              row.hide();
            } else {
              if (row.find(item_tag + "[data-filtered=positive]").size() > 0) {
                row.show();
              }
            }
          }
        });

      }).keyup(function(e) {
        if(e.keyCode == 8){
          if($("#no-results").length > 0){
            $("#no-results").remove();
          }
        }

        $(".clear-filter").on('click', function(){
          $(".filter").val('');
          $(settings.target + " thead").show();
          $("#no-results").remove();
          $this.change();
        });
        $this.change();

        if($(settings.target + ' tbody tr:visible').not('#no-results').length == 0) {
          $(settings.target + " thead").hide();
          if($(settings.target + ' tbody tr#no-results').length <= 0) {
            $(settings.target + ' tr:last').after('<tr id="no-results"><td>No Results</td></tr>');
          }
        } else {
          $(settings.target + " thead").show();
        }
      });
    });
  };
})(jQuery);