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
  <?=$form;?>
  <br/>
  <div class="panel">
    <div class="form_block">
      <div class="panel-heading"><h4>Вид excel файла</h4></div>
      <div class="panel-body">
        <table class="table table-bordered text-center">
          <tr>
            <th class="text-success">№ колонки в excel</th>
            <td>1</td>
            <td>2</td>
            <td>3</td>
            <? foreach ($client_params as $key => $param) {?>
              <td><?=$key+4;?></td>
            <?}?>
          </tr>
          <tr>
            <th class="text-success">Параметр клиента</th>
            <td>Наименование компании</td>
            <td>Регион</td>
            <td>Город</td>
            <? foreach ($client_params as $key => $param) {?>
              <td><?=$param['title'];?></td>
            <?}?>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <a class="btn btn-default btn-xs" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
<br/>