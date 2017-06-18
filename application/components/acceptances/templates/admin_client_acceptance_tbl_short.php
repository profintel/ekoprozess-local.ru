<? if ($item) { ?>
  <table class="table panel table-hover table-acceptances table-dropdown">
    <thead>
      <tr>
        <td class="td-dropdown hidden-print"></td>
        <th>Дата приемки</th>
        <th>Поставщик</th>
        <th>Брутто, кг</th>
        <th>Нетто, кг</th>
        <th>Стоимость, руб.</th>
        <th>Доп. расходы, руб.</th>
        <th>ИТОГО</th>
      </tr>
    </thead>
      <tr>
        <td class="td-dropdown hidden-print" rowspan="<?=count($item['childs']);?>">
          <div class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown"></a>
            <ul class="dropdown-menu">
              <li>
                <a href="/admin/acceptances/acceptance/<?=$item['id'];?>/" title="Просмотреть">
                  <span class="glyphicon glyphicon-share"></span> Просмотреть акт
                </a>
                </li>
              <li>
                <a href="/admin/acceptances/edit_acceptance/<?=$item['id'];?>/" title="Редактировать">
                  <span class="glyphicon glyphicon-edit"></span> Редактировать акт
                </a>
              </li>
              <li>
                <a href="/admin/acceptances/client_acceptance_email/<?=$item['id'];?>/" target="_client_acceptance_email_<?=$item['id'];?>">
                  <span class="glyphicon glyphicon-envelope"></span> Отправить акт по email
                </a>
              </li>
              <? if ($item['client_id']) {?>
                <li class="divider"></li>
                <li>
                  <a href="/admin/clients/edit_client/<?=$item['client_id'];?>/" target="_edit_client_<?=$item['client_id'];?>" title="Карточка клиента">
                    <span class="glyphicon glyphicon-list-alt"></span> Карточка клиента
                  </a>
                </li>
              <? } ?>
              <li class="divider"></li>
              <li>
                <a href="#"
                  onClick="return send_confirm(
                    'Вы уверены, что хотите удалить акт - <?=date('d.m.Y',strtotime($item['date']));?>&emsp;<?=$item['client_title'];?>?',
                    '/admin/acceptances/delete_acceptance/<?=$item['id'];?>/',
                    {},
                    'reload'
                  );"
                  title="Удалить"
                ><span class="glyphicon glyphicon-trash"></span> Удалить акт</a>
              </li>
            </ul>
          </div>
        </td>
        <td>
          <?=date('d.m.Y',strtotime($item['date']));?>
        </td>
        <td>
          <?=$item['client_title'];?>
        </td>
        <td>
          <span class="hidden-print"><?=number_format($item['gross'],0,'.',' ');?></span>
          <span class="visible-print"><?=number_format($item['gross'],0,'.','');?></span>
        </td>
        <td>
          <span class="hidden-print"><?=number_format($item['net'],0,'.',' ');?></span>
          <span class="visible-print"><?=number_format($item['net'],0,'.','');?></span>
        </td>
        <td>
          <span class="hidden-print"><?=number_format($item['price'],2,'.',' ');?></span>
          <span class="visible-print"><?=number_format($item['price'],2,'.','');?></span>
        </td>
        <td>
          <span class="hidden-print"><?=number_format($item['add_expenses'],0,'.',' ');?></span>
          <span class="visible-print"><?=number_format($item['add_expenses'],0,'.','');?></span>
        </td>
        <td>
          <span class="hidden-print"><?=number_format($item['sum'],2,'.',' ');?></span>
          <span class="visible-print"><?=number_format($item['sum'],2,'.','');?></span>
        </td>
      </tr>
  </table>
<? } else { ?>
  <div class="alert alert-warning">
    <h2>Акт приемки не найден</h2>
  </div>
<? } ?>