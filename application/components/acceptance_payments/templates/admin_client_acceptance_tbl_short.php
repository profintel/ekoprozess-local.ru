<? if ($item) { ?>
<table id="table-result" class="table panel table-hover table-bordered table-acceptances table-dropdown">
  <thead>
    <tr>
      <td class="td-dropdown hidden-print"></td>
      <th>Дата приемки</th>
      <th width="20%">Поставщик</th>
      <th>Брутто, кг</th>
      <th>Нетто, кг</th>
      <th>Засор, %</th>
      <th>Вид вторсырья</th>
      <th>Цена, руб.</th>
      <th>Стоимость, руб.</th>
      <th>Доп. расходы, руб.</th>
      <th>Итого, руб.</th>
      <th>Примечания</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="td-dropdown hidden-print" rowspan="<?=count($item['childs']);?>"></td>
      <td id="acceptanceDate<?=$item['acceptance_id'];?>" rowspan="<?=count($item['childs']);?>">
        <?=date('d.m.Y',strtotime($item['date']));?>
      </td>
      <td id="acceptanceClientTitle<?=$item['acceptance_id'];?>" rowspan="<?=count($item['childs']);?>">
        <?=($item['client_child_title'] ? $item['client_child_title'].'<br><small><strong>'.$item['client_title'].'</strong></small>' : $item['client_title']);?>
      </td>
      <td>
        <span class="text-nowrap"><?=number_format(@$item['childs'][0]['gross'],0,'.',' ');?></span>
      </td>
      <td>
        <span class="text-nowrap"><?=number_format(@$item['childs'][0]['net'],0,'.',' ');?></span>
      </td>
      <td>
        <span class="text-nowrap"><?=number_format(@$item['childs'][0]['weight_defect'],0,'.',' ');?></span>
      </td>
      <td><?=@$item['childs'][0]['product']['title_full'];?></td>
      <td>
        <span class="text-nowrap"><?=number_format(@$item['childs'][0]['price'],2,'.',' ');?></span>
      </td>
      <td>
        <span class="text-nowrap"><?=number_format(@$item['childs'][0]['sum'],2,'.',' ');?></span>
      </td>
      <td rowspan="<?=count($item['childs']);?>">
        <span class="text-nowrap"><?=number_format($item['add_expenses'],0,'.',' ');?></span>
      </td>
      <td id="acceptanceSum<?=$item['acceptance_id'];?>" rowspan="<?=count($item['childs']);?>">
        <span class="text-nowrap"><?=number_format($item['sumAcceptance'],2,'.',' ');?></span>
      </td>
      <td rowspan="<?=count($item['childs']);?>">
        <?=$item['comment'];?>
      </td>
    </tr>
    <?//убираем 1 элемент, т.к. вставили его уже выше?>
    <?array_shift($item['childs']);?>
    <?foreach ($item['childs'] as $key => $child) {?>
      <tr>
        <td>
          <span class="text-nowrap"><?=number_format($child['gross'],0,'.',' ');?></span>
        </td>
        <td>
          <span class="text-nowrap"><?=number_format($child['net'],0,'.',' ');?></span>
        </td>
        <td>
          <span class="text-nowrap"><?=number_format($child['weight_defect'],0,'.',' ');?></span>
        </td>
        <td><?=$child['product']['title_full'];?></td>
        <td>
          <span class="text-nowrap"><?=number_format($child['price'],2,'.',' ');?></span>
        </td>
        <td>
          <span class="text-nowrap"><?=number_format($child['sum'],2,'.',' ');?></span>
        </td>
      </tr>
    <?}?>
  </tbody>
  <tfoot>
    <tr>
      <th colspan="2"></th>
      <th></th>
      <th>
        <span class="text-nowrap"><?=number_format($item['gross'],0,'.',' ');?></span>
      </th>
      <th>
        <span class="text-nowrap"><?=number_format($item['net'],0,'.',' ');?></span>
      </th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th>
        <span class="text-nowrap h5"><?=number_format($item['add_expenses'],0,'.',' ');?></span>
      </th>
      <th>
        <span class="text-nowrap h5"><?=number_format($item['sumAcceptance'],2,'.',' ');?></span>
      </th>
      <th></th>
    </tr>
    <tr>
      <td colspan="9" class="text-right"><span class="h4">ИТОГО</span><br><small>с учетом скидки</small></td>
      <td colspan="2" class="text-center">
        <span id="acceptanceSum<?=$item['id'];?>" class="h4"><?=number_format($item['sum'],2,'.',' ');?></span>
      </td>
      <td></td>
    </tr>
  </tfoot>
</table>
<? } else { ?>
  <div class="alert alert-warning">
    <h2>Акт приемки не найден</h2>
  </div>
<? } ?>