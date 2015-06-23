$(function() {
  if ($('input[name="query_string"]').length) {
    $('input[name="query_string"]').click(function(){
      if ($(this).val() == "Поиск...") {
        $(this).val("");
      }
    });
  }
})