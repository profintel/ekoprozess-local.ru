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
            <th class="text-center h3" colspan="10">Оплата</th>
          </tr>
          <tr>
            <td class="td-dropdown hidden-print"></td>
            <th></th>
            <th width="40%" colspan="2" class="text-center">Безналичный расчет</th>
            <th width="40%" colspan="2" class="text-center">Наличный расчет</th>
            <th width="5%" rowspan="2">Дата поставки</th>
            <th width="5%" rowspan="2">Дата оплаты план</th>
            <th width="5%" rowspan="2">Реквизиты перевода</th>
            <th width="5%" rowspan="2">Примечание</th>
          </tr>
          <tr>
            <td class="td-dropdown hidden-print"></td>
            <th>№</th>
            <th width="20%" >Компания</th>
            <th width="20%" >Сумма</th>
            <th width="20%" >Компания</th>
            <th width="20%" >Сумма</th>
          </tr>
        </thead>
        <tbody>
          <? foreach ($items as $num=> $item) { ?>
            <tr style="background-color:<?=($item['status_color'] ? $item['status_color'] : 'none');?>">
              <td class="td-dropdown hidden-print">
                <div class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown"></a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="/admin/acceptance_payments/edit_acceptance_payment/<?=$item['acceptance_id'];?>/" title="Редактировать">
                        <? if ($item['status_id'] < 10) {?>
                          <span class="glyphicon glyphicon-edit"></span> Редактировать
                        <? } else { ?>
                          <span class="glyphicon glyphicon-share"></span> Просмотреть
                        <? } ?>
                      </a>
                    </li>
                    <? if ($item['status_id'] < 10) {?>
                      <li class="divider"></li>
                      <li>
                        <form action="/admin/acceptances/_set_status_acceptance/<?=$item['acceptance_id'];?>/10/0/0/0/" onsubmit="return false;" >
                          <a href="javascript:void(0)" onclick="submit_form(this,'reload')" title="Оплачено">
                            <span class="glyphicon glyphicon-ruble"></span> Оплачено
                          </a>                          
                        </form>
                      </li>
                    <? } ?>
                    <li class="divider"></li>
                    <li>
                      <a href="/admin/acceptances/acceptance/<?=$item['acceptance_id'];?>/" title="Просмотреть">
                        <span class="glyphicon glyphicon-share"></span> Aкт приемки
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
                          'Вы уверены, что хотите удалить строку оплаты - <?=date('d.m.Y',strtotime($item['date']));?>&emsp;<?=$item['client_title'];?>?',
                          '/admin/acceptance_payments/delete_acceptance_payment/<?=$item['id'];?>/',
                          {},
                          'reload'
                        );"
                        title="Удалить"
                      ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
                    </li>
                  </ul>
                </div>
              </td>
              <td><?=$num+1;?></td>
              <td id="acceptanceClientTitle<?=$item['id'];?>">
                <?if($item['method']=='card'){?>
                  <?=($item['client_child_title'] ? $item['client_child_title'].'<br><small><strong>'.$item['client_title'].'</strong></small>' : $item['client_title']);?>
                <?}?>
              </td>
              <td class="text-nowrap">
                <?if($item['method']=='card'){?>
                  <?=number_format($item['sum'],2,'.',' ')?>
                <?}?>
              </td>
              <td>
                <?if($item['method']=='cash'){?>
                  <?=($item['client_child_title'] ? $item['client_child_title'].'<br><small><strong>'.$item['client_title'].'</strong></small>' : $item['client_title']);?>
                <?}?>
              </td>
              <td class="text-nowrap">                
                <?if($item['method']=='cash'){?>
                  <?=number_format($item['sum'],2,'.',' ')?>
                <?}?>
              </td>
              <td id="acceptanceDate<?=$item['id'];?>">
                <?=date('d.m.Y',strtotime($item['date']));?>
              </td>
              <td></td>
              <td>
                <?if($item['method']=='card'){?>
                  <?=$item['client_params']['bank_ru'];?> <br>
                  <?=$item['client_params']['bank_account_ru'];?>
                <?}?>
              </td>
              <td><?=$item['comment'];?> </td>
            </tr>
          <? } ?>
        </tbody>
        <tfoot>
          <tr>
            <td class="text-right h4" colspan="5">Касса</td>
            <td class="h4 text-nowrap"><?=number_format($cashbox['value'],2,'.',' ');?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </tfoot>
      </table>

      <? if(isset($pagination) && $pagination) {?>
        <div class="pagination-wrap"><?=$pagination;?></div>
      <? } ?>
    <? } else { ?>
      <div class="alert alert-warning">
        <h2>Акты приемки не найдены</h2>
        <p>Попробуйте изменить параметры поиска</p>
      </div>
    <? } ?>
  <? } ?>
  </div></div>
</div>