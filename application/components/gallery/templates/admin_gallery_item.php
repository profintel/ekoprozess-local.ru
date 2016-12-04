<div id="item-<?=$item['id'];?>" class="list-group-item item-draggable item-droppable clearfix selection">
  <a class="dropdown-toggle" data-toggle="dropdown">
    <?=end(explode('/', $item['image']));?>
  </a>
  <ul class="dropdown-menu">
    <li>
      <a href="<?=$item['image'];?>" title="Скачать" download target="_blance" ><span class="glyphicon glyphicon-download-alt"></span> Скачать</a>
    </li>
    <li>
      <a href="/admin/gallery/replace_file/<?=$item['id'];?>/" data-toggle="modal" data-target="#modal" title="Заменить" target="_blance" ><span class="glyphicon glyphicon-retweet"></span> Заменить</a>
    </li>
    <li class="divider"></li>
    <li><a href="#"
        onClick="return send_confirm(
          'Вы уверены, что хотите удалить файл <?=end(explode('/', $item['image']));?> ?',
          '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_image/<?=$item['id'];?>/',
          {},
          'reload'
        );"
        title="Удалить"
      ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
    </li>
  </ul>
</div>