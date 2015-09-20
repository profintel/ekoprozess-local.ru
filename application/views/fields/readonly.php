<div class="form-group <?=(isset($vars['form_group_class']) ? $vars['form_group_class'] : '');?>">
  <div class="col-sm-2">
    <? if (isset($vars['title']) && $vars['title']) { ?>
      <label class="control-label" for="<?=(isset($vars['id']) ? $vars['id'] : (isset($vars['name']) ? $vars['name'] : ''));?>" >
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
  
  <div class="col-sm-10">
    <?=(isset($vars['value_field']) ? $vars['value'][$vars['value_field']] : $vars['value']);?>
  </div>
  
  <div class="clear"></div>
</div>