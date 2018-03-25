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
          <a href="/admin/acceptance_payments/edit_cashbox/" class="btn btn-primary btn-xs hidden-print">
            <span class="glyphicon glyphicon-rub"></span> Касса
          </a>
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
      <table id="table-result" border="1" class="table panel table-hover table-bordered table-acceptances">
        <thead>
          <tr>
            <th class="text-center" align="center" colspan="12"><h3>Оплата</h3></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th width="1%"></th>
            <th width="40%" colspan="4" class="text-center">Безналичный расчет</th>
            <th width="40%" colspan="5" class="text-center">Наличный расчет</th>
            <th width="5%" rowspan="2">Примечание</th>
          </tr>
          <tr>
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
          <? foreach ($items as $parent_id=> $parent_items) { ?>
            <?$rowspan = count($parent_items['card']) + count($parent_items['cash']);?>
            <?if(count($parent_items['card']) && count($parent_items['cash'])){
              $rowspan -= 1;
            }?>
            <tr>
              <td width="1%" rowspan="<?=$rowspan;?>"><?=$num;?></td>
              <td data-id="<?=($parent_items['card'] ? $parent_items['card'][0]['id'] : '');?>" data-parent="<?=($parent_items['card'] ? $parent_items['card'][0]['parent_id'] : ($parent_items['cash'] ? $parent_items['cash'][0]['parent_id'] : ''));?>" class="<?=($parent_items['card'] ? 'draggable' : 'droppable');?>" style="background-color:<?=($parent_items['card'] ? $parent_items['card'][0]['status_color'] : 'none');?>" >
                <? if($parent_items['card']) {?>                  
                  <span class="icon_small move_i_s hidden-print"></span>
                  <?if($render_table_email){?>
                    <?=($parent_items['card'][0]['client_child_title'] ? $parent_items['card'][0]['client_child_title'].'<br><small><strong>'.$parent_items['card'][0]['client_title'].'</strong></small>' : $parent_items['card'][0]['client_title']);?>
                  <?} else {?>
                    <div class="dropdown">
                      <a class="dropdown-toggle" data-toggle="dropdown"><?=($parent_items['card'][0]['client_child_title'] ? $parent_items['card'][0]['client_child_title'].'<br><small><strong>'.$parent_items['card'][0]['client_title'].'</strong></small>' : $parent_items['card'][0]['client_title']);?></a>
                      <ul class="dropdown-menu hidden-print">
                        <li>
                          <a data-toggle="modal" href="/admin/acceptance_payments/edit_acceptance_paymentModal/<?=$parent_items['card'][0]['id'];?>/" data-target="#acceptancePaymentEditModal" title="Редактировать">
                            <? if ($parent_items['card'][0]['status_id'] < 10) {?>
                              <span class="glyphicon glyphicon-edit"></span> Редактировать
                            <? } else { ?>
                              <span class="glyphicon glyphicon-share"></span> Просмотреть
                            <? } ?>
                          </a>
                        </li>
                        <? if ($parent_items['card'][0]['status_id'] < 10) {?>
                          <li class="divider"></li>
                          <li>
                            <form action="/admin/acceptance_payments/set_status_acceptance_payment/<?=$parent_items['card'][0]['id'];?>/10/" onsubmit="return false;" >
                              <a href="javascript:void(0)" onclick="submit_form(this)" title="Оплачено">
                                <span class="glyphicon glyphicon-ruble"></span> Оплачено
                              </a>
                            </form>
                          </li>
                        <? } ?>
                        <li class="divider"></li>
                        <li>
                          <a href="/admin/acceptances/edit_acceptance/<?=$parent_items['card'][0]['acceptance_id'];?>/" target="_edit_acceptance_<?=$parent_items['card'][0]['acceptance_id'];?>" title="Акт приемки">
                            <span class="glyphicon glyphicon-list-alt"></span> Акт приемки
                          </a>
                        </li>
                        <? if ($parent_items['card'][0]['client_id']) {?>
                          <li class="divider"></li>
                          <li>
                            <a href="/admin/clients/edit_client/<?=$parent_items['card'][0]['client_id'];?>/" target="_edit_client_<?=$parent_items['card'][0]['client_id'];?>" title="Карточка клиента">
                              <span class="glyphicon glyphicon-list-alt"></span> Карточка клиента
                            </a>
                          </li>
                        <? } ?>
                        <li class="divider"></li>
                        <li>
                          <a href="#"
                            onClick="return send_confirm(
                              'Вы уверены, что хотите удалить строку оплаты?',
                              '/admin/acceptance_payments/delete_acceptance_payment/<?=$parent_items['card'][0]['id'];?>/',
                              {},
                              'reload'
                            );"
                            title="Удалить"
                          ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
                        </li>
                      </ul>
                    </div>
                  <?}?>
                <?}?>
              </td>
              <td class="text-nowrap" style="background-color:<?=($parent_items['card'] ? $parent_items['card'][0]['status_color'] : 'none');?>">
                <? if($parent_items['card']) {?>
                <?=number_format($parent_items['card'][0]['sum'],2,'.',' ')?>
                <?}?>
              </td>
              <td class="text-nowrap" style="background-color:<?=($parent_items['card'] ? $parent_items['card'][0]['status_color'] : 'none');?>">
                <? if($parent_items['card']) {?>
                <?=date('d.m.Y',strtotime($parent_items['card'][0]['date']));?>
                <?}?>
              </td>
              <td class="text-nowrap" style="background-color:<?=($parent_items['card'] ? $parent_items['card'][0]['status_color'] : 'none');?>">
                <? if($parent_items['card']) {?>
                <?=($parent_items['card'][0]['date_payment'] ? date('d.m.Y',strtotime($parent_items['card'][0]['date_payment'])) : '');?>
                <?}?>
              </td>
              <td data-id="<?=($parent_items['cash'] ? $parent_items['cash'][0]['id'] : '');?>" data-parent="<?=($parent_items['cash'] ? $parent_items['cash'][0]['parent_id'] : ($parent_items['card'] ? $parent_items['card'][0]['parent_id'] : ''));?>" class="<?=($parent_items['cash'] ? 'draggable' : 'droppable');?>" style="background-color:<?=($parent_items['cash'] ? $parent_items['cash'][0]['status_color'] : 'none');?>">     
                <? if($parent_items['cash']) {?>
                  <span class="icon_small move_i_s hidden-print"></span>
                  <?if($render_table_email){?>
                    <?=($parent_items['cash'][0]['client_child_title'] ? $parent_items['cash'][0]['client_child_title'].'<br><small><strong>'.$parent_items['cash'][0]['client_title'].'</strong></small>' : $parent_items['cash'][0]['client_title']);?>
                  <?} else {?>
                    <div class="dropdown">
                      <a class="dropdown-toggle" data-toggle="dropdown"><?=($parent_items['cash'][0]['client_child_title'] ? $parent_items['cash'][0]['client_child_title'].'<br><small><strong>'.$parent_items['cash'][0]['client_title'].'</strong></small>' : $parent_items['cash'][0]['client_title']);?></a>
                      <ul class="dropdown-menu hidden-print">
                        <li>
                          <a data-toggle="modal" href="/admin/acceptance_payments/edit_acceptance_paymentModal/<?=$parent_items['cash'][0]['id'];?>/" data-target="#acceptancePaymentEditModal" title="Редактировать">
                            <? if ($parent_items['cash'][0]['status_id'] < 10) {?>
                              <span class="glyphicon glyphicon-edit"></span> Редактировать
                            <? } else { ?>
                              <span class="glyphicon glyphicon-share"></span> Просмотреть
                            <? } ?>
                          </a>
                        </li>
                        <? if ($parent_items['cash'][0]['status_id'] < 10) {?>
                          <li class="divider"></li>
                          <li>
                            <form action="/admin/acceptance_payments/set_status_acceptance_payment/<?=$parent_items['cash'][0]['id'];?>/10/"  method="POST" target="_self" enctype="multipart/form-data" onsubmit="return false;" >
                              <input type="hidden" name="method_pay_cash" value="plus">
                              <a href="javascript:void(0)" onclick="submit_form(this)" title="Оплачено">
                                <span class="glyphicon glyphicon-ruble"></span> Оплачено ( + )
                              </a>
                            </form>
                          </li>
                          <li class="divider"></li>
                          <li>
                            <form action="/admin/acceptance_payments/set_status_acceptance_payment/<?=$parent_items['cash'][0]['id'];?>/10/" method="POST" target="_self" enctype="multipart/form-data" onsubmit="return false;" >
                              <input type="hidden" name="method_pay_cash" value="minus">
                              <a href="javascript:void(0)" onclick="submit_form(this)" title="Оплачено">
                                <span class="glyphicon glyphicon-ruble"></span> Оплачено ( - )
                              </a>
                            </form>
                          </li>
                        <? } ?>
                        <li class="divider"></li>
                        <li>
                          <a href="/admin/acceptances/edit_acceptance/<?=$parent_items['cash'][0]['acceptance_id'];?>/" target="_edit_acceptance_<?=$parent_items['cash'][0]['acceptance_id'];?>" title="Акт приемки">
                            <span class="glyphicon glyphicon-list-alt"></span> Акт приемки
                          </a>
                        </li>
                        <? if ($parent_items['cash'][0]['client_id']) {?>
                          <li class="divider"></li>
                          <li>
                            <a href="/admin/clients/edit_client/<?=$parent_items['cash'][0]['client_id'];?>/" target="_edit_client_<?=$parent_items['cash'][0]['client_id'];?>" title="Карточка клиента">
                              <span class="glyphicon glyphicon-list-alt"></span> Карточка клиента
                            </a>
                          </li>
                        <? } ?>
                        <li class="divider"></li>
                        <li>
                          <a href="#"
                            onClick="return send_confirm(
                              'Вы уверены, что хотите удалить строку оплаты?',
                              '/admin/acceptance_payments/delete_acceptance_payment/<?=$parent_items['cash'][0]['id'];?>/',
                              {},
                              'reload'
                            );"
                            title="Удалить"
                          ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
                        </li>
                      </ul>
                    </div>
                  <?}?>
                <?}?>
              </td>
              <td class="text-nowrap"  style="background-color:<?=($parent_items['cash'] ? $parent_items['cash'][0]['status_color'] : 'none');?>">  
                <? if($parent_items['cash']) {?>           
                <?=number_format($parent_items['cash'][0]['sum'],2,'.',' ')?>
                <?}?>
              </td>
              <td class="text-nowrap"  style="background-color:<?=($parent_items['cash'] ? $parent_items['cash'][0]['status_color'] : 'none');?>">
                <? if($parent_items['cash']) {?>
                <?=date('d.m.Y',strtotime($parent_items['cash'][0]['date']));?>
                <?}?>
              </td>
              <td class="text-nowrap"  style="background-color:<?=($parent_items['cash'] ? $parent_items['cash'][0]['status_color'] : 'none');?>">
                <? if($parent_items['cash']) {?>
                <?=($parent_items['cash'][0]['date_payment'] ? date('d.m.Y',strtotime($parent_items['cash'][0]['date_payment'])) : '');?>
                <?}?>
              </td>
              <td  style="background-color:<?=($parent_items['cash'] ? $parent_items['cash'][0]['status_color'] : 'none');?>">
                <? if($parent_items['cash'] && isset($parent_items['cash'][0]['client_params']['param_1_ru'])) {?>
                <?=$parent_items['cash'][0]['client_params']['param_1_ru'];?>
                <?}?>
              </td>
              <td rowspan="<?=$rowspan;?>"><?=$parent_items['comment'];?> </td>
            </tr>
            <?unset($parent_items['card'][0],$parent_items['cash'][0])?>
            <? foreach ($parent_items['card'] as $num_card=> $item) { ?>
              <tr style="background-color:<?=($item['status_color'] ? $item['status_color'] : 'none');?>">
                <td data-id="<?=$item['id'];?>" data-parent="<?=$item['parent_id'];?>" class="<?=($item['method']=='card' ? 'draggable' : 'droppable');?>" id="acceptanceClientTitle<?=$item['id'];?>">
                  <?if($item['method']=='card'){?>
                    <span class="icon_small move_i_s hidden-print"></span>
                    <?if($render_table_email){?>
                      <?=($item['client_child_title'] ? $item['client_child_title'].'<br><small><strong>'.$item['client_title'].'</strong></small>' : $item['client_title']);?>
                    <?} else {?>
                      <div class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown"><?=($item['client_child_title'] ? $item['client_child_title'].'<br><small><strong>'.$item['client_title'].'</strong></small>' : $item['client_title']);?></a>
                        <ul class="dropdown-menu hidden-print">
                          <li>
                            <a data-toggle="modal" href="/admin/acceptance_payments/edit_acceptance_paymentModal/<?=$item['id'];?>/" data-target="#acceptancePaymentEditModal" title="Редактировать">
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
                              <form action="/admin/acceptance_payments/set_status_acceptance_payment/<?=$item['id'];?>/10/" onsubmit="return false;" >
                                <a href="javascript:void(0)" onclick="submit_form(this)" title="Оплачено">
                                  <span class="glyphicon glyphicon-ruble"></span> Оплачено
                                </a>
                              </form>
                            </li>
                          <? } ?>
                          <li class="divider"></li>
                          <li>
                            <a href="/admin/acceptances/edit_acceptance/<?=$item['acceptance_id'];?>/" target="_edit_acceptance_<?=$item['acceptance_id'];?>" title="Акт приемки">
                              <span class="glyphicon glyphicon-list-alt"></span> Акт приемки
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
                                'Вы уверены, что хотите удалить строку оплаты?',
                                '/admin/acceptance_payments/delete_acceptance_payment/<?=$item['id'];?>/',
                                {},
                                'reload'
                              );"
                              title="Удалить"
                            ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
                          </li>
                        </ul>
                      </div>
                    <?}?>
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
                <td data-id="<?=$item['id'];?>" data-parent="<?=$item['parent_id'];?>" class="<?=($item['method']=='cash' ? 'draggable' : 'droppable');?>">
                  <?if($item['method']=='cash'){?>
                    <span class="icon_small move_i_s hidden-print"></span>
                    <?if($render_table_email){?>
                      <?=($item['client_child_title'] ? $item['client_child_title'].'<br><small><strong>'.$item['client_title'].'</strong></small>' : $item['client_title']);?>
                    <?} else {?>
                      <div class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown"><?=($item['client_child_title'] ? $item['client_child_title'].'<br><small><strong>'.$item['client_title'].'</strong></small>' : $item['client_title']);?></a>
                        <ul class="dropdown-menu hidden-print">
                          <li>
                            <a data-toggle="modal" href="/admin/acceptance_payments/edit_acceptance_paymentModal/<?=$item['id'];?>/" data-target="#acceptancePaymentEditModal" title="Редактировать">
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
                              <form action="/admin/acceptance_payments/set_status_acceptance_payment/<?=$item['id'];?>/10/" method="POST" target="_self" enctype="multipart/form-data" onsubmit="return false;" >
                                <input type="hidden" name="method_pay_cash" value="plus">
                                <a href="javascript:void(0)" onclick="submit_form(this)" title="Оплачено">
                                  <span class="glyphicon glyphicon-ruble"></span> Оплачено ( + )
                                </a>
                              </form>
                            </li>
                            <li class="divider"></li>
                            <li>
                              <form action="/admin/acceptance_payments/set_status_acceptance_payment/<?=$item['id'];?>/10/" method="POST" target="_self" enctype="multipart/form-data" onsubmit="return false;" >
                                <input type="hidden" name="method_pay_cash" value="minus">
                                <a href="javascript:void(0)" onclick="submit_form(this)" title="Оплачено">
                                  <span class="glyphicon glyphicon-ruble"></span> Оплачено ( - )
                                </a>
                              </form>
                            </li>
                          <? } ?>
                          <li class="divider"></li>
                          <li>
                            <a href="/admin/acceptances/edit_acceptance/<?=$item['acceptance_id'];?>/" target="_edit_acceptance_<?=$item['acceptance_id'];?>" title="Акт приемки">
                              <span class="glyphicon glyphicon-list-alt"></span> Акт приемки
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
                                'Вы уверены, что хотите удалить строку оплаты?',
                                '/admin/acceptance_payments/delete_acceptance_payment/<?=$item['id'];?>/',
                                {},
                                'reload'
                              );"
                              title="Удалить"
                            ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
                          </li>
                        </ul>
                      </div>
                    <?}?>
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
                  <?if($item['method']=='cash' && isset($item['client_params']['param_1_ru'])){?>
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
            <td colspan="<?=(!$render_table_email ? '6' : '6')?>"><h4 style="text-align: right">Касса</h4></td>
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