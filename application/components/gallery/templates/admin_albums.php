<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    <?=$_component['title'];?>
  </h1>
</div>

<div class="container-fluid wrapper-list container-gallery">
  <? if (!$parent_id) { ?>
    <div class="clearfix well-sm">
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_album/" class="btn btn-primary btn-xs pull-right">
        <span class="glyphicon glyphicon-plus"></span> Создать альбом
      </a>
    </div>
  <? } else { ?>
    <div class="row clearfix well-sm hidden-xs">
      <div class="col-sm-3 col-md-2 pull-right">
        <a class="btn btn-primary btn-xs btn-block" href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>add_youtube/<?=$parent_id;?>/">
          <span class="glyphicon glyphicon-plus"></span> YouTube
        </a>&nbsp;
      </div>
      <div class="col-sm-3 col-md-2 pull-right">
        <a class="btn btn-primary btn-xs btn-block" href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>add_video/<?=$parent_id;?>/">
          <span class="glyphicon glyphicon-plus"></span> Видео
        </a>&nbsp;
      </div>
      <div class="col-sm-3 col-md-2 pull-right">
        <a class="btn btn-primary btn-xs btn-block" href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>add_image/<?=$parent_id;?>/">
          <span class="glyphicon glyphicon-plus"></span> Изображение
        </a>
      </div>
    </div>
    <div class="visible-xs-block clearfix">
      <div class="dropdown well-sm pull-right">
        <a href="#" class="dropdown-toggle btn btn-primary btn-xs" data-toggle="dropdown">
          <span class="glyphicon glyphicon-align-justify"></span>
        </a>
        <ul class="dropdown-menu">
          <li class=">">
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>add_youtube/<?=$parent_id;?>/">
              <span class="glyphicon glyphicon-plus"></span> Добавить ролик с YouTube
            </a>
          </li>
          <li class=">">
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>add_video/<?=$parent_id;?>/">
              <span class="glyphicon glyphicon-plus"></span> Добавить Видео
            </a>
          </li>
          <li class=">">
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>add_image/<?=$parent_id;?>/">
              <span class="glyphicon glyphicon-plus"></span> Добавить Изображение
            </a>
          </li>
        </ul>
      </div>
    </div>
  <? } ?>
  <ul class="list-group">
    <? foreach ($albums as $item) { ?>
      <li class="clearfix list-group-item">
        <a class="pull-left icon" data-toggle="dropdown">
          <span class="glyphicon glyphicon-cog"></span>
        </a>
        <a class="col-md-11 col-sm-11 col-xs-11 dropdown-toggle" data-toggle="dropdown">
          <?=$item['title'];?>
        </a>
        <ul class="dropdown-menu">
          <li><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$item['id'];?>/" title="Перейти"><span class="glyphicon glyphicon-share"></span> Перейти</a></li>
          <li><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_album/<?=$item['id'];?>/" title="Редактировать"><span class="glyphicon glyphicon-edit"></span> Редактировать</a></li>
          <li class="divider"></li>
          <li><a href="#"
            onClick="return send_confirm(
              'Вы уверены, что хотите удалить галерею и все изображения в этой галерее?',
              '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_gallery/<?=$item['id'];?>/',
              {},
              'reload'
            );"
            title="Удалить"
          ><span class="glyphicon glyphicon-trash"></span> Удалить</a></li>
        </ul>
      </li>
    <? } ?>
  </ul>
  {{cmp:gallery->render_gallery_items<-<?=$parent_id;?>}}
  <? if ($category) { ?>
    <br /><a href="/admin<?=$_component['path'];?><?=($category['parent_id'] != 0 ? $category['parent_id'].'/' : '');?>" class=""><span class="glyphicon glyphicon-backward"></span> Назад</a>
  <? } ?>
</div>