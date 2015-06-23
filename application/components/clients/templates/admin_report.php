<div class="container-fluid padding_0">
  <div class="block-title row">
    <div class="col-sm-7">
      <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
        <?=(@$title ? $title : $_component['title']);?>
      </h1>
      <p class="visible-xs-block">&nbsp;</p>
    </div>

    <div class="col-sm-5 text-right">
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_client/" class="btn btn-primary btn-xs pull-right">
        <span class="glyphicon glyphicon-plus"></span> Создать клиента
      </a>
    </div>
  </div>
</div>
<div class="container-fluid">
  <div class="clearfix">
    <a class="btn btn-default btn-xs pull-left" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
    <a  class="btn btn-default btn-xs pull-right" href="/admin<?=$_component['path'];?>clients_report/">Очистить параметры</a>   
  </div><br/>





  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingOne">
        <h4 class="panel-title">
          <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            Быстрый поиск
          </a>
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body">
          <?=$quick_form;?>
        </div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingTwo">
        <h4 class="panel-title">
          <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            Расширенный поиск
          </a>
        </h4>
      </div>
      <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
        <div class="panel-body">
          <?=$form;?>
        </div>
      </div>
    </div>
  </div>




<br/>












  <table class="table table-report table-hover table-bordered">
    <tr>
      <th>ID</th>
      <th>Менеджер</th>
      <th>Название</th>
      <th>Населенный пункт</th>
      <?foreach ($client_params as $key => $value) {?>
        <th><?=$value['title'];?></th>
      <?}?>
    </tr>
    <? foreach ($items as $item) { ?>
      <tr onclick="window.open('/admin/clients/edit_client/<?=$item['id'];?>/','_client_<?=$item['id'];?>')">
        <td><?=$item['id'];?></td>
        <td><?=($item['admin'] ? $item['admin']['params']['name_'.$_language] : 'Не указан');?></td>
        <td><?=$item['title'];?></td>
        <td>
          <?=$item['city_title'];?>
          <?if ($item['city_number']) {?>
            <br/><?=$item['city_number'];?> т. чел.
          <?}?>
          <?if ($item['city_dist_ekb']) {?>
            <br/><?=$item['city_dist_ekb'];?> км до Екатеринбурга
          <?}?>
        </td>
        <?foreach ($client_params as $key => $value) {?>
          <td><?=@$item['params']['param_'.$value['id'].'_'.$_language];?></td>
        <?}?>
      </tr>
    <? } ?>
  </table>
  <?=(isset($pagination) && $pagination ? $pagination : '');?>
  <a class="btn btn-default btn-xs" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
<br/>