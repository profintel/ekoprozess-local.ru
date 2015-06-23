<div class="container">
  <h1 class="title"><?=$_page['params']['h1_'.$_language];?> по запросу "<?=$query_string;?>"</h1>

  <? if (!$error) { ?>
    <? if ($results) { ?>
      <? foreach ($results as $item) { ?>
        <div class="search_result arrow_right_blue">
          <a href="<?=$item['path'];?>"><?=$item['show_name'];?></a>
          <? if (isset($item['category_name']) && $item['category_name']) { ?>
            <span class="gray"> - <?=$item['category_name'];?></span>
          <? } ?>
        </div>
      <? } ?>
    <? } else { ?>
      Совпадений не найдено
    <? } ?>
  <? } else { ?>
    <?=$error;?>
  <? } ?>
</div>