<div class="container-fluid padding_0">
  <div class="block-title">
    <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
      <?=(@$title ? $title : $_component['title']);?>
    </h1>
  </div>
</div>
<div class="container-fluid">
  <a class="btn btn-default btn-xs" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a><br/><br/>
  <? if ($emails) {?>
    <table class="table table-hover panel table-sm">
      <thead>
        <tr>
          <th colspan="5"><h5>История отправленных писем</h5></th>
        </tr>
        <tr>
          <th>Дата</th>
          <th>Администратор</th>
          <th>Email отправителя</th>
          <th>Email получателя</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <? foreach ($emails as $value) { ?>
          <tr class="panel selection">
            <td><?=rus_date($value['tm'],'d m Yг. H:i');?></td>
            <td><?=$value['username'];?></td>
            <td><?=$value['from'];?></td>
            <td><?=htmlspecialchars($value['to']);?></td>
            <td>
              <a href="javascript:void(0)" class="" title="Ссылки"
                onClick="return my_modal('modal-lg', 'Текст письма', '<?=htmlspecialchars(str_replace(array("\r\n", "\r", "\n"), "", $value['message']));?>',[{text: 'OK', handler: function() {my_modal('hide');}, icon: 'accept'}]);">
                Текст письма
              </a>
            </td>
          </tr>
        <? } ?>
      </tbody>
    </table>
  <?}?>
  <form action="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>_client_acceptance_email/<?=$item['id'];?>/" method="POST" onsubmit="return false;">
    <div class="form-horizontal panel">
      <div class="form_block panel-body">
        <?=$html;?>
        <a href="#" class="btn btn-primary btn-xs pull-right" onclick="return submit_form(this, 'reload', null);">
          <span class="glyphicon glyphicon-save"></span>  Отправить
        </a>
      </div>
    </div>
  </form>
  <a class="btn btn-default btn-xs" href="javascript:void(0);" onclick="goBack()"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>