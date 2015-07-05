<div class="form-default ">
  <form
    class="form-horizontal"
    action="<?=$vars['action'];?>"
    method="<?=$vars['method'];?>"
    target="<?=$vars['target'];?>"
    enctype="<?=$vars['enctype'];?>"
    <?=($vars['onsubmit'] ? ' onSubmit="'. $vars['onsubmit'] .'"' : '');?>
  >
    <div class="form-error"></div>
    <ul class="nav nav-tabs" role="tablist">
      <? foreach ($vars['blocks'] as $num=> $block) { ?>
        <li role="presentation" class="<?=($num==0?'active':'');?>">
          <a href="#tabpanel<?=$num;?>" aria-controls="tabpanel<?=$num;?>" role="tab" data-toggle="tab">
            <?=(isset($block['title']) ? $block['title'] : 'Блок');?>
          </a>
        </li>
      <? } ?>
    </ul>
    <div class="tab-content panel clearfix">
    <? foreach ($vars['blocks'] as $num=> $block) { ?>
      <div id="tabpanel<?=$num;?>" role="tabpanel" class="tab-pane <?=($num==0?'active':'');?>">
        <div class="panel-body clearfix">
          <? if (isset($block['title_btn']) && $block['title_btn']) { ?>
            <?=$block['title_btn'];?>
          <? }  ?>
          <?=$block['fields'];?>
        </div>
      </div>
    <? } ?>
    </div>
  </form>
</div>