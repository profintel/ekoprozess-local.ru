<? if (isset($images) && $images) { ?>
  <div class="site_gallery">
    <? foreach ($images as $image) { ?>
      <?if ($image['type'] != 'image') {continue;}?>
      <?
        if (isset($image['thumb'])) {
          $width = $image['thumb_width'];
          $height = $image['thumb_height'];
        } else {
          $width = 185;
          $height = 135;
        }
      ?>
      <div class="pull-left well-small" style="width:<?=$width;?>px;">
        <div class="desc">
          <div class="title"><?=$image['params']['name_'.$_language];?></div>
          <div class=""><?=$image['params']['description_'.$_language];?></div>
        </div>
        <div class="img" style="width:<?=$width;?>px; height:<?=$height;?>px;">      
          <a class="zoom thumbnail" href="<?=$image['image'];?>"><img src="<?=(isset($image['thumb']) ? $image['thumb'] : (isset($image['standard_thumb']) ? $image['standard_thumb'] : "/components/gallery/media/no_image.png"));?>" /></a>
          <div class="gallery_add_content">
            <div class="title"><?=$image['params']['name_'.$_language];?></div>
            <div class=""><?=$image['params']['description_'.$_language];?></div>
            <br/><br/>
          </div>
        </div>
      </div>
    <? } ?>
    <div class="clear"></div>
  </div>
<? } ?>