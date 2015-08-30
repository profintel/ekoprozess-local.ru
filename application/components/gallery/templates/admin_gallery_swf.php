<div id="item-<?=$item['id'];?>" class="panel item-draggable item-droppable clearfix image">
  <a href="<?=$item['image'];?>" id="banner_<?=$item['id'];?>" target="_blance">
  <script type="text/javascript">
    $("#banner_<?=$item['id'];?>").flash({
      src: '<?=$item["image"];?>',
      width: '180',
      height: '135'
    });
  </script>
  </a>
  <div class="right">
    <div class="buttons">
      <? if (@$item['order']) { ?>
        <a href="#" class="move_i_s" onClick="return false;" title="Переместить"></a>
      <? } ?>
      <a href="#"
        onClick="return send_confirm(
          'Вы уверены, что хотите удалить объект - <?=$item['title'];?> ?',
          '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_image/<?=$item['id'];?>/',
          {},
          'reload'
        );"
        class="cross_i_s"
        title="Удалить"
      ></a>
    </div>
  </div>
</div>