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
  </div>
  <div class="col-sm-10">
    <? if (isset($vars['languages'])) { ?>
      <? foreach ($vars['languages'] as $language) { ?>
        <? if (count($vars['languages']) > 1) { ?>
          <div class="language"><img src="<?=$language['icon'];?>" /><?=$language['title'];?>:</div>
        <? } ?>
        
        <input type="<?=(isset($vars['type']) && $vars['type'] ? $vars['type'] : 'text');?>"
          class="form-control input-sm multilanguage default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
          <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
          <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'_'. $language['name'] .'"' : '');?>
          <?=(isset($vars['placeholder']) ? 'placeholder="'. $vars['placeholder'] .'"' : '');?>
          <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'_'. $language['name'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'_'. $language['name'] .'"' : ''));?>
          <?=(isset($vars['maxlength']) ? 'maxlength="'. $vars['maxlength'] .'"' : '');?>
          <?=(isset($vars['value']) && isset($vars['value'][$vars['name'] .'_'. $language['name']]) ? "value='". $vars['value'][$vars['name'] .'_'. $language['name']] ."'" : '');?>
          <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
          <?=(isset($vars['onkeyup']) && $vars['onkeyup'] ? 'onkeyup="'.$vars['onkeyup'].'"' : '');?>
        />
      <? } ?>
    <? } else { ?>
      <input type="<?=(isset($vars['type']) && $vars['type'] ? $vars['type'] : 'text');?>"
        class="form-control input-sm default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
        <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
        <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
        <?=(isset($vars['placeholder']) ? 'placeholder="'. $vars['placeholder'] .'"' : '');?>
        <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'"' : ''));?>
        <?=(isset($vars['maxlength']) ? 'maxlength="'. $vars['maxlength'] .'"' : '');?>
        <?=(isset($vars['value']) ? "value='". $vars['value'] ."'" : '');?>
        <?=(isset($vars['autofocus']) && $vars['autofocus'] ? 'autofocus="autofocus"' : '');?>
        <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
        <?=(isset($vars['onkeyup']) && $vars['onkeyup'] ? 'onkeyup="'.$vars['onkeyup'].'"' : '');?>
      />
    <? } ?>
    
    <? if (isset($vars['description']) && $vars['description']) { ?>
      <p class="help-block"><?=$vars['description'];?></p>
    <? } ?>
  </div>
</div>