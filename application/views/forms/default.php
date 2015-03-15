<form
  class="form-horizontal"
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
      
      <div class="clearfix"></div>
    </div>
  <? } ?>
</form>