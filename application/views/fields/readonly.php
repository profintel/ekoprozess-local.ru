<div class="input_block">
  <div class="name">
    <div class="title"><?=(isset($vars['title']) ? $vars['title'] : '');?></div>
    <?=(isset($vars['req']) && $vars['req'] ? '<span class="red"> *</span>' : '');?>
    <? if (isset($vars['description']) && $vars['description']) { ?>
      <span class="description"><?=$vars['description'];?></span>
    <? } ?>
  </div>
  
  <div class="">
    <b><?=(isset($vars['value_field']) ? $vars['value'][$vars['value_field']] : $vars['value']);?></b>
  </div>
  
  <div class="clear"></div>
</div>