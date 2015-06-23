var publication_title_slugging = true;

$(function() {
  if ($('#publication-alias').val() != slug($('#publication-title').val())) {
    publication_title_slugging = false;
  }
  
  $('#publication-title').keyup(function(event) {
    if (
      publication_title_slugging
      && [9, 13, 16, 17, 18, 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 144].indexOf(event.keyCode) == -1
    ) {
      $('#publication-alias').val(slug($(this).val()));
    }
  });
  
  $('#publication-alias').keyup(function() {
    if ($(this).val() != '') {
      publication_title_slugging = false;
    } else {
      publication_title_slugging = true;
    }
  });

});