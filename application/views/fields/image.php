<div class="input_block">
  <? if (isset($vars['title']) && $vars['title']) { ?>
    <label class="name">
      <div class="title">
        <? if (isset($vars['icon'])) { ?>
          <img src="<?=$vars['icon'];?>" class="icon" />
        <? } ?>
        
        <?=$vars['title'];?>
        
        <? if (isset($vars['req']) && $vars['req']) { ?>
          <span class="red"> *</span>
        <? } ?>
      </div>
    </label>
  <? } ?>
  
  <? if (isset($vars['description']) && $vars['description']) { ?>
    <div class="description"><?=$vars['description'];?></div>
  <? } ?>
  
  <div class="input">
    <?if(!@$vars['readonly']){?>
      <input type="file"
        class="default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
        <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
        <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
        <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'"' : ''));?>
        <?=(isset($vars['autofocus']) && $vars['autofocus'] ? 'autofocus="autofocus"' : '');?>
        <?=(isset($vars['multiple']) && $vars['multiple'] ? 'multiple="multiple"' : '');?>
        <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
      />
    <?}?>

    <? if (isset($vars['value']) && $vars['value']) { ?>
      <? if ((int)$vars['value']) { ?>
        {{cmp:gallery->render_gallery_items<-<?=$vars['value'];?>}}
      <? } else { ?>
        <? if (@$vars['multiple']) { ?>
            <a href="<?=$vars['value'];?>" target="_blank">Перейти в Галерею</a><br>
        <? } else { ?>
          <div class="input_file_value">
            <a href="<?=$vars['value'];?>" target="_blank"><?=$vars['value'];?></a>
            <input type="checkbox" id="<?=(isset($vars['name']) ? $vars['name'] : '');?>_delete" name="<?=(isset($vars['name']) ? $vars['name'] : '');?>_delete" />
            <label for="<?=(isset($vars['name']) ? $vars['name'] : '');?>_delete" class="red">удалить</label>
          </div>
        <? } ?>
      <? } ?>
    <? } ?>
  </div>
  
  <div class="clear"></div>
</div>