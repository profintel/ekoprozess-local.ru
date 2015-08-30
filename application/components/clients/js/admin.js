$(document).ready(function(){
  //Постраничная навигация ajax
  if($(document).find('#pagination_ajax').length && $.isFunction(window.history.pushState) === true){
    $(document).on('click', '#pagination_ajax a', function(e){
      e.preventDefault();
      locationPagination($(this));
    })  
  }
})

/**
* Обновление контента
* в постраничной навигации
*/
function locationPagination(obj){
  var href, element = '#ajax_result';
  $(element).children("div").fadeOut(400, function(){
    $(element).addClass('loading');
    href = obj.attr('href');
    //меняем путь и сохраняем в историю браузера ссылку
    window.history.pushState({}, document.title, href);
    //получаем html страницы
    $.get(href,function(answer){
      //отображаем результат
      result = $.parseHTML(answer);
      result = $(result).find(element);        
      if(result.length){
        $(document).find(element).html(result.html());
        $(element).removeClass('loading');
      }
    })
  })
}

/**
* Меняет значения select регионов и городов в отчете по клиентам
* @param el - текущий элемент DOM
*        type - тип региона (федеральный округ или регион)
*/
function changeRegion(el,type){
  if(type == 'country'){
    $('#region_federal_id').parents('.form-group').addClass('loading');
  }
  if(type == 'federal' || type == 'country'){
    $('#region_id').parents('.form-group').addClass('loading');
  }
  $('#city_id').parents('.form-group').addClass('loading');
  //id федерального округа
  var html, id = $(el).val();
  $.post('/admin/clients/renderSelectsReport/', {type:type, id: id}, function(result) {
    if(type == 'country'){
      //регионы
      html = $(result.federal_regions).find('.col-sm-10').html();
      $('#region_federal_id').parents('.form-group').find('.col-sm-10').html("").append(html);
      $('#region_federal_id').chosen({
        disable_search: true,
        width: "100%",
        allow_single_deselect: true
      });
      $('#region_federal_id').parents('.form-group').removeClass('loading');
    }
    if(type == 'federal' || type == 'country'){
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