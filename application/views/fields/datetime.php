<div class="form-group">
  <div class="col-sm-2">
    <? if (isset($vars['title']) && $vars['title']) { ?>
      <label class="control-label">
        <? if (isset($vars['icon'])) { ?>
          <img src="<?=$vars['icon'];?>" class="icon" />
        <? } ?>
        
        <?=$vars['title'];?>
        
        <? if (isset($vars['req']) && $vars['req']) { ?>
          <span class="red"> *</span>
        <? } ?>
      </label>
    <? } ?>
    
    <? if (isset($vars['description']) && $vars['description']) { ?>
      <div class="help-block"><?=$vars['description'];?></div>
    <? } ?>
  </div>
  
  <div class="col-sm-10">
    <input type="text"
      class="form-control input-sm input-datetimepicker multilanguage default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
      <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
      <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
      <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'"' : ''));?>
      <?=(isset($vars['value']) ? "value='". stripslashes($vars['value']) ."'" : '');?>
      <?=(isset($vars['autofocus']) && $vars['autofocus'] ? 'autofocus="autofocus"' : '');?>
      <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
      <?=(isset($vars['onkeyup']) && $vars['onkeyup'] ? 'onkeyup="'.$vars['onkeyup'].'"' : '');?>
      <?=(isset($vars['onchange']) && $vars['onchange'] ? 'onchange="'.$vars['onchange'].'"' : '');?>
      <?=(isset($vars['minDate']) && $vars['minDate'] ? 'data-mindate="'.$vars['minDate'].'"' : '');?>
      <?=(isset($vars['maxDate']) && $vars['maxDate'] ? 'data-maxdate="'.$vars['maxDate'].'"' : '');?>
    />
  </div>
</div>