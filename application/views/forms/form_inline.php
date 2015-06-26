<div class="form-inline panel-group" role="tablist" aria-multiselectable="true">
  <form
    class=""
    action="<?=$vars['action'];?>"
    method="<?=$vars['method'];?>"
    target="<?=$vars['target'];?>"
    enctype="<?=$vars['enctype'];?>"
    <?=($vars['onsubmit'] ? ' onSubmit="'. $vars['onsubmit'] .'"' : '');?>
  >
    <div class="form-error"></div>
    <? foreach ($vars['blocks'] as $num=> $block) { ?>
      <div class="clearfix form_block<?=(isset($block['class']) ? ' '. $block['class'] : '');?>">
        <? if (isset($block['title']) && $block['title']) { ?>
          <h4><?=$block['title'];?></h4>  
        <? } ?>
          <?=$block['fields'];?>
      </div>
    <? } ?>
  </form>
</div>