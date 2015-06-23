<? if ($fields) { ?>
    <div class="catalog_filter">
        <form action="<?=$_page['crumbs'][0]['path'];?>filter" method="get">
            <input hidden type="hidden" name="c" value="<?=@$_GET['c'] ? $_GET['c'] :$_page['id'];?>">
            <? foreach ($fields as $field) { ?>
                <div class="catalog_filter_item">
                    <? if ($field['type'] == 2) { ?>
                        <select class="form-control" name="<?=$field['id']?>" value="<?=@$_GET[$field['id']] ? $_GET[$field['id']] : '';?>">
                            <option value="0"><?=$field['name']?></option>
                            <? foreach ($field['values'] as $key=>$value) { ?>
                                <option value="<?=$key+1;?>" <? if (@$_GET[$field['id']] == $key+1) {?> selected<? }?>><?=$value?></option>
                            <? } ?>
                        </select>
                    <? } elseif ($field['type'] == 3) { ?>
                        <span class="field_name"><?=$field['name']?>:</span><br>
                        <span class="white_dig pull-right"><?=$field['values'][1]?></span>
                        <span class="white_dig"><?=$field['values'][0]?></span>
                        <input class="slider form-control" name="<?=$field['id']?>"
                               data-slider-min="<?=$field['values'][0]?>"
                               data-slider-max="<?=$field['values'][1]?>"
                               data-slider-step="1"
                               data-slider-value="[<? if (@$_GET[$field['id']]) { $val1 = @explode(",", $_GET[$field['id']]); echo $val1[0]; } else { echo $field['values'][0]; }?>,
                                                <? if (@$_GET[$field['id']]) { $val2 = @explode(",", $_GET[$field['id']]); echo $val2[1]; } else { echo $field['values'][1]; }?>]"
                               data-slider-orientation="horizontal"
                               data-slider-selection="after"
                               data-slider-tooltip="show" />
                    <? } elseif ($field['type'] == 4) { ?>
                        <span class="field_name"><?=$field['name']?>:</span><br>
                        <? foreach ($field['values'] as $key=>$value) { ?>
                            <div class="radio">
                                <label class="field_label">
                                    <input type="radio" name="<?=$field['id']?>" value="<?=$key+1;?>" <? if (@$_GET[$field['id']] == $key+1) {?>checked<?}?>/>
                                    <?=$value;?>
                                </label>
                            </div>
                        <? } ?>
                    <? } elseif ($field['type'] == 5) { ?>
                        <span class="field_name"><?=$field['name']?>:</span><br>
                        <? foreach ($field['values'] as $key=>$value) { ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="<?=$field['id']?>[<?$key+1;?>]" value="<?=$key+1;?>"
                                       <? if (@$_GET[$field['id']]) foreach ($_GET[$field['id']] as $field_value) {
                                            if ($field_value == $key+1) { echo"checked"; }
                                       }?>
                                    />
                                <?=$value;?>
                            </label>
                        <? } ?>
                    <? } else { ?>
                        <input class="form-control" name="<?=$field['id']?>" placeholder="<?=$field['name']?>" value="<?=@$_GET[$field['id']] ? $_GET[$field['id']] : '';?>"/>
                    <? } ?>
                </div>
            <? } ?>
            <div class="catalog_filter_item">
                <button type="submit" class="btn btn-default">найти</button>
            </div>
        </form>
    </div>
<? } ?>