<h1 class="icon_big menus-title">Управление меню</h1>

<div class="links">
  <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create/" class="icon_small add_i_s">Создать меню</a>
  
  <div class="clear"></div>
</div>

<? foreach ($menus as $item) { ?>
  <div class="panel selection">
    <div class="left">
      <div class="title"><span class="green"><?=$item['project'];?>:</span> <b><?=$item['title'];?></b> / <?=$item['name'];?></div>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <div class="buttons">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit/<?=$item['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
        
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите удалить меню?',
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete/<?=$item['id'];?>/',
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