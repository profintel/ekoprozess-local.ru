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
    $('select[name="region_federal_id"]').val(0);
  }
  if(type == 'federal' || type == 'country'){
    $('#region_id').parents('.form-group').addClass('loading');
    $('select[name="region_id"]').val(0);
    $('select[name="city_id"]').val(0);
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

/**
* Форма прихода / акта приемки
* добавляет в форму блок с вторсырьем
*/
function renderFieldsProducts(path, obj){
  $.post(path, {}, function(result) {
    //отображаем результат
    result = $.parseHTML(result);
    result = $(result).find('.form_block');
    if(result.length){
      $(obj).parents('.form_block').after($(result));
      $('#'+$(result).find('select').attr('id')).chosen({
        width: "100%",
        allow_single_deselect: true
      });
    }
  });
}

/**
* Удаление html блока с классом form_block
* в стандартном шаблоне формы
*/
function removeFormBlock(obj,path){
  return send_confirm('Вы уверены, что хотите удалить блок?',
    (typeof(path) != 'undefined' ? path : ''),{},
    function(){
      var form_block = $(obj).parents(".form_block")
      //если строка с заголовками, удаляем только Input-ы и кнопки
      if(form_block.hasClass('form_block_label')){
        form_block.find('.col-sm-10').remove();
        form_block.find('.btn').remove();
      } else{
        form_block.remove();
      }
      updateAcceptanceSumProduct();
      sheet('hide');
    }
  );
}

/**
* Обновляет стоимость вторсырья в форме акта приемки
*/
function updateAcceptanceSumProduct(){
  var form            = $(document).find('form'),
      formBlockItems  = form.find('.form_block'),
      containerAllSum = form.find('.all_sum'),
      add_expenses    = parseFloat(form.find('.add_expenses').val()),
      allSum  = 0;
  formBlockItems.each(function(key,item){
    var containerSum  = $(item).find('.sum_product'),
        count         = parseFloat($(item).find('.product_field_count').val()),
        price         = parseFloat($(item).find('.product_field_price').val()),
        sum           = count*price;
    if(containerSum.length){
      containerSum.text($.number(sum,2,'.'));
      allSum = allSum+sum;
    }
  })
  //дополнительную стоимость вычитаем из общей суммы
  if(add_expenses){
    allSum = allSum + add_expenses;
  }

  containerAllSum.text($.number(allSum,2,'.'));
}


/**
* Считает нетто в приходах и подставляет в input
*/
function updateComingNet(obj){
  var parent = $(obj).parents('.panel-body'),
      netInput = parent.find('[name="net[]"]'),
      gross = parseFloat(parent.find('[name="gross[]"]').val()),
      weight_defect = parseFloat(parent.find('[name="weight_defect[]"]').val());
      weight_pack = parseFloat(parent.find('[name="weight_pack[]"]').val());
  if(!$.isNumeric(weight_defect)){
    weight_defect = 0;
  }
  if(!$.isNumeric(weight_pack)){
    weight_pack = 0;
  }
  if(!$.isNumeric(gross)){
    gross = 0;
  }

  netInput.val(Math.round(gross - weight_pack - gross*weight_defect/100));
}