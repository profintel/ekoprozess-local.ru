<div class="clearfix">
  <a href="#"
    <?=(isset($vars['id']) ? 'id="'. $vars['id'] .'"' : '');?>"
    class="btn btn-primary btn-sm pull-right <?=(isset($vars['class']) ? ' '. $vars['class'] : '');?>"
    <? if ($vars['type'] == 'ajax') { ?>
      onClick="return submit_form(this, <?=(isset($vars['reaction']) ? "'". $vars['reaction'] ."'" : 'null');?>, <?=(isset($vars['failure']) ? "'". $vars['failure'] ."'" : 'null');?>);"
    <? } else { ?>
      onClick="return submit_form_sync(this);"
    <? } ?>
  >
    <?=(isset($vars['title']) ? ' '. $vars['title'] : 'Отправить');?>
  </a>
</div>