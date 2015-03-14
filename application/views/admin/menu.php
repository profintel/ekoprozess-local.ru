<h1 class="icon_big <?=$_component['name'];?>-title"><?=$title;?></h1>

<? foreach ($items as $item) { ?>
  <a href="<?=$item['link'];?>" class="component-menu-item<?=(isset($item['class']) ? ' '. $item['class'] : '');?>">
    <?=$item['title'];?>
  </a>
<? } ?>

<div class="clear"></div>