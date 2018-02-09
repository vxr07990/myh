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

    }
  });

  function getCurrTime(){
    var d = new Date();
    var h = d.getHours();
    var m = d.getMinutes();
    return hours = h + ':' + m + ':00';
  }

  $('#dispatch-calendar').fullCalendar({
    header: {
      left: '',
      center: 'title',
      right: 'prev,next today'
    },
    theme:false,
    defaultView:'agendaDay',
    minTime: '00:00:00',
    maxTime: '23:30:00',
    scrollTime : getCurrTime(),
    height: 850,
    editable:true,
    slotEventOverlap:false,
    drop: function( date, allDay, jsEvent, ui ){
//       console.log(jsEvent);
//       console.log(ui);
    },
    // add event name to title attribute on mouseover
    eventMouseover: function(event, jsEvent, view) {
    // May be able to use this to show more data on hover
//     console.log(view)
      if(view.name == "month") {
        $(jsEvent.target).attr('title', event.title);
      }
    //alert(event.id);
    },
    events: [
      {
        id: 1,
        title: 'User1',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#E9B33E',
        className: 'user-class1'
      },
      {
        id: 2,
        title: 'User2',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 3,
        title: 'User3',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 4,
        title: 'User4',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 5,
        title: 'User5',
        start: '2015-03-02T08:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 6,
        title: 'User6',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 7,
        title: 'User7',
        start: '2015-03-02T09:30:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 8,
        title: 'User8',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 9,
        title: 'User9',
        start: '2015-03-02T15:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'id-nine'
      },
      {
        id: 10,
        title: 'User10',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#E9B33E',
        className: 'user-class1'
      },
      {
        id: 11,
        title: 'User11',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 12,
        title: 'User12',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 13,
        title: 'User13',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 14,
        title: 'User14',
        start: '2015-03-02T08:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 15,
        title: 'User15',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 16,
        title: 'User16',
        start: '2015-03-02T09:30:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 17,
        title: 'User17',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 18,
        title: 'User18',
        start: '2015-03-02T15:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'id-nine'
      },
      {
        id: 19,
        title: 'User19',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 20,
        title: 'User20',
        start: '2015-03-02T15:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'id-nine'
      },
      {
        id: 21,
        title: 'User21',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#E9B33E',
        className: 'user-class1'
      },
      {
        id: 22,
        title: 'User22',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 23,
        title: 'User23',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 24,
        title: 'User24',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 25,
        title: 'User25',
        start: '2015-03-02T08:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 26,
        title: 'User26',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 27,
        title: 'User27',
        start: '2015-03-02T09:30:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 28,
        title: 'User28',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 29,
        title: 'User29',
        start: '2015-03-02T15:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'id-nine'
      },
      {
        id: 30,
        title: 'User30',
        start: '2015-03-02T13:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'user-class2'
      },
      {
        id: 31,
        title: 'User31',
        start: '2015-03-02T15:00:00',
        end: '2015-03-02T18:00:00',
        color:'#00813E',
        className: 'id-nine'
      }
    ],
    eventRender: function(event,element,calEvent) {
      element.droppable({
        accept: '*',
        tolerance: 'touch',
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

// element.find('.fc-title').append("<br/>ksjdhfkjdshhksfkjh");

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
    eventAfterAllRender: function(view ){
      $('.fc-event').each(function(){
// console.log( $(this) );
/** AJAX call to server. If crew and or trucks are assigned, find id and append appropriate icon to event **/
        $(this).append('<img src="layouts/vlayout/modules/LocalDispatch/resources/images/ico-crew.png" alt="" class="ico-crew" />');
        $(this).append('<img src="layouts/vlayout/modules/LocalDispatch/resources/images/ico-truck.png" alt="" class="ico-truck" />');
      })

//                 $("<img/>").attr({
//                   'src': 'layouts/vlayout/modules/LocalDispatch/resources/images/ico-truck.png',
//                   'alt': '',
//                   'class': 'ico-truck'
//                 })
//                 .appendTo(element);

    },
    eventResize: function(event,dayDelta,minuteDelta,revertFunc){
// AJAX call to server to update times


//     $(this).find('.fc-content').append('saasnewclass');
// console.log(event.source.origArray);
// element.find('.fc-title').append("<br/>ksjdhfkjdshhksfkjh");
//       alert(
//           "The end date of " + event.title + "has been moved " +
//           dayDelta + " days and " +
//           minuteDelta + " minutes."
//       );
//
//       if (!confirm("is this okay?")) {
//           revertFunc();
//       }
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
      cursorAt: {top: -5,left: -5},
      helper:'clone',
      zIndex: 999,
      revert:true,
      revertDuration:800,
      start: function(e, ui){
        $(ui.helper).addClass("ui-draggable-helper");
      }
    });
  });

  function openNewForm(date, jsEvent, view){
    // Presence needed, don't remove
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

  $("#dispatch-panel-btn").click(function(){
    if($( "#dispatch-panel-rt" ).is(":hidden")){
      $("#dispatch-panel-btn").attr('class','icon-chevron-right');
      $("#dispatch-calendar").css('width','68%');
      $("#assets-container").css('width','30%');
    }else{
      $("#dispatch-panel-btn").attr('class','icon-chevron-left');
      $("#dispatch-calendar").css({'width':'98%','margin-right':'0'});
      $("#assets-container").css('width','1%');
    }
    $("#dispatch-panel-rt").toggle("right");
  });


});