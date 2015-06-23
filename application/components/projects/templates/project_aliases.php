<h1 class="icon_big projects-title">Алиасы проекта "<?=$project['title'];?>"</h1>

<div class="links">
  <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_project_alias/<?=$project['id'];?>/" class="icon_small add_i_s">
    Добавить алиас
  </a>
  <div class="clear"></div>
</div>

<div class="panel selected">
  <div class="left">
    <div>Основной домен: <b><?=$project['domain'];?></b></div>
    
    <div class="clear"></div>
  </div>
  
  <div class="clear"></div>
</div>

<? foreach ($aliases as $item) { ?>
  <div class="panel selection">
    <div class="left">
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_project_alias/<?=$item['id'];?>/" class="green icon_small arrow_<?=($item['redirect'] ? 'undo' : 'right');?>_i_s">
        <?=$item['name'];?>
      </a>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <div class="buttons">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_project_alias/<?=$item['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
        
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите удалить алиас?',
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_project_alias/<?=$item['id'];?>/',
            {},
            'reload'
          );"
          class="cross_i_s"
          title="Удалить алиас"
        ></a>
        <div class="clear"></div>
      </div>
    </div>
    
    <div class="clear"></div>
  </div>
<? } ?>

<br /><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>