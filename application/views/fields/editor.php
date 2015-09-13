<div class="form-group clearfix">
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
            toolbar: '<?=(isset($vars['toolbar']) && $vars['toolbar'] ? $vars['toolbar'] : 'Basic');?>',
            height:  '<?=(isset($vars['height']) && $vars['height'] ? $vars['height'] : 200);?>',
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
          toolbar: '<?=(isset($vars['toolbar']) && $vars['toolbar'] ? $vars['toolbar'] : 'Basic');?>',
            height:  '<?=(isset($vars['height']) && $vars['height'] ? $vars['height'] : 200);?>',
        });
        </script>
      <? } ?>
    <? } ?>
  </div>
</div>