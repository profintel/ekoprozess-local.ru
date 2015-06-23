/**
* Меняет значения select регионов и городов в отчете по клиентам
* @param el - текущий элемент DOM
*        type - тип региона (федеральный округ или регион)
*/
function changeRegion(el,type){
  if(type == 'federal'){
    $('#region_id').parents('.form-group').addClass('loading');
  }
  $('#city_id').parents('.form-group').addClass('loading');
  //id федерального округа
  var html, id = $(el).val();
  $.post('/admin/clients/renderSelectsReport/', {type:type, id: id}, function(result) {
    if(type == 'federal'){
      //регионы
      html = $(result.regions).find('.col-sm-10').html();
      $('#region_id').parents('.form-group').find('.col-sm-10').html("").append(html);
      $('#region_id').chosen({
        width: "100%",
        allow_single_deselect: true
      });
      $('#region_id').parents('.form-group').removeClass('loading');
    }
    //города
    html = $(result.city).find('.col-sm-10').html();
    $('#city_id').parents('.form-group').find('.col-sm-10').html("").append(html);
    $('#city_id').chosen({
      width: "100%",
      allow_single_deselect: true
    });
    $('#city_id').parents('.form-group').removeClass('loading');
  },'json')
}