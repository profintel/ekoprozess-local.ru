<div class="block-title">
  <h1><span class="glyphicon <?=($_component['icon']?$_component['icon']:'glyphicon-ok');?>"></span>
    Доступ к компонентам и функциям
  </h1>
</div>

<div class="container-fluid">
  <h4>Установленные компоненты:</h4>
  <ul class="list-group">
    <? foreach ($components as $item) { ?>
      <li class="clearfix list-group-item">
        <a class="col-md-9 col-sm-8 col-xs-6" href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>methods/<?=$item['id'];?>/">
          <?=$item['title'];?><span class="dark"> / <?=$item['name'];?></span>
        </a>      
        <div class="col-md-3 col-sm-4 col-xs-6">
          <div class="buttons text-right">
            <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>component/<?=$item['id'];?>/" class="permits-go" title="Доступ"></a>
          </div>
        </div>        
      </li>
    <? } ?>
  </ul>
  <div class="well-sm">
    <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>"><span class="glyphicon glyphicon-backward"></span> Назад</a>
  </div>
</div>