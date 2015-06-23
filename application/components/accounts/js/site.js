$(function() {
  $('select[name="group"]').change(function(){
    $('div[id^="registration"]').hide();
    $('#'+$(this).val()).show();
  })
});
$("a.confirm").live('click', function() {
  if (confirm('Вы уверены?')) {
    return true;
  }
  return false;
});

function open_form(obj) {
  sheet();
  $(obj).next('form').show();
}
function close_window(obj) {  
  $(obj).parents('form').hide();
  sheet('hide');
}