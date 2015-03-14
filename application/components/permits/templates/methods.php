<h1 class="icon_big permits-title">Доступ к компонентам и функциям</h1>

<h2>Методы компонента "<?=$component['title'];?>":</h2>

<? foreach ($methods as $name => $title) { ?>
  <div class="panel selection">
    <div class="left">
      <div><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>method/<?=$component['id'];?>/<?=$name;?>/" class="title u-no">
        <?=$name;?><span class="dark"><?=$title ? '&nbsp;/ '. $title : '';?></span>
      </a></div>
      
      <div class="clear"></div>
    </div>
    
    <div class="right">
      <div class="buttons">
        <a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>method/<?=$component['id'];?>/<?=$name;?>/" class="permits-go" title="Доступ"></a>
        
        <div class="clear"></div>
      </div>
    </div>
    
    <div class="clear"></div>
  </div>
<? } ?>

<br /><a href="<?=$_lang_prefix;?>/admin<?=$_component['path'];?>components/" class="icon_small arrow_left_i_s">Назад</a>