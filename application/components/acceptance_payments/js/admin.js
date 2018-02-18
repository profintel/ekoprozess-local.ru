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
})

/**
* После измененеия параметров оплаты в модальном окне
* перерендерим таблицу с отчетом
*/
function setAcceptancePaymentModal(context,answer) {
  console.log('setAcceptancePaymentModal');
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