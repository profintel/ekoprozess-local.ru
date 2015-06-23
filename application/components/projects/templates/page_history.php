<h1 class="icon_big projects-title">История изменений страницы "<?=$page['title'];?>"</h1>

<div class="links">
  <a href="#"
    onClick="return send_request('<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_backup/<?=$page['id'];?>/', {}, 'reload');"
    class="icon_small compress_i_s"
  >
    Создать резервную копию
  </a>
  <div class="clear"></div>
</div>

<div class="panel selected">
  <div class="left">
    <div>Текущая версия: от <b><?=rus_date($page['tm'], 'd m Y H:i');?></b></div>
    
    <div class="clear"></div>
  </div>
  
  <div class="clear"></div>
</div>

<? foreach ($backups as $item) { ?>
  <div class="panel selection">
    <div class="left">
      <div>Версия от <b class="green"><?=rus_date($item['tm'], 'd m Y H:i');?></b></div>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <?=$item['username'];?>
      
      <div class="buttons">
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите вернуться к указанной версии?',
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>restore_backup/<?=$item['id'];?>/',
            {},
            'reload'
          );"
          class="accept_i_s"
          title="Восстановить резервную копию"
        ></a>
        
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите удалить резервную копию?',
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_backup/<?=$item['id'];?>/',
            {},
            'reload'
          );"
          class="cross_i_s"
          title="Удалить резервную копию"
        ></a>
        <div class="clear"></div>
      </div>
    </div>
    
    <div class="clear"></div>
  </div>
<? } ?>

<br /><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>