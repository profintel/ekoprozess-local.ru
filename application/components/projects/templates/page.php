<div id="project-page-<?=$page['id'];?>" class="panel-2 selection page-draggable page-droppable">
  <? if (!$page['state']) { ?>
    <a href="#" class="toggle <?=($page['childs'] ? 'toggle_plus_i_s' : 'toggle_none_i_s');?>" data-page_id="<?=$page['id'];?>"></a>
  <? } else { ?>
    <a href="#" class="toggle <?=($page['childs'] ? 'toggle_minus_i_s' : 'toggle_none_i_s');?>" data-page_id="<?=$page['id'];?>"></a>
  <? } ?>
  
  <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_page/<?=$page['id'];?>/" title="<?=$page['path'];?>" class="title">
    <?=$page['title'];?>
  </a>
  
  <div class="buttons">
    <a href="#" class="move_i_s" onClick="return false;" title="Переместить"></a>
    
    <a href="<?=$page['path'];?>" target="_blank" class="magnifier_i_s" title="Посмотреть"></a>
    
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>page_history/<?=$page['id'];?>/" class="compress_i_s" title="Резервирование и восстановление"></a>
    
    <? if ($page['active']) { ?>
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>disable_page/<?=$page['id'];?>/" class="lightbulb_i_s" title="Отключить"></a>
    <? } else { ?>
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>enable_page/<?=$page['id'];?>/" class="lightbulb_off_i_s" title="Включить"></a>
    <? } ?>
    
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>clone_page/<?=$page['id'];?>/" class="copy_i_s" title="Клонировать"></a>
    
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_page/<?=$page['project_id'];?>/<?=$page['id'];?>/" class="add_i_s" title="Создать страницу"></a>
    
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_page/<?=$page['id'];?>/" class="pencil_i_s" title="Изменить"></a>
    
    <a href="#"
      onClick="return send_confirm(
        'Вы уверены, что хотите удалить страницу?',
        '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_page/<?=$page['id'];?>/',
        {},
        '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
      );"
      class="cross_i_s"
      title="Удалить"
    ></a>
    
    <div class="clear"></div>
  </div>
  
  <div class="clear"></div>
</div>

<? if ($page['pages']) { ?>
  <div id="project-page-<?=$page['id'];?>-childs" class="projects-page-container">
    <?=$pages;?>
  </div>
<? } ?>