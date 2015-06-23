<div class="sitemap">
  <h1 class="title"><?=$_page['params']['h1_'.$_language];?></h1>

  <? if (!$error) { ?>
    <? if ($results) { ?>
      <ul>
          <? foreach ($results as $item) { ?>
            <li>
                <? if (isset($item['category_name']) && $item['category_name']) { ?>
                    <span class="gray"><?=$item['category_name'];?> - </span>
                <? } ?>
                <a href="<?=$item['path'];?>"><?=$item['show_name'];?></a>
            </li>
          <? } ?>
      </ul>
    <? } else { ?>
      Нет страниц для отображения в карте
    <? } ?>
  <? } else { ?>
    <?=$error;?>
  <? } ?>
</div>