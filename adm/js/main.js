var search_timeout;

$(function() {
  $('.el-tooltip').tooltip();
  
  $('#autorization').keyup(function(event) {
    if (event.keyCode == 13 && $('#username').val() && $('#password').val()) {
      $('#autorization-submit').click();
    }
  });
  
  $.datepicker.regional['ru'] = {
  	closeText: 'Закрыть',
  	prevText: '<Пред',
  	nextText: 'След>',
  	currentText: 'Сегодня',
  	monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
  	'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
  	monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
  	'Июл','Авг','Сен','Окт','Ноя','Дек'],
  	dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
  	dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
  	dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
  	weekHeader: 'Не',
  	dateFormat: 'dd.mm.yy',
  	firstDay: 1,
  	isRTL: false,
  	showMonthAfterYear: false,
  	yearSuffix: ''
  };
  $.datepicker.setDefaults($.datepicker.regional['ru']);

  $.timepicker.regional['ru'] = {
  	timeOnlyTitle: 'Выберите время',
  	timeText: 'Время',
  	hourText: 'Часы',
  	minuteText: 'Минуты',
  	secondText: 'Секунды',
  	currentText: 'Сейчас',
  	closeText: 'ОК',
  	ampm: false
  };
  $.timepicker.setDefaults($.timepicker.regional['ru']);

  if($(document).find('.input-datetimepicker').length){
    var dateInputs = $(document).find('.input-datetimepicker');
    dateInputs.datetimepicker({
      hourGrid: 4,
      minuteGrid: 10,
      beforeShow: function(input, inst) {
        $('#ui-datepicker-div').removeClass('custom-datepicker');
        $('#ui-datepicker-div').addClass('custom-dateTimePicker');
      }
    });
    $.each(dateInputs,function(key,item){
      if($(item).data('mindate')){
        $(item).datepicker( "option", "minDate", $(item).data('mindate'));
      }
      if($(item).data('maxdate')){
        $(item).datepicker( "option", "maxDate", $(item).data('maxdate'));
      }
    })
  }

  if($(document).find('.input-datepicker').length){
    var dateInputs = $(document).find('.input-datepicker');
    dateInputs.datepicker({
      beforeShow: function(input, inst) {
        $('#ui-datepicker-div').removeClass('custom-dateTimePicker');
        $('#ui-datepicker-div').addClass('custom-datepicker');
      }
    });
    $.each(dateInputs,function(key,item){
      if($(item).data('mindate')){
        $(item).datepicker( "option", "minDate", $(item).data('mindate'));
      }
      if($(item).data('maxdate')){
        $(item).datepicker( "option", "maxDate", $(item).data('maxdate'));
      }
    })
  }
  
  $("a.confirm").on('click', function() {
    if (confirm('Вы уверены?')) {
      return true;
    }
    return false;
  });

  /*** Поиск ***/
  $('#search_string, input.autocomplete_search_string').focus(function() {
    if ($(this).val() == 'Поиск') {
      $(this).removeClass('def');
      $(this).val('');
    }
  });
  $('#search_string, input.autocomplete_search_string').blur(function() {
    if ($(this).val() == '') {
      $(this).addClass('def');
      $(this).val('Поиск');
    }
  });

  $('#search_string').keyup(function(event) {
    switch (event.keyCode) {
      case 9: break; //Tab
      case 13: break; //Enter
      case 16: break; //Shift
      case 17: break; //Ctrl
      case 18: break; //Alt
      case 19: break; //Pause
      case 20: break; //CapsLock
      case 27: break; //Esc
      case 32: break; //Space
      case 33: break; //PageUp
      case 34: break; //PageDown
      case 35: break; //End
      case 36: break; //Home
      case 37: break; //Left Arrow
      case 38: break; //Up Arrow
      case 39: break; //Right Arrow
      case 40: break; //Down Arrow
      case 144: break; //NumLock
      default:
        clearTimeout(search_timeout);
        search_timeout = setTimeout(search, 500);
      break;
    }
  });
  /******/
});

/*
 *  Определяет пользователь с мобильного или с ПК зашел
 *  @return boolean
**/
function isMobile(){
  var mobile = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) mobile = true;})(navigator.userAgent||navigator.vendor||window.opera);
 
  return mobile;
}

/*
* ВОзвращает на предыдущую страницу в истории браузера
*/
function goBack() {
  window.history.back();
}

/*** Поиск ***/
function search() {
  var input_obj = $('#search_string');
  var search_string = $.trim(input_obj.val());
  var search_results = $('#search_items_result');
  var component = input_obj.data('component');
  var name = input_obj.data('name');
  var method = input_obj.data('method');
  search_results.empty();
  if (search_string.length) {
    input_obj.addClass('loading');
    $.post('/admin/'+ component +'/'+ method +'/', {search_string: search_string}, function(result) {
      input_obj.removeClass('loading');
      $('#def_items').hide();
      $('#search_info_all span').text(result.count);
      $('#search_info_view span').text(result.items.length);
      $('#search_items').show();
      
      var html = '';
      $.each(result.items, function(num, item) {
        html += '<div class="panel selection"><div class="left">';
        html += '<a href="/admin/'+ component +'/edit_'+ name +'/'+item.id+'/">'+item.title+'</a>';
        html += '</div>';
        html += '<div class="right">';
        html += '<div class="buttons">';
        if (item.active == 1) {
          html += '<a title="Отключить" class="lightbulb_i_s" href="/admin/'+ component +'/disable_'+ name +'/'+item.id+'/"></a>';
        } else {
          html += '<a title="Включить" class="lightbulb_off_i_s" href="/admin/'+ component +'/enable_'+ name +'/'+item.id+'/"></a>';
        }
        html += '<a title="Изменить" class="pencil_i_s" href="/admin/'+ component +'/edit_'+ name +'/'+item.id+'/"></a>';
        html += '<a title="Удалить" class="confirm cross_i_s" href="/admin/'+ component +'/delete_'+ name +'/'+item.id+'/2/"></a>';
        html += '</div><div class="clear"></div></div><div class="clear"></div></div>';    
      });
      search_results.append(html);
    }, 'json');
  } else {
    search_hide();
  }
}
function search_hide() {
  $('#search_items').hide();
  $('#def_items').show();
  $('#search_string').val('').blur();
  return false;
}
function search_item_del(name,component,item) {
  return send_confirm('Вы уверены, что хотите удалить запись '+item.title+'?','/admin/'+ component +'/delete_'+ name +'/'+item.id+'/',{},'reload');
}

function sheet(action) {
  if (action == 'hide') {
    $('#progress-main .progress-bar').removeClass('active');
    $('#progress-main').hide();
  } else {
    $('#progress-main .progress-bar').addClass('active');
    $('#progress-main').show();
  }
  
  return false;
}

function alert_msg(type,message) {
  $('#alert_msg').addClass('alert-'+type).text(message).fadeIn(400,function(){
    setTimeout(function(){
      $('#alert_msg').fadeOut(200,function(){
        $('#alert_msg').addClass('alert-'+type);
        $('#alert_msg').text("");
      });
    },2000)
  });
  
  return false;
}

function send_confirm(message, url, data, reaction, context, reactionCancel) {
  return my_modal('information', 'Требуется подтверждение', message, [
    {text: 'OK', handler: function(){
      my_modal('hide');

      if(!url){
        // если строкой передали название функции, проверяеми наличие функции и выполняем ее
        if (typeof(reaction) == 'function') {
          reaction.call();
        } else {
          reaction = window[reaction];
          if(typeof(reaction) == 'function'){
            context = context.split(',');
            return reaction(context[0],context[1],context[2],context[3]);
          } else {
            return my_modal('error', 'Возникли следующие ошибки:', 'Не найден метод для обработки запроса', 'OK');
          }
        }
      } else {
        return send_request(url, data, reaction, context);
      }
    }, icon: 'glyphicon-ok'},
    {text: 'Отмена', handler: function(){
      my_modal('hide'); sheet('hide');
      if(typeof(reactionCancel) == 'function'){
        reactionCancel.call();
      }
    }, icon: 'glyphicon-remove', class: 'btn-default'}
  ]);
}

function send_request(url, data, reaction, context) {
  sheet();
  
  url  = (url ? url : '');
  data = (typeof(data) == 'object' ? data : {});
  
  $.ajax({
    type: 'POST',
    url:  url,
    data: data,
    success: function(answer) {
      handle_answer(answer, reaction, context);
    },
    error: function(answer) {
      handle_answer(answer);
    }
  });
  
  return false;
}

function submit_form(context, reaction, uri_postfix, data_type) {
  sheet();
  var form = $(context).parents('form');
  var path = form.attr('action');

  form.children('.form-error').text("").removeClass('alert alert-danger');
  form.find('.has-error').removeClass('has-error');
  form.find('.error').remove();
  
  if (uri_postfix) {
    form.attr('action', path + uri_postfix);
  }

  // если метод get меняем путь браузера и сохраняем в историю браузера ссылку
  if(form.attr('method') == 'GET'){
    if($.isFunction(window.history.pushState) === true){
      // get параметры из формы
      var queryString = form.formSerialize();
      //меняем путь и сохраняем в историю браузера ссылку
      var pathLocation = 'http://' + window.location.hostname + window.location.pathname+'?'+queryString;
      window.history.pushState({}, document.title, pathLocation);
    } else {
      submit_form_sync(context);
      return false;
    }
  }

  form.ajaxSubmit(function(answer) {
    handle_answer(answer, reaction, context, data_type);
  });
  
  form.attr('action', path);
  
  return false;
}

function submit_form_sync(context) {
  $(context).parents('form').attr('onSubmit', '').submit();
  return false;
}

function handle_answer(answer, reaction, context, data_type) {
  if (!answer) {
    return my_modal('error', 'Возникли следующие ошибки:', 'Некорректный ответ сервера', 'OK');
  }

  if(!data_type){
    data_type = 'json';
  }
  if(data_type == 'html'){
    answer = $.parseHTML(answer);
  }

  if(data_type == 'json'){
    try {
      answer = $.parseJSON(answer);
    } catch (e) {
      return my_modal('error', 'Возникли следующие ошибки:', answer, 'OK');
    }
  }
  
  if (answer.sysmsg) {
    return handle_sysmsg(answer.sysmsg);
  }

  if(typeof(answer.confirm) == 'object' && !$.isEmptyObject(answer.confirm)) {
    return send_confirm(answer.confirm.message, answer.confirm.url, answer.confirm.data, answer.confirm.reaction, answer.confirm.context, answer.confirm.reactionCancel);
  }

  if(typeof(answer.errors) == 'object' && !$.isEmptyObject(answer.errors)) {
    if(typeof(context) == "undefined"){
      setTimeout(function(){
        my_modal('error', 'Возникли следующие ошибки:', answer.errors, 'OK');
      },400)
    } else {
      var form = $(context).parents('form'), input, error;
      $.each(answer.errors, function(key,item){
        input = form.find('[name="'+key+'"]');
        if (input.length){
          input.parents('.form-group').addClass('has-error');
          input.before('<small class="error text-danger">'+item+'</small>');
          $(document).scrollTop(input.scrollTop());
        } else {
          error = form.children('.form-error');

          if(error.length){
            error.text(item).addClass('alert alert-danger');
            $(document).scrollTop(error.scrollTop());
          } else {
            my_modal('error', 'Возникли следующие ошибки:', answer.errors, 'OK');
          }
        }
      })
    }
    sheet('hide');
  } else if(typeof(answer.redirect) != 'undefined') {
    document.location = answer.redirect;
  } else {
    var form = $(context).parents('form');
    if (!reaction || reaction == 'null') {
      sheet('hide');
      if(typeof(answer.success) == 'object' && !$.isEmptyObject(answer.success)) {
        alert_msg('success',answer.success);
      }
      if (typeof(answer.messages) == 'object' && answer.messages.length) {
        return my_modal('information', 'Уведомление', answer.messages, 'OK');
      }
    } else if (typeof(reaction) == 'function') {
      if (typeof(answer.messages) == 'object' && answer.messages.length) {
        return my_modal('information', 'Уведомление', answer.messages, [{text: 'OK', handler: reaction, icon: 'glyphicon-ok'}]);
      } else {
        reaction.call((context ? context : this), answer);
      }
    } else if (reaction == 'reload') {
      if (typeof(answer.messages) == 'object' && answer.messages.length) {
        return my_modal('information', 'Уведомление', answer.messages, [{text: 'OK', handler: function() { document.location.reload(); }, icon: 'glyphicon-ok'}]);
      } else {
        document.location.reload();
      }
    } else {
      if (typeof(answer.messages) == 'object' && answer.messages.length) {
        return my_modal('information', 'Уведомление', answer.messages, [{text: 'OK', handler: function() { document.location = reaction; }, icon: 'glyphicon-ok'}]);
      } else {
        document.location = reaction;
      }
    }
  }
  
  return false;
}

/**
* Отображает html результат в #ajaxResult
* @params context - елемент формы
*         answer - json результат запроса формы
*/
function handle_ajaxResultHTML(answer) {
  var element = '#ajax_result';
  var container = $(answer).find(element);
  if(container.length){
    $(document).find('#ajax_result').fadeOut(100,function(){
      $(this).html($(container).html())
      $(this).fadeIn(200,function(){
        sheet('hide');
      });
    })
  }
}

/**
* В цикле выполняет запрос данных из базы, с учетом к-ва страниц
* Отображает html результат в #ajaxResult
* @params context - елемент формы
*         answer - json результат запроса формы
*/
function handle_ajaxResultAllData(answer) {
  var element = '#ajax_result';
  var container = $(answer.html).find(element);
  var form = $('#btn-form').parents('form');    
  if(container.length){
    // добавляем в ajax_result данные
    
    //скрываем пагинацию, если есть 
    $(document).find('.pagination-wrap').hide();
    // если страница первая вставляем анимацию смены контента
    if(parseInt(answer.page) == 1){
      $(document).find(element).fadeOut(100,function(){
        $(this).html($(container).html());
        $(this).fadeIn(200);
      })
    } else {
      $(document).find('#table-result tbody').append($(container).find('#table-result tbody').html());
    }
  }
  
  // если страниц несколько в цикле делаем запрос на остальные данные
  if(parseInt(answer.pages) > parseInt(answer.page)){
    // добавляем input hidden с номером страницы в форму
    // if(!$(form).find('input[name=page]').length){
    //   $(form).append('<input type="hidden" name="page" value="'+(parseInt(answer.page)+1)+'">');
    // } else {
    //   $(form).find('input[name=page]').val((parseInt(answer.page)+1));
    // }
    submit_form($('#btn-form'), handle_ajaxResultAllData, '&page=' + (parseInt(answer.page)+1));
  } else {
    sheet('hide');
    // $(form).find('input[name=page]').remove();
  }
}

function handle_sysmsg(msg) {
  eval('handle_sys_'+ msg +'()');
}

function handle_sys_UNAUTH() {
  document.location = '/autorization/';
}

function locationUrl(url) {
  document.location = url;
}

var defButtons = {
  'OK':     {text: 'OK',     handler: function() { my_modal('hide'); sheet('hide'); }, icon: 'glyphicon-ok'},
  'CANCEL': {text: 'Отмена', handler: function() { my_modal('hide'); sheet('hide'); }, icon: 'glyphicon-remove', class: 'btn-default'}
};

/**
* Формирует html модального окна
* type - тип окна
* title - Заголовок в modal-title
* messages - Сообщения добавляются в modal-body
* buttons - Кнопки в modal-footer
* events - объект с событиями
*/
function my_modal(type, title, messages, buttons, events) {
  var m = $('#modal');
  
  if (type == 'hide') {
    m.modal('hide');
    $('.modal-backdrop').remove();
    return false;
  }
  
  var t = m.find('.modal-title').text(title ? title : '');
  var i = m.find('.modal-body').html('');
  var b = m.find('.modal-footer').html('').hide();
  
  // размер модального окна
  if (!type) { type = 'modal-md'; }
  m.find('.modal-dialog').addClass(type);

  if (typeof(messages) == 'object') {
    messages = messages.join('<br />');
  }
  
  if (buttons) {
    if (typeof(buttons) == 'object') {
      $(buttons).each(function(i, button) {
        make_button(b, button)
      });
    } else {
      make_button(b, buttons);
    }
    b.show();
  }

  if (typeof(events) == 'object') {
    $(events).each(function(i, ev) {
      if (typeof(ev.name) != 'undefined' && typeof(ev.func) == 'function') {
        m.on(ev.name,function(e){
          func = ev.func;
          func.call(e);
        })
      }
    });
  }
  
  i.html(messages);
  
  m.modal();

  return false;
}

function make_button(container, button) {
  if (typeof(button) != 'object') {
    if (defButtons[button]) {
      button = defButtons[button];
    } else {
      button = {text: button};
    }
  }
  
  $(document.createElement('a'))
    .attr({
      href: (button.link ? button.link : 'javascript:void(0)'),
      'class': 'btn btn-xs '+(button.class ? button.class : 'btn-primary')
    })
    .html(button.text ? (button.icon ? '<span class="glyphicon '+ button.icon +'"></span> ' : '')+button.text : '')
    .click(button.handler)
    .appendTo(container);
}

/**
* Сворачивает/Разворачивает блоки 
* в карточке клиента
*/
function togglePanel(obj){
  var container = $(obj).parents('.panel-body').find('.panel-body__fields');
  if(container.length){
    if(container.hasClass('panel-body__fields_close')){
      container.removeClass('panel-body__fields_close').addClass('panel-body__fields_open');
      $(obj).html('<span class="glyphicon glyphicon-menu-up"></span> свернуть');
    } else {
      container.removeClass('panel-body__fields_open').addClass('panel-body__fields_close');
      $(obj).html('<span class="glyphicon glyphicon-menu-down"></span> развернуть');
    }
  }
}

/**
* Меняет значения select компаний (дочерние клиенты) по клиентам
*/
function changeClientChilds(onchange){
  $('#client_child_id').parents('.form-group').addClass('loading').val(0);
  //id клиента
  client_id = $('select[name="client_id"]').val();

  $.post('/admin/clients/renderSelectClientChilds/', {client_id: client_id, onchange:(onchange?1:0)}, function(result) {
    // console.log('result',result, typeof result.html);
    if(typeof result.html != 'undefined'){
      $('#client_child_id').parents('.form-group').find('.col-sm-10').html($(result.html).find('.col-sm-10').html());
      $('#client_child_id').chosen({
        width: "100%",
        allow_single_deselect: true
      });
      $('#client_child_id').parents('.form-group').removeClass('loading');
    }
  },'json')
}