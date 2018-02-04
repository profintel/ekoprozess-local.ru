<div>
  <div id="ajax_result"><div>
  <? if($error) { ?>
    <div class="alert alert-warning">
      <p><?=$error;?></p>
    </div>
  <? } else { ?>
    <? if ($items) { ?>
      <? if (!$render_table_email) { ?>
        <div class="well-sm text-right">
          <a href="/admin/acceptance_payments/send_acceptances_payment_email/?<?=$postfix;?>" onclick="" class="btn btn-primary btn-xs hidden-print">
            <span class="glyphicon glyphicon-print"></span> 
            Отправить по email
          </a>
          <a href="javascript:void(0)" onclick="window.print();" class="btn btn-primary btn-xs hidden-print">
            <span class="glyphicon glyphicon-print"></span> 
            Печать
          </a>
        </div>
      <?}?>
      <table id="table-result" border="1" class="table panel table-hover table-bordered table-acceptances table-dropdown">
        <thead>
          <tr>
            <th class="text-center" align="center" colspan="12"><h3>Оплата</h3></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <?if(!$render_table_email){?><td width="1%" class="td-dropdown hidden-print"></td><?}?>
            <th width="1%"></th>
            <th width="40%" colspan="4" class="text-center">Безналичный расчет</th>
            <th width="40%" colspan="5" class="text-center">Наличный расчет</th>
            <th width="5%" rowspan="2">Примечание</th>
          </tr>
          <tr>
            <?if(!$render_table_email){?><td width="1%" class="td-dropdown hidden-print"></td><?}?>
            <th width="1%">№</th>
            <th width="10%">Компания</th>
            <th width="10%">Сумма</th>
            <th width="10%">Дата поставки</th>
            <th width="10%">Дата оплаты</th>
            <th width="10%">Компания</th>
            <th width="10%">Сумма</th>
            <th width="10%">Дата поставки</th>
            <th width="10%">Дата оплаты</th>
            <th width="10%">Реквизиты перевода</th>
          </tr>
          <?$num=1;?>
          <? foreach ($items as $parent_items) { ?>
            <?$rowspan = count($parent_items['card']) + count($parent_items['cash']) - 1;?>
            <tr style="background-color:<?=($parent_items['status_color'] ? $parent_items['status_color'] : 'none');?>">
              <?if(!$render_table_email){?>
                <td width="1%" class="td-dropdown hidden-print" rowspan="<?=$rowspan;?>">
                  <div class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown"></a>
                    <ul class="dropdown-menu">
                      <li>
                        <a href="/admin/acceptance_payments/edit_acceptance_payment/<?=$parent_items['parent_id'];?>/" title="Редактировать">
                          <? if ($parent_items['status_id'] < 10) {?>
                            <span class="glyphicon glyphicon-edit"></span> Редактировать
                          <? } else { ?>
                            <span class="glyphicon glyphicon-share"></span> Просмотреть
                          <? } ?>
                        </a>
                      </li>
                      <? if ($parent_items['status_id'] < 10) {?>
                        <li class="divider"></li>
                        <li>
                          <form action="/admin/acceptances/_set_status_acceptance/<?=$parent_items['acceptance_id'];?>/10/0/0/0/" onsubmit="return false;" >
                            <a href="javascript:void(0)" onclick="submit_form(this,'reload')" title="Оплачено">
                              <span class="glyphicon glyphicon-ruble"></span> Оплачено
                            </a>                          
                          </form>
                        </li>
                      <? } ?>
                      <li class="divider"></li>
                      <li>
                        <a href="#"
                          onClick="return send_confirm(
                            'Вы уверены, что хотите удалить строку оплаты?',
                            '/admin/acceptance_payments/delete_acceptance_payment/<?=$parent_items['id'];?>/',
                            {},
                            'reload'
                          );"
                          title="Удалить"
                        ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
                      </li>
                    </ul>
                  </div>
                  <??>
                </td>
              <?}?>
              <td width="1%" rowspan="<?=$rowspan;?>"><?=$num;?></td>
              <td id="acceptanceClientTitle<?=$parent_items['id'];?>">
                <? if($parent_items['card']) {?>
                  <?=($parent_items['card'][0]['client_child_title'] ? $parent_items['card'][0]['client_child_title'].'<br><small><strong>'.$parent_items['card'][0]['client_title'].'</strong></small>' : $parent_items['card'][0]['client_title']);?>
                <?}?>
              </td>
              <td class="text-nowrap">
                <? if($parent_items['card']) {?>
                <?=number_format($parent_items['card'][0]['sum'],2,'.',' ')?>
                <?}?>
              </td>
              <td class="text-nowrap">
                <? if($parent_items['card']) {?>
                <?=date('d.m.Y',strtotime($parent_items['card'][0]['date']));?>
                <?}?>
              </td>
              <td class="text-nowrap">
                <? if($parent_items['card']) {?>
                <?=($parent_items['card'][0]['date_payment'] ? date('d.m.Y',strtotime($parent_items['card'][0]['date_payment'])) : '');?>
                <?}?>
              </td>
              <td>
                <? if($parent_items['cash']) {?>
                <?=($parent_items['cash'][0]['client_child_title'] ? $parent_items['cash'][0]['client_child_title'].'<br><small><strong>'.$parent_items['cash'][0]['client_title'].'</strong></small>' : $parent_items['cash'][0]['client_title']);?>
                <?}?>
              </td>
              <td class="text-nowrap">  
                <? if($parent_items['cash']) {?>           
                <?=number_format($parent_items['cash'][0]['sum'],2,'.',' ')?>
                <?}?>
              </td>
              <td class="text-nowrap">
                <? if($parent_items['cash']) {?>
                <?=date('d.m.Y',strtotime($parent_items['cash'][0]['date']));?>
                <?}?>
              </td>
              <td class="text-nowrap">
                <? if($parent_items['cash']) {?>
                <?=($parent_items['cash'][0]['date_payment'] ? date('d.m.Y',strtotime($parent_items['cash'][0]['date_payment'])) : '');?>
                <?}?>
              </td>
              <td>
                <? if($parent_items['cash']) {?>
                <?=$parent_items['cash'][0]['client_params']['param_1_ru'];?>
                <?}?>
              </td>
              <td rowspan="<?=$rowspan;?>"><?=$parent_items['comment'];?> </td>
            </tr>
            <?unset($parent_items['card'][0],$parent_items['cash'][0])?>
            <? foreach ($parent_items['card'] as $num_card=> $item) { ?>
              <tr style="background-color:<?=($item['status_color'] ? $item['status_color'] : 'none');?>">
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
                <td class="text-nowrap">
                  <?if($item['method']=='card'){?>
                    <?=date('d.m.Y',strtotime($item['date']));?>
                  <?}?>
                </td>
                <td class="text-nowrap">
                  <?if($item['method']=='card'){?>
                    <?=($item['date_payment'] ? date('d.m.Y',strtotime($item['date_payment'])) : '');?>
                  <?}?>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            <? } ?>
            <? foreach ($parent_items['cash'] as $num_cash=> $item) { ?>
              <tr style="background-color:<?=($item['status_color'] ? $item['status_color'] : 'none');?>">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td >
                  <?if($item['method']=='cash'){?>
                    <?=($item['client_child_title'] ? $item['client_child_title'].'<br><small><strong>'.$item['client_title'].'</strong></small>' : $item['client_title']);?>
                  <?}?>
                </td>
                <td class="text-nowrap">                
                  <?if($item['method']=='cash'){?>
                    <?=number_format($item['sum'],2,'.',' ')?>
                  <?}?>
                </td>
                <td class="text-nowrap">
                  <?if($item['method']=='cash'){?>
                    <?=date('d.m.Y',strtotime($item['date']));?>
                  <?}?>
                </td>
                <td class="text-nowrap">
                  <?if($item['method']=='cash'){?>
                    <?=($item['date_payment'] ? date('d.m.Y',strtotime($item['date_payment'])) : '');?>
                  <?}?>
                </td>
                <td>
                  <?if($item['method']=='cash'){?>
                    <?=$item['client_params']['param_1_ru'];?>
                  <?}?>
                </td>
              </tr>
            <? } ?>

            <?$num++;?>
          <? } ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="<?=(!$render_table_email ? '7' : '6')?>"><h4 style="text-align: right">Касса</h4></td>
            <td class="text-nowrap"><h4><?=number_format($cashbox['value'],2,'.',' ');?></h4></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </tfoot>
      </table>
      <? if(!$render_table_email && isset($pagination) && $pagination) {?>
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