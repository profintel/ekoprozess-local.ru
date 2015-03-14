<h1 class="icon_big components-installed-icon">Просмотр установленного компонента</h1>

<div class="panel">
  <div class="left">
    <div class="icon"><img src="/admin<?=$_component['path'];?>icon/<?=$item['name'];?>/" /></div>
    
    <div class="title"><?=$item['title'];?></div>
    
    <div class="clear"></div>
  </div>
  
  <div class="right">
    <?=($item['version'] ? 'v. '. sprintf('%.2f', $item['version']) : '');?>
    <?=($item['author'] ? '| &copy; '. $item['author'] : '');?>
  </div>
  
  <div class="clear"></div>
  
  <? if ($item['description']) { ?>
    <div class="description">
      <p><?=$item['description'];?></p>
    </div>
  <? } ?>
  
  <div class="description">
    <div class="left">
      <p>Системное имя: <b><?=$item['name'];?></b></p>
      
      <p>Установлен: <b><?=rus_date($item['tm']);?> года</b></p>
      
      <? if ($item['parent']) { ?>
        <p>
          Родительский компонент:
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>installed/<?=$item['parent'];?>/" class="blank" target="_blank">
            <?=$item['parent'];?>
          </a>
        </p>
      <? } ?>
      
      <? if ($childs) { ?>
        <p>
          Зависимые компоненты:
          <? foreach ($childs as $child) { ?>
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>installed/<?=$child['id'];?>/" class="blank" target="_blank"><?=$child['name'];?></a>
          <? } ?>
        </p>
      <? } ?>
    </div>
    
    <div class="right">
      <? if ($item['main']) { ?>
        <p class="green icon_small tick_i_s"><b>Компонент по умолчанию</b></p>
      <? } else { ?>
        <p>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>set_main/<?=$item['id'];?>/" class="icon_small tick_i_s">
            Сделать компонентом по умолчанию
          </a>
        </p>
      <? } ?>
      
      <p>
        <a href="#"
          onClick="return send_request('<?=$_lang_prefix;?>/admin<?=$_component['path'];?>refresh/<?=$item['id'];?>/');"
          class="icon_small arrow_refresh_i_s"
        >
          Обновить кэш
        </a>
      </p>
      
      <p>
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите удалить компонент?',
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete/<?=$item['id'];?>/',
            {},
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
          );"
          class="icon_small cross_i_s"
        >
          Удалить компонент
        </a>
      </p>
    </div>
    
    <div class="clear"></div>
  </div>
  
</div>

<br /><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>