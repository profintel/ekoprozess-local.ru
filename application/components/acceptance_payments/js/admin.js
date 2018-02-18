$(function() {
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