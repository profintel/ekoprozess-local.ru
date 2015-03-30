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
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_<?=$component_item['name'];?>/" class="btn btn-primary btn-xs pull-right">
      <span class="glyphicon glyphicon-plus"></span> Создать <?=@$component_item['title'];?>
    </a>
  </div>
  <? if (@$search_title && !$items) { ?>
    <div class="alert alert-warning clearfix">
      <h5 class="pull-left">По запросу "<?=$search_title;?>" ничего не найдено</h5>
      <a class="btn btn-default btn-xs pull-right" href="<?=$search_path;?>">Очистить поиск</a>
    </div>
  <? } ?>
  <ul class="list-group">
    <? foreach ($items as $item) { ?>
      <li class="clearfix list-group-item">
        <div class="col-md-9 col-sm-8 col-xs-8">
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_<?=$component_item['name'];?>/<?=$item['id'];?>/"><?=$item['title'];?></a>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-4">
          <div class="buttons text-right">
            <? if (isset($item['active']) && $item['active']) { ?>
              <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>disable_<?=$component_item['name'];?>/<?=$item['id'];?>/" class="glyphicon glyphicon-check" title="Отключить"></a>
            <? } ?>
            <? if (isset($item['active']) && !$item['active']) { ?>
              <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>enable_<?=$component_item['name'];?>/<?=$item['id'];?>/" class="glyphicon glyphicon-unchecked" title="Отключить"></a>
            <? } ?>
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_<?=$component_item['name'];?>/<?=$item['id'];?>/" class="glyphicon glyphicon-edit" title="Изменить"></a>
            <a href="#"
              onClick="return send_confirm(
                'Вы уверены, что хотите удалить объект - <?=$item['title'];?>?',
                '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_<?=$component_item['name'];?>/<?=$item['id'];?>/',
                {},
                'reload'
              );"
              class="glyphicon glyphicon-trash"
              title="Удалить"
            ></a>
          </div>
        </div>
      </li>
  <? } ?>
  </ul>
  <?=(isset($pagination) && $pagination ? $pagination : '');?>
  <a class="btn btn-default btn-xs" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>
<br/>