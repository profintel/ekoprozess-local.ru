<div class="container">

    <h1 class="titles"><?=$_page['params']['h1_'.$_language];?></h1>
    <? if (isset($item['image']) && $item['image']) { ?>
        <div class="clearfix">
            <div class="col-xs-7 padding-0">
                <a class="zoom img" href="<?=$item['image']['image']?>" title="<?=$item['params']['name_'. $_language];?>">
                    <img class="img-responsive" src="<?=$item['image']['image']?>" />
                </a>
                <? if ($item['images']) { ?>
                    <div class="mini_img">
                        <? foreach ($item['images'] as $image) { ?>
                            <div class="clearfix catalog">
                                <div class="img-catalog">
                                    <a class="zoom img" href="<?=$image['image']?>" title="<?=$item['params']['name_'. $_language];?>">
                                        <img src="<?=$image['thumbs']['180_135']?>" alt="<?=$item['params']['name_'. $_language];?>" title="<?=$item['params']['name_'. $_language];?>"/>
                                    </a>
                                </div>
                            </div>
                        <? } ?>
                    </div>
                <? } ?>
            </div>
    <? } else { ?>
        <div class="clearfix">
            <div class="col-xs-7"></div>
    <? } ?>
            <div class="col-xs-5">
                <div class="col-xs-12">
                    <h2 class="titles2"><?=$_page['params']['h1_'.$_language];?></h2>
                    <h2 class="price"><?=number_format($item['values'][1], 0, ',', ' ');?> <i class="fa fa-rub"></i></h2>
                    <h3>Характеристики</h3>
                    <table class="table character">
                        <? foreach ($item['params_fields'] as $field) { ?>
                            <? if ($field['id'] != 1) { ?>
                                <tr>
                                    <td class="character_name"><?=$field['name']?></td>
                                    <td><?=($field['type'] == 2 || $field['type'] == 4 || $field['type'] == 5) ? @$field['values'][$item['values'][$field['id']]-1] : @$item['values'][$field['id']]?></td>
                                </tr>
                            <? } ?>
                        <? } ?>
                    </table>
                    <a class="btn catalog_button" href="#" data-toggle="modal" data-target="#feedbacks">Заказать</a>
                </div>
            </div>
        </div>

    <div class="clearfix descr">
        <?=$item['params']['text_full_'. $_language];?>
    </div>

    <div>
        <h2 class="blueh2">ДРУГИЕ ПРЕДЛОЖЕНИЯ</h2>
        {{cmp:catalog->last_catalog<-katalog<-5}}
    </div>

</div>