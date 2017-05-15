$(function() {
  // при клике на ячейки таблицы .table-dropdown открываем меню dropdown
  $(document).on('click', '.table-dropdown td', function(e){
    if(this.cellIndex > 0){
      e.preventDefault();
      e.stopPropagation();
      $(this).parent().find('[data-toggle="dropdown"]').dropdown('toggle');
    }
  });
})

/**
* Отправление прихода в учет остатков
*/
function sendMovement(url, obj){
  return send_confirm(
    'Вы уверены, что хотите отправить на склад? После выполнения объект будет учтен в остатках, вес нельзя будет отредактировать.',
    url,
    {},
    function(){
      if(!url){
        submit_form(obj,'reload','sendMovement/');
      } else {
        document.location.reload();
      }
    },
    obj
  );
}

/**
* Подсчет остатков сырья на складе
*/
function updateRestProduct(obj){
  var form, store_type_id, client_id, products, form_block, rest;
  form =  $(obj).parents('form');
  // смотрим client_id и пересчитываем все остатки по указанному вторсырью
  params = {
    store_type_id:form.find('input[name="store_type_id"]').val(),
    client_id:(form.find('select[name="client_id"]').length ? form.find('select[name="client_id"]').val() : null),
    store_workshop_id:(form.find('select[name="store_workshop_id"]').length ? form.find('select[name="store_workshop_id"]').val() : null),
    // дата используется для формы расхода
    date:form.find('input[name="date"]').val(),
    product_id:0,
  }
  if((params.store_type_id == 1 && params.client_id) || params.store_type_id == 2){
    // находим все select-ы с вторсырьем и запрашиваем остатки
    products = form.find('select[name="product_id[]"]');
    products.each(function(key,item){
      // если значение указано, запрашиваем остатки
      if($(item).val()){
        params.product_id = $(item).val();
        $.post('/admin/store/get_rest_product/',
          params,
          function(result){
            form_block = $(item).parents('.form_block');
            // Обнуляем остатки
            form_block.find('.rest, .rest_product').text('0.00');
            if(result){
              form_block.find('.rest').text(result.rest);
              form_block.find('.rest_product').text(result.rest_product);
            }
          },
        'json')
      }
    })
  }
}

/**
* Обновление списка клиентов по выбранным параметрам вторсырья
*/
function updateClientsRests(obj){
  var form, store_type_id, client_id, products, form_block, rest;
  form =  $(obj).parents('form');
  // смотрим все указанныые параметры вторсырья и ищем клиентов, по которым числятся такие остатки
  params = {
    store_type_id:form.find('input[name="store_type_id"]').val(),
    date:form.find('input[name="date"]').val(),
    product_id:[],
  }
  
  // считаем только для первичного склада
  if(params.store_type_id != 1) return false;

  // находим все select-ы с вторсырьем
  products = form.find('select[name="product_id[]"]');
  products.each(function(key,item){
    if($(item).val() > 0){
      params.product_id.push($(item).val());
    }
  })
  $('[name="client_id"]').parents('.form-group').addClass('loading');
  $.post('/admin/store/renderSelectClientsRests/',
    params,
    function(result){
      if(result.clients){
        html = $(result.clients).find('.col-sm-10').html();
        $('[name="client_id"]').parents('.form-group').find('.col-sm-10').html("").append(html);
        $('[name="client_id"]').chosen({
          width: "100%",
          allow_single_deselect: true
        });
        $('[name="client_id"]').parents('.form-group').removeClass('loading');
      }
    },
  'json')
}