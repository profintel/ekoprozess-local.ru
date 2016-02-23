$(document).ready(function(){
 
})

/**
* Отправление прихода в учет остатков
*/
function sendComingMovement(path){
  return send_confirm('Вы уверены, что хотите провести приход? После выполнения приход будет учтен в остатках, его нельзя будет отредактировать и удалить.',
    (typeof(path) != 'undefined' ? path : ''),{},
    function(){
      document.location.reload();
      sheet('hide');
    }
  );
}

/**
* Подсчет остатков сырья на складе
*/
function updateRestProduct(obj){
  var form, section, active, store_type_id, client_id, products, form_block, rest;
  form =  $(obj).parents('form');
  // смотрим client_id и пересчитываем все остатки по указанному вторсырью
  store_type_id = form.find('input[name="store_type_id"]').val();
  client_id = form.find('select[name="client_id"]').val();
  // Тип формы (приход или расход)
  section = form.find('input[name="section"]').val();
  // Наличие объекта в остатках
  active = form.find('input[name="active"]').val();
  if(store_type_id && client_id){
    // находим все select-ы с вторсырьем и запрашиваем остатки
    products = form.find('select[name="product_id[]"]');
    products.each(function(key,item){
      // если значение указано, запрашиваем остатки
      if($(item).val()){
        $.post('/admin/store/get_rest_product/',
          {store_type_id: store_type_id,client_id: client_id,product_id:$(item).val()},
          function(result){
            form_block = $(item).parents('.form_block');
            // Обнуляем остатки
            form_block.find('.rest, .rest_all').text('0.00');
            if(result){
              form_block.find('.rest').text(result.rest);
              form_block.find('.rest_all').text(result.rest_all);
              
              // если тип склада первичная продукция и приход не отправлен в остатки, добавляем брутто к остаткам
              if(active == 0 && store_type_id == 1 && section == 'coming'){
                rest = form_block.find('input[name="gross[]"]').val();
                form_block.find('.rest').text((result.rest - 0) + (rest - 0));
                form_block.find('.rest_all').text((result.rest_all - 0) + (rest - 0));
              }
              // если тип склада первичная продукция и расход не отправлен в остатки, вычитаем брутто из остатков
              if(active == 0 && store_type_id == 1 && section == 'expenditure'){
                rest = form_block.find('input[name="gross[]"]').val();
                form_block.find('.rest').text(result.rest - rest);
                form_block.find('.rest_all').text(result.rest_all - rest);
              }

              // если тип склада готовая продукция и приход не отправлен в остатки, добавляем брутто к остаткам
              if(active == 0 && store_type_id == 2 && section == 'coming'){
                rest = form_block.find('input[name="net[]"]').val();
                form_block.find('.rest').text((result.rest - 0) + (rest - 0));
                form_block.find('.rest_all').text((result.rest_all - 0) + (rest - 0));
              }
              // если тип склада готовая продукция и расход не отправлен в остатки, вычитаем брутто из остатков
              if(active == 0 && store_type_id == 2 && section == 'expenditure'){
                rest = form_block.find('input[name="net[]"]').val();
                form_block.find('.rest').text(result.rest - rest);
                form_block.find('.rest_all').text(result.rest_all - rest);
              }
            }
          },
        'json')
      }
    })
  }
}