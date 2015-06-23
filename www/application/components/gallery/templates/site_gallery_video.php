<? if (isset($images) && $images) { ?>
    <? foreach ($images as $item) { ?> 
      <div class="lead"><?=$item['params']['name_'.$_language];?></div>
      <? switch ($item['type']) {
          case 'video'
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
          case 'youtube'
        ?>
          <a href="#" id="gallery_item_<?=$item['id'];?>" class="pull-left well-small" title='<?=stripslashes(str_replace('\n', '', $item['title']));?>' onClick="return false;">
            <iframe width="<?=$item['params']['width_player'];?>" height="<?=$item['params']['height_player'];?>" src="http://www.youtube.com/embed/<?=parse_link($item['image'], 'v');?>?rel=0" frameborder="0" allowfullscreen></iframe>
          </a>
        <? break; ?>
      <? } ?>
      <div class=""><?=$item['params']['description_'.$_language];?></div>
      <br/>
    <? } ?>
<? } ?>
<div class="clear"></div>