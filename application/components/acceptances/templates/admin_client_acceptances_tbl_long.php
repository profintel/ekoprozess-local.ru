<div>
  <div id="ajax_result"><div>
  <? if ($items) { ?>
    <div class="well-sm text-right">
      <a href="javascript:void(0)" onclick="window.print();" class="btn btn-primary btn-xs hidden-print">
        <span class="glyphicon glyphicon-print"></span> 
        Печать
      </a>
    </div>
    <table class="table panel table-hover table-bordered table-acceptances">
      <tr>
        <th>Дата приемки</th>
        <th width="20%">Поставщик</th>
        <th>Брутто, кг</th>
        <th>Нетто, кг</th>
        <th>Засор, %</th>
        <th>Вид вторсырья</th>
        <th>Цена, руб.</th>
        <th>Стоимость, руб.</th>
        <th>Доп. расходы, руб.</th>
      </tr>
      <?$all_gross = $all_net = $all_price = $all_add_expenses = $all_sum = 0; ?>
      <? foreach ($items as $item) { ?>
        <tr>
          <td rowspan="<?=count($item['childs']);?>">
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
          <td rowspan="<?=count($item['childs']);?>">
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
            <span class="text-nowrap"><?=number_format(@$item['childs'][0]['gross'],2,'.',' ');?></span>
          </td>
          <td>
            <span class="text-nowrap"><?=number_format(@$item['childs'][0]['net'],2,'.',' ');?></span>
          </td>
          <td>
            <span class="text-nowrap"><?=number_format(@$item['childs'][0]['weight_defect'],2,'.',' ');?></span>
          </td>
          <td><?=@$item['childs'][0]['product']['title_full'];?></td>
          <td>
            <span class="text-nowrap"><?=number_format(@$item['childs'][0]['price'],2,'.',' ');?></span>
          </td>
          <td>
            <span class="text-nowrap"><?=number_format(@$item['childs'][0]['sum'],2,'.',' ');?></span>
          </td>
          <td>
            <span class="text-nowrap"><?=number_format($item['add_expenses'],2,'.',' ');?></span>
          </td>
        </tr>
        <?array_shift($item['childs']);?>
        <?foreach ($item['childs'] as $key => $child) {?>
          <tr>
            <td>
              <span class="text-nowrap"><?=number_format($child['gross'],2,'.',' ');?></span>
            </td>
            <td>
              <span class="text-nowrap"><?=number_format($child['net'],2,'.',' ');?></span>
            </td>
            <td>
              <span class="text-nowrap"><?=number_format($child['weight_defect'],2,'.',' ');?></span>
            </td>
            <td><?=$child['product']['title_full'];?></td>
            <td>
              <span class="text-nowrap"><?=number_format($child['price'],2,'.',' ');?></span>
            </td>
            <td>
              <span class="text-nowrap"><?=number_format($child['sum'],2,'.',' ');?></span>
            </td>
            <td></td>
          </tr>
        <?}?>
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
        <th></th>
        <th>
          <span class="text-nowrap"><?=number_format($all_gross,2,'.',' ');?></span>
        </th>
        <th>
          <span class="text-nowrap"><?=number_format($all_net,2,'.',' ');?></span>
        </th>
        <th></th>
        <th></th>
        <th></th>
        <th>
          <span class="text-nowrap"><?=number_format($all_price,2,'.',' ');?></span>
        </th>
        <th>
          <span class="text-nowrap"><?=number_format($all_add_expenses,2,'.',' ');?></span>
        </th>
      </tr>
      <tr>
        <td colspan="7" class="text-right"><span class="h4">ИТОГО</span></td>
        <td colspan="2" class="text-center">
          <span class="h4"><?=number_format($all_sum,2,'.',' ');?></span>
        </td>
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