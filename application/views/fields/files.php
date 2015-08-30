<div class="form-group">
  <div class="col-sm-2">
    <? if (isset($vars['title']) && $vars['title']) { ?>
      <label for="<?=(isset($vars['id']) ? $vars['id'] : (isset($vars['name']) ? $vars['name'] : ''));?>" >
        <? if (isset($vars['icon'])) { ?>
          <img src="<?=$vars['icon'];?>" class="icon" />
        <? } ?>
        
        <?=$vars['title'];?>
        
        <? if (isset($vars['req']) && $vars['req']) { ?>
          <span class="text-danger"> *</span>
        <? } ?>
      </label>
    <? } ?>
    
    <? if (isset($vars['description']) && $vars['description']) { ?>
      <p class="help-block"><?=$vars['description'];?></p>
    <? } ?>
  </div>
  
  <div class="col-sm-12">
    <? if (@$vars['multiple'] && @$vars['value']) { ?>
        <a href="/admin/gallery/<?=$vars['value'];?>" target="_blank">Перейти в Галерею</a><br>
    <? } elseif (isset($vars['value']) && $vars['value']) { ?>
      <div class="input_file_value">
        <a href="<?=$vars['value'];?>" target="_blank"><?=$vars['value'];?></a>
        <input type="checkbox" id="<?=(isset($vars['name']) ? $vars['name'] : '');?>_delete" name="<?=(isset($vars['name']) ? $vars['name'] : '');?>_delete" />
        <label for="<?=(isset($vars['name']) ? $vars['name'] : '');?>_delete" class="red">удалить</label>
      </div>
    <? } ?>
    <input type="file"
      class="default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
      <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
      <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
      <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'"' : ''));?>
      <?=(isset($vars['autofocus']) && $vars['autofocus'] ? 'autofocus="autofocus"' : '');?>
      <?=(isset($vars['multiple']) && $vars['multiple'] ? 'multiple="multiple"' : '');?>
      <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
    />
  </div>
  
  <div class="clear"></div>
</div>