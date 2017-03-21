<div>
  <div id="ajax_result"><div>
  <? if ($items) { ?>
    <div class="well-sm text-right">
      <a href="javascript:void(0)" onclick="window.print();" class="btn btn-primary btn-xs hidden-print">
        <span class="glyphicon glyphicon-print"></span> 
        Печать
      </a>
    </div>
    <table class="table panel table-hover table-acceptances">
      <tr>
        <th>Дата приемки</th>
        <th>Поставщик</th>
        <th>Брутто, кг</th>
        <th>Нетто, кг</th>
        <th>Стоимость, руб.</th>
        <th>Дополнительные расходы, руб.</th>
        <th>ИТОГО</th>
      </tr>
      <?$all_gross = $all_net = $all_price = $all_add_expenses = $all_sum = 0; ?>
      <? foreach ($items as $item) { ?>
        <tr>
          <td>
            <div class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown"><?=date('d.m.Y',strtotime($item['date']));?></a>
              <ul class="dropdown-menu">
                <li>
                  <a href="/admin/acceptances/acceptance/<?=$item['id'];?>/" title="Просмотреть">
                    <span class="glyphicon glyphicon-share"></span> Просмотреть
                  </a>
                  </li>
                <li>
                  <a href="/admin/acceptances/edit_acceptance/<?=$item['id'];?>/" title="Редактировать">
                    <span class="glyphicon glyphicon-edit"></span> Редактировать
                  </a>
                </li>
                <li>
                  <a href="/admin/acceptances/client_acceptance_email/<?=$item['id'];?>/" target="_client_acceptance_email_<?=$item['id'];?>">
                    <span class="glyphicon glyphicon-envelope"></span> Отправить по email
                  </a>
                </li>
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
                  ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
                </li>
              </ul>
            </div>
          </td>
          <td>
            <div class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown"><?=$item['client_title'];?></a>
                <ul class="dropdown-menu">
                  <li>
                    <a href="/admin/acceptances/acceptance/<?=$item['id'];?>/" title="Просмотреть">
                      <span class="glyphicon glyphicon-share"></span> Просмотреть
                    </a>
                    </li>
                  <li>
                    <a href="/admin/acceptances/edit_acceptance/<?=$item['id'];?>/" title="Редактировать">
                      <span class="glyphicon glyphicon-edit"></span> Редактировать
                    </a>
                  </li>
                  <li>
                    <a href="/admin/acceptances/client_acceptance_email/<?=$item['id'];?>/" target="_client_acceptance_email_<?=$item['id'];?>">
                      <span class="glyphicon glyphicon-envelope"></span> Отправить по email
                    </a>
                  </li>
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
                    ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
                  </li>
                </ul>
              </div>
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
        <?
          $all_gross += $item['gross'];
          $all_net += $item['net'];
          $all_price += $item['price'];
          $all_add_expenses += $item['add_expenses'];
          $all_sum += $item['sum'];
        ?>
      <? } ?>
      <tr>
        <th>
        </th>
        <th>ИТОГО</th>
        <th>
          <span class="hidden-print"><?=number_format($all_gross,0,'.',' ');?></span>
          <span class="visible-print"><?=number_format($all_gross,0,'.','');?></span>
        </th>
        <th>
          <span class="hidden-print"><?=number_format($all_net,0,'.',' ');?></span>
          <span class="visible-print"><?=number_format($all_net,0,'.','');?></span>
        </th>
        <th>
          <span class="hidden-print"><?=number_format($all_price,2,'.',' ');?></span>
          <span class="visible-print"><?=number_format($all_price,2,'.','');?></span>
        </th>
        <th>
          <span class="hidden-print"><?=number_format($all_add_expenses,0,'.',' ');?></span>
          <span class="visible-print"><?=number_format($all_add_expenses,0,'.','');?></span>
        </th>
        <th>
          <span class="hidden-print"><?=number_format($all_sum,2,'.',' ');?></span>
          <span class="visible-print"><?=number_format($all_sum,2,'.','');?></span>
        </th>
      </tr>
    </table>
    <?=(isset($pagination) && $pagination ? $pagination : '');?>
  <? } else { ?>
    <div class="alert alert-warning">
      <h2>Акты приемки не найдены</h2>
      <p>Попробуйте изменить параметры поиска</p>
    </div>
  <? } ?>
  </div></div>
</div>