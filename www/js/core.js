function sheet(show) {
  if (typeof(show) == 'undefined' || show) {
    $('body').append('<div id="default-generated-sheet-loading"></div><div id="default-generated-sheet"></div>');
  } else {
    $('#default-generated-sheet-loading, #default-generated-sheet').remove();
  }
  return false;
}

function submit_form_sync(context) {
  $(context).parents('form').attr('onSubmit', '').submit();
  return false;
}

function submit_form(context, success, failure) {
  sheet();
  $(context).parents('form').ajaxSubmit(function(answer) {
    handle_answer(answer, context, success, failure);
  });
  return false;
}

function handle_answer(answer, context, success, failure) {
  var failure_func = (failure == 'alert' ? alert_message : failure);
  
  if (!answer) {
    return (failure ? failure_func.call((context ? context : this), 'Некорректный ответ сервера') : sheet(false));
  }
  
  try {
    answer = $.parseJSON(answer);
  } catch (e) {
    return (failure ? failure_func.call((context ? context : this), answer) : sheet(false));
  }
  
  if (typeof(answer.errors) == 'object' && answer.errors.length) {
    return (failure ? failure_func.call((context ? context : this), answer.errors) : sheet(false));
  }
  
  if (!success) {
    return sheet(false);
  }
  
  if (typeof(success) == 'function') {
    return success.call((context ? context : this), answer);
  }
  
  if (success == 'reload') {
    document.location.reload();
  } else {
    document.location = success;
  }
  
  return false;
}

function alert_message(message) {
  if (typeof(message) == 'object') {
    message = message.join("\n");
  }
  alert(message);
  sheet(false);
  return false;
}