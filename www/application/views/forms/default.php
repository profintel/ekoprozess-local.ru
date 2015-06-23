<div class="form-default panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <form
    class="form-horizontal panel"
    action="<?=$vars['action'];?>"
    method="<?=$vars['method'];?>"
    target="<?=$vars['target'];?>"
    enctype="<?=$vars['enctype'];?>"
    <?=($vars['onsubmit'] ? ' onSubmit="'. $vars['onsubmit'] .'"' : '');?>
  >
    <div class="form-error"></div>
    <? foreach ($vars['blocks'] as $num=> $block) { ?>
      <div class="form_block<?=(isset($block['class']) ? ' '. $block['class'] : '');?>">
        <? if (isset($block['title']) && $block['title']) { ?>
          <div class="panel-heading" role="tab" id="heading<?=$num;?>">
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
          <div class="panel-body">
            <?=$block['fields'];?>
          </div>
        </div>
        
        <div class="clearfix"></div>
      </div>
    <? } ?>
  </form>
</div>