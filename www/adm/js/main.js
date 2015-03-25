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

var defButtons = {
  'OK':     {text: 'OK',     handler: function() { return my_modal('hide'); }, icon: 'glyphicon-ok'},
  'CANCEL': {text: 'Отмена', handler: function() { return my_modal('hide'); }, icon: 'glyphicon-remove'}
};

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

function send_confirm(message, url, data, reaction, context) {
  return my_modal('information', 'Требуется подтверждение', message, [
    {text: 'OK', handler: function() {
      my_modal('hide');
      return send_request(url, data, reaction, context);
    }, icon: 'glyphicon-ok'},
    'CANCEL'
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

function submit_form(context, reaction, uri_postfix) {  
  var form = $(context).parents('form');
  var path = form.attr('action');
  
  if (uri_postfix) {
    form.attr('action', path + uri_postfix);
  }
  
  form.ajaxSubmit(function(answer) {
    handle_answer(answer, reaction, context);
  });
  
  form.attr('action', path);
  
  return false;
}

function submit_form_sync(context) {
  $(context).parents('form').attr('onSubmit', '').submit();
  return false;
}

function handle_answer(answer, reaction, context) {
  if (!answer) {
    return my_modal('error', 'Возникли следующие ошибки:', 'Некорректный ответ сервера', 'OK');
  }
  
  try {
    answer = $.parseJSON(answer);
  } catch (e) {
    return my_modal('error', 'Возникли следующие ошибки:', answer, 'OK');
  }
  
  if (answer.sysmsg) {
    return handle_sysmsg(answer.sysmsg);
  }

  if (typeof(answer.errors) == 'object' && !$.isEmptyObject(answer.errors)) {    
    // return my_modal('error', 'Возникли следующие ошибки:', answer.errors, 'OK');
    var form = $(context).parents('form'), input, error;
    $.each(answer.errors, function(key,item){
      input = form.find('[name="'+key+'"]');
      if (input.length){
        input.parents('.form-group').addClass('has-error');
      } else {
        error = form.children('.form-error');
        error.text(item).addClass('alert alert-danger');
        $(document).scrollTop(error.scrollTop());
      }
    })
    console.log(answer.errors);
  } else {
    if (!reaction) {
      if (typeof(answer.messages) == 'object' && answer.messages.length) {
        return my_modal('information', '', answer.messages, 'OK');
      } else {
        sheet('hide');
      }
    } else if (typeof(reaction) == 'function') {
      if (typeof(answer.messages) == 'object' && answer.messages.length) {
        return my_modal('information', '', answer.messages, [{text: 'OK', handler: reaction, icon: 'glyphicon-ok'}]);
      } else {
        reaction.call((context ? context : this), answer);
      }
    } else if (reaction == 'reload') {
      if (typeof(answer.messages) == 'object' && answer.messages.length) {
        return my_modal('information', '', answer.messages, [{text: 'OK', handler: function() { document.location.reload(); }, icon: 'glyphicon-ok'}]);
      } else {
        document.location.reload();
      }
    } else {
      if (typeof(answer.messages) == 'object' && answer.messages.length) {
        return my_modal('information', '', answer.messages, [{text: 'OK', handler: function() { document.location = reaction; }, icon: 'glyphicon-ok'}]);
      } else {
        document.location = reaction;
      }
    }
  }
  
  return false;
}

function handle_sysmsg(msg) {
  eval('handle_sys_'+ msg +'()');
}

function handle_sys_UNAUTH() {
  document.location = '/autorization/';
}

function my_modal(type, title, messages, buttons) {
  var m = $('#modal');
  
  if (type == 'hide') {
    $('#modal').modal('hide');
    return false;
  }
  
  var t = m.find('.modal-title').text(title ? title : '');
  var i = m.find('.modal-body').html('');
  var b = m.find('.modal-footer').html('').hide();
  
  if (!type) { type = 'information'; }
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
  
  i.html(messages);
  
  $('#modal').modal();
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
      href: (button.link ? button.link : '#'),
      'class': 'btn btn-primary btn-xs'
    })
    .html(button.text ? (button.icon ? '<span class="glyphicon '+ button.icon +'"></span> ' : '')+button.text : '')
    .click(button.handler)
    .appendTo(container);
}