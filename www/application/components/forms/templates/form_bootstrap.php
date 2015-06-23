<form role="form"
    action="<?=$vars['action'];?>"
    method="<?=$vars['method'];?>"
    target="<?=$vars['target'];?>"
    enctype="<?=$vars['enctype'];?>"
    <?=($vars['onsubmit'] ? ' onSubmit="'. $vars['onsubmit'] .'"' : '');?>
>
  <? foreach ($vars['blocks'] as $block) { ?>
  <div class="form-group">
    <? if (isset($block['title'])) { ?>
      <label><?=$block['title'];?></label>
    <? } ?>
    <?=$block['fields'];?>    
  </div>
  <? } ?>
</form>