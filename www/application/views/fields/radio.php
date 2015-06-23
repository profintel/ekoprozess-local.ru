<?
$id = (isset($vars['id']) ?
  $vars['id']
: (isset($vars['name']) ?
    preg_replace('/\[\]$/', '-'+ rnd() ,$vars['name'])
  :
    'default-generated-'+ rnd()
  )
);
$value_field = (isset($vars['value_field']) ? $vars['value_field'] : 'id');
$text_field  = (isset($vars['text_field'])  ? $vars['text_field']  : 'title');
?>

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
    <? if (isset($vars['options'])) { ?>
      <? foreach ($vars['options'] as $num => $option) { ?>
        <div class="radio_item">
          <input type="radio"
            id="<?=$id;?>_<?=$num;?>"
            name="<?=(isset($vars['name']) && $vars['name'] ? $vars['name'] : $id);?>"
            class="default-generated<?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
            value="<?=$option[$value_field];?>"
            <?=(isset($vars['tabindex']) ? 'tabindex="'. (int)$vars['tabindex'] .'"' : '');?>
            <?=(isset($vars['value']) && $vars['value'] == $option[$value_field] ? 'checked' : '');?>
            <?=(isset($vars['disabled']) && $vars['disabled'] ? 'disabled="disabled"' : '');?>
          >
          
          <label for="<?=$id;?>_<?=$num;?>"><?=$option[$text_field];?></label>
        </div>
      <? } ?>
    <? } ?>
  </div>
  
  <div class="clear"></div>
</div>