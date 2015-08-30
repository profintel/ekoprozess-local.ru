<? if ($items) { ?>
  <div id="wrapper_panel" class="clearfix" data-move_path="<?=@$move_path;?>"><br />      
    <? foreach ($items as $item) { ?>
      {{cmp:gallery->render_gallery_item<-<?=$item['id'];?><-<?=$type;?>}}
    <? } ?>
  </div>
<? } ?>