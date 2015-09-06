<?
$id = (isset($vars['id']) ?
  $vars['id']
: (isset($vars['name']) ?
    preg_replace('/\[\]$/', '-'+ rnd() ,$vars['name'])
  :
    'default-generated-'+ rnd()
  )
);
?>

<? if (!isset($vars['chosen_disable']) || !$vars['chosen_disable']) { ?>
  <script>
  $(document).ready(function() {
    $('#<?=$id;?>').chosen({
      disable_search: <?=((isset($vars['disable_search']) && $vars['disable_search']) || count($vars['options']) < 9 ? 'true' : 'false');?>,
      auto_width: <?=(isset($vars['auto_width']) && $vars['auto_width'] ? 'true' : 'false');?>,
      allow_single_deselect: <?=(isset($vars['empty']) && $vars['empty'] ? 'true' : 'false');?>
    });
  });
  </script>
<? } ?>

<div class="form-group">
  <div class="col-sm-2">
    <? if (isset($vars['title']) && $vars['title']) { ?>
      <label class="control-label" >
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
    <select
      id="<?=$id;?>"
      data-placeholder="<?=(isset($vars['placeholder']) ? $vars['placeholder'] : 'Выберите элемент...');?>"
      class="form-control input-sm <?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
      <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
      <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
      <?=(isset($vars['onchange']) ? 'onChange="'. $vars['onchange'] .'"' : '');?>
      <?=(isset($vars['multiple']) && $vars['multiple'] ? 'multiple="multiple" size="'. (count($vars['options']) < 10 ? count($vars['options']) : 10) .'"' : '');?>
      <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
    >
      <? if (isset($vars['empty']) && $vars['empty']) { ?>
        <option value="0"></option>
      <? } ?>

      <? if (isset($vars['optgroup']) && $vars['optgroup']) { ?>
        <? foreach ($vars['options'] as $optgroup) { ?>
          <optgroup label="<?=$optgroup[(isset($vars['text_field']) ? $vars['text_field'] : 'title')];?>">
            <? foreach ($optgroup['childs'] as $option) { ?>
              <option
                value="<?=$option[(isset($vars['value_field']) ? $vars['value_field'] : 'id')];?>"
                <?
                if (isset($vars['value'])) {
                  if (is_array($vars['value'])) {
                    if (in_array($option[(isset($vars['value_field']) ? $vars['value_field'] : 'id')], $vars['value'])) {
                      echo ' selected';
                    }
                  } else {
                    if ($vars['value'] == $option[(isset($vars['value_field']) ? $vars['value_field'] : 'id')]) {
                      echo ' selected';
                    }
                  }
                }
                ?>
              ><?=$optgroup[(isset($vars['text_field']) ? $vars['text_field'] : 'title')];?>, <?=$option[(isset($vars['text_field']) ? $vars['text_field'] : 'title')];?></option>
            <? } ?>
          </optgroup>
        <? } ?>
      <? } else { ?>
        <? foreach ($vars['options'] as $option) { ?>
          <option
            value="<?=$option[(isset($vars['value_field']) ? $vars['value_field'] : 'id')];?>"
            <?
            if (isset($vars['value'])) {
              if (is_array($vars['value'])) {
                if (in_array($option[(isset($vars['value_field']) ? $vars['value_field'] : 'id')], $vars['value'])) {
                  echo ' selected';
                }
              } else {
                if ($vars['value'] == $option[(isset($vars['value_field']) ? $vars['value_field'] : 'id')]) {
                  echo ' selected';
                }
              }
            }
            ?>
          ><?=$option[(isset($vars['text_field']) ? $vars['text_field'] : 'title')];?></option>
        <? } ?>
      <? } ?>
    </select>
  </div>
  
  <div class="clear"></div>
</div>