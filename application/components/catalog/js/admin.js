var catalog_title_slugging = true;

$(function() {
  if ($('#catalog-alias').val() != slug($('#catalog-title').val())) {
    catalog_title_slugging = false;
  }
  
  $('#catalog-title').keyup(function(event) {
    if (
        catalog_title_slugging
      && [9, 13, 16, 17, 18, 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 144].indexOf(event.keyCode) == -1
    ) {
      $('#catalog-alias').val(slug($(this).val()));
    }
  });
  
  $('#catalog-alias').keyup(function() {
    if ($(this).val() != '') {
        catalog_title_slugging = false;
    } else {
        catalog_title_slugging = true;
    }
  });

});