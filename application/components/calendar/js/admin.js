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
          createLocalEvent({start:start.format(),end:end.format()},addLastEvent);
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
        editEvent(calEvent,'fullCalendar');
      }
    });
  }
})

/* Модальное окно с формой добавления события календаря
* @param params - массив с данными для полей события
*/
function createLocalEvent(params, reaction){
  $.get('/admin/calendar/create_event/',params,function(result){
    var title = '', form = '';
    result = $.parseHTML(result);
    if(result){
      title = $(result).find('h1').text();
      form = $(result).find('.form-default').html();
      var btnSubmit = {text: 'Сохранить', handler: function() {
        submit_form($('form').find('input'), reaction); 
      }, icon: 'glyphicon-save'};
      my_modal('information', title, form, 
        [btnSubmit,'CANCEL'],
        {name:'shown.bs.modal',
          func:function(){
            $('#modal').find('.input-datetimepicker').datetimepicker({
              hourGrid: 4,
              minuteGrid: 10
            });
          }
        });
    } else {
      my_modal('error', 'Возникли следующие ошибки:', ['Невозможно создать событие'], 'OK');
    }
  })
}

/* Модальное окно с формой редактирования события календаря
* @param calEvent - данные события в формате json
*/
function editEvent(calEvent, reaction){
  $.post('/admin/calendar/edit_event/'+calEvent.id+'/',{},function(result){
    var title = '', form = '';
    result = $.parseHTML(result);
    if(result){
      title = $(result).find('h1').text();
      form = $(result).find('.form-default').html();
      var btnSubmit = {
        text: 'Сохранить', 
        handler: function() { 
          submit_form($('form').find('input'), (reaction == 'fullCalendar' ? updateEvent(calEvent) : 'reload')); 
        }, 
        icon: 'glyphicon-save'};
      my_modal('information', title, form, 
        [btnSubmit,'CANCEL'],
        {name:'shown.bs.modal',
          func:function(){
            $('#modal').find('.input-datetimepicker').datetimepicker({
              hourGrid: 2,
              minuteGrid: 10
            });
          }
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
  sheet('hide');
}