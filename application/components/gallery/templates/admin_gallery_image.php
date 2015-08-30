<div id="item-<?=$item['id'];?>" class="col-xs-6 col-md-3">
  <div class="thumbnail clearfix">
    <a class="zoom" href="<?=$item['image'];?>">
      <img src="<?=$item['thumbs']['180_135'];?>" />
    </a>
    <div class="caption pull-right">
      <? if (@$item['order']) { ?>
        <a href="#" class="move_i_s" onClick="return false;" title="Переместить"></a>  
      <? } ?>

      <?
        $messages = '<a href='.$item['image'].' target=_blance>Исходное изображение</a><br/>';
        foreach ($item['thumbs'] as $thumb) {
          if (is_array($thumb)) {
            $messages .= '<a href='.$item['thumbs'][$thumb['width'].'_'.$thumb['height']].' target=_blance>Миниатюра ('.$thumb['width'].'&times;'.$thumb['height'].')</a><br/>';
          }
        }
        $messages .= 'Код для вставки<br/>{{cmp:gallery->render_file<-'.$item['id'].'}}';
      ?>
      <a href="#" class="glyphicon glyphicon-option-vertical" title="Ссылки" onClick="return my_modal('information', 'Ссылки на изображение', '<?=$messages;?>',[{text: 'OK', handler: function() {my_modal('hide');}, icon: 'accept'}]);"></a>
      
      <a class="glyphicon glyphicon-edit" title="Редактировать" href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_image/<?=$item['id'];?>/" target="_image_<?=$item['id'];?>"></a>
      
      <a href="#"
        onClick="return send_confirm(
          'Вы уверены, что хотите удалить изображение?',
          '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_image/<?=$item['id'];?>/',
          {},
          'reload'
        );"
        class="glyphicon glyphicon-trash"
        title="Удалить"
      ></a>
    </div>
  </div>
</div>