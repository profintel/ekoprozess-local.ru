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