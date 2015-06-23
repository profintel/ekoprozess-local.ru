<h1><?=$_page['params']['h1_'.$_language];?></h1>
<? if (isset($albums) && $albums) { ?>
  <div class="site_gallery">
    <? foreach ($albums as $album) { ?>
      <div class="pull-left well-small" style="height:<?=$album['params']['thumb_height'];?>px">
        <div class="desc">
          <div class="title"><?=$album['params']['name_'.$_language];?></div>
        </div>
        <div class="img">      
          <a href="<?=$_page['path'];?>?album_id=<?=$album['id'];?>">
            <img src="<?=(isset($album['image']) && isset($album['image']['thumbs'][$album['params']['thumb_width'].'_'.$album['params']['thumb_height']]) 
            ? $album['image']['thumbs'][$album['params']['thumb_width'].'_'.$album['params']['thumb_height']] 
            : isset($album['image']['thumbs']['180_135']) ? $album['image']['thumbs']['180_135'] : "/components/gallery/media/no_image.png");?>" />
          </a>     
        </div>
      </div>
    <? } ?>
    <div class="clear"></div>
  </div>
<? } ?>