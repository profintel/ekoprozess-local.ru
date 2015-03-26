<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    <?=$_component['title'];?>
  </h1>
</div>
<div class="container-fluid">
  <? if ($error) { ?>
    <div class="alert alert-danger"><?=$error;?></div>
  <? } else { ?>
    <div class="clearfix">
      <form action="/admin/drive/uploadFile/" onsubmit="return false;" enctype="multipart/form-data" target="_self" method="POST" >
        <input id="image" class="default-generated" type="file" name="file">
        <a onclick="return submit_form(this);" class="btn btn-primary btn-xs pull-right" href="#">
          <span class="glyphicon glyphicon-save"></span>  Загрузить  </a>
      </form>
    </div>
    <ul class="list-group">
      <? foreach ($items as $item) { ?>
        <li class="clearfix list-group-item">
          <div class="col-md-9 col-sm-8 col-xs-8">
            <a href="#">
              <? if(isset($item['iconLink']) && $item['iconLink']) { ?>
                <img src="<?=$item['iconLink'];?>" width="16px" height="16px">
              <? } ?> <?=$item['title'];?>
            </a>
          </div>
          <div class="col-md-3 col-sm-4 col-xs-4">
            <div class="buttons text-right">
              <? if(isset($item['alternateLink'])) { ?>
                <a href="<?=$item['alternateLink'];?>" target="_<?=$item['id'];?>" class="glyphicon glyphicon-edit" title="Изменить"></a>
              <? } ?>
              <? if(isset($item['webContentLink'])) { ?>
                <a href="<?=$item['webContentLink'];?>" target="_<?=$item['id'];?>" class="glyphicon glyphicon-download" title="Скачать"></a>
              <? } ?>
              <a href="#"
                onClick="return send_confirm(
                  'Вы уверены, что хотите удалить <?=$item['title'];?>?',
                  '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_file/<?=$item['id'];?>/',
                  {},
                  '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>'
                );"
                class="glyphicon glyphicon-trash"
                title="Удалить"
              ></a>
            </div1>
          </div>
        </li>
    <? } ?>
    </ul>
  <? } ?>
</div>