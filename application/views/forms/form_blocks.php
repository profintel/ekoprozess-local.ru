<div class="clearfix">
  <form
    class="form-horizontal "
    action="<?=$vars['action'];?>"
    method="<?=$vars['method'];?>"
    target="<?=$vars['target'];?>"
    enctype="<?=$vars['enctype'];?>"
    <?=($vars['onsubmit'] ? ' onSubmit="'. $vars['onsubmit'] .'"' : '');?>
  >
    <div class="form-error"></div>
    <? foreach ($vars['blocks'] as $num=> $block) { ?>
      <div class="col-sm-6 <?=(isset($block['class']) ? ' '. $block['class'] : '');?>">
        <div class="panel">
          <? if (isset($block['title']) && $block['title']) { ?>
            <div class="panel-heading">
              <? if (isset($block['title_btn']) && $block['title_btn']) { ?>
                <h4 class="pull-left">
                  <a role="button" 
                    data-toggle="collapse" 
                    data-parent="#accordion" 
                    href="#collapse<?=$num;?>" 
                    aria-expanded="<?=(@$block['aria-expanded'] === FALSE ? 'false' : 'true');?>" 
                    aria-controls="collapse<?=$num;?>">
                    <?=$block['title'];?>
                  </a>
                </h4>
                <?=$block['title_btn'];?>
              <? } else { ?>
                <h4>
                  <a role="button" 
                    data-toggle="collapse" 
                    data-parent="#accordion" 
                    href="#collapse<?=$num;?>" 
                    aria-expanded="<?=(@$block['aria-expanded'] === FALSE ? 'false' : 'true');?>" 
                    aria-controls="collapse<?=$num;?>">
                    <?=$block['title'];?>
                  </a>
                </h4>   
              <? } ?>
            </div>
          <? } ?>
          <div id="collapse<?=$num;?>" class="panel-collapse collapse <?=(@$block['aria-expanded'] === FALSE ? '' : 'in');?>" role="tabpanel" aria-labelledby="heading<?=$num;?>">
            <div class="panel-body clearfix">
              <?=$block['fields'];?>
            </div>
          </div>
        </div>
      </div>
    <? } ?>
  </form>
</div>