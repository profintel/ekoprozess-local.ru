<script>
$(function() {
  $('#add_field').chosen({
    disable_search: true,
    auto_width:     true
  }).change(function() {
    document.location = '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_field/<?=$form['id'];?>/'+ $(this).val() +'/';
  });
});
</script>

<h1 class="icon_big fields-title">Поля формы "<?=$form['title'];?>"</h1>

<div class="links">
  <div class="link">
    <select id="add_field" class="iconed" data-placeholder="Добавить поле">
      <option></option>
      <option value="text" class="option-text">Однострочный текст</option>
      <option value="textarea" class="option-textarea">Многострочный текст</option>
      <option value="password" class="option-password">Пароль</option>
      <option value="captcha" class="option-captcha">Проверочный код</option>
      <option value="hidden" class="option-hidden">Скрытое</option>
      <option value="file" class="option-file">Файл</option>
      <option value="checkbox" class="option-checkbox">Флажок</option>
      <option value="radio" class="option-radio">Переключатель</option>
      <option value="select" class="option-select">Список</option>
      <option value="submit" class="option-submit">Кнопка отправки</option>
    </select>
  </div>
  
  <div class="clear"></div>
</div>

<? foreach ($fields as $item) { ?>
  <div class="panel selection">
    <div class="left">
      <div class="title option-<?=$item['type'];?>">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_field/<?=$item['id'];?>/"><?=$item['title'];?></a>
        / <?=$item['type'];?>
      </div>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <div class="buttons">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>up_field/<?=$item['id'];?>/" class="arrow_up_i_s" title="Поднять"></a>
        
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>down_field/<?=$item['id'];?>/" class="arrow_down_i_s" title="Спустить"></a>
        
        <? if ($item['active']) { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>disable_field/<?=$item['id'];?>/" class="lightbulb_i_s" title="Отключить"></a>
        <? } else { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>enable_field/<?=$item['id'];?>/" class="lightbulb_off_i_s" title="Включить"></a>
        <? } ?>
        
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_field/<?=$item['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
        
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите удалить поле?',
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_field/<?=$item['id'];?>/',
            {},
            'reload'
          );"
          class="cross_i_s"
          title="Удалить"
        ></a>
      </div>
    </div>
    
    <div class="clear"></div>
  
    <? if ($item['description']) { ?>
      <div class="description">
        <p><?=$item['description'];?></p>
      </div>
    <? } ?>
  </div>
  
  <br />
<? } ?>

<br /><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>