<div class="">
  <? if ($banner['images']) { ?>
    <a href="/component/banners/banner_transition?banner_id=<?=$banner['id'];?>&banner_zone_id=<?=$banner['zone_id'];?>&banner_link=<?=$banner['link'];?>" id="banner_<?=$banner['id'];?>" <?=($banner['target_blank'] ? ' target="_blank"' : '');?>>
    <?
      $ext = get_ext($banner['images'][0]['image']);
      switch ($ext) {
        case 'swf': 
    ?>    
        <script type="text/javascript">
          $("#banner_<?=$banner['id'];?>").flash({
            src: '<?=$banner["images"][0]["image"];?>',
            width: '<?=$banner_zone['width'];?>',
            height: '<?=$banner_zone['height'];?>'
          }); 
        </script>
    <?    
        break;
        default:
    ?>    
        <img src="<?=$banner['images'][0]['image'];?>" border="0" width="<?=$banner_zone['width'];?>" height="<?=$banner_zone['height'];?>" />
    <?    
        break;
      }
    ?>
    </a>
  <? } ?>
</div>