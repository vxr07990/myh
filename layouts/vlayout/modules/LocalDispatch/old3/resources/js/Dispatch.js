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
    slotEventOverlap:false,
    editable:true,
    minTime: '00:00:00',
    maxTime: '23:30:00',
    droppable:true,dayClick: openNewForm,
    drop:function() {
      $(this).closest('tr').remove();
    },
    dayClick: function(date, jsEvent, view) {
        openNewForm(date);

    },

//     dayClick: function(date, jsEvent, view) {
//         alert('Clicked on: ' + date.format());
//         alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
//         alert('Current view: ' + view.name);

        // change the day's background color just for fun
//         $(this).css('background-color', 'red');

//     },
//     eventAfterRender:function( event, element, view ) {
//       alert();
//       $(element).attr("id","event_id_"+event._id);
//     },

//     header: {
//       left: 'prev,next today',
//       center: 'title',
//       right: 'month,agendaWeek,agendaDay'
//     },
  });


  var d = new Date();
  var h = d.getHours();
  var m = d.getMinutes();
  var hours = h + ':' + m + ':00';

  $('#dispatch-calendar').fullCalendar({
    //theme: true,
    header: {
      left: '',
      center: 'title',
      right: 'prev,next today'
    },
    //editable: true,
    defaultView:'agendaDay',
    minTime: '00:00:00',
    maxTime: '23:30:00',
    scrollTime : hours,
    height: 850,
    //disableDragging: true,
    //disableResizing: true,
    //droppable: true,
    editable:true,
    slotEventOverlap:false,
    drop: function( date, allDay, jsEvent, ui ){
//       console.log(jsEvent);
//       console.log(ui);
    },
    // add event name to title attribute on mouseover
    eventMouseover: function(event, jsEvent, view) {
      if (view.name == "month") {
        $(jsEvent.target).attr('title', event.title);
      }
    //alert(event.id);
    },
    events: [
      {
        id: 1,
        title: 'User1',
        start: '2015-02-26T13:00:00',
        end: '2015-02-26T18:00:00',
        color:'#E9B33E',
        className: 'user-class1'
      },
      {
        id: 2,
        title: 'User2',
        start: '2015-02-26T13:00:00',
        end: '2015-02-26T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 3,
        title: 'User3',
        start: '2015-02-26T13:00:00',
        end: '2015-02-26T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 4,
        title: 'User4',
        start: '2015-02-26T13:00:00',
        end: '2015-02-26T16:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 5,
        title: 'User5',
        start: '2015-02-26T08:00:00',
        end: '2015-02-26T13:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 6,
        title: 'User6',
        start: '2015-02-26T13:00:00',
        end: '2015-02-26T15:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 7,
        title: 'User7',
        start: '2015-02-26T09:30:00',
        end: '2015-02-26T21:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 8,
        title: 'User8',
        start: '2015-02-26T13:00:00',
        end: '2015-02-26T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 9,
        title: 'User9',
        start: '2015-02-26T15:00:00',
        end: '2015-02-26T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      }
    ],
    eventRender: function(event,element,calEvent) {
//       console.log(element.attr('id',this.id));
      element.droppable({
        accept: '*',
        tolerance: 'touch', //tolerance: 'touch',
        activeClass: 'ui-state-hover',
        hoverClass: 'ui-state-active',
        drop: function(ev, ui) {

          var title = ui.draggable.text()
          var id = ui.draggable.data('id');
          var role = ui.draggable.closest('tr').find('td').eq(2).text();

          if(role == 'Employee' || role == 'Contractor'){
            if(element.find('img').hasClass('ico-crew') == false){
              if(ui.draggable.closest('tr').find('td').hasClass('unavailable')){
                bootbox.confirm("You are about to assign a crew member with a status of unavailable. Do you want to allow this?", function(result) {
                  if(result == true){
                    $("<img/>").attr({
                      'src': 'layouts/vlayout/modules/LocalDispatch/resources/images/ico-crew.png',
                      'alt': '',
                      'class': 'ico-truck'
                    })
                    .appendTo(element);

                    ui.helper.remove();
                    ui.draggable.closest('tr').hide();
                  }else{
                    return false;
                  }
                });

                ui.helper.remove();
                ui.draggable.closest('tr').hide();

              }else{
                $("<img/>").attr({
                  'src': 'layouts/vlayout/modules/LocalDispatch/resources/images/ico-crew.png',
                  'alt': '',
                  'class': 'ico-crew'
                })
                .appendTo(element);
              }
            }

            ui.helper.remove();
            ui.draggable.closest('tr').hide();
          }else{
            if(element.find('img').hasClass('ico-truck') == false){
              if(ui.draggable.closest('tr').find('td').hasClass('unavailable')){
                bootbox.confirm("You are about to assign a truck with a status of unavailable. Do you want to allow this?", function(result) {
                  if(result == true){
                    $("<img/>").attr({
                      'src': 'layouts/vlayout/modules/LocalDispatch/resources/images/ico-truck.png',
                      'alt': '',
                      'class': 'ico-truck'
                    })
                    .appendTo(element);

//                     ui.helper.remove();
//                     ui.draggable.closest('tr').hide();
                  }else{
                    return false;
                  }
                });
              }else{
                $("<img/>").attr({
                  'src': 'layouts/vlayout/modules/LocalDispatch/resources/images/ico-truck.png',
                  'alt': '',
                  'class': 'ico-truck'
                })
                .appendTo(element);
//                 ui.helper.remove();
//                 ui.draggable.closest('tr').hide();
              }
            }else{
              return false;
            }
          }




//           $("<div/>").text(title).appendTo(element);

//         console.log(ui.draggable.text());
//           alert("\nUser:"+this.id+"\nDocumentID:"+ui.draggable.data('id'));
        }


      });
    },
    eventClick: function(calEvent, jsEvent, view) {
      if(!$(jsEvent.target).hasClass("icon")){
        alert("User "+calEvent.id);
      }
    }
  });

  $('#crew tbody td, #trucks tbody td').each(function() {
    // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
    // it doesn't need to have a start or end
    var eventObject = {
      title: $.trim($(this).text()), // use the element's text as the event title
      className: $(this).attr('class'),
    };

    // store the Event Object in the DOM element so we can get to it later
    $(this).data('eventObject', eventObject);

    // make the event draggable using jQuery UI
    $(this).draggable({
      cursorAt: { top: -5, left: -5 },

      helper:'clone',
      zIndex: 999,
      revert:true,
      revertDuration:800,
      start: function(e, ui){
        $(ui.helper).addClass("ui-draggable-helper");
      }
    });
  });


























//   $('#dispatch-calendar').fullCalendar({
//     header: {
//       left: 'prev,next today',
//       center: 'title',
//       right: 'month,agendaWeek,agendaDay'
//     },
//     minTime: '08:00:00',
//     maxTime: '18:00:00',
//     defaultView:'agendaDay',
//     editable:true,
//     droppable:true,dayClick: openNewForm,
//     drop:function() {
//       $(this).closest('tr').remove();
//     },
//     eventRender: function (event, element){
// //       // store the ID for later...
// //       $(element).data('id', 1);
// //       element.droppable({
// //           drop:function(event, ui){
// //             alert();
// // //            var rowID = $(this).data('id');
// //           }
// //       });
//     },
//
//     events: [
//         {
//             title  : 'event1',
//             start  : '2015-02-23T09:00:00'
//         },
//         {
//             title  : 'event2',
//             start  : '2015-02-23T09:30:00',
//             end    : '2015-02-23T10:30:00'
//         },
//         {
//             title  : 'event3',
//             start  : '2015-02-23T12:30:00',
//             allDay : false // will make the time show
//         }
//     ]
//
// //     dayClick: function(date, jsEvent, view) {
// //         openNewForm(date);
// //
// //     },
//
// //     dayClick: function(date, jsEvent, view) {
// //         alert('Clicked on: ' + date.format());
// //         alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
// //         alert('Current view: ' + view.name);
//
//         // change the day's background color just for fun
// //         $(this).css('background-color', 'red');
//
// //     },
//
//
//
// //     header: {
// //       left: 'prev,next today',
// //       center: 'title',
// //       right: 'month,agendaWeek,agendaDay'
// //     },
//   });

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