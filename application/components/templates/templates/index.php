<h1 class="icon_big templates-title">Установленные шаблоны</h1>

<div class="links">
  <a href="#"
    onClick="return send_request('<?=$_lang_prefix;?>/admin<?=$_component['path'];?>install/', {}, 'reload');"
    class="icon_small arrow_refresh_i_s"
  >Актуализировать список</a>
  
  <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create/" class="icon_small add_i_s">Создать шаблон</a>
  
  <div class="clear"></div>
</div>

<? foreach ($templates as $item) { ?>
  <div class="panel selection">
    <div class="left">
      <div class="title"><b><?=$item['title'];?></b> / <?=$item['name'];?></div>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <? if ($item['component']) { ?>
        <span class="dark">Компонент:</span>
        <a href="<?=$_lang_prefix;?>/admin/components/installed/<?=$item['component_id'];?>/" target="_blank"><?=$item['component'];?></a>
      <? } ?>
      
      <div class="buttons">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit/<?=$item['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
        
        <? if ($item['custom']) { ?>
          <a href="#"
            onClick="return send_confirm(
              'Вы уверены, что хотите удалить шаблон?',
              '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete/<?=$item['id'];?>/',
              {},
              'reload'
            );"
            class="cross_i_s"
            title="Удалить"
          ></a>
        <? } ?>
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