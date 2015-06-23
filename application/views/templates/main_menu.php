<nav class="navbar navbar-default" role="navigation">
    <div class="">
        <ul class="nav navbar-nav">
            <? foreach ($pages as $num=> $item) { ?>
                <? if ($item['pages']) { ?>
                    <li class="dropdown <?=($_page['alias'] == $item['alias'] ? 'active' : '');?>">
                        <a href="<?=$item['path'];?>" class="dropdown-toggle" data-toggle="dropdown" title="<?=$item['params']['name_'.$_language];?>"><?=$item['params']['name_'.$_language];?> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <? foreach ($item['pages'] as $child) { ?>
                                <li><a href="<?=$child['path'];?>" title="<?=$child['params']['name_'.$_language];?>"><?=$child['params']['name_'.$_language];?></a></li>
                            <? } ?>
                        </ul>
                    </li>
                <? } else { ?>
                    <li class="<?=($_page['alias'] == $item['alias'] ? 'active' : '');?>"><a href="<?=$item['path'];?>" title="<?=$item['params']['name_'.$_language];?>"><?=$item['params']['name_'.$_language];?></a></li>
                <? } ?>
            <? } ?>
        </ul>
    </div>
</nav>