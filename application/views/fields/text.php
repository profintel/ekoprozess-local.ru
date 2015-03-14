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
  
  <div class="input">
    <? if (isset($vars['languages'])) { ?>
      <? foreach ($vars['languages'] as $language) { ?>
        <? if (count($vars['languages']) > 1) { ?>
          <div class="language"><img src="<?=$language['icon'];?>" /><?=$language['title'];?>:</div>
        <? } ?>
        
        <input type="text"
          class="form-control multilanguage default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
          <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
          <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'_'. $language['name'] .'"' : '');?>
          <?=(isset($vars['placeholder']) ? 'placeholder="'. $vars['placeholder'] .'"' : '');?>
          <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'_'. $language['name'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'_'. $language['name'] .'"' : ''));?>
          <?=(isset($vars['maxlength']) ? 'maxlength="'. $vars['maxlength'] .'"' : '');?>
          <?=(isset($vars['value']) && isset($vars['value'][$vars['name'] .'_'. $language['name']]) ? "value='". $vars['value'][$vars['name'] .'_'. $language['name']] ."'" : '');?>
          <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
        />
      <? } ?>
    <? } else { ?>
      <input type="text"
        class="form-control default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
        <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
        <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
        <?=(isset($vars['placeholder']) ? 'placeholder="'. $vars['placeholder'] .'"' : '');?>
        <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'"' : ''));?>
        <?=(isset($vars['maxlength']) ? 'maxlength="'. $vars['maxlength'] .'"' : '');?>
        <?=(isset($vars['value']) ? "value='". $vars['value'] ."'" : '');?>
        <?=(isset($vars['autofocus']) && $vars['autofocus'] ? 'autofocus="autofocus"' : '');?>
        <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
      />
    <? } ?>
  </div>
  
  <div class="clear"></div>
</div>