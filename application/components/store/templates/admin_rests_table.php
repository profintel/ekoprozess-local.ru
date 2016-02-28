<div>
  <div id="ajax_result"><div>
  <? if($error) { ?>
    <div class="alert alert-warning">
      <p><?=$error;?></p>
    </div>
  <? } else { ?>
    <? if ($items) { ?>
      <table class="table panel table-hover table-bordered table-store">      
        <tbody>
          <tr>
            <th width="50%" class="text-middle">Общий остаток на складе</th>
            <td><h4><?=number_format(@$rest['rest_all'],2,'.',' ');?></h4></td>
          </tr>
        </tbody>
      </table>
      <table class="table panel table-hover table-bordered table-store">
        <thead>
          <tr>
            <th>Дата</th>
            <th width="40%">Поставщик</th>
            <th>Вид вторсырья</th>
            <th>Приход, кг</th>
            <th>Расход, кг</th>
            <th>Остаток</th>
          </tr>
        </thead>
        <?$all_gross = $all_net = 0; ?>
        <? foreach ($items as $item) { ?>
          <tbody>
            <tr>
              <td>
                <?=date('d.m.Y',strtotime($item['date']));?>
              </td>
              <td>
                <?=$item['client']['title_full'];?>
              </td>
              <td><?=@$item['product']['title_full'];?></td>
              <td>
                <span class="text-nowrap"> + <?=@$item['coming'];?></span>
              </td>
              <td>
                <span class="text-nowrap"><?= - @$item['expenditure'];?></span>
              </td>
              <td><?=@$item['rest_all'];?></td>
            </tr>
          </tbody>
        <? } ?>
      </table>
      <?=(isset($pagination) && $pagination ? $pagination : '');?>
    <? } ?>
  <? } ?>
  </div></div>
</div>