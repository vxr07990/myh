$(document).ready(function() {
  $("#dispatch-people tr:gt(0), #dispatch-truck tr:gt(0)").draggable({
    revert: true,
    helper: 'clone',
    cursor: 'move'
  });

  $( "#dispatch-from" ).datepicker({
    defaultDate: "+1w",
    changeMonth: true,
    numberOfMonths: 2,
    onClose: function( selectedDate ) {
      $( "#dispatch-to" ).datepicker( "option", "minDate", selectedDate );
    }
  });

  $( "#dispatch-to" ).datepicker({
    defaultDate: "+1w",
    changeMonth: true,
    numberOfMonths: 2,
    onClose: function( selectedDate ) {
      $( "#dispatch-from" ).datepicker( "option", "maxDate", selectedDate );
    }
  });

  $('#dispatch-orders tbody td').each(function() {
    $(this).data('event', {
      title:$.trim($(this).text()),
      stick:true
    });

    $(this).draggable({
      zIndex:999,
      helper:'clone',
      revert:true,
      revertDuration:800,
      start: function(e, ui){
        $(ui.helper).addClass("ui-draggable-helper");
      }
    });
  });

  $('#unassigned-calendar').fullCalendar({
    defaultView:'agendaWeek',
    editable:true,
    droppable:true,dayClick: openNewForm,
    drop:function() {
      $(this).closest('tr').remove();
    },
    dayClick: function(date, jsEvent, view) {
        openNewForm(date);

    },

    dayClick: function(date, jsEvent, view) {
//         alert('Clicked on: ' + date.format());
//         alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
//         alert('Current view: ' + view.name);

        // change the day's background color just for fun
//         $(this).css('background-color', 'red');

    },


//     header: {
//       left: 'prev,next today',
//       center: 'title',
//       right: 'month,agendaWeek,agendaDay'
//     },
  });

  $('#dispatch-calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay'
    },
    defaultView:'agendaDay',
    editable:true,
    droppable:true,dayClick: openNewForm,
    drop:function() {
      $(this).closest('tr').remove();
    },
    dayClick: function(date, jsEvent, view) {
        openNewForm(date);

    },

    dayClick: function(date, jsEvent, view) {
//         alert('Clicked on: ' + date.format());
//         alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
//         alert('Current view: ' + view.name);

        // change the day's background color just for fun
//         $(this).css('background-color', 'red');

    },


//     header: {
//       left: 'prev,next today',
//       center: 'title',
//       right: 'month,agendaWeek,agendaDay'
//     },
  });

  function openNewForm(date, jsEvent, view){

  }

  $("#dispatch-orders td input").on('click',function(){
    var title = $(this).closest('tr').find('td:first-of-type').text();
    var attr = $(this).attr('value');
    var id = $(this).data("id");

    if(attr == 'Accept') {
      var dt = $(this).data('date');
      var sp = dt.split('#');
      var e = {
        events: [
          {
            title : title,
            start : sp[0],
            end   : sp[1]
          }
        ],
        //color:'black',
        //textColor:'yellow'
      };

      $('#unassigned-calendar').fullCalendar('addEventSource', e);
      $('#unassigned-calendar').fullCalendar('rerenderEvents');
      $(this).closest('tr').remove();
    }

    if(attr == 'Reject'){
      $(this).closest('tr').remove();
    }

    if(attr == 'Driver Notes'){
      $(".modal-body").html('Driver Notes');
    }

    if(attr == 'Order Notes'){
      $(".modal-body").html('Order Notes');
    }
//     alert(attr + ' (Order #' + id + ')');
//     alert($(this).data("id"));
  });

  $("#unassigned-orders").on('click', function(){
    location.href = 'index.php?module=LocalDispatch&view=Dispatch';
  });

  $("#dispatch").on('click', function(){
    location.href = 'index.php?module=LocalDispatch&view=List';
  });

  if($("#dispatch-calendar").length > 0){
    $('.filter').ldfilter({
      'target'    : 'table#crew',
      'available' : '#showAvailableCrew' /*element container for the filtering input*/
    });
    $('.filter').ldfilter({
      'target'    : 'table#trucks',
      'available' : '#showAvailableTrucks' /*element container for the filtering input*/
    });
  }




});