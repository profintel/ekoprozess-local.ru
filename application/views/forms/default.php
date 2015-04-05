<div class="form-default">
  <form
    class="form-horizontal panel"
    action="<?=$vars['action'];?>"
    method="<?=$vars['method'];?>"
    target="<?=$vars['target'];?>"
    enctype="<?=$vars['enctype'];?>"
    <?=($vars['onsubmit'] ? ' onSubmit="'. $vars['onsubmit'] .'"' : '');?>
  >
    <div class="form-error"></div>
    <? foreach ($vars['blocks'] as $block) { ?>
      <div class="form_block<?=(isset($block['class']) ? ' '. $block['class'] : '');?>">
        <? if (isset($block['title']) && $block['title']) { ?>
          <div class="panel-heading"><h4><?=$block['title'];?></h4></div>
        <? } ?>
        <div class="panel-body"><?=$block['fields'];?></div>
        
        <div class="clearfix"></div>
      </div>
    <? } ?>
  </form>
</div>