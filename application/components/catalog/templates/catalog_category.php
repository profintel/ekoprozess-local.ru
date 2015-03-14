<div class="container">
    <h1 class="title"><?=$_page['params']['h1_'.$_language];?></h1>
    <? foreach ($items as $item) { ?>
        <div class="col-xs-3 clearfix catalog">
            <? if (isset($item['image']) && $item['image']) { ?>
                <a href="<?=$_page['path'];?><?=$item['system_name'];?>/" title="<?=$item['params']['name_'. $_language];?>">
                    <div class="img-catalog">
                        <img src="<?=$item['image']['image']?>" alt="<?=$item['params']['name_'. $_language];?>" title="<?=$item['params']['name_'. $_language];?>"/>
                    </div>
                </a>
                <h3 class="publication-title"><a href="<?=$_page['path'];?><?=$item['system_name'];?>/"><?=$item['params']['name_'. $_language];?></a></h3>
                <h3 class="publication-price"><?=number_format($item['params_values'][1], 0, ',', ' ');?> руб.</h3>
            <? } else { ?>
                <h3 class="publication-title"><a href="<?=$_page['path'];?><?=$item['system_name'];?>/"><?=$item['params']['name_'. $_language];?></a></h3>
                <h3 class="publication-price"><?=number_format($item['params_values'][1], 0, ',', ' ');?> руб.</h3>
            <? } ?>
        </div>
    <? } ?>
    <?=($pagination ? $pagination : '');?>
</div>