<div class="form-group">
  <div class="col-sm-2">
    <div class="title"><?=(isset($vars['title']) ? $vars['title'] : '');?></div>
    <?=(isset($vars['req']) && $vars['req'] ? '<span class="red"> *</span>' : '');?>
    <? if (isset($vars['description']) && $vars['description']) { ?>
      <span class="description"><?=$vars['description'];?></span>
    <? } ?>
  </div>
  
  <div class="col-sm-10">
    <?=(isset($vars['value_field']) ? $vars['value'][$vars['value_field']] : $vars['value']);?>
  </div>
  
  <div class="clear"></div>
</div>