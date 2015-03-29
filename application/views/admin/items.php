<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    <?=(@$title ? $title : $_component['title']);?>
  </h1>
</div>
<div class="container-fluid">
  <div class="clearfix well-sm">
    <? if (isset($pagination) && $pagination) { ?>
      <a class="btn btn-default btn-xs pull-left" href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
    <? } ?>
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_<?=$component_item['name'];?>/" class="btn btn-primary btn-xs pull-right">
      <span class="glyphicon glyphicon-plus"></span> Создать <?=@$component_item['title'];?>
    </a>
  </div>
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