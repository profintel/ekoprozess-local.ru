<div>
  <div id="ajax_result"><div>
  <? if($error) { ?>
    <div class="alert alert-warning">
      <p><?=$error;?></p>
    </div>
  <? } else { ?>
    <? if ($items) { ?>
      <div class="well-sm text-right">
        <a href="javascript:void(0)" onclick="window.print();" class="btn btn-primary btn-xs hidden-print">
          <span class="glyphicon glyphicon-print"></span> 
          Печать
        </a>
      </div>
      <table class="table panel table-hover table-bordered table-acceptances table-dropdown">
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
        <?$all_gross = $all_net = $all_price = $all_add_expenses = $all_sum = 0; ?>
        <? foreach ($items as $item) { ?>
          <tr class="<?=($item['auto'] ? 'info' : '');?>">
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
            <td rowspan="<?=count($item['childs']);?>">
              <?=date('d.m.Y',strtotime($item['date']));?>
            </td>
            <td rowspan="<?=count($item['childs']);?>">
              <?=$item['client_title'];?>
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
            <td><?=@$item['childs'][0]['product_title'];?></td>
            <td>
              <span class="text-nowrap"><?=number_format(@$item['childs'][0]['price'],2,'.',' ');?></span>
            </td>
            <td>
              <span class="text-nowrap"><?=number_format(@$item['childs'][0]['sum'],2,'.',' ');?></span>
            </td>
            <td rowspan="<?=count($item['childs']);?>">
              <span class="text-nowrap"><?=number_format($item['add_expenses'],0,'.',' ');?></span>
            </td>
            <td rowspan="<?=count($item['childs']);?>">
              <span class="text-nowrap"><?=number_format($item['sum'],2,'.',' ');?></span>
            </td>
            <td rowspan="<?=count($item['childs']);?>">
              <?=$item['comment'];?>
            </td>
          </tr>
          <?//убираем 1 элемент, т.к. вставили его уже выше?>
          <?array_shift($item['childs']);?>
          <?foreach ($item['childs'] as $key => $child) {?>
            <tr class="<?=($item['auto'] ? 'info' : '');?>">
              <td>
                <span class="text-nowrap"><?=number_format($child['gross'],0,'.',' ');?></span>
              </td>
              <td>
                <span class="text-nowrap"><?=number_format($child['net'],0,'.',' ');?></span>
              </td>
              <td>
                <span class="text-nowrap"><?=number_format($child['weight_defect'],0,'.',' ');?></span>
              </td>
              <td><?=$child['product_title'];?></td>
              <td>
                <span class="text-nowrap"><?=number_format($child['price'],2,'.',' ');?></span>
              </td>
              <td>
                <span class="text-nowrap"><?=number_format($child['sum'],2,'.',' ');?></span>
              </td>
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
          <th colspan="2"></th>
          <th></th>
          <th>
            <span class="text-nowrap"><?=number_format($all_gross,0,'.',' ');?></span>
          </th>
          <th>
            <span class="text-nowrap"><?=number_format($all_net,0,'.',' ');?></span>
          </th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th>
            <span class="text-nowrap h5"><?=number_format($all_add_expenses,0,'.',' ');?></span>
          </th>
          <th>
            <span class="text-nowrap h5"><?=number_format($all_price,2,'.',' ');?></span>
          </th>
          <th></th>
        </tr>
        <tr>
          <td colspan="9" class="text-right"><span class="h4">ИТОГО</span></td>
          <td colspan="2" class="text-center">
            <span class="h4"><?=number_format($all_sum,2,'.',' ');?></span>
          </td>
          <td></td>
        </tr>
      </table>
      <?=(isset($pagination) && $pagination ? $pagination : '');?>
    <? } else { ?>
      <div class="alert alert-warning">
        <h2>Акты приемки не найдены</h2>
        <p>Попробуйте изменить параметры поиска</p>
      </div>
    <? } ?>
  <? } ?>
  </div></div>
</div>