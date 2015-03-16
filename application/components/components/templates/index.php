<div class="block-title clearfix">
  <h1 class="pull-left"><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    Управление компонентами
  </h1>
  <a class="pull-right btn btn-success btn-sm" href="javascript:void(0);" onClick="send_request('<?=$_lang_prefix;?>/admin<?=$_component['path'];?>refresh/');">
    <span class="glyphicon glyphicon-refresh font-bold"></span> Обновить кэш
  </a>
</div>
<div class="container-fluid">
  <div class="<?=(!$uninstalled ?:"col-md-6");?>">
    <div class="panel">
      <div class="panel-heading">
        <div class="panel-heading-title">
          <span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span> Установленные компоненты
        </div>
      </div>
      <table class="table table-hover table-striped">
        <? foreach ($installed as $item) { ?>
          <tr>
            <td width="5%"><span class="glyphicon <?=($item['icon']?$item['icon']:'glyphicon-ok');?>"></span></td>
            <td width="80%"><a class="text-dark" href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>installed/<?=$item['id'];?>/"><?=$item['title'];?></a></td>
            <td width="15%">
              <div class="table-btn-group text-right">
                <a href="#"
                  onClick="return send_request('<?=$_lang_prefix;?>/admin<?=$_component['path'];?>refresh/<?=$item['id'];?>/');"
                  class="glyphicon glyphicon-refresh text-success font-bold btn"
                  title="Обновить кэш"
                ></a>
                <a href="#"
                  onClick="return send_confirm(
                    'Вы уверены, что хотите удалить компонент?',
                    '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete/<?=$item['id'];?>/',
                    {},
                    '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
                  );"
                  class="glyphicon glyphicon-remove text-danger font-bold btn"
                  title="Удалить компонент"
                ></a>
              </div>

            </td>
          </tr>
        <? } ?>
      </table>
    </div>
  </div>
  <? if ($uninstalled) { ?>
    <div class="col-md-6">
      <div class="panel">
        <div class="panel-heading">
          <div class="panel-heading-title">
            <span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span> Доступные компоненты
          </div>
        </div>

        <table class="table table-hover table-striped">
          <? foreach ($uninstalled as $item) { ?>
            <tr>
              <td width="5%"><span class="glyphicon <?=(@$item['icon']?$item['icon']:'glyphicon-ok');?>"></span></td>
              <td width="80%"><a class="text-dark" href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>uninstalled/<?=$item['name'];?>/"><?=$item['title'];?></a></td>
              <td width="15%">
                <div class="table-btn-group text-right">
                  <? if (!$item['errors']) { ?>
                    <a href="#"
                      onClick="return send_request(
                        '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>install/<?=$item['name'];?>/',
                        {},
                        '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
                      );"
                      class="glyphicon glyphicon-plus btn"
                      title="Установить компонент"
                    ></a>
                  <? } ?>
                </div>
              </td>
            </tr>
          <? } ?>
        </table>
      </div>
    </div>
  <? } ?>
</div>