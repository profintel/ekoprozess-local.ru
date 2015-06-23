<h1 class="icon_big languages-title">Доступные языки</h1>

<div class="links">
  <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create/" class="icon_small add_i_s">Добавить язык</a>
  <div class="clear"></div>
</div>

<? foreach ($languages as $item) { ?>
  <div class="panel selection">
    <div class="left">
      <div class="icon"><img src="<?=$item['icon'];?>" /></div>
      
      <div><?=$item['title'];?></div>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <div class="buttons">
        <? if ($item['active']) { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>disable/<?=$item['id'];?>/" class="lightbulb_i_s" title="Отключить"></a>
        <? } else { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>enable/<?=$item['id'];?>/" class="lightbulb_off_i_s" title="Включить"></a>
        <? } ?>
        
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit/<?=$item['id'];?>/" class="pencil_i_s" title="Изменить"></a>
        
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите удалить язык?',
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete/<?=$item['id'];?>/',
            {},
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
          );"
          class="cross_i_s"
          title="Удалить"
        ></a>
        
        <div class="clear"></div>
      </div>
    </div>
    
    <div class="clear"></div>
  </div>
<? } ?>

<div class="links">
  <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create/" class="icon_small add_i_s">Добавить язык</a>
  <div class="clear"></div>
</div>