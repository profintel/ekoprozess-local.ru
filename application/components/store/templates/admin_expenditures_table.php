<div>
  <div id="ajax_result"><div>
  <? if($error) { ?>
    <div class="alert alert-warning">
      <p><?=$error;?></p>
    </div>
  <? } else { ?>
    <? if ($items) { ?>
      <table class="table panel table-hover table-bordered table-store">
        <thead>
          <tr>
            <th class="text-center hidden-print">Статус</th>
            <th>Дата</th>
            <th>Цех</th>
            <th width="30%">Поставщик</th>
            <? if ($type_id == 1) {?>
              <th>Брутто, кг</th>
            <? } else {?>
              <th>Нетто, кг</th>
            <? } ?>
            <th>Кол-во мест</th>
            <th>Вид вторсырья</th>
          </tr>
        </thead>
        <?$all_gross = $all_net = 0; ?>
        <? foreach ($items as $item) { ?>
          <tbody>
            <tr>
              <?//если active==1 значит приход отправлен в учет движения товара на складе?>
              <td class="text-center hidden-print" rowspan="<?=count($item['childs']);?>">
                <? if ($item['active']) { ?>
                  <span class="glyphicon glyphicon-ok text-success el-tooltip" data-toggle="tooltip" data-placement="right" title="Учтено в остатках"></span>
                <? } else { ?>
                  <span class="glyphicon glyphicon-pencil el-tooltip" data-toggle="tooltip" data-placement="right" title="Черновик. Не учитывается в остатках."></span><br>
                  <a href="javascript:void(0)" class="btn btn-primary btn-xs el-tooltip" data-toggle="tooltip" data-placement="right" title="Отправить на склад" onclick='sendMovement("/admin/store/send_expenditure_movement/<?=$item['id'];?>/");'>
                    <span class="glyphicon glyphicon-save"></span>
                  </a>
                <? } ?>
              </td>
              <td rowspan="<?=count($item['childs']);?>">
                <div class="dropdown">
                  <a class="dropdown-toggle" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><?=date('d.m.Y',strtotime($item['date']));?></a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="/admin<?=$this->component['path'];?>edit_<?=$section;?>/<?=$item['id'];?>/" title="Редактировать">
                        <span class="glyphicon glyphicon-edit"></span> Редактировать
                      </a>
                    </li>
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
              <td rowspan="<?=count($item['childs']);?>">
                <div class="dropdown">
                  <a class="dropdown-toggle" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><?=$item['workshop']['title'];?></a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="/admin<?=$this->component['path'];?>edit_<?=$section;?>/<?=$item['id'];?>/" title="Редактировать">
                        <span class="glyphicon glyphicon-edit"></span> Редактировать
                      </a>
                    </li>
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
              <td rowspan="<?=count($item['childs']);?>">
                <div class="dropdown">
                  <a class="dropdown-toggle" data-toggle="dropdown"><?=$item['client_title'];?></a>
                  <ul class="dropdown-menu">
                    <li>
                      <a href="/admin<?=$this->component['path'];?>edit_<?=$section;?>/<?=$item['id'];?>/" title="Редактировать">
                        <span class="glyphicon glyphicon-edit"></span> Редактировать
                      </a>
                    </li>
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