<? if ($items) { ?>
  <ul class="list-group">
    <? foreach ($items as $item) { ?>
      <li class="clearfix list-group-item">
        <a class="pull-left icon" data-toggle="dropdown">
          <span class="glyphicon glyphicon-cog"></span>
        </a>
        <a class="col-md-11 col-sm-11 col-xs-11 dropdown-toggle" data-toggle="dropdown">
          <?=date('d.m.Y',strtotime($item['date']));?>&emsp;<?=$item['client_title'];?>
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
    <? } ?>
  </ul>
  <?=(isset($pagination) && $pagination ? $pagination : '');?>
<? } else { ?>
  <div class="alert text-warning">Не найдено ни одного акта приемки</div>
<? } ?>