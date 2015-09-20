<div>
  <div id="ajax_result"><div>
  <? if($items) { ?>
    <table class="table table-report table-hover table-bordered">
      <tr>
        <th>Населенный пункт</th>
        <th>Менеджер</th>
        <th>Название</th>
        <?foreach ($client_params as $key => $value) {?>
          <th><?=$value['title'];?></th>
        <?}?>
        <th>События</th>
      </tr>
      <? foreach ($items as $item) { ?>
        <tr>
          <td onclick="window.open('/admin/clients/edit_client/<?=$item['id'];?>/','_client_<?=$item['id'];?>')">
            <?=$item['city_title'];?>
            <?if ($item['city_number']) {?>
              <br/><?=$item['city_number'];?> т. чел.
            <?}?>
            <?if ($item['city_dist_ekb']) {?>
              <br/><?=$item['city_dist_ekb'];?> км до Екатеринбурга
            <?}?>
          </td>
          <td onclick="window.open('/admin/clients/edit_client/<?=$item['id'];?>/','_client_<?=$item['id'];?>')"><?=($item['admin'] ? $item['admin']['params']['name_'.$this->language] : 'Не указан');?></td>
          <td>
            <?=$item['title'];?>
            <div class="well-sm text-center">
              <a href="/admin/clients/create_acceptance/?client_id=<?=$item['id'];?>" class="label label-primary">
                <span class="glyphicon glyphicon-plus"></span> Акт приемки
              </a>
            </div>
          </td>
          <?foreach ($client_params as $key => $value) {?>
            <td onclick="window.open('/admin/clients/edit_client/<?=$item['id'];?>/','_client_<?=$item['id'];?>')"><?=@$item['params']['param_'.$value['id'].'_'.$this->language];?></td>
          <?}?>
          <td>
            <?if ($item['last_event']) {?>
              <strong><?=$item['last_event']['admin']['params']['name_'.$this->language];?></strong>
              <p><?=$item['last_event']['event'];?> (<?=$item['last_event']['result'];?>)</p>
              <div class="text-center">
                <?if ($item['red_events_cnt']) {?>
                  <a class="label label-danger" onclick="loadEvents('red',<?=$item['id'];?>)"><?=$item['red_events_cnt'];?></a>
                <?}?>
                <?if ($item['blue_events_cnt']) {?>
                  <a class="label label-info" onclick="loadEvents('blue',<?=$item['id'];?>)"><?=$item['blue_events_cnt'];?></a>
                <?}?>
              </div>
            <?}?>
            <div class="well-sm text-center">
              <a href="javascript:void(0)" class="label label-primary " onclick='createLocalEvent(<?=$item['event_params'];?>,"reload")'>
                <span class="glyphicon glyphicon-plus"></span> Добавить событие
              </a>
            </div>
          </td>
        </tr>
      <? } ?>
    </table>
    <?=(isset($pagination) && $pagination ? $pagination : '');?>
  <? } else { ?>
    <div class="alert alert-warning">
      <h2>Клиенты не найдены</h2>
      <p>Попробуйте изменить параметры поиска</p>
    </div>
  <? } ?>
  </div></div>
</div>