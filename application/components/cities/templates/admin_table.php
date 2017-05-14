<div>
  <div id="ajax_result"><div>
  <? if($error) { ?>
    <div class="alert alert-warning">
      <p><?=$error;?></p>
    </div>
  <? } else { ?>
    <? if ($items) { ?>
      <table class="table table-report table-hover table-bordered table-dropdown">
        <tr>
          <td class="td-dropdown hidden-print"></td>
          <th>Населенный пункт</th>
          <th>Численность населения, т. чел.</th>
          <th>Расстояние до Екатринбурга, км</th>
        </tr>
        <? foreach ($items as $item) { ?>
          <tr>
            <td class="td-dropdown hidden-print">
              <?//меню общее для всей строки?>
              <div class="dropdown">
                <a data-toggle="dropdown"></a>
                <ul class="dropdown-menu">
                  <li><a href="/admin<?=$this->component['path'];?>edit_<?=$component_item['name'];?>/<?=$item['id'];?>/" title="Редактировать"><span class="glyphicon glyphicon-edit"></span> Редактировать</a></li>
                  <li class="divider"></li>
                  <li>
                    <a href="#"
                      onClick="return send_confirm(
                        'Вы уверены, что хотите удалить объект - <?=$item['title'];?>?',
                        '/admin<?=$this->component['path'];?>delete_<?=$component_item['name'];?>/<?=$item['id'];?>/',
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
              <?=$item['title'];?>
            </td>
            <td>
              <?=$item['number'];?>
            </td>
            <td>
              <?=$item['dist_ekb'];?>
            </td>
          </tr>
        <? } ?>
      </table>
      <?=(isset($pagination) && $pagination ? $pagination : '');?>
    <? } ?>
  <? } ?>
  </div></div>
</div>