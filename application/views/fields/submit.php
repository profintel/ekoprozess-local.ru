<div class="clearfix">
<a href="#"
  <?=(isset($vars['id']) && $vars['id'] ? 'id="'. $vars['id'] .'"' : '');?>"
  class="btn btn-primary btn-xs pull-right <?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
  <? if (isset($vars['onclick']) && $vars['onclick']) { ?>
    onClick='<?=$vars['onclick'];?>'
  <? } else { ?>
    <? if ($vars['type'] == 'ajax') { ?>
      onClick="return submit_form(this, <?=(isset($vars['reaction']) && $vars['reaction'] ? "'". $vars['reaction'] ."'" : 'null');?>, <?=(isset($vars['failure']) && $vars['failure'] ? "'". $vars['failure'] ."'" : 'null');?>);"
    <? } else { ?>
      onClick="return submit_form_sync(this);"
    <? } ?>
  <? } ?>
>
  <span class="glyphicon <?=(isset($vars['icon']) && $vars['icon'] ? $vars['icon'] : 'glyphicon-save');?>"></span> <?=(isset($vars['title']) ? ' '. $vars['title'] : 'Отправить');?>
</a>
</div>