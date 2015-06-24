<div class="container-fluid padding_0">
  <div class="block-title row">
    <div class="<?=(@$search_path ?'col-sm-7':'');?>">
      <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
        <?=(@$title ? $title : $_component['title']);?>
      </h1>
      <p class="visible-xs-block">&nbsp;</p>
    </div>
    <? if (@$search_path) { ?>
      <div class="col-sm-5 text-right">
        <form action="<?=$search_path;?>" method="GET" class="form-inline">
          <div class="form-group">
            <input type="text" name="title" value="<?=$search_title;?>" <?=($search_title ? 'autofocus="true"' : '');?> class="form-control input-sm" id="searchTitle" placeholder="Введите название">
          </div>
          <div class="form-group">
          <button type="submit" class="btn btn-default btn-sm">Поиск</button>
          </div>
        </form>
      </div>
    <? } ?>
  </div>
</div>
<div class="container-fluid">
  <div class="clearfix block_mb20">
    <? if (count($items) > 20) { ?>
      <a class="btn btn-default btn-xs" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
    <? } ?>
  </div>
  <table class="table table-hover panel">
    <thead>
      <tr>
        <th>Администратор</th>
        <th>Компонент</th>
        <th>Метод</th>
        <th>Описание метода</th>
        <th>Дата</th>
      </tr>
    </thead>
    <tbody>
      <? foreach ($items as $item) { ?>
        <tr class="panel selection">
          <td><?=$item['username'];?></td>
          <td><?=$item['component'];?></td>
          <td><?=$item['method'];?></td>
          <td><?=$item['title'];?></td>
          <td><?=rus_date($item['tm'],'d m Yг. H:i');?></td>
        </tr>
      <? } ?>
    </tbody>
  </table>
  <?=(isset($pagination) && $pagination ? $pagination : '');?>
  <a class="btn btn-default btn-xs" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>