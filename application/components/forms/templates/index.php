<h1 class="icon_big forms-types-title">Типы форм</h1>

<div class="links">
  <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_type/" class="icon_small add_i_s">Создать тип</a>
  
  <div class="clear"></div>
</div>

<? foreach ($types as $item) { ?>
  <div class="panel selection">
    <div class="left">
      <div class="title">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_type/<?=$item['id'];?>/"><?=$item['title'];?></a>
      </div>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <div class="buttons">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_type/<?=$item['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
        
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите удалить тип?<br />Все формы данного типа также будут удалены!',
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_type/<?=$item['id'];?>/',
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

<div class="hr"></div>



<h1 class="icon_big forms-title">Созданные формы</h1>

<div class="links">
  <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create/" class="icon_small add_i_s">Добавить форму</a>
  
  <div class="clear"></div>
</div>

<? foreach ($forms as $item) { ?>
  <div class="panel selection">
    <div class="left">
      <div class="title">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit/<?=$item['id'];?>/"><?=$item['title'];?></a>
        / <?=$item['name'];?>
        <span class="blue">(полей: <b><?=$item['fields_amount'];?></b>)</span>
      </div>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <div class="buttons">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>fields/<?=$item['id'];?>/" class="forms-field_i_s" title="Поля формы"></a>
        
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit/<?=$item['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
        
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите удалить форму?',
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