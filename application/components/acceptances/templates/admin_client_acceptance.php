<div class="well-sm clearfix">
  <a href="/admin/acceptances/edit_acceptance/<?=$item['id'];?>/" title="Редактировать" class="btn btn-primary btn-xs hidden-print pull-left">
    <span class="glyphicon glyphicon-edit"></span> Редактировать
  </a>
  <span class="pull-left">&emsp;</span>
  <a href="/admin/acceptances/client_acceptance_email/<?=$item['id'];?>/" title="Редактировать" class="btn btn-primary btn-xs hidden-print pull-left">
    <span class="glyphicon glyphicon-envelope"></span> Отправить по email
  </a>
  <a href="javascript:void(0)" onclick="window.print();" class="btn btn-primary btn-xs hidden-print pull-right">
    <span class="glyphicon glyphicon-print"></span> 
    Печать
  </a>
</div>
<div style="background-color:#ffffff; padding:20px;">
  {{cmp:acceptances->_render_client_acceptance_table<-<?=base64_encode(serialize(array('item'=>$item)));?>}}
</div>
<br/>