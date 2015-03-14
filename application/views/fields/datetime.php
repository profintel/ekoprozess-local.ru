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
    <input type="text"
      class="input_datetime default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
      <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
      <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
      <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'"' : ''));?>
      <?=(isset($vars['value']) ? "value='". stripslashes($vars['value']) ."'" : '');?>
      <?=(isset($vars['autofocus']) && $vars['autofocus'] ? 'autofocus="autofocus"' : '');?>
      <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
    />
    
    <? if ((isset($vars['id']) && $vars['id']) || (isset($vars['name']) && $vars['name'])) { ?>
      <script>
      $(document).ready(function() {
        $('#<?=(isset($vars['id']) ? $vars['id'] : $vars['name']);?>').datetimepicker({
          hourGrid: 4,
          minuteGrid: 5
        });
      });
      </script>
    <? } ?>
  </div>
  
  <div class="clear"></div>
</div>