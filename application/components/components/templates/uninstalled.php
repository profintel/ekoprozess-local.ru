<h1 class="icon_big components-uninstalled-icon">Просмотр доступного компонента</h1>

<div class="panel">
  <div class="left">
    <div class="icon"><img src="/admin<?=$_component['path'];?>icon/<?=$item['name'];?>/" /></div>
    
    <? if (isset($item['title']) && $item['title']) { ?>
      <div class="title"><?=$item['title'];?></div>
    <? } ?>
    
    <div class="clear"></div>
  </div>
  
  <div class="right">
    <?=(isset($item['version']) && $item['version'] ? 'v. '. sprintf('%.2f', $item['version']) : '');?>
    <?=(isset($item['author']) && $item['author'] ? '| &copy; '. $item['author'] : '');?>
  </div>
  
  <div class="clear"></div>
  
  <? if (isset($item['description']) && $item['description']) { ?>
    <div class="description">
      <p><?=$item['description'];?></p>
    </div>
  <? } ?>
  
  <div class="description">
    <div class="left">
      <p>Системное имя: <b><?=$item['name'];?></b></p>
      
      <? if (isset($item['parent']) && $item['parent']) { ?>
        <p>
          Родительский компонент: <b><?=$item['parent'];?></b>
        </p>
      <? } ?>
      
      <? if (isset($item['requirement']) && $item['requirement']) { ?>
        <p>
          Необходимые компоненты: <b><?=implode(', ', $item['requirement']);?></b>
        </p>
      <? } ?>
    </div>
    
    <div class="right">
      <? if (!$item['errors']) { ?>
        <p>
          <a href="#"
            onClick="return send_request(
              '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>install/<?=$item['name'];?>/',
              {},
              '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
            );"
            class="icon_small add_i_s"
            title="Установить компонент"
          >
            Установить компонент
          </a>
        </p>
      <? } else { ?>
        <p class="red"><b>Установка невозможна</b></p>
      <? } ?>
    </div>
    
    <div class="clear"></div>
    
    <? if ($item['errors']) { ?>
      <div class="errors"><?=implode('<br />', $item['errors']);?></div>
    <? } ?>
  </div>
  
</div>

<br /><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>