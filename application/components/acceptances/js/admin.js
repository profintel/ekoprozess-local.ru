/*
* Устанавливает в форму отчета по актам приемки, исключения строк из таблицы отчета
*/
function setAcceptanceExceptions(acceptance_id, action) {
  if(action == 'delete'){
    // если удалить значение, удаляем весь элемент checkbox
    $('[name="exceptions[]"][value="'+parseInt(acceptance_id)+'"]').parents('.checkbox_item').remove();
  } else {
    // если добавить значение, добавляем в форму checkbox checked с указанным значением
    
    // формируем label
    var label = $('#acceptanceDate'+acceptance_id).text() + ',' + $('#acceptanceClientTitle'+acceptance_id).text() + ',' + $('#acceptanceSum'+acceptance_id).text() + ' руб.';
    var newExept = '<div class="checkbox_item"><input type="checkbox" id="exceptions'+parseInt(acceptance_id)+'" name="exceptions[]" class="default-generated" value="'+parseInt(acceptance_id)+'" onchange="setAcceptanceExceptions('+parseInt(acceptance_id)+', \'delete\');" checked="checked">&nbsp;<label for="exceptions'+parseInt(acceptance_id)+'">'+label+'</label></div>';

    //acceptanceExceptions - блок в форме с checkbox-ами
    $('#acceptanceExceptions').append(newExept);
  }

  // после изменений в форме, отправляем ее для обновляения отчета
  submit_form($('#btn-form'), handle_ajaxResultAllData);
}