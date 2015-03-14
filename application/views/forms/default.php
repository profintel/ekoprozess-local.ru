<div class="install rounded bg_white">
  <form
    action="<?=$vars['action'];?>"
    method="<?=$vars['method'];?>"
    target="<?=$vars['target'];?>"
    enctype="<?=$vars['enctype'];?>"
    <?=($vars['onsubmit'] ? ' onSubmit="'. $vars['onsubmit'] .'"' : '');?>
  >
    <? foreach ($vars['blocks'] as $block) { ?>
      <div class="form_block<?=(isset($block['class']) ? ' '. $block['class'] : '');?>">
        <? if (isset($block['title'])) { ?>
          <h1><?=$block['title'];?></h1>
        <? } ?>
        <?=$block['fields'];?>
        
        <div class="clear"></div>
      </div>
    <? } ?>
  </form>
  
  <div class="clear"></div>
</div>