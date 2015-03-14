<h1 class="icon_big permits-title">Доступ к компонентам и функциям</h1>

<h2>Установленные компоненты:</h2>

<? foreach ($components as $item) { ?>
  <div class="panel selection">
    <div class="left">
      <div><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>methods/<?=$item['id'];?>/" class="title u-no">
        <?=$item['title'];?><span class="dark"> / <?=$item['name'];?></span>
      </a></div>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <div class="buttons">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>component/<?=$item['id'];?>/" class="permits-go" title="Доступ"></a>
        
        <div class="clear"></div>
      </div>
    </div>
    
    <div class="clear"></div>
  </div>
<? } ?>

<br /><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>" class="icon_small arrow_left_i_s">Назад</a>