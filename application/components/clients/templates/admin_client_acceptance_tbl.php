<table border="1" width="100%" cellpadding="4" cellspacing="0" class="table table-bordered">
  <tr>
    <th>Дата</th>
    <th>Компания</th>
    <th>Брутто, кг</th>
    <th>Упаковка + засор, %</th>
    <th>Нетто, кг</th>
    <th>Цвет</th>
    <th>Цена</th>
    <th>Стоимость</th>
  </tr>
  <tr>
    <td><?=date('d.m.Y',strtotime($item['date']));?></td>
    <td><?=$item['client'];?></td>
    <td><?=$item['gross'];?></td>
    <td><?=$item['result'];?></td>
    <td><?=$item['net'];?></td>
    <td><?=$item['color'];?></td>
    <td><?=$item['price'];?></td>
    <td><?=number_format($item['price']*$item['net'],2,'.',' ');?></td>
  </tr>
  <tr>
    <td colspan="8" align="center">Примечание: <?=$item['comment'];?></td>
  </tr>
  <tr>
    <td colspan="8" align="center">Сумма к оплате: <?=number_format($item['price']*$item['net'],2,'.',' ');?> рублей</td>
  </tr>
</table>