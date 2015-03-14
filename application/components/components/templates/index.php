<a href="javascript:void();"
  onClick="send_request('<?=$_lang_prefix;?>/admin<?=$_component['path'];?>refresh/');"
  class="components-refresher icon_small arrow_refresh_i_s"
>
  Обновить кэш
</a>

<div class="clear"></div>

<h1 class="icon_big components-installed-icon">Установленные компоненты</h1>

<div class="components-installed">
  <? foreach ($installed as $item) { ?>
    <div class="panel selection">
      <div class="left">
        <div class="icon"><img src="/admin<?=$_component['path'];?>icon/<?=$item['name'];?>/" /></div>
        
        <div><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>installed/<?=$item['id'];?>/" class="title"><?=$item['title'];?></a></div>
        
        <div class="clear"></div>
      </div>
      
      <div class="right">
        <?=($item['version'] ? 'v. '. sprintf('%.2f', $item['version']) : '');?>
        <?=($item['author'] ? '| &copy; '. $item['author'] : '');?>
        
        <div class="buttons">
          <a href="#"
            onClick="return send_request('<?=$_lang_prefix;?>/admin<?=$_component['path'];?>refresh/<?=$item['id'];?>/');"
            class="arrow_refresh_i_s"
            title="Обновить кэш"
          ></a>
          <a href="#"
            onClick="return send_confirm(
              'Вы уверены, что хотите удалить компонент?',
              '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete/<?=$item['id'];?>/',
              {},
              '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
            );"
            class="cross_i_s"
            title="Удалить компонент"
          ></a>
          <div class="clear"></div>
        </div>
      </div>
      
      <div class="clear"></div>
    </div>
  <? } ?>
</div>

<? if ($uninstalled) { ?>
  <h1 class="icon_big components-uninstalled-icon">Доступные компоненты</h1>

  <div class="components-uninstalled">
    <? foreach ($uninstalled as $item) { ?>
      <div class="panel selection">
        <div class="left">
          <div class="icon"><img src="/admin<?=$_component['path'];?>icon/<?=$item['name'];?>/" /></div>
          
          <div>
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>uninstalled/<?=$item['name'];?>/" class="title">
              <?=(isset($item['title']) && $item['title'] ? $item['title'] : $item['name']);?>
            </a>
          </div>
          
          <div class="clear"></div>
        </div>
        
        <div class="right">
          <?=(isset($item['version']) && $item['version'] ? 'v. '. sprintf('%.2f', $item['version']) : '');?>
          <?=(isset($item['author']) && $item['author'] ? '| &copy; '. $item['author'] : '');?>
          
          <div class="buttons">
            <? if (!$item['errors']) { ?>
              <a href="#"
                onClick="return send_request(
                  '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>install/<?=$item['name'];?>/',
                  {},
                  '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
                );"
                class="add_i_s"
                title="Установить компонент"
              ></a>
            <? } ?>
            <div class="clear"></div>
          </div>
        </div>
        
        <div class="clear"></div>
        
        <? if ($item['errors']) { ?>
          <div class="errors"><?=implode('<br />', $item['errors']);?></div>
        <? } ?>
      </div>
    <? } ?>
  </div>
<? } ?>