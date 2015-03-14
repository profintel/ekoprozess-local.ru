<? foreach ($categories as $category) { ?>
    <div class="catalog_menu_item<? if ($_page['path'] == $category['path']) { ?> selected_category<? } ?>">
        <a class="catalog_category" href="<?=$category['path']?>"><?=$category['title']?></a>
    </div>
<? } ?>