<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    Доступ к компонентам и функциям
  </h1>
</div>

<div class="container-fluid wrapper-list">
  <h4>Методы компонента "<?=$component['title'];?>":</h4>

  <? foreach ($methods as $name => $title) { ?>
    <div class="clearfix item-list">
      <div class="col-md-9 col-sm-8 col-xs-8">
        <div><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>method/<?=$component['id'];?>/<?=$name;?>/" class="title u-no">
          <?=$name;?><span class="dark"><?=$title ? '&nbsp;/ '. $title : '';?></span>
        </a></div>
      </div>
      
      <div class="col-md-3 col-sm-4 col-xs-4">
        <div class="buttons">
          <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>method/<?=$component['id'];?>/<?=$name;?>/" class="permits-go" title="Доступ"></a>
        </div>
      </div>
    </div>
  <? } ?>

  <br /><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
</div>