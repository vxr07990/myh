$(function(){
  $( "#dispatch-tabs" ).tabs();

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

  $( "#unassigned-from" ).datepicker({
    defaultDate: "+1w",
    changeMonth: true,
    numberOfMonths: 2,
    onClose: function( selectedDate ) {
      $( "#unassigned-to" ).datepicker( "option", "minDate", selectedDate );
    }
  });

  $( "#unassigned-to" ).datepicker({
    defaultDate: "+1w",
    changeMonth: true,
    numberOfMonths: 2,
    onClose: function( selectedDate ) {
      $( "#unassigned-from" ).datepicker( "option", "maxDate", selectedDate );
    }
  });


  $('#dispatch-orders tbody tr:gt(0)').each(function() {
    $(this).data('event', {
      title: $.trim($(this).text()),
      stick: true
    });

    $(this).draggable({
      zIndex: 999,
      helper: 'clone',
      revert: true,
      revertDuration: 0
    });
  });

  $('#unassigned-calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay'
    },
    editable: true,
    droppable: true,
    drop: function() {
      $(this).remove();
//             if ($('#drop-remove').is(':checked')) {
//               $(this).remove();
//             }
    }
  });


});