<link rel="stylesheet" href="/components/calendar/media/fullcalendar/fullcalendar.css">
<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    Календарь событий
  </h1>
</div>

<div class="container-fluid">
  <div id='calendar' class="col-xs-12 block_mb20"></div>
</div>

<script src='/components/calendar/media/fullcalendar/moment.min.js'></script>
<script src='/components/calendar/media/fullcalendar/fullcalendar.min.js'></script>
<script src='/components/calendar/media/fullcalendar/ru.js'></script>

<script>
$(function() {
  if($(document).find('#calendar').length){
    $(document).on('click', function(){
      $('.popover').popover('hide');
    });
    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      businessHours:{
        start: '8:00',
        end: '19:00',
        dow: [ 1, 2, 3, 4, 5 ]
      },
      selectable: true,
      select: function(start, end, jsEvent, view) {
        if (view.name != 'month') {
          console.log(view.name);
          createEvent(start.format(),end.format());
          $('#calendar').fullCalendar('unselect');
        }
      },
      dayClick: function(date, jsEvent, view) {
        if (view.name == 'month') {
          $('#calendar').fullCalendar('changeView', 'agendaDay');
          $('#calendar').fullCalendar('gotoDate', date.format());
        }
      },
      eventLimit: true,
      events: {
        url: '/admin/calendar/get_events/',
        data: function() {
          return {
            dynamic_value: Math.random()
          };
        }
      },
      eventRender: function(event, element) {
        element.addClass('popover');
        element.attr('title', event.title);
        element.attr('data-content', event.description);
        
      },
      eventMouseover: function( event, jsEvent, view ){
        $(this).popover({container:'body',placement:'bottom'}).popover('show');
      },
      eventMouseout: function( event, jsEvent, view ){
        $(this).popover('hide');
      },
      editable: true,
      eventDrop: function(event, delta, revertFunc) {
        $(this).popover('destroy');
        // console.log('eventDrop',event.allDay);
        params = {id:event.id,allDay:event.allDay,start:event.start.format(),end:((event.end != null) ? event.end.format() : null)}
        $.post('/admin/calendar/eventDrop/',params,function(result){
          if(typeof(result.errors) == 'object' && !$.isEmptyObject(result.errors)){
            my_modal('error', 'Возникли следующие ошибки:', result.errors, 'OK');
            revertFunc();
          }
        },'json');
      },
      eventResize: function( event, delta, revertFunc, jsEvent, ui, view ) {
        $(this).popover('destroy');
        // console.log('eventResize',event.allDay);
        params = {id:event.id,allDay:event.allDay,start:event.start.format(),end:((event.end != null) ? event.end.format() : null)}
        $.post('/admin/calendar/eventDrop/',params,function(result){
          if(typeof(result.errors) == 'object' && !$.isEmptyObject(result.errors)){
            my_modal('error', 'Возникли следующие ошибки:', result.errors, 'OK');
            revertFunc();
          }
        },'json');
      },
      eventClick: function(calEvent, jsEvent, view) {
        $(this).popover('destroy');
        editEvent(calEvent);
      }
    });
  }
})
//Модальное окно с формой добавления события календаря
function createEvent(start, end){
  $.get('/admin/calendar/create_event/',{start:start, end:end},function(result){
    var title = '', form = '';
    result = $.parseHTML(result);
    if(result){
      title = $(result).find('h1').text();
      form = $(result).find('.form-default').html();
      var btnSubmit = {text: 'Сохранить', handler: function() { submit_form($('form').find('input'), addLastEvent()); }, icon: 'glyphicon-save'};
      my_modal('information', title, form, [btnSubmit,'CANCEL']);
    } else {
      my_modal('error', 'Возникли следующие ошибки:', ['Невозможно создать событие'], 'OK');
    }
  })
}
//Модальное окно с формой редактирования события
function editEvent(calEvent){
  $.post('/admin/calendar/edit_event/'+calEvent.id+'/',{},function(result){
    var title = '', form = '';
    result = $.parseHTML(result);
    if(result){
      title = $(result).find('h1').text();
      form = $(result).find('.form-default').html();
      var btnSubmit = {text: 'Сохранить', handler: function() { submit_form($('form').find('input'), updateEvent(calEvent)); }, icon: 'glyphicon-save'};
      my_modal('information', title, form, [btnSubmit,'CANCEL']);
      $('.input-datetimepicker').datetimepicker({
        hourGrid: 2,
        minuteGrid: 5
      });
    } else {
      my_modal('error', 'Возникли следующие ошибки:', ['Ошибка при сохранении изменений'], 'OK');
    }
    
  })
}
//Обновление данных события после отправки формы редактирования
function updateEvent(calEvent){
  setTimeout(function(){
    $.post('/admin/calendar/get_event/'+calEvent.id+'/',function(result){
      if(result){
        calEvent.title = result.title;
        calEvent.description = result.description;
        calEvent.start = result.start;
        calEvent.end = result.end;
        calEvent.color = result.color;
        $('#calendar').fullCalendar('updateEvent', calEvent);
        my_modal('hide');
      }
    },'JSON');
  },100);
}
//Отрисовка события в календаре после отправки формы добавления
function addLastEvent(){
  setTimeout(function(){
    $.post('/admin/calendar/get_lastEvent/',function(result){
      if(result){
        $('#calendar').fullCalendar('renderEvent', result, true); // stick? = true
        my_modal('hide');
      }
    },'JSON');
  },100)
}
</script>