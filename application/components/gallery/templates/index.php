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
        <div class="col-md-9 col-sm-8 col-xs-8">
          <div class="title">
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$item['id'];?>/" title="Перейти">
              <span class="glyphicon glyphicon-folder-open"></span>&emsp;<?=$item['title'];?> / <?=$item['system_name'];?>
            </a>
          </div>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-4">
          <div class="buttons text-right">
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?><?=$item['id'];?>/" class="glyphicon glyphicon-share" title="Перейти"></a>
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_album/<?=$item['id'];?>/" class="glyphicon glyphicon-edit" title="Редактировать"></a>
            <a href="#"
              onClick="return send_confirm(
                'Вы уверены, что хотите удалить галерею и все изображения в этой галерее?',
                '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_gallery/<?=$item['id'];?>/',
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

  <? if ($images) { ?>
    <div class="row image">
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
            <div class="col-xs-4 col-sm-4 col-md-3">
              <a class="zoom thumbnail" href="<?=$image['image'];?>" title='<?=stripslashes(str_replace('\n', '', $image['title']));?>'>
                <img class="item-el" src="<?=$image['thumbs']['180_135'];?>" />

              </a>
              <div class="dropdown item-el-settings">
                <a href="#" class="dropdown-toggle btn btn-default btn-xs" data-toggle="dropdown">
                  <span class="glyphicon glyphicon-wrench"></span>
                </a>
                <?
                  $messages = '<a href='.$image['image'].' target=_blance>Исходное изображение</a><br/>';
                  foreach ($image['thumbs'] as $thumb) {
                    if (is_array($thumb)) {
                      $messages .= '<a href='.$image['thumbs'][$thumb['width'].'_'.$thumb['height']].' target=_blance>Миниатюра</a><br/>';
                    }
                  }
                  $messages .= 'Код для вставки<br/>{{cmp:gallery->render_file<-'.$image['id'].'}}';
                ?>
                <ul class="dropdown-menu">
                  <li class=">">
                    <a href="#" onClick="return my_modal('information', 'Ссылки на изображение', '<?=$messages;?>',[{text: 'OK', handler: function(){$('#modal').modal('hide');}, icon: 'accept'}]);" title="Ссылки">
                     <span class="glyphicon glyphicon-list"></span> Ссылки на миниатюры
                    </a>
                  </li>
                  <li class=">">
                    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>edit_image/<?=$image['id'];?>/" title="Редактировать">
                      <span class="glyphicon glyphicon-edit"></span> Редактировать
                    </a>
                  </li>
                  <li class="divider"></li>
                  <li>
                    <a href="#"
                      onClick="return send_confirm(
                        'Вы уверены, что хотите удалить изображение?',
                        '<?=$_lang_prefix;?>/admin<?=$_component['path'];?>delete_image/<?=$image['id'];?>/',
                        {},
                        'reload'
                      );"                      
                      title="Удалить"
                    ><span class="glyphicon glyphicon-trash"></span> Удалить</a>
                  </li>
                </ul>
              </div> 
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
    </div>
  <? } ?>

  <? if ($category) { ?>
    <br /><a href="/admin<?=$_component['path'];?><?=($category['parent_id'] != 0 ? $category['parent_id'].'/' : '');?>" class=""><span class="glyphicon glyphicon-backward"></span> Назад</a>
  <? } ?>
</div>