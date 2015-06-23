<? foreach ($items as $item) { ?>
  <h2 class="title"><a href="<?=$page_path;?><?=$item['system_name'];?>/"><?=$item['params']['name_'. $_language];?></a></h2>
  <span class="date"><?=rus_date($item['tm_start'], 'd m Y');?></span>
  <? if (isset($item['image']) && $item['image']) { ?>    
    <div class="col-md-8 publications">      
      <p class="text"><?=$item['params']['text_small_'. $_language];?></p>        
    </div>
    <div class="col-md-4">        
      <a class="thumbnail" href="<?=$page_path;?><?=$item['system_name'];?>/" title="<?=$item['params']['name_'. $_language];?>"><img src="<?=$item['image']['thumbs']['180_135']?>" /></a>
    </div>
  <? } else { ?>
    <div class="col-md-12 publications">      
      <p class="text"><?=$item['params']['text_small_'. $_language];?></p>
    </div>
  <? } ?>
  <br>
<? } ?>

<!--
  <a class="a" href="<?=$page_path;?>">Архив</a>
-->