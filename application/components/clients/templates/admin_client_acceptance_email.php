<div class="container-fluid padding_0">
  <div class="block-title">
    <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
      <?=(@$title ? $title : $_component['title']);?>
    </h1>
  </div>
</div>
<div class="container-fluid">
  <a class="btn btn-default btn-xs" href="/admin<?=$_component['path'];?>acceptances/"><span class="glyphicon glyphicon-backward"></span> Назад</a><br/><br/>
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
            <td><?=$value['to'];?></td>
            <td>
              <a href="javascript:void(0)" class="" title="Ссылки"
                onClick="return my_modal('information', 'Текст письма', '<?=htmlspecialchars(str_replace(array("\r\n", "\r", "\n"), "", $value['message']));?>',[{text: 'OK', handler: function() {my_modal('hide');}, icon: 'accept'}]);">
                Текст письма
              </a>
            </td>
          </tr>
        <? } ?>
      </tbody>
    </table>
  <?}?>
  <form action="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>_client_acceptance_email/<?=$item['id'];?>/" method="POST" onsubmit="return false;">
    <table class="table table-bordered panel">
      <tr>
        <th>От кого</th>
        <td>
          <div class="">
            <input type="text" class="form-control" name="from" value="<?=$this->admin['email'];?>" />
          </div>
        </td>
      </tr>
      <tr>
        <th>Кому</th>
        <td>
          <div class="">
            <input type="text" class="form-control" name="to" value="<?=$item['email'];?>" />
          </div>          
        </td>
      </tr>
      <tr>
        <th colspan="2">Текст письма</th>
      </tr>
      <tr>
        <td colspan="2">
          <?=$html;?>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <a href="#" class="btn btn-primary btn-xs pull-right" onclick="return submit_form(this, 'reload', null);">
            <span class="glyphicon glyphicon-save"></span>  Отправить
          </a>
        </td>
      </tr>
    </table>
  </form>
  <a class="btn btn-default btn-xs" href="/admin<?=$_component['path'];?>acceptances/"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>