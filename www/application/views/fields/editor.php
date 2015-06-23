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
        
        <textarea
          class="multilanguage default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
          <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
          <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'_'. $language['name'] .'"' : '');?>
          <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'_'. $language['name'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'_'. $language['name'] .'"' : ''));?>
          <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
        ><?=(isset($vars['value']) && isset($vars['value'][$vars['name'] .'_'. $language['name']]) ? $vars['value'][$vars['name'] .'_'. $language['name']] : '');?></textarea>
        
        <? if ((isset($vars['id']) && $vars['id']) || (isset($vars['name']) && $vars['name'])) { ?>
          <script>
          CKEDITOR.replace('<?=(isset($vars['id']) && $vars['id'] ? $vars['id'] : $vars['name']) .'_'. $language['name'];?>', {
            toolbar: '<?=(isset($vars['toolbar']) && $vars['toolbar'] ? $vars['toolbar'] : 'Basic');?>'
          });
          </script>
        <? } ?>
      <? } ?>
    <? } else { ?>
      <textarea
        class="default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
        <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
        <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
        <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : (isset($vars['name']) ? 'id="'. $vars['name'] .'"' : ''));?>
        <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
      ><?=(isset($vars['value']) ? $vars['value'] : '');?></textarea>
      
      <? if ((isset($vars['id']) && $vars['id']) || (isset($vars['name']) && $vars['name'])) { ?>
        <script>
        CKEDITOR.replace('<?=(isset($vars['id']) && $vars['id'] ? $vars['id'] : $vars['name']);?>', {
          toolbar: '<?=(isset($vars['toolbar']) && $vars['toolbar'] ? $vars['toolbar'] : 'Basic');?>'
        });
        </script>
      <? } ?>
    <? } ?>
  </div>
  
  <div class="clear"></div>
</div>