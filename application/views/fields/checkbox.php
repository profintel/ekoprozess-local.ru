<?
$id = (isset($vars['id']) ?
  $vars['id']
: (isset($vars['name']) ?
    preg_replace('/\[\]$/', '-'+ rnd() ,$vars['name'])
  :
    'default-generated-'+ rnd()
  )
);
$name        = (isset($vars['name']) ? preg_replace('/\[\]$/', '' ,$vars['name']) : $id) . (isset($vars['options']) ? '[]' : '');
$value_field = (isset($vars['value_field']) ? $vars['value_field'] : 'id');
$text_field  = (isset($vars['text_field'])  ? $vars['text_field']  : 'title');
?>

<div class="input_block">
  <div class="name">
    <? if (!isset($vars['options'])) { ?>
      <label for="<?=$id;?>">
    <? } ?>
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
    <? if (!isset($vars['options'])) { ?>
      </label>
    <? } ?>
  </div>
  
  <div class="input">
    <? if (isset($vars['options'])) { ?>
      <? foreach ($vars['options'] as $num => $option) { ?>
        <div class="checkbox_item">
          <input type="checkbox"
            id="<?=$id;?>_<?=$num;?>"
            name="<?=$name;?>"
            class="default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
            value="<?=$option[$value_field];?>"
            <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
            <?
            if (isset($vars['value'])) {
              if (is_array($vars['value'])) {
                if (in_array($option[$value_field], $vars['value'])) {
                  echo ' checked';
                }
              } else {
                if ($vars['value'] == $option[$value_field]) {
                  echo ' checked';
                }
              }
            }
            ?>
            <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
          >
          
          <label for="<?=$id;?>_<?=$num;?>"><?=$option[$text_field];?></label>
        </div>
      <? } ?>
    <? } else { ?>
      <input type="checkbox"
        id="<?=$id;?>"
        class="default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
        <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
        <?=(isset($vars['name']) ? 'name="'. $vars['name'] .'"' : '');?>
        <?=(isset($vars['value']) ? 'value="'. $vars['value'] .'"' : '');?>
        <?=(isset($vars['checked']) && $vars['checked'] ? 'checked' : '');?>
        <?=(isset($vars['readonly']) && $vars['readonly'] ? 'onClick="return false;"' : '');?>
        <?=(isset($vars['autofocus']) && $vars['autofocus'] ? 'autofocus="autofocus"' : '');?>
        <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
      />
    <? } ?>
  </div>
  
  <div class="clear"></div>
</div>