<? if ($item['image']) { ?>  
  <? switch ($item['type']) { 
      case 'image':
  ?>  
      <?
        if ($item['params']['thumb_width'] || $item['params']['thumb_height']) {
          $width = $item['params']['thumb_width'];
          $height = $item['params']['thumb_height'];
        } else {
          $width = 185;
          $height = 135;
        }
      ?>   
      <a class="zoom" href="<?=$item['image'];?>"><img src="<?=(isset($item['thumbs'][$width.'_'.$height]) ? $item['thumbs'][$width.'_'.$height] : "/components/gallery/media/no_image.png");?>" /></a>
      <div class="gallery_add_content">
        <div class="title"><?=$item['params']['name_'.$_language];?></div>
        <div class=""><?=$item['params']['description_'.$_language];?></div>
        <br/><br/>
      </div>
    <? 
      break; 
      case 'video'
    ?>
      <a href="<?=$item['image'];?>" class=""
         id="gallery_item_<?=$item['id'];?>" 
         style="display:block; width:<?=$item['params']['width_player'];?>px; height:<?=$item['params']['height_player'];?>px;" 
         title="<?=stripslashes(str_replace('\n', '', $item['params']['name_'.$_language]));?>">
      </a>
      <?=get_player('gallery_item_'. $item['id'], false);?>
    <? break; ?>
    <? 
      break; 
      case 'youtube'
    ?>
      <a href="#" id="gallery_item_<?=$item['id'];?>" class="" title='<?=stripslashes(str_replace('\n', '', $item['title']));?>' onClick="return false;">
        <iframe width="310" height="135" src="http://www.youtube.com/embed/<?=parse_link($item['image'], 'v');?>?rel=0" frameborder="0" allowfullscreen></iframe>
      </a>
    <? break; ?>
  <? } ?>
<? } ?>