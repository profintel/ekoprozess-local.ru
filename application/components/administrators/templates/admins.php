<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    <?=$_component['title'];?>
  </h1>
</div>
<div class="container-fluid">
  <div class="clearfix well-sm">
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_admin/" class="btn btn-primary btn-xs pull-right">
      <span class="glyphicon glyphicon-plus"></span> Создать администратора
    </a>
  </div>
  <ul class="list-group">
    <? foreach ($admins as $item) { ?>
      <li class="clearfix list-group-item">
        <div class="col-md-9 col-sm-8 col-xs-8">
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_admin/<?=$item['id'];?>/"><?=$item['username'];?></a>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-4">
          <div class="buttons text-right">
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_admin/<?=$item['id'];?>/" class="glyphicon glyphicon-edit" title="Изменить"></a>
            <a href="#"
              onClick="return send_confirm(
                'Вы уверены, что хотите удалить учетную запись?',
                '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_admin/<?=$item['id'];?>/',
                {},
                '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
              );"
              class="glyphicon glyphicon-trash"
              title="Удалить"
            ></a>
          </div>
        </div>
      </li>
  <? } ?>
  </ul>
  <br /><br /><a href="/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>