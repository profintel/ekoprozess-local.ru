<div class="col-xs-3 padding-0">
    {{cmp:catalog->catalog_categories}}<br>
    {{cmp:catalog->catalog_filter<-<?=$catalog_category['id']?>}}
</div>
<div class="col-xs-9">
    <? foreach ($items as $item) { ?>
        <div class="text clearfix">
            <? if (isset($item['image']) && $item['image']) { ?>
                <div class="col-xs-4">
                    <a href="<?=$page_path;?><?=$item['system_name'];?>/" title="<?=$item['params']['name_'. $_language];?>">
                        <img class="img-responsive" src="<?=$item['image']['thumbs']['180_135']?>" />
                    </a>
                </div>
                <div class="col-xs-8">
                    <h3 class="publication-title"><a href="<?=$page_path;?><?=$item['system_name'];?>/"><?=$item['params']['name_'. $_language];?></a></h3>
                    <div class="col-xs-12 publications padding-0">
                        <p class="texts"><?=$item['params']['text_small_'. $_language];?></p>
                    </div>
                    <a class="pull-right orange" href="<?=$page_path;?><?=$item['system_name'];?>/">ПОДРОБНЕЕ</a>
                </div>
            <? } else { ?>
                <div class="col-xs-12">
                    <h3 class="publication-title"><a href="<?=$page_path;?><?=$item['system_name'];?>/"><?=$item['params']['name_'. $_language];?></a></h3>
                    <div class="col-xs-12 publications padding-0">
                        <p class="texts"><?=$item['params']['text_small_'. $_language];?></p>
                    </div>
                    <a class="pull-right orange" href="<?=$page_path;?><?=$item['system_name'];?>/">ПОДРОБНЕЕ</a>
                </div>
            <? } ?>
        </div>
    <? } ?>
</div>