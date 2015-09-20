<div class="clearfix">
<? if ($items) { ?>
  <ul class="list-group">
    <li class="clearfix list-group-item-head">
      <div class="col-xs-3">Дата</div>
      <div class="col-xs-3">Брутто, кг</div>
      <div class="col-xs-3">Нетто, кг</div>
      <div class="col-xs-3">ИТОГО</div>
    </li>
    <? foreach ($items as $item) { ?>
      <li class="clearfix list-group-item">
        <a class="dropdown-toggle" data-toggle="dropdown">
          <div class="col-xs-3"><?=date('d.m.Y',strtotime($item['date']));?></div>
          <div class="col-xs-3"><?=number_format($item['gross'],2,'.',' ');?></div>
          <div class="col-xs-3"><?=number_format($item['net'],2,'.',' ');?></div>
          <div class="col-xs-3"><?=number_format($item['sum'],2,'.',' ');?></div>
        </a>
        <ul class="dropdown-menu">
          <li>
            <a href="/admin/clients/acceptance/<?=$item['id'];?>/" title="Просмотреть">
              <span class="glyphicon glyphicon-share"></span> Просмотреть
            </a>
            </li>
          <li>
            <a href="/admin/clients/edit_acceptance/<?=$item['id'];?>/" title="Редактировать">
              <span class="glyphicon glyphicon-edit"></span> Редактировать
            </a>
          </li>
          <li>
            <a href="/admin/clients/client_acceptance_email/<?=$item['id'];?>/" target="_client_acceptance_email_<?=$item['id'];?>">
              <span class="glyphicon glyphicon-envelope"></span> Отправить по email
            </a>
          </li>
          <li class="divider"></li>
          <li>
            <a href="#"
              onClick="return send_confirm(
                'Вы уверены, что хотите удалить акт - <?=date('d.m.Y',strtotime($item['date']));?>&emsp;<?=$item['client_title'];?>?',
                '/admin/clients/delete_acceptance/<?=$item['id'];?>/',
                {},
                'reload'
              );"                    
              title="Удалить"
            ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
          </li>
        </ul>
      </li>
    <? } ?>
  </ul>
<? } ?>
<? if(isset($client_id)&&$client_id) {?>
  <a class="btn btn-xs btn-primary pull-right" href="/admin/clients/acceptances/?client_id=<?=$client_id;?>">Перейти к актам клиента</a>
<? } ?>
</div>