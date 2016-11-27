<div>
  <div id="ajax_result"><div>
  <? if($error) { ?>
    <div class="alert alert-warning">
      <p><?=$error;?></p>
    </div>
  <? } else { ?>
  <div class="panel">    
    <h3 class="text-center">"<?=$type['title'];?>"<br/>Отчет по остаткам вторсырья на складе</h3><br/>
    <? //если отчет за 1 день ?>
    <? if($get_params['date_start'] == $get_params['date_end']) { ?>
      <h5>Отчет составлен за <?=rus_date($get_params['date_start'],'j m Yг.');?></h5>
    <? } else { ?>
      <h5>Отчет составлен за период  с <?=rus_date($get_params['date_start'],'j m Yг.');?> по <?=rus_date($get_params['date_end'],'j m Yг.');?></h5>
    <? } ?>

    <? if($client) { ?>
      <h5>Поставщик: "<?=$client['title_full'];?>"</h5>
    <? } ?>
    <? if ($products) {?>
      <h5>Вторсырье: 
        <? foreach ($products as $key => $product) {?>
          <? if ($key != 0) echo ", ";?>
          "<?=$product['title_full'];?>"
        <? } ?>
      </h5>
    <? } ?>
    <? if (!$get_params['store_workshop_id']) { ?>
    <h5>Входящий остаток: <?=number_format($rest['start'],2,'.',' ');?> кг. на <?=rus_date($get_params['date_start'],'j m Yг.');?></h5><br/>
    <? } ?>
    <? if ($items) { ?>
      <table id="table-result" class="table panel table-hover table-bordered table-store">
        <thead>
          <tr>
            <th>Дата</th>
            <th>Цех</th>
            <? if ($type_id == 1) {?><th width="40%">Поставщик</th><? } ?>
            <th>Вид вторсырья</th>
            <th>Приход, кг</th>
            <th>Расход, кг</th>
            <th>Остаток</th>
          </tr>
        </thead>
        <tbody>
          <? foreach ($items as $item) { ?>
            <tr>
              <td><?=date('j.m.Y',strtotime($item['date']));?></td>
              <td><?=(isset($item['workshop']) ? $item['workshop']['title'] : '');?></td>
              <? if ($type_id == 1) {?><td><?=$item['client']['title_full'];?></td><? } ?>
              <td><?=$item['product']['title_full'];?></td>
              <td><span class="text-nowrap"><?=($item['coming'] ? '+ '.$item['coming'] : 0);?></span></td>
              <td><span class="text-nowrap"><?=($item['expenditure'] ? '- '.$item['expenditure'] : 0);?></span></td>
              <td><?=$item['rest'];?></td>
            </tr>
          <? } ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="<?=($type_id == 1 ? 4 : 3);?>"><h5 class="text-right">Итого обороты</h5></td>
            <td>
              <h4 class="text-nowrap"><?=number_format($rest['coming'],2,'.',' ');?></h4>
            </td>
            <td>
              <h4 class="text-nowrap"><?=number_format($rest['expenditure'],2,'.',' ');?></h4>
            </td>
            <td></td>
          </tr>
          <? if (!$get_params['store_workshop_id']) { ?>
            <tr>
              <td colspan="<?=($type_id == 1 ? 4 : 3);?>"><h5 class="text-right">Исходящий остаток на <?=rus_date($get_params['date_end'],'j m Yг.');?></h5></td>
              <td>
                <h4 class="text-nowrap"><?=number_format($rest['end'],2,'.',' ');?></h4>
              </td>
              <td></td>
              <td></td>
            </tr>
          <? } ?>
        </tfoot>
      </table>      
    <? } else { ?>
      <table class="table panel table-hover table-store">
        <tbody>
          <tr class="text-uppercase">
            <th width="30%"></th>
            <th>Приход</th>
            <th>Расход</th>
          </tr>
          <tr>
            <td width="30%" class="text-middle"><h5>Итого обороты:</h5></td>
            <td><h4><?=number_format($rest['coming'],2,'.',' ');?> кг.</h4></td>
            <td><h4><?=number_format($rest['expenditure'],2,'.',' ');?> кг.</h4></td>
          </tr>
          <? if (!$get_params['store_workshop_id']) { ?>
            <tr>
              <td width="30%" class="text-middle"><h5>Исходящий остаток на <?=rus_date($get_params['date_end'],'j m Yг.');?></h5><?=$get_params['store_workshop_id'];?></td>
              <td colspan="2"><h4><?=number_format($rest['end'],2,'.',' ');?> кг.</h4></td>
            </tr>
          <? } ?>
        </tbody>
      </table>   
    <? } ?> 
  </div>
  <? } ?>
  </div></div>
</div>