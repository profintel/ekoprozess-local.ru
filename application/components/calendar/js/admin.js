// $(function() {
//   if($(document).find('#calendar').length){
//     $('#calendar').fullCalendar({
//       header: {
//         left: 'prev,next today',
//         center: 'title',
//         right: 'month,agendaWeek,agendaDay'
//       },
//       businessHours:{
//         start: '8:00',
//         end: '19:00',
//         dow: [ 1, 2, 3, 4, 5 ]
//       },
//       selectable: true,
//       select: function(start, end, jsEvent, view) {
//         if (view.name != 'month') {
//           createEvent(start.format(),end.format());
//           $('#calendar').fullCalendar('unselect');
//         }
//       },
//       dayClick: function(date, jsEvent, view) {
//         if (view.name == 'month') {
//           $('#calendar').fullCalendar('changeView', 'agendaDay');
//           $('#calendar').fullCalendar('gotoDate', date.format());
//         }
//       },
//       eventLimit: true,
//       events: {
//         url: '/admin/calendar/get_events/',
//         data: function() {
//           return {
//             dynamic_value: Math.random()
//           };
//         }
//       },
//       eventRender: function(event, element) {
//         element.attr('title', event.title);
//         element.attr('data-content', event.description);
        
//       },
//       eventMouseover: function( event, jsEvent, view ){
//         $(this).popover({container:'body',placement:'bottom'}).popover('show');
//       },
//       eventMouseout: function( event, jsEvent, view ){
//         $(this).popover('hide');
//       },
//       editable: true,
//       eventDrop: function(event, delta, revertFunc) {
//         params = {id:event.id,start:event.start.format(),end:((event.end != null) ? event.end.format() : null)}
//         return false;
//         $.post('/admin/calendar/eventDrop/',params,function(result){
//           if(typeof(result.errors) == 'object' && !$.isEmptyObject(result.errors)){
//             my_modal('error', 'Возникли следующие ошибки:', result.errors, 'OK');
//             revertFunc();
//           }
//         },'json')
//       },
//       eventClick: function(calEvent, jsEvent, view) {
//         editEvent(calEvent);
//       }
//     });
//   }
// })
// //Модальное окно с формой добавления события календаря
// function createEvent(start, end){
//   $.get('/admin/calendar/create_event/',{start:start, end:end},function(result){
//     var title = '', form = '';
//     result = $.parseHTML(result);
//     if(result){
//       title = $(result).find('h1').text();
//       form = $(result).find('.form-default').html();
//       var btnSubmit = {text: 'Сохранить', handler: function() { submit_form($('form').find('input'), addLastEvent()); }, icon: 'glyphicon-save'};
//       my_modal('information', title, form, [btnSubmit,'CANCEL']);
//     } else {
//       my_modal('error', 'Возникли следующие ошибки:', ['Невозможно создать событие'], 'OK');
//     }
//   })
// }
// //Модальное окно с формой редактирования события
// function editEvent(calEvent){
//   $.post('/admin/calendar/edit_event/'+calEvent.id+'/',{},function(result){
//     var title = '', form = '';
//     result = $.parseHTML(result);
//     if(result){
//       title = $(result).find('h1').text();
//       form = $(result).find('.form-default').html();
//       var btnSubmit = {text: 'Сохранить', handler: function() { submit_form($('form').find('input'), updateEvent(calEvent)); }, icon: 'glyphicon-save'};
//       my_modal('information', title, form, [btnSubmit,'CANCEL']);
//       $('.input-datetimepicker').datetimepicker({
//         hourGrid: 2,
//         minuteGrid: 5
//       });
//     } else {
//       my_modal('error', 'Возникли следующие ошибки:', ['Ошибка при сохранении изменений'], 'OK');
//     }
    
//   })
// }
// //Обновление данных события после отправки формы редактирования
// function updateEvent(calEvent){
//   setTimeout(function(){
//     $.post('/admin/calendar/get_event/'+calEvent.id+'/',function(result){
//       if(result){
//         calEvent.title = result.title;
//         calEvent.description = result.description;
//         calEvent.start = result.start;
//         calEvent.end = result.end;
//         calEvent.color = result.color;
//         $('#calendar').fullCalendar('updateEvent', calEvent);
//         my_modal('hide');
//       }
//     },'JSON');
//   },100);
// }
// //Отрисовка события в календаре после отправки формы добавления
// function addLastEvent(){
//   setTimeout(function(){
//     $.post('/admin/calendar/get_lastEvent/',function(result){
//       if(result){
//         $('#calendar').fullCalendar('renderEvent', result, true); // stick? = true
//         my_modal('hide');
//       }
//     },'JSON');
//   },100)
// }