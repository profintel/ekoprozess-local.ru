<? if ($items) { ?>
  <table class="table table-report table-hover table-bordered">
    <tr>
      <th>Дата</th>
      <th>Компания</th>
      <th>Брутто, кг</th>
      <th>Упаковка + засор, %</th>
      <th>Нетто, кг</th>
      <th>Цвет</th>
      <th>Цена</th>
      <th>Стоимость<br/>(нетто*цена)</th>
      <th></th>
    </tr>
    <? foreach ($items as $item) { ?>
      <tr>
        <td onclick="window.open('/admin/clients/edit_acceptance/<?=$item['id'];?>/','_client_acceptance_<?=$item['id'];?>')">
          <?=date('d.m.Y',strtotime($item['date']));?>
        </td>
        <td onclick="window.open('/admin/clients/edit_acceptance/<?=$item['id'];?>/','_client_acceptance_<?=$item['id'];?>')">
          <?=$item['client'];?>
        </td>
        <td onclick="window.open('/admin/clients/edit_acceptance/<?=$item['id'];?>/','_client_acceptance_<?=$item['id'];?>')">
          <?=$item['gross'];?>
        </td>
        <td onclick="window.open('/admin/clients/edit_acceptance/<?=$item['id'];?>/','_client_acceptance_<?=$item['id'];?>')">
          <?=$item['result'];?>
        </td>
        <td onclick="window.open('/admin/clients/edit_acceptance/<?=$item['id'];?>/','_client_acceptance_<?=$item['id'];?>')">
          <?=$item['net'];?>
        </td>
        <td onclick="window.open('/admin/clients/edit_acceptance/<?=$item['id'];?>/','_client_acceptance_<?=$item['id'];?>')">
          <?=$item['color'];?>
        </td>
        <td onclick="window.open('/admin/clients/edit_acceptance/<?=$item['id'];?>/','_client_acceptance_<?=$item['id'];?>')">
          <?=$item['price'];?>
        </td>
        <td onclick="window.open('/admin/clients/edit_acceptance/<?=$item['id'];?>/','_client_acceptance_<?=$item['id'];?>')">
          <?=number_format($item['price']*$item['net'],2,'.',' ');?>
        </td>
        <td><a href="/admin/clients/client_acceptance_email/<?=$item['id'];?>/" target="_client_acceptance_email_<?=$item['id'];?>">Отправить акт по email</a></td>
      </tr>
    <? } ?>
  </table>
  <?=(isset($pagination) && $pagination ? $pagination : '');?>
<? } else { ?>
  <div class="alert text-warning">Не найдено ни одного акта приемки</div>
<? } ?>