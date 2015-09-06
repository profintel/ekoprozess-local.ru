<div class="clearfix">
<a href="javascript:void(0)"
  <?=(isset($vars['id']) && $vars['id'] ? 'id="'. $vars['id'] .'"' : '');?>"
  class="btn btn-xs pull-right <?=(isset($vars['class']) ? $vars['class'] : 'btn-primary');?>"
  <? if (isset($vars['onclick']) && $vars['onclick']) { ?>
    onClick='<?=$vars['onclick'];?>'
  <? } else { ?>
    <? if ($vars['type'] == 'ajax') { ?>
      onClick="return submit_form(this, <?=(isset($vars['reaction']) && $vars['reaction'] ? (isset($vars['reaction_func']) && $vars['reaction_func'] ? $vars['reaction'] : "'". $vars['reaction'] ."'") : 'null');?>, <?=(isset($vars['failure']) && $vars['failure'] ? "'". $vars['failure'] ."'" : 'null');?>, <?=(isset($vars['data_type']) && $vars['data_type'] ? "'". $vars['data_type'] ."'" : "'json'");?>);"
    <? } else { ?>
      onClick="return submit_form_sync(this);"
    <? } ?>
  <? } ?>
>
  <span class="glyphicon <?=(isset($vars['icon']) && $vars['icon'] ? $vars['icon'] : 'glyphicon-save');?>"></span> <?=(isset($vars['title']) ? ' '. $vars['title'] : 'Отправить');?>
</a>
</div>