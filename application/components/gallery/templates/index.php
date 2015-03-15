<h1 class="icon_big gallery-title"><?=$_component['title'];?></h1>
<? if (!$parent_id) { ?>
  <div class="links">
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>create_album/" class="icon_small add_i_s">Создать альбом</a>		
    <div class="clear"></div>
  </div>
<? } else { ?>
  <div class="links">
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>add_youtube/<?=$parent_id;?>/" class="icon_small add_i_s">YouTube</a>
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>add_video/<?=$parent_id;?>/" class="icon_small add_i_s">Видео</a>
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>add_image/<?=$parent_id;?>/" class="icon_small add_i_s">Изображение</a>			
    <div class="clear"></div>
  </div>
<? } ?>

<? foreach ($albums as $item) { ?>
  <div class="panel selection folder">
    <div class="left">
      <div class="title">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$item['id'];?>/" class="" title="Перейти"><?=$item['title'];?> / <?=$item['system_name'];?></a>
      </div>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <div class="buttons">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$item['id'];?>/" class="door_in_i_s" title="Перейти"></a>
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_album/<?=$item['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
        <a href="#"
          onClick="return send_confirm(
            'Вы уверены, что хотите удалить галерею и все изображения в этой галерее?',
            '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_gallery/<?=$item['id'];?>/',
            {},
            'reload'
          );"
          class="cross_i_s"
          title="Удалить"
        ></a>
      </div>
    </div>

    <div class="clear"></div>
  </div>
<? } ?>

<? if ($images) { ?>
    <br />
    <? foreach ($images as $image) { ?>
      <? switch ($image['type']) {  
          case 'image':
      ?>
        <? switch ($image['ext']) {  
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'png':
        ?>
          <div class="panel image">
            <div class="img">
              <a class="zoom" href="<?=$image['image'];?>" title='<?=stripslashes(str_replace('\n', '', $image['title']));?>'><img src="<?=$image['thumbs']['180_135'];?>" /></a>
            </div>

            <div class="right">
              <?
                $messages = '<a href='.$image['image'].' target=_blance>Исходное изображение</a><br/>';
                foreach ($image['thumbs'] as $thumb) {
                  if (is_array($thumb)) {
                    $messages .= '<a href='.$image['thumbs'][$thumb['width'].'_'.$thumb['height']].' target=_blance>Миниатюра</a><br/>';
                  }
                }
                $messages .= 'Код для вставки<br/>{{cmp:gallery->render_file<-'.$image['id'].'}}';
              ?>
              <div class="buttons">
                <a href="#" onClick="return modal('information', 'Ссылки на изображение', '<?=$messages;?>',[{text: 'OK', handler: function() {modal('hide');}, icon: 'accept'}]);" class="reference" title="Ссылки"></a>
                
                <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_image/<?=$image['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
                
                <a href="#"
                  onClick="return send_confirm(
                    'Вы уверены, что хотите удалить изображение?',
                    '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_image/<?=$image['id'];?>/',
                    {},
                    'reload'
                  );"
                  class="cross_i_s"
                  title="Удалить"
                ></a>
              </div>      
            </div>      
            <div class="clear"></div>
          </div>
          <? 
            break;
            case 'swf':
          ?>
            <div class="panel image">
              <a href="<?=$image['image'];?>" id="banner_<?=$image['id'];?>" target="_blance">
              <script type="text/javascript">
                $("#banner_<?=$image['id'];?>").flash({
                  src: '<?=$image["image"];?>',
                  width: '180',
                  height: '135'
                });
              </script>
              </a>
              <div class="right">
                <div class="buttons">
                  <a href="#"
                    onClick="return send_confirm(
                      'Вы уверены, что хотите удалить изображение?',
                      '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_image/<?=$image['id'];?>/',
                      {},
                      'reload'
                    );"
                    class="cross_i_s"
                    title="Удалить"
                  ></a>
                </div>
              </div>
            </div>
          <? break; ?>
        <? } ?>
      <? 
        break;       
        case 'video':
      ?>
        <div class="panel image video">
          <a href="<?=$image['image'];?>" id="gallery_item_<?=$image['id'];?>" class="item float_l" title='<?=stripslashes(str_replace('\n', '', $image['title']));?>'>
            <img src='/adm/flowplayer/splash.gif' class="splash" />
          </a>
          <div class="right">
            <?
              $messages = '<a href='.$image['image'].' target=_blance>Видеофайл</a><br/>';
              $messages .= 'Код для вставки<br/>{{cmp:gallery->render_file<-'.$image['id'].'}}';
            ?>
            <div class="buttons">
              <a href="#" onClick="return modal('information', 'Ссылки на файл', '<?=$messages;?>',[{text: 'OK', handler: function() {modal('hide');}, icon: 'accept'}]);" class="reference" title="Ссылки"></a>
              
              <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_video/<?=$image['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
              
              <a href="#"
                onClick="return send_confirm(
                  'Вы уверены, что хотите удалить изображение?',
                  '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_image/<?=$image['id'];?>/',
                  {},
                  'reload'
                );"
                class="cross_i_s"
                title="Удалить"
              ></a>
            </div> 
          </div>
        </div>
      <?
        echo get_player('gallery_item_'. $image['id']);
        break;       
        case 'youtube':
      ?>
        <div class="panel image video">
          <a href="#" id="gallery_item_<?=$image['id'];?>" class="" title='<?=stripslashes(str_replace('\n', '', $image['title']));?>' onClick="return false;">
            <iframe width="310" height="135" src="http://www.youtube.com/embed/<?=parse_link($image['image'], 'v');?>?rel=0" frameborder="0" allowfullscreen></iframe>
          </a>
          <div class="right">
            <?
              $messages = '<a href='.$image['image'].' target=_blance>Видеофайл</a><br/>';
              $messages .= 'Код для вставки<br/>{{cmp:gallery->render_file<-'.$image['id'].'}}';
            ?>
            <div class="buttons">
              <a href="#" onClick="return modal('information', 'Ссылки на файл', '<?=$messages;?>',[{text: 'OK', handler: function() {modal('hide');}, icon: 'accept'}]);" class="reference" title="Ссылки"></a>
              
              <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_youtube/<?=$image['id'];?>/" class="pencil_i_s" title="Редактировать"></a>
              
              <a href="#"
                onClick="return send_confirm(
                  'Вы уверены, что хотите удалить файл?',
                  '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_image/<?=$image['id'];?>/',
                  {},
                  'reload'
                );"
                class="cross_i_s"
                title="Удалить"
              ></a>
            </div> 
          </div>
        </div>
      <?
        break;       
        default:
      ?>
        <div class="clear"></div>
        <div class="panel selection">
          <div class="left">
            <a href="<?=$image['image'];?>" class="" title="" target="_blance" ><?=end(explode('/', $image['image']));?></a>
          </div>

          <div class="right">
            <div class="buttons">              
              <a href="#"
                onClick="return send_confirm(
                  'Вы уверены, что хотите удалить изображение?',
                  '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_image/<?=$image['id'];?>/',
                  {},
                  'reload'
                );"
                class="cross_i_s"
                title="Удалить"
              ></a>
            </div>      
          </div>      
          <div class="clear"></div>
        </div>
      <? 
        break;
      ?>
    <? } ?>
  <? } ?>
<? } ?>

<div class="clear"></div>

<? if ($category) { ?>
  <br /><a href="/admin<?=$_component['path'];?><?=($category['parent_id'] != 0 ? $category['parent_id'].'/' : '');?>" class="icon_small arrow_left_i_s">Назад</a>
<? } ?>