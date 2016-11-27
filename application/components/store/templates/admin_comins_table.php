<div>
  <div id="ajax_result"><div>
  <? if($error) { ?>
    <div class="alert alert-warning">
      <p><?=$error;?></p>
    </div>
  <? } else { ?>
    <? if ($items) { ?>
      <table id="table-result" class="table panel table-hover table-bordered table-store table-dropdown">
        <thead>
          <tr>
            <td class="td-dropdown hidden-print"></td>
            <th class="text-center hidden-print">Статус</th>
            <? if ($type_id == 1) {?><th>Дата прибытия машины</th><? } ?>
            <th>Дата прихода на склад</th>
            <? if ($type_id == 1) {?><th width="40%">Поставщик</th><? } ?>
            <? if ($type_id == 1) {?><th>Брутто, кг</th><? } ?>
            <? if ($type_id == 2) {?><th>Цех</th><? } ?>
            <? if ($type_id == 2) {?><th>Нетто, кг</th><? } ?>
            <th>Кол-во мест</th>
            <th>Вид вторсырья</th>
            <? if ($type_id == 2) {?><th>Примечания</th><? } ?>
          </tr>
        </thead>
        <tbody>
          <? foreach ($items as $item) { ?>
            <tr>
              <td class="td-dropdown hidden-print" rowspan="<?=count($item['childs']);?>">
                <?//меню общее для всей строки?>
                <div class="dropdown">
                  <a data-toggle="dropdown"></a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="/admin<?=$this->component['path'];?>edit_<?=$section;?>/<?=$type_id;?>/<?=$item['id'];?>/" title="Редактировать">
                        <span class="glyphicon glyphicon-edit"></span> Редактировать приход
                      </a>
                    </li>
                    <? if (!$item['active']) { ?>
                      <li>
                        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="Отправить на склад" onclick='sendMovement("/admin/store/send_coming_movement/<?=$item['id'];?>/");'>
                          <span class="glyphicon glyphicon-save"></span> Отправить на склад
                        </a>
                      </li>
                    <? } ?>
                    <? if ($item['client_id']) { ?>
                      <li class="divider"></li>
                      <li>
                        <a href="/admin/clients/edit_client/<?=$item['client_id'];?>/" target="_edit_client_<?=$item['client_id'];?>" title="Карточка клиента">
                          <span class="glyphicon glyphicon-list-alt"></span> Карточка клиента
                        </a>
                      </li>
                    <? } ?>
                    <? if ($item['acceptance']) { ?>
                      <li class="divider"></li>
                      <li>
                        <a href="/admin/acceptances/edit_acceptance/<?=$item['acceptance']['id'];?>/" target="_acceptance_<?=$item['acceptance']['id'];?>" title="Акт приемки">
                          <span class="glyphicon glyphicon-new-window"></span> Акт приемки
                        </a>
                      </li>
                    <? } ?>
                    <li class="divider"></li>
                    <li>
                      <a href="#"
                        onClick="return send_confirm(
                          'Вы уверены, что хотите удалить объект?',
                          '/admin<?=$this->component['path'];?>delete_<?=$section;?>/<?=$item['id'];?>/',
                          {},
                          'reload'
                        );"
                        title="Удалить"
                      ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
                    </li>
                  </ul>
                </div>
              </td>
              <?//если active==1 значит приход отправлен в учет движения товара на складе?>
              <td class="text-center hidden-print" rowspan="<?=count($item['childs']);?>">
                <? if ($item['active']) { ?>
                  <span class="glyphicon glyphicon-ok text-success el-tooltip" data-toggle="tooltip" data-placement="right" title="Учтено в остатках"></span>
                <? } else { ?>
                  <span class="glyphicon glyphicon-pencil el-tooltip" data-toggle="tooltip" data-placement="right" title="Черновик. Не учитывается в остатках."></span>
                <? } ?>
              </td>
              <? if ($type_id == 1) {?>
                <td rowspan="<?=count($item['childs']);?>"><?=date('d.m.Y',strtotime($item['date_primary']));?></td>
              <? } ?>
              <td rowspan="<?=count($item['childs']);?>"><?=date('d.m.Y',strtotime($item['date_second']));?></td>
              <? if ($type_id == 1) {?>
                <td rowspan="<?=count($item['childs']);?>">
                  <?=$item['client_title'];?>
                </td>
              <? } ?>
              <? if ($type_id == 2) {?>
                <td rowspan="<?=count($item['childs']);?>">
                  <?=$item['workshop']['title'];?>
                </td>
              <? } ?>
              <td>
                <? if ($type_id == 1) {?>
                  <span class="text-nowrap"><?=number_format(@$item['childs'][0]['gross'],2,'.',' ');?></span>
                <? } else {?>
                  <span class="text-nowrap"><?=number_format(@$item['childs'][0]['net'],2,'.',' ');?></span>
                <? } ?>
              </td>
              <td>
                <span class="text-nowrap"><?=number_format(@$item['childs'][0]['cnt_places'],2,'.',' ');?></span>
              </td>
              <td><?=@$item['childs'][0]['product']['title_full'];?></td>
              <? //примечания ?>
              <? if ($type_id == 2) {?>
                <td rowspan="<?=count($item['childs']);?>"><?=$item['comment'];?></td>
              <? } ?>
            </tr>
            <?array_shift($item['childs']);?>
            <?foreach ($item['childs'] as $key => $child) {?>
              <tr>
                <td>
                  <? if ($type_id == 1) {?>
                    <span class="text-nowrap"><?=number_format($child['gross'],2,'.',' ');?></span>
                  <? } else {?>
                    <span class="text-nowrap"><?=number_format($child['net'],2,'.',' ');?></span>
                  <? } ?>
                </td>
                <td>
                  <span class="text-nowrap"><?=number_format($child['cnt_places'],2,'.',' ');?></span>
                </td>
                <td><?=$child['product']['title_full'];?></td>
              </tr>
            <?}?>
          <? } ?>
        </tbody>
        <tfoot>
          <? if(isset($pagination) && $pagination) {?>
            <tr>
              <td colspan="9" class="text-right pagination-wrap">
                <?=$pagination;?>
              </td>
            </tr>
          <? } ?>
          <tr>
            <td colspan="<?=($type_id == 1 ? 5 : 4);?>" class="text-right">
              <h4>ИТОГО <small>за период  с <?=rus_date($get_params['date_start'],'j m Yг.');?> по <?=rus_date($get_params['date_end'],'j m Yг.');?></small></h4>
            </td>
            <td colspan="<?=($type_id == 1 ? 3 : 4);?>"><h4><?=($type_id == 1 ? number_format($all_gross,0,'.',' ') : number_format($all_net,0,'.',' '));?> кг</h4></td>
          </tr>
        </tfoot>
      </table>
    <? } ?>
  <? } ?>
  </div></div>
</div>