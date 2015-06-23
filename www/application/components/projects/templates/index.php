<h1 class="icon_big projects-title">Структура проектов</h1>

<div id="projects-pages-structure">
  <div class="links">
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_project/" class="icon_small add_i_s">Создать проект</a>
    <div class="clear"></div>
  </div>
  
  <? foreach ($projects as $item) { ?>
    <div id="project-<?=$item['id'];?>" class="panel-2 selected page-droppable">
      <div class="icon"><img src="/components/projects/icon.png" /></div>
      
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_project/<?=$item['id'];?>/" class="title">
        <?=$item['title'];?>
      </a> <span class="dark"><?=$item['domain'];?></span>
      
      <div class="buttons">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>project_aliases/<?=$item['id'];?>/" class="world_link_icon" title="Алиасы"></a>
        
        <? if ($item['active']) { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>disable_project/<?=$item['id'];?>/" class="lightbulb_i_s" title="Отключить"></a>
        <? } else { ?>
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>enable_project/<?=$item['id'];?>/" class="lightbulb_off_i_s" title="Включить"></a>
        <? } ?>
        
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>clone_project/<?=$item['id'];?>/" class="copy_i_s" title="Клонировать"></a>
        
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_page/<?=$item['id'];?>/" class="add_i_s" title="Создать страницу"></a>
        
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_project/<?=$item['id'];?>/" class="pencil_i_s" title="Изменить"></a>
        
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите удалить проект?',
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_project/<?=$item['id'];?>/',
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
    
    <div class="projects-page-container">
      <?=$item['pages'];?>
    </div>
    
    <div class="links">
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_project/" class="icon_small add_i_s">Создать проект</a>
      <div class="clear"></div>
    </div>
  <? } ?>
</div>