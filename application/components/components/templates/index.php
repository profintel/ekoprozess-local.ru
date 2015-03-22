<div class="block-title">
  <h1>
    <span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    Управление компонентами
  </h1>
</div>
<div class="container-fluid">
  <div class="clearfix block_mb20">
    <a class="pull-right btn btn-primary btn-xs" href="javascript:void(0);" onClick="send_request('<?=$_lang_prefix;?>/admin<?=$_component['path'];?>refresh/');">
      <span class="glyphicon glyphicon-refresh"></span> Обновить кэш
    </a>
  </div>
  <div class="<?=(!$uninstalled ?:"col-md-6");?>">
    <div class="panel">
      <div class="panel-heading">
        <h5><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span> Установленные компоненты</h5>
      </div>
      <ul class="list-group">
        <? foreach ($installed as $item) { ?>
          <li class="clearfix list-group-item">
            <a class="col-md-9 col-sm-8 col-xs-8" href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>installed/<?=$item['id'];?>/">
              <span class="glyphicon <?=($item['icon']?$item['icon']:'glyphicon-ok');?>"></span>&emsp;<?=$item['title'];?>
            </a>
            <div class="col-md-3 col-sm-4 col-xs-4">
              <div class="buttons text-right">
                <a href="#"
                  onClick="return send_request('<?=$_lang_prefix;?>/admin<?=$_component['path'];?>refresh/<?=$item['id'];?>/');"
                  class="glyphicon glyphicon-refresh"
                  title="Обновить кэш"
                ></a>
                <a href="#"
                  onClick="return send_confirm(
                    'Вы уверены, что хотите удалить компонент?',
                    '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete/<?=$item['id'];?>/',
                    {},
                    '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
                  );"
                  class="glyphicon glyphicon-trash"
                  title="Удалить компонент"
                ></a>
              </div>
            </div>
          </li>
        <? } ?>
      </ul>
    </div>
  </div>
  <? if ($uninstalled) { ?>
    <div class="col-md-6">
      <div class="panel">
        <div class="panel-heading">
          <h5>
            <span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span> Доступные компоненты
          </h5>
        </div>

        <ul class="list-group">
          <? foreach ($uninstalled as $item) { ?>
            <li class="clearfix list-group-item">
              <a class="col-md-9 col-sm-8 col-xs-8" href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>uninstalled/<?=$item['name'];?>/">
                <span class="glyphicon <?=(@$item['icon']?$item['icon']:'glyphicon-ok');?>"></span>&emsp;<?=$item['title'];?>
              </a>
              <div class="col-md-3 col-sm-4 col-xs-4">
                <div class="buttons text-right">
                  <? if (!$item['errors']) { ?>
                    <a href="#"
                      onClick="return send_request(
                        '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>install/<?=$item['name'];?>/',
                        {},
                        '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
                      );"
                      class="glyphicon glyphicon-plus"
                      title="Установить компонент"
                    ></a>
                  <? } ?>
                </div>
              </div>
            </li>
          <? } ?>
        </ul>
      </div>
    </div>
  <? } ?>
</div>