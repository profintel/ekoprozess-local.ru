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
      <table id="table-result" class="table panel table-hover table-bordered table-acceptances table-dropdown">
        <thead>
          <tr>
            <td class="td-dropdown hidden-print"></td>
            <td class="hidden-print"></td>
            <th>Дата приемки</th>
            <th width="20%">Поставщик</th>
            <th>Брутто, кг</th>
            <th>Нетто, кг</th>
            <th>Кол-во мест</th>
            <th>Засор, % <br> акт / приход</th>
            <th>Вид вторсырья</th>
            <th>Цена, руб.</th>
            <th>Стоимость, руб.</th>
            <th>Доп. расходы, руб.</th>
            <th>Итого, руб.</th>
            <th>Примечания</th>
          </tr>
        </thead>
        <tbody>
          <? foreach ($items as $item) { ?>
            <tr style="background-color:<?=(!$item['auto'] && $item['status_color'] ? $item['status_color'] : ($item['auto'] ? 'rgb(217,237,247)' : 'none'));?>">
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
                    <li class="divider"></li>
                    <? if($item['payment_id']) {?>
                      <li>
                        <a href="/admin/acceptance_payments/edit_acceptance_payment/<?=$item['payment_id'];?>" >
                          <span class="glyphicon glyphicon-credit-card"></span> Перейти в бухгалтерию
                        </a>
                      </li>
                    <?} else {?>
                      <li>
                        <a href="javascript:void(0)" onclick="send_request('/admin/acceptances/_set_status_acceptance/<?=$item['id'];?>/4/')">
                          <span class="glyphicon glyphicon-credit-card"></span> Отправить в бухгалтерию
                        </a>
                      </li>
                    <?}?>
                    <li class="divider"></li>
                    <li>
                      <a href="javascript:void(0)" onclick="setAcceptanceExceptions(<?=$item['id'];?>)">
                        <span class="glyphicon glyphicon-"></span> Исключить акт из текущего отчета
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
              <td class="hidden-print" rowspan="<?=count($item['childs']);?>">
                <? if ($item['email']) { ?>
                  <span class="glyphicon glyphicon-envelope text-muted"></span>
                <? } else { ?>
                  <span class="glyphicon glyphicon-envelope text-light-gray"></span>
                <? } ?>
              </td>
              <td id="acceptanceDate<?=$item['id'];?>" rowspan="<?=count($item['childs']);?>">
                <?=date('d.m.Y',strtotime($item['date']));?>
              </td>
              <td id="acceptanceClientTitle<?=$item['id'];?>" rowspan="<?=count($item['childs']);?>">
                <?=($item['client_child_title'] ? $item['client_child_title'].'<br><small><strong>'.$item['client_title'].'</strong></small>' : $item['client_title']);?>
              </td>
              <td>
                <span class="text-nowrap"><?=number_format(@$item['childs'][0]['gross'],0,'.',' ');?></span>
              </td>
              <td>
                <span class="text-nowrap"><?=number_format(@$item['childs'][0]['net'],0,'.',' ');?></span>
              </td>
              <td>
                <span class="text-nowrap"><?=number_format(@$item['childs'][0]['cnt_places'],0,'.',' ');?></span>
              </td>
              <td>
                <span class="text-nowrap">
                  <?=number_format(@$item['childs'][0]['weight_defect'],0,'.',' ');?>
                  <? if (!empty($item['childs'][0]) && !is_null($item['childs'][0]['coming_weight_defect']) && 
                        $item['childs'][0]['weight_defect'] != $item['childs'][0]['coming_weight_defect']) { ?>
                   / <?=number_format(@$item['childs'][0]['coming_weight_defect'],0,'.',' ');?>
                  <? } ?>
                </span>
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
              <td id="acceptanceSum<?=$item['id'];?>" rowspan="<?=count($item['childs']);?>">
                <span class="text-nowrap"><?=number_format($item['sum'],2,'.',' ');?></span>
              </td>
              <td rowspan="<?=count($item['childs']);?>">
                <?=$item['comment'];?>
              </td>
            </tr>
            <?//убираем 1 элемент, т.к. вставили его уже выше?>
            <?array_shift($item['childs']);?>
            <?foreach ($item['childs'] as $key => $child) {?>
              <tr style="background-color:<?=(!$item['auto'] && $item['status_color'] ? $item['status_color'] : ($item['auto'] ? 'rgb(217,237,247)' : 'none'));?>">
                <td>
                  <span class="text-nowrap"><?=number_format($child['gross'],0,'.',' ');?></span>
                </td>
                <td>
                  <span class="text-nowrap"><?=number_format($child['net'],0,'.',' ');?></span>
                </td>
                <td>
                  <span class="text-nowrap"><?=number_format($child['cnt_places'],0,'.',' ');?></span>
                </td>
                <td>
                  <span class="text-nowrap">
                    <?=number_format($child['weight_defect'],0,'.',' ');?>
                    <? if (!is_null($child['coming_weight_defect']) && 
                           $child['weight_defect'] != $child['coming_weight_defect']) { ?>
                    / <?=number_format(@$child['coming_weight_defect'],0,'.',' ');?>
                    <? } ?>
                  </span>
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
          <? } ?>
        </tbody>
        <tfoot>
          <? if(isset($pagination) && $pagination) {?>
            <tr>
              <td colspan="13" class="text-right pagination-wrap">
                <?=$pagination;?>
              </td>
            </tr>
          <? } ?>
          <tr>
            <th colspan="2" class="hidden-print"></th>
            <th></th>
            <th></th>
            <th>
              <span class="text-nowrap"><?=number_format($total_result['gross'],0,'.',' ');?></span>
            </th>
            <th>
              <span class="text-nowrap"><?=number_format($total_result['net'],0,'.',' ');?></span>
            </th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>
              <span class="text-nowrap h5"><?=number_format($total_result['add_expenses'],0,'.',' ');?></span>
            </th>
            <th>
              <span class="text-nowrap h5"><?=number_format($total_result['sum'],2,'.',' ');?></span>
            </th>
            <th></th>
          </tr>
          <tr>
            <th colspan="2" class="hidden-print"></th>
            <td colspan="8" class="text-right"><span class="h4">ИТОГО</span></td>
            <td colspan="2" class="text-center">
              <span class="h4"><?=number_format($total_result['sum_total'],2,'.',' ');?></span>
            </td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    <? } else { ?>
      <div class="alert alert-warning">
        <h2>Акты приемки не найдены</h2>
        <p>Попробуйте изменить параметры поиска</p>
      </div>
    <? } ?>
  <? } ?>
  </div></div>
</div>