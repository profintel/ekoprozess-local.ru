$(function() {
  $('#acceptancePaymentEditModal').on('shown.bs.modal', function(){
    if($(document).find('.input-datepicker').length){
      var dateInputs = $(document).find('.input-datepicker');
      dateInputs.datepicker({
        beforeShow: function(input, inst) {
          $('#ui-datepicker-div').removeClass('custom-dateTimePicker');
          $('#ui-datepicker-div').addClass('custom-datepicker');
        }
      });
      $.each(dateInputs,function(key,item){
        if($(item).data('mindate')){
          $(item).datepicker( "option", "minDate", $(item).data('mindate'));
        }
        if($(item).data('maxdate')){
          $(item).datepicker( "option", "maxDate", $(item).data('maxdate'));
        }
      })

      if($(document).find('.input-datetimepicker').length){
        var dateInputs = $(document).find('.input-datetimepicker');
        dateInputs.datetimepicker({
          hourGrid: 4,
          minuteGrid: 10,
          beforeShow: function(input, inst) {
            $('#ui-datepicker-div').removeClass('custom-datepicker');
            $('#ui-datepicker-div').addClass('custom-dateTimePicker');
          }
        });
        $.each(dateInputs,function(key,item){
          if($(item).data('mindate')){
            $(item).datepicker( "option", "minDate", $(item).data('mindate'));
          }
          if($(item).data('maxdate')){
            $(item).datepicker( "option", "maxDate", $(item).data('maxdate'));
          }
        })
      }
    }


  });
  $('#acceptancePaymentEditModal').on('hidden.bs.modal', function(){
    $(this).removeData('bs.modal');
  });

  //объединение строк в отчете по бухгалтерии
  $('.draggable').draggable({
    cursor: "move",
    axis: 'y',
    scroll: true,
    revert: "invalid",
    handle: "span"
  });
  $('.droppable').droppable({
    addClasses: false,
    hoverClass: 'for-drop',
    tolerance: 'pointer',
    drop: function(event, ui) {
      var id = $(ui.draggable).data('id');
      var parent_id = $(this).data('parent');
      set_acceptancePaymentParent(id, parent_id);
    }
  });
})

/**
* После измененеия параметров оплаты в модальном окне
* перерендерим таблицу с отчетом
*/
function setAcceptancePaymentModal(context) {
  // console.log('setAcceptancePaymentModal');
  $('#acceptancePaymentEditModal').modal('hide');
  $('#btnFormAcceptance_payments_report').click();
}
/**
* Меняет итоговую стоимость акта оплаты после измененеия параметров
*/
function setAcceptancePaymentSum(context,answer) {
  if(answer.success.item){
    $('#acceptanceSum'+answer.success.item.id).text(answer.success.item.sum);
  }
  alert_msg('success','Изменения успешно сохранены');
}
/**
* Отображает select выбор метода подсчета кассы
* в момент когда активна галочка "Оплачено"
*/
function setElMethodPayCash(obj) {
  if($(obj).prop('checked') === true){
    $('.form_group_method_pay_cash').show();
  } else {
    $('.form_group_method_pay_cash').hide();
  }
}

//объединение строк в отчете по бухгалтерии
function set_acceptancePaymentParent(id, parent_id) {
  send_request('/admin/acceptance_payments/edit_acceptancePaymentParent/', {
    id: id,
    parent_id: parent_id
  }, 'reload');
}