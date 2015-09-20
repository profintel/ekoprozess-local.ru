<div>
  <div id="ajax_result"><div>
  <? if ($items) { ?>
    <ul class="list-group">
      <li class="clearfix list-group-item-head">
        <div class="col-xs-1">Дата</div>
        <div class="col-xs-2">Поставщик</div>
        <div class="col-xs-2">Брутто, кг</div>
        <div class="col-xs-2">Нетто, кг</div>
        <div class="col-xs-2">Стоимость, руб.</div>
        <div class="col-xs-2">Доп. расходы, руб.</div>
        <div class="col-xs-1">ИТОГО</div>
      </li>
      <?$all_gross = $all_net = $all_price = $all_add_expenses = $all_sum = 0; ?>
      <? foreach ($items as $item) { ?>
        <li class="clearfix list-group-item">
          <a class="dropdown-toggle" data-toggle="dropdown">
            <div class="col-xs-1"><?=date('d.m.Y',strtotime($item['date']));?></div>
            <div class="col-xs-2"><?=$item['client_title'];?></div>
            <div class="col-xs-2"><?=number_format($item['gross'],2,'.',' ');?></div>
            <div class="col-xs-2"><?=number_format($item['net'],2,'.',' ');?></div>
            <div class="col-xs-2"><?=number_format($item['price'],2,'.',' ');?></div>
            <div class="col-xs-2"><?=number_format($item['add_expenses'],2,'.',' ');?></div>
            <div class="col-xs-1"><?=number_format($item['sum'],2,'.',' ');?></div>
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
        <?
          $all_gross += $item['gross'];
          $all_net += $item['net'];
          $all_price += $item['price'];
          $all_add_expenses += $item['add_expenses'];
          $all_sum += $item['sum'];
        ?>
      <? } ?>
      <li class="clearfix list-group-item-head">
        <div class="col-xs-1"></div>
        <div class="col-xs-2">ИТОГО:</div>
        <div class="col-xs-2"><?=number_format($all_gross,2,'.',' ');?></div>
        <div class="col-xs-2"><?=number_format($all_net,2,'.',' ');?></div>
        <div class="col-xs-2"><?=number_format($all_price,2,'.',' ');?></div>
        <div class="col-xs-2"><?=number_format($all_add_expenses,2,'.',' ');?></div>
        <div class="col-xs-1"><?=number_format($all_sum,2,'.',' ');?></div>
      </li>
    </ul>
    <?=(isset($pagination) && $pagination ? $pagination : '');?>
  <? } else { ?>
    <div class="alert alert-warning">
      <h2>Акты приемки не найдены</h2>
      <p>Попробуйте изменить параметры поиска</p>
    </div>
  <? } ?>
</div>