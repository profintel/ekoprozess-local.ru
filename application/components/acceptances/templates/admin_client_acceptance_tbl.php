<div style="background-color:#ffffff; padding:20px;">
  <h3 style="text-align:center;">Акт приемки</h3>
  <h4 style="text-align:right;"><?=rus_date($item['date'],'d m Y г.');?></h4>
  <table border="1" cellpadding="10" width="100%" style="background-color:#ffffff; border-collapse: collapse; font-size:14px;" class="table table-bordered">
    <tr>
      <th align="left">Поставщик</th>
      <td><?=($item['client_child_id'] ? $item['client_child_title'] : $item['client_title']);?></td>
    </tr>
    <? if(isset($item['city']) && $item['city']){ ?>
      <tr>
        <th align="left">Город</th>
        <td><?=$item['city']['title'];?></td>
      </tr>
    <? } ?>
    <tr>
      <th align="left" width="20%">ТТН и пункт загрузки</th>
      <td><?=$item['date_num'];?></td>
    </tr>
    <tr>
      <th align="left">Транспорт</th>
      <td><?=$item['transport'];?></td>
    </tr>
    <tr>
      <th align="left">Дата и время прибытия</th>
      <td><?=($item['date_time'] ? date('d.m.Y г. H-i ',strtotime($item['date_time'])) : '');?></td>
    </tr>
    <tr>
      <th align="left">Вес брутто/нетто, кг</th>
      <td><?=$item['gross'];?> / <?=$item['net'];?></td>
    </tr>
  </table>
  <br/>
  <table cellpadding="10" border="1" width="100%" style="background-color:#ffffff; border-collapse: collapse; font-size:14px;" class="table table-bordered">
    <tr>
      <th class="text-center">Наименование товара</th>
      <th class="text-center">Вес в ТТН Поставщика, кг</th>
      <th class="text-center">Брутто, кг</th>
      <th class="text-center">Упаковка, кг</th>
      <th class="text-center">Засор, %</th>
      <th class="text-center">Количество мест</th>
      <th class="text-center">Нетто, кг</th>
      <th class="text-center">Цена, руб.</th>
      <th class="text-center">Стоимость вида вторсырья, руб.</th>
    </tr>
    <? foreach ($item['childs'] as $key => $child) {?>
      <tr>
        <td><?=$child['product']['title_full'];?></td>
        <td align="center"><?=$child['weight_ttn'];?></td>
        <td align="center"><?=$child['gross'];?></td>
        <td align="center"><?=$child['weight_pack'];?></td>
        <td align="center"><?=$child['weight_defect'];?></td>
        <td align="center"><?=$child['cnt_places'];?></td>
        <td align="center"><?=number_format($child['net'],2,'.','');?></td>
        <td align="center"><?=number_format($child['price'],2,'.','');?></td>
        <td align="center"><?=number_format(($child['net']*$child['price']),2,'.','');?></td>
      </tr>
    <?}?>
    <? if ($item['add_expenses']) {?>
    <tr>
      <td align="left" colspan="8">Дополнительные расходы</td>
      <td colspan="" align="center"><?=$item['add_expenses'];?></td>
    </tr>
    <?}?>
    <tr>
      <th align="left" colspan="8">Итого к оплате</th>
      <td colspan="" align="center"><?=number_format($item['sum'],2,'.','');?></td>
    </tr>
  </table>
</div>