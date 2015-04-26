<div class="form-group">
  <div class="col-sm-2">
    <? if (isset($vars['title']) && $vars['title']) { ?>
      <label class="control-label">
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
      <div class="help-block"><?=$vars['description'];?></div>
    <? } ?>
  </div>
  
  <div class="col-sm-10">
    <? if (isset($vars['languages'])) { ?>
      <? foreach ($vars['languages'] as $language) { ?>
        <? if (count($vars['languages']) > 1) { ?>
          <div class="language"><img src="<?=$language['icon'];?>" /><?=$language['title'];?>:</div>
        <? } ?>
        
        <textarea
          class="form-control input-sm multilanguage default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
          rows="<?=(isset($vars['rows']) ? $vars['rows'] : 3);?>"
          <?=(isset($vars['cols']) ? 'cols="'. $vars['cols'] .'"' : '');?>
          <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
          <?=(isset($vars['placeholder']) ? 'placeholder="'. $vars['placeholder'] .'"' : '');?>
          <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'_'. $language['name'] .'"' : '');?>
          <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'_'. $language['name'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'_'. $language['name'] .'"' : ''));?>
          <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
        ><?=(isset($vars['value']) && isset($vars['value'][$vars['name'] .'_'. $language['name']]) ? $vars['value'][$vars['name'] .'_'. $language['name']] : '');?></textarea>
      <? } ?>
    <? } else { ?>
      <textarea
        class="form-control default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
        rows="<?=(isset($vars['rows']) ? $vars['rows'] : '3');?>"
        <?=(isset($vars['cols']) ? 'cols="'. $vars['cols'] .'"' : '');?>
        <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
        <?=(isset($vars['placeholder']) ? 'placeholder="'. $vars['placeholder'] .'"' : '');?>
        <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
        <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'"' : ''));?>
        <?=(isset($vars['autofocus']) && $vars['autofocus'] ? 'autofocus="autofocus"' : '');?>
        <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
      ><?=(isset($vars['value']) ? $vars['value'] : '');?></textarea>
    <? } ?>
  </div>
  
  <div class="clear"></div>
</div>