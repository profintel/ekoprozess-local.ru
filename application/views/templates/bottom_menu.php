<? foreach ($pages as $num=> $item) { ?>
    <div class="col-xs-6 padding-0 <?=($_page['alias'] == $item['alias'] ? 'active' : '');?>"><a href="<?=$item['path'];?>"><?=$item['params']['name_'.$_language];?></a></div>
<? } ?>