<? foreach ($items as $item) { ?>
    <div class="clearfix catalog">
        <? if (isset($item['image']) && $item['image']) { ?>
            <a href="/katalog/<?=$item['system_name'];?>/" title="<?=$item['params']['name_'. $_language];?>">
                <div class="img-catalog">
                    <img src="<?=$item['image']['image']?>" alt="<?=$item['params']['name_'. $_language];?>" title="<?=$item['params']['name_'. $_language];?>"/>
                </div>
            </a>
            <h3 class="publication-title"><a href="/katalog/<?=$item['system_name'];?>/"><?=$item['params']['name_'. $_language];?></a></h3>
            <h3 class="publication-price"><?=number_format($item['params_values'][1], 0, ',', ' ');?> руб.</h3>
        <? } else { ?>
            <div class="img-catalog">
            </div>
            <h3 class="publication-title"><a href="/katalog/<?=$item['system_name'];?>/"><?=$item['params']['name_'. $_language];?></a></h3>
            <h3 class="publication-price"><?=number_format($item['params_values'][1], 0, ',', ' ');?> руб.</h3>
        <? } ?>
    </div>
<? } ?>