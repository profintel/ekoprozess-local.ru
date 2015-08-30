<div id="item-<?=$item['id'];?>" class="panel item-draggable item-droppable clearfix image video">
  <a href="<?=$item['image'];?>" id="gallery_item_<?=$item['id'];?>" class="item float_l" title='<?=stripslashes(str_replace('\n', '', $item['title']));?>'>
    <img src='/adm/flowplayer/splash.gif' class="splash" />
  </a>
  <div class="right">
    <?
      $messages = '<a href='.$item['image'].' target=_blance>Видеофайл</a><br/>';
      $messages .= 'Код для вставки<br/>{{cmp:gallery->render_file<-'.$item['id'].'}}';
    ?>
    <div class="buttons">
      <? if (@$item['order']) { ?>
        <a href="#" class="move_i_s" onClick="return false;" title="Переместить"></a>  
      <? } ?>

      <a href="#" onClick="return modal('information', 'Ссылки на файл', '<?=$messages;?>',[{text: 'OK', handler: function() {modal('hide');}, icon: 'accept'}]);" class="reference" title="Ссылки"></a>
      
      <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_video/<?=$item['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
      
      <a href="#"
        onClick="return send_confirm(
          'Вы уверены, что хотите удалить видео - <?=$item['title'];?> ?',
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