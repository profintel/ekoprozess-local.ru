var item_title_slugging = true;

$(function() {
  if ($('#item-alias').val() != slug($('#item-title').val())) {
    item_title_slugging = false;
  }
  
  $('#item-title').keyup(function(event) {
    if (
      item_title_slugging
      && [9, 13, 16, 17, 18, 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 144].indexOf(event.keyCode) == -1
    ) {
      $('#item-alias').val(slug($(this).val()));
    }
  });
  
  $('#item-alias').keyup(function() {
    if ($(this).val() != '') {
      item_title_slugging = false;
    } else {
      item_title_slugging = true;
    }
  });

});