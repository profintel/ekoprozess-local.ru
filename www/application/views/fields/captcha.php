<div class="input_block">
  <div class="name">
    <? if (isset($vars['title']) && $vars['title']) { ?>
      <div class="title">
        <? if (isset($vars['icon'])) { ?>
          <img src="<?=$vars['icon'];?>" class="icon" />
        <? } ?>
        
        <?=$vars['title'];?>
        
        <? if (isset($vars['req']) && $vars['req']) { ?>
          <span class="red"> *</span>
        <? } ?>
      </div>
    <? } ?>
    
    <? if (isset($vars['description']) && $vars['description']) { ?>
      <div class="description"><?=$vars['description'];?></div>
    <? } ?>
  </div>
  
  <div class="input float_l">
    <img border="0" class="image_captcha" src="/component/forms/captcha/<?=(isset($vars['bgcolor']) ? $vars['bgcolor'] : 0);?>/<?=(isset($vars['textcolor']) ? $vars['textcolor'] : 0);?>/<?=(isset($vars['symbols']) ? $vars['symbols'] : 0);?>/<?=(isset($vars['width']) ? $vars['width'] : 0);?>/<?=(isset($vars['height']) ? $vars['height'] : 0);?>/" />
    <input type="text"
      class="input_captcha default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
      maxlength="<?=(isset($vars['symbols']) ? $vars['symbols'] : 6);?>"
      <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
      <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
      <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'"' : ''));?>
      <?=(isset($vars['value']) ? "value='". $vars['value'] ."'" : '');?>
      <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
    />
  </div>
  
  <div class="clear"></div>
</div>