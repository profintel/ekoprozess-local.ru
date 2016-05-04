<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    <?=$title;?>
  </h1>
</div>
<div class="container-fluid container-menu">
  <? foreach ($items as $item) { ?>
    <div class="col-md-4 col-sm-6">
      <a href="<?=$item['link'];?>" class="btn btn-primary btn-block <?=(isset($item['class']) ? ' '. $item['class'] : '');?>">
        <span class="glyphicon glyphicon-ok"></span> <span class="btn-title"><?=$item['title'];?></span>
      </a>

      <? if (isset($item['submenu'])) {?>
        <? foreach ($item['submenu'] as $child) {?>
          <ul class="submenu list-unstyled">
            <li class="<?=(isset($child['active']) && $child['active'] ? 'active' : '');?>">
              <a href="<?=$child['link'];?>" class="btn btn-info btn-block">
                <span><?=$child['title'];?></span>
              </a>
            </li>
          </ul>
        <? } ?>
      <? } ?>
    </div>
  <? } ?>
</div>
<? if(isset($back) && $back) {?>
  <div class="container-fluid">
    <div class="col-xs-12">
      <a class="btn btn-default btn-xs" href="<?=$back;?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
    </div>
  </div>
<? } ?>