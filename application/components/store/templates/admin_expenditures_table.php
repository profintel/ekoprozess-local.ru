<div>
  <div id="ajax_result"><div>
  <? if($error) { ?>
    <div class="alert alert-warning">
      <p><?=$error;?></p>
    </div>
  <? } else { ?>
    <? if ($items) { ?>
      <table class="table panel table-hover table-bordered table-store table-dropdown">
        <thead>
          <tr>
            <td class="td-dropdown hidden-print"></td>
            <th class="text-center hidden-print">Статус</th>
            <th>Дата</th>
            <? if ($type_id == 1) {?><th>Цех</th><? } ?>
            <? if ($type_id == 1) {?><th width="40%">Поставщик</th><? } ?>
            <? if ($type_id == 1) {?><th>Брутто, кг</th><? } ?>
            <? if ($type_id == 2) {?><th>Нетто, кг</th><? } ?>
            <th>Кол-во мест</th>
            <th>Вид вторсырья</th>
          </tr>
        </thead>
        <?$all_gross = $all_net = 0; ?>
        <? foreach ($items as $item) { ?>
          <tbody>
            <tr>
              <td class="td-dropdown hidden-print" rowspan="<?=count($item['childs']);?>">
                <?//меню общее для всей строки?>
                <div class="dropdown">
                  <a class="dropdown-toggle" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"></a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="/admin<?=$this->component['path'];?>edit_<?=$section;?>/<?=$item['id'];?>/" title="Редактировать">
                        <span class="glyphicon glyphicon-edit"></span> <?=($item['active'] ? 'Просмотреть' : 'Редактировать');?>
                      </a>
                    </li>
                    <? if (!$item['active']) { ?>
                      <li>
                        <a href="javascript:void(0)" title="Отправить на склад" onclick='sendMovement("/admin/store/send_expenditure_movement/<?=$item['id'];?>/");'>
                          <span class="glyphicon glyphicon-save"></span> Отправить на склад
                        </a>
                      </li>
                    <? } ?>
                    <? if ($item['client_id']) { ?>
                      <li class="divider"></li>
                      <li>
                        <a href="/admin/clients/edit_client/<?=$item['client_id'];?>/" target="_edit_client_<?=$item['client_id'];?>" title="Редактировать поставщика">
                          <span class="glyphicon glyphicon-edit"></span> Редактировать поставщика
                        </a>
                      </li>
                    <? } ?>
                    <? if (!$item['active']) {?>
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
                    <? } ?>
                  </ul>
                </div>
              </td>
              <?//если active==1 значит расход отправлен в учет движения товара на складе?>
              <td class="text-center hidden-print" rowspan="<?=count($item['childs']);?>">
                <? if ($item['active']) { ?>
                  <span class="glyphicon glyphicon-ok text-success el-tooltip" data-toggle="tooltip" data-placement="right" title="Учтено в остатках"></span>
                <? } else { ?>
                  <span class="glyphicon glyphicon-pencil el-tooltip" data-toggle="tooltip" data-placement="right" title="Черновик. Не учитывается в остатках."></span>
                <? } ?>
              </td>
              <td rowspan="<?=count($item['childs']);?>"><?=date('d.m.Y',strtotime($item['date']));?>
              </td>
              <? if ($type_id == 1) {?>
                <td rowspan="<?=count($item['childs']);?>">
                  <?=$item['workshop']['title'];?>
                </td>
              <? } ?>
              <? if ($type_id == 1) {?>
                <td rowspan="<?=count($item['childs']);?>">
                  <?=$item['client_title'];?>
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
            <?
              $all_gross += $item['gross'];
              $all_net += $item['net'];
            ?>
          </tbody>
        <? } ?>
      </table>
      <?=(isset($pagination) && $pagination ? $pagination : '');?>
    <? } ?>
  <? } ?>
  </div></div>
</div>