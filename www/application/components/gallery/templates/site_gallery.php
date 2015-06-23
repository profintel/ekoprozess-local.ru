<h1><?=$_page['params']['h1_'.$_language];?></h1>
<? if (isset($images) && $images) { ?>
    <? foreach ($images as $item) { ?> 
      <div class="lead"><?=$item['params']['name_'.$_language];?></div>
      <? switch ($item['type']) {
          case 'video':
        ?>
          <a href="<?=$item['image'];?>" class="pull-left well-small"
             id="gallery_item_<?=$item['id'];?>" 
             style="display:block; width:<?=$item['params']['width_player'];?>px; height:<?=$item['params']['height_player'];?>px;" 
             title="<?=stripslashes(str_replace('\n', '', $item['params']['name_'.$_language]));?>">
          </a>
          <?=get_player('gallery_item_'. $item['id'], false);?>
        <? break; ?>
        <? 
          break; 
          case 'youtube':
        ?>
          <a href="#" id="gallery_item_<?=$item['id'];?>" class="pull-left well-small" title='<?=stripslashes(str_replace('\n', '', $item['title']));?>' onClick="return false;">
            <iframe width="<?=$item['params']['width_player'];?>" height="<?=$item['params']['height_player'];?>" src="http://www.youtube.com/embed/<?=parse_link($item['image'], 'v');?>?rel=0" frameborder="0" allowfullscreen></iframe>
          </a>
        <? 
          break; 
          case 'image':
        ?>
          <?
            if (isset($item['thumb'])) {
              $width = $item['thumb_width'];
              $height = $item['thumb_height'];
            } else {
              $width = 185;
              $height = 135;
            }
          ?>
          <div class="pull-left well-small" style="width:<?=$width;?>px;">
            <div class="desc">
              <div class="title"><?=$item['params']['name_'.$_language];?></div>
              <div class=""><?=$item['params']['description_'.$_language];?></div>
            </div>
            <div class="img" style="width:<?=$width;?>px; height:<?=$height;?>px;">      
              <a class="zoom thumbnail" href="<?=$item['image'];?>"><img src="<?=(isset($item['thumb']) ? $item['thumb'] : (isset($item['standard_thumb']) ? $item['standard_thumb'] : "/components/gallery/media/no_image.png"));?>" /></a>
              <div class="gallery_add_content">
                <div class="title"><?=$item['params']['name_'.$_language];?></div>
                <div class=""><?=$item['params']['description_'.$_language];?></div>
                <br/><br/>
              </div>        
            </div>
          </div>
        <? break; ?>
      <? } ?>
    <? } ?>
<? } ?>
<div class="clear"></div>